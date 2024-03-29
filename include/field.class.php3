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
 * @version   $Id: widget.class.php3 2442 2007-06-29 13:38:51Z honzam $
 * @author    Honza Malik <honza.malik@ecn.cz>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Copyright (C) 1999, 2000 Association for Progressive Communications
 * @link      http://www.apc.org/ APC
 *
*/

/** for AA_Generator_*   */
require_once AA_INC_PATH."itemfunc.php3";

class AA_Field {

    /** asociative array of field data as defined in field table
    *   (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored)
    */
    var $data;

    /** Default widget - as parsed from field data (input_show_func) */
    var $widget;

    /** AA_Field function
     *  @param $data
     */
    function __construct($data) {
        $this->data   = is_array($data) ? $data : array();
        $this->widget = null;
    }

    /** storageColumn function
     *  @return the table and column, where the field is stored
     */
    function storageColumn() {
        return $this->data['in_item_tbl'] ? $this->data['in_item_tbl'] :  ($this->data['text_stored'] ? 'text' : 'number');
    }

    /** storageTable function
     *  @return the table and column, where the field is stored
     */
    function storageTable() {
        return $this->data['in_item_tbl'] ? 'item' : 'content';
    }

    /** getProperty function
     *  @return field data
     */
    function getProperty($property) {
        return $this->data[$property];
    }

    /** getId
     * @return id of the field
     */
    function getId() {
        return $this->getProperty('id');
    }

    /** getName function
     * @return name of the field
     */
    function getName() {
        return $this->getProperty('name');
    }

    /** getSliceId function
     * @return long id of the slice of this field
     */
    function getSliceId() {
        return unpack_id($this->getProperty('slice_id'));
    }

    /** required function
     * @return boolean value if the field is requierd  (must be filled)
     */
    function required() {
        return (bool) $this->getProperty('required');
    }

    /** getWidget function
     * @param $widget_type - wi2|sel|...
     *                       used, when we want to use another widget, than the default one
     *                       usualy not used - right now we use it just for constants_sel.php3
     * @param $properties  - array of properties to redefine for $widget_type
     */
    function &getWidget($widget_type=null, $properties=array()) {
        if ( is_null($this->widget) ) {

            // $this->widget = AA_Widget::factoryByString($widget_type ? $widget_type : $this->data['input_show_func']);
            $params       = AA_Widget::parseClassProperties($this->data['input_show_func']);
            $widget_class = $widget_type ? AA_Widget::constructClassName($widget_type) : $params['class'];
            if (!class_exists($widget_class)) {
                $widget_class = $params['class'];
            }
            $this->widget = AA_Object::factory($widget_class, $params);
            if ($properties) {
                // for security reasons we do not want to redefine const (Constants/Slices)
                unset($properties['const'], $properties['bin_filter'], $properties['filter_conds']);
                $this->widget->setProperties($properties);
            }
        }
        return $this->widget;
    }


    /** getDefault function
     */
    function getDefault() {
        // all default should have fnc:param format
        if (!($generator = AA_Generator::factoryByString($this->data['input_default']))) {
            return null;
        }
        return $generator->generate()->setFlag(($this->data['html_default']>0) ? FLAG_HTML : 0);
    }

    /** getAliases function
     *
     */
    function getAliases() {
        $ret = array();
        if ($this->data['alias1']) {
            // fld used in PrintAliasHelp to point to alias editing page
            $ret[$this->data['alias1']] = array("fce" => $this->data['alias1_func'], "param" => $this->data['id'], "hlp" => $this->data['alias1_help'], "fld" => $this->data['id']);
        }
        if ($this->data['alias2']) {
            $ret[$this->data['alias2']] = array("fce" => $this->data['alias2_func'], "param" => $this->data['id'], "hlp" => $this->data['alias2_help'], "fld" => $this->data['id']);
        }
        if ($this->data['alias3']) {
            $ret[$this->data['alias3']] = array("fce" => $this->data['alias3_func'], "param" => $this->data['id'], "hlp" => $this->data['alias3_help'], "fld" => $this->data['id']);
        }
        return $ret;
    }

    /** getConstantGroup function
     * function finds group_id in field.input_show_func parameter
     */
    function getConstantGroup() {
        // does this field use constants? Isn't it slice?
        list($field_type, $field_add) = $this->getSearchType();
        return ( $field_type == 'constants' ) ? $field_add : false;
    }

