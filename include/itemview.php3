<?php
/**
 * File contains definition of itemview class
 * used to display set of item/links
 *
 * Should be included to other scripts
 *
 * @package Include
 * @version $Id$
 * @author Honza Malik <honza.malik@ecn.cz>
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

if (!defined ("ITEMVIEW_INCLUDED"))
     define ("ITEMVIEW_INCLUDED",1);
else return;

require_once $GLOBALS["AA_INC_PATH"]."stringexpand.php3";

/**
 * itemview class - used to display set of items or links or ...
 *
 * it - selects right set of items/link IDs to display (based on page scroller
 *      for example)
 *    - get item/links content from database (using "Abstract Data Structure"
 *      described also in {@link http://apc-aa.sourceforge.net/faq/#1337})
 *    - instantiate 'item' class (defined in item.php3) for each item/link which
 *      should be printed.
 *
 * @see itemview
 */
class itemview {
  var $db;                  // not used anymore
  var $zids;               // zids to show
  var $from_record;        // from which index begin showing items
  var $num_records;        // number of shown records
  var $slice_info;         // record from slice database for current slice
  var $fields;             // array of fields used in this slice
  var $aliases;            // array of alias definitions. If used with multi-slice,
                           // this variable contains an array of aliases for all slices
                           // aliases[slice_id0] are aliases for slice 0 etc.
  var $clean_url;          // url of slice page with session id and encap..
  var $group_fld;          // id of field for grouping
  var $disc;               // array of discussion parameters (see apc-aa/view.php3)
  var $get_content_funct;  // function to call, if we want to get content
                           // (using "Abstract Data Structure" described also
                           // in {@link http://apc-aa.sourceforge.net/faq/#1337})
  var $parameters;         // optional asociative array of additional parameters
                           // - used for category_id (in Links module) ...
                           // - filed by parameter() method

  function itemview( $slice_info, $fields, $aliases, $zids, $from, $number,
                     $clean_url, $disc="", $get_content_funct='GetItemContent'){
    #constructor
    //Not used anymore: $this->db = $db;
    $this->slice_info = $slice_info;  # $slice_info is array with this fields:
                                      #      - print_view() function:
                                      #   compact_top, category_sort,
                                      #   category_format, category_top,
                                      #   category_bottom, even_odd_differ,
                                      #   even_row_format, odd_row_format,
                                      #   compact_remove, compact_bottom,
                                      #   vid - used for scroller
                                      #      - print_item() function:
                                      #   fulltext_format, fulltext_remove,
                                      #   fulltext_format_top,
                                      #   fulltext_format_bottom,
                                      #   banner_position, banner_parameters

    $this->group_fld   = ($slice_info['category_sort'] ?
                         GetCategoryFieldId($fields) : $slice_info['group_by']);

    $this->aliases     = $aliases;
    // add special alias, which is = 1 for selected item (given by
    // set[34]=selected-43535 view.php3 parameter
    if ( !$aliases['_#SELECTED'] AND $slice_info['selected_item'] )
        $this->aliases['_#SELECTED'] = array('fce'=>'f_e:selected:'.$slice_info['selected_item'], "param"=>"", "hlp"=>"");
    $this->fields      = $fields;
    $this->zids        = $zids;
    $this->from_record = $from;      # number or text "random[:<weight_field>]"
    $this->num_records = $number;    # negative number used for displaying n-th group of items only
    $this->clean_url   = $clean_url;
    $this->disc        = $disc;
    $this->parameters  = array();


    switch( (string)$get_content_funct ) {
        case '1':         // - for backward compatibility when $use_short_ids
                          //   bool value was used instead of $get_content_funct
            $this->get_content_funct = 'GetItemContent_Short'; break;
        case '':          // - use default
        case '0':         // - for backward compatibility ...
                          //   Item ids are in long format (default)
            $this->get_content_funct = 'GetItemContent'; break;
            $this->get_content_funct = 'GetItemContent'; break;
        default:
            $this->get_content_funct = $get_content_funct;
    }
  }

