<?php
/**
 * Functions for preparing the Filters and Collections output and sending Alerts with new messages.
 * 
 * @package Alerts
 * @version $Id$
 * @author Jakub Ad�mek <jakubadamek@ecn.cz>, Econnect, December 2002
 * @copyright Copyright (C) 1999-2002 Association for Progressive Communications 
*/
/* 
Copyright (C) 1999-2002 Association for Progressive Communications 
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

//require_once "lang.php3";
require_once $GLOBALS["AA_INC_PATH"]."item.php3";
require_once $GLOBALS["AA_INC_PATH"]."view.php3";
require_once $GLOBALS["AA_INC_PATH"]."pagecache.php3";
require_once $GLOBALS["AA_INC_PATH"]."searchlib.php3";
require_once $GLOBALS["AA_INC_PATH"]."mail.php3";
require_once $GLOBALS["AA_INC_PATH"]."item_content.php3";
require_once "util.php3";

//$debug = 1;

if(! is_object( $db )) $db = new DB_AA;

// -------------------------------------------------------------------------------------
/**  Creates Filter output. Called from send_emails().
*    Finds and formats items.
*    WARNING:  the function does not check when were the items sent last time, 
*              call it only once a day / week / month. In fact, this function
*              works completely the same for any how often (only that it writes
*              the values to the appropriate field). 
*
* The only exception are the "instant" messages: If the paramter $item_id
* contains an unpacked item ID, nothing or a message containing just that item is sent.
*
* @param bool $update    decides whether alerts_filter_howoften.last would be updated, 
*                        i.e. whether this is only a debug trial or the real life
* @param $ho = how often 
* @return array ($filterid => new items)
*/