    /** getRecord function
     *  @deprecated - for backward compatibility only
     */
    function getRecord() {
        return $this->data;
    }

    /** getSearchType function
     * @return text | numeric | date | constants
     */
    function getSearchType() {
        $field_type = 'numeric';
        $field_add  = '';
        if ($this->data['text_stored']) {
            $field_type = 'text';
        }
        if (substr($this->data['input_validate'],0,4)=='date') {
            $field_type = 'date';
        }
        $r = $this->getRelation();

        if (!empty($r)) {
            $field_add  = $r[1];
            $field_type = ($field_type == 'numeric') ? 'numconstants' : $r[0];
        }

        return array($field_type, $field_add);
    }

    /** getTranslations
     *  Returns array of two letters shortcuts for languages used in this slice for translations - array('en','cz','es')
     */
    function getTranslations() {
        return ($this->data['multiple'] & 2) ? AA_Slice::getModule($this->getSliceId())->getTranslations() : array();
    }

    /** getAaProperty function
    * @param $multiple
    * @param $required   - not usual, but sometimes we want to redefine it required for ajax...
    * @todo create validator on input_validate
    */
    function getAaProperty($multiple=null, $required=null, $name=null, $input_help=null) {
        if (is_null($multiple)) {
            $multiple = $this->getWidget()->multiple();
        }
        $translations = $this->getTranslations();

        // AA_Property($id, $name='', $type, $multi=false, $persistent=true, $validator=null, $required=false, $input_help='', $input_morehlp='', $example='', $show_content_type_switch=0, $content_type_switch_default=) {
        return new AA_Property( $this->getId(),
                                $name ?: $this->getName(),
                                $this->getProperty('text_stored') ? 'text' : 'int',
                                $multiple,
                                false,                   // persistent @todo
                                AA_Validate::factoryByString($this->data['input_validate']), // null,              // $validator - @todo create validator
                                $required ? true : $this->required(),
                                $input_help ?: $this->getProperty('input_help'),
                                $this->getProperty('input_morehlp'),
                                null,               // $example;
                                $this->getProperty('html_show') ?  AA_Formatter::getStandardFormattersBitfield() : AA_Formatter::getNoneFormattersBitfield(),
                                AA_Formatter::getFlag($this->getProperty('html_default') ? 'HTML' : 'PLAIN'),
                                null,               // perms
                                $this->getDefault(),
                                $translations
                               );
    }

    /** getWidgetAjaxHtml function
    * @param $item_id
    * @param $required    - redefine default settings of required
    * @param $function    - js function to call after the update
    *                     - not implemented yet (it is here for (optical) parameter compatibility with getWidgetLiveHtml)
    * @param $widget_type - wi2|sel|...
    *                       used, when we want to use another widget, than the default one
    * @param $widget_properties  - array of properties to redefine for $widget_type - array('columns' => 1)
    */
    function getWidgetAjaxHtml($item_id, $required=null, $function=null, $widget_type=null, $widget_properties=array()) {
        $widget      = $this->getWidget($widget_type, $widget_properties);
        $item        = AA_Items::getItem($item_id);
        $aa_property = $this->getAaProperty($widget->multiple(), $required, $widget_properties['name'], $widget_properties['input_help']);
        return $widget->getAjaxHtml($aa_property, $item, $function);
    }

    /** getWidgetLiveHtml function
    * @param $item_id
    * @param $required    - redefine default settings of required
    * @param $function    - js function to call after the update
    * @param $widget_type - wi2|sel|...
    *                       used, when we want to use another widget, than the default one
    * @param $widget_properties  - array of properties to redefine for $widget_type - array('columns' => 1, 'name' => 'My Category', 'input_help' => 'check the categories, please',  )
    */
    function getWidgetLiveHtml($item_id, $required=null, $function=null, $widget_type=null, $widget_properties=array()) {
        $widget      = $this->getWidget($widget_type, $widget_properties);
        $item        = AA_Items::getItem($item_id);
        $aa_property = $this->getAaProperty($widget->multiple(), $required, $widget_properties['name'], $widget_properties['input_help']);
        return $widget->getLiveHtml($aa_property, $item, $function);
    }

