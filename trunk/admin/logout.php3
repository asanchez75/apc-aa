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

/** Logout
 *  safely destroys current session information from database
 */

require_once "../include/init_page.php3";

$sess->delete();

// go to url - we can't use go_url() function since it call page_close().
// We can't call it, because then the deleted session is stored again!
header("Status: 302 Moved Temporarily");
header("Location: ". get_admin_url('index.php3', false));

//page_close();  NO!!!

?>