function create_filter_text ($ho, $collectionid, $update, $item_id)
{
    global $db;

    // the view.aditional field stores info about grouping by selections
    $SQL = "
    SELECT F.conds, view.slice_id, view.aditional,
        F.id AS filterid, F.vid, slice.name AS slicename, 
        slice.lang_file, CH.last
        FROM alerts_filter F INNER JOIN
             alerts_collection_filter CF ON CF.filterid = F.id INNER JOIN
             alerts_collection_howoften CH ON CF.collectionid = CH.collectionid INNER JOIN
             view ON view.id = F.vid INNER JOIN
             slice ON slice.id = view.slice_id
        WHERE CH.howoften='$ho'
        AND CH.collectionid='$collectionid'";
        
    if ($item_id) {
        $db->query("SELECT slice_id FROM item WHERE id='".q_pack_id($item_id)."'");
        if (!$db->next_record())
            return "";
        $SQL .= " AND slice.id='".addslashes($db->f("slice_id"))."'";
    }

    initialize_last ();
        
    $db->query($SQL);
    while ($db->next_record()) {
        $last = $db->f("last");
        $slices[$db->f("slice_id")]["name"] = $db->f("slicename");
        $slices[$db->f("slice_id")]["lang"] = substr ($db->f("lang_file"),0,2);
        $slices[$db->f("slice_id")]["views"][$db->f("vid")]["filters"]
            [$db->f("filterid")] = array ("conds"=>$db->f("conds"));
        $slices[$db->f("slice_id")]["views"][$db->f("vid")]["group"] 
            = $db->f("aditional");
    }
    if (! is_array ($slices))
        return;

    // The function needs global $conds and $sort, because it does some
    // wizardry with add_vars().
    global $debug_alerts, $conds, $sort;
    $howoften_options = get_howoften_options();
    
    // first I create a hierarchical array $slices and than use it instead of the recordset
            
    $now = time();
    
    reset ($slices);
    while (list ($p_slice_id, $slice) = each ($slices)) {
        $slice_id = unpack_id128($p_slice_id);

/* this query is carefully made to include only items, which:                
     a) 1. were moved to active between $last and $now 
        2. and were active (not expired nor pending) between $last and $now,
            i.e. publish_date <= $now AND expiry_date >= $last
     b) 1. were moved to active before $last
        2. and were active (not expired nor pending) between $last and $now
        3. and were not active until $last,
            i.e. publish_date > $last
            
   The field moved2active is filled whenever an item is moved from other bin to Active bin 
       or when it is a new item inserted into Active bin. 
   The field is cleared whenever the item is moved from Active bin to another one.
*/
                
        $SQL = "SELECT id FROM item ";
        
        if ($item_id)
            $SQL .= "WHERE id = '".q_pack_id($item_id)."'";
            
        else $SQL .=
            "WHERE publish_date <= $now AND expiry_date >= $last "  # a) 2. and b) 2.
               ."AND ((moved2active BETWEEN $last AND $now) "       # a) 1.
                     ."OR (moved2active < $last "                   # b) 1.
                         ."AND publish_date > $last) "              # b) 3.
                   .")";
        $db->query ($SQL);                                   
                   
        $all_ids = "";
        while ($db->next_record())
            $all_ids[] = $db->f("id");
        
        reset ($slice["views"]);
        while (list ($vid, $view) = each ($slice["views"])) {                      

            reset ($view["filters"]);
            while (list ($fid, $filter) = each ($view["filters"])) {                
                $dbconds = $filter["conds"];
                if ($debug_alerts) echo "<br>Slice ".$slice["name"].", conds ".$dbconds."<br>";       
                $conds = ""; $sort = "";
                add_vars ($dbconds);
                if ($debug_alerts) { print_r ($conds); echo "<br>"; }
        
                $zids = new zids (null, "p");
                if (is_array ($all_ids)) {
                    // find items for the given filter
                    $zids = QueryZIDs ($fields, $slice_id, $conds, $sort, "", "ACTIVE", "", 0, new zids( $all_ids,'p' ));
                    if ($debug_alerts) echo "<br>Item IDs count: ".$zids->count()."<br>";
                }
                
                $retval[$fid] = array (
                    "group" => $view["group"],
                    "vid" => $vid,
                    "zids" => $zids,
                );
            }
        }
    }

    if (!$debug_alerts && $update) {
        $varset = new CVarset();
        $varset->addkey ("howoften", "text", $ho);
        $varset->addkey ("collectionid", "text", $collectionid);
        $varset->add ("last", "number", $now);    
        $db->query($varset->makeINSERTorUPDATE("alerts_collection_howoften"));               
    }        
    
    //print_r ($retval);
    return $retval;
}

