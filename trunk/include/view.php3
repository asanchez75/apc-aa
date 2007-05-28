<?php
/**
 *
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program (LICENSE); if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package   UserInput
 * @version   $Id$
 * @author    Honza Malik <honza.malik@ecn.cz>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Copyright (C) 1999, 2000 Association for Progressive Communications
 * @link      http://www.apc.org/ APC
 *
*/
require_once AA_INC_PATH . "mgettext.php3";
require_once AA_INC_PATH . "itemview.php3";
require_once AA_INC_PATH . "viewobj.php3";
require_once AA_INC_PATH . "searchlib.php3";
require_once AA_BASE_PATH. "modules/links/util.php3";
require_once AA_BASE_PATH. "modules/links/linksearch.php3";
// add mlx functions
require_once AA_INC_PATH."mlx.php";

// ----------------------------------------------------------------------------
//                         view functions
// ----------------------------------------------------------------------------
/** GetAliasesFromUrl function
 * @param $als
 */
function GetAliasesFromUrl($als) {
    $ret = array();
    if (is_array($als) ) {
        foreach ( $als as $k => $v ) {
            $ret["_#".$k] = GetAliasDef( "f_s:$v" );
        }
    }
    return $ret;
}


/** Class for storing url commands for view (object of this class stores just
 *  one command
 */
class ViewCommand {
    var $command;      /** type of the command (like x, c, d, v, ...) */
    var $parameters;   /** command parameters */

    /** ViewCommand function
     *  constructor
     *  @param $command the letter indicating the command (x, c, d, v, ...)
     *  @param $parameters array of command patameters
     */
    function ViewCommand($command, $parameters) {
        $this->command    = $command;
        $this->parameters = $parameters;
    }
    /** addParameters function
     * @param $parameters
     */
    function addParameters($parameters) {
        $this->parameters = array_merge($this->parameters, $parameters);
    }
    /** getCommand function
     *
     */
    function getCommand() {
        return $this->command;
    }
    /** getParameter function
     * @param $index
     */
    function getParameter($index) {
        return $this->parameters[$index];
    }
    /** getParameterArray function
     * @param $offset
     */
    function getParameterArray($offset=0) {
        return array_slice($this->parameters, $offset);
    }


}

/** Class for storing set of url commands for view  */
class AA_View_Commands {
    var $commands;     /** array of objects of ViewCommand class */

    /** AA_View_Commands function
     *  constructor - calls parseCommand()
     * @param $cmd
     * @param $als
     */
    function AA_View_Commands($cmd, $als=false) {
        $this->commands = array();
        $this->parseCommand($cmd, $als);
    }

    /** get function
     *  @return command given by command letter (say 'd')
     * @param $command
     */
    function get($command) {
        return $this->commands[$command];
    }

    /** count function
     *  returns number of commands in the set
     */
    function count() {
        return count($this->commands);
    }
    /** reset function
     *
     */
    function reset() {
        return reset($this->commands);
    }
    /** next function
     *
     */
    function next() {
        return next($this->commands);
    }
    /** current function
     *
     */
    function current() {
        return current($this->commands);
    }


    /** addCommand function
     *  add new command to the command set
     *  @param $command the letter indicating the command (x, c, d, v, ...)
     *  @param $parameters array of command patameters
     */
    function addCommand($command, $parameters) {
        // only one command of specific type must be present
        if ( isset($this->commands[$command]) ) {
            // if the command is already set, then the parameters are appended
            $this->commands[$command]->addParameters($parameters);
        } else {
            $this->commands[$command] = new ViewCommand($command, $parameters);
        }
    }

