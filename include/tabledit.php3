<?php
//$Id$
/* 
Copyright (C) 1999, 2000 Association for Progressive Communications 
http://www.apc.org/

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program (LICENSE); if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// (c) Econnect, Jakub Adamek, December 2002
// DOCUMENTATION: doc/tabledit.html, doc/tabledit_developer.html, doc/tableview.html

require "tabledit_column.php3";
require "tabledit_util.php3";
require $GLOBALS[AA_INC_PATH]."formutil.php3";

// identifies new record 
$new_key = "__new__";

/**  Class TableEdit
*    See doc/tabledit.html for more info.
*/	
class tabledit {
    // serialization purposes??
    var $classname = "tabledit";

    // VARIABLES SET IN CONSTRUCTOR
    /// active TableView definition (see doc/tableview.html) completed by default values
    var $view;
    /// view ID
    var $viewID;
    /// calling script URL, used as FORM action="$action"
    var $action;
    /** Global cmd[] parameter. See doc/tabledit.html for a description. */
    var $all_cmd;
    /** is this a CHILD table?  */
    var $is_child;
    /** session (for session-stored variables) */
    var $sess;
    /** URL path for images */
    var $imagepath;    
    /**  Function to get other Table View definitions by ID. 
    *    Used in $this->ShowChildren() and ProcessFormData(), see tabledit_util.php3.
    *    The function must get two parameters: string $viewID and bool $processForm.
    *
    *    The parameter $processForm tells whether the function is called from ProcessFormData().
    *    In that case not all settings are important and you can leave out
    *    some of them. Useful when creating a new module with TableEdit, because you must not
    *    call init_page.php3 before the module is added. */    
    var $getTableViewsFn;
    
    // PRIVATE VARIABLES
    // command for this particular Table View (exactly $all_cmd[$viewID])
    var $cmd;
    /// field alias to order by
    var $orderby;
    /// order direction: '' and 'a' mean ascending, 'd' means descending
    var $orderdir;
    /// show a record allowing to add new data?
    var $show_new;
    /// columns (fields) definition completed by GetColumnTypes (see tabledit_util.php3)
    var $cols;
    /// view type (edit / browse)
    var $type;
    /** array of aliases of fields forming primary keys for each table 
    *   (if not using "join", there is only one table) 
    */
    var $primary_aliases;

	/** constructor, see above for parameter description */
    function tabledit($viewID, $action, $cmd, $view, $imagepath, &$sess, $getTableViewsFn, $is_child=false) {
        $this->viewID = $viewID;
        $this->all_cmd = $cmd;
        $this->cmd = $cmd[$viewID];
        $this->view = $view;
        $this->cols = $this->view["fields"];
        // complete the column info
        SetColumnTypes ($this->cols, $this->primary_aliases, 
            $this->view["table"], $this->view["join"],
            $this->view["readonly"], $this->view["primary"]);
        $this->is_child = $is_child;
        $this->action = $action;       
        $this->sess = &$sess;
        $this->imagepath = $imagepath;
        $this->getTableViewsFn = $getTableViewsFn;
        $this->type = $this->view["type"];
        
        $this->UpdateCmd ();
    }

    /** exchanges data between session-stored cmd (as tabledit_cmd) and current URL cmd */
    function UpdateCmd () {
        if (is_object ($this->sess))
            $this->sess->register("tabledit_cmd");
        global $tabledit_cmd;
        $tecmd = &$tabledit_cmd[$this->viewID];
        
        // update ORDERBY and ORDERDIR
        $orderby = $this->cmd["orderby"];
        if (is_array ($orderby)) {
            reset ($orderby);
            $orderby = key ($orderby);
            $this->orderby = $orderby;
            if ($tecmd["orderby"][$orderby]) {
                $tecmd["orderdir"] = 
                    $tecmd["orderdir"] == 'd' ? 'a' : 'd';
                $this->orderdir = $tecmd["orderdir"];
            }
            else {
                $tecmd["orderdir"] = 'a';
                $tecmd["orderby"] = array ($orderby => 1);
            }
        }
        else {
            $orderby = $tecmd["orderby"];
            if (is_array ($orderby)) {
                $orderby = key ($orderby);
                $this->orderby = $orderby;
                $this->orderdir = $tecmd["orderdir"];
            }
            else {
                $this->orderby = $this->view["orderby"];
                $this->orderdir = $this->view["orderdir"];
            }
        }
        
        // update EDIT
        if ($this->cmd["show_new"]) {
            unset($tecmd["edit"]);
            unset($this->cmd["edit"]);
        }
        $edit = $this->cmd["edit"];
        if (is_array ($edit)) {
            reset ($edit);
            $edit_key = key ($edit);
            $tecmd["edit"] = $edit;
        }
        else if (is_array ($tecmd["edit"])) 
            $this->cmd["edit"] = $tecmd["edit"];
    }    

