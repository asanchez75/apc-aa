<?php
//$Id$
/*
Copyright (C) 1999, 2000 Association for Progressive Communications
http://www.apc.org/

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This programp is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program (LICENSE); if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//
// Miscellaneous utility functions
//

require_once $GLOBALS['AA_INC_PATH']."constants.php3";
require_once $GLOBALS['AA_INC_PATH']."mgettext.php3";
require_once $GLOBALS['AA_INC_PATH']."zids.php3";
require_once $GLOBALS['AA_INC_PATH']."logs.php3";
require_once $GLOBALS['AA_INC_PATH']."go_url.php3";

function get_aa_url($href, $session=true) {
    global $AA_INSTAL_PATH, $sess;
    return ($session AND is_object($sess)) ? $sess->url($AA_INSTAL_PATH.$href) :
                               $AA_INSTAL_PATH.$href;
}

function get_admin_url($href, $session=true) {
    return get_aa_url("admin/$href", $session);
}

/** returns url for $morehlp parameter in Frm* functions */
function get_help_url($href, $anchor) {
    return $href."#".$anchor;
}

/** Get <a href> tag */
function a_href($url, $txt) {
    return "<a href=\"$url\">$txt</a>";
}

// Expand return_url, possibly adding a session to it
function expand_return_url($session=true) {
    global $return_url, $sess;
    return ($session AND is_object($sess)) ? $sess->url($return_url) : $return_url;
}

// This function goes to either $return_url if set, or to $url
// if $usejs is set, then it will use inline Javascript, its not clear why this is done
//    sometimes (item.php3) but not others.
// if $session is set, then any session variable will be added, to the return_url case to allow for quicker 2nd access
//    session is always added to the other case
// if $add_param are set, then they are added to the cases EXCEPT return_url
function go_return_or_url($url, $usejs, $session, $add_param="") {
    global $return_url,$sess;
    if ($return_url) {
        go_url(expand_return_url($session), $add_param, $usejs);
    } elseif ($url) {
        go_url($sess->url($url), $add_param);
    }
    // Note if no $url or $return_url then drops through - this is used in index.php3
}

/** Adds slash at the end of a directory name if it is not yet there. */
function endslash(&$s) {
    if (strlen ($s) && substr ($s,-1) != "/")
        $s .= "/";
}

/** Wraps the in_array function, which was introduced only in PHP 4. */
function my_in_array($needle, $array) {
    return in_array($needle, $array);
}

/** To use this function, the file "debuglog.txt" must exist and have writing permission for the www server */
function debuglog($text)
{
    $f = fopen($GLOBALS['AA_INC_PATH']."logs.txt","a");
    if ($f) {
        fwrite($f, date( "h:i:s j-m-y ")  . $text . "\n");
        fclose($f);
    }
}

// adds all items from source to target, but doesn't overwrite items
function array_add($source, &$target)
{
    foreach ( (array)$source as $k => $v) {
        if (!isset($target[$k])) {
            $target[$k] = $v;
        } else {
            $target[]   = $v;
        }
    }
}

function self_complete_url() {
    return self_server().$GLOBALS[REQUEST_URI];
}

// returns server name with protocol and port
function self_server() {
  global $HTTP_HOST, $SERVER_NAME, $HTTPS, $SERVER_PORT;
  if ( isset($HTTPS) && $HTTPS == 'on' ){
    $PROTOCOL='https';
    if ($SERVER_PORT != "443")
      $port = ":$SERVER_PORT";
  } else {
    $PROTOCOL='http';
      if ($SERVER_PORT != "80")
      $port = ":$SERVER_PORT";
  }
  // better to use HTTP_HOST - if we use SERVER_NAME and we try to open window
  // by javascript, it is possible that the new window will be opened in other
  // location than window.opener. That's  bad because accessing window.opener
  // then leads to access denied javascript error (in IE at least)
  $sname = ($HTTP_HOST ? $HTTP_HOST : $SERVER_NAME);
  return("$PROTOCOL://$sname$port");
}

// returns server name with protocol, port and current directory of php script
function self_base() {
  global $PHP_SELF;
  return (self_server(). ereg_replace("/[^/]*$", "", $PHP_SELF) . "/");
}

/** On some serevers isn't defined DOCUMENT_URI
    (canaca.com 2003-09-19 - Apache/1.3.27 (Unix) (Red-Hat/Linux), Honza) */
function document_uri() {
    return get_if($_SERVER['DOCUMENT_URI'],$_SERVER['SCRIPT_URL']);
}

// returns server name with protocol, port and current directory of shtml file
function shtml_base() {
  return (self_server(). ereg_replace("/[^/]*$", "", document_uri()) . "/");
}

// returns url of current shtml file
function shtml_url() {
  return (self_server(). document_uri());
}

/** returns query string passed to shtml file (variables are not quoted) */
function shtml_query_string() {
    global $QUERY_STRING_UNESCAPED, $REDIRECT_QUERY_STRING_UNESCAPED, $REQUEST_URI;
    // there is problem (at least with $QUERY_STRING_UNESCAPED), when
    // param=a%26a&second=2 is returned as param=a\\&a\\&second=2 - we can't
    // expode it! - that's why we use $REQUEST_URI, if possible

    $ret_string = ($REQUEST_URI AND strpos($REQUEST_URI, '?')) ?
                            substr($REQUEST_URI, strpos($REQUEST_URI, '?')+1) :
                  ( isset($REDIRECT_QUERY_STRING_UNESCAPED)    ?
                            $REDIRECT_QUERY_STRING_UNESCAPED :
                            $QUERY_STRING_UNESCAPED );
    // get off magic quotes
    return magic_strip($ret_string);
}

// skips terminating backslashes
function DeBackslash($txt) {
    return str_replace('\\', "", $txt);        // better for two places
}

//explodes $param by ":". The "#:" means true ":" - don't separate
// Returns array
function ParamExplode($param) {
  $a = str_replace("#:", "__-__.", $param);    // dummy string
  $b = str_replace("://", "__-__2", $a);       // replace all <http>:// too
  $c = str_replace(":", "##Sx",$b);            // Separation string is //#Sx
  $d = str_replace("__-__.", ":", $c);         // change "#:" to ":"
  $e = str_replace("__-__2", "://", $d);       // change back "://"
  return explode("##Sx", $e);
}

function ParamImplode_replaceneeded($string) {
   $a = str_replace(":", "#:", $string);
   return $a;
}

function ParamImplode($param) {
   $param = array_map("ParamImplode_replaceneeded", $param);
   return implode(":", $param);
}

/** Adds variables passed by QUERY_STRING_UNESCAPED (or user $query_string)
*   to GLOBALS.
*/
function add_vars($query_string="") {
    $varstring = ( $query_string ? $query_string : shtml_query_string() );

    if ( !$varstring ) return;
    if ( ($pos = strpos('#', $varstring)) === true ) {  // remove 'fragment' part
        $varstring = substr($str,0,$pos);
    }
    // parse_str function is quite unusable, if used with magic_quotes_gpc ON
    // - it adds slashes not only to values, but ALSO to ARRAY KEYS!
    // we have to call magic_strip() to repair it
    parse_str($varstring, $aa_query_arr);
    // we also need PHP to think a['key'] is the same as a[key], that's why we
    // call NormalizeArrayIndex()
    $aa_query_arr = NormalizeArrayIndex(magic_strip($aa_query_arr));
    array_merge_append($GLOBALS, $aa_query_arr);
}

/** Removes starting and closing quotes from array index
 *   arr["key"]=...   transforms to arr[key]=...       */
function NormalizeArrayIndex($arr) {
    if (!is_array($arr)) {
        return $arr;
    }
    foreach ($arr as $k => $v) {
        if ( (($k{0}=='"') AND (substr($k,-1)=='"')) ||
             (($k{0}=="'") AND (substr($k,-1)=="'")) ) {
            $k = substr($k, 1, -1);
        }
        $ret[$k] = NormalizeArrayIndex($v);
    }
    return $ret;
}

/** Adds second array to the first one - values are appended to the array, if
 *  uses the same key (regardless if string or numeric!)
 *  Example:
 *    array_merge_append( $conds[0]['value']=x, $conds[0][operator]=LIKE )
 *    results in $conds[0] = array( 'value'=>'x', 'operator'=>'LIKE' )
 *  no PHP function do it ($a+$b nor array_merge()  array_merge_recursive())
 */
function array_merge_append(&$array, $newValues) {
    foreach ($newValues as $key => $value ) {
        if ( !isset($array[$key]) || !is_array($array[$key]) || !is_array($value)) {
            $array[$key] = $value;
        } else {
            $array[$key] = array_merge_append($array[$key], $value);
        }
    }
    return $array;
}

// function to double backslashes and apostrofs
function quote($str) {
  return addslashes($str);
}


// function addslashes enhanced by array processing
function AddslashesArray($val) {
  if (!is_array($val)) {
    return addslashes($val);
  }
  foreach ($val as $k => $v)
    $ret[$k] = AddslashesArray($v);
  return $ret;
}