    /** parseCommand function
     * Separates 'cmd' parameters for current view into array.
     *
     * Parameters are separated by '-'. To escape '-' character use '--' (then it
     * is not used as parameter separator. If you use aliases inside cmd[], then
     * the aliases are expanded by this function (depending on als[] alias array)
     * @param string $cmd cmd[<vid>] string from url
     * @param array $als array of aliases used to expand inside cmd
     *                   (als[] obviously comes from url, as well)
     * @return array separated cmd parameters
     */
    function parseCommand($cmd, $als=false) {

        if ( $GLOBALS['debug'] ) {
            huhl("<br>ParseCommand - cmd:", $cmd);
        }

        // cmd could be array - in this case more commands are passed to view
        // Ussage: cmd[89][]=c-1-On&cmd[89][]=c-2-Cars&cmd[89][]=x-89-2233-5244
        // this will display items 2233 and 5244 if passes the conditions
        foreach( (array)$cmd as $cmd_part ) {

            // substitute url aliases in cmd
            if (isset($als) AND is_array($als)) {
                foreach ($als as $k => $v) {
                    // we are replacing the aliases in the url, so you can write
                    // something like {view.php3?vid=12&cmd[]=x-34-CAMPAIGN&als[CAMPAIGN]=42525}
                    // which displays item 42525. als[] could come through url, also.
                    // Be carefull with alias names - It is good idea to use longer
                    // aliases (say 8 characters), because you can come into trouble
                    // if you use say als[x], because then all 'x'es are replaced in
                    // cmd including the first x in cmd[]=x-34-..

                    // you can use also _#CAMPAIGN in url:
                    // {view.php3?vid=12&cmd[]=x-34-_%23CAMPAIGN&als[CAMPAIGN]=42525}
                    // (%23 is urlencoded %23)
                    if (substr($k,0,2) != '_#') {
                        $k2 = '_#'.$k;
                    }
                    $cmd_part = str_replace($k2, $v, $cmd_part);

                    $cmd_part = str_replace($k,  $v, $cmd_part);
                }
            }

            $splited = split_escaped("-", $cmd_part, "--");
            $this->addCommand($splited[0], array_slice($splited,1));
        }
    }

}

/** ParseSettings function
 * Separates 'set' parameters for current view into array. To escape ','
 *                 character uses ',,'.
 * @param string $set set[<vid>] string from url. 'set' parameters are in form
 *                    set[<vid>]=property1-value1,property2-value2
 * @return array asociative array of properties
 */
function ParseSettings($set) {
    $sets = split_escaped(",", $set, ",,");
    if (!(isset($sets) AND is_array($sets))) {
        return false;
    }
    foreach ($sets as $v) {
        $pos = strpos($v,'-');
        if ($pos) {
            $ret[substr($v,0,$pos)] = substr($v,$pos+1);
        }
    }
    return $ret;
}

/** ParseViewParameters function
 *  Converts a query string into a data view_params data structure
 * @param $query_string
 */
