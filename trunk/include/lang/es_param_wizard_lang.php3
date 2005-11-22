<?php
# $Id$
# Language: ES
# This file was created automatically by the Mini GetText environment
# on 22.11.2005 17:38

# Do not change this file otherwise than by typing translations on the right of =

# Before each message there are links to program code where it was used.

$mgettext_lang = "es";

# Unused messages
$_m["Do you want to check for uniqueness this slice only \n"
   ."                  or all slices?"]
 = "�Quiere verificar unicidad en este canal solamente o en todos los canales?";

$_m["<b>1</b> = This slice only. \n"
   ."                <b>2</b> = All slices.<br>\n"
   ."                <b>0</b> = Username, special: Checks uniqueness in reader management\n"
   ."                slices and in the permission system. Always uses field ID %1"]
 = "<b>1</b> = S�lo este canal. \n"
   ."<b>2</b> = Todos los canales.<br>\n"
   ."<b>0</b> = Usuario. Comprueba unicidad en canales de Suscriptores y \n"
   ."sistema de permisos. Siempre usa el ID %1";

$_m["PHP-like format - see <a href=\"http://www.php.cz/manual/en/function.date.php\" target=_blank>PHP manual</a>"]
 = "especificar formato seg�n sintaxis de PHP: ver <a href=\"http://www.php.cz/manual/en/function.date.php\" target=_blank>el manual de PHP</a";

$_m["print HTML"]
 = "mostrar HTML";

$_m["prints <i>the field</i> content (or <i>unalias string</i>) depending on the html flag (if html flag is not set, it converts the content to html. In difference to f_h function, it converts to html line-breaks, too. Obviously this function is used for fultexts.)"]
 = "muestra <i>el campo</i> (o la <b>cadena</b>), alterando o no su contenido dependiendo de la selecci�n hecha en 'HTML / texto plano': si se seleccion� 'texto plano', convierte el contenido a HTML, a�adiendo cambios de linea etc.";

$_m["field will be displayed in select box. if not specified, in select box are displayed headlines. (only for constants input type: slice)"]
 = "(solo para cuando seleccione un canal para mostrar en la caja de selecci�n) mostrar este campo en vez del campo por defecto (titulares)";

$_m["show all"]
 = "mostrar todo";

$_m["used only for slices - if set (=1), then all items are shown (including expired and pending ones)"]
 = "(solo para cuando seleccione un canal para mostrar en la caja de selecci�n) si es '1', se muestran en la lista todos los �tems del canal, incluyendo los caducados y los pendientes";

$_m["Defines, which buttons to show in item selection:<br>A - 'Add'<br>M - 'Add Mutual<br>B - 'Backward'.<br> Use 'AMB' (default), 'MA', just 'A' or any other combination. The order of letters A,M,B is important."]
 = "Define qu� botones se muestran en la lista de items:<br>A - 'A�adir'<br>M - 'A�adir mutuo'<br>B - 'Viceversa'. El orden de las letras A,M,B es importante.";

$_m["number of characters from the <b>fulltext</b> field"]
 = "n�mero de caracteres del campo <b>texto completo</b>";

$_m["field id of fulltext field (like full_text.......)"]
 = "id del campo de texto completo (por ejemplo full_text.......)";

$_m["take first paragraph (text until \\<BR\\> or \\<P\\> or \\</P\\) if shorter then <b>length</b>"]
 = "tomar s�lo el primer p�rrafo (el texto hasta \\<BR\\> or \\<P\\> or \\</P\\) si es m�s corto que <b>longitud</b>";

$_m["prints <i>the field</i> as image width (height='xxx' width='yyy') empty string if cant work out, does not special case URLs from uploads directory, might do later! "]
 = "Funci�n anticuada. Use mejor la funci�n f_c";

# End of unused messages
# admin/param_wizard.php3, row 81
$_m["Wizard"]
 = " - Asistente";

# admin/param_wizard.php3, row 166
$_m["This is an undocumented %s. We don't recommend to use it."]
 = "Esta %s no est� documentada. No se recomienda su uso.";

# admin/param_wizard.php3, row 167
$_m["Close the wizard"]
 = "Cerrar asistente";

# admin/param_wizard.php3, row 181
$_m["Available parameters: "]
 = "Par�metros disponibles: ";

# admin/param_wizard.php3, row 194
$_m["integer&nbsp;number"]
 = "n�mero&nbsp;entero";

# admin/param_wizard.php3, row 195
$_m["any&nbsp;text"]
 = "cuarquier&nbsp;texto";

# admin/param_wizard.php3, row 196
$_m["field&nbsp;id"]
 = "id&nbsp;campo";

# admin/param_wizard.php3, row 197
$_m["boolean:&nbsp;0=false,1=true"]
 = "booleano:&nbsp;0=falso,1=verdadero";

# admin/param_wizard.php3, row 210
$_m["This %s has no parameters."]
 = "Esta %s no tiene par�metros.";

# admin/param_wizard.php3, row 220
$_m["Have a look at these examples of parameters sets:"]
 = "Puede tomar ideas de estos ejemplos:";

# admin/param_wizard.php3, row 227
$_m["Show"]
 = "Mostrar";

# admin/param_wizard.php3, row 237
$_m["OK - Save"]
 = "OK - Guardar";

# admin/param_wizard.php3, row 238
$_m["Cancel"]
 = "Cancelar";

# admin/param_wizard.php3, row 239
$_m["Show example params"]
 = "Mostrar ejemplo";

# admin/param_wizard.php3, row 241
$_m["OK"]
 = "";

# include/constants_param_wizard.php3, row 54
$_m["Insert Function"]
 = "Funci�n de Inserci�n";

# include/constants_param_wizard.php3, row 56
$_m["Text = don't modify"]
 = "Texto = no modificar";

# include/constants_param_wizard.php3, row 57
$_m["Does not modify the value."]
 = "No modifica el valor del campo.";

# include/constants_param_wizard.php3, row 59
$_m["Boolean = store 0 or 1"]
 = "Booleano = guardar 0 o 1";

# include/constants_param_wizard.php3, row 61
$_m["File = uploaded file"]
 = "Archivo = subir archivo";

# include/constants_param_wizard.php3, row 62
$_m["Stores the uploaded file and a link to it, parameters only apply if type is image/something."]
 = "Coloca el archivo en la carpeta de archivos de las AA, y almacena el URL que permite acceder a �l. Los par�metros s�lo sirven si el tipo MIME es image/*.";

# include/constants_param_wizard.php3, row 64
$_m["Mime types accepted"]
 = "Tipos MIME aceptados";

# include/constants_param_wizard.php3, row 65
$_m["Only files of matching mime types will be accepted"]
 = "Solo se aceptar�n los tipos de archivo que coincidan";

# include/constants_param_wizard.php3, row 68
$_m["Maximum image width"]
 = "Ancho m�ximo de im�gen";

# include/constants_param_wizard.php3, row 71
$_m["Maximum image height"]
 = "Altura m�xima im�gen";

# include/constants_param_wizard.php3, row 72
$_m["The image will be resampled to be within these limits, while retaining aspect ratio."]
 = "La im�gen ser� redimensionada para que est� dentro de estos l�mites, manteniendo su proporci�n.";

# include/constants_param_wizard.php3, row 75
$_m["Other fields"]
 = "Otros campos";

# include/constants_param_wizard.php3, row 76
$_m["List of other fields to receive this image, separated by ##"]
 = "Lista de otros campos que reciben esta im�gen, separados por ##";

# include/constants_param_wizard.php3, row 79
$_m["Upload policy"]
 = "";

# include/constants_param_wizard.php3, row 84
$_m["new | overwrite | backup<br>This parameter controls what to do if uploaded file alredy exists:\n"
   ."                       <br>new - AA creates new filename (by adding _x postfix) and store it with this new name (default)\n"
   ."                       <br>overwrite - the old file of the same name is overwritten\n"
   ."                       <br>backup - the old file is copied to new (non-existing) file and current file is stored with current name.\n"
   ."                       <br>In all cases the filename is escaped, so any non-word characters will be replaced by an underscore."]
 = "";

# include/constants_param_wizard.php3, row 87
$_m["Exact dimensions"]
 = "";

# include/constants_param_wizard.php3, row 89
$_m["If set to 1 the image will be downsized exactly to the specified dimensions (and croped if needed).\n"
   ."                       Default is 0 or empty: Maintain aspect ratio while resizing the image."]
 = "";

# include/constants_param_wizard.php3, row 94
$_m["User ID = always store current user ID"]
 = "ID usuario = almacenar el ID usuario actual";

# include/constants_param_wizard.php3, row 97, 129
$_m["Login name"]
 = "Nombre de usuario";

# include/constants_param_wizard.php3, row 99
$_m["Item IDs"]
 = "IDs �tems";

# include/constants_param_wizard.php3, row 101
$_m["Now = always store current time"]
 = "Ahora = almacenar hora actual";

# include/constants_param_wizard.php3, row 102
$_m["Inserts the current time, no matter what the user sets."]
 = "Inserta la hora actual, independientemente del valor del campo introducido por el autor.";

# include/constants_param_wizard.php3, row 104, 206, 649
$_m["Password and Change Password"]
 = "Clave y Cambiar Clave";

# include/constants_param_wizard.php3, row 107
$_m["Stores value from a 'Password and Change Password' field type.\n"
   ."           First prooves the new password matches the retyped new password,\n"
   ."           and if so, MD5-encrypts the new password and stores it."]
 = "Almacena el valor de un tipo de entrada 'Clave y Cambio de Clave'.\n"
   ."Primero comprueba que las dos claves coinciden, y si es as�,\n"
   ."inserta la clave encriptada con MD5.";

# include/constants_param_wizard.php3, row 109
$_m["Computed field"]
 = "";

# include/constants_param_wizard.php3, row 110
$_m["The field is the result of expression written in \"Code for unaliasing\". It is good solution for all values, which could be precomputed, since its computation on item-show-time would be slow. Yes, you can use {view...}, {include...}, {switch...} here"]
 = "";

# include/constants_param_wizard.php3, row 112
$_m["Code for unaliasing"]
 = "";

# include/constants_param_wizard.php3, row 113
$_m["There you can write any string. The string will be unaliased on item store, so you can use any {...} construct as well as field aliases here"]
 = "";

# include/constants_param_wizard.php3, row 119
$_m["Default Value Type"]
 = "Valor por defecto";

# include/constants_param_wizard.php3, row 121
$_m["Text from 'Parameter'"]
 = "Texto de 'par�metro'";

# include/constants_param_wizard.php3, row 122
$_m["Text"]
 = "Texto";

# include/constants_param_wizard.php3, row 124
$_m["Date + 'Parameter' days"]
 = "Fecha + 'par�metro' d�as";

# include/constants_param_wizard.php3, row 125
$_m["Number of days"]
 = "N�mero de d�as";

# include/constants_param_wizard.php3, row 127
$_m["User ID"]
 = "ID usuario";

# include/constants_param_wizard.php3, row 131
$_m["Now, i.e. current date"]
 = "Ahora (fecha actual)";

# include/constants_param_wizard.php3, row 133
$_m["Variable"]
 = "";

# include/constants_param_wizard.php3, row 134
$_m["A dangerous function. Do not use."]
 = "Funci�n peligrosa. No la use.";

# include/constants_param_wizard.php3, row 136
$_m["Random string"]
 = "Cadena aleat�ria";

# include/constants_param_wizard.php3, row 137
$_m["Random alphanumeric [A-Z0-9] string."]
 = "Una cadena alfanum�rica [A-Z0-9] elegida al azar.";

