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

// jsview.php3 is exactly the same as view.php3, but the calling of this script
// is not through SSI (server side includes), but through javascript:
//
// Exapmle of calling view.php3:
//    <!--#include virtual="/apps/aa/view.php3?&vid=2&cmd[2]=c-1-Database" -->
//
// Exapmle of calling jsview.php3:
//    <script src="http://www.apc.org/apps/aa/jsview.php3?&vid=2&cmd[2]=c-1-Database"></script>

//expected  vid      // id of view
//optionaly cmd[]    // command to modify the view
                    // cmd[23]=v-25 means: show view id 25 in place of id 23
                    // cmd[23]=i-24-7464674747 means view
                    //   number 23 has to display item 74.. in format defined
                    //   in view 24
                    // cmd[23]=c-1-Environment means display view no 23 in place
                    //   of view no 23 (that's normal), but change value for
                    //   condition 1 to "Environment".
                    // cmd[23]=c-1-Environment-2-Jane means the same as above,
                    //   but there are redefined two conditions
                    // cmd[23]=d-headline........-LIKE-Profit-publish_date....-m:>-86400
                    //   generalized version of cmd[]-c
                    //      - fields and operators specifed
                    //      - unlimited number of conditions
                    //      - all default conditions from view definition are
                    //        completely redefined by the specified ones
//optionaly set[]    // setings to modify view behavior (can be combined with cmd)
                    // set[23]=listlen-20
                    // set[23]=mlx-EN-FR-DE
                    //   - sets maximal number of viewed items in view 23 to 20
                    //   - there can be more settings (future) - comma separated
//optionaly als[]    // user alias - see slice.php3 for more details
//
// please look into /view.php3 for more details

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


require_once "./include/config.php3";
require_once AA_INC_PATH."easy_scroller.php3";
require_once AA_INC_PATH."util.php3";
require_once AA_INC_PATH."item.php3";
require_once AA_INC_PATH."view.php3";
require_once AA_INC_PATH."discussion.php3";
require_once AA_INC_PATH."pagecache.php3";
require_once AA_INC_PATH."searchlib.php3";
$encap = true; // just for calling extsessi.php
require_once AA_INC_PATH."locsess.php3";    // DB_AA object definition

is_object( $db ) || ($db = getDB());

if (ctype_digit((string)$time_limit)) {
    @set_time_limit((int)$time_limit);
}

AA::$debug && AA::$dbg->group("/jsview.php3", "Start");

$view_param = ParseViewParameters();

if ($convertfrom) {
    $view_param['convertfrom'] = $convertfrom;
}
if ($convertto) {
    $view_param['convertto']   = $convertto;
}

//create keystring from values, which exactly identifies resulting content
$cache_key = get_hash($view_param, PageCache::globalKeyArray());

if ($cacheentry = $pagecache->getPage($cache_key, $nocache)) {
    $cacheentry->processPage();
} else {
    list($page_content, $cache_sid) = GetViewFromDB($view_param, true);

    // special for jsview - print it as javascript!!!
    // backslash quotes, remove newlines, escape </script, which will make the code broken
    $page_content = 'document.write(\''. escape4js($page_content) .'\');';

    $cacheentry = new AA_Cacheentry($page_content, AA::getHeaders());
    $cacheentry->processPage();

    if (!$nocache) {
        $str2find = new CacheStr2find($cache_sid, 'slice_id');
        $pagecache->storePage($cache_key, $cacheentry, $str2find);
    }
}

AA::$debug && AA::$dbg->groupend("/jsview.php3", "Completed view");

if (AA::$debug) {
    AA::$dbg->duration_stat();
}

exit;

?>
