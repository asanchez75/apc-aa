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
define("CONFIG_FILE", "sk_news_lang.php3");

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
'<!DOCTYPE html public "-/W3C/DTD HTML 4.0 Transitional/EN">
  <HTML>
  <HEAD>
  <LINK rel=StyleSheet href="../'. ADMIN_CSS .'" 
        type="text/css" title="CPAdminCSS">
  <meta http-equiv="Content-Type" content="text/html; charset=windows-1250">');
               
# aa toolkit specific labels
define("L_ITEM_ID_ALIAS",'alias pre ��slo �l�nku');
define("L_EDITITEM_ALIAS",'alias pou��van� v administrat�vnych str�nk�ch index.php3 pre URL itemedit.php3');
define("L_LANG_FILE","Pou�it� language s�bor");
define("L_PARAMETERS","Parametre");
define("L_SELECT_APP","V�ber aplik�ci�");
define("L_SELECT_OWNER","V�ber vlastn�ka");
     
# toolkit aplication dependent labels
define("L_CANT_UPLOAD", "S�bor (obr�zok) sa ned� ulo�i�");
define("L_MSG_PAGE", "Nastavenia");   // title of message page
define("L_EDITOR_TITLE", "Spr�vy");
define("L_FULLTEXT_FORMAT_TOP", "Horn� HTML k�d");
define("L_FULLTEXT_FORMAT", "HTML k�d textu spr�vy");
define("L_FULLTEXT_FORMAT_BOTTOM", "Spodn� HTML k�d");
define("L_A_FULLTEXT_TIT", "Spr�va modulu - Vzh�ad jednej spr�vy");
define("L_FULLTEXT_HDR", "HTML k�d pre zobrazenie spr�vy");
define("L_COMPACT_HDR", "HTML k�d pre preh�ad spr�v");
define("L_ITEM_HDR", "Vstupn� formul�r spr�vy");
define("L_A_ITEM_ADD", "Prida� spr�vu");
define("L_A_ITEM_EDT", "Upravi� spr�vu");
define("L_IMP_EXPORT", "Povoli� zasielanie spr�v do modulu:");
define("L_ADD_NEW_ITEM", "Nov� spr�va");
define("L_DELETE_TRASH", "Vysypa� k��");
define("L_VIEW_FULLTEXT", "Zobrazi� spr�vu");
define("L_FULLTEXT", "Cel� spr�va");
define("L_HIGHLIGHTED", "D�le�it� spr�va");
define("L_A_FIELDS_EDT", "Nastavenia modulu - Nastavenia pol�");
define("L_FIELDS_HDR", "Pole spr�v");
define("L_NO_PS_EDIT_ITEMS", "Nem�te pr�vo upravova� spr�vy v tomto module");
define("L_NO_DELETE_ITEMS", "Nem�te pr�vo maza� spr�vy");
define("L_NO_PS_MOVE_ITEMS", "Nem�te pr�vo pres�va� spr�vy");
define("L_NO_PS_COPMPACT", "Nem�te pr�vo upravova� vzh�ad preh�adu spr�v");
define("L_FULLTEXT_OK", "Vzh�ad textu spr�vy bol �spe�ne zmenen�");
define("L_NO_ITEM", "�iadna spr�va nevyhovuje v�mu zadaniu.");



# aa toolkit common labels
# can be the same for all toolkit aplications
define("L_ACTIVE_BIN", "Aktu�lne");
define("L_HOLDING_BIN", "Z�sobn�k");
define("L_TRASH_BIN", "K��");

define("L_CATEGORY","Kateg�rie");
define("L_SLICE_NAME", "Meno");
define("L_DELETED", "Zmazan�");
define("L_D_LISTLEN", "Po�et vypisovan�ch spr�v");  // slice
define("L_ERR_CANT_CHANGE", "Nepodarilo sa zmeni� nastavenie modulu");
define("L_ODD_ROW_FORMAT", "Nep�rny z�znam");
define("L_EVEN_ROW_FORMAT", "P�rny z�znam");
define("L_EVEN_ODD_DIFFER", "Odli�n� HTML k�d pre p�rne z�znamy");
define("L_CATEGORY_TOP", "Horn� HTML k�d pre kateg�riu");
define("L_CATEGORY_FORMAT", "Nadpis kateg�rie");
define("L_CATEGORY_BOTTOM", "Spodn� HTML k�d pre kateg�riu");
define("L_CATEGORY_SORT", "Zoradi� spr�vy v preh�ade pod�a kateg�rie");
define("L_COMPACT_TOP", "Horn� HTML k�d");
define("L_COMPACT_BOTTOM", "Spodn� HTML k�d");
define("L_A_COMPACT_TIT", "Nastavenia modulu - Vzh�ad preh�adu spr�v");
define("L_A_FILTERS_TIT", "Nastavenia modulu - Filtre pre v�menu spr�v");
define("L_FLT_SETTING", "Nastavenie filtrov pre pr�jem spr�v");
define("L_FLT_FROM_SL", "Filter pre pr�jem spr�v z modulu");
define("L_FLT_FROM", "Z");
define("L_FLT_TO", "Do");
define("L_FLT_APPROVED", "Ako aktu�lnu spr�vu");
define("L_FLT_CATEGORIES", "Kateg�rie");
define("L_ALL_CATEGORIES", "V�etky kateg�rie");
define("L_FLT_NONE", "Nie je vybran� �iadna vstupn� kateg�ria!");
define("L_THE_SAME", "-- rovnak� --");
define("L_EXPORT_TO_ALL", "Povoli� exportova� spr�vy do v�etk�ch modulov");

