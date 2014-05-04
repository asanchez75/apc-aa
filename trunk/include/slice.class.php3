<?php
/**
 * A class for manipulating slices
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
 * @version   $Id$
 * @author    Mitra Ardron <mitra@mitra.biz>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Copyright (C) 1999, 2000 Association for Progressive Communications
 * @link      http://www.apc.org/ APC
*/

// A class for manipulating slices
//
// Author and Maintainer: Mitra mitra@mitra.biz
//
// It is intended - and you are welcome - to extend this to bring into
// one place the functions for working with slices.
//
// A design goal is to use lazy-evaluation wherever possible, i.e. to only
// go to the database when something is needed.

//If this is needed, comment why! It trips out anything calling sliceobj NOT from one level down
//require_once "../include/config.php3";
//require_once AA_INC_PATH."locsess.php3";
require_once AA_INC_PATH."zids.php3"; // Pack and unpack ids
require_once AA_INC_PATH."view.class.php3"; //GetViewsWhere

class AA_Slice {
    var $name;            // The name of the slice
    var $unpackedid;      // The unpacked id of the slice i.e. 32 chars
    var $fields;          // 2 member array( $fields, $prifields)
    var $dynamic_fields;  // 2 member array( $fields, $prifields)
    var $setting;         // slice setting - Record form slice table
    var $dynamic_setting; // dynamic slice setting fields stored in content table
    var $setting_object;  // newest set of slice setting - stored in object AA_Modulesettings_Slice    

    // computed values form slice fields
    var $js_validation;  // javascript form validation code
    var $show_func_used; // used show functions in the input form
    /** AA_Slice function
     * @param $slice_id
     */
    function AA_Slice($slice_id) {
        global $errcheck;
        if ($errcheck && ! ereg("[0-9a-f]{32}",$slice_id)) {
            huhe(_m("WARNING: slice: %s doesn't look like an unpacked id", array($slice_id)));
        }
        $this->unpackedid     = $slice_id; // unpacked id
        $this->fields         = new AA_Fields($this->unpackedid);
        $this->dynamic_fields = new AA_Fields($this->unpackedid, 1);
        $this->setting_object = null;     
    }

    /** loadsettings function
     *  Load $this from the DB for any of $fields not already loaded
     *  @return true/false if the settings is loaded (= slice_id is OK)
     *  @param  $force
     */
    function loadsettings($force=false) {
        if ( !$force AND isset($this->setting) AND is_array($this->setting) ) {
            return true;
        }

        // get fields from slice table
        $SQL = "SELECT * FROM slice WHERE id = '".$this->sql_id(). "'";
        $this->setting = GetTable2Array($SQL, 'aa_first', 'aa_fields');
        if ( $this->setting ) {
            // do it more secure and do not store it plain
            // (we should be carefull - mainly with debug outputs)
            if ($this->setting['reading_password']) {
                $this->setting['reading_password'] =  AA_Credentials::encrypt($this->setting['reading_password']);
            }
        } else {
            if ($GLOBALS['errcheck']) {
                huhl("Slice ".$this->unpacked_id()." is not a valid slice");
            }
            return false;
        }

        // get fields from module table
        $SQL = "SELECT name, deleted, slice_url, lang_file, created_at, created_by, owner, app_id, priority, flag FROM module WHERE id = '".$this->sql_id(). "'";
        $rec = GetTable2Array($SQL, 'aa_first', 'aa_fields');
        if ( is_array($rec)) {   // not true for AA_Core_Fields.. (but it probably should be filled)
            $this->setting = array_merge($this->setting, $rec);
        }
        return true;
    }

    /** loadsettingfields function
     *  Load $this from the DB for any of $fields not already loaded
     * @param $force
     */
    function loadsettingfields($force=false) {
        if ( !$force AND isset($this->dynamic_setting) AND is_array($this->dynamic_setting) ) {
            return;
        }
        $db = getDB();
        $SQL = "SELECT * FROM content WHERE item_id = '".$this->sql_id()."' ORDER BY content.number";
        $db->tquery($SQL);
        $content4id = array();
        while ($db->next_record()) {
            // which database field is used (from 05/15/2004 we have FLAG_TEXT_STORED set for text-field-stored values
            $db_field = ( ($db->f("text")!="") OR ($db->f("flag") & FLAG_TEXT_STORED) ) ? 'text' : 'number';
            $content4id[$db->f("field_id")][] = array( "value" => $db->f($db_field),
                                                       "flag"  => $db->f("flag") );
        }
        freeDB($db);
        $this->dynamic_setting = new ItemContent($content4id);
    }
    
