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

require_once $GLOBALS["AA_INC_PATH"]."constants_param_wizard.php3";

function PrintRuleRow($rid, $prop, $col1="", $col2="", $col3="", $col4="") {
  global $sess, $uid;
  echo "<tr class='tabtxt'>
          <td>$prop&nbsp;</td>
          <td>$col1&nbsp;</td>
          <td>$col2&nbsp;</td>
          <td>$col3&nbsp;</td>
          <td>$col4&nbsp;</td>
          <td align='right'><a href=\"javascript:document.location='".
            $sess->url("se_profile.php3?del=$rid&uid=$uid"). "'\">". _m("Delete") ."</a></td>
        </tr>";
}

function PrintRule($rule) {
  global $PROPERTY_TYPES, $SORTORDER_TYPES, $fields, $DEFAULT_VALUE_TYPES;
  $INPUT_DEFAULT_TYPES = getSelectBoxFromParamWizard ($DEFAULT_VALUE_TYPES);

  $prop = $rule['property'];
  $rid  = $rule['id'];

  switch($prop) {
    case 'input_view':
    case 'listlen':
      PrintRuleRow($rid, $PROPERTY_TYPES[$prop], "", $rule['value']);
      break;
    case 'bookmark':
      PrintRuleRow($rid, $PROPERTY_TYPES[$prop], $rule['selector']);
      break;
    case 'admin_order':
      $fid = substr( $rule['value'], 0, -1 );
      $ord = substr( $rule['value'], -1 );
      PrintRuleRow($rid, $PROPERTY_TYPES[$prop], $fields[$fid]['name'], $SORTORDER_TYPES[$ord]);
      break;
    case 'admin_search':
      $pos = strpos($rule['value'],':');
      $fid = substr($rule['value'], 0, $pos);
      $val = substr($rule['value'], $pos+1);
      PrintRuleRow($rid, $PROPERTY_TYPES[$prop], $fields[$fid]['name'], $val);
      break;
    case 'hide':
      PrintRuleRow($rid, $PROPERTY_TYPES[$prop], $fields[$rule['selector']]['name']);
      break;
    case 'fill':
    case 'hide&fill':
    case 'predefine':
      $fnc = ParseFnc(substr($rule['value'],2));  # all default should have fnc:param format
      PrintRuleRow($rid, $PROPERTY_TYPES[$prop], $fields[$rule['selector']]['name'],
                   $INPUT_DEFAULT_TYPES[$fnc['fnc']], $fnc['param'],
                   ($rule['value'][0] == '1')? 'HTML' : "" );
      break;
  }
}

function PrintSetRule($n, $rule, $sfld, $func, $sparam, $shtml, $desc) {
  global $PROPERTY_TYPES, $lookup_fields, $SORTORDER_TYPES, $fields;
  echo "<tr class=tabtxt>
         <td>". $PROPERTY_TYPES[$rule]. "<input type=hidden name=prop$n value=$rule></td>
         <td>";
  if($sfld)
    FrmSelectEasy("fld$n", $lookup_fields, "");
   else
    echo "&nbsp;";
  echo " </td>
         <td>";
  if($func)
    FrmSelectEasy("fnc$n", $func, "");
   else
    echo "&nbsp;";
  echo " </td>
         <td>". ($sparam ? "<input type=text name=param$n size=20>" : "&nbsp;"). "</td>
         <td>". ($shtml  ? "<input type=checkbox name=html$n>" : "&nbsp;"). "</td>
         <td><a href=\"javascript:addrule($n)\">". _m("Add") ."</a></td></tr>";
}

?>