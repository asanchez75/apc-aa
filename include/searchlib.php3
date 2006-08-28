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

require_once AA_INC_PATH."sql_parser.php3";
require_once AA_INC_PATH."zids.php3";
require_once AA_INC_PATH."pagecache.php3";

class Conditions {
    /** clasic conds array - array('operator'  => ..,
     *                             'value'     => ..,
     *                             <field_1>   => 1
     *                             [,<field_n> => 1])
     */
    var $conds;

    function Conditions() {
        $this->clear();
    }

    function clear() {
        $this->conds = array();
    }

    /** Creates conditions from d-<fields>-<operator>-<value>-<fields>-<op....
     *  string ie:   d-headline........,category.......1-RLIKE-Bio
     */
    function addFromString($string) {
        $commands = new ViewCommands($string);
        $command  = $commands->get('d');
        if (!$command) {
            return false;
        }
        return $this->addFromCommand($command);
    }

    function addFromCommand($command) {
        if ($command->getCommand() != 'd') {
            return false;
        }
        $i=0;
        $command_params = $command->getParameterArray();
        while ( $command_params[$i] ) {
             if ( Conditions::check($command_params[$i], $command_params[$i+2]) ) {
                 $field_arr = array();
                 foreach (explode(',',$command_params[$i]) as $cond_field) {
                     $field_arr[$cond_field] = 1;
                 }
                 $this->conds[]= array_merge($field_arr, array('operator' => $command_params[$i+1],
                                                               'value'    => stripslashes($command_params[$i+2])));
             }
             $i += 3;
         }
         return true;
    }

    /** retruns $conds[] array - mainny for backward compatibility */
    function getConds() {
        return $this->conds;
    }

    /** static - Checks if the condition is in right format - is valid */
    function check($field, $value) {
        return ($field && ($value != 'AAnoCONDITION'));
    }
}

class Sortorder {
    var $order;

    function Sortorder() {
        $this->clear();
    }

    function clear() {
        $this->order = array();
    }

    /** Transforms 'publish_date....-' like sort definition (used in prifiles, ...)
     *  to $arr['publish_date....'] = 'd' as used in sort[] array
     *  It is also possible to specify "group limit" by the number at the begin
     *  of the string (like 4category........-), which means that we want maximum
     *  4 items of each category. In such case we returned something like:
     *  array( 'limit' => 4, 'category........' => d )
     */
    function addFromString( $sort ) {
        $ret = array();
        if ($sort) {
            // is defined group limit?
            if (($limit_len = strspn($sort,'0123456789')) > 0) {
                $ret['limit'] = (int)substr($sort,0,$limit_len);
                $sort  = substr($sort,$limit_len);        // rest of the string
            }
            switch ( substr($sort,-1) ) {    // last character
                case '-':  $ret[substr($sort,0,-1)] = 'd'; break;
                case '+':  $ret[substr($sort,0,-1)] = 'a'; break;
                default:   $ret[$sort]              = 'a';
            }
        }
        if ( count($ret) > 0 ) {
            $this->order[] = $ret;
        }
    }

    function getOrder() {
        return $this->order;
    }
}


/** Returns sort[] array used by QueryZids functions
 *  $sort - sort definition in varios formats:
 *     1)   sort = headline........-
 *     2)   sort[0] = headline........-
 *     3)   sort[0][headline........]=d
 */
function getSortFromUrl( $sort ) {
    $ret_sort = array();
    $order    = new Sortorder;
    if ( isset($sort) ) {
        if ( !is_array($sort) ) {
            $order->addFromString($sort);
            $ret_sort = $order->getOrder();
        } else {
            ksort( $sort, SORT_NUMERIC); // it is not sorted and the order is important
            foreach ( $sort as $k => $srt) {
                if ($srt) {
                    if ( is_array($srt) ) {
                        $ret_sort[] = array( key($srt) => (strtolower(current($srt)) == "d" ? 'd' : 'a'));
                    } else {
                        $order->addFromString($srt);
                        $ret_sort = array_merge($ret_sort, $order->getOrder());
                    }
                }
            }
        }
    }
    return $ret_sort;
}


function GetWhereExp( $field, $operator, $querystring ) {

  // query string could be slashed - sometimes :-(
  $querystring = stripslashes( $querystring );

  if ( $GLOBALS['debug'] )
    echo "<br>GetWhereExp( $field, $operator, $querystring )";

  // search operator for functions (some operators can be in function:operator
  // fomat - the function is called to $querystring (good for date transform ...)
  if ( $pos = strpos($operator,":") ) {  // not ==
    $func = substr($operator,0,$pos);
    $operator = substr($operator,$pos+1);

    switch( $func ) {
      case 'd': // english style datum (like '12/31/2001' or '10 September 2000')
                $querystring = strtotime($querystring);
                break;
      case 'e': // european datum style (like 24. 12. 2001)
                if ( !ereg("^ *([0-9]{1,2}) *\. *([0-9]{1,2}) *\. *([0-9]{4}) *$", $querystring, $part))
                  if ( !ereg("^ *([[0-9]]{1,2}) *\. *([0-9]{1,2}) *\. *([0-9]{2}) *$", $querystring, $part))
                    if ( !ereg("^ *([[0-9]]{1,2}) *\. *([0-9]{1,2}) *$", $querystring, $part)) {
                      $querystring = time();
                      break;
                    }
                if ( ($operator == "<=") or ($operator == ">") )
                  // end of day used for some operators
                  $querystring = mktime(23,59,59,$part[2],$part[1],$part[3]);
                 else
                  $querystring = mktime(0,0,0,$part[2],$part[1],$part[3]);
                break;
      case 'm':
      case '-': $querystring = time() - $querystring;
                break;
    }
  }

  $querystring =  (string) $querystring;   // to be able to do string operations

  switch( $operator ) {
    case 'LIKE':
    case 'RLIKE':
    case 'LLIKE':
    case 'XLIKE':
    case '=':
      $querystring = str_replace('*', '%', trim($querystring));
      $querystring = str_replace('?', '_', $querystring);
        $syntax = new Syntax($field, $operator, lex( trim($querystring) ) );
      $ret = $syntax->S();
      if ( $ret == "_SYNTAX_ERROR" ) {
        if ( $GLOBALS['debug'] )
          echo "<br>Query syntax error: ". $GLOBALS['syntax_error'];
        return "1=1";
      }
      return ( $ret ? " ($ret) " : "1=1" );
    case 'BETWEEN':
      $arr = explode( ",", $querystring );
      return ( " (($field >= $arr[0]) AND ($field <= $arr[1])) ");
    case 'ISNULL':
      return ( " (($field IS NULL) OR ($field='')) ");
    case 'NOTNULL':
      return ( " (($field IS NOT NULL) AND ($field<>'')) ");
    default:
      $str = ( ($querystring[0] == '"') OR ($querystring[0] == "'") ) ?
                                 substr( $querystring, 1, -1 ) : $querystring ;
      return " ($field $operator '$str') ";
  }
}

