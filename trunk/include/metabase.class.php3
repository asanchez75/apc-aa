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
 * @version   $Id: menu_util.php3 2357 2007-02-06 12:03:49Z honzam $
 * @author    Honza Malik <honza.malik@ecn.cz>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Copyright (C) 1999, 2000 Association for Progressive Communications
 * @link      http://www.apc.org/ APC
 *
*/

/** AA_Metabase holds the database structure of AA
 *  The database structure in metabase is used for
 *    1) creating database
 *    2) updating the database structure
 *    3) constructing queries to database with data type checking
 *
 *  Inner structure looks like (generated with getDefinition() method):
 *
 *       'central_conf' => array(
 *           'id' => array(
 *                0 => "id",                         // 'Field'
 *                1 => "int(10) unsigned",           // 'Type'
 *                2 => "NO",                         // 'Null'
 *                3 => "PRI",                        // 'Key'
 *                4 => "0",                          // 'Default'
 *                5 => "auto_increment",             // 'Extra'
 *                6 => "AA identifier",              // 'Comment'
 *           ),
 *           'dns_conf' => array(
 *               'Field'   => "dns_conf",
 *               'Type'    => "varbinary(255)",
 *               'Null'    => "NO",
 *           ),
 *           ...
 */


class AA_Metabase_Column {

    /** Column definition array
     *             0     1    2     3      4       5       6
     *  array ( field, type, null, key, default, extra, comment )
     *
     *  The reason, why we store it in array is, that the metabase is here
     *  stored as serialized string and I want to keep it as short as possible
     */
    var $c;

    function AA_Metabase_Column($column) {
        $this->c = array( $column['Field'], $column['Type'], $column['Null']=='YES', $column['Key'], $column['Default'], $column['Extra'], $column['Comment'] );
    }

    function isKey() {
        return strpos($this->c[3], 'PRI')!==false;
    }

    function getCreateSql() {
        $SQL  = '`'.  $this->c[0] .'`';                     // column name
        $SQL .= ' '.  $this->c[1];                          // column definition
        if (!$this->c[2]) {                                 // NULL ?
            $SQL .= ' NOT NULL';
        }
        if (strlen($this->c[4]) > 0) {
            // look for keywords for default
            if ( $this->c[4] === 'CURRENT_TIMESTAMP' ) {
                // we ignore it, because DEFAULT CURRENT_TIMESTAMP is
                // not supported in MySQL < 4.1.x and it is not needed for
                // MySQL > 4.1.x, because standard settings of any timestamp
                // column is:
                //     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            } elseif ( $this->c[4] === 'NULL' ) {
                $SQL .= " default ". $this->c[4];     // default
            } else {
                $SQL .= " default '". $this->c[4] ."'";     // default
            }
        }
        $SQL .= ' '.  $this->c[5];                          // extra - like auto_increment
        return $SQL;
    }

    /** returns database structure definition as PHP code (array)
     *  not used, yet (and question is, if ever) */
    function getDefinition() {
        $ret  = "\n        '".$this->c[0]."' => array(";
        if ($this->c[0]) { $ret .= "\n            'Field'   => \"".$this->c[0].'",';  }
        if ($this->c[1]) { $ret .= "\n            'Type'    => \"".$this->c[1].'",';  }
        if ($this->c[2]) { $ret .= "\n            'Null'    => \"".($this->c[2] ? 'YES' : 'NO').'",';  }
        if ($this->c[3]) { $ret .= "\n            'Key'     => \"".$this->c[3].'",';  }
        if ($this->c[4]) { $ret .= "\n            'Default' => \"".$this->c[4].'",';  }
        if ($this->c[5]) { $ret .= "\n            'Extra'   => \"".$this->c[5].'",';  }
        if ($this->c[6]) { $ret .= "\n            'Comment' => \"".$this->c[6].'"';   }
        $ret .= "\n        )";
        return $ret;
    }
}

class AA_Metabase_Index {
    /**
     *  The reason, why we use this short variables is, that the metabase is here
     *  stored as serialized string and I want to keep it as short as possible
     */
    var $t; // table name
    var $n; // index name
    var $s; // sort of index P|U|I  (= PRIMARY|UNIQUE|INDEX)
    var $c; // columns array (array of column names of array (name,part) for partial column index (like text(10))

    function AA_Metabase_Index($table, $name, $sort) {
        $this->t = $table;
        $this->n = $name;
        $this->s = $sort;
        $this->c = array();
    }

    function addColumn($position, $column, $sub_part='') {
        $this->c[(int)$position] = $sub_part ? array($column, $sub_part) : $column;
    }

    function getCreateSql() {
        $cols = array();
        foreach ($this->c as $col) {
            $cols[] = is_array($col) ? '`'.$col[0].'`('.$col[1].')' : "`$col`";
        }
        $cols_list = join(',', $cols);
        switch ($this->s) {
            case 'P': return "PRIMARY KEY ($cols_list)";
            case 'U': return 'UNIQUE KEY '.$this->n." ($cols_list)";
        }
        return 'KEY '.$this->n." ($cols_list)";
    }
}


class AA_Metabase_Table {
    /** Name of the table */
    var $tablename;
    /** array of PRIMARY KEY columns */
    var $primary_key;
    /** array of INDEXES: array(index_name => AA_Metabase_Index) */
    var $index;
    /** array of table columns */
    var $column;
    /** array of table flags - like ENGINE=InnoDB, DEFAULT CHARSET=cp1250 */
    var $flags;

    // This is temporary solution - we will use some better structure (MDB2?)
    // for table definition in order we can check the field type,
    // the indexes, generate sql_update script, ...
    function AA_Metabase_Table($tablename, $columns, $indexes='') {
        $this->tablename   = $tablename;
        $this->column      = array();
        $this->primary_key = array();
        $this->index       = array();
        foreach ($columns as $column) {
            $aa_column  = new AA_Metabase_Column($column);
            $this->column[$column['Field']] = new AA_Metabase_Column($column);
            if ($aa_column->isKey()) {
                $this->primary_key[$column['Field']] = true;
            }
        }
        if (!empty($indexes)) {
            // indexes array looks like:
            // [0] => Array (
            //      [Table] => email [Non_unique] => 0 [Key_name] => PRIMARY [Seq_in_index] => 1 [Column_name] => id [Collation] => A [Cardinality] => 71 [Sub_part] => [Packed] => [Null] => [Index_type] => BTREE [Comment] => )
            foreach ($indexes as $index_part) {
                if ( !isset($this->index[$index_part['Key_name']])) {
                    $index_type = ($index_part['Key_name'] == 'PRIMARY') ? 'P' : (($index_part['Non_unique']) ? 'I': 'U' );
                    $this->index[$index_part['Key_name']] = new AA_Metabase_Index($index_part['Table'], $index_part['Key_name'], $index_type);
                }
                $idx = &$this->index[$index_part['Key_name']]; // to work in php4
                $idx->addColumn((int)$index_part['Seq_in_index'], $index_part['Column_name'], $index_part['Sub_part']);
            }
        }
    }

    function factoryFromDb($tablename) {
        $columns = GetTable2Array("SHOW FULL COLUMNS FROM `$tablename`", 'Field');
        $indexes = GetTable2Array("SHOW INDEX FROM `$tablename`", '');
        return new AA_Metabase_Table($tablename, $columns, $indexes);
    }

    function getColumnNames() {
        return array_keys($this->column);
    }

    /** Is the $columnname the column in this table? */
    function isColumn($columnname) {
        return isset($this->column[$columnname]);
    }

    function getKeys() {
        return array_keys($this->primary_key);
    }

    function isKey($columnname) {
        return ($this->primary_key[$columnname] ? true : false);
    }

    function getCreateSql($prefix='') {
        $sql_parts = array();
        foreach ($this->column as $column) {
            $sql_parts[] = $column->getCreateSql();
        }
        foreach ($this->index as $index) {
            $sql_parts[] = $index->getCreateSql();
        }
        return "CREATE TABLE IF NOT EXISTS `$prefix".$this->tablename."` (\n". join(",\n",$sql_parts) ."\n)";
    }

    /** returns database structure definition as PHP code (array) */
    function getDefinition() {
        $defs = array();
        foreach ($this->column as $column) {
            $defs[] = $column->getDefinition();
        }
        $ret  = "\n    '". $this->tablename ."' => array(";
        $ret .= join(",", $defs);
        $ret .= "\n    )";
        return $ret;
    }


    /** setFromSql function
     *  Fills AA_Metabase_Table structure from the result of SQL command:
     *     SHOW CREATE TABLE $tablename
     * @param $tablename
     * @param $create_SQL
     */
    function setFromSql($tablename, $create_SQL) {
        $this->tablename = $tablename;
        foreach (explode("\n", $create_SQL) as $row) {
            $row = trim($row);
            // first row - CREATE TABLE - no need to grab anything from it
            if ( strpos($row, 'CREATE TABLE') === 0 ) {
                continue;
            }
            // field definition row - grab it
            if ( (strpos($row, 'KEY') === 0) OR
                 (strpos($row, 'UNIQUE KEY') === 0) OR
                 (strpos($row, 'PRIMARY KEY') === 0) ) {
                $this->_setIndexFromSql($row);
                continue;
            }
            if ( strpos($row, ')') === 0 ) {
                $this->_setFlagFromSql($row);
                continue;
            }
            // else urecognized row
            echo $row;
        }
    }
}

/** @todo convert to static class variables after move to PHP5 */
class AA_Metabase {
    var $tables;

    /** AA_Metabase function - constructor
     *  Do not use it - use $metabase = AA_Metabase::singleton() instead
     */
    function AA_Metabase() {
        $this->tables   = array();
    }

