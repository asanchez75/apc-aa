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

// Settings for some table views (see doc/tabledit.html for more info)       

function GetTableView ($viewID) {        
    global $auth, $slice_id, $db;
    global $attrs_edit, $attrs_browse, $format, $langs;
            
    /* ------------------------------------------------------------------------------------
       ww -- browse wizard welcomes
       ww_edit -- edit -"-
    */         
    if ($viewID == "ww") return  array (
        "table" => "wizard_welcome",
        "type" => "browse",
        "readonly" => true,
        "title" => L_EDIT_WIZARD_WELCOME,
        "caption" => L_EDIT_WIZARD_WELCOME,
        "mainmenu" => "aaadmin",
        "submenu" => "te_wizard_welcome",
        "cond" => IsSuperadmin(),
        "fields" => array (
            "description" => array ("required" => true),
            "email" => array ("hint" => _m("mail body"), "validate" => "email", "required" => true),
            "subject" => array ("required" => true),
            "mail_from" => array ("hint" => _m("From: mail header"), "required" => true)),
        "attrs" => $attrs_browse,
        "gotoview" => "ww_edit");
        
    if ($viewID == "ww_edit") {
        $retval = GetTableView ("ww");
        $retval["type"] = "edit";
        $retval["attrs"] = $attrs_edit;
        $retval["readonly"] = false;
        $retval["gotoview"] = "ww";
        $retval["addrecord"] = false;
        return $retval;
    }
    
    /* ------------------------------------------------------------------------------------
       wt -- browse wizard templates 
    */
    if ($viewID == "wt") return  array (
        "table" => "wizard_template",
        "type" => "browse",
        "readonly" => false,
        "cond" => IsSuperadmin(),
        "title" => L_EDIT_WIZARD_TEMPLATE,
        "caption" => L_EDIT_WIZARD_TEMPLATE,
        "mainmenu" => "aaadmin",
        "submenu" => "te_wizard_template",
        "fields" => array (
            "dir"=> array (
                "view" => array ("type" => "text", "size" => array ("cols" => 10)),
                "validate" => "filename",
                "required" => true), 
            "description"=> array (
                "view" => array ("type" => "text", "size" => array ("cols" => 40)),
                "required" => true)
            ),
        "attrs" => $attrs_browse);    

    /* ------------------------------------------------------------------------------------
       cron 
    */
    if ($viewID == "cron") {
        $url = "http://apc-aa.sourceforge.net/faq/#cron";    
        return  array (
        "table" => "cron",
        "type" => "browse",
        "mainmenu" => "aaadmin",
        "submenu" => "te_cron",
        "help" => _m("For help see FAQ: ")."<a target=\"_blank\" href=\"$url\">$url</a>",
        "readonly" => false,
        "addrecord" => true,
        "cond" => IsSuperadmin(),
        "title" => _m ("Cron"),
        "caption" => _m("Cron"),
        "attrs" => $attrs_browse,
        "fields" => array (
            "minutes" => array ("default"=>"*","view" => array ("type" => "text", "size" => array ("cols"=>2))),
            "hours" => array ("default"=>"*","view" => array ("type" => "text", "size" => array ("cols"=>2))),
            "mday" => array ("default"=>"*","view" => array ("type" => "text", "size" => array ("cols"=>2))),
            "mon" => array ("default"=>"*","view" => array ("type" => "text", "size" => array ("cols"=>2))),
            "wday" => array ("default"=>"*","view" => array ("type" => "text", "size" => array ("cols"=>2))),
            "script" => array ("view" => array ("type" => "text", "size" => array ("cols"=>25)),
                "required" => true),
            "params" => array ("view" => array ("type" => "text", "size" => array ("cols"=>20))),
            "last_run" => array ("view" => array ("readonly" => true, "type" => "date", "format" => "j.n.Y G:i"))
        ));
    }
    
} // end of GetTableView
?>

