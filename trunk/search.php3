<html>
<head>
<title>Search Results</title>
</head>
<body>

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

require "./include/config.inc";

require ($GLOBALS[AA_INC_PATH] . "locsess.php3");
require ($GLOBALS[AA_INC_PATH] . "searchlib.php3");

// Variables that we expect from the input form

// echo "$search[keyword]<br>"; // search string
// echo "$s_col[0]<br>";        // in which columns in table items to search
// echo "$search[type]<br>";    // AND or OR

$where = SearchWhere($search, $s_col);

// echo ("<br>$where<br>");

$retflds[] = "headline";
$retflds[] = "abstract";
$retflds[] = "full_text";


$sql = aa_search_db ($where, $retflds);

//echo ("<font color=red>$sql</font><br>");

$db = new DB_AA;
// echo "$db->Host, $db->Database, $db->User, \"$db->Password\"<br>";
   
$c = $db->query($sql);
$a = $db->nf();
echo("Number of results: $a<br>");

echo("<table>");
$i = 0;
while ($db->next_record()) {
   while ( list($key, $val) = each($retflds) ) {
      printf("<tr><td><b>%s</b></td><td>%s</td></tr>", $val, $db->f($val));
      $rs[$i][$val] = $db->f($val);
   }
   reset($retflds);
   $i++;
}
echo("</table>");
   
/*
$Log$
Revision 1.2  2000/07/03 15:00:14  honzam
Five table admin interface. 'New slice expiry date bug' fixed.

Revision 1.5  2000/06/12 19:57:51  madebeer
added GPL LICENSE file, added copyright notice to all files that
added GPL LICENSE
Added copyright (APC) notice to all .inc and .php3 files that have an $Id

Revision 1.4  2000/03/29 15:57:03  honzama
*** empty log message ***

Revision 1.3  2000/03/22 09:36:17  madebeer
config.inc now allows ecn and igc to have different .css files
also added Id and Log keywords to all .php3 and .inc files
*.php3 makes use of new variables in config.inc

*/
?>

</body>