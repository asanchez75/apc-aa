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
# must correspond with fileneme in $ActionAppConfig[xxx][file]!!
define("CONFIG_FILE", "cz_news_lang.php3");

define("HTML_PAGE_BEGIN", 
'<!DOCTYPE html public "-/W3C/DTD HTML 4.0 Transitional/EN">
  <HTML>
  <HEAD>
  <LINK rel=StyleSheet href="../'. ADMIN_CSS .'" 
        type="text/css" title="CPAdminCSS">
  <meta http-equiv="Content-Type" content="text/html; charset=windows-1250">');
               
define("IMG_UPLOAD_MAX_SIZE", "400000");    // max size of file in picture uploading
define("IMG_UPLOAD_URL", "http://web.ecn.cz/aauser/img_upload/");
define("IMG_UPLOAD_PATH", "/usr/local/httpd/htdocs/aauser/img_upload/");
define("EDITOR_GRAB_LEN", 200);                 // not used, i think
define("EDIT_ITEM_COUNT", 20);                  // number of items in editor window

# Default values for database fields
define("DEFAULT_EDIT_FIELDS",    // shown fields (headline if allways shown)
 "y".  // abstract
 "y".  // html_formatted
 "y".  // full_text
 "y".  // highlight
 "y".  // hl_href
 "y".  // link_only
 "y".  // place
 "y".  // source
 "y".  // source_href
 "y".  // status_code
 "y".  // language_code
 "y".  // cp_code
 "y".  // category_id
 "y".  // img_src
 "y".  // img_width
 "y".  // img_height
 "y".  // posted_by
 "y".  // e_posted_by
 "y".  // publish_date
 "y".  // expiry_date
 "y".  // edit_note
 "y".  // reserved
 "y".  // reserved
 "y".  // reserved
 "y".  // reserved
 "y".  // reserved
 "y".  // reserved
 "y".  // reserved
 "y".  // reserved
 "y"); // reserved
define("DEFAULT_NEEDED_FIELDS", 
 "y".  // abstract
 "n".  // html_formatted
 "n".  // full_text
 "n".  // highlight
 "n".  // hl_href
 "n".  // link_only
 "n".  // place
 "n".  // source
 "n".  // source_href
 "n".  // status_code
 "n".  // language_code
 "n".  // cp_code
 "n".  // category_id
 "n".  // img_src
 "n".  // img_width
 "n".  // img_height
 "n".  // posted_by
 "n".  // e_posted_by
 "n".  // publish_date
 "n".  // expiry_date
 "n".  // edit_note
 "n".  // reserved
 "n".  // reserved
 "n".  // reserved
 "n".  // reserved
 "n".  // reserved
 "n".  // reserved
 "n".  // reserved
 "n".  // reserved
 "n"); // reserved

define("DEFAULT_SEARCH_SHOW", 
 "n".  // slice
 "y".  // category
 "y".  // author
 "y".  // language
 "y".  // from
 "y".  // to
 "y".  // headline
 "y".  // abstract
 "y".  // full_text
 "y".  // edit_note
 "y".  // reserve
 "y".  // reserve
 "y".  // reserve
 "y"); // reserve
define("DEFAULT_SEARCH_DEFAULT", 
 "y".  // headline
 "y".  // abstract
 "y".  // full_text
 "n".  // edit_note
 "n".  // reserve
 "n".  // reserve 
 "n"); // reserve 
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

     
# database fields labels
define("L_HEADLINE", "Titulek");
define("L_POSTDATE", "Za�azeno");
define("L_POSTED_BY", "Autor");
define("L_PUBLISH_DATE", "Datum zve�ejn�n�");
define("L_EXPIRY_DATE", "Datum vypr�en�");
define("L_CATEGORY", "Kategorie");
define("L_FIELDS", "Polo�ky");
define("L_ABSTRACT", "Abstrakt");
define("L_FULL_TEXT", "Text p��sp�vku");
define("L_STATUS_CODE", "Stav");
define("L_LANGUAGE_CODE", "Jazyk");
define("L_CP_CODE", "K�dov�n�");
define("L_LINK_ONLY", "Odkaz ven");
define("L_HL_HREF", "URL pro odkaz");
define("L_FT_FORMATTING", "Form�tov�n�");
define("L_FT_FORMATTING_HTML", "HTML k�d");
define("L_FT_FORMATTING_PLAIN", "Prost� text"); 
define("L_HTML_FORMATTED", "HTML k�d");
define("L_HIGHLIGHT", "D�le�it� zpr�va");
define("L_IMG_SRC","URL obr�zku"); 
define("L_IMG_WIDTH","���ka obr�zku"); 
define("L_IMG_HEIGHT","V��ka obr�zku");
define("L_E_POSTED_BY","E-mail"); 
define("L_PLACE","M�sto");
define("L_SOURCE","Zdroj");
define("L_SOURCE_HREF","URL zdroje");
define("L_CREATED_BY","Autor");
define("L_LASTEDIT","Naposled editoval");
define("L_AT","dne");   
define("L_EDIT_NOTE","Pozn�mka editora"); 
define("L_IMG_UPLOAD","Ulo�en� obr�zku"); 
define("L_CANT_UPLOAD","Obr�zek nelze ulo�it"); 

# toolkit aplication dependent labels
define("L_HLP_HEADLINE",'alias pro titulek');
define("L_HLP_CATEGORY",'alias pro jm�no kategorie');
define("L_HLP_HDLN_URL",'alias pro URL zpr�vy<br>(bude nahrazeno bu� "URL pro odkaz" (je-li za�krtnut "odkaz ven") nebo odkazem na text p��sp�vku)<div class=example><em>P��klad: </em>&lt;a href=_#HDLN_URL&gt;_#HEADLINE&lt;/a&gt;</div>');
define("L_HLP_POSTDATE",'alias pro datum za�azen� p��sp�vku');
define("L_HLP_PUB_DATE",'alias pro datum zve�ejn�n�');
define("L_HLP_EXP_DATE",'alias pro datum vypr�en� platnosti p��sp�vku');
define("L_HLP_ABSTRACT",'alias pro abstrakt<br>(pokud abstrabt nen� v datab�zi vypln�n, pak se zobrazi <i>Grab_length</i> znak� z textu p��sp�vku)');
define("L_HLP_FULLTEXT",'alias pro text p��sp�vku<br>(text m��e b�t HTML form�tovan� �i nikoliv - v�e se ��d� za�krtnut�m pol��ka HTML k�d)');
define("L_HLP_IMAGESRC",'alias pro URL obr�zku<br>(pokud nen� odkaz na obr�zek v datab�zi, pou�ije se standardn� obr�zek (viz konstanta NO_PICTURE_URL v souboru cz_*_lang.php3))<div class=example><em>P��klad: </em>&lt;img src="_#IMAGESRC"&gt;</div>');
define("L_HLP_SOURCE",'alias pro zdroj<br>(viz tak� _#LINK_SRC)');
define("L_HLP_SRC_URL",'alias pro URL zdroje<br>(pokud URL zdroje neni zadano, pouzile se standardni URL (viz konstanta NO_SOURCE_URL v souboru cz_*_lang.php3))<br>Pouzijte _#LINK_SRC pro odkaz na zdroj v�etn� jm�na zdroje.<div class=example><em>P��klad: </em>&lt;a href"_#SRC_URL#"&gt;&lt;img src="source.gif"&gt;&lt;/a&gt;</div>');
define("L_HLP_LINK_SRC",'alias pro zdroj v�etn� odkazu.<br>(pokud URL zdroje je vyplneno, alias je nahrazen &lt;a href="_#SRC_URL#"&gt;_#SOURCE##&lt;/a&gt;, jinak se pou�ije jen _#SOURCE##)');
define("L_HLP_PLACE",'alias pro m�sto');
define("L_HLP_POSTEDBY",'alias pro autora');
define("L_HLP_E_POSTED",'alias pro e-mail autora');
define("L_HLP_CREATED",'alias pro datum za�azen�');
define("L_HLP_EDITEDBY",'alias pro cloveka, ktery tento p��sp�vek naposledy editoval');
define("L_HLP_LASTEDIT",'alias pro datum posledn� editace');
define("L_HLP_EDITNOTE","alias pro pozn�mku editora");
define("L_HLP_IMGWIDTH",'alias pro ���ku obr�zku<br>(pokud nen� ���ka zadan�, program se pokus� odstranit <em>width=</em> atribut z form�tovac�ho �et�zce<div class=example><em>P��klad: </em>&lt;img src="_#IMAGESRC" width=_#IMGWIDTH height=_#IMG_HGHT&gt;</div>');
define("L_HLP_IMG_HGHT",'alias pro v��ku obr�zku<br>(pokud nen� v��ka zadan�, program se pokus� odstranit <em>height=</em> atribut z form�tovac�ho �et�zce<div class=example><em>P��klad: </em>&lt;img src="_#IMAGESRC" width=_#IMGWIDTH height=_#IMG_HGHT&gt;</div>');
define("L_HLP_ITEM_ID",'alias pro ��slo p��sp�vku<br>(lze pou��t jako parametr sh_itm= p�d�van� skriptu slice.php3 (nebo souboru .shtml, kter� tento do sebe vkl�d� (include)))');

define("L_GRAB_LEN", "Po�et znak� textu pou�it�ch jako abstrakt");
define("L_D_EXPIRY_LIMIT", "Limit vypr�en� [dny]");
define("L_D_LISTLEN", "Po�et vypisovan�ch zpr�v");
define("L_MSG_PAGE", "Zpr�va aplikace");   // title of message page
define("L_EDITOR_TITLE", "Spr�va zpr�v");
define("L_FULLTEXT_FORMAT", "HTML k�d textu zpr�vy");
define("L_A_FULLTEXT_TIT", "Spr�va web�ku - Vzhled jedn� zpr�vy");
define("L_FULLTEXT_HDR", "HTML k�d pro zobrazen� zpr�vy");
define("L_A_COMPACT_TIT", "Spr�va web�ku - Vzhled p�ehledu zpr�v");
define("L_COMPACT_HDR", "HTML k�d pro p�ehled zpr�v");
define("L_A_FILTERS_TIT", "Spr�va web�ku - Filtry pro v�m�nu zpr�v");
define("L_FLT_SETTING", "Nastaven� filtr� pro p��jem zpr�v");
define("L_FLT_FROM_SL", "Filtr pro p��jem zpr�v z web�ku");
define("L_FLT_APPROVED", "Jako aktu�ln� zpr�vu");
define("L_ITEM_HDR", "Vstupn� formul�� zpr�vy");
define("L_A_ITEM_ADD", "P�idat zpr�vu");
define("L_A_ITEM_EDT", "Upravit zpr�vu");
define("L_IMP_EXPORT", "Povolit zas�l�n� zpr�v do web�ku:");
define("L_EXPORT_TO_ALL", "Povol zas�l�n� zpr�v do jak�hokoliv web�ku");
define("L_IMP_IMPORT", "P�ij�mat zpr�vu z web�ku:");
define("L_ADD_NEW_ITEM", "Nov� zpr�va");
define("L_ERR_FEEDED_ITEMS", "V ko�i je zpr�va, kter� byla zasl�na jin�mu web�ku - nem��e b�t odstran�na.");
define("L_EDIT_ITEMS", "Editace zpr�vy");
define("L_VIEW_FULLTEXT", "Zobraz zpr�vu");
define("L_FULLTEXT", "Text zpr�vy");
define("L_FEEDING", "V�m�na zpr�v");
define("L_HIGHLIGHTED", "D�le�it� zpr�va");
define("L_NO_HIGHLIGHTED", "Oby�ejn� zpr�va");
define("L_A_SLICE_IMP", "Spr�va web�ku - V�m�na zpr�v");
define("L_NO_PS_EDIT_ITEMS", "Nem�te pr�vo upravovat zpr�vy v tomto web�ku");
define("L_NO_DELETE_ITEMS", "Nem�te pr�vo mazat zpr�vy");
define("L_NO_PS_MOVE_ITEMS", "Nem�te pr�vo p�esouvat zpr�vy");
define("L_NO_PS_COPMPACT", "Nem�te pr�vo upravovat vzhled p�ehledu zpr�v");
define("L_NO_PS_FULLTEXT", "Nem�te pr�vo upravovat vzhled textu zpr�vy");
define("L_NO_PS_FEEDING", "Nem�te pr�vo m�nit nastaven� v�m�ny zpr�v");
define("L_COMPACT_OK", "Vzhled p�ehledu zpr�v byl �sp�n� zm�n�n");
define("L_IMPORT_OK", "Nastaven� v�m�ny zpr�v bylo �sp�n� zm�n�no");
define("L_FULLTEXT_OK", "Vzhled textu zpr�vy byl �sp�n� zm�n�n");




# toolkit aplication independent labels (should not be true)

define("L_ACTIVE_BIN", "Aktu�ln�");
define("L_HOLDING_BIN", "Z�sobn�k");
define("L_TRASH_BIN", "Ko�");

define("L_SHORT_NAME", "Zkr�cen� jm�no");
define("L_DELETED", "Vymaz�n");
define("L_SLICE_DEFAULTS", "P�ednastaven� hodnoty polo�ek");

define("L_ERR_CANT_CHANGE", "Nepoda�ilo se zm�nit nastaven� web�ku");
define("L_KONSTANTS_HLP", "Pou�ijte n�sleduj�c� aliasy nam�sto polo�ek datab�ze");

define("L_ODD_ROW_FORMAT", "Lich� ��dky");
define("L_EVEN_ROW_FORMAT", "Sud� ��dky");
define("L_EVEN_ODD_DIFFER", "Pou�ij odli�n� HTML k�d pro sud� �adky");
define("L_CATEGORY_FORMAT", "Nadpis kategorie");
define("L_CATEGORY_SORT", "Se�a� zpr�vy v p�ehledu podle kategorie");
define("L_COMPACT_TOP", "Horn� HTML k�d");
define("L_COMPACT_BOTTOM", "Spodn� HTML k�d");
define("L_A_COMPACT", L_A_COMPACT_TIT);

define("L_FLT_FROM", "Z");
define("L_FLT_TO", "Do");
define("L_FLT_CATEGORIES", "Kategorie");
define("L_ALL_CATEGORIES", "V�echny kategorie");
define("L_THE_SAME", "-- stejn� --");

define("L_IMP_EXPORT_Y", "Zas�l�n� povoleno");
define("L_IMP_EXPORT_N", "Zas�l�n� zak�z�no");
define("L_IMP_IMPORT_Y", "P�ij�mat");
define("L_IMP_IMPORT_N", "Nep�ij�mat");

//define("", "");

define("L_RELOGIN", "P�ihl�sit se jako jin� u�ivatel");

define("L_ERR_IN", "Chyba v");
define("L_ERR_NEED", "mus� b�t vypln�no");
define("L_ERR_LOG", "pou�ijte znaky a-z, A-Z a 0-9");
define("L_ERR_LOGLEN", "mus� b�t dlouh� 5 - 32 znak�");
define("L_ERR_NO_SRCHFLDS", "Nebylo zad�no prohled�van� pole!");
define("L_NO_PRMS_SLICE", "Nem�te pr�vo na zm�nu nastaven� web�ku");

define("L_EDIT", "Editace");
define("L_EDIT_SLICE", "Editace web�ku");
define("L_DELETE", "Vymazat");
define("L_UPDATE", "Zm�nit");
define("L_RESET", "Vymazat formul��");
define("L_CANCEL", "Zru�it");
define("L_ACTION", "Akce");
define("L_INSERT", "Vlo�it");
define("L_VIEW", "Uk�zat");
define("L_NEW", "Nov�");
define("L_GO", "Jdi");
define("L_ADD", "P�idat");
define("L_USERS", "U�ivatel�");
define("L_GROUPS", "Skupiny");
define("L_ORGANIZATION", "Organizace");
define("L_SEARCH", "Hled�n�");
define("L_RENAME", "P�ejmenov�n�");
define("L_DEFAULTS", "Default");
define("L_SLICE", "Web�k");
define("L_DELETE_TRASH", "Vysypat ko�");
define("L_DELETED_SLICE", "Nebyl nalezen ��dn� web�k, ke kter�mu m�te p��stup");
define("L_SLICE_URL", "URL web�ku");
define("L_CURRENT_USERS", "Sou�asn� u�ivatel�");
define("L_A_NEWUSER", "Nov� u�ivatel v syst�mu");
define("L_NEWUSER_HDR", "Nov� u�ivatel");
define("L_USER_LOGIN", "U�ivatelsk� jm�no");
define("L_USER_PASSWORD1", "Heslo");
define("L_USER_PASSWORD2", "Potvrdit helso");
define("L_USER_FIRSTNAME", "Jm�no");
define("L_USER_SURNAME", "P��jmen�");
define("L_USER_MAIL", "E-mail");
define("L_A_USERS_TIT", "Spr�va web�ku - Spr�va u�ivatel�");

define("L_ROLE_AUTHOR", "Autor");
define("L_ROLE_EDITOR", "Editor");
define("L_ROLE_ADMINISTRATOR", "Administr�tor");
define("L_ROLE_SUPER", "Super");

define("L_SLICE_ADM", "Spr�va web�ku");
define("L_MAIN_SET", "Hlavn� nastaven�");
define("L_SLICE_SET", L_SLICE);
define("L_PERMISSIONS", "Nastaven� pr�v");
define("L_PERM_CHANGE", "Zm�na sou�asn�ch pr�v");
define("L_PERM_ASSIGN", "Nov� osoba/skupina");
define("L_PERM_NEW", "Hledej u�ivatele nebo skupinu");
define("L_PERM_SEARCH", "P�i�azen� nov�ch pr�v");
define("L_PERM_CURRENT", "Zm�na sou�asn�ch pr�v");
define("L_USER_NEW", "Nov� u�ivatel");
define("L_DESIGN", "Vzhled");
define("L_COMPACT", "P�ehled zpr�v");
define("L_COMPACT_REMOVE", "Odstra�ovan� �et�zce");
define("L_IMPORT", "Zas�l�n� & P��jem");
define("L_FILTERS", "Filtry");

define("L_A_SLICE_ADD", "Spr�va web�ku - P�id�n� web�ku");
define("L_A_SLICE_EDT", "Spr�va web�ku - �prava web�ku");
define("L_A_SLICE_CAT", "Spr�va web�ku - Nastaven� kategori�");
define("L_A_SLICE_USERS", "Spr�va web�ku - U�ivatel�");
define("L_A_FIELDS_EDT", "Spr�va web�ku - Nastaven� polo�ek");
define("L_FIELD", "Polo�ka");
define("L_FIELD_IN_EDIT", "Zobrazit");
define("L_NEEDED_FIELD", "Povinn�");
define("L_FIELDS_HDR", "Polo�ky zpr�vy");
define("L_A_SEARCH_TIT", "Spr�va web�ku - Vyhled�vac� formul��");

define("L_SEARCH_HDR", "Vyhled�vac� krit�ria");
define("L_SEARCH_HDR2", "Vyhled�vat v polo�k�ch");
define("L_SEARCH_SHOW", "Zobrazit");
define("L_SEARCH_DEFAULT", "Standardni nastaven�");
define("L_SEARCH_SET", "Vyhled�vac� formul��");

define("L_NO_PRMS_SLICE", "Nem�te pr�vo p�idat/upavovat nastaven� web�ku");
define("L_NO_PS_EDIT", "Nem�te pr�vo upravovat tento web�k");
define("L_NO_PS_ADD", "Nem�te pr�vo p�id�vat web�k");
define("L_NO_PS_CATEGORY", "Nem�te pr�vo m�nit nastaven� kategori�");
define("L_NO_PS_USERS", "Nem�te pr�vo ke spr�v� u�ivatel�");
define("L_NO_PS_FIELDS", "Nem�te pr�vo m�nit nastaven� polo�ek");
define("L_NO_PS_SEARCH", "Nem�te pr�vo m�nit nastaven� vyhled�v�n�");
define("L_PS_NO_NEW_USER", "Nem�te pr�vo vytv��et nov� u�ivatele");

define("L_BAD_RETYPED_PWD", "Vypln�n� hesla si neodpov�daj�");
define("L_ERR_USER_ADD", "Nepoda�ilo se p�idat u�ivatele do syst�mu - chyba LDAP");
define("L_NEWUSER_OK", "U�ivatel byl �sp�n� p�id�n do syst�mu");
define("L_CATBINDS_OK", "Nastaven� kategori� bylo �sp�n� zm�n�no");
define("L_FIELDS_OK", "Nasaven� polo�ek �sp�n� zm�n�no");
define("L_SEARCH_OK", "Nastaven� vyhled�vac�ho formul��e �sp�n� zm�n�no");

define("L_NEEDED", "Mus� b�t vypln�no");

define("L_ALL", " - v�e - ");
define("L_CAT_LIST", "Kategorie web�ku");
define("L_CAT_SELECT", "Kategorie tohoto web�ku");
define("L_NEW_CATEG", "N�zev nov� kategorie");
define("L_NEW_SLICE", "P�idat web�k");
define("L_SLICE_NEW", "Nov� web�k");
define("L_RENAME_CATEG", "Nov� n�zev kategorie");
define("L_ASSIGN", "P�i�adit");
define("L_ADMINPAGE", "Zp�t na hlavn� nastaven�");
define("L_NO_CATEGORY", "Nebyla definov�na kategorie");
define("L_NO_IMPORTED_SLICE", "Nebyl nastaven ��dn� web�k pro p��jem zpr�v");
define("L_NO_USERS", "Nebyl nalezen ��dn� u�ivatel (skupina)");
define("L_AND", "A");
define("L_OR", "Nebo");
define("L_SRCH_KW", "Vyhledat");
define("L_SRCH_FROM", "Od");
define("L_SRCH_TO", "Do");
define("L_SRCH_SUBMIT", "Hledej");

define("L_TOO_MUCH_USERS", "Nalezeno p��li� mnoho u�ivatel� �i skupin.");
define("L_MORE_SPECIFIC", "Zkuste zadat p�esn�j�� �daje.");
define("L_REMOVE", "Odstranit");
define("L_ID", "Id");
define("L_TYPE", "Typ");
define("L_SETTINGS", "Nastaven�");
define("L_LOGO", "Econnect");
define("L_USER_MANAGEMENT", "U�ivatel�");
define("L_ITEMS", "Spr�va p��sp�vk�");
define("L_NEW_SLICE_HEAD", "Nov� web�k");
define("L_ERR_USER_CHANGE", "Nelze zm�nit data u�ivatele - LDAP Error");
define("L_PUBLISHED", "Zve�ejn�no");
define("L_EXPIRED", "Vypr�eno");
define("L_NOT_PUBLISHED", "Dosud nepublikov�no");
define("L_EDIT_USER", "Editace u�ivatele");
define("L_EDITUSER_HDR", L_EDIT_USER);
define("L_USER_ID", "Id u�ivatele");
define("NO_PICTURE_URL", "http://web.ecn.cz/aauser/images/no_pict.gif");  // image used when 
  // there is img_source in html format string but no img_source is stored in database 
  // (you can use blank pixel for none picture)
define("NO_SOURCE_URL", "javascript: window.alert('Nebylo zad�no url zdroje')"); 
define("NO_OUTER_LINK_URL", "javascript: window.alert('Nebylo zad�no url odkazu ven')");

# editor interface constants
define("L_PUBLISHED_HEAD", "PUB");
define("L_HIGHLIGHTED_HEAD", "!");
define("L_FEEDED_HEAD", "ciz�");
define("L_FEEDED_INTO_APP", "Aktu�ln� v");
define("L_FEEDED_INTO_HOLD", "V z�sobn�ku");
define("L_FEEDED_INTO_TRASH", "V ko�i");
define("L_MORE_DETAILS", "V�ce podrobnost�");
define("L_LESS_DETAILS", "M�n� podrobnost�");
define("L_ACTION", "Akce");
define("L_MOVE_TO", "P�esunout");
define("L_SELECT_ALL", "Vybrat v�e");
define("L_UNSELECT_ALL", "Zru�it v�b�r");
define("L_SELECT_VISIBLE", "Vybrat zobrazen�");
define("L_UNSELECT_VISIBLE", "Zru�it v�b�r");

define("L_D_LANGUAGE_CODE", L_LANGUAGE_CODE);
define("L_D_CP_CODE", L_CP_CODE);
define("L_D_CATEGORY_ID", L_CATEGORY);
define("L_CATEGORY_ID", L_CATEGORY);
define("L_D_STATUS_CODE", L_STATUS_CODE);
define("L_D_HIGHLIGHT", L_HIGHLIGHT);
define("L_D_EXPIRY_DATE", L_EXPIRY_DATE);
define("L_D_HL_HREF", L_HL_HREF);
define("L_D_SOURCE", L_SOURCE);
define("L_D_SOURCE_HREF", L_SOURCE_HREF);
define("L_D_PLACE", L_PLACE);
define("L_D_HTML_FORMATTED", L_HTML_FORMATTED);
define("L_D_IMG_SRC", L_IMG_SRC);
define("L_D_IMG_WIDTH", L_IMG_WIDTH);
define("L_D_IMG_HEIGHT", L_IMG_HEIGHT);
define("L_D_POSTED_BY", L_POSTED_BY);
define("L_D_E_POSTED_BY", L_E_POSTED_BY);
define("L_D_LINK_ONLY", L_LINK_ONLY);
define("L_A_FULLTEXT", L_A_FULLTEXT_TIT);
define("L_A_FILTERS_FLT", L_A_FILTERS_TIT);
define("L_FULLTEXT_REMOVE", L_COMPACT_REMOVE);
define("L_A_FIELDS_TIT", L_A_FIELDS_EDT);
define("L_A_SLICE_TIT", L_SLICE_ADM);
define("L_A_SLICE_TIT", L_SLICE_ADM);
define("L_A_SEARCH_EDT", L_A_SEARCH_TIT);
define("L_SLICES_HDR", L_SLICE);
define("L_SRCH_ALL", L_ALL);
define("L_SRCH_SLICE", L_SLICE);
define("L_SRCH_CATEGORY", L_CATEGORY);
define("L_SRCH_AUTHOR", L_CREATED_BY);
define("L_SRCH_LANGUAGE", L_LANGUAGE_CODE);
define("L_SRCH_HEADLINE", L_HEADLINE);
define("L_SRCH_ABSTRACT", L_ABSTRACT);
define("L_SRCH_FULL_TEXT", L_FULL_TEXT);
define("L_SRCH_EDIT_NOTE", L_EDIT_NOTE);

//new_constants
define("L_FEEDED_FROM", "P�ebr�no z");
define("DEFAULT_SLICE_CONFIG", "<wddxPacket version='0.9'><header/><data><struct><var name='admin_fields'><struct><var name='chbox'><struct><var name='width'><number>24</number></var></struct></var><var name='post_date'><struct><var name='width'><number>70</number></var></struct></var><var name='headline'><struct><var name='width'><number>224</number></var></struct></var><var name='catname'><struct><var name='width'><number>70</number></var></struct></var><var name='published'><struct><var name='width'><number>24</number></var></struct></var><var name='highlight'><struct><var name='width'><number>24</number></var></struct></var><var name='feed'><struct><var name='width'><number>24</number></var></struct></var></struct></var></struct></data></wddxPacket>");
define("L_FEED", "V�m�na zpr�v");
define("L_FEEDTO_TITLE", "P�edat zpr�vu do web�ku");
define("L_FEED_TO", "P�edat vybrn� zpr�vy do zvolen�ch web�ku");
define("L_NO_PERMISSION_TO_FEED", "Nelze");
define("L_NO_PS_CONFIG", "Nem�te pr�vo nastavovat configura�n� parametry tohoto web�ku");
define("L_A_SLICE_CFG", "Spr�va web�ku - Konfigurace rozhrann�");
define("L_VISIBLE_ADMIN_FIELDS", "Zobrazen� sloupce v administativn�m rozhrann�");
define("L_FIELD_WIDTH", "���ka sloupce");
define("L_VISIBLE", "Zobrazen�");
define("L_HIDDEN", "Skryt�");
define("L_SLICE_CONFIG", "Parametry");
define("L_CHBOX_HEAD", "&nbsp;");   // title of checkbox in admin interface
define("L_CHBOX", "Checkbox"); 
define("L_CATNAME", "Kategorie");
define("L_CATEGORY_ID", "ID kategorie");
define("L_UP", "Nahoru");
define("L_DOWN", "Dol�");
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
define("L_ITEM_NOT_CHANGED", "Zpr�va nezm�n�na");
define("L_CANT_ADD_ITEM", "Nelze p�idat zpr�vu");
define("L_TOO_MUCH_GROUPS", "Nalezeno p��li� mnoho skupin.");
define("L_NO_GROUPS", "Skupina nenalezena");
define("L_GROUP_NAME", "Jm�no");
define("L_GROUP_DESCRIPTION", "Popis");
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

define("L_FEED", "Zaslat");
define("L_FEEDTO_TITLE", "P�ed�v�n� zpr�v");
define("L_FEED_TO", "Zaslat vybran� zpr�vy vybran�m web�k�m");
define("L_NO_PERMISSION_TO_FEED", "Nem�te pr�vo zas�lat zpr�vy");

define("L_ACTIVE_BIN_EXPIRED", "Aktu�ln� - Expirovan�");
define("L_ACTIVE_BIN_PENDING", "Aktu�ln� - P�ipraven�");
define("L_ACTIVE_BIN_EXPIRED_MENU", "... expirovan�");
define("L_ACTIVE_BIN_PENDING_MENU", "... p�ipraven�");

//----------------------------
//define("", "");   //prepared for new constants
 
$l_month = array( 1 => 'Leden', '�nor', 'B�ezen', 'Duben', 'Kv�ten', '�erven', 
		'�ervenec', 'Srpen', 'Z���', '��jen', 'Listopad', 'Prosinec');

/*
$Log$
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
