<?php
//$Id: synchronize.php3 2290 2006-07-27 15:10:35Z honzam $
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


require_once dirname(__FILE__). "/include/init_central.php";
require_once AA_INC_PATH.     'formutil.php3';
require_once AA_INC_PATH.     'files.class.php3';
require_once AA_INC_PATH.     "msgpage.php3";

pageOpen();

if (!IsSuperadmin()) {
  MsgPageMenu($sess->url(self_base())."index.php3", _m("You don't have permissions to synchronize slices."), "admin");
  exit;
}

$aas = AA_Actionapps::getArray();

HtmlPageBegin();   // Print HTML start page tags (html begin, encoding, style sheet, but no title)
?>
<TITLE><?php echo _m("Central - Copy Slice (1/2) - Select Source ActionApps"); ?></TITLE>
</HEAD>
<BODY>
<?php
$useOnLoad = true;
require_once AA_INC_PATH."menu.php3";
showMenu($aamenus, "central", "copyslice");

echo "<H1><B>" . _m("Central - Copy Slice (1/2) - Select Source ActionApps") . "</B></H1>";
PrintArray($err);
echo $Msg;

$aas_array = array();
foreach ( $aas as $k => $aa ) {
    $aas_array[$k] = $aa->getName();
}

$form_buttons = array ("submit");
?>
<form name=f method=post action="<?php echo $sess->url(self_base() ."copyslice2.php") ?>">
<?php
FrmTabCaption('', '','', $form_buttons, $sess, $slice_id);
FrmInputSelect('template_aa', _m('Template ActionApps'), $aas_array, $_POST['template_aa'], true, _m('ActionApps installation used as template'));
FrmTabEnd($form_buttons, $sess, $slice_id);
?>
</FORM>
<?php
HtmlPageEnd();
page_close();
?>
