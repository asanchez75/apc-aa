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
# Functions for parsing XML documents used in item exchange system (like 
# Cross-Server Netvorking (node2node feeding) and off-line filling)
#

define("WDDX_DUPLICATED", 1);
define("WDDX_BAD_PACKET", 2);

# is packet already stored in database?
function IsDuplicated( $packet, $db ) {
  $SQL = "SELECT * FROM offline WHERE digest='". md5($packet) ."'";
  $db->query($SQL);
  return ( $db->next_record() ? 1 : 0 );
}  

# is packet already stored in database?
function RegisterItem( $id, $packet, $db ) {
  $SQL = "INSERT INTO offline ( id, digest, flag ) VALUES ( '$id', '". md5($packet) ."', '' )";
  $db->query($SQL);
}  

# gets one item stored in WDDX format and stored it in database
function StoreWDDX2DB( $packet, $slice_id, $fields ) {
  global $db, $itemvarset, $varset;

  if( IsDuplicated( $packet, $db ) )
    return WDDX_DUPLICATED;

  ($vals =  wddx_deserialize ($packet));
  if( !$vals )
    return WDDX_BAD_PACKET;

  # update database
  $id = new_id();

  reset($vals);
  while(list($key,$val) = each($vals)) {
    if( isset($val) and is_array($val) ) {  
      switch( $val[0] ) {   # field type - defines action to do with content
        case "base64": 
          $value = base64_decode($val[2]);  # $val[1] is filename - not used now
          break;
      }
    }      
    else
      $value = $val;        # if not array - just store content 

    # add to content table or prepare itemvarset for addition in item table
    insert_fnc_qte($id, $fields[$key], quote($value), "", true); 
  }                                                          
  
  # store prepared data to item table 
  $itemvarset->add("id", "unpacked", $id);
  $itemvarset->add("slice_id", "unpacked", $slice_id);
  $itemvarset->ifnoset("status_code", "1", "quoted");
  $itemvarset->ifnoset("post_date", time()+157680000, "quoted"); // 5 years
  $itemvarset->ifnoset("publish_date", time(), "quoted");
  $itemvarset->ifnoset("last_edit", time(), "quoted");
  $itemvarset->ifnoset("expiry_date", time()+157680000, "quoted"); // 5 years
  $SQL = "INSERT INTO item " . $itemvarset->makeINSERT();
  
  $db->query($SQL);

  RegisterItem( q_pack_id($id), $packet, $db );
  
  $cache = new PageCache($db,CACHE_TTL,CACHE_PURGE_FREQ); # database changed - 
  $cache->invalidateFor("slice_id=$slice_id");  # invalidate old cached values

  return WDDX_OK;
}  

/*
$Log$
Revision 1.3  2001/03/06 00:15:14  honzam
Feeding support, color profiles, radiobutton bug fixed, ...

Revision 1.2  2001/02/20 13:25:16  honzam
Better search functions, bugfix on show on alias, constant definitions ...

*/
?>
