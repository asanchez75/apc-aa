<?php
/**
 * File contains definition of AA_Storable class - abstract class which
 * implements two methods for storing and restoring class data (used in
 * searchbar class, manager class, ...
 *
 * Should be included to other scripts (as /include/searchbar.class.php3)
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
 * @author    Honza Malik <honza.malik@ecn.cz>
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Copyright (C) 1999, 2000 Association for Progressive Communications
 * @link      http://www.apc.org/ APC
*/


/** AA_Includer manages php scripts includes.
 *
 *  The class holds the in the static variable the pair "class" => "file"
 *  so if you then call AA_Includer::include($classname), then the file
 *  with the class definition is processed (required)
 */
class AA_Includer {

    /** function simulates static class data (since php4 do not support
     *  class variables
     */
    function manage($action) {

    }

    function weNeed($classname) {

    }

}


/**
 * AA_Storable - abstract class which implements methods for storing and
 * restoring class data (used in searchbar class, manager class, ...).
 *
 * If you want to use strable methods in your class, you should derive the new
 * class from AA_Storable. Then you should define $persistent_slots array,
 * where you specify all the variables you want to store. Then you just call
 * getState() and setFromState() methods for storing and restoring object's data
 */
class AA_Storable {
    
    /** setFromState function
     * Restores the object's data from $state
     * State uses just basic types - array, int, text - not objects
     * @param  array $state state array which stores object's data. The array
     *                      you will get by getState() method.
     */
    function setFromState(&$state) {
        // we need to call getPersistentProperties() with $class parameter
        // because generic static class method (@see below)
        // in AA_Storable is not able to detect, what type of class it is in
        // Grrr! PHP (5.2.0)

        // first - deal with versioning
        // convert state to last version
        $object_version = max($state['aa_version'], 1);
        if ( call_user_func(array(get_class($this), 'version')) > $object_version) {
            // if some object uses version > 1,then method convertState()
            // should be defined
            $state = $this->convertState($object_version, $state);
        }

        foreach (call_user_func_array(array(get_class($this), 'getPersistentProperties'), array(get_class($this))) as $property) {
            $property_id   = $property->getId();
            $propery_value = $this->$property_id;
            if ($property->isMulti()) {
                if ( !is_array($propery_value) ) {
                    $propery_value = array();
                }
                if ( is_array($state[$property_id])) {
                    foreach($state[$property_id] as $k => $v) {
                        // create objects, if necessary
                        $propery_value[$k] = $this->_oneStatePropertyValue($propery_value[$k], $property, $v);
                    }
                }
            } else {
                $propery_value = $this->_oneStatePropertyValue($propery_value, $property, $state[$property_id]);
            }
            $this->$property_id = $propery_value;
        }
    }

    /** _oneStatePropertyValue function
     * @param $obj
     * @param $property
     * @param $state
     */
    function _oneStatePropertyValue($obj, $property, $state) {
        if (!$property->isObject()) {
            return $state;
        }
        // if the object is already created, then just rewrite persistent slots
        if (!is_object($obj))  {
            $property_type = $property->getType();
            $obj           = new $property_type;
        }
        $obj->setFromState($state);
        return $obj;
    }

    /** getState function
     * Returns state array of the object - stores object's data for leter
     * restoring (by setFromState() method)
     */
    function getState() {
        $class = get_class($this);
        foreach (call_user_func_array(array($class, 'getPersistentProperties'), array($class)) as $property) {
            $property_id   = $property->getId();
            if ($property->isMulti()) {
                if ( is_array($this->$property_id) ) {
                    foreach($this->$property_id as $k => $v) {
                        $ret[$property_id][$k] = is_object($v) ? $v->getState() : $v;
                    }
                }
            } else {
                $ret[$property_id] = is_object($this->$property_id) ? $this->$property_id->getState() : $this->$property_id;
            }
        }
        // add version if it is not 1
        if ( ($version = call_user_func(array($class, 'version'))) > 1) {
            $ret['aa_version'] = $version;
        }
        // we need the exact class in order we can factory the object from state
        $ret['aa_class']   = $class;
        return $ret;
    }

    /// Static ///

