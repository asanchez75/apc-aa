<?php
//$Id: se_csv_import.php3 2290 2006-07-27 15:10:35Z honzam $
/*
Copyright (C) 1999, 2000 Association for Progressive Communications
http://www.apc.org/

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program (LICENSE); if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

require_once "../include/init_page.php3";
require_once AA_INC_PATH. 'formutil.php3';

if (!IsSuperadmin()) {
    MsgPageMenu($sess->url(self_base())."index.php3", _m("You don't have permissions to do optimize tests."), "admin");
    exit;
}

/** @todo this class should be abstract after we switch to PHP5 */
class Optimize {
    var $messages = array();
    function name()         {}
    function description()  {}
    function test()         {}
    function repair()       {}

    function message($text) {
        $this->messages[] = $text;
    }

    function report()       {
        return join('<br>', $this->messages);
    }

    function clear_report() {
        unset($this->messages);
        $this->messages = array();
    }
}

/** Testing if relation table contain records, where values in both columns are
 *  identical (which was bug fixed in Jan 2006)
 */
class Optimize_category_sort2group_by extends Optimize {

    function name() {
        return _m("Convert slice.category_sort to slice.group_by");
    }

    function description() {
        return _m("In older version of AA we used just category fields for grouping items. Now it is universal, so boolean category_sort is not enough. We use newer group_by field for quite long time s most probably all your slices are already conevrted.");
    }

    function test() {
        $SQL         = "SELECT name FROM slice WHERE category_sort>0 AND ((group_by IS NULL) OR (group_by=''))";
        $slice_names = GetTable2Array($SQL, '', 'name');
        if ($slice_names AND (count($slice_names) > 0)) {
            $this->message( _m('%1 slices are not converted', array(count($slice_names))). '<br> &nbsp; '. join('<br> &nbsp; ',$slice_names));
            return false;
        }
        $this->message(_m('All slices are already converted'));
        return true;
    }

    function repair() {
        $db      = getDb();
        $SQL    = "SELECT id FROM slice WHERE category_sort>0 AND ((group_by IS NULL) OR (group_by=''))";
        $slices = GetTable2Array($SQL, '', 'id');
        foreach ($slices as $p_slice_id) {
            $q_slice_id = quote($p_slice_id);

            $SQL       = "SELECT id FROM field WHERE id LIKE 'category.......%' AND slice_id='$q_slice_id'";
            $cat_field = GetTable2Array($SQL, "aa_first", 'id');
            if ($cat_field) {
                // number 2 represents 'a' - ascending (because gb_direction in number)
                $SQL = "UPDATE slice SET group_by='". quote($cat_field) ."', gb_direction=2, gb_header=0 WHERE id='$q_slice_id'";
            } else {
                $SQL = "UPDATE slice SET group_by='', gb_direction=0, gb_header=0 WHERE id='$q_slice_id'";
            }
            $db->query($SQL);   // correct it
        }
        // fix all category_sort
        $SQL = "UPDATE slice SET category_sort=0";
        $db->query($SQL);
        freeDb($db);
        return true;
    }
}



/** Testing if relation table contain records, where values in both columns are
 *  identical (which was bug fixed in Jan 2006)
 */
class Optimize_db_relation_dups extends Optimize {

    function name() {
        return _m("Relation table duplicate records");
    }

    function description() {
        return _m("Testing if relation table contain records, where values in both columns are identical (which was bug fixed in Jan 2006)");
    }

    function test() {
        $SQL = 'SELECT count(*) as err_count FROM `relation` WHERE `source_id`=`destination_id`';
        $err_count = GetTable2Array($SQL, "aa_first", 'err_count');
        if ($err_count > 0) {
            $this->message( _m('%1 duplicates found', array($err_count)) );
            return false;
        }
        $this->message(_m('No duplicates found'));
        return true;
    }

    function repair() {
        $db  = getDb();
        $SQL = 'DELETE FROM `relation` WHERE `source_id`=`destination_id`';
        $db->query($SQL);
        freeDb($db);
        return true;
    }
}


/** Fix user login problem, constants editiong problem, ...
 *  Replaces binary fields by varbinary and removes trailing zeros
 *  Needed for MySQL > 5.0.17
 */
class Optimize_db_binary_traing_zeros extends Optimize {

    function name() {
        return _m("Fix user login problem, constants editiong problem, ...");
    }

    function description() {
        return _m("Replaces binary fields by varbinary and removes trailing zeros. Needed for MySQL > 5.0.17");
    }

    function test() {
        return true;
    }

