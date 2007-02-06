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

//expected  slice_id
//expected  encap     // determines wheather this file is ssi included or called directly
//optionaly sh_itm    // if specified - selected item is shown in full text
//optionaly x         // the same as sh_itm, but short_id is used instead
                     // implemented for shorter item url (see _#SITEM_ID alias)
//optionaly o         // the same as x but the hit is not counted
//optionaly srch      // true if this script have to show search results
//optionaly highlight // when true, shows only highlighted items in compact view
//optionaly bigsrch   // NOT SUPPORTED IN AA v 1.5+
//optionaly cat_id    // select only items in category with id cat_id
//optionaly cat_name  // select only items in category with name cat_name
//optionaly inc       // for dispalying another file instead of slice data
                     // (like static html file - inc=/contact.html)
//optionaly listlen   // change number of listed items in compact view
                     // (aplicable in compact viewe only)
//optionaly items[x]  // array of items to show one after one as fulltext
                     // the array format is
//easy_query          // for easiest form of query
//order               // order field id - if other than publish date
                     // add minus sign for descending order (like "headline.......1-");
//timeorder           // rev - reverse publish date order
                     // (less priority than "order")
//no_scr              // if true, no scroller is displayed
//all_scr             // if true, scroller shows also 'All no scroller is displayed
//scr_go              // sets scroller to specified page
//scr_url             // redefines the page where the scroller should go (it is
                     // usefull if you include slice.php3 from another php script)
//restrict            // field id used with "res_val" and "exact" for restricted
                     // output (display only items with
                     //       "restrict" field = "res_val"
//res_val             // see restrict
//exact               // = 1: "restrict" field must match res_val exactly (=)
                     // undefined: substring is sufficient (LIKE '%res_val%')
                     // = 2: the same as 1, but the res_val is taken as
                     // expression (like: "environment or (toxic and not fuel)")
//als[]               // user alias definition. Parameter 'als[MY_ALIAS]=Summary'
                     // defines alias _#MY_ALIAS. If used, it prints 'Summary'.
//lock                // used in join with "key" for multiple slices on one page
                     // display. each slice have to have its lock, so commands
                     // (like sh_itm, scr_go, ...) will be executed only if key
                     // is the same as lock. key is send automaticaly with all
                     // links generated in slice (at this time just prepared)
//key                 // see lock (at this time just prepared)
//slicetext           // displays just the text instead of any output
                     // can be used for hiding the output of slice.hp3

                      //Discussion parameters
//optionaly add_disc   // if set, discussion comment will be added
//optionaly parent_id  // parent id of added disc. comment
//optionally sel_ids    // if set, show only discussion comments in $ids[] array
//optionally ids[]      // array of discussion comments to show in fulltext mode (ids['x'.$id])
//optionally all_ids    // if set, show all discussion comments
//optionally hideFulltext // if set, don't show fulltext part
//optionally neverAllItems // if set, don't show anything when everything would be shown (if no conds[] are set)
//optionally defaultCondsOperator // replaces LIKE for conds with not specified operator
//optionally group_n   // displayes only the n-th group (in listings where items
                      // are grouped by some field (category, for example))
                      // good for display all the items of last magazine issue
                      // ( group_n=1 )
              //
//optionally mlx       // set language extension options (needs MLX field filled in Slice Settings)
                      // mlx=EN sets English as default language
              // mlx=EN-FR-DE first looks for EN, then FR, then DE
              //              and then displays the first one found
              // mlx=EN-ONLY  displays only EN items (like conds)
              // mlx=ALL      turns of language extension


function add2sort(&$sort, $sarr) {
    if (isset($sort) AND is_array($sort)) {
        foreach ($sort as $s) {
            if (key($s) == key($sarr)) {
                return;  // do not add new sort value to the sort array (it is already presented)
            }
        }
    }
    $sort[] = $sarr;
    return;
}

// handle with PHP magic quotes - quote the variables if quoting is set off
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