function ParseViewParameters($query_string="") {
    global $cmd, $set, $vid, $als, $slice_id, $debug;
    global $x;   // url parameter - used for cmd[]=x-111-url view parameter

    // Parse parameters
    // if view in cmd[] or set[] is not specified - fill it from vid
    if ( preg_match("/vid=([0-9]*)/", $query_string, $parts) ) {
        $query_string = str_replace( 'cmd[]', "cmd[".$parts[1]."]", $query_string );
        $query_string = str_replace( 'set[]', "set[".$parts[1]."]", $query_string );
    }
    //  This code below do not work!! - it is not the same as the code above!!
    //  (the code above parses only the specific guerystring for this view)
    //  if (!$cmd[$vid]) {        // (the same for set[])
    //      $cmd[$vid] = $cmd[0];
    //  }
    //  $command = ParseCommand($cmd[$vid], $GLOBALS['als']);

    add_vars($query_string);       // adds values from url (it's not automatical in SSIed script)

    if ( $debug ) {
        $query_string = str_replace("slice_pwd=". $GLOBALS['slice_pwd'], 'slice_pwd=*****', $query_string );
        huhl("ParseViewParameters: vid=$vid, query_string=$query_string", "cmd:", $cmd, "set:", $set, "als:", $als);
    }

    // Splits on "-" and subsitutes aliases
    $commands = new AA_View_Commands($cmd[$vid], $GLOBALS['als']);
    $v_conds  = new AA_Set;

    if ( $GLOBALS['debug'] ) huhl("<br>ParseViewParameters - command:", $commands);

    $commands->reset();
    while($command = $commands->current()) {
        $commands->next();
        switch ($command->getCommand()) {
            case 'v':  $vid = $command->getParameter(0);
                       break;

            case 'o':  // the same as x, but no hit for item is added
            case 'i':  // i is exactly the same as o, now
            case 'x':  $vid = $command->getParameter(0);
                       $zids = new zids(($command->getParameter(1)=='url') ? $x : $command->getParameterArray(1));

                       // This is bizarre code, just incrementing the first item, left as it is
                       // but questioned on apc-aa-coders - mitra
                       if (($command->getCommand()=='x') AND ($zids->count()>0)) {
                           $s_or_l_ids = ( $zids->use_short_ids() ? $zids->shortids() : $zids->longids() );
                           CountHit($s_or_l_ids[0]);
                       }
                       break;

            case 'c':  // Check for experimental c-OR-1-aaa-2-bbb-3-ccc syntax
                       // Note param_conds[0] is not otherwise used
                       // It is converted into conds in GetViewConds
                       // which is consumed in ParseMultiSelectConds
                       if ($command->getParameter(0) == 'OR') {
                           $param_conds[0] = 'OR';
                           $command_params = $command->getParameterArray(1);
                       } else {
                           $command_params = $command->getParameterArray();
                       }


                       if (AA_Set::check($command_params[0], $command_params[1])) {
                           $param_conds[$command_params[0]] = stripslashes($command_params[1]);
                       }
                       if (AA_Set::check($command_params[2], $command_params[3])) {
                           $param_conds[$command_params[2]] = stripslashes($command_params[3]);
                       }
                       if (AA_Set::check($command_params[4], $command_params[5])) {
                           $param_conds[$command_params[4]] = stripslashes($command_params[5]);
                       }
                       break;

            case 'd':  $v_conds->addFromCommand($command);
                       break;
        }
    }

    $arr = ParseSettings($set[$vid]);

    // Following line is here just for caching purposes - we are creating cache
    // keystring from view parameters and we need to add all set[] and cmd[]
    // in the keystring, because of cases like:
    //     view.php3?vid=1781&set[997]=selected-759644
    // (view 997 is called inside 1781)
    $arr['forcache'] = array($set, $cmd);

    if ($arr['slices']) {
        $arr['slices'] = explode('-', $arr['slices']);
    }


    // the parameters for discussion comes (quite not standard way) from globals
    if ( !$arr["all_ids"] ) {
        $arr["all_ids"]     = $GLOBALS['all_ids'];
    }
    if ( !$arr["ids"] ) {
        $arr["ids"]         = $GLOBALS['ids'];
    }
    if ( !$arr["sel_ids"] ) {
        $arr["sel_ids"]     = $GLOBALS['sel_ids'];
    }
    if ( !$arr["add_disc"] ) {
        $arr["add_disc"]    = $GLOBALS['add_disc'];
    }
    if ( !$arr["sh_itm"] ) {
        $arr["sh_itm"]      = $GLOBALS['sh_itm'];
    }
    if ( !$arr["parent_id"] ) {
        $arr["parent_id"]   = $GLOBALS['parent_id'];
    }

    // IDs of discussion items for discussion list
    if ( !$arr["disc_ids"] ) {
        $arr["disc_ids"]    = $GLOBALS['disc_ids'];
    }

    // used for discussion list view
    if ( !$arr["disc_type"] ) {
        $arr["disc_type"]   = $GLOBALS['disc_type'];
    }

    // used for Links module - categories and links
    if ( !$arr["cat"] ) {
        $arr["cat"]         = $GLOBALS['cat'];
    }
    if ( !$arr["show_subcat"] ) {
        $arr["show_subcat"] = $GLOBALS['show_subcat'];
    }

    $arr['als']         = GetAliasesFromUrl($GLOBALS['als']);
    $arr['vid']         = $vid;
    $arr['conds']       = $v_conds->getConds();
    $arr['param_conds'] = $param_conds;
    //  $arr['item_ids'] = $item_ids;
    $arr['zids']        = $zids;

    if ( $debug ) {
        huhl($arr);
    }

    return $arr;
}


/** ResolveCondsConflict function
 * Helper function for GetViewConds() - resolves database x url conds conflict
 * @param $conds
 * @param $fld
 * @param $op
 * @param $val
 * @param $param
 */
