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

/*  A simple calculator (expression parser).

    (c) by Stanislav K�hnl, October 2002
    modified and moved to this file by Jakub Ad�mek
*/

 // TRY IT
/*
$exps = array ("", "4/0", "2", "2*3", "(2*3)+6", "2*(3+6)", "2*3+6", "2^2^3", "((2+(2*3))+4)*10", "4-5", "5--2--3+7");
reset ($exps);
while (list (,$exp) = each ($exps))
    echo "$exp = ".calculate ($exp)."<br>\n";
*/
    
// I replace binary minus by tilde (~) to recognize it from unary minus
function bminus() { return "~"; }

// returns result for an expression consisting of numbers, brackets (), operators
// +,-,*,/,^ and spaces
function calculate ($exp)
{
    if ($GLOBALS[$debug]) huhl("calculate:$exp");
    $exp = str_replace (" ","",$exp);
    $exp = str_replace ("\t","",$exp);

    // find binary minuses, i.e. preceded by number or not succeeded by number
    for ($i = 1; $i < strlen ($exp); $i ++) {
        if ($exp[$i] == "-"
            && (is_digit ($exp[$i-1] || !is_digit ($exp[$i+1]))))
            $exp[$i] = bminus();
    }
    
    while (strstr($exp,"("))
        $exp = calculate_brackets ($exp);
//	echo $exp;
    return calculate_without_brackets ($exp);
}


// return expression with resolved one of most inner brackets
function calculate_brackets ($expr) {
    if ($GLOBALS[$debug]) huhl("calculate_brackets:$expr");
    $beg = strrpos ($expr, "(");
    $expr_beg = substr ($expr, 0, $beg);
    $expr_mid = substr ($expr, $beg+1);
    $end = strpos($expr_mid, ")");
    $expr_end = substr ($expr_mid, $end+1);
    $expr_mid = substr ($expr_mid, 0, $end);
//    echo $expr_beg.calculate_without_brackets ($expr_mid).$expr_end."<br>";
//echo $expr_mid."<br><br>";
    return $expr_beg.calculate_without_brackets ($expr_mid).$expr_end;
}

// calculate expression consisting of numbers, operators +,~,*,/,^, but no brackets ()
function calculate_without_brackets ($expr) {
    if ($GLOBALS[$debug]) huhl("calculate_without_brackets:$expr");
    return calculate_operator ($expr, "+");
}

// recursively resolve operators in priority order
function calculate_operator ($expr, $operator)
{
    if ($GLOBALS[$debug]) huhl("calculate_operator:$expr:$operator");
    $next_operator = array ("+" => bminus(), bminus() => "*", "*" => "/", "/" => "^");

    $parts = explode ($operator, $expr);
    for ($i = count ($parts)-1; $i >= 0; $i --) {
        $part = $parts[$i];
        if ($next_operator [$operator])
            $part = calculate_operator ($part, $next_operator [$operator]);
        if (!isset ($result))
             $result = $part;
        else switch ($operator) {
            case "+": $result = $part + $result; break;
            case bminus(): $result = $part - $result; break;
            case "/" :
              if ($result == 0) return "Error: Division by zero";
                 else $result = $part / $result; break;
            case "*": $result = $part * $result; break;
            case "^": $result = pow ($part+0, $result+0); break;
            default: echo "calculate_operator: Internal Error: unrecognized operator $operator."; exit;
        }
    }
    return $result;
}

function is_digit ($c)
{ return $c >= "0" && $c <= "9"; }

?>
