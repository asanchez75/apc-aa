# 20/08/02 - renewed everything from database
# 20/08/02 - added tables alerts_collection, alerts_collection_filter, alerts_digest_filter, alerts_user, alerts_user_filter
# 20/08/02 - added column moved2active to table item
# xx/07/02 - added columns fileman_access and fileman_dir to table slice
# 21/06/02 - added column javascript to table slice
# 04/22/02 - added constant_slice, jump, mysql_auth_group, mysql_auth_user, 
#            mysql_auth_user_group, mysql_auth_userinfo tables
#          - added some columns to constant, module, slice and view table  
# 01/14/02 - added site, site_spot table for "site" module
# 01/12/02 - added module table
# 01/08/02 - added index to item table
# 11/26/01 - added profile table
#          - added notify_holding_item_s, notify_holding_item_b, 
#            notify_holding_item_edit_s, notify_holding_item_edit_b, 
#            notify_active_item_edit_s, notify_active_item_edit_b,
#            notify_active_item_s, notify_active_item_b, noitem_msg
#            into slice table
#          - added aditional2, aditional3, aditional4, aditional5, aditional6,
#            noitem_msg into profile table
# 11/13/01 - changed external_feeds.newest_item from bigint to varchar(40)
# 09/26/01 - added "nodes" table
#          - added "external_feeds" table
#          - added "ef_categories" table
#          - added "ef_permissions" table
#          - added "from_field_name" to "feedmap" table
# 08/16/01 - added "discussion" table
#          - added "disc_count" and "disc_app" fields to item table
#          - added "vid" in slice table (for discussion view id)
#          - type of selected_item in view table changed to text
# 06/21/01 - added "value mediumtext" to feedmap table
# 06/01/01 - added display_count, short_id and flags to item table
#          - longer value in constant table (150 -> 255)
# 05/30/01 - new sql_update.php3 script updating current database instalation
# 05/13/01 - added view table
# 0x/0x/01 - add feedmap table
#          - add relation table
#          - add flag in feedperms
#          - add offline table, relation table, aditional and flag field in slice table
#          - grab_len and redirect removed

# --------------------------------------------------------

#
# Struktura tabulky `active_sessions`
#

