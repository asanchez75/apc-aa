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
 
# ----------------------- functions for default item values -------------------
function default_fnc_now($param) {
  return now();
}  

function default_fnc_uid($param) {
  global $auth;                                  #  9999999999 for anonymous
  return quote(isset($auth) ? $auth->auth["uid"] : "9999999999");
}  

function default_fnc_dte($param) {
  return mktime(0,0,0,date("m"),date("d")+$param,date("Y"));
}

function default_fnc_qte($param) {
  return quote($param);
}

function default_fnc_txt($param) {
  return quote($param);
}

function default_fnc_($param) {
  global $err;
  $err[default_fnc] = "No default function defined for parameter '$param'- default_fnc_()";
  return "";
}

# ----------------------- insert functions ------------------------------------

function insert_fnc_qte($item_id, $field, $value, $param) {
  global $varset, $itemvarset, $db;

  #huh( "insert_fnc_qte($item_id, $field, $value, $param)"); 
  #p_arr_m($field);

  if( $field[in_item_tbl] ) {
    if( ($field[in_item_tbl] == 'expiry_date') && 
        (date("Hi",$value[value]) == "0000") )
      $value[value] += 86399;  # if time is not specified, take end of day 23:59  
    # field in item table
    $itemvarset->add( $field[in_item_tbl], "quoted", $value[value]);
    return;
  }  

    # field in content table
  $varset->clear();
  if( $field[text_stored] )
    $varset->add("text", "quoted", $value[value]);
   else 
    $varset->add("number", "quoted", $value[value]);
  $varset->add("flag", "quoted", $value[flag]);

    # insert item but new field
  $varset->add("item_id", "unpacked", $item_id);
  $varset->add("field_id", "quoted", $field[id]);
  $SQL =  "INSERT INTO content" . $varset->makeINSERT();
  $db->query( $SQL );
}

function insert_fnc_dte($item_id, $field, $value, $param) {
  insert_fnc_qte($item_id, $field, $value, $param);
}

function insert_fnc_cns($item_id, $field, $value, $param) {
  insert_fnc_qte($item_id, $field, $value, $param);
}

function insert_fnc_num($item_id, $field, $value, $param) {
  insert_fnc_qte($item_id, $field, $value, $param);
}

function insert_fnc_boo($item_id, $field, $value, $param) {
  $value[value] = ( $value[value] ? 1 : 0 );
  insert_fnc_qte($item_id, $field, $value, $param);
}

function insert_fnc_uid($item_id, $field, $value, $param) {
  global $auth;
  # if not $auth, it is from anonymous posting - 9999999999 is anonymous user
  $val = (isset($auth) ?  $auth->auth["uid"] : "9999999999");
  insert_fnc_qte($item_id, $field, array("value"=>$val) , $param);
}

function insert_fnc_now($item_id, $field, $value, $param) {
  insert_fnc_qte($item_id, $field, array("value"=>now()), $param);
}

  # File upload
function insert_fnc_fil($item_id, $field, $value, $param) {
  $varname = 'v'.unpack_id($field[id]);
  $filevarname = $varname."x";
    
  # look if the uploaded picture exists
  if(($GLOBALS[$filevarname] <> "none")&&($GLOBALS[$filevarname] <> "")) {
    $dest_file = $GLOBALS[$filevarname . "_name"];

    # images are copied to subdirectory of IMG_UPLOAD_PATH named as slice_id
    $dirname = IMG_UPLOAD_PATH. $GLOBALS["slice_id"];
    $dirurl  = IMG_UPLOAD_URL. $GLOBALS["slice_id"];

    if( !is_dir( $dirname ))
      if( !mkdir( $dirname, IMG_UPLOAD_DIR_MODE ) ){
        return L_CANT_CREATE_IMG_DIR;
      }    

    if( file_exists("$dirname/$dest_file") )
      $dest_file = new_id().substr(strrchr($dest_file, "." ), 0 );

    # copy the file from the temp directory to the upload directory, and test for success    
    if(!copy($GLOBALS[$filevarname],"$dirname/$dest_file")) {
      return L_CANT_UPLOAD;
    }  
    $value["value"] = "$dirurl/$dest_file";
  }
  # store link to uploaded file or specified file URL if nothing was uploaded
  insert_fnc_qte($item_id, $field, $value, "");
}    

function insert_fnc_nul($item_id, $field, $value, $param) {
}

# not defined insert func in field table (it is better to use insert_fnc_nul)
function insert_fnc_($item_id, $field, $value, $param) {
}

# ----------------------- show functions --------------------------------------

