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
define("L_VIEW_SLICE", "Zobraz modul");
define( "L_SLICE_HINT", '<br>Modul zahrniete do va�ej *.shtml str�nky 
                         pridan�m nasleduj�ceho riadku v HTML k�de: ');
define("L_ITEM_ID_ALIAS",'alias pre ��slo �l�nku');
define("L_EDITITEM_ALIAS",'alias pou��van� v administrat�vnych str�nk�ch index.php3 pre URL itemedit.php3');
define("L_LANG_FILE","Pou�it� language s�bor");
define("L_PARAMETERS","Parametre");
define("L_SELECT_APP","V�ber aplik�ci�");
define("L_SELECT_OWNER","V�ber vlastn�ka");

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
define("L_A_NEWUSER", "Nov� u�ivate� v syst�me");
define("L_NEWUSER_HDR", "Nov� u�ivate�");
define("L_USER_LOGIN", "U�ivate�sk� meno");
define("L_USER_PASSWORD1", "Heslo");
define("L_USER_PASSWORD2", "Potvrdi� heslo");
define("L_USER_FIRSTNAME", "Meno");
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

define("L_A_SLICE_ADD", "Spr�va modulu - Pridanie modulu");
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
define("L_TOO_MUCH_GROUPS", "Pr�li� vela skup�n.");
define("L_NO_GROUPS", "Skupina nen�jden�");
define("L_GROUP_NAME", "Meno");
define("L_GROUP_DESCRIPTION", "Popis");
define("L_GROUP_SUPER", "Administrat�vna skupina");
define("L_ERR_GROUP_ADD", "Nie je mo�n� prida� skupinu do syst�mu");
define("L_NEWGROUP_OK", "Skupina bola �spe�ne pridan�");
define("L_ERR_GROUP_CHANGE", "Nie je mo�n� zmenit skupinu");
define("L_A_UM_USERS_TIT", "Spr�va u�ivate�ov - U�ivatelia");
define("L_A_UM_GROUPS_TIT", "Spr�va u�ivate�ov - Skupiny");
define("L_EDITGROUP_HDR", "Edit�cia skupiny");
define("L_NEWGROUP_HDR", "Nov� skupina");
define("L_GROUP_ID", "ID skupiny");
define("L_ALL_USERS", "V�etci u�ivatelia");
define("L_GROUPS_USERS", "U�ivatelia v skupine");
define("L_POST", "Posla�");
define("L_INSERT_PREV", "Vloit a pozriet");
define("L_POST_PREV", "Posla� a pozrie�");
define("L_OK", "OK");
define("L_ACTIVE_BIN_EXPIRED", "Aktu�lne - Expirovan�");
define("L_ACTIVE_BIN_PENDING", "Aktu�lne - Pripraven�");
define("L_ACTIVE_BIN_EXPIRED_MENU", "... expirovan�");
define("L_ACTIVE_BIN_PENDING_MENU", "... pripraven�");

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
define("L_INPUT_SHOW_FUNC_F_HLP","Funkcia, ktor� sa pou�ije pre zobrazenie pola vo vstupnom formul�ri. Niektor� pou��vaj� Kon�tanty, niektor� pou��vaj� Parametre. Viac inform�ci� n�jdete v Sprievodcovi s N�povedou.");
define("L_INPUT_SHOW_FUNC_C_HLP","Vyberte Skupinu Kon�t�nt alebo Modul.");
define("L_INPUT_SHOW_FUNC_HLP","Parametre s� oddelen� dvojbodkou (:) alebo (v �peci�lnych pr�padoch) apostrofom (').");
define("L_INPUT_DEFAULT_F_HLP","Funkcia, ktor� sa pou�ije pre generovanie defaultn�ch hodn�t po�a:<BR>Now - aktu�lny d�tum<BR>User ID - identifik�tor prihl�sen�ho u�ivate�a<BR>Text - text uveden� v poli Parameter<br>Date - aktu�lny d�tum plus <Parametr> dn�");
define("L_INPUT_DEFAULT_HLP","Parameter pre defauln� hodnoty Text a Date (vi� vy��ie)");

define("L_INPUT_DEFAULT_TXT", "Text");
define("L_INPUT_DEFAULT_DTE", "D�tum");
define("L_INPUT_DEFAULT_UID", "ID u��vate�a");
define("L_INPUT_DEFAULT_NOW", "Aktu�lny d�tum a �as*");
define("L_INPUT_DEFAULT_VAR", "Variable"); # Added by Ram on 5th March 2002

define("L_INPUT_SHOW_TXT","Text Area");
define("L_INPUT_SHOW_EDT","Rich Edit Text Area");
define("L_INPUT_SHOW_FLD","Text Field");
define("L_INPUT_SHOW_SEL","Select Box");
define("L_INPUT_SHOW_RIO","Radio Button");
define("L_INPUT_SHOW_DTE","D�tum");
define("L_INPUT_SHOW_CHB","Check Box");
define("L_INPUT_SHOW_MCH", "Viacero Checkboxov");
define("L_INPUT_SHOW_MSE", "Select Box - multi");
define("L_INPUT_SHOW_FIL","Upload s�boru");
define("L_INPUT_SHOW_ISI","Rel�cia - Select Box");         # added 08/22/01
define("L_INPUT_SHOW_ISO","Rel�cia - Okno");               # added 08/22/01
define("L_INPUT_SHOW_WI2","Dva Select Boxy");              # added 08/22/01
define("L_INPUT_SHOW_PRE","Select Box s prednastaven�m");  # added 08/22/01
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
define("L_INPUT_INSERT_IDS","ID �l�nku");
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

$L_MONTH = array( 1 => 'Janu�r', 'Febru�r', 'Marec', 'Apr�l', 'M�j', 'J�n', 
		'J�l', 'August', 'September', 'Okt�ber', 'November', 'December');

define("L_NO_CATEGORY_FIELD","Pole kateg�rie nie je v tomto module definovan�.<br>  Pridajte pole kateg�rie do modulu na str�nke Polia.");
define("L_PERMIT_ANONYMOUS_POST","Anonymn� vkladanie");
define("L_PERMIT_OFFLINE_FILL","Off-line plnenie");
define("L_SOME_CATEGORY", "<kateg�ria>");

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

define("L_SLICE_URL", "URL modulu");
define("L_BRACKETS_ERR", "Z�tvorky v query nie s� sp�rovan�: ");
define("L_A_SLICE_ADD_HELP", "Nov� modul m��ete vytvori� na z�klade �abl�ny, alebo skop�rova� nastavenia z u� existuj�ceho modulu (vytvor� sa presn� k�pia vr�tane nastaven�.");
define("L_REMOVE_HLP", "Odstr�ni pr�zdn� z�tvorky a pod. Pou�ite ## ako oddelova�.");

define("L_COMPACT_HELP", "Na tejto str�nke je mo�n� nastavi�, �o sa objav� na str�nke preh�adu spr�v");
define("L_A_FULLTEXT_HELP", "Na tejto str�nke je mo�n� nastavi�, �o sa objav� na str�nke pri prezeran� tela spr�vy");
define("L_PROHIBITED", "Zak�zan�");
define("L_HTML", "HTML");
define("L_PLAIN_TEXT", "Oby�ajn� text");
define("L_A_DELSLICE", "Spr�va modulu - Vymaza� modul");
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
define("L_STATE_FEEDABLE", "Kop�rova� obsah" );
define("L_STATE_UNFEEDABLE", "Nekop�rova�" );
define("L_STATE_FEEDNOCHANGE", "Kop�rova� nemenitelne" );
define("L_INPUT_FEED_MODES_HLP", "M� sa kop�rova� obsah tohoto po�a do �al��ch modulov pri v�mene spr�v mezi modulmi?");
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

define("L_SITEM_ID_ALIAS",'alias pre skr�ten� c�slo cl�nku');
define("L_MAP_VALUE","-- Hodnota --");
define("L_MAP_VALUE2","Hodnota");
define("L_ORDER", "Zoradit");
define("L_INSERT_AS_NEW","Vlo�it ako nov�");

// constnt view constants
define("L_CONST_NAME_ALIAS", "Meno");
define("L_CONST_VALUE_ALIAS", "Hodnota");
define("L_CONST_PRIORITY_ALIAS", "Priorita");
define("L_CONST_GROUP_ALIAS", "Skupina hodn�t");
define("L_CONST_CLASS_ALIAS", "Nadkateg�rie (pou�iteln� len pre kateg�rie)");
define("L_CONST_COUNTER_ALIAS", "Poradov� c�slo hodnoty");
define("L_CONST_ID_ALIAS", "Identifikacn� c�slo hodnoty");

define('L_V_CONSTANT_GROUP','Skupina hodn�t');
define("L_NO_CONSTANT", "Hodnota nen�jden�");

// Discussion constants.
define("L_DISCUS_SEL","Zobrazit diskusiu");
define("L_DISCUS_EMPTY"," -- �iadna -- ");
define("L_DISCUS_HTML_FORMAT","Diskusiu form�tovat v HTML");
define("L_EDITDISC_ALIAS","Alias pou��van� v administrat�vnych str�nk�ch index.php3 pre URL discedit.php3");

define("L_D_SUBJECT_ALIAS","Alias pre predmet pr�spevku");
define("L_D_BODY_ALIAS"," Alias pre text pr�spevku");
define("L_D_AUTHOR_ALIAS"," Alias pre autora pr�spevku");
define("L_D_EMAIL_ALIAS","Alias pre e-mail autora");
define("L_D_WWWURL_ALIAS","Alias pre adresu WWW str�nok autora ");
define("L_D_WWWDES_ALIAS","Alias pre popis WWW str�nok autora");
define("L_D_DATE_ALIAS","Alias pre d�tum a cas odoslania pr�spevku");
define("L_D_REMOTE_ADDR_ALIAS","Alias pre IP adresu autorovho poc�taca");
define("L_D_URLBODY_ALIAS","Alias pre odkaz na text pr�spevku<br>
                             <i>Pou�itie: </i>v k�de pre prehladov� zobrazenie pr�spevkov<br>
                             <i>Pr�klad: </i>&lt;a href=_#URL_BODY>_#SUBJECT#&lt;/a>");
define("L_D_CHECKBOX_ALIAS","Alias pre checkbox pre vybranie pr�spevku");
define("L_D_TREEIMGS_ALIAS","Alias pre obr�zky");
define("L_D_ALL_COUNT_ALIAS","Alias pre pocet v�etk�ch pr�spevkov k dan�mu cl�nku");
define("L_D_APPROVED_COUNT_ALIAS","Alias pre pocet schv�len�ch pr�spevkov k dan�mu �l�nku");
define("L_D_URLREPLY_ALIAS","Alias pre odkaz na formul�r<br>
                             <i>Pou�itie: </i>v k�de pre pln� znenie pr�spevku<br>
                             <i>Pr�klad: </i>&lt;a href=_#URLREPLY&gt;Odpovedat&lt;/a&gt;");
define("L_D_URL","Alias pre odkaz na diskusiu<br>
                             <i>Po�itie: </i>v k�de formul�ra<br>
                             <i>Pr�klad: </i>&lt;input type=hidden name=url value=\"_#DISC_URL\">");
define("L_D_ID_ALIAS"," Alias pre c�slo pr�spevku<br>
                             <i>Po�itie: </i>v k�de formul�ra<br>
                             <i>Pr�klad: </i>&lt;input type=hidden name=d_parent value=\"_#DISC_ID#\">");
define("L_D_ITEM_ID_ALIAS"," Alias pre ��slo �l�nku<br>
                             <i>Po�itie: </i>v k�de formul�ra<br>
                             <i>Pr�klad: </i>&lt;input type=hidden name=d_item_id value=\"_#ITEM_ID#\">");

define("L_D_BUTTONS","Alias pre tlac�tka Zobrazit v�etko, Zobrazit vybran�, Pridat nov�<br>
                             <i>Pou�itie: </i>v spodnom HTML k�de");

define("L_D_COMPACT" , "HTML k�d pre prehladov� zobrazenie pr�spevkov");
define("L_D_SHOWIMGS" , "Zobrazit obr�zky");
define("L_D_ORDER" , "Zoradit");
define("L_D_FULLTEXT" ,"HTML k�d pre pln� znenie pr�spevku");

define("L_D_ADMIN","Spr�vy - Spr�va diskusn�ch pr�spevkov");
define("L_D_NODISCUS","�iadne diskusn� pr�spevky");
define("L_D_TOPIC","Titulok");
define("L_D_AUTHOR","Autor");
define("L_D_DATE","D�tum");
define("L_D_ACTIONS","Akcia");
define("L_D_DELETE","Zmazat");
define("L_D_EDIT","Editovat");
define("L_D_HIDE","Skryt");
define("L_D_APPROVE","Schv�lit");

define("L_D_EDITDISC","Spr�vy - Spr�va diskusn�ch pr�spevkov - Edit�cia pr�spevku");
define("L_D_EDITDISC_TABTIT","Edit�cia pr�spevku");
define("L_D_SUBJECT","Predmet");
define("L_D_AUTHOR","Autor");
define("L_D_EMAIL","E-mail");
define("L_D_BODY","Text pr�spevku");
define("L_D_URL_ADDRESS","WWW autora - URL");
define("L_D_URL_DES","WWW autora - popis");
define("L_D_REMOTE_ADDR","IP adresa poc�taca autora");

define('L_D_SELECTED_NONE',"Nebol vybran� �iaden pr�spevok");
define("L_D_DELETE_COMMENT","Chcete zmazat pr�spevok?");

define("L_D_FORM","HTML k�d formul�ra pre poslanie pr�spevku");
define("L_D_BACK","Sp�t");
define("L_D_ITEM","Cl�nok: ");
define("L_D_ADMIN2","Spr�va diskusn�ch pr�spevkov");

define("L_D_SHOW_SELECTED","Zobraz vybran�");
define("L_D_SHOW_ALL","Zobraz v�etko");
define("L_D_ADD_NEW","Pridaj nov�");

define("L_TOO_MUCH_RELATED","Je vybr�n�ch pr�li� vela s�visiacich cl�nkov.");
define("L_SELECT_RELATED","V�ber souvisiacich cl�nkov");
define("L_SELECT_RELATED_1WAY","�no");
define("L_SELECT_RELATED_2WAY","Vz�jomne");

// - Cross server networking --------------------------------------

define("L_INNER_IMPORT","Lok�lna v�mena");
define("L_INTER_IMPORT","Pr�jem z uzlov");
define("L_INTER_EXPORT","Zasielanie do uzlov");

define("L_NODES_MANAGER","Uzly");
define("L_NO_PS_NODES_MANAGER","Nem�te pr�va pre spr�vu uzlov");
define("L_NODES_ADMIN_TIT","Spr�va uzlov");
define("L_NODES_LIST","Zoznam uzlov");
define("L_NODES_YOUR_NODE","N�zov v�ho uzlu");
define("L_NODES_YOUR_GETXML","V� getxml je");
define("L_NODES_ADD_NEW","Pridanie uzlu");
define("L_NODES_EDIT","Edit�cia uzlu");
define("L_NODES_NODE_NAME","Meno uzlu ");
define("L_NODES_SERVER_URL","URL s�boru getxml.php3");
define("L_NODES_PASWORD","Heslo");
define("L_SUBMIT","Poslat");
define("L_NODES_SEL_NONE","Nebol vybran� uzol");
define("L_NODES_CONFIRM_DELETE","Naozaj chcete zmazat uzol?");
define("L_NODES_NODE_EMPTY","Meno uzla mus� byt vyplnen�");

define("L_IMPORT_TIT","Spr�va prijman�ch modulov");
define("L_IMPORT_LIST","Zoznam pr�jman�ch modulov do modulu ");
define("L_IMPORT_CONFIRM_DELETE","Naozaj chcete zru�it pr�jem z tohto modulu?");
define("L_IMPORT_SEL_NONE","Nebol vybran� modul");
define("L_IMPORT_NODES_LIST","Zoznam uzlov");
define("L_IMPORT_CREATE","Pr�jmat moduly z tohto uzla");
define("L_IMPORT_NODE_SEL","Nebol vybran� uzol");
define("L_IMPORT_SLICES","Zoznam pr�jman�ch modulov");
define("L_IMPORT_SLICES2","Zoznam dostupn�ch modulov z uzla ");
define("L_IMPORT_SUBMIT","Zvolte modul");
define("L_IMPORT2_OK","Pr�jem z modulu �spe�ne vytvoren�");
define("L_IMPORT2_ERR","Pr�jem z modulu bol u� vytvoren�");

define("L_RSS_ERROR","Nepodarilo se nadviazat spojenie alebo prijat �daje. Kontaktuje administr�tora");
define("L_RSS_ERROR2","Neplatn� heslo pre uzol: ");
define("L_RSS_ERROR3","Kontaktujte administr�tora lok�lneho uzla.");
define("L_RSS_ERROR4","�iadne dostupn� moduly. Nem�te pr�va pr�jmat �daje z tohto uzla. ".
 "Kontaktujte administr�tora vzdialen�ho modulu a skontrolujte, �e dostal va�e spr�vne u�ivatelsk� meno.");

define("L_EXPORT_TIT","Spr�va povolen� zasielania modulov");
define("L_EXPORT_CONFIRM_DELETE","Naozaj chcete zru�it povolenie zasielania tohto modulu?");
define("L_EXPORT_SEL_NONE","Nebol vybran� uzol a u�ivatel");
define("L_EXPORT_LIST","Zoznam uzlov a u�ivatelov, kde bude zasielan� modul ");
define("L_EXPORT_ADD","Pridajte uzol a u�ivatela");
define("L_EXPORT_NAME","Meno u�ivatela");
define("L_EXPORT_NODES","Zoznam uzlov");

define("L_RSS_TITL", "Meno modulu pre RSS");
define("L_RSS_LINK", "Odkaz na modul pre RSS");
define("L_RSS_DESC", "Kr�tk� popis (vlastn�k a meno) modulu pre RSS");
define("L_RSS_DATE", "D�tum v RSS prehlade je generovan� vo form�te RSS");

define("L_NO_PS_EXPORT_IMPORT", "Nem�te pr�vo exportovat / importovat moduly");
define("L_EXPORT_SLICE", "Export");
define("L_IMPORT_SLICE", "Import");
define("L_EXPIMP_SET", "�trukt�ra modulu");

define("L_E_EXPORT_TITLE", "Export �trukt�ry modulu");
define("L_E_EXPORT_MEMO", "Vyberte si jeden z dvoch sp�sobov exportu:");
define("L_E_EXPORT_DESC", "Pri exporte \"do in�ho Toolkitu\" sa bude exportovat len aktu�lna �abl�na "
		."a vy pre nu vyberiete nov� identifik�tor.");
define("L_E_EXPORT_DESC_BACKUP", "Pri exporte \"do Backupu\" si m��ete vybrat niekolko �abl�n naraz.");
define("L_E_EXPORT_MEMO_ID","Vyberte nov� identifik�tor �abl�ny dlh� presne 16 znakov: ");
define("L_E_EXPORT_SWITCH", "Export do Backupu");
define("L_E_EXPORT_SWITCH_BACKUP", "Export do in�ho Toolkitu");
define("L_E_EXPORT_IDLENGTH", "Dl�ka identifik�tora mus� byt 16 znakov, a nie ");
define("L_E_EXPORT_TEXT_LABEL", "Tento text si niekde ulo�te. M��ete ho pou�it pro import �abl�ny do Toolkitu:");
define("L_E_EXPORT_LIST", "Oznacte moduly, ktor� CHCETE exportovat:");

define("L_E_IMPORT_TITLE", "Import �trukt�ry modulov");
define("L_E_IMPORT_SEND", "Odoslat �trukt�ru modulov");
define("L_E_IMPORT_MEMO", "Import �trukt�ry modulu probehne takto:<br>"
			."Vlo�te exportovan� text do r�mika a stlacte tla��tko Odeslat.<br>"
			."�trukt�ra modulu s defin�ciami pol� sa nac�ta a prid� do Toolkitu.");
define("L_E_IMPORT_OPEN_ERROR","Nezn�ma chyba pri otev�van� s�boru.");
define("L_E_IMPORT_WRONG_FILE","CHYBA: Text nie je v poriadku. Skontrolujte, ci ste ho spr�vne skop�rovali z Exportu.");
define("L_E_IMPORT_WRONG_ID","CHYBA: ");
define("L_E_IMPORT_OVERWRITE", "Prep�sat");
define("L_E_IMPORT_IDLENGTH", "Dl�ka identifik�tora mus� byt 32 znakov, a nie ");

define("L_E_IMPORT_IDCONFLICT", "Moduly s niektor�mi ID u� existuj�. Zmente ID na pravej strane ��pky.<br> "
			."Pou��vajte len hexadecim�lne znaky 0-9,a-f. "
			."Ak urob�te nieco chybne (chybn� pocet znakov, chybn� znaky, alebo zmen�te ID vlavo od ��pky), "
			."bude pr�slu�n� ID pova�ovan� za nezmenen�.</p>"
			."Ak zvol�te PREP�SAT, prep�u sa v�etky �abl�ny s nezmenen�m ID a nov� se pridaj�. <br>"
			."Ak zvol�te ODOSLAT, �abl�ny s konfliktom ID sa budo� ignorovat a nov� se pridaj�.");
define ("L_E_IMPORT_COUNT", "Po�et importovan�ch �abl�n: %d.");			
define ("L_E_IMPORT_ADDED", "Pridan� boli:");
define("L_SHOW_RICH", "Zobraz toto pole v rich text editore (pouijte sa po nain�talov�n� potrebn�ch komponentov!)");
define("L_MAP_JOIN","-- Spojenie pol� --");
define ("L_E_IMPORT_OVERWRITTEN", "Prep�san� boli:");

define("L_PARAM_WIZARD_LINK", "Sprievodca s n�povedou");


// aliases used in se_notify.php3
define("L_NOTIFY_SUBJECT", "Predmet e-mailu (Subject)");
define("L_NOTIFY_BODY", "Text e-mailovej spr�vy");
define("L_NOTIFY_EMAILS", "E-mail adresa (jedna na riadok)");
define("L_NOTIFY_HOLDING", "<h4>Nov� spr�va v Z�sobn�ku</h4> Ktokolvek m��e byt informovan� o tom, �e bola pridan� nov� spr�va do z�sobn�ku. Adresy pr�jemcov nap�te ni��ie, do nasleduj�c�ch pol� vyplnte, text spr�vy, ktor� u�ivatelia dostan� emailom.");
define("L_NOTIFY_HOLDING_EDIT", "<h4>Spr�va v Z�sobn�ku bola zmenen�</h4> Ktokolvek m��e byt informovan� o tom, �e bola zmenen� spr�va v z�sobn�ku. Adresy pr�jemcov nap�te ni��ie, do nasleduj�c�ch pol� vyplnte, text spr�vy, ktor� u�ivatelia dostan� emailom.");
define("L_NOTIFY_APPROVED", "<h4>Nov� spr�va medzi Aktu�lnymi</h4> Ktokolvek m��e byt informovan� o tom, �e pribudla nov� vystaven� spr�va. Adresy pr�jemcov nap�te ni��ie, do nasleduj�c�ch pol� vyplnte, text spr�vy, ktor� u�ivatelia dostan� emailom.");
define("L_NOTIFY_APPROVED_EDIT", "<h4>Aktu�lna spr�va zmenen�</h4> Ktokolvek m��e byt informovan� o tom, �e bola zmenen� vystaven� spr�va. Adresy pr�jemcov nap�te ni��ie, do nasleduj�c�ch pol� vyplnte, text spr�vy, ktor� u�ivatelia dostan� emailom.");
define("L_NOTIFY", "Upozornenie e-mailom");
define("L_A_NOTIFY_TIT", "E-mailov� upozornenia na udalost");
define("L_NOITEM_MSG", "Hl�ka 'Nen�jden� �iadna spr�va'");
define("L_NOITEM_MSG_HLP", "spr�va, ktor� sa objav� pri nen�jden� �iadneho odpovedaj�ceho z�znamu");

# ---------------- Users profiles -----------------------------------------
define('L_PROFILE','Profil');
define('L_DEFAULT_USER_PROFILE','Spolo�n� profil');
define('L_PROFILE_DELETE_OK','Pravidlo �spe�ne vymazan�');
define('L_PROFILE_ADD_OK','Pravidlo pridan�');
define('L_PROFILE_ADD_ERR','Chyba pri prid�van� nov�ho pravidla');
define('L_PROFILE_LISTLEN','Po�et spr�v');
define('L_PROFILE_ADMIN_SEARCH','Filter spr�v');
define('L_PROFILE_ADMIN_ORDER','Radenie');
define('L_PROFILE_HIDE','Skryt pole');
define('L_PROFILE_HIDEFILL','Skryt a vyplnit');
define('L_PROFILE_FILL','Vyplnit pole');
define('L_PROFILE_PREDEFINE','Prednastavit pole');
define('L_A_PROFILE_TIT','Spr�va modulu - U�ivatelsk� profily');
define('L_PROFILE_HDR','Nastaven� pravidl�');
define('L_NO_RULE_SET','�iadne pravidlo nebolo definovan�');
define('L_PROFILE_ADD_HDR','Pridat pravidlo');
define('L_PROFILE_LISTLEN_DESC','po�et spr�v zobrazen�ch v administr�cii');
define('L_PROFILE_ADMIN_SEARCH_DESC','prednastavenie "Hladanie" v administr�cii');
define('L_PROFILE_ADMIN_ORDER_DESC','prednastavenie "Zoradit" v administr�cii');
define('L_PROFILE_HIDE_DESC','skryt pole vo vstupnom formul�ri');
define('L_PROFILE_HIDEFILL_DESC','skryt pole vo vstupnom formul�ri a vyplnit ho danou hodnotou');
define('L_PROFILE_FILL_DESC','vyplnit pole vo vstupnom formul�ri v�dy danou hodnotou');
define('L_PROFILE_PREDEFINE_DESC','prednastavit hodnotu do pola vo vstupnom formul�ri');
define('L_VALUE',L_MAP_VALUE2);
define('L_FUNCTION',L_ALIAS_FUNC);
define('L_RULE','Pravidlo');

define('L_ID_COUNT_ALIAS','po�et nalezen�ch �l�nk�');
define('L_V_NO_ITEM','HTML code for "No item found" message');
define("L_NO_ITEM_FOUND", "Nenalezena ��dn� zpr�va");
define('L_INPUT_SHOW_HCO','Hierachie konstant');

define("L_CONSTANT_HIERARCH_EDITOR","Editovat v Hierarchick�m editoru (umo��uje ur�it hierarchii hodnot)");
define("L_CONSTANT_PROPAGATE","Propagovat zm�ny do st�vaj�c�ch �l�nk�");
define("L_CONSTANT_OWNER","Vlastn�k skupiny - web�k");
define("L_A_CONSTANTS_HIER_EDT","Spr�va web�ku - Hiearchick� nastaven� hodnot");
define("L_CONSTANT_HIER_SORT","Zm�ny nebudou ulo�eny do datab�ze, dokud nestisknete tla��tko dole na str�nce.<br>Konstanty jsou �azeny zaprv� podle �azen� a zadruh� podle N�zvu.");
define("L_CONSTANT_DESC","Popis");
define("L_CONSTANT_HIER_SAVE","Ulo� v�echny zm�ny do datab�ze");

// constants used in param wizard only:
require  $GLOBALS[AA_INC_PATH]."en_param_wizard_lang.php3";
// new constants to be translated are here. Leave this "require" always at the 
// end of this file
// If You want to translate the new texts (which is in new_news_lang.php3 file),
// just copy them from the new_news_lang.php3 file here and translate.
// IMPORTANT: Leave the require new_news_lang.php3 as the last line of this
// file - it will not redefine the constant You translated and helps You when 
// we add new texts !!!
require  $GLOBALS[AA_INC_PATH]."new_news_lang.php3"; ?>