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

/* This script allows to fill the AA database from files in a simple format.
	The field values are separated by a chosen string. 
	You give the ID of the slice concerned. All values are trimmed from whitespace.
	There are several import functions available - see importer.inc.php3.
*/
	
require_once "../../include/config.php3";
require_once $GLOBALS["AA_INC_PATH"]."locsess.php3";
require_once $GLOBALS["AA_INC_PATH"]."util.php3";
require_once $GLOBALS["AA_INC_PATH"]."formutil.php3";
require_once $GLOBALS["AA_INC_PATH"]."varset.php3";
require_once $GLOBALS["AA_INC_PATH"]."itemfunc.php3";
require_once $GLOBALS["AA_INC_PATH"]."notify.php3";
require_once $GLOBALS["AA_INC_PATH"]."pagecache.php3";
require_once $GLOBALS["AA_INC_PATH"]."date.php3";
require_once $GLOBALS["AA_INC_PATH"]."feeding.php3";

function processDataArray($data, $actions) 
{
  global $err;
  reset($actions);
  while (list($fid, $arr) = each($actions)) {
    switch ($arr["action"]) {	
  	  case "store":
        if( $data[$arr["from"]] )
          $retval[$fid][][value] = addslashes($data[$arr["from"]]);
        elseif ($arr["default"])  {
             $deffnc = "default_fnc_" . ($arr["deffnc"] ? $arr["deffnc"] : "qte");
            if (!is_callable($deffnc)) 
                print("Can't call default function $deffnc");
             $retval[$fid][][value] = $deffnc($arr["default"]);
        }
   		break;
      case "random":
        $retval[$fid][][value] = default_fnc_rnd($arr["param"]);
        break;
  	  case "storetrans":
        if( $data[$arr["from"]] ) 
          $retval[$fid][][value] = addslashes($arr["trans"][$data[$arr["from"]]]);
   		break;
  	  case "bool":
	    if ($data[$arr["from"]]) {
    	    if ($data[$arr["from"]] == 1) 
              $retval[$fid][][value] = 1;
        } elseif  ($arr["default"]) {
            $retval[$fid][][value] = $arr["default"];
        }
   	    break;
  	  case "web":
        $value = $data[$arr["from"]];
        if( $value ) {
			if (strtolower(substr($value, 0, 4)) != "http")  
	          $value = "http://". $value;
    	    $retval[$fid][][value] = addslashes($value);
		}
   		break;
  	  case "storeboolarray":
        $pole = $arr["from"];
  	    reset($pole);
        while( list($fld,$tostore) = each($pole)) {
   		   if ($data[$fld] == 1) 
 	    	  $retval[$fid][][value] = addslashes($tostore);
   		}	
  	    break; 
  	  case "storemultiasone":
        $pole = $arr["from"];
		reset($pole);
		$save = "";
        	while( list(,$tostore) = each($pole)) {
          		$savenext = trim($data[$tostore]);
		  	if ($savenext != "") {
				if ($save != "") { $save = $save . $arr["delimiter"]; }
				$save = $save . $savenext;
			}
		}
 	    	$retval[$fid][][value] = addslashes($save);
  	    break; 
  	  case "storeasmulti":
        $pole = $arr["from"];
		reset($pole);
        while( list(,$tostore) = each($pole)) {
          $save = trim($data[$tostore]);
		  if ($save != "") 
 	    	  $retval[$fid][][value] = addslashes($save);
   		}	
  	    break; 
	  case "storeparsemulti":
		$items = split ($arr["delimiter"],trim($data[$arr["from"]]));
		reset ($items);
		while (list (,$save) = each($items)) {
		  if ($save != "")
 	    	  $retval[$fid][][value] = addslashes($save);
		}
		break;
  	  default:
	    $err[] = "Field "+$arr["from"]+" has a wrong function: "+$arr["action"];
  	}  			  
	if ($arr["flag"] != "") {
		if (is_array ($retval[$fid])) {
			for ($i=0; $i < count($retval[$fid]);++$i)
				$retval[$fid][$i]["flag"] = $arr["flag"];
		}
	}
  }
  return $retval;
}

function Importer_SendErrorPage($txt) {
  if( $GLOBALS["err_url"] )
    go_url($GLOBALS["err_url"]);
  HTMLPageBegin();
  echo "</head><body>";
  if( isset( $txt ) AND is_array( $txt ) )
    PrintArray($txt);    
   else 
    echo $txt;
  echo "</body></html>";
  exit;
}  