    /** getWidgetEditHtml function
    * @param $item_id
    * @param $required    - redefine default settings of required
    * @param $function    - js function to call after the update
    * @param $widget_type - wi2|sel|...
    *                       used, when we want to use another widget, than the default one
    * @param $widget_properties  - array of properties to redefine for $widget_type - array('columns' => 1, 'name' => 'My Category', 'input_help' => 'check the categories, please',  )
    */
    function getWidgetEditHtml($item_id, $required=null, $function=null, $widget_type=null, $widget_properties=array()) {
        $widget      = $this->getWidget($widget_type, $widget_properties);
        $item        = AA_Items::getItem($item_id);
        $aa_property = $this->getAaProperty($widget->multiple(), $required, $widget_properties['name'], $widget_properties['input_help']);
        return $widget->getEditHtml($aa_property, $item, $function);
    }

    /** getWidgetNewHtml function
     * @param $item_id
     * @param $required  // redefine default settings of required
     * @param $function - not implemented yet (it is here for (optical) parameter compatibility with getWidgetLiveHtml)
     * @param $widget_type - wi2|sel|...
     *                       used, when we want to use another widget, than the default one
     *                       usualy not used - right now we use it just for constants_sel.php3
     * @param $widget_properties  - array of properties to redefine for $widget_type
     * @param $item_index  - used to identify number of the item - aa[n1_...], aa[n2_...]
     *
     * Ussage: $field->getWidgetNewHtml(null, null, 'mch', array('columns' => 1));
     */
    function getWidgetNewHtml($required=null, $function=null, $widget_type=null, $widget_properties=array(), $preset_value=null, $item_index=null) {
        $widget  = $this->getWidget($widget_type, $widget_properties);
        if ($item_index) {
            $widget->setIndex($item_index);
        }
        $content = new AA_Content();
        $content->setOwnerId($this->getSliceId());
        if (is_object($preset_value)) {
            $content->setAaValue($this->getId(), $preset_value);
        }
        $aa_property = $this->getAaProperty($widget->multiple(), $required, $widget_properties['name'], $widget_properties['input_help']);

        return $widget->getHtml($aa_property, $content, $function);
    }

    /** getRelation function
     *  @return slice_id if constants are from slice, empty string otherwise
     */
    function getRelation() {
        $showfunc = AA_Widget::parseClassProperties($this->data['input_show_func']);
        if (!$showfunc['const']) {
            return array();
        }
        // prefix indicates select from items
        return (substr($showfunc['const'],0,7) == "#sLiCe-") ? array('relation', substr($showfunc['const'],7)) : array('constants', $showfunc['const']);
    }

    /** isMultiline - @return if the default widget allows block elements  */
    function isMultiline() {
        $params = AA_Widget::parseClassProperties($this->data['input_show_func']);
        return in_array($params['class'], array('AA_Widget_Txt','AA_Widget_Tpr','AA_Widget_Edt'));
    }

    function cloneWithId($id) {
        $new = clone $this;
        $new->data['id']=$id;
        return $new;
    }
}

class AA_Fields implements Iterator {

    /** array of object of AA_Field type */
    var $fields;

    /** id of slice/module ... for which the fields are used */
    var $master_id;

    /** collection - each id could have multiple fieldsets.
     *  In fact we do not use this feature yet, it is just abstraction for
     *  "slice fields" - slice has two field sets - normal fields and
     *  "slice (setting) fields", where id of those fields begins with '_'
     */
    var $collection;

    /** Array of field ids sorted by priority */
    var $prifields;

    /** Array of aliases - for caching purposes */
    var $aliases;
    /** AA_Fields function
     * @param $master_id
     * @param $collection
     */
    function __construct($master_id, $collection = 0) {
        $this->master_id  = $master_id;
        $this->fields     = null;
        $this->collection = $collection;
        $this->prifields  = null;
        $this->aliases    = null;
    }

