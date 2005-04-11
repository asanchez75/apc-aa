<?php
/**
 * Event class - this class invokes various event handlers based on input
 * event. Basicaly - AA allways should call event class instance, if any
 * event ocures (new item, item changed, ...). The event istance will look
 * into a table, if any handler waits for the event. If so, all matched
 * handlers are called.
 *
 * @package UserInput
 * @version $Id$
 * @author Honza Malik <honza.malik@ecn.cz>, Econnect
 * @copyright (c) 2002-3 Association for Progressive Communications
*/
/*
Copyright (C) 1999-2003 Association for Progressive Communications
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

require_once $GLOBALS["AA_INC_PATH"]."auth.php3";
require_once $GLOBALS["AA_INC_PATH"]."mailman.php3";
require_once $GLOBALS["AA_INC_PATH"]."mail.php3";
require_once $GLOBALS["AA_BASE_PATH"]."modules/alerts/event.php3";
//mimo add
require_once $GLOBALS["AA_INC_PATH"]."mlx.php";


/**
 * aahandler class - stores handler function and 'trigger' conditions for
 * the function invoking
 */
class aahandler {
    var $funct;    // handler function - function to call is conditions are met
    var $conds;

    function aahandler( $funct, $conds ) {
        $this->funct = $funct;
        $this->conds = $conds;
    }

    function matches( $type, $slice, $slice_type ) {
        if ( !(isset($this->conds) AND is_array($this->conds)) )
            return true;   // no conditions are set for handler - do it allways
        foreach ( $this->conds as $condition => $value ) {
            if ( $$condition != $value ) {
                return false;
            }
        }
        return $this->funct;       // all defined conditions matches
    }
}

/**
 * aaevent class - stores list of aahandlers. If event comes, aa calls 'comes'
 * method. aaevent object then looks for handles, which wait for the event
 * and invoke handler funtion for each.
 */
class aaevent {
    var $handlers = 'not_filled';    // array of aahandler objects
    var $returns;                    // array of return values of last event

    /** Main event function - called when any event ocures. The method then
     *  search all handlers and calls all that matches all criteria
     *
     *  @param string $type        - event type identifier
     *  @param string $slice       - slice id, where event occures
     *  @param string $slice_type  - type of the slice, where event occures
     *                              ('S' for slice, 'Links' for links, ...)
     *  @param mixed  &$ret_params - event parameters which could be modified
     *                               by handler
     *  @param mixed  $params      - event parameters - static different for
     *                               each event $type (mainly new values)
     *  @param mixed  $params2     - event parameters - static different for
     *                               each event $type (mainly old values)
     */
    function comes($type, $slice, $slice_type, &$ret_params, $params='', $params2='') {
        unset($this->returns);
        if ( $this->handlers == 'not_filled' )
            $this->get_handlers();
        if ( !(isset($this->handlers) AND is_array($this->handlers)) )
            return false;
        foreach ( $this->handlers as $handler ) {
            $function = $handler->matches($type, $slice, $slice_type);

            // matches and function begins with 'Event_' - security check
            if ( $function AND (substr($function, 0, 6) == 'Event_') ) {
                $this->returns[] = $function($type, $slice, $slice_type, $ret_params, $params, $params2);
            }
        }
    }

    /** Fills the handlers array from database */
    function get_handlers() {
        // TODO - read the events from database instead of this static definition
        $this->handlers   = array();
        $this->handlers[] = new aahandler('Event_ItemsBeforeDelete',    array('type' => 'ITEMS_BEFORE_DELETE',   'slice_type' => 'S'));  // all slices
        $this->handlers[] = new aahandler('Event_ItemsMoved',           array('type' => 'ITEMS_MOVED',           'slice_type' => 'S'));  // all slices
        $this->handlers[] = new aahandler('Event_ItemBeforeUpdate',     array('type' => 'ITEM_BEFORE_UPDATE',    'slice_type' => 'S'));  // all slices
        $this->handlers[] = new aahandler('Event_ItemBeforeInsert',     array('type' => 'ITEM_BEFORE_INSERT',    'slice_type' => 'S'));  // all slices
        $this->handlers[] = new aahandler('Event_ItemAfterInsert',      array('type' => 'ITEM_NEW',              'slice_type' => 'S'));  // all slices
        $this->handlers[] = new aahandler('Event_ItemAfterUpdate',      array('type' => 'ITEM_UPDATED',          'slice_type' => 'S'));  // all slices
        $this->handlers[] = new aahandler('Event_ConstantBeforeUpdate', array('type' => 'CONSTANT_BEFORE_UPDATE','slice_type' => 'S'));  // all slices
        $this->handlers[] = new aahandler('Event_ConstantUpdated',      array('type' => 'CONSTANT_UPDATED',      'slice_type' => 'S'));  // all slices
        $this->handlers[] = new aahandler('Event_AddLinkGlobalCat',     array('type' => 'LINK_NEW',              'slice_type' => 'Links'));
        $this->handlers[] = new aahandler('Event_AddLinkGlobalCat',     array('type' => 'LINK_UPDATED',          'slice_type' => 'Links'));
        $this->handlers[] = new aahandler('Event_ItemUpdated_DropIn',   array('type' => 'ITEM_UPDATED',     'slice'        => 'c7a5b60cf82652549f518a2476d0d497'));  // dropin poradna
        $this->handlers[] = new aahandler('Event_ItemUpdated_DropIn',   array('type' => 'ITEM_NEW',         'slice'        => 'c7a5b60cf82652549f518a2476d0d497'));  // dropin poradna
    }
}