    /** version function
     *  Class version
     *  Used for getting state from data, which was stored sometimes in
     *  the history, so the inner structure of the class was changed
     */
    function version() {
        return 1;
    }

    /** getPersistentProperties function
     *  Returns array of persistent slots (AA_property)
     *  Uses getClassProperties() method of the classes from which it grabs all
     *  all persistent properties
     * @param $class
     */
    function getPersistentProperties($class=null) {
        // we need to call getPersistentProperties() with $class parameter
        // because this generic static classs method is not able to detect, what
        // type of class it is in. Grrr! PHP (5.2.0)
        $ret = array();

        if (is_callable(array($class, 'getClassProperties'))) {
            $properties = call_user_func(array($class, 'getClassProperties'));
            foreach ( $properties as $id => $property) {
                if ( $property->isPersistent() ) {
                    $ret[$id] = $property;
                }
            }
        }
        return $ret;
    }
}

class AA_Object extends AA_Storable {

    /** Object ID   - 32 characters long hexadecimal number */
    var $aa_id;

    /** Object Name - max 16 characters long object name - optional, unique for whole AA */
    var $aa_name;

    /** Object Owner - id if object's parent, where the object belongs - oprional */
    var $aa_owner;

    /** Constructor
     *  @param $params array of parameters in form 'property_name' => 'value'
     */
    function AA_Object($params) {
        // ask class, which parameters uses and fill it
        // call AA_Widget_Txt::getClassProperties()), for example

        $class = get_class($this);
        foreach (call_user_func_array(array($class, 'getClassProperties'), array($class)) as $name =>$property) {
            if (isset($params[$name])) {
                $this->$name = $params[$name];
            }
        }
    }

    /** setOwner function
     * @param $owner_id
     */
    function setOwner($owner_id) {
        $this->aa_owner = $owner_id;
    }

    /** setName function
     * @param $name
     */
    function setName($name) {
        $this->aa_name = $name;
    }

    /** setId function
     * @param $id
     */
    function setId($id) {
        $this->aa_id = $id;
    }

    function getProperty($property_id, $default=null) {
        return is_null($default) ? $this->$property_id : (($this->$property_id == '')? $default : $this->$property_id);
    }

    /** save function
     *  Save the object to the database
     */
    function save() {
        $this->delete();

        $class = get_class($this);
        foreach (call_user_func_array(array($class, 'getClassProperties'), array($class)) as $property) {
            $property_id   = $property->getId();
            if ($property->isMulti()) {
                if ( is_array($this->$property_id) ) {
                    // all keys are numeric
                    foreach($this->$property_id as $k => $v) {
                        $this->_saveProperty($property, $v, $k);
                    }
                }
            } else {
                $this->_saveProperty($property, $this->$property_id);
            }
        }

        // now the mandatory fields
        // $this->_saveRow('aa_id',       $this->getId(),                        'text');
        $this->_saveRow('aa_type',     get_class($this),                         'text');
        // add version if it is not 1
        $this->_saveRow('aa_version',  call_user_func(array($class, 'version')), 'int');
        if ( $this->aa_name ) {
            $this->_saveRow('aa_name', $this->aa_name,                           'text');
        }
        if ( $this->aa_owner ) {
            $this->_saveRow('aa_owner', $this->aa_owner,                         'text');
        }
        // helper field aa_subobjects used for quicker load of object
        $subobjects = $this->getSubObjects();
        if (count($subobjects) > 0) {
            $this->_saveRow('aa_subobjects', join(',', $subobjects),             'text');
        }
        return $this->aa_id;
    }

    /** delete function
     *  Deletes the object from the database including all the subobjects
     */
    function delete() {
        $to_delete = array( $this->getId() );

        // we have to ask database for subobjects, because it gives us the ids
        // as stored in database, not the ids of current subobjects, which could
        // be different. The $this->getSubObjects() donot help us here!
        $to_delete = array_merge($to_delete, explode(',',$this->loadProperty($to_delete[0], 'aa_subobjects')));
        $varset    = new CVarset;

        $sqlin     = Cvarset::sqlin('object_id', $to_delete);
//        $varset->doDeleteWhere('object_text', Cvarset::sqlin('object_id', AA_Object::query('AA_Set', array('cde50ab466e2211b97ff8f93e7add44f', 'cd8499c809cdbb3b51d026e1e07520c5'))));
//        exit;
        $varset->doDeleteWhere('object_text',    $sqlin);
        $varset->doDeleteWhere('object_integer', $sqlin);
        $varset->doDeleteWhere('object_float',   $sqlin);
    }

