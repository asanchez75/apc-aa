<?php
/**
 * filldisc.php3 - writes a discussion item into the discussion table
 * expected parameters (usually from a HTML form):
 *          $d_item_id
 *          $d_parent_id
 *          $d_subject
 *          $d_author
 *          $d_e_mail
 *          $d_body
 *          $d_state
 *          $d_flag
 *          $d_free1
 *          $d_free2
 *          $d_url_address
 *          $d_url_description
 *
 * date and remote address(IP) of client is set by script.
 *
 * @package UserInput
 * @version $Id$
 * @author
 * @copyright Copyright (C) 1999, 2000 Association for Progressive Communications
*/
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


/**
 * Handle with PHP magic quotes - quote the variables if quoting is set off
 * @param mixed $val the variable or array to quote (add slashes)
 * @return mixed the quoted variables (with added slashes)
 */
function Myaddslashes($val, $n=1) {
  if (!is_array($val)) {
    return addslashes($val);
  }
  for (reset($val); list($k, $v) = each($val); )
    $ret[$k] = Myaddslashes($v, $n+1);
  return $ret;
}

if (!get_magic_quotes_gpc()) {
  // Overrides GPC variables
  if ( isset($HTTP_GET_VARS) AND is_array($HTTP_GET_VARS))
    for (reset($HTTP_GET_VARS); list($k, $v) = each($HTTP_GET_VARS); )
      $$k = Myaddslashes($v);
  if ( isset($HTTP_POST_VARS) AND is_array($HTTP_POST_VARS))
    for (reset($HTTP_POST_VARS); list($k, $v) = each($HTTP_POST_VARS); )
      $$k = Myaddslashes($v);
  if ( isset($HTTP_COOKIE_VARS) AND is_array($HTTP_COOKIE_VARS))
    for (reset($HTTP_COOKIE_VARS); list($k, $v) = each($HTTP_COOKIE_VARS); )
      $$k = Myaddslashes($v);
}

/** APC-AA configuration file */
require_once "./include/config.php3";
/** Main include file for using session management function on a page */
require_once $GLOBALS['AA_INC_PATH']."locsess.php3";
/** Set of useful functions used on most pages */
require_once $GLOBALS['AA_INC_PATH']."util.php3";
/** Defines class for inserting and updating database fields */
require_once $GLOBALS['AA_INC_PATH']."varset.php3";
/** discussion utility functions */
require_once $GLOBALS['AA_INC_PATH']."discussion.php3";
/** defines PageCache class used for caching informations into database */
require_once $GLOBALS['AA_INC_PATH']."pagecache.php3";
/** defines class that prints the items (news, discussions, calendar...) */
require_once $GLOBALS['AA_INC_PATH']."itemview.php3";
/**  Defines class for item manipulation (shows item in compact or fulltext format, replaces aliases ...) */
require_once $GLOBALS['AA_INC_PATH']."item.php3";

$err["Init"] = "";       // error array (Init - just for initializing variable)

$new_id = new_id();

$catVS = new Cvarset();
$catVS->add("id", "unpacked", $new_id);
$catVS->add("parent", "unpacked", $d_parent);
$catVS->add("item_id", "unpacked", $d_item_id);
$catVS->add("subject", "quoted", $d_subject);
$catVS->add("author", "quoted", $d_author);
$catVS->add("e_mail", "quoted", $d_e_mail);
$catVS->add("body", "quoted", $d_body);
$catVS->add("state", "quoted", $d_state);
$catVS->add("flag", "quoted", $d_flag);
$catVS->add("free1", "quoted", $d_free1);
$catVS->add("free2", "quoted", $d_free2);
$catVS->add("url_address", "quoted", $d_url_address);
$catVS->add("url_description", "quoted", $d_url_description);
$catVS->add("date", "quoted", time());
$catVS->add("remote_addr", "quoted", $GLOBALS[REMOTE_ADDR]);

$SQL = "INSERT INTO discussion" . $catVS->makeINSERT();
$db = new DB_AA;
if (!$db->query($SQL)) {  // not necessary - we have set the halt_on_error
  $err["DB"] .= MsgErr("Can't add discussion comment");
}
send2mailList($d_item_id, $new_id);

// invalidate cache
$slice_id = unpack_id128(GetTable2Array("SELECT slice_id FROM item WHERE id='".q_pack_id($d_item_id)."'", 'aa_first', 'slice_id'));
$GLOBALS['pagecache']->invalidateFor("slice_id=$slice_id");  // invalidate old cached values
writeLog('PAGECACHE', "slice_id=$slice_id", "filldisc" );

updateDiscussionCount($d_item_id);        // update a count of the comments belong to the item

// special discussion setting
if ( $_POST['all_ids'] )
    $url = str_replace('&sh_itm', "&all_ids=".$_POST['all_ids'].'&sh_itm', $url);
go_url( $url);

?>