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


# config file identifier
# must correspond with this file name
define("CONFIG_FILE", "cz_news_lang.php3");

define("IMG_UPLOAD_MAX_SIZE", "400000");    // max size of file in picture uploading
define("IMG_UPLOAD_URL", "http://aa.ecn.cz/img_upload/");
define("IMG_UPLOAD_PATH", "/raid/www/htdocs/aa.ecn.cz/img_upload/");
define("IMG_UPLOAD_TYPE", "image/*");
define("EDITOR_GRAB_LEN", 200);                 // not used, i think
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

define("HTML_PAGE_BEGIN", 
'<!DOCTYPE html public "-/W3C/DTD HTML 4.0 Transitional/EN">
  <HTML>
  <HEAD>
  <LINK rel=StyleSheet href="../'. ADMIN_CSS .'" 
        type="text/css" title="CPAdminCSS">
  <meta http-equiv="Content-Type" content="text/html; charset=windows-1250">');
               
# aa toolkit specific labels
define("L_ITEM_ID_ALIAS",'alias pro ��slo �l�nku');
define("L_EDITITEM_ALIAS",'alias pou��van� v administrativn�ch str�nk�ch index.php3 pro URL itemedit.php3');
define("L_LANG_FILE","Pou�it� language soubor");
define("L_PARAMETERS","Parametry");
define("L_SELECT_APP","Vyber aplikaci");
define("L_SELECT_OWNER","Vyber vlastn�ka");
     
# toolkit aplication dependent labels
define("L_CANT_UPLOAD", "Soubor (obr�zek) nelze ulo�it");
define("L_GRAB_LEN", "Po�et znak� textu pou�it�ch jako abstrakt");
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
define("L_FULLTEXT", "Text zpr�vy");
define("L_HIGHLIGHTED", "D�le�it� zpr�va");
define("L_A_FIELDS_EDT", "Spr�va web�ku - Nastaven� pol�");
define("L_FIELDS_HDR", "Pole zpr�v");
define("L_NO_PS_EDIT_ITEMS", "Nem�te pr�vo upravovat zpr�vy v tomto web�ku");
define("L_NO_DELETE_ITEMS", "Nem�te pr�vo mazat zpr�vy");
define("L_NO_PS_MOVE_ITEMS", "Nem�te pr�vo p�esouvat zpr�vy");
define("L_NO_PS_COPMPACT", "Nem�te pr�vo upravovat vzhled p�ehledu zpr�v");
define("L_FULLTEXT_OK", "Vzhled textu zpr�vy byl �sp�n� zm�n�n");
define("L_NO_ITEM", "��dn� zpr�va nevyhovuje va�emu dotazu.");



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
define("L_ODD_ROW_FORMAT", "Lich� ��dky");
define("L_EVEN_ROW_FORMAT", "Sud� ��dky");
define("L_EVEN_ODD_DIFFER", "Pou�ij odli�n� HTML k�d pro sud� �adky");
define("L_CATEGORY_TOP", "Horn� HTML k=od pro kategorii");
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
define("L_GO", "Jdi");
define("L_ADD", "P�idat");
define("L_USERS", "U�ivatel�");
define("L_GROUPS", "Skupiny");
define("L_SEARCH", "Hled�n�");
define("L_DEFAULTS", "Default");
define("L_SLICE", "Web�k");
define("L_DELETED_SLICE", "Nebyl nalezen ��dn� web�k, ke kter�mu m�te p��stup");
define("L_SLICE_URL", "URL web�ku");
define("L_A_NEWUSER", "Nov� u�ivatel v syst�mu");
define("L_NEWUSER_HDR", "Nov� u�ivatel");
define("L_USER_LOGIN", "U�ivatelsk� jm�no");
define("L_USER_PASSWORD1", "Heslo");
define("L_USER_PASSWORD2", "Potvrdit heslo");
define("L_USER_FIRSTNAME", "Jm�no");
define("L_USER_SURNAME", "P��jmen�");
define("L_USER_MAIL", "E-mail");
define("L_USER_SUPER", "Administrativn� ��et");
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
define("L_PERM_CHANGE", "Zm�na sou�asn�ch pr�v");
define("L_PERM_ASSIGN", "P�id�len� nov�ch pr�v");
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
define("L_NO_PS_COPMPACT", "Nem�te pr�vo m�nit vzhled p�ehledu zpr�v");
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
define("L_TYPE", "Typ");
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
define("NO_PICTURE_URL", "http://web.ecn.cz/aauser/images/no_pict.gif");  // image used when 
  // there is img_source in html format string but no img_source is stored in database 
  // (you can use blank pixel for none picture)
define("NO_SOURCE_URL", "javascript: window.alert('Nebylo zad�no url zdroje')"); 
define("NO_OUTER_LINK_URL", "javascript: window.alert('Nebylo zad�no url odkazu ven')");

# editor interface constants
define("L_PUBLISHED_HEAD", "PUB");
define("L_HIGHLIGHTED_HEAD", "!");
define("L_FEEDED_HEAD", "ciz�");
define("L_MORE_DETAILS", "V�ce podrobnost�");
define("L_LESS_DETAILS", "M�n� podrobnost�");
define("L_UNSELECT_ALL", "Zru�it v�b�r");
define("L_SELECT_VISIBLE", "Vybrat zobrazen�");
define("L_UNSELECT_VISIBLE", "Zru�it v�b�r");

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

//new_constants
define("DEFAULT_SLICE_CONFIG", "<wddxPacket version='0.9'><header/><data><struct><var name='admin_fields'><struct><var name='chbox'><struct><var name='width'><number>24</number></var></struct></var><var name='post_date'><struct><var name='width'><number>70</number></var></struct></var><var name='headline'><struct><var name='width'><number>224</number></var></struct></var><var name='catname'><struct><var name='width'><number>70</number></var></struct></var><var name='published'><struct><var name='width'><number>24</number></var></struct></var><var name='highlight'><struct><var name='width'><number>24</number></var></struct></var><var name='feed'><struct><var name='width'><number>24</number></var></struct></var></struct></var></struct></data></wddxPacket>");
define("L_FEED", "V�m�na zpr�v");
define("L_FEEDTO_TITLE", "P�edat zpr�vu do web�ku");
define("L_FEED_TO", "P�edat vybrn� zpr�vy do zvolen�ch web�ku");
define("L_NO_PERMISSION_TO_FEED", "Nelze");
define("L_NO_PS_CONFIG", "Nem�te pr�vo nastavovat configura�n� parametry tohoto web�ku");
define("L_SLICE_CONFIG", "Parametry");
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

//new_constants
define("L_NO_PS_NEW_USER", "Nem�te pr�vo vytvo�it u�ivatele");
define("L_ALL_GROUPS", "V�echny skupiny");
define("L_USERS_GROUPS", "U�ivatelovy skupiny");
define("L_REALY_DELETE_USER", "Opravdu chcete vymazat dan�ho u�ivatele ze syst�mu?");
define("L_REALY_DELETE_GROUP", "Opravdu chcete vymazat danou skupinu ze syst�mu?");
define("L_ITEM_NOT_CHANGED", "Zpr�va nezm�n�na");
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
define("L_OK", "OK");
define("L_ACTIVE_BIN_EXPIRED", "Aktu�ln� - Expirovan�");
define("L_ACTIVE_BIN_PENDING", "Aktu�ln� - P�ipraven�");
define("L_ACTIVE_BIN_EXPIRED_MENU", "... expirovan�");
define("L_ACTIVE_BIN_PENDING_MENU", "... p�ipraven�");

define("L_SOURCE_DESC", "Org Description");
define("L_SOURCE_ADDRESS", "Street Adress");
define("L_SOURCE_CITY", "City");
define("L_SOURCE_PROV", "Province/State");
define("L_SOURCE_COUNTRY", "Country");
define("L_TIME", "Time");
define("L_CON_NAME", "Contact Name");
define("L_CON_EMAIL", "Contact E-mail");
define("L_CON_PHONE", "Contact phone");
define("L_CON_FAX", "Contact FAX");
define("L_LOC_NAME", "Location Name");
define("L_LOC_ADDRESS", "Location Street Address");
define("L_LOC_CITY", "Location City");
define("L_LOC_PROV", "Location Province/State");
define("L_LOC_COUNTRY", "Location Country");
define("L_START_DATE", "Start Date");
define("L_END_DATE", "End Date");

define("L_HLP_SOURCE_DESC", "Alias for Org Description");
define("L_HLP_SOURCE_ADDRESS", "Alias for Street Adress");
define("L_HLP_SOURCE_CITY", "Alias for City");
define("L_HLP_SOURCE_PROV", "Alias for Province/State");
define("L_HLP_SOURCE_COUNTRY", "Alias for Country");
define("L_HLP_TIME", "Alias for Time");
define("L_HLP_CON_NAME", "Alias for Contact Name");
define("L_HLP_CON_EMAIL", "Alias for Contact E-mail");
define("L_HLP_CON_PHONE", "Alias for Contact phone");
define("L_HLP_CON_FAX", "Alias for Contact FAX");
define("L_HLP_LOC_NAME", "Alias for Location Name");
define("L_HLP_LOC_ADDRESS", "Alias for Location Street Address");
define("L_HLP_LOC_CITY", "Alias for Location City");
define("L_HLP_LOC_PROV", "Alias for Location Province/State");
define("L_HLP_LOC_COUNTRY", "Alias for Location Country");
define("L_HLP_START_DATE", "Alias for Start Date");
define("L_HLP_END_DATE", "Alias for End Date");

//----------------------------
//define("", "");   
//prepared for new constants
 
define("L_FIELD_PRIORITY", "Priorita");
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
define("", "");

define("L_FIELD_DELETE_OK", "Pole odstran�no");

define("L_WARNING_NOT_CHANGE","<p>POZOR: Tato nastaven� by m�l m�nit jen ten, kdo v� co d�l�!</p>");
define("L_INPUT_SHOW_FUNC_F_HLP","Funkce, kter� se pou�ije pro zobrazen� pole ve vstupn�m formul��i. Pro n�kter� typu zobrazen� lze pou��t parametr�, kter� n�sleduj�.");
define("L_INPUT_SHOW_FUNC_C_HLP","Hodnoty, pou�it� v p��pad� vstupn�ch funkc� SELECT �i RADIO.");
define("L_INPUT_SHOW_FUNC_HLP","Parametr pou�it� pro vstupn� funkce TEXT (<po�et ��dk�>) �i DATE (<minus roky>'<plus roky>'<od te�?>).");
define("L_INPUT_DEFAULT_F_HLP","Funkce, kter� se pou�ije pro generov�n� defaultn�ch hodnot pole:<BR>Now - aktu�ln� datum<BR>User ID - identifik�tor p�ihl�en�ho u�ivatele<BR>Text - text uveden� v poli Parametr<br>Date - aktu�ln� datum plus <Parametr> dn�");
define("L_INPUT_DEFAULT_HLP","Parametr pro defauln� hodnoty Text a Date (viz v��e)");

define("L_INPUT_DEFAULT_TXT", "Text");
define("L_INPUT_DEFAULT_DTE", "Date");
define("L_INPUT_DEFAULT_UID", "User ID");
define("L_INPUT_DEFAULT_NOW", "Now");

define("L_INPUT_SHOW_TXT","Text Area");
define("L_INPUT_SHOW_FLD","Text Field");
define("L_INPUT_SHOW_SEL","Select Box");
define("L_INPUT_SHOW_RIO","Radio Button");
define("L_INPUT_SHOW_DTE","Date");
define("L_INPUT_SHOW_CHB","Check Box");
define("L_INPUT_SHOW_FIL","File Upload");
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
define("L_CONSTANT_PRIORITY", "Priorita");
define("L_CONSTANT_PRI", "Priorita");
define("L_CONSTANT_GROUP", "Skupina hodnot");
define("L_CONSTANT_GROUP_EXIST", "Tato skupina hodnot ji� existuje");
define("L_CONSTANTS_OK", "Zm�no hodnot �sp�n� provedena");
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

    
/*
$Log$
Revision 1.19  2000/12/21 16:39:34  honzam
New data structure and many changes due to version 1.5.x

Revision 1.18  2000/12/05 14:01:58  honzam
Better help for upload image alias

Revision 1.17  2000/11/13 10:41:14  honzam
Fixed bad order for default setting of show fields and needed fields

Revision 1.16  2000/10/12 15:56:09  honzam
Updated language files with better defaults

Revision 1.15  2000/10/10 18:28:00  honzam
Support for Web.net's extended item table

Revision 1.14  2000/08/17 15:17:55  honzam
new possibility to redirect item displaying (for database changes see CHANGES)

Revision 1.13  2000/08/15 08:58:31  kzajicek
Added missing L_HLP_CATEGORY_ID

Revision 1.12  2000/08/15 08:43:41  kzajicek
Fixed spelling error in constant name

Revision 1.11  2000/08/14 12:39:13  kzajicek
Language definitions required by setup.php3

Revision 1.10  2000/08/03 15:19:57  kzajicek
Language changes

Revision 1.9  2000/08/03 12:49:22  kzajicek
English editing

Revision 1.8  2000/08/03 12:34:27  honzam
Default values for new slice defined.

Revision 1.7  2000/07/27 18:17:21  kzajicek
Added superadmin settings in User/Group management

Revision 1.6  2000/07/27 13:23:58  kzajicek
Language correction

Revision 1.5  2000/07/17 13:40:11  kzajicek
Alert box when no input category selected

Revision 1.4  2000/07/17 12:29:56  kzajicek
Language changes

Revision 1.3  2000/07/12 11:06:26  kzajicek
names of image upload variables were a bit confusing

Revision 1.2  2000/07/03 15:00:14  honzam
Five table admin interface. 'New slice expiry date bug' fixed.

Revision 1.1.1.1  2000/06/21 18:40:27  madebeer
reimport tree , 2nd try - code works, tricky to install

Revision 1.1.1.1  2000/06/12 21:50:14  madebeer
Initial upload.  Code works, tricky to install. Copyright, GPL notice there.

Revision 1.6  2000/06/12 19:58:34  madebeer
Added copyright (APC) notice to all .inc and .php3 files that have an $Id

Revision 1.5  2000/06/09 15:14:10  honzama
New configurable admin interface

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