// -------------------------------------------------------------------------------------------

// show info about non-existing fields in all given slices
function ProoveFieldNames($slices, $conds) {

    if (!(isset($slices) AND is_array($slices) AND isset($conds) AND is_array($conds))) {
        return;
    }

    global $CONDS_NOT_FIELD_NAMES;
    if (!is_array($slices) || !is_array($conds)) {
        return;
    }
    $db = new DB_AA;
    foreach ($slices as $slice_id) {
        $db->query("SELECT * FROM field WHERE slice_id='".q_pack_id($slice_id)."'");
        while ($db->next_record()) {
            $slicefields[$db->f("id")] = 1;
        }
        foreach ($conds as $cond) {
            if (!(isset($cond) AND is_array($cond))) {
                continue;
            }
            foreach ($cond as $key => $foo) {
                if (!$CONDS_NOT_FIELD_NAMES[$key] && !isset($slicefields[$key])) {
                    echo "Field <b>$key</b> does not exist in slice <b>$slice_id</b> (".q_pack_id($slice_id).").<br>";
                }
            }
        }
    }
}

// -------------------------------------------------------------------------------------------

/* parses the conds from a multiple select box: e.g.
    conds[1][value][0] = 'apple'
    conds[1][value][1] = 'cherry'
    conds[1][valuejoin] = 'AND'

    => creates two conds: conds[7] and conds[8] for example,
        fill conds[7][value] = 'apple', conds[8][value] = 'cherry'

    with conds[1][valuejoin] = 'OR' only changes conds[1][value] to '"apple" OR "cherry"'
    (c) Jakub, May 2002
*/
function ParseMultiSelectConds(&$conds)
{
    if (!is_array($conds)) return;
    foreach ($conds as $icond => $cond) {
        if (is_array($cond['value'])) {
            if (!$cond['valuejoin']) {
                echo "ERROR in conds: when using ['value'][], you must use [valuejoin]='OR'|'AND' also!";
                return;
            }
            if ($cond['valuejoin'] == 'OR') {
                $values = "";
                foreach ($cond['value'] as $val) {
                    if ($values != "") $values .= " OR ";
                    $values .= '"'.addslashes($val).'"';
                }
                unset($conds[$icond]['valuejoin']);
                $conds[$icond]['value'] = $values;

            } elseif ($cond['valuejoin'] == 'AND') {
                foreach ($cond['value'] as $val) {
                    $newcond = $cond;
                    $newcond['value'] = '"'.addslashes ($val).'"';
                    unset ($newcond['valuejoin']);
                    $conds[] = $newcond;
                }
                unset ($conds[$icond]);

            } else echo "ERROR in conds: [valuejoin] must be set to 'OR' or 'AND'.";
        }
    }
}

/**
 * Transforms simplified version of conditions to the extended syntax
 * for example conds[0][headline........]='Hi' transforms into
 * conds[0][headline........]=1,conds[0]['value']='Hi',conds[0][operator]=LIKE
 *
 * It also replaces all united field conds
 *    like conds[0][headline........,abstract........]='Hi'
 * with its equivalents:
 *     conds[0][headline........]=1,conds[0][abstract........]=1,
 *     conds[0]['value']='Hi',conds[0][operator]=LIKE
 * (number of united field conds is unlimited and you can use it in simplified
 *  condition syntax as well as in extended condition syntax)
 *
 * @param array $conds input/output - transformed conditions
 * @param array $defaultCondsOperator - could be scalar (default), but also
 *              array: field_id => array('operator'=>'LIKE')
 */
function ParseEasyConds(&$conds, $defaultCondsOperator = "LIKE") {
    if (is_array($conds)) {
        // In first step we remove conds with wrong syntax (like conds[xx]=yy)
        // and replace easy conds with extended syntax conds
        foreach ($conds as $k => $cond) {
            if ( !is_array($cond) ) {
                unset($conds[$k]);
                continue;             // bad condition - ignore
            }
            if ( !isset($cond['value']) && (count($cond) == 1) ) {
                $conds[$k]['value'] = reset($cond);
            }
            if ( !isset($cond['operator']) ) {
                if ( is_array($defaultCondsOperator) ) {
                    if ( is_array($defaultCondsOperator[key($cond)] )) {
                        $conds[$k]['operator'] = get_if($defaultCondsOperator[key($cond)]['operator'], 'LIKE');
                    } else {
                        $conds[$k]['operator'] = 'LIKE';
                    }
                } else {
                    $conds[$k]['operator'] = $defaultCondsOperator;
                }
            }
            if (!($cond['operator'] == 'ISNULL') AND !($cond['operator'] == 'NOTNULL')) {
                // The value could be empty for ISNULL or NOTNULL operators
                if (!isset($conds[$k]['value']) OR ($conds[$k]['value']=="")) {
                    // For other operators we should remove all conditions without value
                    unset ($conds[$k]);
                }
            }
        }
        // and now replace all united conds (like conds[0][headline........,abstract........]=1)
        // with its equivalents
        foreach ($conds as $k => $cond) {
            foreach ( $cond as $field => $val ) {
                if ( strpos( $field, ',') !== false ) {
                    unset($conds[$k][$field]);
                    foreach ( explode(',',$field) as $separate_field ) {
                        $conds[$k][$separate_field] = $val;
                    }
                }
            }
        }
    }
}

/** Returns $conds[] array, which is created from conds[] 'url' string
 *  like conds[0][category........]=first&conds[1][switch.........1]=1
 */
function String2Conds( $conds_string ) {
    $conds = false;
    if (isset($conds_string)) {
        parse_str($conds_string, $aa_query_arr);
        // we also need PHP to think a['key'] is the same as a[key], that's why we
        // call NormalizeArrayIndex()
        $aa_query_arr = NormalizeArrayIndex(magic_strip($aa_query_arr));
        $conds = $aa_query_arr['conds'];
        ParseMultiSelectConds($conds);
        ParseEasyConds($conds,'RLIKE');
    }
    return $conds;
}

/** Returns $sort[] array, which is created from sort[] 'url' string
 *  like sort[0][headline........]=a&sort[2][publish_date....]=d
 */
