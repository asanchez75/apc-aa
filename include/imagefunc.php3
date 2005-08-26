<?php
/**
 * Image manipulation functions - all functions require_once GD Library
 *
 * Should be included to other scripts
 *
 * @version $Id$
 * @author Stanislav K�hn <kuhn@changenet.sk>
 * @copyright Copyright (C) 1999, 2000 Association for Progressive Communications
*/
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

//###################################
// all functions require_once GD Library //
//###################################

/**
 *  Check if GD lib is installed
 */
function GDinstalled() {
    if (!extension_loaded('gd')) {
        //      if (!dl('gd.so'))
        huhe("GD is not installed, cannot perform image manipulation");
        return false;
    }
    return true;
}

/**
 *  Copies image if source and destination is not the same
 *  @return false on success or text string with error message
 */
function CopyImage2Destination($simage, $dimage) {
    if ($simage == $dimage) {
        return false; // no need to copy
    }
    return copy($simage,$dimage) ? false : _m("Cannot copy %1 to %2", array($simage, $dimage) );
}


/**
 *  Resamples image
 *  @return false on success or text string with error message
 */
function ResampleImage($simage,$dimage,$new_w,$new_h,$exact) {
    global $debugupload, $imageTable;

    if ($debugupload) huhl("Resample $simage at $new_w,$new_h to $dimage");
    //determine type, width, height of image.
    $imginfo   = GetImageSize($simage);
    $imagetype = $imageTable[$imginfo[2]][e];

    if ($debugupload) huhl("Type=$imagetype Size now=",$imginfo);
    // if dimensions of new picture are not set, then do not resize
    if (!$new_w && !$new_h) return CopyImage2Destination($simage, $dimage);

    //set ratio of width and height
    if ($new_w!=0) $x=$imginfo[0]/$new_w;
    if ($new_h!=0) $y=$imginfo[1]/$new_h;

    //if image is smaller then limits, do not resample
    if ($x < 1 && $y < 1) return CopyImage2Destination($simage, $dimage);

    //calculate dimensions to which will image be resampled
    //calculate offset for croping image if necessary when $exact is set

    if ($exact == 1) {
    // calculate dimensions
        if ($x<$y) {
            //use max width and calculate height
            $new_height=$imginfo[1]/$imginfo[0]*$new_w;
            $new_width = $new_w;
            $ratio = $x;
        } else {
            //use max height and calculate width
            $new_width=$imginfo[0]/$imginfo[1]*$new_h;
            $new_height = $new_h;
            $ratio = $y;
        }
        // calculate offsets
        if ($new_width > $new_w) {
            $offset_w = $ratio*($new_width-$new_w)/2;
            $offset_h = 0;
        } elseif ($new_height > $new_h) {
            $offset_w = 0;
            $offset_h = $ratio*($new_height-$new_h)/2;
        } else { 
            $offset_w = $ratio*($new_width-$new_w)/2;
            $offset_h = $ratio*($new_height-$new_h)/2;
        }        
    } else {
    //do not resize to exact dimensions, maintain aspect ratio
        if ($x>$y) {
            //use max width and calculate height
            $new_height=$imginfo[1]/$imginfo[0]*$new_w;
            $new_width = $new_w;
            $new_h=$new_height;
        } else {
            //use max height and calculate width
            $new_width=$imginfo[0]/$imginfo[1]*$new_h;
            $new_height = $new_h;
            $new_w=$new_width;
        }
        $offset_w = 0;
        $offset_h = 0;
    }

    if ($debugupload) huhl("Will resample to $new_width:$new_height");
    if (GetSupportedTypes($imginfo[2])) {
        if ($imginfo[2]<4 && $imginfo!=NULL) {
            // in GD2 ImageCreate goes monochrome
            if (  $imageTable[$imginfo[2]][t])
                // test for is_callable("ImageCreateTrueColor") doesn't work
                // GD < 2 have this function, but not implemented
                $dst_img=@ImageCreateTrueColor($new_w,$new_h);
            if (!$dst_img) {
                $dst_img=ImageCreate($new_w,$new_h);
            }
            $f="ImageCreateFrom".$imagetype;
            $src_img=$f($simage);
            if (!$src_img) return _m("ResampleImage unable to %1",array($f));
            if ($debugupload)  huhl("imagecopyresized(...ofset_w=$offset_w,offset_h=$offset_h,width=$new_width,height=$new_height,type=$imginfo[0],$imginfo[1]");
            if (function_exists('imagecopyresampled')) {
                // better quality resizing - works with GD 2.0.1
                imagecopyresampled($dst_img,$src_img,0,0,$offset_w,$offset_h,$new_width,$new_height,$imginfo[0],$imginfo[1]);
            } else {
                imagecopyresized($dst_img,$src_img,0,0,$offset_w,$offset_h,$new_width,$new_height,$imginfo[0],$imginfo[1]);
            }
            $f="Image".$imagetype;
            $f($dst_img,$dimage);
            if ($debugupload) huhl("Resampled it");
        }
        return false;
    } else {
        $err = _m("Type not supported for resize");
        if ($debugupload) huhl($err);
        return $err;
    }
}

// An array cross referencing different ways to refer to images
// Other "x" types could be added from
//http://www.php.cz/manual/en/function.exif-imagetype.php
//but note none of these are supported by GD
// m = mime type, e = extension for GD functions and files,  u = human readable
// b = bitmask x = exif_imagetype or imageinfo[2]
// t = true if should use truecolor
$imageTable = array(
    1 => array("m" => "image/gif",  "e" => "gif",  "u" => "GIF",  "b" => IMG_GIF, " x" => IMAGETYPE_GIF,  "t" => false),
    2 => array("m" => "image/jpeg", "e" => "jpeg", "u" => "JPEG", "b" => IMG_JPG,  "x" => IMAGETYPE_JPEG, "t" => true),
    3 => array("m" => "image/png",  "e" => "png",  "u" => "PNG",  "b" => IMG_PNG,  "x" => IMAGETYPE_PNG,  "t" => false),
    6 => array("m" => "image/wbmp", "e" => "bmp",  "u" => "WBMP", "b" => IMG_WBMP, "x" => IMAGETYPE_BMP,  "t" => false)
);

// function checks type of images supported by GD library
// if type is defined return true ro false,
// if type is not defined return array of supported types
//#####################################################

function PrintSupportedTypes() {
	global $imageTable;
	$it = ImageTypes();
	foreach ( $imageTable as $k => $v) {
		print($v["u"].(($it & $v["b"]) ? " " : " Not ")."Supported \n");
	}
}

function GetSupportedTypes($type) { //type 1-gif, 2-jpeg, 3-png;
    global $imageTable, $debugupload;
    if (!GDInstalled()) {
        if ($debugupload) huhl("GD not installed");
        return false;
    }
    if ($debugupload) huhl("ImageTypes = ",ImageTypes());
    if (ImageTypes() & $imageTable[$type]["b"]) return true;
    // Note won't see this warning when run in itemedit cos redirects
    huhe("Warning: GD cant manipulate ".imgU($type)." images");
    return false;
}

function imgU($type) {
    global $imageTable;
    return ($imageTable[$type]["u"] ? $imageTable[$type]["u"] : ("Type ".$type));
}
?>
