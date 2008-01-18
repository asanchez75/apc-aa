<?php
/**
 * Class ItemContent.
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
 * @package   UserInput
 * @version   $Id: item_content.php3 2410 2007-05-10 14:39:37Z honzam $
 * @author    Jakub Adamek, Econnect
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Copyright (c) 2002-3 Association for Progressive Communications
 * @link      http://www.apc.org/ APC
 *
*/

require_once AA_INC_PATH.  "toexecute.class.php3";

/**
 * Hits logged to two temporary tables - hit_short_id (for short item ids) and
 * hit_long_id (for long item ids). With COUNTHIT_PROBABILITY
 * (eg. default 1000 ==> probability 0.001) we recount logged hits into table
 * item and move the hits to hit_archive table. hit_archive is then used for
 * making statistics - like "10 the most read items in last week, ..."
 *
 * The statistics is quite powerfull - it is counted only for the slices, where
 * you add the hit_1..........., hit_7........... or hit_30.......... field.
 * The hit_1.. field holds display count one day back, hit_7.. one week and
 * hit_30.. one month (no problem to add new field like hit_14, or hit_100 if
 * you need another time period). The statistics counting is quite demanding
 * task - you need to update all the items in the slice. That's why we use
 * periodical updater with different period for each field: Currently the time
 * periods are:
 *    hit_1  (day)   plan +/- 5   minutes
 *    hit_7  (week)  plan +/- 35  minutes
 *    hit_30 (month) plan +/- 150 minutes
 * For the "statistics counting" task we use toexecute table, so if you have
 * problems with statistics, check, if the script misc/toexecute.php3 is runned
 * form the aa cron (see AA -> Cron admin page)
 *
 * Why we use this approach? MySQL lock the item table for updte when someone do
 * a search in that table. If we want to view any fulltext, we can't, because we
 * have to wait for item.display_count update (which is locked). That's why we
 * log the hit into temporary table and from time to time
 * (with probability 1:1000) we update item table based on logs.
 *
 * Spliting into three tables we make increase the speed of the database
 * operations, which are often used in this case
 *
 * @param string $id    id - short, long
 */
class AA_Hitcounter {

    /** Stores one item hot to temporary table and with some probability
     *  invokes the updateDisplayCount() method.
     *
     *  Static class function
     *
     *  We use two temporary tables - hit_short_id (for short item ids) and
     *  hit_long_id (for long item ids).
     */
    function hit($zids) {
        if (!is_object($zids) OR $zids->is_empty()) {
            return;
        }

        // do not count hits from Bots
        $agent = strtolower($_SERVER["HTTP_USER_AGENT"]);
        if ((false !== strpos($agent, 'bot')) OR
            (false !== strpos($agent, 'crawl')) OR
            (false !== strpos($agent, 'check')) OR
            (false !== strpos($agent, 'spider')) OR
            (false !== strpos($agent, 'download'))
            ) {
            return;
        }

        $varset = new CVarset;
        $varset->add('time', 'number', now());
        $varset->add('agent', 'text', $_SERVER["HTTP_USER_AGENT"]);
        $varset->add('info', 'text', $_SERVER["REQUEST_URI"]);
        if ($zids->use_short_ids()) {
            $varset->add('id', 'number', $zids->id(0));
            $varset->doInsert('hit_short_id');
        } else {
            $varset->add('id', 'unpacked', $zids->id(0));
            $varset->doInsert('hit_long_id');
        }

        // it is not necessary to check, if the  AA_Hitcounter_Update is planed
        // on each hit. We check it only once for 1000 (COUNTHIT_PROBABILITY)
        if ( rand(0,COUNTHIT_PROBABILITY) == 1) {
            $display_counter  = new AA_Hitcounter_Update();
            $toexecute        = new AA_Toexecute;
            $toexecute->laterOnce($display_counter, array(), 'AA_Hitcounter_Update', 100, now() + 300);  // run it once in 5 minutes
//            $toexecute->laterOnce($display_counter, array(), 'AA_Hitcounter_Update', 100, now() + 3000);  // run it once in 50 minutes
        }
        return;
    }
}