    // -----------------------------------------------------------------------------------
    
    // shows one table form    
    function view ($where = "1") {
        global $db;
    
        $this->SetViewDefaults();    
        $where = $this->GetWhere ($where);
        
        $db->query ("SELECT COUNT(*) AS mycount FROM ".$this->getSelectFrom()." WHERE ".$where);
        $db->next_record();        
        $rowcount = $db->f("mycount");

        // scroller stuff                                           
        $scrname = "te_".$this->viewID;
        global $$scrname;
        $scroll = $$scrname;
        if (is_object ($this->sess))
            $scrurl = $this->sess->url($GLOBALS[PHP_SELF]."?set_tview=".$this->gotoview2()."&")."&";

        if (is_object($scroll)) {
            $scroll->metapage = $this->view["listlen"];
            $scroll->countPages ($rowcount);
            $scroll->updateScr($scrurl);
        }
        else {
            $$scrname = new scroller($scrname, $scrurl);
            $scroll = $$scrname;
            if (is_object ($this->sess))
                $this->sess->register ($scrname);
            $scroll->metapage = $this->view["listlen"];
            $scroll->countPages ($rowcount);
        }
        $$scrname = $scroll;

        if ($this->orderby) 
            $orderby = " ORDER BY ".$this->orderby.($this->orderdir == 'd' ? " DESC" : "");
        
        reset ($this->cols);
        while (list ($cname,$cprop) = each ($this->cols)) {
            $col = "";
            if ($this->view["join"])
                $col = $cprop["table"].".";
            $col .= $cprop["field"];
            if ($cprop["field"] != $cname)
                $col .= " ".$cname;
            $collist[] = $col;
        }
            
        $db->query (
             " SELECT ".join(",",$collist)
            ." FROM ".$this->getSelectFrom()
            ." WHERE ".$where
            . $orderby
            ." LIMIT ".($scroll->current-1)*$scroll->metapage.",".$scroll->metapage);
            
        $record_count = $db->num_rows();

        if (!$record_count) 
            $no_item = !$this->cmd["search"]["value"];
        else $no_item = false;

        if ($this->view["help"]) {
            echo '<table border="0" cellspacing="0" cellpadding="5"><tr><td class="te_help">'
                .$this->view["help"]
                .'</td></tr></table><br>';
        }
        
        if (!$record_count && !$this->show_new) {
            if ($no_item)
                echo "<span class=te_no_item_msg>".$this->view["messages"]["no_item"]."</span>";
            else echo "<span class=te_no_item_msg>"._m("No record matches your search condition.")."</span>";
        }
            
        if ($record_count || $this->show_new) {      

            if ($this->view["search"] && !$no_item)
                $this->ShowSearchRow ();       
        
            $fnname = "prooveFields_".$this->viewID;
            $this->ShowProoveFields ($fnname);

            $formname = "tv_".$this->viewID;
            if ($this->type == "browse") {
                echo "<TABLE ".$this->view["attrs"]["table"].">";
                $this->showBrowseHeader ($record_count);
                echo "<FORM name='$formname' method=post action='".$this->getAction($this->gotoview2())."'>\n";
            }

            while ($db->next_record()) {
                $records[] = $db->Record;
                $all_keys[] = GetKeyFromRecord (
                    $this->primary_aliases[$this->view["table"]], $this->cols, $db->Record);
            }
            // if $show_new is enabled, show empty record as last one
            if ($this->show_new) {
                $records[] = "new";
                $all_keys[] = $GLOBALS[new_key];
            }

            reset ($records);
            reset ($all_keys);
            $irow = 0;
            while (list (, $record) = each ($records)) {                
                $new_record = $record == "new";
                list (,$key) = each ($all_keys);
                // row number helps to use different CSS styles for even and odd rows
                $irow ++;
                // show new record in row 1 color always
                if ($new_record) $irow = 1;
    
                if ($this->type == "browse") {
                    if ($new_record && count ($records) > 1)
                        $this->showBrowseFooter ($formname, $all_keys, $record_count, $scroll);                    
                    echo "<TR>";
                    $this->ShowButtons ($new_record, $key, $fnname, $formname, $irow, "left");
                }
                else {
                    $formname = "tv_".$this->viewID."_".$key;
                    echo "<TABLE ".$this->view["attrs"]["table"].">";
                    echo "<FORM name='$formname' method=post action='".$this->action."'>\n";
                }
                                            
                $this->ShowColumnValues ($record, $new_record, $key, $irow);
                
                if ($this->type == "browse") {
                    //$this->ShowButtons ($new_record, $key, $fnname, $formname, $irow, "left");
                    echo "</TR>";
                }
                else {
                    echo "<TR><TD align=center colspan=100>";
                    $this->ShowButtons ($new_record, $key, $fnname, $formname, $irow, "down", $all_keys, $record_count);                    
                    echo "</TD></TR></FORM></TABLE>";
                    if (is_array ($this->view["children"]) && !$new_record) 
                        $err = $this->ShowChildren($record);
                }
            }
            if ($this->type == "browse" && !$this->show_new)
                $this->showBrowseFooter ($formname, $all_keys, $record_count, $scroll); 
            echo "</TABLE>";
        }
        else if ($this->type == "browse") {
            $this->ShowButtons (false, "", "", "", 0, "down", array (), $record_count);        
            // scroller
            if ($scroll->pageCount() > 1) {
                echo "<P align=\"center\">";
            	$scroll->pnavbar();
                echo "</P>";        
            }
        }
        
        return $err;
    }        