define("L_IMP_EXPORT_Y", "Zasielanie povolen�");
define("L_IMP_EXPORT_N", "Zasielanie zak�zan�");
define("L_IMP_IMPORT", "Pr�jma� spr�vy z:");
define("L_IMP_IMPORT_Y", "Pr�jma�");
define("L_IMP_IMPORT_N", "Nepr�jma�");
define("L_CONSTANTS_HLP", "Pou�i n�sleduj�ce aliasy datab�zov�ch pol�");

define("L_ERR_IN", "Chyba v");
define("L_ERR_NEED", "mus� b�t vyplnen�");
define("L_ERR_LOG", "pou�ite znaky a-z, A-Z a 0-9");
define("L_ERR_LOGLEN", "mus� by� dlh� 5 - 32 znakov");
define("L_ERR_NO_SRCHFLDS", "Nebolo zadan� preh�ad�van� pole!");

define("L_FIELDS", "Polia");
define("L_EDIT", "Edit�cia");
define("L_DELETE", "Vymaza�");
define("L_REVOKE", "Odstr�ni�");
define("L_UPDATE", "Zmeni�");
define("L_RESET", "Vymaza� formul�r");
define("L_CANCEL", "Zru�i�");
define("L_ACTION", "Akcia");
define("L_INSERT", "Vlo�i�");
define("L_NEW", "Nov�");
define("L_GO", "Cho�");
define("L_ADD", "Prida�");
define("L_USERS", "U�ivatelia");
define("L_GROUPS", "Skupiny");
define("L_SEARCH", "H�adanie");
define("L_DEFAULTS", "Default");
define("L_SLICE", "Modul");
define("L_DELETED_SLICE", "Nebol n�jden� �iaden modul, ku ktor�mu m�te pr�stup");
define("L_SLICE_URL", "URL modulu");
define("L_A_NEWUSER", "Nov� u�ivate� v syst�me");
define("L_NEWUSER_HDR", "Nov� u�ivate�");
define("L_USER_LOGIN", "U�ivate�sk� meno");
define("L_USER_PASSWORD1", "Heslo");
define("L_USER_PASSWORD2", "Potvrdi� heslo");
define("L_USER_FIRSTNAME", "M�no");
define("L_USER_SURNAME", "Priezvisko");
define("L_USER_MAIL", "E-mail");
define("L_USER_SUPER", "Administrat�vny ��et");
define("L_A_USERS_TIT", "Nastavenia modulu - U�ivatelia");
define("L_A_PERMISSIONS", "Nastavenia modulu - Pr�stupov� pr�va");
define("L_A_ADMIN", "Nastavenia modulu - Vzh�ad Administr�cie");
define("L_A_ADMIN_TIT", L_A_ADMIN);
define("L_ADMIN_FORMAT", "HTML k�d pre zobrazenie spr�vy");
define("L_ADMIN_FORMAT_BOTTOM", "Spodn� HTML");
define("L_ADMIN_FORMAT_TOP", "Horn� HTML");
define("L_ADMIN_HDR", "V�pis spr�v v administrat�vnych str�nk�ch");
define("L_ADMIN_OK", "Vzh�ad administrat�vnych str�nok �spe�ne zmenen�");
define("L_ADMIN_REMOVE", "Odstra�ovan� re�azce");

define("L_ROLE_AUTHOR", "Autor");
define("L_ROLE_EDITOR", "Editor");
define("L_ROLE_ADMINISTRATOR", "Administr�tor");
define("L_ROLE_SUPER", "Super");

