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

/* Author: Jakub Adamek

   This script shows the top menu (navigation bar) and second level menu (left bar)
   by the function showMenu ($aamenus, $activeMain, $activeSubmenu = "", $showMain = 1, $showSub = 1).
*/

if (!defined ("AA_MENU_INCLUDED"))
    define("AA_MENU_INCLUDED","1");
else return;


// ----------------------------------------------------------------------------------------
/* creates a JavaScript variable modulesOptions, which allows to create another Module selectbox
    without reprinting all the options */

function PrintModuleSelection() {
  global $slice_id, $g_modules, $sess, $PHP_SELF, $db, $MODULES;

  if( is_array($g_modules) AND (count($g_modules) > 1) ) {

    // create the modulesOptions content:
    $permitted = GetUserSlices();
    if ($GLOBALS[debugpermissions]) huhl("Slice permissions=",$permissions);
    if ($permitted != "all") {
        reset ($permitted);
        while (list ($perm_slice_id) = each ($permitted))
            $slice_ids .= ",'" . q_pack_id ($perm_slice_id) ."'";
        $slice_ids = "WHERE module.id IN (".substr($slice_ids,1).") ";
    }
    echo "
    <SCRIPT language=JAVASCRIPT><!--
        var modulesOptions = ''\n";
    $db->query ("SELECT module.id, module.type, slice.type AS slice_type, module.name
        FROM module LEFT JOIN slice ON module.id=slice.id "
        .$slice_ids);

    $module_types = array (
        "Alerts" => array (5, _m("Alerts")),
        "J" => array (6, _m("Jump inside control panel")),
        "Links" => array (7, _m("Links")),
        "A" => array (8, _m("MySQL Auth (old version)")),
        "P" => array (9, _m("Polls")),
        "W" => array (10,_m("Site")),
        "S" => array (0, _m("Slice")),
        "RM"=> array (1, _m("Reader Management Slice")));

    while ($db->next_record()) {
        if ($db->f("type") == "S")
            $order = $db->f("slice_type") == "ReaderManagement" ? 1 : 0;
        else $order = $module_types[$db->f("type")][0];
        //if (! $module_types[$db->f("type")]) { echo $db->f("type")."!!"; exit; }
        $modules[$order][$db->f("id")] = $db->f("name");
    }
    // count($modules) - count of module types
    $display_modtypes = ( count($modules) > 1 ); // display types in selectbox?

    $option_begin = "\t+'<option value=\"";
    ksort ($modules);
    reset ($modules);
    while (list ($order, $mods) = each ($modules)) {  // for all module types
        if( $display_modtypes ) {
            foreach ($module_types as $module_type)
                if ($module_type[0] == $order)
                     echo $option_begin . '" class="sel_title">*** '.$module_type[1]." ***'\n";
        }
        asort ($mods);
        reset ($mods);
        while (list ($id, $name) = each ($mods)) {
            echo $option_begin . htmlspecialchars(unpack_id ($id))."\"";
            if ($slice_id == unpack_id ($id))
                echo " selected";
            echo ">". str_replace("'","`",safe($name)) . "'\n";
        }
    }

    if( !$slice_id )   // new slice
      echo "\t+'<option value=\"new\" selected>". _m("New slice") + "'";
    echo ";
        document.write('<select name=slice_id onChange=\\'if (this.options[this.selectedIndex].value != \"\") document.location=\"" .con_url($sess->url($PHP_SELF),"change_id=")."\"+this.options[this.selectedIndex].value\\'>');
        document.write(modulesOptions);
        document.write('</select>');
    //-->
    </SCRIPT>\n";
} else
    echo "&nbsp;";
}

// ----------------------------------------------------------------------------------------
//                                SHOW MENU

/* PARAMS: $smmenus -- array with menu information, see menu.php3 for an example
           $activeMain -- selected item in main (top) menu
           $activeSubmenu -- selected item in sub (left) menu
           $showMain -- show the main menu (top navigation bar) ?
           $showSub -- show the submenu (left navigation bar) ?
*/
function showMenu ($smmenus, $activeMain, $activeSubmenu = "", $showMain = 1, $showSub = 1)
{
    global $slice_id, $AA_INSTAL_PATH, $r_slice_headline, $useOnLoad, $sess, $db;
    global $menu_function;
    global $debug;
    trace("+","showMenu",$smmenus);
    #huhsess("Session Variables");
    // load the main AA menu (see menu.php3)
    if ($smmenus == "aamenus") {
        // Menu functions are defined in include/menu.php3 or modules/*/menu.php3
        // We need to call last defined menu function (when switching to another
        // module). This solution is not so nice, but it removes get_aamenus()
        // redeclaration error. We probably change menus to object in the future
        $smmenus = $menu_function();
    }

    // HACKISH: aaadmin menu needs always the _news_ lang file, even in other than slice modules
    if ($activeMain == "aaadmin")
        bind_mgettext_domain ($GLOBALS["AA_INC_PATH"]."lang/".get_mgettext_lang()."_news_lang.php3");

    trace("=","","useOnLoad=".$useOnLoad);
    if( $useOnLoad )
        echo '<body OnLoad="InitPage()" background="'. COLOR_BACKGROUND .'">';
    else
        echo '<body background="'. COLOR_BACKGROUND .'">';

    if ($debug) { echo "<p><font color=purple>showMenu:activeMain=$activeMain;activeSubmenu=$activeSubmenu;showMain=$showMain;showSub=$showSub:</font></p>";  }

    if( !$slice_id )
        $r_slice_headline = _m("New slice");

    $nb_logo = '<a href="'. $AA_INSTAL_PATH .'"><img src="'.$AA_INSTAL_PATH.'images/action.gif" width="106" height="73" border="0" alt="'. _m("APC Action Applications") .'"></a>';

    echo "<TABLE border=0 cellspacing=0 cellpadding=0 width='100%'><TR>";

    trace("=","","showMain=".$showMain);
    if ($showMain) {
        // Show the Alerts and Reader management images in the header
        if ($GLOBALS["g_modules"][$slice_id]["type"] == "Alerts") {
            $title_img =
            "<a href=\"".$AA_INSTAL_PATH."doc/reader.html\">
            <img border=0 src=\"".$AA_INSTAL_PATH."images/alerts.gif\"
                alt=\""._m("Alerts")."\"></a>";
        }
        else if ($GLOBALS["g_modules"][$slice_id]["type"] == "S") {
            $slice_info = GetSliceInfo ($slice_id);
            if ($slice_info ["type"] == "ReaderManagement")
                $title_img =
                "<a href=\"".$AA_INSTAL_PATH."doc/reader.html\">
                 <img border=0 src=\"".$AA_INSTAL_PATH."images/readers.gif\"
                 alt=\""._m("Reader management")."\"></a>";
        }

        echo "
        <TD colspan=2>
        <TABLE border=0 cellpadding=0 cellspacing=0 width='100%'>
            <TR><TD><IMG src=\"$AA_INSTAL_PATH"."images/spacer.gif\" width=122 height=1></TD>
                <TD width=99%><IMG src=\"$AA_INSTAL_PATH"."images/spacer.gif\" height=1></TD><TD></TD>
            </TR>
            <TR><TD rowspan=2 align=center class=nblogo>$nb_logo</td>
                <TD colspan=2 height=43 align=center valign=middle class=slicehead>\n";

        if ($title_img)
            echo "<table><tr><td>$title_img&nbsp;</td>
                <td width=\"0%\" class=slicehead>";
        echo $smmenus[$activeMain]["title"]."  -  $r_slice_headline";
        if ($title_img)
            echo "</td><td>&nbsp;$title_img</td></tr></table>";
        echo "
        </TD>
            </TR>
            <form name=nbform enctype=\"multipart/form-data\" method=post
                action=\"". $sess->url($PHP_SELF) ."\">
            <TR><td align=center class=navbar>";
        $first = true;
        trace("=","","loop");
        reset ($smmenus);
        while (list ($aamenu,$aamenuprop) = each ($smmenus)) {
            if ($aamenuprop["level"] == "main") {
                if ($first) $first = false;
                else echo " | ";
                if (!isset ($aamenuprop["cond"])) $aamenuprop["cond"] = 1;
                if ($aamenu == $activeMain)
                    echo "<span class=nbactive>$aamenuprop[label]</span>\n";
                else if ($slice_id && $aamenuprop["cond"]) {
                     $href = $aamenuprop["exact_href"];
                     if (!$href) $href = get_aa_url($aamenuprop["href"]);
                     $href = con_url ($href, "slice_id=$slice_id");
                     echo "<a href=\"$href\">"
                         ."<span class=nbenable>$aamenuprop[label]</span></a>\n";
                }
                else echo "<span class=nbdisable>$aamenuprop[label]</span>\n";
            }
        }

        echo "</td><td align=center class=navbar align=right> &nbsp; ";
        if( is_array($g_modules) AND (count($g_modules) > 1) )
            echo _m("Switch to:") ."&nbsp; ";
        echo "\n";
        PrintModuleSelection();
        echo "</TD></TR></form></TABLE>
        </TD></TR><TR>";
    }

    if ($showSub) {
        $submenu = $smmenus[$activeMain]["submenu"];
        if ($submenu) {
            echo "<TD valign=top>";
            showSubmenu ($smmenus[$submenu], $activeSubmenu);
            echo "</TD>";
        }
    }
    echo "
        <TD align=left valign=top width='99%'>
        <TABLE border=0 cellspacing=0 cellpadding=10 width='100%'><TR><TD align=left>\n";
    trace("-");
}

function showSubMenuRows( $aamenuitems, $active ) {
    global $AA_INSTAL_PATH, $slice_id,$debug;

    if ( !isset($aamenuitems) OR !is_array($aamenuitems) )
       return;

    reset ($aamenuitems);
    while (list ($itemshow, $item) = each ($aamenuitems)) {
        if (substr($itemshow,0,4) == "text") {
            echo "<tr><td>$item</td></tr>\n";
        } elseif (substr($itemshow,0,6) == "header") {
            echo '<tr><td>&nbsp;</td></tr>
                  <tr><td><img src="'.$AA_INSTAL_PATH.'images/black.gif" width=120 height=1></td></tr>
                  <tr><td class=leftmenu>'.$item.'</td></tr>
                  <tr><td><img src="'.$AA_INSTAL_PATH.'images/black.gif" width=120 height=1></td></tr>'."\n";
        } elseif (substr($itemshow,0,4) == "line") {
            echo '<tr><td><img src="'.$AA_INSTAL_PATH.'images/black.gif" width=120 height=1></td></tr>'."\n";
        } elseif ( $item["function"] ) {
            // call some function to get menu items
            // it is better mainly for left submenus for which we need
            // database access - if we use function, it is called only if
            // submenu should be displayed.
            $function = $item["function"];
            showSubMenuRows( $function( $item["func_param"] ), $active );
        } else {
            echo '<tr><td width="122" valign="TOP">&nbsp;';
            if (!isset ($item["cond"])) $item["cond"] = 1;
            if ($itemshow == $active) {
                echo "<span class=leftmenua>".$item["label"]."</span>\n";
            } elseif (($slice_id || $item["show_always"]) && $item["cond"]) {
                $href = ($item["exact_href"] ?
                              $item["exact_href"] : get_aa_url($item["href"]));
                if ($slice_id && !$item["no_slice_id"])
                    $href = con_url ($href, "slice_id=$slice_id");
                if ($item['js']) {
                    $item['js'] = str_replace("{href}",$href,$item['js']);
                    $item['js'] = str_replace("{exact_href}",$href,$item['js']);
                    $href = "javascript:".$item['js'];
                }
                echo '<a href="'.$href.'" class=leftmenuy>'.$item["label"]."</a>\n";
            } else {
                echo "<span class=leftmenun>".$item["label"]."</span>\n";
            }
            echo "</td></tr>\n";
        }
    }
}

// ----------------------------------------------------------------------------------------
//                                SHOW SUBMENU

function showSubmenu (&$aamenu, $active)
{
    global $debug;
    if ($debug) { echo "<p><font color=purple>showSubmenu:active=$active</font></p>\n"; }
    echo '<table width="122" border="0" cellspacing="0" bgcolor="'.COLOR_TABBG.'" cellpadding="1" align="LEFT" class="leftmenu">'."\n";

    $aamenuitems = $aamenu["items"];
    showSubMenuRows( $aamenuitems, $active );

    echo '<tr><td class=leftmenu>&nbsp;</td></tr>
          <tr><td class=leftmenu height="'.$aamenu["bottom_td"].'">&nbsp;</td></tr>
          <tr><td class=copymsg ><small>'. _m("Copyright (C) 2001 the <a href=\"http://www.apc.org\">Association for Progressive Communications (APC)</a>") .'</small></td></tr>
          </table>'."\n";
}

function CreateMetuItem( $label, $href, $cond = true ) {
    return array( 'label' => $label, 'href' => $href, 'cond' => $cond );
}
