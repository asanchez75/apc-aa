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


# config file identifier
# must correspond with fileneme in $ActionAppConfig[xxx][file]!!
define("CONFIG_FILE", "en_whos_lang.php3");

define("IMG_UPLOAD_MAX_SIZE", "400000");    // max size of file in picture uploading
define("IMG_UPLOAD_URL", "http://web.ecn.cz/aauser/img_upload/");
define("IMG_UPLOAD_PATH", "/usr/local/httpd/htdocs/aauser/img_upload/");
define("EDITOR_GRAB_LEN", 200);                 // not used, i think
define("EDIT_ITEM_COUNT", 20);                  // number of items in editor window

# Default values for database fields
define("DEFAULT_EDIT_FIELDS",    // shown fields (headline if allways shown)
 "y".  // abstract
 "n".  // html_formatted
 "y".  // full_text
 "n".  // highlight
 "n".  // hl_href
 "n".  // link_only
 "n".  // place
 "y".  // source
 "y".  // source_href
 "n".  // status_code
 "y".  // language_code
 "n".  // cp_code
 "y".  // category_id
 "n".  // img_src
 "n".  // img_width
 "n".  // img_height
 "y".  // posted_by
 "y".  // e_posted_by
 "y".  // publish_date
 "n".  // expiry_date
 "y".  // edit_note
 "n".  // img_upload
 "n".  // redirect
 "y".  // con_name
 "y".  // con_email
 "y".  // con_phone
 "y".  // con_fax
 "y".  // source_desc
 "y".  // source_address
 "y".  // source_city
 "y".  // source_prov
 "y".  // source_country
 "n".  // start_date
 "n".  // end_date
 "n".  // time
 "n".  // loc_name
 "n".  // loc_address
 "n".  // loc_city
 "n".  // loc_prov
 "n".  // loc_country
 "n".  // reserved
 "n".  // reserved
 "n".  // reserved
 "n".  // reserved
 "n".  // reserved
 "n".  // reserved
 "n".  // reserved
 "n"); // reserved
define("DEFAULT_NEEDED_FIELDS", 
 "y".  // abstract
 "n".  // html_formatted
 "n".  // full_text
 "n".  // highlight
 "n".  // hl_href
 "n".  // link_only
 "n".  // place
 "n".  // source
 "n".  // source_href
 "n".  // status_code
 "n".  // language_code
 "n".  // cp_code
 "n".  // category_id
 "n".  // img_src
 "n".  // img_width
 "n".  // img_height
 "n".  // posted_by
 "n".  // e_posted_by
 "n".  // publish_date
 "n".  // expiry_date
 "n".  // edit_note
 "n".  // img_upload
 "n".  // redirect
 "n".  // con_name
 "n".  // con_email
 "n".  // con_phone
 "n".  // con_fax
 "n".  // source_desc
 "n".  // source_address
 "n".  // source_city
 "n".  // source_prov
 "n".  // source_country
 "n".  // start_date
 "n".  // end_date
 "n".  // time
 "n".  // loc_name
 "n".  // loc_address
 "n".  // loc_city
 "n".  // loc_prov
 "n".  // loc_country
 "n".  // reserved
 "n".  // reserved
 "n".  // reserved
 "n".  // reserved
 "n".  // reserved
 "n".  // reserved
 "n".  // reserved
 "n"); // reserved

define("DEFAULT_SEARCH_SHOW", 
 "n".  // slice
 "y".  // category
 "y".  // author
 "n".  // language
 "y".  // from
 "y".  // to
 "y".  // headline
 "y".  // abstract
 "y".  // full_text
 "y".  // edit_note
 "y".  // reserve
 "y".  // reserve
 "y".  // reserve
 "y"); // reserve
define("DEFAULT_SEARCH_DEFAULT", 
 "y".  // headline
 "y".  // abstract
 "y".  // full_text
 "n".  // edit_note
 "n".  // reserve
 "n".  // reserve 
 "n"); // reserve 