function String2Sort( $sort_string ) {
    $sort = false;
    if (isset($sort_string)) {
        parse_str($sort_string, $aa_query_arr);
        // we also need PHP to think a['key'] is the same as a[key], that's why we
        // call NormalizeArrayIndex()
        $aa_query_arr = NormalizeArrayIndex(magic_strip($aa_query_arr));
        $sort = $aa_query_arr['sort'];
    }
    return $sort;
}

// function finds group_id in field.input_show_func parameter
function GetConstantGroup( $input_show_func ) {
  $INPUT_SHOW_FUNC_TYPES = inputShowFuncTypes();
  list($fnc,$constgroup) = explode(':', $input_show_func);

  // does this field use constants?
  if ( strstr($INPUT_SHOW_FUNC_TYPES[$fnc]['paramformat'], 'const') AND
      (substr($constgroup,0,7) != "#sLiCe-") )  // prefix indicates select from items, not constants
    return $constgroup;          // yes
  return false;
}

/** Creates array of SQL conditions based on $conds and fields $add
 *  @param function $join_tables           - if some table is needed to join,
 *                                           this function adds it to the array
 *  @param function $additional_field_cond - aditional condition function
 *  @param function $additional_field_cond - aditional condition function parameter
 */
function MakeSQLConditions($fields_arr, $conds, $defaultCondsOperator, &$join_tables, $additional_field_cond=false, $add_param=false) {

    ParseMultiSelectConds($conds);
    ParseEasyConds($conds, $defaultCondsOperator);

    if ( $GLOBALS['debug'] ) huhl( "<br>Conds after ParseEasyConds():", $conds, "<br>--");

    if ( isset($conds) AND is_array($conds)) {
        foreach ($conds as $cond) {
            if ( isset($cond) AND is_array($cond) ) {
                unset($onecond);                    // clear
                foreach ( $cond as $fid => $v ) {
                    $finfo = $fields_arr[$fid];
                    if ( isset($finfo) AND is_array($finfo) ) {
                        if ( $additional_field_cond ) {
                            if ( !$additional_field_cond( $finfo, $v, $add_param ) )
                                continue;
                        }
                        $onecond[] = GetWhereExp( $finfo['field'],
                                            $cond['operator'], $cond['value'] );
                        if ( $finfo['table'] )
                            $join_tables[$finfo['table']] = true;
                    }
                }  // between conditions inside one cond is OR
                if     ( count($onecond) == 1 ) $ret[] = $onecond[0];
                elseif ( count($onecond) >  1 ) $ret[] = '( '. join( ' OR ',$onecond ) . ')';
            }
        }
    }
    return ( isset($ret) AND is_array($ret) ) ?
                       ' AND ( '. join(' AND ', $ret ) .') ' : ' AND (1=1) ';
}

/** Creates array of SQL ORDER BY expresions based on $sort and fields array
 *  @param function $join_tables           - if some table is needed to join,
 *                                           this function adds it to the array
 *  @param function $additional_field_cond - aditional condition function
 *  @param function $additional_field_cond - aditional condition function parameter
 */
function MakeSQLOrderBy($fields_arr, $sort, &$join_tables, $additional_field_cond=false, $add_param=false) {
    if ( isset($sort) AND is_array($sort)) {
        foreach ( $sort as $srt ) {
            if ( isset($srt) AND is_array($srt) ) {
                $finfo = $fields_arr[key($srt)];
                if ( $finfo AND is_array($finfo))  {
                    if ( $additional_field_cond ) {
                        if ( !$additional_field_cond( $finfo, current($srt), $add_param ) )
                                continue;
                    }
                    $ret[] = $finfo['field'] .
                                (stristr(current( $srt ), 'd') ? " DESC" : "");
                    if ( $finfo['table'] )
                        $join_tables[$finfo['table']] = true;
               }
            }
        }
    }
    return ( isset($ret) AND is_array($ret) ) ?
                           ' ORDER BY '. join(' , ', $ret ) : '';
}

/** Get searchresult from cache, if cahed
 * @param bool   $cache_condition - have we look into cache?
 * @param string $keystr          - id_string which identifies cache content
 * @return resulting zids from cache or false;
 */
function CachedSearch($cache_condition, $keystr) {
    global $pagecache, $QueryIDsCount, $debug;

    if ( $cache_condition ) {
        if ( $res = $pagecache->get($keystr)) {
            $zids = unserialize($res);
            $QueryIDsCount = $zids->count();  // global variable
            if ( $debug ) {
                echo "<br>Cache HIT - return $QueryIDsCount IDs<br>";
            }
            return $zids;
        }
    }
    return false;
}

/** Get zids from database and possibly store it into cache
 * @param string $SQL              - SQL query
 * @param string $col              - column in database containing id
 * @param bool   $cache_condition  - have we store result into cache?
 * @param string $keystr           - id_string which identifies cache content
 * @param string $cache_str2find   - CacheStr2find object for cache invalidation
 * @param bool   $empty_result_condition - have we return empty set?
 * @param zids   $sort_zids        - used for sorting zids to right order
 *                                 - if specified, return zids are sorted
 *                                   in the same order as in $sort_zids
 * @param arrray $group_limit      - array('field' => <grouping_column>,
 *                                         'limit' => <number>)
 *                                   Limits the number of returned ids from each
 *                                   group. Group is defined by 'field'. Used
 *                                   for displaying only firs <number> of items
 *                                   from each group. Also good, if you want to
 *                                   list just the group names which is used in
 *                                   selected items (then set the number to 1)
 * @return zids from SQL query;
 */
function GetZidsFromSQL( $SQL, $col, $cache_condition, $keystr, $cache_str2find,
                         $zid_type='s', $empty_result_condition=false,
                         $sort_zids=null, $group_limit=null ) {
    global $pagecache, $QueryIDsCount, $debug;
    $db = getDB();

    $arr = array();                  // result ids array
    if (!$empty_result_condition) {
        $db->tquery($SQL);
        if (!$group_limit) {
            while ($db->next_record()) {
                $arr[] = $db->f($col);
            }
        } else {                     // we have to remove the ids above the limit for group
            $groups  = array();                 // array where we count the number of items in each group
            $glimit  =  $group_limit['limit'];  // shortcut - just for possible speedup
            $gfield  =  $group_limit['field'];  // shortcut - just for possible speedup
            while ($db->next_record()) {
                if (++$groups[$db->f($gfield)] <= $glimit) {
                    $arr[] = $db->f($col);
                }
            }
        }
    }
    $zids = new zids($arr, $zid_type);
    $QueryIDsCount = count($arr);

    if ( is_object($sort_zids) ) {
        $zids->sort_and_restrict_as_in($sort_zids);
    }

    if ( $cache_condition ) {
        $pagecache->store($keystr, serialize($zids), $cache_str2find);
    }

    freeDB($db);
    return $zids;
}