$encap = ( ($encap=="false") ? false : true );

require_once "./include/config.php3";
require_once AA_INC_PATH."easy_scroller.php3";
require_once AA_INC_PATH."util.php3";
require_once AA_INC_PATH."item.php3";
require_once AA_INC_PATH."view.php3";
require_once AA_INC_PATH."pagecache.php3";
require_once AA_INC_PATH."searchlib.php3";
require_once AA_INC_PATH."discussion.php3";
require_once AA_INC_PATH."mgettext.php3";
require_once AA_INC_PATH."sliceobj.php3";
// function definitions:
require_once AA_INC_PATH."slice.php3";

require_once AA_INC_PATH. ($encap ? "locsessi.php3" : "locsess.php3");
$slice_starttime = get_microtime();

//MLX stuff
require_once AA_INC_PATH."mlx.php";

page_open(array("sess" => "AA_SL_Session"));

$sess->register(r_packed_state_vars);
$sess->register(slices);

$r_state_vars = unserialize($r_packed_state_vars);

// there was problems with storing too much ids in session veriable,
// so I commented it out. It is not necessary to have it in session. The only
// reason to have it there is the display speed, but because of impementing
// pagecache.php3, it is not so big problem now

//$sess->register(item_ids);

if ($encap) {                    // adds values from QUERY_STRING_UNESCAPED
    add_vars("");                //       and REDIRECT_STRING_UNESCAPED
}

if (($key != $lock) OR $scrl) {  // command is for other slice on page
    RestoreVariables();          // or scroller
}

// url posted command to display specified text instead of slice content -------
if ($slicetext) {
    echo $slicetext;
    ExitPage();
}

// url posted command to display another file ----------------------------------
if ( $inc ) {                   // this section must be after add_vars()
    //  StoreVariables(array("inc")); // store in session
    if ( !eregi("^([0-9a-z_])+(\.[0-9a-z]*)?$", $inc) ) {
        echo _m("Bad inc parameter - included file must be in the same directory as this .shtml file and must contain only alphanumeric characters"). " $inc";
        ExitPage();
    } else {
        $fp = @fopen(shtml_base().$inc, "r");    //   if encapsulated
        if (!$fp) {
            echo _m("No such file") ." $inc";
        } else {
            FPassThru($fp);
        }
        ExitPage();
    }
}

// Take any slice to work with
if (!$slice_id AND is_array($slices)) {
    reset ($slices);
    $slice_id = current($slices);
}

// if someone breaks <!--#include virtual=""... into two rows, then slice_id
// (or other variables) could contain \n. Should be fixed more generaly -
// We will do it during rewrite AA to $_GET[] variable handling (probably)
$slice_id   = trim($slice_id);
$p_slice_id = q_pack_id($slice_id);

require_once AA_INC_PATH."javascript.php3";

$db  = new DB_AA; 	 // open BD
$db2 = new DB_AA; 	 // open BD	(for subqueries in order to fullfill fulltext in feeded items)

$slice      = AA_Slices::getSlice($slice_id);
$fields     = $slice->fields('record');            // get fields info
$slice_info = GetSliceInfo($slice_id);       // get slice info
if (!$slice_info OR $slice_info['deleted']>0) {
    echo _m("Invalid slice number or slice was deleted") . " (ID: $slice_id)";
    ExitPage();
}

// Use right language (from slice settings) - languages are used for scroller (Next, ...)
$lang_file = substr($slice_info['lang_file'], 0, 2);
if (!$LANGUAGE_NAMES[$lang_file]) {
    $lang_file = "en";
}
bind_mgettext_domain(AA_INC_PATH."lang/".$lang_file."_output_lang.php3");

if (!$slice_info['even_odd_differ']) {
    $slice_info['even_row_format'] = "";
}