define("L_MAIN_SET", "Hlavn� nastavenia");
define("L_PERMISSIONS", "Nastavenia pr�v");
define("L_PERM_CHANGE", "Zmena s��asn�ch pr�v");
define("L_PERM_ASSIGN", "Pridelenie nov�ch pr�v");
define("L_PERM_NEW", "H�adej u�ivate�a alebo skupinu");
define("L_PERM_SEARCH", "Priradenie nov�ch pr�v");
define("L_PERM_CURRENT", "Zmena s��asn�ch pr�v");
define("L_USER_NEW", "Nov� u�ivate�");
define("L_DESIGN", "Vzh�ad");
define("L_COMPACT", "Preh�ad spr�v");
define("L_COMPACT_REMOVE", "Odstra�ovan� re�azce");
define("L_FEEDING", "V�mena spr�v");
define("L_IMPORT", "Zasielanie & Pr�jem");
define("L_FILTERS", "Filtre");

define("L_A_SLICE_ADD", "Spr�va modulu - Prid�nie modulu");
define("L_A_SLICE_EDT", "Spr�va modulu - �prava modulu");
define("L_A_SLICE_CAT", "Spr�va modulu - Nastavenie kateg�ri�");
define("L_A_SLICE_IMP", "Spr�va modulu - V�mena spr�v");
define("L_FIELD", "Polo�ka");
define("L_FIELD_IN_EDIT", "Zobrazi�");
define("L_NEEDED_FIELD", "Povinn�");
define("L_A_SEARCH_TIT", "Spr�va modulu - Vyh�ad�vac� formul�r");
define("L_SEARCH_HDR", "Vyh�ad�vacie krit�ri�");
define("L_SEARCH_HDR2", "Vyh�ad�va� v polo�k�ch");
define("L_SEARCH_SHOW", "Zobrazi�");
define("L_SEARCH_DEFAULT", "�tandardn� nastavenia");
define("L_SEARCH_SET", "Vyh�ad�vac� formul�r");
define("L_AND", "AND");
define("L_OR", "OR");
define("L_SRCH_KW", "Search for");
define("L_SRCH_FROM", "From");
define("L_SRCH_TO", "To");
define("L_SRCH_SUBMIT", "Search");
define("L_NO_PS_EDIT", "Nem�te pr�vo upravova� tento modul");
define("L_NO_PS_ADD", "Nem�te pr�vo prid�va� modul");
define("L_NO_PS_COPMPACT", "Nem�te pr�vo meni� vzh�ad preh�adu spr�v");
define("L_NO_PS_FULLTEXT", "Nem�te pr�vo meni� vzh�ad v�pisu spr�vy");
define("L_NO_PS_CATEGORY", "Nem�te pr�vo meni� nastavenia kateg�ri�");
define("L_NO_PS_FEEDING", "Nem�te pr�vo meni� nastavenia v�meny spr�v");
define("L_NO_PS_USERS", "Nem�te pr�vo ku spr�ve u�ivate�ov");
define("L_NO_PS_FIELDS", "Nem�te pr�vo meni� nastavenia polo�iek");
define("L_NO_PS_SEARCH", "Nem�te pr�vo meni� nastavenia vyh�ad�v�nia");

define("L_BAD_RETYPED_PWD", "Vyplnen� hesl� nie s� rovnak�");
define("L_ERR_USER_ADD", "Nepodarilo se pridat u�ivate�a do syst�mu - chyba LDAP");
define("L_NEWUSER_OK", "U�ivate� bol �spe�ne pridan� do syst�mu");
define("L_COMPACT_OK", "Vzh�ad preh�adu spr�v bol �spe�ne zmenen�");
define("L_BAD_ITEM_ID", "Zl� ��slo spr�vy");
define("L_ALL", " - v�etko - ");
define("L_CAT_LIST", "Kateg�rie spr�v");
define("L_CAT_SELECT", "Kateg�rie v tomto module");
define("L_NEW_SLICE", "Nov� modul");
define("L_ASSIGN", "Priradi�");
define("L_CATBINDS_OK", "Nastavenia kateg�ri� boli �spe�ne zmenen�");
define("L_IMPORT_OK", "Nastavenia v�meny spr�v �spe�ne zmenen�");
define("L_FIELDS_OK", "Nastavenia polo�iek �spe�ne zmenen�");
define("L_SEARCH_OK", "Nastaven�a vyh�ad�vacieho formul�ra �spe�ne zmenen�");
define("L_NO_CATEGORY", "Kateg�rie neboli definovan�");
define("L_NO_IMPORTED_SLICE", "Nie je nastaven� �iaden modul, z ktor�ho sa maj� pr�jma� spr�vy");
define("L_NO_USERS", "U�ivate� (skupina) nen�jden�");

