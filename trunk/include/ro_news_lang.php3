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

# config file identifier
# must correspond with this file name
define("LANG_FILE", "ro_news_lang.php3");

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
  <LINK rel=StyleSheet href="'.AA_INSTAL_URL.ADMIN_CSS.'" type="text/css"  title="CPAdminCSS">
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2">');
    
# aa toolkit specific labels
define("L_VIEW_SLICE", "Vizualizare sit");
define( "L_SLICE_HINT", '<br>Pentru a include sec�iunea �n pagina web, include�i urm�toarea linie 
                         �n codul dvs. shtml: ');
define("L_ITEM_ID_ALIAS",'denumire pentru ID element');
define("L_EDITITEM_ALIAS",'denumire utilizat� �n pagina admin index.php3 pentru url editare element');
define("L_LANG_FILE","Fi�ier limb� utilizat�");
define("L_PARAMETERS","Parametrii");
define("L_SELECT_APP","Alege aplica�ia");
define("L_SELECT_OWNER","Alege proprietar");

define("L_CANT_UPLOAD","Nu pot �nc�rca imaginea"); 
define("L_MSG_PAGE", "Mesaj stiri toolkit");   // title of message page
define("L_EDITOR_TITLE", "Fereastr� editor - manager elemente");
define("L_FULLTEXT_FORMAT_TOP", "Cod HTML cap pagin�");
define("L_FULLTEXT_FORMAT", "Cod HTML text �ntreg");
define("L_FULLTEXT_FORMAT_BOTTOM", "Cod HTML sf�r�it pagin�");
define("L_A_FULLTEXT_TIT", "Admin - design vedere text �ntreg");
define("L_FULLTEXT_HDR", "Cod HTML pentru vedere text �ntreg");
define("L_COMPACT_HDR", "Cod HTML pentru vedere sumar");
define("L_ITEM_HDR", "Articol stiri");
define("L_A_ITEM_ADD", "Ad�ugare element");
define("L_A_ITEM_EDT", "Editare element");
define("L_IMP_EXPORT", "Admite export c�tre sec�iunea:");
define("L_ADD_NEW_ITEM", "Ad�ugare element");
define("L_DELETE_TRASH", "Gole�te co�");
define("L_VIEW_FULLTEXT", "Vizualizare");
define("L_FULLTEXT", "Text �ntreg");
define("L_HIGHLIGHTED", "Eviden�iat");
define("L_A_FIELDS_EDT", "Admin - configurare C�mpuri");
define("L_FIELDS_HDR", "C�mpuri");
define("L_NO_PS_EDIT_ITEMS", "Nu ave�i dreptul s� edita�i elemente �n aceast� sec�iune");
define("L_NO_DELETE_ITEMS", "Nu ave�i dreptul s� �terge�i elemente");
define("L_NO_PS_MOVE_ITEMS", "Nu ave�i dreptul s� muta�i elemente");
define("L_FULLTEXT_OK", "Actualizare format text �ntreg reu�it");
define("L_NO_ITEM", "Nici un element nu corespunde criteriilor de c�utare.");

# aa toolkit common labels
# can be the same for all toolkit aplications
define("L_ACTIVE_BIN", "Activ");
define("L_HOLDING_BIN", "Re�inut");
define("L_TRASH_BIN", "Co�");

define("L_CATEGORY","Categorie");
define("L_SLICE_NAME", "Titlu");          // slice
define("L_DELETED", "�ters");           // slice
define("L_D_LISTLEN", "Lungime text");  // slice
define("L_ERR_CANT_CHANGE", "Nu pot schimba set�rile sec�iunii");
define("L_ODD_ROW_FORMAT", "R�nduri impare");
define("L_EVEN_ROW_FORMAT", "R�nduri pare");
define("L_EVEN_ODD_DIFFER", "Folose�te cod HTML diferit pentru r�nduri pare");
define("L_CATEGORY_TOP", "Cap HTML categorie");
define("L_CATEGORY_FORMAT", "Titlu categorie");
define("L_CATEGORY_BOTTOM", "Sf�r�it HTML categorie");
define("L_CATEGORY_SORT", "Sorteaz� elementele pe categorii");
define("L_COMPACT_TOP", "Cap HTML");
define("L_COMPACT_BOTTOM", "Sf�r�it HTML");
define("L_A_COMPACT_TIT", "Admin - design vedere sumar");
define("L_A_FILTERS_TIT", "Admin - Transfer con�inut - Filtre");
define("L_FLT_SETTING", "Transfer con�inut - Configurare filtre");
define("L_FLT_FROM_SL", "Filtru pentru sec�iune importat�");
define("L_FLT_FROM", "De la");
define("L_FLT_TO", "C�tre");
define("L_FLT_APPROVED", "Activ");
define("L_FLT_CATEGORIES", "Categorii");
define("L_ALL_CATEGORIES", "Toate categoriile");
define("L_FLT_NONE", "Nu din categoria selectat�!");
define("L_THE_SAME", "-- Acela�i --");
define("L_EXPORT_TO_ALL", "Admite export c�tre orice sec�iune");

define("L_IMP_EXPORT_Y", "Export admis");
define("L_IMP_EXPORT_N", "Export blocat");
define("L_IMP_IMPORT", "Import din sec�iunea:");
define("L_IMP_IMPORT_Y", "Import�");
define("L_IMP_IMPORT_N", "Nu importa");
define("L_CONSTANTS_HLP", "Folosi�i aceste denumiri pentru c�mpuri de baz� de date");

define("L_ERR_IN", "Eroare introducere");
define("L_ERR_NEED", "trebuie completat");
define("L_ERR_LOG", "folosi�i caracterele a-z, A-Z �i 0-9");
define("L_ERR_LOGLEN", "trebuie s� fie de lungimea 5 - 32 caractere");
define("L_ERR_NO_SRCHFLDS", "C�mp c�utare nespecificat!");

define("L_FIELDS", "C�mpuri");
define("L_EDIT", "Editare");
define("L_DELETE", "�tergere");
define("L_REVOKE", "Revocare");
define("L_UPDATE", "Actualizare");
define("L_RESET", "Resetare formular");
define("L_CANCEL", "Anulare");
define("L_ACTION", "Actiune");
define("L_INSERT", "Inserare");
define("L_NEW", "Nou");
define("L_GO", "Dute");
define("L_ADD", "Adaug�");
define("L_USERS", "Utilizatori");
define("L_GROUPS", "Grupuri");
define("L_SEARCH", "C�utare");
define("L_DEFAULTS", "Implicit");
define("L_SLICE", "Sectiune");
define("L_DELETED_SLICE", "Nu am g�sit nici o sec�iune");
define("L_A_NEWUSER", "Utilizator nou in permission system");
define("L_NEWUSER_HDR", "Utilizator nou");
define("L_USER_LOGIN", "Nume utilizator");
define("L_USER_PASSWORD1", "Parola");
define("L_USER_PASSWORD2", "Retasta�i parola");
define("L_USER_FIRSTNAME", "Prenumele");
define("L_USER_SURNAME", "Numele");
define("L_USER_MAIL", "E-mail");
define("L_USER_SUPER", "Cont de superadmin");
define("L_A_USERS_TIT", "Admin - Management utilizatori");
define("L_A_PERMISSIONS", "Admin - Permisiuni");
define("L_A_ADMIN", "Admin - design vedere manager elemente");
define("L_A_ADMIN_TIT", "Admin - design vedere manager elemente");
define("L_ADMIN_FORMAT", "Format element");
define("L_ADMIN_FORMAT_BOTTOM", "Sf�r�it HTML");
define("L_ADMIN_FORMAT_TOP", "Cap HTML");
define("L_ADMIN_HDR", "Lista elemente �n interfa�a Admin");
define("L_ADMIN_OK", "Actualizare c�mpuri Admin reu�it");
define("L_ADMIN_REMOVE", "�terge �iruri");

define("L_ROLE_AUTHOR", "Autor");
define("L_ROLE_EDITOR", "Editor");
define("L_ROLE_ADMINISTRATOR", "Administrator");
define("L_ROLE_SUPER", "Super");

define("L_MAIN_SET", "Set�ri principale");
define("L_PERMISSIONS", "Drepturi");
define("L_PERM_CHANGE", "Schimb�");
define("L_PERM_ASSIGN", "Asigneaz�");
define("L_PERM_NEW", "Caut� utilizator sau grup");
define("L_PERM_SEARCH", "Asigneaz� drepturi noi");
define("L_PERM_CURRENT", "Schimb� drepturi curente");
define("L_USER_NEW", "Utilizator nou");
define("L_DESIGN", "Design");
define("L_COMPACT", "Sumar");
define("L_COMPACT_REMOVE", "�terge �iruri");
define("L_FEEDING", "Transfer con�inut");
define("L_IMPORT", "Parteneri");
define("L_FILTERS", "Filtre");

define("L_A_SLICE_ADD", "Ad�ugare sec�iune");
define("L_A_SLICE_EDT", "Admin - Set�ri sec�iune");
define("L_A_SLICE_CAT", "Admin - configurare Categorii");
define("L_A_SLICE_IMP", "Admin - configurare Transfer con�inut");
define("L_FIELD", "C�mp");
define("L_FIELD_IN_EDIT", "Arat�");
define("L_NEEDED_FIELD", "Necesar");
define("L_A_SEARCH_TIT", "Admin - design Pagin� c�utare");
define("L_SEARCH_HDR", "Criterii formular c�utare");
define("L_SEARCH_HDR2", "C�utare �n c�mpurile");
define("L_SEARCH_SHOW", "Arat�");
define("L_SEARCH_DEFAULT", "Set�ri implicite");
define("L_SEARCH_SET", "C�utare");
define("L_AND", "�I");
define("L_OR", "SAU");
define("L_SRCH_KW", "Caut�");
define("L_SRCH_FROM", "De la");
define("L_SRCH_TO", "La");
define("L_SRCH_SUBMIT", "Caut�");
define("L_NO_PS_EDIT", "Nu ave�i dreptul s� edita�i aceast� sec�iune");
define("L_NO_PS_ADD", "Nu ave�i dreptul s� ad�uga�i sec�iune");
define("L_NO_PS_COMPACT", "Nu ave�i dreptul s� schimba�i formatarea vederii compact");
define("L_NO_PS_FULLTEXT", "Nu ave�i dreptul s� schimba�i formatarea vederii text �ntreg");
define("L_NO_PS_CATEGORY", "Nu ave�i dreptul s� schimba�i set�rile de categorie");
define("L_NO_PS_FEEDING", "Nu ave�i dreptul s� schimba�i set�rile de transfer");
define("L_NO_PS_USERS", "Nu ave�i dreptul s� administra�i utilizatori");
define("L_NO_PS_FIELDS", "Nu ave�i dreptul s� schimba�i set�rile de c�mpuri");
define("L_NO_PS_SEARCH", "Nu ave�i dreptul s� schimba�i set�rile de c�utare");

define("L_BAD_RETYPED_PWD", "Parola reintrodus� este diferit� de prima");
define("L_ERR_USER_ADD", "Ad�ugarea de utilizatori la sistemul de permisiuni este imposibil�");
define("L_NEWUSER_OK", "Utilizator ad�ugat cu succes la sistemul de permisiuni");
define("L_COMPACT_OK", "Design al vederii compact schimbat cu succes");
define("L_BAD_ITEM_ID", "ID element gre�it");
define("L_ALL", " - toate - ");
define("L_CAT_LIST", "Categorii sec�iune");
define("L_CAT_SELECT", "Categorii �n aceast� sec�iune");
define("L_NEW_SLICE", "Ad�ugare sec�iune");
define("L_ASSIGN", "Asignare");
define("L_CATBINDS_OK", "Actualizare categorie reu�it");
define("L_IMPORT_OK", "Actualizare Transfer Con�inut reu�it");
define("L_FIELDS_OK", "Actualizare c�mpuri reu�it");
define("L_SEARCH_OK", "Actualizare c�mpuri c�utare reu�it");
define("L_NO_CATEGORY", "Nici o categorie definit�");
define("L_NO_IMPORTED_SLICE", "Nu sunt sec�iuni importate");
define("L_NO_USERS", "Nu am g�sit nici un utilizator (grup)");

define("L_TOO_MUCH_USERS", "Prea multi utilizatori sau grupuri g�site.");
define("L_MORE_SPECIFIC", "Fi�i mai specific.");
define("L_REMOVE", "�terge");
define("L_ID", "Id");
define("L_SETTINGS", "Admin");
define("L_LOGO", "APC Action Applications");
define("L_USER_MANAGEMENT", "Utilizatori");
define("L_ITEMS", "Pagina de administrare elemente");
define("L_NEW_SLICE_HEAD", "Sec�iune nou�");
define("L_ERR_USER_CHANGE", "Nu pot schimba utilizatorul");
define("L_PUBLISHED", "Publicat");
define("L_EXPIRED", "Expirat");
define("L_NOT_PUBLISHED", "Nepublicat, �nc�");
define("L_EDIT_USER", "Editare utilizator");
define("L_EDITUSER_HDR", L_EDIT_USER);

define("NO_SOURCE_URL", "javascript: window.alert('url surs� nespecificat')"); 
define("NO_OUTER_LINK_URL", "javascript: window.alert('url extern nespecificat')");

# editors interface constants
define("L_PUBLISHED_HEAD", "Pub");
define("L_HIGHLIGHTED_HEAD", "&nbsp;!&nbsp;");
define("L_FEEDED_HEAD", "Fed");
define("L_MORE_DETAILS", "Mai multe detalii");
define("L_LESS_DETAILS", "Mai pu�ine detalii");
define("L_UNSELECT_ALL", "Deselecteaz� tot");
define("L_SELECT_VISIBLE", "Selecteaz� tot");
define("L_UNSELECT_VISIBLE", "Deselecteaz� tot");

define("L_SLICE_ADM", "Administrare sec�iune");
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

define("L_FEED", "Export�");
define("L_FEEDTO_TITLE", "Export� element c�tre sec�iunea selectat�");
define("L_FEED_TO", "Export� elementele selectate c�tre sec�iunea selectat�");
define("L_NO_PERMISSION_TO_FEED", "Nu este permis");
define("L_NO_PS_CONFIG", "Nu ave�i dreptul s� seta�i parametrii de configura�ie pentru aceast� sec�iune");
define("L_SLICE_CONFIG", "Administrator Elemente");
define("L_CHBOX_HEAD", "&nbsp;");   // title of checkbox in admin interface
define("L_CHBOX", "C�su��"); 
define("L_CATNAME", "Nume categorie");
define("L_CATEGORY_ID", "ID categorie");
define("L_EDITED_BY","Editat de");
define("L_MASTER_ID", "Master id");
define("L_CHANGE_MARKED", "Elemente selectate");
define("L_MOVE_TO_ACTIVE_BIN", "Mut� �n Activ");
define("L_MOVE_TO_HOLDING_BIN", "Mut� �n Re�inut");
define("L_MOVE_TO_TRASH_BIN", "Mut� �n Co�");
define("L_OTHER_ARTICLES", "Dosare");
define("L_MISC", "Misc");
define("L_HEADLINE_EDIT", "Titlu (click pentru editare)");
define("L_HEADLINE_PREVIEW", "Titlu (click pentru vizualizare)");
define("L_EDIT_LINK", "&nbsp;");
define("L_PREVIEW_LINK", "&nbsp;");
define("L_ARTICLE_MANAGER", "Administrator Elemente");
define("L_SWITCH_TO", "Schimb� la:");
define("L_ADMIN", "Admin");

define("L_NO_PS_NEW_USER", "Nu ave�i dreptul s� crea�i utilizator nou");
define("L_ALL_GROUPS", "Toate grupurile");
define("L_USERS_GROUPS", "Grupurile utilizatorului");
define("L_REALY_DELETE_USER", "Sunte�i sigur c� vre�i s� �terge�i utilizatorul selectat din �ntreg sistemul de permisiuni?");
define("L_REALY_DELETE_GROUP", "Sunte�i sigur c� vre�i s� �terge�i grupul selectat din �ntreg sistemul de permisiuni?");
define("L_TOO_MUCH_GROUPS", "Prea multe grupuri g�site.");
define("L_NO_GROUPS", "Nici un grup g�sit");
define("L_GROUP_NAME", "Nume");
define("L_GROUP_DESCRIPTION", "Descriere");
define("L_GROUP_SUPER", "Grup superadmin");
define("L_ERR_GROUP_ADD", "Este imposibil de ad�ugat grupul la sistemul de permisiuni");
define("L_NEWGROUP_OK", "Grup ad�ugat cu succes la sistemul de permisiuni");
define("L_ERR_GROUP_CHANGE", "Nu pot schimba grupul");
define("L_A_UM_USERS_TIT", "Management utilizatori - Utilizatori");
define("L_A_UM_GROUPS_TIT", "Management utilizatori - Grupuri");
define("L_EDITGROUP_HDR", "Editare grup");
define("L_NEWGROUP_HDR", "Grup nou");
define("L_GROUP_ID", "Id Grup");
define("L_ALL_USERS", "To�i utilizatorii");
define("L_GROUPS_USERS", "Utilizatorii grupului");
define("L_POST", "Trimite");
define("L_POST_PREV", "Trimite & Vizualizeaz�");
define("L_OK", "OK");
define("L_ACTIVE_BIN_EXPIRED", "Expirat");
define("L_ACTIVE_BIN_PENDING", "De rezolvat");
define("L_ACTIVE_BIN_EXPIRED_MENU", "Expirat");
define("L_ACTIVE_BIN_PENDING_MENU", "De rezolvat");

define("L_FIELD_PRIORITY", "Prioritate");
define("L_FIELD_TYPE", "Id");
define("L_CONSTANTS", "Constante");
define("L_DEFAULT", "Implicit");
define("L_DELETE_FIELD", "Chiar vre�i s� �terge�i acest c�mp din aceast� sec�iune?");
define("L_FEEDED", "Alimentat");
define("L_HTML_DEFAULT", "HTML codat ca implicit");
define("L_HTML_SHOW", "Arat� op�iunea 'HTML' / 'text simplu'");
define("L_NEW_OWNER", "Proprietar nou");
define("L_NEW_OWNER_EMAIL", "E-mail al proprietarului nou");
define("L_NO_FIELDS", "Nici un c�mp definit pentru aceast� sec�iune");
define("L_NO_FIELD", "");
define("L_NO_PERM_TO_FEED", "Setarea aliment�rii altor sec�iuni nepermis");
define("L_NO_SLICES", "Nu sunt sec�iuni");
define("L_NO_TEMPLATES", "Nu sunt �abloane");
define("L_OWNER", "Proprietar");
define("L_SLICES", "Sec�iuni");
define("L_TEMPLATE", "�abloane");
define("L_VALIDATE", "Valideaz�");

define("L_FIELD_DELETE_OK", "�tergere c�mp reu�it");

define("L_WARNING_NOT_CHANGE","<p>ATEN�IUNE: Nu schimba�i aceste set�ri dac� nu sunte�i siguri �n ceea ce face�i!</p>");
define("L_INPUT_SHOW_FUNC_F_HLP","Func�ii folosite pentru afi�are �n formularul de introducere. Unele folosesc Constante, altele folosesc Parametrii. Pentru mai multe informa�ii, folosi�i Vr�jitorul cu Ajutor.");
define("L_INPUT_SHOW_FUNC_C_HLP","Alege�i un Grup de Constante sau o Sec�iune.");
define("L_INPUT_SHOW_FUNC_HLP","Parametrii sunt �mp�r�i�i prin dou� puncte (:) sau (�n unele cazuri speciale) de apostrof (').");
define("L_INPUT_DEFAULT_F_HLP","Care func�ie s� fie folosit �n mod implicit:<BR>Acum - implicit este data curent�<BR>ID utilizator - ID utilizator curent<BR>Text - implicit este text �n c�mpul Parametru<br>Data - implicit se utilizeaz� data curent� plus <Parametru> num�r de zile");
define("L_INPUT_DEFAULT_HLP","Dac� tipul implicit este Text, acesta seteaz� implicit text.<BR>Dac� tipul implicit este Dat�, aceasta seteaz� data implicit la data curent� plus num�rul de zile setat aici.");

define("L_INPUT_DEFAULT_TXT", "Text");
define("L_INPUT_DEFAULT_DTE", "Dat�");
define("L_INPUT_DEFAULT_UID", "ID utilizator");
define("L_INPUT_DEFAULT_NOW", "Acum");

define("L_INPUT_SHOW_TXT","Zon� text");
define("L_INPUT_SHOW_FLD","C�mp text");
define("L_INPUT_SHOW_SEL","Selec�ie");
define("L_INPUT_SHOW_RIO","Buton radio");
define("L_INPUT_SHOW_DTE","Dat�");
define("L_INPUT_SHOW_CHB","C�su��");
define("L_INPUT_SHOW_MCH", "C�su�e multiple");
define("L_INPUT_SHOW_MSE", "Selec�ie multipl�");
define("L_INPUT_SHOW_FIL","�nc�rcare fi�ier");
define("L_INPUT_SHOW_ISI","Selec�ie element �nrudit");   # added 08/22/01
define("L_INPUT_SHOW_ISO","Fereastr� element �nrudit");       # added 08/22/01
define("L_INPUT_SHOW_WI2","Dou� selec�ii");                 # added 08/22/01
define("L_INPUT_SHOW_PRE","Selec�ie cu predefiniri");   # added 08/22/01
define("L_INPUT_SHOW_NUL","Nu afi�a");
                              
define("L_INPUT_VALIDATE_TEXT","Text");
define("L_INPUT_VALIDATE_URL","URL");
define("L_INPUT_VALIDATE_EMAIL","E-mail");
define("L_INPUT_VALIDATE_NUMBER","Num�r");
define("L_INPUT_VALIDATE_ID","Id");
define("L_INPUT_VALIDATE_DATE","Dat�");
define("L_INPUT_VALIDATE_BOOL","Logic");

define("L_INPUT_INSERT_QTE","Text");
define("L_INPUT_INSERT_DTE","Dat�");
define("L_INPUT_INSERT_CNS","Constant�");
define("L_INPUT_INSERT_NUM","Num�r");
define("L_INPUT_INSERT_IDS","ID elemente");
define("L_INPUT_INSERT_BOO","Logic");
define("L_INPUT_INSERT_UID","ID utilizator");
define("L_INPUT_INSERT_NOW","Acum");
define("L_INPUT_INSERT_FIL","Fi�ier");
define("L_INPUT_INSERT_NUL","Nul");

define("L_INPUT_DEFAULT","Implicit");
define("L_INPUT_BEFORE","�naintea codului HTML");
define("L_INPUT_BEFORE_HLP","Cod afi�at �n formularul de introducere �naintea acestui c�mp");
define("L_INPUT_FUNC","Tip introducere");
define("L_INPUT_HELP","Ajutor pentru acest c�mp");
define("L_INPUT_HELP_HLP","Afi�eaz� ajutor pentru acest c�mp");
define("L_INPUT_MOREHLP","Ajutor mai detaliat");
define("L_INPUT_MOREHLP_HLP","Text afi�at dup� ce utilizatorul face click pe '?' �n formularul de introducere");
define("L_INPUT_INSERT_HLP","Acesta define�te cum se stocheaz� datele �n baza de date.  �n general, folosi�i 'Text'.<BR>Fi�ier va stoca un fi�ier �nc�rcat.<BR>Acum va insera data current�, indiferent de ce seteaz� utilizatorul.  Uid va insera identitatea utilizatorului curent, indiferent de ce seteaz� utilizatorul.  Logic va stoca 1 sau 0. ");
define("L_INPUT_VALIDATE_HLP","Valideaz� func�ia");

define("L_CONSTANT_NAME", "Nume");
define("L_CONSTANT_VALUE", "Valoare");
define("L_CONSTANT_PRIORITY", "Prioritate");
define("L_CONSTANT_PRI", "Prioritate");
define("L_CONSTANT_GROUP", "Grup constante");
define("L_CONSTANT_GROUP_EXIST", "Acest grup de constante deja exist�");
define("L_CONSTANTS_OK", "Actualizare constante reu�it");
define("L_A_CONSTANTS_TIT", "Admin - Setare constante");
define("L_A_CONSTANTS_EDT", "Admin - Setare constante");
define("L_CONSTANTS_HDR", "Constante");
define("L_CONSTANT_NAME_HLP", "afi�at&nbsp;�n&nbsp;pagina&nbsp;de&nbsp;introducere");
define("L_CONSTANT_VALUE_HLP", "stocat&nbsp;�n&nbsp;baza&nbsp;de&nbsp;date");
define("L_CONSTANT_PRI_HLP", "ordine&nbsp;constante");
define("L_CONSTANT_CLASS", "P�rinte");
define("L_CONSTANT_CLASS_HLP", "categorii&nbsp;numai");
define("L_CONSTANT_DEL_HLP", "�nl�tura�i numele constantei pentru a se �terge");

$L_MONTH = array( 1 => 'Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie', 
		'Iulie', 'August', 'Septembrie', 'Octombrie', 'Noiembrie', 'Decembrie');

define("L_NO_CATEGORY_FIELD","Nu sunt c�mpuri de categorie definite �n aceast� sec�iune.<br>Prima dat� ad�uga�i c�mpuri de categorie la aceast� sec�iune (vezi pagina C�mpuri).");
define("L_PERMIT_ANONYMOUS_POST","Permite trimiterea de elemente �n mod anonim");
define("L_PERMIT_OFFLINE_FILL","Permite completarea elementelor �n mod off-line");
define("L_SOME_CATEGORY", "<o categorie>");

define("L_ALIASES", "C�nd merge�i la Admin-Design, folosi�i un Pseudonim pentru afi�area acestui c�mp");
define("L_ALIAS1", "Pseudonim 1"); 
define("L_ALIAS_HLP", "Trebuie sa �nceap� cu _#.<br>Pseudonimul trebuie s� fie exact de zece caractere incluz�nd \"_#\".<br>Pseudonimul va con�ine litere mari."); 
define("L_ALIAS_FUNC", "Func�ie"); 
define("L_ALIAS_FUNC_F_HLP", "Func�ie care gestioneaz� c�mpul din baza de date �i �l afi�eaz� �n pagin�<BR>uzual, folose�te 'print'.<BR>"); 
define("L_ALIAS_FUNC_HLP", "Parametru �naintat func�iei care gestioneaz� pseudonimele. Pentru detalii vezi fi�ierul include/item.php3"); 
define("L_ALIAS_HELP", "Text de ajutor"); 
define("L_ALIAS_HELP_HLP", "Text de ajutor pentru pseudonime"); 
define("L_ALIAS2", "Pseudonim 2"); 
define("L_ALIAS3", "Pseudonim 3"); 

define("L_TOP_HLP", "Cod HTML care apare �n partea de sus a ariei sec�iunii");
define("L_FORMAT_HLP", "Pune�i aici codul HTML combinat cu pseudonime, pentru a forma partea de jos a paginii
                     <br>Pseudonimele vor fi �nlocuite cu valori reale din baza de date c�nd vor fi trimise c�tre pagin�");
define("L_BOTTOM_HLP", "Cod HTML care apare �n partea de jos a ariei sec�iunii");
define("L_EVEN_ROW_HLP", "Pute�i defini cod diferit pentru r�nduri pare sau impare
                         <br>primul ro�u, al doilea negru, de exemplu");

define("L_SLICE_URL", "URL-ul paginii .shtml (de multe ori se las� necompletat)");
define( "L_BRACKETS_ERR", "Parantezele nu corespund �n formul�: ");
define("L_A_SLICE_ADD_HELP", "Pentru a crea sec�iunea nou�, alege�i un �ablon.
        Sec�iunea nou� va mo�teni c�mpurile implicite din �ablon.  
        pute�i alege �i o sec�iune non-�ablon ca baz� pentru noua sec�iune, 
        dac� are c�mpurile de care ave�i nevoie."); 
define("L_REMOVE_HLP", "�nl�tur� paranteze goale etc. Folosi�i ## ca delimitator.");

define("L_COMPACT_HELP", "Utiliza�i aceste spa�ii ( cu expresiile afi�ate mai jos ) pentru a controla ce apare �n pagina sumar");
define("L_A_FULLTEXT_HELP", "Utiliza�i aceste spa�ii ( cu expresiile afi�ate mai jos ) pentru a controla ce apare �n vederea de text �ntreg");
define("L_PROHIBITED", "Nepermis");
define("L_HTML", "HTML");
define("L_PLAIN_TEXT", "Text simplu");
define("L_A_DELSLICE", "Admin - �tergere sec�iune");
define("L_DELSLICE_TIT", L_A_DELSLICE);
define("L_DELSLICE_HDR", "Alege�i sec�iunea de �ters");
define("L_DEL_SLICE_HLP","<p>Pute�i �terge numai sec�iuni care sunt marcate ca &quot;<b>�ters</b>&quot; �n pagina &quot;<b>". L_SLICE_SET ."</b>.</p>");
define("L_A_DELSLICE", L_DELSLICE_TIT);
define("L_DELETE_SLICE", "Dori�i cu adev�rat s� �terge�i aceast� sec�iune cu toate c�mpurile �i elementele?");
define("L_NO_SLICE_TO_DELETE", "Nici o sec�iune marcat� pentru �tergere");
define("L_NO_SUCH_SLICE", "id sec�iune eronat");
define("L_NO_DELETED_SLICE", "Sec�iunea nu este marcat� pentru �tergere");
define("L_DELSLICE_OK", "Sec�iune �tears� cu succes, tabelele sunt optimizate");
define("L_DEL_SLICE", "�terge sec�iune");
define("L_FEED_STATE", "Mod alimentare");
define("L_STATE_FEEDABLE", "Alimenteaz�" );
define("L_STATE_UNFEEDABLE", "A nu se alimenta" );
define("L_STATE_FEEDNOCHANGE", "Alimentare blocat�" );
define("L_INPUT_FEED_MODES_HLP", "Se poate copia con�inutul acestui c�mp �n alt� sec�iune dac� este alimentat?");
define("L_CANT_CREATE_IMG_DIR","Nu pot crea directorul pentru �nc�rcare imagini");

  # constants for View setting 
define('L_VIEWS','Vederi');
define('L_ASCENDING','Cresc�tor');
define('L_DESCENDING','Descresc�tor');
define('L_NO_PS_VIEWS','Nu ave�i dreptul s� schimba�i  vederi');
define('L_VIEW_OK','Vedere schimbat� cu succes');
define('L_A_VIEW_TIT','Admin - design Vedere');
define('L_A_VIEWS','Admin - design Vedere');
define('L_VIEWS_HDR','Vederi definite');
define('L_VIEW_DELETE_OK','Vedere �tears� cu succes');
define('L_DELETE_VIEW','Sunte�i sigur c� vre�i s� �terge�i vederea selectat�?');
define('L_V_BEFORE',L_COMPACT_TOP);
define('L_V_ODD',L_ODD_ROW_FORMAT);
define('L_V_EVENODDDIF',L_EVEN_ODD_DIFFER);
define('L_V_EVEN',L_EVEN_ROW_FORMAT);
define('L_V_AFTER',L_COMPACT_BOTTOM);
define('L_V_GROUP_BY1','Grupat �n func�ie de');
define('L_V_GROUP1DIR',' ');
define('L_V_GROUP_BY2',L_V_GROUP_BY1);
define('L_V_GROUP2DIR',' ');
define('L_V_GROUP','Format titlu grup');
define('L_V_REMOVE_STRING',L_COMPACT_REMOVE);
define('L_V_MODIFICATION','Tip');
define('L_V_PARAMETER','Parametru');
define('L_V_IMG1','Aspect Vedere 1');
define('L_V_IMG2','Aspect Vedere 2');
define('L_V_IMG3','Aspect Vedere 3');
define('L_V_IMG4','Aspect Vedere 4');
define('L_V_ORDER1','Sortare primar�');
define('L_V_ORDER1DIR',' ');
define('L_V_ORDER2','Sortare secundar�');
define('L_V_ORDER2DIR',' ');
define('L_V_SELECTED','HTML pentru Selectat');
define('L_V_COND1FLD','Condi�ie 1');
define('L_V_COND1OP',' ');
define('L_V_COND1COND',' ');
define('L_V_COND2FLD','Condi�ie 2');
define('L_V_COND2OP',' ');
define('L_V_COND2COND',' ');
define('L_V_COND3FLD','Condi�ie 3');
define('L_V_COND3OP',' ');
define('L_V_COND3COND',' ');
define('L_V_LISTLEN',L_D_LISTLEN);
define('L_V_FLAG','Semn');
define('L_V_SCROLLER','Afi�eaz� scroller de pagin�');
define('L_V_ADITIONAL','Adi�ional');
define('L_COMPACT_VIEW','Afi�are element');
define('L_FULLTEXT_VIEW','Vedere text �ntreg');
define('L_DIGEST_VIEW','Extras elemente');
define('L_DISCUSSION_VIEW','Discu�ie');
define('L_RELATED_VIEW','Element �nrudit');
define('L_CONSTANT_VIEW','Vedere Constante');
define('L_RSS_VIEW','Schimb RSS');
define('L_STATIC_VIEW','Pagin� static�');
define('L_SCRIPT_VIEW','Schimb element Javascript');

define("L_MAP","Mapare");
define("L_MAP_TIT","Admin - Transfer con�inut - Mapare c�mpuri");
define("L_MAP_FIELDS","Mapare c�mpuri");
define("L_MAP_TABTIT","Transfer con�inut - Mapare c�mpuri");
define("L_MAP_FROM_SLICE","Mapare din sec�iune");
define("L_MAP_FROM","Din");
define("L_MAP_TO","La");
define("L_MAP_DUP","Nu pot mapa c�tre acela�i c�mp");
define("L_MAP_NOTMAP","-- Nemapat --");
define("L_MAP_OK","Actualizare mapare c�mpuri reu�it");

define("L_STATE_FEEDABLE_UPDATE", "Alimentare & actualizare" );
define("L_STATE_FEEDABLE_UPDATE_LOCKED", "Alimentare & actualizare & blocare" );

define("L_SITEM_ID_ALIAS",'pseudonim pentru ID scurt de element');
define("L_MAP_VALUE","-- Valoare --");
define("L_MAP_VALUE2","Valoare");
define("L_ORDER", "Ordine");
define("L_INSERT_AS_NEW","Insereaz� ca nou");

// Constant view constants
define("L_CONST_NAME_ALIAS", "Nume constant�");
define("L_CONST_VALUE_ALIAS", "Valoare constant�");
define("L_CONST_PRIORITY_ALIAS", "Prioritate constant�");
define("L_CONST_GROUP_ALIAS", "grup id al constantei");
define("L_CONST_CLASS_ALIAS", "Clas� categorie (numai pentru categorii)");
define("L_CONST_COUNTER_ALIAS", "Num�r constante");
define("L_CONST_ID_ALIAS", "id unic al constantei");

define('L_V_CONSTANT_GROUP','Grup constante');
define("L_NO_CONSTANT", "Nu am g�sit constante");

// Discussion constants.
define("L_DISCUS_SEL","Afi�eaz� discu�ia");
define("L_DISCUS_EMPTY"," -- Vid -- ");
define("L_DISCUS_HTML_FORMAT","Folosi�i tag-uri HTML");
define("L_EDITDISC_ALIAS",'Pseudonim folosit �n pagina admin index.php3 pentru editare url discu�ii');

define("L_D_SUBJECT_ALIAS","Pseudonim pentru subiectul la comentariu discu�ie");
define("L_D_BODY_ALIAS"," Pseudonim pentru textul comentariului la discu�ie");
define("L_D_AUTHOR_ALIAS"," Pseudonim pentru scris de");
define("L_D_EMAIL_ALIAS","Pseudonim pentru adresa e-mail al autorului");
define("L_D_WWWURL_ALIAS","Pseudonim pentru adresa url al sitului autorului");
define("L_D_WWWDES_ALIAS","Pseudonim pentru descrierea sitului autorului");
define("L_D_DATE_ALIAS","Pseudonim pentru data public�rii");
define("L_D_REMOTE_ADDR_ALIAS","Pseudonim al adresei IP a calculatorului autorului");
define("L_D_URLBODY_ALIAS","Pseudonim pentru link la textul comentariului la discu�ie<br>
                             <i>Utilizare: </i>�n codul HTML pentru vederea sumar al comentariului<br>
                             <i>Exemplu: </i>&lt;a href=_#URL_BODY>_#SUBJECT#&lt;/a>");
define("L_D_CHECKBOX_ALIAS","Pseudonim pentru c�su�a folosit� la alegerea comentariului discu�iei");
define("L_D_TREEIMGS_ALIAS","Pseudonim pentru imagini");
define("L_D_ALL_COUNT_ALIAS","Pseudonim pentru num�rul tuturor comentariilor la element");
define("L_D_APPROVED_COUNT_ALIAS","Pseudonim pentru num�rul comentariilor aprobate la element");
define("L_D_URLREPLY_ALIAS","Pseudonim pentru link la formular<br>
                             <i>Utilizare: </i>�n codul HTML pentru vederea de text �ntreg al comentariului<br>
                             <i>Exemplu: </i>&lt;a href=_#URLREPLY&gt;R�spuns&lt;/a&gt;");
define("L_D_URL","Pseudonim pentru link la discu�ie<br>
                             <i>Utilizare: </i>�n codul formularului<br>
                             <i>Exemplu: </i>&lt;input type=hidden name=url value=\"_#DISC_URL\">");
define("L_D_ID_ALIAS"," Pseudonim pentru ID element<br>
                             <i>Utilizare: </i>�n codul formularului<br>
                             <i>Exemplu: </i>&lt;input type=hidden name=d_parent value=\"_#DISC_ID#\">");
define("L_D_ITEM_ID_ALIAS"," Pseudonim pentru ID comentariu<br>
                             <i>Utilizare: </i>�n codul formularului<br>
                             <i>Exemplu: </i>&lt;input type=hidden name=d_item_id value=\"_#ITEM_ID#\">");

define("L_D_BUTTONS","Pseudonim pentru butoane Afi�eaz� tot, Afi�eaz� selectat, Adaug� nou<br>
                             <i>Utilizare: </i> �n codul HTML de la sf�r�it");

define("L_D_COMPACT" , "Cod HTML pentru vederea sumar al comentariului");
define("L_D_SHOWIMGS" , "Afi�eaz� imaginile");
define("L_D_ORDER" , "Ordoneaz� �n func�ie de");
define("L_D_FULLTEXT" ,"Cod HTML pentru vederea text �ntreg al comentariului");

define("L_D_ADMIN","Management al comentariilor discu�iei");
define("L_D_NODISCUS","F�r� comentarii discu�ie");
define("L_D_TOPIC","Titlu");
define("L_D_AUTHOR","Autor");
define("L_D_DATE","Data");
define("L_D_ACTIONS","Ac�iuni");
define("L_D_DELETE","�terge");
define("L_D_EDIT","Editeaz�");
define("L_D_HIDE","Ascunde");
define("L_D_APPROVE","Aprob�");

define("L_D_EDITDISC","Management elemente - Management comentarii discu�ie - Editare comentariu");
define("L_D_EDITDISC_TABTIT","Editare comentariu");
define("L_D_SUBJECT","Subiect");
define("L_D_AUTHOR","Autor");
define("L_D_EMAIL","E-mail");
define("L_D_BODY","Text comentariu discu�ie");
define("L_D_URL_ADDRESS","WWW - URL al autorilor");
define("L_D_URL_DES","WWW al autorilor - descriere");
define("L_D_HOSTNAME","Adresa IP a calculatorului autorilor");

define("L_D_SELECTED_NONE","Nu a fost selectat nici un comentariu");
define("L_D_DELETE_COMMENT","Sunte�i sigur c� vre�i s� �terge�i comentariul selectat?");

define("L_D_FORM","Codul HTML al formularului pentru trimitere comentarii");
define("L_D_ITEM","Element: ");

define("L_D_SHOW_SELECTED","Afi�eaz� selectat");
define("L_D_SHOW_ALL","Afi�eaz� tot");
define("L_D_ADD_NEW","Adaug� nou");

define("L_TOO_MUCH_RELATED","Sunt prea multe elemente �nrudite. Num�rul elementelor �nrudite este limitat�.");
define("L_SELECT_RELATED","Selecta�i elementele �nrudite");
define("L_SELECT_RELATED_1WAY","Adaug�");
define("L_SELECT_RELATED_2WAY","Adaug�&nbsp;mutual");

define("L_D_BACK","Back");
define("L_D_ADMIN2","Management de comentarii discu�ie");

define("L_INNER_IMPORT","Alimentare �n cadrul nodului");
define("L_INTER_IMPORT","Import �ntre noduri");
define("L_INTER_EXPORT","Export �ntre noduri");

define("L_NODES_MANAGER","Noduri");
define("L_NO_PS_NODES_MANAGER","Nu ave�i dreptul s� administra�i noduri");
define("L_NODES_ADMIN_TIT","Administrare nod distant");
define("L_NODES_LIST","Noduri distante cunoscute");
define("L_NODES_ADD_NEW","Ad�ugare nod nou");
define("L_NODES_EDIT","Editare date nod");
define("L_NODES_NODE_NAME","Nume nod");
define("L_NODES_SERVER_URL","URL al getxml.php3");
define("L_NODES_PASWORD","Parola");
define("L_SUBMIT","Trimite");
define("L_NODES_SEL_NONE","Nu este nod selectat");
define("L_NODES_CONFIRM_DELETE","Sunte�i sigur c� vre�i s� �terge�i nodul?");
define("L_NODES_NODE_EMPTY","Numele nodului trebuie completat");

define("L_IMPORT_TIT","Set�ri import �ntre noduri");
define("L_IMPORT_LIST","Importuri distante existente c�tre sec�iune ");
define("L_IMPORT_CONFIRM_DELETE","Sunte�i sigur c� vre�i s� �terge�i importul?");
define("L_IMPORT_SEL_NONE","Nu este import selectat");
define("L_IMPORT_NODES_LIST","Toate nodurile distante");
define("L_IMPORT_CREATE","Creeaz� o nou� linie de alimentare de la nod");
define("L_IMPORT_NODE_SEL","Nu este nod selectat");
define("L_IMPORT_SLICES","Lista sec�iunilor distante");
define("L_IMPORT_SLICES2","Lista sec�iunilor accesibile de la nod ");
define("L_IMPORT_SUBMIT","Alege sec�iunea");
define("L_IMPORT2_OK","Importul a fost creat cu succes");
define("L_IMPORT2_ERR","Importul a fost creat deja");

define("L_RSS_ERROR","Nu m� pot conecta �i/sau prelua date de la nodul distant. Contacta�i administratorul nodului local.");
define("L_RSS_ERROR2","Parol� invalid� pentru numele de node:");
define("L_RSS_ERROR3","Contacta�i administratorul nodului local.");
define("L_RSS_ERROR4","Nu sunt sec�iuni accesibile. Nu ave�i dreptul s� importa�i date de la acel nod. Contacta�i ".
          "administratorul sec�iunii distante �i verifica�i, dac� el a ob�inut numele dvs. utilizator corect.");


define("L_EXPORT_TIT","Set�ri export �ntre noduri");
define("L_EXPORT_CONFIRM_DELETE","Sunte�i sigur c� vre�i s� �terge�i exportul?");
define("L_EXPORT_SEL_NONE","Nu este export selectat");
define("L_EXPORT_LIST","Exporturi existente al sec�iunii ");
define("L_EXPORT_ADD","Insereaz� element nou");
define("L_EXPORT_NAME","Nume utilizator");
define("L_EXPORT_NODES","Noduri distante");

define("L_RSS_TITL", "Titlul sec�iunii pentru RSS");
define("L_RSS_LINK", "Link la sec�iunea pentru RSS");
define("L_RSS_DESC", "Descriere scurt� (proprietar �i nume) a sec�iunii pentru RSS");
define("L_RSS_DATE", "Informa�ia dat� RSS este generat, �n format dat� RSS");

define("L_NO_PS_EXPORT_IMPORT", "Nu ave�i dreptul s� exporta�i / importa�i sec�iuni");
define("L_EXPORT_SLICE", "Export");
define("L_IMPORT_SLICE", "Import");
define("L_EXPIMP_SET", "Structur� sec�iune");

define("L_E_EXPORT_TITLE", "Export structur� sec�iune");
define("L_E_EXPORT_MEMO", "Alege�i una dintre cele dou� tipuri de export:");
define("L_E_EXPORT_DESC", "C�nd exporta�i \"c�tre alt ActionApps\" numai sec�iunea curent� va fi exportat� "
		."�i dvs. alege�i noul ei identificator.");
define("L_E_EXPORT_DESC_BACKUP", "C�nd exporta�i \"c�tre Backup\" pute�i alege mai multe sec�iuni dintr-o dat�.");
define("L_E_EXPORT_MEMO_ID","Alege�i un identificator de sec�iune nou, de exact 16 caractere: ");
define("L_E_EXPORT_SWITCH", "Export� la Backup");
define("L_E_EXPORT_SWITCH_BACKUP", "Export� la alt ActionApps");
define("L_E_EXPORT_IDLENGTH", "Identificatorul trebuie s� fie de lungimea 16 caractere, nu ");
define("L_E_EXPORT_TEXT_LABEL", "Salva�i acest text. �l ve-�i putea utiliza s� importa�i sec�iunile �n orice ActionApps:");
define("L_E_EXPORT_LIST", "Selecta�i sec�iunile pe care VRE�I s� le exporta�i:");

define("L_PARAM_WIZARD_LINK", "Vr�jitor cu ajutor");

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