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

# se_import.php3 - feeding settings
# expected $slice_id for edit slice
# optionaly $Msg to show under <h1>Hedline</h1> (typicaly: Category update successful)

require_once "../include/init_page.php3";
require_once $GLOBALS["AA_INC_PATH"]."formutil.php3";

if($cancel)
  go_url( $sess->url(self_base() . "index.php3"));

if(!IfSlPerm(PS_FEEDING, "admin")) {
  MsgPage($sess->url(self_base())."index.php3", _m("You have not permissions to change feeding setting"));
  exit;
}

$err["Init"] = "";          // error array (Init - just for initializing variable

              // lookup all slices without this one
$SQL        = "SELECT id, name FROM slice WHERE id<>'$p_slice_id' ORDER BY name";
$all_slices = GetTable2Array($SQL, 'unpack:id', 'name');

              // lookup export_to slice
$SQL        = "SELECT name, id FROM slice, feedperms WHERE slice.id=feedperms.to_id AND feedperms.from_id='$p_slice_id' ORDER BY name";
$export_to  = GetTable2Array($SQL, 'unpack:id', 'name');

              // lookup importable slice
$SQL        = "SELECT name, id FROM slice LEFT JOIN feedperms ON slice.id=feedperms.from_id
                WHERE (feedperms.to_id='$p_slice_id' OR slice.export_to_all=1) AND slice.id<>'$p_slice_id' ORDER BY name";
$importable = GetTable2Array($SQL, 'unpack:id', 'name');

              // lookup imported slices
$SQL        = "SELECT name, id FROM slice, feeds LEFT JOIN feedperms ON slice.id=feedperms.from_id
                WHERE slice.id=feeds.from_id
                  AND (feedperms.to_id='$p_slice_id' OR slice.export_to_all=1)
                  AND feeds.to_id='$p_slice_id' ORDER BY name";
$imported   = GetTable2Array($SQL, 'unpack:id', 'name');

              // lookup exported slices
$SQL        = "SELECT name, id FROM slice, feeds LEFT JOIN feedperms ON slice.id=feedperms.to_id
                WHERE slice.id=feeds.to_id
                  AND feeds.from_id='$p_slice_id' ORDER BY name";
$exported   = GetTable2Array($SQL, 'unpack:id', 'name');

              // export_to_all setting
$SQL        = "SELECT export_to_all FROM slice WHERE slice.id='$p_slice_id'";
$export_to_all = GetTable2Array($SQL, 'aa_first', 'export_to_all');


// Print HTML start page tags (html begin, encoding, style sheet, but no title)
// Include also js_lib.js javascript library
HtmlPageBegin('default', true);
?>
 <TITLE><?php echo _m("Slice Administration");?></TITLE>

<SCRIPT Language="JavaScript"><!--

function ExportAllClick() {
  document.f.export_y.disabled=document.f.export_to_all.checked;
  document.f.export_n.disabled=document.f.export_to_all.checked;
}

function InitPage() {
  ExportAllClick()
}

function UpdateImportExport(slice_id)
{
  var url = "<?php echo $sess->url(self_base() . "se_import2.php3")?>"
  url += "&slice_id=" + slice_id
  url += "&to_all=" + (document.f.export_to_all.checked ? '1' : '0')
  for (var i = 0; i < document.f.import_y.options.length; i++) {
    if(document.f.import_y.options[i].value != "0")    // imported slices
      url += "&I%5B" + i + "%5D=" + escape(document.f.import_y.options[i].value)
  }
  for (var i = 0; i < document.f.export_y.options.length; i++) {
    if(document.f.export_y.options[i].value != "0")    // exported to slices
      url += "&E%5B" + i + "%5D=" + escape(document.f.export_y.options[i].value)
  }
  document.location=url
}
// -->
</SCRIPT>
</HEAD>
<?php
  $useOnLoad = true;
  require_once $GLOBALS["AA_INC_PATH"]."menu.php3";
  showMenu ($aamenus, "sliceadmin", "import");

  echo "<H1><B>" . _m("Admin - configure Content Pooling") . "</B></H1>";
  PrintArray($err);
  echo $Msg;

$form_buttons = array ("upd" => array("type"=>"button", "value"=>_m("Update"), "accesskey"=>"S",
                                      "add" =>"onClick=\"UpdateImportExport('".$slice_id."')\""),
                       "cancel"=>array("url"=>"se_fields.php3"));