# include/constants_param_wizard.php3, row 139
$_m["String length"]
 = "Longitud cadena";

# include/constants_param_wizard.php3, row 142
$_m["Field to check"]
 = "Campo a verificar";

# include/constants_param_wizard.php3, row 144
$_m["If you need a unique code, you must send the field ID,\n"
   ."                  the function will then look into this field to ensure uniqueness."]
 = "Si necesita un c�digo �nico, rellene el ID del camp, y la funci�n asegurar� que el valor sea �nico para ese campo.";

# include/constants_param_wizard.php3, row 147, 199
$_m["Slice only"]
 = "S�lo en el canal";

# include/constants_param_wizard.php3, row 149, 201
$_m["Do you want to check for uniqueness this slice only\n"
   ."                  or all slices?"]
 = "Desea verificar la unicidad de este canal solamente o en todos los canales?";

# include/constants_param_wizard.php3, row 156
$_m["Input Validate Type"]
 = "Tipo de validaci�n de entrada";

# include/constants_param_wizard.php3, row 158
$_m["No validation"]
 = "No validar";

# include/constants_param_wizard.php3, row 160
$_m["URL"]
 = "";

# include/constants_param_wizard.php3, row 162
$_m["E-mail"]
 = "Correo-e";

# include/constants_param_wizard.php3, row 164
$_m["Number = positive integer number"]
 = "N�mero = entero positivo";

# include/constants_param_wizard.php3, row 166
$_m["Id = 1-32 hexadecimal digits [0-9a-f]"]
 = "Id = 1-32 d�gitos hexadecimales";

# include/constants_param_wizard.php3, row 168
$_m["Date = store as date"]
 = "Fecha = guardar como fecha";

# include/constants_param_wizard.php3, row 170
$_m["Bool = store as bool"]
 = "Bool = guardar como booleano";

# include/constants_param_wizard.php3, row 172
$_m["User = does nothing ???"]
 = "Usuario";

# include/constants_param_wizard.php3, row 174
$_m["Unique = proove uniqueness"]
 = "Unico = verificar unicidad";

# include/constants_param_wizard.php3, row 176
$_m["Validates only if the value is not yet used. Useful e.g.\n"
   ."        for emails or user names."]
 = "Validar si el valor no est� en uso todav�a. Util para nombres de usuario o e-mails";

# include/constants_param_wizard.php3, row 178, 195
$_m["Field ID"]
 = "ID campo";

# include/constants_param_wizard.php3, row 179, 196
$_m["Field in which to look for matching values."]
 = "Campo en el que buscar valores que coincidan.";

# include/constants_param_wizard.php3, row 182
$_m["Scope"]
 = "Ambito";

# include/constants_param_wizard.php3, row 186
$_m["<b>1</b> = This slice only.\n"
   ."                <b>2</b> = All slices.<br>\n"
   ."                <b>0</b> = Username, special: Checks uniqueness in reader management\n"
   ."                slices and in the permission system. Always uses field ID %1"]
 = "<b>1</b> = Este canal solamente.\n"
   ."                <b>2</b> = Todos los canales.<br>\n"
   ."                <b>0</b> = Nombre de usuario, especial: Verifica la unicidad en el administrador de lectura\n"
   ."                canales y en el sistema de permisos. Siempre utilice el Id del campo %1";

# include/constants_param_wizard.php3, row 192
$_m["Unique e-mail"]
 = "e-mail �nico";

# include/constants_param_wizard.php3, row 193
$_m["Combines the e-mail and unique validations. Validates only if the value is a valid email address and not yet used."]
 = "Combina las validaciones de email y unicidad. Admite s�lo los valores que sean direcciones v�lidas y que no est�n en uso.";

# include/constants_param_wizard.php3, row 210
$_m["Validates the passwords do not differ when changing password.\n"
   ."        <i>The validation is provided only by JavaScript and not by ValidateInput()\n"
   ."        because the insert\n"
   ."        function does the validation again before inserting the new password.</i>"]
 = "Verifica que las claves no sean distintas al cambiar la clave.\n"
   ."<i>La validaci�n se hace por JavaScript y no en el servidor porque la funci�n de inserci�n no valida antes de insertar la nueva clave</i>.";

# include/constants_param_wizard.php3, row 217
$_m["Input Type"]
 = "Tipo de entrada";

# include/constants_param_wizard.php3, row 219
$_m["Hierarchical constants"]
 = "Constantes jer�rquicas";

# include/constants_param_wizard.php3, row 220
$_m["A view with level boxes allows to choose constants."]
 = "Una vista con cajas para seleccionar constantes.";

# include/constants_param_wizard.php3, row 222
$_m["Level count"]
 = "Niveles";

# include/constants_param_wizard.php3, row 223
$_m["Count of level boxes"]
 = "Cajas de nivel";

# include/constants_param_wizard.php3, row 226
$_m["Box width"]
 = "Ancho de la caja";

# include/constants_param_wizard.php3, row 227
$_m["Width in characters"]
 = "Ancho en caracteres";

# include/constants_param_wizard.php3, row 230
$_m["Size of target"]
 = "Tama�o del destino";

# include/constants_param_wizard.php3, row 231
$_m["Lines in the target select box"]
 = "N�mero de lineas visibles en la caja de selecci�n";

# include/constants_param_wizard.php3, row 234
$_m["Horizontal"]
 = "";

# include/constants_param_wizard.php3, row 235
$_m["Show levels horizontally"]
 = "Mostrar niveles en horizontal";

# include/constants_param_wizard.php3, row 238
$_m["First selectable"]
 = "Primer seleccionable";

# include/constants_param_wizard.php3, row 239
$_m["First level which will have a Select button"]
 = "Primer nivel a tener un bot�n de selecci�n";

# include/constants_param_wizard.php3, row 242
$_m["Level names"]
 = "Nombres de niveles";

# include/constants_param_wizard.php3, row 243
$_m["Names of level boxes, separated by tilde (~). Replace the default Level 0, Level 1, ..."]
 = "Nombres de las cajas de nivel, separadas por virgulilla (~).";

# include/constants_param_wizard.php3, row 245
$_m["Top level~Second level~Keyword"]
 = "General~Segundo nivel~Palabras clave";

# include/constants_param_wizard.php3, row 247
$_m["Text Area"]
 = "Area de texto";

# include/constants_param_wizard.php3, row 248
$_m["Text area with 60 columns"]
 = "Area de texto con 60 columnas";

# include/constants_param_wizard.php3, row 250, 258
$_m["row count"]
 = "filas";

# include/constants_param_wizard.php3, row 255
$_m["Rich Edit Area"]
 = "Area de Texto Enriquecido";

# include/constants_param_wizard.php3, row 256
$_m["Rich edit text area. This operates the same way as Text Area in browsers which don't support the Microsoft TriEdit library. In IE 5.0 and higher and in Netscape 4.76 and higher (after installing the necessary features) it uses the TriEdit to provide an incredibly powerful HTML editor.<br><br>\n"
   ."Another possibility is to use the <b>iframe</b> version which should work in IE on Windows and Mac (set the 3rd parameter to \"iframe\").<br><br>\n"
   ."The code for this editor is taken from the Wysiwyg open project (http://www.unica.edu/uicfreesoft/) and changed to fullfill our needs. See http://www.unica.edu/uicfreesoft/wysiwyg_web_edit/Readme_english.txt on details how to prepare Netscape.<br><br>\n"
   ."The javascript code needed to provide the editor is saved in two HTML files, so that the user doesn't have to load it every time she reloads the Itemedit web page."]
 = "Editor de HTML in situ. En IE 5.0 o superior, as� como en Netscape 4.76 o superior con los plugins necesarios, muestra un editor de HTML en el formulario. En navegadores que no lo soportan, muestra un area de texto normal.<br> Otra posibilidad es usar la versi�n <b>iframe</b> que deber�a  funcionar en IE para Windows y MAC (poner \"iframe\" en el tercer  par�metro)<br><br> <b>ATENCION: no olvide marcar <i>las dos</i> opciones 'Mostrar HTML/texto plano' y 'HTML por defecto' cuando use este tipo de entrada</b>";

# include/constants_param_wizard.php3, row 262
$_m["column count"]
 = "columnas";

# include/constants_param_wizard.php3, row 266, 784
# doc/param_wizard_list.php3, row 96
$_m["type"]
 = "tipo";

# include/constants_param_wizard.php3, row 267
$_m["type: class (default) / iframe"]
 = "tipo: class (por defecto) / iframe";

# include/constants_param_wizard.php3, row 269
$_m["class"]
 = "";

# include/constants_param_wizard.php3, row 271
$_m["Text Field"]
 = "Campo de texto";

# include/constants_param_wizard.php3, row 272
$_m["A text field."]
 = "Un simple campo de texto de una linea";

# include/constants_param_wizard.php3, row 274, 338
$_m["max characters"]
 = "m�x. caracteres";

# include/constants_param_wizard.php3, row 275
$_m["max count of characters entered (maxlength parameter)"]
 = "n�mero m�ximo de caracteres que se pueden introducir";

# include/constants_param_wizard.php3, row 278, 342
$_m["width"]
 = "ancho";

# include/constants_param_wizard.php3, row 279
$_m["width of the field in characters (size parameter)"]
 = "ancho del campo en el formulario de entrada (en columnas de texto)";

# include/constants_param_wizard.php3, row 284
$_m["Multiple Text Field"]
 = "Campo de Texto M�ltiple";

# include/constants_param_wizard.php3, row 285
$_m["Text field input type which allows you to enter more than one (multiple) values into one field (just like if you select multiple values from Multiple Selectbox). The new values are filled by popup box."]
 = "Tipo de entrada de campo que permite entrar m�s de un (m�ltiple) valor dentro de un campo (tal como se realiza cuando se utilizan casillas de selecci�n m�ltiple). Los nuevos valores son llenados por la caja popup.";

# include/constants_param_wizard.php3, row 287, 567
$_m["Show Actions"]
 = "Mostrar Acciones";

# include/constants_param_wizard.php3, row 293
$_m["Which action buttons to show:\n"
   ."                       <br>M - Move (up and down)\n"
   ."                       <br>D - Delete value,\n"
   ."                       <br>A - Add new value\n"
   ."                       <br>C - Change the value\n"
   ."                       <br>Use 'MDAC' (default), 'DAC', just 'M' or any other combination. The order of letters M,D,A,C is not important."]
 = "Qu� botones de acci�n mostrar:\n"
   ."                       <br>M - Mover (arriba y abajo)\n"
   ."                       <br>D - Borrar un valor,\n"
   ."                       <br>A - A�adir un valor,\n"
   ."                       <br>C - Cambiar un valor\n"
   ."                       <br>Use 'MDAC' (por defecto), 'DAC', solamente 'M' u otra combinaci�n. El orden de las letras M,D,A,C no es importante.";

# include/constants_param_wizard.php3, row 296, 498, 547, 607
$_m["Row count"]
 = "Filas";

# include/constants_param_wizard.php3, row 297
$_m["Number of rows (values) displayed at once"]
 = "N�mero de columnas (valores) mostrados a la vez";

# include/constants_param_wizard.php3, row 303
$_m["Select Box"]
 = "Caja selecci�n";

# include/constants_param_wizard.php3, row 304
$_m["A selectbox field with a values list.<br><br>It uses the Constants select box - if you choose a constant group there, the constants of this group will be printed, if you choose a slice name, the headlines of all items will be printed (used for related stories or for setting relation to another slice, usually with the f_v alias function)"]
 = "Una caja de selecci�n con una lista de valores.<br><br>Utiliza la caja de selecci�n de constantes: si usted selecciona ah� un grupo de constantes, ese grupo se mostrar� en la lista de valores; si selecciona un nombre de canal se mostrar�n los t�tulos de los �tems (�til para hacer relaciones con otro canal, normalmente usando un alias con la funci�n f_v)";

