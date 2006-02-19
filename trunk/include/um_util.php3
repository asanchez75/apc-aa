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

require_once $GLOBALS['AA_INC_PATH']."constants.php3";

// Prints html tag <select ..
function SelectGU_ID($name, $arr, $selected="", $type="short", $substract="") {
    if ( $substract=="" ) {                // $substract list of values not shovn in <select> even if in $arr
        $substract = array();
    }
    if ( $type == "short" ) {              // 1-row listbox
        echo "<select name=\"$name\">";
    } else {                               // 8-row listbox
        echo "<select name=\"$name\" size=8>";
    }
    if ( isset($arr) AND is_array($arr)) {
        foreach ($arr as $k => $v) {
            if ( ($v['name'] != "") AND ($substract[$k] == "") ) {
                $option_exist = true;
                echo "<option value=\"". htmlspecialchars($k)."\"";
                if ((string)$selected == (string)$k) {
                    echo " selected";
                }
                echo "> ". htmlspecialchars($v[name]) ." </option>";
            }
        }
        if ( !$option_exist ) { // if no options, we must set width of <select> box
            echo '<option value="wIdThTor"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </option>';
        }
    }
    echo "</select>\n";
}

function GetFiltered($type, $filter, $to_much, $none) {
    switch( $type ) {
        case "U": $list = FindUsers($filter);  break;
        case "G": $list = FindGroups($filter); break;
    }
    if ( !is_array($list) ) {
        unset($list);
        $list["n"]['name'] = (( $list == "too much" ) ? $to_much : $none);
    }
    return $list;
}

function PrintModulePermModificator($selected_user, $form_buttons='', $sess='', $slice_id='') {
    global $db;

    FrmTabSeparatorNoHidden( _m("Permissions"), $form_buttons );
    ?>

  <tr><td>
  <table width="100%" border="0" cellspacing="0" cellpadding="4" bgcolor="<?php echo COLOR_TABBG ?>">

    <?php
    echo '<tr>
            <td><b>'. _m("Object") .'</b></td>
            <td><b>'. _m("Permissions") .'</b></td>
            <td><b>'. _m("Revoke") .'</b></td></tr>';

    $perm_slices = GetIDPerms($selected_user, "slice", 1);  // there are not only Slices, but other Modules too
    $SQL = "SELECT name, type, id FROM module ORDER BY type,name";
    $db->query($SQL);
    $i=0;
    while ( $db->next_record() ) {
        $mid = unpack_id128($db->f('id'));
        if ( $perm_slices[$mid] ) {
            $odd = ((gettype($i/2) == "integer") ? true : false);
            PrintModulePermRow($mid, $db->f('type'), $db->f('name'), $perm_slices[$mid], $odd);
            $i++;
        } else {               // no permission to this module
            // this module should be listed in 'Add perm' listbox
            $mod_2B_add .= "<option value=\"$mid\">". safe($db->f('name')) .'</option>';
            $mod_types .= GetModuleLetter($db->f('type'));  // string for javascript
        }                       // to know, what type of module the $mod_2B_add is
    }
    FrmTabSeparator(_m("Assign new permissions"));
    ?>
   <tr><td>
    <table width="100%" border="0" cellspacing="0" cellpadding="4" bgcolor="<?php echo COLOR_TABBG ?>">
    <?php
    if ( isset($mod_2B_add) ) {          // there is some module to add
        PrintModuleAddRow($mod_2B_add, 1);
        PrintModuleAddRow($mod_2B_add, 2);
        PrintModuleAddRow($mod_2B_add, 3);
    }
    ?>
    </table></td></tr>
    <?php
    return $mod_types;
}

