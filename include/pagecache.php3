<?php
/**
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


/** PageCache class used for caching informations into database
 *  uses table:
 *    CREATE TABLE pagecache (
 *      id varchar(32) NOT NULL,   (md5 crypted keystring used as database primary key (for quicker database searching)
 *      str2find text NOT NULL,    (string used to find record on manual invalidate cache record - could be keystring)
 *      content mediumtext,        (cached information)
 *      stored bigint,             (timestamp of information storing)
 *      flag int,                  (flag - not used for now)
 *     PRIMARY KEY (id), KEY (stored)
 *   );
 */

require_once AA_INC_PATH."varset.php3";

class PageCache  {
    var $cacheTime     = 600; // number of seconds to store cached informations

    /** PageCache function
     *  PageCache class constructor
     * @param $ct
     */
    function PageCache($ct = 600) {
        $this->cacheTime = $ct;
    }

    /** getKeyString function
     *  Private method - serialized specified global variables
     *  Returns keystring
     *  @param $keyVars - array of names of global variables which identifies cached
     *                    information
     */
    function getKeystring($keyVars) {
        foreach ( (array)$keyVars as $var ) {
            $ks .= $var."=".$GLOBALS[$var];
        }
        return $ks;
    }

    /** get function
     *  Returns cached informations or false
     * @param $keyString
     * @param $action
     */
    function get($keyString, $action='get') {
        if ( $GLOBALS['debug'] ) {
            huhl("<br>Pagecache->get(keyString):$keyString", '<br>Pagecache key:'.$this->getKeyId($keyString), '<br>Pagecache action:'.$action, 'Pagecach end' );
        }
        if ( ENABLE_PAGE_CACHE ) {
            if ( $action == 'invalidate' ) {
                $this->invalidateById( $this->getKeyId($keyString) );
                if ( $GLOBALS['debug'] ) {
                    huhl("<br>Pagecache: invlaidating");
                }
                return false;
            } elseif (is_numeric($action) ) {  // nocache=1
                if ( $GLOBALS['debug'] ) {
                    huhl("<br>Pagecache: return false - nocache");
                }
                return false;
            }
            return $this->getById( $this->getKeyId($keyString) );
        }
        return false;
    }

    /** cacheDb function
     *  Calls $function with $params and returns its return value. The result
     *  value is then stored into pagecache (database), so next call
     *  of the $function (also from another script) with the same parameters
     *  is returned from cache - function is not performed.
     *  Use this feature mainly for repeating, time consuming functions!
     *  You could use also object methods - then the $function parameter should
     *  be array (see http://php.net/manual/en/function.call-user-func.php)
     * @param $function
     * @param $params
     * @param $str2find
     * @param $action
     */
    function cacheDb($function, $params, $str2find, $action='get') {
        $keyString = (serialize($function).serialize($params));
        if ( $res = $this->get($keyString, $action) ) {
            return unserialize($res);  // it is setrialized for storing in the database
        }
        $res = call_user_func_array($function, $params);
        if (!is_numeric($action)) {  // nocache is not
            $this->store($keyString, serialize($res), $str2find);
        }
        return $res;
    }

    /** cacheMemDb function
     *  Look in memory (contentcache) for the result. If not found, use database
     *  (pagecache). The result is stored into memory as well as to the database
     * @param $function
     * @param $params
     * @param $str2find
     * @param $action
     */
    function cacheMemDb($function, $params, $str2find, $action='get') {
        global $contentcache;
        $keyString = serialize($function).serialize($params);
        if ($res = $contentcache->get($keyString)) {
            return $res;
        }
        $res = $this->cacheDb($function, $params, $str2find, $action);
        $contentcache->set($keyString,$res);
        return $res;
    }

    /** cacheMem function
     *  Wrapper for contentcache->get_result
     * @param $function
     * @param $params
     */
    function cacheMem($function, $params) {
        global $contentcache;
        return $contentcache->get_result( $function, $params );
    }

    /** getById function
     *  Get cache content by ID (not keystring)
     * @param $keyid
     */
    function getById($keyid) {
        $ret   = false;
        $db    = getDB();
        $SQL   = "SELECT * FROM pagecache WHERE id='$keyid'";
        $db->tquery($SQL);
        if ($db->next_record()) {
            if ( (time() - $this->cacheTime) < $db->f("stored") ) {
                $ret = $db->f('content');
            }
        }
        freeDB($db);
        return $ret;
    }

    /** getKeyId function
     *  Returns database identifier of the cache value (MD5 of keystring)
     * @param $keyString
     */
    function getKeyId($keyString) {
        return md5($keyString);
    }

