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

/*
	Author: Jakub Ad�mek
    
    Params: $list = name of an array defined in constants_param_wizard.php3
                    (or anywhere else, but must be included into this script)
            $item = name of the first index in the $$list array
	
	This program should become a parameter wizard:
	useful when choosing a field layout (text area, text field, ...)
	or a function (f_m etc.)
	
	It shows some help to the chosen function / layout and to all parametres.
	It is capable of showing a table of examples as well.
	
	It's an independent tool which can be used to produce another wizards. 
	You just need to define the appropriate array structure in constants_param_wizard.php3.
	See se_inputform and the JavaScript callParamWizard function for an example how to call the wizard.
*/	

require_once "../include/init_page.php3";
bind_mgettext_domain ($GLOBALS["AA_INC_PATH"]."lang/".get_mgettext_lang()."_param_wizard_lang.php3");
require_once $GLOBALS["AA_INC_PATH"]."constants_param_wizard.php3";

HtmlPageBegin();   // Print HTML start page tags (html begin, encoding, style sheet, but no title)

function firstBig ($s)
{
	return strtoupper($s[0]).substr($s,1);
}

// the HTML tags to be printed verbatim are back-slashed in the text
// e.g. <A ....> is written \<A ...\> 
 
function processSlashes ($s)
{
	$s = str_replace ("\\<", HTMLSpecialChars("<"), $s);
	$s = str_replace ("\\>", HTMLSpecialChars(">"), $s);
	return $s;
}

function processValue ($s)
{
    $s = str_replace ("\r", "", $s);
    $s = str_replace ("\n", "", $s);
    $s = str_replace ('"', '&quot;', $s);
    return $s;
}

function processJavaScript ($s)
{
    $s = str_replace ("\r", "", $s);
    $s = str_replace ("\n", "", $s);
    $s = str_replace ('"', '&quot;', $s);
    $s = str_replace ("'", "\\'", $s);
    return $s;
}

$desc = $$list;
$desc = $desc[items][$item];
$title = firstbig($desc[name])." "._m("Wizard");

// by some item (I know only about the date input type) the parameters are divided with ' rather than :
// so I allow to use ' when all params are INT or BOOL

$allow_quote = 1;
if (is_array($desc[params])) {
	reset($desc[params]);
	while (list(,$param)=each($desc[params])) {
		if ($param[type] != 'INT' && $param[type] != 'BOOL') {
			$allow_quote = 3;
			break;
		}
	}
}

?>
<title><?php echo $title ?></title>
</head> 

<body onload=readParams()>
<center>

<?php # ------- Caption ----------- ?>
<table border="0" cellspacing="0" cellpadding="1" width="95%" bgcolor="<?php echo COLOR_TABTITBG ?>">
   <TR><TD align=center class=tablename width="100%"><?php echo $title ?></TD></TR>
</table>

<form name="f" method=post onSubmit="self.close()" ?>
<?php 

?>

