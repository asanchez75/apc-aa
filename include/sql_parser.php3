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

# -----------------------------------------------------------------------------
#  Constants
# -----------------------------------------------------------------------------

$syntax_error = "";

# -------------- Lexical analysis ---------------------------------------------
define("WHITE", " \t");
define("LEFT_PARENTHESES", "({[");
define("RIGHT_PARENTHESES", ")]}");
define("OPERATOR", "+-");
define("SPECIAL", "+-\"' \t(){}[]");
define("QUOT", "\"");
define("APOS", "'");

// return status codes
define("E_NO_ENDING_QUOTAPOS", 16);
define("S_OK", 17);
define("S_IMPLICIT", 18);

# -------------- Syntax analysis ---------------------------------------------

// token types - number representation
define("TOKEN_TYPE_UNKNOWN", 0);
define("TOKEN_TYPE_STRING", 1);
define("TOKEN_TYPE_OPERATOR_AND", 2);
define("TOKEN_TYPE_OPERATOR_OR", 3);
define("TOKEN_TYPE_OPERATOR_NOT", 4);
define("TOKEN_TYPE_LEFT_PARENTHESIS", 5);
define("TOKEN_TYPE_RIGHT_PARENTHESIS", 6);
define("TOKEN_TYPE_EMPTY_TOKEN", 7);
define("TOKEN_TYPE_EOF", 8);

// token types - text representation
define("SYNTAX_LEFT_PAR", "(");
define("SYNTAX_RIGHT_PAR", ")");
define("SYNTAX_AND", "and");
define("SYNTAX_OR", "or");
define("SYNTAX_NOT", "not");
define("SYNTAX_STRING", "string");
define("SYNTAX_EOF", "EOF");

// translates number representation token types to textual reprasentation
$readable = Array(TOKEN_TYPE_LEFT_PARENTHESIS  => SYNTAX_LEFT_PAR,
				  TOKEN_TYPE_RIGHT_PARENTHESIS => SYNTAX_RIGHT_PAR,
				  TOKEN_TYPE_OPERATOR_AND	   => SYNTAX_AND,
				  TOKEN_TYPE_OPERATOR_OR	   => SYNTAX_OR,
				  TOKEN_TYPE_OPERATOR_NOT	   => SYNTAX_NOT,
				  TOKEN_TYPE_STRING			   => SYNTAX_STRING,
				  TOKEN_TYPE_EOF			   => SYNTAX_EOF
				 );
				 
// operator LIKE, RLIKE a LLIKE
$rl = Array(
		"operator" => Array( "LIKE" => "LIKE", "RLIKE" => "LIKE", "LLIKE" => "LIKE", "XLIKE" => "LIKE", "=" => "="),
		"pre"  => Array( "LIKE" => "%", "LLIKE" => "%", "RLIKE" => "",  "XLIKE" => "",  "=" => ""),
		"post" => Array( "LIKE" => "%", "LLIKE" => "",  "RLIKE" => "%", "XLIKE" => "",  "=" => "")
	  );


$N = Array("S", "E", "Eap", "T", "Tap", "F", "G");
$T = Array("string", "and", "or", "not", "(", ")");
$P = Array(
		"S"   => Array(
					Array("E", "lambda")
				 ),
		"E"   => Array(
					Array("T", "Eap", "lambda")
				 ),
		"Eap" => Array(
					Array("or", "T", "Eap", "lambda"),
				 	Array("lambda")
				 ),
		"T"   => Array(
					Array("F", "Tap", "lambda")
				 ),
		"Tap" => Array(
					Array("and", "F", "Tap", "lambda"), 
	             	Array("lambda")
				 ),
		"F"	  => Array(
					Array("not", "G", "lambda"),
					Array("G", "lambda")
				 ),
		"G"   => Array(
					Array("(", "E", ")", "lambda"),
				 	Array("string", "lambda")
				 )
	);

$First = Array(
			"string" => Array("string"),
			"and"    => Array("and"),
			"or"	 => Array("or"),
			"not"	 => Array("not"),
			"("		 => Array("("),
			")"		 => Array(")"),
			"S"		 => Array("string", "(", "not"),
			"E"		 => Array("string", "(", "not"),
			"Eap"	 => Array("lambda", "or"),
			"T"		 => Array("string", "(", "not"),
			"Tap"	 => Array("lambda", "and"),
			"F"		 => Array("string", "(", "not"),
			"G"		 => Array("string", "(")
		 );
		 