    /** Static function called like $metabase = AA_Metabase::singleton() */
    function singleton() {
        static $instance = null;
        if (is_null($instance)) {
            // Now create the metabase object
            // It is serialized for quicker processing in PHP
            //
            // the code below was generated by following code
            //     $metabase  = new AA_Metabase;
            //     $metabase->loadFromDb();
            //     echo '$instance = unserialize(\''. str_replace("'", '\\\'', serialize($metabase)) .'\');';
            //     exit;
            // generated also by "Generate metabase PHP row" optimize action on  AA -> Optimize page
            $instance = unserialize('O:11:"AA_Metabase":1:{s:6:"tables";a:71:{s:15:"active_sessions";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:15:"active_sessions";s:11:"primary_key";a:2:{s:3:"sid";b:1;s:4:"name";b:1;}s:5:"index";a:2:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:15:"active_sessions";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:2:{i:1;s:4:"name";i:2;s:3:"sid";}}s:7:"changed";O:17:"AA_Metabase_Index":4:{s:1:"t";s:15:"active_sessions";s:1:"n";s:7:"changed";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:7:"changed";}}}s:6:"column";a:4:{s:3:"sid";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:3:"sid";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:3:"PRI";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:4:"name";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"name";i:1;s:11:"varchar(32)";i:2;b:0;i:3;s:3:"PRI";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:3:"val";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:3:"val";i:1;s:10:"mediumtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:7:"changed";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"changed";i:1;s:11:"varchar(14)";i:2;b:0;i:3;s:3:"MUL";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:17:"alerts_collection";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:17:"alerts_collection";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:2:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:17:"alerts_collection";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:9:"module_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:17:"alerts_collection";s:1:"n";s:9:"module_id";s:1:"s";s:1:"U";s:1:"c";a:1:{i:1;s:9:"module_id";}}}s:6:"column";a:5:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:7:"char(6)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"module_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"module_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"UNI";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:15:"emailid_welcome";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:15:"emailid_welcome";i:1;s:7:"int(11)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:13:"emailid_alert";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:13:"emailid_alert";i:1;s:7:"int(11)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"slice_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"slice_id";i:1;s:13:"varbinary(16)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:24:"alerts_collection_filter";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:24:"alerts_collection_filter";s:11:"primary_key";a:2:{s:12:"collectionid";b:1;s:8:"filterid";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:24:"alerts_collection_filter";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:2:{i:1;s:12:"collectionid";i:2;s:8:"filterid";}}}s:6:"column";a:3:{s:12:"collectionid";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"collectionid";i:1;s:12:"varbinary(6)";i:2;b:0;i:3;s:3:"PRI";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:8:"filterid";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"filterid";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"PRI";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:7:"myindex";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"myindex";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:26:"alerts_collection_howoften";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:26:"alerts_collection_howoften";s:11:"primary_key";a:2:{s:12:"collectionid";b:1;s:8:"howoften";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:26:"alerts_collection_howoften";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:2:{i:1;s:12:"collectionid";i:2;s:8:"howoften";}}}s:6:"column";a:3:{s:12:"collectionid";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"collectionid";i:1;s:12:"varbinary(6)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"howoften";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"howoften";i:1;s:8:"char(20)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"last";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"last";i:1;s:7:"int(10)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:13:"alerts_filter";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:13:"alerts_filter";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:13:"alerts_filter";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}}s:6:"column";a:4:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}s:3:"vid";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:3:"vid";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:5:"conds";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"conds";i:1;s:4:"text";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"description";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"description";i:1;s:4:"text";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:10:"auth_group";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:10:"auth_group";s:11:"primary_key";a:2:{s:8:"username";b:1;s:6:"groups";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:10:"auth_group";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:2:{i:1;s:8:"username";i:2;s:6:"groups";}}}s:6:"column";a:3:{s:8:"username";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"username";i:1;s:11:"varchar(50)";i:2;b:0;i:3;s:3:"PRI";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:6:"groups";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"groups";i:1;s:11:"varchar(50)";i:2;b:0;i:3;s:3:"PRI";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:12:"last_changed";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"last_changed";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:8:"auth_log";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:8:"auth_log";s:11:"primary_key";a:1:{s:7:"created";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:8:"auth_log";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:7:"created";}}}s:6:"column";a:2:{s:6:"result";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"result";i:1;s:4:"text";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:7:"created";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"created";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"PRI";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:9:"auth_user";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:9:"auth_user";s:11:"primary_key";a:1:{s:8:"username";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:9:"auth_user";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:8:"username";}}}s:6:"column";a:3:{s:8:"username";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"username";i:1;s:11:"varchar(50)";i:2;b:0;i:3;s:3:"PRI";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:6:"passwd";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"passwd";i:1;s:11:"varchar(50)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:12:"last_changed";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"last_changed";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:12:"central_conf";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:12:"central_conf";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:2:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:12:"central_conf";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:5:"AA_ID";O:17:"AA_Metabase_Index":4:{s:1:"t";s:12:"central_conf";s:1:"n";s:5:"AA_ID";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:5:"AA_ID";}}}s:6:"column";a:31:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:16:"int(10) unsigned";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}s:8:"dns_conf";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"dns_conf";i:1;s:14:"varbinary(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:10:"dns_serial";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"dns_serial";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:7:"dns_web";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"dns_web";i:1;s:13:"varbinary(15)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:6:"dns_mx";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"dns_mx";i:1;s:13:"varbinary(15)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:6:"dns_db";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"dns_db";i:1;s:13:"varbinary(15)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:8:"dns_prim";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"dns_prim";i:1;s:14:"varbinary(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:7:"dns_sec";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"dns_sec";i:1;s:14:"varbinary(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:8:"web_conf";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"web_conf";i:1;s:14:"varbinary(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:8:"web_path";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"web_path";i:1;s:14:"varbinary(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:9:"db_server";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"db_server";i:1;s:14:"varbinary(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:7:"db_name";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"db_name";i:1;s:14:"varbinary(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:7:"db_user";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"db_user";i:1;s:14:"varbinary(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:6:"db_pwd";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"db_pwd";i:1;s:14:"varbinary(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:12:"AA_SITE_PATH";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"AA_SITE_PATH";i:1;s:14:"varbinary(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:11:"AA_BASE_DIR";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"AA_BASE_DIR";i:1;s:14:"varbinary(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:14:"AA_HTTP_DOMAIN";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:14:"AA_HTTP_DOMAIN";i:1;s:14:"varbinary(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:5:"AA_ID";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"AA_ID";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:3:"MUL";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:8:"ORG_NAME";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"ORG_NAME";i:1;s:14:"varbinary(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:21:"ERROR_REPORTING_EMAIL";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:21:"ERROR_REPORTING_EMAIL";i:1;s:14:"varbinary(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:12:"ALERTS_EMAIL";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"ALERTS_EMAIL";i:1;s:14:"varbinary(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:19:"IMG_UPLOAD_MAX_SIZE";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:19:"IMG_UPLOAD_MAX_SIZE";i:1;s:10:"bigint(20)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:14:"IMG_UPLOAD_URL";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:14:"IMG_UPLOAD_URL";i:1;s:14:"varbinary(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:15:"IMG_UPLOAD_PATH";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:15:"IMG_UPLOAD_PATH";i:1;s:14:"varbinary(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:15:"SCROLLER_LENGTH";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:15:"SCROLLER_LENGTH";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:16:"FILEMAN_BASE_DIR";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:16:"FILEMAN_BASE_DIR";i:1;s:14:"varbinary(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:16:"FILEMAN_BASE_URL";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:16:"FILEMAN_BASE_URL";i:1;s:14:"varbinary(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:25:"FILEMAN_UPLOAD_TIME_LIMIT";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:25:"FILEMAN_UPLOAD_TIME_LIMIT";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:13:"AA_ADMIN_USER";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:13:"AA_ADMIN_USER";i:1;s:13:"varbinary(30)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:12:"AA_ADMIN_PWD";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"AA_ADMIN_PWD";i:1;s:13:"varbinary(30)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:11:"status_code";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"status_code";i:1;s:11:"smallint(5)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:6:"change";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:6:"change";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:2:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:6:"change";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:18:"type_resource_time";O:17:"AA_Metabase_Index":4:{s:1:"t";s:6:"change";s:1:"n";s:18:"type_resource_time";s:1:"s";s:1:"I";s:1:"c";a:3:{i:1;s:4:"type";i:2;s:11:"resource_id";i:3;s:4:"time";}}}s:6:"column";a:5:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:3:"PRI";i:4;s:32:"                                ";i:5;s:0:"";i:6;s:0:"";}}s:11:"resource_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"resource_id";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:0:"";i:4;s:32:"                                ";i:5;s:0:"";i:6;s:0:"";}}s:4:"type";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"type";i:1;s:8:"char(20)";i:2;b:1;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"user";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"user";i:1;s:8:"char(60)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"time";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"time";i:1;s:10:"bigint(20)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:13:"change_record";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:13:"change_record";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:13:"change_record";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}}s:6:"column";a:6:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:10:"bigint(20)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}s:9:"change_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"change_id";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:0:"";i:4;s:32:"                                ";i:5;s:0:"";i:6;s:0:"";}}s:8:"selector";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"selector";i:1;s:14:"varbinary(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"priority";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"priority";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:5:"value";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"value";i:1;s:8:"longtext";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"type";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"type";i:1;s:11:"varchar(32)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:8:"constant";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:8:"constant";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:3:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:8:"constant";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:8:"group_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:8:"constant";s:1:"n";s:8:"group_id";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:8:"group_id";}}s:8:"short_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:8:"constant";s:1:"n";s:8:"short_id";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:8:"short_id";}}}s:6:"column";a:9:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"group_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"group_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"name";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"name";i:1;s:9:"char(150)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:5:"value";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"value";i:1;s:9:"char(255)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:5:"class";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"class";i:1;s:13:"varbinary(16)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:3:"pri";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:3:"pri";i:1;s:11:"smallint(5)";i:2;b:0;i:3;s:0:"";i:4;s:3:"100";i:5;s:0:"";i:6;s:0:"";}}s:9:"ancestors";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"ancestors";i:1;s:9:"char(160)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"description";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"description";i:1;s:9:"char(250)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"short_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"short_id";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"MUL";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}}s:5:"flags";N;}s:14:"constant_slice";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:14:"constant_slice";s:11:"primary_key";a:1:{s:8:"group_id";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:14:"constant_slice";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:8:"group_id";}}}s:6:"column";a:7:{s:8:"slice_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"slice_id";i:1;s:13:"varbinary(16)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"group_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"group_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"PRI";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:9:"propagate";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"propagate";i:1;s:10:"tinyint(1)";i:2;b:0;i:3;s:0:"";i:4;s:1:"1";i:5;s:0:"";i:6;s:0:"";}}s:10:"levelcount";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"levelcount";i:1;s:10:"tinyint(2)";i:2;b:0;i:3;s:0:"";i:4;s:1:"2";i:5;s:0:"";i:6;s:0:"";}}s:10:"horizontal";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"horizontal";i:1;s:10:"tinyint(1)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:9:"hidevalue";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"hidevalue";i:1;s:10:"tinyint(1)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:8:"hierarch";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"hierarch";i:1;s:10:"tinyint(1)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:7:"content";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:7:"content";s:11:"primary_key";a:0:{}s:5:"index";a:2:{s:7:"item_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:7:"content";s:1:"n";s:7:"item_id";s:1:"s";s:1:"I";s:1:"c";a:3:{i:1;s:7:"item_id";i:2;s:8:"field_id";i:3;a:2:{i:0;s:4:"text";i:1;s:2:"16";}}}s:4:"text";O:17:"AA_Metabase_Index":4:{s:1:"t";s:7:"content";s:1:"n";s:4:"text";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;a:2:{i:0;s:4:"text";i:1;s:2:"12";}}}}s:6:"column";a:5:{s:7:"item_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"item_id";i:1;s:13:"varbinary(16)";i:2;b:1;i:3;s:3:"MUL";i:4;s:16:"                ";i:5;s:0:"";i:6;s:0:"";}}s:8:"field_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"field_id";i:1;s:13:"varbinary(16)";i:2;b:1;i:3;s:0:"";i:4;s:16:"                ";i:5;s:0:"";i:6;s:0:"";}}s:6:"number";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"number";i:1;s:10:"bigint(20)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"text";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"text";i:1;s:10:"mediumtext";i:2;b:1;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"flag";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"flag";i:1;s:11:"smallint(6)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:4:"cron";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:4:"cron";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:4:"cron";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}}s:6:"column";a:9:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:10:"bigint(30)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}s:7:"minutes";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"minutes";i:1;s:11:"varchar(30)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:5:"hours";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"hours";i:1;s:11:"varchar(30)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"mday";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"mday";i:1;s:11:"varchar(30)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:3:"mon";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:3:"mon";i:1;s:11:"varchar(30)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"wday";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"wday";i:1;s:11:"varchar(30)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"script";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"script";i:1;s:12:"varchar(100)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"params";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"params";i:1;s:12:"varchar(200)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"last_run";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"last_run";i:1;s:10:"bigint(30)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:10:"discussion";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:10:"discussion";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:2:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:10:"discussion";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:12:"item_id_date";O:17:"AA_Metabase_Index":4:{s:1:"t";s:10:"discussion";s:1:"n";s:12:"item_id_date";s:1:"s";s:1:"I";s:1:"c";a:2:{i:1;s:7:"item_id";i:2;s:4:"date";}}}s:6:"column";a:15:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"PRI";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:6:"parent";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"parent";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:7:"item_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"item_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"MUL";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:4:"date";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"date";i:1;s:10:"bigint(20)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:7:"subject";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"subject";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"author";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"author";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"e_mail";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"e_mail";i:1;s:11:"varchar(80)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"body";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"body";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:5:"state";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"state";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:4:"flag";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"flag";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:11:"url_address";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"url_address";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:15:"url_description";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:15:"url_description";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"remote_addr";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"remote_addr";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:5:"free1";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"free1";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:5:"free2";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"free2";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:13:"ef_categories";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:13:"ef_categories";s:11:"primary_key";a:2:{s:11:"category_id";b:1;s:7:"feed_id";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:13:"ef_categories";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:2:{i:1;s:11:"category_id";i:2;s:7:"feed_id";}}}s:6:"column";a:6:{s:8:"category";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"category";i:1;s:12:"varchar(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:13:"category_name";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:13:"category_name";i:1;s:12:"varchar(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:11:"category_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"category_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"PRI";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:7:"feed_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"feed_id";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"PRI";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:18:"target_category_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:18:"target_category_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:8:"approved";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"approved";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:14:"ef_permissions";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:14:"ef_permissions";s:11:"primary_key";a:3:{s:8:"slice_id";b:1;s:4:"node";b:1;s:4:"user";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:14:"ef_permissions";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:3:{i:1;s:8:"slice_id";i:2;s:4:"node";i:3;s:4:"user";}}}s:6:"column";a:3:{s:8:"slice_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"slice_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"PRI";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:4:"node";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"node";i:1;s:12:"varchar(150)";i:2;b:0;i:3;s:3:"PRI";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:4:"user";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"user";i:1;s:11:"varchar(50)";i:2;b:0;i:3;s:3:"PRI";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:5:"email";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:5:"email";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:5:"email";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}}s:6:"column";a:12:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}s:11:"description";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"description";i:1;s:12:"varchar(255)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:7:"subject";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"subject";i:1;s:4:"text";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"body";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"body";i:1;s:4:"text";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"header_from";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"header_from";i:1;s:4:"text";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"reply_to";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"reply_to";i:1;s:4:"text";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"errors_to";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"errors_to";i:1;s:4:"text";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"sender";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"sender";i:1;s:4:"text";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"lang";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"lang";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:0:"";i:4;s:2:"en";i:5;s:0:"";i:6;s:0:"";}}s:15:"owner_module_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:15:"owner_module_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"html";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"html";i:1;s:11:"smallint(1)";i:2;b:0;i:3;s:0:"";i:4;s:1:"1";i:5;s:0:"";i:6;s:0:"";}}s:4:"type";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"type";i:1;s:11:"varchar(20)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:12:"email_notify";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:12:"email_notify";s:11:"primary_key";a:3:{s:8:"slice_id";b:1;s:3:"uid";b:1;s:8:"function";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:12:"email_notify";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:3:{i:1;s:8:"slice_id";i:2;s:3:"uid";i:3;s:8:"function";}}}s:6:"column";a:3:{s:8:"slice_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"slice_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:3:"uid";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:3:"uid";i:1;s:8:"char(60)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"function";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"function";i:1;s:11:"smallint(5)";i:2;b:0;i:3;s:3:"PRI";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:5:"event";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:5:"event";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:3:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:5:"event";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:10:"type_class";O:17:"AA_Metabase_Index":4:{s:1:"t";s:5:"event";s:1:"n";s:10:"type_class";s:1:"s";s:1:"I";s:1:"c";a:2:{i:1;s:4:"type";i:2;s:5:"class";}}s:13:"type_selector";O:17:"AA_Metabase_Index":4:{s:1:"t";s:5:"event";s:1:"n";s:13:"type_selector";s:1:"s";s:1:"I";s:1:"c";a:2:{i:1;s:4:"type";i:2;a:2:{i:0;s:8:"selector";i:1;s:2:"32";}}}}s:6:"column";a:6:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:3:"PRI";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:4:"type";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"type";i:1;s:11:"varchar(32)";i:2;b:0;i:3;s:3:"MUL";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:5:"class";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"class";i:1;s:11:"varchar(32)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"selector";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"selector";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"reaction";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"reaction";i:1;s:11:"varchar(50)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:6:"params";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"params";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:14:"external_feeds";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:14:"external_feeds";s:11:"primary_key";a:1:{s:7:"feed_id";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:14:"external_feeds";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:7:"feed_id";}}}s:6:"column";a:8:{s:7:"feed_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"feed_id";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}s:8:"slice_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"slice_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:9:"node_name";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"node_name";i:1;s:12:"varchar(150)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:15:"remote_slice_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:15:"remote_slice_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:7:"user_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"user_id";i:1;s:12:"varchar(200)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:11:"newest_item";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"newest_item";i:1;s:11:"varchar(40)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:17:"remote_slice_name";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:17:"remote_slice_name";i:1;s:12:"varchar(200)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:9:"feed_mode";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"feed_mode";i:1;s:11:"varchar(10)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:7:"feedmap";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:7:"feedmap";s:11:"primary_key";a:0:{}s:5:"index";a:1:{s:13:"from_slice_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:7:"feedmap";s:1:"n";s:13:"from_slice_id";s:1:"s";s:1:"I";s:1:"c";a:2:{i:1;s:13:"from_slice_id";i:2;s:11:"to_slice_id";}}}s:6:"column";a:7:{s:13:"from_slice_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:13:"from_slice_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"MUL";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:13:"from_field_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:13:"from_field_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:11:"to_slice_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"to_slice_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:11:"to_field_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"to_field_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:4:"flag";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"flag";i:1;s:7:"int(11)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:5:"value";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"value";i:1;s:10:"mediumtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:15:"from_field_name";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:15:"from_field_name";i:1;s:12:"varchar(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:9:"feedperms";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:9:"feedperms";s:11:"primary_key";a:0:{}s:5:"index";a:2:{s:7:"from_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:9:"feedperms";s:1:"n";s:7:"from_id";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:7:"from_id";}}s:5:"to_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:9:"feedperms";s:1:"n";s:5:"to_id";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:5:"to_id";}}}s:6:"column";a:3:{s:7:"from_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"from_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"MUL";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:5:"to_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"to_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"MUL";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:4:"flag";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"flag";i:1;s:7:"int(11)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:5:"feeds";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:5:"feeds";s:11:"primary_key";a:0:{}s:5:"index";a:1:{s:7:"from_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:5:"feeds";s:1:"n";s:7:"from_id";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:7:"from_id";}}}s:6:"column";a:6:{s:7:"from_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"from_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:5:"to_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"to_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"category_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"category_id";i:1;s:13:"varbinary(16)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:14:"all_categories";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:14:"all_categories";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"to_approved";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"to_approved";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:14:"to_category_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:14:"to_category_id";i:1;s:13:"varbinary(16)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:5:"field";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:5:"field";s:11:"primary_key";a:2:{s:2:"id";b:1;s:8:"slice_id";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:5:"field";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:2:{i:1;s:8:"slice_id";i:2;s:2:"id";}}}s:6:"column";a:40:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"PRI";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:4:"type";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"type";i:1;s:11:"varchar(16)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:8:"slice_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"slice_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"PRI";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:4:"name";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"name";i:1;s:12:"varchar(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:9:"input_pri";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"input_pri";i:1;s:11:"smallint(5)";i:2;b:0;i:3;s:0:"";i:4;s:3:"100";i:5;s:0:"";i:6;s:0:"";}}s:10:"input_help";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"input_help";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:13:"input_morehlp";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:13:"input_morehlp";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:13:"input_default";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:13:"input_default";i:1;s:10:"mediumtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"required";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"required";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"feed";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"feed";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"multiple";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"multiple";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:15:"input_show_func";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:15:"input_show_func";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:10:"content_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"content_id";i:1;s:13:"varbinary(16)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:10:"search_pri";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"search_pri";i:1;s:11:"smallint(5)";i:2;b:0;i:3;s:0:"";i:4;s:3:"100";i:5;s:0:"";i:6;s:0:"";}}s:11:"search_type";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"search_type";i:1;s:11:"varchar(16)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"search_help";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"search_help";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:13:"search_before";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:13:"search_before";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:16:"search_more_help";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:16:"search_more_help";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"search_show";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"search_show";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:14:"search_ft_show";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:14:"search_ft_show";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:17:"search_ft_default";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:17:"search_ft_default";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"alias1";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"alias1";i:1;s:11:"varchar(10)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"alias1_func";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"alias1_func";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"alias1_help";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"alias1_help";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"alias2";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"alias2";i:1;s:11:"varchar(10)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"alias2_func";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"alias2_func";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"alias2_help";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"alias2_help";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"alias3";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"alias3";i:1;s:11:"varchar(10)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"alias3_func";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"alias3_func";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"alias3_help";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"alias3_help";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:12:"input_before";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"input_before";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"aditional";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"aditional";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:12:"content_edit";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"content_edit";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:12:"html_default";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"html_default";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"html_show";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"html_show";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"in_item_tbl";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"in_item_tbl";i:1;s:11:"varchar(16)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:14:"input_validate";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:14:"input_validate";i:1;s:12:"varchar(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:17:"input_insert_func";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:17:"input_insert_func";i:1;s:12:"varchar(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:10:"input_show";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"input_show";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"text_stored";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"text_stored";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;s:1:"1";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:6:"groups";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:6:"groups";s:11:"primary_key";a:1:{s:4:"name";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:6:"groups";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:4:"name";}}}s:6:"column";a:2:{s:4:"name";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"name";i:1;s:11:"varchar(32)";i:2;b:0;i:3;s:3:"PRI";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:11:"description";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"description";i:1;s:12:"varchar(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:11:"hit_archive";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:11:"hit_archive";s:11:"primary_key";a:0:{}s:5:"index";a:2:{s:4:"time";O:17:"AA_Metabase_Index":4:{s:1:"t";s:11:"hit_archive";s:1:"n";s:4:"time";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:4:"time";}}s:2:"id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:11:"hit_archive";s:1:"n";s:2:"id";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:2:"id";}}}s:6:"column";a:3:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"time";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"time";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"hits";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"hits";i:1;s:12:"mediumint(9)";i:2;b:0;i:3;s:0:"";i:4;s:1:"1";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:11:"hit_long_id";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:11:"hit_long_id";s:11:"primary_key";a:0:{}s:5:"index";a:1:{s:4:"time";O:17:"AA_Metabase_Index":4:{s:1:"t";s:11:"hit_long_id";s:1:"n";s:4:"time";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:4:"time";}}}s:6:"column";a:4:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:10:"binary(16)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"time";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"time";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:5:"agent";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"agent";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"info";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"info";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:12:"hit_short_id";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:12:"hit_short_id";s:11:"primary_key";a:0:{}s:5:"index";a:1:{s:4:"time";O:17:"AA_Metabase_Index":4:{s:1:"t";s:12:"hit_short_id";s:1:"n";s:4:"time";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:4:"time";}}}s:6:"column";a:4:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"time";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"time";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:5:"agent";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"agent";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"info";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"info";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:4:"item";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:4:"item";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:5:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:4:"item";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:8:"short_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:4:"item";s:1:"n";s:8:"short_id";s:1:"s";s:1:"U";s:1:"c";a:1:{i:1;s:8:"short_id";}}s:11:"expiry_date";O:17:"AA_Metabase_Index":4:{s:1:"t";s:4:"item";s:1:"n";s:11:"expiry_date";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:11:"expiry_date";}}s:12:"publish_date";O:17:"AA_Metabase_Index":4:{s:1:"t";s:4:"item";s:1:"n";s:12:"publish_date";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:12:"publish_date";}}s:10:"slice_id_2";O:17:"AA_Metabase_Index":4:{s:1:"t";s:4:"item";s:1:"n";s:10:"slice_id_2";s:1:"s";s:1:"I";s:1:"c";a:3:{i:1;s:8:"slice_id";i:2;s:11:"status_code";i:3;s:12:"publish_date";}}}s:6:"column";a:17:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"short_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"short_id";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"UNI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}s:8:"slice_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"slice_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"status_code";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"status_code";i:1;s:11:"smallint(5)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:9:"post_date";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"post_date";i:1;s:10:"bigint(20)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:12:"publish_date";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"publish_date";i:1;s:10:"bigint(20)";i:2;b:1;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"expiry_date";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"expiry_date";i:1;s:10:"bigint(20)";i:2;b:1;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"highlight";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"highlight";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"posted_by";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"posted_by";i:1;s:8:"char(60)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"edited_by";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"edited_by";i:1;s:8:"char(60)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"last_edit";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"last_edit";i:1;s:10:"bigint(20)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:13:"display_count";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:13:"display_count";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:5:"flags";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"flags";i:1;s:8:"char(30)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:10:"disc_count";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"disc_count";i:1;s:7:"int(11)";i:2;b:1;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:8:"disc_app";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"disc_app";i:1;s:7:"int(11)";i:2;b:1;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:14:"externally_fed";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:14:"externally_fed";i:1;s:9:"char(150)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:12:"moved2active";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"moved2active";i:1;s:7:"int(10)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:4:"jump";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:4:"jump";s:11:"primary_key";a:1:{s:8:"slice_id";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:4:"jump";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:8:"slice_id";}}}s:6:"column";a:3:{s:8:"slice_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"slice_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"destination";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"destination";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:13:"dest_slice_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:13:"dest_slice_id";i:1;s:13:"varbinary(16)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:5:"links";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:5:"links";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:5:"links";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}}s:6:"column";a:6:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"start_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"start_id";i:1;s:7:"int(10)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:10:"tree_start";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"tree_start";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:12:"select_start";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"select_start";i:1;s:7:"int(11)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:16:"default_cat_tmpl";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:16:"default_cat_tmpl";i:1;s:8:"char(60)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"link_tmpl";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"link_tmpl";i:1;s:8:"char(60)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:13:"links_cat_cat";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:13:"links_cat_cat";s:11:"primary_key";a:1:{s:4:"a_id";b:1;}s:5:"index";a:3:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:13:"links_cat_cat";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:4:"a_id";}}s:7:"what_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:13:"links_cat_cat";s:1:"n";s:7:"what_id";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:7:"what_id";}}s:11:"category_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:13:"links_cat_cat";s:1:"n";s:11:"category_id";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:11:"category_id";}}}s:6:"column";a:8:{s:11:"category_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"category_id";i:1;s:16:"int(10) unsigned";i:2;b:0;i:3;s:3:"MUL";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:7:"what_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"what_id";i:1;s:16:"int(10) unsigned";i:2;b:0;i:3;s:3:"MUL";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:4:"base";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"base";i:1;s:13:"enum(\'n\',\'y\')";i:2;b:0;i:3;s:0:"";i:4;s:1:"y";i:5;s:0:"";i:6;s:0:"";}}s:5:"state";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"state";i:1;s:36:"enum(\'hidden\',\'highlight\',\'visible\')";i:2;b:0;i:3;s:0:"";i:4;s:7:"visible";i:5;s:0:"";i:6;s:0:"";}}s:8:"proposal";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"proposal";i:1;s:13:"enum(\'n\',\'y\')";i:2;b:0;i:3;s:0:"";i:4;s:1:"n";i:5;s:0:"";i:6;s:0:"";}}s:8:"priority";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"priority";i:1;s:11:"float(10,2)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:15:"proposal_delete";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:15:"proposal_delete";i:1;s:13:"enum(\'n\',\'y\')";i:2;b:0;i:3;s:0:"";i:4;s:1:"n";i:5;s:0:"";i:6;s:0:"";}}s:4:"a_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"a_id";i:1;s:16:"int(10) unsigned";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}}s:5:"flags";N;}s:16:"links_categories";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:16:"links_categories";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:3:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:16:"links_categories";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:4:"path";O:17:"AA_Metabase_Index":4:{s:1:"t";s:16:"links_categories";s:1:"n";s:4:"path";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:4:"path";}}s:2:"id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:16:"links_categories";s:1:"n";s:2:"id";s:1:"s";s:1:"I";s:1:"c";a:2:{i:1;s:2:"id";i:2;s:4:"path";}}}s:6:"column";a:13:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:16:"int(10) unsigned";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}s:4:"name";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"name";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:13:"html_template";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:13:"html_template";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:7:"deleted";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"deleted";i:1;s:13:"enum(\'n\',\'y\')";i:2;b:0;i:3;s:0:"";i:4;s:1:"n";i:5;s:0:"";i:6;s:0:"";}}s:4:"path";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"path";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"inc_file1";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"inc_file1";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:10:"link_count";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"link_count";i:1;s:12:"mediumint(9)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:9:"inc_file2";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"inc_file2";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"banner_file";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"banner_file";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"description";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"description";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:10:"additional";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"additional";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"note";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"note";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:7:"nolinks";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"nolinks";i:1;s:10:"tinyint(4)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:13:"links_changes";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:13:"links_changes";s:11:"primary_key";a:0:{}s:5:"index";a:3:{s:16:"proposal_link_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:13:"links_changes";s:1:"n";s:16:"proposal_link_id";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:16:"proposal_link_id";}}s:8:"rejected";O:17:"AA_Metabase_Index":4:{s:1:"t";s:13:"links_changes";s:1:"n";s:8:"rejected";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:8:"rejected";}}s:15:"changed_link_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:13:"links_changes";s:1:"n";s:15:"changed_link_id";s:1:"s";s:1:"I";s:1:"c";a:2:{i:1;s:15:"changed_link_id";i:2;s:8:"rejected";}}}s:6:"column";a:3:{s:15:"changed_link_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:15:"changed_link_id";i:1;s:16:"int(10) unsigned";i:2;b:0;i:3;s:3:"MUL";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:16:"proposal_link_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:16:"proposal_link_id";i:1;s:16:"int(10) unsigned";i:2;b:0;i:3;s:3:"MUL";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:8:"rejected";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"rejected";i:1;s:13:"enum(\'n\',\'y\')";i:2;b:0;i:3;s:3:"MUL";i:4;s:1:"n";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:15:"links_languages";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:15:"links_languages";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:2:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:15:"links_languages";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:4:"name";O:17:"AA_Metabase_Index":4:{s:1:"t";s:15:"links_languages";s:1:"n";s:4:"name";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:4:"name";}}}s:6:"column";a:3:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:16:"int(10) unsigned";i:2;b:0;i:3;s:3:"PRI";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:4:"name";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"name";i:1;s:11:"varchar(20)";i:2;b:0;i:3;s:3:"MUL";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:10:"short_name";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"short_name";i:1;s:10:"varchar(5)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:14:"links_link_cat";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:14:"links_link_cat";s:11:"primary_key";a:1:{s:4:"a_id";b:1;}s:5:"index";a:4:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:14:"links_link_cat";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:4:"a_id";}}s:8:"proposal";O:17:"AA_Metabase_Index":4:{s:1:"t";s:14:"links_link_cat";s:1:"n";s:8:"proposal";s:1:"s";s:1:"I";s:1:"c";a:3:{i:1;s:8:"proposal";i:2;s:4:"base";i:3;s:5:"state";}}s:11:"category_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:14:"links_link_cat";s:1:"n";s:11:"category_id";s:1:"s";s:1:"I";s:1:"c";a:4:{i:1;s:11:"category_id";i:2;s:8:"proposal";i:3;s:4:"base";i:4;s:5:"state";}}s:7:"what_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:14:"links_link_cat";s:1:"n";s:7:"what_id";s:1:"s";s:1:"I";s:1:"c";a:4:{i:1;s:7:"what_id";i:2;s:8:"proposal";i:3;s:4:"base";i:4;s:5:"state";}}}s:6:"column";a:8:{s:11:"category_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"category_id";i:1;s:16:"int(10) unsigned";i:2;b:0;i:3;s:3:"MUL";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:7:"what_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"what_id";i:1;s:16:"int(10) unsigned";i:2;b:0;i:3;s:3:"MUL";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:4:"base";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"base";i:1;s:13:"enum(\'n\',\'y\')";i:2;b:0;i:3;s:0:"";i:4;s:1:"y";i:5;s:0:"";i:6;s:0:"";}}s:5:"state";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"state";i:1;s:36:"enum(\'hidden\',\'highlight\',\'visible\')";i:2;b:0;i:3;s:0:"";i:4;s:7:"visible";i:5;s:0:"";i:6;s:0:"";}}s:8:"proposal";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"proposal";i:1;s:13:"enum(\'n\',\'y\')";i:2;b:0;i:3;s:3:"MUL";i:4;s:1:"n";i:5;s:0:"";i:6;s:0:"";}}s:8:"priority";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"priority";i:1;s:11:"float(10,2)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:15:"proposal_delete";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:15:"proposal_delete";i:1;s:13:"enum(\'n\',\'y\')";i:2;b:0;i:3;s:0:"";i:4;s:1:"n";i:5;s:0:"";i:6;s:0:"";}}s:4:"a_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"a_id";i:1;s:16:"int(10) unsigned";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}}s:5:"flags";N;}s:15:"links_link_lang";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:15:"links_link_lang";s:11:"primary_key";a:0:{}s:5:"index";a:1:{s:7:"link_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:15:"links_link_lang";s:1:"n";s:7:"link_id";s:1:"s";s:1:"I";s:1:"c";a:2:{i:1;s:7:"link_id";i:2;s:7:"lang_id";}}}s:6:"column";a:2:{s:7:"link_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"link_id";i:1;s:16:"int(10) unsigned";i:2;b:0;i:3;s:3:"MUL";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:7:"lang_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"lang_id";i:1;s:16:"int(10) unsigned";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:14:"links_link_reg";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:14:"links_link_reg";s:11:"primary_key";a:0:{}s:5:"index";a:1:{s:7:"link_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:14:"links_link_reg";s:1:"n";s:7:"link_id";s:1:"s";s:1:"I";s:1:"c";a:2:{i:1;s:7:"link_id";i:2;s:9:"region_id";}}}s:6:"column";a:2:{s:7:"link_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"link_id";i:1;s:16:"int(10) unsigned";i:2;b:0;i:3;s:3:"MUL";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:9:"region_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"region_id";i:1;s:16:"int(10) unsigned";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:11:"links_links";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:11:"links_links";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:8:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:11:"links_links";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:7:"checked";O:17:"AA_Metabase_Index":4:{s:1:"t";s:11:"links_links";s:1:"n";s:7:"checked";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:7:"checked";}}s:4:"type";O:17:"AA_Metabase_Index":4:{s:1:"t";s:11:"links_links";s:1:"n";s:4:"type";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:4:"type";}}s:9:"validated";O:17:"AA_Metabase_Index":4:{s:1:"t";s:11:"links_links";s:1:"n";s:9:"validated";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:9:"validated";}}s:10:"valid_rank";O:17:"AA_Metabase_Index":4:{s:1:"t";s:11:"links_links";s:1:"n";s:10:"valid_rank";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:10:"valid_rank";}}s:4:"name";O:17:"AA_Metabase_Index":4:{s:1:"t";s:11:"links_links";s:1:"n";s:4:"name";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:4:"name";}}s:2:"id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:11:"links_links";s:1:"n";s:2:"id";s:1:"s";s:1:"I";s:1:"c";a:2:{i:1;s:2:"id";i:2;s:6:"folder";}}s:6:"folder";O:17:"AA_Metabase_Index":4:{s:1:"t";s:11:"links_links";s:1:"n";s:6:"folder";s:1:"s";s:1:"I";s:1:"c";a:2:{i:1;s:6:"folder";i:2;s:2:"id";}}}s:6:"column";a:29:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:16:"int(10) unsigned";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}s:4:"name";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"name";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"description";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"description";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"rate";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"rate";i:1;s:7:"int(10)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:5:"votes";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"votes";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:10:"plus_votes";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"plus_votes";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:10:"created_by";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"created_by";i:1;s:11:"varchar(60)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"edited_by";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"edited_by";i:1;s:11:"varchar(60)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:10:"checked_by";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"checked_by";i:1;s:11:"varchar(60)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"initiator";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"initiator";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:3:"url";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:3:"url";i:1;s:4:"text";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:7:"created";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"created";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:9:"last_edit";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"last_edit";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:7:"checked";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"checked";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"MUL";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:5:"voted";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"voted";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:4:"flag";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"flag";i:1;s:7:"int(11)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:13:"original_name";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:13:"original_name";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"type";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"type";i:1;s:12:"varchar(120)";i:2;b:1;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"org_city";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"org_city";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:13:"org_post_code";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:13:"org_post_code";i:1;s:11:"varchar(20)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"org_phone";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"org_phone";i:1;s:12:"varchar(120)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:7:"org_fax";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"org_fax";i:1;s:12:"varchar(120)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"org_email";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"org_email";i:1;s:12:"varchar(120)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:10:"org_street";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"org_street";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"folder";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"folder";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"MUL";i:4;s:1:"1";i:5;s:0:"";i:6;s:0:"";}}s:4:"note";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"note";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"validated";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"validated";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"MUL";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:11:"valid_codes";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"valid_codes";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:10:"valid_rank";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"valid_rank";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"MUL";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:13:"links_regions";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:13:"links_regions";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:2:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:13:"links_regions";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:4:"name";O:17:"AA_Metabase_Index":4:{s:1:"t";s:13:"links_regions";s:1:"n";s:4:"name";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:4:"name";}}}s:6:"column";a:3:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:16:"int(10) unsigned";i:2;b:0;i:3;s:3:"PRI";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:4:"name";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"name";i:1;s:11:"varchar(60)";i:2;b:0;i:3;s:3:"MUL";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:5:"level";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"level";i:1;s:10:"tinyint(4)";i:2;b:0;i:3;s:0:"";i:4;s:1:"1";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:3:"log";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:3:"log";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:3:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:3:"log";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:4:"time";O:17:"AA_Metabase_Index":4:{s:1:"t";s:3:"log";s:1:"n";s:4:"time";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:4:"time";}}s:9:"type_time";O:17:"AA_Metabase_Index":4:{s:1:"t";s:3:"log";s:1:"n";s:9:"type_time";s:1:"s";s:1:"I";s:1:"c";a:2:{i:1;s:4:"type";i:2;s:4:"time";}}}s:6:"column";a:6:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}s:4:"time";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"time";i:1;s:10:"bigint(20)";i:2;b:0;i:3;s:3:"MUL";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:4:"user";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"user";i:1;s:11:"varchar(60)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"type";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"type";i:1;s:11:"varchar(10)";i:2;b:1;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"selector";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"selector";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"params";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"params";i:1;s:12:"varchar(128)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:10:"membership";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:10:"membership";s:11:"primary_key";a:2:{s:7:"groupid";b:1;s:8:"memberid";b:1;}s:5:"index";a:2:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:10:"membership";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:2:{i:1;s:7:"groupid";i:2;s:8:"memberid";}}s:8:"memberid";O:17:"AA_Metabase_Index":4:{s:1:"t";s:10:"membership";s:1:"n";s:8:"memberid";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:8:"memberid";}}}s:6:"column";a:3:{s:7:"groupid";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"groupid";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"PRI";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:8:"memberid";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"memberid";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"last_mod";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"last_mod";i:1;s:9:"timestamp";i:2;b:0;i:3;s:0:"";i:4;s:17:"CURRENT_TIMESTAMP";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:6:"module";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:6:"module";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:6:"module";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}}s:6:"column";a:12:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"name";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"name";i:1;s:9:"char(100)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:7:"deleted";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"deleted";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"type";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"type";i:1;s:8:"char(16)";i:2;b:1;i:3;s:0:"";i:4;s:1:"S";i:5;s:0:"";i:6;s:0:"";}}s:9:"slice_url";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"slice_url";i:1;s:9:"char(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"lang_file";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"lang_file";i:1;s:8:"char(50)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:10:"created_at";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"created_at";i:1;s:10:"bigint(20)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:10:"created_by";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"created_by";i:1;s:9:"char(255)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:5:"owner";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"owner";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"app_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"app_id";i:1;s:13:"varbinary(16)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"priority";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"priority";i:1;s:11:"smallint(6)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:4:"flag";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"flag";i:1;s:7:"int(11)";i:2;b:1;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:5:"nodes";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:5:"nodes";s:11:"primary_key";a:1:{s:4:"name";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:5:"nodes";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:4:"name";}}}s:6:"column";a:3:{s:4:"name";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"name";i:1;s:12:"varchar(150)";i:2;b:0;i:3;s:3:"PRI";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:10:"server_url";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"server_url";i:1;s:12:"varchar(200)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:8:"password";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"password";i:1;s:11:"varchar(50)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:12:"object_float";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:12:"object_float";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:3:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:12:"object_float";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:7:"item_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:12:"object_float";s:1:"n";s:7:"item_id";s:1:"s";s:1:"I";s:1:"c";a:3:{i:1;s:9:"object_id";i:2;s:8:"property";i:3;s:5:"value";}}s:8:"property";O:17:"AA_Metabase_Index":4:{s:1:"t";s:12:"object_float";s:1:"n";s:8:"property";s:1:"s";s:1:"I";s:1:"c";a:2:{i:1;s:8:"property";i:2;s:5:"value";}}}s:6:"column";a:6:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:10:"bigint(20)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}s:9:"object_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"object_id";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:3:"MUL";i:4;s:32:"                                ";i:5;s:0:"";i:6;s:0:"";}}s:8:"property";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"property";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"MUL";i:4;s:16:"                ";i:5;s:0:"";i:6;s:0:"";}}s:8:"priority";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"priority";i:1;s:12:"smallint(20)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:5:"value";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"value";i:1;s:6:"double";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"flag";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"flag";i:1;s:11:"smallint(6)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:14:"object_integer";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:14:"object_integer";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:3:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:14:"object_integer";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:7:"item_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:14:"object_integer";s:1:"n";s:7:"item_id";s:1:"s";s:1:"I";s:1:"c";a:3:{i:1;s:9:"object_id";i:2;s:8:"property";i:3;s:5:"value";}}s:8:"property";O:17:"AA_Metabase_Index":4:{s:1:"t";s:14:"object_integer";s:1:"n";s:8:"property";s:1:"s";s:1:"I";s:1:"c";a:2:{i:1;s:8:"property";i:2;s:5:"value";}}}s:6:"column";a:6:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:10:"bigint(20)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}s:9:"object_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"object_id";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:3:"MUL";i:4;s:32:"                                ";i:5;s:0:"";i:6;s:0:"";}}s:8:"property";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"property";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"MUL";i:4;s:16:"                ";i:5;s:0:"";i:6;s:0:"";}}s:8:"priority";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"priority";i:1;s:12:"smallint(20)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:5:"value";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"value";i:1;s:10:"bigint(20)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"flag";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"flag";i:1;s:11:"smallint(6)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:11:"object_text";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:11:"object_text";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:3:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:11:"object_text";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:9:"object_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:11:"object_text";s:1:"n";s:9:"object_id";s:1:"s";s:1:"I";s:1:"c";a:3:{i:1;s:9:"object_id";i:2;s:8:"property";i:3;a:2:{i:0;s:5:"value";i:1;s:2:"16";}}}s:8:"property";O:17:"AA_Metabase_Index":4:{s:1:"t";s:11:"object_text";s:1:"n";s:8:"property";s:1:"s";s:1:"I";s:1:"c";a:2:{i:1;s:8:"property";i:2;a:2:{i:0;s:5:"value";i:1;s:2:"10";}}}}s:6:"column";a:6:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:10:"bigint(20)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}s:9:"object_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"object_id";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:3:"MUL";i:4;s:32:"                                ";i:5;s:0:"";i:6;s:0:"";}}s:8:"property";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"property";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"MUL";i:4;s:16:"                ";i:5;s:0:"";i:6;s:0:"";}}s:8:"priority";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"priority";i:1;s:12:"smallint(20)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:5:"value";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"value";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"flag";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"flag";i:1;s:11:"smallint(6)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:7:"offline";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:7:"offline";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:2:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:7:"offline";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:6:"digest";O:17:"AA_Metabase_Index":4:{s:1:"t";s:7:"offline";s:1:"n";s:6:"digest";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:6:"digest";}}}s:6:"column";a:3:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"PRI";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:6:"digest";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"digest";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:3:"MUL";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:4:"flag";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"flag";i:1;s:7:"int(11)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:9:"pagecache";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:9:"pagecache";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:2:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:9:"pagecache";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:6:"stored";O:17:"AA_Metabase_Index":4:{s:1:"t";s:9:"pagecache";s:1:"n";s:6:"stored";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:6:"stored";}}}s:6:"column";a:4:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:3:"PRI";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:7:"content";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"content";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"stored";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"stored";i:1;s:10:"bigint(20)";i:2;b:0;i:3;s:3:"MUL";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:4:"flag";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"flag";i:1;s:7:"int(11)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:18:"pagecache_str2find";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:18:"pagecache_str2find";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:3:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:18:"pagecache_str2find";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:8:"str2find";O:17:"AA_Metabase_Index":4:{s:1:"t";s:18:"pagecache_str2find";s:1:"n";s:8:"str2find";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;a:2:{i:0;s:8:"str2find";i:1;s:2:"20";}}}s:12:"pagecache_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:18:"pagecache_str2find";s:1:"n";s:12:"pagecache_id";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:12:"pagecache_id";}}}s:6:"column";a:3:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:10:"bigint(20)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}s:12:"pagecache_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"pagecache_id";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"str2find";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"str2find";i:1;s:4:"text";i:2;b:0;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:5:"perms";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:5:"perms";s:11:"primary_key";a:3:{s:11:"object_type";b:1;s:8:"objectid";b:1;s:6:"userid";b:1;}s:5:"index";a:2:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:5:"perms";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:3:{i:1;s:8:"objectid";i:2;s:6:"userid";i:3;s:11:"object_type";}}s:6:"userid";O:17:"AA_Metabase_Index":4:{s:1:"t";s:5:"perms";s:1:"n";s:6:"userid";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:6:"userid";}}}s:6:"column";a:5:{s:11:"object_type";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"object_type";i:1;s:8:"char(30)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"objectid";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"objectid";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"userid";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"userid";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:3:"PRI";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:4:"perm";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"perm";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"last_mod";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"last_mod";i:1;s:9:"timestamp";i:2;b:0;i:3;s:0:"";i:4;s:17:"CURRENT_TIMESTAMP";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:5:"polls";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:5:"polls";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:2:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:5:"polls";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:2:"id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:5:"polls";s:1:"n";s:2:"id";s:1:"s";s:1:"I";s:1:"c";a:3:{i:1;s:9:"module_id";i:2;s:11:"status_code";i:3;s:11:"expiry_date";}}}s:6:"column";a:15:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"module_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"module_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"status_code";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"status_code";i:1;s:10:"tinyint(4)";i:2;b:0;i:3;s:0:"";i:4;s:1:"1";i:5;s:0:"";i:6;s:0:"";}}s:8:"headline";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"headline";i:1;s:4:"text";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:12:"publish_date";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"publish_date";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:11:"expiry_date";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"expiry_date";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:6:"locked";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"locked";i:1;s:10:"tinyint(4)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:7:"logging";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"logging";i:1;s:10:"tinyint(1)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:10:"ip_locking";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"ip_locking";i:1;s:10:"tinyint(1)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:15:"ip_lock_timeout";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:15:"ip_lock_timeout";i:1;s:6:"int(4)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"set_cookies";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"set_cookies";i:1;s:10:"tinyint(1)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:14:"cookies_prefix";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:14:"cookies_prefix";i:1;s:13:"varbinary(16)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"design_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"design_id";i:1;s:13:"varbinary(32)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:19:"aftervote_design_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:19:"aftervote_design_id";i:1;s:13:"varbinary(32)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"params";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"params";i:1;s:4:"text";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:12:"polls_answer";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:12:"polls_answer";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:2:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:12:"polls_answer";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:7:"poll_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:12:"polls_answer";s:1:"n";s:7:"poll_id";s:1:"s";s:1:"I";s:1:"c";a:2:{i:1;s:7:"poll_id";i:2;s:8:"priority";}}}s:6:"column";a:5:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:7:"poll_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"poll_id";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"answer";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"answer";i:1;s:4:"text";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:5:"votes";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"votes";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:8:"priority";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"priority";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:12:"polls_design";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:12:"polls_design";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:2:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:12:"polls_design";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:9:"module_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:12:"polls_design";s:1:"n";s:9:"module_id";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:9:"module_id";}}}s:6:"column";a:7:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"module_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"module_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"name";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"name";i:1;s:4:"text";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:7:"comment";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"comment";i:1;s:4:"text";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:3:"top";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:3:"top";i:1;s:4:"text";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"answer";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"answer";i:1;s:4:"text";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"bottom";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"bottom";i:1;s:4:"text";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:13:"polls_ip_lock";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:13:"polls_ip_lock";s:11:"primary_key";a:0:{}s:5:"index";a:2:{s:7:"poll_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:13:"polls_ip_lock";s:1:"n";s:7:"poll_id";s:1:"s";s:1:"I";s:1:"c";a:2:{i:1;s:7:"poll_id";i:2;s:9:"voters_ip";}}s:12:"poll_id_time";O:17:"AA_Metabase_Index":4:{s:1:"t";s:13:"polls_ip_lock";s:1:"n";s:12:"poll_id_time";s:1:"s";s:1:"I";s:1:"c";a:2:{i:1;s:7:"poll_id";i:2;s:9:"timestamp";}}}s:6:"column";a:3:{s:7:"poll_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"poll_id";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"voters_ip";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"voters_ip";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"timestamp";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"timestamp";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:9:"polls_log";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:9:"polls_log";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:9:"polls_log";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}}s:6:"column";a:4:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}s:9:"answer_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"answer_id";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"voters_ip";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"voters_ip";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"timestamp";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"timestamp";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:10:"post2shtml";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:10:"post2shtml";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:10:"post2shtml";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}}s:6:"column";a:3:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:3:"PRI";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:4:"vars";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"vars";i:1;s:4:"text";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"time";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"time";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:7:"profile";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:7:"profile";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:2:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:7:"profile";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:13:"slice_user_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:7:"profile";s:1:"n";s:13:"slice_user_id";s:1:"s";s:1:"I";s:1:"c";a:2:{i:1;s:8:"slice_id";i:2;s:3:"uid";}}}s:6:"column";a:6:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}s:8:"slice_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"slice_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"MUL";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:3:"uid";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:3:"uid";i:1;s:11:"varchar(60)";i:2;b:0;i:3;s:0:"";i:4;s:1:"*";i:5;s:0:"";i:6;s:0:"";}}s:8:"property";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"property";i:1;s:11:"varchar(20)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:8:"selector";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"selector";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:5:"value";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"value";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:8:"relation";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:8:"relation";s:11:"primary_key";a:0:{}s:5:"index";a:2:{s:9:"source_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:8:"relation";s:1:"n";s:9:"source_id";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:9:"source_id";}}s:14:"destination_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:8:"relation";s:1:"n";s:14:"destination_id";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:14:"destination_id";}}}s:6:"column";a:3:{s:9:"source_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"source_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"MUL";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:14:"destination_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:14:"destination_id";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:3:"MUL";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:4:"flag";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"flag";i:1;s:7:"int(11)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:8:"rssfeeds";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:8:"rssfeeds";s:11:"primary_key";a:1:{s:7:"feed_id";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:8:"rssfeeds";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:7:"feed_id";}}}s:6:"column";a:4:{s:7:"feed_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"feed_id";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}s:4:"name";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"name";i:1;s:12:"varchar(150)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:10:"server_url";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"server_url";i:1;s:12:"varchar(200)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:8:"slice_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"slice_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:9:"searchlog";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:9:"searchlog";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:2:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:9:"searchlog";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:4:"date";O:17:"AA_Metabase_Index":4:{s:1:"t";s:9:"searchlog";s:1:"n";s:4:"date";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:4:"date";}}}s:6:"column";a:7:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}s:4:"date";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"date";i:1;s:7:"int(14)";i:2;b:1;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:5:"query";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"query";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"found_count";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"found_count";i:1;s:7:"int(11)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"search_time";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"search_time";i:1;s:7:"int(11)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"user";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"user";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"additional1";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"additional1";i:1;s:4:"text";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:4:"site";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:4:"site";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:4:"site";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}}s:6:"column";a:4:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"PRI";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:10:"state_file";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"state_file";i:1;s:12:"varchar(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:9:"structure";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"structure";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"flag";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"flag";i:1;s:7:"int(11)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:9:"site_spot";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:9:"site_spot";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:2:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:9:"site_spot";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:4:"spot";O:17:"AA_Metabase_Index":4:{s:1:"t";s:9:"site_spot";s:1:"n";s:4:"spot";s:1:"s";s:1:"I";s:1:"c";a:2:{i:1;s:7:"site_id";i:2;s:7:"spot_id";}}}s:6:"column";a:5:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}s:7:"spot_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"spot_id";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:7:"site_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"site_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"MUL";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:7:"content";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"content";i:1;s:8:"longtext";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"flag";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"flag";i:1;s:10:"bigint(20)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:5:"slice";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:5:"slice";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:2:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:5:"slice";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:4:"type";O:17:"AA_Metabase_Index":4:{s:1:"t";s:5:"slice";s:1:"n";s:4:"type";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:4:"type";}}}s:6:"column";a:62:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"PRI";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:4:"name";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"name";i:1;s:12:"varchar(100)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:5:"owner";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"owner";i:1;s:11:"varchar(16)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:7:"deleted";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"deleted";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:10:"created_by";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"created_by";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:10:"created_at";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"created_at";i:1;s:10:"bigint(20)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:13:"export_to_all";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:13:"export_to_all";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"type";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"type";i:1;s:13:"varbinary(16)";i:2;b:1;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"template";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"template";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:19:"fulltext_format_top";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:19:"fulltext_format_top";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:15:"fulltext_format";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:15:"fulltext_format";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:22:"fulltext_format_bottom";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:22:"fulltext_format_bottom";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:14:"odd_row_format";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:14:"odd_row_format";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:15:"even_row_format";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:15:"even_row_format";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:15:"even_odd_differ";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:15:"even_odd_differ";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"compact_top";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"compact_top";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:14:"compact_bottom";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:14:"compact_bottom";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:12:"category_top";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"category_top";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:15:"category_format";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:15:"category_format";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:15:"category_bottom";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:15:"category_bottom";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:13:"category_sort";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:13:"category_sort";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"slice_url";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"slice_url";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"d_listlen";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"d_listlen";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"lang_file";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"lang_file";i:1;s:11:"varchar(50)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:15:"fulltext_remove";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:15:"fulltext_remove";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:14:"compact_remove";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:14:"compact_remove";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:16:"email_sub_enable";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:16:"email_sub_enable";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:16:"exclude_from_dir";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:16:"exclude_from_dir";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:15:"notify_sh_offer";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:15:"notify_sh_offer";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:16:"notify_sh_accept";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:16:"notify_sh_accept";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:16:"notify_sh_remove";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:16:"notify_sh_remove";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:21:"notify_holding_item_s";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:21:"notify_holding_item_s";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:21:"notify_holding_item_b";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:21:"notify_holding_item_b";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:26:"notify_holding_item_edit_s";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:26:"notify_holding_item_edit_s";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:26:"notify_holding_item_edit_b";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:26:"notify_holding_item_edit_b";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:25:"notify_active_item_edit_s";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:25:"notify_active_item_edit_s";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:25:"notify_active_item_edit_b";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:25:"notify_active_item_edit_b";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:20:"notify_active_item_s";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:20:"notify_active_item_s";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:20:"notify_active_item_b";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:20:"notify_active_item_b";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:10:"noitem_msg";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"noitem_msg";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:16:"admin_format_top";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:16:"admin_format_top";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:12:"admin_format";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"admin_format";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:19:"admin_format_bottom";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:19:"admin_format_bottom";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:12:"admin_remove";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"admin_remove";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:16:"admin_noitem_msg";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:16:"admin_noitem_msg";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:21:"permit_anonymous_post";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:21:"permit_anonymous_post";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:21:"permit_anonymous_edit";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:21:"permit_anonymous_edit";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:19:"permit_offline_fill";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:19:"permit_offline_fill";i:1;s:11:"smallint(5)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"aditional";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"aditional";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"flag";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"flag";i:1;s:7:"int(11)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:3:"vid";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:3:"vid";i:1;s:7:"int(11)";i:2;b:1;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:12:"gb_direction";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"gb_direction";i:1;s:10:"tinyint(4)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"group_by";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"group_by";i:1;s:11:"varchar(16)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"gb_header";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"gb_header";i:1;s:10:"tinyint(4)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:7:"gb_case";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"gb_case";i:1;s:11:"varchar(15)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:10:"javascript";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"javascript";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:14:"fileman_access";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:14:"fileman_access";i:1;s:11:"varchar(20)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"fileman_dir";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"fileman_dir";i:1;s:11:"varchar(50)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:16:"auth_field_group";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:16:"auth_field_group";i:1;s:11:"varchar(16)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:19:"mailman_field_lists";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:19:"mailman_field_lists";i:1;s:11:"varchar(16)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:16:"reading_password";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:16:"reading_password";i:1;s:12:"varchar(100)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:7:"mlxctrl";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"mlxctrl";i:1;s:13:"varbinary(32)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:11:"slice_owner";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:11:"slice_owner";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:1:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:11:"slice_owner";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}}s:6:"column";a:3:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"name";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"name";i:1;s:8:"char(80)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:5:"email";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"email";i:1;s:8:"char(80)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:9:"toexecute";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:9:"toexecute";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:4:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:9:"toexecute";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:4:"time";O:17:"AA_Metabase_Index":4:{s:1:"t";s:9:"toexecute";s:1:"n";s:4:"time";s:1:"s";s:1:"I";s:1:"c";a:2:{i:1;s:13:"execute_after";i:2;s:8:"priority";}}s:8:"priority";O:17:"AA_Metabase_Index":4:{s:1:"t";s:9:"toexecute";s:1:"n";s:8:"priority";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:8:"priority";}}s:8:"selector";O:17:"AA_Metabase_Index":4:{s:1:"t";s:9:"toexecute";s:1:"n";s:8:"selector";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:8:"selector";}}}s:6:"column";a:8:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}s:7:"created";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"created";i:1;s:10:"bigint(20)";i:2;b:0;i:3;s:0:"";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:13:"execute_after";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:13:"execute_after";i:1;s:10:"bigint(20)";i:2;b:0;i:3;s:3:"MUL";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:7:"aa_user";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"aa_user";i:1;s:11:"varchar(60)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:8:"priority";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"priority";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"MUL";i:4;s:1:"0";i:5;s:0:"";i:6;s:0:"";}}s:8:"selector";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"selector";i:1;s:12:"varchar(255)";i:2;b:0;i:3;s:3:"MUL";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:6:"object";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"object";i:1;s:8:"longtext";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"params";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"params";i:1;s:8:"longtext";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:5:"users";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:5:"users";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:5:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:5:"users";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:4:"type";O:17:"AA_Metabase_Index":4:{s:1:"t";s:5:"users";s:1:"n";s:4:"type";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:4:"type";}}s:4:"mail";O:17:"AA_Metabase_Index":4:{s:1:"t";s:5:"users";s:1:"n";s:4:"mail";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:4:"mail";}}s:4:"name";O:17:"AA_Metabase_Index":4:{s:1:"t";s:5:"users";s:1:"n";s:4:"name";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:4:"name";}}s:2:"sn";O:17:"AA_Metabase_Index":4:{s:1:"t";s:5:"users";s:1:"n";s:2:"sn";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:2:"sn";}}}s:6:"column";a:10:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:7:"int(11)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}s:4:"type";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"type";i:1;s:13:"varbinary(10)";i:2;b:0;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"password";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"password";i:1;s:14:"varbinary(255)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:3:"uid";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:3:"uid";i:1;s:13:"varbinary(40)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"mail";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"mail";i:1;s:8:"char(40)";i:2;b:0;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"name";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"name";i:1;s:8:"char(80)";i:2;b:0;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"description";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"description";i:1;s:9:"char(255)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"givenname";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"givenname";i:1;s:8:"char(40)";i:2;b:0;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:2:"sn";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"sn";i:1;s:8:"char(40)";i:2;b:0;i:3;s:3:"MUL";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"last_mod";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"last_mod";i:1;s:9:"timestamp";i:2;b:0;i:3;s:0:"";i:4;s:17:"CURRENT_TIMESTAMP";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:4:"view";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:4:"view";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:2:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:4:"view";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:8:"slice_id";O:17:"AA_Metabase_Index":4:{s:1:"t";s:4:"view";s:1:"n";s:8:"slice_id";s:1:"s";s:1:"I";s:1:"c";a:1:{i:1;s:8:"slice_id";}}}s:6:"column";a:52:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:16:"int(10) unsigned";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}s:8:"slice_id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"slice_id";i:1;s:13:"varbinary(16)";i:2;b:0;i:3;s:3:"MUL";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:4:"name";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"name";i:1;s:11:"varchar(50)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"type";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"type";i:1;s:11:"varchar(10)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"before";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"before";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"even";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"even";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:3:"odd";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:3:"odd";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:15:"even_odd_differ";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:15:"even_odd_differ";i:1;s:19:"tinyint(3) unsigned";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:13:"row_delimiter";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:13:"row_delimiter";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:5:"after";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:5:"after";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:13:"remove_string";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:13:"remove_string";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:11:"group_title";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"group_title";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"order1";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"order1";i:1;s:13:"varbinary(16)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:12:"o1_direction";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"o1_direction";i:1;s:19:"tinyint(3) unsigned";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"order2";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"order2";i:1;s:13:"varbinary(16)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:12:"o2_direction";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"o2_direction";i:1;s:19:"tinyint(3) unsigned";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"group_by1";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"group_by1";i:1;s:13:"varbinary(16)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:12:"g1_direction";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"g1_direction";i:1;s:19:"tinyint(3) unsigned";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"gb_header";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"gb_header";i:1;s:10:"tinyint(4)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"group_by2";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"group_by2";i:1;s:13:"varbinary(16)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:12:"g2_direction";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"g2_direction";i:1;s:19:"tinyint(3) unsigned";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:10:"cond1field";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"cond1field";i:1;s:13:"varbinary(16)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:7:"cond1op";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"cond1op";i:1;s:13:"varbinary(10)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"cond1cond";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"cond1cond";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:10:"cond2field";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"cond2field";i:1;s:13:"varbinary(16)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:7:"cond2op";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"cond2op";i:1;s:13:"varbinary(10)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"cond2cond";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"cond2cond";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:10:"cond3field";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"cond3field";i:1;s:13:"varbinary(16)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:7:"cond3op";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"cond3op";i:1;s:13:"varbinary(10)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"cond3cond";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"cond3cond";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:7:"listlen";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:7:"listlen";i:1;s:16:"int(10) unsigned";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:8:"scroller";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:8:"scroller";i:1;s:19:"tinyint(3) unsigned";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:13:"selected_item";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:13:"selected_item";i:1;s:19:"tinyint(3) unsigned";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:12:"modification";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"modification";i:1;s:16:"int(10) unsigned";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"parameter";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"parameter";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"img1";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"img1";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"img2";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"img2";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"img3";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"img3";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"img4";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"img4";i:1;s:12:"varchar(255)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:4:"flag";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:4:"flag";i:1;s:16:"int(10) unsigned";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:9:"aditional";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:9:"aditional";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:10:"aditional2";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"aditional2";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:10:"aditional3";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"aditional3";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:10:"aditional4";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"aditional4";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:10:"aditional5";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"aditional5";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:10:"aditional6";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"aditional6";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:10:"noitem_msg";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:10:"noitem_msg";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:12:"group_bottom";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:12:"group_bottom";i:1;s:8:"longtext";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"field1";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"field1";i:1;s:13:"varbinary(16)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"field2";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"field2";i:1;s:13:"varbinary(16)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:6:"field3";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:6:"field3";i:1;s:13:"varbinary(16)";i:2;b:1;i:3;s:0:"";i:4;N;i:5;s:0:"";i:6;s:0:"";}}s:13:"calendar_type";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:13:"calendar_type";i:1;s:12:"varchar(100)";i:2;b:1;i:3;s:0:"";i:4;s:3:"mon";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}s:15:"wizard_template";O:17:"AA_Metabase_Table":5:{s:9:"tablename";s:15:"wizard_template";s:11:"primary_key";a:1:{s:2:"id";b:1;}s:5:"index";a:2:{s:7:"PRIMARY";O:17:"AA_Metabase_Index":4:{s:1:"t";s:15:"wizard_template";s:1:"n";s:7:"PRIMARY";s:1:"s";s:1:"P";s:1:"c";a:1:{i:1;s:2:"id";}}s:3:"dir";O:17:"AA_Metabase_Index":4:{s:1:"t";s:15:"wizard_template";s:1:"n";s:3:"dir";s:1:"s";s:1:"U";s:1:"c";a:1:{i:1;s:3:"dir";}}}s:6:"column";a:3:{s:2:"id";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:2:"id";i:1;s:11:"tinyint(10)";i:2;b:0;i:3;s:3:"PRI";i:4;N;i:5;s:14:"auto_increment";i:6;s:0:"";}}s:3:"dir";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:3:"dir";i:1;s:9:"char(100)";i:2;b:0;i:3;s:3:"UNI";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}s:11:"description";O:18:"AA_Metabase_Column":1:{s:1:"c";a:7:{i:0;s:11:"description";i:1;s:9:"char(255)";i:2;b:0;i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";}}}s:5:"flags";N;}}}');
        }
        return $instance;
    }

