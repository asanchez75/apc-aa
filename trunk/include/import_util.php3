<?php
//$Id$
/*
Copyright (C) 1999-2003 Association for Progressive Communications
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

define("SHOW_FILE_SIZE",         65536);
define("CSVFILE_LINE_MAXSIZE",   65536);
define("IMPORTFILE_PREVIEW_ROWS",5);
define("IMPORTFILE_TIME_LIMIT",  6000);

define("FILE_PREFIX",   'csvdata');
define("CSV_DIRECTORY", 'csvdata');

define("NOT_STORE",        0);
define("STORE_WITH_NEW_ID",1);
define("UPDATE",           2);

define("INSERT", 1);
define("UPDATE", 2);

/** compare version  phpversion() to $ver (format major.minor.build)
 *  returns -1,0,1 :  if php version is less,equal,greater than $ver
 */
function comparePHPVersion($ver) {
    $v1 = explode(".",phpversion());
    $v2 = explode(".",$ver);
    for ($i=0;$i<3;$i++) {
        if ($v1[$i] < $v2[$i]) return -1;
        if ($v1[$i] > $v2[$i]) return 1;
    }
    return 0;
}

function getCSV($handle,$maxsize = 65536,$delimiter = ";",$enclosure = '"') {
    if (comparePHPVersion("4.3.0") == -1) {
        return fgetcsv($handle,$maxsize,$delimiter);
    } else {
        return fgetcsv($handle,$maxsize,$delimiter, $enclosure);
    }
}

/** create field names from the CSV file
 *  For CSV data: if the first row is used for field names, parse first row
 *                according to additional params (delimiter, enclosure),
 *                otherwise creates field1, field2, ...
 */
function createFieldNames($fileName,&$addParams) {
    $handle = fopen($fileName,"r");
    $fn = getCSV($handle,CSVFILE_LINE_MAXSIZE,$addParams['delimiter'],$addParams['enclosure']);
    fclose($handle);
    if (!is_array($fn)) {
        return $addParams['caption'] ? array($fn=>$fn) : array("Field 1"=>"Field 1");
    }

    if ($addParams['caption']) {
        while (list(,$v) = each($fn)) {
            $fieldNames[$v] = $v;
        }
    } else {
        $l = count($fn);
        for ($i=1; $i<= $l; $i++) {
            $f = "Field ".$i;
            $fieldNames[$f] = $f;
        }
    }
    return $fieldNames;
}

function convertCSV2Items(&$csvRec,&$fieldNames,&$trans_actions,&$slice_fields,&$itemContent) {
    $itemContent1 = new ItemContent;
    $itemContent1->setFromCSVArray($csvRec, $fieldNames);
    $itemContent = new ItemContent;
    $err = $itemContent->transform($itemContent1, $trans_actions, $slice_fields);
    return $err;
}

class Actions {
    var $actions;
    var $globalParams;

    function Actions($actions, $inFields, $html, $params, $globalParams="") {
        foreach ( $actions as $f_id => $action) {
            $this->actions[$f_id] = array( "from"=>$inFields[$f_id],
                                           "action"=>new Action($action,$html[$f_id],stripslashes($params[$f_id])));
        }
        $this->globalParams = $globalParams;
    }

    /** Transform the input item content to the output item content according
     *  to the actions
     */
     function transform(&$itemContent, $slice_fields, &$outputItemContent) {
         global $auth;

         // id is not part of $slice_fields, unfortunatelly (should be changed)
         if ( !isset($slice_fields['id..............']) ) {
             $slice_fields['id..............'] = 'to be processed in next loop';
         }

         foreach ( $slice_fields as $field_id => $foo ) {
             $action = &$this->actions[$field_id];
             unset( $fieldVal, $v);
             $done = false;

             // fill up the output field with default value, if the action does not exist for the output field, or the action is "default"
             if (!$action || $action['action']->getAction() == "default") {
                 switch ($field_id) {
                     // case "display_count..." : $v = 0; break;
                     case "status_code....." : $done = 1; $fieldVal[]['value'] = 1; break; // todo
                     case "flags..........." : $done = 1; $fieldVal[]['value'] = ITEM_FLAG_OFFLINE; break;
                     case "publish_date...." : $done = 1; $fieldVal[]['value'] =  time(); break;
                     case "post_date......." : $done = 1; $fieldVal[]['value'] =  time(); break;
                     case "last_edit......." : $done = 1; $fieldVal[]['value'] =  time(); break;
                     case "expiry_date....." : $done = 1; $fieldVal[]['value'] =  mktime(0,0,0,date("m"),date("d"),date("Y")+10) ; break;	// expiry in 10 years default : TODO
                     case "posted_by......." : $done = 1; $fieldVal[]['value'] = $auth->auth['uid']; break;	// todo
                     case "edited_by......." : $done = 1; $fieldVal[]['value'] = $auth->auth['uid']; break;	// todo
                     case "id.............." : $done = 1; $fieldVal[]['value'] = $auth->auth['uid']; break;
                     default :
                         if ( $action['from'] && ($action['from'] != '__empty__')) {
                             $action['action']->setAction('store');
                         } elseif ( $action['action']->getParams() ) {
                             $action['action']->setAction('value');
                         } else {
                             $done = 1;
                         }
                         break;
                 }
             } elseif ($action['action']->getAction() == "new") {
                  $done = 1;
                  $fieldVal[]['value'] = 'new id';
             }
             if ( !$done ) {

                 // transform the input field to the output field according the action
                 $err = $action['action']->transform($itemContent,$action['from'],$this->globalParams,$fieldVal );
                 if ($err) {
                     return $err;
                 }
             }
             if (isset($fieldVal) and is_array($fieldVal)) {
                 // store the output field to the output item content
                 $outputItemContent->setFieldValue($field_id,$fieldVal);
             }
        }
    }