define("L_TOO_MUCH_USERS", "N�jden�ch pr�li� ve�a u�ivate�ov alebo skup�n.");
define("L_MORE_SPECIFIC", "Sk�ste zada� presnej�ie �daje.");
define("L_REMOVE", "Odstr�ni�");
define("L_ID", "Id");
define("L_TYPE", "Typ");
define("L_SETTINGS", "Nastavenia");
define("L_LOGO", "APC toolkit");
define("L_USER_MANAGEMENT", "U�ivatelia");
define("L_ITEMS", "Spr�va pr�spevkov");
define("L_NEW_SLICE_HEAD", "Nov� modul");
define("L_ERR_USER_CHANGE", "Nie je mo�n� zmeni� �daje u�ivate�a - LDAP Error");
define("L_PUBLISHED", "Zverejnen�");
define("L_EXPIRED", "Expirovan�");
define("L_NOT_PUBLISHED", "Zatia� nepublikovan�");
define("L_EDIT_USER", "Edit�cia u�ivate�a");
define("L_EDITUSER_HDR", L_EDIT_USER);

define("NO_SOURCE_URL", "javascript: window.alert('Nebolo zadan� url zdroja')"); 
define("NO_OUTER_LINK_URL", "javascript: window.alert('Nebolo zadan� url extern�ho odkazu')");

# editor interface constants
define("L_PUBLISHED_HEAD", "PUB");
define("L_HIGHLIGHTED_HEAD", "!");
define("L_FEEDED_HEAD", "cudzie");
define("L_MORE_DETAILS", "Viac podrobnost�");
define("L_LESS_DETAILS", "Menej podrobnost�");
define("L_UNSELECT_ALL", "Zru�i� v�ber");
define("L_SELECT_VISIBLE", "Vybra� zobrazen�");
define("L_UNSELECT_VISIBLE", "Zru�i� v�ber");

define("L_SLICE_ADM","Administr�cia modulu - Menu");
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

define("L_FEED", "V�mena spr�v");
define("L_FEEDTO_TITLE", "Poskytn�� spr�vu do modulu");
define("L_FEED_TO", "Poskytn�� vybran� spr�vy do zvolen�ch modulov");
define("L_NO_PERMISSION_TO_FEED", "Ned� sa");
define("L_NO_PS_CONFIG", "Nem�te pr�vo nastavova� konfigura�n� parametre tohoto modulu");
define("L_SLICE_CONFIG", "Administr�cia");
define("L_CHBOX_HEAD", "&nbsp;");   // title of checkbox in admin interface
define("L_CHBOX", "Checkbox"); 
define("L_CATNAME", "Kateg�ria");
define("L_CATEGORY_ID", "ID kateg�rie");
define("L_EDITED_BY","Editovan�");
define("L_MASTER_ID", "ID zdrojov�ho modulu");
define("L_CHANGE_MARKED", "Zmeni� vybran�");
define("L_MOVE_TO_ACTIVE_BIN", "Vystavi�");
define("L_MOVE_TO_HOLDING_BIN", "Posla� do z�sobn�ku");
define("L_MOVE_TO_TRASH_BIN", "Posla� do ko�a");
define("L_OTHER_ARTICLES", "Ostatn� spr�vy");
define("L_MISC", "Pr�kazy");
define("L_HEADLINE_EDIT", "Nadpis (edit�cia po kliknut�)");
define("L_HEADLINE_PREVIEW", "Nadpis (preview po kliknut�)");
define("L_EDIT_LINK", "&nbsp;");
define("L_PREVIEW_LINK", "&nbsp;");
define("L_ARTICLE_MANAGER", "Spr�va spr�v");
define("L_SWITCH_TO", "Modul:");
define("L_ADMIN", "Administr�cia");

