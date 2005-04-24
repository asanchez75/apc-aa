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

// se_admin.php3 - assigns html format for administation item view (index.php3)
// optionaly $Msg to show under <h1>Hedline</h1> (typicaly: update successful)

require_once "../include/init_page.php3";
require_once $GLOBALS['AA_INC_PATH']."formutil.php3";
require_once $GLOBALS['AA_INC_PATH']."varset.php3";
require_once $GLOBALS['AA_INC_PATH']."item.php3";     // GetAliasesFromField funct def
require_once $GLOBALS['AA_INC_PATH']."pagecache.php3";
require_once $GLOBALS['AA_INC_PATH']."msgpage.php3";

if ($cancel)
  go_url( $sess->url(self_base() . "index.php3"));

if (!IfSlPerm(PS_CONFIG)) {
  MsgPageMenu($sess->url(self_base())."index.php3", _m("You have no permission to set configuration parameters of this slice"), "admin");
  exit;
}

$err["Init"] = "";          // error array (Init - just for initializing variable
$varset = new Cvarset();
$p_slice_id = q_pack_id($slice_id);

if ( $r_fields )
  $fields = $r_fields;
else
  list($fields,) = GetSliceFields($slice_id);


if ( $update )
{
  do
  {
    ValidateInput("admin_format_top",    _m("Top HTML"),                                $admin_format_top,    $err, false, "text");
    ValidateInput("admin_format",        _m("Item format"),                             $admin_format,        $err, true,  "text");
    ValidateInput("admin_format_bottom", _m("Bottom HTML"),                             $admin_format_bottom, $err, false, "text");
    ValidateInput("admin_remove",        _m("Remove strings"),                          $admin_remove,        $err, false, "text");
    ValidateInput("admin_noitem_msg",    _m("HTML code for \"No item found\" message"), $admin_noitem_msg,    $err, false, "text");

    if ( count($err) > 1)
      break;

    $varset->add("admin_format_top",    "quoted", $admin_format_top);
    $varset->add("admin_format",        "quoted", $admin_format);
    $varset->add("admin_format_bottom", "quoted", $admin_format_bottom);
    $varset->add("admin_remove",        "quoted", $admin_remove);
    $varset->add("admin_noitem_msg",    "quoted", $admin_noitem_msg);
    if ( !$db->query("UPDATE slice SET ". $varset->makeUPDATE() .
                     "WHERE id='".q_pack_id($slice_id)."'")) {
      $err["DB"] = MsgErr( _m("Can't change slice settings") );
      break;    // not necessary - we have set the halt_on_error
    }

    $GLOBALS[pagecache]->invalidateFor("slice_id=$slice_id");  // invalidate old cached values

    $admin_format_top = dequote($admin_format_top);
    $admin_format = dequote($admin_format);
    $admin_format_bottom = dequote($admin_format_bottom);
  }while(false);
  if ( count($err) <= 1 )
    $Msg = MsgOK(_m("Admin fields update successful"));
}

if ( $slice_id!="" ) {  // set variables from database
  $SQL= " SELECT admin_format, admin_format_top, admin_format_bottom,
                 admin_remove, admin_noitem_msg
            FROM slice WHERE id='". q_pack_id($slice_id)."'";
  $db->query($SQL);
  if ($db->next_record()) {
    $admin_format_top    = $db->f('admin_format_top');
    $admin_format        = $db->f('admin_format');
    $admin_format_bottom = $db->f('admin_format_bottom');
    $admin_remove        = $db->f('admin_remove');
    $admin_noitem_msg    = $db->f('admin_noitem_msg');
  }
}

HtmlPageBegin();   // Print HTML start page tags (html begin, encoding, style sheet, but no title)
?>
<TITLE><?php echo _m("Admin - design Item Manager view");?></TITLE>
<SCRIPT Language="JavaScript"><!--
function Defaults() {
  document.f.admin_format_top.value = '<?php echo DEFAULT_ADMIN_TOP ?>'
  document.f.admin_format.value = '<?php echo DEFAULT_ADMIN_HTML ?>'
  document.f.admin_format_bottom.value = '<?php echo DEFAULT_ADMIN_BOTTOM ?>'
  document.f.admin_remove.value = '<?php echo DEFAULT_ADMIN_REMOVE ?>'
  document.f.admin_noitem_msg.value = ''
}
// -->
</SCRIPT>
</HEAD>

<?php
  require_once $GLOBALS['AA_INC_PATH']."menu.php3";
  showMenu ($aamenus, "sliceadmin", "config");

  echo "<H1><B>" . _m("Admin - design Item Manager view") . "</B></H1>";
  PrintArray($err);
  echo $Msg;

  $form_buttons = array("update" => array("type"=>"hidden", "value"=>"1"),
                        "update", "cancel"=>array("url"=>"se_fields.php3"),
                        "defaults" => array("type"=>"button", "value"=> _m("Default"), "add"=>'onclick="Defaults()"'));

?>
<form name=f method=post action="<?php echo $sess->url($PHP_SELF) ?>">
<?php
  FrmTabCaption(_m("Listing of items in Admin interface"),'','',$form_buttons, $sess, $slice_id);

  FrmTextarea("admin_format_top", _m("Top HTML"), $admin_format_top, 4, 60,
              false, _m("HTML code which appears at the top of slice area"), DOCUMENTATION_URL, 1);
  FrmTextarea("admin_format", _m("Item format"), $admin_format, 12, 60, true,
                     _m("Put here the HTML code combined with aliases form bottom of this page\n                     <br>The aliase will be substituted by real values from database when it will be posted to page"), DOCUMENTATION_URL, 1);
  FrmTextarea("admin_format_bottom", _m("Bottom HTML"), $admin_format_bottom,
              4, 60, false, _m("HTML code which appears at the bottom of slice area"), DOCUMENTATION_URL, 1);
  FrmInputText("admin_remove", _m("Remove strings"), $admin_remove, 254, 50, false,
               _m("Removes empty brackets etc. Use ## as delimeter."), DOCUMENTATION_URL);
  FrmTextarea("admin_noitem_msg", _m("HTML code for \"No item found\" message"), $admin_noitem_msg,
              4, 60, false, _m("Code to be printed when no item is filled (or user have no permission to any item in the slice)"), DOCUMENTATION_URL, 1);
  PrintAliasHelp(GetAliasesFromFields($fields), $fields, false, $form_buttons);
  FrmTabEnd("", false, true);
?>
</FORM>
<?php HtmlPageEnd();
page_close()?>

