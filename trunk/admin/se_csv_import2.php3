<?php
//$Id$
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

/** Setting the transformation actions for converting an input ItemContent
 *  to an output ItemContent
 *
 * Parameters:
 * Input:
 *   $slice_id        - for edit slice
 *   $fileName        - file in upload directory
 *   $addParamsSerial - serialized additional parameters (file type specific)
 */


require_once "../include/init_page.php3";
require_once AA_INC_PATH."util.php3";
require_once AA_INC_PATH."import_util.php3";
require_once AA_INC_PATH."constants_param_wizard.php3";
require_once AA_INC_PATH."formutil.php3";
require_once AA_INC_PATH."feeding.php3";

/** Returns key of the $array, which value is most similar to given $text */
function findNearestText($text, $array) {
    $ret = '__empty__';
    $max = 5;
    if ( isset($array) AND is_array($array) ) {
        $text = strtoupper($text);
        foreach ( $array as $k => $v ) {
            $distance = levenshtein( $text, strtoupper($v) );
            if ( $distance < $max ) {
                $max = $distance;
                $ret = $k;
            }
        }
    }
    return $ret;
}


/** AA_Csv_Importer
 *  @todo this class should be rewritten to something more general
 */
class AA_Csv_Importer {
    var $slice_id;
    var $fileName;
    var $actions;
    var $mapping;
    var $html;
    var $params;
    var $addParams;

    var $itemId;
    var $itemIdMappedFrom;
    var $itemIdMappedActions;
    var $itemIdMappedParams;
    var $actionIfItemExists;

    var $auth_uid;

    function AA_Csv_Importer() {}

    function loadFromRequest() {
        global $auth;
        $this->slice_id            = $_REQUEST['slice_id'];
        $this->fileName            = $_REQUEST['fileName'];
        $this->actions             = $_REQUEST['actions'];
        $this->mapping             = $_REQUEST['mapping'];
        $this->html                = $_REQUEST['html'];
        $this->params              = $_REQUEST['params'];
        $this->itemId              = $_REQUEST['itemId'];
        $this->itemIdMappedFrom    = $_REQUEST['itemIdMappedFrom'];
        $this->itemIdMappedActions = $_REQUEST['itemIdMappedActions'];
        $this->itemIdMappedParams  = $_REQUEST['itemIdMappedParams'];
        $this->actionIfItemExists  = $_REQUEST['actionIfItemExists'];
        $this->addParams           = unserialize(base64_decode($_REQUEST['addParamsSerial']));
        $this->auth_uid            = $auth->auth["uid"];
    }

    function loadFromDb() {}
    function saveToDb()   {}

    function setFilename($filename) {
        $this->fileName            = $filename;
    }

    function getSliceId() { return $this->slice_id; }

    function check() {
        global $sess;
        if (!CheckPerms( $this->auth_uid, "slice", $this->slice_id, PS_EDIT_ALL_ITEMS)) {
            MsgPage($sess->url(self_base()."index.php3"), _m("You have not permissions to setting "));
            exit;
        }

        // check parameters
/*
        if (!file_exists($fileName)) {
            MsgPage($sess->url(self_base()."se_csv_import.php3"), _m("File for import does not exists:").$fileName );
        }
*/
        if (!$this->addParams) {
            MsgPage($sess->url(self_base()."se_csv_import.php3"), _m("Invalid additional parameters for import"));
            exit;
        }
    }

    function _prepare() {
        if (!isset($this->itemId)) {
            $this->itemId = "new";
        }

        if (!isset($this->actionIfItemExists)) {
            $this->actionIfItemExists = STORE_WITH_NEW_ID;
        }

        // create actions from the form
        $this->actions['slice_id........'] = "value";
        $this->params['slice_id........']  = $this->slice_id;

        $this->actions['id..............'] = $this->itemId;
        if ($this->itemId == 'old') {
            // update items with the id specified in $itemIdMappedFrom field
            $this->mapping['id..............'] = $this->itemIdMappedFrom;
            $this->actions['id..............'] = $this->itemIdMappedActions;
            $this->params['id..............']  = $this->itemIdMappedParams;
        } elseif ( $this->itemId == 'new' ) {
            $this->actions['id..............'] = 'new';  // new_id
        }
    }

