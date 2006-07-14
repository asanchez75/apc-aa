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

require_once AA_INC_PATH."mail.php3";
require_once AA_INC_PATH."mgettext.php3";

if (!is_object($db)) {
    $db=new DB_AA;
}

function set_collectionid() {
    global $collectionid, $collectionprop, $db, $no_slice_id, $slice_id;

    if (!$no_slice_id) {
        if (!$slice_id) { echo "Error: no slice ID"; exit; }
        $db->query("SELECT AC.*, module.name, module.lang_file, module.slice_url
            FROM alerts_collection AC INNER JOIN module
            ON AC.module_id = module.id
            WHERE module_id='".q_pack_id($slice_id)."'");
        if ($db->next_record()) {
            $collectionid = $db->f("id");
            $collectionprop = $db->Record;
        } else {
            echo "Can't find collection with module_id=$slice_id ("
                .HTMLEntities(pack_id($slice_id))."). Bailing out.<br>";
            exit;
        }
    }
}

/// Returns true if $howoften is a regular howoften option.
function is_howoften_option($howoften) {
    $ho = get_howoften_options();
    return $ho[$howoften];
}

function get_howoften_options($include_instant = true) {
    if ($include_instant) {
        $retval["instant"] = _m("instant");
    }
    $retval["daily"]   = _m("daily");
    $retval["weekly"]  = _m("weekly");
    $retval["monthly"] = _m("monthly");
    return $retval;
}

function get_bin_names() {
    return array (
    1 => _m("Active"),
    2 => _m("Holding bin"),
    3 => _m("Trash bin"));
}

function new_user_id() {
    global $db;
    do {
        $new_id = new_numeric_id(32767);
        $db->query("SELECT id FROM alerts_user WHERE id = $new_id");
    } while ($db->next_record());
    return $new_id;
}

function new_collection_id() {
    global $db;
    do {
        $new_id = new_alphanumeric_id(5);
        $db->query("SELECT id FROM alerts_collection WHERE id = '$new_id'");
    } while ($db->next_record());
    return $new_id;
}

function new_alphanumeric_id($saltlen) {
    srand((double) microtime() * 1000000);
    $salt_chars = "abcdefghijklmnoprstuvwxBCDFGHJKLMNPQRSTVWXZ0123456589";
    for ($i = 0; $i < $saltlen; $i ++)
        $salt .= $salt_chars[rand(0,strlen($salt_chars)-1)];
    return $salt;
}

function new_numeric_id ($max) {
    list($usec, $sec) = explode(' ', microtime());
    $seed = (float) $sec + ((float) $usec * 100000);
    srand($seed);
    return rand(1, $max);
}

function new_user_confirm()
{
    global $db;
    do {
        $retval = gensalt(4);
        $db->query("SELECT confirm FROM alerts_user_collection WHERE confirm='".addslashes($retval)."'");
    } while ($db->next_record());
    return $retval;
}

// ----------------------------------------------------------------------------------------

/**
* @param $info  should contain "uid" for $insert=false,
*               may contain "firstname","lastname","lang",
                "password"=md5-encrypted new password
* @param $insert if true, user is inserted, else updated
* @returns new user ID, if $insert=true, nothing otherwise
*/
function insert_or_update_user($info, $insert)
{
    global $db, $slice_id;
    // insert new user
    $varset = new CVarset;
    if ($insert) {
        $userid = new_user_id();
        $varset->addkey("id", "number", $userid);
    } else {
        $varset->addkey("id", "number", $info["uid"]);
    }
    $userfields = array ("email","firstname","lastname","lang","password");
    foreach ($userfields as $field) {
        if (isset ($info[$field])) {
            $varset->add ($field, "quoted", $info[$field]);
        }
    }
    $varset->add("owner_module_id", "unpacked", $slice_id);
    $db->query($varset->makeINSERTorUPDATE("alerts_user"));
    return $userid;
}

// ----------------------------------------------------------------------------------------
function email_address($name, $email) {
    return ($name > " ") ? "$name <$email>" : $email;
}

// ----------------------------------------------------------------------------------------

function alerts_email_headers($record, $default) {
    $headers = array (
        "From" => "header_from",
        "Reply-To" => "reply_to",
        "Errors-To" => "errors_to",
        "Sender" => "sender");
    foreach ($headers as $header => $field) {
        if ($record["$field"]) {
            $retval .= $header.": ".$record["$field"]."\r\n";
        } elseif ($default["$field"]) {
            $retval .= $header.": ".$default["$field"]."\r\n";
        }
    }
    return $retval;
}

// -----------------------------------------------------------------------------------

function AlertsPageBegin() {
    // style sheet
    global $ss;
    $stylesheet = $ss ? $ss : AA_INSTAL_PATH. ADMIN_CSS;

    echo
    '<!DOCTYPE html public "-//W3C//DTD HTML 4.0 Transitional//EN">
       <HTML>
         <HEAD>
           <LINK rel=StyleSheet href="'.$stylesheet.'" type="text/css"  title="CPAdminCSS">
           <meta http-equiv="Content-Type" content="text/html; charset='.$LANGUAGE_CHARSETS[get_mgettext_lang()].'">';
}

// -----------------------------------------------------------------------------------

function getAlertsField($field_id, $collection_id) {
    return substr($field_id.".............", 0, 16 - strlen ($collection_id)). $collection_id;
}

// -----------------------------------------------------------------------------------

function alerts_con_url($Url,$Params){
  return ( strstr($Url, '?') ? $Url."&".$Params : $Url."?".$Params );
}

function GetEmailLangs() {
    global $LANGUAGE_CHARSETS, $LANGUAGE_NAMES;
    foreach ( $LANGUAGE_CHARSETS as $l => $charset ) {
        $ret[$l] = $LANGUAGE_NAMES[$l]." (".$charset.")";
    }
    return $ret;
}
?>