    /** getTranslations
     *  Returns array of two letters shortcuts for languages used in this slice for translations - array('en','cz','es')
     */
    function loadSettingObject()  {
        return $this->setting_object ?: $this->setting_object = AA_Modulesettings_Slice::load(string2id('AA_Modulesettings_Slice'.$this->unpackedid));
    }

    /** getProperty function
     * @param $fname
     */
    function getProperty($fname) {
        if ($fname=='translations') {
            return $this->getTranslations();
        }
        if (AA_Fields::isSliceField($fname)) {
            $this->loadsettingfields();
            return $this->dynamic_setting->getValue($fname);
        }
        $this->loadsettings();
        return $this->setting[$fname];
    }

    /** isField function
     * @param $fname
     */
    function isField($fname) {
        return $this->fields->isField($fname);
    }

    /** name function
     *
     */
    function name() {
        return $this->getProperty('name');
    }

    /** Checks, if the unpackedid is OK and the slice is not deleted */
    function isValid() {
        return $this->loadsettings() AND !$this->deleted();
    }

    /** jumpLink function
     *
     */
    function jumpLink() {
        return "<a href=\"".get_admin_url("index.php3?change_id=".$this->unpacked_id()). "\">".$this->name()."</a>";
    }

    /** deleted function
     *
     */
    function deleted() {
        return $this->getProperty('deleted');
    }

    /** fleman_dir function
     *
     */
    function fileman_dir() {
        return $this->getProperty('fileman_dir');
    }

    /** type function
     *
     */
    function type() {
        return $this->getProperty('type');
    }

    /** unpacked_id function
     *
     */
    function unpacked_id() {
        return $this->unpackedid; // Return a 32 character id
    }

    /** packed_id function
     *
     */
    function packed_id() {
        return pack_id($this->unpackedid);
    }

    /** getFields function
     */
    function & getFields($dynamic_fields = false) {
        return $dynamic_fields ? $this->dynamic_fields : $this->fields;
    }

    /** getField function     */
    function getField($field_id) {
        return $this->fields->getField($field_id);;
    }

    function getWidget($field_id) {
        $field = $this->getField($field_id);
        return $field ? $field->getWidget() : null;
    }

    /** getLang function
     *  Returns lang code ('cz', 'en', 'en-utf8', 'de',...)
     */
    function getLang()     {
        return GetLang($this->getProperty('lang_file'));
    }

    /** getCharset function
     *  Returns character encoding for the slice ('windows-1250', ...)
     */
    function getCharset()     {
        return $GLOBALS["LANGUAGE_CHARSETS"][$this->getLang()];   // like 'windows-1250'
    }

    /** getTranslations
     *  Returns array of two letters shortcuts for languages used in this slice for translations - array('en','cz','es')
     */
    function getTranslations()  {
        return is_null($this->loadSettingObject()) ? array() : $this->setting_object->getProperty('translations');
    }


    /** sql_id function
     *  Return an id in a form that can be passed to sql, (needs outer quotes)
     */
    function sql_id()      { return q_pack_id($this->unpackedid); }

    /** fields function
     *  fetch the fields
     * @param $return_type
     * @param $slice_fields
     * @return an array with two elements [0] is array in form
     * wanted by Storeitem etc, [1] is array of fields in priority order
     */
    function fields( $return_type = null, $slice_fields = false ) {

        $fields = $slice_fields ? $this->dynamic_fields : $this->fields;

        switch ( $return_type ) {
            // record - deprecated
            case 'record':  return $fields->getRecordArray();    // array of field definitions where field_id is key
            case 'pri':     return $fields->getPriorityArray();  // array of field definitions sorted by priority - integer key
            case 'search':  return $fields->getSearchArray();
        }
        return array($fields->getRecordArray(), $fields->getPriorityArray());                         // two member array ('record' array, 'pri' array)
    }

    /** get_dynamic_setting_content function
     *  Returns slice setting field content in ItemContent object
     * @param $ignore_reading_password
     */
    function get_dynamic_setting_content($ignore_reading_password = false) {
        if (!$ignore_reading_password) {
            $credentials = AA_Credentials::singleton();
            if (!$credentials->checkCryptedPassword($this->getProperty('reading_password'))) {
                if ($GLOBALS['errcheck'] OR $GLOBALS['debug']) {
                    huhe(_m("Error: Missing Reading Password"));
                }
                return false;
            }
        }
        $this->loadsettingfields();
        return $this->dynamic_setting;
    }

