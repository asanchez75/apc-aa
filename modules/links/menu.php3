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

# $r_slice_headline - should be defined
# $slice_id - should be defined
# $r_slice_view_url - should be defined
# $editor_page or $usermng_page or $settings_page - should be defined
# $g_modules - should be defined

/*  Top level (navigation bar) menu description:
    label       to be shown
    cond        if not satisfied, don't show the label linked
                slice_id is included in the cond automatically
    href        link, relative to aa/
    exact_href  link, absolute (use either exact_href or href, not both)
*/

/**
 * You can insert special content to the left menu column. Just use following
 * keywords as itentifiers:
 *      "textXXX"   - display text
 *      "lineXXX"   - display line
 *      "headerXXX" - display menu header
 */

if (!defined("LINKS_MENU_INCLUDED"))
      define("LINKS_MENU_INCLUDED",1);
else return;

require_once $GLOBALS[AA_INC_PATH]."menu_util.php3";
require_once $GLOBALS[AA_INC_PATH]."perm_core.php3";
require_once $GLOBALS[AA_INC_PATH]."mgettext.php3";
//bind_mgettext_domain ($GLOBALS[AA_INC_PATH]."lang/".substr(LANG_FILE,0,2)."_news_lang.inc");

// I don't want to call AA menus as early as including menu.php3, because some permissions' functions are called. Hence I call get_aamenus in showMenu().
$aamenus = "aamenus";