function Importer ($sliceID, $fileName, $separator, $actions, $postedBy, $fire=false, $timeLimit=120, $statusCode = 1, $publishDate = 0, $expiryDate = 0, $maxRowLength = 50000)
{
  global $debug;
  if ($debug) {
    huhl("slice_id=",$sliceID,"Filename=",$fileName,"Separator=",$separator);
    huhl("Actions=",$actions);
    huhl("postedBy=",$postedBy,"fire=",$fire,"timeLimit=",$timeLimit);
    huhl("statusCode=",$statusCode,"publishDate=",$publishDate);
    huhl("expiryDate=",$expiryDate,"maxRowLength=",$maxRowLength);
  }
  // set in seconds - allows the script to work so long
  set_time_limit($time_limit);

  if ($publishDate == 0) $publishDate = time();
  // expire in 200 years
  if ($expiryDate == 0) $expiryDate = time() + 200*365*24*60*60;
  $defaults["display_count..."][0][value] = 0;
  $defaults["status_code....."][0][value] = $statusCode;
  $defaults["flags..........."][0][value] = ITEM_FLAG_OFFLINE;
  $defaults["posted_by......."][0][value] = $postedBy;
  $defaults["edited_by......."][0][value] = $postedBy;
  $defaults["publish_date...."][0][value] = $publishDate;
  $defaults["expiry_date....."][0][value] = $expiryDate;
 
  $fd = fopen($fileName,"r");
  $buffer = fgets($fd, $maxRowLength);
  $buffer = ereg_replace ("[\n\r]*","",$buffer);
  $sourceFields = split($separator, $buffer);
  if ($debug) { huhl("Fields=",$sourceFields); }
  $err = "";
  
  global $db, $err, $varset, $itemvarset, $error, $ok;
  $err["Init"] = "";          // error array (Init - just for initializing variable
  $varset = new Cvarset();
  $itemvarset = new Cvarset();
  $db = new DB_AA;
  list($fields,) = GetSliceFields($sliceID);
  if ($debug) { echo "Starting to read<br>"; }
  while (!feof ($fd)) {
    $buffer = fgets($fd, $maxRowLength);
    if ($debug) { echo $buffer . "<br>"; }
    // Concatenate multi-line fields as output by for example Excel 
    $splitlinereg = '/(' . $separator . '["][^"' . $separator . ']*[\n\r]*)$/';
    while (preg_match ($splitlinereg,$buffer,$arr) and !feof($fd)) {
	$buffer = $buffer . fgets($fd, $maxRowLength);
    }
    $buffer = ereg_replace ("[\n\r]*$","",$buffer);   // Only change at end of line
    $arr = split($separator, $buffer);
    for( $i=0; $i< count($sourceFields); $i++) 
	// Strip quotes around fields
      $data[ $sourceFields[$i] ] = ereg_replace('^"(.*)"$',"\\1",$arr[$i]);

  	$content4id_part = processDataArray($data, $actions);
    if( !(isset($content4id_part) AND is_array($content4id_part)) )
      continue;
      
    $content4id = $content4id_part + $defaults;

    if ($fire) {
    	$added_to_db=StoreItem( new_id(), $sliceID, $content4id, $fields, true, true, false ); # insert, invalidatecache, feed
		echo "Added to db: $added_to_db<br>\n";
	}
    else {
    	print_r($content4id);
    	echo "<hr>";
    }
  	unset($data);
  }
  fclose ($fd);
  if (count($err) > 1) Importer_SendErrorPage ($err);
  print("<br><b>Import successful!</b>");
}