    /** getUploadBase function
     *  Get the base for the file uploads
     */
    function getUploadBase() {
        $ret = array();
        $fileman_dir = $this->getProperty('fileman_dir');
        if ($fileman_dir AND is_dir(FILEMAN_BASE_DIR.$fileman_dir)) {
            $ret['path']  = FILEMAN_BASE_DIR.$fileman_dir."/items";
            $ret['url']   = FILEMAN_BASE_URL.$fileman_dir."/items";
            $ret['perms'] = FILEMAN_MODE_DIR;
        } else {
            // files are copied to subdirectory of IMG_UPLOAD_PATH named as slice_id
            $ret['path']  = IMG_UPLOAD_PATH. $this->unpacked_id();
            $ret['url']   = get_if($this->getProperty('_upload_url.....'), IMG_UPLOAD_URL. $this->unpacked_id());
            $ret['perms'] = (int)IMG_UPLOAD_DIR_MODE;
        }
        return $ret;
    }

    /** getUrlFromPath function
     *  Try to transform file path to file url - based on setting of file
     *  uploads or filemanager
     * @param $filename
     */
    function getUrlFromPath($filename) {
        $upload = $this->getUploadBase();
        if (strpos($filename, $upload['path']) === 0) {
            return $upload['url']. substr($filename,strlen($upload['path']));
        }
        return $filename;
    }

    /** views function
     *  Get all the views for this slice
     */
    function views() {
        return AA_Views::getSliceViews($this->unpackedid);
    }

    /** get_format_strings function
     *  Returns array of admin format strings as used in manager class
     */
    function get_format_strings() {
         $this->loadsettings();
         // additional string for compact_top and compact_bottom needed
         // for historical reasons (not manager.class verion of item manager)
         return array ( "compact_top"     => '<table border="0" cellspacing="0" cellpadding="1" bgcolor="#F5F0E7" class="mgr_table">'.
                                             $this->setting['admin_format_top'],
                        "category_sort"   => false,
                        "category_format" => "",
                        "category_top"    => "",
                        "category_bottom" => "",
                        "even_odd_differ" => false,
                        "even_row_format" => "",
                        "odd_row_format"  => $this->setting['admin_format'],
                        "compact_remove"  => $this->setting['admin_remove'],
                        "compact_bottom"  => $this->setting['admin_format_bottom']. '</table>',
                        "noitem_msg"      => $this->setting['admin_noitem_msg'],
                        // id is packed (format string are used as itemview
                        //               parameter, where $slice_info expected)
                        "id"              => $this->setting['id'] );
    }

    /** aliases function
     *  Get standard aliases definition from slice's fields
     * @param $additional_aliases
     */
    function aliases($additional_aliases = false) {
        return $this->fields->getAliases($additional_aliases);
    }

    /** get_js_validation function
     *  Returns javascript code for inputform validation
     * @param $action
     * @param $id
     * @param $shown_fields
     * @param $slice_fields
     */
    function get_js_validation($action, $id=0, $shown_fields=false, $slice_fields=false) {
        $this->_compute_field_stats($action, $id, $shown_fields, $slice_fields);
        return $this->js_validation;
    }

    /** get_show_func_used function
     *  Returns array of inputform function used the in inputform
     * @param $action
     * @param $id
     * @param $shown_fields
     * @param $slice_fields
     */
    function get_show_func_used($action, $id=0, $shown_fields=false, $slice_fields=false) {
        $this->_compute_field_stats($action, $id, $shown_fields, $slice_fields);
        return $this->show_func_used;
    }

    function allowed_bin_4_user() {
        // put the item into the right bin
        $bin2fill = (int) $this->getProperty("permit_anonymous_post");
        return ($bin2fill < 1) ? 4 : $bin2fill;
    }