# include/constants_param_wizard.php3, row 306, 346
$_m["slice field"]
 = "campo del otro canal";

# include/constants_param_wizard.php3, row 307, 347, 503
$_m["field (or format string) that will be displayed in select box (from related slice). if not specified, in select box are displayed headlines. you can use also any AA formatstring here (like: _#HEADLINE - _#PUB_DATE). (only for constants input type: slice)"]
 = "campo (o cadena de formato) que se mostrar� en la caja de selecci�n (del canal relacionado). Si no se especifica, en la caja de selecci�n se mostrar�n los encabezados. Usted puede utilizar ac� un formato de las AA (como: _#HEADLINE - _#PUB_DATE). (solamente para constantes de tipo entrada: slice)";

# include/constants_param_wizard.php3, row 309, 349, 413, 472, 505, 622, 910
$_m["category........"]
 = "";

# include/constants_param_wizard.php3, row 310, 350
$_m["use name"]
 = "usar nombre";

# include/constants_param_wizard.php3, row 311, 351
$_m["if set (=1), then the name of selected constant is used, insted of the value. Default is 0"]
 = "si es '1', se usar� el nombre de las constantes en vez del valor";

# include/constants_param_wizard.php3, row 314, 366, 414, 473, 506, 623
$_m["Show items from bins"]
 = "Mostrar �tems desde las carpetas";

# include/constants_param_wizard.php3, row 322, 374, 422, 481, 514, 631
$_m["(for slices only) To show items from selected bins, use following values:<br>Active bin - '%1'<br>Pending bin - '%2'<br>Expired bin - '%3'<br>Holding bin - '%4'<br>Trash bin - '%5'<br>Value is created as follows: eg. You want show headlines from Active, Expired and Holding bins. Value for this combination is counted like %1+%3+%4&nbsp;=&nbsp;13"]
 = "(para canales solamente). Para mostrar �tems seleccionados de las carpetas utilice los siguientes valores:<br>Aprobados -  '%1'<br>Pendientes - '%2'<br>Expirados - '%3'<br>Por aprobar - '%4'<br>Papelera - '%5'<br>El valor se genera como se muestra a continuaci�n: p.e. Si Usted desea mostrar los encabezados desde Aprobados, Caducados y Por Aprobar. El valor de esta combinaci�n se cuentas as� %1+%3+%4&nbsp;=&nbsp;13";

# include/constants_param_wizard.php3, row 325, 377, 425, 484, 517, 634
$_m["Filtering conditions"]
 = "Condiciones de filtrado";

# include/constants_param_wizard.php3, row 326, 378, 426, 485, 518, 635
$_m["(for slices only) Conditions for filtering items in selection. Use conds[] array."]
 = "(para canales solamente) Condiciones de filtrado de �tems en la selecci�n. Utilice el arreglo conds[]";

# include/constants_param_wizard.php3, row 329, 381, 429, 488, 521, 638
$_m["Sort by"]
 = "Ordenado por";

# include/constants_param_wizard.php3, row 330, 382, 430, 489, 522, 639
$_m["(for slices only) Sort the items in specified order. Use sort[] array"]
 = "(para canales solamente) Ordenar los �tems en un orden espec�fico. Utilice el arreglo sort[]";

# include/constants_param_wizard.php3, row 335
$_m["Text Field with Presets"]
 = "Campo de texto con preselecci�n";

# include/constants_param_wizard.php3, row 336
$_m["Text field with values names list. When you choose a name from the list, the appropriate value is printed in the text field"]
 = "Un campo de texto con una lista de valores al lado. Al seleccionar un valor de la lista, este valor se muestra en el campo de texto";

# include/constants_param_wizard.php3, row 339
$_m["max count of characteres entered in the text field (maxlength parameter)"]
 = "n�mero m�ximo de caracteres que acepta el campo de texto";

# include/constants_param_wizard.php3, row 343
$_m["width of the text field in characters (size parameter)"]
 = "ancho del campo de texto a mostra (n�mero de columnas de texto)r";

# include/constants_param_wizard.php3, row 354
$_m["adding"]
 = "a�adir";

# include/constants_param_wizard.php3, row 355
$_m["adding the selected items to input field comma separated"]
 = "en vez de sobreescribir, ir a�adiendo los valores separados por comas";

# include/constants_param_wizard.php3, row 358
$_m["secondfield"]
 = "otro campo";

# include/constants_param_wizard.php3, row 359
$_m["field_id of another text field, where value of this selectbox will be propagated too (in main text are will be text and there will be value)"]
 = "identificador de otro campo de texto donde propagar el valor de esta selecci�n";

# include/constants_param_wizard.php3, row 361, 859
$_m["source_href....."]
 = "";

# include/constants_param_wizard.php3, row 362
$_m["add2constant"]
 = "crear constantes";

# include/constants_param_wizard.php3, row 363
$_m["if set to 1, user typped value in inputform is stored into constants (only if the value is not already there)"]
 = "si es '1' y el texto que se entra no se encuentra ya en la lista de valores, se a�ade una nueva constante";

# include/constants_param_wizard.php3, row 387
$_m["Text Area with Presets"]
 = "Area de texto con Preselecci�n";

# include/constants_param_wizard.php3, row 388
$_m["Text area with values names list. When you choose a name from the list, the appropriate value is printed in the text area"]
 = "Un campo de texto con un area de texto al lado rellena con una lista de valores. Al seleccionar un valor de la lista, se escribe en el campo de texto";

# include/constants_param_wizard.php3, row 390
$_m["rows"]
 = "filas";

# include/constants_param_wizard.php3, row 391
$_m["Textarea rows"]
 = "n�mero de filas del area de texto";

# include/constants_param_wizard.php3, row 394
$_m["cols"]
 = "columnas";

# include/constants_param_wizard.php3, row 395
$_m["Text area columns"]
 = "n�mero de columnas del area de texto";

# include/constants_param_wizard.php3, row 399
$_m["Radio Button"]
 = "";

# include/constants_param_wizard.php3, row 400
$_m["Radio button group - the user may choose one value of the list. <br><br>It uses the Constants select box - if you choose a constant group there, the constants of this group will be printed, if you choose a slice name, the headlines of all items will be printed (used for related stories or for setting relation to another slice - it is obviously used with f_v alias function then)"]
 = "Grupo de botones para chequear, donde el usuario s�lo puede seleccionar uno a la vez de entre toda la lista. <br><br>Usa la caja de selecci�n de constantes para determinar la lista de botones (puede seleccionar ah� un grupo de constantes o un canal)";

# include/constants_param_wizard.php3, row 402, 461
$_m["Columns"]
 = "Columnas";

# include/constants_param_wizard.php3, row 403, 462
$_m["Number of columns. If unfilled, the checkboxes are all on one line. If filled, they are formatted in a table."]
 = "N�mero de columnas. Si no se especifica, todos los �tems aparecen en linea. Si se pone aqu� un valor, se formatean como una tabla";

# include/constants_param_wizard.php3, row 406, 465
$_m["Move right"]
 = "a la derecha";

# include/constants_param_wizard.php3, row 407, 466
$_m["Should the function move right or down to the next value?"]
 = "Cuando se formatea en tabla, poner los items de izquierda a derecha (si es '1') o de arriba a abajo (si es '0')";

# include/constants_param_wizard.php3, row 410, 469, 502, 597, 619
$_m["Slice field"]
 = "Campo de canal";

# include/constants_param_wizard.php3, row 411
$_m["Field (or format string) that will be displayed as radiobuton's option (from related slice). if not specified, in select box are displayed headlines. you can use also any AA formatstring here (like: _#HEADLINE - _#PUB_DATE). (only for constants input type: slice)"]
 = "Campo (o cadena de formato) que ser� mostrada como una opci�n de bot�n de selecci�n (desde un canal relacionado). Si no se especifica, en la caja de selecci�n se mostrar�n los encabezados. Usted puede utilizar ac� un formato de las AA (como: _#HEADLINE - _#PUB_DATE). (solamente para constantes de tipo entrada: slice)";

# include/constants_param_wizard.php3, row 435
$_m["Date"]
 = "Fecha";

# include/constants_param_wizard.php3, row 436
$_m["you can choose an interval from which the year will be offered"]
 = "puede seleccionar un intervalo de a�os a ofrecer";

# include/constants_param_wizard.php3, row 438
$_m["Starting Year"]
 = "A�o de inicio";

# include/constants_param_wizard.php3, row 439
$_m["The (relative) start of the year interval"]
 = "El primer a�o del rango (relativo al a�o actual)";

# include/constants_param_wizard.php3, row 442
$_m["Ending Year"]
 = "A�o final";

# include/constants_param_wizard.php3, row 443
$_m["The (relative) end of the year interval"]
 = "El �ltimo a�o del rango (relativo al actual)";

# include/constants_param_wizard.php3, row 446
$_m["Relative"]
 = "Relativo";

# include/constants_param_wizard.php3, row 447
$_m["If this is 1, the starting and ending year will be taken as relative - the interval will start at (this year - starting year) and end at (this year + ending year). If this is 0, the starting and ending years will be taken as absolute."]
 = "Si es '1', los a�os de inicio y final se tomar�n relativos al a�o en curso, es decir, el a�o inicial ser� este a�o menos el a�o inicial, y el final ser� este a�o m�s el final. Si es '0', los valores de a�o inicial y final se tomar�n como absolutos";

# include/constants_param_wizard.php3, row 450
$_m["Show time"]
 = "Mostrar hora";

# include/constants_param_wizard.php3, row 451
$_m["show the time box? (1 means Yes, undefined means No)"]
 = "Mostrar una caja para especificar la hora";

# include/constants_param_wizard.php3, row 455
$_m["Checkbox"]
 = "Selecci�n";

# include/constants_param_wizard.php3, row 456
$_m["The field value will be represented by a checkbox."]
 = "Mostrar una cajita de selecci�n que permita 'activar' o 'desactivar' el valor de este campo. Sirve para los campos de tipo booleano (cierto o falso)";

# include/constants_param_wizard.php3, row 458
$_m["Multiple Checkboxes"]
 = "Selecci�n M�ltiple";

# include/constants_param_wizard.php3, row 459
$_m["Multiple choice checkbox group. <br><br>It uses the Constants select box - if you choose a constant group there, the constants of this group will be printed, if you choose a slice name, the headlines of all items will be printed (used for related stories or for setting relation to another slice - it is obviously used with f_v alias function then)"]
 = "Mostrar una lista de valores con cajitas para seleccionarlos individualmente.<br><br>La lista de valores se especifica en la caja de selecci�n de constantes, y puede ser un grupo de constantes o un canal.";

# include/constants_param_wizard.php3, row 470
$_m["Field (or format string) that will be displayed as checbox options (from related slice). if not specified, in select box are displayed headlines. you can use also any AA formatstring here (like: _#HEADLINE - _#PUB_DATE). (only for constants input type: slice)"]
 = "Campo (o cadena de formato) que ser� mostrada como una opci�n de bot�n de selecci�n (desde un canal relacionado). Si no se especifica, en la caja de selecci�n se mostrar�n los encabezados. Usted puede utilizar ac� un formato de las AA (como: _#HEADLINE - _#PUB_DATE). (solamente para constantes de tipo entrada: slice)";

# include/constants_param_wizard.php3, row 495
$_m["Multiple Selectbox"]
 = "Caja de selecci�n m�ltiple";