function StripslashesArray($val) {
    if (!is_array($val)) {
       return stripslashes($val);
    }
    foreach ($val as $k => $v) {
        $ret[stripslashes($k)] = StripslashesArray($v);
    }
    return $ret;
}

// function for processing posted or get variables
// adds quotes, if magic_quotes are switched off
// except of variables in $skip array (usefull for 'encap' for example)
function QuoteVars($method="get", $skip='') {

  if ( get_magic_quotes_gpc() )
    return;

  $transfer = ( ($method == "get") ? "HTTP_GET_VARS" : "HTTP_POST_VARS");
  if ( !isset($GLOBALS[$transfer]) OR !is_array($GLOBALS[$transfer]))
    return;
  reset( $GLOBALS[$transfer] );
  while ( list($varname,$value) = each( $GLOBALS[$transfer] ))
    if ( !is_array($skip) || !isset($skip[$varname]) )
      $GLOBALS[$varname] = AddslashesArray($value);
}

// function to reverse effect of "magic quotes"
// not needed in MySQL and get_magic_quotes_gpc()==1
function dequote($str) {
    return $str;
}

// prints content of a (multidimensional) array
function p_arr_m($arr, $level = 0) {
   if ( !isset($arr) OR !is_array($arr)) {
     for ($i = 0; $i < $level; $i++) { echo "&nbsp;&nbsp;&nbsp;"; };
         echo ( isset($arr) ? " Not array: $arr <br>" : " (Empty Array) <br>");
     return;
   };
   while (list($key, $val) = each($arr)) {
      if ( is_array($val) ) {
         for ($i = 0; $i < $level; $i++) { echo "&nbsp;&nbsp;&nbsp;"; };
         echo htmlspecialchars($key) . " (Array) <br>";
         p_arr_m ($val, $level + 1);
      } else {
         for ($i = 0; $i < $level; $i++) { echo "&nbsp;&nbsp;&nbsp;"; };
         echo htmlspecialchars($key) . " => " . htmlspecialchars($val) . "<br>";
      }
   }
}

// debug function, prints hash size,  keys and values of hash
function p_arr($a,$name="given array") {
    p_arr_m($a);
}

// returns new unpacked md5 unique id, except these which can  force unexpected end of string
function new_id($seed="hugo"){
    do {
        $id = md5(uniqid($seed));
    } while ((strpos($id, '00')!==false) OR (strpos($id, '27')!==false) OR (substr($id,30,2)=='20'));
      // '00' is end of string, '27' is ' and packed '20' is space,
      // which is removed by MySQL
    return $id;
}

/** Returns a unique id from a string.
 *  Note that it will always return the same id from the same string so it
 *  can be used to compare the hashes as well as create new item id (combining
 *  item id of fed item and slice_id, for example - @see xml_fetch.php3)
 */
function string2id($str) {
    do {
        $id  = md5($str);
        $str = $str . " ";
    } while ((strpos($id, '00')!==false) OR (strpos($id, '27')!==false) OR (substr($id,30,2)=='20'));
      // '00' is end of string, '27' is ' and packed '20' is space,
      // which is removed by MySQL
    return $id;
}

// returns packed md5 id, not quoted !!!
// Note that pack_id is used in many places where it is NOT 128 bit ids.
function pack_id($unpacked_id) {
    global $errcheck;
    // Give up tracking this, too many errors in Honza's code!
    /*
    if ($errcheck && !preg_match("/^[0-9a-f]+$/", $unpacked_id)) // Note was + instead {32}
         huhe("Warning: trying to pack $unpacked_id.<br>\n");
    */
    return ((string)$unpacked_id == "0" ? "0" : pack("H*",trim($unpacked_id)));
}

// returns unpacked md5 id
function unpack_id($packed_id){
    if ((string)$packed_id == "0") {
        return "0";
    }
    $foo = bin2hex($packed_id);  // unpack("H*", $str) does not work in PHP 4.0.3 so bin2hex used
    return (string)$foo;
}

/** returns current date/time as timestamp;
 *  $step - time could be returned in steps (good for database query speedup)
 */
function now($step=false) {
    return (($step!='step') ?
        time() :
        ((int)(time()/QUERY_DATE_STEP)+1)*QUERY_DATE_STEP);     // round up
}


// returns number of second since 1970 from date in MySQL format
function date2sec($dat) {
    if ( Ereg("^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})", $dat, $d)) {
        return MkTime($d[4], $d[5], $d[6], $d[2], $d[3], $d[1]);
    }
    return 0;
}

// function which detects the browser
function detect_browser() {
  global $HTTP_USER_AGENT, $BName, $BVersion, $BPlatform;

  // Browser
  if (eregi("(msie) ([0-9]{1,2}.[0-9]{1,3})",$HTTP_USER_AGENT,$match))
    { $BName = "MSIE"; $BVersion=$match[2]; }
  elseif (eregi("(opera) ([0-9]{1,2}.[0-9]{1,3}){0,1}",$HTTP_USER_AGENT,$match) || eregi("(opera/)([0-9]{1,2}.[0-9]{1,3}){0,1}",$HTTP_USER_AGENT,$match))
    { $BName = "Opera"; $BVersion=$match[2]; }
  elseif (eregi("(konqueror)/([0-9]{1,2}.[0-9]{1,3})",$HTTP_USER_AGENT,$match))
    { $BName = "Konqueror"; $BVersion=$match[2]; }
  elseif (eregi("(lynx)/([0-9]{1,2}.[0-9]{1,2}.[0-9]{1,2})",$HTTP_USER_AGENT,$match))
    { $BName = "Lynx"; $BVersion=$match[2]; }
  elseif (eregi("(links) \(([0-9]{1,2}.[0-9]{1,3})",$HTTP_USER_AGENT,$match))
    { $BName = "Links"; $BVersion=$match[2]; }
  elseif (eregi("(netscape6)/(6.[0-9]{1,3})",$HTTP_USER_AGENT,$match))
    { $BName = "Netscape"; $BVersion=$match[2]; }
  elseif (eregi("Gecko/",$HTTP_USER_AGENT))
    { $BName = "Mozilla"; $BVersion="6";}
  elseif (eregi("mozilla/5",$HTTP_USER_AGENT))
    { $BName = "Netscape"; $BVersion="Unknown"; }
  elseif (eregi("(mozilla)/([0-9]{1,2}.[0-9]{1,3})",$HTTP_USER_AGENT,$match))
    { $BName = "Netscape"; $BVersion=$match[2]; }
  elseif (eregi("w3m",$HTTP_USER_AGENT))
    { $BName = "w3m"; $BVersion="Unknown"; }
  else{$BName = "Unknown"; $BVersion="Unknown";}

  // System
  if (eregi("win32",$HTTP_USER_AGENT))
    $BPlatform = "Windows";
  elseif ((eregi("(win)([0-9]{2})",$HTTP_USER_AGENT,$match)) || (eregi("(windows) ([0-9]{2})",$HTTP_USER_AGENT,$match)))
    $BPlatform = "Windows $match[2]";
  elseif (eregi("(winnt)([0-9]{1,2}.[0-9]{1,2}){0,1}",$HTTP_USER_AGENT,$match))
    $BPlatform = "Windows NT $match[2]";
  elseif (eregi("(windows nt)( ){0,1}([0-9]{1,2}.[0-9]{1,2}){0,1}",$HTTP_USER_AGENT,$match))
    $BPlatform = "Windows NT $match[3]";
  elseif (eregi("linux",$HTTP_USER_AGENT))
    $BPlatform = "Linux";
  elseif (eregi("mac",$HTTP_USER_AGENT))
    $BPlatform = "Macintosh";
  elseif (eregi("(sunos) ([0-9]{1,2}.[0-9]{1,2}){0,1}",$HTTP_USER_AGENT,$match))
    $BPlatform = "SunOS $match[2]";
  elseif (eregi("(beos) r([0-9]{1,2}.[0-9]{1,2}){0,1}",$HTTP_USER_AGENT,$match))
    $BPlatform = "BeOS $match[2]";
  elseif (eregi("freebsd",$HTTP_USER_AGENT))
    $BPlatform = "FreeBSD";
  elseif (eregi("openbsd",$HTTP_USER_AGENT))
    $BPlatform = "OpenBSD";
  elseif (eregi("irix",$HTTP_USER_AGENT))
    $BPlatform = "IRIX";
  elseif (eregi("os/2",$HTTP_USER_AGENT))
    $BPlatform = "OS/2";
  elseif (eregi("plan9",$HTTP_USER_AGENT))
    $BPlatform = "Plan9";
  elseif (eregi("unix",$HTTP_USER_AGENT) || eregi("hp-ux",$HTTP_USER_AGENT))
    $BPlatform = "Unix";
  elseif (eregi("osf",$HTTP_USER_AGENT))
    $BPlatform = "OSF";
  else{$BPlatform = "Unknown";}

  if ($GLOBALS[debug]) huhl("$HTTP_USER_AGENT => $BName,$BVersion,$BPlatform");
}

