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

if (!defined ("CZ_NEWS_LANG_INCLUDED"))
   	  define ("CZ_NEWS_LANG_INCLUDED",1);
else return;

# config file identifier
# must correspond with this file name
define("LANG_FILE", "cz_news_lang.php3");
define("CHARSET", "windows-1250");

define("EDIT_ITEM_COUNT", 20);                  // number of items in editor window

define("DEFAULT_FULLTEXT_HTML", '<BR><FONT SIZE=+2 COLOR=blue>_#HEADLINE</FONT> <BR><B>_#PUB_DATE</B> <BR>_#FULLTEXT');
define("DEFAULT_ODD_HTML", '<font face=Arial color=#808080 size=-2>_#PUB_DATE - </font><font color=#FF0000><strong><a href=_#HDLN_URL>_#HEADLINE</a></strong></font><font color=#808080 size=-1><br>_#PLACE###(<a href="_#SRC_URL#">_#SOURCE##</a>) - </font><font color=black size=-1>_#ABSTRACT<br></font><br>');
define("DEFAULT_EVEN_HTML", "");
define("DEFAULT_TOP_HTML", "<br>");
define("DEFAULT_BOTTOM_HTML", "<br>");
define("DEFAULT_CATEGORY_HTML", "<p>_#CATEGORY</p>");
define("DEFAULT_EVEN_ODD_DIFFER", false);
define("DEFAULT_CATEGORY_SORT", true);
define("DEFAULT_COMPACT_REMOVE", "()");
define("DEFAULT_FULLTEXT_REMOVE", "()");