/** Used as object for toexecute - updates item.display_count and hit_archive
 *  based on hit log in hit_short_id and hit_long_id tables
 *  It also plans the the hit_x..... field counting into toexecute queue
 */
class AA_Hitcounter_Update {

    /** updateDisplayCount - updates item.display_count and hit_archive based
     *  on hit log in hit_short_id and hit_long_id tables
     *                     - it also plans the the hit_x..... field counting
     *                       into toexecute queue
     */
    function toexecutelater() {

        // we can't count with current second, since the records for current
        // second could grow. Two seconds back should be OK.
        $time = now() - 2;

        $counts = array();

        // first look to the short id hit table;
        $hits_s = GetTable2Array("SELECT id, count(*) as count FROM hit_short_id WHERE time < $time GROUP BY id", 'id', 'count');

        // now look for long ids hits
        $hits_l = GetTable2Array("SELECT item.short_id, count(*) as count FROM hit_long_id INNER JOIN item ON hit_long_id.id=item.id
                                   WHERE hit_long_id.time < $time GROUP BY item.short_id", 'short_id', 'count');

        if (is_array($hits_s)) {
            foreach ( $hits_s as $short_id => $count ) {
                // add long ids count
                if ( isset($hits_l[$short_id]) ) {
                    $count += $hits_l[$short_id];
                    unset($hits_l[$short_id]);
                }
                if ( $count > 0 ) {
                    tryQuery( "UPDATE item SET display_count=(display_count+$count) WHERE short_id = $short_id");
                }
            }
        }

        // Now the rest long_ids
        if (is_array($hits_l)) {
            foreach ( $hits_l as $short_id => $count ) {
                if ( $count > 0 ) {
                    tryQuery( "UPDATE item SET display_count=(display_count+$count) WHERE short_id = $short_id");
                }
            }
        }

        tryQuery("INSERT INTO hit_archive (id, time) SELECT id, time FROM hit_short_id WHERE time < $time");
        tryQuery("DELETE FROM hit_short_id WHERE time < $time");

        tryQuery("INSERT INTO hit_archive (id, time) SELECT item.short_id, hit_long_id.time FROM hit_long_id INNER JOIN item ON hit_long_id.id=item.id WHERE time < $time");
        tryQuery("DELETE FROM hit_long_id WHERE time < $time");

        // once a day plan 3 new grouping (which means, that each day is counted
        // 3 times - in case it fails in one or two cases. If it fails three
        // times it is not so big problem - we can plan(0) sometime in the future
        $grouper = new AA_Hitcounter_Group;
        $grouper->plan(3);

        $this->updateDisplayStatistics();
    }

    /** Plan task for counting statistics fields (hit_1, hit_7, ...)
     *  It is planed as AA_Toexecute jobs
     */
    function updateDisplayStatistics() {
        $stats2count = GetTable2Array("SELECT id, slice_id FROM field WHERE slice_id <> 'AA_Core_Fields..' AND id LIKE 'hit_%'", '');

        if (is_array($stats2count)) {

            $toexecute = new AA_Toexecute;
            $timeshift = 0;
            foreach ($stats2count as $to_count) {
                $count_slice_id = unpack_id($to_count['slice_id']);
                $field_id       = $to_count['id'];
                $stats_counter  = new AA_Hitcounter_Stats($count_slice_id, $field_id);

                // we plan this tasks for future
                // hit_1  (day)   plan +/- 50   minutes later
                // hit_7  (week)  plan +/- 350  minutes later (5,8 hours)
                // hit_30 (month) plan +/- 1500 minutes later (25 hours)
                $time2execute   = now() + ($stats_counter->getDays() * 300 * (10 + $timeshift++));
                $toexecute->laterOnce($stats_counter, array(), "Count_". $count_slice_id.'_'.$field_id, 100, $time2execute);
            }
        }
    }
}

/** Updates the hit_x..... fields for all items from the slice based on the data
 *  in hit_archive table.
 */
class AA_Hitcounter_Stats {