    /** Returns array of all table names */
    function getTableNames() {
        return array_keys($this->tables);
    }

    /** Returns array of keys for given table */
    function getKeys($tablename) {
        $table = $this->tables[$tablename];
        return is_object($table) ? $table->getKeys() : array();
    }

    /** is the $columnname the key for given table */
    function isKey($tablename, $columnname) {
        $table = $this->tables[$tablename];
        return is_object($table) ? $table->isKey($columnname) : false;
    }

    /** Returns array of all columns */
    function getColumnNames($tablename) {
        $table = $this->tables[$tablename];
        return is_object($table) ? $table->getColumnNames() : array();
    }

    /** Is the $columnname the column in the $tablename? */
    function isColumn($tablename, $columnname) {
        $table = $this->tables[$tablename];
        return is_object($table) ? $table->isColumn($columnname) : false;
    }

    function fillKeys(&$data, $identifier) {

        $module_id  = $identifier->getModuleId();
        $tablename  = $identifier->getTable();
        $row        = $identifier->getRow();

        if (!AA_Metabase::isTableKeysSupported($tablename)) {
            // you can't use this function for that the table - this is programmers mistake - correct the code
            echo "table $tablename not supported in AA_Metabase::fillKeys()";
            exit;
        }
        $table      = $this->tables[$tablename];
        $keys       = $table->getKeys();

        // make sense just for single-keys or two-key, where second key
        // is replaced by $module_field below (for field table)
        $module_field = AA_Metabase::getModuleField($tablename);
        foreach ($keys as $key) {
            if ($key == $module_field) {
                // we will assign it in nex step - reassignModule()
                continue;
            }
            $data[$key] = AA_Metabase::isPacked($tablename, $key) ? pack_id($row) : $row;
        }
        $this->reassignModule($data, $tablename, $module_id);
    }