/** variable count of variables */
function debug() {
    // could be toggled from Item Manager left menu 'debug' (by Superadmins!)
    if ( $_COOKIE['aa_debug'] != 1 ) return;
    echo "<pre>\n";
    $messages = func_get_args();
    foreach ( $messages as $msg ) {
        huhlo( $msg );
        echo "<br>\n";
    }
    echo "</pre>\n";
}

// debug function for printing debug messages
function huh($msg, $name="") {
    //  if (! $GLOBALS['debug'] )
    //    return;
    if ( @is_array($msg) OR @is_object($msg) ) {
        echo "<br>\n$name";
        print_r($msg);
    } else {
        echo "<br>\n$name$msg";
    }
}

// debug function for printing debug messages escaping HTML
function huhw($msg) {
    if (!$GLOBALS['debug'] ) {
        return;
    }
    echo "<br>\n". HTMLspecialChars($msg);
}

// Report only if errcheck is set, this is used to test for errors to speed debugging
// Use to catch cases in the code which shouldn't exist, but are handled anyway.
function huhe ($a, $b="", $c="",$d="",$e="",$f="",$g="",$h="",$i="",$j="") {
    global $errcheck;
    if ($errcheck) {
        huhl($a, $b, $c,$d,$e,$f,$g,$h,$i,$j);
        if ($GLOBALS["trace"] || $GLOBALS["debug"]) { trace("p"); }
    }
}
// Only called from within huhl
function huhlo($a) {
    if (isset($a)) {
        if (is_object($a) && is_callable(array($a,"printobj"))) {
            $a->printobj();
        } else {
            print_r($a);
        }
    }
}

function get_microtime() {
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}

// Set a starting timestamp, if checking times, huhl can report
// Debug function to print debug messages recursively - handles arrays
function huhl ($a, $b="", $c="",$d="",$e="",$f="",$g="",$h="",$i="",$j="") {
    global $debugtimes,$debugtimestart;
    if (isset($a)) {
        print("<listing>");
        if ($debugtimes) {
           if (! $debugtimestart) {
                $debugtimestart = get_microtime();
            }
            print("Time: ".get_microtime() - $debugtimestart."\n");
        }
        huhlo($a);
        huhlo($b);
        huhlo($c);
        huhlo($d);
        huhlo($e);
        huhlo($f);
        huhlo($g);
        huhlo($h);
        huhlo($i);
        huhlo($j);
        print("</listing>\n");
    }
}

function huhsess($msg="") {
    global $sess;
    foreach (array_keys($sess->pt) as $i) {
        $sessvars[$i]=$GLOBALS[$i];
    }
    huhl($msg,$sessvars);
}

//Prints all values from array
function PrintArray($a) {
    if (is_array($a)) {
        while ( list( $key, $val ) = each( $a ) ) {
            if (is_array($val)) {
               PrintArray($val);
            } else {
               echo $val;
            }
        }
    }
}

//Prepare OK Message
function MsgOK($txt){
  return "<div class=okmsg>$txt</div>";
}

//Prepare Err Message
function MsgERR($txt){
  return "<div class=err>$txt</div>";
}

// function for unpacking string in edit_fields and needed_fields in database to array
function UnpackFieldsToArray($packed, $fields) {
  reset($fields);
  $i=0;
  while ( list($field,) = each($fields))
    $arr[$field] = (substr($packed,$i++,1)=="y" ? true : false);
  return $arr;
}

// returns true, if specified input show func (name in $which) have constants
function HaveConstants($which) {
    $inputtypes = inputShowFuncTypes();
    foreach ( $inputtypes as $key => $values) {
        if (substr($values['paramformat'], 4, 5) == "const") {
            if ($which == $key) { return true;}
        }
        $i++;
    }
    return false;
}

// returns true if constants are from slice
function AreSliceConstants($name) {
    if ( substr($name,0,7) == "#sLiCe-" )  // prefix indicates select from items
        return true;
   else
        return false;
}

/** Function fills the array from constants table
 *  @param $column - column used as values. We can use 'name' as well as
 *                   'const_name' for name of fields
 */
function GetConstants($group, $order='pri', $column='name', $keycolumn='value') {
    // we can use 'const_name' instedad of real name of the column 'name' => translate
    $order     = str_replace( 'const_', '', $order);
    $column    = str_replace( 'const_', '', $column);
    $keycolumn = str_replace( 'const_', '', $keycolumn);

    $const_fields = array('id'=>1,'group_id'=>1,'name'=>1,'value'=>1,'class'=>1,'pri'=>1,'ancestors'=>1,'description'=>1,'short_id'=>1);

    $db = getDB();
    if (  $const_fields[$order] )     { $order_by  = "ORDER BY $order"; }
    if ( !$const_fields[$column] )    { $column    = 'name';            }
    if ( !$const_fields[$keycolumn] ) { $keycolumn = 'value';           }
    $fields = ($column==$keycolumn ? $column : "$keycolumn, $column");

    $db->tquery("SELECT $fields FROM constant WHERE group_id='$group' $order_by");
    while ($db->next_record()) {
        $key = $db->f($keycolumn);
        // generate unique keys by adding space
        while ( $already_key[$key] ) {
            $key .= ' ';                   // add space in order we get unique keys
        }
        $already_key[$key] = true;       // mark the $key
        $arr[$key] = $db->f($column);
    }
    freeDB($db);
    return $arr;
}

// gets fields from main table of the module
function GetModuleInfo($module_id, $type) {
    global $MODULES;
    if (!$module_id) {
        return false;
    }
    $p_module_id = q_pack_id($module_id);

    $SQL = "SELECT * FROM " .$MODULES[$type]['table']. " WHERE id = '$p_module_id'";
    $ret = GetTable2Array($SQL, 'aa_first', 'aa_fields');
    if ( $ret AND $ret['reading_password'] ) {
        // do it more secure and do not store it plain
        // (we should be carefull - mainly with debug outputs)
        $ret['reading_password'] = md5($ret['reading_password']);
    }
    return $ret;
}

// gets slice fields
function GetSliceInfo($slice_id) {
  return  GetModuleInfo($slice_id,'S');
}

/*Obsoleted - see viewobj.php3 (mitra)
// gets view fields
function GetViewInfo($vid) {
  global $db;
  $db->query("SELECT view.*, slice.deleted FROM view, slice
               WHERE slice.id=view.slice_id
                 AND view.id='$vid'");
  return  ($db->next_record() ? $db->Record : false);
}
*/

/** function converts table from SQL query to array
 *  $key    - return array's key - 'NoCoLuMn' | '' | 'aa_first' | <database_column> | 'unpack:<database_column>'
 *  $values - return array's val - 'aa_all' |
 *                                 'aa_mark' |
 *                                 'aa_fields' |
 *                                 <database_column> |
 *                                 'unpack:<database_column>' |
 *                                 true
 */
function GetTable2Array($SQL, $key="id", $values='aa_all') {
    $db = getDB();
    $db->tquery($SQL);
    while ($db->next_record()) {
        if ($values == 'aa_all') {
            $val = $db->Record;
        } elseif ($values == 'aa_mark') {
            $val = true;
        } elseif (substr($values,0,7) == 'unpack:') {
            $val = unpack_id128($db->f(substr($values,7)));
        } elseif (is_string($values) AND isset( $db->Record[$values] )) {
            $val = $db->Record[$values];
        } else {  // true or 'aa_fields'
            $val = DBFields($db);
        }

        if ( $key == 'aa_first' ) {
            freeDB($db);
            return $val;
        } elseif ( ($key == "NoCoLuMn") OR !$key ) {
            $arr[] = $val;
        } elseif ( substr($key,0,7) == 'unpack:' ) {
            $arr[unpack_id128($db->f(substr($key,7)))] = $val;
        } else {
            $arr[$db->f($key)] = $val;
        }
    }
    freeDB($db);
    return isset($arr) ? $arr : false;
}

/** Returns list of fields which belongs to the slice
 *  The result is in two arrays - $fields    (key is field_id)
 *                              - $prifields (just field_id sorted by priority)
 *  @param $slice_id       - id of slice for which you want to get fields array
 *  @param $slice_fields   - if true, the result contains only "slice fields"
 *                           which are not used for items, but rather for slice
 *                           setting
 *  @see sliceobj:slice->fields()
 */
function GetSliceFields($slice_id, $slice_fields = false) {
    $p_slice_id = q_pack_id($slice_id);
    $db = getDB();
    // slice_fields are begins with underscore
    // slice fields are the fields, which we do not use for items in the slice,
    // but rather for setting parameters of the slice
    $slice_fields_where = ($slice_fields) ? "AND id LIKE '\_%'" : "AND id NOT LIKE '\_%'";
    $SQL = "SELECT * FROM field WHERE slice_id='$p_slice_id' $slice_fields_where ORDER BY input_pri";
    $db->query($SQL);
    while ($db->next_record()) {
        $fid          = $db->f("id");
        $fields[$fid] = $db->Record;
        $prifields[]  = $fid;
    }
    freeDB($db);
    return array($fields, $prifields);
}

