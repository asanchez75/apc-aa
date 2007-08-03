<?php
/**
 * File contains definitions of functions which corresponds with actions
 * on Item Manager page (admin/index.php3) - manipulates with central_confs
 *
 * Should be included to other scripts (admin/index.php3)
 *
 *   Move central_conf to app/hold/trash based on param
 *  @param $status    static function parameter defined in manager action
 *                   in this case it holds bin number, where the central_confs should go
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
 * @version   $Id: actions.php3 2404 2007-05-09 15:10:58Z honzam $
 * @author    Honza Malik <honza.malik@ecn.cz>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Copyright (C) 1999, 2000 Association for Progressive Communications
 * @link      http://www.apc.org/ APC
 *
*/

require_once AA_INC_PATH."linkcheck.class.php3";

/** AA_Manageraction - Item manager actions. Just create new class and assign 
 *  it to your manager
 */
class AA_Manageraction {
    
    var $id;
    
    /** constructor - assigns identifier of action */
    function AA_Manageraction($id) {
        $this->id = $id;
    }
    
    /** Name of this Manager's action */
    function getName() {}

    /** Name of this Manager's action */
    function getId()         { return $this->id; }
    
    /** Should this action open new window? And if so, which one? */
    function getOpenUrl()    { return false; }
    
    /** Any addition to url */
    function getOpenUrlAdd() { return false; }
    
    /** main executive function
    * @param $manager    - back link to the manager   
    * @param $state      - state array
    * @param $param     
    * @param $item_arr  
    * @param $akce_param
    */
    function perform(&$manager, &$state, $item_arr, $akce_param) {
    }
    
    /** Checks if the user have enough permission to perform the action */
    function isPerm(&$manager) {
        return true;
    }
}

class AA_Manageractions {
    /** set of AA_Manageraction s */
    var $actions;
    
    function AA_Manageractions() {
        $this->actions = array();
    }
    
    function getAction($id) {
        return isset($this->actions[$id]) ? $this->actions[$id] : false;
    }
    
    /** We unfortunately need this function, because in manager.class.php3 
     *  we have to loop through all switches and the Iterator is not available
     *  for PHP4
     */
    function &getArray() {
        return $this->actions;
    }
    
    function addAction($action) {
        return $this->actions[$action->getId()] = $action;
    }
}

/** AA_Manageraction_Central_Linkcheck - checks if the AA are acessible */
class AA_Manageraction_Central_Linkcheck extends AA_Manageraction {
    
    /** Name of this Manager's action */
    function getName() {
        return _m('Check the AA availability');
    }
    
    /** main executive function
    * @param $param       - not used
    * @param $item_arr    - array of id of AA records to check
    * @param $akce_param  - not used
    */
    function perform(&$manager, &$state, $item_arr, $akce_param) {
        $item_ids = array_keys($item_arr);
    
        if (count($item_ids)<1) {
            return false;                                     // OK - no error
        }
        
        $db  = getDB();    
        $SQL = "SELECT * FROM central_conf WHERE id IN ('".join_and_quote("','",$item_ids)."')";
        $db->tquery($SQL);
        $results[] = array('<b>'._m('AA (Organization)').'</b>', '<b>'._m('URL').'</b>', '<b>'._m('Status code').'</b>', '<b>'._m('Description').'</b>');
        $linkcheck = new linkcheck();
    
        while ($db->next_record()) {
            $url       = $db->f('AA_HTTP_DOMAIN'). $db->f('AA_BASE_DIR'). "view.php3";
            $status    = $linkcheck->check_url($url);
            $results[] = array($db->f('ORG_NAME'), $url, $status['code'], $status['comment']);
        }
        
        freeDB($db);
        return GetHtmlTable($results). "<br>";                                     // OK - no error
    }
    
    /** Checks if the user have enough permission to perform the action */
    function isPerm(&$manager) {
        return  IsSuperadmin();
    }
}

/** AA_Manageraction_Central_Sqlupdate - Runs sql_update.php3 script on selected 
 *  AAs
 */
class AA_Manageraction_Central_Sqlupdate extends AA_Manageraction {

    /** Name of this Manager's action */
    function getName() {
        return _m('Update database (sql_update)');
    }
    
    /** main executive function
    * @param $param       - not used
    * @param $item_arr    - array of id of AA records to check
    * @param $akce_param  - not used
    */
    function perform(&$manager, &$state, $item_arr, $akce_param) {
        $item_ids = array_keys($item_arr);
    
        if (count($item_ids)<1) {
            return false;                                     // OK - no error
        }
        set_time_limit(360);
        $db  = getDB();
        
        $SQL = "SELECT * FROM central_conf WHERE id IN ('".join_and_quote("','",$item_ids)."')";
        $db->tquery($SQL);
        $ret = '';
        while ($db->next_record()) {
            $params   = 'dbpw5='.substr($db->f('db_pwd'),0,5).'&fire=1&dbcreate=on&copyold=on&backup=on&replacecateg=on&replaceconst=on&newcore=on&templates=on&view_templates=on&addstatistic=on&additemidfields=on&fixmissingfields=on&update_modules=on&cron=on&generic_emails=on&links_create=on&update=Run+Update';
            $file     = $db->f('AA_HTTP_DOMAIN'). $db->f('AA_BASE_DIR'). "sql_update.php3?$params";
            $response = file_get_contents($file);
            $toggle   = '{htmltoggle:&gt;&gt;:'.AA_Stringexpand::quoteColons($db->f('AA_HTTP_DOMAIN')).':&lt;&lt;:'. AA_Stringexpand::quoteColons($response).'}';
            $ret     .= AA_Stringexpand::unalias($toggle);
        }
        freeDB($db);
        return $ret;                                     // OK - no error
    }
    