# -----------------------------------------------------------------------------
#  Functions for Lexical analisys
# -----------------------------------------------------------------------------
     
function eatWhiteSpaces($input, $i, $length) {
	while ( ($i<$length) && isWhite($input[$i]) ) $i++;
	return $i;
}

function isSpecial($c) {
	# if StrPos returns 0, so it is false or zero?
	$s = SPECIAL;
	if ($s[0]==$c) return TRUE;
	else return StrPos(SPECIAL, $c);
}

// functions isOperator a resolveOperator works together
function isOperator($c) {
	$o = OPERATOR;
	if ($o[0]==$c) return TRUE;
	else return StrPos(OPERATOR, $c);
}

function resolveOperator($op) {
	if ( $op == "+" ) return Array("type"=>TOKEN_TYPE_OPERATOR_AND, "value"=>"and");
	else if ( $op == "-" ) return Array("type"=>TOKEN_TYPE_OPERATOR_NOT, "value"=>"not");
	else return Array("value"=>TOKEN_TYPE_UNKNOWN, "value"=>"divny operator");
}

function isWhite($c) {
	$w = WHITE;
	if ($w[0]==$c) return TRUE;
	else return StrPos(WHITE, $c);
}

function isLeftParenthesis($c) {
	$p = LEFT_PARENTHESES;
	if ($p[0]==$c) return TRUE;
	else return StrPos(LEFT_PARENTHESES, $c);
}

function isRightParenthesis($c) {
	$p = RIGHT_PARENTHESES;
	if ($p[0]==$c) return TRUE;
	else return StrPos(RIGHT_PARENTHESES, $c);
}

// there if $input[$i-1]==QUOT (or APOS) on begin
// $ending should be QUOT or APOS
function tillTheEndingQuotApos($input, $i, $length, $ending) {
	$tok="";
	while ($input[$i] != $ending) {
		$tok = $tok . $input[$i];
		$i++;
		if ($i >= $length) return Array("status"=>E_NO_ENDING_QUOTAPOS, "value"=>"", "i"=>$i);
	}
	return Array("status"=>S_OK, "value"=>$tok, "i"=>$i+1);	// i+1  =>  skips string terminator
}

function tillTheFirstSpecial($input, $i, $length) {
	$tok="";
	while ( ($i<$length) && !isSpecial($input[$i]) ) {
		$tok = $tok . $input[$i];
		$i++;
	}
	return Array("status"=>S_OK, "value"=>$tok, "i"=>$i);
}

function getToken($input, $i, $length) {
	$i = eatWhiteSpaces($input, $i, $length);
	if ($i >= $length) {
		// no more tokens
		return Array("status"=>S_NO_MORE_TOKENS, "value"=>"", "i"=>$i, "type"=>TOKEN_TYPE_EMPTY_TOKEN);
	}
	if (($input[$i]==QUOT) || ($input[$i]==APOS)) {
		$tok = tillTheEndingQuotApos($input, $i+1, $length, $input[$i]);
		if ($tok["status"]==S_OK) $tok["type"]=TOKEN_TYPE_STRING;
		else $tok["type"]=TOKEN_TYPE_UNKNOWN;	// error handling
	}
	else if ( isSpecial($input[$i]) ) {
		if ( isOperator($input[$i]) ) { // includes '+' and '-' (one character operators)
			$tok["status"] = S_OK;
			$val = resolveOperator($input[$i]);
			$tok["value"]  = $val["value"];	// 'and' (for +) and 'not' (for -)
			$tok["i"]      = $i+1;
			$tok["type"]   = $val["type"];
		}
		else if ( isLeftParenthesis($input[$i]) ) { // includes '(', '[' a '{'
			$tok["status"] = S_OK;
			$tok["value"]  = "(";	// all parenthesis are equal
			$tok["i"]	   = $i+1;
			$tok["type"]   = TOKEN_TYPE_LEFT_PARENTHESIS;
		}
		else if ( isRightParenthesis($input[$i]) ) { // includes ')', ']' a '}'
			$tok["status"] = S_OK;
			$tok["value"]  = ")";	// all parenthesis are equal
			$tok["i"]	   = $i+1;
			$tok["type"]   = TOKEN_TYPE_RIGHT_PARENTHESIS;
		}
		$i++;
	}
	else {
		$tok = tillTheFirstSpecial($input, $i, $length);
		$tok["type"] = TOKEN_TYPE_UNKNOWN;
	}
	return $tok;
}