    /** getSubObjects function
     *  Get all object ids which is inside this object
     */
    function getSubObjects() {
        $ret   = array();
        $class = get_class($this);
        foreach (call_user_func_array(array($class, 'getClassProperties'), array($class)) as $property) {
            if (!$property->isObject()) {
                continue;
            }
            $property_id   = $property->getId();
            if ( is_array($this->$property_id) ) {
                // all keys are numeric
                foreach($this->$property_id as $v) {
                    if ( is_object($v) ) {
                        $ret[] = $v->getId();
                        $ret   = array_merge($ret, $v->getSubObjects());
                    }
                }
            } elseif ( is_object($this->$property_id) ) {
                $obj   = $this->$property_id;
                $ret[] = $obj->getId();
                $ret   = array_merge($ret, $obj->getSubObjects());
            }
        }
        return $ret;
    }

    /** _saveProperty function
     * @param $property
     * @param $value
     * @param $priority
     */
    function _saveProperty($property, $value, $priority=0) {

        // Property type - text | int | bool | float | <class_name>
        if ( !$property->isObject()) {
            $this->_saveRow($property->getId(), $value, $property->getType(), $priority);
            return true;
        }

        if (!is_object($value)) {
            return false;
        }
        //  this property is object - so save it (the id of the object is returned)
        $object_id = $value->save();
        // if not saved, then it returns null
        if (!$object_id) {
            return false;
        }
        $this->_saveRow($property->getId(), $object_id, 'text', $priority);
        return true;
    }

    /** _saveRow function
     * @param $property_id
     * @param $value
     * @param $type
     * @param $priority
     */
    function _saveRow($property_id, $value, $type, $priority=0) {
        $TABLE_NAMES = array('text'=>'text', 'bool'=>'integer', 'int'=>'integer', 'float'=>'float');

        $varset = new CVarset();
        $varset->add('object_id', 'text',   $this->getId());
        $varset->add('priority',  'number', $priority);
        $varset->add('property',  'text',   $property_id);

        // Property type - text | int | bool | float | <class_name>
        $varset->add('value',      $type,   $value);
        $varset->doInsert('object_'. $TABLE_NAMES[$type]);
    }

    /** getId function
     *
     */
    function getId() {
        // id of the object is not deffined, yet
        if (!$this->aa_id) {
            $this->aa_id = new_id();
        }
        return $this->aa_id;
    }

    /** getName function
     *
     */
    function getName() {
        return $this->aa_name;
    }

    /// Static ///

    /** getClassProperties function
     *   - abstract method defining the class properties
     *   - properties are used for two reasons
     *      - it could be stored in the database object storage when the object
     *        is stored (save()) (when persistent is set to true)
     *      - the object could be edited on html page - the form is automaticaly
     *        created using the properties (@see AA_Components)
     */
    function getClassProperties()  {
        // array of AA parameters (can't be object's data, since we need
        // to call it staticaly (as class method)
        return array();
    }

    /** getNameArray function
     * @param $obj_type
     * @param $owner
     */
    function getNameArray($obj_type, $owner) {
        if ( empty($owner) ) {
            return array();
        }
        $SQL = "SELECT o1.object_id, o3.value FROM object_text as o1 INNER JOIN object_text as o2 ON o2.object_id=o1.object_id INNER JOIN object_text as o3 ON o3.object_id=o1.object_id
                 WHERE o1.property = 'aa_type'  AND o1.value = '$obj_type'
                   AND o2.property = 'aa_owner' AND ". CVarset::sqlin('o2.value', $owner) ."
                   AND o3.property = 'aa_name'";

        $ret = GetTable2Array($SQL, 'object_id', 'value');
        return is_array($ret) ? $ret : array();
    }

