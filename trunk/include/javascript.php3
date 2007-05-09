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
// used by the itemedit.php3 page, which calls getTrig ()

require_once AA_INC_PATH."util.php3";

$GLOBALS["js_triggers"] = array (
    "input"    => array ("onBlur", "onClick", "onDblClick", "onFocus", "onChange", "onKeyDown", "onKeyPress", "onKeyUp", "onMouseDown", "onMouseMove", "onMouseOut", "onMouseOver", "onMouseUp", "onSelect"),
    "select"   => array ("onBlur", "onFocus", "onChange"),
    "textarea" => array ("onBlur", "onClick", "onDblClick", "onFocus", "onChange", "onKeyDown", "onKeyPress", "onKeyUp", "onMouseDown", "onMouseMove", "onMouseOut", "onMouseOver", "onMouseUp", "onSelect"),
    "form"     => array ("onClick", "onDblClick", "onKeyDown", "onKeyPress", "onKeyUp", "onMouseDown", "onMouseMove", "onMouseOut", "onMouseOver", "onMouseUp", "onReset", "onSubmit"));

$GLOBALS["js_trig"] = getTrig();
/** getJavascript function
 * @param $slice_id
 */
function getJavascript($slice_id) {
    $db = new DB_AA;
    if ($slice_id) {
        $p_slice_id = q_pack_id ($slice_id);
        $db->query("SELECT javascript FROM slice WHERE id='$p_slice_id'");
        if ($db->next_record()) {
            $javascript = $db->f("javascript");
        }
    }
    return $javascript;
}

/* $js_trig is an array with triggers used, e.g.
    $js_trig["onBlur"] = 1
    $js_trig["onClick"] = 1
    means: aa_onBlur, aa_onClick are defined */
/** getTrig function
 *
 */
function getTrig() {
    global $js_triggers;
    unset($js_trig);

    foreach ($js_triggers as $control => $ctrigs) {
        foreach ($ctrigs as $ctrig) {
            $js_trig[$ctrig] = 1;
        }
    }

    $javascript = getJavascript($GLOBALS["slice_id"]);

    $ws = "[ \t\n\r]*";
    foreach ($js_trig as $trg => $foo) {
        // match e.g. aa_onSubmit ( fieldid ) {
        $js_trig[$trg] = preg_match ("/aa_$trg$ws\($ws"."fieldid$ws\)$ws\{/", $javascript) ? 1 : 0;
    }
    return $js_trig;
}

// $unpacked_fieldid -- the function ignores the first character (usually 'v')
/** getTriggers function
 * @param $control
 * @param $upacked_fieldid
 * @param $add
 */
function getTriggers($control, $unpacked_fieldid, $add="") {
    global $js_triggers;
    global $js_trig;

    if (!is_array($js_trig)) {
        return;
    }
    if (substr ($unpacked_fieldid, -2) == "[]") {
        $unpacked_fieldid = substr ($unpacked_fieldid, 0, strlen($unpacked_fieldid) - 2);
    }
    if (substr ($unpacked_fieldid, -1) == "x") {
        $unpacked_fieldid = substr ($unpacked_fieldid, 0, strlen($unpacked_fieldid) - 1);
    }

    if (preg_match("/^v[0-9a-f]+$/", $unpacked_fieldid)) {
        $fieldid = pack_id(substr ($unpacked_fieldid,1));
    } elseif(preg_match("/^[0-9a-f]+$/", $unpacked_fieldid)) {
        $fieldid = pack_id($unpacked_fieldid);
    } else {
        $fieldid = $unpacked_fieldid;
    }

    foreach ($js_triggers[$control] as $ctrig) {
        $funcs = "";
        // Omar Martinez fix - 2005/04/28
        // onSubmit is special case - for all other events we can concaternate
        // the commands, but if you do so for onSubmit, you will get:
        //   return BeforeSubmit(); aa_onSubmit('inputform');
        // which is nonsence.
        if (($ctrig == "onSubmit") AND ($add[$ctrig] == "return BeforeSubmit()")) {
            if ($js_trig[$ctrig]){
                $retval .= " $ctrig=\"if( BeforeSubmit() ){ return aa_$ctrig('$fieldid'); } else { return false; }\"";
            } else {
                $retval .= " $ctrig=\"$add[$ctrig]\"";
            }
        } else {
            if ($add[$ctrig]) {
                $funcs = $add[$ctrig].";";
            }
            if ($js_trig[$ctrig]) {
                $funcs .= "aa_$ctrig('$fieldid');";
            }
            if ($funcs) {
                $retval .= " $ctrig=\"$funcs\"";
            }
        }
    }
    return $retval;
}
?>