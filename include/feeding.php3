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

#
# Functions for feeding
#

# Find fields mapping.
function GetFieldMapping($from_slice_id, $to_slice_id, $fields_from, $fields_to="") {
  global $db;

  if (!$fields_to)
    list($fields_to,) = GetSliceFields($to_slice_id);

  if (!$fields_to || !is_array($fields_to))
    return;

  $p_from_slice_id = q_pack_id($from_slice_id);
  $p_to_slice_id = q_pack_id($to_slice_id);

  $SQL = "SELECT from_field_id, to_field_id, flag, value
            FROM feedmap
            WHERE from_slice_id = '$p_from_slice_id'
              AND to_slice_id = '$p_to_slice_id'";
  $db->query($SQL);
  while( $db->next_record() ) {
    $val = ($db->f(flag) == FEEDMAP_FLAG_MAP) ?
              $db->f(from_field_id) :
              ( ($db->f(flag) == FEEDMAP_FLAG_VALUE) ? $db->f(value) : "" );
    $m[$db->f(to_field_id)] = array("feedmap_flag"=>$db->f(flag),"val"=>$val);
  }

  reset( $fields_to ) ;
  while( list( $k, $v ) = each( $fields_to ) ) {
    if( $m[$k] )
      $map[$k] = $m[$k];                 # set if mapped
    else                                 # if not mapped - store in the same; if not exist, set empty
      $map[$k] =  $fields_from[$k] ? array("feedmap_flag"=>FEEDMAP_FLAG_MAP,"val"=>$k)  :
                                     array("feedmap_flag"=>FEEDMAP_FLAG_EMPTY,"val"=>"") ;
  }
  return $map;
}

// get base item of $item_id from relation table
function GetBaseItem($item_id) {
  global $db;
  $dest_id = $item_id;

  while ($item_id) {
    $dest_id = $item_id;
    $p_dest_id = q_pack_id($dest_id);
    $SQL=  "SELECT source_id FROM relation
           WHERE  destination_id = '$p_dest_id'
             AND flag & ". REL_FLAG_FEED;
    $db->query($SQL);
    $item_id = $db->next_record() ? unpack_id($db->f(source_id)) : false;
  }
  return $dest_id;
}

// find if item was already fed to $destination slice or it comes from it
function IsItemFed($item_id, $destination) {
  global $db;
  $p_destination = q_pack_id($destination);

  $base_id = GetBaseItem($item_id);
  $p_base_id = q_pack_id($base_id);

  // if item comes from $destination slice
  $db->query("SELECT slice_id FROM item WHERE id='$p_base_id'");
  if ($db->next_record())
    if (unpack_id($db->f(slice_id)) == $destination)
      return true;

  // get all items, which were fed to $destination slice
  $SQL = "SELECT source_id FROM relation, item
           WHERE relation.destination_id = item.id
             AND item.slice_id = '$p_destination'
             AND flag & ". REL_FLAG_FEED;
  $db->query($SQL);
  while ($db->next_record() )
    $sources[] = unpack_id($db->f(source_id));

  if (!isset($sources) || !is_array($sources))
    return;

  while (list(,$source_id) = each($sources))
    if ($base_id ==  GetBaseItem($source_id))
      return true;

  return false;
}

