<?php
/**
 * Provides AA_Router and AA_Router_Seo
 *
 * @version $Id: rrouter.class.php 2667 2006-08-28 11:18:24Z honzam $
 * @author Honza Malik <honza.malik@ecn.cz>
 * @copyright Copyright (C) 1999, 2000 Association for Progressive Communications
*/
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



/** AA_Router - base class for all routers
 *  Router is the class, which controls the url in AA site module. It should be
 *  used in "site control file". It provides several methods, which you can
 *  override in your "site control file".
 *
 *  The intended use is AA_Router -> AA_Router_Seo -> AA_Router_Mysite
 *    AA_Router - the generic class which defines the interface (API)
 *                and several common methods
 *
 *    AA_Router_Seo - is the class (below in this file), which implements
 *                    the AA_Router interface in some way. It is just like
 *                    "best practice" class. During the time we find, that
 *                    most sites, which uses the site module uses the same
 *                    url handling, the same variables, ... So, such setting
 *                    is in this class.
 *                    Of course, if your sites uses another configuration
 *                    of site variables, url strings, ... then it is good idea
 *                    to add another "best practice" class in this file.
 *
 *   AA_Router_Mysite - is extension of AA_Router_Seo for your site "Mysite".
 *                      It is not in this file - it is rather
 *                      in "site control file" and basically looks like:
 *
 *                      class AA_Router_Mysite extends AA_Router_Seo {
 *
 *                          function someOfYourMethodsHere() {
 *                              [...]
 *                          }
 *                      }
 *
 *                      You live without this class, if you do not need any
 *                      specific methods - you can instantiate the AA_Router_Seo
 *                      class directly.
 */
class AA_Router {

    protected $apc;
    protected $slices;
    protected $home;

    // Store the single instance of Database
    private static $_instance;

    function __construct($slices=null, $home='') {
        $this->slices = is_array($slices) ? $slices : array();
        $this->home   = $home;
    }

    public static function singleton($type, $slices=null, $home='') {
        if (!isset(self::$_instance)) {
            self::$_instance = new $type($slices, $home);
        }
        return self::$_instance;
    }

    /** view scroller function
     * @param $page   - current page
     * @param $max    - number of pages
     */
    function scroller($page, $max) {
        if ($max<=1) {
            return $this->getParam('scroller_nopage');
        }
        if ($page<1)    { $page = 1; }
        if ($page>$max) { $page = $max; }

        $nav_arr = $this->_scrollerArray($page, $max);
        $add     = $this->getParam('scroller_add');

        foreach ( $nav_arr as $k => $v) {
            if ( $v ) {
                $arr[] = "<a href=\"$v\" $add>$k</a>";
            } else {
                $arr[] = $k;
            }
        }

        return $this->getParam('scroller_begin'). join($this->getParam('scroller_delimiter'), $arr) . $this->getParam('scroller_end');
    }

    function getParam($param) {
        $params = array(
                         'page_variable'      => 'xpage',
                         'scroller_delimiter' => ' | ',
                         'scroller_begin'     => '',
                         'scroller_end'       => '',
                         'scroller_add'       => '',
                         'scroller_nopage'    => '',
                         'scroller_next'      => _m('Next'),
                         'scroller_previous'  => _m('Previous'),
                         'scroller_length'    => SCROLLER_LENGTH
                       );
        if (!isset($params[$param])) {
            return '';
        }
        return $params[$param];
    }


    /** _scrollerArray function
     *  Return navigation bar as a hash
     *  labels as keys, query string fragments a values
     */
    function _scrollerArray($page, $max) {
        $variable = $this->getParam('page_variable');
        $scrl_len = $this->getParam('scroller_length');
        $mp       = floor(($page - 1) / $scrl_len);  // current means current page
        $from     = max(1, $mp * $scrl_len);                // SCROLLER_LENGTH - number of displayed pages in navbab
        $to       = min(($mp + 1) * $scrl_len + 1, $max);
        if ($page > 1) {
            $arr[$this->getParam('scroller_previous')] = $this->go2url($variable.'='.($page-1));
        }
        if ($from > 1) {
            $arr["1"] = $this->go2url("$variable=1");
        }
        if ($from > 2) {
            $arr[".. "] = "";
        }
        for ($i=$from; $i <= $to; $i++) {
            $arr[(string)$i] = ($i==$page ? "" : $this->go2url("$variable=$i"));
        }
        if ($to < $max - 1) {
            $arr[" .."] = "";
        }
        if ($to < $max) {
            $arr[(string)$max] = $this->go2url("$variable=$max");
        }
        if ($page < $max) {
            $arr[$this->getParam('scroller_next')] = $this->go2url($variable.'='.($page+1));
        }
        return $arr;
    }