# include/constants_param_wizard.php3, row 496
$_m["Multiple choice select box. <br><br>It uses the Constants select box - if you choose a constant group there, the constants of this group will be printed, if you choose a slice name, the headlines of all items will be printed (used for related stories or for setting relation to another slice - it is obviously used with f_v alias function then)"]
 = "Muestra una caja de selecci�n de donde se puede seleccionar m�s de un valor a la vez (con la tecla control o shift).<br><br>La lista de valores se especifica en la caja de selecci�n de constantes, y puede ser un grupo de constantes o un canal.";

# include/constants_param_wizard.php3, row 528
$_m["File"]
 = "Archivo";

# include/constants_param_wizard.php3, row 529
$_m["File upload - a text field with the file find button"]
 = "Campo para subir archivo: un campo de texto con un bot�n que permite buscar un archivo en el disco local";

# include/constants_param_wizard.php3, row 531
$_m["Allowed file types"]
 = "Tipos aceptados";

# include/constants_param_wizard.php3, row 534
$_m["image/*"]
 = "";

# include/constants_param_wizard.php3, row 535
$_m["Label"]
 = "Etiqueta";

# include/constants_param_wizard.php3, row 536
$_m["To be printed before the file upload field"]
 = "Se muestra delante del campo";

# include/constants_param_wizard.php3, row 538
$_m["File: "]
 = "Archivo#: ";

# include/constants_param_wizard.php3, row 539
$_m["Hint"]
 = "Ayuda";

# include/constants_param_wizard.php3, row 540
$_m["appears beneath the file upload field"]
 = "Aparece bajo el campo de subir archivo";

# include/constants_param_wizard.php3, row 542
$_m["You can select a file ..."]
 = "Solo se aceptan im�genes en formato JPG.";

# include/constants_param_wizard.php3, row 544
$_m["Related Item Window"]
 = "Ventana para relacionar �tems";

# include/constants_param_wizard.php3, row 545
$_m["List of items connected with the active one - by using the buttons Add and Delete you show a window, where you can search in the items list"]
 = "Abre otra ventana para interrelacionar �tems, mediatne los botones A�adir y Borrar";

# include/constants_param_wizard.php3, row 548
$_m["Row count in the list"]
 = "Filas a listar";

# include/constants_param_wizard.php3, row 551
$_m["Buttons to show"]
 = "Mostrar botones";

# include/constants_param_wizard.php3, row 556
$_m["Defines, which buttons to show in item selection:\n"
   ."                       <br>A - Add\n"
   ."                       <br>M - add Mutual\n"
   ."                       <br>B - Backward\n"
   ."                       <br> Use 'AMB' (default), 'MA', just 'A' or any other combination. The order of letters A,M,B is important."]
 = "Define que botones se muestran en la selecci�n de un �tem:\n"
   ."                       <br>A - A�adir\n"
   ."                       <br>M - Mutuo\n"
   ."                       <br>B - Inverso\n"
   ."                       <br> Utilice 'AMB' (por defecto), 'MA', solo 'A' u otra combinaci�n. El orden de las letras A,M,B es importante.";

# include/constants_param_wizard.php3, row 558, 566
$_m["AMB"]
 = "";

# include/constants_param_wizard.php3, row 559
$_m["Admin design"]
 = "dise�o administrador";

# include/constants_param_wizard.php3, row 560
$_m["If set (=1), the items in related selection window will be listed in the same design as in the Item manager - 'Design - Item Manager' settings will be used. Only the checkbox will be replaced by the buttons (see above). It is important that the checkbox must be defined as:<br> <i>&lt;input type=checkbox name=\"chb[x_#ITEM_ID#]\" value=\"1\"&gt;</i> (which is default).<br> If unset (=0), just headline is shown (default)."]
 = "Si es verdadero (1), la lista de items se muestra usando el dise�o del administrador de �tems ('Dise�o - Administrador de �tems'), solo que reemplazando la caja de selecci�n por los botones.<br><b>Importante:</b> el dise�o de la caja de selecci�n debe ser  <i>&lt;input type=checkbox name=\"chb[x_#ITEM_ID#]\" value=\"1\"&gt;</i> (es as� por defecto).<br> Si es falso (0), mostrar solo los t�tulos.";

# include/constants_param_wizard.php3, row 563
$_m["Tag Prefix"]
 = "Prefijo de etiqueta";

# include/constants_param_wizard.php3, row 564
$_m["Selects tag set ('AMB' / 'GYR'). Ask Mitra for more details."]
 = "Selecciona el set de etiquetas ('AMB' / 'GYR').";

# include/constants_param_wizard.php3, row 574
$_m["Which action buttons to show:\n"
   ."                       <br>M - Move (up and down)\n"
   ."                       <br>D - Delete relation,\n"
   ."                       <br>R - add Relation to existing item\n"
   ."                       <br>N - insert new item in related slice and make it related\n"
   ."                       <br>E - Edit related item\n"
   ."                       <br>Use 'DR' (default), 'MDRNE', just 'N' or any other combination. The order of letters M,D,R,N,E is not important."]
 = "Qu� botones de acci�n mostrar:\n"
   ."                       <br>M - Mover (arriba y abajo)\n"
   ."                       <br>D - Borrar relaci�n,\n"
   ."                       <br>R - a�adir relaci�n al �tem actual\n"
   ."                       <br>N - insertar nuevo �tem en el canal relacionado y haga la relaci�n\n"
   ."                       <br>E - Editar el �tem relacionado\n"
   ."                       <br>Use 'DR' (por defecto), 'MDRNE', solo 'N' u otra combinaci�n. El orden de las letras M,D,R,N,E no es importante.";

# include/constants_param_wizard.php3, row 577
$_m["Show headlines from selected bins"]
 = "Mostrar encabezados de las carpetas seleccionadas";

# include/constants_param_wizard.php3, row 586
$_m["To show headlines in related window from selected bins.<br>Use this values for bins:<br>Active bin - '%1'<br>Pending bin - '%2'<br>Expired bin - '%3'<br>Holding bin - '%4'<br>Trash bin - '%5'<br>Value is created as follows: eg. You want show headlines from Active, Expired and Holding bins. Value for this combination is counted like %1+%3+%4&nbsp;=&nbsp;13"]
 = "Para mostrar �tems seleccionados de las carpetas utilice los siguientes valores:<br>Aprobados -  '%1'<br>Pendientes - '%2'<br>Expirados - '%3'<br>Por aprobar - '%4'<br>Papelera - '%5'<br>El valor se genera como se muestra a continuaci�n: p.e. Si Usted desea mostrar los encabezados desde Aprobados, Caducados y Por Aprobar. El valor de esta combinaci�n se cuentas as� %1+%3+%4&nbsp;=&nbsp;13";

# include/constants_param_wizard.php3, row 589
$_m["Filtering conditions - unchangeable"]
 = "Condiciones de filtrado - No es posible cambiarlas";

# include/constants_param_wizard.php3, row 590
$_m["Conditions for filtering items in related items window. This conds user can't change."]
 = "Condiciones para filtrado de �tems en la ventana de �tems relacionados. Estas condiniones no pueden ser cambiadas por el usuario";

# include/constants_param_wizard.php3, row 593
$_m["Filtering conditions - changeable"]
 = "Condiciones de filtrado - No es posible cambiarlas";

# include/constants_param_wizard.php3, row 594
$_m["Conditions for filtering items in related items window. This conds user can change."]
 = "Condiciones para filtrado de �tems en la ventana de �tems relacionados. Estas condiniones no pueden ser cambiadas por el usuario";

# include/constants_param_wizard.php3, row 598
$_m["field (or format string) that will be displayed in the boxes (from related slice). if not specified, in select box are displayed headlines. you can use also any AA formatstring here (like: _#HEADLINE - {publish_date....})."]
 = "";

# include/constants_param_wizard.php3, row 600
$_m["publish_date...."]
 = "";

# include/constants_param_wizard.php3, row 604
$_m["Two Windows"]
 = "Dos cajas";

# include/constants_param_wizard.php3, row 605
$_m["Two Windows. <br><br>It uses the Constants select box - if you choose a constant group there, the constants of this group will be printed, if you choose a slice name, the headlines of all items will be printed (used for related stories or for setting relation to another slice - it is obviously used with f_v alias function then)"]
 = "Muestra dos cajas con botones para intercambiar entre los valores ofrecidos y seleccionados. La lista de valores se especifica en la caja de selecci�n de constantes, y puede ser un grupo de constantes o un canal.";

# include/constants_param_wizard.php3, row 611
$_m["Title of \"Offer\" selectbox"]
 = "T�tulo de la caja \"oferta\"";

# include/constants_param_wizard.php3, row 614
$_m["Our offer"]
 = "Oferta";

# include/constants_param_wizard.php3, row 615
$_m["Title of \"Selected\" selectbox"]
 = "T�tulo de la caja \"seleccionados\"";

# include/constants_param_wizard.php3, row 618
$_m["Selected"]
 = "Seleccionados";

# include/constants_param_wizard.php3, row 620
$_m["field (or format string) that will be displayed in the boxes (from related slice). if not specified, in select box are displayed headlines. you can use also any AA formatstring here (like: _#HEADLINE - _#PUB_DATE). (only for constants input type: slice)"]
 = "Campo (o cadena de formato) que ser� mostrada como una opci�n de bot�n de selecci�n (desde un canal relacionado). Si no se especifica, en la caja de selecci�n se mostrar�n los encabezados. Usted puede utilizar ac� un formato de las AA (como: _#HEADLINE - _#PUB_DATE). (solamente para constantes de tipo entrada: slice)";

# include/constants_param_wizard.php3, row 645
$_m["Hidden field"]
 = "Campo oculto";

# include/constants_param_wizard.php3, row 646
$_m["The field value will be shown as &lt;input type='hidden'. You will probably set this filed by javascript trigger used on any other field."]
 = "El valor de este campo no aparecer� en el formulario, sino que estar� ahi en la forma &lt;input type='hidden'&gt;. Este tipo de entrada es �til para usar Verificadores de Campos con JavaScript sobre cualquier otro campo.";

# include/constants_param_wizard.php3, row 654
$_m["Password input boxes allowing to send password (for password-protected items)\n"
   ."        and to change password (including the \"Retype password\" box).<br><br>\n"
   ."        When a user fills new password, it is checked against the retyped password,\n"
   ."        MD5-encrypted so that nobody may learn it and stored in the database.<br><br>\n"
   ."        If the field is not Required, shows a 'Delete Password' checkbox."]
 = "Campos de texto para claves. Si el campo no es obligatorio, tambi�n muestra una caja para 'Borrar Clave'";

# include/constants_param_wizard.php3, row 656
$_m["Field size"]
 = "Tama�o del campo";

# include/constants_param_wizard.php3, row 657
$_m["Size of the three fields"]
 = "Tama�o de los campos";

# include/constants_param_wizard.php3, row 660
$_m["Label for Change Password"]
 = "Etiqueta para Cambio de clave";

# include/constants_param_wizard.php3, row 661
$_m["Replaces the default 'Change Password'"]
 = "Sustituye la normal 'Cambiar Clave'";

# include/constants_param_wizard.php3, row 663
$_m["Change your password"]
 = "Nueva clave";

# include/constants_param_wizard.php3, row 664
$_m["Label for Retype New Password"]
 = "Etiqueta para confirmar clave";

# include/constants_param_wizard.php3, row 665
$_m["Replaces the default \"Retype New Password\""]
 = "Sustituye la normal 'Reescriba nueva clave'";

# include/constants_param_wizard.php3, row 667
$_m["Retype the new password"]
 = "Confirmar Nueva clave";

