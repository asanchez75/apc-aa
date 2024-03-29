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
 * @version   $Id: formutil.php3 2800 2009-04-16 11:01:53Z honzam $
 * @author    Honza Malik <honza.malik@ecn.cz>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Copyright (C) 1999, 2000 Association for Progressive Communications
 * @link      http://www.apc.org/ APC
 *
*/


class AA_Formrow extends AA_Storable {
    function getRowProperty() {}
    function getWidget()      {}

    function getRowHtml($content, $type='') {
        $widget = $this->getWidget();
        switch ($type) {
            case 'ajax': return $widget->getAjaxHtml($this->getRowProperty(), $content);
            case 'live': return $widget->getliveHtml($this->getRowProperty(), $content);
        }
        return $widget->getHtml($this->getRowProperty(), $content);
    }

    // in order we can dispaly row widget (used for editing forms)
    function addFormrows($form) {
        $slice  = AA_Slice::getModule($form->object_owner);
        $fields = $slice->getFields();
        $f_arr  = $fields->getNameArray();
              // AA_Property($id='', $name='',              $type='text', $multi=false, $persistent=true, $validator=null, $required=false, $input_help='', $input_morehlp='', $example='', $show_content_type_switch=0, $content_type_switch_default=FLAG_HTML, $perms=null, $default=null) {
        $p = new AA_Property('rows', _m("Fields to show"),  'AA_Formrow_Field', true, false);
        $w = new AA_Widget_Formrow(array('const_arr' => $f_arr));
        //$w = new AA_Widget_Mch(array('columns'=>1,'const_arr' => $f_arr));
        $form->addRow(new AA_Formrow_Full($p, $w));  // use default widget for the field
    }
}

class AA_Formrow_Text extends AA_Formrow {
    protected $text;        // protected - we need the data visible for AA_Statestore
    function __construct($text=null) { // default values are needed for AA_Storable's construction
        $this->text = $text;
    }
    function getRowHtml($content, $type='') {
        return '<div>'.$this->text.'</div>';
    }
}

/** Fully qualified form row */
class AA_Formrow_Full extends AA_Formrow {
    protected $property;        // protected - we need the data visible for AA_Statestore
    protected $widget;

    /** Constructor - use the default for AA_Object */
    function __construct($property=null, $widget=null) { // default values are needed for AA_Storable's construction
        $this->property       = $property;
        $this->widget         = $widget;
    }

    function getRowProperty() { return $this->property; }

    function getWidget()      { return $this->widget; }

    /**      */
    static function getClassProperties() {
        return array (                   //       id                   name                  type        multi  persist validator, required, help, morehelp, example
            'property'       => new AA_Property( 'property',       _m("Property"),          'AA_Property', false, true),
            'widget'         => new AA_Property( 'widget',         _m("Widget"),            'AA_Widget',   false, true)
            );
    }
}

class AA_Formrow_Field extends AA_Formrow {
    protected $field_id;        // protected - we need the data visible for AA_Statestore
    protected $slice_id;        // protected - we need the data visible for AA_Statestore

    /** Constructor - use the default for AA_Object */
    function __construct($field_id='', $slice_id='') { // default values are needed for AA_Storable's construction
        $this->field_id       = $field_id;
        $this->slice_id       = $slice_id;
    }

    function getRowProperty() {
        $field  = $this->_getField();
        $widget = $field->getWidget();
        return $field->getAaProperty($widget->multiple());
    }

    function getWidget()      {
        $field  = $this->_getField();
        return $field->getWidget();
    }

    private function _getField() {
        $slice  = AA_Slice::getModule($this->slice_id);
        $fields = $slice->getFields();
        return $fields->getField($this->field_id);
    }

    /**      */
    static function getClassProperties() {
        return array (                   //       id                   name               type        multi  persist validator, required, help, morehelp, example
            'field_id'       => new AA_Property( 'field_id',       _m("Field"),          'string', false, true),
            'slice_id'       => new AA_Property( 'slice_id',       _m("Slice ID"),       'string', false, true)
            );
    }
}



/** Form Row - special widget for form setting */
class AA_Widget_Formrow extends AA_Widget {