    // -----------------------------------------------------------------------------------
    
    function showBrowseHeader ($record_count) {
        echo "<TR>";
        $header = "<TD colspan=".count ($this->view["buttons_left"]).">&nbsp;</TD>";
        /*
        reset ($this->view["buttons_left"]);
        while (list ($button, $use) = each ($this->view["buttons_left"])) {
            $bt = $this->ButtonsText (false);
            $bt = $bt[$button];
            $alt = $bt["alt"] ? $bt["alt"] : "&nbsp;";
            $header .= "<TD class=te_b_col_head align=center>".$alt."</TD>\n"; 
        }*/
    
        echo $header;
        $td = "<TD class=te_".substr($this->type,1,1)."_td>";
        reset ($this->cols);
        while (list ($colname,$column) = each ($this->cols)) {
            if ($column["view"]["type"] != "hide" && $column["view"]["type"] != "ignore") {
                $caption = $column["caption"];
                if ($column["required"] && substr ($column["caption"], -1) != "*"
                    && !$column["view"]["readonly"])
                    $caption .= " *";
                if ($record_count > 0) {
                     echo "$td<a href='".$this->getAction($this->gotoview2())."&cmd[".$this->viewID."]"
                         ."[orderby][$colname]=1'><span class=te_b_col_head>$caption</span>\n";
                     if ($this->orderby == $colname) {
                         echo "&nbsp;<img src='".$this->imagepath;
                         if ($this->orderdir == 'd')
                              echo "down.gif' alt='"._m("order ascending")."'";
                         else echo "up.gif' alt='"._m("order descending")."'";
                         echo " border=0>";                                                
                     }
                     echo "</a>";
                }
                else echo "$td<span class=te_b_col_head>$caption</span>\n";
                if ($column["hint"])
                    echo "<br>\n<span class=te_b_col_hint>".$column["hint"]."</span>";
                echo "</TD>\n";
            }
        }
        //echo $header;
        echo "</TR>";    
    }