    /** changes the column of the table which identifies to which module it
     *  belongs. That way you just move the data to another module
     *  It modifies $data parameter
     */
    function reassignModule(&$data, $tablename, $module_id) {
        $module_field = AA_Metabase::getModuleField($tablename);
        if ($module_field) {
            $data[$module_field] = AA_Metabase::isPacked($tablename, $module_field) ? pack_id($module_id) : $module_id;
        }
    }

    /** static method */
    function isTableKeysSupported($tablename) {
        return in_array($tablename, AA_Metabase::_getSupportedTables());
    }

    /** static method */
    function isPacked($tablename, $column) {
        return in_array($column, AA_Metabase::getPacked($tablename));
    }

    /** static method */
    function getModuleField($tablename) {
        static $MODULE_KEYS = array(
                                     'alerts_collection'   => 'module_id',
                                     'constant_slice'      => 'slice_id',
                                     'ef_permissions'      => 'slice_id',
                                     'email'               => 'owner_module_id',
                                     'email_notify'        => 'slice_id',
                                     'external_feeds'      => 'slice_id',
                                     'field'               => 'slice_id',
                                     'item'                => 'slice_id',
                                     'links'               => 'id',
                                     'module'              => 'id',
                                     'mysql_auth_group'    => 'slice_id',
                                     'mysql_auth_userinfo' => 'slice_id',
                                     'polls'               => 'module_id',
                                     'profile'             => 'slice_id',
                                     'rssfeeds'            => 'slice_id',
                                     'site'                => 'id',
                                     'site_spot'           => 'site_id',
                                     'slice'               => 'id',
                                     'view'                => 'slice_id'
            );
        return $MODULE_KEYS[$tablename];
    }