    /** Constructor - use the default for AA_Object */
    // function __construct($params=array()) { parent::__construct($params); }  // not needed - called as default

    /** - static member functions
     *  used as simulation of static class variables (not present in php4)
     */

    /** name function */
    function name() {
        return _m('Form Row - special widget for form setting');
    }   // widget name

    /** multiple function */
    function multiple() {
        return true;   // returns multivalue or single value
    }

    /** getClassProperties function of AA_Serializable
     *  Used parameter format (in fields.input_show_func table)
     */
    static function getClassProperties()  {
        return array (                      //           id                        name                        type    multi  persist validator, required, help, morehelp, example
            'const_arr'              => new AA_Property( 'const_arr',              _m("Values array"),         'string', true,  true, 'string', false, _m("Directly specified array of values (do not use Constants, if filled)"))
            );
    }


    /** Creates base widget HTML, which will be surrounded by Live, Ajxax
     *  or normal decorations (added by _finalize*Html)
     */
    function _getRawHtml($aa_property, $content, $type='normal') {
        // $property_id  = $aa_property->getId();
        // $input_name   = AA_Form_Array::getName4Form($property_id, $content)."[formrow][]";

        $base_name   = AA_Form_Array::getName4Form($aa_property->getId(), $content);
        $input_name  = $base_name ."[]";
        $base_id     = AA_Form_Array::formName2Id($base_name);
        $widget_add  = ($type == 'live') ? " class=\"live\" onchange=\"AA_SendWidgetLive('$base_id')\"" : '';

        $ser_values   = $content->getValuesArray($aa_property->getId());
        $arr          = array();
        foreach ($ser_values as $ser_row) {
            $row   = unserialize($ser_row);
            $field = $row->getRowProperty();
            $arr[] = $field->getId();
        }

        $fields    = new AA_Content;
        $selected  = new AA_Value($arr);
        $fields->setAaValue($aa_property->getId(), $selected);
        $widget    = "<select name=\"$input_name\" id=\"$input_name\" multiple size=20>";
        $options   = $this->getOptions($selected, $fields, false, false, $aa_property->isRequired() ? 2 : 1);
        $widget   .= $this->getSelectOptions( $options );
        $widget   .= "</select>";

        return array('html'=>$widget, 'last_input_name'=>$input_name, 'base_name' => $base_name, 'base_id'=>$base_id, 'required'=>$aa_property->isRequired());
    }

}


/** Form manipulating class
 *  The code could be a bit confusing since this class manages forms, but the
 *  forms are also manageable as form - that's why it must implement some
 *  AA_Object's methods */
class AA_Form extends AA_Object {

    protected $rows = array();

    public   $object_type  = '';  // for which object class is current form
    public   $object_id    = '';  // for which object is current form
    public   $object_owner = '';  // to whom the form belongs

    const PREPARED = 1;
    const SAVED    = 2;

    /** allows storing form in database
     *  AA_Object's method
     */
    static function getClassProperties() {
        return array (          //           id       name       type        multi  persist validator, required, help, morehelp, example
            'rows'        => new AA_Property( 'rows',        _m("Rows"),         'AA_Formrow', true, true),
            'object_type' => new AA_Property( 'object_type', _m("Object Type"),  'text', false, true)
            );
    }

    function addRow($row) {
        $this->rows[] = $row;
    }
    function addRows($rows) {
        $this->rows = array_merge($this->rows,$rows);
    }

    function setObject($otype, $oid, $oowner) {
        $this->object_type  = $otype;
        $this->object_id    = $oid;
        $this->object_owner = $oowner;
    }

    static public function factoryForm($otype, $oid, $oowner) {
        $form = new AA_Form();
        $form->setObject($otype, $oid, $oowner);
        $otype::addFormrows($form);
        return $form;
    }

    function process($aa) {
        if ( !empty($aa) ) {
            // update or insert
            $object = call_user_func_array(array($this->object_type, 'factoryFromForm'), array($this->object_owner, $this->object_type));
            // @todo check permissions, validation, $store_mode state (see readFromForm)

            $object->save();

            return AA_Form::SAVED;
        }
        return AA_Form::PREPARED;
    }

