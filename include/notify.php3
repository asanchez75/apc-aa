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
# utility for notifying people of evets by email
#


if( !defined("ITEM_PHP3_INC") ) { include $GLOBALS[AA_INC_PATH]."item.php3"; }
if( !defined("VIEW_PHP3_INC") ) { include $GLOBALS[AA_INC_PATH]."view.php3"; }  

// notify users of an event
function email_notify($slice_id, $event, $item_id, $extra = ""){
  global $db;
  $p_slice_id = q_pack_id($slice_id);

  # expand the body template using the itemview function
  $format['group_by'] = '';
  $format['category_format'] = '';
  $format['compact_bottom'] = '';
  $format['compact_remove'] = '';
  $format['even_row_format'] ='';
  $format['even_odd_differ'] = '0';
  $format['id'] = $slice_id;

  // get alias list from database
  list($fields,) = GetSliceFields($slice_id);
  $aliases = GetAliasesFromFields($fields, $als);

  // select the text templates
  switch ($event){
    case 1: $prefix = 'notify_holding_item'; break;
    case 2: $prefix = 'notify_holding_item_edit'; break;
    case 3: $prefix = 'notify_active_item'; break;
    case 4: $prefix = 'notify_active_item_edit'; break;
  }  

  $SQL = "SELECT ${prefix}_s as s, ${prefix}_b as b from slice where id = '$p_slice_id'";
  $db->query($SQL);
  if( $db->next_record() ){
    $s = $db->f('s');
    $b = $db->f('b');
  } else {
    die ("bad slice_id");
  }

  // determine body of message
  $format['odd_row_format'] = $b;
  $item_ids[] = $item_id;

  $itemview = new itemview( $db, $format, $fields, $aliases, $item_ids, 
                            0, 1, '', "", 0);
  $body = $itemview->get_output_cached("view");

  // select all the users
  $SQL = "SELECT uid from email_notify where slice_id = '$p_slice_id' AND function = '$event'";
  $db->query($SQL);

  // loop through the users for the event
  while( $db->next_record() ){
    // unalias the text
   $email = $db->f('uid');
   // mail the text
   mail($email, $s, $body);
   echo "DONE $email, $s, $body <BR>";
  }

}

/*
$Log$
Revision 1.3  2001/12/21 11:44:56  honzam
fixed bug of includes in e-mail notify

Revision 1.2  2001/12/20 00:27:18  honzam
Fixed bugs in notify - now works with PHP3

Revision 1.1  2001/12/18 12:36:03  honzam
new notification e-mail possibility (notify new item in slice, bins, ...)

*/
?>