/** ------------- Handlers --------------*/

/** Called after inserting a new item.
*   $itemContent is sent by reference but for better performance only.
*/
function Event_ItemAfterInsert( $type, $slice_id, $slice_type, &$itemContent, $foo, $foo2 ) {
    $item_id = $itemContent->getItemID();
    AuthUpdateReaders( array( pack_id( $item_id )), $slice_id );
    AlertsSendWelcome( $item_id, $slice_id, $itemContent );
//    AlertsSendInstantAlert( $item_id, $slice_id );
    MailmanCreateSynchroFiles ($slice_id);

    // notifications
    switch ($itemContent->getStatusCode()) {
        case SC_ACTIVE:      email_notify($slice_id, 3, $item_id); break;
        case SC_HOLDING_BIN: email_notify($slice_id, 1, $item_id); break;
    }
    return true;
}

/** Called after updating an existing item.
*   $itemContent is sent by reference but for better performance only.
*/
function Event_ItemAfterUpdate( $type, $slice_id, $slice_type, &$itemContent, $oldItemContent, $foo2 ) {
    $item_id = $itemContent->getItemID();
    AuthUpdateReaders( array( pack_id( $item_id )), $slice_id );
//    AlertsSendInstantAlert( $item_id, $slice_id );
    MailmanCreateSynchroFiles ($slice_id);

    // notifications
    switch ($itemContent->getStatusCode()) {
        case SC_ACTIVE:      email_notify($slice_id, 4, $item_id); break;
        case SC_HOLDING_BIN: email_notify($slice_id, 2, $item_id); break;
    }
    return true;
}

/** Called on updating an existing item.
*   @param object $itemContent is sent by reference - you can change the data
*/
function Event_ItemBeforeUpdate( $type, $slice_id, $slice_type, &$itemContent, $oldItemContent, $foo2 ) {
    $item_id = $itemContent->getItemID();
    // Delete reader from Auth tables because if the username changes,
    // AuthUpdateReaders can not recognize it.
    AuthDeleteReaders( array( pack_id( $item_id)), $slice_id );
    return true;
}

/** Called on inserting a new item.
*   @param object $itemContent is sent by reference - you can change the data
*/
function Event_ItemBeforeInsert( $type, $slice_id, $slice_type, &$itemContent, $foo, $foo2 ) {
    return true;
}

/** Called on deleting several items.
*   @param object $item_ids is sent by reference but for better performance only
*/
function Event_ItemsBeforeDelete( $type, $slice_id, $slice_type, &$item_ids, $foo, $foo2 ) {
    /* It is not really necessary to delete the readers from Auth tables,
       because they should be deleted on moving to Trash bin. But it is
       perhaps better to make sure. */
    AuthDeleteReaders( $item_ids, $slice_id );
    MailmanCreateSynchroFiles ($slice_id);
    //mimo added
    $mlx = new MLXEvents();
    $mlx->itemsBeforeDelete($item_ids,$slice_id);
    return true;
}

/** Called after moving items to another bin (changing status code). */
function Event_ItemsMoved( $type, $slice_id, $slice_type, &$item_ids, $new_status, $foo2 ) {
    AuthUpdateReaders( $item_ids, $slice_id );
    MailmanCreateSynchroFiles( $slice_id );
}

/** Called on propagating a change in a constant value.
 *  @param string $newvalue, $oldvalue Both have added slashes (e.g. from a form).
 *  @param string $constant_id Unpacked ID of constant from the constant table.
 */
function Event_ConstantBeforeUpdate( $type, $slice_id, $slice_type, &$newvalue, $oldvalue, $constant_id ) {
    return true;
};

/** Called after propagating a change in a constant value.
*   @param string $newvalue, $oldvalue Both have added slashes (e.g. from a form).
*   @param string $constant_id Unpacked ID of constant from the constant table.
*/
function Event_ConstantUpdated( $type, $slice_id, $slice_type, &$newvalue, $oldvalue, $constant_id ) {
    AuthChangeGroups ($constant_id, $oldvalue, $newvalue);
    MailmanConstantsChanged( $constant_id, $oldvalue, $newvalue );
}


/** Creates 'general' categories (if not created yet) when new link type belongs
 *  to 'global categories'. Then it modifies category set, where to assign link
 *
 *  @param array  &$ret_params  - category set, where to assign link - modified
 *                                array[] = category_id
 *  @param string  $params      - global category name or false
 *  @param string  $params2     - old (previous) global category name or false
 */
