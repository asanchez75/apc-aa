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

require_once $GLOBALS['AA_INC_PATH']."varset.php3";
require_once $GLOBALS['AA_INC_PATH']."pagecache.php3";
require_once $GLOBALS['AA_INC_PATH']."notify.php3";
require_once $GLOBALS['AA_INC_PATH']."imagefunc.php3";
require_once $GLOBALS['AA_INC_PATH']."javascript.php3";
require_once $GLOBALS['AA_INC_PATH']."date.php3";
require_once $GLOBALS['AA_INC_PATH']."item_content.php3";
require_once $GLOBALS['AA_INC_PATH']."event.class.php3";
require_once $GLOBALS['AA_INC_PATH']."profile.class.php3";
require_once $GLOBALS['AA_INC_PATH']."files.class.php3";

if ( !is_object($event) ) $event = new aaevent;   // not defined in scripts which do not include init_page.php3 (like offline.php3)

/** ---------------- functions for default item values ----------------------/*/
function default_fnc_now($param) {
    return now();
}

function default_fnc_uid($param) {
    global $auth;                                  // 9999999999 for anonymous
    return (isset($auth) ? $auth->auth["uid"] : "9999999999");
}

function default_fnc_log($param) {
    global $auth;                                  // "anonymous" for anonymous
    return (isset($auth) ? $auth->auth["uname"] : "anonymous");
}

function default_fnc_dte($param) {
    return mktime(0,0,0,date("m"),date("d")+$param,date("Y"));
}

function default_fnc_qte($param) {
    return $param;
}

function default_fnc_txt($param) {
    return $param;
}

function default_fnc_rnd($param) {
    global $slice_id;

    $params = explode (":", $param);
    list($len, $field_id) = $params;
    if (!$len) {
        $len = 5;
    }
    $slice_only = (count($params) < 3) ? true : $params[2];

    $db = getDB();
    do {
        srand((double) microtime() * 1000000);
        $salt_chars = "abcdefghijklmnoprstuvwxABCDEFGHIJKLMNOPQRSTUVWX0123456789";
        for ($i=0; $i < $len; $i ++) {
            $salt .= $salt_chars[rand(0,strlen($salt_chars)-1)];
        }

        if (strlen($field_id) != 16) {
            break;
        }
        if ($slice_only) {
            $SQL = "SELECT * FROM content INNER JOIN item ON content.item_id = item.id
                     WHERE item.slice_id='".q_pack_id($slice_id)."'
                       AND field_id='".quote($field_id)."'
                       AND text='$salt'";
        } else {
            $SQL = "SELECT * FROM content WHERE field_id='".quote($field_id)
                    ."' AND text='$salt'";
        }
        $db->query ($SQL);
    } while ($db->next_record());
    freeDB($db);

    return $salt;
}

function default_fnc_($param) {
    global $err;
    $err["default_fnc"] = "No default function defined for parameter '$param'- default_fnc_()";
    return "";
}

/*
//Originally used by Mitra/Setu/Ram in PTS, but Commented out because of
//security risk, if required should be rewritten with a list of variables
//permitted

function default_fnc_variable($param) {
  return ($GLOBALS[$param]);
}
*/
// ----------------------- insert functions ------------------------------------