// copy one item
function FeedItemTo($item_id, $from_slice_id, $destination, $fields, $approved, $tocategory=0,
                    $content="") {
  global $db,  $varset, $itemvarset;

  if( $destination == $from_slice_id )  # don't feed into the same slice
    return false;
  if (isItemFed($item_id, $destination)) # don't feed if the item is already fed.
    return false;

  $p_item_id = q_pack_id($item_id);
  $p_destination = q_pack_id($destination);

  list($fields_to,) = GetSliceFields($destination);
  $map = GetFieldMapping($from_slice_id, $destination, $fields,$fields_to);

  if( !$content )
    $content = GetItemContent($item_id);
  $content4id = $content[$item_id];   # shortcut

  $catfieldid = GetCategoryFieldId( $fields );

  if( $catfieldid AND ( (string)$tocategory != "0" ) ) {
    $SQL = "SELECT value FROM constant WHERE id='".q_pack_id($tocategory)."'";
    $db->query($SQL);
    if( $db->next_record() )
      $destinationcat = $db->f(value);
  }

  $varset = new Cvarset;
  $itemvarset = new Cvarset;  // must be defined before insert_fnc_qte
  $id = new_id();
  $p_id = q_pack_id($id);

  # prepare new4id array before call StoreItem function
  while(list($newfld,$newfldname) = each($fields_to)) {
    $flag = $map[$newfld][feedmap_flag];
    $val = $map[$newfld][val];

    switch ($flag) {
      case FEEDMAP_FLAG_EMPTY : continue;
      case FEEDMAP_FLAG_MAP :
        if ($fields[$val][feed]==STATE_UNFEEDABLE)
          continue;
        else {
          $new4id[$newfld] = $content4id[$val];
         if ($fields[$val][feed]==STATE_FEEDNOCHANGE )
            $new4id[$newfld][0][flag] |= FLAG_FREEZE;  # don't allow to change
          else if ($fields[$val][feed]==STATE_FEEDABLE_UPDATE_LOCKED)
            $new4id[$newfld][0][flag] |= FLAG_FREEZE | FLAG_UPDATE;   #update and don't allow to change
        }
        break;
      case FEEDMAP_FLAG_VALUE : $new4id[$newfld][0][value] = $val;
    }

    $new4id[$newfld][0][flag] |= FLAG_FEED;      # mark as fed

    # category mapping
    if( $newfld == $catfieldid ) {
      if( (string)$tocategory != "0" )    # if 0 - don't change category
        $new4id[$newfld][0][value] = $destinationcat;
    }
    $new4id[$newfld][0][value]=quote($new4id[$newfld][0][value]);
  }

  # fill required fields if not set
  $new4id["status_code....."][0][value] = ($approved=='y' ? 1 : 2);
  $field_ids = array("post_date", "publish_date","expiry_date", "highlight", "posted_by",
                     "edited_by", "last_edit");
  while (list(,$fid) = each($field_ids)) {
    $f_id = substr($fid."................",0,16);
    if (!$new4id[$f_id])
      $new4id[$f_id] = $content4id[$f_id];
  }

  StoreItem( $id, $destination, $new4id, $fields_to, true, true, false );
                                        # insert, invalidatecache, not feed

  # update relation table - stores where is what fed
  $SQL = "INSERT INTO relation ( destination_id, source_id,   flag )
               VALUES ( '$p_id', '$p_item_id', '". REL_FLAG_FEED ."' )";
  $db->query($SQL);

  return $id;
}

// Return feeding tree where items should be fed.
// $tree[$from, $to] = array(approved=>$appr, category=>$cat_id) means, that item from
// slice $from will be fed to slice $to, category $cat_id and bin depending on $appr :
//  $appp="y" => active folder else hold bin.

function CreateFeedTree($sl_id, $from_category_id) {
  global $db;

  $slice_queue[$sl_id] = array(approved=>"y", category=>$from_category_id);

  while (list($sl_id, $val) = each($slice_queue))
    if ($val[approved] == "y") {

      $from_category_id = $val[category];
      if( $from_category_id )
          $p_from_cat_id = q_pack_id($from_category_id);

      $SQL = "SELECT feeds.to_id, feeds.category_id, feeds.all_categories,
                   feeds.to_approved, feeds.to_category_id
            FROM feeds, slice LEFT JOIN feedperms ON feedperms.from_id=feeds.from_id
            WHERE feeds.from_id = slice.id
                  AND feeds.from_id='". q_pack_id($sl_id) ."'
                  AND (slice.export_to_all=1
                  OR  feedperms.to_id = feeds.to_id)";  # check perms to feed, too
      $db->query($SQL);

      while($db->next_record()) {
        $to_id = unpack_id($db->f(to_id));
        if(isset($slice_queue[$to_id]))   // condition is necessary for multi feeding to this slice
          continue;
        $approved = $db->f(to_approved) ? "y" : "n";
        if (($p_from_cat_id == $db->f(category_id)) OR $db->f(all_categories) )
           $slice_queue[$to_id] = $tree[$sl_id][$to_id] = array( approved=>$approved, category=>unpack_id($db->f(to_category_id)));
      }
    }
  return $tree;
}

// Update $dest_d item according to $item_id
function Update($item_id, $slice_id, $dest_id, $destination) {
  global $varset, $itemvarset;

  list($fields,) = GetSliceFields($slice_id);
  list($fields_to,) = GetSliceFields($destination);

  $map = GetFieldMapping($slice_id, $destination, $fields, $fields_to);

  $oldcontent = GetItemContent($dest_id);
  $oldcontent4id = $oldcontent[$dest_id];
  $content = GetItemContent($item_id);
  $content4id = $content[$item_id];   # shortcut

  $varset = new Cvarset;
  $itemvarset = new Cvarset;  // must be defined before insert_fnc_qte

  while(list($key,$fval) = each($oldcontent4id)) {
    if ($map[$key][feedmap_flag] != FEEDMAP_FLAG_MAP)    # skip field, if field from source item is not mapped to dest
      continue;
    $val = $map[$key][val];
    if (($fval[0][flag] & FLAG_UPDATE) || $fields[$val][feed]==STATE_FEEDABLE_UPDATE )
       $oldcontent4id[$key][0][value] = quote($content4id[$val][0][value]);
  }
  StoreItem( $dest_id, $destination, $oldcontent4id, $fields_to, false, true, false );
                                        # update, invalidatecache, not feed
}

// Update all items descending from $item_id
// it's expected, that items don't change their category, so $cat_id is unneccessary.

