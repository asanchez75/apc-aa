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

// Miscellaneous utility functions for "site" module

define ('MODW_FLAG_DISABLE',   1);   // deactivated spot
define ('MODW_FLAG_JUST_TEXT', 2);   // not used - planed for site speedup
                                     // (= flag means "don't stringexpand text)"
define ('MODW_FLAG_COLLAPSE',  4);   // (visualy) Collapsed spot


function SiteAdminPage($spot_id, $add = null) {
    global $sess;
    $url = con_url($_SERVER['PHP_SELF'],"spot_id=$spot_id");   // Always used this way
    if ($add) {
        $url = con_url($url, $add);
    }
    // Don't add AA_CP_Session if already there (it isn't in PHP_SELF)
    return ((strpos($url, 'AA_CP_Session') === false) ? $sess->url($url) : $url);
}

function ModW_HiddenRSpotId() {
    print('<input type="hidden" name="spot_id" value="'.$GLOBALS['r_spot_id'].'">');
}

function ModW_StoreTree(&$tree, $site_id) {
    $p_site_id = q_pack_id($site_id);
    $data      = addslashes(serialize($tree));
    $SQL       = "UPDATE site SET structure='$data' WHERE id='$p_site_id'";
    tryQuery($SQL);
}

function ModW_GetTree( &$tree, $site_id ){
    $db        = getDB();
    $p_site_id = q_pack_id($site_id);

    $SQL = "SELECT structure FROM site WHERE id='$p_site_id'";
    $db->query();
    if ($db->next_record()) {
        $tree = unserialize( $db->f('structure') );
    }
    freeDB($db);
}

// This function does nothing and is used when walking the tree fixing it
function ModW_DoNothing($spot_id,$depth) {
    return;
}

function ModW_SpotHtml($spot_id, $spot_name, $selected, $disabled) {
    // get class for this spot div
    $class  = ($selected) ? ' class="selected"' : '';

    // get actions (eye for disable/enable, at this moment
    $action = $disabled ?
        '<a href="'. SiteAdminPage($spot_id, 'akce=e'). '">' .GetModuleImage('site', 'disabled.gif', '', 16, 12, 'class="eye"') .'</a>' :
        '<a href="'. SiteAdminPage($spot_id, 'akce=h'). '">' .GetModuleImage('site', 'enabled.gif',  '', 16, 12, 'class="eye"') .'</a>';

    // print the spot <div>
    echo "<div$class>${action}<a href=\"". SiteAdminPage($spot_id, "go_sid=$spot_id"). "\" class=\"spot\">$spot_name</a></div>";
}

function ModW_PrintSpotName_Start($spot_id, $depth) {
    global $r_spot_id, $tree;

    // print the spot itself
    ModW_SpotHtml($spot_id, $tree->getName($spot_id), $spot_id == $r_spot_id, ($tree->get('flag', $spot_id) & MODW_FLAG_DISABLE));

    // variables defined? - print it
    if (!$tree->isFlag($spot_id, MODW_FLAG_COLLAPSE) AND ($vars = $tree->get('variables', $spot_id))) {
        echo "\n  <div class=\"variables\">(". implode($vars, ',') .')</div>';
    }
}

function ModW_PrintChoice_Start($spot_id, $depth, $choices_index, $choices_count) {
    global $tree;
    // begin of the choice (open <div>)

    // last choice in the list is special
    $last  = ($choices_index == $choices_count-1);    // just shortcut variable
    $class = $last ? 'lastchoice' : 'choice';

    if ($tree->isLeaf($spot_id)) {
        $colaps = GetModuleImage('site', $last ? 'l.gif' : 't.gif', '', 21, 13);
    } elseif ($tree->get('flag', $spot_id) & MODW_FLAG_COLLAPSE) {
        $colaps = '<a href="'. SiteAdminPage($spot_id, "akce=m").'">'. GetModuleImage('site', 'plus.gif', '', 21, 13) .'</a> ';
    } else {
        $colaps = '<a href="'. SiteAdminPage($spot_id, "akce=p").'">'. GetModuleImage('site', 'minus.gif', '', 21, 13) .'</a> ';
    }
    echo "\n<div class=\"$class\">$colaps";

    // print conditions
    $conditions = '';
    if ($conds = $tree->get('conditions', $spot_id)) {
        $delim = ' ';
        foreach ($conds as $k => $v) {
            $conditions .= "$delim$k=$v";
            $delim = ', ';
        }
    }
    if ($conditions == '') {
        $conditions = '&nbsp;';
    }
    echo "\n  <div class=\"conditions\"><a href=\"". SiteAdminPage($spot_id, "go_sid=$spot_id'"). "\">$conditions</a></div>";
}

