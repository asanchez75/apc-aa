<?php
/**
 * Alerts - Collection Forms
*  This is the "Form Wizard" page from Alerts Admin menu
*
* @package Alerts
* @version $Id$
* @author Jakub Ad�mek <jakubadamek@ecn.cz>, Econnect, December 2002
* @copyright Copyright (C) 1999-2002 Association for Progressive Communications 
*/
/* 
Copyright (C) 1999-2002 Association for Progressive Communications 
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

$directory_depth = "../";
require "../../include/init_page.php3";
require $GLOBALS[AA_INC_PATH]."formutil.php3";
require $MODULES[$g_modules[$slice_id]['type']]['menu'];   

HtmlPageBegin();   // Print HTML start page tags (html begin, encoding, style sheet, but no title)
echo "<TITLE>". _m("Collection Form Wizard") ."</TITLE>
</HEAD>";

set_collectionid();

showMenu ($aamenus, "admin", "formwizard");

if (!is_object ($db)) $db = new DB_AA;
if ($formlang) 
    bind_mgettext_domain ($GLOBALS[AA_INC_PATH]."lang/".$formlang."_alerts_lang.inc");

// after bind_mgettext_domain!
require "cf_common.php3";
    
// create the Choose Filters code
$db->query ("
    SELECT DF.* FROM alerts_collection_filter CF INNER JOIN
    alerts_filter DF on CF.filterid = DF.id
    WHERE CF.collectionid = $collectionid
    ORDER BY CF.myindex");
while ($db->next_record()) 
    $filters .= "\n    <INPUT TYPE=\"checkbox\" NAME=\"alerts[filters][".$db->f("id")."]\" CHECKED> "
        .$db->f("description")."<BR>";
        
$cf_fields["filters"] = array ("code"=>$filters, "label"=>_m("Choose Filters"));        

if (get_howoften_options ($collectionprop["fix_howoften"]))
    unset ($cf_fields["howoften"]);

$db->query ("SELECT AC.id, name, slice_url FROM alerts_collection AC 
    INNER JOIN module ON AC.moduleid = module.id
    WHERE AC.id=$collectionid");
if (!$db->next_record()) { echo "Error: collection id $collectionid not found."; exit; }
$cf_url = $db->f("slice_url");

echo "
<table width='440' border='0' cellspacing='0' cellpadding='10' bgcolor=".COLOR_TABBG." align='center'>
<TR><TD class=tabtxt>";
echo "<h1>"._m("Collection Form Wizard for")." ".$db->f("name")."</h1>";
echo "<FORM name='collection_form_wizard' METHOD=post ACTION='cf_wizard.php3#formcode'>";
echo _m("Form language").": ".$cf_fields["lang"]["code"]."<br>";
echo _m("After you have created the form, jump to it: ")."<a target=_blank href='$cf_url'>$cf_url</a>\n";
echo "<BR><BR>
    <INPUT TYPE=hidden NAME='collectionid' VALUE=$collectionid>
    <INPUT TYPE=hidden NAME='".$sess->name."' VALUE='".$sess->id."'>";

$formaction = $AA_INSTAL_PATH."post2shtml.php3?shtml_page=".$cf_url;    
        
echo _m("Choose form fields. The required ones are marked by asterix *:")."
<BR>
<TABLE border=0>\n";
//<TR><TD class=tabtxt><B>"._m("show")."</B></TD><TD class=tabtxt colspan=2><b>"._m("example")."</b></TD></TR>";
    
$formcode = "<FORM name=\"cf$collectionid\" action=\"$formaction\" method=\"post\" 
    onsubmit=\"return validate();\">
<INPUT type=\"hidden\" name=\"alerts[lang]\" value=\"".get_mgettext_lang()."\">
<INPUT type=\"hidden\" name=\"alerts[collectionid]\" value=\"$collectionid\">
<INPUT type=\"hidden\" name=\"alerts[userid]\">
<INPUT type=\"hidden\" name=\"alerts[run_filler]\" value=\"1\">
<INPUT type=\"hidden\" name=\"alerts[choose_filters]\" value=\""
    .($show["filters"] || !$showme ? "checkbox" : "no")."\">
<TABLE border=\"0\">\n";
$rowspan = 1;
reset ($cf_fields);
while (list ($fname, $fprop) = each ($cf_fields)) {
    if ($fprop["hidewizard"])
        continue;
    echo "<TR>";

    if (is_array ($show))
        $showme = $fprop["checkbox"] ? $show[$fprop["checkbox"]] : $show[$fname];
    else $showme = true;

    $rowspan --;
    if ($rowspan == 0) {
        $rowspan = $fprop["rowspan"];
        echo "<TD rowspan=$rowspan>";
        if ($fprop["required"]) {
            echo "*";
            $showme = true;
        }
        else FrmChBoxEasy ("show[$fname]", $showme);
        echo "</TD>\n";
    }
    $row = "  <TD><B>$fprop[label]</B></TD>\n";
    echo $row;
    
    if ($showme)
        $formcode .= "<TR>\n".$row
                  ."  <TD>$fprop[code]</TD></TR>\n";
}
$formcode .= "
<TR><TD colspan=2><INPUT type=\"submit\" value=\""._m("OK")."\"></TD></TR>
</TABLE></FORM>
<!--#include virtual=\"".$AA_INSTAL_PATH."modules/alerts/cf_filler.php3?cid=$collectionid\"-->
<!--#include virtual=\"".$AA_INSTAL_PATH."modules/alerts/cf_fillform.php3?cid=$collectionid\"-->
";
    
echo "</TABLE><BR><BR>";
echo "<INPUT TYPE=submit NAME=showme VALUE='"._m("Reload form code")."'>";
echo "</FORM>";    

echo "<A id='formcode'>
<TEXTAREA cols=80 rows=20>".HTMLEntities($formcode)."</TEXTAREA>\n";
    
echo "</TD></TR></TABLE>";
HTMLPageEnd();
page_close();
?>

