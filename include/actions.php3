<?php
/**
 * File contains definitions of functions which corresponds with actions
 * on Item Manager page (admin/index.php3) - manipulates with items
 *
 * Should be included to other scripts (admin/index.php3)
 *
 *   Move item to app/hold/trash based on param
 *  @param $status    static function parameter defined in manager action
 *                   in this case it holds bin number, where the items should go
 *  @param $item_arr array, where keys are unpacked ids of items prefixed by
 *                   'x' character (javascript purposes only)
 *  @param $akce_param additional parameter for the action - not used here
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
 * @version   $Id$
 * @author    Honza Malik <honza.malik@ecn.cz>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Copyright (C) 1999, 2000 Association for Progressive Communications
 * @link      http://www.apc.org/ APC
 *
*/

/** Item_MoveItem function
 * @param $status
 * @param $item_arr
 * @param $akce_param
 * @return false
 */
function Item_MoveItem($status, $item_arr, $akce_param) {
    global $event, $auth, $slice_id, $pagecache;
    $db = getDB();
    $now = now();
    foreach ( $item_arr as $it_id => $foo ) {
        $item_ids[] = pack_id(substr($it_id,1));      // remove initial 'x'
    }

    if ($item_ids) {
        $SQL = "UPDATE item SET
           status_code = $status,
           last_edit   = '$now',
           edited_by   = '". quote(isset($auth) ? $auth->auth["uid"] : "9999999999")."'";

        // E-mail Alerts
        $moved2active = ( ($status == 1) ? $now : 0 );
        $SQL .= ", moved2active = $moved2active";

        $SQL .= " WHERE id IN ('".join_and_quote("','",$item_ids)."')";
        $db->tquery($SQL);

        if ($status == 1) {
            foreach ($item_ids as $iid) {
                FeedItem(unpack_id128($iid));
            }
        }
        $event->comes('ITEMS_MOVED', $slice_id, 'S', $item_ids, $status );
    }
    $pagecache->invalidateFor("slice_id=$slice_id");  // invalidate old cached values

    freeDB($db);
    return false;                                     // OK - no error
}

/** Item_Feed function
 *  Export (Copy) items to another slice
 *  @param $slice      slice object - slice, from which we export
 *  @param $item_arr   array, where keys are unpacked ids of items prefixed by
 *                     'x' character (javascript purposes only)
 *  @param $akce_param Special string, where destination slices are coded.
 *                     The format is "<status>-<unpacked_slice_id>,<status>-.."
 * @return false or error message
 */
function Item_Feed($slice, $item_arr, $akce_param) {
    if (strlen($akce_param) < 1) {
        return _m('No slice selected');
    }
    $export_to = explode(",", $akce_param);          // <status>-<slice_id> pairs

    foreach ( $item_arr as $it_id => $foo ) {
        $it_id = substr($it_id,1);                 // remove initial 'x'
        foreach ( $export_to as $exp_slice_pair ) {
            list($status,$sid) = explode("-", $exp_slice_pair);
            FeedItemTo($it_id, $slice->unpacked_id(), $sid, ($status=='1' ? 'y':'n'), 0);
        }
    }
    return false;                                  // OK - no error
}

/** Item_Move2Slice function
 *  Move items to another slice
 *  @param $slice      slice object - slice, from which we export
 *  @param $item_arr   array, where keys are unpacked ids of items prefixed by
 *                     'x' character (javascript purposes only)
 *  @param $akce_param unpacked id of slice, where items should be moved
 */
function Item_Move2Slice($slice, $item_arr, $akce_param) {
    global $event, $auth, $slice_id, $pagecache;
    if (strlen($akce_param) < 1) {
        return _m('No slice selected');
    }

    if ( !IfSlPerm(PS_DELETE_ITEMS) ) {    // permission to delete items?
        return _m("You have not permissions to remove items");
    }

    $db = getDB();
    foreach ( $item_arr as $it_id => $foo ) {
        $item_ids[] = pack_id(substr($it_id,1));      // remove initial 'x'
    }

    if ($item_ids AND (strlen($akce_param) < 1)) {
        $p_dest_slice = pack_id($akce_param);
        $SQL = "UPDATE item SET slice_id = '$p_dest_slice' ".
               "WHERE id IN ('".join_and_quote("','",$item_ids)."')";

        // TODO set also moved2active flag according to status of the moved
        //       items (moved2active used by E-mail Alerts
        // $moved2active = ( ($status == 1) ? $now : 0 );
        // $SQL .= ", moved2active = $moved2active";
        $db->tquery ($SQL);

        // TODO: $event->comes('ITEMS_MOVED', $slice_id, 'S', $item_ids, $status );
    }
    $pagecache->invalidateFor("slice_id=$slice_id");    // invalidate old cached values
    $pagecache->invalidateFor("slice_id=$akce_param");  // invalidate old cached values

    freeDB($db);
    return false;                                     // OK - no error
}


/** Item_DeleteTrash function
 *  Handler for DeleteTrash switch - Delete all items in the trash bin
 *  @param $param       'selected' if we have to delete only items specified
 *                      in $item_arr - otherwise delete all items in Trash
 *  @param $item_arr    Items to delete (if 'selected' is $param)
 *  @param $akce_param  Not used
 * @return false or error message
 */
function Item_DeleteTrash($param, $item_arr, $akce_param) {
    global $pagecache, $slice_id, $event;
    $db = getDB();

    if ( !IfSlPerm(PS_DELETE_ITEMS) ) {    // permission to delete items?
        return _m("You have not permissions to remove items");
    }

    $wherein = '';

    // restrict the deletion only to selected items
    if ($param == 'selected') {
        $items_to_delete = array();
        foreach ( $item_arr as $it_id => $foo ) {
            $items_to_delete[] = pack_id(substr($it_id,1));      // remove initial 'x'
        }
        if (count($items_to_delete) < 1) {
            freeDB($db);
            return;
        }
        $wherein = " AND id IN ('".join_and_quote("','", $items_to_delete)."')";
    }

    // now we ask, which items we have to delete. We are checking the items even
    // it is specified in $item_arr - for security reasons - we can delete only
    // items in current slice and in trash
    $db->query("SELECT id FROM item
               WHERE status_code=3 AND slice_id = '". q_pack_id($slice_id) ."' $wherein");
    $items_to_delete = array();
    while ( $db->next_record() ) {
        $items_to_delete[] = $db->f("id");
    }
    if (count($items_to_delete) < 1) {
        freeDB($db);
        return;
    }

    // mimo enabled -- problem?
    $event->comes('ITEMS_BEFORE_DELETE', $slice_id, 'S', $items_to_delete);

    // delete content of all fields
    // don't worry about fed fields - content is copied
    $wherein = "IN ('".join_and_quote("','", $items_to_delete)."')";
    $db->query("DELETE FROM content WHERE item_id ".$wherein);
    $db->query("DELETE FROM item WHERE id ".$wherein);

    $pagecache->invalidateFor("slice_id=$slice_id");
    freeDB($db);
}

/** Item_Tab function
 * Handler for Tab switch - switch between bins
 * @param $value
 * @param $param
 */
function Item_Tab($value, $param) {
    global $manager;
    $GLOBALS['r_state']['bin'] = $value;
    $manager->go2page(1);
}

?>
