<?php 
/**
*  Fills info from collection form into database.
*  Parameters: $alerts .. array of info 
*               $alerts[userid] 
*               $alerts[collectionid]
*               $alerts[password]
*               etc.
*
 * @package Alerts
 * @version $Id$
 * @author Jakub Ad�mek <jakubadamek@ecn.cz>, Econnect, December 2002
 * @copyright Copyright (C) 1999-2002 Association for Progressive Communications 
*/
/* 
Copyright (C) 1999-2002 Association for Progressive Communications 
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

require_once "../../include/config.php3";
require_once $GLOBALS["AA_INC_PATH"]."util.php3";
require_once $GLOBALS["AA_INC_PATH"]."formutil.php3";
require_once $GLOBALS["AA_INC_PATH"]."varset.php3";
require_once $GLOBALS["AA_INC_PATH"]."locsess.php3"; 
require_once "cf_common.php3";

if (!$alerts["run_filler"]) return;

$db = new DB_AA;

$userid = $alerts["userid"];
$collid = $alerts["collectionid"];

if ($userid) {
    // is the user ID valid?
    $db->query("SELECT * FROM alerts_user WHERE id=$userid");
    if (!$db->next_record()) { my_die(_m("Wrong user ID")); return; }
}

else {
    // does such email exist in database?
    $db->query("SELECT * FROM alerts_user WHERE email='$alerts[email]'");
    if ($db->next_record())
        $userid = $db->f("id");
    // no email nor user ID given => nothing will be changed in database
    else if (!$alerts["email"]) return;
    else if (!ValidateInput ("email", "email", $alerts["email"], $err, true, "email")) 
        { my_die($err); return; }
}

if ($userid)
    if ($db->f("password") && $db->f("password") != $alerts["password"]) 
        { my_die (_m("Wrong password.")); return; }

// change password        
if ($alerts["chpwd"]) {
    if ($alerts["chpwd"] != $alerts["chpwd2"]) my_die ("Passwords differ.");
    else $alerts["password"] = $alerts["chpwd"];
}

$alerts["uid"] = $userid;
insert_or_update_user ($alerts, $userid != 0);
    
// change collection settings
if ($alerts["collectionid"]) {
    $db->query("DELETE FROM alerts_user_collection_filter
                 WHERE userid=$userid AND collectionid=$collid");

    switch ($alerts["choose_filters"]) {
    // filters were chosen by checkboxes
    case "checkbox":
        $db->query("SELECT * FROM alerts_collection_filter WHERE collectionid=$collid");
        if (!is_array ($alerts["filters"]))
            break;
        $allfilters = true;
        while ($db->next_record()) {
            if ($alerts["filters"][$db->f("filterid")])
                // add 100 to exclude filters not part of this collection
                $alerts["filters"][$db->f("filterid")] = 100 + $db->f("myindex");
            else $allfilters = false;
        }
        if (!$allfilters) {
            reset ($alerts["filters"]);    
            while (list ($filterid, $myindex) = each ($alerts["filters"])) 
                if ($myindex >= 100)
                    $db->query("INSERT INTO alerts_user_collection_filter
                        (userid, collectionid, filterid, myindex)
                        VALUES ($userid, $collid, $filterid, $myindex)");
        }
        break;
    // filters were not chosen, all will be used 
    case "no":
        $allfilters = true;
        break;
    default: 
        my_die ("Wrong value of alerts[choose_filters]");
        $allfilters = true;
        break;
    }

    $info["userid"] = $userid;
    $info["allfilters"] = $allfilters;
    $info["howoften"] = $alerts["howoften"];
    $info["email"] = $alerts["email"];
    $db->query("SELECT * FROM alerts_collection WHERE id=$collid");
    $db->next_record();
    
    insert_or_update_user_collection ($info, $db->Record, false, true);
}        

function my_die ($err) {
   echo "<p class=\"cf_warning\">$err</p>";
}

?>
