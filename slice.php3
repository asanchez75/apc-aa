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

#expected  slice_id 
#expected  encap     // determines wheather this file is ssi included or called directly  
#optionaly sh_itm    // if specified - selected item is shown in full text
#optionaly srch      // true if this script have to show search results
#optionaly highlight // when true, shows only highlighted items in compact view
#optionaly bigsrch   // true, if this script have to show big search form
#optionaly cat_id    // select only items in category with id cat_id
#optionaly cat_name  // select only items in category with name cat_name
#optionaly inc       // for dispalying another file instead of slice data 
                     // (like static html file - inc=/contact.html)
#optionaly listlen   // change number of listed items in compact view
                     // (aplicable in compact viewe only) 
#optionaly items[x]  // array of items to show one after one as fulltext 
                     // the array format is 
#easy_query          // for easiest form of query
#order               // order field id - if other than publish date
                     // add minus sign for descending order (like "headline.......1-"); 
#no_scr              // if true, no scroller is displayed                     
#scr_go              // sets scroller to specified page
#restrict            // field id used with "res_val" and "exact" for restricted
                     // output (display only items with 
                     //       "restrict" field = "res_val"
#res_val             // see restrict
#exact               // if set, "restrict" field must match res_val exactly (=)
                     // otherwise substring is sufficient (LIKE '%res_val%')
#lock                // used in join with "key" for multiple slices on one page
                     // display. each slice have to have its lock, so commands 
                     // (like sh_itm, scr_go, ...) will be executed only if key
                     // is the same as lock. key is send automaticaly with all 
                     // links generated in slice (at this time just prepared)
#key                 // see lock (at this time just prepared)

$encap = ( ($encap=="false") ? false : true );

require "./include/config.php3";
require $GLOBALS[AA_INC_PATH]."easy_scroller.php3";
require $GLOBALS[AA_INC_PATH]."util.php3";
require $GLOBALS[AA_INC_PATH]."item.php3";
require $GLOBALS[AA_INC_PATH]."view.php3";
require $GLOBALS[AA_INC_PATH]."pagecache.php3";
require $GLOBALS[AA_INC_PATH]."searchlib.php3";

# $debugtimes[]=microtime();

if ($encap){require $GLOBALS[AA_INC_PATH]."locsessi.php3";}
else {require $GLOBALS[AA_INC_PATH]."locsess.php3";} 
page_open(array("sess" => "AA_SL_Session"));
$sess->register(r_packed_state_vars); 

$r_state_vars = unserialize($r_packed_state_vars);

# there was problems with storing too much ids in session veriable, 
# so I commented it out. It is not necessary to have it in session. The only
# reason to have it there is the display speed, but because of impementing
# pagecache.php3, it is not so big problem now

//$sess->register(item_ids);    


//-----------------------------Functions definition--------------------------------------------------- 

function Page_HTML_Begin($cp, $title="") {  ?>
  <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
  <HTML>
  <HEAD>
	  <TITLE><?php echo $title ?></TITLE>
    <LINK rel=StyleSheet href="<?php echo ADM_SLICE_CSS ?>" type="text/css" title="SliceCS">
  <?php  
  if ($cp) 
    echo '<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset='. $cp. '">';
  ?>
  </HEAD>
  <BODY>
<?php        
}

# print closing HTML tags for page
function Page_HTML_End(){ ?>
  </BODY>
  </HTML><?php
}

function GetCategories($db,$p_slice_id){
 $SQL= " SELECT name, value FROM constant WHERE group_id='".$p_slice_id."'";
 $db->query($SQL);
 while ($db->next_record()){
   $unpacked=unpack_id($db->f("value"));  
   $arr[$unpacked]=$db->f("name");  
 }
 return $arr;  
} 
 