function ModW_PrintChoice_End($spot_id, $depth, $choices_index, $choices_count) {
    // close previously opened <div class="choice">
    echo "\n</div>";
}

function ModW_PrintVariables( $spot_id, $vars ) {
    global $sess;
    echo "<tr><td valign=top><b>"._m("Spot&nbsp;variables")."</b></td><td>";
    if (isset($vars) AND is_array($vars)) {
        foreach ($vars as $k => $v) {
            echo "$v <span align=right><a href=\"". SiteAdminPage($spot_id, "delvar=". urlencode($k)) ."\">"._m("Delete")."</a></span><br>";
        }
    }
    echo "<form name=fvar action=\"".$_SERVER['PHP_SELF']."\"><input type='text' name='addvar' value='' size='20' maxlength='50'><span align=right><a href='javascript:document.fvar.submit()'>"._m("Add")."</a></span>";
    ModW_HiddenRSpotId();
    $sess->hidden_session();
    echo "</form></td></tr>";
}

function ModW_PrintConditions($spot_id, $conds, $vars) {
    global $sess;
    echo "<tr><td valign=top><b>"._m("Spot&nbsp;conditions")."</b></td><td>";
    if ( isset($vars) AND is_array($vars) ) {
        $i=0;
        foreach ($vars as $k => $v) {
            if ($conds[$v]) {
                echo "$v = $conds[$v] <span align=right><a href=\"". SiteAdminPage($spot_id, "delcond=$v") ."\">"._m("Delete")."</a></span><br>";
            } else {
                echo "<form name=fcond$i action=\"". $_SERVER['PHP_SELF'] ."\">$k = <input type='text' name='addcond' value='' size='20' maxlength='50'>
                     <input type='hidden' name='addcondvar' value='$v'>
                     <span align=right><a href='javascript:document.fcond$i.submit()'>"._m("Add")."</a></span>";
                $sess->hidden_session();
                ModW_HiddenRSpotId();
                echo "</form>";
            }
            $i++;
        }
    }
    echo "</td></tr>";
}

function ModW_ShowSpot(&$tree, $site_id, $spot_id) {
    global $sess;

    $db  = getDB();
    $SQL = "SELECT * FROM site_spot WHERE site_id = '". q_pack_id($site_id). "' AND spot_id = '$spot_id'";
    $db->query($SQL);
    $content = safe($db->next_record() ? $db->f('content') : "");
    freeDB($db);
    
    echo '<table align=left border=0 cellspacing=0 width="100%" class=tabtxt>';
    ModW_PrintVariables($spot_id, $tree->get('variables',$spot_id));
    if (($vars=$tree->isOption($spot_id))) {
        ModW_PrintConditions($spot_id, $tree->get('conditions',$spot_id), $vars);
    }

    echo "<form method='post' name=fs action=\"". $_SERVER['PHP_SELF'] ."\">";
    ModW_HiddenRSpotId();
    FrmInputText('name', _m("Spot name"), $tree->get('name', $spot_id), 50, 50, true, false, false, false);
    echo "<tr><td align=center colspan=2><textarea name='content' rows=20 cols=80>$content</textarea><br><br>
          <input type=submit name='". _m("Submit") ."'>";
    $sess->hidden_session();
    echo "</td></tr>
    </form>
    </table>";
}
?>