    // -----------------------------------------------------------------------------------
    
    function showBrowseFooter ($formname, $all_keys, $record_count, $scroll) {
        echo "<TR><TD colspan=100><TABLE width=\"100%\">
            <TR><TD class=\"te_b_col_head\" width=\"100\" valign=top>";
        // icon explanation ("= update" etc.)
        if ($record_count) {    
            reset ($this->view["buttons_left"]);
            while (list ($button, $use) = each ($this->view["buttons_left"])) {
                $bt = $this->ButtonsText (false);
                $bt = $bt[$button];
                $alt = $bt["alt"] ? $bt["alt"] : "&nbsp;";
                $img = '<img border="0" src="'.$this->imagepath.$bt["img"].$big.'.gif" alt="'.$bt["alt"].'">';
                echo "<span class=te_button_text>$img = $alt</span><br>";
            }
        }
        else echo "&nbsp;";
        $space = $scroll->pageCount() > 1 ? 20 : 50;
        echo '</TD><TD width="'.$space.'">&nbsp;</TD>';
    
        // scroller 
        if ($scroll->pageCount() > 1) {
            echo "<TD>";
        	$scroll->pnavbar();
            echo "</TD><TD width=20>&nbsp;</TD>";
        }
        echo "<TD>";
        $this->ShowButtons (false, "", "", $formname, 0, "down", $all_keys, $record_count);
        // scroller
        echo "\n</TD></TR></TABLE></TD></TR></FORM>";
    }
    
    // -----------------------------------------------------------------------------------

    function getAction ($viewID) {
        return $this->action. (strstr($this->action,"?") ? "&" : "?") . "set_tview=".$viewID
            .($this->is_child ? "#" . $this->viewID : "");
    } 
        
    // -----------------------------------------------------------------------------------

    function SetViewDefaults () {
        if (!$this->view["type"]) $err[] = "Missing type.";
        if (!$this->view["table"]) $err[] = "Missing table.";
        if (is_array ($err)) {
            echo "Table Edit : error in Table View params for ".$this->viewID." :<BR>";
            echo join ("<BR>", $err);
            exit;
        }
    
        setDefault ($this->view["addrecord"], true);
        setDefault ($this->view["listlen"],   15);
        setDefault ($this->view["search"],    $this->type == "browse");
        setDefault ($this->view["messages"]["no_item"], _m("Nothing to be shown."));
        setDefault ($this->view["readonly"],  true);
                       
        $this->view["attrs"]["table"] .= " class=te_".substr($this->type,0,1)."_table ";
                
        $this->setDefaultButtons();
    }        
    
    // -----------------------------------------------------------------------------------

    function getSelectFrom () {
        $from = $this->view[table];    
        if (is_array ($this->view["join"])) {
            reset ($this->view["join"]);
            while (list ($tname, $tprop) = each ($this->view["join"])) {
                unset ($froms);
                reset ($tprop["joinfields"]);
                while (list ($thisfield, $joinfield) = each ($tprop["joinfields"])) 
                    $froms[] = $this->view["table"].".$thisfield=".$tname.".$joinfield";
                switch ($tprop["jointype"]) {
                case "1 to 1":
                case "n to 1": $from .= " INNER JOIN "; break;
                default: $from .= " error .. bad jointype .. "; break;
                }
                $from .= $tname." ON ".join(" AND ", $froms);
            }
        }                 
        return $from;
    }   
                