function pCatSelector($sess_name,$sess_id,$url,$cats,$selected,$sli_id=0,$encaps=true){
 if (sizeof($cats)>0)
 {   
   echo "<form action=$url method=get>";
   echo "<input type=hidden name=$sess_name value=$sess_id>";
   if( !$encaps )    // not encapsulated - need to send slice_id
   { echo "<input type=hidden name=slice_id value=$sli_id>";
     echo "<input type=hidden name=encap value=".($encaps ? "true":"false").">";
   }
   echo L_SELECT_CATEGORY . "<select name=cat_id>";
   $seloption=(($selected=="")?"selected":"");
   echo '<option value="all" $seloption>'.L_ALLCTGS.'</option>';
   while (list($id,$name)= each($cats)) {
     $seloption=(($selected==$id)?"selected":"");
     echo "<option value=$id $seloption>".htmlspecialchars($name)."</option>";  
   }
   echo "<input type=hidden name=scr_".$scr_name."_Go value=1>";
   echo "<input type=submit name=Go value=Go>";
   echo "</select>"; 
   echo "</form>";  
 }
}    

function ExitPage() {
  global $encap, $r_packed_state_vars, $r_state_vars;
  if (!$encap)
    Page_HTML_End();
  $r_packed_state_vars = serialize($r_state_vars);
  page_close();
  exit;
}  

function StoreVariables( $vars ) {
  global $r_state_vars;

  unset($r_state_vars);
  if( isset($vars) AND is_array($vars) ) {
    reset($vars);
    while( list(,$v) = each( $vars ) )
      $r_state_vars[$v] = $GLOBALS[$v];
  }
}  

function RestoreVariables() {
  global $r_state_vars;
  if( isset($r_state_vars) AND is_array($r_state_vars) ) {
    reset($r_state_vars);
    while( list($k,$v) = each( $r_state_vars ) )
      $GLOBALS[$k] = $v;
  }
}  

//-----------------------------End of functions definition---------------------
# $debugtimes[]=microtime();

if ($encap) $sess->add_vars(); # adds values from QUERY_STRING_UNESCAPED 
                               #       and REDIRECT_STRING_UNESCAPED

// p_arr_m( $r_state_vars );

if( ($key != $lock) OR $scrl ) # command is for other slice on page
  RestoreVariables();          # or scroller

# url posted command to display another file ----------------------------------
if( $inc ) {                   # this section must be after $sess->add_vars()
//  StoreVariables(array("inc")); # store in session
  if( !eregi("^([0-9a-z_])+(\.[0-9a-z]*)?$", $inc) ) {
    echo L_BAD_INC. " $inc";
    ExitPage();
  } else {  
    $fp = @fopen( shtml_base().$inc, "r");    #   if encapsulated
    if( !$fp )
      echo L_NO_SUCH_FILE ." $inc";
     else
      FPassThru($fp); 
    ExitPage();
  }  
}  

$p_slice_id= q_pack_id($slice_id);
$db = new DB_AA; 		 // open BD	
$db2 = new DB_AA; 	 // open BD	(for subqueries in order to fullfill fulltext in feeded items)

  # get fields info
list($fields,) = GetSliceFields($slice_id);

  # get slice info
$slice_info = GetSliceInfo($slice_id);
if ($slice_info AND ($slice_info[deleted]<1)) {
  include $GLOBALS[AA_INC_PATH] . $slice_info[lang_file];  // language constants (used in searchform...)
}
else {
  echo L_SLICE_INACCESSIBLE . " (ID: $slice_id)";
  ExitPage();
}  


if( !$slice_info[even_odd_differ] )
  $slice_info[even_row_format] = "";

if (!$encap)
  Page_HTML_Begin("iso-8859-1", $slice_info[name] );  // TODO codepage


# big search form -------------------------------------------------------------

if( $bigsrch ) {
  StoreVariables(array("bigsrch")); # store in session
  $show = Array("slice"=>true, "category"=>true, "author"=>true, "lang"=>true, "headline"=>true,
                "full_text"=>true, "abstract"=>true, "from"=>true, "to"=>true, "edit_note"=>true);
  require $GLOBALS[AA_INC_PATH]."big_srch.php3";
  ExitPage();
}