# include/constants_param_wizard.php3, row 668
$_m["Label for Delete Password"]
 = "Etiqueta para Borrar Clave";

# include/constants_param_wizard.php3, row 669
$_m["Replaces the default \"Delete Password\""]
 = "Sustituye la normal 'Borrar Clave'";

# include/constants_param_wizard.php3, row 671
$_m["Delete password (set to empty)"]
 = "Borrar (vaciar)";

# include/constants_param_wizard.php3, row 672
$_m["Help for Change Password"]
 = "Ayuda";

# include/constants_param_wizard.php3, row 673
$_m["Help text under the Change Password box (default: no text)"]
 = "Texto a mostrar debajo de la caja de camibo de clave";

# include/constants_param_wizard.php3, row 675
$_m["To change password, enter the new password here and below"]
 = "Para cambiar su clave, escribala aqu� y debajo para confirmar";

# include/constants_param_wizard.php3, row 676
$_m["Help for Retype New Password"]
 = "Ayuda para Confirmaci�n";

# include/constants_param_wizard.php3, row 677
$_m["Help text under the Retype New Password box (default: no text)"]
 = "Texto a mostrar debajo de la caja de confirmaci�n de clave";

# include/constants_param_wizard.php3, row 680
$_m["Retype the new password exactly the same as you entered into \"Change Password\"."]
 = "Reescriba aqu� su clave exactamente como la introdujo en el campo anterior";

# include/constants_param_wizard.php3, row 684
$_m["Do not show"]
 = "No mostrar";

# include/constants_param_wizard.php3, row 685
$_m["This option hides the input field"]
 = "Esta opci�n esconde el campo de entrada";

# include/constants_param_wizard.php3, row 689
$_m["Function"]
 = "Funci�n";

# include/constants_param_wizard.php3, row 690
$_m["How the formatting in the text on this page is used:<br><i>the field</i> in italics stands for the field edited in the \"configure Fields\" window,<br><b>parameter name</b> in bold stands for a parameter on this screen."]
 = "Convenciones de presentaci�n: <br><i>el campo</i> en cursiva significa el valor del campo.,<br><b>par�metro</b> en negrilla se refiera a uno de los par�metros de esta ventana";

# include/constants_param_wizard.php3, row 692
$_m["null function"]
 = "funci�n nula";

# include/constants_param_wizard.php3, row 693
$_m["prints nothing"]
 = "no muestra nada";

# include/constants_param_wizard.php3, row 694
$_m["abstract"]
 = "resumen";

# include/constants_param_wizard.php3, row 695
$_m["prints abstract (if exists) or the beginning of the <b>fulltext</b>"]
 = "muestra el res�men (si existe), o el principio del <b>texto completo</b>";

# include/constants_param_wizard.php3, row 697
$_m["length"]
 = "longitud";

# include/constants_param_wizard.php3, row 698
$_m["max number of characters grabbed from the <b>fulltext</b> field"]
 = "m�ximo n�mero de caracteres tomados del campo de <b>texto completo</b>";

# include/constants_param_wizard.php3, row 701
$_m["fulltext"]
 = "texto completo";

# include/constants_param_wizard.php3, row 702
$_m["field id of fulltext field (like full_text.......), from which the text is grabbed. If empty, the text is grabbed from <i>the field</i> itself."]
 = "id de campo o campo de texto completo (como full_text.......), desde donde el texto es tomado. Si se deja vac�o, el texto se tomar� del <i>mismo</i> campo.";

# include/constants_param_wizard.php3, row 704, 731, 922
$_m["full_text......."]
 = "texto_completo..";

# include/constants_param_wizard.php3, row 705
$_m["paragraph"]
 = "p�rrafo";

# include/constants_param_wizard.php3, row 706
$_m["take first paragraph (text until \\<BR\\> or \\<P\\> or \\</P\\> or at least '.' (dot)) if shorter then <b>length</b>"]
 = "tome el primer p�rrafo (texto hasta  \\<BR\\> o \\<P\\> o \\</P\\> o al menos '.' (punto)) si es m�s corto entonces <b>longitud</b>";

# include/constants_param_wizard.php3, row 709
$_m["extended fulltext link"]
 = "enlace a texto completo extendido";

# include/constants_param_wizard.php3, row 710
$_m["Prints some <b>text</b> (or field content) with a link to the fulltext. A more general version of the f_f function. This function doesn't use <i>the field</i>."]
 = "Esta es una versi�n m�s gen�rica de la funci�n f_f que no usa <i>el campo</i>. Muestra el <i>texto</i> (o su contenido) como un enlace al texto completo.";

# include/constants_param_wizard.php3, row 712, 795
$_m["link only"]
 = "externo";

# include/constants_param_wizard.php3, row 713
$_m["field id (like 'link_only.......') where switch between external and internal item is stored.  (That page should contain SSI include ../slice.php3.). If unfilled, the same page as for item index is used for fulltext (which is obvious behavior)."]
 = "identificador del campo que decide si el enlace debe ser al texto completo o bien externo.";

# include/constants_param_wizard.php3, row 715, 798
$_m["link_only......."]
 = "";

# include/constants_param_wizard.php3, row 716
$_m["url_field"]
 = "campo url";

# include/constants_param_wizard.php3, row 717
$_m["field id if field, where external URL is stored (like hl_href.........)"]
 = "identificador del campo que contiene el URL en caso de enlaces externos";

# include/constants_param_wizard.php3, row 719
$_m["hl_href........."]
 = "";

# include/constants_param_wizard.php3, row 720, 799
$_m["redirect"]
 = "redirecci�n";

# include/constants_param_wizard.php3, row 721, 800
$_m["The URL of another page which shows the content of the item. (That page should contain SSI include ../slice.php3.). If unfilled, the same page as for item index is used for fulltext (which is obvious behavior)."]
 = "URL de la p�gina que sabe mostrar el texto completo (esta p�gina es la que contiene el SSI include de slice.php3). Si no se pone nada, se asume la p�gina actual.";

# include/constants_param_wizard.php3, row 723, 802
$_m["http#://www.ecn.cz/articles/solar.shtml"]
 = "http#://mi.sitio.com/noticias.shtml";

# include/constants_param_wizard.php3, row 724
$_m["text"]
 = "texto";

# include/constants_param_wizard.php3, row 725
$_m["The text to be surrounded by the link. If this parameter is a field id, the field's content is used, else it is used verbatim"]
 = "El texto del enlace. Puede poner un identificador de campo aqu�, en cuyo caso se muestra el contenido de dicho campo. De lo contrario, se muestra este texto literalmente.";

# include/constants_param_wizard.php3, row 728
$_m["condition field"]
 = "campo condici�n";

# include/constants_param_wizard.php3, row 729
$_m["when the specified field hasn't any content, no link is printed, but only the <b>text</b>"]
 = "cuando el campo especificado no tenga contenido no se va a generar un enlace, s�lo se va a mostrar el <b>texto</b>";

# include/constants_param_wizard.php3, row 732, 879
$_m["tag addition"]
 = "a�adir al tag";

# include/constants_param_wizard.php3, row 733, 880
$_m["additional text to the \"\\<a\\>\" tag"]
 = "atributos adicionales para el tag 'A'";

# include/constants_param_wizard.php3, row 735, 882
$_m["target=_blank"]
 = "";

# include/constants_param_wizard.php3, row 736, 803
$_m["no session id"]
 = "sin sesi�n";

# include/constants_param_wizard.php3, row 737, 804
$_m["If 1, the session id (AA_SL_Session=...) is not added to url"]
 = "Si es '1', se omite el identificador de sesi�n al generar los enlaces a texto completo";

# include/constants_param_wizard.php3, row 740, 743
$_m["condition"]
 = "condici�n";

# include/constants_param_wizard.php3, row 741
$_m["This is a very powerful function. It may be used as a better replace of some previous functions. If <b>cond_field</b> = <b>condition</b>, prints <b>begin</b> <i>field</i> <b>end</b>, else prints <b>else</b>. If <b>cond_field</b> is not specified, <i>the field</i> is used. Condition may be reversed (negated) by the \"!\" character at the beginning of it."]
 = "Esta funci�n es muy poderosa. Entre otras cosas, puede remplazar a otras funciones. Si <b>campo_condici�n</b> = <b>condici�n</b>, muestra <b>inicio</b> <i>el campo</i> <b>fin</b>, de lo contrario muestra <b>si_no</b>. Si no se especifica <b>campo_condici�n</b>, se usa <i>el campo</i>. La condici�n se puede invertir (negar) poniendo un signo de admiraci�n (\"!\") al principio.";

# include/constants_param_wizard.php3, row 744
$_m["you may use \"!\" to reverse (negate) the condition"]
 = "puede usar \"!\" para invertir (negar) la condici�n";

# include/constants_param_wizard.php3, row 747, 863
$_m["begin"]
 = "inicio";

# include/constants_param_wizard.php3, row 748
$_m["text to print before <i>field</i>, if condition is true"]
 = "texto a mostrar antes de <i>el campo</i> si la condici�n es cierta";

# include/constants_param_wizard.php3, row 750
$_m["Yes"]
 = "Si";

# include/constants_param_wizard.php3, row 751
$_m["end"]
 = "fin";

# include/constants_param_wizard.php3, row 752
$_m["text to print after <i>field</i>, if condition is true"]
 = "texto a mostrar despu�s de <i>el campo</i> si la condici�n es cierta";

# include/constants_param_wizard.php3, row 755
$_m["else"]
 = "si_no";

# include/constants_param_wizard.php3, row 756
$_m["text to print when condition is not satisfied"]
 = "texto a mostrar cuando la condicion no se satisface";

# include/constants_param_wizard.php3, row 758
$_m["No"]
 = "";

# include/constants_param_wizard.php3, row 759
$_m["cond_field"]
 = "campo_condicion";

# include/constants_param_wizard.php3, row 760
$_m["field to compare with the <b>condition</b> - if not filled, <i>field</i> is used"]
 = "campo sobre el que evaluar la <b>condici�n</b>. Si no se pone nada, se compara la condici�n con <i>el campo</i>";

# include/constants_param_wizard.php3, row 763
$_m["skip_the_field"]
 = "omitir_campo";

# include/constants_param_wizard.php3, row 764
$_m["if set to 1, skip <i>the field</i> (print only <b>begin end</b> or <b>else</b>)"]
 = "si se pone '1', no muestra <i>el campo</i> (muestra solo <b>inicio fin</b>)";

# include/constants_param_wizard.php3, row 768
$_m["This example is usable e.g. for Highlight field - it shows Yes or No depending on the field content"]
 = "Ejemplo para mostrar el campo 'Resaltado': muestra 'Si' o 'No' dependiendo del contenido del campo";

# include/constants_param_wizard.php3, row 769
$_m["1:Yes::No::1"]
 = "1:Si::No::1";

# include/constants_param_wizard.php3, row 770
$_m["When e-mail field is filled, prints something like \"Email: email@apc.org\", when it is empty, prints nothing"]
 = "Si se rellena el campo 'Fuente', muestra algo as�: \"Fuente: Reuters\"; de lo contrario, no muestra nada";

# include/constants_param_wizard.php3, row 771
$_m["!:Email#:&nbsp;"]
 = "!:Fuente#:&nbsp;";

# include/constants_param_wizard.php3, row 772
$_m["Print image height attribute, if <i>the field</i> is filled, nothing otherwise."]
 = "Igual que el anterior, pero si no se conoce la fuente, muestra el autor (suponiendo que el alias _#AUTOR___ lo muestra)";

