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

// variable $editor_perms should be set
// variable $slice_id should be set

function PrintAddableUser($usr, $usr_id, $editor_role, $new_usr=true) {  
// $usr_id is DN in LDAP
  global $sess, $perms_roles_id;
  $usr_id = rawurlencode($usr_id);

  echo "<tr><td class=tabtxt width=\"25%\">". $usr[name] ."</td>\n";
  echo "<td class=tabtxt width=\"25%\">". 
       (($usr[mail]) ? $usr[mail] : "&nbsp;") ."</td>\n";

  IfLink(($editor_role > $perms_roles_id["AUTHOR"]) && $new_usr, 
         $sess->url(self_base() . "se_users.php3") . 
               "&UsrAdd=$usr_id&role=AUTHOR", L_ROLE_AUTHOR);
  IfLink(($editor_role > $perms_roles_id["EDITOR"]) && $new_usr,
         $sess->url(self_base() . "se_users.php3") .
               "&UsrAdd=$usr_id&role=EDITOR", L_ROLE_EDITOR);
  IfLink(($editor_role > $perms_roles_id["ADMINISTRATOR"]) && $new_usr,
         $sess->url(self_base() . "se_users.php3") .
               "&UsrAdd=$usr_id&role=ADMINISTRATOR", L_ROLE_ADMINISTRATOR);
  echo "</tr>\n";
}

?>
<form method=post action="<?php echo $sess->url($PHP_SELF) ?>">
<table width="440" border="0" cellspacing="0" cellpadding="1" bgcolor="<?php echo COLOR_TABTITBG ?>" align="center">
<tr><td class=tabtit><b>&nbsp;<?php echo L_PERM_NEW ?></b></td></tr>
<tr><td>
<table width="100%" border="0" cellspacing="0" cellpadding="4" bgcolor="<?php echo COLOR_TABBG ?>">
<tr>
        <td width="30%" class=tabtxt><b><?php echo L_USERS ?></b></td>
        <td width="40%"><input type=Text name=usr value=<?php echo safe($usr)?>></td>
        <td width="30%"><input type=submit name="UsrSrch" value=<?php echo L_SEARCH?>></td>
</tr>
<tr>
        <td class=tabtxt><b><?php echo L_GROUPS ?></b></td>
        <td><input type=Text name=grp value=<?php echo safe($grp)?>></td>
        <td><input type=submit name="GrpSrch" value=<?php echo L_SEARCH?>></td>
</tr>
</table></tr></td> <?php 
$continue=false;       
if ($GrpSrch || $UsrSrch) {
  if ($GrpSrch)
    $addable = FindGroups($grp);
   else
    $addable = FindUsers($usr); ?>
  <tr><td class=tabtit><b>&nbsp;<?php echo L_PERM_SEARCH ?></b></td></tr>
  <tr><td>
  <table width="100%" border="0" cellspacing="0" cellpadding="4" bgcolor="<?php echo COLOR_TABBG ?>"><?php 

  // determine role of this user
  if (ComparePerms($editor_perms, $perms_roles_perms["SUPER"])!="L")
    $curr_role = $perms_roles_id["SUPER"];
  elseif (ComparePerms($editor_perms, $perms_roles_perms["ADMINISTRATOR"])!="L")
    $curr_role = $perms_roles_id["ADMINISTRATOR"];
  elseif (ComparePerms($editor_perms, $perms_roles_perms["EDITOR"])!="L")
    $curr_role = $perms_roles_id["EDITOR"];
  elseif (ComparePerms($editor_perms, $perms_roles_perms["AUTHOR"])!="L")
    $curr_role = $perms_roles_id["AUTHOR"];
  else
    $curr_role=0;

  $slice_users = GetObjectsPerms($slice_id, "slice");
  $aa_users = GetObjectsPerms(AA_ID, "aa");   // higher than slice

  if( isset($slice_users) AND !is_array($slice_users) )
    unset($slice_users);
  if( isset($aa_users) AND !is_array($aa_users) )
    unset($aa_users);

  if( is_array($aa_users)) {
    reset($aa_users);    // add aa users too
    while( list($usr_id,$usr)= each($aa_users))
      if( !$slice_users[$usr_id] )
        $slice_users[$usr_id] = $aa_users[$usr_id];
  }
  
  $l_counter = 1;
  if( !is_array($addable) ) {
    if( $addable == "too much" )
      echo "<tr><td class=tabtxt>". L_TOO_MUCH_USERS ." ". L_MORE_SPECIFIC."</td>\n";
     else      
      echo "<tr><td class=tabtxt>". L_NO_USERS ."</td>\n";
  } else {  
    reset($addable);
    while( list($usr_id,$usr) = each($addable)) {
      if(!$slice_users[$usr_id])                         // show only new users
        PrintAddableUser($usr, $usr_id, $curr_role, true);
       else 
        PrintAddableUser($usr, $usr_id, $curr_role, false);
      if($l_counter++ >= MAX_ENTRIES_SHOWN)
        break;
    }    
  }?>
  </table></tr></td>
<?php
}
?>
<tr><td align="center">
<input type=hidden name="slice_id" value="<?php echo $slice_id ?>">
<input type=submit name=back value="<?php echo L_BACK ?>">
</td></tr>
</table>
<br><br><small><?php echo L_SEARCH_TIP ?></small>
</FORM>