// What are the parameters to this function - $field must be an array with values [input_show_func] etc
function insert_fnc_qte($item_id, $field, $value, $param, $additional='') {
    global $itemvarset;

    $varset = new Cvarset();
    // if input function is 'selectbox with presets' and add2connstant flag is set,
    // store filled value to constants
    $fnc = ParseFnc($field["input_show_func"]);   // input show function
    if ($fnc AND ($fnc['fnc']=='pre')) {
        // get add2constant and constgroup (other parameters are irrelevant in here)
        list($constgroup, $maxlength, $fieldsize,$slice_field, $usevalue, $adding,
             $secondfield, $add2constant) = explode(':', $fnc['param']);
        // add2constant is used in insert_fnc_qte - adds new value to constant table
        if ($add2constant AND $constgroup AND (substr($constgroup,0,7) != "#sLiCe-")) {
            $db = getDB();
            // does this constant already exist?
            $constgroup = quote($constgroup);
            $constvalue = quote($value['value']);
            $SQL = "SELECT * FROM constant
                     WHERE group_id='$constgroup' AND value='$constvalue'";
            $db->query($SQL);
            if (!$db->next_record()) {
                // constant is not in database yet => add it

                // first we have to get max priority in order we can add new constant
                // with bigger number
                $SQL = "SELECT max(pri) as max_pri FROM constant
                        WHERE group_id='$constgroup'";
                $db->query($SQL);
                $new_pri = ($db->next_record() ? $db->f('max_pri') + 10 : 1000);

                // we have priority - we can add
                $varset->set("name",  $constvalue, 'quoted');
                $varset->set("value", $constvalue, 'quoted');
                $varset->set("pri",   $new_pri, "number");
                $varset->set("id", new_id(), "unpacked" );
                $varset->set("group_id", $constgroup, 'quoted' );
                $varset->doInsert('constant');
            }
            freeDB($db);
        }
    }

    if ($field["in_item_tbl"]) {
        // Mitra thinks that this might want to be 'expiry_date.....' ...
        // ... which is not correct because in 'in_item_tbl' database field
        // we store REAL database field names from aadb.item table (honzam)
        if (($field["in_item_tbl"] == 'expiry_date') AND (date("Hi",$value['value']) == "0000")) {
            // $value['value'] += 86399;
            // if time is not specified, take end of day 23:59:59
            // !!it is not working for daylight saving change days !!!
            $value['value'] = mktime(23,59,59,date("m",$value['value']),date("d",$value['value']),date("Y",$value['value']));
        }

        // field in item table
        $itemvarset->add($field["in_item_tbl"], "text", $value['value']);
        return;
    }

    // field in content table
    $varset->clear();
    if ($field["text_stored"]) {
        // do not store empty values in content table for text_stored fields
        // if ( !$value['value'] ) { return false; }    // can't do it, conditions do not work then (ecn joblist)
        $varset->add("text", "text", $value['value']);
        // set "TEXT stored" flag
        $varset->add("flag", "number", (int)$value['flag'] | FLAG_TEXT_STORED );
        if (is_numeric($additional["order"])) {
            $varset->add("number", "number", $additional["order"]);
        } else {
            $varset->add("number","null", "");
        }
    } else {
        $varset->add("number", "number", (int)$value['value']);
        // clear "TEXT stored" flag
        $varset->add("flag",   "number", (int)$value['flag'] & ~FLAG_TEXT_STORED );
    }

    // insert item but new field
    $varset->add("item_id", "unpacked", $item_id);
    $varset->add("field_id", "text", $field["id"]);
    $varset->doInsert('content');
}

function insert_fnc_dte($item_id, $field, $value, $param, $additional='') {
    insert_fnc_qte($item_id, $field, $value, $param, $additional);
}

function insert_fnc_cns($item_id, $field, $value, $param, $additional='') {
    insert_fnc_qte($item_id, $field, $value, $param, $additional);
}

function insert_fnc_num($item_id, $field, $value, $param, $additional='') {
    insert_fnc_qte($item_id, $field, $value, $param, $additional);
}

function insert_fnc_boo($item_id, $field, $value, $param, $additional='') {
    $value['value'] = ( $value['value'] ? 1 : 0 );
    insert_fnc_qte($item_id, $field, $value, $param, $additional);
}

