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

# script for MySQL database update

# this script updates the database to last structure, create all tables, ...
# can be used for upgrade from apc-aa v. >= 1.5 or for create new database


# need config.php3 to set db access, and phplib, and probably other stuff
$AA_INC_PATH = "/usr/local/httpd/htdocs/apc-aa/include/"; 
#$AA_INC_PATH = "/home/groups/a/ap/apc-aa/htdocs/apc-aa/include/"; 

require $GLOBALS[AA_INC_PATH]."config.php3";

require $GLOBALS[AA_INC_PATH]."locsess.php3";   # DB_AA definition
require $GLOBALS[AA_INC_PATH]."util.php3";
require $GLOBALS[AA_INC_PATH]."formutil.php3";

# init used objects
$db = new DB_AA;
$db2 = new DB_AA;
$err["Init"] = "";          // error array (Init - just for initializing variable


function IsPaired($field, $fld_array) {
  reset( $fld_array );
  while( list( ,$fld_info) = each( $fld_array ) ) {   # copy all tables
    if( $fld_info[name] == $field )
      return true;
  }
  return false;
}      

# --------------- create temporary tables SQLs ---
$tablelist = array("active_sessions", 
                   "offline", 
                   "relation", 
                   "constant", 
                   "content", 
                   "db_sequence", 
                   "email_auto_user", 
                   "email_notify", 
                   "feedmap", 
                   "feedperms", 
                   "feeds", 
                   "field", 
                   "groups", 
                   "item", 
                   "log", 
                   "membership", 
                   "pagecache", 
                   "perms", 
                   "slice", 
                   "slice_owner", 
                   "subscriptions", 
                   "users", 
                   "view");

# this script copies data from old tables to temp tables, 
# and then replaces the old tables with the temp tables.
# if an old table is 'missing' it will cause an error, 
# so here, we create missing old tables.

$SQL_create_new_tables[] = "
  CREATE TABLE IF NOT EXISTS feedmap (
     from_slice_id char(16) NOT NULL,
     from_field_id char(16) NOT NULL,
     to_slice_id char(16) NOT NULL,
     to_field_id char(16) NOT NULL,
     flag int(11),
     value mediumtext,
     KEY from_slice_id (from_slice_id, to_slice_id)
  )";

$SQL_create_new_tables[] = "
  CREATE TABLE IF NOT EXISTS view (
     id int(10) unsigned NOT NULL auto_increment,
     slice_id varchar(16) NOT NULL,
     name varchar(50),                
     type varchar(10),                
     before text,
     even text,
     odd text,
     even_odd_differ tinyint unsigned,
     after text,
     remove_string text,
     group_title text,
     order1 varchar(16),
     o1_direction tinyint unsigned,
     order2 varchar(16),
     o2_direction tinyint unsigned,
     group_by1 varchar(16),
     g1_direction tinyint unsigned,
     group_by2 varchar(16),
     g2_direction tinyint unsigned,
     cond1field varchar(16),
     cond1op varchar(10),
     cond1cond varchar(255),
     cond2field varchar(16),
     cond2op varchar(10),
     cond2cond varchar(255),
     cond3field varchar(16),
     cond3op varchar(10),
     cond3cond varchar(255),
     listlen int(10) unsigned,
     scroller tinyint unsigned,
     selected_item tinyint unsigned,
     modification int(10) unsigned,
     parameter varchar(255),
     img1 varchar(255),
     img2 varchar(255),
     img3 varchar(255),
     img4 varchar(255),
     flag int(10) unsigned,
     aditional text,
     PRIMARY KEY (id),
     KEY slice_id (slice_id)
  )";

$SQL_create_tmp_tables[] = "
  CREATE TABLE tmp_active_sessions (
     sid varchar(32) NOT NULL,
     name varchar(32) NOT NULL,
     val text,
     changed varchar(14) NOT NULL,
     PRIMARY KEY (name, sid),
     KEY changed (changed)
  )";

$SQL_create_tmp_tables[] = "
  CREATE TABLE tmp_offline (
     id char(16) NOT NULL,
     digest char(32) NOT NULL,
     flag int,
     PRIMARY KEY (id),
     KEY digest (digest)
  )";
  
$SQL_create_tmp_tables[] = "
  CREATE TABLE tmp_relation (
     source_id char(16) NOT NULL,
     destination_id char(32) NOT NULL,
     flag int,
     KEY source_id (source_id),
     KEY destination_id (destination_id)
  )";
  
$SQL_create_tmp_tables[] = "
  CREATE TABLE tmp_constant (
     id char(16) NOT NULL,
     group_id char(16) NOT NULL,
     name char(150) NOT NULL,
     value char(255) NOT NULL,
     class char(16),
     pri smallint(5) DEFAULT '100' NOT NULL,
     PRIMARY KEY (id),
     KEY group_id (group_id)
  )";
  
$SQL_create_tmp_tables[] = "
  CREATE TABLE tmp_content (
     item_id varchar(16) NOT NULL,
     field_id varchar(16) NOT NULL,
     number bigint(20),
     text mediumtext,
     flag smallint(6),
     KEY slice_id (item_id, field_id)
  )";
  
$SQL_create_tmp_tables[] = "
  CREATE TABLE tmp_db_sequence (
     seq_name varchar(127) NOT NULL,
     nextid int(10) unsigned DEFAULT '0' NOT NULL,
     PRIMARY KEY (seq_name)
  )";
  
$SQL_create_tmp_tables[] = "
  CREATE TABLE tmp_email_auto_user (
     uid char(50) NOT NULL,
     creation_time bigint(20) DEFAULT '0' NOT NULL,
     last_change bigint(20) DEFAULT '0' NOT NULL,
     clear_pw char(40),
     confirmed smallint(5) DEFAULT '0' NOT NULL,
     confirm_key char(16),
     PRIMARY KEY (uid)
  )";
  
$SQL_create_tmp_tables[] = "
  CREATE TABLE tmp_email_notify (
     slice_id char(16) NOT NULL,
     uid char(60) NOT NULL,
     function smallint(5) DEFAULT '0' NOT NULL,
     PRIMARY KEY (slice_id, uid, function),
     KEY slice_id (slice_id)
  )";
  
$SQL_create_tmp_tables[] = "
  CREATE TABLE tmp_feedmap (
     from_slice_id char(16) NOT NULL,
     from_field_id char(16) NOT NULL,
     to_slice_id char(16) NOT NULL,
     to_field_id char(16) NOT NULL,
     flag int(11),
     value mediumtext,
     KEY from_slice_id (from_slice_id, to_slice_id)
  )";
  
$SQL_create_tmp_tables[] = "
  CREATE TABLE tmp_feedperms (
     from_id varchar(16) NOT NULL,
     to_id varchar(16) NOT NULL,
     flag int(11)
  )";
  
$SQL_create_tmp_tables[] = "
  CREATE TABLE tmp_feeds (
     from_id varchar(16) NOT NULL,
     to_id varchar(16) NOT NULL,
     category_id varchar(16),
     all_categories smallint(5),
     to_approved smallint(5),
     to_category_id varchar(16),
     KEY from_id (from_id)
  )";
  