  /** Optional asociative array of additional parameters
   *  Used for category_id (in Links module) ... */
  function parameter($property, $value ) {
      $this->parameters[$property] = $value;
  }

  function assign_items($zids) {
      $this->zids = $zids;
  }

  /** returns true, if view have to show random item (weighted or not) */
  function is_random() {
      return (substr($this->from_record, 0, 6) == 'random');
  }

  function get_output_cached($view_type="") {
    trace("+","get_output_cached",null); #$this);

    #create keystring from values, which exactly identifies resulting content

    if( $this->is_random() ) {                         # don't cache random item
        $res = $this->get_output($view_type);
        trace("-");
        return $res;
    }
    if (isset($this->zids))
      $keystr = serialize($this->slice_info).
              $view_type.
              $this->from_record.
              $this->num_records.
              $this->clean_url.
              ((isset($this->zids)) ? $this->zids->id(0) : "");
    $number_of_ids = ( ($this->num_records < 0) ? MAX_NO_OF_ITEMS_4_GROUP :  # negative used for displaying n-th group of items only
                                        $this->from_record+$this->num_records );
    for( $i=$this->from_record; $i<$number_of_ids; $i++)
        if (isset($this->zids))
            $keystr .= $this->zids->id($i);
        $keystr .=serialize($this->disc);
        $keystr .=serialize($this->aliases);

        $keystr .= stringexpand_keystring();

        if( !$GLOBALS['nocache'] && ($res = $GLOBALS['pagecache']->get($keystr)) ) {
            trace("-");
            return $res;
        }

        $str2find_save = $GLOBALS['str2find_passon'];
        $GLOBALS['str2find_passon'] = "";
        #cache new value
        $res = $this->get_output($view_type);

        $str2find_this = ",slice_id=".unpack_id128($this->slice_info["id"]);
        if (!strstr($GLOBALS['str2find_passon'],$str2find_this))
            $GLOBALS['str2find_passon'] .= $str2find_this; // append our str2find
        $GLOBALS['pagecache']->store($keystr, $res, $GLOBALS['str2find_passon']);
        $GLOBALS['str2find_passon'] .= $str2find_save;
        trace("-");
        return $res;
  }

  function get_disc_buttons($empty) {
    if (!$empty) {
      $out.= $this->slice_info['d_sel_butt'];
      $out.= ' '. $this->slice_info['d_all_butt'];
    }
    $out.= ' '. $this->slice_info['d_add_butt'];
    return $out;
  }

  // show list of discussion items --- useful as search form return value

  function get_disc_list(&$CurItem) {
    $CurItem->setformat ($this->slice_info['d_top']);
    $out = $CurItem->get_item();
    if (is_array ($this->disc['disc_ids'])) {
        $ids = $this->disc['disc_ids'];
        $ids_sql = "";
        reset ($ids);
        while (list (,$id) = each ($ids)) {
            if ($ids_sql != "") $ids_sql .= ",";
            $ids_sql .= '"'.addslashes(q_pack_id($id)).'"';
        }
        $SQL = "SELECT * FROM discussion WHERE id IN ($ids_sql)";
        if ($debug) echo $SQL;
        $d_content = GetDiscussionContentSQL ($SQL, "", "",$this->disc['vid'],true,$this->disc['html_format'],$this->clean_url);
        if (is_array ($d_content)) {
            reset ($d_content);
            while (list ($id,$disc) = each ($d_content)) {
                $CurItem->columns = $disc;
                $CurItem->setformat ($this->slice_info['d_compact']);
                $out .= $CurItem->get_item();
            }
        }
    }
    $CurItem->setformat ($this->slice_info['d_bottom']);
    $out .= $CurItem->get_item();
    return $out;
  }