function insert_fnc_ids($item_id, $field, $value, $param, $additional='') {

    $add_mode = substr($value['value'],0,1);          // x=add, y=add mutual, z=add backward
    if (($add_mode == 'x') || ($add_mode == 'y') || ($add_mode == 'z')) {
        $value['value'] = substr($value['value'],1);  // remove x, y or z
    }
    switch( $add_mode ) {

        case 'y':   // y means 2way related item id - we have to store it for both
            insert_fnc_qte($item_id, $field, $value, $param, $additional);
            // !!!!! there is no break or return - CONTINUE with 'z' case !!!!!

        case 'z':   // z means backward related item id - store it only backward
            // add reverse related
            $reverse_id     = $value['value'];
            $value['value'] = $item_id;

            // mimo added
            // get rid of empty dummy relations (text='')
            // this is only a problem for text content
            $db = getDB();
            if($field["text_stored"]) {
              $SQL = "DELETE FROM content
                       WHERE item_id = '". q_pack_id($reverse_id) ."'
                         AND field_id = '". $field["id"] ."'
                         AND `text`=''";
              $db->query( $SQL );
            }
            // is reverse relation already set?
            $SQL = "SELECT * FROM content
                     WHERE item_id = '". q_pack_id($reverse_id) ."'
                       AND field_id = '". $field["id"] ."'
                       AND ". ($field["text_stored"] ? "text" : "number") ."= '". $value['value'] ."'";
            $db = getDB();
            $db->query( $SQL );
            if (!$db->next_record()) { // not found
                insert_fnc_qte($reverse_id, $field, $value, $param);
            }
            freeDB($db);
            break;;

        case 'x':   // just filling character - remove it
        default:
            insert_fnc_qte($item_id, $field, $value, $param, $additional);
    }
    return;
}

function insert_fnc_uid($item_id, $field, $value, $param, $additional='') {
    global $auth;
    // if not $auth, it is from anonymous posting - 9999999999 is anonymous user
    $val = (isset($auth) ?  $auth->auth["uid"] : ((strlen($value['value'])>0) ?
                                                  $value['value'] : "9999999999"));
    insert_fnc_qte($item_id, $field, array('value' => $val), $param, $additional);
}

function insert_fnc_log($item_id, $field, $value, $param, $additional='') {
    global $auth;
    // if not $auth, it is from anonymous posting
    $val = (isset($auth) ?  $auth->auth["uname"] : ((strlen($value['value'])>0) ?
                                                    $value['value'] : 'anonymous'));
    insert_fnc_qte($item_id, $field, array('value' => $val), $param, $additional);
}

function insert_fnc_now($item_id, $field, $value, $param, $additional='') {
    insert_fnc_qte($item_id, $field, array("value"=>now()), $param, $additional);
}