$SQL_create_tmp_tables[] = "
  CREATE TABLE tmp_field (
     id varchar(16) NOT NULL,
     type varchar(16) NOT NULL,
     slice_id varchar(16) NOT NULL,
     name varchar(255) NOT NULL,
     input_pri smallint(5) DEFAULT '100' NOT NULL,
     input_help varchar(255),
     input_morehlp text,
     input_default mediumtext,
     required smallint(5),
     feed smallint(5),              
     multiple smallint(5),
     input_show_func varchar(255),
     content_id varchar(16),
     search_pri smallint(5) DEFAULT '100' NOT NULL,
     search_type varchar(16),
     search_help varchar(255),
     search_before text,
     search_more_help text,
     search_show smallint(5),
     search_ft_show smallint(5),
     search_ft_default smallint(5),
     alias1 varchar(10),
     alias1_func varchar(255),
     alias1_help varchar(255),
     alias2 varchar(10),
     alias2_func varchar(255),
     alias2_help varchar(255),
     alias3 varchar(10),
     alias3_func varchar(255),
     alias3_help varchar(255),
     input_before text,
     aditional text,
     content_edit smallint(5),
     html_default smallint(5),
     html_show smallint(5),
     in_item_tbl varchar(16),
     input_validate varchar(16) NOT NULL,
     input_insert_func varchar(255) NOT NULL,
     input_show smallint(5),
     text_stored smallint(5) DEFAULT '1',
     KEY slice_id (slice_id, id)
  )";
  
$SQL_create_tmp_tables[] = "
  CREATE TABLE tmp_groups (
     name varchar(32) NOT NULL,
     description varchar(255) NOT NULL,
     PRIMARY KEY (name)
  )";
  
$SQL_create_tmp_tables[] = "
  CREATE TABLE tmp_item (
     id char(16) NOT NULL,
     short_id int(11) NOT NULL auto_increment,
     slice_id char(16) NOT NULL,
     status_code smallint(5) DEFAULT '0' NOT NULL,
     post_date bigint(20) DEFAULT '0' NOT NULL,
     publish_date bigint(20),
     expiry_date bigint(20),
     highlight smallint(5),
     posted_by char(60),
     edited_by char(60),
     last_edit bigint(20),
     display_count int(11) DEFAULT '0' NOT NULL,
     flags char(30),
     PRIMARY KEY (id),
     KEY short_id (short_id)
  )";
  
$SQL_create_tmp_tables[] = "
  CREATE TABLE tmp_log (
     id int(11) DEFAULT '0' NOT NULL auto_increment,
     time bigint(20) DEFAULT '0' NOT NULL,
     user char(60) NOT NULL,
     type char(10) NOT NULL,
     params char(128),
     PRIMARY KEY (id),
     KEY time (time)
  )";
  
$SQL_create_tmp_tables[] = "
  CREATE TABLE tmp_membership (
     groupid int(11) DEFAULT '0' NOT NULL,
     memberid int(11) DEFAULT '0' NOT NULL,
     last_mod timestamp(14),
     PRIMARY KEY (groupid, memberid),
     KEY memberid (memberid)
  )";
  
$SQL_create_tmp_tables[] = "
  CREATE TABLE tmp_pagecache (
     id varchar(32) NOT NULL,
     str2find text,
     content mediumtext,
     stored bigint NOT NULL,
     flag int,
     PRIMARY KEY (id),
     KEY stored (stored)
  )";
  
$SQL_create_tmp_tables[] = "
  CREATE TABLE tmp_perms (
     object_type char(30) NOT NULL,
     objectid char(32) NOT NULL,
     userid int(11) DEFAULT '0' NOT NULL,
     perm char(32) NOT NULL,
     last_mod timestamp(14),
     PRIMARY KEY (objectid, userid, object_type),
     KEY userid (userid)
  )";
  
$SQL_create_tmp_tables[] = "
  CREATE TABLE tmp_slice (
     id varchar(16) NOT NULL,
     name varchar(100) NOT NULL,
     owner varchar(16),
     deleted smallint(5),
     created_by varchar(255),
     created_at bigint(20),
     export_to_all smallint(5),
     type varchar(16),
     template smallint(5),
     fulltext_format_top text,
     fulltext_format text,
     fulltext_format_bottom text,
     odd_row_format text,
     even_row_format text,
     even_odd_differ smallint(5),
     compact_top text,
     compact_bottom text,
     category_top text,
     category_format text,
     category_bottom text,
     category_sort smallint(5),
     config text NOT NULL,
     slice_url varchar(255),
     d_expiry_limit smallint(5),
     d_listlen smallint(5),
     lang_file varchar(50),
     fulltext_remove text,
     compact_remove text,
     email_sub_enable smallint(5),
     exclude_from_dir smallint(5),
     notify_sh_offer mediumtext,
     notify_sh_accept mediumtext,
     notify_sh_remove mediumtext,
     notify_holding_item mediumtext,
     admin_format_top text,
     admin_format text,
     admin_format_bottom text,
     admin_remove text,
     permit_anonymous_post smallint(5),
     permit_offline_fill smallint(5),  
     aditional text,
     flag int DEFAULT '0' NOT NULL,    
     PRIMARY KEY (id)
  )";
  
$SQL_create_tmp_tables[] = "
  CREATE TABLE tmp_slice_owner (
     id char(16) NOT NULL,
     name char(80) NOT NULL,
     email char(80) NOT NULL,
     PRIMARY KEY (id)
  )";
  
$SQL_create_tmp_tables[] = "
  CREATE TABLE tmp_subscriptions (
     uid char(50) NOT NULL,
     category char(16),
     content_type char(16),
     slice_owner char(16),
     frequency smallint(5) DEFAULT '0' NOT NULL,
     last_post bigint(20) DEFAULT '0' NOT NULL,
     KEY uid (uid, frequency)
  )";
  
$SQL_create_tmp_tables[] = "
  CREATE TABLE tmp_users (
     id int(11) DEFAULT '0' NOT NULL auto_increment,
     type char(10) NOT NULL,
     password char(30) NOT NULL,
     uid char(40) NOT NULL,
     mail char(40) NOT NULL,
     name char(80) NOT NULL,
     description char(255) NOT NULL,
     givenname char(40) NOT NULL,
     sn char(40) NOT NULL,
     last_mod timestamp(14),
     PRIMARY KEY (id),
     KEY type (type),
     KEY mail (mail),
     KEY name (name),
     KEY sn (sn)
  )";
  