    function repair() {
        $this->_fixTable('constant','id','varbinary(16) NOT NULL default \'\'');
        $this->_fixTable('constant','group_id','varbinary(16) NOT NULL default \'\'');
        $this->_fixTable('constant','class','varbinary(16) default NULL');
        $this->_fixTable('alerts_collection','module_id',"varbinary(16) NOT NULL default ''");
        $this->_fixTable('alerts_collection','slice_id',"varbinary(16) default NULL");
        $this->_fixTable('alerts_collection_filter','collectionid',"varbinary(6) NOT NULL default ''");
        $this->_fixTable('alerts_collection_howoften','collectionid',"varbinary(6) NOT NULL default ''");
        $this->_fixTable('constant_slice','slice_id',"varbinary(16) default NULL");
        $this->_fixTable('constant_slice','group_id',"varbinary(16) NOT NULL default ''");
        $this->_fixTable('email_notify','slice_id',"varbinary(16) NOT NULL default ''");
        $this->_fixTable('item','id',"varbinary(16) NOT NULL default ''");
        $this->_fixTable('item','slice_id',"varbinary(16) NOT NULL default ''");
        $this->_fixTable('links','id',"varbinary(16) NOT NULL default ''");
        $this->_fixTable('membership','memberid','varbinary(32) NOT NULL');
        $this->_fixTable('module','id',"varbinary(16) NOT NULL default ''");
        $this->_fixTable('module','owner',"varbinary(16) NOT NULL default ''");
        $this->_fixTable('module','app_id',"varbinary(16) default NULL");
        $this->_fixTable('offline','id',"varbinary(16) NOT NULL default ''");
        $this->_fixTable('offline','digest',"varbinary(32) NOT NULL default ''");
        $this->_fixTable('perms','objectid',"varbinary(32) NOT NULL default ''");
        $this->_fixTable('perms','userid',"varbinary(32) NOT NULL default '0'");
        $this->_fixTable('perms','perm',"varbinary(32) NOT NULL default ''");
        $this->_fixTable('polls_log','votersIP',"varbinary(16) NOT NULL default ''");
        $this->_fixTable('slice_owner','id',"varbinary(16) NOT NULL default ''");
        $this->_fixTable('subscriptions','slice_owner',"varbinary(16) default NULL");
        $this->_fixTable('users','type',"varbinary(10) NOT NULL default ''");
        $this->_fixTable('users','password',"varbinary(30) NOT NULL default ''");
        $this->_fixTable('users','uid',"varbinary(40) NOT NULL default ''");
        return true;
    }

    function _fixTable($table, $field, $definition) {
        $db  = getDb();
        $SQL = "ALTER TABLE `$table` CHANGE `$field` `$field` $definition";
        $this->messages($SQL);
        $db->query($SQL);
        $SQL = "UPDATE `$table` SET $field=TRIM(TRAILING '\0' FROM $field)";
        $this->messages($SQL);
        $db->query($SQL);
        freeDb($db);
    }
}

/** There was change in Reader management functionality in AA v2.8.1 */
class Optimize_readers_login2id extends Optimize {

    function name() {
        return _m("Convert Readers login to reader id");
    }

    function description() {
        return _m("There was change in Reader management functionality in AA v2.8.1, so readers are not internaly identified by its login, but by reader ID (item ID of reader in Reader slice). This is much more powerfull - you can create relations just as in normal slice. It works well without any change. The only problem is, if you set any slice to be editable by users from Reader slice. In that case the fields edited_by........ and posted_by........ are filled by readers login instead of reader id. You can fix it by \"Repair\".");
    }

    function test() {
        $this->clear_report();
        $ret = true;  // which means OK

        // get all readers in array: id => arrary( name => ...)
        $readers = FindReaderUsers('');
        $posted_by_found = $this->_test_field($readers, 'posted_by');
        if (count($posted_by_found) > 0) {
            $this->message(_m('%1 login names from reader slice found as records in item.posted_by which is wrong (There should be reader ID from AA v2.8.1). "Repair" will correct it.', array(count($posted_by_found))));
            $ret = false;
        }
        $edited_by_found = $this->_test_field($readers, 'edited_by');
        if (count($edited_by_found) > 0) {
            $this->message(_m('%1 login names from reader slice found as records in item.edited_by which is wrong (There should be reader ID from AA v2.8.1). "Repair" will correct it.', array(count($edited_by_found))));
            $ret = false;
        }
        return $ret;
    }