// -----------------------------------------------------------------------------
/** Insert function for File Upload.
*   @return Array of fields stored inside this function as thumbnails.
*/
// There are three cases here
// 1: uploaded - overwrites any existing value, does resampling etc
// 2: file name left over from existing record, just stores the value
// 3: newly entered URL, this is not distinguishable from case //2 so
//    its just stored, and no thumbnails etc generated, this could be
//    fixed later (mtira)
// in $additional are fields
function insert_fnc_fil($item_id, $field, $value, $param, $additional="") {
    global $FILEMAN_MODE_FILE, $FILEMAN_MODE_DIR, $err;

    if (is_array($additional)) {
        $fields  = $additional["fields"];
        $order   = $additional["order"];
        $context = $additional["context"];
    }

    $filevarname = "v".unpack_id($field["id"])."x";
    $up_file = $_FILES[$filevarname];

    // look if the uploaded picture exists
    if ($up_file['name'] AND ($up_file['name'] != 'none') AND ($context != 'feed')) {
        $slice = new slice($GLOBALS["slice_id"]);

        // $pdestination and $purl is not used, yet - it should be used to allow
        // slice administrators to store files to another directory
        // list($ptype, $pwidth, $pheight, $potherfield, $preplacemethod, $pdestination, $purl) = ParamExplode($param);
        list($ptype, $pwidth, $pheight, $potherfield, $preplacemethod) = ParamExplode($param);

        $dest_file = Files::uploadFile($filevarname, Files::destinationDir($slice), $ptype, $preplacemethod);
        if ($dest_file === false) {   // error
            $err[$field["id"]] = Files::lastErrMsg();
            return;
        }

        // ---------------------------------------------------------------------
        // Create thumbnails (image miniature) into fields identified in this
        // field's parameters if file type is supported by GD library.

        // This has been considerable simplified, by making ResampleImage
        // return true for unsupported types IF they are already small enough
        // and also making ResampleImage copy the files if small enough

        if ($e = ResampleImage($dest_file, $dest_file, $pwidth, $pheight)) {
            $err[$field["id"]] = $e;
            return;
        }
        if ($potherfield != "") {
            // get ids of field store thumbnails
            $thumb_arr=explode("##",$potherfield);

            foreach ($thumb_arr as $thumb) {
                $num++; // Note sets it initially to 1

                //copy thumbnail
                $f              = $fields[$thumb];       // Array from fields
                $fncpar         = ParseFnc($f["input_insert_func"]);
                $thumb_params   = explode(":",$fncpar['param']);  // (type, width, height)

                $dest_file_tmb  = Files::generateUnusedFilename($dest_file, '_thumb');  // xxx_thumb1.jpg

                if ($e = ResampleImage($dest_file,$dest_file_tmb, $thumb_params[1],$thumb_params[2])) {
                    $err[$field["id"]] = $e;
                    return;
                }

                // delete content just for displayed fields
                $SQL = "DELETE FROM content WHERE item_id='". q_pack_id($item_id). "'
                        AND field_id = '".$f['id']."'";
                $db = getDB(); $db->tquery($SQL); freeDB($db);

                // store link to thumbnail
                $val['value'] = Files::getUrlFromPath($dest_file_tmb);
                insert_fnc_qte( $item_id, $f, $val, "", $additional);
            }
        } // params[3]

        $value['value'] = Files::getUrlFromPath($dest_file);
    } // File uploaded
    // store link to uploaded file or specified file URL if nothing was uploaded
    insert_fnc_qte( $item_id, $field, $value, "", $additional);

    // return array with fields that were filled with thumbnails  (why?)
    return $thumb_arr;
} // end of insert_fnc_fil

// -----------------------------------------------------------------------------

function insert_fnc_pwd($item_id, $field, $value, $param, $additional='')
{
    $change_varname = "v".unpack_id($field["id"])."a";
    $retype_varname = "v".unpack_id($field["id"])."b";
    // "c" created in ValidateContent4Id:
    $original_varname="v".unpack_id($field["id"])."c";
    $delete_varname = "v".unpack_id($field["id"])."d";
    global $$change_varname, $$retype_varname, $$delete_varname, $$original_varname;

    if ($$change_varname && $$change_varname == $$retype_varname) {
        $value['value'] = crypt($$change_varname, 'xx');
    } elseif ($$delete_varname) {
        $value['value'] = "";
    } else {
        $value['value'] = $$original_varname;
    }

    insert_fnc_qte($item_id, $field, $value, $param, $additional);
}

// -----------------------------------------------------------------------------

function insert_fnc_nul($item_id, $field, $value, $param, $additional='') {
}

// not defined insert func in field table (it is better to use insert_fnc_nul)
function insert_fnc_($item_id, $field, $value, $param, $additional='') {
}

// ----------------------- show functions --------------------------------------
// moved to formutil into aainputfield class (formutil.php3)
// -----------------------------------------------------------------------------

function IsEditable($fieldcontent, $field, &$profile) {
    return (!($fieldcontent[0]['flag'] & FLAG_FREEZE)
        AND $field["input_show"]
        AND !$profile->getProperty('hide',$field['id'])
        AND !$profile->getProperty('hide&fill',$field['id'])
        AND !$profile->getProperty('fill',$field['id']));
}