    /** Returns list of fields which belongs to the slice
     *  The result is in two arrays - $fields    (key is field_id)
     *                              - $prifields (just field_id sorted by priority)
     *  @param $slice_id       - id of slice for which you want to get fields array
     *  @param $slice_fields   - if true, the result contains only "slice fields"
     *                           which are not used for items, but rather for slice
     *                           setting
     *  @see sliceobj:slice->fields()
     */
     /** load function
      * @param $force
      */
    function load($force=false) {
        if ( !$force AND !is_null($this->fields) ) {
            return;
        }

        $this->fields    = array();
        $this->prifields = array();

        $p_master_id = q_pack_id($this->master_id);
        $db = getDB();

        // slice_fields are begins with underscore
        // slice fields are the fields, which we do not use for items in the slice,
        // but rather for setting parameters of the slice
        $fields_where = ($this->collection == 0) ? "AND id NOT LIKE '\_%'" : "AND id LIKE '\_%'";
        $SQL = "SELECT * FROM field WHERE slice_id='$p_master_id' $fields_where ORDER BY input_pri";
        $db->query($SQL);
        while ($db->next_record()) {
            $fid                = $db->f("id");
            $this->fields[$fid] = new AA_Field($db->Record);
            $this->prifields[]  = $fid;
        }
        freeDB($db);
    }

    /** getField function
     *  @return the field (copy - just because of syntax - it is not possible
     *  to return null in &function())
     * @param $field_id
     */
    function getField($field_id) {
        $this->load();
        return isset($this->fields[$field_id]) ? $this->fields[$field_id] : null;
    }

    /** isField function
     *  @return bool - if the field exists
     *  @param $field_id
     */
    function isField($field_id) {
        $this->load();
        return isset($this->fields[$field_id]);
    }

    /** getProperty function
     * @param $field_id
     * @param $property
     */
    function getProperty($field_id, $property) {
        $this->load();
        return isset($this->fields[$field_id]) ? $this->fields[$field_id]->getProperty($property) : null;
    }

    /** getAliases function
     * @param $additional
     * @param $type
     */
    function getAliases($additional='', $type='') {
        if ( !is_null($this->aliases) ) {
            return is_array($additional) ? array_merge($additional, $this->aliases) : $this->aliases;
        }
        $this->load();

        $this->aliases = is_array($additional) ? $additional : array();

        //  Standard aliases
        $this->aliases["_#ID_COUNT"] = GetAliasDef( "f_e:itemcount",        "id..............", _m("number of found items"));
        $this->aliases["_#ITEMINDX"] = GetAliasDef( "f_e:itemindex",        "id..............", _m("index of item within whole listing (begins with 0)"));
        $this->aliases["_#PAGEINDX"] = GetAliasDef( "f_e:pageindex",        "id..............", _m("index of item within a page (it begins from 0 on each page listed by pagescroller)"));
        $this->aliases["_#GRP_INDX"] = GetAliasDef( "f_e:groupindex",       "id..............", _m("index of a group on page (it begins from 0 on each page)"));
        $this->aliases["_#IGRPINDX"] = GetAliasDef( "f_e:itemgroupindex",   "id..............", _m("index of item within a group on page (it begins from 0 on each group)"));
        $this->aliases["_#ITEM_ID_"] = GetAliasDef( "f_1",                  "unpacked_id.....", _m("alias for Item ID"));
        $this->aliases["_#SITEM_ID"] = GetAliasDef( "f_1",                  "short_id........", _m("alias for Short Item ID"));

        if ( $type == 'justids') {  // it is enough for view of urls
            // maybe we should make $this->aliases = null (to be recounted next time with all aliases), but there was no problem so far, so we left here qucker solution. Honza 2016-10-20
            return is_array($additional) ? array_merge($additional, $this->aliases) : $this->aliases;
        }

        $this->aliases["_#EDITITEM"] = GetAliasDef(  "f_e",            "id..............", _m("alias used on admin page index.php3 for itemedit url"));
        $this->aliases["_#ADD_ITEM"] = GetAliasDef(  "f_e:add",        "id..............", _m("alias used on admin page index.php3 for itemedit url"));
        $this->aliases["_#EDITDISC"] = GetAliasDef(  "f_e:disc",       "id..............", _m("Alias used on admin page index.php3 for edit discussion url"));
        $this->aliases["_#RSS_TITL"] = GetAliasDef(  "f_r",            "SLICEtitle",       _m("Title of Slice for RSS"));
        $this->aliases["_#RSS_LINK"] = GetAliasDef(  "f_r",            "SLICElink",        _m("Link to the Slice for RSS"));
        $this->aliases["_#RSS_DESC"] = GetAliasDef(  "f_r",            "SLICEdesc",        _m("Short description (owner and name) of slice for RSS"));
        $this->aliases["_#RSS_DATE"] = GetAliasDef(  "f_r",            "SLICEdate",        _m("Date RSS information is generated, in RSS date format"));
        $this->aliases["_#SLI_NAME"] = GetAliasDef(  "f_e:slice_info", "name",             _m("Slice name"));

        $this->aliases["_#MLX_LANG"] = GetAliasDef(  "f_e:mlx_lang",   MLX_CTRLIDFIELD,             _m("Current MLX language"));
        $this->aliases["_#MLX_DIR_"] = GetAliasDef(  "f_e:mlx_dir",   MLX_CTRLIDFIELD,             _m("HTML markup direction tag (e.g. DIR=RTL)"));

        // database stored aliases
        foreach ($this->fields as $field) {
            $this->aliases = array_merge($this->aliases, $field->getAliases());
        }
        return is_array($additional) ? array_merge($additional, $this->aliases) : $this->aliases;
    }