function Event_AddLinkGlobalCat( $type, $slice, $slice_type, &$ret_params, $params, $params2) {
    global $db, $LINK_TYPE_CONSTANTS;

    // quite Econnectonous code
    $name    = $params;              // name of general category is in params
    $oldname = $params2;             // name of old general category in params2

    // if new link type is not general (global) category or general category
    // was already set - return
    if( !( trim($name)) OR trim($oldname) )
        return false;

    // get all informations about general categories
    $SQL = "SELECT pri, description, name FROM constant
             WHERE group_id='$LINK_TYPE_CONSTANTS'
               AND value='$name'";
    $db->tquery($SQL);
    if( $db->next_record() ) {
        $general_cat = $db->Record;
    } else {
        return false;    // not general category - do not modify category set
    }

    // category translations are stored in 'description' field of constants
    // the format of translations are: 1,2-1,2,1224:1,2,4-1,2,4,42:...
    // which means - do not store to category 1,2 but to the category 1,2,1224

    // Example: 1,2-1,2,1223:1,2,4-1,2,4,43:1,2,983-1,2,983,1226:1,2,984-1,2,984,1229:1,2,985-1,2,985,1232:1,2,986-1,2,986,1235:1,2,987-1,2,987,1238
    $trans_string = str_replace( array("\n","\t","\r",' '), '', $general_cat['description'] );
    $translations = explode(':', $trans_string);             // parse
    if ( isset($translations) AND is_array($translations) ) {
        foreach ( $translations as $k ) {
            list($from,$to) = explode('-', $k);
            $trans[$from] = $to;
        }
    }

    // get categories in which we have to create global category
    // = categories and all subcategories - 1,2,33,88 => 1,2; 1,2,33; 1,2,33,88
    $final_categories = array();     // clear return categories
    if ( isset($ret_params) AND is_array($ret_params) ) {
        foreach( $ret_params as $cid ) {
            $cpath = GetCategoryPath( $cid );
            if ( substr($cpath, 0, 4) != '1,2,' ) {
                // category is not in 'Kormidlo' => do not add link to
                // subcategories, but only to category itself
                $final_categories[] = $cid;
                continue;
            }
            $cat_on_path = explode(',', $cpath);
            $curr_path = '';
            $i=0;
            unset($reverse_cat);
            unset($reverse_path);
            foreach ( $cat_on_path as $subcat ) {
                $curr_path .= ( $i ? ',' : ''). $subcat;  // Create path
                if ( $i++ ) {                             // Skip first level
                    $reverse_cat[]  = $subcat;
                    $reverse_path[] = $curr_path;         // There we have to
                }                                         // add general categ.
            }
            // created categories are in wrong order - we need the deepest
            // category as first
            for ( $j = count($reverse_cat)-1; $j>=0 ; $j-- ) {
                $subcategories[$reverse_cat[$j]] = $reverse_path[$j];
            }
        }
    }
    // Now we have $subcategories[] (before translation), where link and global
    // category will be added AND $final_categories[] with other categories,
    // where we want to add link (without translation or global cat. creation)

    // go through desired categories and translate it, if we have to
    if ( isset($subcategories) AND isset($trans) ) {
        foreach( $subcategories as $cid => $path ) {
            foreach( $trans as $from => $to ) {
                if ( $path == $from ) {        // translate this category
                    $subcat_translated[GetCategoryFromPath($to)] = $to;
                    continue 2;  // next subcategory
                }
            }
            $subcat_translated[$cid] = $path;  // no need to translate
        }
    } else {
        $subcat_translated = $subcategories;   // translation is no defined
    }

    // So, finaly create categories, if not created yet and build the result
    // category list
    $ret_params = $final_categories;  // add non-Kormidlo categories
    if (!$subcat_translated AND count($ret_params)<=0) {
        return false;       // no category to assign - seldom case
    }

    $tree = new cattree( $db );

    // we have $subcat_translated[] AND $ret_params[] already prefilled
    foreach ( $subcat_translated as $cid => $path ) {
        $sub_cat_id = $tree->subcatExist($cid, $name);
        if ( !$sub_cat_id AND ($tree->getName($cid) != $name) ) {
            $sub_cat_id = Links_AddCategory($name, $cid, $path);
            Links_AssignCategory($cid, $sub_cat_id, $general_cat['pri']);
        }
        // result set of categories to assign link
        $ret_params[] = $sub_cat_id ? $sub_cat_id : $cid;
    }
    return true;
}

/** Send email with answer to Dropin staff with the answer (from item)
 *  @param object  &$ret_params  - ItemContent object with new values
 */
function Event_ItemUpdated_DropIn( $type, $slice, $slice_type, &$ret_params, $params, $params2) {
    global $db;

    $short_id = $ret_params->getValue('short_id........');              // item's short_id is in params
    $email    = trim($ret_params->getValue('con_email......1'));
    $otazka   = trim($ret_params->getValue('abstract.......1'));
    $odpoved  = trim($ret_params->getValue('abstract.......2'));
    $send     = trim($ret_params->getValue('switch.........2'));

    if ( $email AND $otazka AND $odpoved AND (($send == 'on') OR ($send == '1')) ) {
        $item = GetItemFromId(new zids($short_id, 's'));
        return send_mail_from_table_inner (8, $email, $item) > 0 ;
    }
    return false;
}

?>