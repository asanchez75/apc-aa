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

// Settings for each table view (see doc/tabledit.html for more info)       

function GetTableView ($viewID) {        
    global $auth, $slice_id;
    $db = new DB_AA;

    $attrs_edit = array (
        "table"=>"border=0 cellpadding=3 cellspacing=0 bgcolor='".COLOR_TABBG."'");
    $attrs_browse = array (
        "table"=>"border=1 cellpadding=3 cellspacing=0 bgcolor='".COLOR_TABBG."'",
        "table_search" => "border=0 cellpadding=3 cellspacing=0 bgcolor='".COLOR_TABBG."'");
    $format = array (
        "hint" => array (
            "before" => "<i>",
            "after" => "</i>"),
        "caption" => array (
            "before" => "<b>",
            "after" => "</b>"));            
    
    /* ------------------------------------------------------------------------------------
       alerts_admin
    */
    if ($viewID == "alerts_admin") {
        $db->query ("SELECT * FROM alerts_admin");
        if ($db->num_rows() == 0)
            $db->query ("INSERT INTO alerts_admin (mail_confirm, delete_not_confirmed) VALUES (0,0)");
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
    
    /* ------------------------------------------------------------------------------------
       polls_designs
    */
    if ($viewID == "polls_designs") {
        return  array (
        "table" => "polls_designs",
        "type" => "browse",
        "mainmenu" => "modadmin",
        "submenu" => "design",
        "readonly" => true,
        "addrecord" => false,
        "where" => "(pollsModuleID='". q_pack_id($slice_id)."')",
        "cond" => CheckPerms( $auth->auth["uid"], "slice", $slice_id, PS_POLLS_EDIT_DESIGN),
        "title" => _m ("Polls Design"),
        "caption" => _m("Polls Design"),
        "attrs" => $attrs_browse,
        "gotoview" => "polls_designs_edit",
        "fields" => array (
            "designID" => array ("caption" => _m("Id")),
            "name"     => array ("caption" => _m("Name")),
            "comment"  => array ("caption" => _m("Comment"))
        ));
    }

    if ($viewID == "polls_designs_edit") {
        $retval = GetTableView ("polls_designs");
        $retval["type"] = "edit";
        $retval["attrs"] = $attrs_edit;
        $retval["readonly"] = false;
        $retval["gotoview"] = "polls_designs";
        $retval["addrecord"] = false;
        $retval["fields"] = array (
            "designID"        => array ("caption" => _m("Id"),
                                        "view" => array( "type"=>"text",
                                                         "readonly" => true )),
            "name"            => array ("caption" => _m("Name"),
                                        "view" => array( "type"=>"text" ),
                                        "required" => true ),
            "comment"         => array ("caption" => _m("Comment"),
                                        "view" => array( "type"=>"text" ),
                                         "hint" => _m("design description (for administrators only)")),
            "resultBarFile"   => array ("caption" => _m("Bar image"),
                                        "view" => array( "type"=>"text" ),
                                        "hint" => _m("url of image for bar")),
            "resultBarWidth"  => array ("caption" => _m("Bar width"),
                                        "hint" => _m("width of poll bar")),
            "resultBarHeight" => array ("caption" => _m("Bar height"),
                                        "hint" => _m("height of poll bar")),
            "top"             => array ("caption" => _m("Top HTML")),
            "answer"          => array ("caption" => _m("Answer HTML")),
            "bottom"          => array ("caption" => _m("Bottom HTML")),
            "params"          => array ("caption" => _m("Params"))
            );
        return $retval;
    }
} // end of GetTableView

// ----------------------------------------------------------------------------------        

// user function for confirmed
function te_au_confirm ($val) {
    return $val ? _m("no") : _m("yes");
}
?>

