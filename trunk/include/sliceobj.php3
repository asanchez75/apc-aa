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

# A class for manipulating slices
#
# Author and Maintainer: Mitra mitra@mitra.biz
#
# And yes, I'll move the docs to phpDocumentor as soon as someone explains how
# to use it! 
#
# It is intended - and you are welcome - to extend this to bring into 
# one place the functions for working with slices.
#
# A design goal is to use lazy-evaluation wherever possible, i.e. to only 
# go to the database when something is needed.

require_once "../include/config.php3";
require_once $GLOBALS["AA_INC_PATH"]."locsess.php3";
require_once $GLOBALS["AA_INC_PATH"]."zids.php3"; // Pack and unpack ids

class slice {
    var $name;  # The name of the slice
    var $id;    # The Id of the slice

    function slice($init_id="",$init_name=null) {
        $this->id = $init_id; // unpacked id
        if (isset($init_name)) $this->name = $init_name;
    }

    // Load $this from the DB for any of $fields not already loaded
    function getfields($fields,$force=false) {
        global $db3;
        if (is_string($fields)) $fields = array ( $fields);
        if (!isset($db3)) $db3 = new DB_AA;
        reset($fields);
        if ($force)   // Ignore existing fields (not normally used)
            $fieldsreq = $fields;
        else
            foreach ($fields as $f) 
                if (!isset($this->$f))
                    $fieldsreq[] = $f;
        if (isset($fieldsreq)) {
            $SQL = "SELECT ".implode(",",$fieldsreq).
                " FROM slice WHERE id = '".q_pack_id($this->id) . "'";
            $db3->tquery($SQL);
            if (! $db3->next_record()) {
                if ($GLOBALS[errcheck]) 
                    huhl("Slice ".$this->id." is not a valid slice");
            }
            else {
                foreach ($fieldsreq as $f)
                    $this->$f = $db3->f($f);
            }  
	    }
    }

    function name() {
        $this->getfields("name");
        return $this->name;
    }
}

class slices {
    var $a;     # Array unpackedsliceid -> slice obj

    // Create slices array from unpacked slice ids
    function slices($iarr) {
        $this->a = array();
        reset $iarr;
        foreach($iarr as $unpackedsliceid) {
            $this->a[$unpackedsliceid] = new slice($unpackedsliceid);
        }
    }
}


// Utility functions to avoid mucking with classes where only used once
function sliceid2name($unpackedsliceid) {
    $s = new slice($unpackedsliceid);
    return $s->name();
}

// Function just here for debugging 
/*
function report_sliceids() {
    global $db3;
    $db3->tquery("SELECT name,id FROM slice");
    while($db3->next_record()) {
        print("\nName=".$db3->f("name")." unpacked ID=".unpack_id128($db3->f("id")));
    }
}
report_sliceids();
*/
/* A set of functions to exercise this object and test code */
/*
function test_sliceobj() {
    $v = new slice(unpack_id128("AA_Core_Fields.."));
    $n = $v->name();
    if ($debug) huhl("test_sliceobj:slice=",$v);
    if ($n != "Action Aplication Core")  {
        print("\n<br>Sliceobj test didn't work, either 'Action Application Core' slice is missing, or code broken");
        return false;
    } else return true;
}
test_sliceobj();    
*/
?>