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

$New_slice = true;  // variable tells to init_page, there should not be defined slices, here
require "../include/init_page.php3";

if($cancel)
  go_url( $sess->url(self_base() . "index.php3"));

if(!CheckPerms( $auth->auth["uid"], "aa", AA_ID, PS_ADD)) {
  MsgPage($sess->url(self_base())."index.php3", L_NO_PS_ADD);
  exit;
}

$err["Init"] = "";          // error array (Init - just for initializing variable

HtmlPageBegin();   // Print HTML start page tags (html begin, encoding, style sheet, but no title)
?>
 <TITLE><?php echo L_A_SLICE_TIT;?></TITLE>
</HEAD>
<?php 
  echo "<H1><B>" . L_A_SLICE_ADD ."</B></H1>";
  PrintArray($err);
  echo $Msg;  
?>

<center>
<form enctype="multipart/form-data" method=post action="<?php echo $sess->url("slicedit.php3") ?>">
<table border="0" cellspacing="0" cellpadding="1" bgcolor="#584011" align="center">
<tr><td class=tabtit><b>&nbsp;<?php echo L_SELECT_APP?></b>
</td>
</tr>
<tr><td>
<table width="440" border="0" cellspacing="0" cellpadding="4" bgcolor="#EBDABE">
<?php
  echo "<tr><td class=tabtxt><b>". L_APP_TYPE ."</b>";
  echo "</td>\n <td><select name=\"slice_type\">";	
  reset($ActionAppConfig);
  while(list($k, $v) = each($ActionAppConfig)) { 
    echo "<option value=\"". htmlspecialchars($k)."\"";
    echo "> ". htmlspecialchars($v[name]) ." </option>";
  }
  echo "</select></td></tr>\n";
?>  
</table>
<tr><td align="center">
<?php 
  echo '<input type=submit name=Add_slice value="'. L_ADD .'">&nbsp;&nbsp;';
  echo '<input type=submit name=cancel value="'. L_CANCEL .'">';
/*
$Log$
Revision 1.1  2000/06/21 18:40:04  madebeer
Initial revision

Revision 1.1.1.1  2000/06/12 21:49:52  madebeer
Initial upload.  Code works, tricky to install. Copyright, GPL notice there.

Revision 1.7  2000/06/12 19:58:25  madebeer
Added copyright (APC) notice to all .inc and .php3 files that have an $Id

Revision 1.6  2000/04/24 16:45:03  honzama
New usermanagement interface.

Revision 1.5  2000/03/22 09:36:44  madebeer
also added Id and Log keywords to all .php3 and .inc files
*.php3 makes use of new variables in config.inc

*/
?>   
</td></tr></table>
</FORM>
</center>
<?php echo L_APP_TYPE_HELP ?>
</BODY>
</HTML>
<?php page_close()?>