    /** store function
     *  Cache informations based on $keyString
     *  Returns database identifier of the cache value (MD5 of keystring)
     * @param $keyString
     * @param $content
     * @param $str2find
     */
    function store($keyString, $content, $str2find) {
        global $cache_nostore;
        if ( $GLOBALS['debug'] ) {
            huhl("<br>Pagecache->store(keyString):$keyString", '<br>Pagecache key:'.$this->getKeyId($keyString), '<br>Pagecache str2find:'.$str2find->getStr2find(), '<br>Pagecache content (length):'.strlen($content), '<br>Pagecache cache_nostore:'.$cache_nostore );
        }
        if (ENABLE_PAGE_CACHE AND !$cache_nostore) {  // $cache_nostore used when
                                                      // {user:xxxx} alias is used
            $keyid  = $this->getKeyId($keyString);
            if ( $GLOBALS['debug'] ) {
                huhl("<br>Pagecache->store(): - storing");
            }
            $varset = new Cvarset( array( 'content' => $content,
                                          'stored'  => time()));
            $varset->addkey('id', 'text', $keyid);
            $str2find->store($keyid);

            // true replace mean it calls REPLACE command and no
            // SELECT+INSERT/UPDATE (which is better for tables with
            // autoincremented columns). There is no autoincrement, so we can
            // use true Replace
            // I'm trying to avoid problms with:
            //    Database error: Invalid SQL: INSERT INTO pagecache ...
            //    Error Number (description): 1062 (Duplicate entry '52e2804826c438a439cf301817c07020' for key 1)

            $varset->doTrueReplace('pagecache');  // true replace mean it calls REPLACE command and no SELECT+INSERT/UPDATE (which is better for tables with autoincremented columns, which is no

            // writeLog('PAGECACHE', $keyid.':'.serialize($str2find)); // for debug

            if (rand(0,PAGECACHEPURGE_PROBABILITY) == 1) {
                // purge only each PAGECACHEPURGE_PROBABILITY-th call of store
                $this->purge();
            }
        }
        return $keyid;
    }

    /** invalidateById function
     *  Remove specified ids from cache
     * @param $keys
     */
    function invalidateById( $keys ) {
        $keystring = join("','", (array)$keys);
        if ( $keystring != '' ) {
            $varset = new Cvarset();
            $varset->doDeleteWhere('pagecache', "id IN ('$keystring')", true);
            $varset->doDeleteWhere('pagecache_str2find', " pagecache_id IN ('$keystring')", true);
        }
    }

    /** purge function
     *  Clears all old cached data
     */
    function purge() {
        $tm   = time();
        $keys = GetTable2Array("SELECT id FROM pagecache WHERE stored<'".($tm - ($this->cacheTime))."'", '', 'id');
        $this->invalidateById( $keys );
    }

    /** invalidateFor function
     *  Remove cached informations for all rows which have the $cond in str2find
     * @param $cond
     */
    function invalidateFor($cond) {
        // We do not want to report errors here. Sometimes this SQL leads to:
        //   "MySQL Error: 1213 (Deadlock found when trying to get lock; Try
        //    restarting transaction)" error.
        // It is not so big problem if we do not invalidate cache - much less than
        // halting the operation.

        // writeLog('PAGECACHE', $cond, 'invalidate'); // for debug

        $keys = GetTable2Array("SELECT pagecache_id FROM pagecache_str2find WHERE str2find = '".quote($cond)."'", '', 'pagecache_id');

        // invalidateById() is quite slow - mainly if we have to delete mor rows
        // I do not know, how to make it quicker. I tried to refine the SQL
        // command, but following SQL do not help either:
        //
        //   DELETE pagecache, pagecache_str2find FROM pagecache, pagecache_str2find
        //    WHERE pagecache.id = pagecache_str2find.pagecache_id AND pagecache_str2find.str2find = '".quote($cond)."'";

        $this->invalidateById( $keys );
    }

    /** invalidate function
     *  Remove cached informations for all rows
     */
    function invalidate() {
        $db  = getDB();
        $SQL = "DELETE FROM pagecache";
        $db->query_nohalt($SQL);
        $SQL = "DELETE FROM pagecache_str2find";
        $db->query_nohalt($SQL);
        freeDB($db);
    }
}


/** CacheStr2find class - storage for str2find pairs used by pagecache
 *  to identify records to be deleted (invalidated) from cache
 */
class CacheStr2find {
    var $ids = array();   /** */
    /** CacheStr2find function
     * @param $ids
     * @param $type
     */
    function CacheStr2find( $ids=null, $type='slice_id') {
        $this->add($ids, $type);
    }

    /** add function
     *  Add ids (array) of specified type (common to all added ids)
     * @param $ids
     * @param $type
     */
    function add($ids, $type='slice_id') {
        if ( !$ids ) {
            return;
        }
        foreach ((array)$ids as $id) {
            $this->ids["$type=$id"] = true;   // match type-id pair
        }
    }
    /** add_str2find function
     * @param $str2find
     */
    function add_str2find($str2find) {
        if (strtolower(get_class($str2find)) == 'cachestr2find') {
            foreach ($str2find->ids as $k => $v) {
                $this->ids[$k] = true;   // copy all the $str2find ids to this
            }
        }
    }
    /** clear function
     *
     */
    function clear() {
        unset($this->ids);
        $this->ids = array();
    }
    /** store function
     * @param $keyid
     */
    function store($keyid) {
        $varset = new Cvarset( array( 'pagecache_id' => $keyid,
                                      'str2find'  => ''));
        $varset->doDeleteWhere('pagecache_str2find', "pagecache_id='$keyid'" );
        foreach ((array)$this->ids as $id => $v) {
            $varset->set('str2find', $id);
            $varset->doInsert('pagecache_str2find');
        }
    }
    /** getStr2find function
     *
     */
    function getStr2find() {
        $out = '';
        foreach ((array)$this->ids as $id => $v) {
            $out .= ",$id";
        }
        return $out;
    }
}

$GLOBALS['pagecache'] = new PageCache(CACHE_TTL);
?>
