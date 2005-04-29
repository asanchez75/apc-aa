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

require_once $GLOBALS['AA_INC_PATH']."searchlib.php3";
require_once $GLOBALS['AA_INC_PATH']."itemview.php3";
require_once $GLOBALS['AA_INC_PATH']."item.php3";

define("HTML", 0);
define("PLAIN",1);

define("FEEDTYPE_RSS",  1);
define("FEEDTYPE_APC",  2);
define("FEEDTYPE_EXACT",3);

// server module's error message
define("ERR_NO_SLICE","Error 1");
define("ERR_PASSWORD","Error 2");

define( 'UNPACKED_AA_THE_SAME_CATE', unpack_id128('AA_The_Same_Cate') );
define( 'UNPACKED_AA_OTHER_CATEGOR', unpack_id128('AA_Other_Categor') );

$CONTENT_FORMATS = array("http://www.isi.edu/in-notes/iana/assignments/media-types/text/html" => HTML,
                         "http://www.isi.edu/in-notes/iana/assignments/media-types/text/plain"=> PLAIN);

/** Each slice can use different character encoding. The encoding is based on
 *  selected language for the slice
 */
function getSliceEncoding($slice_id) {
    $db = getDB();
    $p_slice_id = q_pack_id($slice_id);
    $db->query("SELECT lang_file FROM module WHERE id='$p_slice_id'");
    $db->next_record();
    $lang = substr($db->f("lang_file"),0,2);
    freeDB($db);
    return $GLOBALS["LANGUAGE_CHARSETS"][$lang];
}

/** Get categories from table ef_categories
 *  ef_categories is used only for APC type of feedin (APC RSS) and the change
 *  would by tricky, since there could unfortunatelly be two feeds with the same
 *  id (one APC and one other RSS)
 *  @param $add_other - if there are external categories it will add default
 *                      (AA_Other_Categor) category to the array (if not present
 *                      already
 */