    /** factory function
     * @param $classname
     * @param $params
     */
    function &factory($classname, $params=null) {
        if ( class_exists($classname) ) {
            return new $classname($params);
        }
        if ( AA_Includer::weNeed($classname) ) {
            return new $classname($params);
        }
        return null;
    }

    /** factoryByName function
     * @param $mask
     * @param $name
     * @param $params
     */
    function &factoryByName($class_mask, $name, $params=null) {
        return AA_Object::factory($class_mask. ucwords(strtolower($name)), $params);
    }


    /** parseClassProperties function
     *  Parses class parameters from the string, which is stored in the database
     *  Typical use is for fields.input_show_func, where parameters are stored
     *  as string in the form: fnc:const:param
     *  @param $class_mask
     *  @param $param
     *  @return asociative array of parameters, the name of parameters is given
     *  by the class itself ($class_mask . fnc).
     */
    function parseClassProperties($class_mask, $string) {
        // we do not use ParamExplode() - I  do not like the http:// replacement there
        $a       = str_replace("#:", "__-__.", $string);    // dummy string
        $b       = str_replace(":", "##Sx", $a);            // Separation string is //#Sx
        $c       = str_replace("__-__.", ":", $b);         // change "#:" to ":"
        $splited = explode("##Sx", $c);

        // first parameter is the class identifier - the parameters starts then
        $i      = 1;
        $class  = $class_mask. ucwords(strtolower($splited[0]));
        $params = array('class' => $class);

        // ask class, which parameters uses
        // call AA_Widget_Txt::getClassProperties()), for example
        foreach (call_user_func_array(array($class, 'getClassProperties'), array($class)) as $name => $property) {
            if (isset($splited[$i])) {
                $params[$name] = $splited[$i++];
            }
        }

        return $params;
    }

    /** factoryByString function
     *  Creates object from the string, which is used for storing setting in
     *  the database (older approach). The string looks like:
     *    dte:1:10:1
     *  which means, that it is instance of AA_Widget_Dte (when $class_mask == 'AA_Widget')
     *  and the properties are filled with values 1, 10 and 1 (in this order)
     *
     * @param $class_mask like 'AA_Widget'
     * @param $string     like dte:1:10:1 in field.input_show_func
     */
    function &factoryByString($class_mask, $string) {
        $params = AA_Object::parseClassProperties($class_mask, $string);
        return AA_Object::factory($params['class'], $params);
    }

    function loadProperty($id, $property) {
        return GetTable2Array("SELECT value FROM object_text WHERE object_id = '$id' AND property = '$property'", 'aa_first', 'value');
    }

    /** getObjectType
     * @param $id
     */
    function getObjectType($id) {
        return AA_Object::loadProperty($id, 'aa_type');
    }

    /** load function
     * @param $id
     * @param $type
     */
    function &load($id, $type=null) {
        // @todo optimize the load
        //    - get used tables from properties,
        //    - load object from database in one step using aa_subobjects property of the objects

        if ( !$type ) {
            $type = getObjectType($id);
        }
        if ( !$type ) {
            return null;
        }
        $obj = new $type;
        $obj->setId($id);
        $properties = call_user_func_array(array($type, 'getClassProperties'), array($type));

        $props_from_db = GetTable2Array("SELECT `property`, `value` FROM object_text WHERE object_id = '$id' ORDER by property, priority", '');
        if ( is_array($props_from_db) ) {
            foreach ( $props_from_db as $v ) {
                $property_id = $v['property'];
                if ( !is_object($properties[$property_id])) {
                    if (in_array($property_id, array('aa_name', 'aa_owner'))) {
                        $obj->$property_id = $v['value'];
                    }
                    continue;
                }
                $property    = $properties[$property_id];
                if ( $property->isMulti() ) {
                    $p = (array)$obj->$property_id;
                    $p[] = ($property->isObject() ? AA_Object::load($v['value'], $property->getType()) : $v['value']);
                    $obj->$property_id   = $p;
                } else {
                    $obj->$property_id   = ($property->isObject() ? AA_Object::load($v['value'], $property->getType()) : $v['value']);
                }
            }
        }

        $props_from_db = GetTable2Array("SELECT `property`,`value` FROM `object_integer` WHERE object_id = '$id' ORDER by property, priority", '');
        if ( is_array($props_from_db) ) {
            foreach ( $props_from_db as $v ) {
                $property_id = $v['property'];
                $property    = $properties[$property_id];
                if ( !is_object($properties[$property_id]) ) {
                    continue;
                }
                if ( $property->isMulti() ) {
                    $p = (array)$obj->$property_id;
                    $p[] = ($property->isObject() ? AA_Object::load($v['value'], $property->getType()) : $v['value']);
                    $obj->$property_id   = $p;
                } else {
                    $obj->$property_id   = $v['value'];
                }
            }
        }

        $props_from_db = GetTable2Array("SELECT `property`,`value` FROM `object_float` WHERE object_id = '$id' ORDER by property, priority", '');
        if ( is_array($props_from_db) ) {
            foreach ( $props_from_db as $v ) {
                $property_id = $v['property'];
                $property    = $properties[$property_id];
                if ( !is_object($properties[$property_id]) ) {
                    continue;
                }
                if ( $property->isMulti() ) {
                    $p = (array)$obj->$property_id;
                    $p[] = ($property->isObject() ? AA_Object::load($v['value'], $property->getType()) : $v['value']);
                    $obj->$property_id   = $p;
                } else {
                    $obj->$property_id   = $v['value'];
                }
            }
        }
        return $obj;
    }