define("DEFAULT_FULLTEXT_HTML", '<BR><FONT SIZE=+2 COLOR=blue>_#HEADLINE</FONT> <BR><B>_#PUB_DATE</B> <BR>_#FULLTEXT');
define("DEFAULT_ODD_HTML", '<font face=Arial color=#808080 size=-2>_#PUB_DATE - </font><font color=#FF0000><strong><a href=_#HDLN_URL>_#HEADLINE</a></strong></font><font color=#808080 size=-1><br>_#PLACE###(<a href="_#SRC_URL#">_#SOURCE##</a>) - </font><font color=black size=-1>_#ABSTRACT<br></font><br>');
define("DEFAULT_EVEN_HTML", "");
define("DEFAULT_TOP_HTML", "<br>");
define("DEFAULT_BOTTOM_HTML", "<br>");
define("DEFAULT_CATEGORY_HTML", "<p>_#CATEGORY</p>");
define("DEFAULT_EVEN_ODD_DIFFER", false);
define("DEFAULT_CATEGORY_SORT", true);
define("DEFAULT_COMPACT_REMOVE", "()");
define("DEFAULT_FULLTEXT_REMOVE", "()");

define("DEFAULT_LIST_LENGTH", "20");
define("DEFAULT_GRAB_LEN", "200");
define("DEFAULT_LANGUAGE_CODE", "EN");
define("DEFAULT_CP_CODE", "iso8859-1");
define("DEFAULT_STATUS_CODE", "1");
define("DEFAULT_HIGHLIGHT", "0");
define("DEFAULT_EXPIRY_LIMIT", "5000");