    function go2url($query_string) {
        global $apc_state;
        return $this->getState($this->newState($apc_state, $query_string));
    }

    // should be refined in subclass
    function xid($param='') { return ''; }

    // should be refined in subclass
    function xuser($param='') { return ''; }

}


/** AA_Router_Seo implements the AA_Router interface in some way. It is just
 *  like "best practice" class. During the time we find, that most sites, which
 *  uses the site module uses the same url handling, the same variables, ...
 *  So, such setting is in this class.
 *
 *  You can instatniate the class directly in your own "site control file",
 *  or you can extend this class
 *
 *  Using this this class requires mod_rewrite with the rules set as follows:
 *
 *    RewriteRule ^$ /apc-aa/modules/site/site.php3?site_id=670d58c34e6671be2460dde59ab5aab1&apc=en/home [L,QSA]
 *    RewriteRule ^((en|cz|de).*$) /apc-aa/modules/site/site.php3?site_id=670d58c34e6671be2460dde59ab5aab1&apc=$1 [L,QSA]
 *
 *  The URL then looks like
 *
 *    www.example.org/en2test/about-us/projekts/eficiency
 *
 *  which is parsed into following variables
 *
 *    www.example.org/<xlang>[<xpage>][<xflag>][-<xcat>]/<xseo1>/<xseo2>/<xseo3>...  (<xseo> = <xseo3> - the last one)
 *
 *       xlang = en
 *       xpage = 2
 *       xflag = test
 *       xcat  = bio
 *       xseo1 = about-us
 *       xseo2 = projekts
 *       xseo3 = eficiency
 *       xseo  = eficiency
 *
 *  For URL construction yo can use go2url:
 *
 *    original url:                             cz/news/about-us
 *
 *    {go:xlang=de&xseo1=faq&xseo2=questions}   de/faq/questions
 *    {go:xseo1=faq&xseo2=questions}            cz/faq/questions
 *    {go:xseo1=faq}                            cz/faq
 *    {go:xseo2=projects}                       cz/news/projects
 *    {go:xseoadd=nika}                         cz/news/about-us/nika
 *    {go:xlang=de}                             de/
 *    {go:xpage=2}                              cz2/news/about-us
 */
class AA_Router_Seo extends AA_Router {

    /** array of translations xseoX -> id */
    protected $_seocache;

    function parse($url) {
        $this->apc          = self::parseApc($url, $this->home);
        $this->apc['state'] = self::getState($this->apc);
        return $this->apc;
    }

    /** static function - caling from outside is not necessary, now */
    function parseApc($apc, $home='') {

        $parsed_url = parse_url($apc);
        $arr = explode('/', ltrim($parsed_url['path'],'/'));
        $ret = AA_Router_Seo::_parseRegexp(array('xlang','xpage','xflag','xcat'), '/([a-z]{2})([0-9]*)([^-0-9]*)[-]?(.*)/',$arr[0],$home);

        for ($i=1; $i < count($arr); $i++) {
            $ret['xseo'.$i] = $arr[$i];
        }

        // add querystring
        if ($parsed_url['query']) {
            $ret['xqs'] = $parsed_url['query'];
        }

        // set default values - like xseo, xseo10, ...
        $ret = self::newState($ret, '');
        return $ret;
    }

    /** static function - caling from outside is not necessary, now */
    function getState($apc_state) {
        $ret = '/'.$apc_state['xlang'].$apc_state['xpage'].$apc_state['xflag'].($apc_state['xcat'] ? '-'. $apc_state['xcat'] : '').'/';
        $i=1;
        while (!empty($apc_state['xseo'.$i])) {
            $ret .= $apc_state['xseo'.$i]. '/';
            $i++;
        }
        $ret = rtrim($ret,"/");
        // add querystring
        if ($apc_state['xqs']) {
            $ret .= '?'. $apc_state['xqs'];
        }
        return $ret;
    }