// -------------------------------------------------------------------------------------------

/** Finds item IDs for items to be shown in a slice / view
*
*   @param string $slice_id older parameter, used only when $slices is not set,
*                           translated to $slices = array($slice_id)
*   @param array  $conds    search conditions (see FAQ)
*   @param array  $sort     sort fields (see FAQ)
*   @param array  $group_by not used (it was implemented, but never used, so
*                           removed in file version 1.69)
*   @param array  $slices array of slices in which to look for items
*   @param string $type
*       sets status, pub_date and expiry_date according to specified type:
*       ACTIVE | EXPIRED | PENDING | HOLDING | TRASH | ALL.
*       If you want to specify it in conds, set to ALL.
*
*   @param bool   $neverAllItems
*       if no conds[] apply (all are wrong formatted or empty),
*       should the function generate an empty set?
*       Otherwise all items from given slices are returned.
*
*   @param array  $restrict_zids
*       ids are packed but not quoted in $restrict_ids or short.
*       Use it if you want to choose only from a set of items
*       (used by E-mail Alerts and related item view
*       (for sorting and eliminating of expired items)).
*
*   @param string $defaultCondsOperator
*       replaces the default "LIKE" for conditions with no operator set
*
*   @param bool   $use_cache should be the cache searched for the result?
*
*   @return A zids object with a list of the ids that match the query.
*
*   @global  bool $debug (in) many debug messages
*   @global  bool $debugfields (in) useful mainly for multiple slices mode -- views info about field_ids
*               used in conds[] but not existing in some of the slices
*   @global  int $QueryIDsCount (out) is set to the count of IDs returned
*   @global  bool $nocache (in) do not use cache, even if use_cache is set
*
*   Parameter format example:
*   <pre>
*   conds[0][fulltext........] = 1;   // returns id of items where word 'Prague'
*   conds[0][abstract........] = 1;   // is in fulltext, absract or keywords
*   conds[0][keywords........] = 1;
*   conds[0][operator] = "=";
*   conds[0][value] = "Prague";
*   conds[1][source..........] = 1;   // and source field of that item is
*   conds[1][operator] = "=";         // 'Econnect'
*   conds[1][value] = "Econnect";
*   sort[0][category........]='a';    // order items by category ascending
*   sort[1][publish_date....]='d';    // and publish_date descending (secondary)
*   sort[0][category........]='1';    // order items by category priority - ascending
*   sort[0][category........]='9';    // order items by category priority - descending
*   </pre>
*/
function QueryZIDs($fields, $slice_id, $conds, $sort="", $group_by="",    // group_by is never used
    $type="ACTIVE", $slices="", $neverAllItems=0, $restrict_zids=false,
    $defaultCondsOperator = "LIKE", $use_cache=false ) {

  // select * from item, content as c1, content as c2 where item.id=c1.item_id AND item.id=c2.item_id AND       c1.field_id IN ('fulltext........', 'abstract..........') AND c2.field_id = 'keywords........' AND c1.text like '%eufonie%' AND c2.text like '%eufonie%' AND item.highlight = '1';

  global $debug;                 // displays debug messages
  global $nocache;               // do not use cache, if set
  global $CONDS_NOT_FIELD_NAMES; // list of special conds[] indexes (defined in constants.php3)
  global $QueryIDsCount;

  $db = new DB_AA;

  if ( $debug ) huhl("<br>Conds=",$conds,"<br>Sort=",$sort, "<br>Slices=",$slices);


  //create keystring from values, which exactly identifies resulting content
  $keystr = $slice_id. serialize($conds). serialize($sort). $group_by. $type.
            serialize($slices). $neverAllItems.
            ((isset($restrict_zids) && is_object($restrict_zids)) ? serialize($restrict_zids) : "").
            $defaultCondsOperator;
  $cache_condition = ($use_cache AND !$nocache);

  if ( $res = CachedSearch( $cache_condition, $keystr )) {
      return $res;
  }

  if (!$slices) {
      if ($slice_id)
           $slices = array($slice_id);
  }

  if ($GLOBALS[debugfields] || $debug) {
      if ($slices) ProoveFieldNames($slices, $conds);
  }

  ParseMultiSelectConds($conds);
  ParseEasyConds($conds, $defaultCondsOperator);

  if ( $debug ) huhl("Conds=",$conds,"Sort=",$sort, "Slices=",$slices);

  // parse conditions ----------------------------------
  if ( is_array($conds)) {
    $tbl_count=0;
    foreach ($conds as $cond) {

      // fill arrays according to this condition
      $field_count = 0;
      $cond_flds   = '';
      foreach ( $cond as $fid => $v ) {
          // search in all content table fields (new in AA v2.8)
          switch ( strtolower($fid) ) {
              case 'all_fields':          $cond_flds = 'all_fields';
                                          $store     = 'text';
                                          continue;
              case 'all_fields_numeric':  $cond_flds = 'all_fields';
                                          $store     = 'number';
                                          continue;
          }
          if ( $CONDS_NOT_FIELD_NAMES[$fid] ) {
              continue;      // it is not field_id parameters - skip it for now
          }
          if ( !$fields[$fid] OR $v=="") {
              if ($debug) echo "Skipping $fid in conds[]: not known.<br>"; {
                  continue;            // bad field_id or not defined condition - skip
              }
          }

          if ( $fields[$fid]['in_item_tbl'] ) {   // field is stored in table 'item'
              // Long ID in conds should be specified as unpacked, but in db it is packed
              if (($fid == 'id..............') AND (guesstype($cond['value'])=='l')) {
                  $cond['value'] = q_pack_id($cond['value']);
              }
              $select_conds[] = GetWhereExp( 'item.'.$fields[$fid]['in_item_tbl'], $cond['operator'], $cond['value'] );
              if ( $fid == 'expiry_date.....' ) {
                  $ignore_expiry_date = true;
              }
          } else {
              $cond_flds .= ( ($field_count++>0) ? ',' : "" ). "'$fid'";
              // will not work with one condition for text and number fields
              $store      = ($fields[$fid]['text_stored'] ? "text" : "number");
          }
      }
      if ( $cond_flds != '' ) {
        $tbl = 'c'.$tbl_count++;
        // fill arrays to be able construct select command
        $select_conds[] = GetWhereExp( "$tbl.$store",
                                          $cond['operator'], $cond['value'] );

        if ( strpos($cond_flds, 'all_fields')!== false ) {  // we are searching all fields in content table
            $select_tabs[] = "LEFT JOIN content as $tbl
                                     ON ($tbl.item_id=item.id)";
        } elseif ($field_count>1) {
            $select_tabs[] = "LEFT JOIN content as $tbl
                                     ON ($tbl.item_id=item.id
                                     AND ($tbl.field_id IN ($cond_flds) OR $tbl.field_id is NULL))";
        } else {
            $select_tabs[] = "LEFT JOIN content as $tbl
                                     ON ($tbl.item_id=item.id
                                     AND ($tbl.field_id=$cond_flds OR $tbl.field_id is NULL))";
                      // mark this field as sortable (store without apostrofs)
            $sortable[ str_replace( "'", "", $cond_flds) ] = $tbl;
        }
      }
    }
  }

  if ( !is_array($sort) OR count($sort)<1 ) {
    $select_order =  is_object($restrict_zids) ? '' : 'item.publish_date DESC';   // default item order
  } else {
    $delim='';
    foreach ($sort as  $sort_no => $srt) {
      if (key($srt)=='limit') {
        next($srt);       // skip the 'limit' record in the array
      }
      $fid = key($srt);
      if ( !$fields[$fid] ) { // bad field_id - skip
        if ($debug) echo "Skipping sort[x][$fid], don't know $fid.<br>";
        continue;
      }

      if ( $fields[$fid]['in_item_tbl'] ) {   // field is stored in table 'item'
        $fieldId          = 'item.' . $fields[$fid]['in_item_tbl'];
        $select_order    .= $delim  . $fieldId;
        // select_distinct added in order we can group by multiple value fields
        // (items are shown more times)
        $select_distinct .= ", $fieldId as s$sort_no";
        if ( stristr(current( $srt ), 'd'))
          $select_order .= " DESC";
        $delim=',';
      } else {
        if ( !$sortable[ $fid ] ) {           // this field is not joined, yet
          $tbl = 'c'.$tbl_count++;
          // fill arrays to be able construct select command
          $select_tabs[] = "LEFT JOIN content as $tbl
                                   ON ($tbl.item_id=item.id
                                   AND ($tbl.field_id='$fid' OR $tbl.field_id is NULL))";
                        // mark this field as sortable (store without apostrofs)
          $sortable[$fid] = $tbl;
        }

        // join constant table if we want to sort by priority
        $direction = current( $srt );
        if ( stristr($direction,'1') OR stristr($direction,'9') ) { // sort by priority
          if ( !($constgroup = GetConstantGroup($fields[$fid]['input_show_func']) ))
            continue;   // no constant group defined - can't assign priority

          $tbl = 'o'.$tbl_count++;
          // fill arrays to be able construct select command
          $select_tabs[] = "LEFT JOIN constant as $tbl
                                   ON ($tbl.value=". $sortable[$fid] .".text
                                   AND ($tbl.group_id='$constgroup'
                                        OR $tbl.group_id is NULL))";
                        // mark this field as sortable (store without apostrofs)

          // fill arrays according to this sort specification
          $fieldId          = $tbl. ".pri";
          $select_order    .= $delim . $fieldId;
          $select_distinct .= ", $fieldId as s$sort_no";
          if ( stristr($direction,'9') )
            $select_order  .= " DESC";
        } else {                                                   // sort by value
          $store = ($fields[$fid]['text_stored'] ? "text" : "number");
          // fill arrays according to this sort specification
          $fieldId          = $sortable[$fid]. ".$store";
          $select_order    .= $delim . $fieldId;
          $select_distinct .= ", $fieldId as s$sort_no";
          if ( stristr(current( $srt ), 'd'))
            $select_order  .= " DESC";
        }
        $delim=',';
      }
      if ($srt['limit']) {
        $select_limit_field = array('field' => "s$sort_no", 'limit' => $srt['limit']);
      }
    }
  }

  // parse group by parameter ----------------------------
  // .. removed 2/27/2005 Honza (was never used)
  // ---

  if ( $debug )
    huhl("QueryZIDs:slice_id=",$slice_id,"  select_tabs=",$select_tabs,
        "  select_conds=",$select_conds,"  select_order=",$select_order );

  // construct query --------------------------
  $SQL = "SELECT DISTINCT item.id as itemid $select_distinct FROM item ";
  if ( isset($select_tabs) AND is_array($select_tabs))
    $SQL .= " ". implode (" ", $select_tabs);

  $SQL .= " WHERE ";                                         // slice ----------

  if ( is_array($slices) AND (count($slices) >= 1) ) {
      reset ($slices);
      $slicesText = "";
      while (list (,$slice) = each ($slices)) {
          if ($slicesText != "") $slicesText .= ",";
          $slicesText .= "'".q_pack_id($slice)."'";
      }
      $SQL .= 'item.slice_id' . ((count($slices) == 1) ? " = $slicesText AND " :
                                      " IN ($slicesText) AND ");
  }
  if (is_object($restrict_zids)) {
    if ($restrict_zids->count() == 0) {
        return new zids(); // restrict_zids defined but empty - no result
    } else {
       $SQL .= " ".$restrict_zids->sqlin() ." AND ";
    }
  } else {
    // slice(s) or item_ids MUST be specified (in order we can get answer in limited time)
    if (!$slicesText) return new zids();
  }

  // now is rounded in order the time is in steps - it is better for search
  // caching - SQL is THE SAME during one time step
  $now = now('step');            // round up

  /* new version of bin selecting, now we use type of bin from constants.php3 */
  if (is_numeric($type)) { /* $type is numeric constant */
      $numeric_type = max(1,$type);
  } elseif (is_string($type)) { /* for backward compatibility */
      switch ($type) { /* assign to string type it's numeric constant */
          case 'ACTIVE'  : $numeric_type = AA_BIN_ACTIVE;  break;  // 1
          case 'PENDING' : $numeric_type = AA_BIN_PENDING; break;  // 2
          case 'EXPIRED' : $numeric_type = AA_BIN_EXPIRED; break;  // 4
          case 'HOLDING' : $numeric_type = AA_BIN_HOLDING; break;  // 8
          case 'TRASH'   : $numeric_type = AA_BIN_TRASH;   break;  // 16
          case 'ALL'     : $numeric_type = (AA_BIN_ACTIVE | AA_BIN_EXPIRED | AA_BIN_PENDING | AA_BIN_HOLDING | AA_BIN_TRASH); break;
          default        : $numeric_type = AA_BIN_ACTIVE;  break;  // 1
      }
  } else { /* strange case, I think never possible :) */
      $numeric_type = AA_BIN_ACTIVE;
  }
/* create SQL query for different types of numeric constants */
    if ($numeric_type == (AA_BIN_ACTIVE | AA_BIN_EXPIRED | AA_BIN_PENDING | AA_BIN_HOLDING | AA_BIN_TRASH)) {
      $SQL .= " 1=1 ";
    } elseif ($numeric_type == (AA_BIN_ACTIVE | AA_BIN_EXPIRED | AA_BIN_PENDING)) {
      $SQL .= " item.status_code=1 ";
    } elseif ($numeric_type == (AA_BIN_ACTIVE | AA_BIN_PENDING)) {
//      $SQL .= " item.status_code=1 AND (item.expiry_date > '$now' OR item.expiry_date IS NULL) ";
      $SQL .= " item.status_code=1 AND (item.expiry_date > '$now') ";
    } else {
        $SQL2 = "";
        if ($numeric_type & AA_BIN_ACTIVE) {
//          $SQL2 .= " ( item.status_code=1 AND (  item.publish_date <= '$now' OR item.publish_date IS NULL ) ";
          $SQL2 .= " ( item.status_code=1 AND (item.publish_date <= '$now') ";
            /* condition can specify expiry date (good for archives) */
            if ( !( $ignore_expiry_date &&
                   defined("ALLOW_DISPLAY_EXPIRED_ITEMS") && ALLOW_DISPLAY_EXPIRED_ITEMS) ) {
//              $SQL2 .= " AND (item.expiry_date > '$now' OR item.expiry_date IS NULL) ";
              $SQL2 .= " AND (item.expiry_date > '$now') ";
            }
          $SQL2 .= ' )';
        }
        if ($numeric_type & AA_BIN_EXPIRED) {
            if ($SQL2 != "") { $SQL2 .= ' OR '; }
            $SQL2 .= " (item.status_code=1 AND item.expiry_date <= '$now') ";
        }
        if ($numeric_type & AA_BIN_PENDING) {
            if ($SQL2 != "") { $SQL2 .= ' OR '; }
            $SQL2 .= " (item.status_code=1 AND item.publish_date > '$now') ";
        }
        if ($numeric_type & AA_BIN_HOLDING) {
            if ($SQL2 != "") { $SQL2 .= ' OR '; }
            $SQL2 .= " (item.status_code=2) ";
        }
        if ($numeric_type & AA_BIN_TRASH) {
            if ($SQL2 != "") { $SQL2 .= ' OR '; }
            $SQL2 .= " (item.status_code=3) ";
        }
        $SQL .= " ( ".$SQL2 ." ) ";
    }

  if ( isset($select_conds) AND is_array($select_conds))      // conditions -----
    $SQL .= " AND (" . implode (") AND (", $select_conds) .") ";

  if ( $select_order )                                 // order ----------
    $SQL .= " ORDER BY $select_order";

  // add comment to the SQL command (for debug purposes)
  $SQL .= " -- AA slice_id: $slice_id";
  if ($GLOBALS['slice_info']) $SQL .= ", slice_name: ". $GLOBALS['slice_info']['name'];
  if ($GLOBALS['vid'])        $SQL .= ", vid: ".        $GLOBALS['vid'];
  if ($GLOBALS['view_info'])  $SQL .= ", view_name: ".  $GLOBALS['view_info']['name'];

  // if neverAllItems is set, return empty set if no conds[] are used
  $str2find = new CacheStr2find($slices, 'slice_id');
  $str2find->add($slice_id, 'slice_id');
  return GetZidsFromSQL($SQL, 'itemid', $cache_condition, $keystr, $str2find, 'p',
                  !is_array($select_conds) && $neverAllItems,
                  // last parameter is used for sorting zids to right order
                  // - if no order specified and restrict_zids are specified,
                  // return zids in unchanged order
                  (is_object($restrict_zids) AND !$select_order) ? $restrict_zids : null, $select_limit_field);
}

