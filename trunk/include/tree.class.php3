<?php
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
 * along with this program (LICENSE); if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package   UserInput
 * @version   $Id: tree.class.php3 2551 2007-12-05 18:49:34Z honzam $
 * @author    Honza Malik <honzam.malik@ecn.cz>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Copyright (C) 1999, 2000 Association for Progressive Communications
 * @link      http://www.apc.org/ APC
 *
*/


class AA_Supertree {
    protected $_i;        // Array of items
    protected $_relation_field;  //
    protected $_sort;     // sort array().
                          // Currently wors only for Reverse trees. @todo
    protected $_modules;  // Array of modules

    protected $_restrict_slices;  // Array of allowed slices

    function __construct($relation_field, $sort=null, $slices=array()) {
        $this->_relation_field  = $relation_field;
        $this->_sort            = $sort;
        $this->_i               = array();
        $this->_modules         = array();
        $this->_restrict_slices = $slices;
    }

    /** load function
     * @param $force
     */
    function load( $id ) {
        if (isset($this->_i[$id])) {
            return;
        }

        /** items, which are already in trash, or expired, ... */
        $invalid       = array();

        $new_subitems  = array($id);
        while (1) {
            $content4ids   = GetItemContent($new_subitems, false, false, array('id..............','slice_id........', $this->_relation_field), null, AA_BIN_ACTIVE);

            $invalid = array_merge($invalid, array_diff($new_subitems, array_keys($content4ids)));

            $new = array();
            if (is_array($content4ids) ) {
                foreach ($content4ids as $item_id => $columns) {
                    $sid = $columns['u_slice_id......'][0]['value'];
                    if (count($this->_restrict_slices) AND !in_array($sid, $this->_restrict_slices)) {
                        $invalid = array_merge($invalid, array($item_id));
                        continue;
                    }
                    // mark module_id
                    $this->_modules[$sid] = true;
                    $next  = $this->_compactValue($columns[$this->_relation_field]);
                    $new   = array_merge($new,$next);
                    $this->_i[$item_id] = $next;
                }
            }

            $new_subitems = array_diff($new, array_keys($this->_i));
            if ( count($new_subitems) < 1 ) {
                break;
            }
        }

        // remove all links to invalid items
        if (count($invalid) > 0) {
            foreach ($this->_i as $item_id => $next) {
                $this->_i[$item_id] = array_diff($next, $invalid);
            }
        }
        return;
    }

    /** returns ids in array - ids are in tree order (walked into deep) */
    function getIds($id) {
        $this->load($id);
        $ids = $this->_subIds($id, 1);
        return $ids;
    }

    function _subIds($id, $level=1) {
        if ($level > 100) {
            // @todo throw error
            return array();
        }
        $sub = array($id);
        foreach($this->_i[$id] as $down_id) {
            $sub = array_merge($sub, $this->_subIds($down_id, $level+1));
        }
        return $sub;
    }

    function getMenu($ids, $current_ids, $code) {
        $ret = '';
        $xid = end($current_ids);

        foreach($ids as $mid) {
            if ($item = AA_Items::getItem(new zids($mid, 'l'))) {
                if ($menu_txt = trim($item->subst_alias($code))) {
                    if (in_array($mid, $current_ids)) {
                        $this->load($mid);
                        $sub  = empty($this->_i[$mid]) ? '' : $this->getMenu($this->_i[$mid], $current_ids, $code);
                        $ret .= '  <li id="menu-'.$mid.'" class="inpath'.($mid==$xid  ? ' active':'').'">';
                        $ret .= $menu_txt. $sub;
                        $ret .= "</li>\n";
                    } else {
                        $ret .= "  <li id=\"menu-$mid\">$menu_txt</li>\n";
                    }
                }
            }
        }
        return ($ret ? "\n<ul>\n$ret</ul>\n" : '');
    }

    /** returns ids in array - ids are in tree order (walked into deep) */
    function getTreeString($id) {
        $this->load($id);
        $ids = $this->_subTreeString($id, 1);
        return $ids;
    }

