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
// DOCUMENTATION: doc/tableview.html

// Settings for Alerts-related table views 

require_once "util.php3";
require_once $GLOBALS["AA_INC_PATH"]."tv_email.php3";
require_once $GLOBALS["AA_INC_PATH"]."perm_core.php3";

/** see class tabledit :: var $getTableViewsFn for an explanation of the parameters */                        
function GetAlertsTableView ($viewID, $processForm = false) {        

    if ($viewID == "email_edit") {
        $tableview = GetEmailTableView ($viewID);
        $tableview["mainmenu"] = "admin";
        return $tableview;
    }

    if ($viewID == "email") {
        $tableview = GetEmailTableView ($viewID);
        $tableview["mainmenu"] = "admin";
        $tableview["submenu"] = "email";
        return $tableview;
    }
	
    global $auth, $slice_id, $db, $collectionid;
    global $attrs_edit, $attrs_browse, $format, $langs;
    
    /* ------------------------------------------------------------------------------------
       au -- browse Alerts users
    */       

    global $sess;         
    if ($viewID == "aucf") {
        global $collectionid;
        $db->query("SELECT AF.description, AF.id FROM alerts_collection_filter ACF
            INNER JOIN alerts_filter AF ON AF.id = ACF.filterid
            WHERE collectionid='$collectionid'");
        if ($db->num_rows()) {
            while ($db->next_record()) 
                $collection_filters [$db->f("id")] = $db->f("description");
            $no_filters = false;
        }
        else {
            $collection_filters[-1] = _m("No filters found for this collection.");
            $no_filters = true;
        }
        return  array (
        "table" => "alerts_user_collection_filter",
        "type" => "browse",
        "readonly" => false, //$no_filters,
        //"buttons_down" => array (), //"update_all" => 1, "delete_all" => 1),
        "addrecord" => true,
        "gotoview" => "au_edit",
        "cond" => 0,
        "orderby" => "myindex",
        "search" => false,
        "messages" => array (
            "error_insert" => _m("This filter has already been added.")),
        "fields" => array (
            "collectionid" => array (
                "view" => array ("type"=>"hide"),
                "default" => $collectionid),
            "filterid" => array (
                "caption" => _m("filter"),
                "view" => array ("type"=>"select","source"=>$collection_filters)),
            "myindex" => array (
                "default" => 1,
                "caption" => _m("order"))),
        "attrs" => $attrs_browse,
		"help" => _m("If 'all filters' is set to 'yes', no filters should be assigned to this user."));
    }
         
    // ------------------------------------------------------------------------------------

    if ($viewID == "acf") {
        global $collectionid;
        $db->query("SELECT AF.description, AF.id FROM alerts_filter AF");
        if ($db->num_rows()) {
            while ($db->next_record()) 
                $collection_filters [$db->f("id")] = $db->f("description");
            $no_filters = false;
        }
        else {
            $collection_filters[-1] = _m("No filters defined. You must define some.");
            $no_filters = true;
        }

        // filter select box        
        $SQL = "SELECT slice.name, DF.description as fdesc, DF.id AS filterid, 
                       view.id AS view_id, view.type as view_type, view.slice_id FROM
                        slice INNER JOIN
                        view ON slice.id = view.slice_id INNER JOIN
                        alerts_filter DF ON DF.vid = view.id";
        $SQL .= " ORDER BY slice.name, DF.description";  
        $db->tquery ($SQL);
        global $sess;
        $myslices = GetUsersSlices( $auth->auth["uid"] );    
        while ($db->next_record()) {
            $txt = HTMLSpecialChars ($db->f("fdesc"));
            if (IsSuperadmin() || strchr ($myslices [unpack_id128($db->f("slice_id"))], PS_FULLTEXT)) {
                $new_filters[$db->f("filterid")] = $txt;
                $txt =            
                    "<a href='".$sess->url("tabledit.php3"
                    ."?change_id=".unpack_id128($db->f("slice_id"))
                    ."&change_page=se_view.php3"
                    ."&change_params[view_id]=".$db->f("view_id")
                    ."&change_params[view_type]=".$db->f("view_type"))
                    ."'>".$txt."</a>";
            }
            $filters[$db->f("filterid")] = $txt;
        }        

        return  array (
        "table" => "alerts_collection_filter",
        "type" => "browse",
        "readonly" => false, //$no_filters,
        "buttons_left" => array ("delete_checkbox" => 1),
        "buttons_down" => array ("update_all" => 1, "delete_all" => 1),
        "addrecord" => is_array ($new_filters),
        "gotoview" => "au_edit",
        "mainmenu" => "admin",
        "submenu" => "design",
        "search" => false,
        "caption" => _m("Filters"),
        "title" => _m("Filters"),
        "cond" => IfSlPerm(PS_FULLTEXT),
        "orderby" => "myindex",
        "where" => "collectionid = '$collectionid'",
        "fields" => array (
            "collectionid" => array (
                "view" => array ("type" => "hide"),
                "default" => $collectionid),
            "filterid" => array (
				"caption" => _m("filter"),
                "view" => array (
                    "readonly" => true,
                    "type" => "select",
                    "html" => true,
                    "source" => $filters),
                "view_new_record" => array (
                    "type" => "select",
                    "source" => $new_filters)),
            "myindex" => array (
                "caption" => _m("order"),
                "default" => 1)),
        "attrs" => $attrs_browse);
    }
         
    /* ------------------------------------------------------------------------------------
       modedit 
       Alerts collection setting 
       
       modedit_insert
       Processing form data on Alerts module addition.
    */    
    
    if ($viewID == "modedit") {
        $fix_howoften_options = get_howoften_options();
        $fix_howoften_options[""] = _m("don't fix");
        global $LANGUAGE_NAMES;
        reset ($LANGUAGE_NAMES);
        while (list ($l, $langname) = each ($LANGUAGE_NAMES)) 
            $alertslangs[$l."_alerts_lang.php3"] = $langname;
        return array (
        "table" => "module",
        "join" => array (
            "alerts_collection" => array (
                "joinfields" => array (
                    "id" => "moduleid"),
                "jointype" => "1 to 1")),                    
        "type" => "edit",
        "readonly" => false,
        "cond" => 1,
        "title" => _m("Alerts Collection"), 
        "caption" => _m("Alerts Collection"),
        "mainmenu" => "admin",
        "submenu" => "settings",
        "triggers" => array ( 
            "AfterInsert" => "AlertsModeditAfterInsert"),
        "fields" => array (
            "_alerts_collection_id_" => array (
                "table" => "alerts_collection",
                "field" => "id",
                "default" => new_collection_id(),
                "view" => array ("readonly" => true),
                "caption" => _m("collection ID")),
/*            "sliceid" => array (
                "table" => "alerts_collection",
                "view" => array ("type"=>"select", "source"=>getReaderManagementSlices()),
                "caption" => _m("reader management slice")),*/
            "name" => array (
                "view" => array ("type" => "text", "size" => array("cols"=>60)),
				"caption" => _m("name"),
                "required" => true),
            "slice_url" => array ("caption" => _m("form URL"), "required"=>false),
            "lang_file" => array (
                "caption" => _m("language"),
                "view" => array ("type"=>"select","source"=>$alertslangs)),
            "deleted" => array (
                "caption" => _m("deleted"),
                "hint" => _m("Use AA Admin / Delete<br>to delete permanently"),
                "view" => array ("type"=>"checkbox")),
            "emailid_welcome" => array (
                "table" => "alerts_collection",
                "caption" => _m("welcome email"),
                "view" => ($processForm ? "" : array (
                    "type"=>"select",
                    "href_view" => "email_edit",
                    "source"=>GetUserEmails("alerts welcome")))),
            "emailid_alert" => array (
                "table" => "alerts_collection",
                "caption" => _m("alert email"),
                "view" => ($processForm ? "" : array (
                    "type"=>"select",
                    "href_view" => "email_edit",
                    "source"=>GetUserEmails("alerts alert")))),
            "emailid_access" => array (
                "table" => "alerts_collection",
                "caption" => _m("single usage access email"),
                "view" => ($processForm ? "" : array (
                    "type"=>"select",
                    "href_view" => "email_edit",
                    "source"=>GetUserEmails("alerts access")))),
            "type" => array ("default" => "Alerts", "view" => array ("type"=>"hide")),
            "id" => array (
                "default" => pack_id128(new_id()),                 
                "view" => array("type"=>"text", "unpacked" => true, "readonly" => true)),
            "created_at" => array (
                "caption" => _m("created at"),
                "default"=>time(), 
                "view"=>array (
                    "type"=>"date",
                    "format" => "j.m.y",
                    "readonly" => 1)),
            "created_by" => array (
                "caption" => _m("created by"),
                "default"=>$auth->auth["uid"], 
                "view"=>array(
                    "type"=>"text",
                    "readonly" => 1))
        ),    
        "attrs" => $attrs_edit,
		"messages" => array (
	        "no_item" => _m("You don't have permissions to edit any collection or no collection exists.")
		));        
    }       
    
    /* ------------------------------------------------------------------------------------
       alerts_admin
    */
    if ($viewID == "alerts_admin") {
        $db->query("SELECT * FROM alerts_admin");
        if ($db->num_rows() == 0)
            $db->query("INSERT INTO alerts_admin (mail_confirm, delete_not_confirmed) VALUES (0,0)");
        return array (
        "table" => "alerts_admin",
        "caption" => _m("Alerts Admin"),
        "title" => _m("Alerts Admin"),
        "mainmenu" => "sliceadmin",
        "submenu" => "te_alerts_admin",
        "buttons_down" => array ("update"=>1),
        "attrs" => array ("table"=>"border=1 cellpadding=3 cellspacing=0 bgcolor='".COLOR_TABBG."'"),    
        "type" => "edit",
        "readonly" => false,
        "addrecord" => false,
        "cond" => IsSuperadmin(),
        "newrecord" => false,
        "fields" => array (
            "mail_confirm" => array (
                "caption" => _m("confirm mail"),
                "hint" => _m("number of days, 0 = off"),
                "view" => array (
                    "type" => "text",
                    "size" => array ("cols" => 3)),
                "validate" => "number"),
            "delete_not_confirmed" => array (
                "caption" => _m("delete not confirmed"),
                "hint" => _m("number of days, 0 = off"),
                "view" => array (
                    "type" => "text",
                    "size" => array ("cols" => 3)),
                "validate" => "number"),
            "last_mail_confirm" => array (
                "caption" => _m ("last confirm mail"),
                "view" => array (
                    "readonly" => true,
                    "type" => "date",
                    "size" => array ("cols" => 6), 
                    "format" => "j.m.y G:i")),
            "last_delete" => array (
                "caption" => _m ("last delete not confirmed"),
                "view" => array (
                    "readonly" => true,
                    "type" => "date",
                    "size" => array ("cols" => 6), 
                    "format" => "j.m.y G:i"))),
        "help" => _m (
            "This table sets handling of not confirmed users. It's accessible only
            to superadmins.
            You can delete not confirmed users after a number of days and / or send them an email 
            demanding them to do confirmation
            after a smaller number of days. To switch either of the actions off,
            set number of days to 0. The two last fields are for your information only.<br>
            <br>
            To run the script, you must have cron set up with a row running
            misc/alerts/admin_mails.php3.<br>
            For more information, see <a href='http://apc-aa.sourceforge.net/faq/#1389'>the FAQ</a>."));
    }            
} // end of GetTableView
            
// ----------------------------------------------------------------------------------        

function FindAlertsFilterPermissions() {   
    global $auth, $_filter_permissions;
    $db = new DB_AA;
    
    // work only once
    if (isset ($_filter_permissions))
        return $_filter_permissions;
        
    if (IsSuperadmin()) 
        return 0;
    
    $myslices = GetUsersSlices( $auth->auth["uid"] );
    reset ($myslices);
    while (list ($my_slice_id, $perms) = each ($myslices)) 
        if (strchr ($perms, PS_FULLTEXT))
            $restrict_slices[] = q_pack_id($my_slice_id);
    $_filter_permissions = array ();
    if (is_array ($restrict_slices)) {
        $db->query("SELECT DISTINCT ADF.id FROM alerts_filter ADF 
                     INNER JOIN view ON view.id = ADF.vid
                     INNER JOIN slice ON slice.id = view.slice_id
                     WHERE slice_id IN ('".join("','",$restrict_slices)."')");
        $_filter_permissions = array ();             
        while ($db->next_record())
            $_filter_permissions[] = $db->f("id");
    }
    return $_filter_permissions;
}

// ----------------------------------------------------------------------------------        

function FindAlertsUserPermissions () {
    global $Tab, $db, $collectionid, $sess, $setTab;
    $now = time();
    switch ($Tab) {
        case 'appb': $where = "status_code = 1 AND start_date > $now"; break;
        case 'appc': $where = "status_code = 1 AND start_date <= $now AND expiry_date < $now"; break;
        case 'hold': $where = "status_code = 2"; break;
        case 'trash':$where = "status_code = 3"; break;        
        case 'app': 
        default: $where = "status_code = 1 AND start_date <= $now
            AND expiry_date >= $now"; break;
    }
    
    $db->query("SELECT userid 
        FROM alerts_user_collection 
        WHERE collectionid='$collectionid' AND $where");
    $retval = array ();
    while ($db->next_record()) 
        $retval[] = $db->f("userid");
    return $retval;
}

// ----------------------------------------------------------------------------------        

// user function for confirmed
function te_au_confirm ($val) {
    return $val ? _m("no") : _m("yes");
}

function AlertsModeditAfterInsert ($varset) {
    global $change_id;
    $change_id = unpack_id128($varset->get ("id"));
}
?>