$menu_function = 'get_aamenus_links';
function get_aamenus_links()
{
    global $r_slice_view_url,
           $auth,
           $AA_INSTAL_PATH,
           $AA_CP_Session,
           $linkedit,
           $r_state,
           $bookmarks,
           $slice_id;

    $module_location = "modules/links/";


    $aamenus["view"] = array (
        "label" => _m('View'),
        "exact_href"  => $r_slice_view_url,
        "cond"  => 1,
        "level" => "main");

    $aamenus["addlink"] = array (
        "label" => ($linkedit ? _m('Edit Link') : _m('Add Link')),
        "title" => ($linkedit ? _m('Edit Link') : _m('Add new link')),
        "href"  => $module_location."linkedit.php3",
        "level" => "main",
        "submenu" => "addlink_submenu");

    $aamenus["linkmanager"] = array (
        "label" => _m('Link Manager'),
        "title" => _m('Link Manager'),
        "href"  => $module_location."index.php3?Tab=app",
        "level" => "main",
        "submenu" => "linkmanager_submenu");

    $aamenus["modadmin"] = array (
        "label" => _m('Link Admin'),
        "title" => _m('Link Admin'),
        "href"  => $module_location."modedit.php3",
        "cond"  => IfSlPerm(PS_LINKS_SETTINGS),
        "level" => "main",
        "submenu"=>"modadmin_submenu");

    $aamenus["aaadmin"] = array (
        "label" => _m('AA'),
        "title" => _m('AA'),
        "href"  => "admin/um_uedit.php3",
        "cond"  => IsSuperadmin(),
        "level" => "main",
        "submenu"=>"aaadmin_submenu");

    $aamenus["linkmanager_submenu"] = array(
        "bottom_td"=>200,
        "level"=>"submenu",
        "items"=> array(
            "header1"=>_m('Folders'),
            "app"=>array ("cond"=> $r_state['bin'] != "app",
                            "href"=>$module_location."index.php3?Tab=app",
                            "label"=>_m('Active')." (".$r_state['bin_cnt']['app'].")"),
            "changed"=>array ("cond"=> $r_state['bin'] != "changed",
                            "href"=>$module_location."index.php3?Tab=changed",
                            "label"=>_m('Changed Links')." (".$r_state['bin_cnt']['changed'].")"),
            "new"=>array ("cond"=> $r_state['bin'] != "new",
                            "href"=>$module_location."index.php3?Tab=new",
                            "label"=>_m('New Links')." (".$r_state['bin_cnt']['new'].")"),
            "folder2"=>array ("cond"=> $r_state['bin'] != "folder2",
                            "href"=>$module_location."index.php3?Tab=folder2",
                            "label"=>_m('Holding bin')." (".$r_state['bin_cnt']['folder2'].")"),
            "folder3"=>array ("cond"=> $r_state['bin'] != "folder3",
                            "href"=>$module_location."index.php3?Tab=folder3",
                            "label"=>_m('Trash')." (".$r_state['bin_cnt']['folder3'].")"),
            "unasigned"=>array ("cond"=> $r_state['bin'] != "unasigned",
                            "href"=>$module_location."index.php3?Tab=unasigned",
                            "label"=>_m('Unasigned')." (".$r_state['bin_cnt']['unasigned'].")"),
            "header2" => _m('Bookmarks')));
    if( isset($bookmarks) AND is_array($bookmarks) ) {
        reset( $bookmarks );
        while( list( $bookid, $bookname ) = each( $bookmarks ) ) {
            $aamenus['linkmanager_submenu']['items']['bookmark'.$bookid] =
                array( "href"=>$module_location."index.php3?GoBookmark=$bookid",
                       "label"=>$bookname);
        }
    }
/*    $aamenus['linkmanager_submenu']['items']['header3'] = _m('Misc');
    $aamenus['linkmanager_submenu']['items']['item6'] =  array (
                            "cond"=>IfSlPerm(PS_LINKS_DELETE_LINKS),
                            "href"=>$module_location."index.php3?Delete=trash",
                            "label"=>"<img src='../../images/empty_trash.gif' border=0>"._m('Empty trash')); */

    $aamenus["addlink_submenu"] = array (
        "bottom_td"=>200,
        "level"=>"submenu",
        "items" => array(),);

    $aamenus["modadmin_submenu"] = array(
        "bottom_td"=>200,
        "level"=>"submenu",
        "items"=> array(
        "header1"=>_m('Main settings'),
        "main"=>array ("cond"      => IfSlPerm(PS_LINKS_SETTINGS),
                       "href"      => $module_location."modedit.php3",
                       "label"     => _m('Links')),
        "header2" => _m("Design"),
        "views"=>array('function'  => 'CreateMenu4Views',
                       'func_param'=> ''),
        "newcatview" =>  array ("cond"  => IfSlPerm(PS_LINKS_EDIT_DESIGN),
                                "href"  => 'admin/se_view.php3?new=1&view_type=categories',
                                "label" => _m('New Category View')),
        "newlinkview" => array ("cond"  => IfSlPerm(PS_LINKS_EDIT_DESIGN),
                                "href"  => 'admin/se_view.php3?new=1&view_type=links',
                                "label" => _m('New Link View'))
        ));

    /*  Second-level (left) menu description:
        bottom_td       empty space under the menu
        items           array of menu items in form item_id => properties
                        if item_id is "headerxxx", shows a header,
                            be careful that xxx be always a different number
                        if item_id is "line", shows a line
            label       to be shown
            cond        if not satisfied, don't show the label linked
                        slice_id is included in the cond automatically
            href        link, relative to aa/
            exact_href  link, absolute (use either exact_href or href, not both)
            show_always don't include slice_id in cond
            function    allows not define items directly - the function is
                        called when the left menu is displayed. I
                        It is better mainly for left submenus for which we need
                        database access - we do not spend time on DB operations
                        of unused menu rows
            func_param  Param to function (see above)
    */

    // left menu for aaadmin is common to all modules, so it is shared
    require_once $GLOBALS[AA_INC_PATH]."menu_aa.php3";
    return $aamenus;
}

/** Create view menu for current slice */
function CreateMenu4Views( $foo ) {
    global $slice_id;

    if ( !IfSlPerm(PS_LINKS_EDIT_DESIGN) )
        return;

    $db = getDB();

    $SQL = "SELECT id, name, type FROM view WHERE slice_id='". q_pack_id($slice_id)."'";
    $db->tquery( $SQL );
    while($db->next_record()) {
        $menu['view'.$db->f('id')] = CreateMetuItem( $db->f('name'),  // label, href [, cond]
            'admin/se_view.php3?view_id='.$db->f('id').'&view_type='.$db->f('type'));
    }
    freeDB($db);
    return $menu;
}
?>
