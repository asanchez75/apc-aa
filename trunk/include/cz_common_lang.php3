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
define("L_LOGIN", "P�ihl�en� (Login) - <a href='http://www.ecn.cz'>Econnect</a> Toolkit 2.2");
define("L_LOGIN_TXT", "V�tejte! P�ihla�te se pros�m Va��m jm�nem a heslem<br>(Welcome! Log in by your name and password): ");
define("L_LOGINNAME_TIP", "U�ivatelsk� jm�no �i e-mail<br>(User name or e-mail)");
define("L_SEARCH_TIP", "Seznam je omezen na 5 u�ivatel�.<br>Pokud v seznamu nen� po�adovan� u�ivatel, up�esn�te sv�j dotaz");
define("L_USERNAME", "U�ivatelsk� jm�no<br>(User name):");
define("L_PASSWORD", "Heslo<br>(Password):");
define("L_LOGINNOW", "P�ihl�sit se - Log in");
define("L_BAD_LOGIN", "U�ivatelsk� jm�no �i heslo je neplatn�. (User name or password is not valid.)");
define("L_TRY_AGAIN", "Zkuste to znovu! (Try it again!)");
define("L_BAD_HINT", "Pokud ur�it� zad�v�te spr�vn� heslo, kontaktujte <a href=mailto:". ERROR_REPORTING_EMAIL . ">" . ERROR_REPORTING_EMAIL . "</a>.
	(If you are sure you use the right password, contact ...)");
define("LOGIN_PAGE_BEGIN",
'<!DOCTYPE html public "-/W3C/DTD HTML 4.0 Transitional/EN">
  <HTML>
  <HEAD>
  <LINK rel=StyleSheet href="../'. ADMIN_CSS .'" 
        type="text/css" title="CPAdminCSS">
  <meta http-equiv="Content-Type" content="text/html; charset=windows-1250">');
		
// scroller language constants
define("L_NEXT", ">>>");
define("L_PREV", "<<<");
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
define("L_SLICE_INACCESSIBLE", "�patn� identifika�n� ��slo web�ku, nebo byl web�k vymaz�n");
define("L_APP_TYPE", "Typ web�ku");
define("L_SELECT_APP", "Zvol typ web�ku");
define("L_APP_TYPE_HELP", "<br><br><br><br>");

// log texts
define( "LOG_EVENTS_UNDEFINED", "Undefined" );

// offline filling --------------
define( "L_OFFLINE_ERR_BEGIN",
 '<!DOCTYPE html public "-/W3C/DTD HTML 4.0 Transitional/EN">
  <HTML>
  <HEAD>
  <LINK rel=StyleSheet href="./'.ADMIN_CSS.'" type="text/css">
  <meta http-equiv="Content-Type" content="text/html; charset=windows-1250">
  </HEAD>
  <BODY>');
define( "L_OFFLINE_OK_BEGIN",L_OFFLINE_ERR_BEGIN);
define( "L_OFFLINE_ERR_END","</body></html>");
define( "L_OFFLINE_OK_END",L_OFFLINE_ERR_END);
define( "L_NO_SLICE_ID","Nebylo zad�no ��slo web�ku");
define( "L_NO_SUCH_SLICE","�patn� ��slo web�ku");
define( "L_OFFLINE_ADMITED","Tento web�k m� zak�z�no pln�n� off-line");
define( "L_WDDX_DUPLICATED","Byl zasl�n duplicitn� �l�nek - nevad�, p�esko�en");
define( "L_WDDX_BAD_PACKET","Chba v datech (WDDX)");
define( "L_WDDX_OK","OK - �l�nek vlo�en do datab�ze");
define( "L_CAN_DELETE_WDDX_FILE","Zd� se �e <b>v�e prob�hlo v po��dku</b>, m��ete klidn� 
                                  smazat soubor z disku.");
define( "L_DELETE_WDDX"," Smazat ");

// copyright message for all screens
define( "L_COPYRIGHT", 'Copyright (C) 2001 the 
						<a href="http://www.apc.org">Association for Progressive  Communications (APC)</a>'); 
                   
define("DEFAULT_CODEPAGE","windows-1250");

?>