    /** ze stavajiciho $apc_state a naparsovaneho query-stringu
        aktualizuje hodnoty v $apc_state a vrati novy aktualni
        apc retezec */
    function newState($apc_state, $query_string) {
        $new_arr = array();
        parse_str($query_string, $new_arr);   // now we have $new_arr['x'], $new_arr['p'], etc.

        if (!empty($new_arr['xlang'])) { //change language
            $apc_state = self::parseApc($new_arr['xlang']);
        }

        // convert xseoX to array temporarily - it will be easier to work with it
        // $old_x for current state, $new_x for new state
        $new_x_max = self::_maxKey($new_arr, 'xseo');

        if ( $new_x_max > 0 ) {
            $old_x_max = self::_maxKey($apc_state, 'xseo');
            $max       = max($new_x_max, $old_x_max);
            $state     = 'COPY';
            for ( $i=1; $i <= $max; $i++) {
                if ($state == 'CLEAR') {
                    unset($apc_state['xseo'. $i]);
                } elseif ($new_arr['xseo'. $i]) {
                    $apc_state['xseo'. $i] = $new_arr['xseo'.$i];
                    $state = 'REDEFINING';
                } elseif ($state == 'REDEFINING') {
                    unset($apc_state['xseo'. $i]);
                    $state = 'CLEAR';
                } elseif (!$apc_state['xseo'. $i]) {
                    unset($apc_state['xseo'. $i]);
                    $state = 'CLEAR';
                } else {// $state = 'COPY';
                    $apc_state['xseo'. $i] = $apc_state['xseo'. $i];
                }
            }
            if ($state != 'COPY') {
                $apc_state['xpage'] = '';
            }
        }
        if (!empty($new_arr['xseoadd'])) {
            $x_max = self::_maxKey($apc_state, 'xseo');
            $apc_state['xseo'. ($x_max+1)] = $new_arr['xseoadd'];
        }
        // xseo changed - reset pager
        if (($new_x_max > 0) OR (!empty($new_arr['xseoadd']))) {
            $apc_state['xpage'] = '';
            $apc_state['xqs']   = '';
        }
        if (!empty($new_arr['xpage'])) { //'scroll' to other page
            $apc_state['xpage'] = ($new_arr['xpage'] < 2) ? '' : $new_arr['xpage'];
        }
        if (!empty($new_arr['xflag'])) { //change flag
            $apc_state['xflag'] = $new_arr['xflag'];
        }
        if (!empty($new_arr['xcat'])) { //change flag
            $apc_state['xcat'] = $new_arr['xcat'];
        }
        if (isset($new_arr['xqs'])) { //change xqs  (we can use also {go:xqs=} for current url without query string
            $apc_state['xqs'] = $new_arr['xqs'];
        }
        $x_max = self::_maxKey($apc_state, 'xseo');
        // we clear all unused {xseoX} in order {xseoX} is allaways SEO string or empty string and not something like {xseo4}
        for ($i=$x_max+1; $i<10; $i++) {
            $apc_state['xseo'.$i] = '';
        }
        $apc_state['xseo']   = ($x_max>0) ? $apc_state['xseo'. $x_max] : '';
        if (empty($apc_state['xseo']) AND ($x_max>1)) {
            $apc_state['xseo']   = $apc_state['xseo'. ($x_max-1)];
        }

        // workaround for the static::get_class()
        // returns real class name, not the AA_Router_Seo (if called staticaly)
        $backtrace           = debug_backtrace();
        $apc_state['router'] = $backtrace[0]['class'];

        return $apc_state;
    }

    /** returns: 1) ID of current item (if no param specified)
     *           2) ID of item on specified level (if param is number)
     *           3) IDs path of current item - like 2587(2877(3004)) as used
     *              in {item...} syntax (good for breadcrumbs)
     *              (if param = "path")
     */
    function xid($param=null) {

        if (empty($param)) {
            // current item id
            return $this->_xseo2id($this->apc['xseo']);
        }
        if (is_numeric($param)) {
            // item on specified level
            return $this->_xseo2id($this->apc['xseo'.$param]);
        }
        if ($param == 'path') {
            // tree for breadcrumb - just like 7663(7434(7432))
            $i     = 1;
            $delim = '';
            $path  = '';
            while (!empty($this->apc['xseo'.$i])) {
                $path  .= $delim. $this->_xseo2id($this->apc['xseo'.$i]);
                $delim  = '(';
                $i++;
            }
            if ($i > 2) {
                $path .= str_repeat(')', $i-2);   // close all open brackets
            }
            return $path;
        }
    }

