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

// includes for alerts files

require "../../include/config.php3";
if ($encap) require $GLOBALS[AA_INC_PATH]."locsessi.php3";
else require $GLOBALS[AA_INC_PATH]."locsess.php3"; 
require $GLOBALS[AA_INC_PATH]."util.php3";
require $GLOBALS[AA_INC_PATH]."varset.php3";
// mini gettext language environment (the _m() function)
require $GLOBALS[AA_INC_PATH]."mgettext.php3";

if (!$lang) $lang = "en";
set_mgettext_domain ($lang);
// common language constants
require $GLOBALS[AA_INC_PATH].$lang."_news_lang.php3";

require "./util.php3";
?>
