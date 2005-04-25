<?php
/**
 * Usage of the mini-gettext system.
 * See include/lang/readme.html for more info, and misc/mgettext for scripts
 * used to maintain the language files.
 *
 * @package MiniGetText
 * @version $Id$
 * @author Jakub Adamek, Econnect, January 2003
 * @copyright Copyright (C) 1999-2003 Association for Progressive Communications
*/
/*
Copyright (C) 1999-2003 Association for Progressive Communications
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

/** Returns current language (two-letter acronym, e.g. "es", "cz"). */
function get_mgettext_lang () {
    global $mgettext_lang;
    if (!isset ($mgettext_lang))
        return "en";
    else return $mgettext_lang;
}

/** Reads language constants from given file/
*   @param $filename  full path
*   @param $cache     Should the old language constants remain in memory?
*                     You will need this behaviour only when using a script which
*                     several times changes the language,
*                     e.g. sends a lot of emails in different languages.
*   @param $lang      If you want to include several language files,
*                     you must tell mgettext not too free the
*                     translations from the previous lang file. You do so by
*                     sending the language shortcut.
*/
function bind_mgettext_domain ($filename, $cache = false, $lang = "") {
    global $_m, $mgettext_lang, $mgettext_domain;

    if ( $mgettext_domain == $filename )
        return;                             // allready loaded

    // store strings into backup and look for new strings in backup
    if (!$_m_backup[$mgettext_domain] && $cache)
        $_m_backup[$mgettext_domain] = $_m;

    if ( $mgettext_domain == $filename )
        return;                             // allready loaded

    $mgettext_domain = $filename;
    if ($cache) {
        $_m = $_m_backup[$mgettext_domain];
        if ($_m) return;
    }

    if ( !is_file($filename)) {
        echo "<h1>WRONG MGETTEXT DOMAIN $filename</h1>";
//        exit;
    }
    else {
        if ($lang != get_mgettext_lang())
            $_m = "";
        include $filename;
    }
}

/** Translates given message.
*
*   @param string $id       Text to be translated. Escape % by backslash (\%).
*   @param array $params    You may use %1,%2,... in $id and supply an array of params,
*                           which are substituted for %i, e.g.
*                           _m("Hello %1, how are you?",array($username))
*   @return  if translation in the active language (get_mgettext_lang()) does not yet exist,
*                 returns $id, i.e. the English version
*/
function _m ($id, $params = 0) {
    global $_m;

    $retval = $_m[$id];
    if (!$retval)
        $retval = $id;

    if (is_array($params)) {
        $foo = "#$&*-";
        $retval = str_replace ('\%', $foo, $retval);
        for ($i = 0; $i < count ($params); $i ++)
            $retval = str_replace ("%".($i+1), $params[$i], $retval);
        $retval = str_replace ($foo, "%", $retval);
    }

    return $retval;
}

/** Works the same as _m() but is not parsed by xmgettext. This way it is
*   useful to translate a non constant message, counted at run-time. */
function _mdelayed ($id, $params = 0) {
    return _m ($id, $params);
}
?>
