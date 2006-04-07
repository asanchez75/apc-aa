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

//
// Functions for parsing XML documents used in item exchange system (like
// Cross-Server Netvorking (node2node feeding) and off-line filling)
//

define("WDDX_DUPLICATED", 1);
define("WDDX_BAD_PACKET", 2);

// is packet already stored in database?
function IsDuplicated( $packet, $db ) {
    if ( is_array($packet) || is_object($packet)) {
        $packet = serialize($packet);
    }
    $SQL = "SELECT * FROM offline WHERE digest='". md5($packet) ."'";
    $db->query($SQL);
    return ( $db->next_record() ? 1 : 0 );
}

// is packet already stored in database?
function RegisterItem( $id, $packet, $db ) {
    if (is_array($packet) || is_object($packet)) {
        $packet = serialize($packet);
    }
    $SQL = "INSERT INTO offline ( id, digest, flag ) VALUES ( '$id', '". md5($packet) ."', '' )";
    $db->query($SQL);
}

// gets one item stored in WDDX format and stored it in database
function StoreWDDX2DB( $packet, $slice_id, $fields, $bin2fill ) {
    global $db, $itemvarset, $varset;

    if (IsDuplicated($packet, $db)) {
        return WDDX_DUPLICATED;
    }

    $vals =  wddx_deserialize($packet);
    if (!$vals) {
        return WDDX_BAD_PACKET;
    }

    // update database
    $id = new_id();

    // prepare content4id array before call StoreItem function
    while (list($key,$val) = each($vals)) {
        if (isset($val) AND is_array($val)) {
            switch( $val[0] ) {   // field type - defines action to do with content
                case "base64":
                    $content4id[$key][0]['value'] = base64_decode($val[2]);
                    // $val[1] is filename - not used now
                    break;
                default:                           // store multiple values
                    reset($val);
                    $i=0;
                    while (list(,$v) = each($val)) {
                        $content4id[$key][$i]['value']   = $v;
                        $content4id[$key][$i++]['flag'] |= FLAG_OFFLINE;  // mark as offline filled
                    }
            }
        } else {                           // if not array - just store content
            $content4id[$key][0]['value'] = $val;
        }
        // set html flag from field default
        if ( $fields[$key]["html_default"] > 0 ) {
            $content4id[$key][0]['flag'] |= FLAG_HTML;
        }
        $content4id[$key][0]['flag'] |= FLAG_OFFLINE;      // mark as offline filled
    }

    // fill required fields if not set
    $content4id["status_code....."][0]['value'] = ($bin2fill==1 ? 1 : 2);
    if (!$content4id["post_date......."]) {
        $content4id["post_date......."][0]['value'] = time();
    }
    if (!$content4id["publish_date...."]) {
        $content4id["publish_date...."][0]['value'] = time();
    }
    if (!$content4id["expiry_date....."]) {
        $content4id["expiry_date....."][0]['value'] = time()+157680000;
    }
    if (!$content4id["last_edit......."]) {
        $content4id["last_edit......."][0]['value'] = time();
    }
    $content4id["flags..........."][0]['value'] = ITEM_FLAG_OFFLINE;

    StoreItem($id, $slice_id, $content4id, $fields, true, true, true);
                                      // insert, invalidatecache, feed
    RegisterItem(q_pack_id($id), $packet, $db);
    return WDDX_OK;
}

?>