    function upload() {
        global $sess;
        $this->_prepare();

        //-----------------------------------------------------------------------------
        // Output items should contain just these slice fields

        // $slice = AA_Slices::getSlice($this->slice_id);  // will be working after AA 2.10.0
        $slice = $GLOBALS['allknownslices']->addslice($this->slice_id);

        $trans_actions = new Actions($this->actions,$this->mapping, $this->html, $this->params);

        // Create list of fields from the first row of csv data
        $fieldNames = createFieldNames($this->fileName,$this->addParams);


        set_time_limit(IMPORTFILE_TIME_LIMIT);	// set time for the executing this script : todo ???

        $handle       = fopen($this->fileName,"r");
        $numProcessed = 0;
        $numError     = 0;

        // if first row is used for field names, skip it
        if ($this->addParams['caption']) {
            getCSV($handle,CSVFILE_LINE_MAXSIZE,$this->addParams['delimiter'],$this->addParams['enclosure']);
        }

        while ($csvRec = getCSV($handle,CSVFILE_LINE_MAXSIZE,$this->addParams['delimiter'],$this->addParams['enclosure'])) {
            $err = convertCSV2Items($csvRec, $fieldNames, $trans_actions, $slice->fields('record'), $itemContent);
            $numProcessed++;
            $msg .= _m("Item:").$numProcessed .":";

            if (!$err) {
                $itemContent->setSliceID($this->slice_id);
                $added_to_db = $itemContent->storeItem($this->actionIfItemExists, false);     // not invalidate cache
                if ($added_to_db == false) {
                    $err = _m("Cannot store item to DB"). ' '. ItemContent::LastErrMsg();
                }
            }
            if ($err) {
                $numError++;
                $msg.= _m("Transformation error:"). $err . "not inserted";
            } else {
                $msg.= _m('Ok: Item %1 stored', array($added_to_db));
            }
            $msg .= "<br>\n";
        }
        // log
        $logMsg = "Slice " .$this->slice_id. ": Processed ". $numProcessed. ", Stored ". ($numProcessed-$numError) .", Error: ". $numError. " items";
        writeLog("CSV_IMPORT",$logMsg);

        // invalidate cache;
        $GLOBALS['pagecache']->invalidateFor("slice_id=".$this->slice_id);  // invalidate old cached values

        fclose($handle);

        // deletes  uploaded file, todo - uncomment
        if ( !in_array( Files::sourceType($this->fileName), array('HTTP', 'HTTPS')) ) {
            if (unlink($this->fileName)) {
                writeLog("CSV_IMPORT",_m("Ok : file deleted "). $this->fileName );
            } else {
                writeLog("CSV_IMPORT",_m("Error: Cannot delete file"). $this->fileName );
            }
        }

        $msg = _m("Added to slice"). $this->slice_id ." :<br><br>\n". $msg." <br><br>\n";
        MsgPage($sess->url(self_base()."se_csv_import.php3"), $msg.$logMsg );
    }

    function preview() {
        global $sess;
        $this->_prepare();

        // $slice = AA_Slices::getSlice($this->slice_id);  // will be working after AA 2.10.0
        $slice = $GLOBALS['allknownslices']->addslice($this->slice_id);

        $trans_actions = new Actions($this->actions,$this->mapping, $this->html, $this->params);

        // Create list of fields from the first row of csv data
        $fieldNames = createFieldNames($this->fileName,$this->addParams);

        $slice_fields = $slice->fields('record');

        $slf['id..............'] = "Item id";
        foreach ( $slice_fields as $k => $v ) {
            $slf[$k] = $v['name'];
        }

        $handle = fopen($this->fileName, "r");

        FrmTabCaption(_m("Mapping preview"));
        FrmTabRow($slf);			// print output fields

        // if the first row is used for field names, skip it
        if ($this->addParams['caption']) {
            getCSV($handle,CSVFILE_LINE_MAXSIZE,$this->addParams['delimiter'],$this->addParams['enclosure']);
        }

        $numRows=5;		// number of showed items(rows) in the table
        while ($numRows-- > 0) {
            $csvRec = getCSV($handle,CSVFILE_LINE_MAXSIZE,$this->addParams['delimiter'],$this->addParams['enclosure']);
            if (!$csvRec) {  // end of file
                break;
            }
            $err = convertCSV2Items($csvRec,$fieldNames,$trans_actions,$slice_fields,$itemContent);
            if ($err) {
                echo "<tr><td>Transformation error: $err </td></tr>";	// todo
            }
            $itemContent->showAsRowInTable($slf);
        }
        FrmTabEnd("");
        // end preview
    }