    // -----------------------------------------------------------------------------------    
    // sets $where and $this->show_new
    function GetWhere ($where)
    {
        //echo "edit ".$this->cmd["edit"]." show new ".$this->cmd["show_new"]." readonly ".$this->view["readonly"]." addrecord ".$this->view["addrecord"]." gotoview ".$this->view["gotoview"];
        $this->show_new = false;
        // create SQL SELECT        
        // finish processing of insert
        if ($this->cmd["insert"] && $this->view["where"]) {
            // value of $this->cmd["insert"] was changed in TableInsert to the SQL WHERE clause
            $this->view["where"] = "(".$this->view["where"].") OR ".$this->cmd["insert"];            
        }        
        // apply edit command only in Edit view
        if ($this->cmd["edit"] && $this->type == "edit") {
            $where = CreateWhereCondition (key ($this->cmd["edit"]), 
                $this->primary_aliases [$this->view["table"]], $this->cols, $this->view["table"]);
        }
        else if ($this->cmd["show_new"]) {
            $where = "0";
            $this->show_new = true;
        }
        else if (!$this->view["readonly"] && $this->view["addrecord"])
            $this->show_new = true;
        if (!isset($where)) 
            $where = "1";            
        
        // process search row
        $srch = &$this->cmd["search"];
        if ($srch["where"]) {
            // care user can't add another SQL command with this field
            $srch["where"] = str_replace (";", "", $srch["where"]);
            $where .= " AND (".stripslashes_magic($srch["where"]).") ";
            unset($srch["value"]);
            unset($srch["field"]);
        }
        else if ($srch["value"] || $srch["value"] == "0") {
            if (is_field_type_numerical ($this->cols[$srch["field"]]["type"]))
                 $where .= " AND $srch[field] = $srch[value] ";
            else $where .= " AND $srch[field] LIKE '%".addslashes_magic($srch[value])."%' ";
        }
        
        if (isset ($this->view["where"])) 
            $where .= " AND (".$this->view["where"].") ";
            
        echo "<!-- where_condition $where -->";
            
        return $where;
    }   

    // -----------------------------------------------------------------------------------

    function ShowSearchRow () 
    {
        $td = "<TD class=te_search_td>";
        $tdd = "</span></TD>";
        
        $formname = "search_".$this->viewID;
        $searchimage = "<a href='javascript:document.".$formname.".submit()'>"
            ."<img src='".$this->imagepath."search.gif' alt='"._m("Search")."' border=0></a>";

        echo "<FORM name='".$formname."' method=post action='".$this->getAction($this->gotoview2())."'>
              <TABLE ".$this->view["attrs"]["table_search"]." class=te_search_table><TR>$td"
            .$searchimage.'&nbsp;'
            ._m("search").": $tdd"
            ."$td";
        reset ($this->cols);
        while (list ($colname,$column) = each ($this->cols)) 
            if ($column["view"]["type"] != "hide" && $column["view"]["type"] != "ignore") 
                $options[$colname] = $column["caption"];
        $srch = $this->cmd["search"];
        FrmSelectEasy ("cmd[".$this->viewID."][search][field]", $options, $srch["field"]);
        echo "&nbsp;<INPUT name='cmd[".$this->viewID."][search][value]' type=text size=30 "
                ."value=\"".stripslashes_magic($srch[value])."\">$tdd"
            ."$td".$searchimage.$tdd."</TR>";
    /*  echo "<TR>$td"._m("Complex Search (SQL WHERE clause): ")."$tdd"  
            ."$td<INPUT name='cmd[".$this->viewID."][search][where]' type=text size=50 "
                ."value=\"".stripslashes_magic($srch[where])."\">$tdd"
            ."$td<INPUT type=submit name='go' value='"._m("Go")."'>$tdd</TR>"; */
        echo "</TABLE></FORM>";
    }

    // -----------------------------------------------------------------------------------
   
    // prints javascript for input validation 
    function ShowProoveFields ($fnname)
    {
        PrintJavaScript_Validate();
        echo "
        <script language=javascript>
        <!--
            function $fnname (formname, keys) {
                myform = document.forms[formname];
                for (ikey = 0; ikey < keys.length; ikey ++) {
                    name = 'val[' + keys [ikey] + ']';\n";                
        reset ($this->cols);
        while (list ($colname, $column) = each ($this->cols)) {
            $req = $column["required"];
            if (!$req) $req = "0";
            if ($column["validate"] || $req != 0) {
                if ($column["validate_min"] && $column["validate"] == "number") 
                     echo "if (!validate_number(myform[name+'[".$colname."]'],".$column["validate_min"].",".$column["validate_max"].", $req))
                        return false;\n";
                else echo "if (!validate(myform[name+'[".$colname."]'],\"".$column["validate"]."\", $req))
                    return false;\n";
            }
        }
        echo "
                }
                return true;
            }
        // -->
        </script>";
    }
    