$SQL_create_tmp_tables[] = "
  CREATE TABLE tmp_view (
     id int(10) unsigned NOT NULL auto_increment,
     slice_id varchar(16) NOT NULL,
     name varchar(50),                
     type varchar(10),                
     before text,
     even text,
     odd text,
     even_odd_differ tinyint unsigned,
     after text,
     remove_string text,
     group_title text,
     order1 varchar(16),
     o1_direction tinyint unsigned,
     order2 varchar(16),
     o2_direction tinyint unsigned,
     group_by1 varchar(16),
     g1_direction tinyint unsigned,
     group_by2 varchar(16),
     g2_direction tinyint unsigned,
     cond1field varchar(16),
     cond1op varchar(10),
     cond1cond varchar(255),
     cond2field varchar(16),
     cond2op varchar(10),
     cond2cond varchar(255),
     cond3field varchar(16),
     cond3op varchar(10),
     cond3cond varchar(255),
     listlen int(10) unsigned,
     scroller tinyint unsigned,
     selected_item tinyint unsigned,
     modification int(10) unsigned,
     parameter varchar(255),
     img1 varchar(255),
     img2 varchar(255),
     img3 varchar(255),
     img4 varchar(255),
     flag int(10) unsigned,
     aditional text,
     PRIMARY KEY (id),
     KEY slice_id (slice_id)
  )";

$SQL_apc_categ[] = "DELETE FROM constant WHERE group_id = 'lt_apcCategories'";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined100', 'lt_apcCategories', 'Internet & ICT', 'Internet & ICT', '', '1000')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined101', 'lt_apcCategories', 'Internet & ICT - Free software & Open Source', 'Internet & ICT - Free software & Open Source', '', '1100')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined102', 'lt_apcCategories', 'Internet & ICT - Access', 'Internet & ICT - Access', '', '1200')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined103', 'lt_apcCategories', 'Internet & ICT - Connectivity', 'Internet & ICT - Connectivity', '', '1300')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined104', 'lt_apcCategories', 'Internet & ICT - Women and ICT', 'Internet & ICT - Women and ICT', '', '1400')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined105', 'lt_apcCategories', 'Internet & ICT - Rights', 'Internet & ICT - Rights', '', '1500')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined106', 'lt_apcCategories', 'Internet & ICT - Governance', 'Internet & ICT - Governance', '', '1600')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined107', 'lt_apcCategories', 'Development', 'Development', '', '2000')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined108', 'lt_apcCategories', 'Development - Resources', 'Development - Resources', '', '2100')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined109', 'lt_apcCategories', 'Development - Structural adjustment', 'Development - Structural adjustment', '', '2200')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined110', 'lt_apcCategories', 'Development - Sustainability', 'Development - Sustainability', '', '2300')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined111', 'lt_apcCategories', 'News and media', 'News and media', '', '3000')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined112', 'lt_apcCategories', 'News and media - Alternative', 'News and media - Alternative', '', '3100')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined113', 'lt_apcCategories', 'News and media - Internet', 'News and media - Internet', '', '3200')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined114', 'lt_apcCategories', 'News and media - Training', 'News and media - Training', '', '3300')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined115', 'lt_apcCategories', 'News and media - Traditional', 'News and media - Traditional', '', '3400')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined116', 'lt_apcCategories', 'Environment', 'Environment', '', '4000')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined117', 'lt_apcCategories', 'Environment - Agriculture', 'Environment - Agriculture', '', '4100')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined118', 'lt_apcCategories', 'Environment - Animal rights/protection', 'Environment - Animal rights/protection', '', '4200')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined119', 'lt_apcCategories', 'Environment - Climate', 'Environment - Climate', '', '4300')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined120', 'lt_apcCategories', 'Environment - Biodiversity/conservetion', 'Environment - Biodiversity/conservetion', '', '4400')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined121', 'lt_apcCategories', 'Environment - Energy', 'Environment - Energy', '', '4500')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined122', 'lt_apcCategories', 'Environment - Campaigns', 'Environment - Campaigns', '', '4550')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined123', 'lt_apcCategories', 'Environment - Legislation', 'Environment - Legislation', '', '4600')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined124', 'lt_apcCategories', 'Environment - Genetics', 'Environment - Genetics', '', '4650')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined125', 'lt_apcCategories', 'Environment - Natural resources', 'Environment - Natural resources', '', '4700')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined126', 'lt_apcCategories', 'Environment - Rural development', 'Environment - Rural development', '', '5750')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined127', 'lt_apcCategories', 'Environment - Transport', 'Environment - Transport', '', '4800')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined128', 'lt_apcCategories', 'Environment - Urban ecology', 'Environment - Urban ecology', '', '4850')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined129', 'lt_apcCategories', 'Environment - Pollution & waste', 'Environment - Pollution & waste', '', '4900')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined130', 'lt_apcCategories', 'NGOs', 'NGOs', '', '5000')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined131', 'lt_apcCategories', 'NGOs - Fundraising', 'NGOs - Fundraising', '', '5100')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined132', 'lt_apcCategories', 'NGOs - Funding agencies', 'NGOs - Funding agencies', '', '5200')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined133', 'lt_apcCategories', 'NGOs - Grants/scholarships', 'NGOs - Grants/scholarships', '', '5300')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined134', 'lt_apcCategories', 'NGOs - Jobs', 'NGOs - Jobs', '', '5400')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined135', 'lt_apcCategories', 'NGOs - Management', 'NGOs - Management', '', '5500')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined136', 'lt_apcCategories', 'NGOs - Volunteers', 'NGOs - Volunteers', '', '5600')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined137', 'lt_apcCategories', 'Society', 'Society', '', '6000')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined138', 'lt_apcCategories', 'Society - Charities', 'Society - Charities', '', '6100')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined139', 'lt_apcCategories', 'Society - Community', 'Society - Community', '', '6200')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined140', 'lt_apcCategories', 'Society - Crime & rehabilitation', 'Society - Crime & rehabilitation', '', '6300')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined141', 'lt_apcCategories', 'Society - Disabilities', 'Society - Disabilities', '', '6400')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined142', 'lt_apcCategories', 'Society - Drugs', 'Society - Drugs', '', '6500')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined143', 'lt_apcCategories', 'Society - Ethical business', 'Society - Ethical business', '', '6600')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined144', 'lt_apcCategories', 'Society - Health', 'Society - Health', '', '6700')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined145', 'lt_apcCategories', 'Society - Law and legislation', 'Society - Law and legislation', '', '6750')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined146', 'lt_apcCategories', 'Society - Migration', 'Society - Migration', '', '6800')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined147', 'lt_apcCategories', 'Society - Sexuality', 'Society - Sexuality', '', '6850')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined148', 'lt_apcCategories', 'Society - Social services and welfare', 'Society - Social services and welfare', '', '6900')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined149', 'lt_apcCategories', 'Economy & Work', 'Economy & Work', '', '7000')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined150', 'lt_apcCategories', 'Economy & Work - Informal Sector', 'Economy & Work - Informal Sector', '', '7100')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined151', 'lt_apcCategories', 'Economy & Work - Labour', 'Economy & Work - Labour', '', '7200')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined152', 'lt_apcCategories', 'Culture', 'Culture', '', '8000')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined153', 'lt_apcCategories', 'Culture - Arts and literature', 'Culture - Arts and literature', '', '8100')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined154', 'lt_apcCategories', 'Culture - Heritage', 'Culture - Heritage', '', '8200')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined155', 'lt_apcCategories', 'Culture - Philosophy', 'Culture - Philosophy', '', '8300')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined156', 'lt_apcCategories', 'Culture - Religion', 'Culture - Religion', '', '8400')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined157', 'lt_apcCategories', 'Culture - Ethics', 'Culture - Ethics', '', '8500')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined158', 'lt_apcCategories', 'Culture - Leisure', 'Culture - Leisure', '', '8600')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined159', 'lt_apcCategories', 'Human rights', 'Human rights', '', '9000')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined160', 'lt_apcCategories', 'Human rights - Consumer Protection', 'Human rights - Consumer Protection', '', '9100')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined161', 'lt_apcCategories', 'Human rights - Democracy', 'Human rights - Democracy', '', '9200')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined162', 'lt_apcCategories', 'Human rights - Minorities', 'Human rights - Minorities', '', '9300')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined163', 'lt_apcCategories', 'Human rights - Peace', 'Human rights - Peace', '', '9400')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined164', 'lt_apcCategories', 'Education', 'Education', '', '10000')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined165', 'lt_apcCategories', 'Education - Distance learning', 'Education - Distance learning', '', '10100')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined166', 'lt_apcCategories', 'Education - Non-formal education', 'Education - Non-formal education', '', '10200')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined167', 'lt_apcCategories', 'Education - Schools', 'Education - Schools', '', '10300')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined168', 'lt_apcCategories', 'Politics & Government', 'Politics & Government', '', '11000')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined169', 'lt_apcCategories', 'Politics & Government - Internet', 'Politics & Government - Internet', '', '11100')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined170', 'lt_apcCategories', 'Politics & Government - Local', 'Politics & Government - Local', '', '11200')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined171', 'lt_apcCategories', 'Politics & Government - Policies', 'Politics & Government - Policies', '', '11300')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined172', 'lt_apcCategories', 'Politics & Government - Administration', 'Politics & Government - Administration', '', '11400')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined173', 'lt_apcCategories', 'People', 'People', '', '12000')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined174', 'lt_apcCategories', 'People - Children', 'People - Children', '', '12100')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined175', 'lt_apcCategories', 'People - Adolescents/teenagers', 'People - Adolescents/teenagers', '', '12200')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined176', 'lt_apcCategories', 'People - Gender', 'People - Gender', '', '12300')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined177', 'lt_apcCategories', 'People - Older people', 'People - Older people', '', '12400')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined178', 'lt_apcCategories', 'People - Family', 'People - Family', '', '12500')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined179', 'lt_apcCategories', 'World', 'World', '', '13000')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined180', 'lt_apcCategories', 'World - Globalization', 'World - Globalization', '', '13100')";
$SQL_apc_categ[] = "INSERT INTO constant VALUES( 'AA-predefined181', 'lt_apcCategories', 'World - Debt', 'World - Debt', '', '13200')";
$SQL_apc_categ[] = "REPLACE INTO constant VALUES( 'AA-predefined056', 'lt_groupNames', 'APC-wide Categories', 'lt_apcCategories', '', '1000')";
 