/** Finds constant ZIDs for constants to be shown in a slice / view
*   @param string $group_id   constant group to search in
*   @param array  $conds      search conditions {see QueryZIDs, FAQ}
*   @param array  $sort       sort fields       {see QueryZIDs, FAQ}
*   @param string $type       not used, yet
*   @param array  $restrict_zids
*       Use it if you want to choose only from a set of constants
*   @param string $defaultCondsOperator
*       replaces the default "RLIKE" for conditions with no operator set
*   @param bool   $use_cache should be the cache searched for the result?
*
*   @return A zids object with a list of the ids that match the query.
*
*   @global  int $QueryIDsCount (out) is set to the count of IDs returned
*
*   Parameter format example - {see QueryZIDs, FAQ}
*   Fields definition - {see include/constants.php3}
*/
function QueryConstantZIDs($group_id, $conds, $sort="", $type="",
    $restrict_zids=false, $defaultCondsOperator = "RLIKE", $use_cache=false ) {

    global $debug;                 // displays debug messages
    global $nocache;               // do not use cache, if set

    // set default sortorder for constants if sortorder is not set
    if ( !isset($sort) OR !is_array($sort) OR count($sort)<1) {
        $sort[] = array( 'const_priority' => 'a');
        $sort[] = array( 'const_name' => 'a');
    }
    // for backward compatibily rename value to const_value ... (used in old views)
    if ( key($sort[0]) == 'value' ) $sort[0] = array('const_value'    => $sort[0]['value']);
    if ( key($sort[0]) == 'name' )  $sort[0] = array('const_name'     => $sort[0]['name']);
    if ( key($sort[0]) == 'pri' )   $sort[0] = array('const_priority' => $sort[0]['pri']);


    if ( $debug ) huhl( "<br>Conds:", $conds, "<br>--<br>Sort:", $sort, "<br>--");

    //create keystring from values, which exactly identifies resulting content
    $keystr = $group_id. serialize($conds). serialize($sort). $type.
                  ((isset($restrict_zids) && is_object($restrict_zids)) ? serialize($restrict_zids) : "").
                  $defaultCondsOperator;

    $cache_condition = ($use_cache AND !$nocache);
    if ( $res = CachedSearch( $cache_condition, $keystr )) {
        return $res;
    }

    // parse conditions and sort order ----------------------------------
    $where_sql    = MakeSQLConditions( GetConstantFields(), $conds, $defaultCondsOperator, $foo);
    $order_by_sql = MakeSQLOrderBy(    GetConstantFields(), $sort,  $foo);

    // construct query --------------------------
    $SQL  = "SELECT DISTINCT constant.short_id FROM constant
             WHERE group_id='$group_id' ";
    $SQL .=  $where_sql . $order_by_sql;

    if (is_object($restrict_zids)) {
        if ($restrict_zids->count() == 0)
            return new zids(); // restrict_zids defined but empty - no result
        $SQL .= ' AND '.$restrict_zids->sqlin();
    }


    // get result --------------------------
    $str2find = new CacheStr2find($group_id, 'group_id');
    return GetZidsFromSQL($SQL, 'short_id', $cache_condition, $keystr, $str2find);
}

