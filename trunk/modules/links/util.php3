<?php 
//$Id$

# Miscellaneous utility functions ---------------------------------------------

# Counts links in category subtree (only real) and stores value to db
function CountCategLinks($path, $cid) {
  global $db;
  $SQL= " SELECT count(*) as links_count FROM links_categories, links_link_cat 
  	WHERE links_link_cat.category_id = links_categories.id
  	  AND links_link_cat.proposal = 'n'
  	  AND (links_categories.path = '$path' OR links_categories.path like '$path,%')";
  $db->query($SQL);

  if ($db->next_record()) {
    $links_count = $db->f('links_count');
    $SQL= "UPDATE LOW_PRIORITY links_categories SET link_count = '$links_count'
            WHERE id='$cid'";
    $db->query($SQL);
  }  
  return $links_count;
}

# Counts links in all subcategories
function CountAllLinks() {
  $db2  = new DB_AA;   // can't use $db - colision with CountCategLinks
  $SQL= "SELECT id, path FROM links_categories";
  $db2->query($SQL);
  while($db2->next_record())
    CountCategLinks($db2->f('path'), $db2->f('id'));
}  

# Counts links in each category and returns array[category]=link_count
function CountLinks4Each() {
  global $db;
  $SQL= " SELECT category_id, count(*) as links_count FROM links_link_cat 
  	WHERE links_link_cat.proposal = 'n'
    GROUP BY category_id";
  $db->query($SQL);

  while ($db->next_record())
    $links_count[$db->f('category_id')] = $db->f('links_count');
  return  $links_count;
}
  
# Get all informations about link
function GetLinkInfo( $lid ) {
  global $db;
  $SQL = "SELECT * FROM links_links WHERE id = '$lid'";
  $db->query($SQL);
  return ( $db->next_record() ? $db->Record : "");
}  

# Get path from category id
function GetCategoryPath( $cid ) {
  global $db;
  $db->query("SELECT path FROM links_categories WHERE id=$cid");
  return ( $db->next_record() ? $db->f(path) : "");
}  

# Get category id from path
function GetCategoryFromPath( $path ) {
  if( strrchr ($path, ",") )
    return   substr (strrchr ($path, ","), 1);
  return $path;  
}  