    function getObjectEditHtml() {
        $content = $this->_getContent();

        $html    = $this->_getRowsHtml($content);
        $html   .= with( new AA_Widget_Hid )->getHtml(AA_Object::getPropertyObject('aa_owner'), $content);
        $html   .= with( new AA_Widget_Hid )->getHtml(AA_Object::getPropertyObject('aa_type'),  $content);

        if ($this->object_id) {
            $html .= with( new AA_Widget_Hid )->getHtml(AA_Object::getPropertyObject('aa_id'), $content);
        }

        return $html;
    }

    /** Add item form */
    function getAjaxHtml($ret_code, $type='add') {
        if (!isset($this->object_type)) { $this->object_type = 'AA_Item'; } // older forms stored in database do not have this field set
        $this->object_owner = $this->getOwnerId();                          // slice_id is the same as the slice_id of the form (where it is defined)

        $id  = get_if($this->object_id, new_id());
        $content = $this->_getContent();

        $html  = "\n  <fieldset id=\"form$id\">";
        $html .= "\n    ". '<input id="inline" name="inline" value="1" type="hidden">';
        $html .= "\n    ". '<input id="slice_id" name="slice_id" value="'.$this->object_owner.'" type="hidden">';
        $html .= "\n    ". '<input id="ret_code" name="ret_code" value="'.$ret_code.'" type="hidden">';

        $html .= $this->_getRowsHtml($content);

        switch ($type) {
            case 'add':      $html .= "\n    <input id=\"ajaxsend$id\" name=\"ajaxsend$id\" value=\"". _m('Insert'). "\" onclick=\"AA_AjaxSendAddForm('form$id')\" type=\"button\">";
                             break;
            case 'inplace':  $html .= "\n    <input id=\"ajaxsend$id\" name=\"ajaxsend$id\" value=\"". _m('Insert'). "\" onclick=\"AA_AjaxSendForm('form$id', 'filler.php3')\" type=\"button\">";
                             break;
        }
        $html .= "\n  </fieldset>";

        return $html;
    }

    /** Edit item form
     *  @param $type  ajax|live
     */
    function getEditFormHtml($type='live') {
        if (!$this->object_id) {
            return '';
        }
        $content = $this->_getContent();

        $html  = "\n  <fieldset id=\"form".$this->object_id."\">";
        $html .= $this->_getRowsHtml($content, $type);
        $html .= "\n  </fieldset>";

        return $html;
    }

    /** @todo write permission function, which modifies the form based on the
     *        actual user's permissions and profile
     *  @param $type  normal|ajax|live
     */
    private function _getRowsHtml($content, $type='') {
        $ret = '';
        foreach($this->rows as $row) {
            $ret .= $row->getRowHtml($content, $type);
        }
        return $ret;
    }

    private function _getContent() {
        if (empty($this->object_id)) {
            $content = new AA_Content();
            $content->setOwnerId($this->object_owner);
        } else {
            $contents = AA_Object::getContent(array('class'=>$this->object_type), new zids($this->object_id));
            $content = $contents[$this->object_id];
        }
        $content->setAaValue('aa_type', new AA_Value($this->object_type));
        return $content;
    }

    /** iEditable method - creates Object from the form data */
    public static function factoryFromForm($oowner, $otype=null) {
        $grabber = new AA_Objectgrabber_Form();
        $grabber->prepare();    // maybe some initialization in grabber
        // we expect just one form - no need to loop through contents
        $content    = $grabber->getContent();
        // while ($content = $grabber->getContent())
        $store_mode = $grabber->getStoreMode();        // add | update | insert
        $grabber->finish();    // maybe some finalization in grabber

        // specific part for form
        $object = new AA_Form();
        $object->setId($content->getId());
        $object->setOwnerId($oowner);
        $object->setName($content->getName());
        $object->setObject('AA_Item', '', $oowner);

        $fields2show = $content->getValuesArray('rows');
        foreach ($fields2show as $field) {
            $object->addRow( new AA_Formrow_Field($field, $oowner));   // use default widget for the field (@todo - make possible to change the widget)
        }
        return $object;
    }

    /** iEditable method - adds Object's editable properties to the $form */
    // public static function addFormrows($form);

    /** iEditable method - save the object to the database */
    // public        function save();
}

?>
