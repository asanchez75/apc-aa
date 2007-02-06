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

// $slice_id - should be defined
// $r_slice_view_url - should be defined
// $editor_page or $usermng_page or $settings_page - should be defined
// $g_modules - should be defined

/*  Top level (navigation bar) menu description:
    label       to be shown
    cond        if not satisfied, don't show the label linked
                slice_id is included in the cond automatically
    href        link, relative to aa/
    exact_href  link, absolute (use either exact_href or href, not both)
*/

require_once AA_INC_PATH."menu_util.php3";
require_once AA_INC_PATH."perm_core.php3";
require_once AA_INC_PATH."mgettext.php3";

// I don't want to call AA menus as early as including menu.php3, because some permissions' functions are called. Hence I call get_aamenus in showMenu().
$aamenus = "aamenus";
$menu_function = 'get_aamenus';

function get_aamenus() {
    global $r_slice_view_url,
           $auth,
           $slice_id,
           $r_state,
           $AA_CP_Session,
           $profile;
    trace("+","get_aamenus");

    $aamenus["view"] = array (
        "label"       => _m("View site"),
        "exact_href"  => $r_slice_view_url,
        "cond"        => 1,
        "level"       => "main");

    $input_view = (isset($profile) AND $profile->getProperty('input_view')) ?
                  '&vid='.$profile->getProperty('input_view') : '';

    $aamenus["additem"] = array (
        "label" => _m("Add Item"),
        "href"  => "admin/itemedit.php3?encap=false&add=1$input_view",
        "level" => "main");

    $aamenus["itemmanager"] = array (
        "label"   => _m("Item Manager"),
        "title"   => _m("Item Manager"),
        "href"    => "admin/index.php3?Tab=app",
        "level"   => "main",
        "submenu" => "itemmanager_submenu");

    $aamenus["sliceadmin"] = array (
        "label"   => _m("Slice Admin"),
        "title"   => _m("Slice Administration"),
        "href"    => "admin/se_fields.php3",
        "cond"    => IfSlPerm(PS_FIELDS),
        "level"   => "main",
        "submenu" => "sliceadmin_submenu");

    trace("=","","Creating main menu");
    $aamenus["aaadmin"] = array (
        "label"   => _m("AA"),
        "title"   => _m("AA Administration"),
        "href"    => "admin/um_uedit.php3",
        "cond"    => IfSlPerm(PS_NEW_USER),
        "level"   => "main",
        "submenu" => "aaadmin_submenu");

    /** Second-level (left) menu description:
     *  bottom_td       empty space under the menu
     *  items           array of menu items in form item_id => properties
     *                  if item_id is "headerxxx", shows a header,
     *                      be careful that xxx be always a different number
     *                  if item_id is "line", shows a line
     *      label       to be shown
     *      cond        if not satisfied, don't show the label linked
     *                  slice_id is included in the cond automatically
     *      href        link, relative to aa/
     *      exact_href  link, absolute (use either exact_href or href, not both)
     *      js          javascript function to call after click on link
     *                  you can use following aliases as function parameters:
     *                  {href} - alias for href (link, relative to aa/)
     *                  {exact_href} - alias for exact_href (link, absolute)
     *      show_always don't include slice_id in cond
     *      no_slice_id don't add slice_id to the URL
     */

    trace("=","","Creating submenu");
    $aamenus ["sliceadmin_submenu"] = array (
        "bottom_td" => 50,
        "level"     => "submenu",

        "items"     => array(

        "header1"       => _m("Main settings"),
        "main"          => array("cond"=>IfSlPerm(PS_EDIT),     "href"=>"admin/slicedit.php3",                 "label"=>_m("Slice"), "show_always"=>1), //"href"=>"admin/tabledit.php3?set_tview=sl_edit&cmd[sl_edit][edit][".$slice_id."]=1&slice_id=".$slice_id
        "category"      => array("cond"=>IfSlPerm(PS_CATEGORY), "href"=>"admin/se_constant.php3?category=1",   "label"=>_m("Category")),
        "fields"        => array("cond"=>IfSlPerm(PS_FIELDS),   "href"=>"admin/se_fields.php3",                "label"=>_m("Fields")),
        "slice_fields"  => array("cond"=>IfSlPerm(PS_FIELDS),   "href"=>"admin/se_fields.php3?slice_fields=1", "label"=>_m("Slice Fields")),
        "notify"        => array("cond"=>IfSlPerm(PS_EDIT),     "href"=>"admin/se_notify.php3",                "label"=>_m("Email Notification")),
        //"te_emails"   => array("cond"=>IfSlPerm(PS_FULLTEXT), "href"=>"admin/tabledit.php3?set_tview=email", "label"=>_m("Emails")),

        "header2"       => _m("Permissions"),
        "addusers"      => array("cond"=>IfSlPerm(PS_ADD_USER), "href"=>"admin/se_users.php3?adduser=1",       "label"=>_m("Assign")),
        "users"         => array("cond"=>IfSlPerm(PS_USERS),    "href"=>"admin/se_users.php3",                 "label"=>_m("Change")),

        "header3"       => _m("Design"),
        "compact"       => array("cond"=>IfSlPerm(PS_COMPACT),  "href"=>"admin/se_compact.php3",               "label"=>_m("Index")),
        "fulltext"      => array("cond"=>IfSlPerm(PS_FULLTEXT), "href"=>"admin/se_fulltext.php3",              "label"=>_m("Fulltext")),
        "views"         => array("cond"=>IfSlPerm(PS_FULLTEXT), "href"=>"admin/se_views.php3",                 "label"=>_m("Views")),
        "config"        => array("cond"=>IfSlPerm(PS_CONFIG),   "href"=>"admin/se_admin.php3",                 "label"=>_m("Item Manager")),
        "sets"          => array("cond"=>IfSlPerm(PS_FULLTEXT), "href"=>"admin/se_sets.php3",                  "label"=>_m("Sets of Items")),

        "header4"       => _m("Content Pooling"),
        "nodes"         => array("cond"=>isSuperadmin(),        "href"=>"admin/se_nodes.php3",                 "label"=>_m("Nodes")),
        "import"        => array("cond"=>IfSlPerm(PS_FEEDING),  "href"=>"admin/se_import.php3",                "label"=>_m("Inner Node Feeding")),
        "n_import"      => array("cond"=>IfSlPerm(PS_FEEDING),  "href"=>"admin/se_inter_import.php3",          "label"=>_m("Inter Node Import")),
        "n_export"      => array("cond"=>IfSlPerm(PS_FEEDING),  "href"=>"admin/se_inter_export.php3",          "label"=>_m("Inter Node Export")),
        "rssfeeds"      => array("cond"=>IfSlPerm(PS_FEEDING),  "href"=>"admin/se_rssfeeds.php3",              "label"=>_m("RSS Feeds")),
        "filters"       => array("cond"=>IfSlPerm(PS_FEEDING),  "href"=>"admin/se_filters.php3",               "label"=>_m("Filters")),
        "mapping"       => array("cond"=>IfSlPerm(PS_FEEDING),  "href"=>"admin/se_mapping.php3",               "label"=>_m("Mapping")),
        "CSVimport"     => array("cond"=>IfSlPerm(PS_FEEDING),  "href"=>"admin/se_csv_import.php3",            "label"=>_m("Import CSV")),

        "header5"       => _m("Misc"),
        "field_ids"     => array("cond"=>IfSlPerm(PS_FIELDS),   "href"=>"admin/se_fieldid.php3",               "label"=>_m("Change field IDs")),
        "javascript"    => array("cond"=>IfSlPerm(PS_FIELDS),   "href"=>"admin/se_javascript.php3",            "label"=>_m("Field Triggers")),
        "fileman"       => array("cond"=>FilemanPerms($auth, $slice_id), "href"=>"admin/fileman.php3",         "label"=>_m("File Manager")),
        "anonym_wizard" => array("cond"=>IfSlPerm(PS_FIELDS),   "href"=>"admin/anonym_wizard.php3",            "label"=>_m("Anonymous Form Wizard")),
        "email"         => array("cond"=>IfSlPerm(PS_USERS),    "href"=>"admin/tabledit.php3?set_tview=email", "label"=>_m("Email templates")),
    ));

    trace("=","","Getting slice info");

    $slice = AA_Slices::getSlice($slice_id);
    if ( $slice->getProperty("mailman_field_lists")) {
        $aamenus ["sliceadmin_submenu"]["items"]["mailman_create_list"] = array (
            "cond"  => IfSlPerm(PS_FIELDS),
            "href"  => "admin/mailman_create_list.php3",
            "label" => _m("Mailman: create list"));
    }

    $aamenus["itemmanager_submenu"] = array(
        "bottom_td" => 200,
        "level"     => "submenu",
        "items"     => array(
            "header1"   => _m("Folders"),
            "app"       => array("cond"=>1,                           "href"=>"admin/index.php3?Tab=app",                                   "label"=>"<img src='../images/ok.gif' border=0>"._m("Active")." (". $r_state['bin_cnt']['app'] .")"),
            "appb"      => array("cond"=>1,                           "href"=>"admin/index.php3?Tab=appb",                                  "label"=>_m("... pending")." (". $r_state['bin_cnt']['pending'] .")", "show"=>!$apple_design),
            "appc"      => array("cond"=>1,                           "href"=>"admin/index.php3?Tab=appc",                                  "label"=>_m("... expired")." (". $r_state['bin_cnt']['expired'] .")", "show"=>!$apple_design),
            "hold"      => array("cond"=>1,                           "href"=>"admin/index.php3?Tab=hold",                                  "label"=>"<img src='../images/edit.gif' border=0>"._m("Hold bin")." (". $r_state['bin_cnt']['folder2'] .")"),
            "trash"     => array("cond"=>1,                           "href"=>"admin/index.php3?Tab=trash",                                 "label"=>"<img src='../images/delete.gif' border=0>"._m("Trash bin")." (". $r_state['bin_cnt']['folder3'] .")"),

            "header2"   => _m("Misc"),
            "slice_fld" => array("cond"=>IfSlPerm(PS_EDIT_ALL_ITEMS), "href"=>"admin/slicefieldsedit.php3?edit=1&encap=false&id=$slice_id", "label"=> _m("Setting")),
            "item6"     => array("cond"=>IfSlPerm(PS_DELETE_ITEMS),   "href"=>"admin/index.php3?DeleteTrash=1",                              "label"=>"<img src='../images/empty_trash.gif' border=0>"._m("Empty trash"), "js"=>"EmptyTrashQuestion('{href}','"._m("Are You sure to empty trash?")."')"),
            "CSVimport" => array("cond"=>IfSlPerm(PS_EDIT_ALL_ITEMS), "href"=>"admin/se_csv_import.php3",                                   "label"=>_m("Import CSV")),
            "debug"     => array("cond"=>IsSuperadmin(),              "js"  =>"ToggleCookie('aa_debug','1')", "hide"=>!IsSuperadmin(),      "label"=> ($_COOKIE['aa_debug'] ? _m("Set Debug OFF") : _m("Set Debug ON"))),
            "line"      => ""
    ));

    trace("=","","Pre PS_EDIT_ALL_ITEMS");

    if ($slice_id && IfSlPerm(PS_EDIT_ALL_ITEMS)) {

        $db = new DB_AA;
        $items = &$aamenus["itemmanager_submenu"]["items"];

        // Add associated Alerts to Item Manager submenu
        if ($slice->getProperty("type") == "ReaderManagement" ) {
            $db->query("SELECT module_id, module.name FROM alerts_collection AC
                INNER JOIN module ON AC.module_id = module.id
                WHERE slice_id='".q_pack_id($slice_id)."'");
            AddAlertsModules($items, $db, _m("Alerts"),
                    _m("List of Alerts modules using this slice as Reader Management."));

            $items["header4"] = _m("Bulk Emails") ."&nbsp;&nbsp;&nbsp;".GetAAImage("help50.gif", _m("Send bulk email to selected users or to users in Stored searches"));
            $items["item1"]   = array("cond" => 1,
                                    "exact_href" => "javascript:WriteEmailGo()",
                                    "label" => _m("Send emails"),
                                    "no_slice_id"=>1);
        }
        trace("=","","module ids slice_id=".$slice_id);
        $db->query("SELECT DISTINCT AC.module_id, module.name FROM alerts_collection AC
            INNER JOIN module ON AC.module_id = module.id
            INNER JOIN alerts_collection_filter ACF ON AC.id = ACF.collectionid
            INNER JOIN alerts_filter AF ON AF.id = ACF.filterid
            INNER JOIN view ON view.id = AF.vid
            WHERE view.slice_id = '".q_pack_id($slice_id)."'");
        AddAlertsModules($items, $db, _m("Alerts Sent"), _m("List of Alerts modules sending items from this slice."));
    }

    // left menu for aaadmin is common to all modules, so it is shared
    require_once AA_INC_PATH."menu_aa.php3";
    trace("-");
    return $aamenus;
}

function AddAlertsModules(&$submenu, &$db, $header, $help) {
    global $auth;
    if ($db->num_rows()) {
        $submenu["header3"] = $header."&nbsp;&nbsp;&nbsp;". GetAAImage("help50.gif", $help);
        $i = 100;
        while ($db->next_record()) {
            $submenu["item".$i] = array(
                "cond"        => CheckPerms( $auth->auth["uid"], "slice", unpack_id($db->f("moduleid")), PS_FIELDS),
                "href"        => "modules/alerts/index.php3?slice_id=".unpack_id($db->f("module_id")),
                "no_slice_id" => 1,
                "label"       => $db->f("name"));
            $i++;
        }
    }
}

?>
