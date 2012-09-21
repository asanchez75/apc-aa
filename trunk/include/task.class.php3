<?php
/**
 * File contains definition of inputform class - used for displaying input form
 * for item add/edit and other form utility functions
 *
 * Should be included to other scripts (as /admin/itemedit.php3)
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
 * @version   $Id: task.class.php3 2800 2009-04-16 11:01:53Z honzam $
 * @author    Honza Malik <honza.malik@ecn.cz>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Copyright (C) 1999, 2000 Association for Progressive Communications
 * @link      http://www.apc.org/ APC
 *
*/


/** Task executed at planed time
 */
class AA_Plannedtask extends AA_Object {

    protected $task = '';

    /** allows storing form in database
     *  AA_Object's method
     */
    function getClassProperties() {
        return array (          //           id       name       type        multi  persist validator, required, help, morehelp, example
            'task'    => new AA_Property( 'task', _m("Task"),         'text', false, true, '', true),
            'time'    => new AA_Property( 'time', _m("Time to run"),  'string', false, true, '', true, _m('Specify the time, when the task shoud be executed. It will be then procesed periodicaly at this time. The specification of the time should be in "<a href="http://www.php.net/manual/en/datetime.formats.relative.php">Relative Format</a>", so the time like:<br>"midnight" - runs every midnight <br>"+1 hour" - runs every hour, <br>"+30 min" - runs every 30 minutes, <br>"16:00" - runs every day at 16:00<br>The times are not exact, the tasks are performed one after another by the script, which runs every 5 minutes, or so.'))
            );
    }

    // static function factoryFromForm($oowner, $otype=null)        ... could be redefined here, but we use the standard one from AA_Object
    // static function getForm($oid=null, $owner=null, $otype=null) ... could be redefined here, but we use the standard one from AA_Object


    function nexttime() {
        // every 5 min
        return strtotime($this->getProperty('time'));
    }

    function toexecutelater() {
        AA_Stringexpand::unalias($this->task);
    }
}



/** Used as object for toexecute - updates item.display_count and hit_archive
 *  based on hit log in hit_short_id and hit_long_id tables
 *  It also plans the the hit_x..... field counting into toexecute queue
 */
class AA_Plannedtask_Schedule {

    /** updateDisplayCount - updates item.display_count and hit_archive based
     *  on hit log in hit_short_id and hit_long_id tables
     *                     - it also plans the the hit_x..... field counting
     *                       into toexecute queue
     */
    function toexecutelater() {

        $aa_set = new AA_Set();
        //$aa_set->setModules($module_id);
        //$aa_set->addCondition(new AA_Condition('aa_user',       '=', $auth->auth['uid']));

        $zids  = AA_Object::querySet('AA_Plannedtask', $aa_set);
        $toexecute = new AA_Toexecute;

        foreach ($zids as $id) {
            $task = AA_Object::load($id, 'AA_Plannedtask');
            $time = $task->nexttime();
            if ($time >= time()) {
                $toexecute->laterOnce($task, array(), "Plannedtask_$id", 100, $task->nexttime());
            }
        }
    }
}



?>