    // -----------------------------------------------------------------------------------
    
    function ShowColumnValues ($record, $new_record, $key, $irow)
    {
        if ($this->type == "browse") 
            $td = "<TD class=te_b_row".($irow % 2 ? "1" : "2").">";
        else $td .= "<TD class=te_e_td>";
        reset ($this->cols);
        while (list ($alias,$column) = each ($this->cols)) {
            $cview = $column["view"];
            if ($new_record && $column["view_new_record"])
                $cview = $column["view_new_record"];
                
            $val = $new_record ? $column["default"] : $record[$alias];
            
            $visible = $cview["type"] != "ignore" && $cview["type"] != "hide";
            if ($visible && $this->type == "edit") {
                $caption = $column["caption"];
                if ($column["required"] && substr ($column["caption"], -1) != "*"
                    && !$cview["readonly"])
                    $caption .= " *";
                echo "<TR>$td<span class=te_e_col_head>".$caption."</span><br>\n";
                if ($column["hint"])
                    echo "<span class=\"te_e_col_hint\">".$column["hint"]."</span>";
                echo "</TD>\n";
			}        

            if ($cview["href_view"])
                $href_view = "<a href='".$this->getAction($cview["href_view"])
                    ."&cmd[".$cview["href_view"]."][edit]"
                    ."[".str_replace("\"","\\\"",$val)."]=1'>";

            if ($visible && $cview["href_view"] && $cview["readonly"]) 
                echo $href_view;
                    
            if ($visible)
                echo $td;        
            
            $name = str_replace ("\"", "\\\"", "val[$key][$alias]");
            // in tabledit_column.php3
            ColumnFunctions ($cview, $val, "show", $name);
        
            if ($visible) {
                if ($cview["href_view"]) {
                    if ($cview["readonly"]) 
                        echo "</a>\n";
                    else echo $href_view.
                        '<img border="0" src="'.$this->imagepath.'edit_big.gif" alt="'._m("edit").'">
                        </a>'."\n";
                }

                echo "</TD>\n";                
                if ($this->type == "edit") 
                    echo "</TR>";
            }
        }
    }

    // -----------------------------------------------------------------------------------

    // gotoview = edit in browse view; update in edit view
    function gotoview () {
        return $this->view["gotoview"] ? $this->view["gotoview"] : $this->viewID;
    }
    
    // gotoview2 = delete,update in browse view; search form
    function gotoview2() {
        return ($this->is_child && $this->view["gotoview"]) 
            ? $this->view["gotoview"]
            : $this->viewID;
    }
                
    // -----------------------------------------------------------------------------------
    
    function SetDefaultButtons ()
    {
        $bl = $this->view["buttons_left"];
        $bd = $this->view["buttons_down"];
        
        $gotoview = $this->view["gotoview"] && $this->view["gotoview"] != $this->gotoview2();
        
        // default buttons:
        
        if ($this->view["type"] == "edit") {
            if ($gotoview) {
                if (!$this->view["readonly"]) {
                    $bd["update"] = 1;
                    $bd["delete"] = 1;
                }
                $bd["cancel"] = 1;
            }
            else if (!$this->view["readonly"])
                $bd["update"] = 1;
        }
                
        // browse view        
                
        else {
            if ($this->view["readonly"]) {
                if ($gotoview) {
                    $bl["edit"] = true;
                    if (!$this->view["addrecord"])
                        $bd["add"] = true;
                }
            }
            else {
                $bd["update_all"] = true;
                if ($gotoview) {
                    $bd["add"] = true;
                    $bl["edit"] = true;
                }
                $bd["delete_all"] = true;
                $bl["delete_checkbox"] = true;
            }
                    
            if ($bd["update"] || $bd["delete"]) 
                echo $this->viewID.": You should not use bottom buttons 'update' or 'delete' in
                        browse view, use 'update_all' and 'delete_all' instead.";
        }
        
        setDefault ($this->view["buttons_left"], $bl);
        setDefault ($this->view["buttons_down"], $bd);
    }