/** Returns content4id - values in content4id are quoted (addslashes) */
function GetContentFromForm( $slice, $oldcontent4id="", $insert=true ) {
    global $profile, $auth;

    list($fields, $prifields) = $slice->fields();
    if (!isset($prifields) OR !is_array($prifields)) {
        return false;
    }

    if (!is_object($profile)) {
        $profile = new aaprofile($auth->auth["uid"], $slice->unpacked_id());  // current user settings
    }

    foreach ( $prifields as $pri_field_id) {
        $f = $fields[$pri_field_id];

        // to content4id array add just displayed fields (see ShowForm())
        if (!IsEditable($oldcontent4id[$pri_field_id], $f, $profile) AND !$insert) {
            continue;
        }

        // "v" prefix - database field var
        $varname     = 'v'. unpack_id($pri_field_id);
        $htmlvarname = $varname."html";

        // if there are predefined values in user profile, fill it.
        // Fill it only if $insert (new item). Otherwise left there filled value

        $profile_value = $profile->getProperty('hide&fill',$f['id']);
        if (!$profile_value) {
            $profile_value = $profile->getProperty('fill',$f['id']);
        }

        if ( $profile_value ) {
            $x = $profile->parseContentProperty($profile_value);
            $GLOBALS[$varname]     = quote($x[0]);  // not quoted
            $GLOBALS[$htmlvarname] = $x[1];
        }

        global $$varname;
        $var = $$varname;
        if (!is_array($var)) {
            $var = array (0=>$var);
        }

        // fill the multivalues
        foreach ($var as $v) {
            $flag = $f["html_show"] ? ($GLOBALS[$htmlvarname]=="h" ? FLAG_HTML : 0)
                                    : ($f["html_default"] > 0      ? FLAG_HTML : 0);
            // data in $content4id are already DB escaped (addslashes)
            $content4id[$pri_field_id][]   = array('value'=>$v, 'flag'=>$flag);
        }
    }

    // the status_code must be set in order we can use email_notify()
    // in StoreItem() function.
    if (!$insert AND !$content4id['status_code.....'][0]['value']) {
        $content4id['status_code.....'][0]['value'] = max(1,$oldcontent4id['status_code.....'][0]['value']);
    }

    if (!$insert) {
        $content4id["flags..........."][0]['value'] = $oldcontent4id["flags..........."][0]['value'];
    }

    return $content4id;
}

// -----------------------------------------------------------------------------
/** StoreItem - deprecated function - $content4id->storeItem() instead
*
*   Basic function for changing contents of items.
*   Use always this function, not direct SQL queries.
*   Updates the tables @c item and @c content.
*   $GLOBALS[err][field_id] should be set on error in function
*   It looks like it will return true even if inset_fnc_xxx fails
*
*   @param array $content4id   array (field_id => array of values
*						      (usually just a single value, but still an array))
*   @param array $oldcontent4id if not sent, StoreItem finds it
*   @return true on success, false otherwise
*/
function StoreItem( $id, $slice_id, $content4id, $fields, $insert, $invalidatecache=true, $feed=true, $oldcontent4id="", $context='direct' ) {
    $content4id = new ItemContent($content4id);
    $content4id->setItemID($id);
    $content4id->setSliceID($slice_id);
    return $content4id->storeItem( $insert ? 'insert' : 'update', $invalidatecache, $feed, $context);     // invalidatecache, feed
} // end of StoreItem

// -----------------------------------------------------------------------------

function GetDefault($f) {
    // all default should have fnc:param format
    $fnc = ParseFnc($f["input_default"]);
    if ($fnc) {                     // call function
        $fncname = 'default_fnc_' . $fnc["fnc"];
        return $fncname($fnc["param"]);
    }
    return false;
}

function GetDefaultHTML($f) {
    return (($f["html_default"]>0) ? FLAG_HTML : 0);
}