define("L_NO_PS_NEW_USER", "Nem�te pr�vo vytvori� u�ivate�a");
define("L_ALL_GROUPS", "V�etky skupiny");
define("L_USERS_GROUPS", "U�ivate�ove skupiny");
define("L_REALY_DELETE_USER", "Naozaj chcete vymaza� dan�ho u�ivate�a zo syst�mu?");
define("L_REALY_DELETE_GROUP", "Naozaj chcete vymaza� dan� skupinu zo syst�mu?");
define("L_ITEM_NOT_CHANGED", "Spr�va nezmenen�");
define("L_NO_GROUPS", "Skupina nen�jden�");
define("L_GROUP_NAME", "M�no");
define("L_GROUP_DESCRIPTION", "Popis");
define("L_GROUP_SUPER", "Administrat�vna skupina");
define("L_ERR_GROUP_ADD", "Nie je mo�n� prida� skupinu do syst�mu");
define("L_NEWGROUP_OK", "Skupina bola �spe�ne pridan�");
define("L_ERR_GROUP_CHANGE", "Nie je mo�n� zmenit skupinu");
define("L_A_UM_USERS_TIT", "Spr�va u�ivate�ov - U�ivatelia");
define("L_A_UM_GROUPS_TIT", "Spr�va u�ivate�ov - Skupiny");
define("L_EDITGROUP_HDR", "Edit�cai skupiny");
define("L_NEWGROUP_HDR", "Nov� skupina");
define("L_GROUP_ID", "ID skupiny");
define("L_ALL_USERS", "V�etci u�ivatelia");
define("L_GROUPS_USERS", "U�ivatelia v skupine");
define("L_POST", "Posla�");
define("L_POST_PREV", "Posla� a pozrie�");
define("L_OK", "OK");
define("L_ACTIVE_BIN_EXPIRED", "Aktu�lne - Expirovan�");
define("L_ACTIVE_BIN_PENDING", "Aktu�lne - Pripraven�");
define("L_ACTIVE_BIN_EXPIRED_MENU", "... expirovan�");
define("L_ACTIVE_BIN_PENDING_MENU", "... pripraven�");

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
define("L_DELETE_FIELD", "Naozaj chcete vymaza� toto pole z modulu?");
define("L_FEEDED", "Prebran�");
define("L_HTML_DEFAULT", "�tandardne pou�i� HTML k�d");
define("L_HTML_SHOW", "Zobrazi� vo�bu 'HTML' / 'oby�ajn� text'");
define("L_NEW_OWNER", "Nov� vlastn�k");
define("L_NEW_OWNER_EMAIL", "E-mail nov�ho vlastn�ka");
define("L_NO_FIELDS", "V tomto module nie s� definovan� �iadne polia (�o je �udn�)");
define("L_NO_FIELD", "");
define("L_NO_PERM_TO_FEED", "Nem�te pr�vo nastavi� v�menu spr�v so �iadnym modulom");
define("L_NO_SLICES", "�iaden modul");
define("L_NO_TEMPLATES", "�iadna �abl�na");
define("L_OWNER", "Vlastn�k");
define("L_SLICES", "Moduly");
define("L_TEMPLATE", "�abl�na");
define("L_VALIDATE", "Zkontrolova�");

define("L_FIELD_DELETE_OK", "Pole odstr�nen�");

define("L_WARNING_NOT_CHANGE","<p>POZOR: Tieto nastavenia by mal meni� iba ten, kto vie �o rob�!</p>");
define("L_INPUT_SHOW_FUNC_F_HLP","Funkcia, ktor� sa pou�ije pre zobrazenie po�a vo vstupnom formul�ri. Pre niektor� typy zobrazenia je mo�n� pou�i� parametre, ktor� nasleduj�.");
define("L_INPUT_SHOW_FUNC_C_HLP","Hodnoty, pou�it� v pr�pad� vstupn�ch funkci� SELECT alebo RADIO.");
define("L_INPUT_SHOW_FUNC_HLP","Parameter pou�it� pre vstupn� funkcie TEXT (<po�et riadkov>) alebo DATE (<m�nus roky>'<plus roky>'<od teraz?>).");
define("L_INPUT_DEFAULT_F_HLP","Funkcia, ktor� sa pou�ije pre generovanie defaultn�ch hodn�t po�a:<BR>Now - aktu�lny d�tum<BR>User ID - identifik�tor prihl�sen�ho u�ivate�a<BR>Text - text uveden� v poli Parameter<br>Date - aktu�lny d�tum plus <Parametr> dn�");
define("L_INPUT_DEFAULT_HLP","Parameter pre defauln� hodnoty Text a Date (vi� vy��ie)");

define("L_INPUT_DEFAULT_TXT", "Text");
define("L_INPUT_DEFAULT_DTE", "D�tum");
define("L_INPUT_DEFAULT_UID", "ID u��vate�a");
define("L_INPUT_DEFAULT_NOW", "Aktu�lny d�tum a �as*");

define("L_INPUT_SHOW_TXT","Text Area");
define("L_INPUT_SHOW_FLD","Text Field");
define("L_INPUT_SHOW_SEL","Select Box");
define("L_INPUT_SHOW_RIO","Radio Button");
define("L_INPUT_SHOW_DTE","D�tum");
define("L_INPUT_SHOW_CHB","Check Box");
define("L_INPUT_SHOW_MCH", "Viacero Checkboxov");
define("L_INPUT_SHOW_MSE", "Select Box - multi");
define("L_INPUT_SHOW_FIL","Upload s�boru");
define("L_INPUT_SHOW_NUL","Nezobrazova�");
                              
define("L_INPUT_VALIDATE_TEXT","Text");
define("L_INPUT_VALIDATE_URL","URL");
define("L_INPUT_VALIDATE_EMAIL","E-mail");
define("L_INPUT_VALIDATE_NUMBER","��slo");
define("L_INPUT_VALIDATE_ID","Id");
define("L_INPUT_VALIDATE_DATE","D�tum");
define("L_INPUT_VALIDATE_BOOL","�no/Nie");

