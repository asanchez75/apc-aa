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


# identificador del arhivo de configuraci�n
# debe corresponder con el nombre de este archivo.
define("CONFIG_FILE", "es_news_lang.php3");

define("EDIT_ITEM_COUNT", 20);                  // n�mero de �tems en la ventana del editor.

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


# HTML al comienzo de la p�gina de administraci�n
# Usted debe definir el lenguaje de las p�ginas de administraci�n y posiblemente algunos meta-tags
define("HTML_PAGE_BEGIN",
 '<!DOCTYPE html public "-/W3C/DTD HTML 4.0 Transitional/SP">
  <HTML>
  <HEAD>
  <LINK rel=StyleSheet href="../'.ADMIN_CSS.'" type="text/css">
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">');
    
# etiquetas espec�ficas de las aa 
define("L_ITEM_ID_ALIAS",'alias para el Id del �tem');
define("L_EDITITEM_ALIAS",'alias usado en la p�gina de administraci�n index.php3 para el url de edici�n del �tem'); 
define("L_LANG_FILE","Archivo de idioma utilizado");
define("L_PARAMETERS","Par�metros");
define("L_SELECT_APP","Seleccione aplicaci�n");
define("L_SELECT_OWNER","Seleccione propietario");

define("L_CANT_UPLOAD","No se pud� copiar la imagen"); 
define("L_MSG_PAGE", "Mensaje de las aa");   // t�tulo de la p�gina de mensajes
define("L_EDITOR_TITLE", "Ventana de edici�n - administraci�n de �tems");
define("L_FULLTEXT_FORMAT_TOP", "C�digo HTML para encabezado");
define("L_FULLTEXT_FORMAT", "C�digo HTML para texto");
define("L_FULLTEXT_FORMAT_BOTTOM", "C�digo HTML para pie");
define("L_A_FULLTEXT_TIT", "Administraci�n - dise�o para texto completo");
define("L_FULLTEXT_HDR", "C�digo HTML para vista de texto completo del �tem");
define("L_COMPACT_HDR", "C�digo HTML para el �ndice de los �tems");
define("L_ITEM_HDR", "Item");
define("L_A_ITEM_ADD", "A�adir un �tem");
define("L_A_ITEM_EDT", "Editar un �tem");
define("L_IMP_EXPORT", "Permitir exportar al canal:");
define("L_ADD_NEW_ITEM", "A�adir un �tem");
define("L_DELETE_TRASH", "Vaciar papelera");
define("L_VIEW_FULLTEXT", "Vista preliminar");
define("L_FULLTEXT", "Texto completo");
define("L_HIGHLIGHTED", "Resaltado");
define("L_A_FIELDS_EDT", "Administraci�n - campos");
define("L_FIELDS_HDR", "Campos");
define("L_NO_PS_EDIT_ITEMS", "Usted no tiene permisos para editar �tems en este canal");
define("L_NO_DELETE_ITEMS", "Usted no tiene permisos para remover �tems");
define("L_NO_PS_MOVE_ITEMS", "Usted no tiene permisos para mover �tems");
define("L_FULLTEXT_OK", "Formato de texto completo actualizado con �xito");
define("L_NO_ITEM", "Ning�n �tem coincide con su consulta");

# etiquetas comunes de las aa 
# pueden ser las mismas para todas las aplicaciones 
define("L_ACTIVE_BIN", "Aprobados");
define("L_HOLDING_BIN", "Por Aprobar");
define("L_TRASH_BIN", "Papelera");

define("L_CATEGORY","Categor�a");
define("L_SLICE_NAME", "T�tulo");          // canal
define("L_DELETED", "Borrar");           // canal
define("L_D_LISTLEN", "Longitud listado");  // canal
define("L_ERR_CANT_CHANGE", "No se puede cambiar configuraci�n del canal");
define("L_ODD_ROW_FORMAT", "Filas impares");
define("L_EVEN_ROW_FORMAT", "Filas pares");
define("L_EVEN_ODD_DIFFER", "Utilice otro c�digo HTML para filas pares");
define("L_CATEGORY_TOP", "Encabezado categor�a HTML");
define("L_CATEGORY_FORMAT", "T�tulo categor�a");
define("L_CATEGORY_BOTTOM", "Pie categor�a HTML");
define("L_CATEGORY_SORT", "Ordene �tems por categor�a");
define("L_COMPACT_TOP", "Encabezado HTML");
define("L_COMPACT_BOTTOM", "Pie HTML");
define("L_A_COMPACT_TIT", "Administraci�n - formato del listado");
define("L_A_FILTERS_TIT", "Administraci�n - contenido compartido - filtros");
define("L_FLT_SETTING", "Contenido compartido - configuraci�n filtros");
define("L_FLT_FROM_SL", "Filtro para el canal importado");
define("L_FLT_FROM", "De");
define("L_FLT_TO", "A");
define("L_FLT_APPROVED", "Aprobado");
define("L_FLT_CATEGORIES", "Categor�as");
define("L_ALL_CATEGORIES", "Todas categor�as");
define("L_FLT_NONE", "No se ha seleccionado categor�a!");
define("L_THE_SAME", "-- Igual --");
define("L_EXPORT_TO_ALL", "Habilitar exportaci�n a cualquier canal");

define("L_IMP_EXPORT_Y", "Habilitar exportaci�n");
define("L_IMP_EXPORT_N", "Deshabilitar exportaci�n");
define("L_IMP_IMPORT", "Importar desde canal:");
define("L_IMP_IMPORT_Y", "Importar");
define("L_IMP_IMPORT_N", "No importe");
define("L_CONSTANTS_HLP", "Utilice estos alias para los campos de la base de datos");

define("L_ERR_IN", "Error en");
define("L_ERR_NEED", "debe ser llenado");
define("L_ERR_LOG", "Utilice los caracteres a-z, A-Z o 0-9");
define("L_ERR_LOGLEN", "debe ser de 5 a 32 caracteres de longitud");
define("L_ERR_NO_SRCHFLDS", "No se especific� campo de b�squeda!");

define("L_FIELDS", "Campos");
define("L_EDIT", "Editar");
define("L_DELETE", "Borrar");
define("L_REVOKE", "Revocar");
define("L_UPDATE", "Actualizar");
define("L_RESET", "Deshacer cambios");
define("L_CANCEL", "Cancelar");
define("L_ACTION", "Acci�n");
define("L_INSERT", "Insertar");
define("L_NEW", "Nuevo");
define("L_GO", "Ir");
define("L_ADD", "A�adir");
define("L_USERS", "Usuarios");
define("L_GROUPS", "Grupos");
define("L_SEARCH", "Buscar");
define("L_DEFAULTS", "Predeterminado");
define("L_SLICE", "Canal");
define("L_DELETED_SLICE", "No se encontro el canal");
define("L_A_NEWUSER", "Nuevo usuario en el sistema de permisos");
define("L_NEWUSER_HDR", "Nuevo usuario");
define("L_USER_LOGIN", "Nombre");
define("L_USER_PASSWORD1", "Clave");
define("L_USER_PASSWORD2", "Teclee la clave nuevamente");
define("L_USER_FIRSTNAME", "Nombre");
define("L_USER_SURNAME", "Apellido");
define("L_USER_MAIL", "Correo electr�nico");
define("L_USER_SUPER", "Cuenta de superadministrador");
define("L_A_USERS_TIT", "Administraci�n - gesti�n de usuarios");
define("L_A_PERMISSIONS", "Administraci�n - permisos");
define("L_A_ADMIN", "Admininistraci�n - vista de gesti�n de dise�o");
define("L_A_ADMIN_TIT", "Administraci�n - vista de gesti�n de dise�o");
define("L_ADMIN_FORMAT", "Formato �tem");
define("L_ADMIN_FORMAT_BOTTOM", "Pie HTML");
define("L_ADMIN_FORMAT_TOP", "Encabezado HTML");
define("L_ADMIN_HDR", "Listado de �tems en la interface de administraci�n");
define("L_ADMIN_OK", "Campos de administraci�n actualizados con �xito");
define("L_ADMIN_REMOVE", "Borrar cadena");

define("L_ROLE_AUTHOR", "Autor");
define("L_ROLE_EDITOR", "Editor");
define("L_ROLE_ADMINISTRATOR", "Administrador");
define("L_ROLE_SUPER", "Super");

define("L_MAIN_SET", "Propiedades generales");
define("L_PERMISSIONS", "Permisos");
define("L_PERM_CHANGE", "Cambiar");
define("L_PERM_ASSIGN", "Asignar");
define("L_PERM_NEW", "Buscar usuario o grupo");
define("L_PERM_SEARCH", "Asignar nuevos permisos");
define("L_PERM_CURRENT", "Cambiar permisos actuales");
define("L_USER_NEW", "Nuevo usuario");
define("L_DESIGN", "Dise�o");
define("L_COMPACT", "Indice");
define("L_COMPACT_REMOVE", "Remover cadena");
define("L_FEEDING", "Compartir contenido");
define("L_IMPORT", "Importar & Exportar");
define("L_FILTERS", "Filtros");

define("L_A_SLICE_ADD", "A�adir canal");
define("L_A_SLICE_EDT", "Administraci�n - Propiedades canal");
define("L_A_SLICE_CAT", "Administraci�n - configurar categor�as");
define("L_A_SLICE_IMP", "Administraci�n - configurar compartir contenido");
define("L_FIELD", "Campo");
define("L_FIELD_IN_EDIT", "Mostrar");
define("L_NEEDED_FIELD", "Requerido");
define("L_A_SEARCH_TIT", "Administraci�n - dise�o p�gina b�squeda");
define("L_SEARCH_HDR", "Criterio de b�squeda");
define("L_SEARCH_HDR2", "Buscar en campos");
define("L_SEARCH_SHOW", "Mostrar");
define("L_SEARCH_DEFAULT", "Configuraci�n predeterminada");
define("L_SEARCH_SET", "Buscar");
define("L_AND", " Y ");
define("L_OR", " O ");
define("L_SRCH_KW", "Buscar");
define("L_SRCH_FROM", "Desde");
define("L_SRCH_TO", "Hasta");
define("L_SRCH_SUBMIT", "Buscar");
define("L_NO_PS_EDIT", "Usted no tiene permisos para editar este canal");
define("L_NO_PS_ADD", "Usted no tiene permisos para a�adir un canal");
define("L_NO_PS_COPMPACT", "Usted no tiene permisos para para cambiar el formato de presentaci�n compacto");
define("L_NO_PS_FULLTEXT", "Usted no tiene permisos para para cambiar el formato de texto completo");
define("L_NO_PS_CATEGORY", "Usted no tiene permisos para cambiar la configuraci�n de categor�a");
define("L_NO_PS_FEEDING", "Usted no tiene permisos para cambiar la configuraci�n de alimentaci�n");
define("L_NO_PS_USERS", "Usted no tiene permisos para administrar usuarios");
define("L_NO_PS_FIELDS", "Usted no tiene permisos para cambiar la configuraci�n de campos");
define("L_NO_PS_SEARCH", "Usted no tiene permisos para cambiar la configuraci�n de b�squeda");

define("L_BAD_RETYPED_PWD", "Las claves no coinciden");
define("L_ERR_USER_ADD", "Imposible a�adir el usuario al sistema de permisos");
define("L_NEWUSER_OK", "Usuario a�adido al sistema de permisos con �xito");
define("L_COMPACT_OK", "Dise�o compacto actualizado con �xito");
define("L_BAD_ITEM_ID", "Identificaci�n de �tem inv�lido");
define("L_ALL", " - todos - ");
define("L_CAT_LIST", "Categor�as de canales");
define("L_CAT_SELECT", "Categor�as para este canal");
define("L_NEW_SLICE", "A�adir canal");
define("L_ASSIGN", "Asignar");
define("L_CATBINDS_OK", "Categor�a actualizada con �xito");
define("L_IMPORT_OK", "Contenido compartido actualizado con �xito");
define("L_FIELDS_OK", "Campos actualizados con �xito");
define("L_SEARCH_OK", "Campos de b�squeda actualizados con �xito");
define("L_NO_CATEGORY", "Categor�a no definida");
define("L_NO_IMPORTED_SLICE", "No hay canales importados");
define("L_NO_USERS", "No se encuentra grupo de usuarios");

define("L_TOO_MUCH_USERS", "Demasiados grupos de usuarios encontrados");
define("L_MORE_SPECIFIC", "Trate de ser m�s espec�fico");
define("L_REMOVE", "Remover");
define("L_ID", "Id");
define("L_SETTINGS", "Administraci�n");
define("L_LOGO", "APC Aplicaciones para la Acci�n");
define("L_USER_MANAGEMENT", "Usuarios");
define("L_ITEMS", "Mantenimiento de �tems");
define("L_NEW_SLICE_HEAD", "Nuevo canal");
define("L_ERR_USER_CHANGE", "No se puede cambiar de usuario");
define("L_PUBLISHED", "Publicado");
define("L_EXPIRED", "Expirado");
define("L_NOT_PUBLISHED", "No publicado, aun");
define("L_EDIT_USER", "Editar Usuario");
define("L_EDITUSER_HDR", L_EDIT_USER);

define("NO_SOURCE_URL", "javascript: window.alert('URL fuente no especificada')"); 
define("NO_OUTER_LINK_URL", "javascript: window.alert('URL externa no especificada')");

# contantes de edici�n de la interface
define("L_PUBLISHED_HEAD", "Pub");
define("L_HIGHLIGHTED_HEAD", "&nbsp;!&nbsp;");
define("L_FEEDED_HEAD", "Importado");
define("L_MORE_DETAILS", "M�s detalles");
define("L_LESS_DETAILS", "Menos detalles");
define("L_UNSELECT_ALL", "Desmarcar todos");
define("L_SELECT_VISIBLE", "Marcar visibles");
define("L_UNSELECT_VISIBLE", "Desmarcar visibles");

define("L_SLICE_ADM", "Canal administraci�n ");
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

define("L_FEED", "Exportar");
define("L_FEEDTO_TITLE", "Exportar �tem al canal seleccionado");
define("L_FEED_TO", "Exportar �tems seleccionados al canal seleccionado");
define("L_NO_PERMISSION_TO_FEED", "Sin permisos");
define("L_NO_PS_CONFIG", "Usted no tiene permisos para configurar los par�metros de este canal");
define("L_SLICE_CONFIG", "Administrador de �tems");
define("L_CHBOX_HEAD", "&nbsp;");   // t�tulo de caja de selecci�n (checkbox) en interface de administraci�n
define("L_CHBOX", "Checkbox"); 
define("L_CATNAME", "Nombre categor�a");
define("L_CATEGORY_ID", "Id categor�a");
define("L_EDITED_BY","Editado por");
define("L_MASTER_ID", "Id maestro");
define("L_CHANGE_MARKED", "Items seleccionados");
define("L_MOVE_TO_ACTIVE_BIN", "Mover a activos");
define("L_MOVE_TO_HOLDING_BIN", "Mover a por aprobar");
define("L_MOVE_TO_TRASH_BIN", "Mover a papelera");
define("L_OTHER_ARTICLES", "Carpetas");
define("L_MISC", "Otras opciones");
define("L_HEADLINE_EDIT", "Encabezado (editar)");
define("L_HEADLINE_PREVIEW", "Encabezado (vista preliminar)");
define("L_EDIT_LINK", "&nbsp;");
define("L_PREVIEW_LINK", "&nbsp;");
define("L_ARTICLE_MANAGER", "Administrador de �tems");
define("L_SWITCH_TO", "Cambiar a canal");
define("L_ADMIN", "Administraci�n");

define("L_NO_PS_NEW_USER", "No tiene permisos para crear un nuevo usuario");
define("L_ALL_GROUPS", "Todos los grupos");
define("L_USERS_GROUPS", "Grupos de usuarios");
define("L_REALY_DELETE_USER", "Est� seguro de borrar el usuario seleccionado del sistema de permisos?");
define("L_REALY_DELETE_GROUP", "Est� seguro de borrar el grupo seleccionado del sistema de permisos?");
define("L_TOO_MUCH_GROUPS", "Muchos grupos encontrados");
define("L_NO_GROUPS", "No se encontraron grupos");
define("L_GROUP_NAME", "Nombre");
define("L_GROUP_DESCRIPTION", "Descripci�n");
define("L_GROUP_SUPER", "Grupo super-administraci�n");
define("L_ERR_GROUP_ADD", "Imposible a�adir el grupo al sistema de permisos");
define("L_NEWGROUP_OK", "Grupo a�adido al sistema de permisos con �xito");
define("L_ERR_GROUP_CHANGE", "No se puede cambiar el grupo");
define("L_A_UM_USERS_TIT", "Gesti�n usuarios - usuarios");
define("L_A_UM_GROUPS_TIT", "Gesti�n usuarios - grupos");
define("L_EDITGROUP_HDR", "Editar grupo");
define("L_NEWGROUP_HDR", "Nuevo grupo");
define("L_GROUP_ID", "Id grupo");
define("L_ALL_USERS", "Todos los usuarios");
define("L_GROUPS_USERS", "Usuarios del grupo");
define("L_POST", "Publicar");
define("L_POST_PREV", "Publicar y vista preliminar");
define("L_OK", "Aceptar");
define("L_ACTIVE_BIN_EXPIRED", "Caducados");
define("L_ACTIVE_BIN_PENDING", "Pendientes");
define("L_ACTIVE_BIN_EXPIRED_MENU", "Caducados");
define("L_ACTIVE_BIN_PENDING_MENU", "Pendientes");

define("L_FIELD_PRIORITY", "Prioridad");
define("L_FIELD_TYPE", "Tipo");
define("L_CONSTANTS", "Constantes");
define("L_DEFAULT", "Predeterminadas");
define("L_DELETE_FIELD", "Est� seguro de borrar este campo del canal?");
define("L_FEEDED", "Importado");
define("L_HTML_DEFAULT", "HTML codificado como predeterminado");
define("L_HTML_SHOW", "Mostrar 'HTML' / 'texto plano'");
define("L_NEW_OWNER", "Nuevo due�o");
define("L_NEW_OWNER_EMAIL", "Correo nuevo due�o");
define("L_NO_FIELDS", "No hay campos definidos para este canal");
define("L_NO_FIELD", "");
define("L_NO_PERM_TO_FEED", "Sin permisos para configurar opciones de alimentaci�n para ning�n canal");
define("L_NO_SLICES", "Sin canales");
define("L_NO_TEMPLATES", "Sin plantillas");
define("L_OWNER", "Due�o");
define("L_SLICES", "Canaless");
define("L_TEMPLATE", "Plantilla");
define("L_VALIDATE", "Validar");

define("L_FIELD_DELETE_OK", "Campo borrado con �xito");

define("L_WARNING_NOT_CHANGE","<p>ATENCION: No cambie este par�metro a menos que est� seguro!</p>");
define("L_INPUT_SHOW_FUNC_F_HLP","Funci�n utilizada para mostrar en formato de entrada. Para algunos tipos Usted puede utilizar par�metros como sigue:");
define("L_INPUT_SHOW_FUNC_C_HLP","Constantes utilizadas con la funci�n de selecci�n o bot�n de selecci�n.");
define("L_INPUT_SHOW_FUNC_HLP","Si se trata de entrada de texto, este es el n�mero de filas.");
define("L_INPUT_DEFAULT_F_HLP","Qu� funci�n debe utilizarse por defecto:<BR>Ahora - la fecha es la predeterminada<BR>Id del usuario - Id usuario actual<BR>Texto - en par�metros del campo texto es el predeterminado<br>Fecha - por defecto se utiliza la fecha actual m�s <parametro> n�mero de d�as");
define("L_INPUT_DEFAULT_HLP","Si el tipo por defecto es texto, este especifica el texto por defecto<BR>Si el tipo por defecto es fecha, se establece la fecha por defecto como la fecha actual m�s el n�mero de d�as que se especifique aqu�.");

define("L_INPUT_DEFAULT_TXT", "Texto");
define("L_INPUT_DEFAULT_DTE", "Fecha");
define("L_INPUT_DEFAULT_UID", "Id Usuario");
define("L_INPUT_DEFAULT_NOW", "Ahora");

define("L_INPUT_SHOW_TXT","Area texto");
define("L_INPUT_SHOW_FLD","Campo texto");
define("L_INPUT_SHOW_SEL","Caja Selecci�n");
define("L_INPUT_SHOW_RIO","Bot�n selecci�n");
define("L_INPUT_SHOW_DTE","Fecha");
define("L_INPUT_SHOW_CHB","Selecci�n");
define("L_INPUT_SHOW_MCH", "Selecci�n m�ltiple");
define("L_INPUT_SHOW_FIL","Archivo");
define("L_INPUT_SHOW_NUL","No muestre");
                              
define("L_INPUT_VALIDATE_TEXT","Texto");
define("L_INPUT_VALIDATE_URL","URL");
define("L_INPUT_VALIDATE_EMAIL","Correo");
define("L_INPUT_VALIDATE_NUMBER","N�mero");
define("L_INPUT_VALIDATE_ID","Id");
define("L_INPUT_VALIDATE_DATE","Fecha");
define("L_INPUT_VALIDATE_BOOL","Boleano");

define("L_INPUT_INSERT_QTE","Texto");
define("L_INPUT_INSERT_DTE","Fecha");
define("L_INPUT_INSERT_CNS","Constante");
define("L_INPUT_INSERT_NUM","N�mero");
define("L_INPUT_INSERT_BOO","Boleano");
define("L_INPUT_INSERT_UID","Id usuario");
define("L_INPUT_INSERT_NOW","Ahora");
define("L_INPUT_INSERT_FIL","Archivo");
define("L_INPUT_INSERT_NUL","Ninguno");

define("L_INPUT_DEFAULT","Predeterminado");
define("L_INPUT_BEFORE","Antes de c�digo HTML");
define("L_INPUT_BEFORE_HLP","C�digo mostrado en formato de entrada antes de este campo");
define("L_INPUT_FUNC","Tipo de entrada");
define("L_INPUT_HELP","Ayuda para este campo");
define("L_INPUT_HELP_HLP","Ayuda mostrada para este campo");
define("L_INPUT_MOREHLP","M�s ayuda");
define("L_INPUT_MOREHLP_HLP","Texto mostrado despu�s que el usuario selecciona '?' en el formato de entrada");
define("L_INPUT_INSERT_HLP","Esto define como el valor es almacenado en la base de datos. Generalmente utilice 'Texto'.<BR>El campo almacenar� un archivo importado.<BR>Ahora se insertar� la hora actual, no importa la configuraci�n del usuario. Se insertar� la identificaci�n del usuario actual, no importa la confiuraci�n del usuario. Boleanos almacenaran 1 o 0. ");
define("L_INPUT_VALIDATE_HLP","Validar funci�n");

define("L_CONSTANT_NAME", "Nombre");
define("L_CONSTANT_VALUE", "Valor");
define("L_CONSTANT_PRIORITY", "Prioridad");
define("L_CONSTANT_PRI", "Prioridad");
define("L_CONSTANT_GROUP", "Grupo constantes");
define("L_CONSTANT_GROUP_EXIST", "Este grupo de constantes ya existe");
define("L_CONSTANTS_OK", "Constantes actualizadas con �xito");
define("L_A_CONSTANTS_TIT", "Administraci�n - Configuraci�n de constantes");
define("L_A_CONSTANTS_EDT", "Administraci�n - Configuraci�n de constantes");
define("L_CONSTANTS_HDR", "Constantes");
define("L_CONSTANT_NAME_HLP", "mostrado&nbsp;en&nbsp;formato de entrada");
define("L_CONSTANT_VALUE_HLP", "almacenado&nbsp;en&nbsp;la base de datos");
define("L_CONSTANT_PRI_HLP", "orden&nbsp;constante");
define("L_CONSTANT_CLASS", "Padre");
define("L_CONSTANT_CLASS_HLP", "solamente&nbsp;categorias");
define("L_CONSTANT_DEL_HLP", "Remover el nombre de la constante para su borrado");

$L_MONTH = array( 1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
		'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

define("L_NO_CATEGORY_FIELD","No se ha definido campo de categor�a para este canal.<br>A�ada un campo de categoria para el canal primero (vea p�gina de campos)..");
define("L_PERMIT_ANONYMOUS_POST","Permitir publicaci�n an�nima de �tems");
define("L_PERMIT_OFFLINE_FILL","Permitir alimentaci�n de �tems fuera de l�nea");
define("L_SOME_CATEGORY", "<misma categor�a>");

define("L_ALIAS_FUNC_A", "f_a - Resumen");
define("L_ALIAS_FUNC_B", "f_b - Enlace texto-completo");
define("L_ALIAS_FUNC_C", "f_c - condici�n");
define("L_ALIAS_FUNC_D", "f_d - Fecha");
define("L_ALIAS_FUNC_E", "f_e - Editar �tem");
define("L_ALIAS_FUNC_F", "f_f - Enlace texto-completo");
define("L_ALIAS_FUNC_G", "f_g - Altura imagen");
define("L_ALIAS_FUNC_H", "f_h - Imprimir");
define("L_ALIAS_FUNC_I", "f_i - Fuente imagen");
define("L_ALIAS_FUNC_L", "f_l - Campo enlazado");
define("L_ALIAS_FUNC_N", "f_n - Id");
define("L_ALIAS_FUNC_S", "f_s - Url");
define("L_ALIAS_FUNC_T", "f_t - Texto completo");
define("L_ALIAS_FUNC_W", "f_w - Ancho imagen");
define("L_ALIAS_FUNC_0", "f_0 - Ninguno");

define("L_ALIASES", "Cuando Usted accede a la administraci�n de dise�o, Usted utiliza el alias mostrado en este campo");
define("L_ALIAS1", "Alias 1"); 
define("L_ALIAS_HLP", "Debe comenzar con _#.<br>El alias debe ser de exactamente diez caracteres de longitud incluyendo  \"_#\" y en may�sculas.<br>"); 
define("L_ALIAS_FUNC", "Funci�n"); 
define("L_ALIAS_FUNC_F_HLP", "Esta funci�n maneja los campos de la base de datos y los muestra en la p�gina<BR>usualmente se utiliza 'imprimir'.<BR>"); 
define("L_ALIAS_FUNC_HLP", "Parametro enviando a la funci�n de manejo del alias. Para detalles vea el archivo include/item.php3"); 
define("L_ALIAS_HELP", "Texto de ayuda"); 
define("L_ALIAS_HELP_HLP", "Texto de ayuda para el alias"); 
define("L_ALIAS2", "Alias 2"); 
define("L_ALIAS3", "Alias 3"); 

define("L_TOP_HLP", "C�digo HTML que aparece en el �rea de encabezado del canal");
define("L_FORMAT_HLP", "Coloque aqu� el c�digo HTML combinado con el formato de aliases al final de esta p�gina. 
                        El alias ser� sustituido por valores reales provenientes de la base de datos cuando se publiquen en la p�gina");
define("L_BOTTOM_HLP", "C�digo HTML que aparece en el �rea de pie de p�gina del canal");
define("L_EVEN_ROW_HLP", "Usted puede definir diferentes c�digos para filas pares e impares
                          Por ejemplo, primero rojo, segundo negro");

define("L_SLICE_URL", "URL para la p�gina .shtml (normalmente deje en blanco)");
define("L_A_SLICE_ADD_HELP", "Para crear una nuevo canal, seleccione la plantilla.
        El nuevo canal contendr� los campos de la plantilla seleccionada.
        Usted puede tambi�n seleccionar un canal existente como plantilla para su nuevo canal."); 
define("L_REMOVE_HLP", "Remueve brackets vacios etc. Utilice ## como delimitador.");

define("L_COMPACT_HELP", "Utilice estas casillas ( y los tags listados abajo ) para controlar lo que aparece en la p�gina de sumario");
define("L_A_FULLTEXT_HELP", "Utilice estas casillas ( con los tags listados abajo ) para controlar lo que aparece en la vista de texto completo para cada �tem");
define("L_PROHIBITED", "No permitido");
define("L_HTML", "HTML");
define("L_PLAIN_TEXT", "Texto plano");
define("L_A_DELSLICE", "Administraci�n - Borrar Canal");
define("L_DELSLICE_TIT", L_A_DELSLICE);
define("L_DELSLICE_HDR", "Seleccione el canal a borrar");
define("L_DEL_SLICE_HLP","<p>Usted solamente puede borrar canales que est�n marcados como &quot;<b>borrados</b>&quot; en la p�gina &quot;<b>". L_SLICE_SET ."</b>&quot;.</p>");
define("L_A_DELSLICE", L_DELSLICE_TIT);
define("L_DELETE_SLICE", "Est� seguro que desea borrar este canal con todos sus campos y todos sus �tems?");
define("L_NO_SLICE_TO_DELETE", "No hay canales marcados para borrado");
define("L_NO_SUCH_SLICE", "Identificaci�n del canal errada");
define("L_NO_DELETED_SLICE", "El canal no est� marcado para borrado");
define("L_DELSLICE_OK", "Canal borrado con �xito y las tablas han sido optimizadas");
define("L_DEL_SLICE", "Borrar canal");
define("L_FEED_STATE", "Modo de alimentaci�n");
define("L_STATE_FEEDABLE", "Alimentar" );
define("L_STATE_UNFEEDABLE", "No alimentar" );
define("L_STATE_FEEDNOCHANGE", "Alimentaci�n bloqueada" );
define("L_INPUT_FEED_MODES_HLP", "Debe el contenido de este campo ser copiado a otro canal si es alimentado?");
define("L_CANT_CREATE_IMG_DIR","No se puede crear el directorio para copiado de im�genes");

// ------------------------- New - not translated ----------------------------

  # constants for View setting 
define('L_VIEWS','Views');
define('L_ASCENDING','Ascending');
define('L_DESCENDING','Descending');
define('L_NO_PS_VIEWS','You do not have permission to change views');
define('L_VIEW_OK','View successfully changed');
define('L_A_VIEW_TIT','Admin - design View');
define('L_A_VIEWS','Admin - design View');
define('L_VIEWS_HDR','Defined Views');
define('L_VIEW_DELETE_OK','View successfully deleted');
define('L_DELETE_VIEW','Are you sure you want to delete selected view?');
define('L_V_BEFORE',L_COMPACT_TOP);
define('L_V_ODD',L_ODD_ROW_FORMAT);
define('L_V_EVENODDDIF',L_EVEN_ODD_DIFFER);
define('L_V_EVEN',L_EVEN_ROW_FORMAT);
define('L_V_AFTER',L_COMPACT_BOTTOM);
define('L_V_GROUP_BY1','Group by');
define('L_V_GROUP1DIR','');
define('L_V_GROUP_BY2',L_V_GROUP_BY1);
define('L_V_GROUP2DIR','');
define('L_V_GROUP','Group title format');
define('L_V_REMOVE_STRING',L_COMPACT_REMOVE);
define('L_V_MODIFICATION','Type');
define('L_V_PARAMETER','Parameter');
define('L_V_IMG1','View image 1');
define('L_V_IMG2','View image 2');
define('L_V_IMG3','View image 3');
define('L_V_IMG4','View image 4');
define('L_V_ORDER1','Sort primary');
define('L_V_ORDER1DIR','');
define('L_V_ORDER2','Sort secondary');
define('L_V_ORDER2DIR','');
define('L_V_SELECTED','Use selected item');
define('L_V_COND1FLD','Condition 1');
define('L_V_COND1OP','');
define('L_V_COND1COND','');
define('L_V_COND2FLD','Condition 2');
define('L_V_COND2OP','');
define('L_V_COND2COND','');
define('L_V_COND3FLD','Condition 3');
define('L_V_COND3OP','');
define('L_V_COND3COND','');
define('L_V_LISTLEN',L_D_LISTLEN);
define('L_V_FLAG','Flag');
define('L_V_SCROLLER','Display page scroller');
define('L_V_ADITIONAL','Aditional');
define('L_COMPACT_VIEW','Item listing');
define('L_FULLTEXT_VIEW','Fulltext view');
define('L_DIGEST_VIEW','Item digest');
define('L_DISCUSSION_VIEW','Discussion');
define('L_RELATED_VIEW','Related item');
define('L_CONSTANT_VIEW','View of Constants');
define('L_RSS_VIEW','RSS exchange');
define('L_STATIC_VIEW','Static page');
define('L_SCRIPT_VIEW','Javscript item exchange');

/*
$Log$
Revision 1.2  2001/05/18 13:55:04  honzam
New View feature, new and improved search function (QueryIDs)

Revision 1.1  2001/05/10 10:01:43  honzam
New spanish language files, removed <form enctype parameter where not needed, better number validation

Revision 1.32  2001/03/06 00:15:14  honzam
Feeding support, color profiles, radiobutton bug fixed, ...


Revision 1.31  2001/02/25 08:49:54  madebeer
added help for admin-Fields-Edit

Revision 1.30  2001/02/25 08:33:40  madebeer
fixed some table formats, cleaned up admin headlines

Revision 1.29  2001/02/23 11:18:04  madebeer
interface improvements merged from wn branch

Revision 1.28  2001/02/20 13:25:16  honzam
Better search functions, bugfix on show on alias, constant definitions ...

Revision 1.27  2001/01/31 02:46:03  madebeer
moved Fields leftbar section back up to Slice main settings section.
updated some english language titles

Revision 1.26  2001/01/25 10:09:09  honzam
Gived back acidentaly deleted Michael's definitions

Revision 1.25  2001/01/23 23:58:03  honzam
Aliases setings support, bug in permissions fixed (can't login not super user), help texts for aliases page

Revision 1.21  2001/01/08 13:31:58  honzam
Small bugfixes

Revision 1.20  2000/12/23 19:56:50  honzam
Multiple fulltext item view on one page, bugfixes from merge v1.2.3 to v1.5.2

Revision 1.19  2000/12/21 16:39:34  honzam
New data structure and many changes due to version 1.5.x

Revision 1.18  2000/12/05 14:01:19  honzam
Better help for upload image alias

Revision 1.17  2000/11/16 11:48:39  madebeer
11/16/00 a- changed admin leftbar menu order and labels
         b- changed default article editor field order & fields
         c- improved some of the english labels

Revision 1.16  2000/11/13 10:41:14  honzam
Fixed bad order for default setting of show fields and needed fields

Revision 1.15  2000/10/12 15:56:09  honzam
Updated language files with better defaults

Revision 1.14  2000/10/11 20:18:29  honzam
Upadted database structure and language files for web.net's extended item table

Revision 1.13  2000/10/10 18:28:00  honzam
Support for Web.net's extended item table

Revision 1.12  2000/08/17 15:17:55  honzam
new possibility to redirect item displaying (for database changes see CHANGES)

Revision 1.11  2000/08/15 08:58:31  kzajicek
Added missing L_HLP_CATEGORY_ID

Revision 1.10  2000/08/15 08:43:41  kzajicek
Fixed spelling error in constant name

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

Revision 1.1.1.1  2000/06/21 18:40:33  madebeer
reimport tree , 2nd try - code works, tricky to install

Revision 1.1.1.1  2000/06/12 21:50:19  madebeer
Initial upload.  Code works, tricky to install. Copyright, GPL notice there.

Revision 1.12  2000/06/12 19:58:35  madebeer
Added copyright (APC) notice to all .inc and .php3 files that have an $Id

Revision 1.11  2000/06/09 15:14:11  honzama
New configurable admin interface

Revision 1.10  2000/04/24 16:50:34  honzama
New usermanagement interface.

Revision 1.9  2000/03/29 15:54:47  honzama
Better Netscape Navigator javascript support, new direct feeding support, minor changes in texts and look.

Revision 1.8  2000/03/22 09:38:39  madebeer
perm_mysql improvements
Id and Log added to all .php3 and .inc files
system for config-ecn.inc and config-igc.inc both called from
config.inc

*/
?>