# include/constants_param_wizard.php3, row 773
$_m["!:height="]
 = "!:Fuente#:&nbsp;::_#AUTOR___";

# include/constants_param_wizard.php3, row 774
$_m["date"]
 = "fecha";

# include/constants_param_wizard.php3, row 775
$_m["prints date in a user defined format"]
 = "muestra la fecha en el formato definido";

# include/constants_param_wizard.php3, row 777
$_m["format"]
 = "formato";

# include/constants_param_wizard.php3, row 778
$_m["PHP-like format - see <a href=\"http://php.net/manual/en/function.date.php\" target=_blank>PHP manual</a>"]
 = "Formato estilo PHP - vea <a href=\"http://php.net/manual/en/function.date.php\" target=_blank>el manual de PHP</a>";

# include/constants_param_wizard.php3, row 780
$_m["m-d-Y"]
 = "d-m-Y";

# include/constants_param_wizard.php3, row 781
$_m["edit item"]
 = "editar �tem";

# include/constants_param_wizard.php3, row 782
$_m["_#EDITITEM used on admin page index.php3 for itemedit url"]
 = "alias _#EDITITEM usado en la p�gina de administraci�n index.php3 para el enlace para editar el �tem";

# include/constants_param_wizard.php3, row 785
$_m["disc - for editing a discussion<br>itemcount - to output an item count<br>safe - for safe html<br>slice_info - select a field from the slice info<br>edit - URL to edit the item<br>add - URL to add a new item"]
 = "disc - para editar comentarios<br>itemcount - recuento de �tems<br>safe - HTML seguro<br>slice_info - seleccionar un campo del canal<br>edit - URL para editar el �tem<br>add - URL para a�adir un �tem";

# include/constants_param_wizard.php3, row 787
$_m["edit"]
 = "editar";

# include/constants_param_wizard.php3, row 788
$_m["return url"]
 = "url retorno";

# include/constants_param_wizard.php3, row 789
$_m["Return url being called from, usually leave blank and allow default"]
 = "URL de la p�gina a regresar. Si no se especifica, regresa a la actual.";

# include/constants_param_wizard.php3, row 791
$_m["/mysite.shtml"]
 = "/noticias.shtml";

# include/constants_param_wizard.php3, row 792
$_m["fulltext link"]
 = "enlace a texto completo";

# include/constants_param_wizard.php3, row 793
$_m["Prints the URL name inside a link to the fulltext - enables using external items. To be used immediately after \"\\<a href=\""]
 = "URL para el texto completo del �tem. Permite usar enlaces externos. Para usar dentro de \"\\<a href=\"";

# include/constants_param_wizard.php3, row 796
$_m["field id (like 'link_only.......') where switch between external and internal item is stored. Usually this field is represented as checkbox. If the checkbox is checked, <i>the field</i> is printed, if unchecked, link to fulltext is printed (it depends on <b>redirect</b> parameter, too)."]
 = "identificador del campo que determina si el enlace es externo o interno (normalmente este campo se muestra como un checkbox). Si el checkbox est� seleccionado, se muestra <i>el campo</i>, y si no est� seleccionado se genera un enlace al texto completo (que depende del par�metro <b>redirecci�n</b>).";

# include/constants_param_wizard.php3, row 807
$_m["image height"]
 = "alto im�gen";

# include/constants_param_wizard.php3, row 808
$_m["An old-style function. Prints <i>the field</i> as image height value (\\<img height=...\\>) or erases the height tag. To be used immediately after \"height=\".The f_c function provides a better way of doing this with parameters \":height=\". "]
 = "Funci�n anticuada. Use mejor la funci�n f_c";

# include/constants_param_wizard.php3, row 809
$_m["print HTML multiple"]
 = "m�ltiples - HTML";

# include/constants_param_wizard.php3, row 810
$_m["prints <i>the field</i> content depending on the html flag (escape html special characters or just print)"]
 = "Muestra <i>el campo</i>, adminitiendo que �ste tenga m�ltiples valores. Adem�s, dependiendo del flag 'HTML / texto plano', puede preformatear la salida";

# include/constants_param_wizard.php3, row 812
$_m["delimiter"]
 = "separador";

# include/constants_param_wizard.php3, row 813
$_m["if specified, a field with multiple values is displayed with the values delimited by it"]
 = "Si se especifica, cuando hay m�ltiples valores se muestran separados por este delimitador";

# include/constants_param_wizard.php3, row 816
$_m["image src"]
 = "url imagen";

# include/constants_param_wizard.php3, row 817
$_m["prints <i>the field</i> as image source (\\<img src=...\\>) - NO_PICTURE for none. The same could be done by the f_c function with parameters :::NO_PICTURE. "]
 = "Funci�n anticuada. Use mejor la funci�n f_c";

# include/constants_param_wizard.php3, row 818
$_m["image size"]
 = "tama�o im�gen";

# include/constants_param_wizard.php3, row 819
$_m["prints <i>the field</i> as image size (height='xxx' width='yyy') (or other image information) or empty string if cant work out, does not special case URLs from uploads directory, might do later! "]
 = "imprima <i>el campo</i> como tama�o de imagen (height='xxx' width='yyy') (u otra informaci�n de la imagen).";

# include/constants_param_wizard.php3, row 821
$_m["information"]
 = "informaci�n";

# include/constants_param_wizard.php3, row 822
$_m["specifies returned information: <br> - <i>html</i> - (default) - returns image size as HTML atributes (height='xxx' width='yyy')<br> - <i>width</i> - returns width of image in pixels<br> - <i>height</i> - returns height of image in pixels<br> - <i>imgtype</i> - returns flag indicating the type of the image: 1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP, 7 = TIFF(intel byte order), 8 = TIFF(motorola byte order), 9 = JPC, 10 = JP2, 11 = JPX, 12 = JB2, 13 = SWC, 14 = IFF, 15 = WBMP, 16 = XBM<br> - <i>mime</i> - returns mimetype of the image (like 'image/gif', 'application/x-shockwave-flash', ...)"]
 = "especifica la informaci�n regresada: <br> - <i>html</i> - (por defecto) - retorna el tama�o de la imagen como atributos de HTML (height='xxx' width='yyy')<br> - <i>width</i> - retorna el ancho de la imagen en pixels.<br> - <i>height</i> - retorna el alto de la imagen en pixels<br> - <i>imgtype</i> - retorna una bandera indicando el tipo de la imagen: 1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP, 7 = TIFF(orden byte intel), 8 = TIFF(orden byte motorola), 9 = JPC, 10 = JP2, 11 = JPX, 12 = JB2, 13 = SWC, 14 = IFF, 15 = WBMP, 16 = XBM<br> - <i>mime</i> - retorna el mimetype de la imagen (como 'image/gif', 'application/x-shockwave-flash', ...)";

# include/constants_param_wizard.php3, row 826
$_m["expanded string"]
 = "expandir cadena";

# include/constants_param_wizard.php3, row 827
$_m["expands the string in the parameter"]
 = "expande la cadena del par�metro";

# include/constants_param_wizard.php3, row 829
$_m["string to expand"]
 = "cadena";

# include/constants_param_wizard.php3, row 830
$_m["if specified then this string is expanded, if not specified then expands the contents of the field"]
 = "si se especifica, expande esta cadena. Si no, se expande <i>el campo</i>";

# include/constants_param_wizard.php3, row 833
$_m["substring with case change"]
 = "subcadena y corrige caja";

# include/constants_param_wizard.php3, row 834
$_m["prints a part of <i>the field</i>"]
 = "muestra una parte d<i>el campo</i>";

# include/constants_param_wizard.php3, row 836
$_m["start"]
 = "inicio";

# include/constants_param_wizard.php3, row 837
$_m["position of substring start (0=first, 1=second, -1=last,-2=two from end)"]
 = "posici�n para empezar a escribir: 0=primer caracter. 1=segundo, -1=�ltimo, -2=pen�ltimo";

# include/constants_param_wizard.php3, row 840
$_m["count"]
 = "cuantos";

# include/constants_param_wizard.php3, row 841
$_m["count of characters (0=until the end)"]
 = "n�mero de caracteres a mostrar (0=hasta el final)";

# include/constants_param_wizard.php3, row 844
$_m["case"]
 = "caja";

# include/constants_param_wizard.php3, row 845
$_m["upper - convert to UPPERCASE, lower - convert to lowercase, first - convert to First Upper; default is don't change"]
 = "upper - convertir a MAYUSCULAS<br>lower - convertir a min�sculas<br>first - convertir Primera May�scula<br>si no se pone nada, no cambia la caja";

# include/constants_param_wizard.php3, row 848
$_m["add string"]
 = "a�adir cadena";

# include/constants_param_wizard.php3, row 849
$_m["if string is shorted, <i>add string</i> is appended to the string (probably something like [...])"]
 = "si la cadena es recortada, <i>a�adir cadena</i> ser� adicionada a la cadena (probablemente algo como [...])";

# include/constants_param_wizard.php3, row 852
$_m["Auto Update Checkbox"]
 = "auto-actualizaci�n";

# include/constants_param_wizard.php3, row 853
$_m["linked field"]
 = "campo relacionado";

# include/constants_param_wizard.php3, row 854
$_m["prints <i>the field</i> as a link if the <b>link URL</b> is not NULL, otherwise prints just <i>the field</i>"]
 = "muestra <i>el campo</i> como un enlace si el <b>URL</b> no est� vac�o. De lo contrario, muestra solamente <i>el campo</i>";

# include/constants_param_wizard.php3, row 856
$_m["link URL"]
 = "URL";

# include/constants_param_wizard.php3, row 860
$_m["e-mail or link"]
 = "e-mail o enlace";

# include/constants_param_wizard.php3, row 861
$_m["mailto link - prints: <br>\"<b>begin</b>\\<a href=\"(mailto:)<i>the field</i>\" <b>tag adition</b>\\><b>field/text</b>\\</a\\>. If <i>the field</i> is not filled, prints <b>else_field/text</b>."]
 = "enlace a correo - muestra:<br>\"<b>inicio</b>\\<a href=\"(mailto:)<i>el campo</i>\" <b>a�adir al tag</b>\\><b>campo/texto</b>\\</a\\>. Si <i>el campo</i> no tiene contenido, muestra <b>si_no/texto</b>.";

# include/constants_param_wizard.php3, row 864
$_m["text before the link"]
 = "texto antes del enlace";

# include/constants_param_wizard.php3, row 866
$_m["e-mail"]
 = "Correo-e:";

# include/constants_param_wizard.php3, row 867
$_m["field/text"]
 = "campo/texto";

# include/constants_param_wizard.php3, row 868
$_m["if this parameter is a field id, the field's content is used, else it is used verbatim"]
 = "si escribe un id de campo, se muestra el contenido de ese campo. De lo contrario, se muestra el texto literal";

# include/constants_param_wizard.php3, row 871
$_m["else_field/text"]
 = "si_no/texto";

# include/constants_param_wizard.php3, row 872
$_m["if <i>the field</i> is empty, only this text (or field content) is printed"]
 = "si <i>el campo</i> est� vac�o, muestra solo este texto (si escribe un id de campo, se mostrar� el contenido de ese campo)";

# include/constants_param_wizard.php3, row 875
$_m["linktype"]
 = "tipo";

# include/constants_param_wizard.php3, row 876
$_m["mailto / href (default is mailto) - it is possible to use f_m function for links, too - just type 'href' as this parameter"]
 = "mailto / href (por defecto es mailto). Si quiere usar esta funci�n para enlaces normales, escriba href en este par�metro";

# include/constants_param_wizard.php3, row 878
$_m["href"]
 = "";