?>
<form method=post name="f" action="<?php echo $sess->url($PHP_SELF) ?>">
<?php
  FrmTabCaption(_m("Enable export to slice:"));
?>
<tr>
    <td width="45%" class=tabtxt align=center><b><?php echo _m("Export disable") ?></b></td>
    <td width="10%">&nbsp;</td>
    <td width="45%" class=tabtxt align=center><b><?php echo _m("Export enable") ?></b></td>
</tr>
<tr>
<td align="CENTER" valign="TOP">
<SELECT name="export_n" size=8 class=tabtxt>
  <?php
  reset($all_slices);
  if( isset($export_to) AND is_array($export_to)) {
    while(list($s_id,$name) = each($all_slices))
      if( $export_to[$s_id] == "" )
        echo "<option value=\"$s_id\"> $name </option>";
  }else
    while(list($s_id,$name) = each($all_slices))
      echo "<option value=\"$s_id\"> $name </option>";
  ?>
</SELECT></td>
<td><input type="button" VALUE="  >>  " onClick = "MoveSelected('document.f.export_n','document.f.export_y')" align=center>
    <br><br><input type="button" VALUE="  <<  " onClick = "MoveSelected('document.f.export_y','document.f.export_n')" align=center></td>
<td align="CENTER" valign="TOP">
<SELECT name="export_y" size=8 class=tabtxt multiple>
  <?php
  if( isset($export_to) AND is_array($export_to)) {
    reset($export_to);
    while(list($s_id,$name) = each($export_to))
      echo "<option value=\"$s_id\"> $name </option>";
  }    ?>
</SELECT>
</td>
</tr>
<tr><td colspan=3><table>
<?php
  FrmInputChBox("export_to_all", _m("Enable export to any slice"), $export_to_all, true, "OnClick=\"ExportAllClick()\"");
  if ( isset($exported) AND is_array($exported) ) {
    FrmStaticText(_m("Currently exported to"), join('<br>',$exported));
  }
?>
</table></td></tr>
<?php
  FrmTabSeparator(_m("Import from slice:"));
  /*
<tr><td colspan=3>&nbsp;</td></tr>
</table></tr></td>
<tr><td class=tabtit><b>&nbsp;<?php echo _m("Import from slice:") ?></b></td></tr>
<tr><td>
<table width="100%" border="0" cellspacing="0" cellpadding="4" bgcolor="<?php echo COLOR_TABBG ?>">*/
?>
<tr>
    <td width="45%" class=tabtxt align=center><b><?php echo _m("Do not import") ?></b></td>
    <td width="10%">&nbsp;</td>
    <td width="45%" class=tabtxt align=center><b><?php echo _m("Import") ?></b></td>
</tr>
<tr>
<td align="CENTER" valign="TOP">
<SELECT name="import_n" size=8 class=tabtxt>
  <?php
  if( isset($importable) AND is_array($importable)) {
    reset($importable);
    while(list($s_id,$name) = each($importable))
      if( $imported[$s_id] == "" )
        echo "<option value=\"$s_id\"> $name </option>";
  }
  ?>
</SELECT></td>
<td><input type="button" VALUE="  >>  " onClick = "MoveSelected('document.f.import_n','document.f.import_y')" align=center>
    <br><br><input type="button" VALUE="  <<  " onClick = "MoveSelected('document.f.import_y','document.f.import_n')" align=center></td>
<td align="CENTER" valign="TOP">
<SELECT name="import_y" size=8 class=tabtxt>
  <?php
  if( isset($imported) AND is_array($imported)) {
    reset($imported);
    while( list($id, $name) = each($imported)) {
      echo "<option value=$id> $name </option>";
    }
  }     ?>
</SELECT>
</td>
</tr>
<?php

  FrmTabEnd($form_buttons, $sess, $slice_id);
  /*
<tr><td colspan=3>&nbsp;</td></tr>
</table></tr></td>
<tr><td align="center">
<input type=hidden name="slice_id" value="<?php echo $slice_id ?>">
<input type="button" VALUE="<?php echo _m("Update") ?>" onClick = "UpdateImportExport('<?php echo $slice_id ?>')" align=center>&nbsp;&nbsp;
<input type=submit name=cancel value="<?php echo _m("Cancel") ?>">
</td></tr></table>*/
echo "
</FORM>";

 HtmlPageEnd();
page_close()?>