// -------------------------------------------------------------------------------------------

/* Function: QueryDiscIDs
   Purpose:  Finds discussion items IDs to be shown by the aa/discussion.php3 script
*/

function QueryDiscIDs($slice_id, $conds, $sort, $slices ) {
  // parameter format example:
  // conds[0][discussion][subject] = 1;   // discussion fields are preceded by [discussion]
  // sort[0][category........]='a';    // order items by category ascending

    if (!$slice_id && !$slices) return;

    $fields = array ("date","subject","author","e_mail","body","state","flag","url_address",
        "url_description", "remote_addr", "free1", "free2");

    global $debug;          // displays debug messages

    $db = new DB_AA;
    if ( $debug ) {
      echo "<br>Conds:<br>";
      p_arr_m($conds);
      echo "<br><br>Sort:<br>";
      p_arr_m($sort);
      echo "<br><br>Slices:<br>";
      p_arr_m($slices);
    }

    // parse conditions ----------------------------------
    if (is_array($conds)) {
        reset($conds);
        $tbl_count=0;
        while ( list( , $cond) = each( $conds )) {
            if ( !is_array($cond) OR !$cond['discussion']
                              OR !$cond['operator'] OR ($cond['value']==""))
              continue;             // bad condition - ignore

            // fill arrays according to this condition
            reset($cond);
            while ( list( $fid, $vv) = each( $cond ))
                if ( $fid == 'discussion' ) {
                    unset ($select_cond);
                    while ( list ($fid) = each ($vv)) {
                        if ( my_in_array ($fid,$fields) AND $cond['value'] > "" ) {
                            $select_cond[] = GetWhereExp( "discussion.$fid",
                                              $cond['operator'], $cond['value'] );
                        }
                    }
                    if (is_array($select_cond))
                        $select_conds[] = join ($select_cond, " OR ");
                }
        }
    }
/*
  // parse sort order ----------------------------
  if ( !(isset($sort) AND is_array($sort)))
    $select_order = 'item.publish_date DESC';   // default item order
  else {
    reset($sort);
    $delim='';
    while ( list( , $srt) = each( $sort )) {
      $fid = key($srt);
      if ( !$fields[$fid] )  // bad field_id - skip
          continue;

      if ( $fields[$fid]['in_item_tbl'] ) {   // field is stored in table 'item'
        $select_order .= $delim . 'item.' . $fields[$fid]['in_item_tbl'];
        if ( stristr(current( $srt ), 'd'))
          $select_order .= " DESC";
        $delim=',';
      } else {
        if ( !$sortable[ $fid ] ) {           // this field is not joined, yet
          $tbl = 'c'.$tbl_count++;
          // fill arrays to be able construce select command
          $select_tabs[] = "LEFT JOIN content as $tbl
                                   ON ($tbl.item_id=item.id
                                   AND ($tbl.field_id='$fid' OR $tbl.field_id is NULL))";
                        // mark this field as sortable (store without apostrofs)
          $sortable[$fid] = $tbl;
        }

        $store = ($fields[$fid]['text_stored'] ? "text" : "number");
        // fill arrays according to this sort specification
        $select_order .= $delim .$sortable[$fid]. ".$store";
        if ( stristr(current( $srt ), 'd'))
          $select_order .= " DESC";
        $delim=',';
      }
    }
  }
*/

if ( $debug ) {
  echo "<br><br>select_conds:";
  print_r($select_conds);
  echo "<br><br>select_order:";
  print_r($select_order);
}

    // construct query --------------------------
    $SQL = "SELECT discussion.id
            FROM discussion INNER JOIN item ON item.id = discussion.item_id
            WHERE ";
    if ( $slices ) {
        $slicesText = "";
        for ($islice = 0; $islice < count($slices); ++$islice) {
            if ($islice) $slicesText .= ",";
            $slicesText .= "'".q_pack_id($slices[$islice])."'";
        }
        $SQL .= " item.slice_id IN ( $slicesText )";
    }
    else if ( $slice_id )
        $SQL .= " item.slice_id = '". q_pack_id($slice_id) ."'";

    if ( isset($select_conds) AND is_array($select_conds))      // conditions -----
        $SQL .= " AND (" . implode (") AND (", $select_conds) .") ";

    if ( isset($select_order) )                                 // order ----------
        $SQL .= " ORDER BY $select_order";

    // get result --------------------------
    $db->tquery($SQL);

    while ( $db->next_record() )
        $arr[] = unpack_id128($db->f(id));

    return $arr;
}