    /** _compute_field_stats function
     *  Computes js_validation code and show_func_used
     * @param $action       - 'update' | 'edit'
     * @param $id           - id of item to edit
     * @param $shown_fields - array of field ids which we will use in the output
     *                  (inputform)(we have to count with them).
     *                  If false, then we use all the fields
     * @param $slice_fields
     */
    function _compute_field_stats($action, $id=0, $shown_fields=false, $slice_fields=false) {
        if (isset($this->js_validation)) {
            return;                                // already computed
        }

        global $auth;
        $profile = AA_Profile::getProfile($auth->auth["uid"], $this->unpacked_id()); // current user settings
        $this->loadsettings();

        // get slice fields and its priorities in inputform
        list( $fields, $prifields) = $this->fields(null, $slice_fields);
        if (!is_array($prifields)) {
            return '';
        }

        // it is needed to call IsEditable() function and GetContentFromForm()
        if ( $id ) {
            $oldcontent = GetItemContent($id);
            $oldcontent4id = $oldcontent[$id];   // shortcut
        }

        $js_proove_fields = 'true';
        foreach ( $prifields as $pri_field_id ) {

            $f = $fields[$pri_field_id];

            //  'status_code.....' is not in condition - could be set from defaults
            if (($pri_field_id=='edited_by.......') || ($pri_field_id=='posted_by.......')) {
                continue;   // filed by AA - it could not be filled here
            }
            $varname = 'v'. unpack_id($pri_field_id);  // "v" prefix - database field var

            list($validate) = explode(":", $f["input_validate"]);
            if ($validate == 'e-unique') {
                $validate = "email";
            }

            $editable = IsEditable($oldcontent4id[$pri_field_id], $f, $profile);
            if (is_array($shown_fields) AND !$shown_fields[$pri_field_id]) {
                $editable = false;
            }

            $js_proove_password_filled = ($action != "edit") && $f["required"] && !$oldcontent4id[$pri_field_id][0]['value'];

            // prepare javascript function for validation of the form
            if ( $editable ) {

                // fill show_func_used array - used on several places
                // to destinguish, which javascripts we should include and
                // if we have to use form multipart or not
                list($show_func) = explode(":", $f["input_show_func"], 2);
                $this->show_func_used[$show_func] = true;
                if ($show_func == 'fil') {
                    continue;
                }

                switch( $validate ) {
                    case 'text':
                    case 'url':
                    case 'email':
                    case 'number':
                    case 'id':
                    case 'pwd':
                        $js_proove_fields .= "\n && validate (myform, '$varname', '$validate', "
                            .($f["required"] ? "1" : "0").", "
                            .($js_proove_password_filled ? "1" : "0").")";
                    break;
                }
            }
        }

        $this->js_validation = get_javascript_field_validation(). "\n
            function proove_fields () {
                var myform = document.inputform;
                return $js_proove_fields;
            }\n";
    }
}

class AA_Slices {
    var $a = array();     // Array unpackedsliceid -> slice obj
    /** AA_Slices function
     *
     */
    function AA_Slices() {
        $this->a = array();
    }

    /** singleton
     *  called from getSlice method
     *  This function makes sure, there is just ONE static instance if the class
     *  @todo  convert to static class variable (after migration to PHP5)
     */
    function singleton() {
        static $instance = null;
        if (is_null($instance)) {
            // Now create the AA_Slices object
            $instance = new AA_Slices;
        }
        return $instance;
    }

    /** getSlice function
     *  main factory static method
     * @param $slice_id
     */
    function getSlice($slice_id) {
        if (guesstype($slice_id) != 'l') {
            return null;
        }
        $slices = AA_Slices::singleton();
        return $slices->_getSlice($slice_id);
    }

    /** getSliceProperty function
     *  static function called as: AA_Slices::getSliceProperty($slice_id, 'translations');
     * @param $slice_id
     * @param $field
     */
    function getSliceProperty($slice_id, $field) {
        $slice = AA_Slices::getSlice($slice_id);
        return $slice ? $slice->getProperty($field) : null;
    }

    /** getField function - returns slice's field
     *  static function
     * @param $slice_id
     * @param $field
     */
    function getField($slice_id, $field_id) {
        $slices = AA_Slices::singleton();
        $slice  = $slices->_getSlice($slice_id);
        return $slice ? $slice->getField($field_id) : null;
    }

    /** getName function
     *  static function
     * @param $slice_id
     */
    function getName($slice_id) {
        return AA_Slices::getSliceProperty($slice_id, 'name');
    }

    /** _getSlice function
     * @param $slice_id
     */
    function & _getSlice($slice_id) {
        if (!isset($this->a[$slice_id])) {
            $this->a[$slice_id] = new AA_Slice($slice_id);
        }
        return $this->a[$slice_id];
    }
}



class AA_Modules {

    // Store the single instance of Database
    private static $_instance;

    var $a = array();     // Array unpacked module id -> AA_Module object

    private function __construct() {
        $this->a = array();
    }

    public static function singleton() {
        if(!isset(self::$_instance)) {
            self::$_instance = new AA_Modules();
        }
        return self::$_instance;
    }

