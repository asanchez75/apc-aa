<?php  //slice_id expected
/**
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
 * along with this program (LICENSE); if not, write tao the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @version   $Id: index.php3 2404 2007-05-09 15:10:58Z honzam $
 * @author    Honza Malik <honza.malik@ecn.cz>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Copyright (C) 1999, 2000 Association for Progressive Communications
 * @link      http://www.apc.org/ APC
 *
*/

// @todo only_action option which prints on the output the result of the action
// Then it could be used as AJAX call for this action!

require_once "../../include/init_page.php3";
require_once AA_INC_PATH . "varset.php3";
require_once AA_INC_PATH . "formutil.php3";
require_once AA_INC_PATH . "pagecache.php3";
require_once AA_INC_PATH . "item.php3";
require_once AA_INC_PATH . "manager.class.php3";
require_once AA_INC_PATH . "actions.php3";
require_once AA_BASE_PATH. "modules/polls/include/actions.php3";
require_once AA_BASE_PATH. "modules/polls/include/util.php3";

if ( !IfSlPerm(PS_EDIT_ALL_ITEMS) ) {
    MsgPage($sess->url(self_base())."index.php3", _m("You do not have permission to manage Polls"));
    exit;
}

$actions   = new AA_Manageractions;
$actions->addAction(new AA_Manageraction_Polls_MoveItem('Activate', 1));
$actions->addAction(new AA_Manageraction_Polls_MoveItem('Folder2',  2));
$actions->addAction(new AA_Manageraction_Polls_MoveItem('Folder3',  3));
$actions->addAction(new AA_Manageraction_Polls_DeleteTrash('DeleteTrashAction',true));

$switches  = new AA_Manageractions;

// no problem to write tabs as one action, but we use 3
$switches->addAction(new AA_Manageraction_Item_Tab('Folder1a', 'app'));      // we use the same action as for Items!
$switches->addAction(new AA_Manageraction_Item_Tab('Folder1b', 'appb'));     // we use the same action as for Items!
$switches->addAction(new AA_Manageraction_Item_Tab('Folder1c', 'appc'));     // we use the same action as for Items!
$switches->addAction(new AA_Manageraction_Item_Tab('Folder2',  'folder2'));  // we use the same action as for Items!
$switches->addAction(new AA_Manageraction_Item_Tab('Folder3',  'folder3'));  // we use the same action as for Items!
$switches->addAction(new AA_Manageraction_Polls_DeleteTrash('DeleteTrash',false));


// id of the editted module (id in long form (32-digit hexadecimal number))
$module_id = $slice_id;
// module_id is the same as slice_id (slice_id was used before AA introduced
// modules. Now it is better to use module_id, because in other modules
// (like Links, ...) it is not so confusing

$p_module_id = q_pack_id($module_id); // packed to 16-digit as stored in database

$metabase         = AA_Metabase::singleton();
$manager_settings = $metabase->getManagerConf('polls', $actions, $switches, $module_id);
$manager_settings['itemview']['aliases'] = GetPollsAliases();
$manager_settings['itemview']['format'] = array(
             'compact_top'      => '<table border="0" cellspacing="0" cellpadding="5">
                                        <tr class=tabtxt><th width="30">&nbsp;</th><th>'. _m('ID'). '</th><th>'. _m('Poll Question'). '</th><th>'. _m('Publish Date'). '</th><th>'. _m('Expiry Date'). '</th></tr>
             ',
             'category_sort'    => false,
             'category_format'  => "",
             'category_top'     => "",
             'category_bottom'  => "",
             'even_odd_differ'  => false,
             'even_row_format'  => "",
             'odd_row_format'   => '<tr><td width="30"><input type="checkbox" name="chb[x_#POLL_ID_]" value=""></td><td>_#POLL_ID_</td><td><a href="_#EDITPOLL">_#QUESTION</a></td><td>_#PUB_DATE</td><td>_#EXP_DATE</td></tr>
             ',
             'compact_remove'   => "",
             'compact_bottom'   => "</table>",
             'id'               => $module_id
          );
$manager = new AA_Manager($manager_settings);

$profile = new aaprofile($auth->auth["uid"], $module_id); // current user settings

// r_state array holds all configuration of Links Manager
// the configuration then could be Bookmarked
if ( !isset($r_state) OR $change_id OR ($r_state["module_id"] != $module_id)) {
    // we are here for the first time or we are switching to another slice
    unset($r_state);
    // set default admin interface settings from user's profile
    $r_state["module_id"]       = $module_id;
    $sess->register('r_state');

    $manager->setFromProfile($profile);
}

if ($r_state['manager']) {        // do not set state for the first time calling
    $manager->setFromState($r_state['manager']);
}

$manager->performActions();

$set = $manager->getSet();

// exact match - no SQL parsing (we added this because SQL Syntax parser in AA have problems with packed ids)
$set->addCondition(new AA_Condition('module_id', '==', $p_module_id));

// there is also one poll which acts as template - managed from Polls Admin
// (and not from the Polls Manager page) - it has status_code=0,
// so it is filtered out automaticaly

$now = now();
$bin_sets['app'] = clone($set);
$bin_sets['app']->addCondition(new AA_Condition('status_code', '=', '1'));
$bin_sets['app']->addCondition(new AA_Condition('expiry_date', '>=', $now));
$bin_sets['app']->addCondition(new AA_Condition('publish_date', '<=', $now));

$bin_sets['appb'] = clone($set);
$bin_sets['appb']->addCondition(new AA_Condition('status_code', '=', '1'));
$bin_sets['appb']->addCondition(new AA_Condition('publish_date', '>', $now));

$bin_sets['appc'] = clone($set);
$bin_sets['appc']->addCondition(new AA_Condition('status_code', '=', '1'));
$bin_sets['appc']->addCondition(new AA_Condition('expiry_date', '<', $now));

$bin_sets['folder2'] = clone($set);
$bin_sets['folder2']->addCondition(new AA_Condition('status_code', '=', '2'));

$bin_sets['folder3'] = clone($set);
$bin_sets['folder3']->addCondition(new AA_Condition('status_code', '=', '3'));

$r_state['bin_cnt']['app']     = AA_Metabase::queryCount(array('table'=>'polls'), $bin_sets['app']);
$r_state['bin_cnt']['appb']    = AA_Metabase::queryCount(array('table'=>'polls'), $bin_sets['appb']);
$r_state['bin_cnt']['appc']    = AA_Metabase::queryCount(array('table'=>'polls'), $bin_sets['appc']);
$r_state['bin_cnt']['folder2'] = AA_Metabase::queryCount(array('table'=>'polls'), $bin_sets['folder2']);
$r_state['bin_cnt']['folder3'] = AA_Metabase::queryCount(array('table'=>'polls'), $bin_sets['folder3']);

$zids  = AA_Metabase::queryZids(array('table'=>'polls'), $bin_sets[$manager->getBin()]);

$manager->printHtmlPageBegin(true);  // html, head, css, title, javascripts

require_once AA_BASE_PATH."modules/polls/include/menu.php3";
showMenu($aamenus, "pollsmanager", $manager->getBin());

$manager->display($zids);

$r_state['manager'] = $manager->getState();

HtmlPageEnd();
page_close();
?>