# include/constants_param_wizard.php3, row 883
$_m["hide email"]
 = "esconder correo-e";

# include/constants_param_wizard.php3, row 884
$_m["if 1 then hide email from spam robots. Default is 0."]
 = "si el valor es 1 entonces oculte el correo-e de robots de spam. Por defecto es 0";

# include/constants_param_wizard.php3, row 887
$_m["'New' sign"]
 = "signo 'Nuevo'";

# include/constants_param_wizard.php3, row 888
$_m["prints 'New' or 'Old' or any other text in <b>newer text</b> or <b>older text</b> depending on <b>time</b>. Time is specified in minutes from current time."]
 = "para mostrar 'Nuevo' o 'Viejo' o cualquier otro texto en <b>texto nuevo</b> o <b>texto viejo</b> en funci�n del <b>tiempo</b>, relativo a la fecha y hora actuales.";

# include/constants_param_wizard.php3, row 890
$_m["time"]
 = "tiempo";

# include/constants_param_wizard.php3, row 891
$_m["Time in minutes from current time."]
 = "minutos desde la hora actual";

# include/constants_param_wizard.php3, row 893
$_m["1440"]
 = "";

# include/constants_param_wizard.php3, row 894
$_m["newer text"]
 = "texto nuevo";

# include/constants_param_wizard.php3, row 895
$_m["Text to be printed, if the date in <i>the filed</i> is newer than <i>current_time</i> - <b>time</b>."]
 = "Texto que se muestra si la fecha en <i>el campo</i> es m�s reciente que la hora actual menos <b>tiempo</b>";

# include/constants_param_wizard.php3, row 897
$_m["NEW"]
 = "<b>�NUEVO!</b>";

# include/constants_param_wizard.php3, row 898
$_m["older text"]
 = "texto viejo";

# include/constants_param_wizard.php3, row 899
$_m["Text to be printed, if the date in <i>the filed</i> is older than <i>current_time</i> - <b>time</b>"]
 = "Texto que se muestra si la fecha en <i>el campo</i> es posterior a la hora actual menos <b>tiempo</b>";

# include/constants_param_wizard.php3, row 901, 950
$_m[""]
 = "";

# include/constants_param_wizard.php3, row 902
$_m["id"]
 = "";

# include/constants_param_wizard.php3, row 903
$_m["prints unpacked id (use it, if you watn to show 'item id' or 'slice id')"]
 = "mostrar el identificador interno dentro de las ActionApps, sean de items o de canales.";

# include/constants_param_wizard.php3, row 904
$_m["text (blurb) from another slice"]
 = "texto de otro canal";

# include/constants_param_wizard.php3, row 905
$_m["prints 'blurb' (piece of text) from another slice, based on a simple condition.<br>If <i>the field</i> (or the field specifield by <b>stringToMatch</b>) in current slice matches the content of <b>fieldToMatch</b> in <b>blurbSliceId</b>, it returns the content of <b>fieldToReturn</b> in <b>blurbSliceId</b>."]
 = "muestra un pedazo de texto de otro canal, bas�ndose en una codici�n simple.<br>Si <i>el campo</i> (o el campo especificado en <b>cadenaAComparar</b>) del canal actual coincide con el contenido de <b>campoAComparar</b> en <b>canalPedazo</b>, muestra el contenido de <b>campoContenido</b> del <b>canalContenido</b>";

# include/constants_param_wizard.php3, row 907
$_m["stringToMatch"]
 = "cadenaAComparar";

# include/constants_param_wizard.php3, row 908
$_m["By default it is <i>the field</i>.  It can be formatted either as the id of a field (headline........) OR as static text."]
 = "Por defecto es <i>el campo</i>. Puede ser formateado tanto como id de un campo (headline........) O como texto est�tico.";

# include/constants_param_wizard.php3, row 911
$_m["blurbSliceId"]
 = "canalPedazo";

# include/constants_param_wizard.php3, row 912
$_m["unpacked slice id of the slice where the blurb text is stored"]
 = "id del canal desempaquetado donde el pedazo de texto es almacenado";

# include/constants_param_wizard.php3, row 914
$_m["41415f436f72655f4669656c64732e2e"]
 = "";

# include/constants_param_wizard.php3, row 915
$_m["fieldToMatch"]
 = "campoAComparar";

# include/constants_param_wizard.php3, row 916
$_m["field id of the field in <b>blurbSliceId</b> where to search for <b>stringToMatch</b>"]
 = "id del campo del campo en <b>IdCanalPedazo</b> donde se busca por <b>cadenaABuscar</b>";

# include/constants_param_wizard.php3, row 918
$_m["headline........"]
 = "";

# include/constants_param_wizard.php3, row 919
$_m["fieldToReturn"]
 = "campoContenido";

# include/constants_param_wizard.php3, row 920
$_m["field id of the field in <b>blurbSliceId</b> where the blurb text is stored (what to print)"]
 = "id de campo del campo en <b>IdCanalPropaganda</b> donde el pedazo de texto es almacenado (que imprimir)";

# include/constants_param_wizard.php3, row 923
$_m["RSS tag"]
 = "tag RSS";

# include/constants_param_wizard.php3, row 924
$_m["serves for internal purposes of the predefined RSS aliases (e.g. _#RSS_TITL). Adds the RSS 0.91 compliant tags."]
 = "funci�n usada internamente para los alias de RSS predefinidos (ej. _#RSS_TITL). A�ade los tags RSS 0.91";

# include/constants_param_wizard.php3, row 925, 928, 1034
$_m["default"]
 = "por defecto";

# include/constants_param_wizard.php3, row 926
$_m["prints <i>the field</i> or a default value if <i>the field</i> is empty. The same could be done by the f_c function with parameters :::<b>default</b>."]
 = "muestra <i>el campo</i> o un valor por defecto si <i>el campo</i> est� vac�o. Se puede hacer exactamente lo mismo con la funci�n f_c con los par�metros :::<b>valor por defecto</b>.";

# include/constants_param_wizard.php3, row 929
$_m["default value"]
 = "valor por defecto";

# include/constants_param_wizard.php3, row 931
$_m["javascript: window.alert('No source url specified')"]
 = "javascript#:window.alert(\"No se especific� URL de la fuente\")";

# include/constants_param_wizard.php3, row 932
$_m["print fied"]
 = "imprimir campo";

# include/constants_param_wizard.php3, row 933
$_m["prints <i>the field</i> content (or <i>unalias string</i>) depending on the html flag (if html flag is not set, it converts the content to html. In difference to f_h function, it converts to html line-breaks, too (in its basic variant)"]
 = "imprima el contenido <i>del campo</i> (o <i>cadena alias</i>) dependiendo de la bandera HTML (si la bandera HTML no est� asignada, convierte el contenido a HTML. En diferencia a la funci�n f_h, �sta convierte a HTML los saltos de p�gina, tambi�n (en su variante b�sica)";

# include/constants_param_wizard.php3, row 935
$_m["unalias string"]
 = "cadena";

# include/constants_param_wizard.php3, row 936
$_m["if the <i>unalias string</i> is defined, then the function ignores <i>the field</i> and it rather prints the <i>unalias string</i>. You can of course use any aliases (or fields like {headline.........}) in this string"]
 = "si est� definida, la funci�n ignora <i>el campo</i> y muestra esta cadena. Tenga en cuenta que puede escribir aqu� otros alias.";

# include/constants_param_wizard.php3, row 938
$_m["<img src={img_src.........1} _#IMG_WDTH _#IMG_HGHT>"]
 = "";

# include/constants_param_wizard.php3, row 939
$_m["output modify"]
 = "modificar salida";

# include/constants_param_wizard.php3, row 948
$_m["You can use some output modifications:<br>\n"
   ."                   &nbsp; - [<i>empty</i>] - no modification<br>\n"
   ."                   &nbsp; - <i>csv</i>  - prints the field for CSV file (Comma Separated Values) export<br>\n"
   ."                   &nbsp; - <i>urlencode</i> - URL-encodes string (see <a href=\"http://php.net/urlencode\">urlencode<a> PHP function)<br>\n"
   ."                   &nbsp; - <i>safe</i> - converts special characters to HTML entities (see <a href=\"http://php.net/htmlspecialchars\">htmlspecialchars<a> PHP function)<br>\n"
   ."                   &nbsp; - <i>javascript</i> - escape ' (replace ' with \\')<br>\n"
   ."                   &nbsp; - <i>striptags</i>  - strip HTML and PHP tags from the string<br>\n"
   ."                   &nbsp; - <i>asis</i>  - prints field content 'as is' - it do not add &lt;br&gt; at line ends even if field is marked as 'Plain text'. 'asis' parameter is good for Item Manager's 'Modify content...' feature, for example<br>\n"
   ."                   "]
 = "Usted puede utilizar algunas modificaciones de salida:<br>\n"
   ."                   &nbsp; - [<i>empty</i>] - sin modificaci�n<br>\n"
   ."                   &nbsp; - <i>csv</i>  - imprime el campo para exportaci�n a un archivo CSV (Comma Separated Values - Valores Separados por Coma)<br>\n"
   ."                   &nbsp; - <i>urlencode</i> - cadenas de URL-encodes (vea funci�n <a href=\"http://php.net/urlencode\">urlencode<a> de PHP)<br>\n"
   ."                   &nbsp; - <i>safe</i> - convierte caracteres especiales a entidades de HTML (vea la funci�n <a href=\"http://php.net/htmlspecialchars\">htmlspecialchars<a> de PHP)<br>\n"
   ."                   &nbsp; - <i>javascript</i> - escape ' (reemplazar ' con \\')<br>\n"
   ."                   &nbsp; - <i>striptags</i>  - quitar etiquetas de HTML y PHP de la cadena<br>\n"
   ."                   &nbsp; - <i>asis</i>  - imprime los contenidos del campo 'como son' - no a�ade &lt;br&gt; al final de la l�nea aunque el campo est� marcado como 'Texto plano'. El par�metro 'asis' es bueno para la funci�n de modificar contenido 'Modify content...' del administrador de �tems.<br>\n"
   ."                   ";

# include/constants_param_wizard.php3, row 951
$_m["transformation"]
 = "transformaci�n";

# include/constants_param_wizard.php3, row 952
$_m["Allows to transform the field value to another value.<br>Usage: <b>content_1</b>:<b>return_value_1</b>:<b>content_1</b>:<b>return_value_1</b>:<b>default</b><br>If the content <i>the field</i> is equal to <b>content_1</b> the <b>return_value_1</b> is returned. If the content <i>the field</i> is equal to <b>content_2</b> the <b>return_value_2</b> is returned. If <i>the field is not equal to any <b>content_x</b>, <b>default</b> is returned</i>."]
 = "Permite transformar el valor del campo en otro valor.<br>Uso: <b>contenido_1</b>:<b>valor_retorno_1</b>:<b>contenido_1</b>:<b>valor_retorno_1</b>:<b>por_defecto</b><br>Si el contenido <i>del campo</i> es igual a <b>contenido_1</b> el <b>valor_retorno_1</b> es mostrado. Si el contenido <i>del campo</i> es igual a <b>contenido_2</b> el <b>valor_retorno_2</b> es mostrado. Si <i>el campo no es igual a ning�n <b>contenido_x</b>, <b>por_defecto</b> es mostrado</i>.";

# include/constants_param_wizard.php3, row 954, 962, 970, 978, 986, 994, 1002, 1010, 1018, 1026
$_m["content"]
 = "contenido";

# include/constants_param_wizard.php3, row 955, 963, 971, 979, 987, 995, 1003, 1011, 1019, 1027
$_m["string for comparison with <i>the field</i> for following return value"]
 = "valor a comparar con <i>el campo</i> para que se muestre el valor siguiente";