    /** getCategoryFieldId function
     *  returns field id of field which stores category (usually "category........")
     */
    function getCategoryFieldId() {
        $this->load();
        $no = 10000;
        foreach ($this->fields as  $fid => $foo ) {
            if ( substr($fid, 0, 8) != "category" ) {
                continue;
            }
            $last = AA_Fields::getFieldNo($fid);
            $no = min( $no, ( ($last=='') ? -1 : (integer)$last) );
        }
        if ($no==10000) {
            return false;
        }
        $no = ( ($no==-1) ? '.' : (string)$no);
        return AA_Fields::createFieldId("category", $no);
    }

    /** getRecordArray function
     *  deprecated - for backward compatibility only
     */
    function getRecordArray() {
        $this->load();
        $ret = array();
        foreach ( $this->fields as $fid => $fld ) { // in priority order
            $ret[$fid] = $fld->getRecord();
        }
        return $ret;
    }

    /** getNameArray function */
    function getNameArray() {
        $this->load();
        $ret = array();
        foreach ( $this->fields as $fid => $fld ) { // in priority order
            $ret[$fid] = $fld->getProperty('name');
        }
        return $ret;
    }

    /** getPriorityArray function
     *
     */
    function getPriorityArray() {
        $this->load();
        return $this->prifields;
    }

    /** getSearchArray function
     *
     */
    function getSearchArray() {
        $this->load();
        $i = 0;
        $ret = array();
        foreach ( $this->fields as $field_id => $field ) { // in priority order
            list($field_type,$field_add) = $field->getSearchType();
            if ($field_type=='relation') {
                $field_type = 'constants';     // @todo - deal with relations in search
            }
            // we can hide the field, if we put in fields.search_pri=0
            $search_pri = ($field->getProperty('search_pri') ? ++$i : 0 );
                               //             $name,        $field,   $operators, $table, $search_pri, $order_pri
            $ret[$field_id] = GetFieldDef( $field->getProperty('name'), $field_id, $field_type, false, $search_pri, $search_pri);
        }
        return $ret;
    }

    /** isSliceField function
     *  Returns true, if the passed field id looks like slice setting field
     *  "slice fields" are not used for items, but rather for slice setting.
     *  Such fields are destinguished by underscore on first letter of field_id
     *  - static class function
     * @param $field_id
     */
    function isSliceField($field_id) {
        return $field_id AND ($field_id{0} == '_');
    }

    /** createFieldId function
     *  Create field id from type and number
     *  - static class function
     * @param $ftype
     * @param $no
     */
    function createFieldId($ftype, $no="0", $id_type='.') {
        if ((string)$no == "0") {
            $no = "";    // id for 0 is "xxxxx..........."
        }
        return $ftype. substr( str_pad($no, 16, $id_type, STR_PAD_LEFT), -(16-strlen($ftype)));
    }

    /** getFieldType function
     *  get field type from id (works also for AA_Core_Fields (without dots))
     *  - static class function
     * @param $id
     */
    function getFieldType($id) {
        $id = ltrim($id, "_");  // slice (module) fields are prefixed by underscore
        $dot_pos = strpos($id, ".");
        return ($dot_pos === false) ? $id : substr($id, 0, $dot_pos);
    }