// it is possible to redefine the design of fulltext or compact view by the view
// see fview and iview url parameters for this file (slice.php3)
if ($fview || $iview) {
    if ($fview) {                       // use formating from view for fulltext
        $fview_info = AA_Views::getView($fview);
        if ($fview_info AND ($fview_info->f('deleted')<1)) {
            $slice_info['fulltext_format']        = $fview_info->f('odd');
            $slice_info['fulltext_format_top']    = $fview_info->f('before');
            $slice_info['fulltext_format_bottom'] = $fview_info->f('after');
            $slice_info['fulltext_remove']        = $fview_info->f('remove_string');
        }
    }
    if ($iview) {                       // use formating from view for index
        $iview_info = AA_Views::getView($iview);
        if ($iview_info AND ($iview_info->f('deleted')<1)) {
            $slice_info['group_by']        = $iview_info->f('group_by1');
            $slice_info['gb_direction']    = $VIEW_SORT_DIRECTIONS[$iview_info->f('g1_direction')];  // views uses different sort codes (historical reasons)
            $slice_info['category_format'] = $iview_info->f('group_title');
            $slice_info['category_bottom'] = $iview_info->f('group_bottom');
            $slice_info['compact_top']     = $iview_info->f('before');
            $slice_info['compact_bottom']  = $iview_info->f('after');
            $slice_info['compact_remove']  = $iview_info->f('remove_string');
            $slice_info['even_row_format'] = $iview_info->f('even');
            $slice_info['odd_row_format']  = $iview_info->f('odd');
            $slice_info['even_odd_differ'] = $iview_info->f('even_odd_differ');
        }
    }
}
define("DEFAULT_CODEPAGE","windows-1250");

if (!$encap) {
    Page_HTML_Begin($slice_info['name']);
}

if ($bigsrch) {  // big search form ------------------------------------------
    echo '<!-- bigsrch parameter is NOT SUPPORTED IN AA v 1.5+ <br> See
          <a href="http://apc-aa.sourceforge.net/faq/index.shtml#215">AA FAQ</a>
          for more details. -->';
    ExitPage();
}

$add_aliases = $aliases    = GetAliasesFromUrl($als);

// this is not good way - aliases are then different on each call, so it isn't
// cached. The better way is below
// $add_aliases['_#SESSION_'] = GetAliasDef( 'f_s:'. $sess->id, '', _m('session id'));

$add_aliases['_#SESSION_'] = GetAliasDef( 'f_e:session', 'id..............', _m('session id'));

// if banner parameter supplied => set format
$slice_info = array_merge( $slice_info, ParseBannerParam($banner));

// get alias list from database and possibly from url
// if working with multi-slice, get aliases for all slices
if (!is_array($slices)) {
    $aliases = GetAliasesFromFields($fields);
    array_add($add_aliases, $aliases);
} else {
    foreach ($slices as $sid) {
        list($fields) = GetSliceFields($sid);
        // hack for searching in multiple slices. This is not so nice part
        // of code - we mix there $aliases[<alias>] with $aliases[<p_slice_id>][<alias>]
        // it is needed by itemview::set_column() (see include/itemview.php3)
        $aliases[q_pack_id($sid)] = GetAliasesFromFields($fields,$als);
        array_add($add_aliases, $aliases[q_pack_id($sid)]);
    }
}