    /** For which slice we will cout the hits */
    var $slice_id;

    /** For which field we will cont the hits (hit_1...., hit_2...) */
    var $field_id;

    function AA_Hitcounter_Stats($slice_id, $field_id) {
        $this->slice_id = $slice_id;
        $this->field_id = $field_id;
    }

    function toexecutelater() {
        $days        = $this->getDays();
        $time        = now() - ($days * 86400);
        $qp_slice_id = q_pack_id($this->slice_id);
        $hits        = GetTable2Array("SELECT item.id, sum(hits) as count FROM hit_archive INNER JOIN item ON hit_archive.id=item.short_id
                                        WHERE hit_archive.time > $time AND item.slice_id = '$qp_slice_id' GROUP BY hit_archive.id", 'id', 'count');
        $item_ids    = GetTable2Array("SELECT id FROM item WHERE slice_id = '$qp_slice_id'", '', 'id');

        $db = getDb();
        if ( is_array($item_ids) ) {
            // shuffle - if there are a lot of items, so we reach timelimit, it is better to
            // count hits in random order, so each item will be counted after some time period
            shuffle($item_ids);
            $field = GetTable2Array("SELECT * FROM field WHERE slice_id = '$qp_slice_id' AND id='". quote($this->field_id) ."'", 'aa_first', 'aa_all');
            foreach ($item_ids as $id) {
                $db->query("DELETE FROM content WHERE item_id ='".quote($id)."' AND field_id = '". quote($this->field_id)."'");
                $value = isset($hits[$id]) ? $hits[$id] : 0;
                StoreToContent(unpack_id($id), $field, array('value'=>$value, 'flag'=>0));
            }
        }
        freeDb($db);
    }

    function getDays() {
        return (int) substr($this->field_id, 4);
    }
}

/** Groups data in hit_archive table
 *  We store all the hits into the hit_archive table. It is good for counting
 *  hit_1..., ... field statistics as well as for item display statistics.
 *  On the other hand the amount of data is huge, so we group the hit count for
 *  item on day basis (for older hits)
 **/
class AA_Hitcounter_Group {

    function toexecutelater($begin, $step) {
        $varset = new CVarset;
        $time_cond = "time >= $begin AND time < ". ($begin + $step);
        $hits      = GetTable2Array("SELECT id, sum(hits) as sum FROM hit_archive WHERE $time_cond GROUP BY id", 'id', 'sum');

        tryQuery("DELETE FROM hit_archive WHERE $time_cond");
        if (is_array($hits)) {
            foreach ($hits as $id => $sum ) {
                $varset->clear();
                $varset->add('id',   'number', $id);
                $varset->add('hits', 'number', $sum);
                $varset->add('time', 'number', $begin);
                $varset->doInsert('hit_archive');
            }
        }
    }

    /** plan grouping tasks to toexecute queue
     *  - by default we plan all the task
     */
    function plan($max_task = 0) {
        $oldest         = GetTable2Array("SELECT min( time ) as oldest FROM `hit_archive`", 'aa_first', 'oldest');
        $grouping_start = mktime(0, 0, 0, date("m")  , date("d")-8, date("Y")); // week ago
        $step           = 60 * 60 * 24;                        // day

        $toexecute        = new AA_Toexecute;
        // run for all the days from $oldest to week ago
        $no_task = 0;
        for ($begin = $grouping_start; $begin + $step >= $oldest; $begin = $begin - $step) {
            $toexecute->laterOnce($this, array($begin, $step), "AA_Hitcounter_Group_$begin", 40, now() + 60*60*24); // once a day

            // by default we plan all the task
            if (++$no_task == $max_task) {
                break;
            }
        }
    }
}

?>