// ----------- Easy query -------- parse query functions first

// test for closed parenthes
function test_for_closed($search) {
  $zavorky=0;
  $uvozovky=0;
  for ($i=0; $i< strlen($search); $i++){
    switch ($search[$i]) {
      case "(" : $zavorky++;
                 break;
      case ")" : $zavorky--;
                     break;
      case "\"" :
                  if ($uvozovky==1)
                    $uvozovky--;
                      else
                    $uvozovky++;
                    break;
    }
  }
  $retval = $zavorky+$uvozovky;
  return $retval;
}

// Prepares query
//   - replaces + and - sing with AND and NOT
//   - replaces wildcards * and ?
function arrange_query($search) {
  // make case insenzitive
  $search = eregi_replace(" AND "," and ", $search);
  $search = eregi_replace(" OR "," or ", $search);
  $search = eregi_replace(" NOT "," not ",$search);
  $retstr = "";
  for ($i=0; $i<strlen($search); $i++) {
    switch($search[$i]) {
      case "\"" : if ($uvozovky == 1) { $uvozovky--; }
                  else { $uvozovky++; }
                  $retstr = $retstr . $search[$i];
                  break;
      case "+" : if ($uvozovky == 0) { $retstr = $retstr . " and "; }
                  else { $retstr = $retstr . $search[$i]; }
                  break;
      case "-" : if ($uvozovky == 0)
                    { $retstr = $retstr . " not "; }
                  else { $retstr = $retstr . $search[$i]; }
                  break;
      case "(" : break;
      case ")" : break;
      case "*" : if ($uvozovky == 0) { $retstr = $retstr . "%"; }
                 else { $retstr = $retstr . "*"; }
                 break;
      case "?" : if ($uvozovky == 0) { $retstr = $retstr . "_"; }
                 else { $retstr = $retstr . "?"; }
                 break;
      default : $retstr = $retstr . $search[$i];
    }
  }
  $retstr = ereg_replace("([[:blank:]]+)"," ", $retstr);
  return $retstr;
}

