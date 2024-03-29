<?php
/**
 * View discussions, parse search conditions (conds[discussion] array)
 * @package UserOutput
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

// ----- input variables normalization - start --------------------------------

// This code handles with "magic quotes" and "register globals" PHP (<5.4) setting
// It make us sure, taht
//  1) in $_POST,$_GET,$_COOKIE,$_REQUEST variables the values are not quoted
//  2) the variables are imported in global scope and is quoted
// We are trying to remove any dependecy on the point 2) and use only $_* superglobals
function AddslashesDeep($value)   { return is_array($value) ? array_map('AddslashesDeep',   $value) : addslashes($value);   }
function StripslashesDeep($value) { return is_array($value) ? array_map('StripslashesDeep', $value) : stripslashes($value); }

if ( get_magic_quotes_gpc() ) {
    $_POST    = StripslashesDeep($_POST);
    $_GET     = StripslashesDeep($_GET);
    $_COOKIE  = StripslashesDeep($_COOKIE);
    $_REQUEST = StripslashesDeep($_REQUEST);
}

if (!ini_get('register_globals') OR !get_magic_quotes_gpc()) {
    foreach ($_REQUEST as $k => $v) {
        $$k = AddslashesDeep($v);
    }
}
// ----- input variables normalization - end ----------------------------------


/**
 * PutSearchLog
 */
function PutSearchLog() {
    global $searchlog, $view_param;

    $httpquery        = $_SERVER['QUERY_STRING_UNESCAPED'].$_SERVER['REDIRECT_QUERY_STRING_UNESCAPED'];
    $httpquery        = DeBackslash($httpquery);
    $httpquery        = str_replace("'", "\\'", $httpquery);
    $db               = getDb();
    $found_count      = count($view_param["disc_ids"]);
    list($usec, $sec) = explode(" ",microtime());
    $slice_time       = 1000 * ((float)$usec + (float)$sec - $GLOBALS['disc_starttime']);
    $user             = $_SERVER['PHP_AUTH_USER'];
    $db->query("INSERT INTO searchlog (date,query,user,found_count,search_time,additional1) VALUES (".time().",'$httpquery','$user',$found_count,$slice_time,'discuss $searchlog')");
    freeDb($db);
}

/** APC-AA configuration file */
require_once "./include/config.php3";
/** Defines simplified class for page scroller */
require_once AA_INC_PATH."easy_scroller.php3";
/** Set of usefull functions used on most pages */
require_once AA_INC_PATH."util.php3";
/** Mail sending functions */
require_once AA_INC_PATH."mail.php3";
/**  Defines class for item manipulation (shows item in compact or fulltext format, replaces aliases ...) */
require_once AA_INC_PATH."item.php3";
/** parses view settings, gets view data and other functions */
require_once AA_INC_PATH."view.php3";
/** discussion utility functions */
require_once AA_INC_PATH."discussion.php3";
/** defines PageCache class used for caching informations into database */
require_once AA_INC_PATH."pagecache.php3";
/** functions for searching and filtering items */
require_once AA_INC_PATH."searchlib.php3";
/** Main include file for using session management function on page */
$encap = true; // just for calling extsessi.php
require_once AA_INC_PATH."locsess.php3";    // DB_AA object definition

add_vars();

$p_slice_id= q_pack_id($slice_id);
is_object( $db ) || ($db = getDB());

list($usec, $sec) = explode(" ",microtime());
$disc_starttime   = ((float)$usec + (float)$sec);

$view_param              = ParseViewParameters();
$view_param["disc_ids"]  = QueryDiscIDs($slice_id, $conds, $sort, $slices );
$view_param["disc_type"] = "list";
// special url parameter disc_url - tell us, where we have to show
// discussion fulltext (good for discussion search)
if ( $disc_url ) {
    $view_param["disc_url"] = $disc_url;
}

//create keystring from values, which exactly identifies resulting content
$cache_key = get_hash($view_param, PageCache::globalKeyArray());

if ($cacheentry = $pagecache->getPage($cache_key, $nocache)) {
    $cacheentry->processPage();
} else {
    list($page_content, $cache_sid) = GetViewFromDB($view_param, true);
    $cacheentry = new AA_Cacheentry($page_content, AA::getHeaders());
    $cacheentry->processPage();

    if (!$nocache) {
        $str2find = new CacheStr2find($cache_sid, 'slice_id');
        $pagecache->storePage($cache_key, $cacheentry, $str2find);
    }
}

if ($searchlog) {
    PutSearchLog();
}
exit;
?>