// fulltext view ---------------------------------------------------------------
if ( $sh_itm OR $x OR $o ) {
    //  $r_state_vars = StoreVariables(array("sh_itm")); // store in session
    if ( !$x AND $o ) {
        $x = $o;
        $count_hit = false;
    } else {
        $count_hit = true;
    }

    if ($sh_itm) {
        LogItem($sh_itm, "id", $count_hit);
    } else {
        $sh_itm = LogItem($x,"short_id", $count_hit);
    }

    if (!isset ($hideFulltext)) {
        $itemview = new itemview($slice_info, $fields, $aliases, new zids($sh_itm,"l"),
                                 0,1, $sess->MyUrl($slice_id, $encap));
        $itemview->print_item();
    }

    // show discussion if assigned
    $discussion_vid = ( isset($dview) ? $dview : $slice_info['vid']);
    // you can set dview=0 to not show discussion
    if ($discussion_vid > 0) {
        $db->query("SELECT view.*, slice.flag FROM view, slice
                     WHERE slice.id='".q_pack_id($slice_id)."' AND view.id=$discussion_vid");
        if ($db->next_record()) {
            $view_info = $db->Record;
            // create array of parameters
            $disc = array('ids'         => $all_ids ? "" : $ids,
                          'type'        => $add_disc ? "adddisc" : (($sel_ids || $all_ids) ? "fulltext" : "thread"),
                          'item_id'     => $sh_itm,
                          'vid'         => $view_info['id'],
                          'html_format' => $view_info['flag'] & DISCUS_HTML_FORMAT,
                          'parent_id'   => $parent_id
                         );
            $aliases = GetDiscussionAliases();

            $format  = GetDiscussionFormat($view_info);
            $format['id'] = $p_slice_id;                  // set slice_id because of caching

            $itemview = new itemview($format, "", $aliases, null,"", "", $sess->MyUrl($slice_id, $encap), $disc);
            $itemview->print_discussion('nocache');  // discussions should not be
            // cached or even better (TODO) discussions should have its separate slice
            // which is cached independently form the item itself through standard
            // AA caching

        }
    }
    ExitPage();
}

// multiple items fulltext view ------------------------------------------------
if ( $items AND is_array($items) ) {   // shows all $items[] as fulltext one after one
    //  $r_state_vars = StoreVariables(array("items")); // store in session
    while (list($k) = each( $items )) {
        $ids[] = substr($k,1);    //delete starting character ('x') - used for interpretation of index as string, not number (by PHP)
    }
    $zids     = new zids($ids,"l");
    $itemview = new itemview($slice_info, $fields, $aliases, $zids, 0,$zids->count(), $sess->MyUrl($slice_id, $encap));
    $itemview->print_itemlist();
    ExitPage();
}

// compact view ----------------------------------------------------------------
if (!is_object($scr)) {
    $sess->register('scr');
    $scr_url_param = ($scr_url ? $sess->url("$scr_url") : $sess->MyUrl($slice_id, $encap))."&amp;";
    $scr = new easy_scroller( 'scr', $scr_url_param, $slice_info['d_listlen'], 0);
}
// display 'All' option in scroller
if ($all_scr) { $scr->setShowAll($all_scr); }

// change number of listed items
if ($listlen) { $scr->setMetapage($listlen); }

// default start page = 1
if (!$scr_go) { $scr_go = 1; }

// $scrl comes from easy_scroller
if ($scrl)    { $scr->update(); }

/** Add scroller aliases - page number, listlen */
$scr_aliases['_#PAGE_NO_'] = GetAliasDef( 'f_s:'. $scr->current,  '', _m('number of current page (on pagescroller)'));
$scr_aliases['_#PAGE_LEN'] = GetAliasDef( 'f_s:'. $scr->metapage, '', _m('page length (number of items)'));
// aliases array have two form (quite stupid - will be changed in future - TODO)
// depending on listing for one slice or many slices
if (!is_array($slices)) {
    array_add($scr_aliases, $aliases);
} else {
    foreach ($slices as $sid) {
        // hack for searching in multiple slices. This is not so nice part
        // of code - we mix there $aliases[<alias>] with $aliases[<p_slice_id>][<alias>]
        // it is needed by itemview::set_column() (see include/itemview.php3)
        array_add($scr_aliases, $aliases[q_pack_id($sid)]);
    }
}



/* old version of automatiocaly created search form - not used in AA > 1.2
   $easy_query .. easy query form
   $srch .. bigsrch form ??
*/

if (($easy_query || $srch) AND !(is_array($conds) OR isset($group_by) OR isset($sort))) {

    if ($easy_query) {     // posted by easy query form ----------------
        $r_state_vars = StoreVariables(array("listlen","no_scr","scr_go","srch_fld","srch_from", "srch_to",
                                             "easy_query", "qry", "srch_relev", "mlx")); // store in session, added mlx
        $item_ids     = GetIDs_EasyQuery($fields, $db, $p_slice_id, $srch_fld, $srch_from, $srch_to, $qry, $srch_relev);
        if ( isset($item_ids) AND !is_array($item_ids) ) {
            echo "<div>$item_ids</div>";
        }
        if ( !$scrl ) {
            $scr->current = $scr_go;
        }
    }
    elseif ($srch) {     // posted by bigsrch form -------------------
        $r_state_vars = StoreVariables(array("listlen","no_scr","scr_go","big","search", "s_col", "mlx")); // store in session
        if ( !$big ) {
            $search['slice'] = $slice_id;
        }
        $item_ids = SearchWhere($search, $s_col);
        if ( !$scrl ) {
            $scr->current = $scr_go;
        }
    }
    elseif ($debug) {
        echo "ERROR: This branch should never be entered.";
    }
} else {
    /** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *         Parse parameters posted by query form and from $slice_info
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

    $r_state_vars = StoreVariables(array("no_scr","scr_go","order","cat_id", "cat_name",
     "exact","restrict","res_val","highlight","conds","group_by", "sort","als","defaultCondsOperator","mlx")); // store in session, added mlx

    // ***** CONDS *****

    if ($cat_id) {  // optional parameter cat_id - deprecated - slow ------
        $cat_field = $slice->getFields()->getCategoryFieldId();
        $cat_group = GetCategoryGroup($slice_id);

        $SQL = "SELECT value FROM constant
                 WHERE group_id = '$cat_group' AND id='". q_pack_id($cat_id) ."'";
        $db->query($SQL);
        if ( $db->next_record() ) {
            $conds[] = array( $cat_field => 1,
                              'value'    => $db->f('value'),
                              'operator' => ($exact ? '=' : 'LIKE'));
        }
    } elseif ($cat_name)  {  // optional parameter cat_name -------
        $cat_field = $slice->getFields()->getCategoryFieldId();
        $conds[]     = array( $cat_field => 1,
                              'value'    => $cat_name,
                              'operator' => ($exact ? '=' : 'LIKE'));
    }

    if ($restrict) {
        $conds[]     = array( $restrict  => 1,
                              'value'    => ((($res_val[0] == '"' OR $res_val[0] == "'") AND $exact != 2 ) ? $res_val : "\"$res_val\""),
                              'operator' => ($exact ? '=' : 'LIKE'));
    }

    if ($highlight != "") {
        $conds[] = array('highlight.......' => 1);
    }

    if (!isset($defaultCondsOperator)) {
        $defaultCondsOperator = 'LIKE';
    }
    if (is_array($conds)) {
        ParseEasyConds($conds, $defaultCondsOperator);
        foreach ( $conds as $k => $v ) {
            SubstituteAliases( $als, $conds[$k]['value'] );
        }
    }

    // ***** SORT *****

    /** order by field xy if other than publish date.
    *  Syntax: [number]field_id[-]
    *  (add minus sign for descending order (like "headline.......1-")
    *  (add number before the field if you want to group limit (limit number of items of the same value))
    */
    if ($order) {
        $sortorder = new AA_Sortorder;
        $sortorder->addSortFromString($order);
        $order = reset($sortorder->getOrder());  // get the first from array
        list($order, $orderdirection) = each($order);
    }

    if ($debug) {
        echo "<BR>Group by: -$group_by- <br>Slice_info[category_sort] -$slice_info[category_sort]-<br>slice_info[group_by] -$slice_info[group_by]-";
    }

    $sort_tmp = array();
    if ($group_by) {
        $sortorder = new AA_Sortorder;
        $sortorder->addSortFromString($group_by);
        $sort_tmp = $sortorder->getOrder();
        $slice_info["group_by"] = key($sort_tmp[0]);
    }
    elseif ($slice_info['category_sort']) {
        $group_field = $slice->getFields()->getCategoryFieldId();
        $grp_odir    = (($order==$group_field) AND ($orderdirection!='d')) ? 'a' : 'd';
        $sort_tmp[]  = array( $group_field => $grp_odir );
    }
    elseif ($slice_info['group_by']) {
        switch( (string)$slice_info['gb_direction'] ) {  // gb_direction is number
            case '1': $gbd = '1'; break;      // 1 (1)- ascending by priority
            case 'd':                         //    d - descending - goes from view (iview) settings
            case '8': $gbd = 'd'; break;      // d (8)- descending
            case '9': $gbd = '9'; break;      // 9 (9)- descending by priority (for fields using constants)
            default:  $gbd = 'a';             // 2 (2)- ascending;
        }
        $sort_tmp[] = array($slice_info['group_by'] => $gbd);
    }

    $sort_tmp = array_merge($sort_tmp, getSortFromUrl($sort));

    if ($order) {
        add2sort($sort_tmp, array($order => (strstr('aAdD19',$orderdirection) ? $orderdirection : 'a')));
    }

    // time order the fields in compact view
    add2sort($sort_tmp, array('publish_date....' => (($timeorder == "rev") ? 'a' : 'd')));
    $sort  = $sort_tmp;

    //mlx stuff
    if (isMLXSlice($slice_info)) {
        if (!$mlxView) {
            $mlxView = new MLXView($mlx);
        }
        $mlxView->preQueryZIDs(unpack_id128($slice_info[MLX_SLICEDB_COLUMN]),$conds,$slices);
    }

    $zids = QueryZIDs( ($slices ? $slices : array($slice_id)), $conds, $sort, "ACTIVE", $neverAllItems, 0, $defaultCondsOperator, true );

    if (isMLXSlice($slice_info)) {
        $mlxView->postQueryZIDs($zids,unpack_id128($slice_info[MLX_SLICEDB_COLUMN]),$slice_id, $conds, $sort, $slice_info['group_by'],"ACTIVE", $slices, $neverAllItems, 0, $defaultCondsOperator,$nocache);
    }

    if ( !$scrl ) {
        $scr->current = $scr_go;
    }
}

if ( !$srch AND !$encap AND !$easy_query ) {
    $cur_cats=GetCategories($db,$p_slice_id);     // get list of categories
    pCatSelector($sess->name,$sess->id,$sess->MyUrl($slice_id, $encap, true),$cur_cats,$scr->filters[category_id]['value'], $slice_id, $encap);
}


if ($zids->count() > 0) {
    $scr->countPages( $zids->count() );

    $itemview = new itemview($slice_info, $fields, $aliases, $zids, $scr->metapage * ($scr->current - 1),
                             ($group_n ? -$group_n : $scr->metapage),  // negative number used for displaying n-th group
                             $sess->MyUrl($slice_id, $encap) );
    $itemview->print_view();

    if (($scr->pageCount() > 1) AND !$no_scr AND !$group_n) {
        $scr->pnavbar();
    }
} else {
    // test if the the noitem_msg is filled (be carefull - "0" should be considered as filled)
    echo (isset($slice_info['noitem_msg']) AND (strlen($slice_info['noitem_msg']) > 0)) ?               // <!--Vacuum--> is keyword for removing 'no item message'
          str_replace( '<!--Vacuum-->', '', $slice_info['noitem_msg']) : ("<div>"._m("No item found") ."</div>");
}

if ($searchlog) {
    PutSearchLog();
}

if ($debug) {
    $timeend = get_microtime();
    $time    = $timeend - $slice_starttime;
    echo "<br><br>Page generation time: $time";
}

ExitPage();
?>