  // show discussion comments in the thread mode
  function get_disc_thread(&$CurItem) {
//    if (!$this->slice_info['d_showimages']) {
       $order =  $this->slice_info['d_order'];
//    }

    $d_content = GetDiscussionContent($this->disc['item_id'], "",$this->disc['vid'],true,$order,$this->disc['html_format'],$this->clean_url);
    $d_tree = GetDiscussionTree($d_content);

    $out .= "<a name=\"disc\"></a><form name=discusform>";
    $script_loc = con_url($this->clean_url,"sh_itm=".$this->disc['item_id']); // location of the shtml with slice.php3 script

    $cnt = 0;     // count of discussion comments

    $out .= $this->slice_info['d_top'];         // top html code

    if ($d_tree) {    // if not empty tree
      $CurItem->setformat( $this->slice_info['d_compact']);

      if ($this->slice_info['d_showimages'] || $this->slice_info['d_order'] == 'thread') {  // show discussion in the thread mode
         GetDiscussionThread($d_tree, "0", 0, $outcome);
         if( $outcome ) {
             while ( list( $d_id, $images ) = each( $outcome )) {
                SetCheckboxContent( $d_content, $d_id, $cnt++ );
                SetImagesContent( $d_content, $d_id, $images, $this->slice_info['d_showimages'], $this->slice_info['images']);
                $this->set_columns ($CurItem, $d_content, $d_id);
                $out.= $CurItem->get_item();
             }
         }
      }
      else {                      // show discussion sorted by date
        reset($d_content);
         while ( list( $d_id, ) = each( $d_content )) {
          if ( $d_content[$d_id]["hide"] )
            continue;
          SetCheckboxContent( $d_content, $d_id, $cnt++ );
          $this->set_columns ($CurItem, $d_content, $d_id);
          $out.= $CurItem->get_item();
        }
      }
    }

    // buttons bar
     $CurItem->setformat($this->slice_info['d_bottom']);        // bottom html code
     $col["d_buttons......."][0]['value'] = $this->get_disc_buttons($cnt==0);
     $col["d_buttons......."][0]['flag'] = FLAG_HTML;
     $col["d_item_id......."][0]['value'] = $this->disc['item_id'];
     $col["d_disc_url......"][0]['value'] = $this->clean_url ."&nocache=1&sh_itm=".$this->disc['item_id'];
     $col["d_disc_url......"][0]['flag'] = FLAG_HTML;   // do not change &->&amp;
     $CurItem->columns = $col;
     $out.= $CurItem->get_item() ;

     $out.= "</form>";

    // create a javascript code for getting selected ids and sending them to a script
    $out .= "
      <SCRIPT Language=\"JavaScript\"><!--
        function showSelectedComments() {
          var url = \"". $script_loc . "&nocache=1&sel_ids=1\"
          var done = 0;

          for (var i = 0; i < ".$cnt."; i++) {
            if( eval('document.forms[\"discusform\"].c_'+i).checked) {
              done = 1
              url += \"&ids[\" +  escape(eval('document.forms[\"discusform\"].h_'+i).value) + \"]=1\"
            }
          }
          url += \"\#disc\"
          if (done == 0) {
            alert (\" ". _m("No comment was selected") ."\" )
          } else {
            document.location = url
          }
        }
        function showAllComments() {
          document.location = \"". $script_loc . "&nocache=1&all_ids=1#disc\"
        }
        function showAddComments() {
          document.location = \"". $script_loc . "&nocache=1&add_disc=1#disc\"
        }
       // --></SCRIPT>";
   return $out;
  }

  // show discussion comments in the fulltext mode
  function get_disc_fulltext(&$CurItem) {

    $CurItem->setformat( $this->slice_info['d_fulltext']);      // set fulltext format
    $d_content = GetDiscussionContent($this->disc['item_id'], $this->disc['ids'],
                                      $this->disc['vid'], true, 'timeorder',
                                      $this->disc['html_format'], $this->clean_url);
    $d_tree = GetDiscussionTree($d_content);
    if ($this->disc['ids'] && is_array($this->disc['ids']) && is_array ($d_content)) {  // show selected cooments
      reset($d_content);
      while (list ($id,) = each($d_content)) {
        if ($outcome[$id])            // if the comment is already in the outcome => skip
          continue;
        GetDiscussionThread($d_tree, $id, 1, $outcome);
      }
    }
    else     // show all comments
      GetDiscussionThread($d_tree, "0", 0, $outcome);

    $out.= '<a name="disc"></a>';
    if( isset($outcome) AND is_array($outcome) ) {
      while ( list( $d_id, $images ) = each( $outcome )) {
        $this->set_columns ($CurItem, $d_content, $d_id);
        $depth = count($images)-1;
        $spacer = "";
        $out.= '
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>';
        for( $i=0; $i<$depth; $i++)
          $spacer .= $this->slice_info['d_spacer'];
        if($spacer)
          $out .= "
              <td valign=top>$spacer</td>";
        $out .= "
              <td width=\"99%\">".$CurItem->get_item()."
              </td>
            </tr>
                  </table>
          <br>";
      }
    }
    return $out;
  }

  // show the form for adding discussion comments
  function get_disc_add(&$CurItem) {
    trace("+","get_disc_add","Parent=".$this->disc['parent_id']);
    // if parent_id is set => show discussion comment
    $out.= '<a name="disc"></a>';
    if ($this->disc['parent_id']) {
      $d_content = GetDiscussionContent($this->disc['item_id'], $this->disc['ids'],$this->disc['vid'],true,'timeorder',$this->disc['html_format'],$this->clean_url);
      $CurItem->setformat( $this->slice_info['d_fulltext']);
      $this->set_columns ($CurItem, $d_content, $this->disc['parent_id']);
      $out .= $CurItem->get_item();
    } else {
      $col["d_item_id......."][0]['value'] = $this->disc['item_id'];
      $col["d_disc_url......"][0]['value'] = $this->clean_url . "&sh_itm=".$this->disc['item_id'];
      $col["d_disc_url......"][0]['flag'] = FLAG_HTML;   // do not change &->&amp;
      $CurItem->columns = $col;
    }
    // show a form for posting a comment
    $CurItem->setformat( $this->slice_info['d_form']);
    $out .= $CurItem->get_item();
    trace("-");
    return $out;
  }

  function unaliasWithScroller($txt, $item=null) {
    # get HTML code, unalias it and add scroller, if needed
    $level = 0; $maxlevel = 0;
    # If no item is specified, then still try and expand aliases using parameters
    if (! $item) {
        $item = new item(null,null,$this->aliases,null,null,null,null,null,null,$this->parameters);
    }
    return new_unalias_recurent($txt,"",$level,$maxlevel,$item,$this,null);
  }

  // set the aliases from the slice of the item ... used to view items from
  // several slices at once: all slices have to define aliases with the same names

  function set_columns(&$CurItem, &$content, $iid)
  {
     // hack for searching in multiple slices. This is not so nice part
     // of code - we mix there $aliases[<alias>] with $aliases[<p_slice_id>][<alias>]
     // used (filled) in slice.php3
     $CurItem->columns = $content[$iid];
     // slice_id... in content is packed!!!
     $p_slice_id = addslashes($content[$iid]["slice_id........"][0]['value']);
     if (is_array ($this->aliases[$p_slice_id]))
        $CurItem->aliases = $this->aliases[$p_slice_id];
     else $CurItem->aliases = $this->aliases;
  }

  // ----------------------------------------------------------------------------------
  //  get_output

  #view_type used internaly for different view types
  function get_output($view_type="") {
    global $debug;
    trace("+","itemview:get_output",$view_type);

    if ($view_type == "discussion") {
      trace("=","","discussion type ".$this->disc['type']);
      $CurItem = new item("", "", $this->aliases, $this->clean_url, "", "");   # just prepare
      $CurItem->set_parameters($this->parameters);
      switch ($this->disc['type']) {
        case 'thread' : $out = $this->get_disc_thread($CurItem); break;
        case 'fulltext' : $out = $this->get_disc_fulltext($CurItem); break;
        case 'list' : $out = $this->get_disc_list($CurItem); break;
        case 'add_disc':
        default: $out = $this->get_disc_add($CurItem); break;
      }
      trace("-");
      return $out;
    }
     // other view_type than discussion

    if( !( isset($this->zids) AND is_object($this->zids) )) {
      trace("-");
      return;
    }

    $is_random = $this->is_random();

    # fill the foo_ids - ids to itemids to get from database
    if( !$is_random ) {
      $foo_zids = $this->zids->slice((integer)$this->from_record,
         ( ($this->num_records < 0) ? MAX_NO_OF_ITEMS_4_GROUP :  $this->num_records ));
    } else { // Selecting random record
      list( $random, $rweight ) = explode( ":", $this->from_record);
      if( !$rweight || !is_array($this->fields[$rweight]) ) {
        # not weighted, we can select random id(s)
        for( $i=0; $i<$this->num_records; $i++) {
          $sel = rand( 0, $this->zids->count() - 1) ;
          if( $this->zids->id($sel) )
            $foo_ids[] = $this->zids->id($sel);
        }
        $foo_zids = new zids($foo_ids, $this->zids->onetype());
        $this->zids = $foo_zids;  // Set zids so can index into it
      } else {   # weighted - we must get all items (banners) and then select
                 # the one based on weight field (now in $rweight variable
        $foo_zids = $this->zids;
      }
    }
    trace("=","",$view_type." after zids");
    // fill Abstract Data Structure by the right function
    // (GetItemContent / GetItemContent_Short / GetLinkContent)
    $function2call = $this->get_content_funct;
    trace("=","",$view_type." pre call to ".$function2call);
    // Create an array of content, indexed by either long or short id (not tagged id)
    $content = $function2call($foo_zids);

    trace("=","",$view_type." after content");
    if ($debug) huhl("itemview:get_content: found",$content);

    $CurItem = new item("", "", $this->aliases, $this->clean_url, "", "");   # just prepare
    $CurItem->set_parameters($this->parameters);

    # process the random selection (based on weight)
    if( $rweight && is_array($this->fields[$rweight]) ) {
      $this->zids->clear('l');   // remove id and prepare for long ids
      #get sum of all weight
      reset( $content );
      while( list(,$v) = each($content) ) {
        $weightsum += $v[$rweight][0]['value'];
      }
      for( $i=0; $i<$this->num_records; $i++) {
        $winner = rand(1,$weightsum);
        reset( $content );
        $ws=0;
        while( list($k,$v) = each($content) ) {
          $ws += $v[$rweight][0]['value'];
          if( $ws >= $winner ) {
            $this->zids->add($k);
            break;
          }
        }
      }
      $this->from_record = 0;
    }

    // count hit for random item - it is used for banners, so it is important
    // to count display number
    if ( $is_random AND ($this->zids->count()==1) ) {
        $l_ids = $this->zids->longids();
        CountHit($l_ids[0]);
    }

    switch ( $view_type ) {
      case "fulltext":
        $iid = $this->zids->short_or_longids(0);  # unpacked or short id
        $this->set_columns($CurItem, $content, $iid);   # set right content for aliases

        # print item
        $CurItem->setformat( $this->slice_info['fulltext_format'],
                             $this->slice_info['fulltext_remove']);
        $out  = $this->unaliasWithScroller($this->slice_info['fulltext_format_top'], $CurItem);
        $out .= $CurItem->get_item();
        $out .= $this->unaliasWithScroller($this->slice_info['fulltext_format_bottom'], $CurItem);
        break;

      case "itemlist":          # multiple items as fulltext one after one
        $out = $this->slice_info['fulltext_format_top'];
        for( $i=0; $i<$this->num_records; $i++ ) {
          $iid = $this->zids->short_or_longids($this->from_record+$i);
          if( !$iid )
            continue;                                     # iid = quoted or short id

          $this->set_columns ($CurItem, $content, $iid);   # set right content for aliases

            # print item
          $CurItem->setformat( $this->slice_info['fulltext_format'],
                               $this->slice_info['fulltext_remove']);
          $out .= $CurItem->get_item();
        }
        $out .= $this->unaliasWithScroller($this->slice_info['fulltext_format_bottom'], $CurItem);
        break;

      case "calendar":
        $out = $this->get_output_calendar ($content);
        break;

      default:                         # compact view (of items or links)
        $oldcat = "_No CaTeg";
        $group_n = 0;                  # group counter (see group_n slice.php3 parameter)

        # negative num_record used for displaying n-th group of items only
        $number_of_ids = ( ($this->num_records < 0) ? MAX_NO_OF_ITEMS_4_GROUP :
                                        $this->num_records );
        for( $i=0; $i<$number_of_ids; $i++ ) {
          $GLOBALS['QueryIDsIndex'] = $i;   # So that _#ITEMINDX = f_e:itemindex can find it
          # display banner, if you have to
          if( $this->slice_info['banner_parameters'] &&
              ($this->slice_info['banner_position']==$i) )
            $out .= GetView(ParseViewParameters($this->slice_info['banner_parameters']));


          $zidx = $this->from_record+$i;
          if ($zidx >= $this->zids->count()) continue;
          $iid = $this->zids->short_or_longids($zidx);
          if( !$iid ) { huhe("Warning: itemview: got a null id"); continue; }
          # Note if iid is invalid, then expect empty answers
          $catname = $content[$iid][$this->group_fld][0]['value'];
          if ($this->slice_info['gb_header'])
            $catname = substr($catname, 0, $this->slice_info['gb_header']);

          $this->set_columns($CurItem, $content, $iid);   # set right content for aliases

          # get top HTML code, unalias it and add scroller, if needed
          if( !$top_html_already_printed ) {
            $out = $this->unaliasWithScroller($this->slice_info['compact_top'], $CurItem);
            # we move printing of top HTML here, in order we can use aliases
            # data from the first found item
            $top_html_already_printed = true;
          }

            # print category name if needed
          if($this->group_fld AND strcasecmp ($catname,$oldcat)) {
            if( $this->num_records >= 0 ) {
              if ($oldcat != "_No CaTeg") {
                  $CurItem->setformat( $this->slice_info['category_bottom'] );
                  $out .= $CurItem->get_item();
              }
              $CurItem->setformat( $this->slice_info['category_format'] );

              $out .= $this->slice_info['category_top'];
              $out .= $CurItem->get_item();
            } else {
              $group_n++;
            }
            $oldcat = $catname;
          }

          if( ($this->num_records < 0) AND ($group_n != -$this->num_records ))
            continue;    # we have to display just -$this->num_records-th group

            # print item
          $CurItem->setformat(
             (($i%2) AND $this->slice_info['even_odd_differ']) ?
             $this->slice_info['even_row_format'] : $this->slice_info['odd_row_format'],
             $this->slice_info['compact_remove'] );

          $out .= $CurItem->get_item();
        }
        if($this->group_fld) {
            $CurItem->setformat( $this->slice_info['category_bottom'] );
            $out .= $CurItem->get_item();
        }
        if( !$top_html_already_printed )   # print top HTML even no item found
          $out = $this->unaliasWithScroller($this->slice_info['compact_top'], $CurItem);
        $out .= $this->unaliasWithScroller($this->slice_info['compact_bottom'], $CurItem);
    }
    trace("-");
    return $out;
  }

# ----------------------------------------------------------------------------
#                            calendar view
# ----------------------------------------------------------------------------
    function resolve_calendar_aliases ($txt,$day="") {
        $month = $this->slice_info['calendar_month'];
        $year = $this->slice_info['calendar_year'];

        $aliases = array (
            "_#CV_NUM_Y" => $year,
            "_#CV_NUM_M" => $month,
            "_#CV_NUM_D" => $day,
            "_#CV_TST_1" => mktime (0,0,0,$month,$day,$year),
            "_#CV_TST_2" => mktime (0,0,0,$month,$day+1,$year));

        reset ($aliases);
        while (list ($alias,$replace) = each($aliases))
            $txt = str_replace ($alias,$replace,$txt);
        return $txt;
    }

    # send content via reference to be quicker
    function get_output_calendar (&$content) {
        trace("+","get_output_calendar");
        $CurItem = new item("", "", $this->aliases, $this->clean_url, "", "");   # just prepare
        $CurItem->set_parameters($this->parameters);

        $month = $this->slice_info['calendar_month'];
        $year = $this->slice_info['calendar_year'];

        $min_cell_date = mktime (0,0,0,$month,1,$year);
        $max_cell_date = mktime (0,0,0,$month+1,1,$year);

        $min_cell = getdate($min_cell_date);
        $max_cell = getdate($max_cell_date-1);

        $max_cell = $max_cell["mday"] - $min_cell["mday"] + 1;
        $min_cell = 1;

        /* calendar is an array of days, every day contains info about events starting on that day:
            iid is short_id of event
            span is number of days in current month
            start is 1 when it's the first cell containing this iid. The event is repeated in all days over which it spans.
        */
        $calendar = array();
        $max_events = 0;

        trace("=","","pre-for");
        for( $i=0;
            $i<$this->num_records
            && ($i+$this->from_record < $this->zids->count());
            $i++ ) {
            $iid = $this->zids->short_or_longids($this->from_record+$i);
            if( !$iid )
                continue;                                     # iid = unpacked item id
            $start_date = $content[$iid][$this->slice_info['calendar_start_date']][0][value];
            $end_date = $content[$iid][$this->slice_info['calendar_end_date']][0][value];
            if ($start_date > $max_cell_date || $end_date < $min_cell_date)
                if ($debug) echo "<h1>Some error in calendar view!
                $start_date &gt; $max_cell_date || $end_date &lt; $min_cell_date </h1>";
            if ($start_date < $min_cell_date)
                $start_date = $min_cell;
            else {
                $start_date = getdate ($start_date);
                $start_date = $start_date["mday"];
            }
            if ($end_date >= $max_cell_date)
                $end_date = $max_cell;
            else {
                $end_date = getdate ($end_date);
                $end_date = $end_date["mday"];
            }

            $ievent = 0;
            do {
                $free = true;
                for ($date = $start_date; $date <= $end_date; ++$date)
                    if ($calendar[$date][$ievent]["iid"]) {
                        $free = false;
                        break;
                    }
                if (!$free) $ievent ++;
            } while (!$free);

            $max_events = max ($max_events, $ievent+1);

            $calendar [$start_date][$ievent] = array ("iid"=>$iid,"span"=>$end_date-$start_date+1,"start"=>1);
            for ($date = $start_date+1; $date <= $end_date; ++$date)
                $calendar [$date][$ievent] = array ("iid" => $iid,"span"=>$end_date-$date+1);
        }
        trace("=","","post-for");

        if ($this->slice_info['calendar_type'] == 'mon_table') {
            $row_len = 7;
            $firstday = getdate (mktime (0,0,0,$month,1,$year));
            $firstday = $firstday ["wday"] - 2;
            if ($firstday < -1) $firstday += $row_len;
            $rowcount = ($max_cell + $firstday + 1) / $row_len;

            for ($cell = 7 - $firstday; $cell <= $max_cell; $cell += $row_len) {
                for ($ievent = 0; $ievent < $max_events; ++$ievent)
                    if ($calendar [$cell][$ievent]["iid"]) {
                        $calendar [$cell][$ievent]["start"] = 1;
                        $calendar [$cell][$ievent]["span"] = min ($calendar[$cell][$ievent]["span"],$row_len);
                    }
            }
            $rowview = true;
        }
        else {
            $rowview = false;
        }

        $out = $this->unaliasWithScroller(
               $this->resolve_calendar_aliases ($this->slice_info['compact_top']), $CurItem);

        if (!$rowview) {
            for ($cell = $min_cell; $cell <= $max_cell; ++$cell) {
                $calendar_aliases["_#CV_NUM_D"] = $cell;
                $events = $calendar[$cell];
                if ($this->slice_info['even_odd_differ'] && count($events) == 0) {
                    $header = $this->slice_info['aditional'];
                    $footer = $this->slice_info['aditional2'];
                }
                else {
                    $header = $this->slice_info['category_format'];
                    $footer = $this->slice_info['category_bottom'];
                }
                $CurItem->setformat ($this->resolve_calendar_aliases($header,$cell));
                $out .= $CurItem->get_item();

                for ($ievent = 0; $ievent < $max_events; ++$ievent) {
                    $event = $events[$ievent];
                    if ($event["iid"] && $event["start"]) {
                        $this->set_columns ($CurItem, $content, $event['iid']);
                        $CurItem->setformat ($this->slice_info['aditional3']);
                        $tdattribs = $CurItem->get_item();
                        $CurItem->setformat ($this->slice_info['odd_row_format']);
                        $out .= "<td valign=top rowspan=".$event['span']." $tdattribs>"
                            .$CurItem->get_item()."</td>";
                    }
                    else if (!$event["iid"])
                        $out .= "<td class='empty'></td>";
                }

                $CurItem->setformat ($this->resolve_calendar_aliases($footer,$cell));
                $out .= $CurItem->get_item();
            }
        }

        if ($rowview) {
            for ($row=0; $row < $rowcount; ++$row) {
                $outrow = "";
                $firstcell = $row * $row_len - $firstday;
                for ($cell = $firstcell; $cell < $firstcell + $row_len; ++$cell) {
                    $events = $calendar[$cell];
                    if ($this->slice_info['even_odd_differ'] && count($events) == 0)
                        $header = $this->slice_info['aditional'];
                    else $header = $this->slice_info['category_format'];
                    $label = $cell >= $min_cell && $cell <= $max_cell ? $cell : "";
                    $CurItem->setformat ($this->resolve_calendar_aliases($header,$label));
                    $outrow .= $CurItem->get_item();
                }
                if ($outrow) $out .= "<tr>$outrow</tr>";

                if ($this->slice_info['odd_row_format'])
                for ($ievent = 0; $ievent < $max_events; ++$ievent) {
                    $out .= "<tr>";
                    for ($cell = $firstcell; $cell < $firstcell + $row_len; ++$cell) {
                        $event = $calendar[$cell][$ievent];
                        if ($event["iid"] && $event["start"]) {
                            $this->set_columns($CurItem, $content, $event['iid']);
                            $CurItem->setformat($this->slice_info['aditional3']);
                            $tdattribs = $CurItem->get_item();
                            $CurItem->setformat ($this->slice_info['odd_row_format']);
                            $out .= "<td valign=top colspan=".$event['span']." $tdattribs>"
                                .$CurItem->get_item()."</td>";
                        }
                        else if (!$event["iid"])
                            $out .= "<td class='empty'></td>";
                    }
                    $out .= "</tr>";
                }

                $outrow = "";
                for ($cell = $firstcell; $cell < $firstcell + $row_len; ++$cell) {
                    $events = $calendar[$cell];
                    if ($this->slice_info['even_odd_differ'] && count($events) == 0)
                        $footer = $this->slice_info['aditional2'];
                    else $footer = $this->slice_info['category_bottom'];
                    $label = $cell >= $min_cell && $cell <= $max_cell ? $cell : "";
                    $CurItem->setformat ($this->resolve_calendar_aliases($footer,$label));
                    $outrow .= $CurItem->get_item();
                }
                $out .= "<tr>$outrow</tr>";
            }
        }

        $out .= $this->unaliasWithScroller(
               $this->resolve_calendar_aliases ($this->slice_info['compact_bottom']), $CurItem);
        trace("-");
        return $out;
    }

# ----------------------------------------------------------------------------
#                            end of calendar view
# ----------------------------------------------------------------------------

  # compact view
  function print_view($flag="CACHE") {
    if( $flag == "CACHE" )
      echo $this->get_output_cached("view");
     else
      echo $this->get_output("view");
  }

  # fulltext of one item
  function print_item($flag="CACHE") {
    if( $flag == "CACHE" )
      echo $this->get_output_cached("fulltext");
     else
      echo $this->get_output("fulltext");
  }

  # multiple items as fulltext one after one
  function print_itemlist($flag="CACHE") {
    if( $flag == "CACHE" )
      echo $this->get_output_cached("itemlist");
     else
      echo $this->get_output("itemlist");
  }

  # discusion thread or fulltext or one comment
  function print_discussion($flag="CACHE") {
    if( $flag == "CACHE" )
      echo $this->get_output_cached("discussion");
     else
      echo $this->get_output("discussion");
  }

  function idcount() {
        return $this->zids->count();
  }

};   //class itemview

?>
