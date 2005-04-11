<?php # -*-mode: Fundamental; tab-width: 4; -*-
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

#
#	Class scroller
#	Implements navigation bar for scrolling through long lists
#
if (!defined ("SCROLLER_INCLUDED"))
define ("SCROLLER_INCLUDED",1);
else return;

// tranformation from english style datum (3/16/1999 or 3/16/99) to mySQL date
// break year for short year description is 1950
function userdate2sec ($dttm, $time="") {
    if( !ereg("^ *([[:digit:]]{1,2}) */ *([[:digit:]]{1,2}) */ *([[:digit:]]{4}) *$", $dttm, $part))
        if( !ereg("^ *([[:digit:]]{1,2}) */ *([[:digit:]]{1,2}) */ *([[:digit:]]{2}) *$", $dttm, $part))
            return "";
    if( !ereg("^ *([[:digit:]]{1,2}) *: *([[:digit:]]{1,2}) *: *([[:digit:]]{1,2}) *$", $time, $tpart))
        return mktime(0,0,0,$part[1],$part[2],$part[3]);
    else
        return mktime($tpart[1],$tpart[2],$tpart[3],$part[1],$part[2],$part[3]);
}

require_once $GLOBALS['AA_INC_PATH'] . "statestore.php3";

class scroller extends storable_class {
    var $classname = "scroller";
    var $persistent_slots = Array("pgcnt", "current", "id", "visible", "sortdir",
                      "sortcol", "filters", "itmcnt", "metapage", "urldefault");
    var $current = 1;		# current page
    var $id;			    # scroller id
    var $visible = 1;
    var $pgcnt;			    # total page count
    var $itmcnt;            # total item count
    var $metapage = 10;	    # "metapage" size
    var $urldefault;		# cache self url
    var $sortdir = 1;
    var $sortcol = "";
    var $filters;

    # constructor
    # $id identifies scroller on a web page
    # $pgcnt is the number of pages to scroll
    function scroller($id = "", $url = "", $pgcnt = 0) {
        $this->id = $id;
        $this->pgcnt = $pgcnt;
        $this->urldefault = $url;
        $this->filters = array();
        $this->current = 1;
        $this->metapage = 10;
        $this->visible = 1;
    }

    # return part of a query string for move of $pages relative of current position
    function Relative($pages) {
        return urlencode("scr_" . $this->id . "_Mv") . "=" . urlencode($pages);
    }

    # return part of a query string for move to absolute position $page
    function Absolute($page) {
        return urlencode("scr_" . $this->id . "_Go") . "=" . urlencode($page);
    }

    # print Toggle Visibility button
    function pVisButton($url = "",
                        $show = "<img src=\"../images/expand.gif\" border=0 align=left alt=Expand>",
                        $hide = "<img src=\"../images/collapse.gif\" border=0 align=left alt=Collapse>") {
        if(!$url)
            $url = $this->urldefault;
        echo "<a href=\"$url" . $this->ToggleVis() . "\">" .
            ($this->visible ? $hide : $show) . "</a>";
    }

    # return part of a query string for move to toggle visibility
    function ToggleVis() {
        return urlencode("scr_" . $this->id . "_Vi") . "=" . ($this->visible ? "0" : "1");
    }

    # return part of a query string for move to toggle visibility
    function Sort($sortcol) {
        return urlencode("scr_" . $this->id . "_Sort") . "=" . urlencode($sortcol);
    }

    # print sort label
    function pSort($sortcol, $show, $url = "") {
        if(!$url)
            $url = $this->urldefault;
        echo "<a href=\"$url" . $this->Sort($sortcol) . "\">$show";
        if($this->sortcol == $sortcol && $this->sortdir)
            echo "<img src=\"../images/sort" . $this->sortdir . ".gif\" border=0>";
        echo "</a>";
    }

    # return "order by" sql clause
    function sortSql() {
        if($this->sortcol && $this->sortdir)
            return " order by $this->sortcol " . ($this->sortdir == 2 ? "desc" : "");
        return "";
    }

    # keep current page within bounds
    function checkBounds() {
        if($this->current < 1)            $this->current = 1;
        if($this->current > $this->pgcnt) $this->current = max($this->pgcnt,1);
    }

    # adjust number of pages
    # deprecated - use coutPages instead
    function adjustSize($pgcnt) {
        $this->pgcnt = $pgcnt;
        $this->checkBounds();
    }

    # adjust number of pages depends on item count and metapage
    function countPages($itmcnt) {
        $this->pgcnt = floor(($itmcnt - 1) / $this->metapage) + 1;
        $this->checkBounds();
        $this->itmcnt = $itmcnt;
    }

    function go2page($page) {
        $this->current=$page;
        $this->checkBounds();
    }

    #returns number of pages
    function pageCount() {
        return floor(($this->itmcnt - 1) / max(1,$this->metapage)) + 1;
    }

    function setSort($column, $desc="") {
        $this->sortcol = $column;
        $this->sortdir = ( $desc ? 2 : 1 );
    }

