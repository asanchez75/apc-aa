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

# se_filters2.php3 - assigns feeding filters to specified slice - writes it to database
# expected $slice_id for edit slice
#          $import_id for id of imported slice
#          $all (set to 1 if import from all categories is selected)
#          $C contains category into which all categories are imported (only when $all is 1)
#             if $C==0 then import to the same category as source item category
#          $F[] array of imported categories, plus string "-0" or "-1" in order to approved checked
#          $T[] array of categories into which we should import (corresponds to F[] array)
#             if $T[]==0 then import to the same category as source item category

require "../include/init_page.php3";
require $GLOBALS[AA_INC_PATH]."varset.php3";

if(!CheckPerms( $auth->auth["uid"], "slice", $slice_id, PS_FEEDING)) {
  MsgPage($sess->url(self_base())."index.php3", L_NO_PS_FEEDING);
  exit;
}  

function ParseIdA($param,$app) {
   ERegI("([0-9a-f]{1,32})-([01])", $param, $parse); 
   $app = $parse[2];
   return $parse[1];
}   

$p_import_id= q_pack_id($import_id);

$err["Init"] = "";          // error array (Init - just for initializing variable
$catVS = new Cvarset();

do {
  $db->query("DELETE FROM feeds WHERE to_id = '$p_slice_id' AND from_id = '$p_import_id'");

  if( $all ) {  // all_categories
    $id = ParseIdA($C, &$app);
    $catVS->clear();
    $catVS->add("to_id", "unpacked", $slice_id);
    $catVS->add("from_id", "unpacked", $import_id);
    $catVS->add("all_categories", "number", 1);
    $catVS->add("to_approved", "number", $app);
    $catVS->add("to_category_id", "unpacked", $id);   // zero means import to the same category
    $SQL = "INSERT INTO feeds" . $catVS->makeINSERT();
//huh($id);
//huh($SQL."<br>");
    $db->query($SQL);
    if ($db->affected_rows() == 0) 
      $err["DB"] .= "<div class=err>Can't add import from $val</div>";
    break;
  }  

//p_arr($F, "F");
//p_arr($T, "T");
  
  if( isset($F) AND is_array($F) ) {  // insert to categories
    reset($F);
    while( list($index,$val) = each($F) ) {
      $from_cat = ParseIdA($val, &$app);
      $to_cat = $T[$index];
      if( $to_cat == "0" )
        $to_cat = $from_cat;
      $catVS->clear();
      $catVS->add("to_id", "unpacked", $slice_id);
      $catVS->add("from_id", "unpacked", $import_id);
      $catVS->add("all_categories", "number", 0);
      $catVS->add("to_approved", "number", $app);
      $catVS->add("category_id", "unpacked", $from_cat);  
      $catVS->add("to_category_id", "unpacked", $to_cat);
      $SQL = "INSERT INTO feeds" . $catVS->makeINSERT();
//huh($SQL."<br>");
      $db->query($SQL);
      if ($db->affected_rows() == 0)
      { $err["DB"] .= "<div class=err>Can't add import from $val</div>";
        break;
      }
    }
  }        
} while(false);
if( count($err) <= 1 )
  go_url( $sess->url(self_base() . "se_filters.php3") ."&import_id=$import_id&Msg=" . rawurlencode(L_IMPORT_OK));
else
  MsgPage($sess->url(self_base()."se_import.php3"), $err);

page_close();
/*
$Log$
Revision 1.1  2000/06/21 18:40:01  madebeer
Initial revision

Revision 1.1.1.1  2000/06/12 21:49:50  madebeer
Initial upload.  Code works, tricky to install. Copyright, GPL notice there.

Revision 1.10  2000/06/12 21:40:57  madebeer
added $Id $Log and $Copyright to some stray files

*/
?> 