    function getOutputFields() {
        return array_keys( $this->actions );
    }

    function getActions() {
        foreach ( $this->actions as $v ) { $f[] = $v['action']; }
        return $f;
    }

    function getHtml() {
        foreach ( $this->actions as $v ) { $f[] = $v['action']->getHtml(); }
        return $f;
    }

    function getParams() {
        foreach ( $this->actions as $v ) { $f[] = $v['action']->getParams(); }
        return $f;
    }

    function getGlobalParams() {
        return $this->globalParams;
    }
}

/** Represents an action, which transform a field from an input ItemContent to
 *  one field of an output ItemContent.
 *	Each action has two additional parameters:
 *      $html   - if set => store as HTML
 *  	$params - values or array of values specific to each action
 */
class Action {
    var $action;
    var $html;
    var $params;

    function Action($action, $html, $params) {
        $this->action = $action;
        $this->html = ($html ? FLAG_HTML : 0);
        $this->params = $params;
    }

    function getAction()        { return $this->action; }
    function getHtml()          { return $this->html;   }
    function getParams()        { return $this->params; }
    function setAction($action) { $this->action = $action; }

    /* transforms a value from $itemContent[$from] to $fvalues */
    function transform(&$itemContent, $from, &$globalParams, &$fvalues ) {
        switch ( $this->action) {
            case "store": {
                $fvalues[]['value'] = $itemContent->GetValue($from);		// todo - pokud neexistuje pole s $from , co delat?
                break;
            }
            case "pack_id": {
                $fvalues[]['value'] = pack_id($itemContent->GetValue($from));		// todo - pokud neexistuje pole s $from , co delat?
                break;
            }
            case "removestring": {
                $v =  $itemContent->GetValue($from);
                $fvalues[]['value'] = $this->params ? ereg_replace($this->params, "", $v) :$v;
                break;
            }
            case "formatdate": {
                $v = strtotime($itemContent->GetValue($from));
                if ($v == -1)
                    return "Invalid date: ".$itemContent->GetValue($from);
                $fvalues[]['value'] = $v;
                break;
            }
            case "convertvalue": {
                // ???
                $fvalues[]['value'] = $globalParams["table"][$itemContent->GetValue($from)]["return"];
                break;
            }
            case "web": {
                $value = $itemContent->GetValue($from);
                if ( $value ) {
                    if (strtolower(substr($value, 0, 4)) != "http")
                    $value = "http://". $value;
                }
                $fvalues[]['value'] = $value;
                break;
                }
            case "storemultiasone" : {
                // $param is delimiter
                if (!is_array($from)) $from = array($from);
                $save = "";
                foreach ( $from as $tostore ) {
                    $savenext = trim($itemContent->GetValue($tostore));
                    if ($savenext != "") {
                        if ($save != "") {
                            $save = $save . $this->params;
                        }
                        $save = $save . $savenext;
                    }
                }
                $fvalues[]['value'] = $save;
                break;
            }
            case "storeasmulti": {
                if (!is_array($from)) $from = array($from);
                foreach ( $from as $tostore ) {
                    $save = trim($itemContent->GetValue($tostore));
                        $fvalues[]['value'] = $save;
                }
                break;
            }
            case "storeparsemulti": {
                $items = explode ($this->params,trim($itemContent->GetValue($from)));
                foreach ( $items as $save ) {
                    $fvalues[]['value'] = $save;
                }
                break;
            }
            case "string2id": {
                // from the same string we create allways the same id. This is
                // true in case user provide param (which is strongly
                // recommended or within the same slice (because used slice_id)
                $string = $itemContent->GetValue($from) . get_if($this->params, $GLOBALS['slice_id']);
                $fvalues[]['value'] = string2id($string);
                break;
            }
            case "not map": {
                $fvalues[0]['value'] = "";
                break;
            }
            case "value" : {
                $fvalues[0]['value'] = $this->params;
                break;
            }
            case "nszmciselnik": {
                $from = array('podoblast1','podoblast2','podoblast3');
                foreach ( $from as $tostore ) {
                    $save = $GLOBALS['nszmciselnik'][(string)trim($itemContent->GetValue($tostore))];
                    if ( $save ) {
                        $fvalues[]['value'] = $save;
                    }
                }
                break;
            }
            case "nszmmesta": {
                $save = $GLOBALS['nszmmesta'][(string)trim($itemContent->GetValue($from))];
                if ( $save ) {
                    $fvalues[]['value'] = $save;
                }
                break;
            }
            case "nszmstav": {
                $fvalues[]['value'] = get_if($itemContent->GetValue($from), 1);
                break;
            }
            default: {
                  return "Unknown function: $this->action";
            }
        }
        // set HTML flag and add slashes because of SQL syntax
        if (isset($fvalues) AND is_array($fvalues)) {
            foreach ( $fvalues as $k => $v ) {
                if ($this->html) {
                    $v['flag'] = $this->html;
                }
                $fvalues[$k] = $v;
            }
        }
        return "";
    }
}