function PrintModulePermRow($mid, $type, $name, $perm, $odd=false) {
    global $MODULES, $perms_roles_modules, $perms_roles;
    echo "<tr>
        <td ".($odd ? " bgcolor=\"".COLOR_BACKGROUND."\"" : "")." align='top'>".$MODULES[$type]['name'] .":&nbsp;$name<br>&nbsp;&nbsp;&nbsp;&nbsp;($mid)</td>
        <td ".($odd ? " bgcolor=\"".COLOR_BACKGROUND."\"" : "")." nowrap align='top'>";
    if ( isset($perms_roles_modules[$type]) AND is_array($perms_roles_modules[$type]) ) {
        foreach ($perms_roles_modules[$type] as $role) {
            echo "<input type=\"radio\" name=\"perm_mod[x$mid]\" value=\"$role\"";
            echo ( ComparePerms($perm,$perms_roles[$role]['id'])=='E' ) ?
            ' checked>' : '>';
            echo _mdelayed($role). ' ';
        }
    } else {
        echo "<input type=\"radio\" name=\"perm_mod[x$mid]\" value=\"ADMINISTRATOR\"
        checked>" . _m('ADMINISTRATOR');
    }
    echo "  </td>
    <td ".($odd ? " bgcolor=\"".COLOR_BACKGROUND."\"" : "")." nowrap align='top'>
    <input type=\"radio\" name=\"perm_mod[x$mid]\" value=\"REVOKE\">". _m("Revoke") ."</td>
    </tr>";
}


function PrintModuleAddRow($mod_options, $no) {
    echo "<tr>
           <td><select name=\"new_module[$no]\" onchange=\"SetRole($no)\">
                 <option> </option>
                 $mod_options</select></td>
           <td><select name=\"new_module_role[$no]\">
                  <option> </option>
                  <option value=\"AUTHOR\">". _m('AUTHOR'). "</option>
                  <option value=\"EDITOR\">". _m('EDITOR'). "</option>
                  <option value=\"ADMINISTRATOR\">". _m('ADMINISTRATOR'). "</option>
               </select></td>
          </tr>";
}


/** Change module permissions if user wants
 * Works not only with users, but with groups too
 */
function ChangeUserModulePerms( $perm_mod, $selected_user, $perms_roles ) {
    if ($debug) {
        echo "<br>function ChangeUserModulePerms( $perm_mod, $selected_user, $perms_roles )";
        print_r($perm_mod);
        print_r($perms_roles);
    }
    if ( isset($perm_mod) AND is_array($perm_mod) ) {
        $perm_slices = GetIDPerms($selected_user, "slice", 1);  // there are not only Slices, but other Modules too
        foreach ($perm_mod as $xmid => $role) {
            $mid = substr($xmid,1);   // removes first 'x' character (makes index string)
            if ( $role == 'REVOKE' ) {
                DelPerm($selected_user, $mid, 'slice');
            }
            elseif( ComparePerms($perm_slices[$mid], $perms_roles[$role]['id']) != 'E' ) {
                ChangePerm($selected_user, $mid, 'slice', $perms_roles[$role]['id']);
            }
        }
    }
}

/** Add new modules for this user
 *  Works not only with users, but with groups too
 */
function AddUserModulePerms( $new_module, $new_module_role, $selected_user, $perms_roles) {
    if ( isset($new_module) AND is_array($new_module) ) {
        foreach ($new_module as $no => $mid) {
            if ( (trim($mid) != "") AND isset($perms_roles[$new_module_role[$no]]) ) {
                AddPerm($selected_user, $mid, 'slice', $perms_roles[$new_module_role[$no]]['id']);
            }
        }
    }
}

/**
 * Returned Module letter is used for as full identification of the module
 * by 1-letter long id (we need it for some javascripts in um_util.php3)
 */
function GetModuleLetter($type) {
    global $MODULES;
    // get 'letter' or first letter of MODULE type
    return ($MODULES[$type]['letter'] ? $MODULES[$type]['letter'] : substr($type,0,1));
}

function PrintPermUmPageEnd($MODULES, $mod_types, $perms_roles_modules) { ?>
    <script language="JavaScript"><!--
      var mod       = new Array();
      var mod_names = new Array();
    <?php
    // tell javascript, which module uses which permission roles
    echo "\n var mod_types='$mod_types';\n";
    foreach ($MODULES as $k => $v) {
        $letter = GetModuleLetter($k);             // get 'letter' or first letter of MODULE type
        if ( isset($perms_roles_modules[$k]) AND is_array($perms_roles_modules[$k]) ) {
            echo " mod[".ord($letter)."] = new Array('". join("','", $perms_roles_modules[$k]) ."');  // module type $k\n";
            echo " mod_names[".ord($letter)."] = new Array('". join("','", array_map( '_mdelayed', $perms_roles_modules[$k])) ."');\n";
        }
    }
    ?>
      // set right roles for modules listed in 'Add rows'
      SetRole(1);
      SetRole(2);
      SetRole(3);
      // -->
    </script>
    <?php
}