    function printForm($set_default) {
        global $sess;

        //----------------------------------------------------------------------------
        // Create output fields

        // $slice = AA_Slices::getSlice($this->slice_id);  // will be working after AA 2.10.0
        $slice = $GLOBALS['allknownslices']->addslice($this->slice_id);

        list($slice_fields, $prifields) =  $slice->fields();

        foreach ( $prifields as $v ) {
            if ($v != "slice_id........") {
                $outFields[$v] = $slice_fields[$v]['name'];
            }
        }

        //create list of actions, : todo : possible loading from a file
        $actionList = getActions();

        // Create input fields from the first row of CSV data
        $inFields = createFieldNames($this->fileName,$this->addParams);


        $form_buttons = array("preview"         => array( "type"      => "submit",
                                                          "value"     => _m("Preview"),
                                                          "accesskey" => "P"),
                              "upload"          => array( "type"      => "submit",
                                                          "value"     => _m("Finish"),
                                                          "accesskey" => "S"),
                              "save"            => array( "type"      => "submit",
                                                          "value"     => _m("Save"),
                                                          "accesskey" => "A"),
                              "load"            => array( "type"      => "submit",
                                                          "value"     => _m("Load"),
                                                          "accesskey" => "L"),
                              "fileName"        => array( "value"     => $this->fileName ),
                              "addParamsSerial" => array( "value"     => base64_encode(serialize($this->addParams)))
                             );


        echo '<form enctype="multipart/form-data" method=post name="f" action="'. $sess->url(self_base() . "se_csv_import2.php3") .'">';

        FrmTabCaption(_m("Mapping settings"));
        ?>
            <tr>
              <td class=tabtxt><b><?php echo _m("To") ?></b></td>
              <td class=tabtxt><b><?php echo _m("From") ?></b></td>
              <td class=tabtxt><b><?php echo _m("Action") ?></b></td>
              <td class=tabtxt><b><?php echo _m("Html") ?></b></td>
              <td class=tabtxt><b><?php echo _m("Action parameters") ?></b></td>
              <td class=tabtxt><b><?php echo _m("Parameter wizard") ?></b></td>
             </tr>

               <?php
               $inFields["__empty__"] = "     ";
               foreach ( $outFields as $f_id => $f_name) {
                   echo "<tr><td class=tabtxt><b>$f_name</b></td>\n";
                   echo "<td>";
                   FrmSelectEasy("mapping[$f_id]",$inFields,!$set_default ? $this->mapping[$f_id] : findNearestText($f_name, $inFields));		// todo - multiple
                   echo "</td>";
                   echo "<td class=tabtxt>";
                   FrmSelectEasy("actions[$f_id]",$actionList,!$set_default ? $this->actions[$f_id] : "default");
                   echo "</td>";

                   echo "<td class=tabtxt ><input type=checkbox name=\"html[$f_id]\" "; if (!$set_default AND $this->html[$f_id]) echo  "CHECKED";  echo  "></input></td>";
                   echo "<td class=tabtxt><input type=text name=\"params[$f_id]\" value=\""; if (!$set_default) echo stripslashes($this->params[$f_id]);  echo "\"></input></td>";
                   echo "<td class=tabhlp><a href='javascript:CallParamWizard(\"TRANS_ACTIONS\",\"actions[$f_id]\",\"params[$f_id]\")'><b>"
                   ._m("Help: Parameter Wizard")."</b></a></td>";
                   echo "</tr>\n";
               }
               FrmTabSeparator(_m("Import options"));
               ?>

               <tr><td class=tabtxt colspan=2>Setting item id:</td><tr>

               <tr><td class=tabtxt align=center><input type="radio" <?php if ($this->itemId == "new") echo "CHECKED"; ?> NAME="itemId" value="new"></td>
                <td class=tabtxt >Create new id</td>
               </tr>
               <tr>
               <td class=tabtxt align=center><input type="radio" <?php if ($this->itemId == "old") echo "CHECKED"; ?> NAME="itemId" value="old"></td>
               <td class=tabtxt ><?php
                 echo _m("Map item id from"). '&nbsp';
                 FrmSelectEasy("itemIdMappedFrom",$inFields, $this->itemIdMappedFrom ? $this->itemIdMappedFrom : ( !$set_default ? $this->idFrom : $inFields[0]));
                 echo '<br>';
                 $mapping_options = array ( 'pack_id'   => _m('unpacked long id (pack_id)'),
                                            'store'     => _m('packed long id (store)'),
                                            'string2id' => _m('string to be converted (string2id) - with param:'));

                 FrmSelectEasy("itemIdMappedActions",$mapping_options, !$set_default ? $this->itemIdMappedActions : 'pack_id');
                 echo '&nbsp<input type="text" name="itemIdMappedParams" value="'. (!$set_default ? $this->itemIdMappedParams : '').'"></input>';
               ?></td>
            </tr>

        <?php
        FrmTabSeparator(_m("Select, how to store the items"));
        $storage_mode = array('insert_if_new' => _m('Do not store the item'),
                              'insert_new'    => _m('Store the item with new id'),
                              'overwrite'     => _m('Update the item (overwrite)'),
                              'add'           => _m('Add the values in paralel to current values (the multivalues are stored, where possible)'),
                              'update'        => _m('Rewrite only the fields, for which the action is defined')
                              );
        FrmInputRadio('actionIfItemExists', _m('If the item id is already in the slice'), $storage_mode, !$set_default ? $this->actionIfItemExists : "insert_if_new", true, '', '', 1);
        FrmTabSeparator(_m("Data source"));
        FrmInputText('fileName', _m('Source'), $this->fileName, 254, 100);

        FrmTabSeparator(_m("Store settings..."));

        $load_arr = GetTable2Array("SELECT o1.object_id, o2.text FROM object_text as o1 INNER JOIN object_text as o2 ON o1.object_id=o2.object_id
                                     WHERE o1.property LIKE 'type' AND o1.text LIKE 'AA_CSV_Importer'
                                     AND o2.property LIKE 'name'", 'object_id', 'text');

        FrmInputSelect('load_id', _m('Load setting'), (array)$load_arr);

        FrmInputText('save_name', _m('Save setting as'), $this->save_name);

        if ( in_array(Files::sourceType($this->fileName), array('HTTP','HTTPS'))) {
            FrmInputChBox('save_periodical', _m('Upload periodicaly'), $this->save_periodical);
        }

        FrmTabEnd($form_buttons, $sess, $this->slice_id);
        echo "</FORM>";
    }

}

if ( $load ) {
    // should be rewritten to true object storing functions
    $SQL      = "SELECT text FROM object_text WHERE object_id = '$load_id' AND property='importer'";
    $ret      = GetTable2Array($SQL, 'aa_first', 'aa_fields');
    $importer = unserialize($ret['text']);
    $importer->setFilename($_REQUEST['fileName']);
} else {
    $importer = new AA_Csv_Importer();
    $importer->loadFromRequest();
}

function SaveObjectProperty($obj_id, $property, $value) {
    $varset = new CVarset();
    $varset->add('object_id', 'text', $obj_id);
    $varset->add('property',  'text', $property);
    $varset->add('text',      'text', $value);
    $varset->doInsert('object_text');
}

if ( $save ) {
    $obj_id = new_id();
    SaveObjectProperty($obj_id, 'type',       'AA_CSV_Importer');
    SaveObjectProperty($obj_id, 'importer',   serialize($importer));
    SaveObjectProperty($obj_id, 'name',       $save_name);
    SaveObjectProperty($obj_id, 'periodical', $save_periodical);
    SaveObjectProperty($obj_id, 'owner',      $importer->getSliceId());
}

$importer->check();
if ($upload) {
    $importer->upload();
}


// -- Output form
HtmlPageBegin();   // Print HTML start page tags (html begin, encoding, style sheet, but no title)
?>
<TITLE><?php echo _m("Admin - Import .CSV file"); ?></TITLE>
<SCRIPT Language="JavaScript"><!--
function InitPage() {}

/* Calls the parameters wizard. Parameters are as follows:
    list = name of the array containing all the needed data for the wizard
    combo_list = a combobox of which the selected item will be shown in the wizard
    text_param = the text field where the parameters are placed
  */
  function CallParamWizard(list, combo_list, text_param ) {
    page = "<?php echo $sess->url(self_base()."param_wizard.php3")?>"
        + "&list=" + list + "&combo_list=" + combo_list + "&text_param=" + text_param;
    combo_list_el = document.f.elements[combo_list];
    page += "&item=" + combo_list_el.options [combo_list_el.selectedIndex].value;
    param_wizard = window.open(page,"somename","width=450,scrollbars=yes,menubar=no,hotkeys=no,resizable=yes");
    param_wizard.focus();
  }

//-->
</SCRIPT>
</HEAD>
<BODY>
<?php
$useOnLoad = true;
require_once AA_INC_PATH."menu.php3";
showMenu($aamenus, "sliceadmin","CSVimport");
PrintArray($err);
echo stripslashes($Msg);
echo "<H1><B>" . _m("Admin - Import CSV (2/2) - Mapping and Actions") . "</B></H1>";

if ($preview) {
    $importer->preview();
}

$importer->printForm(!($preview OR $load OR $save));

HtmlPageEnd();
page_close();
?>