    /** static method
     *  @todo - would be probably better to move it to AA_Metabase_Table
     *  @todo - convert to static class members for PHP5
     **/
    function getPacked($tablename) {
        static $PACKED = array(
                  'alerts_collection'   => array('module_id','slice_id'),
                  'constant'            => array('id','ancestors'), // ancestors are multiple - joined!
                  'constant_slice'      => array('slice_id'),
                  'content'             => array('item_id'),
                  'discussion'          => array('id','item_id'),
                  'ef_categories'       => array('category_id','target_category_id'),
                  'ef_permissions'      => array('slice_id'),
                  'email'               => array('owner_module_id'),
                  'email_notify'        => array('slice_id'),
                  'external_feeds'      => array('slice_id', 'remote_slice_id'),
                  'feedmap'             => array('from_slice_id', 'to_slice_id'),
                  'feedperms'           => array('from_id','to_id'),
                  'feeds'               => array('from_id','to_id','category_id','to_category_id'),
                  'field'               => array('slice_id'),
                  'hit_long_id'         => array('id'),
                  'item'                => array('id','slice_id'),  // slice_id is not part of key, here
                  'jump'                => array('slice_id', 'dest_slice_id'),
                  'links'               => array('id'),             // special meaning of first characters - category!!!
                  'module'              => array('id', 'owner'),
                  'mysql_auth_group'    => array('slice_id'),
                  'mysql_auth_userinfo' => array('slice_id'),
                  'offline'             => array('id'),
                  'polls'               => array('module_id'),
                  'polls_design'        => array('module_id'),
                  'profile'             => array('slice_id'),
                  'relation'            => array('source_id', 'destination_id'),
                  'rssfeeds'            => array('slice_id'),
                  'site'                => array('id'),
                  'site_spot'           => array('site_id'),
                  'slice'               => array('id', 'owner', 'mlxctrl'),
                  'slice_owner'         => array('id'),
                  'subscriptions'       => array('slice_owner'),
                  'view'                => array('slice_id')
                  );
        return isset($PACKED[$tablename]) ? $PACKED[$tablename] : array();
    }

