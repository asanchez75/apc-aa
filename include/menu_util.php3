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

/* Author: Jakub Adamek 

   This script shows the top menu (navigation bar) and second level menu (left bar)
   by the function showMenu ($aamenus, $activeMain, $activeSubmenu = "", $showMain = 1, $showSub = 1).
*/

if (!defined ("AA_MENU_INCLUDED"))
    define("AA_MENU_INCLUDED","1");
else return;

// use instead of </body></html> on pages which show menu
function HtmlPageEnd() {
  echo "
    </TD></TR></TABLE>
    </TD></TR></TABLE>
    </BODY></HTML>";
}

// ----------------------------------------------------------------------------------------
/* creates a JavaScript variable modulesOptions, which allows to create another Module selectbox
    without reprinting all the options */

function PrintModuleSelection() {
  global $slice_id, $g_modules, $sess, $PHP_SELF;

  if( is_array($g_modules) AND (count($g_modules) > 1) ) {
  
    // create the modulesOptions content:
    echo "<SCRIPT language=JAVASCRIPT>\n
        <!--\n modulesOptions = ''\n";
    reset($g_modules);
    while(list($k, $v) = each($g_modules)) { 
      echo "\t+'<option value=\"". htmlspecialchars($k)."\"";
      if ( ($slice_id AND (string)$slice_id == (string)$k)) 
        echo " selected";
      echo ">". str_replace("'","`",safe($v['name'])) . "'\n";
    }
    if( !$slice_id )   // new slice
      echo "\t+'<option value=\"new\" selected>". L_NEW_SLICE_HEAD + "'";
    echo ";\n //-->\n </SCRIPT>";

    // print the select box
    echo "
          <span class=nbdisable> &nbsp;". L_SWITCH_TO ."&nbsp; </span>
          <SCRIPT language=javascript><!--\n
                document.writeln('<select name=slice_id onChange=\'document.location=\"" .con_url($sess->url($PHP_SELF),"change_id=")."\"+this.options[this.selectedIndex].value\'>');\n
                document.writeln(modulesOptions);\n
                document.writeln('</select>');\n
          //-->\n
          </SCRIPT>\n";
  } else
    echo "&nbsp;"; 
}  

// ----------------------------------------------------------------------------------------
//                                SHOW MENU