/*
OneBigFile and DataDirectory are two proprietary functions used once in Econnect to import
file/files in a special format - not maintained now 


function OneBigFile($fieldsfile) {
global $fieldsfile, $val1, $data, $slice_id, $fire;
global $OddelovacStart,$PrvniPolozka,$OddelovacStop;

  $fd = fopen($fieldsfile,"r");
  $prvni=$OddelovacStart.$PrvniPolozka.$OddelovacStop;
  $radek = chop(get_line($fd));
  $i=0;
  do {
    do {
  	  if ((substr($radek,0,2)==$OddelovacStart) && (substr($radek,-3)==$OddelovacStop)) {
     		$sectname = ereg_replace("^(".$OddelovacStart.")", "", $radek);
    		$sectname = ereg_replace("(".$OddelovacStop.")$","", $sectname);
  	  } elseif ($radek != "")
        $val[$sectname] .= $radek; 
  
  	  $radek=chop(get_line($fd));
  	} while (($radek != $prvni)&&(!feof($fd)));

  	$content4id_part = processDataArray($val);
    $content4id = $content4id_part + $val1;    // add defaults

  	print("Story with headline <b>".$content4id["text...........1"][0][value]."</b> added!<br>\n");

    if ($fire)
    	$added_to_db=StoreItem( new_id(), $slice_id, $content4id, $data, true, true, false ); # insert, invalidatecache, feed
     else {
    	print_r($content4id);
    	echo "<hr>";
    }
      
  	$i=0;
  	unset($val);
  } while (!feof($fd));
  fclose($fd);
}
function DataDirectory($fieldsfile) {
global $fieldsfile, $val1, $data, $slice_id;

  $files=ScanDirforDataFiles($fieldsfile); // search the files in directory
  for ($j=0; $j<count($files); $j++) {
    $fieldsarray = file ($fieldsfile ."/". $files[$j]);
    $myfile=processDataFile($fieldsarray);  // processing files
	
  	$content4id=$myfile + $val1;  // $content4id contains values from file and constants
    if ($fire) {
    	$added_to_db=StoreItem( new_id(), $slice_id, $content4id, $data, true, true, false ); # insert, invalidatecache, feed
    }	
  	print("Story with headline <b>".$content4id["headline........"][0][value]."</b> added!<br>\n");
  }
}

// erases commas and space between brackets
function stripCommas($string) {
  $string = ereg_replace("\], \[","][",$string);
  return $string;
}

// search for subdirs in specified directory
function ScanDirforSubdirs($directory) {
  $handle = opendir($directory);
  
  while ($file = readdir($handle)) {
    if (($file != ".") && ($file != "..")) {
      $datedirs[]=$file;
    }
  }
  closedir($handle);
  
  if (count($datedirs) > 0) sort($datedirs);
  
  return $datedirs;
}

// search for all files in specified directory
function ScanDirforDataFiles($directory) {
  $handle = opendir($directory);
  
  while ($file = readdir($handle)) {
    if (($file != ".") && ($file != "..")) {
      $monfiles[]=$file;
    }
  }
  closedir($handle);
  
  if (count($monfiles) > 0) sort($monfiles);
  
  return $monfiles;    
}

// czech names of months
$ceske_mesice = array( "ledna","�nora","b�ezna","dubna","kv�tna","�ervna","�ervence","srpna","z���","��jna","listopadu","prosince");

// convertor from czech long date to unix timestamp
function CZDateToTimestamp($string) {
global $ceske_mesice;

  list($den, $czmesic, $rok, $cas) = split(" ", $string);
  $mesic=0;
  do {
    $mesic++;
  } while ($czmesic!=$ceske_mesice[$mesic-1]);
  $den = ereg_replace("\.", "", $den);
  list($hodiny, $minuty) = split (":", $cas);
  $timestamp = mktime($hodiny, $minuty, 0, $mesic, $den, $rok);
  return $timestamp;
}

// czech names of months
$ceske_mesice = array( "ledna","�nora","b�ezna","dubna","kv�tna","�ervna","�ervence","srpna","z���","��jna","listopadu","prosince");

// convertor from czech long date to unix timestamp
function CZDateToTimestamp($string) {
global $ceske_mesice;

  list($den, $czmesic, $rok, $cas) = split(" ", $string);
  $mesic=0;
  do {
    $mesic++;
  } while ($czmesic!=$ceske_mesice[$mesic-1]);
  $den = ereg_replace("\.", "", $den);
  list($hodiny, $minuty) = split (":", $cas);
  $timestamp = mktime($hodiny, $minuty, 0, $mesic, $den, $rok);
  return $timestamp;
}


// parse function
function processDataFile($data) {
  global $OddelovacStart, $OddelovacStop, $KonecTextu, $field_ids;
  global $val, $val1;

  $i = 0;
  $polozka=""; $text="";
  do {
	   $fieldsline=trim($data[$i]); // erase whitespaces
	   
	   if ((substr($fieldsline,0,2)==$OddelovacStart) && (substr($fieldsline,-3)==$OddelovacStop)){	
	   // if true, then we have in $fieldsline section name
	     if (($polozka!="")&&($text!="")) {

		   if ($polozka == "publish_date") {
		     $text = trim($text);
		     $text =  CZDateToTimestamp($text); // convert to timestamp
			 $val["post_date......."][0][value]=$text;
			 $val["publish_date...."][0][value]=$text;
			 $val["last_edit......."][0][value]=$text;
		   } else {
             $val[$polozka][0][value]=addslashes($text); // put value to array
	     	 $val[$polozka][0][flag]=$vlajka;
		   }	 
		   $polozka=""; $text="";
		 }  
	     $fieldsline = ereg_replace("^(".$OddelovacStart.")", "", $fieldsline);
		 $fieldsline = ereg_replace("(".$OddelovacStop.")$","", $fieldsline);
		 switch ($fieldsline) {
		   case "Datumzverejneni" :
		     $polozka="publish_date";
			 break;
		   default :	 
     	     $polozka = $field_ids[$fieldsline][name];
			 $vlajka  = $field_ids[$fieldsline][flag];
		 }	 
	   } else {
	     $text = $text . $data[$i];
	   } 	   
	   $i++;
	 } while ($i != count($data));
  return $val;	 
}

// this function gets the realy all line from file
// $file is file reference to file (it must be opened with fopen function)
function get_line ($file) { 
  $read_again = true; 
  $buffer_size = 4096; 
  $full = ""; 
  while ((!feof ($file)) && ($read_again)) { 
    $read_again = false; 
    $line = fgets ($file, $buffer_size); 
    $full .= $line; 
    if (strlen($line) >= ($buffer_size - 1)) { 
      if ($line[($buffer_size - 2)] != '\n') { 
        $read_again = true; 
      } 
    } 
  } 
  return $full; 
}

*/

?>