    function _subTreeString($id, $level) {
        if ($level > 100) {
            // @todo throw error
            return '';
        }
        $treestring = '';
        $delim      = '';
        foreach($this->_i[$id] as $down_id) {
            $treestring .= $delim. $this->_subTreeString($down_id, $level+1);
            $delim       = '-';
        }
        return $id. (empty($treestring) ? '' : "($treestring)");
    }

    function _compactValue($value_arr) {
        $ret = array();
        if (is_array($value_arr)) {
            foreach ( $value_arr as $fld_content ) {
                if ( !empty($fld_content['value']) ) {
                    $ret[] = $fld_content['value'];
                }
            }
        }
        return $ret;
    }
}

/** The same as AA_Supertree, but it holds reversed tree - tree construced not
 *  as parent->childrens but children->parent. The diferrence is the direction,
 *  the relation field points.
 *  !! The Reverse Tree is limitted to one relation slice only !! - @todo - fix
 *  It is the same - we just change the way, how to construct the tree.
 */
class AA_Supertree_Reverse extends AA_Supertree {

    /** load function
     * @param $force
     */
    function load( $id ) {
        if (isset($this->_i[$id])) {
            return;
        }

        if (!count($this->_restrict_slices)) {
            $zid = new zids($id,'l');
            $sid = $zid->getFirstSlice();
            //huhl($this->_restrict_slices,$zid,$sid,$this);
            if (!$sid) {
                return;
            }
            $this->_restrict_slices = array($sid);
        }
        $this->_modules = array_fill_keys($this->_restrict_slices, true);

        /** items, which are already in trash, or expired, ... */
        $invalid  = array();
        $queue    = array($id);

        // prepare cond in order we can be as quick as possible
        $cond[$this->_relation_field] = 1;
        $cond['operator'] = '=';

        while (count($queue)) {

            $item_id  = array_pop($queue);
            if (isset($this->_i[$item_id])) {
                continue;
            }

            $cond['value'] = $item_id;
            $zids          = QueryZIDs($this->_restrict_slices, array($cond), $this->_sort);

            $next    = $zids->longids();
            $this->_i[$item_id] = $next;

            $queue   = array_merge($queue,$next);
        }
        return;
    }
}


class AA_Trees {
    /** parent->child trees */
    var $a   = array();

    /** reverse - child->parent trees */
    var $rev = array();

    /** Constructor  */
    function __construct() {
        $this->a   = array();
    }

    /** singleton function
     *  called as AA_Trees::singleton() from getTree() method;
     *  This function makes sure, there is global instance of the class
     *  @todo  convert to static class variable (after migration to PHP5)
     */
    function singleton() {
        static $instance = null;
        if (is_null($instance)) {
            // Now create the AA_Trees object
            $instance = new AA_Trees;
        }
        return $instance;
    }

    /** getTree function
     *  main factory static method
     * @param $id
     */
    function getTreeString($id, $relation_field, $reverse=false, $sort=null, $slices=null) {
        $supertree = AA_Trees::getSupertree($relation_field, $reverse, $sort, $slices);
        return $supertree->getTreeString($id);
    }

    /** getTree function
     *  main factory static method
     * @param $id
     */
    function getIds($id, $relation_field, $reverse=false, $sort=null, $slices=null) {
        $supertree = AA_Trees::getSupertree($relation_field, $reverse, $sort, $slices);
        return $supertree->getIds($id);
    }

    function getSupertree($relation_field, $reverse, $sort, $slices=array()) {
        $trees = AA_Trees::singleton();
        $key   = get_hash($relation_field, $reverse, $sort, $slices);
        if (!isset($trees->a[$key])) {
            $trees->a[$key] = $reverse ? new AA_Supertree_Reverse($relation_field, $sort, $slices) : new AA_Supertree($relation_field, $sort, $slices);
        }
        return $trees->a[$key];
    }
}

?>