/* PARAMS: $smmenus -- array with menu information, see menu.php3 for an example
           $activeMain -- selected item in main (top) menu
           $activeSubmenu -- selected item in sub (left) menu
           $showMain -- show the main menu (top navigation bar) ?
           $showSub -- show the submenu (left navigation bar) ?
*/             
function showMenu ($smmenus, $activeMain, $activeSubmenu = "", $showMain = 1, $showSub = 1)
{
    global $slice_id, $AA_INSTAL_PATH, $r_slice_headline, $useOnLoad, $sess;
    global $debug;
    
    // load the main AA menu (see menu.php3)
    if ($smmenus == "aamenus")
        $smmenus = get_aamenus();

    // HACKISH: aaadmin menu needs always the _news_ lang file, even in other than slice modules
    if ($activeMain == "aaadmin") {
        if (substr (LANG_FILE, 4, 4) != "news") {
            $lang = substr (LANG_FILE, 0, 2);
            require $GLOBALS[AA_INC_PATH].$lang."_news_lang.php3";
        }
    }
         
    if( $useOnLoad )
        echo '<body OnLoad="InitPage()" background="'. COLOR_BACKGROUND .'">';
    else
        echo '<body background="'. COLOR_BACKGROUND .'">';

    if ($debug) { echo "<p><font color=purple>showMenu:activeMain=$activeMain;activeSubmenu=$activeSubmenu;showMain=$showMain;showSub=$showSub:</font></p>";  }
   
    if( !$slice_id )
        $r_slice_headline = L_NEW_SLICE_HEAD;

    $nb_logo = '<a href="'. $AA_INSTAL_PATH .'"><img src="'.$AA_INSTAL_PATH.'images/action.gif" width="106" height="73" border="0" alt="'. L_LOGO .'"></a>';

    echo "<TABLE border=0 cellspacing=0 cellpadding=0><TR>";

    if ($showMain) {
        echo "
        <TD colspan=2>
        <TABLE border=0 cellpadding=0 cellspacing=0 width=800>
            <TR><TD><IMG src=\"$AA_INSTAL_PATH"."images/spacer.gif\" width=122 height=1></TD>
                <TD><IMG src=\"$AA_INSTAL_PATH"."images/spacer.gif\" width=300 height=1></TD>
                <TD><IMG src=\"$AA_INSTAL_PATH"."images/spacer.gif\" width=267 height=1></TD>
            </TR>
            <form name=nbform enctype=\"multipart/form-data\" method=post
                action=\"". $sess->url($PHP_SELF) ."\">            
            <TR><TD rowspan=2 align=center class=nblogo>$nb_logo</td>
                <TD height=43 colspan=2 align=center valign=middle class=slicehead>
                    ".$smmenus[$activeMain]["title"]."  -  $r_slice_headline</TD>
            </TR>
            </form>
            <TR><td align=center class=navbar>";
                            
        $first = true;
        reset ($smmenus);
        while (list ($aamenu,$aamenuprop) = each ($smmenus)) {
            if ($aamenuprop["level"] == "main") {
                if ($first) $first = false;
                else echo " | ";
                if (!isset ($aamenuprop["cond"])) $aamenuprop["cond"] = 1;
                if ($aamenu == $activeMain)
                    echo "<span class=nbactive>$aamenuprop[label]</span>";
                else if ($slice_id && $aamenuprop["cond"]) {
                     $href = $aamenuprop["exact_href"];
                     if (!$href) $href = get_aa_url($aamenuprop["href"]);                    
                     echo "<a href=\"$href\">"
                         ."<span class=nbenable>$aamenuprop[label]</span></a>";
                }
                else echo "<span class=nbdisable>$aamenuprop[label]</span>";
            }
        }
        
        echo "</td><TD class=navbar>";
        PrintModuleSelection();
        echo "</TD></TR></TABLE>
        </TD></TR><TR>";
    }
    
    if ($showSub) {
        $submenu = $smmenus[$activeMain]["submenu"];
        if ($submenu) {
            echo "<TD valign=top>";
            showSubmenu ($smmenus[$submenu], $activeSubmenu);
            echo "</TD>";
        }
    }
    echo "
        <TD align=left valign=top>
        <TABLE border=0 cellspacing=0 cellpadding=10><TR><TD align=left>";
}   

// ----------------------------------------------------------------------------------------
//                                SHOW SUBMENU

function showSubmenu (&$aamenu, $active)
{
    global $AA_INSTAL_PATH, $slice_id,$debug;
    if ($debug) { echo "<p><font color=purple>showSubmenu:active=$active</font></p>"; }
    echo '<table width="122" border="0" cellspacing="0" bgcolor="'.COLOR_TABBG.'" cellpadding="1" align="LEFT" class="leftmenu">';

    $aamenuitems = $aamenu["items"];
    reset ($aamenuitems);
    while (list ($itemshow, $item) = each ($aamenuitems)) {
        if (substr($itemshow,0,6) == "header") 
            echo '<tr><td>&nbsp;</td></tr>
      <tr><td><img src="'.$AA_INSTAL_PATH.'images/black.gif" width=120 height=1></td></tr>
                  <tr><td class=leftmenu>'.$item.'</td></tr>
                  <tr><td><img src="'.$AA_INSTAL_PATH.'images/black.gif" width=120 height=1></td></tr>';
        else if (substr($itemshow,0,4) == "line")
            echo '<tr><td><img src="'.$AA_INSTAL_PATH.'images/black.gif" width=120 height=1></td></tr>';
        else {
            echo '<tr><td width="122" valign="TOP">&nbsp;&nbsp;';
            if (!isset ($item["cond"])) $item["cond"] = 1;
            if ($itemshow == $active)
                echo "<span class=leftmenua>".$item["label"]."</span>";
            else if (($slice_id || $item["show_always"]) && $item["cond"]) {
                echo '<a href="';
                if ($item["exact_href"]) echo $item["exact_href"]; 
                else echo get_aa_url($item["href"]);
                echo '" class=leftmenuy>'.$item["label"].'</a>';
            }  
            else echo "<span class=leftmenun>".$item["label"]."</span>";
            echo "</td></tr>";
        }
    }
  
    echo '<tr><td class=leftmenu>&nbsp;</td></tr>
          <tr><td class=leftmenu height="'.$aamenu["bottom_td"].'">&nbsp;</td></tr>
          <tr><td class=copymsg ><small>'. L_COPYRIGHT .'</small></td></tr>
          </table>';
}
