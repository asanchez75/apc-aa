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

// setup constats
define("L_SETUP_PAGE_BEGIN", 
 '<!DOCTYPE html public "-/W3C/DTD HTML 4.0 Transitional/EN">
  <HTML>
  <HEAD>
  <LINK rel=StyleSheet href="../'.ADMIN_CSS.'" type="text/css">
  <meta http-equiv="Content-Type" content="text/html; charset=windows-1250">');
define("L_SETUP_TITLE", "AA Setup");
define("L_SETUP_H1", "AA Setup");
define("L_SETUP_NO_ACTION", "Tento skript nem��e b�t pou�it na ji� nakonfigurovan�m syst�mu.");
define("L_SETUP_INFO1", "V�tejte! Pomoc� tohoto skriptu vytvo��te " .
                        "V� administrativn� ��et.<p>" .
      "Pokud instalujete novou kopii tohoto programu, zvolte <b>Inicializace</b>.<br>");
define("L_SETUP_INFO2", "Pokud jste omylem smazali V� administrativn� ��et, zvolte <b>Obnovit</b>.<br>");
define("L_SETUP_INIT", "Inicializace");  
define("L_SETUP_RECOVER", "Obnovit");
define("L_SETUP_TRY_RECOVER", "Nemohu p�idat prim�rn� objekt v datab�zi p�istupov�ch pr�v.<br>" .
       "Pros�m zkontrolujte p��stupov� pr�va do datab�ze p�istupov�ch pr�v.<br>" .
       "Pokud jste pouze smazali V� administrativn� ��et, pou�ijte volbu <b>Obnovit</b>");
define("L_SETUP_USER", "Administrativn� ��et");
define("L_SETUP_LOGIN", "U�ivatelsk� jm�no");
define("L_SETUP_PWD1", "Heslo");
define("L_SETUP_PWD2", "Potvrdit heslo");
define("L_SETUP_FNAME", "Jm�no");
define("L_SETUP_LNAME", "P��jmen�");
define("L_SETUP_EMAIL", "E-mail");
define("L_SETUP_CREATE", "Vytvo�it");
define("L_SETUP_DELPERM", "Nekonzistentn� pr�va byla smaz�na (��et/skupina neexistuje): ");
define("L_SETUP_ERR_ADDPERM", "Nen� mo�n� p�i�adit administrativn� pr�va.");
define("L_SETUP_ERR_DELPERM", "Nen� mo�n� smazat nekonzistentn� pr�va.");
define("L_SETUP_OK", "Gratulace! ��et byl vytvo�en.");
define("L_SETUP_NEXT", "Zalogujte se t�mto ��tem a vytvo�te prvn� web�k:");
define("L_SETUP_SLICE", "P�idat web�k");

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
define("L_BAD_HINT", "Pokud ur�it� zad�v�te spr�vn� heslo, kontaktujte <a href=mailto:". ERROR_REPORTING_EMAIL . ">" . ERROR_REPORTING_EMAIL . "</a>.");
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
define("NO_PICTURE_URL", AA_INSTAL_URL ."images/pixel_blank.gif");  // image used when 
  // there is img_source in html format string but no img_source is stored in database 
  // (you can use blank pixel for none picture)
define("L_ALLCTGS", "V�echny kategorie");
define("L_BAD_INC", "�patn� parametr inc - soubor m��e b�t v tomt� adres��i, kde je .shtml soubor - smi obsahovat jen znaky a cisla");
define("L_NO_SUCH_FILE", "Soubor nenalezen");
define("L_SELECT_CATEGORY", "Zvol Kategorii ");
define("L_NO_ITEM", "Nenalezena ��dn� zpr�va");
define("L_VIEW_SLICE", "Zobraz web�k");
define("L_SLICE_INACCESSIBLE", "�patn� identifika�n� ��slo web�ku, nebo byl web�k vymaz�n");
define("L_APP_TYPE", "Typ web�ku");
define("L_SELECT_APP", "Zvol typ web�ku");
define("L_APP_TYPE_HELP", "<br><br><br><br>");

define( "L_ICON_LEGEND", '
                  <br>
                  <i>Legenda</i>
                  <TABLE BORDER=1>
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
                  </TABLE>');
                           
define( "L_SLICE_HINT", '
                  <br>
                  Web�k zahrnete do sv� *.shtml str�nky p�id�n�m n�sleduj�c� ��dky v HTML k�du:
                  ');
                   
// tranformation from english style datum (3/16/1999 or 3/16/99) to timestamp
// break year for short year description is 1970
function userdate2sec ($dttm, $time="") {
  if( !ereg("^ *([[:digit:]]{1,2}) */ *([[:digit:]]{1,2}) */ *([[:digit:]]{4}) *$", $dttm, $part))
    if( !ereg("^ *([[:digit:]]{1,2}) */ *([[:digit:]]{1,2}) */ *([[:digit:]]{2}) *$", $dttm, $part))
      return "";
  if( !ereg("^ *([[:digit:]]{1,2}) *: *([[:digit:]]{1,2}) *: *([[:digit:]]{1,2}) *$", $time, $tpart))
    return mktime(0,0,0,$part[1],$part[2],$part[3]);
   else
    return mktime($tpart[1],$tpart[2],$tpart[3],$part[1],$part[2],$part[3]);
}

function dateExample() {
	return "mm/dd/yyyy";
}

/*
$Log$
Revision 1.13  2001/01/23 23:58:03  honzam
Aliases setings support, bug in permissions fixed (can't login not super user), help texts for aliases page

Revision 1.11  2000/12/21 16:39:34  honzam
New data structure and many changes due to version 1.5.x

Revision 1.8  2000/08/23 12:29:57  honzam
fixed security problem with inc parameter to slice.php3

Revision 1.7  2000/08/17 15:17:55  honzam
new possibility to redirect item displaying (for database changes see CHANGES)

Revision 1.6  2000/08/14 12:39:13  kzajicek
Language definitions required by setup.php3

Revision 1.5  2000/07/26 16:01:48  kzajicek
More descriptive message for "login failed"

Revision 1.4  2000/07/12 14:26:40  kzajicek
Poor printing of the SSI statement fixed

Revision 1.3  2000/07/03 15:00:14  honzam
Five table admin interface. 'New slice expiry date bug' fixed.

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