    /** query function
     * Finds object IDs for objects given by conditions
     *
     *   @param string        $type   - object type
     *   @param AA_Slices     $owners - search only objects owned by those slices
     *   @param $set
     *   @param zids          $restrict_zids - use it if you want to choose only from a set of ids
     *   @return A zids object with a list of the ids that match the query.
     *
     *   @global  bool $debug (in) many debug messages
     *   @global  bool $nocache (in) do not use cache, even if use_cache is set
     */
    function query($type, $owners=null, $set=null, $restrict_zids=null) {
        // select * from item, content as c1, content as c2 where item.id=c1.item_id AND item.id=c2.item_id AND
        // c1.field_id IN ('fulltext........', 'abstract..........') AND c2.field_id = 'keywords........' AND c1.text like '%eufonie%' AND c2.text like '%eufonie%' AND item.highlight = '1';
        global $debug;                 // displays debug messages
        global $nocache;               // do not use cache, if set

        $SQL = "SELECT o1.object_id FROM object_text as o1, object_text as o2 WHERE o1.object_id=o2.object_id
                AND o1.property='aa_type'  AND o1.value='".quote($type)."'
                AND o2.property='aa_owner' AND o2.value='".quote(reset($owners))."'";

        $ids = GetTable2Array($SQL, '', 'object_id');
        return $ids ? $ids : array();

        // @todo !!! - rewrite it.
        // do the same as in quryZids for any object
    }
}

/** Components (plugins) manipulation class */
class AA_Components extends AA_Object {

    /// Static ///

    /** Used parameter format (in fields.input_show_func table)
     *  @todo - specify the parameters better - value type, used widget, ... so
     *          we could generate Parameter wizard (and validation) from those
     *          informations
     */


    /** getClassNames function
     *  Return names of all known AA classes, which begins with $mask
     *  static function
     * @param $mask
     */
    function getClassNames($mask) {
        $right_classes = array();

        // php4 returns classes all in lower case :-(
        $mask          = strtolower($mask);
        $mask_length   = strlen($mask);
        foreach (get_declared_classes() as $classname) {
            if ( substr(strtolower($classname),0,$mask_length) == $mask ) {
                $right_classes[] = $classname;
            }
        }
        return $right_classes;
    }

    /** getSelectionCode function
     * @param $mask
     * @param $input_id
     * @param $params
     */
    function getSelectionCode($mask, $input_id, &$params) {
        $options      = array('AA_Empty' => _m('select ...'));
        $html_options = array('AA_Empty' => '');
        foreach (AA_Components::getClassNames($mask) as $selection_class) {
            // call static class methods
            $options[$selection_class]      = call_user_func(array($selection_class, 'name'));
            $html_options[$selection_class] = call_user_func_array(array($selection_class, 'htmlSetting'), array($input_id, &$params));
        }
        return getSelectWithParam($input_id, $options, "", $html_options);
    }
}

?>