define("L_INPUT_INSERT_QTE","Text");
define("L_INPUT_INSERT_DTE","D�tum");
define("L_INPUT_INSERT_CNS","Kon�tanta");
define("L_INPUT_INSERT_NUM","��slo");
define("L_INPUT_INSERT_BOO","�Ano/Nie");
define("L_INPUT_INSERT_UID","ID u��vate�a");
define("L_INPUT_INSERT_NOW","Aktu�lny d�tum a �as");
define("L_INPUT_INSERT_FIL","S�bor");
define("L_INPUT_INSERT_NUL","Pr�zdne");

define("L_INPUT_DEFAULT","Default");
define("L_INPUT_BEFORE","HTML k�d pred t�mto po�om");
define("L_INPUT_BEFORE_HLP","HTML k�d, ktor� sa zobraz� vo vstupnom formul�ri pred t�mto po�om");
define("L_INPUT_FUNC","Typ Vstupu");
define("L_INPUT_HELP","N�poveda");
define("L_INPUT_HELP_HLP","N�poveda zobrazen� pre toto pole vo vstupnom formul�ri");
define("L_INPUT_MOREHLP","Viac inform�ci�");
define("L_INPUT_MOREHLP_HLP","N�poveda, ktor� sa zobraz� po stla�en� '?' vo vstupnom formul�ri");
define("L_INPUT_INSERT_HLP","Sp�sob ulo�enia do datab�zy");
define("L_INPUT_VALIDATE_HLP","Funkcia pre kontrolu vstupu (validace)");

define("L_CONSTANT_NAME", "Meno");
define("L_CONSTANT_VALUE", "Hodnota");
define("L_CONSTANT_PRIORITY", "Priorita");
define("L_CONSTANT_PRI", "Priorita");
define("L_CONSTANT_GROUP", "Skupina hodn�t");
define("L_CONSTANT_GROUP_EXIST", "T�to skupina hodn�t u� existuje");
define("L_CONSTANTS_OK", "Zmena hodn�t �spe�ne vykonan�");
define("L_A_CONSTANTS_TIT", "Spr�va modulu - Nastavenia hodn�t");
define("L_A_CONSTANTS_EDT", "Spr�va modulu - Nastavenia hodn�t");
define("L_CONSTANTS_HDR", "Hodnoty");
define("L_CONSTANT_NAME_HLP", "zobrazen�&nbsp;vo&nbsp;vstupnom&nbsp;formul�ri");
define("L_CONSTANT_VALUE_HLP", "ulo�en�&nbsp;v&nbsp;datab�ze");
define("L_CONSTANT_PRI_HLP", "Poradie&nbsp;hodn�t");
define("L_CONSTANT_CLASS", "Nadkateg�rie");
define("L_CONSTANT_CLASS_HLP", "len&nbsp;pre&nbsp;kateg�rie");
define("L_CONSTANT_DEL_HLP", "Pre odstr�nenie karteg�rie vyma�te jej meno");

$L_MONTH = array( 1 => 'Janu�r', 'Febru�r', 'Marec', 'Apr�ln', 'M�j', 'J�n', 
		'J�l', 'August', 'September', 'Okt�ber', 'November', 'December');

define("L_NO_CATEGORY_FIELD","Pole kateg�rie nie je v tomto module definovan�.<br>  Pridajte pole kateg�rie do modulu na str�nke Polia.");
define("L_PERMIT_ANONYMOUS_POST","Anonymn� vkladanie");
define("L_PERMIT_OFFLINE_FILL","Off-line plnenie");
define("L_SOME_CATEGORY", "<kateg�ria>");

define( "L_BRACKETS_ERR", "Brackets doesn't match in query: ");
define("L_SLICE_ADM","Administr�cia modulu - Menu");
define("L_A_SLICE_TIT", L_SLICE_ADM);
define("L_A_SLICE_ADD_HELP", "Nov� modul m��ete vytvori� na z�klade �abl�ny, alebo skop�rova� nastavenia z u� existuj�ceho modulu (vytvor� sa presn� k�pia vr�tane nastaven�.");

define("L_ALIAS_FUNC_A", "abstrakt");
define("L_ALIAS_FUNC_B", "fulltext odkaz");
define("L_ALIAS_FUNC_C", "podmienka");
define("L_ALIAS_FUNC_D", "d�tum");
define("L_ALIAS_FUNC_E", "edit�cia �l�nku");
define("L_ALIAS_FUNC_F", "odkaz na pln� text");
define("L_ALIAS_FUNC_G", "v��ka obr�zku");
define("L_ALIAS_FUNC_H", "zobrazi�");
define("L_ALIAS_FUNC_I", "zdroj obr�zku");
define("L_ALIAS_FUNC_L", "pole s odkazom");
define("L_ALIAS_FUNC_N", "id");
define("L_ALIAS_FUNC_S", "url");
define("L_ALIAS_FUNC_T", "pln� text");
define("L_ALIAS_FUNC_W", "���ka obr�zku");
define("L_ALIAS_FUNC_0", "- �iadna -");