function preProcess($toks) {
	Reset($toks);
	while ( List($ind,$val) = Each($toks) ) {
	
		// UNKNOWN will be set for strings not delimeted by apostrofs,
		// we can't say: "it is string" (see "and", ...)
		if ( $val["type"] == TOKEN_TYPE_UNKNOWN ) {
			$lower = StrToLower($val["value"]);
			// Strings AND, OR, NOT in meaning as strings must be quoted ( "and" )
			if ( $lower=="and" ) {
				$val["value"] = "and";
				$val["type"]  = TOKEN_TYPE_OPERATOR_AND;
			}
			else if ( $lower=="or" ) {
				$val["value"] = "or";
				$val["type"]  = TOKEN_TYPE_OPERATOR_OR;
			}
			else if ( $lower=="not" ) {
				$val["value"] = "not";
				$val["type"]  = TOKEN_TYPE_OPERATOR_NOT;
			}
			else $val["type"] = TOKEN_TYPE_STRING;
		}
//		else if ( $val["type"] == TOKEN_TYPE_OPERATOR ) {
//			if ( $val["value"] == "+" ) $val["value"] = "and";
//			else if ( $val["value"] == "-" ) $val["value"] = "not";
//		}
		$newtoks[] = $val;		
	}
	Reset($newtoks);
	while ( $t1 = Current($newtoks) ) {
		$newtoks2[] = $t1;
		$t2 = Next($newtoks);
		if ($t1["type"]==TOKEN_TYPE_STRING) {
			if ( ($t2) && (($t2["type"]==TOKEN_TYPE_STRING) || ($t2["type"]==TOKEN_TYPE_LEFT_PARENTHESIS) || ($t2["type"]==TOKEN_TYPE_OPERATOR_NOT)))
				$newtoks2[] = Array("status"=>S_IMPLICIT, "value"=>"and", "i"=>$t1["i"], "type"=>TOKEN_TYPE_OPERATOR_AND);
		}
	}
	$newtoks2[] = Array("status"=>S_IMPLICIT, "value"=>"end of input", "i"=>65536, "type"=>TOKEN_TYPE_EOF);
	return $newtoks2;
}

function lex($input) {
	$length = StrLen($input);
	$i = 0;
	do {
		$r = getToken($input, $i, $length);
		if ($r["status"] == S_OK) $res[] = $r;
		$i = $r["i"];
	} while ($r["type"] != TOKEN_TYPE_EMPTY_TOKEN);
	$res = preProcess($res);
	return $res;
}


# -----------------------------------------------------------------------------
#  Functions for Syntax analisys
# -----------------------------------------------------------------------------

class TokenList {
	var $tList;
	var $index;
	
	function TokenList($t) {
		$this->tList = $t;
		$this->index = 0;
	}
	
	function lookAhead() {
		return $this->tList[$this->index]["type"];
	}
	
	function match($symbol) {
		global $readable;
		if ( ($la=$this->lookAhead()) == $symbol ) 
      $this->index++;
		else // error - match something, what is not in order
			return "Syntax error at position ".$this->getTokenBegin().". Found ".B($readable[$la])." while expecting ".B($readable[$symbol]).".<BR>\n";
	}
	
	function getStringValue() {
		if ( $this->tList[$this->index]["type"] == TOKEN_TYPE_STRING )
			return $this->tList[$this->index]["value"];
		else return "";
	}
	
	function getTokenBegin() {
		$t = $this->tList[$this->index];
		return $t["i"] - strlen($t["value"]);
	}
}	// class TokenList


/*
 * Class for syntax analysis contains variableu tList, which is list of token, 
 * which comes from lexical analysis.
 */
class Syntax {
	var $tList;
	var $column;
	var $operator;
	var $pre;
	var $post;