function ResolveCondsConflict(&$conds, $fld, $op, $val, $param) {
    if ($fld AND $op) {
        $conds[] = array( 'operator' => $op,
                          'value'    => (strlen((string)$param)>0 ? $param : $val),  // param could be also "0"
                          $fld       => 1 );
    }
}

/** GetViewConds function
 * Fills array with conditions defined through
 * 'Slice Admin' -> 'Design View - Edit' -> 'Conditions' setting
 * @param array $view_info view definition from database in asociative array
 * @param array $param_conds possibly redefinition of conds from url (cmd[]=c)
 * @return array conditions array
 */
// If param_conds[0] = "OR" as set by ParseViewParameters then set valuejoin
// used by ParseMultiSelectConds
function GetViewConds($view_info, $param_conds) {
    // param_conds - redefines default condition values by url parameter (cmd[]=c)

    if ( $GLOBALS['debug'] ) {
        huhl("<br>(GetViewConds) param_conds=",$param_conds);
    }

    ResolveCondsConflict($conds, $view_info['cond1field'], $view_info['cond1op'], $view_info['cond1cond'],  $param_conds[1]);
    ResolveCondsConflict($conds, $view_info['cond2field'], $view_info['cond2op'], $view_info['cond2cond'],  $param_conds[2]);
    ResolveCondsConflict($conds, $view_info['cond3field'], $view_info['cond3op'], $view_info['cond3cond'],  $param_conds[3]);
    if ($param_conds[0]) {
        $cond['valuejoin'] = $param_conds[0];
    }

    if ( $GLOBALS['debug'] ) huhl("<br>(GetViewConds) conds=",$conds);

    return $conds;
}
/** GetViewSort function
 * @param $view_info
 * @param $param_sort
 */
function GetViewSort(&$view_info, $param_sort=null) {
    global $VIEW_SORT_DIRECTIONS;
    // translate sort codes (we use numbers in views from historical reason)
    // '0'=>_m("Ascending"), '1' => _m("Descending"), '2' => _m("Ascending by Priority"), '3' => _m("Descending by Priority")

    $sort = false;
    if ($param_sort['sort']) {
        if ( $param_sort['sort'] == 'AAnoSORT' ) {
            return false;
        }
        $order    = new AA_Sortorder;
        $order->addSortFromString($param_sort['sort']);
        $sort = $order->getOrder();
    }
    // grouping
    if ($view_info['group_by1']) {
        $sort[] = array($view_info['group_by1'] => $VIEW_SORT_DIRECTIONS[$view_info['g1_direction']]);
    }
    if ($view_info['group_by2']) {
        $sort[] = array($view_info['group_by2'] => $VIEW_SORT_DIRECTIONS[$view_info['g2_direction']]);
    }
    //sorting
    if ($view_info['order1']) {
        $sort[] = array($view_info['order1'] => $VIEW_SORT_DIRECTIONS[$view_info['o1_direction']]);
    }
    if ($view_info['order2']) {
        $sort[] = array($view_info['order2'] => $VIEW_SORT_DIRECTIONS[$view_info['o2_direction']]);
    }

    if ($param_sort['group_limit'] AND count($sort)>0) {
        reset($sort);   // go to first record
        $sort[key($sort)]['limit'] = $param_sort['group_limit'];
    }
    return $sort;
}

/** ParseBannerParam function
 *  Parses banner url parameter (for view.php3 as well as for slice.php3
 *  (banner parameter format: banner-<position in list>-<banner vid>-[<weight_field>]
 *  (@see {@link http://apc-aa.sourceforge.net/faq/#219})
 */
function ParseBannerParam($banner_param) {
    $ret = array();
    if ( $banner_param ) {
        list( $foo_pos, $foo_vid, $foo_fld ) = explode('-',$banner_param);
        $ret['banner_position']   = $foo_pos;
        $ret['banner_parameters'] = "vid=$foo_vid";
        if ($foo_fld == 'norandom') {
            return $ret;
        }
        $ret['banner_parameters'] .= "&set[$foo_vid]=random-". ($foo_fld ? $foo_fld : 1);
    }
    return $ret;
}
/** GetListLength function
 * @param $listlen
 * @param $to
 * @param $from
 * @param $page
 * @param $idscount
 * @param $random
 */