    # process query string and execute commands for this scroller
    # query string is taken from global variables
    # deprecated - better to use updateScr (based on $itmcnt)
    function update($url = "", $pgcnt = "") {
        $this->updateFilters();
        if($url)   $this->urldefault = $url;
        if($pgcnt) $this->pgcnt = $pgcnt; # adjust size
        if(isset($GLOBALS["scr_" . $this->id . "_Vi"]))
            $this->visible = $GLOBALS["scr_" . $this->id . "_Vi"];
        if($GLOBALS["scr_" . $this->id . "_Go"])
            $this->current = $GLOBALS["scr_" . $this->id . "_Go"];
        if($GLOBALS["scr_" . $this->id . "_Mv"])
            $this->current += $GLOBALS["scr_" . $this->id . "_Mv"];
        if($GLOBALS["scr_" . $this->id . "_Sort"]) {
            $sortcol = $GLOBALS["scr_" . $this->id . "_Sort"];
            $this->sortdir = ($sortcol == $this->sortcol) ?
                             (($this->sortdir + 1) % 3) : 1;
            $this->sortcol = $sortcol;
        }
        $this->checkBounds();
    }

    # process query string and execute commands for this scroller
    # query string is taken from global variables
    # (based on $itmcnt)
    function updateScr($url = "", $itmcnt = "") {
        if($itmcnt)
        $this->countPages($itmcnt);
        $this->update($url);
    }

    # return navigation bar as a hash
    # labels as keys, query string fragments a values
    function navarray() {
        if(!$this->pgcnt) return array();
        $mp   = floor(($this->current - 1) / SCROLLER_LENGTH);  // current means current page
        $from = max(1, $mp * SCROLLER_LENGTH);                // SCROLLER_LENGTH - number of displayed pages in navbab
        $to   = min(($mp + 1) * SCROLLER_LENGTH + 1, $this->pgcnt);
        if($this->current > 1)
        $arr["<<"]                             = $this->Relative(-1);
        if($from > 1)              $arr["1"]   = $this->Absolute(1);
        if($from > 2)              $arr[".. "] = "";
        for($i = $from; $i <= $to; $i++) {
            $arr[(string)$i] = ($i == $this->current ? "" : $this->Absolute($i));
        }
        if($to < $this->pgcnt - 1) $arr[" .."] = "";
        if($to < $this->pgcnt)
            $arr[(string) $this->pgcnt] = $this->Absolute($this->pgcnt);
        if($this->current < $this->pgcnt)
            $arr[">>"] = $this->Relative(1);
        return $arr;
    }

    # convert array provided by navarray into HTML code
    # commands are added to $url
    function pnavbar($url = "") {
        if(!$this->visible) return;
        if(!$url)           $url = $this->urldefault;
        $i = 0;
        $arr = $this->navarray();
        while(list($k, $v) = each($arr)) {
            if($i++) echo " | ";
            if($v) echo "<a href=\"$url$v\" class=\"scroller\">$k</a>\n";
            else   echo "<span class=\"scroller_actual\">$k</span>\n";
        }
        echo "| <a href=\"" . $url . "listlen=99999\" class=\"scroller\">" . _m("All") . "</a>";
    }

    # add filter
    # type: "char", "date", "int","md5" (other can be added)
    function addFilter($name, $type, $value = "", $truename = "") {
        $this->filters[$name][value] = $value;
        $this->filters[$name][type] = $type;
        $this->filters[$name][truename] = $truename;   // truename is for storing names like "categories.name" which cannot by real names for php variables
    }

    # process query string, execute commands for filters
    function updateFilters() {
        reset($this->filters);
        while(list($name, $flt) = each($this->filters)) {
            if(isset($GLOBALS["flt_" . $this->id . "_${name}_val"])){
                $this->filters[$name][value] = $GLOBALS["flt_" . $this->id . "_${name}_val"];
            }
        }
    }

    # return sql "where" clause generated by filters
    function sqlCondFilter() {
        reset($this->filters);
        while(list($name, $flt) = each($this->filters)) {
            if(!$flt[value]) continue;
            if(ereg("^ *(>|<|=|>=|<=) *(.*)", $flt[value], $regs)) {
                $op = $regs[1];
                $value = $regs[2];
            }
            else {
                $op = ($flt[type] == "char" ? "like" : "=");
                $value = $flt[value];
            }
            if( $flt[truename]!="" )
            $name = $flt[truename];
            switch($flt[type]) {
                case "char":
                    if($op == "like") $value = "%$value%";
                    $cond[] = "$name $op '$value'";
                    break;
                case "int":
                    $cond[] = "$name $op " . $value;
                    break;
                case "date":
                    $cond[] = "$name $op '" . userdate2sec($value) . "'";
                    break;
                case "md5":
                    $cond[]= "$name $op '". quote(pack("H*",$value))."'";
            }
        }
        if(!is_array($cond)) return "1 = 1";
        return join(" AND ", $cond);
    }
}

?>