# HTML begin of admin page
# You should set language of admin pages and possibly any meta tags
define("HTML_PAGE_BEGIN", 
'<!DOCTYPE html public "-//W3C//DTD HTML 4.0 Transitional//EN">
  <HTML XMLNS:XS>
  <HEAD>
  <LINK rel=StyleSheet href="'.$AA_INSTAL_PATH.ADMIN_CSS.'" type="text/css"  title="CPAdminCSS">
  <meta http-equiv="Content-Type" content="text/html; charset=windows-1250">');
               
# aa toolkit specific labels
define("L_VIEW_SLICE", "Zobraz");
define( "L_SLICE_HINT", '<br>Web�k zahrnete do sv� *.shtml str�nky p�id�n�m 
                             n�sleduj�c� ��dky v HTML k�du: ');
define("L_ITEM_ID_ALIAS","alias pro ��slo �l�nku");
define("L_EDITITEM_ALIAS","alias pou��van� v administrativn�ch str�nk�ch index.php3 pro URL itemedit.php3");
define("L_LANG_FILE","Pou�it� language soubor");
define("L_PARAMETERS","Parametry");
define("L_SELECT_APP","Vyber aplikaci");
define("L_SELECT_OWNER","Vyber vlastn�ka");

define("L_CANT_UPLOAD", "Soubor (obr�zek) nelze ulo�it");
define("L_MSG_PAGE", "Zpr�va aplikace");   // title of message page
define("L_EDITOR_TITLE", "Spr�va zpr�v");
define("L_FULLTEXT_FORMAT_TOP", "Horn� HTML k�d");
define("L_FULLTEXT_FORMAT", "HTML k�d textu zpr�vy");
define("L_FULLTEXT_FORMAT_BOTTOM", "Spodn� HTML k�d");
define("L_A_FULLTEXT_TIT", "Spr�va web�ku - Vzhled jedn� zpr�vy");
define("L_FULLTEXT_HDR", "HTML k�d pro zobrazen� zpr�vy");
define("L_COMPACT_HDR", "HTML k�d pro p�ehled zpr�v");
define("L_ITEM_HDR", "Vstupn� formul�� zpr�vy");
define("L_A_ITEM_ADD", "P�idat zpr�vu");
define("L_A_ITEM_EDT", "Upravit zpr�vu");
define("L_IMP_EXPORT", "Povolit zas�l�n� zpr�v do web�ku:");
define("L_ADD_NEW_ITEM", "Nov� zpr�va");
define("L_DELETE_TRASH", "Vysypat ko�");
define("L_VIEW_FULLTEXT", "Zobraz zpr�vu");
define("L_FULLTEXT", "Cel� zpr�va");
define("L_HIGHLIGHTED", "D�le�it� zpr�va");
define("L_A_FIELDS_EDT", "Spr�va web�ku - Nastaven� pol�");
define("L_FIELDS_HDR", "Pole zpr�v");
define("L_NO_PS_EDIT_ITEMS", "Nem�te pr�vo upravovat zpr�vy v tomto web�ku");
define("L_NO_DELETE_ITEMS", "Nem�te pr�vo mazat zpr�vy");
define("L_NO_PS_MOVE_ITEMS", "Nem�te pr�vo p�esouvat zpr�vy");
define("L_FULLTEXT_OK", "Vzhled textu zpr�vy byl �sp�n� zm�n�n");

# aa toolkit common labels
# can be the same for all toolkit aplications
define("L_ACTIVE_BIN", "Aktu�ln�");
define("L_HOLDING_BIN", "Z�sobn�k");
define("L_TRASH_BIN", "Ko�");

define("L_CATEGORY","Kategorie");
define("L_SLICE_NAME", "Jm�no");
define("L_DELETED", "Vymaz�n");
define("L_D_LISTLEN", "Po�et vypisovan�ch zpr�v");  // slice
define("L_ERR_CANT_CHANGE", "Nepoda�ilo se zm�nit nastaven� web�ku");
define("L_ODD_ROW_FORMAT", "Lich� z�znam");
define("L_EVEN_ROW_FORMAT", "Sud� z�znam");
define("L_EVEN_ODD_DIFFER", "Odli�n� HTML k�d pro sud� z�znamy");
define("L_CATEGORY_TOP", "Horn� HTML k�d pro kategorii");
define("L_CATEGORY_FORMAT", "Nadpis kategorie");
define("L_CATEGORY_BOTTOM", "Spodn� HTML k�d pro kategorii");
define("L_CATEGORY_SORT", "Se�a� zpr�vy v p�ehledu podle kategorie");
define("L_COMPACT_TOP", "Horn� HTML k�d");
define("L_COMPACT_BOTTOM", "Spodn� HTML k�d");
define("L_A_COMPACT_TIT", "Spr�va web�ku - Vzhled p�ehledu zpr�v");
define("L_A_FILTERS_TIT", "Spr�va web�ku - Filtry pro v�m�nu zpr�v");
define("L_FLT_SETTING", "Nastaven� filtr� pro p��jem zpr�v");
define("L_FLT_FROM_SL", "Filtr pro p��jem zpr�v z web�ku");
define("L_FLT_FROM", "Z");
define("L_FLT_TO", "Do");
define("L_FLT_APPROVED", "Jako aktu�ln� zpr�vu");
define("L_FLT_CATEGORIES", "Kategorie");
define("L_ALL_CATEGORIES", "V�echny kategorie");
define("L_FLT_NONE", "Nen� vybr�na ��dn� vstupn� kategorie!");
define("L_THE_SAME", "-- stejn� --");
define("L_EXPORT_TO_ALL", "Povol exportovat zpr�vy do v�ech web�k�");

define("L_IMP_EXPORT_Y", "Zas�l�n� povoleno");
define("L_IMP_EXPORT_N", "Zas�l�n� zak�z�no");
define("L_IMP_IMPORT", "P�ij�mat zpr�vy z:");
define("L_IMP_IMPORT_Y", "P�ij�mat");
define("L_IMP_IMPORT_N", "Nep�ij�mat");
define("L_CONSTANTS_HLP", "Pou�ij n�sleduj�t� aliasy datab�zov�ch pol�");

define("L_ERR_IN", "Chyba v");
define("L_ERR_NEED", "mus� b�t vypln�no");
define("L_ERR_LOG", "pou�ijte znaky a-z, A-Z a 0-9");
define("L_ERR_LOGLEN", "mus� b�t dlouh� 5 - 32 znak�");
define("L_ERR_NO_SRCHFLDS", "Nebylo zad�no prohled�van� pole!");

define("L_FIELDS", "Pol��ka");
define("L_EDIT", "Editace");
define("L_DELETE", "Vymazat");
define("L_REVOKE", "Odstranit");
define("L_UPDATE", "Zm�nit");
define("L_RESET", "Vymazat formul��");
define("L_CANCEL", "Zru�it");
define("L_ACTION", "Akce");
define("L_INSERT", "Vlo�it");
define("L_NEW", "Nov�");
define("L_GO", "OK");
define("L_ADD", "P�idat");
define("L_USERS", "U�ivatel�");
define("L_GROUPS", "Skupiny");
define("L_SEARCH", "Hled�n�");
define("L_DEFAULTS", "Default");
define("L_SLICE", "Web�k");
define("L_DELETED_SLICE", "Nebyl nalezen ��dn� web�k, ke kter�mu m�te p��stup");
define("L_A_NEWUSER", "Nov� u�ivatel v syst�mu");
define("L_NEWUSER_HDR", "Nov� u�ivatel");
define("L_USER_LOGIN", "U�ivatelsk� jm�no");
define("L_USER_PASSWORD1", "Heslo");
define("L_USER_PASSWORD2", "Potvrdit heslo");
define("L_USER_FIRSTNAME", "Jm�no");
define("L_USER_SURNAME", "P��jmen�");
define("L_USER_MAIL", "E-mail");
define("L_USER_SUPER", "Superadmin");
define("L_A_USERS_TIT", "Spr�va web�ku - U�ivatel�");
define("L_A_PERMISSIONS", "Spr�va web�ku - P��stupov� pr�va");
define("L_A_ADMIN", "Spr�va web�ku - Vzhled Administrace");
define("L_A_ADMIN_TIT", L_A_ADMIN);
define("L_ADMIN_FORMAT", "HTML k�d pro zobrazen� zpr�vy");
define("L_ADMIN_FORMAT_BOTTOM", "Spodn� HTML");
define("L_ADMIN_FORMAT_TOP", "Horn� HTML");
define("L_ADMIN_HDR", "V�pis zpr�v v administrativn�ch str�nk�ch");
define("L_ADMIN_OK", "Vzheld administrativn�ch st�nek �sp�n� zm�n�n");
define("L_ADMIN_REMOVE", "Odstra�ovan� �et�zce");

define("L_ROLE_AUTHOR", "Autor");
define("L_ROLE_EDITOR", "Editor");
define("L_ROLE_ADMINISTRATOR", "Administr�tor");
define("L_ROLE_SUPER", "Super");

define("L_MAIN_SET", "Hlavn� nastaven�");
define("L_PERMISSIONS", "Nastaven� pr�v");
define("L_PERM_CHANGE", "Zm�nit");
define("L_PERM_ASSIGN", "P�idat");
define("L_PERM_NEW", "Hledej u�ivatele nebo skupinu");
define("L_PERM_SEARCH", "P�i�azen� nov�ch pr�v");
define("L_PERM_CURRENT", "Zm�na sou�asn�ch pr�v");
define("L_USER_NEW", "Nov� u�ivatel");
define("L_DESIGN", "Vzhled");
define("L_COMPACT", "P�ehled zpr�v");
define("L_COMPACT_REMOVE", "Odstra�ovan� �et�zce");
define("L_FEEDING", "V�m�na zpr�v");
define("L_IMPORT", "Zas�l�n� & P��jem");
define("L_FILTERS", "Filtry");

define("L_A_SLICE_ADD", "Spr�va web�ku - P�id�n� web�ku");
define("L_A_SLICE_EDT", "Spr�va web�ku - �prava web�ku");
define("L_A_SLICE_CAT", "Spr�va web�ku - Nastaven� kategori�");
define("L_A_SLICE_IMP", "Spr�va web�ku - V�m�na zpr�v");
define("L_FIELD", "Polo�ka");
define("L_FIELD_IN_EDIT", "Zobrazit");
define("L_NEEDED_FIELD", "Povinn�");
define("L_A_SEARCH_TIT", "Spr�va web�ku - Vyhled�vac� formul��");
define("L_SEARCH_HDR", "Vyhled�vac� krit�ria");
define("L_SEARCH_HDR2", "Vyhled�vat v polo�k�ch");
define("L_SEARCH_SHOW", "Zobrazit");
define("L_SEARCH_DEFAULT", "Standardni nastaven�");
define("L_SEARCH_SET", "Vyhled�vac� formul��");
define("L_AND", "AND");
define("L_OR", "OR");
define("L_SRCH_KW", "Search for");
define("L_SRCH_FROM", "From");
define("L_SRCH_TO", "To");
define("L_SRCH_SUBMIT", "Search");
define("L_NO_PS_EDIT", "Nem�te pr�vo upravovat tento web�k");
define("L_NO_PS_ADD", "Nem�te pr�vo p�id�vat web�k");
define("L_NO_PS_COMPACT", "Nem�te pr�vo m�nit vzhled p�ehledu zpr�v");
define("L_NO_PS_FULLTEXT", "Nem�te pr�vo m�nit vzhled v�pisu zpr�vy");
define("L_NO_PS_CATEGORY", "Nem�te pr�vo m�nit nastaven� kategori�");
define("L_NO_PS_FEEDING", "Nem�te pr�vo m�nit nastaven� v�m�ny zpr�v");
define("L_NO_PS_USERS", "Nem�te pr�vo ke spr�v� u�ivatel�");
define("L_NO_PS_FIELDS", "Nem�te pr�vo m�nit nastaven� polo�ek");
define("L_NO_PS_SEARCH", "Nem�te pr�vo m�nit nastaven� vyhled�v�n�");

define("L_BAD_RETYPED_PWD", "Vypln�n� hesla si neodpov�daj�");
define("L_ERR_USER_ADD", "Nepoda�ilo se p�idat u�ivatele do syst�mu - chyba LDAP");
define("L_NEWUSER_OK", "U�ivatel byl �sp�n� p�id�n do syst�mu");
define("L_COMPACT_OK", "Vzhled p�ehledu zpr�v byl �sp�n� zm�n�m");
define("L_BAD_ITEM_ID", "�patn� ��slo zpr�vy");
define("L_ALL", " - v�e - ");
define("L_CAT_LIST", "Kategorie zpr�v");
define("L_CAT_SELECT", "Kategorie v tomto web�ku");
define("L_NEW_SLICE", "Nov� web�k");
define("L_ASSIGN", "P�i�adit");
define("L_CATBINDS_OK", "Nastaven� kategori� bylo �sp�n� zm�n�no");
define("L_IMPORT_OK", "Nastaven� v�m�ny zpr�v �sp�n� zm�n�no");
define("L_FIELDS_OK", "Nasaven� polo�ek �sp�n� zm�n�no");
define("L_SEARCH_OK", "Nastaven� vyhled�vac�ho formul��e �sp�n� zm�n�no");
define("L_NO_CATEGORY", "Kategorie nebyly definov�ny");
define("L_NO_IMPORTED_SLICE", "Nen� nastaven ��dn� web�k, ze kter�ho se maj� p�ij�mat zpr�vy");
define("L_NO_USERS", "U�ivatel (skupina) nenalezena");

define("L_TOO_MUCH_USERS", "Nalezeno p��li� mnoho u�ivatel� �i skupin.");
define("L_MORE_SPECIFIC", "Zkuste zadat p�esn�j�� �daje.");
define("L_REMOVE", "Odstranit");
define("L_ID", "Id");
define("L_SETTINGS", "Nastaven�");
define("L_LOGO", "APC toolkit");
define("L_USER_MANAGEMENT", "U�ivatel�");
define("L_ITEMS", "Spr�va p��sp�vk�");
define("L_NEW_SLICE_HEAD", "Nov� web�k");
define("L_ERR_USER_CHANGE", "Nelze zm�nit data u�ivatele - LDAP Error");
define("L_PUBLISHED", "Zve�ejn�no");
define("L_EXPIRED", "Vypr�eno");
define("L_NOT_PUBLISHED", "Dosud nepublikov�no");
define("L_EDIT_USER", "Editace u�ivatele");
define("L_EDITUSER_HDR", L_EDIT_USER);

define("NO_SOURCE_URL", "javascript: window.alert('Nebylo zad�no url zdroje')"); 
define("NO_OUTER_LINK_URL", "#top");

# editor interface constants
define("L_PUBLISHED_HEAD", "PUB");
define("L_HIGHLIGHTED_HEAD", "!");
define("L_FEEDED_HEAD", "ciz�");
define("L_MORE_DETAILS", "V�ce podrobnost�");
define("L_LESS_DETAILS", "M�n� podrobnost�");
define("L_UNSELECT_ALL", "Zru�it v�b�r");
define("L_SELECT_VISIBLE", "Vybrat zobrazen�");
define("L_UNSELECT_VISIBLE", "Zru�it v�b�r");

define("L_SLICE_ADM","Administrace web�ku - Menu");
define("L_A_FILTERS_FLT", L_A_FILTERS_TIT);
define("L_A_COMPACT", L_A_COMPACT_TIT);
define("L_A_FULLTEXT", L_A_FULLTEXT_TIT);
define("L_SRCH_ALL", L_ALL);
define("L_SRCH_SLICE", L_SLICE);
define("L_SRCH_CATEGORY", L_CATEGORY);
define("L_SRCH_AUTHOR", L_CREATED_BY);
define("L_SRCH_LANGUAGE", L_LANGUAGE_CODE);
define("L_SRCH_HEADLINE", L_HEADLINE);
define("L_SRCH_ABSTRACT", L_ABSTRACT);
define("L_SRCH_FULL_TEXT", L_FULL_TEXT);
define("L_SRCH_EDIT_NOTE", L_EDIT_NOTE);
define("L_SLICES_HDR", L_SLICE);
define("L_A_SEARCH_EDT", L_A_SEARCH_TIT);
define("L_A_SLICE_TIT", L_SLICE_ADM);
define("L_A_FIELDS_TIT", L_A_FIELDS_EDT);
define("L_SLICE_SET", L_SLICE);
define("L_FULLTEXT_REMOVE", L_COMPACT_REMOVE);

define("L_FEED", "V�m�na zpr�v");
define("L_FEEDTO_TITLE", "P�edat zpr�vu do web�ku");
define("L_FEED_TO", "P�edat vybran� zpr�vy do zvolen�ch web�ku");
define("L_NO_PERMISSION_TO_FEED", "Nelze");
define("L_NO_PS_CONFIG", "Nem�te pr�vo nastavovat configura�n� parametry tohoto web�ku");
define("L_SLICE_CONFIG", "Administrace");
define("L_CHBOX_HEAD", "&nbsp;");   // title of checkbox in admin interface
define("L_CHBOX", "Checkbox"); 
define("L_CATNAME", "Kategorie");
define("L_CATEGORY_ID", "ID kategorie");
define("L_EDITED_BY","Editov�no");
define("L_MASTER_ID", "ID zdrojov�ho web�ku");
define("L_CHANGE_MARKED", "Zm�nit vybran�");
define("L_MOVE_TO_ACTIVE_BIN", "Vystavit");
define("L_MOVE_TO_HOLDING_BIN", "Poslat do z�sobn�ku");
define("L_MOVE_TO_TRASH_BIN", "Poslat do ko�e");
define("L_OTHER_ARTICLES", "Ostatn� zpr�vy");
define("L_MISC", "P��kazy");
define("L_HEADLINE_EDIT", "Nadpis (editace po kliknut�)");
define("L_HEADLINE_PREVIEW", "Nadpis (preview po kliknut�)");
define("L_EDIT_LINK", "&nbsp;");
define("L_PREVIEW_LINK", "&nbsp;");
define("L_ARTICLE_MANAGER", "Spr�va zpr�v");
define("L_SWITCH_TO", "Web�k:");
define("L_ADMIN", "Administrace");

define("L_NO_PS_NEW_USER", "Nem�te pr�vo vytvo�it u�ivatele");
define("L_ALL_GROUPS", "V�echny skupiny");
define("L_USERS_GROUPS", "U�ivatelovy skupiny");
define("L_REALY_DELETE_USER", "Opravdu chcete vymazat dan�ho u�ivatele ze syst�mu?");
define("L_REALY_DELETE_GROUP", "Opravdu chcete vymazat danou skupinu ze syst�mu?");
define("L_TOO_MUCH_GROUPS", "Too much groups found.");
define("L_NO_GROUPS", "Skupina nenalezena");
define("L_GROUP_NAME", "Jm�no");
define("L_GROUP_DESCRIPTION", "Popis");
define("L_GROUP_SUPER", "Administrativn� skupina");
define("L_ERR_GROUP_ADD", "Nelze p�idat skupinu do syst�mu");
define("L_NEWGROUP_OK", "Skupina byla �sp�n� p�id�na");
define("L_ERR_GROUP_CHANGE", "Nelze zm�nit skupinu");
define("L_A_UM_USERS_TIT", "Spr�va u�ivatel� - U�ivalel�");
define("L_A_UM_GROUPS_TIT", "Spr�va u�ivatel� - Skupiny");
define("L_EDITGROUP_HDR", "Editace skupiny");
define("L_NEWGROUP_HDR", "Nov� skupina");
define("L_GROUP_ID", "ID skupiny");
define("L_ALL_USERS", "V�ichni u�ivatel�");
define("L_GROUPS_USERS", "U�ivatel� ve skupin�");
define("L_POST", "Poslat");
define("L_POST_PREV", "Poslat a prohl�dnout");
define("L_INSERT_PREV", "Vlo�it & Prohl�dnout");
define("L_OK", "OK");
define("L_ACTIVE_BIN_EXPIRED", "Aktu�ln� - Expirovan�");
define("L_ACTIVE_BIN_PENDING", "Aktu�ln� - P�ipraven�");
define("L_ACTIVE_BIN_EXPIRED_MENU", "... expirovan�");
define("L_ACTIVE_BIN_PENDING_MENU", "... p�ipraven�");
 
define("L_FIELD_PRIORITY", "�azen�");
define("L_FIELD_TYPE", "Typ");
define("L_CONSTANTS", "Hodnoty");
define("L_DEFAULT", "Default");
define("L_DELETE_FIELD", "Opravdu chcete vymazat toto pole z web�ku?");
define("L_FEEDED", "P�ejato");
define("L_HTML_DEFAULT", "defaultn� pou��t HTML k�d");
define("L_HTML_SHOW", "Zobrazit volbu 'HTML' / 'prost� text'");
define("L_NEW_OWNER", "Nov� vlastn�k");
define("L_NEW_OWNER_EMAIL", "E-mail nov�ho vlastn�ka");
define("L_NO_FIELDS", "V tomto web�ku nejsou definov�na ��dn� pole (co� je divn�)");
define("L_NO_FIELD", "");
define("L_NO_PERM_TO_FEED", "Nem�te pr�vo nastavit v�m�nu zpr�v s ��dn�m web�kem");
define("L_NO_SLICES", "��dn� web�k");
define("L_NO_TEMPLATES", "��dn� �ablona");
define("L_OWNER", "Vlastn�k");
define("L_SLICES", "Web�ky");
define("L_TEMPLATE", "�ablona");
define("L_VALIDATE", "Zkontrolovat");

define("L_FIELD_DELETE_OK", "Pole odstran�no");

define("L_WARNING_NOT_CHANGE","<p>POZOR: Tato nastaven� by m�l m�nit jen ten, kdo v� co d�l�!</p>");
define("L_INPUT_SHOW_FUNC_F_HLP","Funkce, kter� se pou�ije pro zobrazen� pole ve vstupn�m formul��i. N�kter� pou��vaj� Konstanty, n�kter� pou��vaj� Parametry. V�ce informac� se dozv�te, kdy� pou�ijete Pr�vodce s N�pov�dou.");
define("L_INPUT_SHOW_FUNC_C_HLP","Vyberte Skupinu Konstant nebo Web�k.");
define("L_INPUT_SHOW_FUNC_HLP","Parametry jsou odd�leny dvojte�kou (:) nebo (ve speci�ln�ch p��padech) apostrofem (').");
define("L_INPUT_DEFAULT_F_HLP","Funkce, kter� se pou�ije pro generov�n� defaultn�ch hodnot pole:<BR>Now - aktu�ln� datum<BR>User ID - identifik�tor p�ihl�en�ho u�ivatele<BR>Text - text uveden� v poli Parametr<br>Date - aktu�ln� datum plus <Parametr> dn�");
define("L_INPUT_DEFAULT_HLP","Parametr pro defauln� hodnoty Text a Date (viz v��e)");

define("L_INPUT_DEFAULT_TXT", "Text");
define("L_INPUT_DEFAULT_DTE", "Date");
define("L_INPUT_DEFAULT_UID", "User ID");
define("L_INPUT_DEFAULT_NOW", "Now");
define("L_INPUT_DEFAULT_VAR", "Variable"); # Added by Ram on 5th March 2002

define("L_INPUT_SHOW_TXT","Text Area");
define("L_INPUT_SHOW_EDT","Rich Edit Text Area");
define("L_INPUT_SHOW_FLD","Text Field");
define("L_INPUT_SHOW_SEL","Select Box");
define("L_INPUT_SHOW_RIO","Radio Button");
define("L_INPUT_SHOW_DTE","Date");
define("L_INPUT_SHOW_CHB","Check Box");
define("L_INPUT_SHOW_MCH", "Multiple Checkboxes");
define("L_INPUT_SHOW_MSE", "Multiple Selectbox");
define("L_INPUT_SHOW_FIL","File Upload");
define("L_INPUT_SHOW_ISI","Related Item Select Box");
define("L_INPUT_SHOW_ISO","Related Item Window");
define("L_INPUT_SHOW_WI2","Two Boxes");
define("L_INPUT_SHOW_PRE","Select Box with Presets");
define("L_INPUT_SHOW_NUL","Do not show");
                              
define("L_INPUT_VALIDATE_TEXT","Text");
define("L_INPUT_VALIDATE_URL","URL");
define("L_INPUT_VALIDATE_EMAIL","E-mail");
define("L_INPUT_VALIDATE_NUMBER","Number");
define("L_INPUT_VALIDATE_ID","Id");
define("L_INPUT_VALIDATE_DATE","Date");
define("L_INPUT_VALIDATE_BOOL","Boolean");

define("L_INPUT_INSERT_QTE","Text");
define("L_INPUT_INSERT_DTE","Date");
define("L_INPUT_INSERT_CNS","Constant");
define("L_INPUT_INSERT_NUM","Number");
define("L_INPUT_INSERT_IDS","Item IDs");
define("L_INPUT_INSERT_BOO","Boolean");
define("L_INPUT_INSERT_UID","User ID");
define("L_INPUT_INSERT_NOW","Now");
define("L_INPUT_INSERT_FIL","File");
define("L_INPUT_INSERT_NUL","None");

define("L_INPUT_DEFAULT","Default");
define("L_INPUT_BEFORE","HTML k�d p�ed t�mto polem");
define("L_INPUT_BEFORE_HLP","HTML k�d, kter� se zobraz� ve vstupn�m formul��i p�ed t�mto polem");
define("L_INPUT_FUNC","Typ Vstupu");
define("L_INPUT_HELP","N�pov�da");
define("L_INPUT_HELP_HLP","N�pov�da zobrazen� pro toto pole ve vstupn�m formul��i");
define("L_INPUT_MOREHLP","V�ce informac�");
define("L_INPUT_MOREHLP_HLP","N�pov�da, kter� se zobraz� po stisku '?' ve vstupn�m formul��i");
define("L_INPUT_INSERT_HLP","Zp�sob ulo�en� do datab�ze");
define("L_INPUT_VALIDATE_HLP","Funkce pro kontrolu vstupu (validace)");

define("L_CONSTANT_NAME", "Jm�no");
define("L_CONSTANT_VALUE", "Hodnota");
define("L_CONSTANT_PRIORITY", "�azen�");
define("L_CONSTANT_PRI", "�azen�");
define("L_CONSTANT_GROUP", "Skupina hodnot");
define("L_CONSTANT_GROUP_EXIST", "Tato skupina hodnot ji� existuje");
define("L_CONSTANTS_OK", "Zm�na hodnot �sp�n� provedena");
define("L_A_CONSTANTS_TIT", "Spr�va web�ku - Nastaven� hodnot");
define("L_A_CONSTANTS_EDT", "Spr�va web�ku - Nastaven� hodnot");
define("L_CONSTANTS_HDR", "Hodnoty");
define("L_CONSTANT_NAME_HLP", "zobrazeno&nbsp;ve&nbsp;vstupn�m&nbsp;formul��i");
define("L_CONSTANT_VALUE_HLP", "ulo�eno&nbsp;v&nbsp;datab�zi");
define("L_CONSTANT_PRI_HLP", "Po�ad�&nbsp;hodnot");
define("L_CONSTANT_CLASS", "Nadkategorie");
define("L_CONSTANT_CLASS_HLP", "jen&nbsp;pro&nbsp;kategorie");
define("L_CONSTANT_DEL_HLP", "Pro odstran�n� kartegorie vyma�te jej� jm�no");

$L_MONTH = array( 1 => 'Leden', '�nor', 'B�ezen', 'Duben', 'Kv�ten', '�erven', 
		'�ervenec', 'Srpen', 'Z���', '��jen', 'Listopad', 'Prosinec');

define("L_NO_CATEGORY_FIELD","Pole kategorie nen� v tomto web�ku definov�no.<br>  P�idejte pole kategorie do web�ku na str�nce Pol��ka.");
define("L_PERMIT_ANONYMOUS_POST","Anonymn� vkl�d�n�");
define("L_PERMIT_OFFLINE_FILL","Off-line pln�n�");
define("L_SOME_CATEGORY", "<kategorie>");

define("L_ALIASES", "Aliasy pro pol��ka v datab�zi");
define("L_ALIAS1", "Alias 1"); 
define("L_ALIAS_HLP", "Mus� za��nat znaky \"_#\".<br>Alias mus� b�t p�esn� 10 znak� dlouh� v�etn� \"_#\".<br>M�l by b�t kapit�lkami."); 
define("L_ALIAS_FUNC", "Funkce"); 
define("L_ALIAS_FUNC_F_HLP", "Funkce, kter� zajist� zobrazen� pol��ka na str�nce"); 
define("L_ALIAS_FUNC_HLP", "Dopl�kov� parametr p�ed�van� zobrazovac� funkci. Podrobnosti viz include/item.php3 file"); 
define("L_ALIAS_HELP", "N�pov�da"); 
define("L_ALIAS_HELP_HLP", "N�pov�dn� text�k pro tento alias"); 
define("L_ALIAS2", "Alias 2"); 
define("L_ALIAS3", "Alias 3"); 

define("L_TOP_HLP", "HTML k�d, kter� se zobraz� p�ed k�dem web�ku");
define("L_FORMAT_HLP", "Sem pat�� HTML k�d v kombinaci s aliasy uveden�mi dole na str�nce
                     <br>Aliasy budou v okam�iku zobrazov�n� na web nahrazeny skute�n�mi hodnotami z datab�ze");
define("L_BOTTOM_HLP", "HTML k�d, kter� se zobraz� za vlasn�m k�dem web�ku");
define("L_EVEN_ROW_HLP", "TIP: Rozli�en�m sud�ch a lich�ch z�znam� lze doc�lit nap��klad odli�en� ��dk� jin�mi barvami pozad�
                         - prvn� t�eba zelen�, druh� �lut�, atd.");

define("L_SLICE_URL", "URL web�ku");
define( "L_BRACKETS_ERR", "Brackets doesn't match in query: ");
define("L_A_SLICE_ADD_HELP", "Nov� web�k m��ete vytvo�it na z�klad� �ablony, nebo zkop�rovat nastaven� z ji� existuj�c�ho web�ku (vytvo�� se p�esn� kopie v�etn� nastaven� .");
define("L_REMOVE_HLP", "Odstran� pr�zdn� z�vorky atd. Pou�ijte ## jako odd�lova�.");

define("L_COMPACT_HELP", "Na t�to str�nce lze nastavit, co se objev� na str�nce p�ehledu zpr�v");
define("L_A_FULLTEXT_HELP", "Na t�to str�nce lze nastavit, co se objev� na str�nce p�i prohl�en� t�la zpr�vy");
define("L_PROHIBITED", "Zak�z�no");
define("L_HTML", "HTML");
define("L_PLAIN_TEXT", "Prost� text");
define("L_A_DELSLICE", "Spr�va web�ku - Vymaz�n� web�ku");
define("L_DELSLICE_TIT", L_A_DELSLICE);
define("L_DELSLICE_HDR", "Vyber web�k pro smaz�n�");
define("L_DEL_SLICE_HLP","<p>Lze vymazat jen web�ky, kter� byly ozna�eny pro vymaz�n� na str�nce &quot;<b>". L_SLICE_SET ."</b>&quot;</p>");
define("L_A_DELSLICE", L_DELSLICE_TIT);
define("L_DELETE_SLICE", "Opravdu chcete vymazat tento web�k a v�echny jeho zpr�vy?");
define("L_NO_SLICE_TO_DELETE", "��dn� web�k nebyl ozna�en za vymazan�");
define("L_NO_SUCH_SLICE", "�patn� ��slo web�ku");
define("L_NO_DELETED_SLICE", "Web�k nen� ozna�en za vymazan�");
define("L_DELSLICE_OK", "Web�k byl vymaz�n, tabulky byly optimalizov�ny");
define("L_DEL_SLICE", "Smazat Web�k");
define("L_FEED_STATE", "Sd�len� tohoto pole");
define("L_STATE_FEEDABLE", "Kop�rovat obsah" );
define("L_STATE_UNFEEDABLE", "Nekopirovat" );
define("L_STATE_FEEDNOCHANGE", "Kop�rovat nem�niteln�" );
define("L_INPUT_FEED_MODES_HLP", "M� se kop�rovat obsah tohoto pol��ka do dal��ch web�k� p�i v�m�n� zpr�v mezi web�ky?");
define("L_CANT_CREATE_IMG_DIR","Nelze vytvo�it adres�� pro obr�zky");

  # constants for View setting 
define('L_VIEWS','Pohledy');
define('L_ASCENDING','Vzestupn�');
define('L_DESCENDING','Sestupn�');
define('L_NO_PS_VIEWS','Nem�te pr�vo m�nit pohledy');
define('L_VIEW_OK','Pohled byl �sp�n� zm�n�n');
define('L_A_VIEW_TIT','Spr�va web�ku - definice Pohledu');
define('L_A_VIEWS','Spr�va web�ku - definice Pohledu');
define('L_VIEWS_HDR','Definovan� pohledy');
define('L_VIEW_DELETE_OK','Pohled by �sp�n� smaz�n');
define('L_DELETE_VIEW','Opravdu chcete smazat vybran� pohled?');
define('L_V_BEFORE',L_COMPACT_TOP);
define('L_V_ODD',L_ODD_ROW_FORMAT);
define('L_V_EVENODDDIF',L_EVEN_ODD_DIFFER);
define('L_V_EVEN',L_EVEN_ROW_FORMAT);
define('L_V_AFTER',L_COMPACT_BOTTOM);
define('L_V_GROUP_BY1','Seskupit dle');
define('L_V_GROUP1DIR',' ');
define('L_V_GROUP_BY2',L_V_GROUP_BY1);
define('L_V_GROUP2DIR',' ');
define('L_V_GROUP','Nadpis skupiny');
define('L_V_REMOVE_STRING',L_COMPACT_REMOVE);
define('L_V_MODIFICATION','Typ');
define('L_V_PARAMETER','Parametr');
define('L_V_IMG1','Obr�zek 1');
define('L_V_IMG2','Obr�zek 2');
define('L_V_IMG3','Obr�zek 3');
define('L_V_IMG4','Obr�zek 4');
define('L_V_ORDER1','Se�adit');
define('L_V_ORDER1DIR',' ');
define('L_V_ORDER2','Se�adit druhotn�');
define('L_V_ORDER2DIR',' ');
define('L_V_SELECTED','Pou��t vybran� �l�nek');
define('L_V_COND1FLD','Podm�nka 1');
define('L_V_COND1OP',' ');
define('L_V_COND1COND',' ');
define('L_V_COND2FLD','Podm�nka 2');
define('L_V_COND2OP',' ');
define('L_V_COND2COND',' ');
define('L_V_COND3FLD','Podm�nka 3');
define('L_V_COND3OP',' ');
define('L_V_COND3COND',' ');
define('L_V_LISTLEN',L_D_LISTLEN);
define('L_V_FLAG','Flag');
define('L_V_SCROLLER','Zobrazit rolov�n� str�nek');
define('L_V_ADITIONAL','Dal��');
define('L_COMPACT_VIEW','P�ehled');
define('L_FULLTEXT_VIEW','�l�nek');
define('L_DIGEST_VIEW','Obsah - souhrn');
define('L_DISCUSSION_VIEW','Diskuse');
define('L_RELATED_VIEW','Souvisej�c� zpr�vy');
define('L_CONSTANT_VIEW','Zobrazen� konstant');
define('L_RSS_VIEW','V�m�na zpr�v RSS');
define('L_STATIC_VIEW','Statick� str�nka');
define('L_SCRIPT_VIEW','Javscript');

define("L_MAP","Mapov�n�");
define("L_MAP_TIT","Spr�va web�ku - v�m�na zpr�v - mapov�n� pol�");
define("L_MAP_FIELDS","Mapov�n� pol�");
define("L_MAP_TABTIT","V�m�na zpr�v - mapov�n� pol�");
define("L_MAP_FROM_SLICE","Mapov�n� z web�ku");
define("L_MAP_FROM","Z");
define("L_MAP_TO","Do");
define("L_MAP_DUP","Nelze mapovat do stejn�ho pole");
define("L_MAP_NOTMAP","-- Nemapovat --");
define("L_MAP_OK","Nastaven� mapov�n� pol� �sp�n� zm�n�no");
    
define("L_STATE_FEEDABLE_UPDATE", "Kop�rovat obsah a zm�ny" );
define("L_STATE_FEEDABLE_UPDATE_LOCKED", "Kop�rovat obsah a zm�ny nem�niteln�");

define("L_SITEM_ID_ALIAS",'alias pro zkr�cen� ��slo �l�nku');
define("L_MAP_VALUE","-- Hodnota --");
define("L_MAP_VALUE2","Hodnota");
define("L_ORDER", "Se�adit");
define("L_INSERT_AS_NEW","Vlo�it jako nov�");

// constnt view constants
define("L_CONST_NAME_ALIAS", "Jm�no");
define("L_CONST_VALUE_ALIAS", "Hodnota");
define("L_CONST_PRIORITY_ALIAS", "�azen�");
define("L_CONST_GROUP_ALIAS", "Skupina hodnot");
define("L_CONST_CLASS_ALIAS", "Nadkategorie (pou�iteln� jen pro kategorie)");
define("L_CONST_COUNTER_ALIAS", "Po�adov� ��slo hodnoty");
define("L_CONST_ID_ALIAS", "Identifika�n� ��slo hodnoty");

define('L_V_CONSTANT_GROUP','Skupina hodnot');
define("L_NO_CONSTANT", "Hodnota nenalezena");

// Discussion constants.
define("L_DISCUS_SEL","Zobrazit diskusi");
define("L_DISCUS_EMPTY"," -- ��dn� -- ");
define("L_DISCUS_HTML_FORMAT","Diskusi form�tovat v HTML");
define("L_EDITDISC_ALIAS","Alias pou��van� v administrativn�ch str�nk�ch index.php3 pro URL discedit.php3");

define("L_D_SUBJECT_ALIAS","Alias pro p�edm�t p��sp�vku");
define("L_D_BODY_ALIAS"," Alias pro text p��sp�vku");
define("L_D_AUTHOR_ALIAS"," Alias pro autora p��sp�vku");
define("L_D_EMAIL_ALIAS","Alias pro e-mail autora");
define("L_D_WWWURL_ALIAS","Alias pro adresu WWW str�nek autora ");
define("L_D_WWWDES_ALIAS","Alias for popis WWW str�nek autora");
define("L_D_DATE_ALIAS","Alias pro datum a �as posl�n� p��sp�vku");
define("L_D_REMOTE_ADDR_ALIAS","Alias pro IP adresu autorova po��ta�e");
define("L_D_URLBODY_ALIAS","Alias pro odkaz na text p��sp�vku<br>
                             <i>U�it�: </i>v k�du pro p�ehledov� zobrazen� p��sp�vku<br>
                             <i>P��klad: </i>&lt;a href=_#URL_BODY>_#SUBJECT#&lt;/a>");
define("L_D_CHECKBOX_ALIAS","Alias pro checkbox pro vybr�n� p��sp�vku");
define("L_D_TREEIMGS_ALIAS","Alias pro obr�zky");
define("L_D_ALL_COUNT_ALIAS","Alias pro po�et v�ech p��sp�vk� k dan�mu �l�nku");
define("L_D_APPROVED_COUNT_ALIAS","Alias pro po�et schv�len�ch p��sp�vk� k dan�mu �l�nku");
define("L_D_URLREPLY_ALIAS","Alias pro odkaz na formul��<br>
                             <i>U�it�: </i>v k�du pro pln� zn�n� p��sp�vku<br>
                             <i>P��klad: </i>&lt;a href=_#URLREPLY&gt;Odpov�d�t&lt;/a&gt;");
define("L_D_URL","Alias pro odkaz na diskusi<br>
                             <i>U�it�: </i>v k�du formul��e<br>
                             <i>P��klad: </i>&lt;input type=hidden name=url value=\"_#DISC_URL\">");
define("L_D_ID_ALIAS"," Alias pro ��slo p��sp�vku<br>
                             <i>U�it�: </i>v k�du formul��e<br>
                             <i>P��klad: </i>&lt;input type=hidden name=d_parent value=\"_#DISC_ID#\">");
define("L_D_ITEM_ID_ALIAS"," Alias pro ��slo �l�nku<br>
                             <i>U�it�: </i>v k�du formul��e<br>
                             <i>P��klad: </i>&lt;input type=hidden name=d_item_id value=\"_#ITEM_ID#\">");

define("L_D_BUTTONS","Alias pro tla��tka Zobraz v�e, Zobraz vybran�, P�idej nov�<br>
                             <i>U�it�: </i>ve spodn�m HTML k�du");

define("L_D_COMPACT" , "HTML k�d pro p�ehledov� zobrazen� p��sp�vku");
define("L_D_SHOWIMGS" , "Zobrazit obr�zky");
define("L_D_ORDER" , "Se�adit");
define("L_D_FULLTEXT" ,"HTML k�d pro pln� zn�n� p��sp�vku");

define("L_D_ADMIN","Spr�va zpr�v - Spr�va diskusn�ch p��sp�vk�");
define("L_D_NODISCUS","��dn� diskusn� p��sp�vky");
define("L_D_TOPIC","Titulek");
define("L_D_AUTHOR","Autor");
define("L_D_DATE","Datum");
define("L_D_ACTIONS","Akce");
define("L_D_DELETE","Smazat");
define("L_D_EDIT","Editovat");
define("L_D_HIDE","Skr�t");
define("L_D_APPROVE","Schv�lit");

define("L_D_EDITDISC","Spr�va zpr�v - Spr�va diskusn�ch p��sp�vk� - Editace p��sp�vku");
define("L_D_EDITDISC_TABTIT","Editace p��sp�vku");
define("L_D_SUBJECT","P�edm�t");
define("L_D_AUTHOR","Autor");
define("L_D_EMAIL","E-mail");
define("L_D_BODY","Text p��sp�vku");
define("L_D_URL_ADDRESS","WWW autora - URL");
define("L_D_URL_DES","WWW autora - popis");
define("L_D_REMOTE_ADDR","IP adresa po��ta�e autora");

define('L_D_SELECTED_NONE',"Nebyl vybr�n ��dn� p��sp�vek");
define("L_D_DELETE_COMMENT","P�ejete si smazat p��sp�vek?");

define("L_D_FORM","HTML k�d formul��e pro posl�n� p��sp�vku");
define("L_D_BACK","Zp�t");
define("L_D_ITEM","�l�nek: ");
define("L_D_ADMIN2","Spr�va diskusn�ch p��sp�vk�");

define("L_D_SHOW_SELECTED","Zobraz vybran�");
define("L_D_SHOW_ALL","Zobraz v�e");
define("L_D_ADD_NEW","P�idej nov�");

define("L_TOO_MUCH_RELATED","Je vybr�no p��li� mnoho souvisej�c�ch �l�nk�.");
define("L_SELECT_RELATED","V�b�r souvisej�c�ch �l�nk�");
define("L_SELECT_RELATED_1WAY","Ano");
define("L_SELECT_RELATED_2WAY","Vz�jemn�");
define("L_SELECT_RELATED_BACK", "Zp�tn�");

// - Cross server networking --------------------------------------

define("L_INNER_IMPORT","Lok�ln� v�m�na");
define("L_INTER_IMPORT","P��jem z uzl�");
define("L_INTER_EXPORT","Zas�l�n� do uzl�");

define("L_NODES_MANAGER","Uzly");
define("L_NO_PS_NODES_MANAGER","Nem�te pr�va pro spr�vu uzl�");
define("L_NODES_ADMIN_TIT","Spr�va uzl�");
define("L_NODES_LIST","Seznam uzl�");
define("L_NODES_ADD_NEW","P�id�n� uzlu");
define("L_NODES_EDIT","Editace uzlu");
define("L_NODES_NODE_NAME","Jm�no uzlu ");
define("L_NODES_YOUR_NODE","Your node name");
define("L_NODES_SERVER_URL","URL souboru getxml.php3");
define("L_NODES_YOUR_GETXML","Your getxml is");
define("L_NODES_PASWORD","Heslo");
define("L_SUBMIT","Poslat");
define("L_NODES_SEL_NONE","Nebyl vybr�n uzel");
define("L_NODES_CONFIRM_DELETE","Opravdu chcete smazat uzel?");
define("L_NODES_NODE_EMPTY","Jm�no uzlu mus� b�t vypln�no");

define("L_IMPORT_TIT","Spr�va p�ij�man�ch web�k�");
define("L_IMPORT_LIST","Seznam p�ij�man�ch web�k� do web�ku ");
define("L_IMPORT_CONFIRM_DELETE","Opravdu chcete zru�it p��jem z tohoto web�ku?");
define("L_IMPORT_SEL_NONE","Nebyl zvolen web�k");
define("L_IMPORT_NODES_LIST","Seznam uzl�");
define("L_IMPORT_CREATE","P�ij�mat web�ky z tohoto uzlu");
define("L_IMPORT_NODE_SEL","Nebyl vybr�n uzel");
define("L_IMPORT_SLICES","Seznam p�ij�man�ch web�k�");
define("L_IMPORT_SLICES2","Seznam dostupn�ch web�k� z uzlu ");
define("L_IMPORT_SUBMIT","Zvolte web�k");
define("L_IMPORT2_OK","P��jem z web�ku �sp�n� vytvo�en");
define("L_IMPORT2_ERR","P��jem z web�ku byl ji� vytvo�en");

define("L_RSS_ERROR","Nepoda�ilo se nav�zat spojen� nebo p�ijmout data. Kontaktuje administr�tora");
define("L_RSS_ERROR2","Neplatn� heslo pro uzel: ");
define("L_RSS_ERROR3","Kontaktujte administr�tora lok�ln�ho uzlu.");
define("L_RSS_ERROR4","��dn� dostupn� web�ky. Nem�te pr�va p�ij�mat data z tohoto uzlu. ".
 "Kontaktujte administr�tora vzd�len�ho web�ku a zkontrolujte, �e obdr�el va�e spr�vn� u�ivatelsk� jm�no.");

define("L_EXPORT_TIT","Spr�va povolen� zas�l�n� web�k�");
define("L_EXPORT_CONFIRM_DELETE","Opravdu chcete zru�it povolen� zas�l�n� tohoto web�ku?");
define("L_EXPORT_SEL_NONE","Nebyl zvolen uzel a u�ivatel");
define("L_EXPORT_LIST","Seznam uzl� a u�ivatel�, kam bude zas�l�n web�k ");
define("L_EXPORT_ADD","P�idejte uzel a u�ivatele");
define("L_EXPORT_NAME","Jm�no u�ivatele");
define("L_EXPORT_NODES","Seznam uzl�");

define("L_RSS_TITL", "Jm�no web�ku pro RSS");
define("L_RSS_LINK", "Odkaz na web�k pro RSS");
define("L_RSS_DESC", "Kr�tk� popisek (vlastn�k a jm�no) web�ku pro RSS");
define("L_RSS_DATE", "Datum v RSS p�ehledu je generov�no v datov�m form�tu RSS");

define("L_NO_PS_EXPORT_IMPORT", "Nem�te pr�vo exportovat / importovat web�ky");
define("L_EXPORT_SLICE", "Export");
define("L_IMPORT_SLICE", "Import");
define("L_EXPIMP_SET", "Struktura web�ku");

define("L_E_EXPORT_TITLE", "Export struktury web�ku");
define("L_E_EXPORT_MEMO", "Vyberte si jeden ze dvou zp�sob� exportu:");
define("L_E_EXPORT_DESC", "P�i exportu \"do jin�ho Toolkitu\" se bude exportovat pouze aktu�ln� �ablona "
		."a vy pro ni zvol�te nov� identifik�tor.");
define("L_E_EXPORT_DESC_BACKUP", "P�i exportu \"do Backupu\" si m��ete vybrat n�kolik �ablon najednou.");
define("L_E_EXPORT_MEMO_ID","Zvolte nov� identifik�tor �ablony o d�lce p�esn� 16 znak�: ");
define("L_E_EXPORT_SWITCH", "Export do Backupu");
define("L_E_EXPORT_SWITCH_BACKUP", "Export do jin�ho Toolkitu");
define("L_E_EXPORT_IDLENGTH", "D�lka identifik�toru mus� b�t 16 znak�, a ne ");
define("L_E_EXPORT_TEXT_LABEL", "Tento text si n�kde ulo�te. M��ete ho pou��t pro naimportov�n� �ablony do Toolkitu:");
define("L_E_EXPORT_LIST", "Ozna�te web�ky, kter� CHCETE exportovat:");

define("L_PARAM_WIZARD_LINK", "Pr�vodce s n�pov�dou");
define("L_SHOW_RICH", "Zobraz toto pole v rich text editoru (pou�ijte a� po nainstalov�n� pot�ebn�ch komponent!)");
define("L_MAP_JOIN","-- Spojen� pol� --");

// aliases used in se_notify.php3 
define("L_NOTIFY_SUBJECT", "P�edm�t e-mailu (Subject)"); 
define("L_NOTIFY_BODY", "Vlastn� e-mailov� zpr�va"); 
define("L_NOTIFY_EMAILS", "E-mailov� adresa (jedna na ��dek)");
define("L_NOTIFY_HOLDING", "<h4>Nov� zpr�va v Z�sobn�ku</h4> Kdokoliv m��e b�t informov�n o tom, �e p�ibyla nov� zpr�va do z�sobn�ku. Adresy p��jemc� napi�te n�e, do n�sleduj�c�ch pol��ek pak vypl�te, jak m� vypadat e-mail, kter� pak u�ivatel� dostanou."); 
define("L_NOTIFY_HOLDING_EDIT", "<h4>Zpr�va v Z�sobn�ku byla zm�n�na</h4> Kdokoliv m��e b�t informov�n o tom, �e byla zm�n�na zpr�va v z�sobn�ku. Adresy p��jemc� napi�te n�e, do n�sleduj�c�ch pol��ek pak vypl�te, jak m� vypadat e-mail, kter� pak u�ivatel� dostanou."); 
define("L_NOTIFY_APPROVED", "<h4>Nov� zpr�va mezi Aktu�ln�mi</h4> Kdokoliv m��e b�t informov�n o tom, �e p�ibyla nov� zpr�va na web. Adresy p��jemc� napi�te n�e, do n�sleduj�c�ch pol��ek pak vypl�te, jak m� vypadat e-mail, kter� pak u�ivatel� dostanou."); 
define("L_NOTIFY_APPROVED_EDIT", "<h4>Aktu�ln� zpr�va zm�n�na</h4> Kdokoliv m��e b�t informov�n o tom, �e byla zm�n�na vystaven� zpr�va. Adresy p��jemc� napi�te n�e, do n�sleduj�c�ch pol��ek pak vypl�te, jak m� vypadat e-mail, kter� pak u�ivatel� dostanou.");
define("L_NOTIFY", "Upozorn�n� e-mailem"); 
define("L_A_NOTIFY_TIT", "E-mailov� upozorn�n� na ud�losti");

define("L_NOITEM_MSG", "Hl�ka 'Nenalezena ��dn� zpr�va'");
define("L_NOITEM_MSG_HLP", "zpr�va, kter� se objev� p�i nenalezen� ��dn�ho odpov�daj�c�ho �l�nku");

# ---------------- Users profiles -----------------------------------------
define('L_PROFILE','Profil');
define('L_DEFAULT_USER_PROFILE','Spole�n� profil');
define('L_PROFILE_DELETE_OK','Pravidlo �sp�n� vymaz�no');
define('L_PROFILE_ADD_OK','Pravidlo p�id�no');
define('L_PROFILE_ADD_ERR','Chyba p�i p�id�v�n� nov�ho pravidla');
define('L_PROFILE_LISTLEN','Po�et zpr�v');
define('L_PROFILE_ADMIN_SEARCH','Filtr zpr�v');
define('L_PROFILE_ADMIN_ORDER','�azen�');
define('L_PROFILE_HIDE','Skr�t pol��ko');
define('L_PROFILE_HIDEFILL','Skr�t a vyplnit');
define('L_PROFILE_FILL','Vyplnit pol��ko');
define('L_PROFILE_PREDEFINE','P�ednastavit pol���ko');
define('L_A_PROFILE_TIT','Spr�va web�ku - U�ivatelsk� profily');
define('L_PROFILE_HDR','Nastaven� pravidla');
define('L_NO_RULE_SET','��dn� pravidlo nebylo definov�no');
define('L_PROFILE_ADD_HDR','P�idat pravidlo');
define('L_PROFILE_LISTLEN_DESC','po�et zpr�v zobrazen�ch v administraci');
define('L_PROFILE_ADMIN_SEARCH_DESC','p�ednastaven� "Hled�n�" v administraci');
define('L_PROFILE_ADMIN_ORDER_DESC','p�ednastaven� "Se�adit" v administraci');
define('L_PROFILE_HIDE_DESC','sk�t pol��ko ve vstupn�m forul��i');
define('L_PROFILE_HIDEFILL_DESC','sk�t pol��ko ve vstupn�m forul��i a vyplnit je danou hodnotou');
define('L_PROFILE_FILL_DESC','vyplnit pol��ko ve vstupn�m forul��i v�dy danou hodnotou');
define('L_PROFILE_PREDEFINE_DESC','p�ednastavit hodnotu do pol��ka ve vstupn�m formul��i');
define('L_VALUE',L_MAP_VALUE2);
define('L_FUNCTION',L_ALIAS_FUNC);
define('L_RULE','Pravidlo');

define('L_ID_COUNT_ALIAS','po�et nalezen�ch �l�nk�');
define('L_V_NO_ITEM','HTML kod nam�sto zpr�vy "Nenalezena ��dn� zpr�va"');
define("L_NO_ITEM_FOUND", "Nenalezena ��dn� zpr�va");
define('L_INPUT_SHOW_HCO','Hierachie konstant');

define("L_CONSTANT_HIERARCH_EDITOR","Editovat v Hierarchick�m editoru (umo��uje ur�it hierarchii hodnot)");
define("L_CONSTANT_PROPAGATE","Propagovat zm�ny do st�vaj�c�ch �l�nk�");
define("L_CONSTANT_OWNER","Vlastn�k skupiny - web�k");
define("L_CONSTANT_HIER_SORT","Zm�ny nebudou ulo�eny do datab�ze, dokud nestisknete tla��tko dole na str�nce.<br>Konstanty jsou �azeny zaprv� podle �azen� a zadruh� podle N�zvu.");
define("L_CONSTANT_DESC","Popis");
define("L_CONSTANT_HIER_SAVE","Ulo� v�echny zm�ny do datab�ze");
define("L_CONSTANT_CHOWN", "Zm�nit vlastn�ka");
define("L_CONSTANT_OWNER_HELP", "Vlastn�kem se stane prvn� web�k, kter� uprav� hodnoty.");
define("L_NO_PS_FIELDS_GROUP","Nem�te administr�torsk� pr�va k web�ku, kter� vlastn� tuto skupinu hodnot");
define("L_CONSTANTS_HIER_EDT","Konstanty - Hiearchick� editor");

define ("L_CONSTANT_LEVELS_HORIZONTAL", "�rovn� horizont�ln�");
define ("L_CONSTANT_VIEW_SETTINGS", "Zobrazen�");
define ("L_CONSTANT_HIERARCHICAL", "Hierarchck�");
define ("L_CONSTANT_HIDE_VALUE", "Skryj hodnotu");
define ("L_CONSTANT_CONFIRM_DELETE","Zatrhn�te pro potvrzen� maz�n�");
define ("L_CONSTANT_COPY_VALUE","Kop�rovat hodnotu z n�zvu");
define ("L_CONSTANT_LEVEL_COUNT","Po�et �rovn�");
define ("L_CONSTANT_LEVEL", "�rove�");
define ("L_SELECT","Zvolit");
define ("L_ADD_NEW","P�idat");

define("L_GROUP_BY", "Seskupit dle");
define("L_GROUP_BY_HLP", "");
define("L_GROUP_HEADER", "Nadpis skupiny");
define ("L_WHOLE_TEXT", "Cel� text");
define ("L_FIRST_LETTER", "1. p�smeno");
define ("L_LETTERS", "p�smena");
define ("L_CASE_NONE", "Nem�nit");
define ("L_CASE_UPPER","VELK�MI");
define ("L_CASE_LOWER","mal�mi");
define ("L_CASE_FIRST","Prvn� Velk�");

define("L_INPUT_VALIDATE_USER","U�ivatel");			# added 03/01/02, setu@gwtech.org
define("L_INPUT_DEFAULT_VAR", "Prom�nn�"); # Added by Ram on 5th March 2002 (Only for English)

define("L_E_EXPORT_DESC_EXPORT","Zvolte, chcete-li exportovat strukturu web�ku, data nebo oboj�.");
define("L_E_EXPORT_EXPORT_DATA","Export dat");
define("L_E_EXPORT_EXPORT_STRUCT","Export struktury");
define("L_E_EXPORT_EXPORT_GZIP","Komprimovat");
define("L_E_EXPORT_EXPORT_TO_FILE","Ulo�it exportovan� data do souboru");
define("L_E_EXPORT_MUST_SELECT","Mus�te vybrat n�jak� web�ky pro z�lohov�n�");
define("L_E_EXPORT_SPEC_DATE","Export dat z ur�it�ch dn�: ");
define("L_E_EXPORT_FROM_DATE","Od ");
define("L_E_EXPORT_TO_DATE","do");
define("L_E_EXPORT_DATE_ERROR","Toto nen� platn� datum");
define("L_E_EXPORT_DATE_TYPE_ERROR","Mus�te pou��t form�t: DD.MM.YYYY");

define ("L_CALENDAR_VIEW", "Kalend��");
define ("L_V_FROM_DATE", "Pol��ko za��tku ud�losti");
define ("L_V_TO_DATE", "Pol��ko konce ud�losti");
define ("L_V_GROUP_BOTTOM", "Spodn� k�d skupiny");
define ("L_V_DAY", "Horn� k�d bu�ky s datem");
define ("L_V_DAY_BOTTOM", "Doln� k�d bu�ky s datem");
define ("L_V_EVENT", "K�d ud�losti");
define ("L_V_EMPTY_DIFFER", "Pou��t jin� nadpis pro pr�zdn� bu�ky");
define ("L_V_DAY_EMPTY", "Horn� k�d pro pr�zdn� datum");
define ("L_V_DAY_EMPTY_BOTTOM", "Spodn� k�d pro pr�zdn� datum");
define ("L_MONTH", "M�s�c - seznam");
define ("L_MONTH_TABLE", "M�s�c - tabulka");
define ("L_V_CALENDAR_TYPE", "Typ kalend��e");
define ("L_CONST_DELETE", "Smazat celou skupinu");
define ("L_CONST_DELETE_PROMPT","Jste si jisti, �e chcete PERMANENTN� SMAZAT tuto skupinu? Napi�te ano �i ne.");
define ("L_NO", "ne");
define ("L_YES", "ano");
define ("L_V_EVENT_TD", "Dal�� atributy do TD tagu pro ud�lost");
define('L_C_TIMESTAMP1','Kalend��: Time stamp v 0:00 p��slu�n�ho data');
define('L_C_TIMESTAMP2', 'Kalend��: Time stamp v 24:00 p��slu�n�ho data');
define('L_C_NUMD','Kalend��: Den v m�s�ci p��slu�n�ho data');
define('L_C_NUMM','Kalend��: ��slo m�s�ce p��slu�n�ho data');
define('L_C_NUMY','Kalend��: Rok p��slu�n�ho data');

define('L_CONSTANT_WHERE_USED', 'Kde jsou konstanty pou�ity?');
define('L_CONSTANT_USED','Hodnoty pou�ity v');

define('L_VIEW_CREATE_NEW', 'Vytvo�it nov� pohled');    
define('L_VIEW_CREATE_TYPE', 'dle&nbsp;typu:');    
define('L_VIEW_CREATE_TEMPL', 'dle&nbsp;�ablony:');
define('L_USE_AS_NEW', 'Nov�&nbsp;dle&nbsp;vybran�ch');
define('L_SLICE_NOT_CONST', 'V poli -Hodnoty- m�te zvolen web�k. Web�k nelze takto m�nit. Vyberte n�jakou skupinu hodnot (v��e v seznamu).');


define ("L_E_IMPORT_DATA_COUNT", "Po�et importovan�ch �l�nk�: %d.");			
define ("L_E_IMPORT_ADDED", "P�id�n byl:");
define ("L_E_IMPORT_OVERWRITTEN", "P�eps�n byl:");
define ("L_CHOOSE_JUMP", "Zvolte modul, kter� chcete editovat");

define ("L_A_FIELD_IDS_TIT", "Spr�va web�ku - Zm�na ID pol��ka");
define ("L_FIELD_IDS", "Zm�na ID pol��ka");
define ("L_FIELD_IDS_CHANGED", "ID pol��ka bylo zm�n�no");
define ("L_V_MONTH_LIST", "Month list (separated by ,)");
define ("L_F_JAVASCRIPT", "Javascript pro pol��ka");
define ("L_FIELD_ALIASES", "Aliasy");

define('L_CHANGE_FROM','Zm�nit z');
define('L_TO', 'na');
define('L_FIELD_ID_HELP',
    'Tato str�nka umo��uje zm�nit identifik�tory jednotliv�ch pol��ek. 
     Je to pom�rn� nebezpe�n� operace a m��e trvat dlouho. Je dost 
     pravd�podobn�, �e tuto operaci nikdy nevyu�ijete - pou��v� se jen 
     ve v�jime�n�ch p��padech (nastaven� formul��e pro vyhled�v�n� ve v�ce 
     web�c�ch.<br><br>
     Vyberte ID pol��ka, kter� chcete zm�nit a potom nov� ID a ��slo. Te�ky 
     budou automaticky dopln�ny.<br>');

define('L_AA_ADMIN','Administrace AA Toolkitu');
define('L_SLICE_ADMIN','Administrace web�ku');
define('L_AA_ADMIN2','AA');
define('L_SLICE_ADMIN2','Nastaven�');
define('L_ARTICLE_MANAGER2','Spr�va zpr�v');
define('L_MODULES', 'Web�ky / Moduly');
define ('L_ADD_MODULE', "Nov�");
define ('L_DELETE_MODULE', "Vymazat");
define ('L_A_MODULE_ADD', 'Vytvo�it nov� Web�k / Modul');
define ('L_A_SLICE', 'Web�k');
define ('L_A_MODULE', 'Modul');

define('L_MODULE_NAME','Jm�no modulu');
define('L_JUMP_TO','P�ej�t na');
define('L_AA_RELATIVE','Zadej cestu relativn� k adres��i AA - t.j.');
define('L_JUMP_SLICE','P�ej�t do web�ku');
define('L_A_JUMP_EDT','Editovat modul Jump');
define('L_A_JUMP_ADD','Vytvo�it nov� Jump modul');
define('L_EDIT_JUMP','Editovat Jump');
define('L_MODULE_ID','ID modulu');
define('L_UPDATE','Zm��');
define('L_CREATE','Vytvo�');

define("L_ASCENDING_PRI","Vzestupn� dle �azen�");
define("L_DESCENDING_PRI","Sestupn� dle �azen�");
define("L_SORT_DIRECTION_HLP","'dle �azen�' lze pou��t jen pro pole pou��vaj�c� konstant (kategorie) - tam take najdete hodnoty pro '�azen�'");

define("L_ALERTS","Zas�l�n� zpr�v");
define("L_ALERTS_COLLECTIONS", "Kolekce");
define("L_ALERTS_USERS","U�ivatel�");
define("L_ALERTS_UI","U�ivatelsk� rozhran�.");
     
// constants used in param wizard only:
require  $GLOBALS[AA_INC_PATH]."en_param_wizard_lang.php3";

// new constants to be translated are here. Leave this "require" always at the 
// end of this file
// If You want to translate the new texts (which is in new_news_lang.php3 file),
// just copy them from the new_news_lang.php3 file here and translate.
// IMPORTANT: Leave the require new_news_lang.php3 as the last line of this
// file - it will not redefine the constant You translated and helps You when
// we add new texts !!!
require  $GLOBALS[AA_INC_PATH]."new_news_lang.php3";
?>