# fulltext view ---------------------------------------------------------------

if( $sh_itm ) {
//  StoreVariables(array("sh_itm")); # store in session
  $aliases = GetAliasesFromFields($fields);
  $itemview = new itemview( $db, $slice_info, $fields, $aliases, array(0=>$sh_itm), 0,1, $sess->MyUrl($slice_id, $encap));
  $itemview->print_item();
  ExitPage();
}

# multiple items fulltext view ------------------------------------------------
if( $items  AND is_array($items) ) {   # shows all $items[] as fulltext one after one
//  StoreVariables(array("items")); # store in session
  while(list($k,) = each( $items ))
    $ids[] = substr($k,1);    #delete starting character ('x') - used for interpretation of index as string, not number (by PHP)
  $aliases = GetAliasesFromFields($fields);
  $itemview = new itemview( $db, $slice_info, $fields, $aliases, $ids, 0,count($ids), $sess->MyUrl($slice_id, $encap));
  $itemview->print_itemlist();
  ExitPage();
}

# compact view ----------------------------------------------------------------
if(!is_object($scr)) {
  $sess->register(scr); 
  $scr = new easy_scroller("scr",$sess->MyUrl($slice_id, $encap)."&", $slice_info[d_listlen]);	
}
if( $listlen )    // change number of listed items
  $scr->metapage = $listlen;

if( $scr_go )     // optional script parameter
  $scr->current = $scr_go;
  
if( $scrl ) {      // comes from easy_scroller -----------
  if (is_object($scr)) 
    $scr->update();
}    
  
if($query) {              # complex query - posted by big search form ---
  StoreVariables(array("listlen","no_scr","scr_go","query")); # store in session
  $item_ids = ExtSearch ($query, $p_slice_id, 0);
  if( isset($item_ids) AND !is_array($item_ids))
    echo "<div>$item_ids</div>";   # it must be error message
  $scr->current = 1;
}
elseif($easy_query) {     # posted by easy query form ----------------
  StoreVariables(array("listlen","no_scr","scr_go","srch_fld","srch_from", "srch_to",
                      "easy_query", "srch_relev")); # store in session
  $item_ids = GetIDs_EasyQuery($fields, $db, $p_slice_id, $srch_fld, 
                               $srch_from, $srch_to, $easy_query, $srch_relev);
  if( isset($item_ids) AND !is_array($item_ids))
    echo "<div>$item_ids</div>";
  $scr->current = 1;
}
elseif($srch) {            # posted by bigsrch form -------------------
  StoreVariables(array("listlen","no_scr","scr_go","big","search", "s_col")); # store in session
  if( !$big )
    $search[slice] = $slice_id;
  $item_ids = SearchWhere($search, $s_col);
  $scr->current = 1;
}
else {
  StoreVariables(array("listlen","no_scr","scr_go","order","cat_id", "cat_name",
                      "exact","restrict","res_val","highlight")); # store in session
  if( $cat_id ) {  // optional parameter cat_id - deprecated - slow ------
    $cat_group = GetCategoryGroup($slice_id);
    $SQL = "SELECT value FROM constant
             WHERE group_id = '$cat_group'
               AND id = '". q_pack_id($cat_id) ."'";
    $db->query($SQL);
    if( $db->next_record() ) {
      $res_field = GetCategoryFieldId( $fields );
      $res_value = $db->f(value);
      $exact = true;
    }  
  }  
  elseif ( $cat_name ) {  // optional parameter cat_name -------
    $res_field = GetCategoryFieldId( $fields );
    $res_value = $cat_name;
  }
  elseif ( $restrict ) { 
    $res_field = $restrict;
    $res_value = $res_val;
  } else {             # no parameters - initial settings ---
    $res_field = "";
    $res_value = "";
  }  

  # order the fields in compact view - how?
  if( $order ) {
    if( substr($order,-1) == '-' ) {
      $orderdirection = "DESC";
      $order = substr($order,0,-1);
    }
  }    

  if( $res_field != "" )
    $conditions[$res_field] = $res_value;
  if( $highlight != "" )
    $conditions['highlight.......'] = 1;

  $item_ids = GetItemAppIds($fields, $db, $slice_id, $conditions, "DESC",
    ($slice_info[category_sort] ? GetCategoryFieldId( $fields ) : $order),
    $orderdirection, "", $exact );
}    
if(!$encap) 
  echo '<a href="'. $sess->MyUrl($slice_id, $encap). '&bigsrch=1">Search form</a><br>';