// -----------------------------------------------------------------------------
/** Shows the Add / Edit item form fields
*   @param $show is used by the Anonymous Form Wizard, it is an array
*                (packed field id => 1) of fields to show
*/
function ShowForm($content4id, $fields, $prifields, $edit, $show="") {
    global $slice_id, $auth, $profile;

    if ( !isset($prifields) OR !is_array($prifields) ) {
        return MsgErr(_m("No fields defined for this slice"));
    }

    if ( !is_object($profile) ) {
        $profile = new aaprofile($auth->auth["uid"], $slice_id);  // current user settings
    }

    foreach ($prifields as $pri_field_id) {
        $f   = $fields[$pri_field_id];
        $fnc = ParseFnc($f["input_show_func"]);   // input show function

        if (is_array($show)) {
            $showme = $show [$f['id']];
        } else {
            $showme = $f["input_show"] AND !$profile->getProperty('hide',$f['id']) AND !$profile->getProperty('hide&fill',$f['id']);
        }

        if (!$fnc OR !$showme) {
            continue;
        }

        // get varname - name of field in inputform
        $varname     = 'v'. unpack_id($pri_field_id); // "v" prefix - database field var
        $htmlvarname = $varname."html";

        if (!IsEditable($content4id[$pri_field_id], $f, $profile)) {
            // if fed as unchangeable
            $show_fnc_prefix = 'show_fnc_freeze_';          // display it only
        } else {
            $show_fnc_prefix = 'show_fnc_';
        }

        $fncname = $show_fnc_prefix . $fnc["fnc"];

        // look for alternative function for Anonym Wizard (used for passwords)
        if (is_array($show) && function_exists($fncname."_anonym")) {
            $fncname .= "_anonym";
        }

        // updates content table or fills $itemvarset
        if ($edit) {
            $fncname($varname, $f, $content4id[$pri_field_id],
                     $fnc["param"], $content4id[$pri_field_id][0]['flag'] & FLAG_HTML );
        } else {
            // insert or new reload of form after error in inserting
            // first get values from profile, if there are some predefined value
            $foo = $profile->getProperty('predefine',$f['id']);
            if ($foo AND !$GLOBALS[$varname]) {
                $x = $profile->parseContentProperty($foo);
                $GLOBALS[$varname]     = $x[0];  // not quoted
                $GLOBALS[$htmlvarname] = $x[1];
            }

            // get values from form (values are filled when error on form ocures
            if ($f["multiple"] AND is_array($GLOBALS[$varname])) {
                // get the multivalues
                $i=0;
                foreach ($GLOBALS[$varname] as $v) {
                    $arr[$i++]['value'] = $v;
                }
            } else {
                $arr[0]['value'] = $GLOBALS[$varname];
            }

            $fncname($varname, $f, $arr, $fnc["param"],
                     ((string)$GLOBALS[$htmlvarname]=='h') || ($GLOBALS[$htmlvarname]==1));
        }
    }
}

// ----------------------------------------------------------------------------