$SQL_constants[] = "DELETE FROM constant WHERE group_id IN ('lt_codepages', 'lt_languages', 'AA_Core_Bins....')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined000', 'lt_codepages', 'iso8859-1', 'iso8859-1', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined001', 'lt_codepages', 'iso8859-2', 'iso8859-2', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined002', 'lt_codepages', 'windows-1250', 'windows-1250', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined003', 'lt_codepages', 'windows-1253', 'windows-1253', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined004', 'lt_codepages', 'windows-1254', 'windows-1254', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined005', 'lt_codepages', 'koi8-r', 'koi8-r', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined006', 'lt_codepages', 'ISO-8859-8', 'ISO-8859-8', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined007', 'lt_codepages', 'windows-1258', 'windows-1258', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined008', 'lt_languages', 'Afrikaans', 'AF', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined009', 'lt_languages', 'Arabic', 'AR', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined010', 'lt_languages', 'Basque', 'EU', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined011', 'lt_languages', 'Byelorussian', 'BE', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined012', 'lt_languages', 'Bulgarian', 'BG', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined013', 'lt_languages', 'Catalan', 'CA', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined014', 'lt_languages', 'Chinese (ZH-CN)', 'ZH', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined015', 'lt_languages', 'Chinese', 'ZH-TW', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined016', 'lt_languages', 'Croatian', 'HR', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined017', 'lt_languages', 'Czech', 'CS', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined018', 'lt_languages', 'Danish', 'DA', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined019', 'lt_languages', 'Dutch', 'NL', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined020', 'lt_languages', 'English', 'EN-GB', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined021', 'lt_languages', 'English (EN-US)', 'EN', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined022', 'lt_languages', 'Estonian', 'ET', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined023', 'lt_languages', 'Faeroese', 'FO', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined024', 'lt_languages', 'Finnish', 'FI', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined025', 'lt_languages', 'French (FR-FR)', 'FR', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined026', 'lt_languages', 'French', 'FR-CA', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined027', 'lt_languages', 'German', 'DE', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined028', 'lt_languages', 'Greek', 'EL', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined029', 'lt_languages', 'Hebrew (IW)', 'HE', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined030', 'lt_languages', 'Hungarian', 'HU', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined031', 'lt_languages', 'Icelandic', 'IS', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined032', 'lt_languages', 'Indonesian (IN)', 'ID', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined033', 'lt_languages', 'Italian', 'IT', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined034', 'lt_languages', 'Japanese', 'JA', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined035', 'lt_languages', 'Korean', 'KO', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined036', 'lt_languages', 'Latvian', 'LV', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined037', 'lt_languages', 'Lithuanian', 'LT', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined038', 'lt_languages', 'Neutral', 'NEUTRAL', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined039', 'lt_languages', 'Norwegian', 'NO', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined040', 'lt_languages', 'Polish', 'PL', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined041', 'lt_languages', 'Portuguese', 'PT', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined042', 'lt_languages', 'Portuguese', 'PT-BR', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined043', 'lt_languages', 'Romanian', 'RO', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined044', 'lt_languages', 'Russian', 'RU', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined045', 'lt_languages', 'Serbian', 'SR', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined046', 'lt_languages', 'Slovak', 'SK', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined047', 'lt_languages', 'Slovenian', 'SL', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined048', 'lt_languages', 'Spanish (ES-ES)', 'ES', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined049', 'lt_languages', 'Swedish', 'SV', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined050', 'lt_languages', 'Thai', 'TH', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined051', 'lt_languages', 'Turkish', 'TR', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined052', 'lt_languages', 'Ukrainian', 'UK', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined053', 'lt_languages', 'Vietnamese', 'VI', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined058', 'AA_Core_Bins....', 'Approved', '1', '', '100')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined059', 'AA_Core_Bins....', 'Holding Bin', '2', '', '200')";
$SQL_constants[] = "INSERT INTO constant VALUES( 'AA-predefined060', 'AA_Core_Bins....', 'Trash Bin', '3', '', '300')";
$SQL_constants[] = "REPLACE INTO constant VALUES( 'AA-predefined054', 'lt_groupNames', 'Code Pages', 'lt_codepages', '', '0')";
$SQL_constants[] = "REPLACE INTO constant VALUES( 'AA-predefined055', 'lt_groupNames', 'Languages Shortcuts', 'lt_languages', '', '1000')";
$SQL_constants[] = "REPLACE INTO constant VALUES( 'AA-predefined057', 'lt_groupNames', 'AA Core Bins', 'AA_Core_Bins....', '', '10000')";