function UpdateItems($tree, $item_id, $slice_id, $cat_id) {
  global $db;

  $items[$item_id] = $slice_id;

  while (list($item_id,$slice_id) = each($items)) {
    $p_item_id = q_pack_id($item_id);

    # get fed items
    $SQL = "SELECT destination_id, slice_id  FROM relation, item
            WHERE destination_id = id
             AND  source_id='$p_item_id'
             AND flag & ". REL_FLAG_FEED;
    $db->query($SQL);
    while( $db->next_record() ) {
      $update = true;
      $d_id = unpack_id($db->f(destination_id));
      $dest_sl_id = unpack_id($db->f(slice_id));
//    if (!isset($tree[$slice_id][$dest_sl_id]))        // option : take a $tree into account or not
//      continue;

      Update($item_id,$slice_id,$d_id,$dest_sl_id);
      $items[$d_id] = $dest_sl_id;
    }
  }
  return $update;
}

# Feeds item to all apropriate slices
# item_id is unpacked id of feeded item
function FeedItem($item_id, $fields) {   
  global $db, $slice_id;    

  # get item field definition
  $content = GetItemContent($item_id);
  $content4id = $content[$item_id];   # shortcut

  # if not approved - exit
  if( $content4id["status_code....."][0][value] != '1' )
    return false;

  # get this item category_id

  $cat_group = GetCategoryGroup($slice_id);
  $cat_field = GetCategoryFieldId( $fields );

  if($cat_group AND $cat_field) {
    $SQL = "SELECT id FROM constant 
             WHERE group_id = '$cat_group' 
               AND value = '". $content4id[$cat_field][0][value] ."'";
    $db->query($SQL);
    if( $db->next_record() )
      $cat_id = unpack_id($db->f(id));
  }

  $tree = CreateFeedTree($slice_id, $cat_id);
  // now we have the feeding tree in $tree array

  // we try to update items
  $update = UpdateItems($tree, $item_id, $slice_id, $cat_id);
  if ($update)    // if update was done, don't feed
    return;

  if (!$tree)     // if empty tree => no feed
    return;

  // feed item to all destination slices
  $items_id[$slice_id] = $item_id;
  while( list($from_slice,$slices) = each($tree) ) {
    list($fields,) = GetSliceFields($from_slice);
    while (list($to_slice, $atribs) = each($slices)) {
      if ($items_id[$from_slice]) {
        $new_item = FeedItemTo($items_id[$from_slice], $from_slice, $to_slice, $fields, $atribs[approved], $atribs[category]);
        $items_id[$to_slice] = $new_item;
      }
    }
  }
}

# completely deletes item content from database with all subsequencies
# but not deleted item from item table !!!
function DeleteItem($db, $id) {
  $p_itm_id = q_pack_id($id);

  # delete content
  $SQL = "DELETE LOW_PRIORITY FROM content WHERE item_id='$p_itm_id'";
  $db->query($SQL);

  # delete offline
  $SQL = "DELETE LOW_PRIORITY FROM offline WHERE id='$p_itm_id'";
  $db->query($SQL);

  # delete feeding relation

  $SQL = "DELETE LOW_PRIORITY FROM relation WHERE (source_id='$p_itm_id'
                                               OR destination_id='$p_itm_id')
                                              AND flag & ".REL_FLAG_FEED;
  $db->query($SQL);
}  

/*
$Log$
Revision 1.13  2001/06/21 14:15:44  honzam
feeding improved - field value redefine possibility in se_mapping.php3

Revision 1.12  2001/06/12 16:07:22  honzam
new feeding modes -  "Feed & update" and "Feed & update & lock"

Revision 1.11  2001/05/21 13:52:32  honzam
New "Field mapping" feature for internal slice to slice feeding

Revision 1.10  2001/05/10 10:01:43  honzam
New spanish language files, removed <form enctype parameter where not needed, better number validation

Revision 1.9  2001/04/17 21:32:08  honzam
New conditional alias. Fixed bug of not displayed top/bottom HTML code in fulltext and category

Revision 1.8  2001/03/20 16:10:37  honzam
Standardized content management for items - filler, itemedit, offline, feeding
Better feeding support

Revision 1.7  2001/03/07 14:34:01  honzam
fixed bug with radiobuttons dispaly

Revision 1.6  2001/03/06 00:15:14  honzam
Feeding support, color profiles, radiobutton bug fixed, ...

Revision 1.5  2001/01/22 17:32:48  honzam
pagecache, logs, bugfixes (see CHANGES from v1.5.2 to v1.5.3)

Revision 1.2  2000/07/07 21:28:17  honzam
Both manual and automatical feeding bug fixed

Revision 1.1.1.1  2000/06/21 18:40:38  madebeer
reimport tree , 2nd try - code works, tricky to install

Revision 1.1.1.1  2000/06/12 21:50:23  madebeer
Initial upload.  Code works, tricky to install. Copyright, GPL notice there.

Revision 1.4  2000/06/12 21:41:24  madebeer
removed whitespace from config-ecn.inc

added $Id $Log and $Copyright to some stray files

*/
?>