/** Validates new content, sets defaults, reads dates from the 3-selectbox-AA-format,
*   sets global variables:
*       $oldcontent4id
*       $special input variables
*
*   This function is used in itemedit.php3, filler.php3 and file_import.php3.
*
*   @author Jakub Adamek, Econnect, January 2003
*           Most of the code is taken from itemedit.php3, created by Honza.
*
*   @param string $action should be one of:
*                         "add" .... a "new item" page (not form-called)
*                         "edit" ... an "edit item" page (not form-called)
*                         "insert" . call for inserting an item
*                         "update" . call for updating an item
*   @param id $id is useful only for "update"
*   @param bool $do_validate Should validate the fields?
*   @param array $notshown is an optional array ("field_id"=>1,...) of fields
*                          not shown in the anonymous form
*/
//./admin/itemedit.php3:                               ValidateContent4Id($err, $slice, $action, $id);
//./misc/file2slice/tab2slice_php/file_import.php3:    ValidateContent4Id($err, $slice, "insert", 0, ! $notvalidate);
//./filler.php3:                                       ValidateContent4Id($err_valid, $slice, $insert ? "insert" : "update", $my_item_id, !$notvalidate, $notshown)
function ValidateContent4Id(&$err, &$slice, $action, $id=0, $do_validate=true, $notshown="")
{
    global $oldcontent4id, $profile, $auth;

    if (!is_object($profile)) {             // current user settings
        $profile = new aaprofile($auth->auth["uid"], $slice->unpacked_id());
    }

    // error array (Init - just for initializing variable
    if (!is_array($err)) {
        $err["Init"] = "";
    }

    // get slice fields and its priorities in inputform
    list($fields, $prifields) = $slice->fields();

    if (!is_array($prifields)) {
        return;
    }

    // it is needed to call IsEditable() function and GetContentFromForm()
    if ( $action == "update" ) {
        $oldcontent = GetItemContent($id);
        $oldcontent4id = $oldcontent[$id];   // shortcut
    }

    foreach ($prifields as $pri_field_id) {
        $f = $fields[$pri_field_id];
        //  'status_code.....' is not in condition - could be set from defaults
        if (($pri_field_id=='edited_by.......') || ($pri_field_id=='posted_by.......')) {
            continue;   // filed by AA - it could not be filled here
        }
        $varname = 'v'. unpack_id($pri_field_id);  // "v" prefix - database field var
        $htmlvarname = $varname."html";

        global $$varname, $$htmlvarname;

        $setdefault = $action == "add"
                || !$f["input_show"]
                || $profile->getProperty('hide',$pri_field_id)
                || ($action == "insert" && $notshown [$varname]);

        list($validate) = explode(":", $f["input_validate"], 2);

        if ($setdefault) {
            // modify the value to be compatible with $_GET[] array - we use
            // slashed variables (this will be changed in future) - TODO
            $$varname     = addslashes(GetDefault($f));
            $$htmlvarname = GetDefaultHTML($f);
        } elseif ($validate=='date') {
            // we do not know at this moment, if we have to use default
            $default_val  = addslashes(GetDefault($f));
        }

        $editable = IsEditable($oldcontent4id[$pri_field_id], $f, $profile) && !$notshown[$varname];

        // Run the "validation" which changes field values
        if ($editable && ($action == "insert" || $action == "update")) {
            switch( $validate ) {
                case 'date':
                    $foo_datectrl_name = new datectrl($varname);
                    $foo_datectrl_name->update();           // updates datectrl
                    if ($$varname != "") {                  // loaded from defaults
                        $foo_datectrl_name->setdate_int($$varname);
                    }
                    $foo_datectrl_name->ValidateDate($f["name"], $err, $f["required"], $default_val);
                    $$varname = $foo_datectrl_name->get_date();  // write to var
                    break;
                case 'bool':
                    $$varname = ($$varname ? 1 : 0);
                    break;
                case 'pwd':
                    // store the original password to use it in
                    // insert_fnc_pwd when it is not changed
                    if ($action == "update"){
                        $GLOBALS[$varname."c"] = $oldcontent4id[$pri_field_id][0]['value'];
                    }
                    break;
            }
        }

        // Run the validation which really only validates
        if ($do_validate && ($action == "insert" || $action == "update")) {
            switch( $validate ) {
                case 'text':
                case 'url':
                case 'email':
                case 'number':
                case 'id':
                    ValidateInput($varname, $f["name"], $$varname, $err, // status code is never required
                        ($f["required"] AND ($pri_field_id!='status_code.....')) ? 1 : 0, $f["input_validate"]);
                    break;
                // necessary for 'unique' validation: do not validate if
                // the value did not change (otherwise would the value always
                // be found)
                case 'e-unique':
                case 'unique':
                    if (addslashes ($oldcontent4id[$pri_field_id][0]['value']) != $$varname)
                        ValidateInput($varname, $f["name"], $$varname, $err,
                                  $f["required"] ? 1 : 0, $f["input_validate"]);
                    break;
                case 'user':
                    // this is under development.... setu, 2002-0301
                    // value can be modified by $$varname = "new value";
                    $$varname = usr_validate($varname, $f["name"], $$varname, $err, $f, $fields);
                    break;
            }
        }
    }
}

?>