    /** test if we can find an item which was edited by reader and is idetified
     *  by login name (instead of item_id)
     *  @returns array of such users
     */
    function _test_field(&$readers, $item_field) {
        // get posted_by, edit_by, ... array:  posted_by => 1
        $SQL     = "SELECT DISTINCT $item_field FROM item";
        $editors = GetTable2Array($SQL, $item_field, 'aa_mark');
        $ret     = array();
        foreach ( $readers as $r_id => $reader ) {
            if ($reader['name'] AND isset($editors[$reader['name']])) {
                $ret[$r_id] = $reader['name'];
            }
        }
        return $ret;
    }

    function repair() {
        $this->clear_report();

        // get all readers in array: id => arrary( name => ...)
        $readers = FindReaderUsers('');
        $posted_by_found = $this->_test_field($readers, 'posted_by');
        $edited_by_found = $this->_test_field($readers, 'edited_by');
        $db = getDb();
        if (count($posted_by_found) > 0) {
            foreach ($posted_by_found as $r_id => $r_login ) {
                $SQL = "UPDATE item SET posted_by = '$r_id' WHERE posted_by = '$r_login'";
                $db->query($SQL);
                $this->message(_m('Column item.posted_by updated for %1 (id: %2).', array($r_login, $r_id)));
            }
        }
        if (count($edited_by_found) > 0) {
            foreach ($edited_by_found as $r_id => $r_login ) {
                $SQL = "UPDATE item SET edited_by = '$r_id' WHERE edited_by = '$r_login'";
                $db->query($SQL);
                $this->message(_m('Column item.edited_by updated for %1 (id: %2).', array($r_login, $r_id)));
            }
        }
        return true;
    }
}

/** There was change in Reader management functionality in AA v2.8.1 */
class Optimize_database_structure extends Optimize {

    function name() {
        return _m("Checks if all tables have right columns and indexes");
    }

    function description() {
        return _m("We are time to time add new table or collumn to existing table in order we can support new features. This option will update the datastructure to the last one. No data will be lost.");
    }

    function test() {
        $this->clear_report();
        $ret = true;  // which means OK

        $db = getDb();
        foreach ( $db->table_names() as $table ) {
            $table_name = $table['table_name'];
            $db->query("SHOW CREATE TABLE $table_name" );
            if (!$db->next_record()) {
                continue;
            }
            $table_SQL = $db->f('Create Table');

        }
        freeDb($db);
        return $ret;

    }


    function repair() {
        $this->clear_report();

        // get all readers in array: id => arrary( name => ...)
        $readers = FindReaderUsers('');
        $posted_by_found = $this->_test_field($readers, 'posted_by');
        $db = getDb();
        if (count($posted_by_found) > 0) {
            foreach ($posted_by_found as $r_id => $r_login ) {
                $SQL = "UPDATE item SET posted_by = '$r_id' WHERE posted_by = '$r_login'";
                $db->query($SQL);
                $this->message(_m('Column item.posted_by updated for %1 (id: %2).', array($r_login, $r_id)));
            }
        }
        if (count($edited_by_found) > 0) {
            foreach ($edited_by_found as $r_id => $r_login ) {
                $SQL = "UPDATE item SET edited_by = '$r_id' WHERE edited_by = '$r_login'";
                $db->query($SQL);
                $this->message(_m('Column item.edited_by updated for %1 (id: %2).', array($r_login, $r_id)));
            }
        }
        return true;
    }
}

/** Whole pagecache will be invalidated and deleted */
class Optimize_clear_pagecache extends Optimize {

    function name() {
        return _m("Clear Pagecache");
    }

    function description() {
        return _m("Whole pagecache will be invalidated and deleted");
    }

    function test() {
        $this->messages[] = _m('There is nothing to test.');
        return true;
    }

    /** Deletes the pagecache - the renaming and deleting is much, much quicker,
     *  than easy DELETE FROM ...
     */
    function repair() {
        $db  = getDb();
        $db->query('CREATE TABLE pagecache_new LIKE pagecache');
        $this->messages[] = _m('Table pagecache_new created');
        $db->query('CREATE TABLE pagecache_str2find_new LIKE pagecache_str2find');
        $this->messages[] = _m('Table pagecache_str2find_new created');
        $db->query('RENAME TABLE pagecache_str2find TO pagecache_str2find_bak, pagecache TO pagecache_bak');
        $this->messages[] = _m('Renamed tables pagecache_* to pagecache_*_bak');
        $db->query('RENAME TABLE pagecache_str2find_new TO pagecache_str2find, pagecache_new TO pagecache');
        $this->messages[] = _m('Renamed tables pagecache_*_new to pagecache_*');
        $db->query('DROP TABLE pagecache_str2find_bak, pagecache_bak');
        $this->messages[] = _m('Old pagecache_*_bak tables dropped');
        freeDb($db);
        return true;
    }
}