    /** @return array id => name of current user's modules
     *  @param $module_type
     *  @param $perm
     *  @param $user_id
     */
    public static function getUserModules( $module_type = '', $perm = PS_EDIT_SELF_ITEMS, $user_id = '') {
        global $auth;

        $where_add = empty($module_type) ? '' : "AND type='$module_type'";
        $all_modules = GetTable2Array("SELECT id, name FROM module WHERE deleted<>1 $where_add ORDER BY priority, name", 'unpack:id', 'name');

        if (empty($user_id)) {
            $user_id =  $auth->auth["uid"];
        }

        $ret = array();
        foreach($all_modules as $mid => $mname) {
            if (CheckPerms( $user_id, 'slice', $mid, $perm)) {
                $ret[$mid] = $mname;
            }
        }

        return $ret;
    }

    /** main factory static method: $module = AA_Modules::getModule($module_id);
     *  @param $module_id
     */
    function getModule($module_id) {
        $modules = AA_Modules::singleton();
        return $modules->_getModule($module_id);
    }

    /** getModuleProperty function
     *  static function
     * @param $slice_id
     * @param $field
     */
    function getModuleProperty($module_id, $field) {
        $module = AA_Modules::getModule($module_id);
        return $module ? $module->getProperty($field) : null;
    }

    /** _getSlice function
     * @param $slice_id
     */
    function _getModule($module_id) {
        if (!isset($this->a[$module_id])) {
            $this->a[$module_id] = new AA_Module($module_id);
        }
        return $this->a[$module_id];
    }
}

class AA_Module {
    var $id;
    var $fields; // Array of fields

    function __construct($id) {
        $this->id = $id;
        $this->fields = array();
    }

    /** load function
     * @param $force
     */
    function load() {
        if (empty($this->fields)) {
            $SQL = "SELECT name, deleted, slice_url, lang_file, created_at, created_by, owner, app_id, priority, flag FROM module WHERE id = '".q_pack_id($this->id). "'";
            $this->fields = GetTable2Array($SQL, 'aa_first', 'aa_fields');
            if (!$this->fields) {
                $this->fields = array();
            }
        }
        return !empty($this->fields);
    }

    function getProperty($field) {
        return $this->load() ? $this->fields[$field] : false;
    }

    /** getLang function
     *  Returns lang code ('cz', 'en', 'en-utf8', 'de',...)
     */
    function getLang()     {
        $lang_file = $this->getProperty('lang_file');
        $lang_file = substr($lang_file, 0, strpos($lang_file, '_'));
        return isset($GLOBALS['LANGUAGE_NAMES'][$lang_file]) ? $lang_file : substr(DEFAULT_LANG_INCLUDE, 0, 2);
    }

    /** getCharset function
     *  Returns character encoding for the slice ('windows-1250', ...)
     */
    function getCharset()     {
        return $GLOBALS["LANGUAGE_CHARSETS"][$this->getLang()];   // like 'windows-1250'
    }
}

/** Slice settings */
class AA_Modulesettings_Slice extends AA_Object {

    // must be protected or public - AA_Object needs to read it
    protected $translations;
    
    /** do not display Name property on the form by default */
    const USES_NAME = false;    
    

    /** allows storing object in database
     *  AA_Object's method
     */
    static function getClassProperties() {
        return array ( //                        id             name                            type     multi  persist validator, required, help, morehelp, example
            'translations' => new AA_Property( 'translations', _m("Languages for translation"), 'string', true,  true, new AA_Validate_Regexp(array('pattern'=>'/^[a-z]{2}$/', 'maxlength'=>2)), false, _m('specify language codes in which you want translate content - small caps, two letters - like: en, es, de, ...'))
            );
    }
}

/** Slice settings */
class AA_Modulesettings_Site extends AA_Object {

    // must be protected or public - AA_Object needs to read it
    protected $translation_slice;
    protected $additional_aliases;
    
    /** do not display Name property on the form by default */
    const USES_NAME = false;    

    /** allows storing object in database
     *  AA_Object's method
     */
    static function getClassProperties() {
        return array ( //                        id             name                            type     multi  persist validator, required, help, morehelp, example
            'translations' => new AA_Property( 'translations', _m("Languages for translation"), 'string', true,  true, new AA_Validate_Regexp(array('pattern'=>'/^[a-z]{2}$/', 'maxlength'=>2)), false, _m('specify language codes in which you want translate content - small caps, two letters - like: en, es, de, ...'))
            );
    }
}




?>