# Get base path from link id
function GetBaseCategoryPath( $lid ) {
  global $db;
  $db->query("SELECT path FROM links_categories, links_link_cat
               WHERE links_categories.id = links_link_cat.category_id
                 AND what_id='$lid'
                 AND links_link_cat.base='y'");
  return ( $db->next_record() ? $db->f('path') : "");
}  
    
# Transforms path to named path with links ( <a href=...>Base</a> > <a ...)
#   based on $translate array; skips first "skip" fields
#   url: ""      - do not make links on categories
#        url     - make links to categories except the last one
#   whole - if set, make links to all categories

function NamePath($skip, $path, $translate, $separator = " > ", $url="", $whole=false, $target="") {
  $target_atrib = $target != "" ? " target=\"$target\" " : "";
  $ids = explode(",",$path);
  if( isset($ids) AND is_array($ids)) {
    $last=end($ids);
    reset($ids);
    if( $url ) {
      while(list(,$catid) = each($ids)) {
        if(--$skip >= 0)
          continue;
        if( ($catid != $last) OR $whole )  // do not make link for last category  
          $name .= $delimeter."<a href=\"$url$catid\" $target_atrib>".$translate[$catid]."</a>";
         else 
          $name .= $delimeter.$translate[$catid];
        $delimeter = $separator;
      }  
    }else{
      while(list(,$catid) = each($ids)) {
        if(--$skip >= 0)
          continue;
        $name .= $delimeter.$translate[$catid];
        $delimeter = $separator;
      }  
    }  
  }  
  return $name;
}  

# Returns HTML code for image link to specified url
function AHrefImg($url, $src, $width="", $height="", $alt="") {
  if($url)
    return "<a href=\"$url\"><img src=\"$src\" width=\"$width\" height=\"$height\" alt=\"$alt\" border=\"0\"></a>";
  return "<img src=\"$src\" width=\"$width\" height=\"$height\" alt=\"$alt\" border=\"0\">";
}  

# returns url of requested file
function ThisFileName() {
  if( $GLOBALS['SERVER_PROTOCOL']=='INCLUDED' ) {
    return $GLOBALS['DOCUMENT_URI'];
  }  
  return $GLOBALS['PHP_SELF'];
}    

function FillCategoryInfo($category) {
	global $db, $r_category_id, $r_category_path, $r_category_template;
  	$SQL= "SELECT * FROM links_categories WHERE id = $category";
  	$db->query($SQL);
  	if($db->next_record()) {
    	$r_category_id       = $db->f(id);
    	$r_category_template = $db->f(html_template);
    	$r_category_path     = $db->f(path);
  	}
}      

# get information from view table, where information for viewing are stored
function Links_GetViewInfo($view_id, $server_url) {
	global $db;
	
	if ($view_id)
		$test = " (id = $view_id) ";
    elseif (!$server_url)
  	    $test = " (id = ".DEFAULT_BASE_CATEGORY." )"; 
    elseif (strlen($server_url) <= 4)
		$test = " (server_name = '$server_url') ";
    else 
        $test = ( (StrCaseCmp(SubStr($server_url, 0, 4), "www.") == 0) ? 
			" (server_name = '". Substr($server_url,4) . "') " :
      " (server_name = '$server_url') " );

	$SQL = " SELECT * FROM links_views WHERE $test";
	$db->query($SQL);

	if ($db->next_record())
       return $db->Record;

	return false;
}

# get information from profile table, where user setting are stored
function GetProfileInfo($uid) {
	global $db;
	
  if( !$uid )
    $uid = "nobody";

	$SQL = "SELECT * FROM links_profiles WHERE uid = '$uid'";
	$db->query($SQL);
	if ($db->next_record())
    return $db->Record;

  # if user not exist - get nobody's settings
	$SQL = "SELECT * FROM links_profiles WHERE uid = 'nobody'";
	$db->query($SQL);
	if ($db->next_record())
    return $db->Record;
	return false;
}

function TestBaseCat($ctg, $base_cat, $ctg_path) {
	$cats = explode(",", $ctg_path);
	for ($found = false, reset($cats); current($cats); next($cats))
		if (current($cats) == $base_cat) {
			$found = true; break; 
		}

	return ($found ? $ctg : $base_cat);
}

/** 
 * Slice id for each category in Links module is not random - it is predictable:
 * <category_id>'Links'<shorted AA_ID>
 * @returns packed slice id
 */
function Links_Category2SliceID($cid) {
    return unpack_id128(substr( $cid.'Links'.q_pack_id(AA_ID), 0, 16 ));
}

/** 
 * Just helper function for storing data from database to Abstract Data Structure
 */
function StoreTable2Content(&$db, &$content, $SQL, $prefix, $id_field) {
    $db->tquery($SQL);
    while( $db->next_record() ) {
        $foo_id = $db->f($id_field);
        reset( $db->Record );
        while( list( $key, $val ) = each( $db->Record )) {
            if( !is_int($key))
                $content[$foo_id][$prefix . $key][] = array('value' => $val);
        }  
    }  
}


/** 
 * Loads data from database for given link ids (called in itemview class)
 * and stores it in the 'Abstract Data Structure' for use with 'item' class
 * 
 * @see GetItemContent(), itemview class, item class
 * @param array $ids array if ids to get from database 
 * @return array - Abstract Data Structure containing the links data 
 *                 {@link http://apc-aa.sourceforge.net/faq/#1337}
 */
function Links_GetLinkContent($zids) {
    global $db;
    
    if (!is_object($db)) 
        $db = new DB_AA;
    
    if( !$zids )
        return false;

    // construct WHERE clausule
    $sel_in = $zids->sqlin( false );

    // get not rejected changes                         
    $SQL = "SELECT changed_link_id, proposal_link_id FROM links_changes 
             WHERE changed_link_id $sel_in 
               AND rejected='n'";              // get only not rejected changes
    $db->tquery($SQL);
    while( $db->next_record() ) {
        $changes_ids[] = $db->f('proposal_link_id');
        $changes_map[$db->f('proposal_link_id')] = $db->f('changed_link_id');
    }    

    if( isset($changes_ids) AND is_array($changes_ids) )
        $changes_where = ' OR id '. (count($changes_ids)>1 ? 
        'IN ('. implode( ",", $changes_ids ). ')' : "='". $changes_ids[0] ."'");
        
    // get link data (including data of link changes)
    $SQL = "SELECT * FROM links_links WHERE id $sel_in $changes_where";
    $db->tquery($SQL);
    while( $db->next_record() ) {
        $foo_id = $db->f('id');
        reset( $db->Record );
        while( list( $key, $val ) = each( $db->Record )) {
            if( is_int($key))
                continue;
            if( $changes_map[$foo_id] )    // this link is just change-link
                $content[$changes_map[$foo_id]]["change_$key"][] = array('value' => $val);
            else  
                $content[$foo_id][$key][] = array('value' => $val);
        }  
    }  

    // get language data for links
    $SQL = "SELECT links_languages.*, links_link_lang.link_id 
              FROM links_link_lang, links_languages 
             WHERE links_languages.id = links_link_lang.lang_id
               AND links_link_lang.link_id $sel_in";
    StoreTable2Content($db, $content, $SQL, 'lang_', 'link_id');

    // get region data for links
    $SQL = "SELECT links_regions.*, links_link_reg.link_id 
              FROM links_link_reg, links_regions 
             WHERE links_regions.id = links_link_reg.region_id
               AND links_link_reg.link_id $sel_in";
    StoreTable2Content($db, $content, $SQL, 'reg_', 'link_id');

    // get categories data for links
    $SQL = "SELECT * FROM links_link_cat, links_categories 
             WHERE links_categories.id = links_link_cat.category_id
               AND links_link_cat.what_id $sel_in";
    StoreTable2Content($db, $content, $SQL, 'cat_', 'what_id');
    return $content;
}  

/** 
 * Is current user anonymous (=public) user? 
 * @return boolean true or false 
 */
function Links_IsPublic() {
    global $perms_roles;
    
    return $GLOBALS['perms_roles']['AUTHOR']['perm'] == 
           $GLOBALS['permission_to']["slice"][$GLOBALS['slice_id']];
} 

?>
