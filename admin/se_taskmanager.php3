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

/** Test Task */
/*
class AA_Task_Test {

    function __construct() {
    }

    function toexecutelater() {
        // synchronize accepts array of sync_actions, so it is possible
        // to do more action by one call
        echo time(). " ";
        sleep(25);
        return "OK" ;
    }
}

$toexecute = new AA_Toexecute;
$test_task = new AA_Task_Test();
$toexecute->userQueue($test_task, array(), 'AA_Task_Test');
*/

// we do not manage more "modules" here, so unique id is OK

$actions   = new AA_Manageractions;
$actions->addAction(new AA_Manageraction_Taskmanager_Execute('ExecuteTaskAction'));
$actions->addAction(new AA_Manageraction_Taskmanager_Delete('DeleteTaskAction'));

//$switches  = new AA_Manageractions;  // we do not need switches here

$metabase  = AA_Metabase::singleton();

$manager_settings = $metabase->getManagerConf('toexecute', $actions);
$manager_settings['itemview']['format']['compact_top'] = '
                                          <table border="0" cellpadding="5" cellspacing="0">
                                            <tbody><tr>
                                              <th width="30">&nbsp;</th>
                                              <th>id</th>
                                              <th>created</th>
                                              <th>aa_user</th>
                                              <th>priority</th>
                                              <th>selector</th>
                                              <th>params</th>
                                            </tr>';
$manager_settings['itemview']['format']['odd_row_format'] = '
                                      <tr><td width="30"><input name="chb[x_#ID______]" value="" type="checkbox"></td>
                                        <td>_#ID______</td>
                                        <td>_#CREATED_</td>
                                        <td>_#AA_USER_</td>
                                        <td>_#PRIORITY</td>
                                        <td>_#SELECTOR</td>
                                        <td>_#PARAMS__</td>
                                    </tr>';                      // <td>_#OBJECT__</td><td>_#EXECUTE_</td>

$manager = new AA_Manager('task'.$auth->auth['uid'],  $manager_settings);
$manager->performActions();

$set  = $manager->getSet();
$set->addCondition(new AA_Condition('execute_after', '=', TOEXECUTE_USER_TASK_TIME));
$set->addCondition(new AA_Condition('aa_user',       '=', $auth->auth['uid']));
$zids = AA_Metabase::queryZids(array('table'=>'toexecute'), $set);

require_once AA_INC_PATH."menu.php3";

$manager->displayPage($zids, 'sliceadmin', 'taskmanager');
page_close();
?>