    /** Checks if the user have enough permission to perform the action */
    function isPerm(&$manager) {
        return IsSuperadmin();
    }
}

/** AA_Manageraction - Item manager actions. Just create new class and assign 
 *  it to your manager
 */
class AA_Manageraction_Central_MoveItem extends AA_Manageraction {
    
    /** specifies, to which bin the move should be performed */
    var $to_bin;

    /** Constructor - fills the information about the target bin */
    function AA_Manageraction_Central_MoveItem($id, $to_bin) {
        $this->to_bin = $to_bin;
        parent::AA_Manageraction($id);
    }
    
    /** Name of this Manager's action */
    function getName() {
        switch($this->to_bin) {
            case 1: return _m('Move to Active');
            case 2: return _m('Move to Holding bin');
            case 3: return _m('Move to Trash');
        }
        return "";
    }

    /** main executive function
    * @param $param       - not used
    * @param $item_arr    - array of id of AA records to check
    * @param $akce_param  - not used
    */
    function perform(&$manager, &$state, $item_arr, $akce_param) {
        $item_ids = array_keys($item_arr);

        if ($item_ids) {
            $SQL = "UPDATE central_conf SET status_code = '".$this->to_bin."'
                     WHERE id IN ('".join_and_quote("','",$item_ids)."')";
            tryQuery($SQL);
        }
        return false;                                     // OK - no error
    }
    
    /** Checks if the user have enough permission to perform the action */
    function isPerm(&$manager) {
        $current_bin     =  $manager->getBin();
        
        /** for acces to Central you have to be superadmin */
        if (!IsSuperadmin()) {
            return false;
        }
        
        switch($this->to_bin) {
            case 1: return ($current_bin != 'app' ) AND
                           ($current_bin != 'appb') AND
                           ($current_bin != 'appc');
                    // Folder2 is Holding bin - prepared for more than three bins
            case 2: return ($current_bin != 'hold');
                    // Folder3 is Trash
            case 3: return ($current_bin != 'trash');
        }
    }
}
                                    
/** AA_Manageraction_Central_DeleteTrash - Handler for DeleteTrash switch 
 *  Delete all AAs in the trash bin 
 */
class AA_Manageraction_Central_DeleteTrash extends AA_Manageraction {

    /** specifies, if we have to delete only items specified in $item_arr
     *  otherwise delete all items in Trash 
     *  With $selected=true  it is used as "action" of manager
     *  With $selected=false it is used as "switch" of manager (left menu)
     */
    var $selected;

    /** Constructor - fills the information about the target bin */
    function AA_Manageraction_Central_DeleteTrash($id, $selected=false) {
        $this->selected = $selected;
        parent::AA_Manageraction($id);
    }
    
    /** Name of this Manager's action */
    function getName() {
        return _m('Remove (delete from database)');
    }
    
    /** main executive function
     *  @param $param       'selected' if we have to delete only items specified
     *                      in $item_arr - otherwise delete all items in Trash
     *  @param $item_arr    Items to delete (if 'selected' is $param)
     *  @param $akce_param  Not used
     */
    function perform(&$manager, &$state, $item_arr, $akce_param) {
        if ( !isSuperadmin() ) {    // permission to delete items?
            return _m("You have not permissions to remove items");
        }
        
        $wherein = '';
    
        // restrict the deletion only to selected items
        if ($this->selected) {
            if (!is_array($item_ids)) {
                return false;
            }

            $items_to_delete = array();
            $item_ids        = array_keys($item_arr);
            if (count($items_to_delete) < 1) {
                return false;
            }
            $wherein = " AND id IN ('".join_and_quote("','", $items_to_delete)."')";
        }
    
        $db = getDB();
        
        // now we ask, which items we have to delete. We are checking the items even
        // it is specified in $item_arr - for security reasons - we can delete only
        // items in current slice and in trash
        $db->query("SELECT id FROM central_conf WHERE status_code=3 $wherein");
        $items_to_delete = array();
        while ( $db->next_record() ) {
            $items_to_delete[] = $db->f("id");
        }
        if (count($items_to_delete) < 1) {
            freeDB($db);
            return;
        }
    
        // delete content of all fields
        // don't worry about fed fields - content is copied
        $wherein = "IN ('".join_and_quote("','", $items_to_delete)."')";
        $db->query("DELETE FROM central_conf WHERE id ".$wherein);
        freeDB($db);
    }
    
    /** Checks if the user have enough permission to perform the action */
    function isPerm(&$manager) {
        // if we want to use it as "action" (not "switch"), then we should be in trash bin
        return (IsSuperadmin() AND (!$this->selected OR ($manager->getBin() == 'trash')));
    }
}


/** AA_Manageraction_Central_Tab - Swith to another bin in Manager */
class AA_Manageraction_Central_Tab extends AA_Manageraction {
    
    /** specifies, to which bin we want to switch */
    var $to_bin;

    /** Constructor - fills the information about the target bin */
    function AA_Manageraction_Central_Tab($id, $to_bin) {
        $this->to_bin = $to_bin;
        parent::AA_Manageraction($id);
    }

    /** main executive function - Handler for Tab switch - switch between bins */
    function perform(&$manager, &$state, $item_arr, $akce_param) {
        $manager->setBin($this->to_bin);
        $manager->go2page(1);
    }
    
    /** Checks if the user have enough permission to perform the action */
    function isPerm(&$manager) {
        return IsSuperadmin();
    }
}

?>