    // -----------------------------------------------------------------------------------
    
    function ButtonsText ($new_record) {
        // "new" is label for new record, "new_name" is command for new record, 
        // "view" is view on which $this->cmd operates, "gotoview" is view which will be shown                
        $buttons_text["edit"] = array (
            "name" => "edit",
            "img" => $new_record ? "" : "edit",
            "alt" => _m("edit"),
            "view" => $this->gotoview(), 
            "gotoview" => $this->gotoview());
        $buttons_text["add"] = array (
            "name" => "add",
            "img" => "edit",
            "alt" => _m("add"),
            "view" => $this->viewID,
            "gotoview" => $this->gotoview());            
        $buttons_text["delete"] = array (
            "name" => "delete",
            "img" => $new_record ? "" : "delete",
            "alt" => _m("delete"),
            "view" => $this->viewID, 
            "gotoview" => $this->type == "browse" ? $this->gotoview2() : $this->gotoview());
            
        // SPECIAL: "delete_checkbox" becomes "add" on new records
        $buttons_text["delete_checkbox"] = array (
            "name" => $new_record ? "insert" : "delete_all",
            "img" => $new_record ? "ok" : "delete",
            "alt" => $new_record ? _m("insert") : _m("delete"),
            "checkbox" => !$new_record,
            "view" => $this->viewID,
            "gotoview" => $this->gotoview2());
        $buttons_text["delete_all"] = array (
            "name" => "run_delete_all",
            "img" => "delete",
            "alt" => _m("delete checked"),
            "view" => $this->viewID,
            "gotoview" => $this->gotoview2());
        $buttons_text["update"] = array (
            "name" => $new_record ? "insert" : "update",
            "img" => "ok",
            "alt" => $new_record ? _m("insert") : _m("update"),
            "view" => $this->viewID, 
            "gotoview" => $this->gotoview2());
        $buttons_text["update_all"] = array (
            "name" => "update_all",
            "img" => "ok",
            "alt" => _m("update all"),
            "view" => $this->viewID, 
            "gotoview" => $this->gotoview2());
        $buttons_text["cancel"] = array (
            "name" => "cancel",
            "img" => "exit",
            "alt" => _m("browse"),
            "view" => $this->viewID,
            "gotoview" => $this->gotoview());
        return $buttons_text;
    }    
    
    function ShowButtons ($new_record, $key, $fnname, $formname, $irow, $place="left", $all_keys="", $record_count=0) {                
        if (!is_array ($this->view["buttons_$place"])) 
            return;

        if ($place == "down")
            echo "<TABLE><TR>\n";
            
        reset ($this->view["buttons_$place"]);
        while (list ($button,$use) = each ($this->view["buttons_$place"])) {
            $bt = $this->ButtonsText ($new_record);
            $bt = $bt[$button];
            if (!$use || !$bt)
                continue;
            switch ($place) {
                case "left": echo "<TD class=te_b_row".($irow % 2 ? "1" : "2").">"; break;
                case "down" :echo "<TD align=center width=50>"; break;
            }
            $this->ShowButton ($bt, $new_record, $key, $fnname, $formname, $place, $all_keys, $record_count);
            echo "</td>\n";
        }
        if ($place == "down")
            echo "</TR></TABLE>\n";
    }