function show_fnc_chb($varname, $field, $value, $param, $html) {
  echo $field[input_before];
  FrmInputChBox($varname, $field[name], $value[0][value], false, "", 1,
                $field[required], $field[input_help], $field[input_morehlp] );
}

function show_fnc_freeze_chb($varname, $field, $value, $param, $html) {
  echo $field[input_before];
  FrmStaticText($field[name], $value[0][value] ? L_SET : L_UNSET );
}

function show_fnc_txt($varname, $field, $value, $param, $html){
  echo $field[input_before];
  $rows      = ($param ? $param : 4);
  $htmlstate = ( !$field[html_show] ? 0 : ( $html ? 1 : 2 ));
  FrmTextarea($varname, $field[name], $value[0][value], 
   $rows, 60, $field[required], $field[input_help], $field[input_morehlp], 
   false, $htmlstate );
}

function show_fnc_freeze_txt($varname, $field, $value, $param, $html) {
  echo $field[input_before];
  FrmStaticText($field[name], $value[0][value]);
}

function show_fnc_fld($varname, $field, $value, $param, $html) {
   echo $field[input_before];
   $maxlength = 255;
   $fieldsize = 60;
   if (!empty($param)) 
     list($maxlength, $fieldsize) = split('[ ,:]+', $param, 2);

   $htmlstate = ( !$field[html_show] ? 0 : ( $html ? 1 : 2 ));
   FrmInputText($varname, $field[name], $value[0][value], $maxlength,
                $fieldsize, $field[required], $field[input_help],
                $field[input_morehlp], $htmlstate );
}

function show_fnc_freeze_fld($varname, $field, $value, $param, $html) {
  echo $field[input_before];
  FrmStaticText($field[name], $value[0][value]);
}

function show_fnc_rio($varname, $field, $value, $param, $html) {
  global $db;
  $arr = GetConstants($param, $db); 
  echo $field[input_before];
  FrmInputRadio($varname, $field[name], $arr, $value[0][value],
                $field[required], $field[input_help], $field[input_morehlp] );
}

function show_fnc_freeze_rio($varname, $field, $value, $param, $html) {
  echo $field[input_before];
  FrmStaticText($field[name], $value[0][value]);
}

function show_fnc_mch($varname, $field, $value, $param, $html) {
  global $db;

  $arr = GetConstants($param, $db); 

  # fill selected array from value
  if( isset($value) AND is_array($value) ) {
    reset($value);   
    while( list( ,$x ) = each( $value )) {
      if( $x[value] )
        $selected[$x[value]] = true;
    }
  }  
  
  echo $field[input_before];
  FrmInputMultiChBox($varname."[]", $field[name], $arr, $selected, 
    $field[required], $field[input_help], $field[input_morehlp]);
}
  
function show_fnc_freeze_mch($varname, $field, $value, $param, $html) {
  echo $field[input_before];
  FrmStaticText($field[name], implode (", ", $value));
}

function show_fnc_mse($varname, $field, $value, $param, $html) {
  global $db;

  if (!empty($param)) 
    list($contgroup, $selectsize) = explode(':', $param);

  if( $selectsize < 1 )   # default size
    $selectsize = 5;
      
  $arr = GetConstants($contgroup, $db); 

  # fill selected array from value
  if( isset($value) AND is_array($value) ) {
    reset($value);   
    while( list( ,$x ) = each( $value )) {
      if( $x[value] )
        $selected[$x[value]] = true;
    }
  }  
  
  echo $field[input_before];
  FrmInputMultiSelect($varname."[]", $field[name], $arr, $selected, $selectsize, 
    $field[required], $field[input_help], $field[input_morehlp]);
}
  
function show_fnc_freeze_mse($varname, $field, $value, $param, $html) {
  echo $field[input_before];
  FrmStaticText($field[name], implode (", ", $value));
}

function show_fnc_sel($varname, $field, $value, $param, $html) {
  global $db;
  $arr = GetConstants($param, $db); 
  echo $field[input_before];
  FrmInputSelect($varname, $field[name], $arr, $value[0][value],
                 $field[required], $field[input_help], $field[input_morehlp] );
}

function show_fnc_freeze_sel($varname, $field, $value, $param, $html) {
  echo $field[input_before];
  FrmStaticText($field[name], $value[0][value]);
}

# $param is uploaded_file_type:field_name:help (like "image/*::Select image")
# if no $param specified, no file upload field is displayed
function show_fnc_fil($varname, $field, $value, $param, $html) {
  echo $field[input_before];
  FrmInputText($varname, $field[name], $value[0][value], 255,60, 
               $field[required], $field[input_help], $field[input_morehlp], 0);
  if( !$param )
    return;                       # no upload field displayed
  $arr = explode(":",$param);  

  FrmInputFile($varname."x", $arr[1], 60, $field[required], 
               $arr[0], $arr[2], false );
}