function GetFields4Select($slice_id, $slice_fields = false, $order = 'name', $add_empty = false) {
    $p_slice_id = q_pack_id($slice_id);
    $db = getDB();
    if ($slice_fields == 'all') {
        // all fields (item as well as slice fields)
        $slice_fields_where = '';
    } elseif (!$slice_fields) {
        // only item fields (not begins with underscore)
        $slice_fields_where = "AND id NOT LIKE '\_%'";
    } else {
        // only slice fields (begins with underscore)
        $slice_fields_where = "AND id LIKE '\_%'";
    }
    $db->query("SELECT id, name FROM field WHERE slice_id='$p_slice_id' $slice_fields_where ORDER BY $order");
    $lookup_fields = array();
    if ($add_empty) {
        $lookup_fields[''] = " ";  // default - none
    }
    while ($db->next_record()) {
        $lookup_fields[$db->f('id')] = $db->f(name);
    }
    return $lookup_fields;
}

/** Returns true, if the passed field id looks like slice setting field
 *  "slice fields" are not used for items, but rather for slice setting.
 *  Such fields are destinguished by underscore on first letter of field_id
 */
function isSliceField($field_id) {
    return $field_id AND ($field_id{0} == '_');
}

/** Create field id from type and number */
function CreateFieldId($ftype, $no="0") {
    if ((string)$no == "0") {
        $no = "";    // id for 0 is "xxxxx..........."
    }
    return $ftype. substr("................$no", -(16-strlen($ftype)));
}

/** get field type from id (works also for AA_Core_Fields (without dots)) */
function GetFieldType($id) {
    $dot_pos = strpos($id, ".");
    return ($dot_pos === false) ? $id : substr($id, 0, $dot_pos);
}

/** get field number from id ('.', '0', '1', '12', ... ) */
function GetFieldNo($id) {
    return (string)substr(strrchr($id,'.'), 1);
}

// -------------------------------------------------------------------------------

// helper function for GetItemContent and such functions
function itemContent_getWhere($zids, $use_short_ids=false) {
    // convert array or single value to zids
    if ( !is_object($zids) ) {
        $zids = new zids( $zids, $use_short_ids ? 's' : 'l' );
    }
    $sel_in = $zids->sqlin( false );
    if ($zids->onetype() == "t") {
        $settags = true;  // Used below
    }
    return array( $sel_in, $settags );
}

/** Basic function to get item content. Use this function, not direct SQL queries.
*
*   @param bool  $ignore_reading_password
*       Use carefully only when you are sure the data is used safely and not viewed
*       to unauthorized persons.
*   @param array $fields2get
*       restrict return fields only to listed fields (so the content4id array
*       is not so big)
*       like: array('headline........', 'category.......1')
*       (only content table fields are restricted (yet))
*/
function GetItemContent($zids, $use_short_ids=false, $ignore_reading_password=false, $fields2get=false) {
    // Fills array $content with current content of $sel_in items (comma separated ids).
    trace("+","GetItemContent",$zids);

    $db = getDB();

    // construct WHERE clause
    list($sel_in, $settags) = itemContent_getWhere($zids, $use_short_ids);
    if (!$sel_in) { freeDB($db); trace("-"); return false; }

    // get content from item table
    $delim = "";

    if ( is_object($zids) ) {
        if ( $zids->onetype() == 's' )
            $use_short_ids = true;
    }

    $id_column = ($use_short_ids ? "short_id" : "id");
    $SQL = "SELECT item.*, slice.reading_password
            FROM item INNER JOIN slice ON item.slice_id = slice.id
            WHERE item.$id_column $sel_in";
    $db->tquery($SQL);

    $n_items = 0;
    while ( $db->next_record() ) {
        // proove permissions for password-read-protected slices
        $reading_permitted = $ignore_reading_password
           || ($db->f("reading_password") == "")
           || ($db->f("reading_password") == $GLOBALS["slice_pwd"]);
        $item_permitted[$db->f("id")] = $reading_permitted;

        $n_items = $n_items+1;
        reset( $db->Record );
        if ( $use_short_ids ) {
            $foo_id = $db->f("short_id");
            $translate[unpack_id128($db->f("id"))] = $db->f("short_id"); // id -> short_id
            // WHERE for query to content table
            $new_sel_in .= "$delim '". quote($db->f("id")) ."'";
            $delim = ",";
        } else {
            $foo_id = unpack_id128($db->f("id"));
        }
        // Note that it stores into the $content[] array based on the id being used which
        // could be either shortid or longid, but is NOT tagged id.
        while (list($key, $val) = each($db->Record)) {
            // we need only item fields
            if (!is_numeric($key) AND ($key != 'reading_password')) {
                $content[$foo_id][CreateFieldId($key)][] = array(
                     "value" => $reading_permitted ? $val : _m("Error: Missing Reading Password"));
            }
        }
    }

    // Skip the rest if no items found
    if ($n_items == 0) { freeDB($db); trace("-"); return null; }

    // If its a tagged id, then set the "idtag..........." field
    if ($settags) {
        $tags = $zids->gettags();
        while ( list($k,$v) = each($tags)) {
            $content[$k]["idtag..........."][] = array("value" => $v);
        }
    }

    // construct WHERE query to content table if used short_ids
    if ( $use_short_ids) {
        $sel_in = (count($translate) > 1) ? " IN ( $new_sel_in ) " : " = $new_sel_in ";
    }

    if ( isset( $fields2get ) AND is_array( $fields2get ) AND (count($fields2get)>0 )) {
        $restrict_field = join( "','", $fields2get );
        $restrict_cond  = (count($fields2get) > 1) ?
                       " AND field_id IN ( '". $restrict_field ."' ) " :
                       " AND field_id = '$restrict_field' ";
    }

    // get content from content table

    // feeding - don't worry about it - when fed item is updated, informations
    // in content table is updated too

    $SQL = "SELECT * FROM content
             WHERE item_id $sel_in $restrict_cond
             ORDER BY content.number"; // usable just for constants

    $db->tquery($SQL);

    while ( $db->next_record() ) {

        $fooid = ($use_short_ids ? $translate[unpack_id128($db->f("item_id"))] :
                                   unpack_id128($db->f("item_id")) );

        if ( !$item_permitted[$db->f("item_id")] ) {
            $content[$fooid][$db->f("field_id")][0] = array( "value" => _m("Error: Missing Reading Password"));
            continue;
        }

        // which database field is used (from 05/15/2004 we have FLAG_TEXT_STORED set for text-field-stored values
        $db_field = ( ($db->f("text")!="") OR ($db->f("flag") & FLAG_TEXT_STORED) ) ? 'text' : 'number';
        $content[$fooid][$db->f("field_id")][] = array( "value" => $db->f($db_field),
                                                        "flag"  => $db->f("flag") );
    }

    // add special fields to all items (zids)
    foreach ($content as $iid => $foo ) {
        // slice_id... and id... is packed  - add unpacked variant now
        $content[$iid]['u_slice_id......'][] =
            array('value' => unpack_id128($content[$iid]['slice_id........'][0]['value']));
        $content[$iid]['unpacked_id.....'][] =
            array('value' => unpack_id128($content[$iid]['id..............'][0]['value']));
    }

    freeDB($db);
    trace("-");
    return $content;   // Note null returned above if no items found
}

// fills content arr with current content of $sel_in items (comma separated short ids)
function GetItemContent_Short($ids) {
    GetItemContent($ids, true);
}

/** The same as GetItemContent function, but it returns just id and short_id
 *  (or other fields form item table - specified in $fields2get) for the item
 *  (used in URL listing view @see view_type['urls']).
 *  If $fields2get is specified, it MUST contain at least 'id'.
 */
function GetItemContentMinimal($zids, $fields2get=false) {
  if ( !$fields2get ) {
      $fields2get = array( 'id', 'short_id' );
  }
  $db = getDB();
  $columns = join(',',$fields2get);

  // construct WHERE clause
  list($sel_in, $settags) = itemContent_getWhere($zids);
  if ($sel_in) {
      // get content from item table
      $delim = "";
      $SQL = "SELECT $columns FROM item WHERE id $sel_in";
      $db->tquery($SQL);
      $n_items = 0;
      while ( $db->next_record() ) {
          $n_items++;
          $foo_id = unpack_id128($db->f("id"));
          foreach ( $fields2get as $fld ) {
              $content[$foo_id][CreateFieldId($fld)][] = array("value" => $db->f($fld));
          }
      }
  }

  freeDB($db);
  return ($n_items == 0) ? null : $content;   // null returned if no items found
}

