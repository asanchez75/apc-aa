<?php  //slice_id expected
/**
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
 * @version   $Id: index.php3 2404 2007-05-09 15:10:58Z honzam $
 * @author    Honza Malik <honza.malik@ecn.cz>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Copyright (C) 1999, 2000 Association for Progressive Communications
 * @link      http://www.apc.org/ APC
 *
*/

// @todo only_action option which prints on the output the result of the action
// Then it could be used as AJAX call for this action!

require_once "../include/init_page.php3";
require_once AA_INC_PATH . "varset.php3";
require_once AA_INC_PATH . "formutil.php3";
require_once AA_INC_PATH . "pagecache.php3";
require_once AA_INC_PATH . "item.php3";
require_once AA_INC_PATH . "manager.class.php3";
require_once AA_INC_PATH. "actions.php3";
require_once AA_BASE_PATH. "central/include/actionapps.class.php";

if ( !IsSuperadmin() ) {
    MsgPage($sess->url(self_base())."index.php3", _m("You do not have permission to manage ActioApps tasks"));
    exit;
}

// we do not manage more "modules" here, so unique id is OK

$actions   = new AA_Manageractions;
$actions->addAction(new AA_Manageraction_Taskmanager_Delete('DeleteTaskAction'));
$actions->addAction(new AA_Manageraction_Taskmanager_Execute('ExecuteTaskAction'));


//$switches  = new AA_Manageractions;  // we do not need switches here
$module_id = 'toexecute';

//$metabase  = new AA_Metabase;
//$metabase->loadFromDb();
//echo serialize($metabase);
//exit;

//echo $metabase->getDefinition();

$metabase         = AA_Metabase::singleton();

$manager_settings = $metabase->getManagerConf('toexecute', $actions);

//huh($manager_settings);

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

// need for menu
//$r_state['bin_cnt'] = CountItemsInBins();

$manager->printHtmlPageBegin(true);  // html, head, css, title, javascripts

require_once AA_INC_PATH."menu.php3";
showMenu($aamenus, "sliceadmin", "taskmanager");

$conds = $manager->getConds();
$sort  = $manager->getSort();

$zids  = AA_Metabase::queryZids(array('table'=>'toexecute'), $conds, $sort, 'app');

$manager->printSearchbarBegin();
$manager->printSearchbarEnd();   // close the searchbar form

$manager->printAndClearMessages();
PrintArray($r_err);
PrintArray($r_msg);
unset($r_err);
unset($r_msg);

$manager->printItems($zids);   // print links and actions
$r_state['manager'] = $manager->getState();

HtmlPageEnd();
page_close();
?>