$nszmciselnik = array (
'1'=>'1.',
'11'=>'1.1',
'12'=>'1.2',
'13'=>'1.3',
'14'=>'1.4',
'15'=>'1.5',
'16'=>'1.6',
'17'=>'1.7',
'18'=>'1.8',
'2'=>'2.',
'21'=>'2.1',
'22'=>'2.2',
'23'=>'2.3',
'24'=>'2.4',
'25'=>'2.5',
'26'=>'2.6',
'27'=>'2.7',
'28'=>'2.8',
'3'=>'3.',
'31'=>'3.1',
'32'=>'3.2',
'33'=>'3.3',
'34'=>'3.4',
'35'=>'3.5',
'36'=>'3.6',
'37'=>'3.7',
'38'=>'3.8',
'39'=>'3.9',
'4'=>'4.',
'41'=>'4.1',
'42'=>'4.2',
'43'=>'4.3',
'44'=>'4.4',
'45'=>'4.5',
'46'=>'4.6',
'5'=>'5.',
'51'=>'5.1',
'52'=>'5.2',
'53'=>'5.3',
'54'=>'5.4',
'55'=>'5.5',
'56'=>'5.6',
'6'=>'6.',
'61'=>'6.1',
'62'=>'6.2',
'63'=>'6.3',
'64'=>'6.4',
'65'=>'6.5',
'66'=>'6.6',
'67'=>'6.7',
'68'=>'6.8',
'69'=>'6.9',
'7'=>'7.',
'71'=>'7.1',
'72'=>'7.2',
'73'=>'7.3',
'74'=>'7.4',
'75'=>'7.5',
'76'=>'7.6',
'8'=>'8.',
'81'=>'8.1',
'82'=>'8.2',
'83'=>'8.3',
'84'=>'8.4',
'85'=>'8.5',
'86'=>'8.6',
'87'=>'8.7',
'9'=>'9.',
'91'=>'9.1',
'92'=>'9.2',
'93'=>'9.3',
'94'=>'9.4',
'95'=>'9.5',
'96'=>'9.6',
'97'=>'9.7',
'98'=>'9.8'
);

$nszmmesta = array(
    '���r nad S�zavou' => '4cbe4c9f495659db69aba35fbbe85e75',
    'Velk� Mezi����' => '6bfe94a7be75cba6bf4d382d2cfa56c3',
    'T�eb��' => '14f758c5c201e56b0ea1ad4ec0419b6e',
    'Tel�' => 'c1843ecad65dff9c85bb208429e87bb8',
    'Sv�tl� nad S�zavou' => '5129aa53e4cf252b2a12b82090c5f839',
    'Pelh�imov' => '0e7bdd6bfad9a1218c22f54b5f48168c',
    'Pacov' => '50e8c206869659e6fda872aaff285b8c',
    'Nov� M�sto na Morav�' => '7971bd8fd5a3235c149b2fe3bafd675e',
    'N�m욝 nad Oslavou' => '7f1ae6154b41558936845d2103578696',
    'Moravsk� Bud�jovice' => '8a5ba7e453725107797b6e1e80c8ab8e',
    'Jihlava' => '2acc8da86106cee2f93692f309f46b92',
    'Humpolec' => '05dc3562487ab1299409aa28edf58d49',
    'Havl��k�v Brod' => 'a116f13755011872fbd7b2829466a340',
    'Chot�bo�' => '09599b24d01f70502928a2b9e0779a05',
    'Byst�ice nad Pern�tejnem' => 'a05eb1555aa9b5f74e1f24786b5b6899');


/** Get List of actions */
function getActions() {
    $a = array("store","removestring","formatdate", "web", "storeparsemulti","value","string2id", 'nszmstav', 'nszmciselnik', 'nszmmesta', "default");
    // not used:  "convertvalue","storemultiasone", "storeasmulti", "not map"
    foreach ( $a as $v ) { $actions[$v] = $v; }
    return $actions;
}

function GetUploadFileName($ident) {
    return $ident . "_" . md5(uniqid(rand(),1))  . "_" . date("mdY");
}

?>