$SQL_aacore[] = "DELETE FROM field WHERE slice_id='AA_Core_Fields..'";
$SQL_aacore[] = "REPLACE INTO slice_owner VALUES( 'AA_Core.........', 'Action Aplications System', 'technical@ecn.cz')";
$SQL_aacore[] = "REPLACE INTO slice VALUES( 'AA_Core_Fields..', 'Action Aplication Core', 'AA_Core_Fields..', '0', '', '975157733', '1', 'AA_Core_Fields..', '0', '', '', '','', '', '0', '', '', '', '', '', '1', '', 'http://aa.ecn.cz', '5000', '10000', 'en_news_lang.php3', '()', '()', '1', '0', '', '', '', '', '', '', '', '', '', '', '', '0')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'headline', '', 'AA_Core_Fields..', 'Headline', '100', 'Headline', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'abstract', '', 'AA_Core_Fields..', 'Abstract', '189', 'Abstract', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'txt:8', '', '100', '', '', '', '', '0', '1', '1', '_#UNDEFINE', 'f_t', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '1', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'full_text', '', 'AA_Core_Fields..', 'Fulltext', '300', 'Fulltext', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'txt:8', '', '100', '', '', '', '', '0', '1', '1', '_#UNDEFINE', 'f_t', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '1', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'hl_href', '', 'AA_Core_Fields..', 'Headline URL', '1655', 'Link for the headline (for external links)', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_f:link_only.......', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'url', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'link_only', '', 'AA_Core_Fields..', 'External item', '1755', 'Use External link instead of fulltext?', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'chb', '', '100', '', '', '', '', '0', '0', '1', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'bool', 'boo', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'place', '', 'AA_Core_Fields..', 'Locality', '2155', 'Item locality', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'source', '', 'AA_Core_Fields..', 'Source', '1955', 'Source of the item', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'source_href', '', 'AA_Core_Fields..', 'Source URL', '2055', 'URL of the source', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_s:javascript: window.alert(\'No source url specified\')', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'url', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'lang_code', '', 'AA_Core_Fields..', 'Language Code', '1700', 'Code of used language', 'http://aa.ecn.cz/aa/doc/help.html', 'txt:EN', '0', '0', '0', 'sel:lt_languages', '', '100', '', '', '', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'cp_code', '', 'AA_Core_Fields..', 'Code Page', '1800', 'Language Code Page', 'http://aa.ecn.cz/aa/doc/help.html', 'txt:iso8859-1', '0', '0', '0', 'sel:lt_codepages', '', '100', '', '', '', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'category', '', 'AA_Core_Fields..', 'Category', '1000', 'Category', 'http://aa.ecn.cz/aa/doc/help.html', 'txt:', '0', '0', '0', 'sel:lt_apcCategories', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'img_src', '', 'AA_Core_Fields..', 'Image URL', '2055', 'URL of the image', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#UNDEFINE', 'f_i', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'url', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'img_width', '', 'AA_Core_Fields..', 'Image width', '2455', 'Width of image (like: 100, 50%)', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#UNDEFINE', 'f_w', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'img_height', '', 'AA_Core_Fields..', 'Image height', '2555', 'Height of image (like: 100, 50%)', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#UNDEFINE', 'f_g', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'e_posted_by', '', 'AA_Core_Fields..', 'Author`s e-mail', '2255', 'E-mail to author', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'email', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'created_by', '', 'AA_Core_Fields..', 'Created By', '2355', 'Identification of creator', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'nul', '', '100', '', '', '', '', '0', '0', '0', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'uid', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'edit_note', '', 'AA_Core_Fields..', 'Editor`s note', '2355', 'There you can write your note (not displayed on the web)', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'txt', '', '100', '', '', '', '', '0', '0', '0', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'img_upload', '', 'AA_Core_Fields..', 'Image upload', '2222', 'Select Image for upload', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fil:image/*', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'fil', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'lang_code', '', 'AA_Core_Fields..', 'Language Code', '1700', 'Code of used language', 'http://aa.ecn.cz/aa/doc/help.html', 'txt:EN', '0', '0', '0', 'sel:lt_languages', '', '100', '', '', '', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'source_desc', '', 'AA_Core_Fields..', 'Source description', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'source_addr', '', 'AA_Core_Fields..', 'Source address', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'source_city', '', 'AA_Core_Fields..', 'Source city', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'source_prov', '', 'AA_Core_Fields..', 'Source province', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'source_cntry', '', 'AA_Core_Fields..', 'Source country', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'time', '', 'AA_Core_Fields..', 'Time', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '0')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'con_name', '', 'AA_Core_Fields..', 'Contact name', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'con_email', '', 'AA_Core_Fields..', 'Contact e-mail', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'con_phone', '', 'AA_Core_Fields..', 'Contact phone', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'con_fax', '', 'AA_Core_Fields..', 'Contact fax', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'loc_name', '', 'AA_Core_Fields..', 'Location name', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'loc_address', '', 'AA_Core_Fields..', 'Location address', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'loc_city', '', 'AA_Core_Fields..', 'Location city', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'loc_prov', '', 'AA_Core_Fields..', 'Location province', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'loc_cntry', '', 'AA_Core_Fields..', 'Location country', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'start_date', '', 'AA_Core_Fields..', 'Start date', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'now', '1', '0', '0', 'dte:1:10:1', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_d:m/d/Y', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'date', 'dte', '1', '0')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'end_date', '', 'AA_Core_Fields..', 'End date', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'now', '1', '0', '0', 'dte:1:10:1', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_d:m/d/Y', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'date', 'dte', '1', '0')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'keywords', '', 'AA_Core_Fields..', 'Keywords', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'subtitle', '', 'AA_Core_Fields..', 'Subtitle', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'year', '', 'AA_Core_Fields..', 'Year', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'number', '', 'AA_Core_Fields..', 'Number', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'number', 'num', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'page', '', 'AA_Core_Fields..', 'Page', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'number', 'num', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'price', '', 'AA_Core_Fields..', 'Price', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'number', 'num', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'organization', '', 'AA_Core_Fields..', 'Organization', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'file', '', 'AA_Core_Fields..', 'File', '2222', 'Select file for upload', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fil:* /*', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'fil', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'text', '', 'AA_Core_Fields..', 'Text', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'unspecified', '', 'AA_Core_Fields..', 'Unspecified', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_aacore[] = "INSERT INTO field VALUES( 'url', '', 'AA_Core_Fields..', 'URL', '2055', 'Internet URL address', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#UNDEFINE', 'f_i', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'url', 'qte', '1', '1')";

$SQL_templates[] = "DELETE FROM field WHERE slice_id='News_EN_tmpl....'";
$SQL_templates[] = "REPLACE INTO slice VALUES( 'News_EN_tmpl....', 'News (EN) Template', 'AA_Core.........', '0', '', '975157733', '1', 'News_EN_tmpl....', '1', '', '<BR><FONT SIZE=+2 COLOR=blue>_#HEADLINE</FONT> <BR><B>_#PUB_DATE</B> <BR><img src=\"_#IMAGESRC\" width=\"_#IMGWIDTH\" height=\"_#IMG_HGHT\">_#FULLTEXT ', '','<font face=Arial color=#808080 size=-2>_#PUB_DATE - </font><font color=#FF0000><strong><a href=_#HDLN_URL>_#HEADLINE</a></strong></font><font color=#808080 size=-1><br>_#PLACE###(_#LINK_SRC) - </font><font color=black size=-1>_#ABSTRACT<br></font><br>', '', '0', '<br>', '<br>', '', '<p>_#CATEGORY</p>', '', '1', '', 'http://aa.ecn.cz', '5000', '10000', 'en_news_lang.php3', '()', '()', '1', '0', '', '', '', '', '<tr class=tablename><td width=30>&nbsp;</td><td>Click on Headline to Edit</td><td>Date</td></tr>', '<tr class=tabtxt><td width=30><input type=checkbox name=\"chb[x_#ITEM_ID#]\" value=\"\"></td><td><a href=\"_#EDITITEM\">_#HEADLINE</a></td><td>_#PUB_DATE</td></tr>', '', '', '1', '1', '', '0')";
$SQL_templates[] = "INSERT INTO field VALUES( 'abstract........', '', 'News_EN_tmpl....', 'Abstract', '150', 'Abstract', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'txt:8', '', '100', '', '', '', '', '0', '1', '1', '_#ABSTRACT', 'f_t', 'alias for abstract', '', '', '', '', '', '', '', '', '0', '0', '1', '', 'text', 'qte', '1', '1')";
$SQL_templates[] = "INSERT INTO field VALUES( 'category........', '', 'News_EN_tmpl....', 'Category', '500', 'Category', 'http://aa.ecn.cz/aa/doc/help.html', 'txt:', '0', '0', '0', 'sel:lt_apcCategories', '', '100', '', '', '', '', '1', '1', '1', '_#CATEGORY', 'f_h', 'alias for Item Category', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '0', '1')";
$SQL_templates[] = "INSERT INTO field VALUES( 'cp_code.........', '', 'News_EN_tmpl....', 'Code Page', '1800', 'Language Code Page', 'http://aa.ecn.cz/aa/doc/help.html', 'txt:iso8859-1', '0', '0', '0', 'sel:lt_codepages', '', '100', '', '', '', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '0', '1')";
$SQL_templates[] = "INSERT INTO field VALUES( 'created_by......', '', 'News_EN_tmpl....', 'Author', '470', 'Identification of creator', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#CREATED#', 'f_h', 'alias for Written By', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_templates[] = "INSERT INTO field VALUES( 'edited_by.......', '', 'News_EN_tmpl....', 'Edited by', '5030', 'Identification of last editor', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'nul', '', '100', '', '', '', '', '0', '0', '0', '_#EDITEDBY', 'f_h', 'alias for Last edited By', '', '', '', '', '', '', '', '', '0', '0', '0', 'edited_by', 'text', 'uid', '0', '1')";
$SQL_templates[] = "INSERT INTO field VALUES( 'edit_note.......', '', 'News_EN_tmpl....', 'Editor`s note', '2355', 'There you can write your note (not displayed on the web)', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'txt', '', '100', '', '', '', '', '0', '0', '0', '_#EDITNOTE', 'f_h', 'alias for Editor`s note', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_templates[] = "INSERT INTO field VALUES( 'expiry_date.....', '', 'News_EN_tmpl....', 'Expiry Date', '955', 'Date when the news expires', 'http://aa.ecn.cz/aa/doc/help.html', 'dte:2000', '1', '0', '0', 'dte:1:10:1', '', '100', '', '', '', '', '0', '0', '0', '_#EXP_DATE', 'f_d:m/d/Y', 'alias for Expiry Date', '', '', '', '', '', '', '', '', '0', '0', '0', 'expiry_date', 'date', 'dte', '1', '0')";
$SQL_templates[] = "INSERT INTO field VALUES( 'e_posted_by.....', '', 'News_EN_tmpl....', 'Author`s e-mail', '480', 'E-mail to author', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#E_POSTED', 'f_h', 'alias for Author`s e-mail', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'email', 'qte', '1', '1')";
$SQL_templates[] = "INSERT INTO field VALUES( 'full_text.......', '', 'News_EN_tmpl....', 'Fulltext', '200', 'Fulltext', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'txt:8', '', '100', '', '', '', '', '0', '1', '1', '_#FULLTEXT', 'f_t', 'alias for Fulltext<br>(HTML tags are striped or not depending on HTML formated item setting)', '', '', '', '', '', '', '', '', '0', '0', '1', '', 'text', 'qte', '1', '1')";
$SQL_templates[] = "INSERT INTO field VALUES( 'headline........', '', 'News_EN_tmpl....', 'Headline', '100', 'Headline of the news', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#HEADLINE', 'f_h', 'alias for Item Headline', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_templates[] = "INSERT INTO field VALUES( 'highlight.......', '', 'News_EN_tmpl....', 'Highlight', '450', 'Interesting news - shown on homepage', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'chb', '', '100', '', '', '', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', 'highlight', 'bool', 'boo', '1', '0')";
$SQL_templates[] = "INSERT INTO field VALUES( 'hl_href.........', '', 'News_EN_tmpl....', 'Headline URL', '400', 'Link for the headline (for external links)', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#HDLN_URL', 'f_f:link_only.......', 'alias for News URL<br>(substituted by External news link URL(if External news is checked) or link to Fulltext)<div class=example><em>Example: </em>&lt;a href=_#HDLN_URL&gt;_#HEADLINE&lt;/a&gt;</div>', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'url', 'qte', '1', '1')";
$SQL_templates[] = "INSERT INTO field VALUES( 'img_height......', '', 'News_EN_tmpl....', 'Image height', '2300', 'Height of image (like: 100, 50%)', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#IMG_HGHT', 'f_g', 'alias for Image Height<br>(if no height defined, program tries to remove <em>height=</em> atribute from format string<div class=example><em>Example: </em>&lt;img src=\"_#IMAGESRC\" width=_#IMGWIDTH height=_#IMG_HGHT&gt;</div>', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_templates[] = "INSERT INTO field VALUES( 'img_src.........', '', 'News_EN_tmpl....', 'Image URL', '2100', 'URL of the image', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#IMAGESRC', 'f_i', 'alias for Image URL<br>(if there is no image url defined in database, default url is used instead (see NO_PICTURE_URL constant in en_*_lang.php3 file))<div class=example><em>Example: </em>&lt;img src=\"_#IMAGESRC\"&gt;</div>', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'url', 'qte', '1', '1')";
$SQL_templates[] = "INSERT INTO field VALUES( 'img_width.......', '', 'News_EN_tmpl....', 'Image width', '2200', 'Width of image (like: 100, 50%)', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#IMGWIDTH', 'f_w', 'alias for Image Width<br>(if no width defined, program tries to remove <em>width=</em> atribute from format string<div class=example><em>Example: </em>&lt;img src=\"_#IMAGESRC\" width=_#IMGWIDTH height=_#IMG_HGHT&gt;</div>', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_templates[] = "INSERT INTO field VALUES( 'lang_code.......', '', 'News_EN_tmpl....', 'Language Code', '1700', 'Code of used language', 'http://aa.ecn.cz/aa/doc/help.html', 'txt:EN', '0', '0', '0', 'sel:lt_languages', '', '100', '', '', '', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '0', '1')";
$SQL_templates[] = "INSERT INTO field VALUES( 'last_edit.......', '', 'News_EN_tmpl....', 'Last Edit', '5040', 'Date of last edit', 'http://aa.ecn.cz/aa/doc/help.html', 'now:', '0', '0', '0', 'dte:1:10:1', '', '100', '', '', '', '', '0', '0', '0', '_#LASTEDIT', 'f_d:m/d/Y', 'alias for Last Edit', '', '', '', '', '', '', '', '', '0', '0', '0', 'last_edit', 'date', 'now', '0', '0')";
$SQL_templates[] = "INSERT INTO field VALUES( 'link_only.......', '', 'News_EN_tmpl....', 'External news', '300', 'Use External link instead of fulltext?', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'chb', '', '100', '', '', '', '', '0', '0', '1', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'bool', 'boo', '1', '0')";
$SQL_templates[] = "INSERT INTO field VALUES( 'place...........', '', 'News_EN_tmpl....', 'Locality', '630', 'News locality', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#PLACE###', 'f_h', 'alias for Locality', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_templates[] = "INSERT INTO field VALUES( 'posted_by.......', '', 'News_EN_tmpl....', 'Posted by', '5035', 'Identification of author', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#POSTEDBY', 'f_h', 'alias for Author', '', '', '', '', '', '', '', '', '0', '0', '0', 'posted_by', 'text', 'qte', '0', '1')";
$SQL_templates[] = "INSERT INTO field VALUES( 'post_date.......', '', 'News_EN_tmpl....', 'Post Date', '5005', 'Date of posting this news', 'http://aa.ecn.cz/aa/doc/help.html', 'now:', '1', '0', '0', 'nul', '', '100', '', '', '', '', '0', '0', '0', '_#POSTDATE', 'f_d:m/d/Y', 'alias for Post Date', '', '', '', '', '', '', '', '', '0', '0', '0', 'post_date', 'date', 'now', '0', '0')";
$SQL_templates[] = "INSERT INTO field VALUES( 'publish_date....', '', 'News_EN_tmpl....', 'Publish Date', '900', 'Date when the news will be published', 'http://aa.ecn.cz/aa/doc/help.html', 'now:', '1', '0', '0', 'dte:1:10:1', '', '100', '', '', '', '', '0', '0', '0', '_#PUB_DATE', 'f_d:m/d/Y', 'alias for Publish Date', '', '', '', '', '', '', '', '', '0', '0', '0', 'publish_date', 'date', 'dte', '1', '0')";
$SQL_templates[] = "INSERT INTO field VALUES( 'source..........', '', 'News_EN_tmpl....', 'Source', '600', 'Source of the news', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#SOURCE##', 'f_h', 'alias for Source Name<br>(see _#LINK_SRC for text source link)', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1')";
$SQL_templates[] = "INSERT INTO field VALUES( 'source_href.....', '', 'News_EN_tmpl....', 'Source URL', '610', 'URL of the source', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#SRC_URL#', 'f_s:javascript: window.alert(\'No source url specified\')', 'alias for Source URL<br>(if there is no source url defined in database, default source url is displayed (see ALIAS definition on field setting page))<br>Use _#LINK_SRC for text source link.<div class=example><em>Example: </em>&lt;a href\"_#SRC_URL#\"', '_#LINK_SRC', 'f_l', 'alias for Source Name with link.<br>(substituted by &lt;a href=\"_#SRC_URL#\"&gt;_#SOURCE##&lt;/a&gt; if Source URL defined, otherwise _#SOURCE## only)', '', '', '', '', '', '0', '0', '0', '', 'url', 'qte', '1', '1')";
$SQL_templates[] = "INSERT INTO field VALUES( 'status_code.....', '', 'News_EN_tmpl....', 'Status Code', '5020', 'Select in which bin should the news appear', 'http://aa.ecn.cz/aa/doc/help.html', 'qte:1', '1', '1', '0', 'sel:AA_Core_Bins....', '', '100', '', '', '', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', 'status_code', 'number', 'num', '0', '0')";
$SQL_templates[] = "INSERT INTO field VALUES( 'slice_id........', '', 'News_EN_tmpl....', 'Slice', '5000', 'Internal field - do not change', 'http://aa.ecn.cz/aa/doc/help.html', 'qte:1', '1', '1', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#SLICE_ID', 'f_n:slice_id........', 'alias for id of slice', '', '', '', '', '', '', '', '', '0', '0', '0', 'slice_id', '', 'nul', '0', '1')";
$SQL_templates[] = "INSERT INTO field VALUES( 'display_count...', '', 'News_EN_tmpl....', 'Displayed n Times', '5050', 'Internal field - do not change', 'http://aa.ecn.cz/aa/doc/help.html', 'qte:0', '1', '1', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#DISPL_NO', 'f_h', 'alias for number of displaying of this item', '', '', '', '', '', '', '', '', '0', '0', '0', 'display_count', '', 'nul', '0', '1')";

if( !$update ) {
  echo '
  <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
  <html>
  <head>
  	<title>APC-AA database update script</title>
  </head>
  <body>

  <h1>APC-AA database update</h1>
  <p>This script is written to be not destructive. It creates temporary tables 
     first, then copies data from old tables to the temporary ones (tmp_*) and
     after successfull copy it drops old tables and renames temporary ones to 
     right names. Then it possibly updates common records (like default field 
     definitions, APC-wide constants and templates).</p>
  <p><font color="red">However, it is strongly recommended backup your current 
  database !!!</font><br>Something like:<br><code>mysqldump --lock-tables -u root -p --opt linkdb &gt; ./linkdb/linkdb.sql</code></p>

  <form name=f action="' .$PHP_SELF .'">
  <table width="440" border="0" cellspacing="0" cellpadding="1" bgcolor="#589868" align="center">
  <tr><td class=tabtit><b>&nbsp;APC-AA database update options</b>
  </td>
  </tr>
  <tr><td>
  <table width="100%" border="0" cellspacing="0" cellpadding="4" bgcolor="#A8C8B0">';
  FrmInputChBox("dbcreate", "Update DB structure", true, false, "", 1, false, 
                "create or update database structure","");
  FrmInputChBox("copyold", "Copy current data", true, false, "", 1, false, 
                "copy data from current database to the updated one","");
  FrmInputChBox("backup", "Backup current tables", true, false, "", 1, false, 
                "left the current tables in database named like bck_xxx","");
  FrmInputChBox("replacecateg", "Refresh APC-wide categories", true, false, "", 1, false, 
                "updates APC-wide (parent) categories","");
  FrmInputChBox("replaceconst", "Refresh other constants", true, false, "", 1, false, 
                "updates Codepage constants, Language constants, Bin constants","");
  FrmInputChBox("newcore", "Redefine field defaults", true, false, "", 1, false, 
                "Updates field templates, which is used when you adding new field to slice","");
  FrmInputChBox("templates", "Redefine slice templates", true, false, "", 1, false, 
                "Updates only slice templates, which is in standard AA installation","");
  FrmInputChBox("addstatistic", "Add statistic field", true, false, "", 1, false, 
                "New field (display_count) in v1.8 should be added to all slice definitions","");

  echo '
  </table></td></tr>
  <tr><td align="center">
    <input type=submit name=update value="Run Update">
  </td></tr></table>
  </FORM>
  </body>
  </html>
  ';
  exit;
}  
  
echo '
  <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
  <html>
  <head>
  	<title>APC-AA database update script</title>
  </head>
  <body>

  <h1>APC-AA database update</h1>
  <p>Updating ...</p>';
  
if( $dbcreate ) {
  echo '<h2>Delete temporary tables if exists</h2>';
  reset( $tablelist );
  while( list( ,$t) = each( $tablelist ) ) {
    $SQL = "DROP TABLE IF EXISTS tmp_$t";
    echo $SQL."<br>";
    $db->query("$SQL");
  }  

  echo '<h2>Creating temporary databases</h2>';
  reset( $SQL_create_tmp_tables );
  while( list( ,$SQL) = each( $SQL_create_tmp_tables ) ) {
    echo $SQL."<br>";
    $db->query( $SQL );
  }

  echo '<h2>Creating new tables that do not exist in database</h2>';
  reset( $SQL_create_new_tables );
  while( list( ,$SQL) = each( $SQL_create_new_tables ) ) {
    echo $SQL."<br>";
    $db->query( $SQL );
  }
  
}

if( $copyold ) {
  echo '<h2>Copying old values to new tables </h2>';
  reset( $tablelist );
  while( list( ,$t) = each( $tablelist ) ) {   # copy all tables
    $old_info = $db->metadata( $t );
    $tmp_info = $db->metadata( "tmp_$t" );
    
    if( isset( $old_info ) AND is_array($old_info) ) {
      $delim = "";
      $field_list = "";
      while( list ( ,$fld ) = each ($tmp_info)) {  #construct field list
        if( IsPaired($fld[name], $old_info) ) 
          $field_list .= $delim . $fld[name];
         else 
          $field_list .= $delim .( strstr($fld[flags],"not_null") ? '" "':'""');
        $delim = ",";
      }
      $SQL = "INSERT INTO tmp_$t SELECT $field_list FROM $t";
      echo $SQL."<br>";
      $db->query($SQL);
    }
}
}

if( $backup )
  echo '<h2>Backup old tables to bck_xxxx tables and use new tables instead</h2>';
 else 
  echo '<h2>delete old tables and use new tables instead</h2>';

if( $dbcreate ) {
  reset( $tablelist );
  while( list( ,$t) = each( $tablelist ) ) {
    if( $backup ) {
      $SQL = "DROP TABLE IF EXISTS bck_$t";
      echo $SQL."<br>";
      $db->query($SQL);
  
      $SQL = "ALTER TABLE $t RENAME bck_$t";
      echo $SQL."<br>";
      $db->query($SQL);
    }  
    $SQL = "DROP TABLE IF EXISTS $t";
    echo $SQL."<br>";
    $db->query($SQL);

    $SQL = "ALTER TABLE tmp_$t RENAME $t";
    echo $SQL."<br>";
    $db->query($SQL);
  }  
}

if( $addstatistic ) {
  echo '<h2>Add statistic field for each slice</h2>';
  $SQL = "SELECT id FROM slice";
  $db->query( $SQL );
  while( $db->next_record() ) {
    if( $db->f(id) == 'AA_Core_Fields..' )
      continue;
    $SQL = "REPLACE INTO field VALUES( 'display_count...', '', '". quote($db->f(id)) ."', 'Displayed Times', '5050', 'Internal field - do not change', 'http://aa.ecn.cz/aa/doc/help.html', 'qte:0', '1', '1', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#DISPL_NO', 'f_h', 'alias for number of displaying of this item', '', '', '', '', '', '', '', '', '0', '0', '0', 'display_count', '', 'nul', '0', '1')";
    echo $SQL."<br>";
    $db2->query( $SQL );
  }  
}  
  

if( $replacecateg ) {
  echo '<h2>Updating APC wide categories</h2>';
  reset( $SQL_apc_categ );
  while( list( ,$SQL) = each( $SQL_apc_categ ) ) {
    echo $SQL."<br>";
    $db->query( $SQL );
  }  
}

if( $replaceconst ) {
  echo '<h2>Updating Constants</h2>';
  reset( $SQL_constants );
  while( list( ,$SQL) = each( $SQL_constants ) ) {
    echo $SQL."<br>";
    $db->query( $SQL );
  }  
}

if( $newcore ) {
  echo '<h2>Updating Core field definitions</h2>';
  reset( $SQL_aacore );
  while( list( ,$SQL) = each( $SQL_aacore ) ) {
    echo $SQL."<br>";
    $db->query( $SQL );
  }  
}

if( $templates ) {
  echo '<h2>Updating Slice templates</h2>';
  reset( $SQL_templates );
  while( list( ,$SQL) = each( $SQL_templates ) ) {
    echo $SQL."<br>";
    $db->query( $SQL );
  }  
}

echo '<h2>Update OK</h2>';

/*
$Log$
Revision 1.7  2001/08/21 18:12:51  honzam
fixed bug of missing value field in feedmap table

Revision 1.6  2001/07/09 18:02:57  honzam
removed not used database credentials

Revision 1.5  2001/06/21 14:15:45  honzam
feeding improved - field value redefine possibility in se_mapping.php3

Revision 1.4  2001/06/05 18:24:08  madebeer
adds tables that do not exist, to avoid errors.
loads config.php3

Revision 1.3  2001/06/04 10:53:37  honzam
create table bug fixed for not updating reinstallation

Revision 1.1  2001/06/03 15:54:31  honzam
new sql_update.php3 script for easy database install & reinstalation

*/
?>