/** Whole pagecache will be invalidated and deleted */
class Optimize_copy_content extends Optimize {

    function name() {
        return _m("Copy Content Table");
    }

    function description() {
        return _m("Copy data for all items newer than short_id=1941629 from content table to content2 table. Used for recovery content table on Ecn server. Not usefull for any other users, I think.");
    }

    function test() {
        $this->messages[] = _m('There is nothing to test.');
        return true;
    }

    /** Deletes the pagecache - the renaming and deleting is much, much quicker,
     *  than easy DELETE FROM ...
     */
    function repair() {
        $db  = getDb();

        $SQL = "INSERT INTO content2 SELECT content.* FROM content
                LEFT JOIN item on content.item_id=item.id
                WHERE item.short_id>1941629";

        // Situation was:
        //    Content table was corrupted, so we replace i from backup. The last item in backup was short_id=1941629;
        //    After one day we found, that we restore the table from backup by wrong way, so it is corrupted for UTF slices
        //    So we decided to import old backup of content table to content2 table, and copy theer new items from content table


        // First of all we insert new content, which is missing in content2 table
        // INSERT INTO content2 SELECT content.* FROM content LEFT JOIN item on content.item_id=item.id WHERE item.short_id>1941629;

        // Then we switch from backup conten2 to content
        // RENAME TABLE content TO contentblb, content2 TO content;

        // And now we update all content of the item, which was updated after the first switch (one day before)
        // DELETE FROM content USING content, item WHERE content.item_id=item.id AND item.last_edit>1165360279 AND item.last_edit<1165500000 AND item.last_edit<>item.post_date AND item.short_id<1941629;
        // INSERT INTO content SELECT contentblb.* FROM contentblb LEFT JOIN item on contentblb.item_id=item.id WHERE item.last_edit>1165360279 AND item.last_edit<1165500000 AND item.last_edit<>item.post_date AND item.short_id<1941629;


        // $db->query($SQL);
        $this->messages[] = _m('Coppied');

        freeDb($db);
        return true;
    }
}


$Msg = '';

if ($_GET['test'] AND (strpos($_GET['test'], 'Optimize_')===0)) {
    $optimizer = AA_Components::factory($_GET['test']);
    $optimizer->test();
    $Msg .= $optimizer->report();
}

if ($_GET['repair'] AND (strpos($_GET['repair'], 'Optimize_')===0)) {
    $optimizer = AA_Components::factory($_GET['repair']);
    $optimizer->repair();
    $Msg .= $optimizer->report();
}

$optimize_names        = array();
$optimize_descriptions = array();

foreach (AA_Components::getClassNames('Optimize_') as $optimize_class) {
    // call static class methods
    $optimize_names[]        = call_user_func(array($optimize_class, 'name'));
    $description = call_user_func(array($optimize_class, 'description'));
    $optimize_descriptions[] = "
    <div>
      <div style=\"float: right;\">
        <a href=\"?test=$optimize_class\">Test</a>
        <a href=\"?repair=$optimize_class\">Repair</a>
      </div>
      <div>$description</div>
    </div>";
}

HtmlPageBegin();   // Print HTML start page tags (html begin, encoding, style sheet, but no title)
?>
<TITLE><?php echo _m("Admin - Optimize a Repair ActionApps"); ?></TITLE>
<SCRIPT Language="JavaScript"><!--
function InitPage() {}
//-->
</SCRIPT>
</HEAD>
<BODY>
<?php
$useOnLoad = true;
require_once AA_INC_PATH."menu.php3";
showMenu($aamenus, "aaadmin", "optimize");

echo "<H1><B>" . _m("Admin - Optimize a Repair ActionApps") . "</B></H1>";
PrintArray($err);
echo $Msg;

//$form_buttons = array ("submit");
$form_buttons = array ();
//$destinations = array_flip(array_unique($COLNODO_DOMAINS));

?>
<form name=f method=post action="<?php echo $sess->url($PHP_SELF) ?>">
<?php
FrmTabCaption(_m('Optimalizations'), '','', $form_buttons, $sess, $slice_id);
foreach ( $optimize_names as $i => $name ) {
    FrmStaticText($name, $optimize_descriptions[$i], false, '', '', false);
}
FrmTabEnd($form_buttons, $sess, $slice_id);
?>
</FORM>
<?php
HtmlPageEnd();
page_close()
?>