function show_fnc_freeze_fil($varname, $field, $value, $param, $html) {
  echo $field[input_before];
  FrmStaticText($field[name], $value[0][value]);
}

function show_fnc_dte($varname, $field, $value, $param, $html) {
  echo $field[input_before];
  if( strstr($param, "'"))
    $arr = explode("'",$param);  // old format
   else 
    $arr = explode(":",$param);  // new format
  $datectrl = new datectrl($varname, $arr[0], $arr[1], $arr[2], $arr[3]);
  $datectrl->setdate_int($value[0][value]);
  FrmStaticText($field[name], $datectrl->getselect(), $field[required], 
                $field[input_help], $field[input_morehlp], "0" );
}

function show_fnc_freeze_dte($varname, $field, $value, $param, $html) {
  echo $field[input_before];
  $datectrl->setdate_int($value[0][value]);
  FrmStaticText($field[name], $datectrl->get_datestring());
}

function show_fnc_nul($varname, $field, $value, $param, $html) {
}

function show_fnc_freeze_nul($varname, $field, $value, $param, $html) {
}

# -----------------------------------------------------------------------------

function IsEditable($fieldcontent, $field) {
  return (!($fieldcontent[0][flag] & FLAG_FREEZE) AND $field[input_show]);
}  
  
function GetContentFromForm( $fields, $prifields, $oldcontent4id="", $insert=true ) {
  if( !isset($prifields) OR !is_array($prifields) )
    return false;
  reset($prifields);
  while(list(,$pri_field_id) = each($prifields)) {
    $f = $fields[$pri_field_id];

      # to content4id array add just displayed fields (see ShowForm())
    if( !IsEditable($oldcontent4id[$pri_field_id], $f) AND !$insert )
	    continue;

    $varname = 'v'. unpack_id($pri_field_id); # "v" prefix - database field var
    $htmlvarname = $varname."html";

    if( isset($GLOBALS[$varname]) and is_array($GLOBALS[$varname]) ) {
        # fill the multivalues    
      reset($GLOBALS[$varname]);
      $i=0;
      while( list(,$v) = each($GLOBALS[$varname]) ) {
        $content4id[$pri_field_id][$i][value] = $v;    # add to content array
        $content4id[$pri_field_id][$i][flag] = ( $f[html_show] ? 
                             (($GLOBALS[$htmlvarname]=="h") ? FLAG_HTML : 0) :
                             (($f[html_default]>0) ? FLAG_HTML : 0));
        $i++;                     
      }  
    } else {
      $content4id[$pri_field_id][0][value] = $GLOBALS[$varname];
      $content4id[$pri_field_id][0][flag] = ( $f[html_show] ? 
                             (($GLOBALS[$htmlvarname]=="h") ? FLAG_HTML : 0) :
                             (($f[html_default]>0) ? FLAG_HTML : 0));
    }  
  }
  return $content4id;
}                                             
                                              
function StoreItem( $id, $slice_id, $content4id, $fields, $insert, 
                    $invalidatecache=true, $feed=true ) {
  global $db, $varset, $itemvarset;

  if( !( $id AND isset($fields) AND is_array($fields)
        AND isset($content4id) AND is_array($content4id)) )
    return false;

  if( !$insert ) {  # remove old content first (just in content table - item is updated)
    reset($content4id);
    $delim="";
    while(list($fid,) = each($content4id)) {
      if ( !$fields[$fid]['in_item_tbl']) {
        $in .= $delim."'$fid'";
        $delim = ",";
      }
    }
    if ( $in ) { # delete content just for displayed fields 
      $SQL = "DELETE FROM content WHERE item_id='". q_pack_id($id). "' 
                                    AND field_id IN ($in)";
      $db->query($SQL);
    }  
  }  

//print_r($content4id);

  reset($content4id);
  while(list($fid,$cont) = each($content4id)) {
    $f = $fields[$fid];
    $fnc = ParseFnc($f[input_insert_func]);   # input insert function
    if( $fnc ) {                  # function to call
      $fncname = 'insert_fnc_' . $fnc[fnc];
        # updates content table or fills $itemvarset 
      if( !( isset($cont) AND is_array($cont)))    
        continue;
      reset($cont);               # it must serve multiple values for one field
      while(list(,$v) = each($cont)) {
          # add to content table or to itemvarset
        $fncname($id, $f, $v, $fnc[param]); 
          # do not store multiple values if field is not marked as multiple
        if( !$f[multiple]!=1 ) 
          continue;
      }
    }
  }
 
    # update item table
  if( !$insert ) {
    $itemvarset->add("slice_id", "unpacked", $slice_id);
    $itemvarset->add("last_edit", "quoted", default_fnc_now(""));
    $itemvarset->add("edited_by", "quoted", default_fnc_uid(""));
    $SQL = "UPDATE item SET ". $itemvarset->makeUPDATE() . " WHERE id='". q_pack_id($id). "'";
  } else {
    $itemvarset->add("id", "unpacked", $id);
    $itemvarset->add("slice_id", "unpacked", $slice_id);
    $itemvarset->add("flags", "quoted", "");
    $itemvarset->add("display_count", "quoted", "0");
    $SQL = "INSERT INTO item " . $itemvarset->makeINSERT();
  }  

  $db->query($SQL);

  if( $invalidatecache ) {
    $cache = new PageCache($db,CACHE_TTL,CACHE_PURGE_FREQ); # database changed - 
    $cache->invalidateFor("slice_id=$slice_id");  # invalidate old cached values
  }  

  if( $feed )
    FeedItem($id, $fields);

  return true;
}