	function Syntax($column, $operator, $t) {
		global $rl;
		$operator = StrToUpper($operator);
		$this->column = $column;
		$this->operator = $rl["operator"][$operator];
		if ($this->operator == "") $operator = $this->operator = "LIKE";	// unknown operator
		$this->tList = new TokenList($t);
		$this->pre  = $rl["pre"][$operator];
		$this->post = $rl["post"][$operator];	
	}

/*
 *	Gramar rules. 
 *  If there are more rules for one noterminal, we should chose the right one. 
 *  It is suitable to look for 1 token - gramar is LL(1).
 */
	function S() {
		global $readable;
		$la = $this->tList->lookAhead();
		if ( ($la == TOKEN_TYPE_LEFT_PARENTHESIS) ||
			 ($la == TOKEN_TYPE_STRING) ||
			 ($la == TOKEN_TYPE_OPERATOR_NOT) )		// S -> E
		{
//			echo("S->E<BR>\n");//debug
			if( ($val = $this->E()) == "_SYNTAX_ERROR" )
        return $val;
      if( ($err = $this->tList->match(TOKEN_TYPE_EOF) )) {
        $GLOBALS["syntax_error"]=$err;
        return "_SYNTAX_ERROR";
      }  
//			echo("<B>Input string is OK.</B><BR>\n");
		}
		else {
      $GLOBALS["syntax_error"]="Syntax error at position ".$this->tList->getTokenBegin().". Found ".B($readable[$la])." while expecting ".B(SYNTAX_LEFT_PAR).", ".B(SYNTAX_STRING)." or ".B(SYNTAX_NOT).".<BR>\n";
      return "_SYNTAX_ERROR";
  	}
		return $val;
	}

	function E() {
		global $readable;
		$la = $this->tList->lookAhead();
		if ( ($la == TOKEN_TYPE_LEFT_PARENTHESIS) ||
			 ($la == TOKEN_TYPE_STRING) ||
			 ($la == TOKEN_TYPE_OPERATOR_NOT) )		// E -> TE'
		{
//			echo("E->TEap<BR>\n");//debug
			if( ($val = $this->T()) == "_SYNTAX_ERROR" )
        return $val;
			if( ($foo = $this->Eap()) == "_SYNTAX_ERROR" )
        return $foo;
			$val = $val . $foo;
		}
		else {
      $GLOBALS["syntax_error"]="Syntax error at position ".$this->tList->getTokenBegin().". Found ".B($readable[$la])." while expecting ".B(SYNTAX_LEFT_PAR).", ".B(SYNTAX_STRING)." or ".B(SYNTAX_NOT).".<BR>\n";
      return "_SYNTAX_ERROR";
		}
		return $val;
	}
	
	function Eap() {
		global $readable;
		$la = $this->tList->lookAhead();
		if ( $la == TOKEN_TYPE_OPERATOR_OR ) {	// E' -> orTE'
//			echo("Eap->orTEap<BR>\n");//debug
			if( ($err = $this->tList->match(TOKEN_TYPE_OPERATOR_OR) )) {
        $GLOBALS["syntax_error"]=$err;
        return "_SYNTAX_ERROR";
      }  
			if( ($val = $this->T()) == "_SYNTAX_ERROR" )
        return $val;
			if( ($foo = $this->Eap()) == "_SYNTAX_ERROR" )
        return $foo;
			$val = " or " . $val . $foo;
		}
		else if ( ($la == TOKEN_TYPE_RIGHT_PARENTHESIS) || ($la == TOKEN_TYPE_EOF) ) { // E' -> lambda
//			echo("Eap-><BR>\n");//debug
			$val = "";
		}
		else {
      $GLOBALS["syntax_error"]="Syntax error at position ".$this->tList->getTokenBegin().". Found ".B($readable[$la])." while expecting ".B(SYNTAX_OR).", ".B(SYNTAX_LEFT_PAR)." or ".B(SYNTAX_EOF).".<BR>\n";
      return "_SYNTAX_ERROR";
		}
		return $val;
	}
	
	function T() {
		global $readable;
		$la = $this->tList->lookAhead();
		if ( ($la == TOKEN_TYPE_LEFT_PARENTHESIS) ||
			 ($la == TOKEN_TYPE_STRING) ||
			 ($la == TOKEN_TYPE_OPERATOR_NOT) ) 		// T -> FT'
		{
//			echo("T->FTap<BR>\n");//debug
			if( ($val = $this->F()) == "_SYNTAX_ERROR" )
        return $val;
			if( ($foo = $this->Tap()) == "_SYNTAX_ERROR" )
        return $foo;
			$val = $val . $foo;
		}
		else {
      $GLOBALS["syntax_error"]="Syntax error at position ".$this->tList->getTokenBegin().". Found ".B($readable[$la])." while expecting ".B(SYNTAX_LEFT_PAR).", ".B(SYNTAX_STRING)." or ".B(SYNTAX_NOT).".<BR>\n";
      return "_SYNTAX_ERROR";
		}
		return $val;
	}
	
