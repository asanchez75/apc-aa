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

# common lanmguage file - comm

// loginform language constants
define("L_LOGIN", "P�ihl�en�");
define("L_LOGIN_TXT", "V�tejte! P�ihla�te se pros�m Va��m jm�nem a heslem:");
define("L_LOGINNAME_TIP", "U�ivatelsk� jm�no �i e-mail");
define("L_LOGINORG_TIP", "nap�. ecn.cz (pr�zdn� pro p�ihl�en� u�ivatelsk�m jm�nem)");
define("L_SEARCH_TIP", "Seznam je omezen na 5 u�ivatel�.<br>Pokud v seznamu nen� po�adovan� u�ivatel, up�esn�te sv�j dotaz");
define("L_USERNAME", "U�ivatelsk� jm�no:");
define("L_PASSWORD", "Heslo:");
define("L_LOGINNOW", "P�ihl�sit se");
define("L_BAD_LOGIN", "U�ivatelsk� jm�no �i heslo je neplatn�.");
define("L_TRY_AGAIN", "Zkuste to znovu!");
define("LOGIN_PAGE_BEGIN",
'<!DOCTYPE html public "-/W3C/DTD HTML 4.0 Transitional/EN">
  <HTML>
  <HEAD>
  <LINK rel=StyleSheet href="../'. ADMIN_CSS .'" 
        type="text/css" title="CPAdminCSS">
  <meta http-equiv="Content-Type" content="text/html; charset=windows-1250">');
		
// scroller language constants
define("L_NEXT", "Dal��");
define("L_PREV", "P�edchoz�");
define("L_BACK", "Zp�t");
define("L_HOME", "Dom�");

// permission language constants (perm_ldap.php3, perm_all.php3)
define("L_USER", "U�ivatel");
define("L_GROUP", "Skupina");

// permission configuration constants um_uedit
define("L_NEW_USER", "Nov� u�ivatel");
define("L_NEW_GROUP", "Nov� skupina");
define("L_EDIT_GROUP", "Editace Skupiny");

// application not specific strings
define("L_ALLCTGS", "V�echny kategorie");
define("L_SELECT_CATEGORY", "Zvol Kategorii ");
define("L_NO_ITEM", "Nenalezena ��dn� zpr�va");
define("L_VIEW_SLICE", "Zobraz web�k");
define("L_SLICE_INACCESSIBLE", "�patn� identifika�n� ��slo web�ku, nebo byl web�k vymaz�n");
define("L_APP_TYPE", "Typ web�ku");
define("L_SELECT_APP", "Zvol typ web�ku");
define("L_APP_TYPE_HELP", "<small><br><br><br><br> Vytvo�en� nov�ho typu web�ku je jednoduch�:<br><ul>
                           <li>vytvo�te nov� jazykov� soubor (see en_news_lang.php3)
                           <li>p�idejte do config.php3 n�sleduj�c� ��dky (samoz�ejm� upraven�):<br>
                            &nbsp; \$ActionAppConfig[en_news][name] = \"Novinky\";<br>
                            &nbsp; \$ActionAppConfig[en_news][file] = \"en_news_lang.php3\";
                           </ul></small>");

define( "L_ICON_LEGEND", '
                  <br>
                  <i>Legenda</i>
                  <TABLE BORDER=1>
                  <TR>
                  <TD><img src="../images/notpubl.gif" width=24 height=24 border=0 alt=""> Dosud nepublikov�no</TD>
                  <TD><img src="../images/publish.gif" width=24 height=24 border=0 alt=""> Publikovan� zpr�va</TD>
                  <TD><img src="../images/expired.gif" width=24 height=24 border=0 alt=""> Expirovan� zpr�va</TD>
                  </TR>
                  <TR bgcolor="#FFCC66">
                  <TD><img src="../images/hlight.gif" border=0 alt=""> D�le�it�</TD>
                  <TD COLSPAN=2><img src="../images/feed.gif" border=0 alt=""> Importov�no</TD></TR>
                  <TR>
                  <TD><img src="../images/app.gif" border=0 alt=""> Aktu�ln�</TD><TD>
                  <img src="../images/hold.gif" border=0 alt=""> Z�sobn�k</TD><TD>
                  <img src="../images/trsh.gif" border=0 alt=""> Ko�</TD></TR>
                  <TR bgcolor="#FFCC66">
                  <TD><img src="../images/less.gif" border=0 alt=""> M�n� detail�</TD>
                  <TD COLSPAN=2><img src="../images/more.gif" border=0 alt=""> V�ce detail�</TD></TR>
                  </TABLE>
                  Kategorie zobrazen� �erven� nejsou sou��st� tohoto web�ku (�l�nek byl z�ejm� importov�n z jin�ho web�ku)<br><br>
                  Web�k zahrnete do sv� *.shtml str�nky p�id�n�m n�sleduj�c�ho p��kazu do HTML k�du:<br><code>&lt;!--#include virtual=&quot;/aa/slice.php3?slice_id='. $slice_id .'&quot;--&gt;
                  </code>');

                   
function datetime2date ($dttm) {
	return ereg_replace("^([[:digit:]]{4})-([[:digit:]]{2})-([[:digit:]]{2}).*", 
		"\\2/\\3/\\1", $dttm);
}

// tranformation from english style datum (3/16/1999 or 3/16/99) to mySQL date
// break year for short year description is 1950
function date2datetime ($dttm) {
  if( !ereg("^ *([[:digit:]]{1,2}) */ *([[:digit:]]{1,2}) */ *([[:digit:]]{4}) *$", $dttm, $part))
    if( !ereg("^ *([[:digit:]]{1,2}) */ *([[:digit:]]{1,2}) */ *([[:digit:]]{2}) *$", $dttm, $part))
      return "";
     else
      $part[3] = ($part[3]<50 ? "20".$part[3] : "19".$part[3]);
	return $part[3] . "-" . $part[1] . "-" . $part[2];
}

function dateExample() {
	return "mm/dd/yyyy";
}

/*
$Log$
Revision 1.2  2000/07/01 07:03:13  kzajicek
fixed typo

Revision 1.1.1.1  2000/06/21 18:40:23  madebeer
reimport tree , 2nd try - code works, tricky to install

Revision 1.1.1.1  2000/06/12 21:50:12  madebeer
Initial upload.  Code works, tricky to install. Copyright, GPL notice there.

Revision 1.6  2000/06/12 19:58:34  madebeer
Added copyright (APC) notice to all .inc and .php3 files that have an $Id

Revision 1.5  2000/05/30 09:11:39  honzama
MySQL permissions upadted and completed.

Revision 1.4  2000/04/24 16:50:33  honzama
New usermanagement interface.

Revision 1.3  2000/03/29 15:54:46  honzama
Better Netscape Navigator javascript support, new direct feeding support, minor changes in texts and look.

Revision 1.2  2000/03/22 09:38:39  madebeer
perm_mysql improvements
Id and Log added to all .php3 and .inc files
system for config-ecn.inc and config-igc.inc both called from
config.inc

*/
?>