function parse_query($search, $default_op="and") {
  $terms=array();
  $dummy=$search;
  $strtype=1;

  do {
    if ($dummy[0]=="\"") {
      $strtype=0;
      $dummy=substr($dummy, 1, strlen($dummy));
      $dummy2=substr($dummy, 0, strpos($dummy, "\"")+1);
      $dummy2="\"".$dummy2;
      if (strpos($dummy, "\"")+1 == strlen($dummy)) {
        $dummy2 = "\"". $dummy; $dummy = "";
      } else {
        $dummy=substr($dummy, strpos($dummy,  "\" ")+2, strlen($dummy));
      }
      $dummy2 = ereg_replace("\"","",$dummy2);
// tady to ma nejaky problemy, s tema zavorkama to beha neunosne pomalu
//    } elseif ($dummy[0]=="(") {
//      $dummy=substr($dummy, 1, strlen($dummy));
//      $dummy2="(";
//    } elseif ($dummy[0]==")") {
//      $dummy=substr($dummy, 1, strlen($dummy));
//      $dummy2=")";
    } else {
      $dummy2=substr($dummy, 0, strpos($dummy, " "));
      switch ($dummy2) {
        case "and" :
        case "not" :
        case "or" : $strtype=1;
                      break;
        default : if ($strtype != 1) { $terms[]=$default_op; } else { $strtype = 0; }
      }
      if (strpos($dummy, " ") != false) {
        $dummy=substr($dummy, strpos($dummy, " ")+1, strlen($dummy));
      } else { $dummy2=$dummy; $dummy=""; }
    }
    $terms[]=$dummy2;
  }
  while (strlen($dummy)!=0);
  return $terms;
}

// creates SQL query
function build_sql_query($searchterms, $field) {
  reset($searchterms);
  $retstr = "";
  $notcls = 0; $typecls=0;
  while (current($searchterms)) {
    switch (current($searchterms)) {
      case "and" : if ($typecls==0) { $retstr = $retstr . " AND "; $typecls=1; }
                 next($searchterms);
                 break;
      case "or" : if ($typecls==0) { $retstr = $retstr . " OR "; $typecls=1; }
                    next($searchterms);
                    break;
      case "not" : if ($typecls==0) {
                    $retstr = $retstr . " AND "; $typecls=1;
                    $notcls = 1;
                  }
                  next($searchterms);
                  break;
      case "(" : $retstr = $retstr . "(";
                 break;
      case ")" : $retstr = $retstr . ")";
                 break;
      default : if ($notcls==1) { $retstr = $retstr . "(". $field . " NOT LIKE '%" . current($searchterms) . "%')"; }
                else { $retstr = $retstr . "(". $field . " LIKE '%" . current($searchterms) . "%')"; }
                $notcls = 0; $typecls=0;
                next($searchterms);
    }
  }
  if ($typecls != 0) { $retstr=""; }
  return  $retstr;
}


function GetIDs_EasyQuery($fields, $db, $p_slice_id, $srch_fld, $from, $to,
                          $query, $relevance=false) {

  $in = "";
  $delim = "";
  $field_no = 0;

  // prepare query
  $search=trim(stripslashes(rawurldecode($query)));
    $query = str_replace("\\", "\\\\", $query);
    $query = str_replace("%", "\%", $query);
    $query = str_replace("_", "\_", $query);
    $query = str_replace("'", "\'", $query);

  if (test_for_closed($search) != 0)
   return false;
  $search = arrange_query($search);
  $myqueryterms = parse_query($search);

  $sqlstring=build_sql_query($myqueryterms, "text"); // "concat(' ',text)"); // add space to begining for better word matching

  if ( trim($sqlstring) == "" )
    $sqlstring = "1=1";

  if ( !isset($srch_fld) OR !is_array($srch_fld) OR !$query )
    $field_id_cond = "1=1";               // no fields to search - all rows

  reset($srch_fld);
  while ( list( $fid, $val ) = each($srch_fld) ) {
    if ( !$fields[$fid] )     // bad condition - field not exist in this slice
      continue;
    $in .= $delim. "'$fid'";
    $delim=',';
    $field_no++;
  }

  if ( $field_no == 0 )
    $field_id_cond = "1=1";  // bad condition - field not exist in this slice
   else
    $field_id_cond = "field_id IN ( $in )";

  // from date
  if ( ereg("^ *([[:digit:]]{1,2}) */ *([[:digit:]]{1,2}) */ *([[:digit:]]{4}) *$", $from, $part))
    $cond = " AND (publish_date >= '". mktime(0,0,0,$part[1],$part[2],$part[3]). "') ";
  elseif( ereg("^ *([[:digit:]]{1,2}) */ *([[:digit:]]{1,2}) */ *([[:digit:]]{2}) *$", $from, $part))
    $cond = " AND (publish_date >= '". mktime(0,0,0,$part[1],$part[2],"20".$part[3]). "') ";

  // to date
  if ( ereg("^ *([[:digit:]]{1,2}) */ *([[:digit:]]{1,2}) */ *([[:digit:]]{4}) *$", $to, $part))
    $cond .= " AND (publish_date <= '". mktime(23,59,59,$part[1],$part[2],$part[3]). "') ";
  elseif( ereg("^ *([[:digit:]]{1,2}) */ *([[:digit:]]{1,2}) */ *([[:digit:]]{2}) *$", $to, $part))
    $cond .= " AND (publish_date <= '". mktime(23,59,59,$part[1],$part[2],"20".$part[3]). "') ";

  $distinct = ( $relevance ? "" : "DISTINCT" );

  $SQL = "SELECT $distinct id from item, content WHERE item.id=content.item_id
            AND slice_id='$p_slice_id'
            AND ($field_id_cond)
            AND ($sqlstring)
            AND status_code='1'
            AND expiry_date > '". time() ."'
            $cond
            ORDER BY publish_date DESC";

  $db->query($SQL);

  // search by relevance? (not at all - just count the fields, where the word appears)
  if ( $relevance ) {
    $count=0;
    if ( $db->next_record() )      //preset first old id
      $oldid = $db->f(id);

    while ( $db->next_record() ) {
      if ( $oldid != $db->f(id)) {
        $tmp[$count][] = unpack_id128($db->f(id));
        $oldid = $db->f(id);
        $count=0;
      }
      else  {
        $count++;
      }
    }
    $tmp[$count][] = unpack_id128($oldid);  // last value isn't stored

//print_r($tmp);

    // put the array one after one - first goes the best one
    for ( $i=$field_no; $i > 0; $i-- )
      $ret = array_merge( $tmp[$i], $tmp[$i-1] );
  } else {
    while ( $db->next_record() )
      $ret[] = unpack_id128($db->f(id));
  }

  return $ret;
}

?>