DROP TABLE IF EXISTS active_sessions;
CREATE TABLE active_sessions (
  sid varchar(32) NOT NULL default '',
  name varchar(32) NOT NULL default '',
  val text,
  changed varchar(14) NOT NULL default '',
  PRIMARY KEY  (name,sid),
  KEY changed (changed)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `alerts_collection`
#

DROP TABLE IF EXISTS alerts_collection;
CREATE TABLE alerts_collection (
  id int(11) NOT NULL auto_increment,
  description text NOT NULL,
  showme tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `alerts_collection_filter`
#

DROP TABLE IF EXISTS alerts_collection_filter;
CREATE TABLE alerts_collection_filter (
  collectionid int(11) NOT NULL default '0',
  filterid int(11) NOT NULL default '0',
  myindex tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (collectionid,filterid)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `alerts_digest_filter`
#

DROP TABLE IF EXISTS alerts_digest_filter;
CREATE TABLE alerts_digest_filter (
  id int(11) NOT NULL auto_increment,
  vid int(11) NOT NULL default '0',
  conds text NOT NULL,
  showme tinyint(1) NOT NULL default '1',
  description text NOT NULL,
  last_daily int(11) NOT NULL default '0',
  last_weekly int(11) NOT NULL default '0',
  last_monthly int(11) NOT NULL default '0',
  text_daily text NOT NULL,
  text_weekly text NOT NULL,
  text_monthly text NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `alerts_user`
#

DROP TABLE IF EXISTS alerts_user;
CREATE TABLE alerts_user (
  id int(10) NOT NULL auto_increment,
  email varchar(255) NOT NULL default '',
  password varchar(255) NOT NULL default '',
  firstname varchar(100) NOT NULL default '',
  lastname varchar(100) NOT NULL default '',
  session varchar(32) NOT NULL default '',
  sessiontime int(10) NOT NULL default '0',
  confirm varchar(20) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `alerts_user_filter`
#

DROP TABLE IF EXISTS alerts_user_filter;
CREATE TABLE alerts_user_filter (
  id int(10) NOT NULL auto_increment,
  userid int(11) default NULL,
  filterid int(11) default NULL,
  howoften varchar(10) NOT NULL default 'daily',
  collectionid int(11) default NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY user_filter (userid,filterid),
  KEY alerts_collection (userid,collectionid)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `constant`
#

DROP TABLE IF EXISTS constant;
CREATE TABLE constant (
  id varchar(16) NOT NULL default '',
  group_id varchar(16) NOT NULL default '',
  name varchar(150) NOT NULL default '',
  value varchar(255) NOT NULL default '',
  class varchar(16) default NULL,
  pri smallint(5) NOT NULL default '100',
  ancestors varchar(160) default NULL,
  description varchar(250) default NULL,
  short_id int(11) NOT NULL auto_increment,
  PRIMARY KEY  (id),
  KEY group_id (group_id),
  KEY short_id (short_id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `constant_slice`
#

DROP TABLE IF EXISTS constant_slice;
CREATE TABLE constant_slice (
  slice_id char(16) default NULL,
  group_id char(16) NOT NULL default '',
  propagate tinyint(1) NOT NULL default '1',
  levelcount tinyint(2) NOT NULL default '2',
  horizontal tinyint(1) NOT NULL default '0',
  hidevalue tinyint(1) NOT NULL default '0',
  hierarch tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (group_id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `content`
#

DROP TABLE IF EXISTS content;
CREATE TABLE content (
  item_id varchar(16) NOT NULL default '',
  field_id varchar(16) NOT NULL default '',
  number bigint(20) default NULL,
  text mediumtext,
  flag smallint(6) default NULL,
  KEY item_id (item_id,field_id,text(16)),
  KEY text (text(10))
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `cron`
#

DROP TABLE IF EXISTS cron;
CREATE TABLE cron (
  id bigint(30) NOT NULL auto_increment,
  slice_id varchar(16) NOT NULL default '',
  minutes varchar(30) default NULL,
  hours varchar(30) default NULL,
  mday varchar(30) default NULL,
  mon varchar(30) default NULL,
  wday varchar(30) default NULL,
  script varchar(100) default NULL,
  params varchar(200) default NULL,
  last_run bigint(30) default NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `db_sequence`
#

DROP TABLE IF EXISTS db_sequence;
CREATE TABLE db_sequence (
  seq_name varchar(127) NOT NULL default '',
  nextid int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (seq_name)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `discussion`
#

DROP TABLE IF EXISTS discussion;
CREATE TABLE discussion (
  id varchar(16) NOT NULL default '',
  parent varchar(16) NOT NULL default '',
  item_id varchar(16) NOT NULL default '',
  date bigint(20) NOT NULL default '0',
  subject text,
  author varchar(255) default NULL,
  e_mail varchar(80) default NULL,
  body text,
  state int(11) NOT NULL default '0',
  flag int(11) NOT NULL default '0',
  url_address varchar(255) default NULL,
  url_description text,
  remote_addr varchar(255) default NULL,
  free1 text,
  free2 text,
  PRIMARY KEY  (id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `ef_categories`
#

DROP TABLE IF EXISTS ef_categories;
CREATE TABLE ef_categories (
  category varchar(255) NOT NULL default '',
  category_name varchar(255) NOT NULL default '',
  category_id varchar(16) NOT NULL default '',
  feed_id int(11) NOT NULL default '0',
  target_category_id varchar(16) NOT NULL default '',
  approved int(11) NOT NULL default '0',
  PRIMARY KEY  (category_id,feed_id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `ef_permissions`
#

DROP TABLE IF EXISTS ef_permissions;
CREATE TABLE ef_permissions (
  slice_id varchar(16) NOT NULL default '',
  node varchar(150) NOT NULL default '',
  user varchar(50) NOT NULL default '',
  PRIMARY KEY  (slice_id,node,user)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `email_auto_user`
#

DROP TABLE IF EXISTS email_auto_user;
CREATE TABLE email_auto_user (
  uid char(50) NOT NULL default '',
  creation_time bigint(20) NOT NULL default '0',
  last_change bigint(20) NOT NULL default '0',
  clear_pw char(40) default NULL,
  confirmed smallint(5) NOT NULL default '0',
  confirm_key char(16) default NULL,
  PRIMARY KEY  (uid)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `email_notify`
#

DROP TABLE IF EXISTS email_notify;
CREATE TABLE email_notify (
  slice_id char(16) NOT NULL default '',
  uid char(60) NOT NULL default '',
  function smallint(5) NOT NULL default '0',
  PRIMARY KEY  (slice_id,uid,function),
  KEY slice_id (slice_id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `external_feeds`
#

DROP TABLE IF EXISTS external_feeds;
CREATE TABLE external_feeds (
  feed_id int(11) NOT NULL auto_increment,
  slice_id varchar(16) NOT NULL default '',
  node_name varchar(150) NOT NULL default '',
  remote_slice_id varchar(16) NOT NULL default '',
  user_id varchar(200) NOT NULL default '',
  newest_item varchar(40) NOT NULL default '',
  remote_slice_name varchar(200) NOT NULL default '',
  PRIMARY KEY  (feed_id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `feedmap`
#

DROP TABLE IF EXISTS feedmap;
CREATE TABLE feedmap (
  from_slice_id varchar(16) NOT NULL default '',
  from_field_id varchar(16) NOT NULL default '',
  to_slice_id varchar(16) NOT NULL default '',
  to_field_id varchar(16) NOT NULL default '',
  flag int(11) default NULL,
  value mediumtext,
  from_field_name varchar(255) NOT NULL default '',
  KEY from_slice_id (from_slice_id,to_slice_id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `feedperms`
#

DROP TABLE IF EXISTS feedperms;
CREATE TABLE feedperms (
  from_id varchar(16) NOT NULL default '',
  to_id varchar(16) NOT NULL default '',
  flag int(11) default NULL
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `feeds`
#

DROP TABLE IF EXISTS feeds;
CREATE TABLE feeds (
  from_id varchar(16) NOT NULL default '',
  to_id varchar(16) NOT NULL default '',
  category_id varchar(16) default NULL,
  all_categories smallint(5) default NULL,
  to_approved smallint(5) default NULL,
  to_category_id varchar(16) default NULL,
  KEY from_id (from_id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `field`
#

DROP TABLE IF EXISTS field;
CREATE TABLE field (
  id varchar(16) NOT NULL default '',
  type varchar(16) NOT NULL default '',
  slice_id varchar(16) NOT NULL default '',
  name varchar(255) NOT NULL default '',
  input_pri smallint(5) NOT NULL default '100',
  input_help varchar(255) default NULL,
  input_morehlp text,
  input_default mediumtext,
  required smallint(5) default NULL,
  feed smallint(5) default NULL,
  multiple smallint(5) default NULL,
  input_show_func varchar(255) default NULL,
  content_id varchar(16) default NULL,
  search_pri smallint(5) NOT NULL default '100',
  search_type varchar(16) default NULL,
  search_help varchar(255) default NULL,
  search_before text,
  search_more_help text,
  search_show smallint(5) default NULL,
  search_ft_show smallint(5) default NULL,
  search_ft_default smallint(5) default NULL,
  alias1 varchar(10) default NULL,
  alias1_func varchar(255) default NULL,
  alias1_help varchar(255) default NULL,
  alias2 varchar(10) default NULL,
  alias2_func varchar(255) default NULL,
  alias2_help varchar(255) default NULL,
  alias3 varchar(10) default NULL,
  alias3_func varchar(255) default NULL,
  alias3_help varchar(255) default NULL,
  input_before text,
  aditional text,
  content_edit smallint(5) default NULL,
  html_default smallint(5) default NULL,
  html_show smallint(5) default NULL,
  in_item_tbl varchar(16) default NULL,
  input_validate varchar(16) NOT NULL default '',
  input_insert_func varchar(255) NOT NULL default '',
  input_show smallint(5) default NULL,
  text_stored smallint(5) default '1',
  KEY slice_id (slice_id,id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `groups`
#

DROP TABLE IF EXISTS groups;
CREATE TABLE groups (
  name varchar(32) NOT NULL default '',
  description varchar(255) NOT NULL default '',
  PRIMARY KEY  (name)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `item`
#

DROP TABLE IF EXISTS item;
CREATE TABLE item (
  id varchar(16) NOT NULL default '',
  short_id int(11) NOT NULL auto_increment,
  slice_id varchar(16) NOT NULL default '',
  status_code smallint(5) NOT NULL default '0',
  post_date bigint(20) NOT NULL default '0',
  publish_date bigint(20) default NULL,
  expiry_date bigint(20) default NULL,
  highlight smallint(5) default NULL,
  posted_by varchar(60) default NULL,
  edited_by varchar(60) default NULL,
  last_edit bigint(20) default NULL,
  display_count int(11) NOT NULL default '0',
  flags varchar(30) default NULL,
  disc_count int(11) default '0',
  disc_app int(11) default '0',
  externally_fed varchar(150) NOT NULL default '',
  moved2active int(10) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY short_id (short_id),
  KEY slice_id_2 (slice_id,status_code,publish_date),
  KEY expiry_date (expiry_date)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `jump`
#

DROP TABLE IF EXISTS jump;
CREATE TABLE jump (
  slice_id varchar(16) NOT NULL default '',
  destination varchar(255) default NULL,
  dest_slice_id varchar(16) default NULL,
  PRIMARY KEY  (slice_id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `log`
#

DROP TABLE IF EXISTS log;
CREATE TABLE log (
  id int(11) NOT NULL auto_increment,
  time bigint(20) NOT NULL default '0',
  user char(60) NOT NULL default '',
  type char(10) NOT NULL default '',
  params char(128) default NULL,
  PRIMARY KEY  (id),
  KEY time (time)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `membership`
#

DROP TABLE IF EXISTS membership;
CREATE TABLE membership (
  groupid int(11) NOT NULL default '0',
  memberid int(11) NOT NULL default '0',
  last_mod timestamp(14) NOT NULL,
  PRIMARY KEY  (groupid,memberid),
  KEY memberid (memberid)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `module`
#

DROP TABLE IF EXISTS module;
CREATE TABLE module (
  id varchar(16) NOT NULL default '',
  name varchar(100) NOT NULL default '',
  deleted smallint(5) default NULL,
  type varchar(16) default 'S',
  slice_url varchar(255) default NULL,
  lang_file varchar(50) default NULL,
  created_at bigint(20) NOT NULL default '0',
  created_by varchar(255) NOT NULL default '',
  owner varchar(16) NOT NULL default '',
  flag int(11) default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `mysql_auth_group`
#

DROP TABLE IF EXISTS mysql_auth_group;
CREATE TABLE mysql_auth_group (
  slice_id varchar(16) NOT NULL default '',
  groupparent varchar(30) NOT NULL default '',
  groups varchar(30) NOT NULL default ''
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `mysql_auth_user`
#

DROP TABLE IF EXISTS mysql_auth_user;
CREATE TABLE mysql_auth_user (
  uid int(10) NOT NULL default '0',
  username char(30) NOT NULL default '',
  passwd char(30) NOT NULL default '',
  PRIMARY KEY  (uid),
  UNIQUE KEY username (username)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `mysql_auth_user_group`
#

DROP TABLE IF EXISTS mysql_auth_user_group;
CREATE TABLE mysql_auth_user_group (
  username char(30) NOT NULL default '',
  groups char(30) NOT NULL default '',
  PRIMARY KEY  (username,groups)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `mysql_auth_userinfo`
#

DROP TABLE IF EXISTS mysql_auth_userinfo;
CREATE TABLE mysql_auth_userinfo (
  slice_id varchar(16) NOT NULL default '',
  uid int(10) NOT NULL auto_increment,
  first_name varchar(20) default NULL,
  last_name varchar(30) default NULL,
  organisation varchar(50) default NULL,
  start_date bigint(20) default NULL,
  renewal_date bigint(20) default NULL,
  email varchar(50) default 'admin@ein.org.uk',
  membership_type varchar(50) default NULL,
  status_code smallint(5) default '2',
  todo varchar(250) default NULL,
  PRIMARY KEY  (uid)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `nodes`
#

DROP TABLE IF EXISTS nodes;
CREATE TABLE nodes (
  name varchar(150) NOT NULL default '',
  server_url varchar(200) NOT NULL default '',
  password varchar(50) NOT NULL default '',
  PRIMARY KEY  (name)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `offline`
#

DROP TABLE IF EXISTS offline;
CREATE TABLE offline (
  id char(16) NOT NULL default '',
  digest char(32) NOT NULL default '',
  flag int(11) default NULL,
  PRIMARY KEY  (id),
  KEY digest (digest)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `pagecache`
#

DROP TABLE IF EXISTS pagecache;
CREATE TABLE pagecache (
  id varchar(32) NOT NULL default '',
  str2find text,
  content mediumtext,
  stored bigint(20) NOT NULL default '0',
  flag int(11) default NULL,
  PRIMARY KEY  (id),
  KEY stored (stored)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `perms`
#

DROP TABLE IF EXISTS perms;
CREATE TABLE perms (
  object_type char(30) NOT NULL default '',
  objectid char(32) NOT NULL default '',
  userid int(11) NOT NULL default '0',
  perm char(32) NOT NULL default '',
  last_mod timestamp(14) NOT NULL,
  PRIMARY KEY  (objectid,userid,object_type),
  KEY userid (userid)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `profile`
#

DROP TABLE IF EXISTS profile;
CREATE TABLE profile (
  id int(11) NOT NULL auto_increment,
  slice_id varchar(16) NOT NULL default '',
  uid varchar(60) NOT NULL default '*',
  property varchar(20) NOT NULL default '',
  selector varchar(255) default NULL,
  value text,
  PRIMARY KEY  (id),
  KEY slice_user_id (slice_id,uid)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `relation`
#

DROP TABLE IF EXISTS relation;
CREATE TABLE relation (
  source_id char(16) NOT NULL default '',
  destination_id char(32) NOT NULL default '',
  flag int(11) default NULL,
  KEY source_id (source_id),
  KEY destination_id (destination_id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `searchlog`
#

DROP TABLE IF EXISTS searchlog;
CREATE TABLE searchlog (
  id int(11) NOT NULL auto_increment,
  date bigint(20) default NULL,
  query text,
  found_count int(11) default NULL,
  search_time int(11) default NULL,
  user text,
  additional1 text,
  PRIMARY KEY  (id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `slice`
#

DROP TABLE IF EXISTS slice;
CREATE TABLE slice (
  id varchar(16) NOT NULL default '',
  name varchar(100) NOT NULL default '',
  owner varchar(16) default NULL,
  deleted smallint(5) default NULL,
  created_by varchar(255) default NULL,
  created_at bigint(20) default NULL,
  export_to_all smallint(5) default NULL,
  type varchar(16) default NULL,
  template smallint(5) default NULL,
  fulltext_format_top text,
  fulltext_format text,
  fulltext_format_bottom text,
  odd_row_format text,
  even_row_format text,
  even_odd_differ smallint(5) default NULL,
  compact_top text,
  compact_bottom text,
  category_top text,
  category_format text,
  category_bottom text,
  category_sort smallint(5) default NULL,
  config text NOT NULL,
  slice_url varchar(255) default NULL,
  d_expiry_limit smallint(5) default NULL,
  d_listlen smallint(5) default NULL,
  lang_file varchar(50) default NULL,
  fulltext_remove text,
  compact_remove text,
  email_sub_enable smallint(5) default NULL,
  exclude_from_dir smallint(5) default NULL,
  notify_sh_offer mediumtext,
  notify_sh_accept mediumtext,
  notify_sh_remove mediumtext,
  notify_holding_item_s mediumtext,
  notify_holding_item_b mediumtext,
  notify_holding_item_edit_s mediumtext,
  notify_holding_item_edit_b mediumtext,
  notify_active_item_edit_s mediumtext,
  notify_active_item_edit_b mediumtext,
  notify_active_item_s mediumtext,
  notify_active_item_b mediumtext,
  noitem_msg mediumtext,
  admin_format_top text,
  admin_format text,
  admin_format_bottom text,
  admin_remove text,
  permit_anonymous_post smallint(5) default NULL,
  permit_offline_fill smallint(5) default NULL,
  aditional text,
  flag int(11) NOT NULL default '0',
  vid int(11) default '0',
  gb_direction tinyint(4) default NULL,
  group_by varchar(16) default NULL,
  gb_header tinyint(4) default NULL,
  gb_case varchar(15) default NULL,
  javascript text,
  fileman_access varchar(20) default NULL,
  fileman_dir varchar(50) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `slice_owner`
#

DROP TABLE IF EXISTS slice_owner;
CREATE TABLE slice_owner (
  id char(16) NOT NULL default '',
  name char(80) NOT NULL default '',
  email char(80) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `subscriptions`
#

DROP TABLE IF EXISTS subscriptions;
CREATE TABLE subscriptions (
  uid char(50) NOT NULL default '',
  category char(16) default NULL,
  content_type char(16) default NULL,
  slice_owner char(16) default NULL,
  frequency smallint(5) NOT NULL default '0',
  last_post bigint(20) NOT NULL default '0',
  KEY uid (uid,frequency)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `users`
#

DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id int(11) NOT NULL auto_increment,
  type char(10) NOT NULL default '',
  password char(30) NOT NULL default '',
  uid char(40) NOT NULL default '',
  mail char(40) NOT NULL default '',
  name char(80) NOT NULL default '',
  description char(255) NOT NULL default '',
  givenname char(40) NOT NULL default '',
  sn char(40) NOT NULL default '',
  last_mod timestamp(14) NOT NULL,
  PRIMARY KEY  (id),
  KEY type (type),
  KEY mail (mail),
  KEY name (name),
  KEY sn (sn)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `view`
#

DROP TABLE IF EXISTS view;
CREATE TABLE view (
  id int(10) unsigned NOT NULL auto_increment,
  slice_id varchar(16) NOT NULL default '',
  name varchar(50) default NULL,
  type varchar(10) default NULL,
  before text,
  even text,
  odd text,
  even_odd_differ tinyint(3) unsigned default NULL,
  after text,
  remove_string text,
  group_title text,
  order1 varchar(16) default NULL,
  o1_direction tinyint(3) unsigned default NULL,
  order2 varchar(16) default NULL,
  o2_direction tinyint(3) unsigned default NULL,
  group_by1 varchar(16) default NULL,
  g1_direction tinyint(3) unsigned default NULL,
  group_by2 varchar(16) default NULL,
  g2_direction tinyint(3) unsigned default NULL,
  cond1field varchar(16) default NULL,
  cond1op varchar(10) default NULL,
  cond1cond varchar(255) default NULL,
  cond2field varchar(16) default NULL,
  cond2op varchar(10) default NULL,
  cond2cond varchar(255) default NULL,
  cond3field varchar(16) default NULL,
  cond3op varchar(10) default NULL,
  cond3cond varchar(255) default NULL,
  listlen int(10) unsigned default NULL,
  scroller tinyint(3) unsigned default NULL,
  selected_item tinyint(3) unsigned default NULL,
  modification int(10) unsigned default NULL,
  parameter varchar(255) default NULL,
  img1 varchar(255) default NULL,
  img2 varchar(255) default NULL,
  img3 varchar(255) default NULL,
  img4 varchar(255) default NULL,
  flag int(10) unsigned default NULL,
  aditional text,
  aditional2 text,
  aditional3 text,
  aditional4 text,
  aditional5 text,
  aditional6 text,
  noitem_msg text,
  group_bottom text,
  field1 varchar(16) default NULL,
  field2 varchar(16) default NULL,
  field3 varchar(16) default NULL,
  calendar_type varchar(100) default 'mon',
  PRIMARY KEY  (id),
  KEY slice_id (slice_id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Struktura tabulky `wizard_template`
#

DROP TABLE IF EXISTS wizard_template;
CREATE TABLE wizard_template (
  id tinyint(10) NOT NULL auto_increment,
  dir varchar(100) NOT NULL default '',
  description varchar(255) NOT NULL default '',
  PRIMARY KEY  (id),
  UNIQUE KEY dir (dir)
) TYPE=MyISAM COMMENT='List of templates for the New Slice Wizard';
# --------------------------------------------------------

#
# Struktura tabulky `wizard_welcome`
#

DROP TABLE IF EXISTS wizard_welcome;
CREATE TABLE wizard_welcome (
  id int(11) NOT NULL auto_increment,
  description varchar(200) NOT NULL default '',
  email text,
  subject varchar(255) NOT NULL default '',
  mail_from varchar(255) NOT NULL default '_#ME_MAIL_',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

# --------------------------------------------------------

# Dumping data for table 'constant'
#

INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined000', 'lt_codepages', 'iso8859-1', 'iso8859-1', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined001', 'lt_codepages', 'iso8859-2', 'iso8859-2', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined002', 'lt_codepages', 'windows-1250', 'windows-1250', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined003', 'lt_codepages', 'windows-1253', 'windows-1253', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined004', 'lt_codepages', 'windows-1254', 'windows-1254', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined005', 'lt_codepages', 'koi8-r', 'koi8-r', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined006', 'lt_codepages', 'ISO-8859-8', 'ISO-8859-8', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined007', 'lt_codepages', 'windows-1258', 'windows-1258', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined008', 'lt_languages', 'Afrikaans', 'AF', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined009', 'lt_languages', 'Arabic', 'AR', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined010', 'lt_languages', 'Basque', 'EU', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined011', 'lt_languages', 'Byelorussian', 'BE', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined012', 'lt_languages', 'Bulgarian', 'BG', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined013', 'lt_languages', 'Catalan', 'CA', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined014', 'lt_languages', 'Chinese (ZH-CN)', 'ZH', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined015', 'lt_languages', 'Chinese', 'ZH-TW', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined016', 'lt_languages', 'Croatian', 'HR', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined017', 'lt_languages', 'Czech', 'CS', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined018', 'lt_languages', 'Danish', 'DA', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined019', 'lt_languages', 'Dutch', 'NL', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined020', 'lt_languages', 'English', 'EN-GB', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined021', 'lt_languages', 'English (EN-US)', 'EN', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined022', 'lt_languages', 'Estonian', 'ET', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined023', 'lt_languages', 'Faeroese', 'FO', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined024', 'lt_languages', 'Finnish', 'FI', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined025', 'lt_languages', 'French (FR-FR)', 'FR', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined026', 'lt_languages', 'French', 'FR-CA', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined027', 'lt_languages', 'German', 'DE', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined028', 'lt_languages', 'Greek', 'EL', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined029', 'lt_languages', 'Hebrew (IW)', 'HE', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined030', 'lt_languages', 'Hungarian', 'HU', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined031', 'lt_languages', 'Icelandic', 'IS', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined032', 'lt_languages', 'Indonesian (IN)', 'ID', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined033', 'lt_languages', 'Italian', 'IT', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined034', 'lt_languages', 'Japanese', 'JA', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined035', 'lt_languages', 'Korean', 'KO', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined036', 'lt_languages', 'Latvian', 'LV', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined037', 'lt_languages', 'Lithuanian', 'LT', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined038', 'lt_languages', 'Neutral', 'NEUTRAL', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined039', 'lt_languages', 'Norwegian', 'NO', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined040', 'lt_languages', 'Polish', 'PL', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined041', 'lt_languages', 'Portuguese', 'PT', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined042', 'lt_languages', 'Portuguese', 'PT-BR', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined043', 'lt_languages', 'Romanian', 'RO', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined044', 'lt_languages', 'Russian', 'RU', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined045', 'lt_languages', 'Serbian', 'SR', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined046', 'lt_languages', 'Slovak', 'SK', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined047', 'lt_languages', 'Slovenian', 'SL', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined048', 'lt_languages', 'Spanish (ES-ES)', 'ES', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined049', 'lt_languages', 'Swedish', 'SV', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined050', 'lt_languages', 'Thai', 'TH', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined051', 'lt_languages', 'Turkish', 'TR', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined052', 'lt_languages', 'Ukrainian', 'UK', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined053', 'lt_languages', 'Vietnamese', 'VI', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined054', 'lt_groupNames', 'Code Pages', 'lt_codepages', '', '0');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined055', 'lt_groupNames', 'Languages Shortcuts', 'lt_languages', '', '1000');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined056', 'lt_groupNames', 'APC-wide Categories', 'lt_apcCategories', '', '1000');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined057', 'lt_groupNames', 'AA Core Bins', 'AA_Core_Bins....', '', '10000');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined058', 'AA_Core_Bins....', 'Approved', '1', '', '100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined059', 'AA_Core_Bins....', 'Holding Bin', '2', '', '200');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined060', 'AA_Core_Bins....', 'Trash Bin', '3', '', '300');

INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined100', 'lt_apcCategories', 'Internet & ICT', 'Internet & ICT', '', '1000');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined101', 'lt_apcCategories', 'Internet & ICT - Free software & Open Source', 'Internet & ICT - Free software & Open Source', '', '1100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined102', 'lt_apcCategories', 'Internet & ICT - Access', 'Internet & ICT - Access', '', '1200');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined103', 'lt_apcCategories', 'Internet & ICT - Connectivity', 'Internet & ICT - Connectivity', '', '1300');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined104', 'lt_apcCategories', 'Internet & ICT - Women and ICT', 'Internet & ICT - Women and ICT', '', '1400');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined105', 'lt_apcCategories', 'Internet & ICT - Rights', 'Internet & ICT - Rights', '', '1500');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined106', 'lt_apcCategories', 'Internet & ICT - Governance', 'Internet & ICT - Governance', '', '1600');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined107', 'lt_apcCategories', 'Development', 'Development', '', '2000');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined108', 'lt_apcCategories', 'Development - Resources', 'Development - Resources', '', '2100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined109', 'lt_apcCategories', 'Development - Structural adjustment', 'Development - Structural adjustment', '', '2200');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined110', 'lt_apcCategories', 'Development - Sustainability', 'Development - Sustainability', '', '2300');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined111', 'lt_apcCategories', 'News and media', 'News and media', '', '3000');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined112', 'lt_apcCategories', 'News and media - Alternative', 'News and media - Alternative', '', '3100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined113', 'lt_apcCategories', 'News and media - Internet', 'News and media - Internet', '', '3200');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined114', 'lt_apcCategories', 'News and media - Training', 'News and media - Training', '', '3300');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined115', 'lt_apcCategories', 'News and media - Traditional', 'News and media - Traditional', '', '3400');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined116', 'lt_apcCategories', 'Environment', 'Environment', '', '4000');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined117', 'lt_apcCategories', 'Environment - Agriculture', 'Environment - Agriculture', '', '4100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined118', 'lt_apcCategories', 'Environment - Animal rights/protection', 'Environment - Animal rights/protection', '', '4200');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined119', 'lt_apcCategories', 'Environment - Climate', 'Environment - Climate', '', '4300');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined120', 'lt_apcCategories', 'Environment - Biodiversity/conservetion', 'Environment - Biodiversity/conservetion', '', '4400');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined121', 'lt_apcCategories', 'Environment - Energy', 'Environment - Energy', '', '4500');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined122', 'lt_apcCategories', 'Environment - Campaigns', 'Environment - Campaigns', '', '4550');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined123', 'lt_apcCategories', 'Environment - Legislation', 'Environment - Legislation', '', '4600');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined124', 'lt_apcCategories', 'Environment - Genetics', 'Environment - Genetics', '', '4650');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined125', 'lt_apcCategories', 'Environment - Natural resources', 'Environment - Natural resources', '', '4700');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined126', 'lt_apcCategories', 'Environment - Rural development', 'Environment - Rural development', '', '5750');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined127', 'lt_apcCategories', 'Environment - Transport', 'Environment - Transport', '', '4800');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined128', 'lt_apcCategories', 'Environment - Urban ecology', 'Environment - Urban ecology', '', '4850');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined129', 'lt_apcCategories', 'Environment - Pollution & waste', 'Environment - Pollution & waste', '', '4900');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined130', 'lt_apcCategories', 'NGOs', 'NGOs', '', '5000');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined131', 'lt_apcCategories', 'NGOs - Fundraising', 'NGOs - Fundraising', '', '5100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined132', 'lt_apcCategories', 'NGOs - Funding agencies', 'NGOs - Funding agencies', '', '5200');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined133', 'lt_apcCategories', 'NGOs - Grants/scholarships', 'NGOs - Grants/scholarships', '', '5300');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined134', 'lt_apcCategories', 'NGOs - Jobs', 'NGOs - Jobs', '', '5400');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined135', 'lt_apcCategories', 'NGOs - Management', 'NGOs - Management', '', '5500');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined136', 'lt_apcCategories', 'NGOs - Volunteers', 'NGOs - Volunteers', '', '5600');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined137', 'lt_apcCategories', 'Society', 'Society', '', '6000');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined138', 'lt_apcCategories', 'Society - Charities', 'Society - Charities', '', '6100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined139', 'lt_apcCategories', 'Society - Community', 'Society - Community', '', '6200');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined140', 'lt_apcCategories', 'Society - Crime & rehabilitation', 'Society - Crime & rehabilitation', '', '6300');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined141', 'lt_apcCategories', 'Society - Disabilities', 'Society - Disabilities', '', '6400');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined142', 'lt_apcCategories', 'Society - Drugs', 'Society - Drugs', '', '6500');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined143', 'lt_apcCategories', 'Society - Ethical business', 'Society - Ethical business', '', '6600');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined144', 'lt_apcCategories', 'Society - Health', 'Society - Health', '', '6700');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined145', 'lt_apcCategories', 'Society - Law and legislation', 'Society - Law and legislation', '', '6750');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined146', 'lt_apcCategories', 'Society - Migration', 'Society - Migration', '', '6800');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined147', 'lt_apcCategories', 'Society - Sexuality', 'Society - Sexuality', '', '6850');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined148', 'lt_apcCategories', 'Society - Social services and welfare', 'Society - Social services and welfare', '', '6900');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined149', 'lt_apcCategories', 'Economy & Work', 'Economy & Work', '', '7000');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined150', 'lt_apcCategories', 'Economy & Work - Informal Sector', 'Economy & Work - Informal Sector', '', '7100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined151', 'lt_apcCategories', 'Economy & Work - Labour', 'Economy & Work - Labour', '', '7200');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined152', 'lt_apcCategories', 'Culture', 'Culture', '', '8000');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined153', 'lt_apcCategories', 'Culture - Arts and literature', 'Culture - Arts and literature', '', '8100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined154', 'lt_apcCategories', 'Culture - Heritage', 'Culture - Heritage', '', '8200');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined155', 'lt_apcCategories', 'Culture - Philosophy', 'Culture - Philosophy', '', '8300');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined156', 'lt_apcCategories', 'Culture - Religion', 'Culture - Religion', '', '8400');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined157', 'lt_apcCategories', 'Culture - Ethics', 'Culture - Ethics', '', '8500');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined158', 'lt_apcCategories', 'Culture - Leisure', 'Culture - Leisure', '', '8600');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined159', 'lt_apcCategories', 'Human rights', 'Human rights', '', '9000');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined160', 'lt_apcCategories', 'Human rights - Consumer Protection', 'Human rights - Consumer Protection', '', '9100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined161', 'lt_apcCategories', 'Human rights - Democracy', 'Human rights - Democracy', '', '9200');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined162', 'lt_apcCategories', 'Human rights - Minorities', 'Human rights - Minorities', '', '9300');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined163', 'lt_apcCategories', 'Human rights - Peace', 'Human rights - Peace', '', '9400');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined164', 'lt_apcCategories', 'Education', 'Education', '', '10000');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined165', 'lt_apcCategories', 'Education - Distance learning', 'Education - Distance learning', '', '10100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined166', 'lt_apcCategories', 'Education - Non-formal education', 'Education - Non-formal education', '', '10200');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined167', 'lt_apcCategories', 'Education - Schools', 'Education - Schools', '', '10300');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined168', 'lt_apcCategories', 'Politics & Government', 'Politics & Government', '', '11000');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined169', 'lt_apcCategories', 'Politics & Government - Internet', 'Politics & Government - Internet', '', '11100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined170', 'lt_apcCategories', 'Politics & Government - Local', 'Politics & Government - Local', '', '11200');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined171', 'lt_apcCategories', 'Politics & Government - Policies', 'Politics & Government - Policies', '', '11300');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined172', 'lt_apcCategories', 'Politics & Government - Administration', 'Politics & Government - Administration', '', '11400');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined173', 'lt_apcCategories', 'People', 'People', '', '12000');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined174', 'lt_apcCategories', 'People - Children', 'People - Children', '', '12100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined175', 'lt_apcCategories', 'People - Adolescents/teenagers', 'People - Adolescents/teenagers', '', '12200');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined176', 'lt_apcCategories', 'People - Gender', 'People - Gender', '', '12300');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined177', 'lt_apcCategories', 'People - Older people', 'People - Older people', '', '12400');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined178', 'lt_apcCategories', 'People - Family', 'People - Family', '', '12500');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined179', 'lt_apcCategories', 'World', 'World', '', '13000');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined180', 'lt_apcCategories', 'World - Globalization', 'World - Globalization', '', '13100');
INSERT INTO constant (id, group_id, name, value, class, pri) VALUES( 'AA-predefined181', 'lt_apcCategories', 'World - Debt', 'World - Debt', '', '13200');

INSERT INTO slice_owner (id, name, email) VALUES ('AA_Core.........', 'Action Aplications System', 'actionapps@ecn.cz');

# --------------------------------------------------------
# AA Core slice for internal use only (defines APC wide field types and its default values in process of  creation

INSERT INTO slice (id, name, owner, deleted, created_by, created_at, export_to_all, type, template, fulltext_format_top, fulltext_format, fulltext_format_bottom, odd_row_format, even_row_format, even_odd_differ, compact_top, compact_bottom, category_top, category_format, category_bottom, category_sort, config, slice_url, d_expiry_limit, d_listlen, lang_file, fulltext_remove, compact_remove, email_sub_enable, exclude_from_dir, notify_sh_offer, notify_sh_accept, notify_sh_remove, notify_holding_item_s, notify_holding_item_b, notify_holding_item_edit_s, notify_holding_item_edit_b, notify_active_item_edit_s, notify_active_item_edit_b, notify_active_item_s, notify_active_item_b, noitem_msg, admin_format_top, admin_format, admin_format_bottom, admin_remove, permit_anonymous_post, permit_offline_fill, aditional, flag, vid, gb_direction, group_by, gb_header, gb_case) VALUES ('AA_Core_Fields..', 'Action Aplication Core', 'AA_Core_Fields..', 0, '', 975157733, 0,       'AA_Core_Fields..', 0, '', '', '', '', '', 0, '', '', '', '', '', 1, '', 'http://aa.ecn.cz', 5000, 10000, 'en_news_lang.php3', '()', '()', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', 0, 0, NULL, NULL, NULL, NULL);

INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'headline', '', 'AA_Core_Fields..', 'Headline', '100', 'Headline', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'abstract', '', 'AA_Core_Fields..', 'Abstract', '189', 'Abstract', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'txt:8', '', '100', '', '', '', '', '0', '1', '1', '_#UNDEFINE', 'f_t', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '1', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'full_text', '', 'AA_Core_Fields..', 'Fulltext', '300', 'Fulltext', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'txt:8', '', '100', '', '', '', '', '0', '1', '1', '_#UNDEFINE', 'f_t', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '1', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'hl_href', '', 'AA_Core_Fields..', 'Headline URL', '1655', 'Link for the headline (for external links)', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_f:link_only.......', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'url', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'link_only', '', 'AA_Core_Fields..', 'External item', '1755', 'Use External link instead of fulltext?', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'chb', '', '100', '', '', '', '', '0', '0', '1', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'bool', 'boo', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'place', '', 'AA_Core_Fields..', 'Locality', '2155', 'Item locality', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'source', '', 'AA_Core_Fields..', 'Source', '1955', 'Source of the item', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'source_href', '', 'AA_Core_Fields..', 'Source URL', '2055', 'URL of the source', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_s:javascript: window.alert(\'No source url specified\')', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'url', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'lang_code', '', 'AA_Core_Fields..', 'Language Code', '1700', 'Code of used language', 'http://aa.ecn.cz/aa/doc/help.html', 'txt:EN', '0', '0', '0', 'sel:lt_languages', '', '100', '', '', '', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'cp_code', '', 'AA_Core_Fields..', 'Code Page', '1800', 'Language Code Page', 'http://aa.ecn.cz/aa/doc/help.html', 'txt:iso8859-1', '0', '0', '0', 'sel:lt_codepages', '', '100', '', '', '', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'category', '', 'AA_Core_Fields..', 'Category', '1000', 'Category', 'http://aa.ecn.cz/aa/doc/help.html', 'txt:', '0', '0', '0', 'sel:lt_apcCategories', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'img_src', '', 'AA_Core_Fields..', 'Image URL', '2055', 'URL of the image', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#UNDEFINE', 'f_i', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'url', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'img_width', '', 'AA_Core_Fields..', 'Image width', '2455', 'Width of image (like: 100, 50%)', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#UNDEFINE', 'f_w', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'img_height', '', 'AA_Core_Fields..', 'Image height', '2555', 'Height of image (like: 100, 50%)', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#UNDEFINE', 'f_g', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'e_posted_by', '', 'AA_Core_Fields..', 'Author`s e-mail', '2255', 'E-mail to author', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'email', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'created_by', '', 'AA_Core_Fields..', 'Created By', '2355', 'Identification of creator', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'nul', '', '100', '', '', '', '', '0', '0', '0', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'uid', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'edit_note', '', 'AA_Core_Fields..', 'Editor`s note', '2355', 'There you can write your note (not displayed on the web)', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'txt', '', '100', '', '', '', '', '0', '0', '0', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'img_upload', '', 'AA_Core_Fields..', 'Image upload', '2222', 'Select Image for upload', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fil:image/*', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'fil', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'lang_code', '', 'AA_Core_Fields..', 'Language Code', '1700', 'Code of used language', 'http://aa.ecn.cz/aa/doc/help.html', 'txt:EN', '0', '0', '0', 'sel:lt_languages', '', '100', '', '', '', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'source_desc', '', 'AA_Core_Fields..', 'Source description', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'source_addr', '', 'AA_Core_Fields..', 'Source address', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'source_city', '', 'AA_Core_Fields..', 'Source city', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'source_prov', '', 'AA_Core_Fields..', 'Source province', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'source_cntry', '', 'AA_Core_Fields..', 'Source country', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'time', '', 'AA_Core_Fields..', 'Time', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '0');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'con_name', '', 'AA_Core_Fields..', 'Contact name', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'con_email', '', 'AA_Core_Fields..', 'Contact e-mail', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'con_phone', '', 'AA_Core_Fields..', 'Contact phone', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'con_fax', '', 'AA_Core_Fields..', 'Contact fax', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'loc_name', '', 'AA_Core_Fields..', 'Location name', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'loc_address', '', 'AA_Core_Fields..', 'Location address', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'loc_city', '', 'AA_Core_Fields..', 'Location city', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'loc_prov', '', 'AA_Core_Fields..', 'Location province', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'loc_cntry', '', 'AA_Core_Fields..', 'Location country', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'start_date', '', 'AA_Core_Fields..', 'Start date', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'now', '1', '0', '0', 'dte:1:10:1', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_d:m/d/Y', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'date', 'dte', '1', '0');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'end_date', '', 'AA_Core_Fields..', 'End date', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'now', '1', '0', '0', 'dte:1:10:1', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_d:m/d/Y', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'date', 'dte', '1', '0');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'keywords', '', 'AA_Core_Fields..', 'Keywords', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'subtitle', '', 'AA_Core_Fields..', 'Subtitle', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'year', '', 'AA_Core_Fields..', 'Year', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'number', '', 'AA_Core_Fields..', 'Number', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'number', 'num', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'page', '', 'AA_Core_Fields..', 'Page', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'number', 'num', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'price', '', 'AA_Core_Fields..', 'Price', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'number', 'num', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'organization', '', 'AA_Core_Fields..', 'Organization', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'file', '', 'AA_Core_Fields..', 'File upload', '2222', 'Select file for upload', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fil:*/*', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'fil', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'text', '', 'AA_Core_Fields..', 'Text', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'unspecified', '', 'AA_Core_Fields..', 'Unspecified', '100', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_h', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'url', '', 'AA_Core_Fields..', 'URL', '2055', 'Internet URL address', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#UNDEFINE', 'f_i', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'url', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'switch', '', 'AA_Core_Fields..', 'Switch', '2055', '', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'chb', '', '100', '', '', '', '', '0', '0', '0', '_#UNDEFINE', 'f_i', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'boo', '1', '0');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'password', '', 'AA_Core_Fields..', 'Password', '2055', 'Password which user must know if (s)he want to edit item on public site', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#UNDEFINE', 'f_i', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'relation', '', 'AA_Core_Fields..', 'Relation', '2055', '', '', 'txt:', '0', '0', '1', 'mse:#sLiCe-4e6577735f454e5f746d706c2e2e2e2e:', '', '100', '', '', '', '', '1', '1', '1', '_#UNDEFINE', 'f_v:vid=243&cmd[243]=x-243-_#this', 'alias undefined - see Admin pages - Field setting', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');


# --------------------------------------------------------
# Templete slices

INSERT INTO slice (id, name, owner, deleted, created_by, created_at, export_to_all, type, template, fulltext_format_top, fulltext_format, fulltext_format_bottom, odd_row_format, even_row_format, even_odd_differ, compact_top, compact_bottom, category_top, category_format, category_bottom, category_sort, config, slice_url, d_expiry_limit, d_listlen, lang_file, fulltext_remove, compact_remove, email_sub_enable, exclude_from_dir, notify_sh_offer, notify_sh_accept, notify_sh_remove, notify_holding_item_s, notify_holding_item_b, notify_holding_item_edit_s, notify_holding_item_edit_b, notify_active_item_edit_s, notify_active_item_edit_b, notify_active_item_s, notify_active_item_b, noitem_msg, admin_format_top, admin_format, admin_format_bottom, admin_remove, permit_anonymous_post, permit_offline_fill, aditional, flag, vid, gb_direction, group_by, gb_header, gb_case) VALUES( 'News_EN_tmpl....', 'News (EN) Template', 'AA_Core.........', '0', '', '975157733', '0', 'News_EN_tmpl....', '1', '', '<BR><FONT SIZE=+2 COLOR=blue>_#HEADLINE</FONT> <BR><B>_#PUB_DATE</B> <BR><img src=\"_#IMAGESRC\" width=\"_#IMGWIDTH\" height=\"_#IMG_HGHT\">_#FULLTEXT ', '','<font face=Arial color=#808080 size=-2>_#PUB_DATE - </font><font color=#FF0000><strong><a href=_#HDLN_URL>_#HEADLINE</a></strong></font><font color=#808080 size=-1><br>_#PLACE###(_#LINK_SRC) - </font><font color=black size=-1>_#ABSTRACT<br></font><br>', '', '0', '<br>', '<br>', '', '<p>_#CATEGORY</p>', '', '1', '', 'http://aa.ecn.cz', '5000', '10000', 'en_news_lang.php3', '()', '()', '1', '0', '', '', '', '', '', '', '', '', '', '', '', 'No item found', '<tr class=tablename><td width=30>&nbsp;</td><td>Click on Headline to Edit</td><td>Date</td></tr>', '<tr class=tabtxt><td width=30><input type=checkbox name="chb[x_#ITEM_ID#]" value="1"></td><td><a href="_#EDITITEM">_#HEADLINE</a></td><td>_#PUB_DATE</td></tr>', '', '', '1', '1', '', '0', '0', NULL, NULL, NULL, NULL);

INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'abstract........', '', 'News_EN_tmpl....', 'Abstract', '150', 'Abstract', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'txt:8', '', '100', '', '', '', '', '0', '1', '1', '_#ABSTRACT', 'f_t', 'alias for abstract', '_#RSS_IT_D', 'f_r:256', 'Abstract for RSS', '', '', '', '', '', '0', '0', '1', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'category........', '', 'News_EN_tmpl....', 'Category', '500', 'Category', 'http://aa.ecn.cz/aa/doc/help.html', 'txt:', '0', '0', '0', 'sel:lt_apcCategories', '', '100', '', '', '', '', '1', '1', '1', '_#CATEGORY', 'f_h', 'alias for Item Category', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '0', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'cp_code.........', '', 'News_EN_tmpl....', 'Code Page', '1800', 'Language Code Page', 'http://aa.ecn.cz/aa/doc/help.html', 'txt:iso8859-1', '0', '0', '0', 'sel:lt_codepages', '', '100', '', '', '', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '0', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'created_by......', '', 'News_EN_tmpl....', 'Author', '470', 'Identification of creator', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#CREATED#', 'f_h', 'alias for Written By', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'edited_by.......', '', 'News_EN_tmpl....', 'Edited by', '5030', 'Identification of last editor', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'nul', '', '100', '', '', '', '', '0', '0', '0', '_#EDITEDBY', 'f_h', 'alias for Last edited By', '', '', '', '', '', '', '', '', '0', '0', '0', 'edited_by', 'text', 'uid', '0', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'edit_note.......', '', 'News_EN_tmpl....', 'Editor`s note', '2355', 'There you can write your note (not displayed on the web)', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'txt', '', '100', '', '', '', '', '0', '0', '0', '_#EDITNOTE', 'f_h', 'alias for Editor`s note', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'expiry_date.....', '', 'News_EN_tmpl....', 'Expiry Date', '955', 'Date when the news expires', 'http://aa.ecn.cz/aa/doc/help.html', 'dte:2000', '1', '0', '0', 'dte:1:10:1', '', '100', '', '', '', '', '0', '0', '0', '_#EXP_DATE', 'f_d:m/d/Y', 'alias for Expiry Date', '', '', '', '', '', '', '', '', '0', '0', '0', 'expiry_date', 'date', 'dte', '1', '0');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'e_posted_by.....', '', 'News_EN_tmpl....', 'Author`s e-mail', '480', 'E-mail to author', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#E_POSTED', 'f_h', 'alias for Author`s e-mail', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'email', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'full_text.......', '', 'News_EN_tmpl....', 'Fulltext', '200', 'Fulltext', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'txt:8', '', '100', '', '', '', '', '0', '1', '1', '_#FULLTEXT', 'f_t', 'alias for Fulltext<br>(HTML tags are striped or not depending on HTML formated item setting)', '', '', '', '', '', '', '', '', '0', '0', '1', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'headline........', '', 'News_EN_tmpl....', 'Headline', '100', 'Headline of the news', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '1', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#HEADLINE', 'f_h', 'alias for Item Headline', '_#RSS_IT_T', 'f_r:100', 'item title, for RSS', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'highlight.......', '', 'News_EN_tmpl....', 'Highlight', '450', 'Interesting news - shown on homepage', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'chb', '', '100', '', '', '', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', 'highlight', 'bool', 'boo', '1', '0');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'hl_href.........', '', 'News_EN_tmpl....', 'Headline URL', '400', 'Link for the headline (for external links)', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#HDLN_URL', 'f_f:link_only.......', 'alias for News URL<br>(substituted by External news link URL(if External news is checked) or link to Fulltext)<div class=example><em>Example: </em>&lt;a href=_#HDLN_URL&gt;_#HEADLINE&lt;/a&gt;</div>', '_#RSS_IT_L', 'f_r:link_only.......', 'item link, for RSS', '', '', '', '', '', '0', '0', '0', '', 'url', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'img_height......', '', 'News_EN_tmpl....', 'Image height', '2300', 'Height of image (like: 100, 50%)', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#IMG_HGHT', 'f_g', 'alias for Image Height<br>(if no height defined, program tries to remove <em>height=</em> atribute from format string<div class=example><em>Example: </em>&lt;img src=\"_#IMAGESRC\" width=_#IMGWIDTH height=_#IMG_HGHT&gt;</div>', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'img_src.........', '', 'News_EN_tmpl....', 'Image URL', '2100', 'URL of the image', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#IMAGESRC', 'f_i', 'alias for Image URL<br>(if there is no image url defined in database, default url is used instead (see NO_PICTURE_URL constant in en_*_lang.php3 file))<div class=example><em>Example: </em>&lt;img src=\"_#IMAGESRC\"&gt;</div>', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'url', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'img_width.......', '', 'News_EN_tmpl....', 'Image width', '2200', 'Width of image (like: 100, 50%)', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#IMGWIDTH', 'f_w', 'alias for Image Width<br>(if no width defined, program tries to remove <em>width=</em> atribute from format string<div class=example><em>Example: </em>&lt;img src=\"_#IMAGESRC\" width=_#IMGWIDTH height=_#IMG_HGHT&gt;</div>', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'lang_code.......', '', 'News_EN_tmpl....', 'Language Code', '1700', 'Code of used language', 'http://aa.ecn.cz/aa/doc/help.html', 'txt:EN', '0', '0', '0', 'sel:lt_languages', '', '100', '', '', '', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '0', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'last_edit.......', '', 'News_EN_tmpl....', 'Last Edit', '5040', 'Date of last edit', 'http://aa.ecn.cz/aa/doc/help.html', 'now:', '0', '0', '0', 'dte:1:10:1', '', '100', '', '', '', '', '0', '0', '0', '_#LASTEDIT', 'f_d:m/d/Y', 'alias for Last Edit', '', '', '', '', '', '', '', '', '0', '0', '0', 'last_edit', 'date', 'now', '0', '0');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'link_only.......', '', 'News_EN_tmpl....', 'External news', '300', 'Use External link instead of fulltext?', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'chb', '', '100', '', '', '', '', '0', '0', '1', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'bool', 'boo', '1', '0');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'place...........', '', 'News_EN_tmpl....', 'Locality', '630', 'News locality', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#PLACE###', 'f_h', 'alias for Locality', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'posted_by.......', '', 'News_EN_tmpl....', 'Posted by', '5035', 'Identification of author', 'http://aa.ecn.cz/aa/doc/help.html', 'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#POSTEDBY', 'f_h', 'alias for Author', '', '', '', '', '', '', '', '', '0', '0', '0', 'posted_by', 'text', 'qte', '0', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'post_date.......', '', 'News_EN_tmpl....', 'Post Date', '5005', 'Date of posting this news', 'http://aa.ecn.cz/aa/doc/help.html',              'now:', '1', '0', '0', 'nul', '', '100', '', '', '', '', '0', '0', '0', '_#POSTDATE', 'f_d:m/d/Y', 'alias for Post Date', '', '', '', '', '', '', '', '', '0', '0', '0', 'post_date', 'date', 'now', '0', '0');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'publish_date....', '', 'News_EN_tmpl....', 'Publish Date', '900', 'Date when the news will be published', 'http://aa.ecn.cz/aa/doc/help.html', 'now:', '1', '0', '0', 'dte:1:10:1', '', '100', '', '', '', '', '0', '0', '0', '_#PUB_DATE', 'f_d:m/d/Y', 'alias for Publish Date', '', '', '', '', '', '', '', '', '0', '0', '0', 'publish_date', 'date', 'dte', '1', '0');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'source..........', '', 'News_EN_tmpl....', 'Source', '600', 'Source of the news', 'http://aa.ecn.cz/aa/doc/help.html',                         'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#SOURCE##', 'f_h', 'alias for Source Name<br>(see _#LINK_SRC for text source link)', '_#SRC_URL#', 'f_l:source_href.....', 'alias for Source with URL<br>(if there is no source url defined in database, the source is displayed as link)', '', '', '', '', '', '0', '0', '0', '', 'text', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'source_href.....', '', 'News_EN_tmpl....', 'Source URL', '610', 'URL of the source', 'http://aa.ecn.cz/aa/doc/help.html',                      'qte', '0', '0', '0', 'fld', '', '100', '', '', '', '', '1', '1', '1', '_#LINK_SRC', 'f_l', 'alias for Source Name with link.<br>(substituted by &lt;a href=\"_#SRC_URL#\"&gt;_#SOURCE##&lt;/a&gt; if Source URL defined, otherwise _#SOURCE## only)', '', '', '', '', '', '', '', '', '0', '0', '0', '', 'url', 'qte', '1', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'status_code.....', '', 'News_EN_tmpl....', 'Status Code', '5020', 'Select in which bin should the news appear', 'http://aa.ecn.cz/aa/doc/help.html', 'qte:1', '1', '0', '0', 'sel:AA_Core_Bins....', '', '100', '', '', '', '', '0', '0', '0', '', '', '', '', '', '', '', '', '', '', '', '0', '0', '0', 'status_code', 'number', 'num', '0', '0');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'slice_id........', '', 'News_EN_tmpl....', 'Slice', '5000', 'Internal field - do not change', 'http://aa.ecn.cz/aa/doc/help.html', 'qte:1', '1', '0', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#SLICE_ID', 'f_n:slice_id........', 'alias for id of slice', '', '', '', '', '', '', '', '', '0', '0', '0', 'slice_id', '', 'nul', '0', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'display_count...', '', 'News_EN_tmpl....', 'Displayed Times', '5050', 'Internal field - do not change', 'http://aa.ecn.cz/aa/doc/help.html', 'qte:0', '1', '1', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#DISPL_NO', 'f_h', 'alias for number of displaying of this item', '', '', '', '', '', '', '', '', '0', '0', '0', 'display_count', '', 'nul', '0', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'disc_count......', '', 'News_EN_tmpl....', 'Comments Count', '5060', 'Internal field - do not change', 'http://aa.ecn.cz/aa/doc/help.html', 'qte:0', '1', '1', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#D_ALLCNT', 'f_h', 'alias for number of all discussion comments for this item', '', '', '', '', '', '', '', '', '0', '0', '0', 'disc_count', '', 'nul', '0', '1');
INSERT INTO field (id, type, slice_id, name, input_pri, input_help, input_morehlp, input_default, required, feed, multiple, input_show_func, content_id, search_pri, search_type, search_help, search_before, search_more_help, search_show, search_ft_show, search_ft_default, alias1, alias1_func, alias1_help, alias2, alias2_func, alias2_help, alias3, alias3_func, alias3_help, input_before, aditional, content_edit, html_default, html_show, in_item_tbl, input_validate, input_insert_func, input_show, text_stored) VALUES( 'disc_app........', '', 'News_EN_tmpl....', 'Approved Comments Count', '5070', 'Internal field - do not change', 'http://aa.ecn.cz/aa/doc/help.html', 'qte:0', '1', '1', '0', 'fld', '', '100', '', '', '', '', '0', '0', '0', '_#D_APPCNT', 'f_h', 'alias for number of approved discussion comments for this item', '', '', '', '', '', '', '', '', '0', '0', '0', 'disc_app', '', 'nul', '0', '1');

# --------------------------------------------------------
# Templete views

INSERT INTO view (id, slice_id, name, type, before, even, odd, even_odd_differ, after, remove_string, group_title, order1, o1_direction, order2, o2_direction, group_by1, g1_direction, group_by2, g2_direction, cond1field, cond1op, cond1cond, cond2field, cond2op, cond2cond, cond3field, cond3op, cond3cond, listlen, scroller, selected_item, modification, parameter, img1, img2, img3, img4, flag, aditional, aditional2, aditional3, aditional4, aditional5, aditional6, noitem_msg, group_bottom, field1, field2, field3, calendar_type) VALUES ('', 'AA_Core_Fields..', 'Discussion ...', 'discus', '<table bgcolor=#000000 cellspacing=0 cellpadding=1 border=0><tr><td><table width=100% bgcolor=#f5f0e7 cellspacing=0 cellpadding=0 border=0><tr><td colspan=8><big>Comments</big></td></tr>', '<table  width=500 cellspacing=0 cellpadding=0 border=0><tr><td colspan=2><hr></td></tr><tr><td width=20%><b>Date:</b></td><td> _#DATE####</td></tr><tr><td><b>Comment:</b></td><td> _#SUBJECT#</td></tr><tr><td><b>Author:</b></td><td><A href=mailto:_#EMAIL###>_#AUTHOR##</a></td></tr><tr><td><b>WWW:</b></td><td><A href=_#WWW_URL#>_#WWW_DESC</a></td></tr><tr><td><b>IP:</b></td><td>_#IP_ADDR#</td></tr><tr><td colspan=2>&nbsp;</td></tr><tr><td colspan=2>_#BODY####</td></tr><tr><td colspan=2>&nbsp;</td></tr><tr><td colspan=2><a href=_#URLREPLY>Reply</a></td></tr></table><br>', '<tr><td width="10">&nbsp;</td><td><font size=-1>_#CHECKBOX</font></td><td width="10">&nbsp;</td><td align=center nowrap><SMALL>_#DATE####</SMALL></td><td width="20">&nbsp;</td><td nowrap>_#AUTHOR## </td><td><table cellspacing=0 cellpadding=0 border=0><tr><td>_#TREEIMGS</td><td><img src=http://work.ecn.cz/apc-aa/images/blank.gif width=2 height=21></td><td nowrap>_#SUBJECT#</td></tr></table></td><td width="20">&nbsp;</td></tr>', 1, '</table></td></tr></table>_#BUTTONS#', '<SCRIPT Language="JavaScript"><!--function checkData() { var text=""; if(!document.f.d_subject.value) { text+="subject " } if (text!="") { alert("Please, fill the field: " + text);  return false; } return true; } // --></SCRIPT><form name=f method=post action="/apc-aa/filldisc.php3" onSubmit=" return checkData()"><p>Author<br><input type=text name=d_author > <p>Subject<br><input type=text name=d_subject value="_#SUBJECT#"><p>E-mail<br><input type=text name=d_e_mail><p>Comment<br><textarea rows="5" cols="40" name=d_body ></textarea><p>WWW<br><input type=text name=d_url_address value="http://"><p>WWW description<br><input type=text name=d_url_description><br><input type=submit value=Send align=center><input type=hidden name=d_parent value="_#DISC_ID#"><input type=hidden name=d_item_id value="_#ITEM_ID#"><input type=hidden name=url value="_#DISC_URL"></FORM>', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 23, NULL, '<img src=http://work.ecn.cz/apc-aa/images/i.gif width=9 height=21>', '<img src=http://work.ecn.cz/apc-aa/images/l.gif width=9 height=21>', '<img src=http://work.ecn.cz/apc-aa/images/t.gif width=9 height=21>', '<img src=http://work.ecn.cz/apc-aa/images/blank.gif width=12 height=21>', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No item found', NULL, '', NULL, NULL, 'mon');
INSERT INTO view (id, slice_id, name, type, before, even, odd, even_odd_differ, after, remove_string, group_title, order1, o1_direction, order2, o2_direction, group_by1, g1_direction, group_by2, g2_direction, cond1field, cond1op, cond1cond, cond2field, cond2op, cond2cond, cond3field, cond3op, cond3cond, listlen, scroller, selected_item, modification, parameter, img1, img2, img3, img4, flag, aditional, aditional2, aditional3, aditional4, aditional5, aditional6, noitem_msg, group_bottom, field1, field2, field3, calendar_type) VALUES ('', 'AA_Core_Fields..', 'Constant view ...', 'const', '<table border=0 cellpadding=0 cellspacing=0>', '', '<tr><td>_#VALUE###</td></tr>', 0, '</table>', NULL, NULL, 'value', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10, NULL, 0, NULL, 'lt_languages', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No item found', NULL, '', NULL, NULL, 'mon');
INSERT INTO view (id, slice_id, name, type, before, even, odd, even_odd_differ, after, remove_string, group_title, order1, o1_direction, order2, o2_direction, group_by1, g1_direction, group_by2, g2_direction, cond1field, cond1op, cond1cond, cond2field, cond2op, cond2cond, cond3field, cond3op, cond3cond, listlen, scroller, selected_item, modification, parameter, img1, img2, img3, img4, flag, aditional, aditional2, aditional3, aditional4, aditional5, aditional6, noitem_msg, group_bottom, field1, field2, field3, calendar_type) VALUES ('', 'AA_Core_Fields..', 'Javascript ...', 'script', '/* output of this script can be included to any page on any server by adding:&lt;script type="text/javascript" src="http://work.ecn.cz/apc-aa/view.php3?vid=3"&gt; &lt;/script&lt; or such.*/', NULL, 'document.write("_#HEADLINE");', NULL, '// script end ', NULL, NULL, '', 0, '', 0, NULL, NULL, NULL, NULL, '', '<', '', '', '<', '', '', '<', '', 8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No item found', NULL, '', NULL, NULL, 'mon');
INSERT INTO view (id, slice_id, name, type, before, even, odd, even_odd_differ, after, remove_string, group_title, order1, o1_direction, order2, o2_direction, group_by1, g1_direction, group_by2, g2_direction, cond1field, cond1op, cond1cond, cond2field, cond2op, cond2cond, cond3field, cond3op, cond3cond, listlen, scroller, selected_item, modification, parameter, img1, img2, img3, img4, flag, aditional, aditional2, aditional3, aditional4, aditional5, aditional6, noitem_msg, group_bottom, field1, field2, field3, calendar_type) VALUES ('', 'AA_Core_Fields..', 'rss', 'rss', '<!DOCTYPE rss PUBLIC "-//Netscape Communications//DTD RSS 0.91//EN" "<http://my.netscape.com/publish/formats/rss-0.91.dtd>http://my.netscape.com/publish/formats/rss-0.91.dtd"> <rss version="0.91"> <channel>  <title>_#RSS_TITL</title>  <link>_#RSS_LINK</link>  <description>_#RSS_DESC</description>  <lastBuildDate>_#RSS_DATE</lastBuildDate> <language></language>', NULL, ' <item> <title>_#RSS_IT_T</title> <link>_#RSS_IT_L</link> <description>_#RSS_IT_D</description> </item>', NULL, '</channel></rss>>', NULL, NULL, 'publish_date....', 0, 'headline........', 0, NULL, NULL, NULL, NULL, 'source..........', '', '', '', '<', '', '', '<', '', 15, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NO ITEM FOUND', NULL, NULL, NULL, NULL, 'mon');
INSERT INTO view (id, slice_id, name, type, before, even, odd, even_odd_differ, after, remove_string, group_title, order1, o1_direction, order2, o2_direction, group_by1, g1_direction, group_by2, g2_direction, cond1field, cond1op, cond1cond, cond2field, cond2op, cond2cond, cond3field, cond3op, cond3cond, listlen, scroller, selected_item, modification, parameter, img1, img2, img3, img4, flag, aditional, aditional2, aditional3, aditional4, aditional5, aditional6, noitem_msg, group_bottom, field1, field2, field3, calendar_type) VALUES ('', 'AA_Core_Fields..', 'Calendar', 'calendar', '<table border=1>\r\n<tr><td>Mon</td><td>Tue</td><td>Wen</td><td>Thu</td><td>Fri</td><td>Sat</td><td>Sun</td></tr>', NULL, '_#STARTDAT-_#END_DATE <b>_#HEADLINE</b>', 1, '</table>', '', '<td><font size=+2><A href=\"calendar.shtml?vid=319&cmd[319]=c-1-_#CV_TST_2-2-_#CV_TST_1&month=_#CV_NUM_M&year=_#CV_NUM_Y&day=_#CV_NUM_D\"><B>_#CV_NUM_D</B></A></font></td>', '', 0, '', 0, NULL, NULL, NULL, NULL, 'publish_date....', '<', '', '', '<', '', '', '<', '', 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '<td><font size=+2>_#CV_NUM_D</font></td>', '', 'bgcolor=\"_#COLOR___\"', NULL, NULL, NULL, 'There are no events in this month.', '', 'start_date.....1', 'end_date.......1', NULL, 'mon_table');

REPLACE INTO module (id, name, deleted, type, slice_url, lang_file, created_at, created_by, owner) SELECT id, name, deleted, 'S', slice_url, lang_file, created_at, created_by, owner FROM slice;
