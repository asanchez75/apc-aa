<?php
/** Logging functions
 *
 *  @TODO Convert all loging into some class
 *        Enable setting of log level online
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
 * @package   Include
 * @version   $Id$
 * @author    Honza Malik <honza.malik@ecn.cz>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Copyright (C) 1999, 2000 Association for Progressive Communications
 * @link      http://www.apc.org/ APC
 *
*/

/** By $DO_NOT_LOG array you are able to specify, which events you don't want
 *  to log - it's just like filter
 *  This should be list of all logable events (at least now - 2005-11-9)
 */
$DO_NOT_LOG = array(
   // 'ALERTS',
   // 'BM_CREATE',
   // 'BM_DELETE',
   // 'BM_RENAME',
   // 'BM_UPDATE',
   // 'CSN',
   // 'CSV_IMPORT',
   // 'EMAIL_SENT',
   // 'FEED2ALL_0',
   // 'FEED2ALL_1',
   // 'FEED_ADD',
   // 'FEED_DEL',
   // 'FEED_DSBLE',
   // 'FEED_ENBLE',
   // 'FILE IMP:',
   // 'ITEM_FIELD_FILLED',
   // 'PAGECACHE',
      'TOEXECUTE'
);

/*
Events logged into AA log
type            selector                        parameters
-------------------------------------------------------------------------------
BM_CREATE       bookmark id in profile table    name
BM_UPDATE       bookmark id in profile table    name
BM_RENAME       bookmark id in profile table    new_name:old_name
BM_DELETE       bookmark id in profile table    name
EMAIL_SENT      bookmark id/LIST/TEST           users:valid_emails:emails_sent
TOEXECUTE       object's class                  return code
ALERTS          howoften                        Start/email sent
*/


class AA_Log {

    /** AA_Log::write function - Write log entry
     *  Static function
     *
     * @param $event
     * @param $params
     * @param $selector
     */
    function write($event, $params="", $selector="" ) {
        global $auth, $DO_NOT_LOG;

        if ( isset($DO_NOT_LOG[$event]) ) {
            return false;
        }

        $db = getDB();

        if (is_array($params)) {
            $params = ParamImplode($params);
        }

        $event    = addslashes($event);
        $params   = addslashes($params);
        $selector = addslashes($selector);

        $SQL = "INSERT INTO log (id, time, user, type, selector, params)
                         VALUES ('', '". time() ."','". $auth->auth["uid"] ."','$event','$selector','$params')";
        $db->query($SQL);
        freeDB($db);

        // with probability 1:1000 call log cleanup
        if ( rand(0,1000) == 1) {
            AA_Log::cleanup();
        }
        return true;
    }

    function cleanup() {
        $toexecute = new toexecute;
        // clean all older than 40 days
        $cleaner   = new AA_Log_Clenup(now() - (60*60*24*40));

        // we plan this tasks for future (tomorrow)
        // it should be enough to clean the logs once a day
        $time2execute   = now() + (60*60*24);
        $toexecute->laterOnce($cleaner, array(), "AA_Log_Clenup", 10, $time2execute);
    }
}

class AA_Log_Clenup {

    var $time;
    var $type;

    function AA_Log_Clenup($time, $type='') {
        $this->time = $time;
        $this->type = $type;
    }

    function toexecutelater() {
        $type_where = ( $this->type ) ? " AND type = '".quote($this->type)."' " : '';
        tryQuery("DELETE FROM log WHERE time < '".quote($this->time)."' $type_where");
    }
}

/** getLogEvents function
 *  Get events from log
 *  @param $event           - type of event
 *  @param $from            - events from date
 *  @param $to              - events to date
 *  @param $group_by_params - if true, returns events grouped by params and their count
 *                            as count
 *  @param $delete_old_logs -
 *  @param $selector
 */
function getLogEvents($event, $from="", $to="", $group_by_param=false, $delete_old_logs=false, $selector="") {

    $time = time();

    $like = (strpos($event, '%') !== false);

    // if "to" isn't set, we use time of query, because of saving log entries
    // written in (and after) query
    if ($to == "") {
        $to = $time;
    }

    if ($selector != "") {
        $slctr = " AND selector = '$selector'";
    }

    if ($group_by_param) {
        $SQL = "SELECT params,COUNT(*) AS count FROM log WHERE type". ($like ? " LIKE " : "=") ."'$event'";
        if ($from) {
            $SQL .= " AND time >= '$from'";
        }
        if ($to) {
            $SQL .= " AND time <= '$to'";
        }
        if ($slctr) {
            $SQL .= $slctr;
        }
        $SQL .= " GROUP BY params";
        $return = GetTable2Array($SQL, 'NoCoLuMn');
    } else {
        $SQL = "SELECT * FROM log WHERE type". ($like ? " LIKE " : "=") ."'$event'";
        if ($from) {
            $SQL .= " AND time >= '$from'";
        }
        if ($to) {
            $SQL .= " AND time <= '$to'";
        }
        if ($slctr) {
            $SQL .= $slctr;
        }
        // $SQL .= "ORDER BY TIME";
        $return = GetTable2Array($SQL, 'id');
    }

    // remove old log entries from table
    if ($delete_old_logs) {
        $SQL = "DELETE FROM log WHERE type='$event'";
        if ($from) {
            $SQL .= " AND time >= '$from'";
        }
        if ($to) {
            $SQL .= " AND time <= '$to'";
        }
        if ($slctr) {
            $SQL .= $slctr;
        }
        tryQuery($SQL);
    }

    return $return;

}


?>