/** Fills Abstract data srtructure for Constants */
function GetConstantContent( $zids ) {
  if ( !$zids ) return false;
  $db = getDB();

  $SQL = 'SELECT * FROM constant WHERE short_id '. $zids->sqlin(false);
  $db->tquery( $SQL );
  $i=1;
  while ($db->next_record()) {
    $coid = $db->f('short_id');
    $content[$coid]["const_name"][]        = array( "value"=> $db->f("name") );
    $content[$coid]["const_value"][]       = array( "value"=> $db->f("value"),
                                                    "flag" => FLAG_HTML );
    $content[$coid]["const_pri"][]         = array( "value"=> $db->f("pri") );
    $content[$coid]["const_group"][]       = array( "value"=> $db->f("group_id") );
    $content[$coid]["const_class"][]       = array( "value"=> $db->f("class") );
    $content[$coid]["const_counter"][]     = array( "value"=> $i++ );
    $content[$coid]["const_id"][]          = array( "value"=> unpack_id128($db->f("id") ));
    $content[$coid]["const_description"][] = array( "value"=> $db->f("description"),
                                                    "flag" => FLAG_HTML);
    $content[$coid]["const_short_id"][]    = array( "value"=> $db->f("short_id") );
    $content[$coid]["const_level"][]       = array( "value"=> strlen($db->f("ancestors"))/16);
  }
  freeDB($db);

  return $content;
}

/** Just helper function for storing data from database to Abstract Data Structure */
function StoreTable2Content(&$db, &$content, $SQL, $prefix, $id_field) {
    $db->tquery($SQL);
    while ( $db->next_record() ) {
        $foo_id = $db->f($id_field);
        reset( $db->Record );
        while ( list( $key, $val ) = each( $db->Record )) {
            if ( !is_int($key))
                $content[$foo_id][$prefix . $key][] = array('value' => $val);
        }
    }
}

// -------------------------------------------------------------------------------

function GetHeadlineFieldID($sid, $slice_field="headline.") {
  $db = getDB();

  // get id of headline field
  $SQL = "SELECT id FROM field
           WHERE slice_id = '". q_pack_id( $sid ) ."'
             AND id LIKE '$slice_field%'
        ORDER BY id";
  $db->query( $SQL );
  $ret = ( $db->next_record() ? $db->f(id) : false );
  freeDB($db);
  return $ret;
}

// -------------------------------------------------------------------------------
// returns group_id from $show_input_func string
function GetCategoryGroupId($input_show_func) {
    $arr = explode( ":", $input_show_func);
    return $arr[1];
}

// find group_id for constants of the slice
function GetCategoryGroup($slice_id, $field='') {
    global $db;

    $condition = $field ? "id = '$field'" : "id LIKE 'category%'";
    $SQL  = "SELECT input_show_func FROM field
              WHERE slice_id='". q_pack_id($slice_id) ."'
                AND $condition
                ORDER BY id";  // first should be category........,
                               // then category.......1, etc.
    $db->tquery($SQL);
    if ( $db->next_record() ){
        return GetCategoryGroupId($db->f('input_show_func'));
    } else {
        return false;
    }
}

// -------------------------------------------------------------------------------

// returns field id of field which stores category (usually "category........")
function GetCategoryFieldId( $fields ) {
  $no = 10000;
  if ( isset($fields) AND is_array($fields) ) {
    reset( $fields );
    while ( list( $k,$val ) = each( $fields ) ) {
      if ( substr($val[id], 0, 8) != "category" )
        continue;
      $last = GetFieldNo($val[id]);
      $no = min( $no, ( ($last=='') ? -1 : (integer)$last) );
    }
  }
  if ($no==10000)
    return false;
  $no = ( ($no==-1) ? '.' : (string)$no);
  return CreateFieldId("category", $no);
}

// -------------------------------------------------------------------------------

// get id from item short id
function GetId4Sid($sid) {
  global $db;

  if (!$sid)
    return false;
  $SQL = "SELECT id FROM item WHERE short_id='$sid'";
  $db->query( $SQL );
  if ( $db->next_record() )
    return unpack_id128($db->f("id"));
  return false;
}

// -------------------------------------------------------------------------------

// get short item id item short id
function GetSid4Id($iid) {
  global $db;

  if (!$iid)
    return false;
  $SQL = "SELECT short_id FROM item WHERE id='". q_pack_id($iid) ."'";
  $db->query( $SQL );
  if ( $db->next_record() )
    return $db->f("short_id");
  return false;
}

// -------------------------------------------------------------------------------

// in_array and compact is available since PHP4
if (substr(PHP_VERSION, 0, 1) < "4") {
  function in_array($needle,$haystack){
    if (!is_array($haystack)) return false;
    reset ($haystack);
    while (list (,$val) = each ($haystack))
        if ($val == $needle)
            return true;
    return false;
  }
}

// Parses the string xxx:yyyy (database stored func) to arr[fce]=xxx [param]=yyyy
function ParseFnc($s) {
  $pos = strpos($s,":");
  if ( $pos ) {
    $arr[fnc] = substr($s,0,$pos);
    $arr[param] = substr($s,$pos+1);
  } else
    $arr[fnc] = $s;
  return $arr;
}

// returns html safe code (used for preparing variable to print in form)
function safe( $var ) {
  return htmlspecialchars( stripslashes($var) );  // stripslashes function added because of quote varibles sended to form before
}

// is the browser able to show rich edit box? (using triedit.dll)
function richEditShowable () {
  global $BName, $BVersion, $BPlatform;
    global $showrich;
    detect_browser();
  // Note that Macintosh IE 5.2 does not support either richedit or current iframe
  // Mac Omniweb/4.1.1 detects as Netscape 4.5 and doesn't support either
  return (($BName == "MSIE" && $BVersion >= "5.0" && $BPlatform != "Macintosh") || $showrich > "");
  // Note that RawRichEditTextarea could force iframe for certain BPlatform
}

/** Is it valid e-mail */
function valid_email( $str ) {
    return EReg("^.+@.+\..+", $str);  // should be improved
}

function clean_email($line) {
  // consider using imap_rfc822_parse_adrlist
  // file://localhost/usr/local/doc/php/function.imap-rfc822-parse-adrlist.html
  //string ereg_replace (string pattern, string replacement, string string);

  $patterns = array ('/^\s+/', '/\s+$/');
  $replace  = array ('', '');
  return preg_replace ($patterns, $replace, $line);
  /*  $line = ereg_replace ('^\s+','', $line);
    $line = ereg_replace ('\s+$','',$line);
    return $line;
  */
}

/**
* Prints HTML start page tags (html begin, encoding, style sheet, but no title).
* Chooses the right encoding by get_mgettext_lang().
* @param string $stylesheet  if empty, no StyleSheet tag is printed
* @param bool   $js_lib      if true, includes js_lib.js javascript
*/
function HtmlPageBegin($stylesheet='default', $js_lib=false) {
    if ($stylesheet == "default")
        $stylesheet = $GLOBALS["AA_INSTAL_PATH"].ADMIN_CSS;
    echo
'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">
  <HTML>
    <HEAD>
      <LINK rel="SHORTCUT ICON" href="'.$GLOBALS['AA_INSTAL_PATH'] .'images/favicon.ico">';
    if ($stylesheet) echo '
      <LINK rel="StyleSheet" href="'.$stylesheet.'" type="text/css">';
    echo '
      <META http-equiv="Content-Type" content="text/html; charset='
        .$GLOBALS["LANGUAGE_CHARSETS"][get_mgettext_lang()].'">
';
    if ($js_lib) FrmJavascriptFile( 'javascript/js_lib.js' );
}

// use instead of </body></html> on pages which show menu
function HtmlPageEnd() {
  echo "
    </TD></TR></TABLE>
    </TD></TR></TABLE>
    </BODY></HTML>";
}

function getHtmlPage($param='') {
    $ret  = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
    $ret .= "\n<html>\n<head>";
    if ($param['title']) {
        $ret .= "\n    <title>".$param['title']."</title>";
    }
    foreach ( (array)$param['css'] as $css ) {
        $ret .= "\n    <link rel=\"StyleSheet\" href=\"".$css."\" type=\"text/css\">";
    }
    foreach ( (array)$param['js'] as $js ) {
        $ret .= "\n    ". getJavascriptFile( $js );
    }
    $ret .= "\n</head>\n<body>\n";
    $ret .= $param['body'];
    $ret .= "\n</body>\n</html>";
    return $ret;
}

function FrmHtmlPage($param='') {
    echo getHtmlPage($param);
}

// Displays page with message and link to $url
//   url - where to go if user clicks on Back link on this message page
//   msg - displayed message
//   dummy - was used in past, now you should use MsgPageMenu from msgpage.php3
function MsgPage($url, $msg, $dummy="standalone") {
  HtmlPageBegin();   // Print HTML start page tags (html begin, encoding, style sheet, but no title)

  echo "<title>"._m("Toolkit news message")."</title>
    </head>
  <body>";

  if ( isset($msg) AND is_array($msg))
    PrintArray($msg);
   else
    echo "<P>$msg</p><br><br>";
  echo "<a href=\"$url\">"._m("Back")."</a>";
  echo "</body></html>";
  page_close();
  exit;
}