    /** static method
     *  @todo - would be probably better to move it to AA_Metabase_Table
     *  @todo - convert to static class members for PHP5
     **/
    function &_getSupportedTables() {
        static $SUPPORTED_TABLES = array(
                  // single keys
                  'alerts_admin', 'alerts_collection', 'alerts_filter',
                  'auth_log', 'auth_user', 'change', 'change_record',
                  'central_conf', 'constant', 'constant_slice', 'cron',
                  'db_sequence', 'discussion', 'email', 'email_auto_user',
                  'event', 'external_feeds', 'groups', 'item', 'jump', 'links',
                  'links_cat_cat', 'links_categories', 'links_languages',
                  'links_link_cat', 'links_links', 'links_regions', 'log',
                  'module', 'mysql_auth_user', 'mysql_auth_userinfo', 'nodes',
                  'offline', 'object_float', 'object_integer', 'object_text',
                  'pagecache', 'pagecache_str2find', 'polls', 'polls_answer', 'polls_design',
                  'polls_log', 'post2shtml', 'profile', 'rssfeeds', 'searchlog',
                  'site', 'site_spot', 'slice', 'slice_owner', 'toexecute',
                  'users', 'view', 'wizard_template', 'wizard_welcome',
                  // supported table using double keys (slice_id,id)
                  'field'
                  // unsupported table using triple keys (slice_id,uid,`function`)
                  // 'email_notify'
            );
             // search and replace should be done here
             // feeds from_id to_id
             // feedmap   from_slice_id, to_slice_id
             // feedperms from_id,       to_id
             // relation  source_id,     destination_id
         return $SUPPORTED_TABLES;
    }

