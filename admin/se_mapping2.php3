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

# se_mapping2.php3 - writes feed mapping to feedmap table
# expected $slice_id for edit slice
#          $from_slice_id for id of imported slice
#          $fmap - array of fields mapping
#          $fval - array of field value

require "../include/init_page.php3";
require $GLOBALS[AA_INC_PATH]."varset.php3";

if(!CheckPerms( $auth->auth["uid"], "slice", $slice_id, PS_FEEDING)) {
  MsgPage($sess->url(self_base())."index.php3", L_NO_PS_FEEDING);
  exit;
}  

$p_from_slice_id= q_pack_id($from_slice_id);

$err["Init"] = "";       // error array (Init - just for initializing variable)

// First we DELETE current fields mapping and then INSERT new.
$db->query("DELETE FROM feedmap WHERE from_slice_id = '$p_from_slice_id' AND to_slice_id = '$p_slice_id' ");

// insert into feedmap
$catVS = new Cvarset();
while (list($to_field_id,$val) = each($fmap)) {
  if ($to_field_id == $val)     // do not insert into db if to_id is the same as from_id
    continue;

    $catVS->clear();
    $catVS->add("from_slice_id", "unpacked", $from_slice_id);
    $catVS->add("to_slice_id", "unpacked", $slice_id);
  $catVS->add("to_field_id", "quoted",$to_field_id);
  switch ($val) {
    case L_MAP_NOTMAP : $flag = FEEDMAP_FLAG_EMPTY; break;
    case L_MAP_VALUE:  $flag = FEEDMAP_FLAG_VALUE ; $catVS->add("value", "quoted", $fval[$to_field_id]); break;
    default:            $flag = FEEDMAP_FLAG_MAP ;  $catVS->add("from_field_id", "quoted", $val );
  }
  $catVS->add("flag", "quoted",$flag);
    $SQL = "INSERT INTO feedmap" . $catVS->makeINSERT();
    if (!$db->query($SQL)) {  # not necessary - we have set the halt_on_error
      $err["DB"] .= MsgErr("Can't add fields mapping");
  }
 }

go_url( $sess->url(self_base() . "se_mapping.php3") . "&from_slice_id=".rawurlencode($from_slice_id) .
        "&Msg=" . rawurlencode(MsgOK(L_MAP_OK)));
page_close();
?>