function GetListLength($listlen, $to, $from, $page, $idscount, $random) {
    $list_from = max(0, $from-1);    // user counts items from 1, we from 0
    $list_to   = max(0, $to-1);      // user counts items from 1, we from 0

    if ( $to > 0 ) {
        $listlen = max(0, $list_to - $list_from + 1);
    }

    if ($page) {      // split listing to pages
        // Format:  <page>-<number of pages>
        $pos = strpos($page,'-');
        if ( $pos ) {
            $no_of_pages = substr($page,$pos+1);
            $page_n      = substr($page,0,$pos)-1;    // count from zero
            // to be last page shorter than others if there is bad number of items
            $list_from   = $page_n * floor($idscount/$no_of_pages);
            $listlen     = floor(($idscount*($page_n+1))/$no_of_pages) - floor(($idscount*$page_n)/$no_of_pages);
        } else {
            // second parameter is not specified - take listlen parameter
            $list_from   = $listlen * ($page-1);
        }
    }
    return array( $listlen, $random ? $random : ($list_from ? $list_from : 0) );
}


/** GetView function
 *  Expand a set of view parameters, and return the view
 * @param $view_param
 */
function GetView($view_param) {
    global $nocache, $debug;
    //create keystring from values, which exactly identifies resulting content
    $keystr = serialize($view_param). AA_Stringexpand_Keystring::expand();

    if ( $res = $GLOBALS['pagecache']->get($keystr, $nocache) ) {
        return $res;
    }

    global $str2find_passon, $pagecache;
    $str2find_save   = $str2find_passon;    // Save str2find from same level
    $str2find_passon = new CacheStr2find(); // clear it for caches stored further down
    $res             = GetViewFromDB($view_param, $cache_sid);
    $str2find_passon->add($cache_sid, 'slice_id');
    $pagecache->store($keystr, $res, $str2find_passon);
    $str2find_passon->add_str2find($str2find_save); // and append saved for above
    return $res;
}