// function returns true if $fld fits the field scheme (used in unaliasing)
function IsField($fld) {
    if ( !isset($GLOBALS['LINKS_FIELDS']) ) {
         $GLOBALS['LINKS_FIELDS'] = GetLinkFields();
         $GLOBALS['CATEGORY_FIELDS'] = GetCategoryFields();
         $GLOBALS['CONSTANT_FIELDS'] = GetConstantFields();
    }
    // changed this from [a-z_]+\.+[0-9]*$ because of alerts[12]....abcde
    return( ((strlen($fld)==16) && ereg("^[a-z0-9_]+\.+[0-9A-Za-z]*$",$fld))
           OR $GLOBALS['LINKS_FIELDS'][$fld]
           OR $GLOBALS['CATEGORY_FIELDS'][$fld]
           OR $GLOBALS['CONSTANT_FIELDS'][$fld] );
}

/**
 * Fulltext is viewed - count hit
 *
 * UPDATE - hits logged to table log. With COUNTHIT_PROBABILITY
 * (eg. onetimes from 100 - probability 0.01) we write logged hits into table
 * item. Why this way? MySQL lock the item table for updte when someone do
 * a search in that table. If we want to view any fulltext, we can't, because we
 * have to wait for item.display_count update (which is locked). That's why we
 * log the hit into log table and from time to time (with probability 1:100) we
 * update item table based on logs.
 * @param string $id    id - short, long or tagged - it does not matter
 *                      (zids decides)
 */
function CountHit($id) {
    global $db;

    writeLog("COUNT_HIT",$id);

    $zid = new zids();

    if ( rand(0,COUNTHIT_PROBABILITY) == 1) {
        $logarray = getLogEvents("COUNT_HIT", $from="", $to="", true, true);
        if ( isset($logarray) AND is_array($logarray) ) {
            foreach ($logarray as $log) {
                $myid = $log["params"];
                $zid->refill($myid);
                switch ($zid->onetype()) {
                    case "l":
                    case "t":
                        $myid = $zid->q_packedids(0);
                        $where = "(id='".$myid."')";
                        break;
                    case "s":
                        $myid = $zid->shortids(0);
                        $where = "(short_id='".$myid."')";
                        break;
                    default:
                }
                $zid->clear();
                $SQL = "UPDATE item
                           SET display_count=(display_count+".$log["count"].")
                         WHERE $where";
                $db->tquery($SQL);
            }
        }
    }
}


function is_field_type_numerical ($field_type) {
    $number_db_types = array ("float","double","decimal","int", "timestamp");
    reset ($number_db_types);

    while (list (,$n_col) = each ($number_db_types))
        if (strstr ($field_type, $n_col))
            return true;

    return false;
}

// -----------------------------------------------------------------------------
/** Copies rows within a table changing only given columns and omitting given columns.
*   @author Jakub Ad�mek
*	@return bool  true if all additions succeed, false otherwise
*
*   @param string $table    table name
*   @param string $where    where condition (filter)
*   @param array  $set_columns  array ($column_name => $value, ...) - fields the value of which will be changed
*   @param array  $omit_columns [optional] array ($column_name, ...) - fields to be omitted
*   @param array  $id_columns   [optional] array ($column_name, ...) - fields with the 16 byte ID to be generated for each row a new one
*/
function CopyTableRows ($table, $where, $set_columns, $omit_columns = "", $id_columns = "") {
    if (!$omit_columns) $omit_columns = array();
    if (!$id_columns) $id_columns = array();

    if ($GLOBALS[debug]) {
        echo "CopyTableRows: SELECT * FROM $table WHERE $where<br>
        set_columns = ";
        print_r ($set_columns);
        echo "<br>omit_columns = ";
        print_r ($omit_columns);
        echo "<br>";
    }

    $db = getDB();
    $varset = new CVarset();

    $columns = $db->metadata($table);
    freeDB($db);

    if ($GLOBALS[debug]) $rows = 0;

    $data = GetTable2Array("SELECT * FROM $table WHERE $where", "NoCoLuMn");

    if ($GLOBALS[debug]) { echo "data: "; print_r ($data); echo "<br>"; }

    if (!is_array($data)) {
        return true;
    }

    reset ($data);
    while (list (,$datarow) = each ($data)) {
        $varset->Clear();
        reset ($columns);

        // create the varset
        while (list (,$col) = each ($columns)) {
            if (my_in_array ($col["name"], $omit_columns))
                continue;

            if (is_field_type_numerical ($col["type"]))
                 $type = "number";
            else $type = "text";

            // look into $set_columns
            if (isset ($set_columns[$col["name"]]))
                 $val = $set_columns[$col["name"]];
            else if (my_in_array ($col["name"], $id_columns))
                 $val = q_pack_id(new_id());
            else $val = $datarow[$col["name"]];

            $varset->set ($col["name"],$val,$type);
        }

        if ($GLOBALS[debug]) { echo "Row $rows<br>"; $rows ++; }

        if (!tryQuery("INSERT INTO $table ".$varset->makeINSERT())) {
            return false;
        }
    }
    return true;
}

// -----------------------------------------------------------------------------

function get_last_insert_id ($db, $table)
{
    $db->tquery ("SELECT LAST_INSERT_ID() AS lid FROM $table");
    $db->next_record();
    return $db->f("lid");
}

// -----------------------------------------------------------------------------

/** returns the suffix part of the filename (beginning with the last dot (.) in the filename) */
function filesuffix ($filename) {
    if (!strstr ($filename,".")) return "";
    $i = strlen($filename);
    while ($filename[$i] != ".") $i --;
    return substr ($filename,$i+1);
}

function filepath ($filename) {
    if (!strstr ($filename,"/")) return "./";
    $i = strlen($filename);
    while ($filename[$i] != "/") $i --;
    return substr ($filename,0,$i+1);
}

function filename ($filename) {
    if (!strstr ($filename,"/")) return "./";
    $i = strlen($filename);
    while ($filename[$i] != "/") $i --;
    return substr ($filename,$i+1);
}

function GetTimeZone() {
    $d = getdate();
    return (mktime ($d['hours'],$d['minutes'],$d['seconds'],$d['mon'],$d['mday'],$d['year'])
        - gmmktime ($d['hours'],$d['minutes'],$d['seconds'],$d['mon'],$d['mday'],$d['year'])) / 3600;
}

/** generates random string of given length (useful as MD5 salt) */
function gensalt($saltlen)
{
    srand((double) microtime() * 1000000);
    $salt_chars = "abcdefghijklmnoprstuvwxBCDFGHJKLMNPQRSTVWXZ0123456589";
    for ($i = 0; $i < $saltlen; $i++) {
        $salt .= $salt_chars[rand(0,strlen($salt_chars)-1)];
    }
    return $salt;
}

/** Moves uploaded file to given directory and (optionally) changes permissions
 *   @return string  error description or empty string
 */
function aa_move_uploaded_file($varname, $destdir, $perms = 0, $filename = null)
{
    endslash($destdir);
    if (!$GLOBALS[$varname]) return "No $varname?";
    if ($filename == "") {
        // get filename and replace bad characters
        $filename = eregi_replace("[^a-z0-9_.~]","_",$GLOBALS[$varname."_name"]);
    }

    if (!is_dir($destdir)) {
        return _m("Internal error. File upload: Dir does not exist?!");
    }

    if (file_exists("$destdir$filename")) {
        return _m("File with this name already exists."). " $destdir$filename";
    }

    // copy the file from the temp directory to the upload directory, and test for success

    if (is_uploaded_file($GLOBALS[$varname])) {
        if (!move_uploaded_file($GLOBALS[$varname], "$destdir$filename")) {
            return sprintf(_m("Can't move image  %s to %s"), $GLOBALS[$varname],"$destdir$filename");
        } elseif ($perms) {
            chmod ($destdir.$filename, $perms);
        }
    }
    return "";
}

// ---------------------------------------------------------------------------------------------

// like PHP split, but additionally provides $escape_pattern to stand for occurences of $pattern,
// e.g. split_escaped (":", "a#:b:c", "#:") returns array ("a:b","c")

function split_escaped($pattern, $string, $escape_pattern) {
    $dummy = "~#$?_";
    while (strpos($string, $dummy) !== false) {
        $dummy .= '^';   // add another strange character to the
    }
    $string  = str_replace($escape_pattern, $dummy, $string);
    $strings = explode($pattern, $string);
    foreach ($strings as $key => $val) {
        $strings[$key] = (string)str_replace($dummy, $pattern, $val);
    }
    return $strings;
}

function join_escaped($pattern, $strings, $escape_pattern) {
    foreach ((array)$strings as $val) {
        if ($retval) {
            $retval .= $pattern;
        }
        $retval .= str_replace($pattern, $escape_pattern, $val);
    }
    return $retval;
}

function join_and_quote( $pattern, $strings ) {
    foreach ((array)$strings as $string) {
        if ($retval) {
            $retval .= $pattern;
        }
        $retval .= addslashes($string);
    }
    return $retval;
}

