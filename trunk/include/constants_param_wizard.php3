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

/* The arrays INPUT_TYPES and FIELD_FUNCTIONS and all constants used in them
	were generated from another array structure by a program - see param_wizard_generate.php3
*/

/* These arrays serve to the parameter wizard. You can describe each item and its parameters.
	You can write some more described examples as well.
	
You can use HTML tags in the description text. If you want < or > to be printed verbatim, 
use the escape character \ - the wizard will translate the characters. Remember you have to write
\\ in the PHP strings (e.g. "... writes some text like \\<a href=...\\>").

	Each array has this structure:
		"name"=>describes the items contained in the array (used on various places in the wizard)
		"hint"=>a common text displayed at the bottom of the wizard
		"items"=>array of items
	Each item has this structure:
		"name"=>a brief name
		"desc"=>a thoroughfull description
		"params"=>array of parameters
		"examples"=>array of examples
	Each param has this structure:
		"name"=>a brief name
		"desc"=>a thoroughfull description
		"type"=>see the param wizard constants for a description of available types
		"example"=>an example value
	Each example has this structure:
		"desc"=>a thoroughfull description
		"params"=>the params in the internal format (divided by :)
*/		
	

$INPUT_TYPES = array ("name"=>L_PARAM_WIZARD_INPUT_NAME,
"items"=>array(
"txt"=>array("name"=>L_PARAM_WIZARD_INPUT_txt_NAME,
	"desc"=>L_PARAM_WIZARD_INPUT_txt_DESC,
	"params"=>array(
		array("name"=>L_PARAM_WIZARD_INPUT_txt_PAR0_NAME,
		"desc"=>L_PARAM_WIZARD_INPUT_txt_PAR0_DESC,
		"type"=>"INT",
		"example"=>L_PARAM_WIZARD_INPUT_txt_PAR0_EXAMPLE))),
"fld"=>array("name"=>L_PARAM_WIZARD_INPUT_fld_NAME,
	"desc"=>L_PARAM_WIZARD_INPUT_fld_DESC,
	"params"=>array(
		array("name"=>L_PARAM_WIZARD_INPUT_fld_PAR0_NAME,
		"desc"=>L_PARAM_WIZARD_INPUT_fld_PAR0_DESC,
		"type"=>"INT",
		"example"=>L_PARAM_WIZARD_INPUT_fld_PAR0_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_INPUT_fld_PAR1_NAME,
		"desc"=>L_PARAM_WIZARD_INPUT_fld_PAR1_DESC,
		"type"=>"INT",
		"example"=>L_PARAM_WIZARD_INPUT_fld_PAR1_EXAMPLE))),
"sel"=>array("name"=>L_PARAM_WIZARD_INPUT_sel_NAME,
	"desc"=>L_PARAM_WIZARD_INPUT_sel_DESC),
"pre"=>array("name"=>L_PARAM_WIZARD_INPUT_pre_NAME,
	"desc"=>L_PARAM_WIZARD_INPUT_pre_DESC,
	"params"=>array(
		array("name"=>L_PARAM_WIZARD_INPUT_pre_PAR0_NAME,
		"desc"=>L_PARAM_WIZARD_INPUT_pre_PAR0_DESC,
		"type"=>"INT",
		"example"=>L_PARAM_WIZARD_INPUT_pre_PAR0_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_INPUT_pre_PAR1_NAME,
		"desc"=>L_PARAM_WIZARD_INPUT_pre_PAR1_DESC,
		"type"=>"INT",
		"example"=>L_PARAM_WIZARD_INPUT_pre_PAR1_EXAMPLE))),
"rio"=>array("name"=>L_PARAM_WIZARD_INPUT_rio_NAME,
	"desc"=>L_PARAM_WIZARD_INPUT_rio_DESC),
"dte"=>array("name"=>L_PARAM_WIZARD_INPUT_dte_NAME,
	"desc"=>L_PARAM_WIZARD_INPUT_dte_DESC,
	"params"=>array(
		array("name"=>L_PARAM_WIZARD_INPUT_dte_PAR0_NAME,
		"desc"=>L_PARAM_WIZARD_INPUT_dte_PAR0_DESC,
		"type"=>"INT",
		"example"=>L_PARAM_WIZARD_INPUT_dte_PAR0_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_INPUT_dte_PAR1_NAME,
		"desc"=>L_PARAM_WIZARD_INPUT_dte_PAR1_DESC,
		"type"=>"INT",
		"example"=>L_PARAM_WIZARD_INPUT_dte_PAR1_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_INPUT_dte_PAR2_NAME,
		"desc"=>L_PARAM_WIZARD_INPUT_dte_PAR2_DESC,
		"type"=>"BOOL",
		"example"=>L_PARAM_WIZARD_INPUT_dte_PAR2_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_INPUT_dte_PAR3_NAME,
		"desc"=>L_PARAM_WIZARD_INPUT_dte_PAR3_DESC,
		"type"=>"BOOL",
		"example"=>L_PARAM_WIZARD_INPUT_dte_PAR3_EXAMPLE))),
"chb"=>array("name"=>L_PARAM_WIZARD_INPUT_chb_NAME,
	"desc"=>L_PARAM_WIZARD_INPUT_chb_DESC),
"mch"=>array("name"=>L_PARAM_WIZARD_INPUT_mch_NAME,
	"desc"=>L_PARAM_WIZARD_INPUT_mch_DESC),
"mse"=>array("name"=>L_PARAM_WIZARD_INPUT_mse_NAME,
	"desc"=>L_PARAM_WIZARD_INPUT_mse_DESC,
	"params"=>array(
		array("name"=>L_PARAM_WIZARD_INPUT_mse_PAR0_NAME,
		"desc"=>L_PARAM_WIZARD_INPUT_mse_PAR0_DESC,
		"type"=>"INT",
		"example"=>L_PARAM_WIZARD_INPUT_mse_PAR0_EXAMPLE))),
"fil"=>array("name"=>L_PARAM_WIZARD_INPUT_fil_NAME,
	"desc"=>L_PARAM_WIZARD_INPUT_fil_DESC,
	"params"=>array(
		array("name"=>L_PARAM_WIZARD_INPUT_fil_PAR0_NAME,
		"desc"=>L_PARAM_WIZARD_INPUT_fil_PAR0_DESC,
		"type"=>"STR",
		"example"=>L_PARAM_WIZARD_INPUT_fil_PAR0_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_INPUT_fil_PAR1_NAME,
		"desc"=>L_PARAM_WIZARD_INPUT_fil_PAR1_DESC,
		"type"=>"STR",
		"example"=>L_PARAM_WIZARD_INPUT_fil_PAR1_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_INPUT_fil_PAR2_NAME,
		"desc"=>L_PARAM_WIZARD_INPUT_fil_PAR2_DESC,
		"type"=>"STR",
		"example"=>L_PARAM_WIZARD_INPUT_fil_PAR2_EXAMPLE))),
"iso"=>array("name"=>L_PARAM_WIZARD_INPUT_iso_NAME,
	"desc"=>L_PARAM_WIZARD_INPUT_iso_DESC,
	"params"=>array(
		array("name"=>L_PARAM_WIZARD_INPUT_iso_PAR0_NAME,
		"desc"=>L_PARAM_WIZARD_INPUT_iso_PAR0_DESC,
		"type"=>"INT",
		"example"=>L_PARAM_WIZARD_INPUT_iso_PAR0_EXAMPLE))),
"nul"=>array("name"=>L_PARAM_WIZARD_INPUT_nul_NAME,
	"desc"=>L_PARAM_WIZARD_INPUT_nul_DESC)));

