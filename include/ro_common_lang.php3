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

# Translation 1.0  2002/03/08 15:44:20  Mih�ly Bak�, StrawberryNet Foundation

# common language file

// setup constats
define("L_SETUP_PAGE_BEGIN", 
 '<!DOCTYPE html public "-//W3C//DTD HTML 4.0 Transitional//EN">
  <HTML>
  <HEAD>
  <LINK rel=StyleSheet href="../'.ADMIN_CSS.'" type="text/css">
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2">');
define("L_SETUP_TITLE", "Setare AA");
define("L_SETUP_H1", "Setare AA");
define("L_SETUP_NO_ACTION", "Acest script nu se poate aplica unui sistem configurat.");
define("L_SETUP_INFO1", "Bine a�i venit! Cu acest program ve�i crea " .
                        "contul de superadmin.<p>" .
      "Dac� instala�i o nou� copie al AA, ap�sa�i <b>Ini�ializare</b>.<br>");
define("L_SETUP_INFO2", "Dac� a�i �ters din gre�eal� contul de superadmin, ap�sa�i <b>Refacere</b>.<br>");
define("L_SETUP_INIT", " Ini�ializare ");  
define("L_SETUP_RECOVER", "Refacere");
define("L_SETUP_TRY_RECOVER", "Nu pot ad�uga obiectul primar de permisiuni.<br>" .
       "V� rog verifica�i set�rile de acces al sistemului dvs. de permisiuni.<br>" .
       "Dac� v-a�i �ters contul de superadmin, folosi�i <b>Refacere</b>");
define("L_SETUP_USER", "Cont de superadmin");
define("L_SETUP_LOGIN", "Nume utilizator");
define("L_SETUP_PWD1", "Parol�");
define("L_SETUP_PWD2", "Parola �nc� o dat�");
define("L_SETUP_FNAME", "Prenumele");
define("L_SETUP_LNAME", "Numele");
define("L_SETUP_EMAIL", "E-mail");
define("L_SETUP_CREATE", "Creeaz�");
define("L_SETUP_DELPERM", "Permisiune invalid� �tears� (utilizator/grup inexistent): ");
define("L_SETUP_ERR_ADDPERM", "Nu pot asigna permisiune de super acces.");
define("L_SETUP_ERR_DELPERM", "Nu pot �terge permisiune invalid�.");
define("L_SETUP_OK", "Felicit�ri! Contul a fost creat.");
define("L_SETUP_NEXT", "Folosi�i acest cont pentru intrare �i ad�ugarea primei sec�iuni:");
define("L_SETUP_SLICE", "Ad�ugare sec�iune");

// loginform language constants
define("L_LOGIN", "Bine a�i venit!");
define("L_LOGIN_TXT", "Bine a�i venit! V� rog s� v� identifica�i printr-un nume utilizator �i o parol�:");
define("L_LOGINNAME_TIP", "Tasta�i-v� numele utilizator sau adresa de e-mail");
define("L_SEARCH_TIP", "Lista este limitat� la 5 utilizatori.<br>Dac� un utilizator nu apare �n list�, folosi�i termeni de c�utare mai specifici");
define("L_USERNAME", "Nume utilizator:");
define("L_PASSWORD", "Parol�:");
define("L_LOGINNOW", "Intr� acum");
define("L_BAD_LOGIN", "Numele utilizator sau parola nu este valid�.");
define("L_TRY_AGAIN", "V� rog �ncerca�i din nou!");
define("L_BAD_HINT", "Dac� sunte�i siguri c� a�i introdus parola corect�, trimite�i e-mail la <a href=mailto:". ERROR_REPORTING_EMAIL . ">" . ERROR_REPORTING_EMAIL . "</a>.");
define("LOGIN_PAGE_BEGIN",
 '<!DOCTYPE html public "-//W3C//DTD HTML 4.0 Transitional//EN">
  <HTML>
  <HEAD>
  <LINK rel=StyleSheet href="../'.ADMIN_CSS.'" type="text/css">
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2">');
		
// scroller language constants
define("L_NEXT", "Urm�tor");
define("L_PREV", "Precedent");
define("L_BACK", "�napoi");
define("L_HOME", "Prima");

// permission language constants (perm_ldap.php3, perm_all.php3)
define("L_USER", "Utilizator");
define("L_GROUP", "Grup");

// permission configuration constants um_uedit
define("L_NEW_USER", "Utilizator nou");
define("L_NEW_GROUP", "Grup nou");
define("L_EDIT_GROUP", "Editare grup");

// application not specific strings
define("NO_PICTURE_URL", AA_INSTAL_URL ."images/pixel_blank.gif");  // image used when 
  // there is img_source in html format string but no img_source is stored in database 
  // (you can use blank pixel for none picture)

define("L_ALLCTGS", "Toate categoriile");
define("L_NO_SUCH_FILE", "Fi�ier inexistent");
define("L_BAD_INC", "Paramentru inc gre�it - fi�ierul inclus trebuie s� fie �n acela�i catalog cu acest fi�ier .shtml �i trebuie s� con�in� numai caractere alfanumerice");
define("L_SELECT_CATEGORY", "Selecta�i categoria ");
define("L_NO_ITEM", "Nu am g�sit nici o pozitie");
define("L_SLICE_INACCESSIBLE", "Num�r sec�iune invalid� sau sec�iunea a fost �tears�");
define("L_APP_TYPE", "Tip sec�iune");
define("L_SELECT_APP", "Alege�i tipul sec�iunii");
define("L_APP_TYPE_HELP", "<br><br><br><br>");

// log texts
define( "LOG_EVENTS_UNDEFINED", "Nedefinit" );

// offline filling --------------
define( "L_OFFLINE_ERR_BEGIN",
 '<!DOCTYPE html public "-//W3C//DTD HTML 4.0 Transitional//EN">
  <HTML>
  <HEAD>
  <LINK rel=StyleSheet href="./'.ADMIN_CSS.'" type="text/css">
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2">
  </HEAD>
  <BODY>');
define( "L_OFFLINE_OK_BEGIN",L_OFFLINE_ERR_BEGIN);
define( "L_OFFLINE_ERR_END","</body></html>");
define( "L_OFFLINE_OK_END",L_OFFLINE_ERR_END);
define( "L_NO_SLICE_ID","ID al sec�iunii nedefinit");
define( "L_NO_SUCH_SLICE","ID sec�iune gre�it");
define( "L_OFFLINE_ADMITED","Nu ave�i permisiunea de a completa aceast� sec�iune off-line");
define( "L_WDDX_DUPLICATED","Date duplicate - s�rit");
define( "L_WDDX_BAD_PACKET","Data eronat� (pachet WDDX)");
define( "L_WDDX_OK","Item OK - stocat �n baza de date");
define( "L_CAN_DELETE_WDDX_FILE","Acum pute�i �terge fi�ierul local. ");
define( "L_DELETE_WDDX"," �terge ");

// copyright message for all screens
define( "L_COPYRIGHT", 'Copyright (C) 2001 the 
						<a href="http://www.apc.org">Association for Progressive  Communications (APC)</a> 
						under the 
						<a href="http://www.gnu.org/copyleft/gpl.html">GNU General Public License</a>'); 

define("DEFAULT_CODEPAGE","iso-8859-2");

# ------------------- New constants (not in other lang files ------------------

/*
Translation 1.0  2002/03/08 15:44:20  Mih�ly Bak�, StrawberryNet Foundation
*/
?>