/** stripslashes if magic quotes are set */
function magic_strip($val) {
    return get_magic_quotes_gpc() ? StripslashesArray($val) : $val;
}

function magic_add($str) {
    return (get_magic_quotes_gpc() ? $str : addslashes($str));
}

function isdigit($c) {
    return $c >= "0" && $c <= "9";
}

function isalpha($c) {
    return ($c >= "a" && $c <= "z") || ($c >= "A" && $c <= "Z");
}

function isalnum($c) {
    return ($c >= "0" && $c <= "9") || ($c >= "a" && $c <= "z") || ($c >= "A" && $c <= "Z");
}

function gfd_error($x) {
    echo "Unrecognized date format charcacter $x";
    exit;
}

/*  Returns the Unix timestamp counted from the formatted date string.
    Does not check the date format, rather returns nonsence values for
    wrong date strings.
    Uses non-format letters as separators only,
    i.e. "2.3.2002" is parsed the same as "2/3/2002" or even "2;3#2002". */
function get_formatted_date ($datestring, $format) {
    // don't work with empty string
    if (!$datestring) return "";

    // Split the date into parts consisting only of digits or only of letters
    for ($i = 0; $i < strlen ($datestring); $i++) {
        if (isalpha($datestring[$i]) && ($s == "" || isalpha($datestring[$i-1]))) {
            $s .= $datestring[$i];
        } elseif (isdigit($datestring[$i]) && ($s == "" || isdigit($datestring[$i-1]))) {
            $s .= $datestring[$i];
        } elseif ($s) {
            $dateparts[] = $s;
            $s = "";
        }
    }
    if ($s) {
        $dateparts[] = $s;
    }

    // Split the format into parts consisting of one letter
    for ($i = 0; $i < strlen ($format); $i++) {
        if (isalpha($format[$i])) {
            $formatparts[] = $format[$i];
        }
    }

    $month_names = array ("January"=>1,"February"=>2,"March"=>3,"April"=>4,"May"=>5,"June"=>6,
                          "July"=>7,"August"=>8,"September"=>9,"October"=>10,"November"=>11,"December"=>12);
    $month3_names = array ("Jan"=>1,"Feb"=>2,"Mar"=>3,"Apr"=>4,"May"=>5,"Jun"=>6,"Jul"=>7,"Aug"=>8,"Sep"=>9,"Oct"=>10,"Nov"=>11,"Dec"=>12);

    // assing date parts to format parts
    for ($i = 0; $i < count ($dateparts); $i ++) {
        $d = $dateparts[$i];
        switch ($formatparts[$i]) {
            case 'a': $pm = $d == "pm"; break;
            case 'A': $pm = $d == "PM"; break;
            case 'B': gfd_error ('B'); break;
            case 'd': $day = $d; break;
            case 'D': break;
            case 'F': $month = $month_names[$d]; break;
            case 'g':
            case 'h': $hour = $d; $use_pm = true; break;
            case 'H':
            case 'G': $hour = $d; $use_pm = false; break;
            case 'i': $minute = $d; break;
            case 'I': break;
            case 'j': $day = $d; break;
            case 'l': break;
            case 'L': break;
            case 'n':
            case 'm': $month = $d; break;
            case 'M': $month = $month3_names[$d]; break;
            case 'O': break;
            case 'r': gfd_error ('r'); break;
            case 's': $second = $d; break;
            case 'S': break;
            case 't': break;
            case 'T': break;
            case 'U': return $d; break;
            case 'w': break;
            case 'W': gfd_error ('W'); break;
            case 'Y': $year = $d; break;
            case 'y': $year = $d; break; // mktime works with 2-digit year
            case 'z': $day = $d; break;
            case 'Z': break;
        }
    }

    //echo "hour $hour minute $minute second $second month $month day $day year $year pm $pm";

    if ($use_pm && $pm) $hour += 12;

    // mktime replaces missing values by today's values
    if (!isset ($year)) {
        if (!isset ($day)) {
            if (!isset ($month)) {
                return mktime ( $hour, $minute, $second);
            }
            else return mktime ( $hour, $minute, $second, $month);
        }
        else return mktime ( $hour, $minute, $second, $month, $day);
    }
    else return mktime ( $hour, $minute, $second, $month, $day, $year);
}

function setdefault (&$var, $default) {
    if (!isset ($var)) $var = $default;
}

/** Cooperates with the script post2shtml.php3 (see more doc there),
 * which allows to easily post variables
 * to PHP scripts SSI-included in a .shtml page.
 *
 * @param bool $delete Should delete the vars from database after recalling them?
 *                     If you use the vars in several scripts included in one
 *                     shtml page, delete them in the last script.
 *
 * @author Jakub Adamek, Econnect, December 2002
 */
function add_post2shtml_vars ($delete = true) {
    global $post2shtml_id;
    global $debugfill;
    add_vars();
    if (!$post2shtml_id) return;
    $db = getDB();
    $db->query("SELECT * FROM post2shtml WHERE id='$post2shtml_id'");
    $db->next_record();
    $vars = unserialize ($db->f("vars"));
    if ($delete)
        $db->query("DELETE FROM post2shtml WHERE id='$post2shtml_id'");
    freeDB($db);
    $var_types = array ("post","get","files","cookie");

    reset ($var_types);
    while (list (,$var_type) = each ($var_types)) {
        if (is_array($vars[$var_type])) {
            reset ($vars[$var_type]);
            while (list ($var, $value) = each ($vars[$var_type])) {
                global $$var;
                if ($debugfill) huhl("add_post2shtml_vars:$$var=",$value);
                $$var = $value;
            }
        }
    }
}

/** List of email types with translated description.
    You should never list email types directly, always call this function. */
function get_email_types() {
    return array (
        "alerts alert" => _m("alerts alert"),
        "alerts welcome" => _m("alerts welcome"),
        "slice wizard welcome" => _m("slice wizard welcome"),
        "other" => _m("other"),
    );
}

/// @return array month names
function monthNames()
{
    return array( 1 => _m('January'), _m('February'), _m('March'), _m('April'), _m('May'), _m('June'),
        _m('July'), _m('August'), _m('September'), _m('October'), _m('November'), _m('December'));
}

/** Creates values for a select box showing some param wizard section. */
function getSelectBoxFromParamWizard($var)
{
    reset ($var["items"]);
    while (list ($value, $prop) = each ($var["items"]))
        $retval[$value] = $prop["name"];
    return $retval;
}

// This pair of functions remove the guessing about which of $db $db2
// to use
// Usage: $db = getDB(); ..do stuff with sql ... freeDB($db)
//
$spareDBs = array();

function getDB() {
    global $spareDBs;
    if (!($db = array_pop($spareDBs)))
        $db = new DB_AA;
    return $db;
}
function freeDB($db) {
    global $spareDBs;
    array_push($spareDBs,$db);
}

// Try a query, displaying debugging if $debug, return true on success, false on failure
function tryQuery($SQL) {
    $db = getDB();
    $res = $db->tquery($SQL);
    freeDB($db);
    return $res;
}

// Return an array of fields, skipping numeric ones
// See also GetTable2Array
function DBFields(&$db) {
    $a = array();
    foreach ( $db->Record as $key => $val ) {
        if ( !is_numeric($key) ) {
            $a[$key] = $val;
        }
    }
    return $a;
}

function ShowWizardFrames($aa_url, $wizard_url, $title, $noframes_html="") {
    require $GLOBALS["AA_BASE_PATH"]."post2shtml.php3";
    global $post2shtml_id;
    echo
'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
    <title>'.$title.'</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2">
</head>

<frameset cols="*,300" frameborder="YES" border="1" framespacing="0">
    <frame src="'.$aa_url.'&called_from_wizard=1" name="aaFrame">
    <frame src="'.con_url($wizard_url,"post2shtml_id=$post2shtml_id").'" name="wizardFrame">
</frameset>
<noframes><body>
'.$noframes_html.'
</body></noframes>
</html>';
}

/** Shows JavaScript which updates the Wizard frame, if it exists. */
function ShowRefreshWizardJavaScript() {
    FrmJavascript( 'if (top.wizardFrame != null) top.wizardFrame.wizard_form.submit();' );
}

function GetAAImage($filename, $alt='', $width=0, $height=0, $add='', $add_path='') {
    $image_path = $GLOBALS['AA_BASE_PATH'].   $add_path. "images/$filename";
    $image_url  = $GLOBALS['AA_INSTAL_PATH']. $add_path. "images/$filename";
    $title      = ($alt ? "title=\"$alt\"" : '');
    if ( $width ) {
        $size = "width=\"$width\" height=\"$height\"";
    } else {
        $im_size = @GetImageSize($image_path);
        $size = $im_size[3];
    }
    return "<img border=\"0\" src=\"$image_url\" alt=\"$alt\" $title $size $add>";
}

function GetModuleImage($module, $filename, $alt='', $width=0, $height=0, $add='') {
    return GetAAImage($filename, $alt, $width, $height, $add, "modules/$module/");
}