    function _xseo2id($seo_string) {
        if (!isset($this->_seocache[$seo_string])) {
            $this->_seocache[$seo_string] = AA_Stringexpand_Seo2ids::expand(join('-', $this->slices), $seo_string);
        }
        return $this->_seocache[$seo_string];
    }

    /** $varnames - array()! of varnames */
    function _parseRegexp($varnames, $regexp, $str, $strdef='') {
        if (!$str) { $str = $strdef; }
        $ret     = array();
        $matches = array();
        if (!(preg_match($regexp, $str, $matches))) {
            print("Error initial string $strdef doesn't match regexp $regexp\n<br>");
        }
        foreach ($varnames as $key => $varname) {
            $ret[$varname] = $matches[$key+1];
        }
        return $ret;
    }

    function _maxKey($arr, $prefix) {
        $max = 0;
        foreach($arr as $key => $val) {
            $prefix_len = strlen($prefix);
            if ( !empty($val) AND (substr($key,0,$prefix_len) == $prefix) ) {
                $max = max((int)substr($key,$prefix_len), $max);
            }
        }
        return $max;
    }
}

/** {go:<query-string>}
 *  @return url based on current state (apc) and query-string paramater
 *  Ussage: {go:xseo1=faq&xseo2=questions}
 *  (we used {go2url} custom function in previous versions of AA. This function
 *  is however core function)
 */
class AA_Stringexpand_Go extends AA_Stringexpand_Nevercache {
    // Never cached (extends AA_Stringexpand_Nevercache)
    /** expand function */
    function expand($query_string='') {
        $router_class = $GLOBALS['apc_state']['router'];
        if (empty($router_class)) {
            return '<div class="aa-error">Err in {go} - router not found - {go} is designed for site modules</div>';
        }
        $router = AA_Router::singleton($router_class);
        return $router->go2url($query_string);
    }
}

/** {xid[:<level>]} - complement to {xseo1},.. variables of AA_Router_Seo
 *  @return id of the current item on specifield level
 *  {xid}      - returns id of current item (the id of {xseo} item)
 *  {xid:1}    - returns id of item on first level (the id of {xseo1} item)
 *               for /cz/project/about-us returns id of "project" item
 *  {xid:path} - returns ids path of current item - like 2587(2877(3004)) as
 *               used in {item...} syntax (good for breadcrumbs:
 *               {item:{xid:path}: _#HEADLINE:: _#HEADLINK &gt;}
 **/
class AA_Stringexpand_Xid extends AA_Stringexpand_Nevercache {
    // Never cached (extends AA_Stringexpand_Nevercache)
    // Cached inside the router itself
    /** expand function */
    function expand($param='') {
        $router_class = $GLOBALS['apc_state']['router'];
        if (empty($router_class)) {
            return '<div class="aa-error">Err in {xid} - router not found - {xid} is designed for site modules</div>';
        }
        $router = AA_Router::singleton($router_class);
        return $router->xid($param);
    }
}

/** Expands {xuser:xxxxxx} alias - auth user informations (of current user)
*   @param $field - field to show ('headline........', '_#SURNAME_' ...).
*                   empty for username (of curent logged user)
*                   id - for long id
*
*   We do not use {user} in this case, since views with {user} are not cached,
*   but the views with {xuser} could be (xuser is part of apc variable)
*/
class AA_Stringexpand_Xuser extends AA_Stringexpand {

    /** expand function
     * @param $field
     */
    function expand($field='') {
        $xuser = $GLOBALS['apc_state']['xuser'];
        if (!$xuser) {
            return '';
        }
        switch ($field) {
            case '':     return $xuser;
            case 'id':   return ReaderName2Id($xuser);
        }
        $item = AA_Items::getItem(new zids(ReaderName2Id($xuser),'l'));
        return empty($item) ? '' : $item->subst_alias($field);
    }
}
?>