<SCRIPT Language="JavaScript"><!--
  function changeFunction (combo) {
  	page = "<?php echo $sess->url(self_base()."param_wizard.php3")?>"
		+ "<?php echo "&list=$list"."&combo_list='$combo_list'&text_param='$text_param'"?>"
		+ "&item=" + combo.options [combo.selectedIndex].value;
  	document.location = page;
  }
  function writeParams () {
	params = new String("");
	for (i=0; i < <?php echo count($desc[params]) ?>; ++i) {
		if (i > 0) params += ":";
		val = document.f.elements["param"+i].value;
		params += val.replace(/:/g,"#:");
	}	
  	window.opener.document.f.elements["<?php echo $text_param ?>"].value = params;
  }
  function fillParams (params) {
	params = params.replace(/#:/g,"#~") + ":";
	<?php if ($allow_quote) echo "params = params.replace(/'/g,\":\");" ?>
	for (i=0; i < <?php echo count($desc[params])?>; ++i) {
		if (params > "") {
			str = params.substr(0,params.search(":"));
			params = params.substr (params.search(":")+1);
		}
		else str = "";
		document.f.elements["param"+i].value = str.replace(/#~/g,":");
	}
  }
  function readParams () {
  	fillParams ( window.opener.document.f.elements["<?php echo $text_param ?>"].value );
  }  
  function useExample(iExample) {
    switch(iExample) {
	<?php 
		for ($i = 0; $i < count($desc[examples]); ++$i) {
			$exm = $desc[examples][$i];
  			echo "case $i: pars=\"".$exm[params]."\"; break;";
		}
	?>
	}
	fillParams(pars);
  }
// -->
</SCRIPT>

<table align=left width="95%" border="0" cellspacing="0" cellpadding="1" bgcolor="<?php echo COLOR_TABTITBG ?>" align="center">
<tr><td class=tabtit align=left>
<?php 
	if ($desc[name])
		echo $desc[name].": ".processSlashes($desc[desc]); 
	else {
   	$what = $$list;
	 	printf(_m("This is an undocumented %s. We don't recommend to use it."),$what[name]);
		echo "<p align=center><input type=submit value=\""._m("Close the wizard")."\">";
		echo "</td></tr></table></body></html>";
		exit;
	}

echo '
</td></tr>
<tr><td>
<table width="100%" border="0" cellspacing="0" cellpadding="4" bgcolor="'.COLOR_TABBG.'">
<tr><td class=tabtxt>';

	// show the parameter boxes with hints

	if (is_array($desc[params])):
		echo _m("Available parameters: ")."<br><br>";
		echo "<table align=left width=\"100%\" border=0 cellspacing=0 cellpadding = 2>";
		reset($desc[params]);
		$iparam = 0;
		$example = "";
		while (list(,$param)=each($desc[params])) {
			echo "<tr><td align=left size=\"50%\" class=tabtxt valign=top>"
			."<b>".strtolower("$param[name]:")."</b></td>
			<td align=left class = tabtxt>
			<INPUT TYPE=TEXT NAME=param$iparam VALUE=\""
                .processValue($param[example])."\">
        	<span class=tabhlp>";
			switch($param[type]) {
			case "INT":  echo " ("._m("integer&nbsp;number").")"; break;
			case "STR":  echo " ("._m("any&nbsp;text").")"; break;
			case "STRID":echo " ("._m("field&nbsp;id").")"; break;
			case "BOOL": echo " ("._m("boolean:&nbsp;0=false,1=true").")"; break;
			}
			echo "<br>".processSlashes($param[desc])."</span>
			</td></tr>";
			if ($iparam > 0) $example .= ":";
			$example .= $param[example];
			++$iparam;
		}
		echo "</table>";

	else:
		echo "<table width = \"100%\" border=0 cellspacing=0 cellpadding = 2><tr><td class=tabtxt>";
		$what = $$list;
		printf (_m("This %s has no parameters.")."<br>", strtolower($what[name]));
        echo "</td></tr></table>";
	endif;

echo "
</td></tr></table>
</td></tr>
<tr><td class=tabtit height=30>";

if (is_array ($desc[examples])) {
	echo _m("Have a look at these examples of parameters sets:");
	echo "<table width = \"100%\" border=1 cellspacing=1 cellpadding = 2>";
	for ($i = 0; $i < count($desc[examples]); ++$i) {
		$exm = $desc[examples][$i];
		echo "<tr><td class = tabtit>";
		echo $exm[desc];
		echo "</td><td class = tabtit>";
		echo "<a href=\"javascript:useExample($i)\">"._m("Show")."</a></td></tr>";
	}
	echo "</table>";
}
	
$what = $$list; 
echo $what[hint];
echo '
<p align=center>';
if (is_array($desc[params])) echo '
    <input type=button value="'._m("OK - Save").'" onclick="writeParams(); self.close()">&nbsp;&nbsp;
    <input type=button value="'._m("Cancel").'" onclick="self.close()">&nbsp;&nbsp;
    <input type=button value="'._m("Show example params").'" onclick="fillParams(\''.processJavaScript($example).'\')">';
else echo '
    <input type=button value="'._m("OK").'" onclick="self.close()">';
echo '    
</p>
</td></tr></table>
</form>
</body>
</html>';
?>