# HTML begin of admin page
# You should set language of admin pages and possibly any meta tags
define("HTML_PAGE_BEGIN",
 '<!DOCTYPE html public "-/W3C/DTD HTML 4.0 Transitional/EN">
  <HTML>
  <HEAD>
  <LINK rel=StyleSheet href="../'.ADMIN_CSS.'" type="text/css">
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">');
    
# Input form field names
define("L_HEADLINE", "Contact Last Name");
define("L_ABSTRACT", "Short Description/Bio");
define("L_FULL_TEXT", "Long Description/Bio/Resume");
define("L_FT_FORMATTING", "Sector");
define("L_FT_FORMATTING_HTML", "HTML code");
define("L_FT_FORMATTING_PLAIN", "Plain text"); 
define("L_POSTDATE", "Post date");
define("L_POSTED_BY", "Posted by");
define("L_E_POSTED_BY","Posted by e-mail"); 
define("L_PUBLISH_DATE", "Publish date");
define("L_EXPIRY_DATE", "Expiry date");
define("L_CATEGORY", "Category");
define("L_STATUS_CODE", "News status");
define("L_LANGUAGE_CODE", "Language");
define("L_CP_CODE", "Encoding");
define("L_LINK_ONLY", "External news");
define("L_HL_HREF", "External news URL");
define("L_HTML_FORMATTED", "HTML formatted");
define("L_HIGHLIGHT", "Highlight");
define("L_IMG_SRC","Image URL"); 
define("L_IMG_WIDTH","Image width"); 
define("L_IMG_HEIGHT","Image height");
define("L_PLACE","Location");
define("L_SOURCE","Organization Name");
define("L_SOURCE_HREF","Organization/Contact URL");
define("L_REDIRECT","Show on URL");
define("L_CREATED_BY","Written by");
define("L_LASTEDIT","Last edited by");
define("L_AT","at");   
define("L_EDIT_NOTE","Editor's note"); 
define("L_IMG_UPLOAD","Image upload"); 
define("L_SOURCE_DESC", "Org Description");
define("L_SOURCE_ADDRESS", "Street Adress");
define("L_SOURCE_CITY", "City");
define("L_SOURCE_PROV", "Province/State");
define("L_SOURCE_COUNTRY", "Country");
define("L_TIME", "Time");
define("L_CON_NAME", "Contact Name");
define("L_CON_EMAIL", "Contact E-mail");
define("L_CON_PHONE", "Contact phone");
define("L_CON_FAX", "Contact FAX");
define("L_LOC_NAME", "Location Name");
define("L_LOC_ADDRESS", "Location Street Address");
define("L_LOC_CITY", "Location City");
define("L_LOC_PROV", "Location Province/State");
define("L_LOC_COUNTRY", "Location Country");
define("L_START_DATE", "Start Date");
define("L_END_DATE", "End Date");

# aa toolkit specific labels
define("L_HLP_HEADLINE",'alias for Item Headline');
define("L_HLP_CATEGORY",'alias for Category Name');
define("L_HLP_HDLN_URL",'alias for News URL<br>(substituted by External news link URL(if External news is checked) or link to Fulltext)<div class=example><em>Example: </em>&lt;a href=_#HDLN_URL&gt;_#HEADLINE&lt;/a&gt;</div>');
define("L_HLP_POSTDATE",'alias for Post Date');
define("L_HLP_PUB_DATE",'alias for Publish Date');
define("L_HLP_EXP_DATE",'alias for Date of Expiration');
define("L_HLP_ABSTRACT",'alias for Summary<br>(if there is no abstract in database, first Grab_length characters from Fulltext are used)');
define("L_HLP_FULLTEXT",'alias for Fulltext<br>(HTML tags are striped or not depending on HTML formated item setting)');
define("L_HLP_IMAGESRC",'alias for Image URL<br>(if there is no image url defined in database, default url is used instead (see NO_PICTURE_URL constant in en_*_lang.php3 file))<div class=example><em>Example: </em>&lt;img src="_#IMAGESRC"&gt;</div>');
define("L_HLP_SOURCE",'alias for Source Name<br>(see _#LINK_SRC for text source link)');
define("L_HLP_SRC_URL",'alias for Source URL<br>(if there is no source url defined in database, default source url is displayed (see NO_SOURCE_URL constant in en_*_lang.php3 file))<br>Use _#LINK_SRC for text source link.<div class=example><em>Example: </em>&lt;a href"_#SRC_URL#"&gt;&lt;img src="source.gif"&gt;&lt;/a&gt;</div>');
define("L_HLP_LINK_SRC",'alias for Source Name with link.<br>(substituted by &lt;a href="_#SRC_URL#"&gt;_#SOURCE##&lt;/a&gt; if Source URL defined, otherwise _#SOURCE## only)');
define("L_HLP_PLACE",'alias for Locality');
define("L_HLP_POSTEDBY",'alias for Author');
define("L_HLP_E_POSTED","alias for Author's e-mail");
define("L_HLP_CREATED",'alias for Written By');
define("L_HLP_EDITEDBY",'alias for Last edited By');
define("L_HLP_LASTEDIT",'alias for Date of last editation');
define("L_HLP_EDITNOTE","alias for Editor's note");
define("L_HLP_IMGWIDTH",'alias for Image Width<br>(if no width defined, program tries to remove <em>width=</em> atribute from format string<div class=example><em>Example: </em>&lt;img src="_#IMAGESRC" width=_#IMGWIDTH height=_#IMG_HGHT&gt;</div>');
define("L_HLP_IMG_HGHT",'alias for Image Height<br>(if no height defined, program tries to remove <em>height=</em> atribute from format string<div class=example><em>Example: </em>&lt;img src="_#IMAGESRC" width=_#IMGWIDTH height=_#IMG_HGHT&gt;</div>');
define("L_HLP_ITEM_ID",'alias for News ID<br>(can be used as parameter sh_itm= given to slice.php3 (or to any .shtml file, which this script includes))');
define("L_HLP_CATEGORY_ID",'alias for Category ID<br>(can be used with HTML tag &lt;A NAME="_#CATEG_ID"&gt; in Category headline field)');
define("L_HLP_SOURCE_DESC", "Alias for Org Description");
define("L_HLP_SOURCE_ADDRESS", "Alias for Street Adress");
define("L_HLP_SOURCE_CITY", "Alias for City");
define("L_HLP_SOURCE_PROV", "Alias for Province/State");
define("L_HLP_SOURCE_COUNTRY", "Alias for Country");
define("L_HLP_TIME", "Alias for Time");
define("L_HLP_CON_NAME", "Alias for Contact Name");
define("L_HLP_CON_EMAIL", "Alias for Contact E-mail");
define("L_HLP_CON_PHONE", "Alias for Contact phone");
define("L_HLP_CON_FAX", "Alias for Contact FAX");
define("L_HLP_LOC_NAME", "Alias for Location Name");
define("L_HLP_LOC_ADDRESS", "Alias for Location Street Address");
define("L_HLP_LOC_CITY", "Alias for Location City");
define("L_HLP_LOC_PROV", "Alias for Location Province/State");
define("L_HLP_LOC_COUNTRY", "Alias for Location Country");
define("L_HLP_START_DATE", "Alias for Start Date");
define("L_HLP_END_DATE", "Alias for End Date");

define("L_CANT_UPLOAD","Can't upload Image"); 
define("L_GRAB_LEN", "Fulltext into abstract grab length [characters]");
define("L_SLICE_DEFAULTS", "Default values for new news");
define("L_D_EXPIRY_LIMIT", "Expire limit [days]");
define("L_MSG_PAGE", "Toolkit news message");   // title of message page
define("L_EDITOR_TITLE", "Editor window - news management");
define("L_FULLTEXT_FORMAT", "Fulltext HTML code");
define("L_A_FULLTEXT_TIT", "Slice Administration - News Fulltext Format");
define("L_FULLTEXT_HDR", "HTML code for fulltext view of news");
define("L_COMPACT_HDR", "HTML code for compact view of news");
define("L_ITEM_HDR", "News Data");
define("L_A_ITEM_ADD", "Add News");
define("L_A_ITEM_EDT", "Edit News");
define("L_IMP_EXPORT", "Enable export of news to slice:");
define("L_ADD_NEW_ITEM", "Add Article");
define("L_EDIT_ITEMS", "Edit News");
define("L_DELETE_TRASH", "Empty trash");
define("L_VIEW_FULLTEXT", "Preview");
define("L_FULLTEXT", "Public View: Fulltext");
define("L_HIGHLIGHTED", "Highlighted");
define("L_NO_HIGHLIGHTED", "No highlighted");
define("L_A_FIELDS_EDT", "Slice Administration - News Fields Settings");
define("L_FIELDS_HDR", "News fields");
define("L_NO_PS_EDIT_ITEMS", "You have not permissions to edit news in this slice");
define("L_NO_DELETE_ITEMS", "You have not permissions to remove news");
define("L_NO_PS_MOVE_ITEMS", "You have not permissions to move news");
define("L_FULLTEXT_OK", "Fulltext format update successful");
define("L_NO_ITEM", "No news matching your query.");

# aa toolkit common labels
# can be the same for all toolkit aplications
define("L_ACTIVE_BIN", "Approved");
define("L_HOLDING_BIN", "Holding bin");
define("L_TRASH_BIN", "Trash");
define("L_SLICE_NAME", "Title");          // slice
define("L_SHORT_NAME", "Short name");     // slice
define("L_DELETED", "Deleted");           // slice
define("L_D_LISTLEN", "Listing length");  // slice
define("L_ERR_CANT_CHANGE", "Can't change slice settings");
define("L_ODD_ROW_FORMAT", "Odd Rows");
define("L_EVEN_ROW_FORMAT", "Even Rows");
define("L_EVEN_ODD_DIFFER", "Use another HTML code for even rows");
define("L_CATEGORY_FORMAT", "Category Headline");
define("L_CATEGORY_SORT", "Sort items by category");
define("L_COMPACT_TOP", "Top HTML");
define("L_COMPACT_BOTTOM", "Bottom HTML");
define("L_A_COMPACT_TIT", "Slice Administration - Compact view format");
define("L_A_FILTERS_TIT", "Slice Administration - Feeding filters setting");
define("L_FLT_SETTING", "Feeding import filters setting");
define("L_FLT_FROM_SL", "Filter for imported slice");
define("L_FLT_FROM", "From");
define("L_FLT_TO", "To");
define("L_FLT_APPROVED", "Approved");
define("L_FLT_CATEGORIES", "Categories");
define("L_ALL_CATEGORIES", "All Categories");
define("L_FLT_NONE", "No From category selected!");
define("L_THE_SAME", "-- The same --");
define("L_IMP_EXPORT_Y", "Export enable");
define("L_IMP_EXPORT_N", "Export disable");
define("L_EXPORT_TO_ALL", "Enable export to any slice");
define("L_IMP_IMPORT", "Import from slice:");
define("L_IMP_IMPORT_Y", "Import");
define("L_IMP_IMPORT_N", "Do not import");
define("L_CONSTANTS_HLP", "Use these aliases for database fields");
define("L_RELOGIN", "Logout and login as another user");
define("L_ERR_IN", "Error in");
define("L_ERR_BE_POSITIVE", "Number should be positive");
define("L_ERR_NEED", "it must be filled");
define("L_ERR_LOG", "you should use a-z, A-Z and 0-9 characters");
define("L_ERR_LOGLEN", "it must by 5 - 32 characters long");
define("L_ERR_FEEDED_ITEMS", "There is an item in trash bin, which is fed. It is imposible to delete it.");
define("L_ERR_NO_SRCHFLDS", "No searchfield specified!");
define("L_NO_PRMS_SLICE", "You have no permissions for changing slice");

define("L_FIELDS", "Author: Add Article");
define("L_EDIT", "Edit");
define("L_EDIT_SLICE", "Edit Slice");
define("L_DELETE", "Delete");
define("L_REVOKE", "Revoke");
define("L_UPDATE", "Update");
define("L_RESET", "Reset form");
define("L_CANCEL", "Cancel");
define("L_ACTION", "Action");
define("L_INSERT", "Insert");
define("L_VIEW", "View");
define("L_NEW", "New");
define("L_GO", "Go");
define("L_ADD", "Add");
define("L_USERS", "Users");
define("L_GROUPS", "Groups");
define("L_ORGANIZATION", "Organization");
define("L_SEARCH", "Search");
define("L_RENAME", "Rename");
define("L_DEFAULTS", "Default");
define("L_SLICE", "Slice");
define("L_DELETED_SLICE", "No slice found for you");
define("L_SLICE_URL", "Slice URL");
define("L_CURRENT_USERS", "Current Users");
define("L_A_NEWUSER", "New user in permission system");
define("L_NEWUSER_HDR", "New user");
define("L_USER_LOGIN", "Login name");
define("L_USER_PASSWORD1", "Password");
define("L_USER_PASSWORD2", "Retype password");
define("L_USER_FIRSTNAME", "First name");
define("L_USER_SURNAME", "Surname");
define("L_USER_MAIL", "E-mail");
define("L_USER_SUPER", "Superadmin account");
define("L_A_USERS_TIT", "Slice Administration - User Management");
define("L_A_PERMISSIONS", "Slice Administration - Permissions");

define("L_ROLE_AUTHOR", "Author");
define("L_ROLE_EDITOR", "Editor");
define("L_ROLE_ADMINISTRATOR", "Administrator");
define("L_ROLE_SUPER", "Super");

define("L_SLICE_ADM", "Slice Administration");
define("L_MAIN_SET", "Main settings");
define("L_PERMISSIONS", "Permissions");
define("L_PERM_CHANGE", "Change");
define("L_PERM_ASSIGN", "Assign");
define("L_PERM_NEW", "Search user or group");
define("L_PERM_SEARCH", "Assign new permissions");
define("L_PERM_CURRENT", "Change current permissions");
define("L_USER_NEW", "New User");
define("L_DESIGN", "Design");
define("L_COMPACT", "Public View: Index");
define("L_COMPACT_REMOVE", "Remove strings");
define("L_FEEDING", "Feeding");
define("L_IMPORT", "Import & Export");
define("L_FILTERS", "Filters");
define("L_A_SLICE_ADD", "Slice Administration - Add Slice");
define("L_A_SLICE_EDT", "Slice Administration - Edit Slice");
define("L_A_SLICE_CAT", "Slice Administration - Category Assignment");
define("L_A_SLICE_IMP", "Slice Administration - Feeding Settings");
define("L_A_SLICE_USERS", "Slice Administration - User Management");
define("L_FIELD", "Field");
define("L_FIELD_IN_EDIT", "Show");
define("L_NEEDED_FIELD", "Required");
define("L_A_SEARCH_TIT", "Slice Administration - Search Form Settings");
define("L_SEARCH_HDR", "Search form criteria");
define("L_SEARCH_HDR2", "Search in fields");
define("L_SEARCH_SHOW", "Show");
define("L_SEARCH_DEFAULT", "Default settings");
define("L_SEARCH_SET", "Public View: Search");
define("L_AND", "AND");
define("L_OR", "OR");
define("L_SRCH_KW", "Search for");
define("L_SRCH_FROM", "From");
define("L_SRCH_TO", "To");
define("L_SRCH_SUBMIT", "Search");
define("L_NO_PRMS_SLICE", "You have not permissions to add/edit slice");
define("L_NO_PS_EDIT", "You have not permissions to edit this slice");
define("L_NO_PS_ADD", "You have not permissions to add slice");
define("L_NO_PS_COPMPACT", "You have not permissions to change compact view formatting");
define("L_NO_PS_FULLTEXT", "You have not permissions to change fulltext formatting");
define("L_NO_PS_CATEGORY", "You have not permissions to change category settings");
define("L_NO_PS_FEEDING", "You have not permissions to change feeding setting");
define("L_NO_PS_USERS", "You have not permissions to manage users");
define("L_NO_PS_FIELDS", "You have not permissions to change fields settings");
define("L_NO_PS_SEARCH", "You have not permissions to change search settings");
define("L_PS_NO_NEW_USER", "You have not permissions to create new user");
define("L_BAD_RETYPED_PWD", "Retyped password is not the same as the first one");
define("L_ERR_USER_ADD", "It is impossible to add user to permission system");
define("L_NEWUSER_OK", "User successfully added to permission system");
define("L_COMPACT_OK", "Design of compact design successfully changed");
define("L_NEEDED", "Must be filled");
define("L_ALL", " - all - ");
define("L_CAT_LIST", "Slice Categories");
define("L_CAT_SELECT", "This Slice Categories");
define("L_NEW_CATEG", "Enter the name for the new category");
define("L_NEW_SLICE", "Add Slice");
define("L_SLICE_NEW", "New Slice");
define("L_RENAME_CATEG", "Enter the new name for this category");
define("L_ASSIGN", "Assign");
define("L_CATBINDS_OK", "Category update successful");
define("L_IMPORT_OK", "Feeding update successful");
define("L_FIELDS_OK", "Fields update successful");
define("L_SEARCH_OK", "Search fields update successful");
define("L_ADMINPAGE", "Back to Main Administration Page");
define("L_NO_CATEGORY", "No category defined");
define("L_NO_IMPORTED_SLICE", "There are no imported slices");
define("L_NO_USERS", "No user (group) found");

define("L_TOO_MUCH_USERS", "Too many users or groups found.");
define("L_MORE_SPECIFIC", "Try to be more specific.");
define("L_REMOVE", "Remove");
define("L_ID", "Id");
define("L_TYPE", "Type");
define("L_SETTINGS", "Admin");
define("L_SETTINGS", "Admin");
define("L_LOGO", "APC toolkit");
define("L_USER_MANAGEMENT", "Users");
define("L_ITEMS", "Item management page");
define("L_NEW_SLICE_HEAD", "New slice");
define("L_ERR_USER_CHANGE", "Can't change user");
define("L_PUBLISHED", "Published");
define("L_EXPIRED", "Expired");
define("L_NOT_PUBLISHED", "Not published, yet");
define("L_EDIT_USER", "Edit User");
define("L_EDITUSER_HDR", L_EDIT_USER);
define("L_USER_ID", "User Id");
define("NO_PICTURE_URL", "http://web.ecn.cz/aauser/images/no_pict.gif");  // image used when 
  // there is img_source in html format string but no img_source is stored in database 
  // (you can use blank pixel for none picture)
define("NO_SOURCE_URL", "javascript: window.alert('No source url specified')"); 
define("NO_OUTER_LINK_URL", "javascript: window.alert('No outer url specified')");

# editors interface constants
define("L_PUBLISHED_HEAD", "Pub");
define("L_HIGHLIGHTED_HEAD", "&nbsp;!&nbsp;");
define("L_FEEDED_HEAD", "Fed");
define("L_FEEDED_INTO_APP", "Fed into Approved bin");
define("L_FEEDED_INTO_HOLD", "Fed into Holding bin");
define("L_FEEDED_INTO_TRASH", "Fed into Trash bin");
define("L_MORE_DETAILS", "More details");
define("L_LESS_DETAILS", "Less details");
define("L_ACTION", "Action");
define("L_MOVE_TO", "Move selected to");
define("L_SELECT_ALL", "Select all");
define("L_UNSELECT_ALL", "Unselect all");
define("L_SELECT_VISIBLE", "Select visible");
define("L_UNSELECT_VISIBLE", "Unselect visible");

define("L_D_LANGUAGE_CODE", L_LANGUAGE_CODE);
define("L_D_CP_CODE", L_CP_CODE);
define("L_D_CATEGORY_ID", L_CATEGORY);
define("L_CATEGORY_ID", L_CATEGORY);
define("L_D_STATUS_CODE", L_STATUS_CODE);
define("L_D_HIGHLIGHT", L_HIGHLIGHT);
define("L_D_EXPIRY_DATE", L_EXPIRY_DATE);
define("L_D_HL_HREF", L_HL_HREF);
define("L_D_SOURCE", L_SOURCE);
define("L_D_SOURCE_HREF", L_SOURCE_HREF);
define("L_D_REDIRECT", L_REDIRECT);
define("L_D_PLACE", L_PLACE);
define("L_D_HTML_FORMATTED", L_HTML_FORMATTED);
define("L_D_IMG_SRC", L_IMG_SRC);
define("L_D_IMG_WIDTH", L_IMG_WIDTH);
define("L_D_IMG_HEIGHT", L_IMG_HEIGHT);
define("L_D_POSTED_BY", L_POSTED_BY);
define("L_D_E_POSTED_BY", L_E_POSTED_BY);
define("L_D_LINK_ONLY", L_LINK_ONLY);
define("L_A_FILTERS_FLT", L_A_FILTERS_TIT);
define("L_A_COMPACT", L_A_COMPACT_TIT);
define("L_A_FULLTEXT", L_A_FULLTEXT_TIT);
define("L_SRCH_ALL", L_ALL);
define("L_SRCH_SLICE", L_SLICE);
define("L_SRCH_CATEGORY", L_CATEGORY);
define("L_SRCH_AUTHOR", L_CREATED_BY);
define("L_SRCH_LANGUAGE", L_LANGUAGE_CODE);
define("L_SRCH_HEADLINE", L_HEADLINE);
define("L_SRCH_ABSTRACT", L_ABSTRACT);
define("L_SRCH_FULL_TEXT", L_FULL_TEXT);
define("L_SRCH_EDIT_NOTE", L_EDIT_NOTE);
define("L_SLICES_HDR", L_SLICE);
define("L_A_SEARCH_EDT", L_A_SEARCH_TIT);
define("L_A_SLICE_TIT", L_SLICE_ADM);
define("L_A_FIELDS_TIT", L_A_FIELDS_EDT);
define("L_A_SLICE_TIT", L_SLICE_ADM);
define("L_SLICE_SET", L_SLICE);
define("L_FULLTEXT_REMOVE", L_COMPACT_REMOVE);

//define("", "");
//prepared for new constants
define("L_FEEDED_FROM", "Fed from");
define("DEFAULT_SLICE_CONFIG", "<wddxPacket version='0.9'><header/><data><struct><var name='admin_fields'><struct><var name='chbox'><struct><var name='width'><string>24</string></var></struct></var><var name='edit'><struct><var name='width'><string>30</string></var></struct></var><var name='headlinepreview'><struct><var name='width'><string>224</string></var></struct></var><var name='catname'><struct><var name='width'><string>70</string></var></struct></var><var name='post_date'><struct><var name='width'><string>70</string></var></struct></var><var name='e_posted_by'><struct><var name='width'><string>70</string></var></struct></var></struct></var></struct></data></wddxPacket>");
define("L_FEED", "Export");
define("L_FEEDTO_TITLE", "Export Item to Selected Slice");
define("L_FEED_TO", "Export selected items to selected slice");
define("L_NO_PERMISSION_TO_FEED", "No permission");
define("L_NO_PS_CONFIG", "You do not have permission to set the configuration parameters of this slice");
define("L_A_SLICE_CFG", "Slice Administration - Configuration parameters");
define("L_VISIBLE_ADMIN_FIELDS", "Visible columns in admin interface");
define("L_FIELD_WIDTH", "Column width");
define("L_VISIBLE", "Shown");
define("L_HIDDEN", "Hidden");
define("L_SLICE_CONFIG", "Editor: Article Manager");
define("L_CHBOX_HEAD", "&nbsp;");   // title of checkbox in admin interface
define("L_CHBOX", "Checkbox"); 
define("L_CATNAME", "Category Name");
define("L_CATEGORY_ID", "Category ID");
define("L_UP", "Up");
define("L_DOWN", "Down");
define("L_EDITED_BY","Edited by");
define("L_MASTER_ID", "Master id");
define("L_CHANGE_MARKED", "Change Marked");
define("L_MOVE_TO_ACTIVE_BIN", "Move to Approved");
define("L_MOVE_TO_HOLDING_BIN", "Move to Holding bin");
define("L_MOVE_TO_TRASH_BIN", "Move to Trash");
define("L_OTHER_ARTICLES", "Other Articles");
define("L_MISC", "Misc");
define("L_HEADLINE_EDIT", "Headline (edit on click)");
define("L_HEADLINE_PREVIEW", "Headline (preview on click)");
define("L_EDIT_LINK", "&nbsp;");
define("L_PREVIEW_LINK", "&nbsp;");
define("L_ARTICLE_MANAGER", "Article Manager");
define("L_SWITCH_TO", "Switch to slice");
define("L_ADMIN", "Admin");

//new_constants
define("L_NO_PS_NEW_USER", "No permission to create new user");
define("L_ALL_GROUPS", "All Groups");
define("L_USERS_GROUPS", "User's Groups");
define("L_REALY_DELETE_USER", "Are you sure to delete selected user from whole permission system?");
define("L_REALY_DELETE_GROUP", "Are you sure to delete selected group from whole permission system?");
define("L_ITEM_NOT_CHANGED", "Item not changed");
define("L_CANT_ADD_ITEM", "Can't add item");
define("L_TOO_MUCH_GROUPS", "Too many groups found.");
define("L_NO_GROUPS", "No groups found");
define("L_GROUP_NAME", "Name");
define("L_GROUP_DESCRIPTION", "Description");
define("L_GROUP_SUPER", "Superadmin group");
define("L_ERR_GROUP_ADD", "It is impossible to add group to permission system");
define("L_NEWGROUP_OK", "Group successfully added to permission system");
define("L_ERR_GROUP_CHANGE", "Can't change group");
define("L_A_UM_USERS_TIT", "User management - Users");
define("L_A_UM_GROUPS_TIT", "User management - Groups");
define("L_EDITGROUP_HDR", "Edit group");
define("L_NEWGROUP_HDR", "New group");
define("L_GROUP_ID", "Group Id");
define("L_ALL_USERS", "All Users");
define("L_GROUPS_USERS", "Group's Users");
define("L_POST", "Post");
define("L_POST_PREV", "Post & Preview");
define("L_OK", "OK");
define("L_FEEDED_FROM", "Fed from");
define("L_ACTIVE_BIN_EXPIRED", "Approved - Expired");
define("L_ACTIVE_BIN_PENDING", "Approved - Pending");
define("L_ACTIVE_BIN_EXPIRED_MENU", "... expired");
define("L_ACTIVE_BIN_PENDING_MENU", "... pending");

// not appended to other lang files
//define("", "");


$l_month = array( 1 => 'January', 'February', 'March', 'April', 'May', 'June', 
		'July', 'August', 'September', 'October', 'November', 'December');

/*
$Log$
Revision 1.4  2000/11/16 11:48:39  madebeer
11/16/00 a- changed admin leftbar menu order and labels
         b- changed default article editor field order & fields
         c- improved some of the english labels

Revision 1.3  2000/11/13 10:41:14  honzam
Fixed bad order for default setting of show fields and needed fields

Revision 1.2  2000/10/12 15:56:09  honzam
Updated language files with better defaults

Revision 1.1  2000/10/11 20:18:29  honzam
Upadted database structure and language files for web.net's extended item table

Revision 1.1  2000/10/10 18:28:00  honzam
Support for Web.net's extended item table

Revision 1.12  2000/08/17 15:17:55  honzam
new possibility to redirect item displaying (for database changes see CHANGES)

Revision 1.11  2000/08/15 08:58:31  kzajicek
Added missing L_HLP_CATEGORY_ID

Revision 1.10  2000/08/15 08:43:41  kzajicek
Fixed spelling error in constant name

Revision 1.9  2000/08/03 12:49:22  kzajicek
English editing

Revision 1.8  2000/08/03 12:34:27  honzam
Default values for new slice defined.

Revision 1.7  2000/07/27 18:17:21  kzajicek
Added superadmin settings in User/Group management

Revision 1.6  2000/07/27 13:23:58  kzajicek
Language correction

Revision 1.5  2000/07/17 13:40:11  kzajicek
Alert box when no input category selected

Revision 1.4  2000/07/17 12:29:56  kzajicek
Language changes

Revision 1.3  2000/07/12 11:06:26  kzajicek
names of image upload variables were a bit confusing

Revision 1.2  2000/07/03 15:00:14  honzam
Five table admin interface. 'New slice expiry date bug' fixed.

Revision 1.1.1.1  2000/06/21 18:40:33  madebeer
reimport tree , 2nd try - code works, tricky to install

Revision 1.1.1.1  2000/06/12 21:50:19  madebeer
Initial upload.  Code works, tricky to install. Copyright, GPL notice there.

Revision 1.12  2000/06/12 19:58:35  madebeer
Added copyright (APC) notice to all .inc and .php3 files that have an $Id

Revision 1.11  2000/06/09 15:14:11  honzama
New configurable admin interface

Revision 1.10  2000/04/24 16:50:34  honzama
New usermanagement interface.

Revision 1.9  2000/03/29 15:54:47  honzama
Better Netscape Navigator javascript support, new direct feeding support, minor changes in texts and look.

Revision 1.8  2000/03/22 09:38:39  madebeer
perm_mysql improvements
Id and Log added to all .php3 and .inc files
system for config-ecn.inc and config-igc.inc both called from
config.inc

*/
?>