if( !$srch AND !$encap AND !$query AND !$easy_query ) {
  $cur_cats=GetCategories($db,$p_slice_id);     // get list of categories 
  pCatSelector($sess->name,$sess->id,$sess->MyUrl($slice_id, $encap, true),$cur_cats,$scr->filters[category_id][value], $slice_id, $encap);
}

$ids_cnt = count( $item_ids );
if( $ids_cnt > 0 ) {
  $scr->countPages( count( $item_ids ) );

  $aliases = GetAliasesFromFields($fields);
  $itemview = new itemview( $db, $slice_info, $fields, $aliases, $item_ids,
              $scr->metapage * ($scr->current - 1), $scr->metapage, $sess->MyUrl($slice_id, $encap) );
  $itemview->print_view();
    
	if( ($scr->pageCount() > 1) AND !$no_scr)
    $scr->pnavbar();
}  
else 
  echo "<div>". L_NO_ITEM ."</div>";

ExitPage();

/*
$Log$
Revision 1.16  2001/03/20 15:21:33  honzam
Scrollers used in search output too, better parameters handling

Revision 1.15  2001/03/07 14:34:56  honzam
no message

Revision 1.14  2001/02/23 11:18:03  madebeer
interface improvements merged from wn branch

Revision 1.13  2001/02/20 13:25:15  honzam
Better search functions, bugfix on show on alias, constant definitions ...

Revision 1.10  2000/12/23 19:56:02  honzam
Multiple fulltext item view on one page, bugfixes from merge v1.2.3 to v1.5.2

Revision 1.9  2000/12/21 16:39:33  honzam
New data structure and many changes due to version 1.5.x

Revision 1.8  2000/08/23 12:29:57  honzam
fixed security problem with inc parameter to slice.php3

Revision 1.7  2000/08/22 12:30:06  honzam
fixed problem with lost session id AA_SL_Session in cgi (PHP4) instalation.

Revision 1.6  2000/08/19 11:53:31  kzajicek
Removed debugging output ()

Revision 1.5  2000/08/17 15:09:11  honzam
new inc parameter for displaying specified file instead of slice data

Revision 1.4  2000/07/12 16:53:09  kzajicek
No min-max games are necessary, scroller keeps us within boundaries.

Revision 1.3  2000/07/07 21:31:15  honzam
Wrong parameter count in min() - fixed

Revision 1.22  2000/06/12 19:57:51  madebeer
added GPL LICENSE file, added copyright notice to all files that
added GPL LICENSE
Added copyright (APC) notice to all .inc and .php3 files that have an $Id

Revision 1.21  2000/05/30 09:11:13  honzama
MySQL permissions upadted and completed.

Revision 1.20  2000/04/24 16:35:18  honzama
Small changes in design.

Revision 1.19  2000/03/29 15:56:34  honzama
Encap=true is default parameter to this script.

Revision 1.18  2000/03/22 09:36:17  madebeer
config.inc now allows ecn and igc to have different .css files
also added Id and Log keywords to all .php3 and .inc files
*.php3 makes use of new variables in config.inc

*/
?>

