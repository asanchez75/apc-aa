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

# common language file

// setup constats
define("L_SETUP_PAGE_BEGIN", 
 '<!DOCTYPE html public "-/W3C/DTD HTML 4.0 Transitional/EN">
  <HTML>
  <HEAD>
  <LINK rel=StyleSheet href="../'.ADMIN_CSS.'" type="text/css">
  <meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">');
define("L_SETUP_TITLE", "AA ����");
define("L_SETUP_H1", "AA ����");
define("L_SETUP_NO_ACTION", "���Υ�����ץȤϡ�����ѤΥ����ƥ�ǤϻȤ��ޤ���");
define("L_SETUP_INFO1", "Welcome! Use this script to create " .
                        "the superadmin account.<p>" .
      "If you are installing a new copy of AA, press <b>Init</b>.<br>");
define("L_SETUP_INFO2", "If you deleted your superadmin account by mistake, press <b>Recover</b>.<br>");
define("L_SETUP_INIT", " Init ");  
define("L_SETUP_RECOVER", "Recover");
define("L_SETUP_TRY_RECOVER", "Can't add primary permission object.<br>" .
       "Please check the access settings to your permission system.<br>" .
       "If you just deleted your superadmin account, use <b>Recover</b>");
define("L_SETUP_USER", "Ķ�����ԥ��������");
define("L_SETUP_LOGIN", "������̾");
define("L_SETUP_PWD1", "�ѥ����");
define("L_SETUP_PWD2", "�ѥ���ɤ򷫤��֤�");
define("L_SETUP_FNAME", "̾��");
define("L_SETUP_LNAME", "��");
define("L_SETUP_EMAIL", "E-�᡼��");
define("L_SETUP_CREATE", "����");
define("L_SETUP_DELPERM", "Invalid permission deleted (no such user/group): ");
define("L_SETUP_ERR_ADDPERM", "Can't assign super access permission.");
define("L_SETUP_ERR_DELPERM", "Can't delete invalid permission.");
define("L_SETUP_OK", "Congratulations! The account was created.");
define("L_SETUP_NEXT", "Use this account to login and add your first slice:");
define("L_SETUP_SLICE", "���饤�����ɲ�");

// loginform language constants
define("L_LOGIN", "�褦����!");
define("L_LOGIN_TXT", "�褦����! ����ʬ�����Ѽ�̾�ȥѥ���ɤǾ������Ƥ���������");
define("L_LOGINNAME_TIP", "���Ѽ�̾�ȥ᡼��򥿥��פ��Ʋ�����");
define("L_SEARCH_TIP", "List is limitted to 5 users.<br>If some user is not in list, try to be more specific in your query");
define("L_USERNAME", "���Ѽ�̾:");
define("L_PASSWORD", "�ѥ����:");
define("L_LOGINNOW", "�����󤹤�");
define("L_BAD_LOGIN", "���Ѽ�̾�ޤ��ϥѥ���ɤ��ְ�äƤ��ޤ���");
define("L_TRY_AGAIN", "�⤦���٤��ᤷ�Ʋ�����!");
define("L_BAD_HINT", "�⤷����ʬ�������פ����ѥ���ɤ��ְ㤨�ʤ����Ȥ��Τ��Ǥ����顢������� e-�᡼������겼������ <a href=mailto:". ERROR_REPORTING_EMAIL . ">" . ERROR_REPORTING_EMAIL . "</a>.");
define("LOGIN_PAGE_BEGIN",
 '<!DOCTYPE html public "-/W3C/DTD HTML 4.0 Transitional/EN">
  <HTML>
  <HEAD>
  <LINK rel=StyleSheet href="../'.ADMIN_CSS.'" type="text/css">
  <meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">');
		
// scroller language constants
define("L_NEXT", "��");
define("L_PREV", "��");
define("L_BACK", "���");
define("L_HOME", "�ۡ���");

// permission language constants (perm_ldap.php3, perm_all.php3)
define("L_USER", "User");
define("L_GROUP", "Group");

// permission configuration constants um_uedit
define("L_NEW_USER", "���������Ѽ�");
define("L_NEW_GROUP", "���������롼��");
define("L_EDIT_GROUP", "���롼�פ��Խ�");

// application not specific strings
define("NO_PICTURE_URL", $AA_INSTAL_PATH ."images/pixel_blank.gif");  // image used when 
  // there is img_source in html format string but no img_source is stored in database 
  // (you can use blank pixel for none picture)

define("L_ALLCTGS", "All categories");
define("L_NO_SUCH_FILE", "No such file");
define("L_BAD_INC", "Bad inc parameter - included file must be in the same directory as this .shtml file and must contain only alphanumeric characters");
define("L_SELECT_CATEGORY", "Select Category ");
define("L_NO_ITEM", "No item found");
define("L_SLICE_INACCESSIBLE", "Invalid slice number or slice was deleted");
define("L_APP_TYPE", "Slice type");
define("L_SELECT_APP", "Select slice type");
define("L_APP_TYPE_HELP", "<br><br><br><br>");

// log texts
define( "LOG_EVENTS_UNDEFINED", "Undefined" );

// offline filling --------------
define( "L_OFFLINE_ERR_BEGIN",
 '<!DOCTYPE html public "-/W3C/DTD HTML 4.0 Transitional/EN">
  <HTML>
  <HEAD>
  <LINK rel=StyleSheet href="./'.ADMIN_CSS.'" type="text/css">
  <meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
  </HEAD>
  <BODY>');
define( "L_OFFLINE_OK_BEGIN",L_OFFLINE_ERR_BEGIN);
define( "L_OFFLINE_ERR_END","</body></html>");
define( "L_OFFLINE_OK_END",L_OFFLINE_ERR_END);
define( "L_NO_SLICE_ID","���饤��ID���������Ƥ��ޤ���");
define( "L_NO_SUCH_SLICE","���饤�� ID�� �ɤ��ʤ��Ǥ�");
define( "L_OFFLINE_ADMITED","���Υ��饤���򥪥ե饤��ǽ񤭹�����Ĥ�����ޤ���");
define( "L_WDDX_DUPLICATED","Duplicated item send - skipped");
define( "L_WDDX_BAD_PACKET","Wrong data (WDDX packet)");
define( "L_WDDX_OK","���ܤ�OK�Ǥ� - �ǡ����١����˳�Ǽ����ޤ���");
define( "L_CAN_DELETE_WDDX_FILE","������ʥե������ä��Ƥ��ޤ��ޤ��� ");
define( "L_DELETE_WDDX"," �ä� ");

// copyright message for all screens
define( "L_COPYRIGHT", 'Copyright (C) 2001 the 
						<a href="http://www.apc.org">Association for Progressive  Communications (APC)</a> 
						under the 
						<a href="http://www.gnu.org/copyleft/gpl.html">GNU General Public License</a>'); 

define("DEFAULT_CODEPAGE","EUC-JP");

# ------------------- New constants (not in other lang files ------------------
# define( ...

?>