// Return view result based on parameters, set cache_sid
function GetViewFromDB($view_param, &$cache_sid) {
    global $debug;
    trace("+","GetViewFromDB",$view_param);
    $vid           = $view_param["vid"];
    $als           = $view_param["als"];
    $conds         = $view_param["conds"];
    $slices        = $view_param["slices"];
    $param_conds   = $view_param["param_conds"];
    $param_sort    = array('sort' => $view_param["sort"], 'group_limit' => $view_param["group_limit"]);
    $category_id   = $view_param['cat'];
    // $item_ids   = $view_param["item_ids"];
    $zids          = $view_param["zids"];
    //  $use_short_ids = $view_param["use_short_ids"];
    $list_page     = $view_param["page"];
    if ( $view_param["random"] ) {
        $random    = (($view_param["random"]==1) ? 'random' : 'random:'.$view_param["random"]);
    }

    $selected_item = $view_param["selected"];      // used for boolean (1|0) _#SELECTED
    // alias - =1 for selected item
    // gets view data
    $view      = AA_Views::getView($vid);
    $view_info = $view->getViewInfo($vid);

    // user could make the view to display view ID before and after the view output
    // which is usefull mainly for debugging. See view setting in admin interface
    $comment_begin = $comment_end = '';
    if ( $debug OR ($view_info['flag'] & VIEW_FLAG_COMMENTS) ) {
        $comment_begin = "<!-- $vid -->";
        $comment_end   = "<!-- /$vid -->";
    }

    if (!$view_info OR ($view_info['deleted']>0)) {
        trace("-");
        return false;
    }

    // Use right language (from slice settings) - languages are used for
    // 'No item found', ... messages
    $lang_file = substr( get_if($view_info['lang_file'],DEFAULT_LANG_INCLUDE), 0, 2);
    if (!$GLOBALS['LANGUAGE_NAMES'][$lang_file]) {
        $lang_file = "en";
    }
    bind_mgettext_domain(AA_INC_PATH."lang/".$lang_file."_output_lang.php3");

    $noitem_msg = (isset($view_param["noitem"]) ? $view_param["noitem"] :
                   ( (isset($view_info['noitem_msg']) AND (strlen($view_info['noitem_msg']) > 0) ) ?
                   str_replace( '<!--Vacuum-->', '', $view_info['noitem_msg']) :
                                     ("<div>"._m("No item found") ."</div>")));

    $view->setBannerParam(ParseBannerParam($view_param["banner"]));  // if banner set format

    $listlen    = ($view_param["listlen"] ? $view_param["listlen"] : $view_info['listlen'] );

    /* Old code, had problems because unpack_id128 won't unpack quoted
    $p_slice_id = ($view_param["slice_id"] ? q_pack_id($view_param["slice_id"]) : $view_info['slice_id'] );
    // This is to fix a bug where get_output_cached, caches under wrong slice (mitra)
    $view_info['slice_id'] = $p_slice_id;
    $slice_id = unpack_id128($p_slice_id);
    */

    if ($view_param["slice_id"]) {
        $view_info["slice_id"] = pack_id($view_param["slice_id"]);  // packed,not quoted
        $slice_id = $view_param["slice_id"]; // unpacked
    } else {
        $slice_id = unpack_id128($view_info["slice_id"]);
    }

    // At this point, view_info["slice_id"] = $slice_id
    // and view_param[slice_id] is empty or same

    $cache_sid = $slice_id;     // pass back to GetView (passed by reference)

    // ---- display content in according to view type ----
    if ($debug) {
        huhl("GetViewFromDB:view_info=",$view_info);
    }
    trace("=","GetViewFromDB",$view_info['type']);
    switch( $view_info['type'] ) {
        case 'full':  // parameters: zids, als
            $format = $view->getViewFormat($selected_item);
            if ( isset($zids) && ($zids->count() > 0) ) {
                // get alias list from database and possibly from url
                list($fields,) = GetSliceFields($slice_id);
                $aliases = GetAliasesFromFields($fields, $als);
                //mlx stuff
                $slice = AA_Slices::getSlice($slice_id);
                if (isMLXSlice($slice)) {  //mlx stuff, display the item's translation
                    $mlx = ($view_param["mlx"]?$view_param["mlx"]:$view_param["MLX"]);
                    //make sure the lang info doesnt get reused with different view
                    $GLOBALS['mlxView'] = new MLXView($mlx,unpack_id128($slice->getProperty(MLX_SLICEDB_COLUMN)));
                    $GLOBALS['mlxView']->preQueryZIDs(unpack_id128($slice->getProperty(MLX_SLICEDB_COLUMN)),$conds,$slices);
                    $zids3 = new zids($zids->longids());
                    $GLOBALS['mlxView']->postQueryZIDs($zids3,unpack_id128($slice->getProperty(MLX_SLICEDB_COLUMN)),$slice_id,
                                    $conds, '', $slice->getProperty('group_by'),"ACTIVE", $slices, '', 0,
                                    '',$GLOBALS['nocache'], "vid=$vid t=full i=".serialize($zids3));
                    $zids->a    = $zids3->a;
                    $zids->type = $zids3->type;
                }
                $itemview = new itemview($format, $fields, $aliases, $zids, 0, 1, shtml_url(), "");
                $ret      = $itemview->get_output_cached("view");
            } else {
                $ret      = $noitem_msg;
            }
            trace("-");
            return $comment_begin. $ret . $comment_end;

        case 'discus':
            // create array of discussion parameters
            $disc = array('ids'         => ($view_param["all_ids"] ? "" : $view_param["ids"]),
                          'item_id'     => $view_param["sh_itm"],
                          'vid'         => $vid,
                          'html_format' => ($view_info['flag'] & DISCUS_HTML_FORMAT),
                          'parent_id'   => $view_param["parent_id"],
                          'disc_ids'    => $view_param["disc_ids"]);
            if (($view_param["disc_type"] == "list") || is_array($view_param["disc_ids"])) {
                $disc['type'] = "list";
            } elseif ($view_param["add_disc"]) {
                $disc['type'] = "adddisc";
            } elseif ($view_param["sel_ids"] || $view_param["all_ids"]) {
                $disc['type'] = "fulltext";
            } else {
                $disc['type'] = "thread";
            }
            $aliases = GetDiscussionAliases();

            $format = GetDiscussionFormat($view_info);
            // This is probably a bug, I think it should be
            //  $format['slice_id'] = pack_id128($slice_id); // packed, not quoted
            //  Re: No, it is not bug - format normaly holds data from slice table,
            //      where id of slice is stored in 'id' column (honzam)

            $format['id'] = pack_id128($slice_id);                  // set slice_id because of caching

            // special url parameter disc_url - tell us, where we have to show
            // discussion fulltext (good for discussion search)
            $durl = ( $view_param["disc_url"] ? $view_param["disc_url"] : shtml_url());
            // add state variable, if defined (apc - AA Pointer Cache)
            if ( $GLOBALS['apc_state'] ) {
                $durl = con_url($durl,'apc='.$GLOBALS['apc_state']['state']);
            }

            $itemview = new itemview($format,"",$aliases,null,"","",$durl, $disc);
            $ret      = $itemview->get_output_cached("discussion");
            trace("-");
            return $comment_begin. $ret. $comment_end;

        case 'links':              // links       (module Links)
        case 'categories':         // categories  (module Likns)
        case 'const':              // constants
            if ( !$category_id ) {
                $category_id = Links_SliceID2Category($slice_id);             // get default category for the view
            }
            $format    = $view->getViewFormat($selected_item);
            $aliases   = GetAliases4Type($view_info['type'],$als);
            if (!$conds) {          // conds could be defined via cmd[]=d command
                $conds = GetViewConds($view_info, $param_conds);
            }
            $sort      = GetViewSort($view_info, $param_sort);
            if ( $view_info['type'] == 'const' ) {
                $zids             = QueryConstantZIDs($view_info['parameter'], $conds, $sort);
                $content_function = 'GetConstantContent';
            } elseif ( ($view_info['type'] == 'links') AND $category_id ) {
                $cat_path = Links_GetCategoryColumn( $category_id, 'path');
                if ( $cat_path ) {
                    $zids             = Links_QueryZIDs($cat_path, $conds, $sort, $view_param['show_subcat']);
                    $content_function = 'Links_GetLinkContent';
                }
            } elseif ( ($view_info['type'] == 'categories') AND $category_id ) {
                $zids             = Links_QueryCatZIDs($category_id, $conds, $sort, $view_param['show_subcat']);
                $content_function = 'Links_GetCategoryContent';
            }

            list( $listlen, $list_from ) = GetListLength($listlen, $view_param["to"],
            $view_param["from"], $list_page, $zids->count(), $random);

            $itemview = new itemview( $format, GetConstantFields(), $aliases, $zids, $list_from, $listlen, shtml_url(), "", $content_function);
            $itemview->parameter('category_id', $category_id);
            $itemview->parameter('start_cat',   $view_param['start_cat']);

            if ( !isset($zids) || $zids->count() <= 0) {
                $ret = $itemview->unaliasWithScrollerEasy($noitem_msg);
                return $comment_begin. $ret. $comment_end;
            }

            $ret = $itemview->get_output_cached();
            return $comment_begin. $ret. $comment_end;

        case 'seetoo':

        case 'calendar':
            $today = getdate();
            $month = $view_param['month'];
            if ($month < 1 || $month > 12) {
                $month = $today['mon'];
            }
            $year = $view_param['year'];
            if ($year < 1900 || $year > 3000) {
                $year = $today['year'];
            }
            if ($debug) huhl("GetViewFromDB:year=$year;month=$month");
            $calendar_conds = array (array( 'operator' => '<',
                                            'value' => mktime (0,0,0,$month+1,1,$year),
                                            $view_info['field1'] => 1 ),
                                     array( 'operator' => '>=',
                                            'value' => mktime (0,0,0,$month,1,$year),
                                            $view_info['field2'] => 1 ));
            // Note drops through to next case
            trace("=","","calendar - drop through to digest, script etc");

        case 'digest':
        case 'list':
        case 'rss':
        case 'urls':
        case 'script':  // parameters: conds, param_conds, als
            if ($view_info['type'] == 'rss') {
                header("Content-type: text/xml");
            }

            if (! $conds ) {        // conds could be defined via cmd[]=d command
                $conds = GetViewConds($view_info, $param_conds);
            }
            // merge $conds with $calendar_conds
            if (is_array($calendar_conds)) {
                foreach ( $calendar_conds as $v) {
                    $conds[] = $v;
                }
            }
            trace("=","","in script with slice_id=".$slice_id."; and view_param=".$view_param["slice_id"].";");
            list($fields,) = GetSliceFields($slice_id);
            $aliases       = GetAliasesFromFields($fields, $als);

            if (is_array($slices)) {
                foreach ( $slices as $sid) {
                    list($fields,) = GetSliceFields($sid);
                    $aliases[q_pack_id($sid)] = GetAliasesFromFields($fields,$als);
                }
            }

            $sort  = GetViewSort($view_info, $param_sort);

            //mlx stuff
            if (!$slice) {
                $slice = AA_Slices::getSlice($slice_id);
            }
            if (isMLXSlice($slice)) {
                $mlx = ($view_param["mlx"]?$view_param["mlx"]:$view_param["MLX"]);
                //make sure the lang info doesnt get reused with different view
                $GLOBALS['mlxView'] = new MLXView($mlx,unpack_id128($slice->getProperty(MLX_SLICEDB_COLUMN)));
                $GLOBALS['mlxView']->preQueryZIDs(unpack_id128($slice->getProperty(MLX_SLICEDB_COLUMN)),$conds,$slices);
            }
            $zids2 = QueryZIDs($zids ? false : (is_array($slices) ? $slices : array($slice_id)), $conds, $sort, "ACTIVE", 0, $zids);

            if (isMLXSlice($slice)) {
                $GLOBALS['mlxView']->postQueryZIDs($zids2,unpack_id128($slice->getProperty(MLX_SLICEDB_COLUMN)),$slice_id,
                                                   $conds, $sort, $slice->getProperty('group_by'),"ACTIVE", $slices, '', 0,
                                                   '',$GLOBALS['nocache'],"vid=$vid t=list");
            }
            //end mlx stuff
            // Note this zids2 is always packed ids, so lost tag information
            if ($debug) huhl("GetViewFromDB retrieved ".(isset($zids2) ? $zids2->count : 0)." IDS");
            if (isset($zids) && isset($zids2) && ($zids->onetype() == "t")) {
                $zids2 = $zids2->retag($zids);
                if ($debug) huhl("Retagged zids=",$zids2);
            }

            if ($debug) {
                huhl("GetViewFromDB: Filtered ids=",$zids2);
            }

            $format = $view->getViewFormat($selected_item);
            $format['calendar_month'] = $month;
            $format['calendar_year']  = $year;

            list($listlen, $list_from) = GetListLength($listlen, $view_param["to"], $view_param["from"], $list_page, $zids2->count(), $random);

            $itemview = new itemview( $format, $fields, $aliases, $zids2, $list_from, $listlen, shtml_url(), "",
                                      ($view_info['type'] == 'urls') ? 'GetItemContentMinimal' : '');

            if (isset($zids2) && ($zids2->count() > 0)) {
                $itemview_type = (($view_info['type'] == 'calendar') ? 'calendar' : 'view');
                $ret = $itemview->get_output_cached($itemview_type);
            }   //zids2->count >0
            else {
                /* Not sure if this was a necessary change that got missed, or got changed again
                // $ret = $noitem_msg;
                $level = 0; $maxlevel = 0;
                // This next line is not 100% clear, might not catch aliases
                //since there are two formats for aliases structures. (mitra)
                //    huhl("XYZZY:v578, msg=",$noitem_msg);
                $ret = new_unalias_recurent($noitem_msg,"",$level,$maxlevel,null,null,$aliases);
                */
                $ret = $itemview->unaliasWithScrollerEasy($noitem_msg);
            }
            // 	if ( ($scr->pageCount() > 1) AND !$no_scr)  $scr->pnavbar();
            trace("-");
            return $comment_begin. $ret. $comment_end;

        case 'static':
            // $format = $view->getViewFormat();  // not needed now
            // I create a CurItem object so I can use the unalias function
            $CurItem      = new AA_Item("", $als);
            $formatstring = $view_info["odd"];          // it is better to copy format-
            $ret = $CurItem->unalias( $formatstring );  // string to variable - unalias
            trace("-");
            return $comment_begin. $ret. $comment_end;
    }                                             // uses call by reference
}
?>