# include/constants_param_wizard.php3, row 958, 966, 974, 982, 990, 998, 1006, 1014, 1022, 1030
$_m["return value"]
 = "valor retornado";

# include/constants_param_wizard.php3, row 959, 967, 975, 983, 991, 999, 1007, 1015, 1023, 1031
$_m["string to return if previous content matches - You can use field_id too"]
 = "texto a retornar si el contenido anterior coincide - Tambi�n puede usar id_campo para mostrar el contenido de otro campo";

# include/constants_param_wizard.php3, row 961, 969, 977, 985, 993, 1001, 1009, 1017, 1025, 1033
$_m["Environment"]
 = "Educaci�n";

# include/constants_param_wizard.php3, row 1035
$_m["if no content matches, use this string as return value"]
 = "si ninguno coincide, mostrar este valor";

# include/constants_param_wizard.php3, row 1038
$_m["user function"]
 = "definida por el usuario";

# include/constants_param_wizard.php3, row 1039
$_m["calls a user defined function (see How to create new aliases in <a href='http://apc-aa.sourceforge.net/faq/#aliases'>FAQ</a>)"]
 = "hace un llamado a una funci�n definida por el administrador de este sistema (vea <em>How to create new aliases</em> en el <a href='http://apc-aa.sourceforge.net/faq/#aliases' target=_blank>FAQ</a>)";

# include/constants_param_wizard.php3, row 1041
$_m["function"]
 = "funci�n";

# include/constants_param_wizard.php3, row 1042
$_m["name of the function in the include/usr_aliasfnc.php3 file"]
 = "nombre de la funci�n en el archivo include/usr_aliasfnc.php3";

# include/constants_param_wizard.php3, row 1044
$_m["usr_start_end_date_cz"]
 = "usr_encuentra_fecha";

# include/constants_param_wizard.php3, row 1045
$_m["parameter"]
 = "par�metro";

# include/constants_param_wizard.php3, row 1046
$_m["a parameter passed to the function"]
 = "un par�metro a pasar a la funci�n";

# include/constants_param_wizard.php3, row 1049
$_m["view"]
 = "vista";

# include/constants_param_wizard.php3, row 1050
$_m["allows to manipulate the views. This is a complicated and powerful function, described in <a href=\"../doc/FAQ.html#viewparam\" target=_blank>FAQ</a>, which allows to display any view in place of the alias. It can be used for 'related stories' table or for dislaying content of related slice."]
 = "permite mostrar vistas. Esta funci�n es potente y complicada, y est� descrita en el <a href=\"http://apc-aa.sourceforge.net/faq#viewparam\" target=_blank>FAQ</a>. Muestra una vista en el lugar del alias. Es �til, entre otras cosas, para hacer enlaces a items relacionados de otros canales.";

# include/constants_param_wizard.php3, row 1052
$_m["complex parameter"]
 = "par�metro complejo";

# include/constants_param_wizard.php3, row 1053
$_m["this parameter is the same as we use in view.php3 url parameter - see the FAQ"]
 = "este par�metro sigue la sintaxis de los par�metros de view.php3";

# include/constants_param_wizard.php3, row 1055
$_m["vid=4&amp;cmd[23]=v-25"]
 = "";

# include/constants_param_wizard.php3, row 1056
$_m["image width"]
 = "ancho im�gen";

# include/constants_param_wizard.php3, row 1057
$_m["An old-style function. Prints <i>the field</i> as image width value (\\<img width=...\\>) or erases the width tag. To be used immediately after \"width=\".The f_c function provides a better way of doing this with parameters \":width=\". "]
 = "Funci�n anterior. Muestra <i>el campo</i> como el valor del ancho de la imagen (\\<img width=...\\>) o borra la etiqueta de ancho de imagen. A ser utilizada inmediatamente despu�s \"width=\". La funci�n f_c provee a una mejor manera de realizar esta funci�n con los par�metros \":width=\".";

# include/constants_param_wizard.php3, row 1062
$_m["Transformation action"]
 = "Acci�n de transformaci�n";

# include/constants_param_wizard.php3, row 1064
$_m["Store"]
 = "Almacenar";

# include/constants_param_wizard.php3, row 1065
$_m["Simply store a value from the input field"]
 = "Simplemente almacene el valor del campo de entrada";

# include/constants_param_wizard.php3, row 1069
$_m["Remove string"]
 = "Remueva la cadena";

# include/constants_param_wizard.php3, row 1070
$_m["Remove all occurences of a string from the input field."]
 = "Remueva todas las ocurrencias de una cadena desde el campo de entrada";

# include/constants_param_wizard.php3, row 1072, 1090, 1107
$_m["string parameter"]
 = "par�metro de cadena";

# include/constants_param_wizard.php3, row 1073
$_m["Removed string"]
 = "cadena removida";

# include/constants_param_wizard.php3, row 1077
$_m["Format date"]
 = "Formato de fecha";

# include/constants_param_wizard.php3, row 1078
$_m["Parse the date in the input field expected to be in English date format. In case of error, the transformation fails"]
 = "Coloca la fecha en el campo de entrada que se espera sea en formato de fecha Ingl�s. En caso de error, la transformaci�n falla";

# include/constants_param_wizard.php3, row 1082
$_m["Add http prefix"]
 = "A�ade el prefijo http";

# include/constants_param_wizard.php3, row 1083
$_m["Adds 'http://' prefix to the field if not beginning with 'http://' and not empty."]
 = "A�ade el prefijo 'http://' al campo que no comienza con 'http://' y no es vac�o.";

# include/constants_param_wizard.php3, row 1087
$_m["Store parameter"]
 = "Almacene el par�metro";

# include/constants_param_wizard.php3, row 1088
$_m["Store parameter instead of the input field"]
 = "Almacene el par�metro en lugar del campo de entrada";

# include/constants_param_wizard.php3, row 1094
$_m["Store as long id"]
 = "Almacene tan largo como el id";

# include/constants_param_wizard.php3, row 1095
$_m["Creates long id from the string. The string is combined with the parameter!! or with slice_id (if the parameter is not provided. From the same string (and the same parameter) we create allways the same id."]
 = "Crea un id largo desde la cadena. La cadena es combinada con el par�metro!! o con el identificador de canal slice_id (si el par�matro no se incluye. Desde la misma cadena (y el mismo par�metro) se crea siempre el mismo id.";

# include/constants_param_wizard.php3, row 1097
$_m["string to add"]
 = "cadena a a�adir";

# include/constants_param_wizard.php3, row 1098
$_m["this parameter will be added to the string before conversion (the reason is to aviod empty strings and also in order we do not generate allways the same id for common strings (in different imports). If this param is not specified, slice_id is used istead."]
 = "Este par�metro ser� a�adido a la cadena antes de la conversi�n (la razon es para abolir cadenas vacias y tambi�n para no generar siempre el mismo id para cadenas similares (desde diferentes importaciones). Si este par�metro no se especifica, el identificador del canal slice_id ser� utilizado.";

# include/constants_param_wizard.php3, row 1104
$_m["Split input field by string"]
 = "Dividir el campo de entrada por la cadena";

# include/constants_param_wizard.php3, row 1105
$_m["Split input field by string parameter and store the result as multi-value."]
 = "Dividir el campo de entrada por el par�metro de la cadena y almacena el resultado como un valor m�ltiple";

# include/constants_param_wizard.php3, row 1108
$_m["string which separates the values of the input field"]
 = "cadena que separa los valores en el campo de entrada";

# include/constants_param_wizard.php3, row 1113
$_m["Store default value"]
 = "Valor almacenado por defecto";

# include/constants_param_wizard.php3, row 1126
$_m["Store these default values for the following output fields. The other output fields will filled form <i>From</i> field (if specified). Else it is filled by <i>Action parameters</i> string.\n"
   ."    <table>\n"
   ."        <tr><td><b>Output field</b></td><td><b>Value</b></td><td><b>Description</b></td></tr></b>\n"
   ."    <tr><td>Status code</td><td>1</td><td>The item will be stored in Active bin (Hint: set it to 2 for Holding bin)</td></tr>\n"
   ."    <tr><td>Display count</td><td>0</td><td></td></tr>\n"
   ."        <tr><td>Publish date</td><td>Current date</td><td></td></tr>\n"
   ."    <tr><td>Post date</td><td>Current date</td><td></td></tr>\n"
   ."    <tr><td>Last edit</td><td>Current date</td><td></td></tr>\n"
   ."    <tr><td>Expiry date</td><td>Current date + 10 years</td><td></td></tr>\n"
   ."    <tr><td>Posted by</td><td>Active user</td><td></td></tr>\n"
   ."    <tr><td>Edited by</td><td>Active user</td><td></td></tr>\n"
   ."      </table>\n"
   ."    "]
 = "Almacena estos valores por defecto para los siguientes campos de salida. Los dem�s campos de salida ser�n alimentados desde el campo <i>Desde - From</i> (si se especifica). De lo contrario ser� alimentado por la cadena de <i>los par�metros de acci�n</i>.\n"
   ."    <table>\n"
   ."        <tr><td><b>Campo de salida</b></td><td><b>Valor</b></td><td><b>Descripci�n</b></td></tr></b>\n"
   ."    <tr><td>C�digo de estado</td><td>1</td><td>El �tem ser� almacenado en la carpeta Aprobados (Truco: dejelo en 2 para la carpeta Por abrobar)</td></tr>\n"
   ."    <tr><td>Contador de visitas</td><td>0</td><td></td></tr>\n"
   ."        <tr><td>Fecha de documento</td><td>Fecha actual</td><td></td></tr>\n"
   ."    <tr><td>Fecha de publicaci�n</td><td>Fecha actual</td><td></td></tr>\n"
   ."    <tr><td>Ultima edici�n</td><td>Fecha actual</td><td></td></tr>\n"
   ."    <tr><td>Fecha de expiraci�n</td><td>Fecha actual + 10 a�os</td><td></td></tr>\n"
   ."    <tr><td>Publicado por</td><td>Usuario actual</td><td></td></tr>\n"
   ."    <tr><td>Editado por</td><td>Usuario actual</td><td></td></tr>\n"
   ."      </table>\n"
   ."    ";

# doc/param_wizard_list.php3, row 36
$_m["Param Wizard Summary"]
 = "Resumen de Asistentes de par�metros";

# doc/param_wizard_list.php3, row 45
$_m["Choose a Parameter Wizard"]
 = "Escoja un Asistente de par�metros";

# doc/param_wizard_list.php3, row 54, 71
$_m["Go"]
 = "Ir";

# doc/param_wizard_list.php3, row 63
$_m["Change to: "]
 = "Cambiar a: ";

# doc/param_wizard_list.php3, row 78
$_m["TOP"]
 = "ARRIBA";

# doc/param_wizard_list.php3, row 92
$_m["Parameters:"]
 = "Par�metros:";

# doc/param_wizard_list.php3, row 95
$_m["name"]
 = "nombre";

# doc/param_wizard_list.php3, row 97
$_m["description"]
 = "descripci�n";

# doc/param_wizard_list.php3, row 98
$_m["example"]
 = "ejemplo";

# doc/param_wizard_list.php3, row 104
$_m["integer number"]
 = "n�mero entero";

# doc/param_wizard_list.php3, row 105
$_m["any text"]
 = "cualquier texto";

# doc/param_wizard_list.php3, row 106
$_m["field id"]
 = "id campo";

# doc/param_wizard_list.php3, row 107
$_m["boolean: 0=false,1=true"]
 = "booleano: 0=falso,1=verdadero";

?>