$FIELD_FUNCTIONS = array ("name"=>L_PARAM_WIZARD_FUNC_NAME,
"hint"=>L_PARAM_WIZARD_FUNC_HINT,
"items"=>array(
"f_0"=>array("name"=>L_PARAM_WIZARD_FUNC_F_0_NAME,
	"desc"=>L_PARAM_WIZARD_FUNC_F_0_DESC),
"f_h"=>array("name"=>L_PARAM_WIZARD_FUNC_F_H_NAME,
	"desc"=>L_PARAM_WIZARD_FUNC_F_H_DESC,
	"params"=>array(
		array("name"=>L_PARAM_WIZARD_FUNC_F_H_PAR0_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_H_PAR0_DESC,
		"type"=>"STR",
		"example"=>L_PARAM_WIZARD_FUNC_F_H_PAR0_EXAMPLE))),
"f_d"=>array("name"=>L_PARAM_WIZARD_FUNC_F_D_NAME,
	"desc"=>L_PARAM_WIZARD_FUNC_F_D_DESC,
	"params"=>array(
		array("name"=>L_PARAM_WIZARD_FUNC_F_D_PAR0_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_D_PAR0_DESC,
		"type"=>"STR",
		"example"=>L_PARAM_WIZARD_FUNC_F_D_PAR0_EXAMPLE))),
"f_i"=>array("name"=>L_PARAM_WIZARD_FUNC_F_I_NAME,
	"desc"=>L_PARAM_WIZARD_FUNC_F_I_DESC),
"f_n"=>array("name"=>L_PARAM_WIZARD_FUNC_F_N_NAME,
	"desc"=>L_PARAM_WIZARD_FUNC_F_N_DESC),
"f_g"=>array("name"=>L_PARAM_WIZARD_FUNC_F_G_NAME,
	"desc"=>L_PARAM_WIZARD_FUNC_F_G_DESC),
"f_w"=>array("name"=>L_PARAM_WIZARD_FUNC_F_W_NAME,
	"desc"=>L_PARAM_WIZARD_FUNC_F_W_DESC),
"f_a"=>array("name"=>L_PARAM_WIZARD_FUNC_F_A_NAME,
	"desc"=>L_PARAM_WIZARD_FUNC_F_A_DESC,
	"params"=>array(
		array("name"=>L_PARAM_WIZARD_FUNC_F_A_PAR0_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_A_PAR0_DESC,
		"type"=>"INT",
		"example"=>L_PARAM_WIZARD_FUNC_F_A_PAR0_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_FUNC_F_A_PAR1_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_A_PAR1_DESC,
		"type"=>"STRID",
		"example"=>L_PARAM_WIZARD_FUNC_F_A_PAR1_EXAMPLE))),
"f_f"=>array("name"=>L_PARAM_WIZARD_FUNC_F_F_NAME,
	"desc"=>L_PARAM_WIZARD_FUNC_F_F_DESC,
	"params"=>array(
		array("name"=>L_PARAM_WIZARD_FUNC_F_F_PAR0_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_F_PAR0_DESC,
		"type"=>"STRID",
		"example"=>L_PARAM_WIZARD_FUNC_F_F_PAR0_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_FUNC_F_F_PAR1_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_F_PAR1_DESC,
		"type"=>"STR",
		"example"=>L_PARAM_WIZARD_FUNC_F_F_PAR1_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_FUNC_F_F_PAR2_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_F_PAR2_DESC,
		"type"=>"BOOL",
		"example"=>L_PARAM_WIZARD_FUNC_F_F_PAR2_EXAMPLE))),
"f_b"=>array("name"=>L_PARAM_WIZARD_FUNC_F_B_NAME,
	"desc"=>L_PARAM_WIZARD_FUNC_F_B_DESC,
	"params"=>array(
		array("name"=>L_PARAM_WIZARD_FUNC_F_B_PAR0_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_B_PAR0_DESC,
		"type"=>"STRID",
		"example"=>L_PARAM_WIZARD_FUNC_F_B_PAR0_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_FUNC_F_B_PAR1_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_B_PAR1_DESC,
		"type"=>"STRID",
		"example"=>L_PARAM_WIZARD_FUNC_F_B_PAR1_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_FUNC_F_B_PAR2_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_B_PAR2_DESC,
		"type"=>"STR",
		"example"=>L_PARAM_WIZARD_FUNC_F_B_PAR2_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_FUNC_F_B_PAR3_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_B_PAR3_DESC,
		"type"=>"STR",
		"example"=>L_PARAM_WIZARD_FUNC_F_B_PAR3_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_FUNC_F_B_PAR4_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_B_PAR4_DESC,
		"type"=>"STRID",
		"example"=>L_PARAM_WIZARD_FUNC_F_B_PAR4_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_FUNC_F_B_PAR5_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_B_PAR5_DESC,
		"type"=>"STR",
		"example"=>L_PARAM_WIZARD_FUNC_F_B_PAR5_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_FUNC_F_B_PAR6_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_B_PAR6_DESC,
		"type"=>"BOOL",
		"example"=>L_PARAM_WIZARD_FUNC_F_B_PAR6_EXAMPLE))),
"f_t"=>array("name"=>L_PARAM_WIZARD_FUNC_F_T_NAME,
	"desc"=>L_PARAM_WIZARD_FUNC_F_T_DESC),
"f_s"=>array("name"=>L_PARAM_WIZARD_FUNC_F_S_NAME,
	"desc"=>L_PARAM_WIZARD_FUNC_F_S_DESC,
	"params"=>array(
		array("name"=>L_PARAM_WIZARD_FUNC_F_S_PAR0_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_S_PAR0_DESC,
		"type"=>"STR",
		"example"=>L_PARAM_WIZARD_FUNC_F_S_PAR0_EXAMPLE))),
"f_l"=>array("name"=>L_PARAM_WIZARD_FUNC_F_L_NAME,
	"desc"=>L_PARAM_WIZARD_FUNC_F_L_DESC,
	"params"=>array(
		array("name"=>L_PARAM_WIZARD_FUNC_F_L_PAR0_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_L_PAR0_DESC,
		"type"=>"STRID",
		"example"=>L_PARAM_WIZARD_FUNC_F_L_PAR0_EXAMPLE))),
"f_e"=>array("name"=>L_PARAM_WIZARD_FUNC_F_E_NAME,
	"desc"=>L_PARAM_WIZARD_FUNC_F_E_DESC),
"f_c"=>array("name"=>L_PARAM_WIZARD_FUNC_F_C_NAME,
	"desc"=>L_PARAM_WIZARD_FUNC_F_C_DESC,
	"params"=>array(
		array("name"=>L_PARAM_WIZARD_FUNC_F_C_PAR0_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_C_PAR0_DESC,
		"type"=>"STR",
		"example"=>L_PARAM_WIZARD_FUNC_F_C_PAR0_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_FUNC_F_C_PAR1_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_C_PAR1_DESC,
		"type"=>"STR",
		"example"=>L_PARAM_WIZARD_FUNC_F_C_PAR1_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_FUNC_F_C_PAR2_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_C_PAR2_DESC,
		"type"=>"STR",
		"example"=>L_PARAM_WIZARD_FUNC_F_C_PAR2_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_FUNC_F_C_PAR3_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_C_PAR3_DESC,
		"type"=>"STR",
		"example"=>L_PARAM_WIZARD_FUNC_F_C_PAR3_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_FUNC_F_C_PAR4_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_C_PAR4_DESC,
		"type"=>"STRID",
		"example"=>L_PARAM_WIZARD_FUNC_F_C_PAR4_EXAMPLE)),
	"examples"=>array(
		array("desc"=>L_PARAM_WIZARD_FUNC_F_C_EXAMPLE0_DESC,
		"params"=>L_PARAM_WIZARD_FUNC_F_C_EXAMPLE0_PARAMS),
		array("desc"=>L_PARAM_WIZARD_FUNC_F_C_EXAMPLE1_DESC,
		"params"=>L_PARAM_WIZARD_FUNC_F_C_EXAMPLE1_PARAMS),
		array("desc"=>L_PARAM_WIZARD_FUNC_F_C_EXAMPLE2_DESC,
		"params"=>L_PARAM_WIZARD_FUNC_F_C_EXAMPLE2_PARAMS))),
"f_u"=>array("name"=>L_PARAM_WIZARD_FUNC_F_U_NAME,
	"desc"=>L_PARAM_WIZARD_FUNC_F_U_DESC,
	"params"=>array(
		array("name"=>L_PARAM_WIZARD_FUNC_F_U_PAR0_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_U_PAR0_DESC,
		"type"=>"STR",
		"example"=>L_PARAM_WIZARD_FUNC_F_U_PAR0_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_FUNC_F_U_PAR1_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_U_PAR1_DESC,
		"type"=>"STR",
		"example"=>L_PARAM_WIZARD_FUNC_F_U_PAR1_EXAMPLE))),
"f_r"=>array("name"=>L_PARAM_WIZARD_FUNC_F_R_NAME,
	"desc"=>L_PARAM_WIZARD_FUNC_F_R_DESC),
"f_v"=>array("name"=>L_PARAM_WIZARD_FUNC_F_V_NAME,
	"desc"=>L_PARAM_WIZARD_FUNC_F_V_DESC,
	"params"=>array(
		array("name"=>L_PARAM_WIZARD_FUNC_F_V_PAR0_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_V_PAR0_DESC,
		"type"=>"STR",
		"example"=>L_PARAM_WIZARD_FUNC_F_V_PAR0_EXAMPLE))),
"f_m"=>array("name"=>L_PARAM_WIZARD_FUNC_F_M_NAME,
	"desc"=>L_PARAM_WIZARD_FUNC_F_M_DESC,
	"params"=>array(
		array("name"=>L_PARAM_WIZARD_FUNC_F_M_PAR0_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_M_PAR0_DESC,
		"type"=>"STR",
		"example"=>L_PARAM_WIZARD_FUNC_F_M_PAR0_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_FUNC_F_M_PAR1_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_M_PAR1_DESC,
		"type"=>"STR",
		"example"=>L_PARAM_WIZARD_FUNC_F_M_PAR1_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_FUNC_F_M_PAR2_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_M_PAR2_DESC,
		"type"=>"STR",
		"example"=>L_PARAM_WIZARD_FUNC_F_M_PAR2_EXAMPLE),
		array("name"=>L_PARAM_WIZARD_FUNC_F_M_PAR3_NAME,
		"desc"=>L_PARAM_WIZARD_FUNC_F_M_PAR3_DESC,
		"type"=>"STR",
		"example"=>L_PARAM_WIZARD_FUNC_F_M_PAR3_EXAMPLE)))));

/*
$Log$
Revision 1.2  2001/11/26 11:07:30  honzam
No session add option for itemlink in alias

Revision 1.1  2001/10/24 18:44:10  honzam
new parameter wizard for function aliases and input type parameters

*/

?>