    /** getFieldNo function
     *  get field number from id ('.', '0', '1', '12', ... )
     *  - static class function
     * @param $id
     */
    function getFieldNo($id) {
        return (string)substr(strrchr($id,'.'), 1);
    }

    /** createSliceField function
     *  creates slice field
     *  - static class function
     * @param $type
     */
    function createSliceField($type) {
        //todo

    //    $varset = new CVarset();
    //
    //
    //    // copy fields
    //            // use the same setting for new field as template in AA_Core_Fields..
    //            $varset->addArray( $FIELD_FIELDS_TEXT, $FIELD_FIELDS_NUM );
    //            $varset->setFromArray($field_types[$type]);   // from template for this field
    //
    //            // in AA_Core_Fields.. are fields identified by 'switch' or 'text'
    //            // identifiers (without dots!) by default. However if user add new
    //            // "template" field to the AA_Core_Fields.. slice, then the identifier
    //            // is full (it contains dots). We need base identifier, for now.
    //            // Also we will add underscore for all "slice fields" - the ones
    //            // which are not set for items, but rather for slice (settings)
    //            $ftype_base = ($slice_fields ? '_' : '') . AA_Fields::getFieldType($type);
    //
    //            // get new field id
    //            $SQL = "SELECT id FROM field
    //                    WHERE slice_id='$p_slice_id' AND id like '". $ftype_base ."%'";
    //            $max = -1;  // Was 0
    //            $db->query($SQL);   // get all fields with the same type in this slice
    //            while ( $db->next_record() ) {
    //                $max = max( $max, AA_Fields::getFieldNo($db->f('id')), 0);
    //            }
    //            $max++;
    //            //create name like "time...........2"
    //            $fieldid = AA_Fields::createFieldId($ftype_base, $max);
    //
    //            $varset->set("slice_id", $slice_id, "unpacked" );
    //            $varset->set("id", $fieldid, "quoted" );
    //            $varset->set("name",  $val, "quoted");
    //            $varset->set("input_pri", $pri[$key], "number");
    //            $varset->set("required", ($req[$key] ? 1 : 0), "number");
    //            $varset->set("input_show", ($shw[$key] ? 1 : 0), "number");
    //            if (!$varset->doInsert('field')) {
    //                $err["DB"] .= MsgErr("Can't copy field");
    //                break;
    //            }
    }

    /** Iterator interface */
    public function rewind()  { if (is_null($this->fields)) {$this->load();} reset($this->prifields);                         }
    public function current() { if (is_null($this->fields)) {$this->load();} return $this->fields[current($this->prifields)]; }
    public function key()     { if (is_null($this->fields)) {$this->load();} return current($this->prifields);                }
    public function next()    { if (is_null($this->fields)) {$this->load();} next($this->prifields);                          }
    public function valid()   { if (is_null($this->fields)) {$this->load();} return (current($this->prifields) !== false);    }
}

/** GetFields4Select function
 * @param $slice_id
 * @param $slice_fields
 * @param $order
 * @param $add_empty
 */
function GetFields4Select($slice_id, $slice_fields = false, $order = 'name', $add_empty = false, $add_all=false) {
    $p_slice_id = q_pack_id($slice_id);
    $db = getDB();
    if ($slice_fields == 'all') {
        // all fields (item as well as slice fields)
        $slice_fields_where = '';
    } elseif (!$slice_fields) {
        // only item fields (not begins with underscore)
        $slice_fields_where = "AND id NOT LIKE '\_%'";
    } else {
        // only slice fields (begins with underscore)
        $slice_fields_where = "AND id LIKE '\_%'";
    }
    $db->query("SELECT id, name FROM field WHERE slice_id='$p_slice_id' $slice_fields_where ORDER BY $order");
    $lookup_fields = array();
    if ($add_empty) {
        $lookup_fields[''] = " ";  // default - none
    }
    while ($db->next_record()) {
        $lookup_fields[$db->f('id')] = $db->f('name');
    }
    if ($add_all) {
        $lookup_fields['all_fields'] = _m('-- any text field --');
    }
    freeDB($db);
    return $lookup_fields;
}

?>
