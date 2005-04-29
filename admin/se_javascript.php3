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

/* The Field Triggers edit page
    (c) Jakub Adamek, June 2002
*/

require_once "../include/init_page.php3";
require_once $GLOBALS['AA_INC_PATH']."formutil.php3";
require_once $GLOBALS['AA_INC_PATH']."varset.php3";
require_once $GLOBALS['AA_INC_PATH']."pagecache.php3";
require_once $GLOBALS['AA_INC_PATH']."msgpage.php3";

if ($cancel) {
    go_url( $sess->url(self_base() . "index.php3"));
}

if (!IfSlPerm(PS_FIELDS)) {
    MsgPageMenu($sess->url(self_base())."index.php3", _m("You have not permissions to change fields settings"), "admin");
    exit;
}

$err["Init"] = "";          // error array (Init - just for initializing variable

// update database or get the value

if (get_magic_quotes_gpc() && $javascript) {
    $javascript = stripslashes ($javascript);
}

if ($p_slice_id && $update) {
    tryQuery("UPDATE slice SET javascript=\"".myaddslashes($javascript)."\"
        WHERE id='$p_slice_id'");
} else {
    $db = getDB();
    $db->tquery("SELECT javascript FROM slice WHERE id='$p_slice_id'");
    if ($db->next_record())
        $javascript = $db->f("javascript");
    freeDB($db);
}

HtmlPageBegin();   // Print HTML start page tags (html begin, encoding, style sheet, but no title)
?>
 <TITLE><?php echo _m("Field Triggers");?></TITLE>

</HEAD>
<?php
  require_once $GLOBALS['AA_INC_PATH']."menu.php3";
  showMenu($aamenus, "sliceadmin","javascript");

  echo "<H1><B>" . _m("Field Triggers") . "</B></H1>";
  PrintArray($err);
  echo $Msg;
?>
<form method=post action="<?php echo $sess->url($PHP_SELF) ?>">
<?php
  FrmTabCaption(_m("JavaScript for fields"));
?>
<tr><td><?php FrmStaticText(_m("Enter code in the JavaScript language. It will be included in the Add / Edit item page (itemedit.php3)."),""); ?></td></tr>
<tr><td class=tabtxt><hr></td></tr>
<tr><td class=tabtxt><textarea name="javascript" cols=100 rows=20>
<?php
echo $javascript.'</textarea></td></tr>';
FrmTabSeparator(_m("Available fields and triggers"),array("update", "update"=>array("type"=>"hidden", "value"=>"1"),
                      "cancel"=>array("url"=>"se_fields.php3")),$sess, $slice_id);
echo '
</FORM>';

$SQL = "SELECT id FROM field
        WHERE slice_id='$p_slice_id'
        ORDER BY id";
$db = getDB();
$db->query($SQL);
echo '
<tr><td valign=top><table border="0" cellspacing="0" cellpadding="1" bgcolor="'.COLOR_TABTXTBG.'">
<tr><td class=tabtit>'._m("Field IDs").':</td></tr>';
while ($db->next_record())
    echo "<tr><td class=tabtxt>".$db->f("id")."</td></tr>";
freeDB($db);
echo '</table>
</td>
<td valign=top><table border="0" cellspacing="0" cellpadding="1" bgcolor="'.COLOR_TABTXTBG.'">
<tr><td class=tabtit>'._m("Triggers").':</td></tr>
<tr><td class=tabtxt>'._m("Write trigger functions like").' "aa_onSubmit (fieldid) { }", <a href="http://actionapps.org/faq/detail.shtml?x=1706" target="_blank">'._m("see FAQ</a> for more details and examples").'</td></td></tr>
<tr><td class=tabtxt><table border="1" cellspacing="0" cellpadding="1" bgcolor="'.COLOR_TABTXTBG.'">';
echo '<tr><td class=tabtit><b>'._m("Field Type").'</b></td><td class=tabtit><b>'._m("Triggers Available -- see some JavaScript help for when a trigger is run").'</b></td></tr>';
foreach ($js_triggers as $control => $trigs) {
    echo '<tr><td class=tabtxt>'.$control.'</td><td class=tabtxt>'.join($trigs,", ").'</td></tr>';
}
echo '
</table></td></tr>
</table></td>
</tr></table>';
HtmlPageEnd();
page_close()

?>
