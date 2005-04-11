 // Esta informaci�n corresponde al archivo Dhtmled.js 
 // DHTML Editing Component Constants for JavaScript 
 // 
 // Command IDs 
 // 
 DECMD_BOLD =                      "bold";//5000 
 DECMD_COPY =                      "copy";//5002 
 DECMD_CUT =                       "cut";//5003 
 DECMD_DELETE =                    "delete";
 DECMD_DELETECELLS =               "deletecells";
 DECMD_DELETECOLS =                "deletecols";
 DECMD_DELETEROWS =                "deleterows";
 DECMD_FINDTEXT =                  "findtext";
 DECMD_FONT =                      "font";
 DECMD_HYPERLINK =                 "CreateLink";
 DECMD_IMAGE =                     "createimage";
 DECMD_INDENT =                    "indent";//5018 
 DECMD_INSERTCELL =                "insertcell";
 DECMD_INSERTCOL =                 "insertcol";
 DECMD_INSERTROW =                 "insertrow";
 DECMD_INSERTTABLE =               "insertTable";
 DECMD_ITALIC =                    "italic";
 DECMD_JUSTIFYCENTER =             "justifycenter";
 DECMD_JUSTIFYLEFT =               "justifyleft";
 DECMD_JUSTIFYRIGHT =              "justifyright";
 DECMD_MERGECELLS =                "mergecells";
 DECMD_ORDERLIST =                 "insertOrderedList";
 DECMD_OUTDENT =                   "outdent";
 DECMD_PASTE =                     "paste"; 
 DECMD_REDO =                      "redo";
 DECMD_REMOVEFORMAT =              "removeFormat";
 DECMD_SELECTALL =                 "selectall";
 DECMD_SETBACKCOLOR =              "backcolor";
 DECMD_SETFONTNAME =               "fontname";
 DECMD_SETFONTSIZE =               "fontSize";
 DECMD_SETFORECOLOR =              "forecolor";
 DECMD_SPLITCELL =                 "splitcell";
 DECMD_UNDERLINE =                 "underline";
 DECMD_UNDO =                      "undo";
 DECMD_UNLINK =                    "unlink";
 DECMD_UNORDERLIST =               "insertUnorderedList";
 
 // 
 // Enums 
 // 
 
 // OLECMDEXECOPT   
 OLECMDEXECOPT_DODEFAULT =         0  
 OLECMDEXECOPT_PROMPTUSER =        1 
 OLECMDEXECOPT_DONTPROMPTUSER =    2 
 
 // DHTMLEDITCMDF 
 DECMDF_NOTSUPPORTED =             0  
 DECMDF_DISABLED =                 1  
 DECMDF_ENABLED =                  3 
 DECMDF_LATCHED =                  7 
 DECMDF_NINCHED =                  11 
 
 // DHTMLEDITAPPEARANCE 
 DEAPPEARANCE_FLAT =               0 
 DEAPPEARANCE_3D =                 1  
 
 // OLE_TRISTATE 
 OLE_TRISTATE_UNCHECKED =          0 
 OLE_TRISTATE_CHECKED =            1 
 OLE_TRISTATE_GRAY =               2 
 var obj_editor = 0 ; 
 // Per canvis de gifs a la barra d'icones: 
 function canvi_imatge(nom_img,graf){ 
 document.images[nom_img].src = "../misc/wysiwyg/"+"images/"+graf; 
 return true; 
 } var colors = new Array(
		'#FFFFFF','#FFCCCC','#FFCC99','#FFFFCC','#99FF99','#CCFFFF','#FFCCFF',
		'#CCCCCC','#FF6666','#FFCC33','#FFFF99','#66FF99','#66FFFF','#FF99FF',
		'#C0C0C0','#FF0000','#FF9900','#FFFF00','#33FF33','#33CCFF','#CC66CC',
		'#999999','#CC0000','#FF6600','#FFCC00','#00CC00','#3366FF','#CC33CC',
		'#666666','#CC0000','#CC6600','#999900','#009900','#3333FF','#993366',
		'#333333','#660000','#993300','#666600','#006600','#000099','#663366',
		'#000000','#330000','#663300','#333300','#003300','#000066','#330033');
	function html2text (html) {
		html = html.replace (/</g,"&lt;");
		html = html.replace (/>/g,"&gt;");
		html = html.replace (/\\/g, "&quot;");
		html = html.replace (/\'/g, "&#039;");
		return html;
	}

	function text2html (text) {
		text = text.replace (/&lt;/g,"<");
		text = text.replace (/&gt;/g,">");
		text = text.replace (/&quot;/g, "\"");
		text = text.replace (/&#039;/g, "'");
		return text;
	}
	
	text_state = "HTML";
	
	function get_text (nom_editor) {
		text = contingut_html (nom_editor);
		if (text_state == "TEXT") text = text2html (text);
        // there is a nonsence text when the field is empty
        if (text == "<P>&nbsp;</P>" || text == "<P> </P>") text = "";
		return text;
	}

	function change_state (nom_editor) {
		text = contingut_html (nom_editor);
	  text = text_state == "HTML" ? html2text (text) : text2html (text);
		posa_contingut_html (nom_editor, text);
		text_state = text_state == "HTML" ? "TEXT" : "HTML";
	};

 	function taula_colors(){ 
 var t=0,taco 
 taco='<center><br><br><table border=1 cellspacing=0 cellpadding=0>'; 
 while(t<49){ 
 if(t%7==0){ 
 if(t!=0){ 
 taco+='</tr>' 
 } 
 taco+='<tr>' 
 } 
 taco+='<td bgcolor="'+colors[t]+'" ><a href=javascript:canvi("'+colors[t]+'"); ><img src="../misc/wysiwyg/images/trans.gif" border=0 width=18 height=18 alt="'+colors[t]+'"></a></td>'; 
 t++ 
 } 
 taco+='</tr></table></center>' 
 return taco 
 } 
 function paleta_colors(ruta_funct){ 
 var pal_col, k, tc 
 pal_col=window.open("","paleta_colors","screenX=80,screenY=80,width=360,height=250") 
 pal_col.document.open() 
 k=pal_col.document; 
 k.writeln("<html><head><style> td,body { font-family:Arial; font-size:8pt; } </style> <script> function canvi(hexa) { "+ruta_funct+"(hexa); window.close(); }</"+"script></head><body bgcolor=white ><center>") 
 k.writeln("<font color=black face=arial size=-1 ><b> Click on a color to choose it:</b></font>") 
 tc=taula_colors() 
 k.writeln(tc) 
 k.writeln("</center></body></html>") 
 k.close() 
 pal_col.focus() 
 } 
 function contingut_html(nom_editor) { 
     var obj_ed = eval(nom_editor); 
     var cont = obj_ed.document.body.innerHTML; // changed
     var texto = "" + cont 
     var complet = eval(nom_editor + '_doc_complet');
     if(!complet) 
    	texto = strip_body(texto); 
     return texto ;
 } 
 function posa_contingut_html(nom_editor,contingut) { 
     var obj_ed = eval(nom_editor);
     obj_ed.document.open();
     obj_ed.document.write(contingut); // changed
     obj_ed.document.close();
     obj_ed.document.designMode = "on";
 } 
 function strip_body(cont) { 
 var  ini_cos = cont.search(/<BODY/i); 
 if( ini_cos == -1 ){ 
 	return cont; 
 } 
 var  lon = cont.length 
 var  fi = false 
 var prob = false 
 var  i = ini_cos + 5 
 while( !fi ){ 
 car = cont.charAt(i); 
 if( car == '>' ){ 
 ini_cos = i + 1  
 fi = true 
 } 
 if( car == '"' || car == "'" ){ 
 fi_com = false 
 i++ 
 if( i >= lon ){ 
 fi = true; 
 prob = true; 
 fi_com = true; 
 } 
 while( !fi_com ){ 
 car_aux = cont.charAt(i); 
 if( car_aux == car ){ 
 fi_com = true 
 } 
 else 
 { 
 i++; 
 } 
 if( i >= lon ){ 
 fi = true; 
 prob = true; 
 fi_com = true; 
 } 
 } 
 } 
 i++; 
 if( i >= lon ){ 
 fi = true; 
 prob = true; 
 } 
 } 
 if( prob == true ){ 
 alert('Due to problems with the HTML code of the page it is not possible to execute this action.'); 
 } 
 else 
 { 
 var fi_cos = cont.search(/<\/BODY/i); 
 var aux = cont.substring(ini_cos,fi_cos)
 cont = aux 
 } 
 return cont;
 }
 function nou_doc(){ 
 if( confirm('If you open a new blank document you will loose the unsaved changes of the current document.\nAre you sure you want to continue?') && confirm('Are you sure you want to open a blank document and loose the unsaved changes of the current document?') ){ 
 obj_editor.NewDocument(); 
 } 
 } 
 function cortar() { 
 obj_editor.execCommand(DECMD_CUT,OLECMDEXECOPT_DODEFAULT); 
 obj_editor.focus(); 
 } 
 function copiar() { 
 obj_editor.execCommand(DECMD_COPY,OLECMDEXECOPT_DODEFAULT); 
 obj_editor.focus(); 
 } 
 function pegar(){ 
 obj_editor.execCommand(DECMD_PASTE,OLECMDEXECOPT_DODEFAULT); 
 obj_editor.focus(); 
 } 
 function desfer() { 
 obj_editor.execCommand(DECMD_UNDO,OLECMDEXECOPT_DODEFAULT); 
 obj_editor.focus(); 
 } 
 function refer() { 
 obj_editor.execCommand(DECMD_REDO,OLECMDEXECOPT_DODEFAULT); 
 obj_editor.focus(); 
 } 
 function cercar() { 
 obj_editor.execCommand(DECMD_FINDTEXT,OLECMDEXECOPT_PROMPTUSER); 
 obj_editor.focus(); 
 } 
 function fer_link() { 
 obj_editor.execCommand(DECMD_HYPERLINK,OLECMDEXECOPT_PROMPTUSER); 
 obj_editor.focus(); 
 } 
 function ins_img() { 
 obj_editor.execCommand(DECMD_IMAGE,OLECMDEXECOPT_PROMPTUSER); 
 obj_editor.focus(); 
 } 
 function mostra_insert_table() { 
 var pVar = document.ObjTableInfo; 
 var NR = pVar.NumRows; 
 var NC = pVar.NumCols; 
 var TA = pVar.TableAttrs; 
 var CA = pVar.CellAttrs; 
 var funct = 'opener.inserta_table' 
 var par_tab, k, tc 
 par_tab=window.open("","param_tables","screenX=80,screenY=80,width=400,height=215") 
 par_tab.document.open() 
 k=par_tab.document 
 k.writeln('<HTML><HEAD><TITLE>Definici� de Taula</TITLE>') 
 k.writeln('<STYLE TYPE="text/css">')  
 k.writeln(" td,body { font-family:Arial; font-size:9pt; font-weight:bold; } ") 
 k.writeln('</STYLE>') 
 k.writeln("<script> function comprova_valors() { ") 
 k.writeln("           var nf, nc, at, ac, tit, nerr=0 , avis") 
 k.writeln("           avis = '\\nTable can�t be created due to:' ") 
 k.writeln("           nf = document.info_table.NumRows.value") 
 k.writeln("           nc = document.info_table.NumCols.value") 
 k.writeln("           at = document.info_table.TableAttrs.value") 
 k.writeln("           ac = document.info_table.CellAttrs.value") 
 k.writeln("           tit = document.info_table.Caption.value") 
 k.writeln("           if( nf != parseInt(nf) || nf < 0 ){ ") 
 k.writeln("               nerr++") 
 k.writeln("               avis += '\\n\\n-The number of rows must be a positive integer.'") 
 k.writeln("           }") 
 k.writeln("           if( nc != parseInt(nc) || nc < 0 ){ ") 
 k.writeln("               nerr++") 
 k.writeln("               avis += '\\n\\n-The number of columns must be a positive integer.'") 
 k.writeln("           }") 
 k.writeln("           if( nerr == 0){ ") 
 k.writeln("               "+funct+"(nf,nc,at,ac,tit) ") 
 k.writeln("               window.close(); ") 
 k.writeln("           }") 
 k.writeln("           else") 
 k.writeln("           {") 
 k.writeln("             alert(avis)") 
 k.writeln("           }") 
 k.writeln("           return true ") 
 k.writeln("         }</"+"script>") 
 k.writeln('</HEAD><BODY bgcolor=white ><center>') 
 k.writeln('<form name=info_table onsubmit="comprova_valors();" >'); 
 k.writeln("<font color=black face=arial size=-1 ><b> Give values for the parameters and press OK:</b></font>") 
 k.writeln('<TABLE CELLSPACING=10><TR><TD valign=absmiddle >Rows:&nbsp;&nbsp;&nbsp;<INPUT TYPE=TEXT SIZE=3  maxlength=2 NAME=NumRows value='+NR+' ></TD>') 
 k.writeln('<TD valign=absmiddle >Columns:&nbsp;&nbsp;&nbsp;<INPUT TYPE=TEXT SIZE=3 maxlength=2 NAME=NumCols value='+NC+'></TD></TR>') 
 k.writeln('<TR><TD>Table attributes:</TD><TD valign=absmiddle ><INPUT TYPE=TEXT SIZE=20 NAME=TableAttrs maxlength=120 value='+TA+'></TD></TR>') 
 k.writeln('<TR><TD>Cell atributes:</TD><TD><INPUT TYPE=TEXT SIZE=20 NAME=CellAttrs value='+CA+'></TD></TR>') 
 k.writeln('<TR><TD>Table title:</TD><TD><INPUT TYPE=TEXT SIZE=20 NAME=Caption ></TD></TR></TABLE>') 
 k.writeln('<TR><TD valign=absmiddle colspan=2 align=center ><INPUT TYPE=BUTTON NAME=OK VALUE=OK onclick="comprova_valors()" ></TD></TR></TABLE></form>') 
 k.writeln('</center></BODY></HTML>') 
 k.close() 
 par_tab.focus() 
 return true 
 } 
 function inserta_table(nf,nc,at,ac,tit) { 
 var pVar = document.ObjTableInfo; 
 pVar.NumRows = nf; 
 pVar.NumCols = nc; 
 pVar.TableAttrs = at; 
 pVar.CellAttrs = ac; 
 obj_editor.execCommand(DECMD_INSERTTABLE,OLECMDEXECOPT_DODEFAULT, pVar); 
 return true; 
 } 
 function insert_fila_table() { 
 obj_editor.execCommand(DECMD_INSERTROW,OLECMDEXECOPT_DODEFAULT); 
 obj_editor.focus(); 
 } 
 function elim_fila_table() { 
 obj_editor.execCommand(DECMD_DELETEROWS,OLECMDEXECOPT_DODEFAULT); 
 obj_editor.focus(); 
 } 
 function insert_col_table() { 
 obj_editor.execCommand(DECMD_INSERTCOL,OLECMDEXECOPT_DODEFAULT); 
 obj_editor.focus(); 
 } 
 function elim_col_table() { 
 obj_editor.execCommand(DECMD_DELETECOLS,OLECMDEXECOPT_DODEFAULT); 
 obj_editor.focus(); 
 } 
 function insert_celda_table() { 
 obj_editor.execCommand(DECMD_INSERTCELL,OLECMDEXECOPT_DODEFAULT); 
 obj_editor.focus(); 
 } 
 function elim_celda_table() { 
 obj_editor.execCommand(DECMD_DELETECELLS,OLECMDEXECOPT_DODEFAULT); 
 obj_editor.focus(); 
 } 
 function combinar_celdas_table() { 
 obj_editor.execCommand(DECMD_MERGECELLS,OLECMDEXECOPT_DODEFAULT); 
 obj_editor.focus(); 
 } 
 function split_celdas_table() { 
 obj_editor.execCommand(DECMD_SPLITCELL,OLECMDEXECOPT_DODEFAULT); 
 obj_editor.focus(); 
 } 
 function FontName_onchange(sel_obj) { 
 var ty = sel_obj.options[sel_obj.selectedIndex].value; 
 if( ty != 0 ){ 
 obj_editor.execCommand(DECMD_SETFONTNAME, OLECMDEXECOPT_DODEFAULT, ty); 
 //obj_editor.SetFocus(); 
 } 
 sel_obj.options[0].selected = true 
 } 
 function FontSize_onchange(sel_obj) { 
 var sz = sel_obj.options[sel_obj.selectedIndex].value; 
 if( sz != 0 ){ 
 obj_editor.execCommand(DECMD_SETFONTSIZE, OLECMDEXECOPT_DODEFAULT, sz); 
 obj_editor.focus(); 
 } 
 sel_obj.options[0].selected = true 
 } 
 function negreta(){ 
 obj_editor.execCommand(DECMD_BOLD,OLECMDEXECOPT_DODEFAULT); 
 obj_editor.focus(); 
 return false; 
 } 
 function cursiva() { 
 obj_editor.execCommand(DECMD_ITALIC,OLECMDEXECOPT_DODEFAULT); 
 //obj_editor.SetFocus(); 
 } 
 function subry() { 
 obj_editor.execCommand(DECMD_UNDERLINE,OLECMDEXECOPT_DODEFAULT); 
 obj_editor.focus(); 
 } 
 function mostra_pal_fg_color(){ 
 paleta_colors('opener.fg_color_posa') 
 return true; 
 } 
 function fg_color_posa(arr) { 
 if (arr != null) { 
 obj_editor.execCommand(DECMD_SETFORECOLOR,OLECMDEXECOPT_DODEFAULT, arr); 
 } 
 } 
 function mostra_pal_bg_color(){ 
 paleta_colors('opener.bg_color_posa') 
 return true; 
 } 
 function bg_color_posa(arr) { 
 if (arr != null) { 
 obj_editor.execCommand(DECMD_SETBACKCOLOR,OLECMDEXECOPT_DODEFAULT, arr); 
 } 
 } 
 function alin_dreta() { 
 obj_editor.execCommand(DECMD_JUSTIFYRIGHT,OLECMDEXECOPT_DODEFAULT); 
 obj_editor.focus(); 
 } 
 function centrat() { 
 obj_editor.execCommand(DECMD_JUSTIFYCENTER,OLECMDEXECOPT_DODEFAULT); 
 obj_editor.focus(); 
 } 
 function alin_esq() { 
 obj_editor.execCommand(DECMD_JUSTIFYLEFT,OLECMDEXECOPT_DODEFAULT); 
 obj_editor.focus(); 
 } 
 function llista_numerada() { 
 obj_editor.execCommand(DECMD_ORDERLIST,OLECMDEXECOPT_DODEFAULT); 
 obj_editor.focus(); 
 } 
 function llista_no_num() { 
 obj_editor.execCommand(DECMD_UNORDERLIST,OLECMDEXECOPT_DODEFAULT); 
 obj_editor.focus(); 
 } 
 function indentat() { 
// obj_editor.execCommand(DECMD_INDENT,OLECMDEXECOPT_DODEFAULT); 
 obj_editor.execCommand(DECMD_INDENT); 
 obj_editor.focus(); 
 } 
 function deindentat() { 
 obj_editor.execCommand(DECMD_OUTDENT,OLECMDEXECOPT_DODEFAULT); 
 obj_editor.focus(); 
 } 