<?php
/**  se_mapping2.php3 - writes feed mapping to feedmap table
 *     expected $slice_id for edit slice
 *              $from_slice_id for id of imported slice
 *              $fmap - array of fields mapping
 *              $fval - array of field value
 *              $extslice
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
 *
*/


require_once "../include/init_page.php3";

require_once AA_INC_PATH."varset.php3";
require_once AA_INC_PATH."csn_util.php3";

if (!IfSlPerm(PS_FEEDING)) {
  MsgPage($sess->url(self_base())."index.php3", _m("You have not permissions to change feeding setting"));
  exit;
}

$p_from_slice_id = q_pack_id($from_slice_id);

$err["Init"] = "";       // error array (Init - just for initializing variable)

// save old values
list($map_to,$field_map) = GetExternalMapping($slice_id, $from_slice_id );

// First we DELETE current fields mapping and then INSERT new.
$db->query("DELETE FROM feedmap WHERE from_slice_id = '$p_from_slice_id' AND to_slice_id = '$p_slice_id' ");

// insert into feedmap
$catVS = new Cvarset();
while (list($to_field_id,$val) = each($fmap)) {

    $catVS->clear();
    $catVS->add("from_slice_id", "unpacked", $from_slice_id);
    $catVS->add("to_slice_id", "unpacked", $slice_id);
    $catVS->add("to_field_id", "text",$to_field_id);
    $catVS->add("from_field_name", "text", $map_to[$val]);

    switch ($val) {
        case _m("-- Not map --") :
            $flag = FEEDMAP_FLAG_EMPTY;
            break;
        case _m("-- Value --"):
            $flag = FEEDMAP_FLAG_VALUE ;
            $catVS->add("value", "quoted", $fval[$to_field_id]); break;
        case _m("-- Joined fields --"):
            $flag = FEEDMAP_FLAG_JOIN;
            $catVS->add("value", "quoted", $fval[$to_field_id]); break;
        case _m("-- RSS field or expr --"):
            $flag = FEEDMAP_FLAG_RSS;
            $catVS->add("value", "quoted", $fval[$to_field_id]);
            if (! $map_to[$val]) $catVS->add("from_field_name", "quoted", $fval[$to_field_id]);
            unset($map_to[$val]);
            break;
        case  FEEDMAP_FLAG_EXTMAP :
        case  FEEDMAP_FLAG_MAP :
            $flag = ($ext_slice) ? FEEDMAP_FLAG_EXTMAP : FEEDMAP_FLAG_MAP ;
            $catVS->add("from_field_id", "text", $val );
            unset($map_to[$val]);
            break;
    }
    $catVS->add("flag", "quoted",$flag);

    $SQL = "INSERT INTO feedmap" . $catVS->makeINSERT();
    if (!$db->query($SQL)) {  // not necessary - we have set the halt_on_error
        $err["DB"] .= MsgErr("Can't add fields mapping");
    }
}

// Write external fields, which did not mapped.
if ($map_to && is_array($map_to)) {
    while (list($from_field_id,) = each($map_to)) {
        $catVS->clear();
        $catVS->add("from_slice_id", "unpacked", $from_slice_id);
        $catVS->add("to_slice_id", "unpacked", $slice_id);
        $catVS->add("from_field_id",   "text", $from_field_id );
        $catVS->add("from_field_name", "text", $map_to[$from_field_id] );

        $catVS->add("flag", "quoted",FEEDMAP_FLAG_EXTMAP);
        $SQL = "INSERT INTO feedmap" . $catVS->makeINSERT();
        if (!$db->query($SQL)) {  // not necessary - we have set the halt_on_error
            $err["DB"] .= MsgErr("Can't add fields mapping");
        }
    }
}

go_url( $sess->url(self_base() . "se_mapping.php3") . "&from_slice_id=".rawurlencode($from_slice_id) .
        "&Msg=" . rawurlencode(MsgOK(_m("Fields' mapping update succesful"))));
page_close();
?>