function GetDefault($f) {
  $fnc = ParseFnc($f[input_default]);    # all default should have fnc:param format
  if( $fnc ) {                     # call function
    $fncname = 'default_fnc_' . $fnc[fnc];
    return $fncname($fnc[param]);
  } else
    return false;
}    

function GetDefaultHTML($f) {
  return (($f[html_default]>0) ? FLAG_HTML : 0);
}  

function ShowForm($content4id, $fields, $prifields, $edit) {
  if( !isset($prifields) OR !is_array($prifields) )
    return MsgErr(L_NO_FIELDS);

	reset($prifields);
	while(list(,$pri_field_id) = each($prifields)) {
    $f = $fields[$pri_field_id];

      #get varname - name of field in inputform
    $varname = 'v'. unpack_id($pri_field_id); # "v" prefix - database field var
    $htmlvarname = $varname."html";

    if( !IsEditable($content4id[$pri_field_id], $f) ) # if fed as unchangeable 
      $show_fnc_prefix = 'show_fnc_freeze_';            # display it only
     else 
      $show_fnc_prefix = 'show_fnc_';

	  if( !$f[input_show] )        # if set to not show - don't show
	    continue;

	  $fnc = ParseFnc($f[input_show_func]);   # input show function
	  if( $fnc ) {                     # call function
	    $fncname = $show_fnc_prefix . $fnc[fnc];
	      # updates content table or fills $itemvarset 
      if( !$edit ) {
        if( $f[multiple] AND isset($GLOBALS[$varname])
                         AND is_array($GLOBALS[$varname]) ) {
              # get the multivalues    
          reset($GLOBALS[$varname]);
          $i=0;
          while( list(,$v) = each($GLOBALS[$varname]) )
            $arr[$i++][value] = $v;
        } else
          $arr[0][value] = $GLOBALS[$varname];
  	    $fncname($varname, $f, $arr, $fnc[param], $GLOBALS[$htmlvarname]==1);
      } else
   	    $fncname($varname, $f, $content4id[$pri_field_id], 
                 $fnc[param], $content4id[$pri_field_id][0][flag] & FLAG_HTML );
    }
	}
}	


/*
$Log$
Revision 1.14  2001/08/03 10:09:30  honzam
if no time in expiry date is specified, the end of day is stored

Revision 1.13  2001/07/09 09:28:44  honzam
New supported User defined alias functions in include/usr_aliasfnc.php3 file

Revision 1.12  2001/06/12 16:00:55  honzam
date inputs support time, now
new multivalue input possibility - <select multiple>

Revision 1.11  2001/06/03 16:00:49  honzam
multiple categories (multiple values at all) for item now works

Revision 1.10  2001/05/23 23:07:00  honzam
fixed bug of not updated list of item in Item manager after item edit

Revision 1.9  2001/04/10 02:00:32  keb
Added explanation for Text Field parameters.
Handle case of multiple parameter delimiters, e.g. " : " or ", ".

Revision 1.8  2001/04/09 21:00:36  honzam
added sife and maxlength parameters to field input type

Revision 1.7  2001/03/30 11:54:35  honzam
offline filling bug and others small bugs fixed

Revision 1.6  2001/03/20 16:10:37  honzam
Standardized content management for items - filler, itemedit, offline, feeding
Better feeding support

Revision 1.5  2001/03/06 00:15:14  honzam
Feeding support, color profiles, radiobutton bug fixed, ...

Revision 1.1  2001/01/22 17:32:48  honzam
pagecache, logs, bugfixes (see CHANGES from v1.5.2 to v1.5.3)

*/
?>
