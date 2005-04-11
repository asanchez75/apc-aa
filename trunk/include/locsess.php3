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

if (!is_array($_PHPLIB)) {
  $_PHPLIB["libdir"] = "";
}

if (! PHPLIB_ALREADY_LOADED && ! defined ("PHPLIB_AA_LOADED")) {
  /* Change this to match your database. */
  $db_type_filename = (defined("DB_TYPE") ? DB_TYPE .".inc" : "db_mysql.inc");
  require_once($_PHPLIB["libdir"] . $db_type_filename);

  /* Change this to match your data storage container */
  require_once($_PHPLIB["libdir"] . "ct_sql.inc");

  /* Required for everything below.      */
  require_once($_PHPLIB["libdir"] . "session.inc");

  /* Disable this, if you are not using authentication. */
  require_once($_PHPLIB["libdir"] . "auth.inc");
}

/* Required, contains your local session management extension */
require_once($GLOBALS["AA_INC_PATH"] . "extsess.php3");

/* Required, contains the page management functions. */
if (! PHPLIB_ALREADY_LOADED && ! defined ("PHPLIB_AA_LOADED")) {
   require_once($_PHPLIB["libdir"] . "page.inc");
};

define ("PHPLIB_AA_LOADED", 1);
?>