// -------------------------------------------------------------------------------------
/** Sends emails to all or chosen users, from all or chosen collections.
*
*   @param string $ho      = how often 
*   @param array $collection_ids (collection_id, collection_id, ...).
*            If set to "all", all collections are processed.
*   @param array $email (email, email, ...)
*            If set to "all", all Alerts users are processed.
*   @return count of emails sent
*/    
function send_emails ($ho, $collection_ids, $emails, $update, $item_id)
{
    global $db, $db2, $LANGUAGE_CHARSETS;
            
    if (is_array ($collection_ids))
        $where = " WHERE AC.id IN ('".join ("','", $collection_ids)."')";    
    $db->tquery("
            SELECT module.slice_url, AC.id AS collectionid, AC.*, email.* 
            FROM alerts_collection AC
            INNER JOIN email ON AC.emailid_alert = email.id
            INNER JOIN module ON AC.module_id = module.id
            $where");
    while ($db->next_record()) 
        $colls[$db->f("collectionid")] = $db->Record;
    if (!is_array ($colls)) return;
    
    $readerContent = new ItemContent ();
    if (!is_object ($db2))            
        $db2 = new DB_AA;
        
    reset ($colls);
    while (list ($cid, $collection) = each ($colls)) {        
        $unordered_filters = create_filter_text ($ho, $cid, $update, $item_id);

        // find filters for this collection
        $db->query("SELECT CF.filterid 
            FROM alerts_collection_filter CF
            WHERE CF.collectionid = '$cid'
            ORDER BY CF.myindex");
        
        $filters = "";    
        while ($db->next_record()) 
            $filters[$db->f("filterid")] = &$unordered_filters[$db->f("filterid")];            

        // Find all users who should receive anything
        if (! is_array ($emails)) {
    
            $db2->query("
                SELECT id FROM item 
                WHERE slice_id = '".addslashes($collection["slice_id"])."' 
                  AND status_code = 1
                  AND publish_date <= ".time()."
                  AND expiry_date >= ".time());
                  
            $field_howoften = getAlertsField (FIELDID_HOWOFTEN, $cid);
            
            while ($db2->next_record()) {
                $readerContent->setByItemID( unpack_id( $db2->f("id")), true);
                if( $readerContent->getValue( $field_howoften ) != $ho
                    || ! $readerContent->getValue( FIELDID_MAIL_CONFIRMED ))
                    continue;
                    
                $user_text = get_filter_text_4_reader ($readerContent, $filters, $cid);
                   
                // Don't send if nothing new emerged         
                if ($user_text) {
                    $alias["_#FILTERS_"] = $user_text;
                    $alias["_#HOWOFTEN"] = $ho;
                    $alias["_#COLLFORM"] = alerts_con_url ($collection["slice_url"],
                        "ac=".$readerContent->getValue(FIELDID_ACCESS_CODE));
        
                    if (send_mail_from_table ($collection["emailid_alert"], 
                        $readerContent->getValue(FIELDID_EMAIL), $alias))
                        $email_count ++;            
                }
            }

        // Use the emails sent as param
        } else {
            reset ($emails);
            while (list (,$email) = each ($emails)) {
                $alias["_#FILTERS_"] = get_filter_text_4_reader (null, $filters, $cid);
                $alias["_#HOWOFTEN"] = $ho;
                $alias["_#COLLFORM"] = alerts_con_url ($collection["slice_url"], "ac=ABCDE");
    
                $GLOBALS["debug_email"] = 0;
                if (send_mail_from_table ($collection["emailid_alert"], 
                    $email, $alias)) 
                    $email_count ++;
            }             
        }        
    }
    
    return $email_count;
}

// -------------------------------------------------------------------------------------
/**  Initializes the "last" field (which tells when were the Alerts last time sent)
*   for all unfilled filter / howoften combinations 
*   to a value depending on howoften, i.e. to one week ago for weekly etc.
*/    
function initialize_last ()
{
    global $db;
    $now = getdate ();
    
    $init["instant"] = time();
    // one day ago
    $init["daily"] = mktime ($now[hours], $now[minutes], $now[seconds], $now[mon], $now[mday]-1);
    // one week (7 days) ago
    $init["weekly"] = mktime ($now[hours], $now[minutes], $now[seconds], $now[mon], $now[mday]-7);
    // one month ago
    $init["monthly"] = mktime ($now[hours], $now[minutes], $now[seconds], $now[mon]-1);
    
    $db2 = new DB_AA;
 
    $hos = get_howoften_options();   
    reset ($hos);
    while (list ($ho) = each ($hos)) {
        $db->query("SELECT C.id FROM alerts_collection C LEFT JOIN
            alerts_collection_howoften CH ON CH.collectionid = C.id AND howoften='$ho'
            WHERE last IS NULL");
        while ($db->next_record()) 
            $db2->query("INSERT INTO alerts_collection_howoften (collectionid, howoften, last)
                VALUES ('".$db->f("id")."', '$ho', ".$init[$ho].")");

        $db->query("SELECT C.id FROM alerts_collection C INNER JOIN
            alerts_collection_howoften CH ON CH.collectionid = C.id
            WHERE last=0 AND howoften='$ho'");
        while ($db->next_record()) 
            $db2->query("UPDATE alerts_collection_howoften SET last=".$init[$ho]
                ." WHERE collectionid=".$db->f("id")." AND howoften='$ho'");
    }
}           

// -------------------------------------------------------------------------------------
/** Warning: This function caches all combinations of selections. If there
*   are too many different combinations, memory may be exhausted. 
*
*   TODO: Rewrite this function using a DB table. 
*
*   @param string $filter_settings  One or more filter IDs separted by comma ",".
*/
function get_filter_output_cached ($vid, $filter_settings, $zids) {
    global $cached_view_settings,
           $cached_filter_outputs,
           $db;

    //echo "zids"; print_r ($zids);
    if ($zids->count() == 0)
        return "";
        
    if (! $cached_view_settings [$vid]) {
        $view_info = GetViewInfo($vid);
        list($fields) = GetSliceFields (unpack_id ($view_info ["slice_id"]));        
        $slice_info = GetSliceInfo (unpack_id ($view_info ["slice_id"]));
        $cached_view_settings[$vid] = array (
            "lang" => substr ($slice_info["lang_file"],0,2),
            "info" => $view_info,
            "fields" => $fields,
            "aliases" => GetAliasesFromFields ($fields),
            "format" => GetViewFormat ($view_info),
        );
    }
    
    if (! isset ($cached_filter_settings [$filter_settings])) {
        $set = &$cached_view_settings[$vid];
        // set language
        bind_mgettext_domain ($GLOBALS["AA_INC_PATH"]."lang/".
            $set["lang"]."_alerts_lang.php3", true);
        // $set["info"]["aditional2"] stores item URL
        //global $debug;        $debug = 1;
        //echo "<i>";print_r ($zids->a);echo"</i>";
        $itemview = new itemview( $db, $set["format"], $set["fields"], 
            $set["aliases"], $zids, 0, 9999, $set["info"]["aditional2"]);                          
        $items_text = $itemview->get_output ("view");        
    //echo "<h1>items $items_text</h1>"; print_r ($set["format"]); exit;
        //if (! strstr ($filter_settings, ","))
        $cached_filter_settings [$filter_settings] = $items_text;
    }
    
    else $items_text = $cached_filter_settings [$filter_settings];
        
  //  echo "items $items_text"; exit;
    return $items_text;
}        

// -------------------------------------------------------------------------------------
/** Used in send_emails(). Finds filter text for the given reader.
*   @param object $readerContent  if null, use all filters
*   @param array $filters  sent by reference just for better performance. 
*/
function get_filter_text_4_reader ($readerContent, &$filters, $cid)
{  
    if ($readerContent) {
        $user_filters_value = $readerContent->getValues( 
            getAlertsField (FIELDID_FILTERS, $cid));
            
        if (! is_array ($user_filters_value)) 
            return "";
            
        foreach ($user_filters_value as $user_filter)
            // filter numbers are stored with "f" added to the beginning
            $user_filters [substr ($user_filter ["value"], 1)] = 1;
    }

    $last_fprop = "";
    $filter_ids = "";
    $user_zids = new zids (null, "p");
    
    for (reset ($filters); list ($filterid, $fprop) = each ($filters); ) {
        if (! $readerContent || $user_filters [$filterid]) {
            if ($fprop["group"]) {
                $user_text .= get_filter_output_cached (
                    $fprop["vid"], $filterid, $fprop["zids"]);                                
            }
            else {
                if ($last_fprop 
                  && $last_fprop["vid"] != $fprop["vid"]
                  && ! $last_fprop["group"]) {
                    $user_text .= get_filter_output_cached (
                        $fprop["vid"], join (",",$filter_ids), $user_zids);
                    $filter_ids = "";
                    $user_zids->clear("p");
                }
                $user_zids->union ($fprop["zids"]);
                $filter_ids[] = $filterid;
            }
            $last_fprop = $fprop;
        }
    }
    
    // add the last filter group
    if (is_array ($filter_ids)) {
        $user_text .= get_filter_output_cached (
            $last_fprop["vid"], join (",",$filter_ids), $user_zids);
    }
    
    return $user_text;
}

?>