define("L_ALIASES", "Aliasy pre polia v datab�ze");
define("L_ALIAS1", "Alias 1"); 
define("L_ALIAS_HLP", "Mus� za��na� znakmi \"_#\".<br>Alias mus� by� presne 10 znakov dlh� vr�tane \"_#\".<br>Mal by by� kapit�lkami."); 
define("L_ALIAS_FUNC", "Funkcia"); 
define("L_ALIAS_FUNC_F_HLP", "Funkcia, ktor� zabezpe�� zobrazenie polia na str�nke"); 
define("L_ALIAS_FUNC_HLP", "Doplnkov� parameter odovzd�van� zobrazovacej funkcii. Podrobnosti vi� include/item.php3 file"); 
define("L_ALIAS_HELP", "N�poveda"); 
define("L_ALIAS_HELP_HLP", "N�povedn� text pre tento alias"); 
define("L_ALIAS2", "Alias 2"); 
define("L_ALIAS3", "Alias 3"); 

define("L_TOP_HLP", "HTML k�d, ktor� sa zobraz� pred k�dom modulu");
define("L_FORMAT_HLP", "Sem patr� HTML k�d v kombin�cii s aliasmi uveden�mi dole na str�nke
                     <br>Aliasy bud� v momente zobrazovania na web nahraden� skuto�n�mi hodnotami z datab�zy");
define("L_BOTTOM_HLP", "HTML k�d, ktor� sa zobraz� za vlastn�m k�dom modulu");
define("L_EVEN_ROW_HLP", "TIP: Rozl�en�m p�rnych a nep�rnych z�znamov m��ete dosiahn�� napr�klad farebn� odl�enie riadkov");

define("L_REMOVE_HLP", "Odstr�ni pr�zdn� z�tvorky a pod. Pou�ite ## ako oddelova�.");

define("L_COMPACT_HELP", "Na tejto str�nke je mo�n� nastavi�, �o sa objav� na str�nke preh�adu spr�v");
define("L_A_FULLTEXT_HELP", "Na tejto str�nke je mo�n� nastavi�, �o sa objav� na str�nke pri prezeran� tela spr�vy");
define("L_PROHIBITED", "Zak�zan�");
define("L_HTML", "HTML");
define("L_PLAIN_TEXT", "Oby�ajn� text");
define("L_A_DELSLICE", "Spr�va modulu - Vymaza� modulu");
define("L_DELSLICE_TIT", L_A_DELSLICE);
define("L_DELSLICE_HDR", "Vyber modul pre vymazanie");
define("L_DEL_SLICE_HLP","<p>Je mo�n� vymaza� len moduly, ktor� boli ozna�en� pre vymazanie na str�nke &quot;<b>". L_SLICE_SET ."</b>&quot;</p>");
define("L_A_DELSLICE", L_DELSLICE_TIT);
define("L_DELETE_SLICE", "Naozaj chcete vymaza� tento modul a v�etky jeho spr�vy?");
define("L_NO_SLICE_TO_DELETE", "�iaden modul nebol ozna�en� za vymazan�");
define("L_NO_SUCH_SLICE", "Zl� ��slo modulu");
define("L_NO_DELETED_SLICE", "Modul nie je ozna�en� za vymazan�");
define("L_DELSLICE_OK", "Modul bol vymazan�, tabu�ky boly optimalizovan�");
define("L_DEL_SLICE", "Zmaza� modul");
define("L_FEED_STATE", "Zdie�anie tohto po�a");
define( "L_STATE_FEEDABLE", "Kop�rova� obsah" );
define( "L_STATE_UNFEEDABLE", "Nekop�rova�" );
define( "L_STATE_FEEDNOCHANGE", "Kop�rova� nemenitelne" );
define( "L_INPUT_FEED_MODES_HLP", "M� sa kop�rova� obsah tohoto po�a do �al��ch modulov pri v�mene spr�v mezi modulmi?");
define("L_CANT_CREATE_IMG_DIR","Nie je mo�n� vytvori� adres�r pre obr�zky");
    

  # constants for View setting 
define('L_VIEWS','Poh�ady');
define('L_ASCENDING','Vzostupne');
define('L_DESCENDING','Zostupne');
define('L_NO_PS_VIEWS','Nem�te pr�vo meni� poh�ady');
define('L_VIEW_OK','Poh�ad bol �spe�ne zmenen�');
define('L_A_VIEW_TIT','Spr�va modulu - defin�cia Poh�adu');
define('L_A_VIEWS','Spr�va modulu - defin�cia Poh�adu');
define('L_VIEWS_HDR','Definovan� poh�ady');
define('L_VIEW_DELETE_OK','Poh�ad bol �spe�ne zmazan�');
define('L_DELETE_VIEW','Naozaj chcete zmaza� vybran� poh�ad?');
define('L_V_BEFORE',L_COMPACT_TOP);
define('L_V_ODD',L_ODD_ROW_FORMAT);
define('L_V_EVENODDDIF',L_EVEN_ODD_DIFFER);
define('L_V_EVEN',L_EVEN_ROW_FORMAT);
define('L_V_AFTER',L_COMPACT_BOTTOM);
define('L_V_GROUP_BY1','Zoskupi� pod�a');
define('L_V_GROUP1DIR',' ');
define('L_V_GROUP_BY2',L_V_GROUP_BY1);
define('L_V_GROUP2DIR',' ');
define('L_V_GROUP','Nadpis skupiny');
define('L_V_REMOVE_STRING',L_COMPACT_REMOVE);
define('L_V_MODIFICATION','Typ');
define('L_V_PARAMETER','Parameter');
define('L_V_IMG1','Obr�zok 1');
define('L_V_IMG2','Obr�zok 2');
define('L_V_IMG3','Obr�zok 3');
define('L_V_IMG4','Obr�zok 4');
define('L_V_ORDER1','Zoradi�');
define('L_V_ORDER1DIR',' ');
define('L_V_ORDER2','Zoradi� druhotne');
define('L_V_ORDER2DIR',' ');
define('L_V_SELECTED','Pou�i� vybran� �l�nok');
define('L_V_COND1FLD','Podmienka 1');
define('L_V_COND1OP',' ');
define('L_V_COND1COND',' ');
define('L_V_COND2FLD','Podmienka 2');
define('L_V_COND2OP',' ');
define('L_V_COND2COND',' ');
define('L_V_COND3FLD','Podmienka 3');
define('L_V_COND3OP',' ');
define('L_V_COND3COND',' ');
define('L_V_LISTLEN',L_D_LISTLEN);
define('L_V_FLAG','Flag');
define('L_V_SCROLLER','Zobrazi� rolovanie str�nok');
define('L_V_ADITIONAL','�al��');
define('L_COMPACT_VIEW','Preh�ad');
define('L_FULLTEXT_VIEW','�l�nok');
define('L_DIGEST_VIEW','Obsah - s�hrn');
define('L_DISCUSSION_VIEW','Diskusia');
define('L_RELATED_VIEW','S�visiace �l�nky');
define('L_CONSTANT_VIEW','Zobrazenie kon�t�nt');
define('L_RSS_VIEW','V�mena spr�v RSS');
define('L_STATIC_VIEW','Statick� str�nka');
define('L_SCRIPT_VIEW','JavaScript');

define("L_MAP","Mapovanie");
define("L_MAP_TIT","Spr�va modulu - v�mena spr�v - mapovanie pol�");
define("L_MAP_FIELDS","Mapovanie pol�");
define("L_MAP_TABTIT","V�mena spr�v - mapovanie pol�");
define("L_MAP_FROM_SLICE","Mapovanie z modulu");
define("L_MAP_FROM","Z");
define("L_MAP_TO","Do");
define("L_MAP_DUP","Ned� sa mapova� do rovnak�ho po�a");
define("L_MAP_NOTMAP","-- Nemapova� --");
define("L_MAP_OK","Nastavenie mapovania pol� �spe�ne zmenen�");
    
define("L_STATE_FEEDABLE_UPDATE", "Kop�rova� obsah a zmeny" );
define("L_STATE_FEEDABLE_UPDATE_LOCKED", "Kop�rova� obsah a zmeny nemenite�ne");

define("L_SITEM_ID_ALIAS",'alias pro skr�ten� ��slo �l�nku');
define("L_MAP_VALUE","-- Hodnota --");
define("L_MAP_VALUE2","Hodnota");


/*
$Log$
Revision 1.2  2001/06/21 14:15:44  honzam
feeding improved - field value redefine possibility in se_mapping.php3

Revision 1.1  2001/06/12 16:07:22  honzam
new feeding modes -  "Feed & update" and "Feed & update & lock"

Revision 1.28  2001/05/10 10:01:43  honzam
New spanish language files, removed <form enctype parameter where not needed, better number validation

Revision 1.27  2001/04/17 21:32:08  honzam
New conditional alias. Fixed bug of not displayed top/bottom HTML code in fulltext and category

Revision 1.26  2001/03/30 11:54:35  honzam
offline filling bug and others small bugs fixed

Revision 1.25  2001/03/20 16:10:37  honzam
Standardized content management for items - filler, itemedit, offline, feeding
Better feeding support

Revision 1.24  2001/02/20 13:25:16  honzam
Better search functions, bugfix on show on alias, constant definitions ...

Revision 1.21  2001/01/08 13:31:58  honzam
Small bugfixes

Revision 1.20  2000/12/23 19:56:50  honzam
Multiple fulltext item view on one page, bugfixes from merge v1.2.3 to v1.5.2

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