    /** static method
     *  @todo - would be probably better to move it to AA_Metabase_Table
     *  @todo - convert to static class members for PHP5
     **/
    function &_getUnsupportedTables() {
        static $UNSUPPORTED_TABLES = array(
                  'active_sessions', 'alerts_collection_filter',
                  'alerts_collection_howoften', 'auth_group', 'content',
                  'ef_categories', 'ef_permissions', 'email_notify', 'feedmap', 'feedperms',
                  'hit_archive', 'hit_long_id', 'hit_short_id', 'links_changes',
                  'links_link_lang', 'links_link_reg', 'membership',
                  'mysql_auth_group', 'mysql_auth_user_group',
                  'mysql_auth_userlog', 'perms', 'polls_ip_lock',
                  'relation', 'subscriptions');
        return $UNSUPPORTED_TABLES;
    }

    function doUpdate($tablename, $data) {
        $varset     = new Cvarset();
        $table      = $this->tables[$tablename];

        foreach ( $data as $key => $val ) {
            // @todo - do some validity checks for the data
            if ($table->isKey($key)) {
                $varset->addkey($key, 'text', $val);
            } else {
                $varset->add($key, 'text', $val);
            }
        }
        return $varset->doUpdate($tablename);
    }

    function doInsert($tablename, $data, $nohalt=null) {
        $varset     = new Cvarset();
        $varset->resetFromRecord($data);
        return $varset->doInsert($tablename, $nohalt);
    }

    function doDelete($tablename, $data) {
        $varset     = new Cvarset();
        $table      = $this->tables[$tablename];
        $table_keys = $table->getKeys();

        foreach ( $table_keys as $key ) {
            // @todo - do some validity checks for the data
            if (!$data[$key]) {
                // you can't use this function for that table - this is programmers mistake - correct the code
                echo "Missing key $key for table $tablename in AA_Metabase::doDelete()";
                exit;
            }
            $varset->addkey($key, 'text', $data[$key]);
        }
        return $varset->doDelete($tablename);
    }

    /** analyzeStructure - reads table and column definitions form database */
    function loadFromDb() {
        $db     = getDb();
        $tables = $db->table_names();
        foreach ($tables as $table) {
            $this->tables[$table['table_name']] = AA_Metabase_Table::factoryFromDb($table['table_name']);
        }
    }

    function getCreateSql($tablename='', $prefix='') {
        $sql_parts = array();
        $tables = $tablename ? array($this->tables[$tablename]) : $this->tables;
        foreach ($tables as $table) {
            if ( is_object($table) ) {
                $sql_parts[] = $table->getCreateSql($prefix);
            }
        }
        return join("\n",$sql_parts);
    }

    /** returns database structure definition as PHP code (array) */
    function getDefinition() {
        $defs = array();
        foreach ($this->tables as $table) {
            $defs[]= $table->getDefinition();
        }
        $ret = "array(";
        $ret .= join(",", $defs);
        $ret .= "\n);\n";
        return $ret;
    }

    /** addTableFromSql function
     * @param $tablename
     * @param $create_SQL
     */
    function addTableFromSql($tablename, $create_SQL) {
        $this->tables[$tablename] = new AA_Metabase_Table;
        $this->tables[$tablename]->setFromSQL($tablename, $create_SQL);
    }