/// On many places in Admin panel, it is secure to read sensitive data => use this function
function FetchSliceReadingPassword() {
    global $slice_id, $slice_pwd, $db;
    $db->query ("SELECT reading_password FROM slice WHERE id='".q_pack_id($slice_id)."'");
    if ($db->next_record()) {
        $slice_pwd = $db->f("reading_password");
    }
}

$tracearr = array();
// Support function for debugging, because of the lack of a stacktrace in PHP
// $d = + for entering a function - for leaving = for a checkpoint.
function trace($d,$v="NONE",$c="") {
    global $tracearr,$traceall;
    if ($traceall) huhl("TRACE: $d:",$v," ",$c);
// Below here you can put variables you want traced
    if ($traceall) huhl("TRACE:slice_id=",$slice_id);
// end variables
    switch ($d) {
    case "+": array_push($tracearr,$v,$c); break;
    case "-": array_pop($tracearr); array_pop($tracearr); break;
    case "=": array_pop($tracearr); array_push($tracearr,$c); break ;
    case "p": huhl("TRACE: ",$tracearr); break;
    default: echo "Illegal argument to trace:$d"; break;
    }
}

/** contentcache class - prevents from executing the same - time consuming code
 *  twice in one run of the script.
 *  Ussage:
 *    Instead of calling:
 *        $result = function_name(param1, param2);
 *    we will use
 *        $result = $contentcache->get_result("function_name", array(param1, param2));
 *    For the first time call the function_name is called, for second, third,...
 *    time calling, the result is returned from cache (for the same parameters)
 *
 *    The best to use this class for time-consuming functions with small results
 */
class contentcache {
    // used for global cache of contents
    var $content;

    /** "class function" obviously called as contentcache::global_instance();
     *  This function makes sure, there is global instance of the class
     */
    function global_instance() {
        if ( !isset($GLOBALS['contentcache']) ) {
            $GLOBALS['contentcache'] = new contentcache;
        }
    }

    /** Calls $function with $params and returns its return value. The result
     *  value is then stored into cache, so next call of the $function with the
     *  same parameters is returned from cache - function is not performed.
     *  Use this feature mainly for repeating, time consuming functions!
     *  You could use also object methods - then the $function parameter should
     *  be array (see http://php.net/manual/en/function.call-user-func.php)
     *  For static class methods:
     *     $result = $contentcache->get_result(array('Classname', 'function_name'), array(param1, param2));
     *  For instance methods:
     *     $result = $contentcache->get_result(array($this, 'function_name'), array(param1, param2));
     */
    function get_result( $function, $params=array() ) {
        $key = md5(serialize($function).serialize($params));
        if ( isset( $this->content[$key]) ) {
            return $this->content[$key];
        }
        $val = call_user_func_array($function, $params);
        $this->content[$key] = $val;
        return $val;
    }


    // set new value for key $key
    function set($access_code, &$val) {
        $this->content[md5($access_code)] = $val;
    }

    /** Get value for $access_code.
     *  Returns false if the value is not cached for the $access_code (use ===)
     */
    function get($access_code) {
        $key = md5($access_code);
        if ( isset($this->content[$key]) )  return $this->content[$key];
        return false;
    }

    // clear key or all content from contentcache
    function clear($key="") {
        if ($key) {
            unset($this->content[$key]);
        } else {
            unset($this->content);
        }
    }

// end of contentcache class
}



/** toexecute class - used for many short tasks, such as sending an e-mail for
 *  alerts. Instead of sending thounsands of e-mails in one php script run (bad
 *  eperienses with 1000+ emails), we store just store the task in the database.
 *  Then we call misc/toexecute.php3 script from AA cron (say each 2 minutes)
 *  and if there is any task in the quweue, it is executed. This way we spread
 *  sending of weekls alerts to thounsands users to several hours.
 *  Ussage:
 *    Instead of calling:
 *        $object->function_name(param1, param2);
 *    we will use
 *        $toexecute = new toexecute;
 *        $toexecute->later($object, array(param1, param2));
 *
 *        Then we create method $object->toexecutelater(param1, param2)
 *        in which we will call $object->function_name(param1, param2);
 *
 *  The name of 'toexecutelater' method is fixed - This is because of security.
 *  We do not want to allow users to execute any method of any object just
 *  by inserting some data in the database.
 */
class toexecute {

    /** "class function" obviously called as toexecute::global_instance();
     *  This function makes sure, there is global instance of the class
     */
    function global_instance() {
        if ( !isset($GLOBALS['toexecute']) ) {
            $GLOBALS['toexecute'] = new toexecute;
        }
    }

    /** Stores the object and params to the database for later execution.
     *  Such task is called from cron (the order depends on priority)
     *  selector is used for identifying class of task - used for deletion
     *  of duplicated task
     *  Example: we need to recount all links in allcategories (Links module),
     *           so we need to cancel all older "recount" tasks, since it will
     *           be dubled in the queue (we call cancel_all() method for it)
     */
    function later( &$object, $params=array(), $selector='', $priority=100, $time=null ) {
        global $auth;
        $varset = new Cvarset(
            array( 'created'       => time(),
                   'execute_after' => ($time ? $time : time()),
                   'aa_user'       => $auth->auth['uid'],
                   'priority'      => $priority,
                   'selector'      => $selector,
                   'object'        => serialize($object),
                   'params'        => serialize($params)
                  ));
         // store the task in the queue (toexecute table)
         if ( !$varset->doInsert('toexecute') ) {
             // if you can't store it in the queue (table not created?)
             // - execute it directly
             return $this->execute_one($object,$params);
         }
         return true;
    }

    function cancel_all($selector) {
        $varset = new Cvarset;
        $varset->doDeleteWhere('toexecute',"selector='".quote($selector)."'");
    }

    function execute($allowed_time = 0) {  // standard run is 10 s

        if ( !$allowed_time ) {
            $allowed_time = (float) (defined('TOEXECUTE_ALLOWED_TIME' ) ? TOEXECUTE_ALLOWED_TIME : 16.0);
        }
        /** there we store the the time needed for last task of given type
         *  (selector) - this value we use in next round to determine, if we can
         *  run one more such task or if we left it for next time */
        $execute_times = array();

        // get just ids - the task itself we will grab later, since the objects
        // in the database could be pretty big, so we want to grab it one by one
        $tasks = GetTable2Array("SELECT id FROM toexecute WHERE execute_after < ".now()." ORDER by priority DESC", 'id', 'aa_mark');

        $execute_start = get_microtime();
        if (is_array($tasks)) {
            foreach ($tasks as $task_id => $foo) {
                $task = GetTable2Array("SELECT * FROM toexecute WHERE id='$task_id'", 'aa_first', 'aa_fields');

                $task_type     = get_if($tasks['selector'],'aa_unspecified');
                $expected_time = get_if($execute_times[$task_type], 1.0);  // default time expected for one task if 1 second
                $task_start    = get_microtime();

                // can we run next task? Does it (most probably) fit in allowed_time?
                if ( (($task_start + $expected_time) - $execute_start) > $allowed_time) {
                    break;
                }
                $varset = new Cvarset( array( 'priority' => max( $task['priority']-1, 0 )));
                $varset->addkey('id', 'number', $task['id']);
                // We lower the priority for this task before the execution, so
                // if the task is not able to finish, then other tasks with the same
                // priority is called before this one (next time)
                $varset->doUpdate('toexecute');

                $object = unserialize($task['object']);
                if ( $GLOBALS['debug'] ) {huhl($object);}
                $retcode = $this->execute_one($object, unserialize($task['params']));

                // Task is done - remove it from queue
                $varset->doDelete('toexecute');
                writeLog('TOEXECUTE', "$expected_time:$retcode:".$task['params'], get_class($object));
                $execute_times[$task_type] = get_microtime() - $task_start;
            }
        }
    }

    function execute_one(&$object, $params) {
        if ( !is_object($object) ) {
            return 'No object'; // Error
        }
        set_time_limit( 30 );   // 30 seconds for each task
        return call_user_func_array(array($object, 'toexecutelater'), $params);
    }


// end of toexecute class
}

/** If $value is set, returns $value - else $else */
function get_if($value, $else, $else2='aa_NoNe') {
    return $value ? $value :
           ($else ? $else :
           (($else2=='aa_NoNe') ? $else : $else2));
}

/** Version of AA - automaticaly included also date and revision of util.php3
 *  file, for better version informations
 */
function aa_version() {
    return 'ActionApps 2.8.1 ($Date$, $Revision$)';
}

// file_get_contents works in PHP >=4.3.0
if (!function_exists("file_get_contents")) {
    function file_get_contents($filename, $use_include_path = 0) {
        $data = ""; // just to be safe. Dunno, if this is really needed
        $file = @fopen($filename, "rb", $use_include_path);
        if ($file) {
            while (!feof($file)) {
                $data .= fread($file, 1024);
            }
            fclose($file);
        }
        return $data;
    }
}


?>