function GetExternalCategories($feed_id, $add_other=false) {
    global $db;
    $db->query("SELECT category_id, category, category_name, target_category_id, approved
                  FROM ef_categories
                 WHERE feed_id='$feed_id' ORDER BY category_name");
    while ($db->next_record()) {
        $ext_categs[unpack_id($db->f('category_id'))] = array(
            "value"              => $db->f('category'),
            "name"               => $db->f('category_name'),
            "approved"           => $db->f('approved'),
            "target_category_id" => unpack_id128($db->f('target_category_id')));
    }
    if ( $add_other AND (count($ext_categs)>0) AND !isset($ext_categs[unpack_id('AA_Other_Categor')])) {
        $ext_categs[UNPACKED_AA_OTHER_CATEGOR] = array(
            "value"              => 'AA_Other_Categor',
            "name"               => _m('Other categories'),
            "approved"           => false,
            "target_category_id" => ''  // default is umpapped, not UNPACKED_AA_THE_SAME_CATE
            );
    }
    return $ext_categs;
}

/** Returns true if filters are set for 'All categories' option
 *  (and not separately for each category)
 */
function UseAllCategoriesOption( &$ext_categs ) {
    foreach ( $ext_categs as $k => $v ) {
        if ( $v['target_category_id'] AND ($k != UNPACKED_AA_OTHER_CATEGOR) ) {
            return false;
        }
    }
    return true;
}


// get external mapping from remote slice to local slice = returns two array
// map_to = from_field_id -> from_field_name  (but just for fields with flag = FEEDMAP_FLAG_MAP
// map_from = to_field_id -> { feedmap_flag => flag, value => from_field_id|value, from_field_name
function GetExternalMapping($l_slice_id, $r_slice_id) {
  global $db;

  $db->query("SELECT * FROM feedmap WHERE from_slice_id='".q_pack_id($r_slice_id)."'
                                      AND to_slice_id='".q_pack_id($l_slice_id)."'
                                    ORDER BY from_field_name");
  while ($db->next_record()) {
    switch ($f = $db->f(flag)) {
      case FEEDMAP_FLAG_EXTMAP :
      case FEEDMAP_FLAG_MAP:
        $v = $db->f(from_field_id);
        $map_to[$v] = $db->f(from_field_name) ;
        break;
      case FEEDMAP_FLAG_JOIN:
      case FEEDMAP_FLAG_RSS:
      case FEEDMAP_FLAG_VALUE :  $v = $db->f(value); break;
      case FEEDMAP_FLAG_EMPTY :  $v = ""; break;
    }
    $map_from[$db->f(to_field_id)] = array("feedmap_flag"=>$f,"value"=>$v,
                                            "from_field_name"=>$db->f(from_field_name));
  }

  return array($map_to,$map_from);
}

/** Returns category_id of category with cat_group $cat_group and value $value
 */
function GetCategoryIdFromValue($cat_group, $value) {
    global $debugfeed;
    if (!$cat_group || !is_array($cat_group)) {
        return;
    }
    $SQL = "SELECT id FROM constant WHERE group_id='$group_id' AND value='".addslashes($value)."'";
    if ($debugfeed >= 8) print("\n<br>$SQL");
    $db = getDB();
    $db->query($SQL);
    if ($db->next_record()) {
        $ret = unpack_id128($db->f('id'));
    }
    freeDB($db);
    return $ret;
}

/** Returns first field id of specified type */
function GetBaseFieldId( &$fields, $ftype ) {
    $no = 10000;
    if ( isset($fields) AND is_array($fields) ) {
        foreach ( $fields as  $k => $val ) {
            if (!strstr($val['id'],$ftype)) {
                continue;
            }
            $last = GetFieldNo($val['id']);
            $no   = min($no, (($last=='') ? -1 : (integer)$last));
        }
    }
    return ($no == 10000) ? false : CreateFieldId($ftype, ($no==-1) ? '.' : (string)$no);
}


/** Returns Category definitions for specified slice
 *  $cat[<unpacked_id>] = array( 'name'=> , 'value'=> 'parent_id'=> );
 */
function GetGroupConstants($slice_id) {
    global $db;
    $cat_group = GetCategoryGroup($slice_id);
    if (!$cat_group) {
        return false;
    }

    $SQL = "SELECT id, name, value, class FROM constant WHERE group_id = '$cat_group' ORDER BY pri";
    $db->query($SQL);         // get all categories
    while ($db->next_record()) {
        $cat_ids[unpack_id128($db->f('id'))] = array("name"     => $db->f('name'),
                                                     "value"    => $db->f('value'),
                                                     "parent_id"=> $db->f('class'));
    }
    return $cat_ids;
}

function MapDefaultCategory(&$categories, $value, $parent_id) {
    // try to find the same category
    foreach ( $categories as $to_id => $v) {
        if ($v['value'] == $value) {
            return $to_id;
        }
    }
    // try to find the same parent category
    foreach ($categories as $to_id => $v) {
        if ($v['parent_id'] == $parent_id) {
            return $to_id;
        }
    }
    // return the first category
    reset($categories);
    return key($categories);
}

function unixstamp_to_iso8601($t) {
    $tz=date("Z", $t)/60;
    $tm=$tz % 60;
    $tz=$tz/60;
    if ($tz<0) {
        $ts="-";
        $tz=-$tz;
    } else {
        $ts="+";
    }
    $tz=substr("0" . $tz, -2);
    $tm=substr("0" . $tm, -2);
    return date("Y-m-d\TH:i:s", $t). "${ts}${tz}:${tm}";
}

function iso8601_to_unixstamp($t) {
    ereg ("([0-9]{4})-([0-9]{2})-([0-9]{2})[T ]([0-9]{2})\:([0-9]{2})\:([0-9]{2})(\+|\-)([0-9]{2})\:([0-9]{2})", $t, $r);
    $tz = (int)$r[8]*3600+$r[9]*60;
    if ($r[7] == "+") {
        $tz =-$tz;
    }
    return gmmktime($r[4],$r[5],$r[6],$r[2],$r[3],$r[1])+$tz;
}

$DEFAULT_RSS_MAP = array (
    // Note this matches code in xml_rssparse.php3 for parsing DC fields
    // Can change the names without affecting anything
        "author.........." => array("feedmap_flag"=>FEEDMAP_FLAG_RSS,"value"=>"DC/creator","from_field_name"=>"DC:creator"),
        "abstract........" => array("feedmap_flag"=>FEEDMAP_FLAG_RSS,"value"=>"ITEM/description|DC/description|DC/subject","from_field_name"=>"Any abstract"),
        "publish_date...." => array("feedmap_flag"=>FEEDMAP_FLAG_RSS,"value"=>"DATE(DC/date)|DATE(ITEM/pubdate)|NOW","from_field_name"=>"DC:date"),
        "source.........." => array("feedmap_flag"=>FEEDMAP_FLAG_RSS,"value"=>"DC/source|CHANNEL/title","from_field_name"=>"DC:source"),
        "lang_code......." => array("feedmap_flag"=>FEEDMAP_FLAG_RSS,"value"=>"DC/language","from_field_name"=>"DC:language"),
        "source_href....." => array("feedmap_flag"=>FEEDMAP_FLAG_RSS,"value"=>"DC/relation|CHANNEL/link","from_field_name"=>"DC:relation"),
        "place..........." => array("feedmap_flag"=>FEEDMAP_FLAG_RSS,"value"=>"DC/coverage","from_field_name"=>"DC:coverage"),
        "headline........" => array("feedmap_flag"=>FEEDMAP_FLAG_RSS,"value"=>"DC/title|ITEM/title","from_field_name"=>"DC:title"),
        "full_text......." => array("feedmap_flag"=>FEEDMAP_FLAG_RSS,"value"=>"CONTENT","from_field_name"=>"Content"),
        "status_code....." => array("feedmap_flag"=>FEEDMAP_FLAG_VALUE,"value"=>2,"from_field_name"=>"Approved"),
        "hl_href........." => array("feedmap_flag"=>FEEDMAP_FLAG_RSS,"value"=>"ITEM/link|ITEM/guid","from_field_name"=>"ITEM:link"),
        "expiry_date....." => array("feedmap_flag"=>FEEDMAP_FLAG_VALUE,"value"=>(time()+2000*24*60*60),"from_field_name"=>"Expiry Date")
    );

// This function converts an attribute string to a unique id,
// this function must: always return the same result; and not contain 00 or 27
// the tricky part is that APC attribute strings contain a prefix and 32 digits, while
// non APC strings need the whole string hashed.
function attr2id($str) {
    if (ereg("/(items|cat|slices)/([0-9a-f]{32})",$str,$regs)) { // Looks like an APC id
        return $regs[2]; // Maybe this should be 0 ?
    } else {
        return(string2id($str));
    }
}

function name2rssfeed($slice_id,$name) {
    $db = getDB();
    $db->query("SELECT * FROM rssfeeds WHERE name='$name' AND slice_id = '".q_pack_id($slice_id)."'");
    if ($db->next_record()) {
      $res = $db->Record;
      $res["feed_type"] = FEEDTYPE_RSS;
      $res["remote_slice_id"] = attr2id($db->f(server_url));
    }
    freeDB($db);
    return $res;
}

/** used as parameter for $itemview in generating item_id-lastedit pair */
function GetXml_GetContent(&$zids) {
    return GetItemContentMinimal($zids, array('id', 'last_edit'));
}

class LastEditList {
    var $lastlist = null;

    function LastEditList() {
    }

    /** return list of items with last_edit date (id-last_edit,) used for 'exact'
     *  feeding between slices
     */
    function setFromSlice($conds, &$slice) {
        ParseEasyConds($conds);
        $zids    = QueryZIDs($slice->fields('record'), $slice->unpacked_id(), $conds, '', '', 'ALL');
        $format  = array('odd_row_format' => '{id..............}-{last_edit.......}',
                         'row_delimiter'  => ',');
        $itemview = new itemview($format, '', '', $zids,
                                           0,       // first item
                                           $zids->count(), // item count
                                           '',      // not necessary
                                           '',      // no discussion settings
                                           'GetXml_GetContent');
        $this->lastlist = $itemview->get_output_cached("view");
    }

    function setList($list) {
        $this->lastlist = $list;
    }

    function printList() {        // print() can't be name of the method :-( -
        echo $this->lastlist;     // parse error, unexpected T_PRINT, expecting
    }                             // T_STRING (php 4.3.10),
                                  // so I postfixed all functions with *List :-(

    function getList() {
        return $this->lastlist;
    }

    function getPairs() {
        foreach (explode(',', $this->lastlist) as $pair) {
            list($id,$time) = explode('-', $pair);
            $ret[$id]       = $time;
        }
        return $ret;
    }
}


?>