    /** getSearchArray function
     *
     */
    function getSearchArray($tablename) {
        $i = 0;
        $table         = $this->tables[$tablename];
        $table_columns = $table->getColumnNames();
        foreach ($table_columns as $column_name) { // in priority order
            $field_type = 'text';    // @todo - get the type from field type
            // we can hide the field, if we put in fields.search_pri=0
            $search_pri = ++$i;
                               //             $name,        $field,       $operators, $table, $search_pri, $order_pri
            $ret[$column_name] = GetFieldDef( $column_name, $column_name, $field_type, false, $search_pri, $search_pri);
        }
        return $ret;
    }

    /** generateAliases
     *
     */
    function generateAliases($tablename) {
        $aliases = array();
        $table         = $this->tables[$tablename];
        $table_columns = $table->getColumnNames();
        foreach ($table_columns as $column_name) { // in priority order
            // @todo - make alias field type aware
            $aliases["_#". substr(str_pad(strtoupper($column_name),8,'_'),0,8)] = GetAliasDef( "f_h", $column_name, $column_name);
        }
        return $aliases;
    }

    /** @return rows from $tablename for given $module_id in form
     *          ret[id] = array('column'=>value, ...)
     *  @param  ids are always UNPACKED (as keys as well as in values)
     */
    function getModuleRows($tablename, $module_id) {
        $JOIN = array(
          // @todo - do the 'constant' better - check the fields setting, and get all the constants used
          'constant'   => array('scr_field' => 'group_id', 'dest_table' => 'constant_slice', 'dest_field' => 'group_id'),

          'content'    => array('scr_field' => 'item_id',  'dest_table' => 'item',           'dest_field' => 'id'),
          'discussion' => array('scr_field' => 'item_id',  'dest_table' => 'item',           'dest_field' => 'id'),

          'alerts_collection_filter'   => array('scr_field' => 'collectionid',  'dest_table' => 'alerts_collection', 'dest_field' => 'id'),
          'alerts_collection_howoften' => array('scr_field' => 'collectionid',  'dest_table' => 'alerts_collection', 'dest_field' => 'id')
        );

        $module_table = $tablename;
        $join_sql     = '';

        if ( isset($JOIN[$tablename]) ) {
            $j = $JOIN[$tablename];
            $module_table = $j['dest_table'];
            $join_sql     = "INNER JOIN $module_table ON $tablename.". $j['scr_field']."=$module_table.".$j['dest_field'];
        }

        $module_field = $this->getModuleField($module_table);
        if (!$module_field) {
            // you can't use this function for that table - this is programmers mistake - correct the code
            echo "table $tablename not supported in AA_Metabase::getModuleRows() - no module field";
            exit;
        }

        $table_keys   = $this->getKeys($tablename);
        if (count($table_keys) == 1) {
            $table_key = $table_keys[0];
        }
        elseif ((count($table_keys) == 2) AND in_array($module_field, $table_keys)) {
            // two keys, but one of them is module_id, which is OK
            $table_key = ($table_keys[0] == $module_field) ? $table_keys[1] : $table_keys[0];
        }
        else {
            // you can't use this function for that table - this is programmers mistake - correct the code
            //echo "table $tablename not supported in AA_Metabase::getModuleRows() - too much keys";
            //exit;
            $table_key = '';  // we do not use the key
        }

        $module_val   = $this->isPacked($module_table, $module_field) ? q_pack_id($module_id) : $module_id;
        $key_used     = !$table_key ? '' : ($this->isPacked($tablename, $table_key) ? "unpack:$table_key" : $table_key);

        $SQL = "SELECT $tablename.* FROM $tablename $join_sql WHERE $module_table.$module_field = '$module_val'";

        $ret            = GetTable2Array($SQL, $key_used, 'aa_fields');
        if (!is_array($ret)) {
            $ret = array();
        }

        $this->unpackIds($tablename, $ret);
        return $ret;
    }

    function unpackIds($tablename, &$data) {
        $packed_columns = $this->getPacked($tablename);
        foreach ($packed_columns as $column) {
            foreach ($data as $k => $v) {
                $data[$k][$column] = unpack_id($v[$column]);
            }
        }
    }

    function packIds($tablename, &$data) {
        $packed_columns = $this->getPacked($tablename);
        foreach ($packed_columns as $column) {
            foreach ($data as $k => $v) {
                $data[$k][$column] = pack_id($v[$column]);
            }
        }
    }


    /** Compares two metabases - this and the $metabase supplied by the parameter
     *  You can use it to check, which tables should be updated
     *  @param $metabase - the second metabase which will be compated to $this
     **/
    function compare($metabase) {
        $diffs = array();

        // for us are varchar and char the same - some tables are never converted
        // to char in some versions of MySQL, so the test is always false
        $eq_vars   = array('varchar', " default '                '", " default '                                '", " default '0'", "timestamp NOT NULL", 'varbinary');
        $eq_novars = array('char'   , '', '', '', 'timestamp', 'binary');

        foreach ($this->tables as $tablename => $table) {
            $table_sql_1 = $table->getCreateSql();
            $table_sql_2 = $metabase->getCreateSql($tablename);

            $diffs[$tablename] = array('equal'  => (str_replace($eq_vars, $eq_novars, $table_sql_1) == str_replace($eq_vars, $eq_novars, $table_sql_2)),
                                       'table1' => $table_sql_1,
                                       'table2' => $table_sql_2
                                      );
        }
        return $diffs;
    }

    /** getContent function for loading content of specified table for manager
     *  class
     *
     * Loads data from database for given table ids (called in itemview class)
     * and stores it in the 'Abstract Data Structure' for use with 'item' class
     *
     * @see GetItemContent(), itemview class, item class
     * @param array $settings array - just one parameter: table, where to search
     * @param array $zids array if ids to get from database
     * @return array - Abstract Data Structure containing the links data
     *                 {@link http://apc-aa.sourceforge.net/faq/#1337}
     */
    function getContent($settings, $zids) {
        $content = array();
        $ret     = array();

        $tablename   = $settings['table'];
        $metabase    = AA_Metabase::singleton();
        $keys        = $metabase->getKeys($tablename);
        if (count($keys) != 1) {
            // you can't use this function for that table - this is programmers mistake - correct the code
            echo "Missing key for table $tablename in AA_Metabase::getContent()";
            exit;
        }
        $key = $keys[0];


        // construct WHERE clausule
        $sel_in = $zids->sqlin( false , true);  // asis
        $SQL = "SELECT * FROM $tablename WHERE $key $sel_in";
        StoreTable2Content($content, $SQL, '', $key);
        // it is unordered, so we have to sort it:
        for($i=0; $i<$zids->count(); $i++ ) {
            $ret[(string)$zids->id($i)] = $content[$zids->id($i)];
        }
        return $ret;
    }

    /** Central_QueryZids - Finds link IDs for links according to given  conditions
     *  @param array  $settings - array - just one parameter: table, where to search
     *  @param array  $set      - AA_Set object which specifies Sortorder and Conditions
     *                          - there we store also BINs conditions, since each
     *                            table/module can use different Bins AND for other
     *                            the idea of BINs makes no sense at all
     *  @global int  $QueryIDsCount - set to the count of IDs returned
     *  @global bool $debug=1       - many debug messages
     *  @global bool $nocache       - do not use cache, even if use_cache is set
     */
    function queryZids($settings, $set) {
        global $debug;                 // displays debug messages
        global $nocache;               // do not use cache, if set

        $tablename = $settings['table'];
        $conds     = $set->getConds();
        $sort      = $set->getSort();

        if ( $debug ) huhl( "<br>Conds:", $conds, "<br>--<br>Sort:", $sort, "<br>--");

        $metabase    = AA_Metabase::singleton();

        $fields      = $metabase->getSearchArray($tablename);
        $join_tables = array();   // not used in this function

        $keys        = $metabase->getKeys($tablename);
        if (count($keys) != 1) {
            // you can't use this function for that table - this is programmers mistake - correct the code
            echo "Missing key for table $tablename in AA_Metabase::queryZids()";
            exit;
        }
        $key = $keys[0];

        $SQL    = "SELECT DISTINCT $key FROM $tablename ";
//        $SQL .= CreateBinCondition($type, $tablename);
        $where  = MakeSQLConditions($fields, $conds, $fields, $join_tables);
        $SQL   .= ($where ? "WHERE (1=1) $where" : '');
        $SQL   .= MakeSQLOrderBy($fields, $sort, $join_tables);

        return GetZidsFromSQL($SQL, $key);
    }

    function queryCount($settings, $set) {
        $tablename = $settings['table'];
        $conds     = $set->getConds();

        $metabase    = AA_Metabase::singleton();

        $fields      = $metabase->getSearchArray($tablename);
        $join_tables = array();   // not used in this function

        $SQL    = "SELECT count(*) as count FROM $tablename ";
        $where  = MakeSQLConditions($fields, $conds, $fields, $join_tables);
        $SQL   .= ($where ? "WHERE (1=1) $where" : '');

        return GetTable2Array($SQL, 'aa_first', 'count');
    }


    /** Get tabledit cofiguration for easy edit and add to the table */
    function getTableditConf($tablename) {
        $ret = array (
            "table"     => $tablename,
            "type"      => "edit",
//          "mainmenu"  => "modadmin",
//          "submenu"   => "design",
            "readonly"  => false,
            "addrecord" => false,
//          "cond"      => CheckPerms( $auth->auth["uid"], "slice", $slice_id, PS_MODP_EDIT_DESIGN),
//          "title"     => $title,
//          "caption"   => $title,
            "attrs"     => array ("table"=>"border=0 cellpadding=3 cellspacing=0 bgcolor='".COLOR_TABBG."'"),
//          "gotoview"  => "polls_designs_edit",
        );

        $table         = $this->tables[$tablename];
        $table_columns = $table->getColumnNames();
        foreach ($table_columns as $column_name) { // in priority order
            $field_type = 'text';    // @todo - get the type from field type
            $ret['fields'][$column_name] = array('caption' => $column_name,
                                                 'view'    => array('type' => $field_type)
                                                );
            // @todo - do better check - based on table setting
            if ($column_name = 'id') {
                $ret['fields'][$column_name]['view']['readonly'] = true;
            }
        }
        return $ret;
    }

    /** generate manager from database structure
     * @param $classname
     * @param $params
     */
    function getManagerConf($tablename, $actions=null, $switches=null) {
        $aliases       = $this->generateAliases($tablename);
        $search_fields = $this->getSearchArray($tablename);

        $manager_settings = array(
             'show'      =>  MGR_ACTIONS | MGR_SB_SEARCHROWS | MGR_SB_ORDERROWS | MGR_SB_BOOKMARKS,    // MGR_ACTIONS | MGR_SB_SEARCHROWS | MGR_SB_ORDERROWS | MGR_SB_BOOKMARKS
             'searchbar' => array(
                 'fields'               => $search_fields,
                 'search_row_count_min' => 1,
                 'order_row_count_min'  => 1,
                 'add_empty_search_row' => true,
                 'function'             => false  // name of function for aditional action hooked on standard filter action
                                 ),
             'scroller'  => array(
                 'listlen'              => EDIT_ITEM_COUNT
                                 ),
             'itemview'  => array(
                 'manager_vid'          => false,    // $slice_info['manager_vid'],      // id of view which controls the design
                 'format'               => array(    // optionaly to manager_vid you can set format array
                     'compact_top'      => '<table border="0" cellspacing="0" cellpadding="5">
                                            <tr>
                                              <th width="30">&nbsp;</td>
                                              <th>'.join("</th>\n<th>", array_keys($search_fields)).'</th>
                                            </tr>
                                            ',
                     'category_sort'    => false,
                     'category_format'  => "",
                     'category_top'     => "",
                     'category_bottom'  => "",
                     'even_odd_differ'  => false,
                     'even_row_format'  => "",
                     'odd_row_format'   => '
                                            <tr class=tabtxt>
                                              <td width="30"><input type="checkbox" name="chb[x_#ID______]" value=""></td>
                                              <td class=tabtxt>'.join("</td>\n<td class=tabtxt>", array_keys($aliases)).'</td>
                                            </tr>
                                           ',
                     'compact_remove'   => "",
                     'compact_bottom'   => "</table>"
                                  ),
                 'fields'               => $search_fields,
                 'aliases'              => $aliases,
                                           //    static class method               , first parameter to the method
                 'get_content_funct'    => array(array('AA_Metabase', 'getContent'), array('table'=>$tablename))
                                 ),
             'actions'   => $actions,
             'switches'  => $switches,
             'bin'       => 'app',
             'messages'  => array(
                 'title'       => _m('Manage %1', array($tablename))
                                 )
                 );

        return $manager_settings;
    }
}

?>