	function Tap() {
		global $readable;
		$la = $this->tList->lookAhead();
		if ( $la == TOKEN_TYPE_OPERATOR_AND ) {	// T' -> andFT'
//			echo("Tap->andFTap<BR>\n");//debug
			if( ($err = $this->tList->match(TOKEN_TYPE_OPERATOR_AND) )) {
        $GLOBALS["syntax_error"]=$err;
        return "_SYNTAX_ERROR";
      }  
			if( ($val = $this->F()) == "_SYNTAX_ERROR" )
        return $val;
			if( ($foo = $this->Tap()) == "_SYNTAX_ERROR" )
        return $foo;
			$val = " and " . $val . $foo;
		}
		else if ( ($la == TOKEN_TYPE_OPERATOR_OR) ||
				  ($la == TOKEN_TYPE_RIGHT_PARENTHESIS) ||
				  ($la == TOKEN_TYPE_EOF) ) {				// T' -> lambda
//			echo("Tap-><BR>\n");//debug
			$val = "";
		}
		else {
      $GLOBALS["syntax_error"]="Syntax error at position ".$this->tList->getTokenBegin().". Found ".B($readable[$la])." while expecting ".B(SYNTAX_AND).", ".B(SYNTAX_OR).", ".B(SYNTAX_RIGHT_PAR)." or ".SYNTAX_EOF.".<BR>\n";
      return "_SYNTAX_ERROR";
		}
		return $val;
	}
	
	function F() {
		global $readable;
		$la = $this->tList->lookAhead();
		if ( $la == TOKEN_TYPE_OPERATOR_NOT ) {	// F -> not G
//			echo("F->notG<BR>\n");//debug
			if( ($err = $this->tList->match(TOKEN_TYPE_OPERATOR_NOT) )) {
        $GLOBALS["syntax_error"]=$err;
        return "_SYNTAX_ERROR";
      }  
			if( ($foo = $this->G()) == "_SYNTAX_ERROR" )
        return $foo;
			$val = "not ($foo)";
		}
		else if ( ($la == TOKEN_TYPE_LEFT_PARENTHESIS) || ($la == TOKEN_TYPE_STRING) ) { // F -> G
//			echo("F->G<BR>\n");//debug
			if( ($val = $this->G()) == "_SYNTAX_ERROR" )
        return $val;
		}
		else {
      $GLOBALS["syntax_error"]="Syntax error at position ".$this->tList->getTokenBegin().". Found ".B($readable[$la])." while expecting ".B(SYNTAX_LEFT_PAR).", ".B(SYNTAX_STRING)." or ".B(SYNTAX_NOT).".<BR>\n";
      return "_SYNTAX_ERROR";
		}
		return $val;
	}
	
	function G() {
		global $readable;
		$la = $this->tList->lookAhead();
		if ( $la == TOKEN_TYPE_LEFT_PARENTHESIS ) { // G -> (E)
//			echo("G->(E)<BR>\n");//debug
			if( ($err = $this->tList->match(TOKEN_TYPE_LEFT_PARENTHESIS) )) {
        $GLOBALS["syntax_error"]=$err;
        return "_SYNTAX_ERROR";
      }  
			$val = SYNTAX_LEFT_PAR;
			if( ($foo = $this->E()) == "_SYNTAX_ERROR" )
        return $foo;
			$val = $val . $foo;
			if( ($err = $this->tList->match(TOKEN_TYPE_RIGHT_PARENTHESIS) )) {
        $GLOBALS["syntax_error"]=$err;
        return "_SYNTAX_ERROR";
      }  
			$val = $val . SYNTAX_RIGHT_PAR;
		}
		else if ( $la == TOKEN_TYPE_STRING ) {	// G -> string
//			echo("G->string<BR>\n");//debug
			$val = $this->column . " " . $this->operator . " \"" . $this->pre . $this->tList->getStringValue() . $this->post . "\"";
			if( ($err = $this->tList->match(TOKEN_TYPE_STRING) )) {
        $GLOBALS["syntax_error"]=$err;
        return "_SYNTAX_ERROR";
      }  
		}
		else {
      $GLOBALS["syntax_error"]="Syntax error at position ".$this->tList->getTokenBegin().". Found ".B($readable[$la])." while expecting ".B(SYNTAX_LEFT_PAR)." or ".B(SYNTAX_STRING).".<BR>\n";
      return "_SYNTAX_ERROR";
		}
		return $val;
	}
} // class Syntax



function B($text) {
	return "<B>".$text."</B>";
}
?>