/** Procces group data */
function ChangeUserGroups($posted_groups, $sel_groups, $selected_user) {
    if ( isset($sel_groups) AND is_array($sel_groups) AND ($sel_groups["n"]=="")) {
        // first we remove user from all groups
        foreach ($sel_groups as $foo_gid => $foo) {
            DelGroupMember($foo_gid, $selected_user);
        }
    }
    // now we add user to specified groups

    // posted_groups contains comma delimeted list of selected groups for user
    $assigned_groups = explode(",",$posted_groups);
    if ( isset($assigned_groups) AND is_array($assigned_groups) ) {
        foreach ($assigned_groups as $foo_gid) {
            AddGroupMember(urldecode($foo_gid), $selected_user);
        }
    }
}

function FillUserRecord(&$err, $user_login, $user_surname, $user_firstname, $user_password1, $user_password2,  $user_mail1, $user_mail2, $user_mail3) {

    $userrecord = array();
    ValidateInput("user_login", _m("Login name"), $user_login, $err, true, "login");
    ValidateInput("user_surname", _m("Surname"), $user_surname, $err, true, "text");
    ValidateInput("user_firstname", _m("First name"), $user_firstname, $err, true, "text");
    // if unchanged password, don't post it
    if (($user_password1 != "nOnEwpAsswD") OR ($user_password2 != "nOnEwpAsswD")) {
        ValidateInput("user_password1", _m("Password"), $user_password1, $err, true, "password");
        ValidateInput("user_password2", _m("Retype password"), $user_password2, $err, true, "password");
        if ( $user_password1 != $user_password2 ) {
            $err[$user_password2] = MsgErr(_m("Retyped password is not the same as the first one"));
        }
        $userrecord["userpassword"]        = $user_password1;
    }
    ValidateInput("user_mail1", _m("E-mail")." 1", $user_mail1, $err, true, "email");
    ValidateInput("user_mail2", _m("E-mail")." 2", $user_mail2, $err, false, "email");
    ValidateInput("user_mail3", _m("E-mail")." 3", $user_mail3, $err, false, "email");

    $userrecord["givenname"]               = $user_firstname;
    $userrecord["sn"]                      = $user_surname;

    if ($user_mail1) $userrecord["mail"][] = $user_mail1;
    if ($user_mail2) $userrecord["mail"][] = $user_mail2;
    if ($user_mail3) $userrecord["mail"][] = $user_mail3;
    return $userrecord;
}

function NewUserData( &$err, $uid, &$userrecord, $user_super, &$perms_roles, $um_uedit_no_go_url) {
    global $sess;
    $userrecord["uid"] = $uid;
    if (!($newuserid = AddUser($userrecord))) {
        $err["LDAP"] = MsgErr( _m("It is impossible to add user to permission system") );
    }
    if ( count($err) <= 1 ) {
        if ($user_super) {	// set super admin privilege
            AddPerm($newuserid, AA_ID, "aa", $perms_roles["SUPER"]['id']);
        }
        $Msg = MsgOK(_m("User successfully added to permission system"));
        if (!$um_uedit_no_go_url) {
            go_url( get_url($sess->url($PHP_SELF), 'UsrSrch=1&usr='. urlencode($user_login)), $Msg);
        }
    }
}

function ChangeUserData( &$err, $uid, &$userrecord, $user_super, &$perms_roles) {
      $userrecord["uid"] = $uid;
      if (!ChangeUser($userrecord)) {
          $err["LDAP"] = MsgErr( _m("Can't change user") );
      } elseif ($user_super != 'AA_NO_CHANGE') {
          if ($user_super) {		// set or revoke super admin privilege
              AddPerm($userrecord["uid"], AA_ID, "aa", $perms_roles["SUPER"]['id']);
          } else {
              DelPerm($userrecord["uid"], AA_ID, "aa");
          }
      }
}

?>