    function ShowButton ($bt, $new_record, $key, $fnname, $formname, $place, $all_keys, $record_count) {               
        if ($place != "left")
            $big = "_big";
                
        switch ($bt["name"]) {
            case "add":
                $url = $this->getAction($bt[gotoview])."&cmd[$bt[gotoview]][show_new]=1";
                break;
            case "delete":
                $url = $this->getAction($bt[gotoview])."&cmd[$bt[view]][$bt[name]][$key]=1";
                $url = "javascript:confirmDelete (\"".$url."\");";
                break;
            case "cancel":
                $url = $this->getAction($bt[gotoview]);
                break;
            case "edit":
                $url = $this->getAction($bt[gotoview])."&cmd[$bt[view]][$bt[name]][$key]=1";
                break;
            case "run_delete_all":
                $hidden = "cmd[".$this->viewID."][run_delete_all]";
                $url = "javascript: if (confirm (\""._m("Are you sure you want to permanently DELETE all the checked records?")."\"))\n exec_commit (\"$formname\",\"$hidden\");";
                echo "<INPUT type=hidden name='$hidden' value=0>\n";
                break;
            case "insert":
            case "update":
                $hidden = "cmd[".$this->viewID."][update][$key]";
                $url = "javascript:if ($fnname (\"$formname\",new Array(\"$key\")))\n exec_commit (\"$formname\",\"$hidden\");";
                echo "<INPUT type=hidden name='$hidden' value=0>\n";
                break;
            case "update_all":
                 // javascript array of all keys for form validation
                $js_all_keys = 'new Array ("'.join ('","', $all_keys).'")';          
                $hidden = "cmd[".$this->viewID."][update_all]";
                $url = "javascript:if ($fnname (\"$formname\",$js_all_keys))\n exec_commit (\"$formname\",\"$hidden\");";
                echo "<INPUT type=hidden name='$hidden' value=0>\n";
                break;
            default:
                $url = "";
                break;
        }

        if ($bt["img"])
             $img = '<img border="0" src="'.$this->imagepath.$bt["img"].$big.'.gif" alt="'.$bt["alt"].'">';
        else $img = "";
    
        if ($this->type == "browse" && $place == "down" && $record_count == 0 && $bt["name"] != "add")
            $text = "";
        //if ($bl["delete_checkbox"] && $bl["update"] && $new_record) 
            //echo "";
        else if ($bt["checkbox"]) 
            $text = "$img<INPUT TYPE=checkbox NAME=cmd[$bt[view]][$bt[name]][$key]>\n";
        else if ($img) $text = "<a href='$url'>$img</a>";                
        else $text = "";   
        
        echo $text ? $text : "&nbsp;";
        // show the text label for bottom buttons and for insert                     
        if ($text && ($place == "down" || $new_record)) 
            echo "<br><a href='$url'><span class=te_button_text>".$bt["alt"]."</span></a>"; 
    }
    
    // -----------------------------------------------------------------------------------
    
    /** shows children forms
    *
    * @param $key - identification of the parent row
    */
    function ShowChildren ($record) {
        reset ($this->view["children"]);
        while (list ($chview, $child) = each ($this->view["children"])) {       
            $fn = $this->getTableViewsFn;
            $chtv = $fn ($chview);

            SetColumnTypes ($chtv["fields"], $primary_aliases, $chtv["table"], 
                $chtv["readonly"], $chtv["primary"]);

            $varset = new CVarset;

            reset ($child["join"]);
            while (list ($masterf,$childf) = each ($child["join"])) {                
                reset ($chtv["fields"]);
                while (list ($alias, $cprop) = each ($chtv["fields"])) 
                    if ($cprop["field"] == $childf)
                        break;
                reset ($this->cols);
                while (list ($malias, $mcprop) = each ($this->cols)) 
                    if ($mcprop["field"] == $masterf)
                        break;
                $cprop = &$chtv["fields"][$alias];
                if (!$cprop) { echo "Error in ShowChildren."; exit; }
                $cprop["default"] = $record[$malias];
                $varset->addkey ($cprop["field"], "text", $record[$malias]);
            }
            
            $where = $varset->makeWHERE();
            
            echo "<br>
                <a name='$chview'>
                <span class=te_child_header>".$child["header"]."</span><br>";
            $chtv["gotoview"] = $this->viewID;
            $childte = new tabledit ($chview, $this->action, 
                $this->all_cmd[$chview], $chtv, $this->imagepath, $this->sess, 
                $this->getTableViewsFn, true);
            $err = $childte->view($where);
            if ($err) return $err;
        }
    }
}
// END of class tabledit

?>
