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

if (!defined ("AA_EXTSESS_INCLUDED"))
    define ("AA_EXTSESS_INCLUDED", 1);
else return;

if (!defined ("AA_EXTSESSEITHER_INCLUDED"))
    define ("AA_EXTSESSEITHER_INCLUDED", 1);
else return;

class DB_AA extends DB_Sql {
  var $Host     = DB_HOST;
  var $Database = DB_NAME;
  var $User     = DB_USER;
  var $Password = DB_PASSWORD;
  var $Auto_Free = 'yes';

  function tquery($SQL) {
      if ($GLOBALS[debug])
          return $this->dquery ($SQL);
      else return $this->query($SQL);
  }
  
  function dquery($SQL) {
    echo "<br>$SQL";

    $SelectQuery = (strpos( " ".$SQL, "SELECT") == 1);
    // only SELECT queries can be explained
    if ( $SelectQuery )  {   
        $this->query("explain ".$SQL);
    
        echo "<table><tr><td><b>table</b></td> <td><b>type</b></td><td><b>possible_keys</b></td><td><b>key</b></td><td><b>key_len</b></td><td><b>ref</b></td><td><b>rows</b></td><td><b>Extra</b></td></tr>";
        while( $this->next_record() ) 
          printf( "<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>", 
                  $this->f('table'), $this->f('type'), $this->f('possible_keys'), $this->f('key'), $this->f('key_len'), $this->f('ref'), $this->f('rows'), $this->f('Extra'));
        echo "</table>";
    }
    
    list($usec, $sec) = explode(" ",microtime()); 
    $starttime = ((float)$usec + (float)$sec); 

    $retval = $this->query($SQL);

    list($usec, $sec) = explode(" ",microtime()); 
    $endtime = ((float)$usec + (float)$sec); 
    echo "<br>Query duration: ". ($endtime - $starttime);
    echo $SelectQuery ? "<br>Rows returned: ".$this->num_rows() :
                        "<br>Affected rows: ".$this->affected_rows();
    return $retval;
  }  

  function halt($msg) {
    if( $this->Halt_On_Error == "no" )
      return;
    printf("</td></table><b>Database error:</b> %s<br>\n", $msg);
    printf("<b>MySQL Error</b>: %s (%s)<br>\n",
      $this->Errno, $this->Error);
    echo("Please contact ". ERROR_REPORTING_EMAIL ." and report the ");
    printf("exact error message.<br>\n");    
    if( $this->Halt_On_Error == "yes" )
      die("Session halted.");
  }  
}

class AA_CT_Sql extends CT_Sql {
  var $database_class = "DB_AA";          ## Which database to connect...
  var $database_table = "active_sessions"; ## and find our session data in this table.
}

class AA_CP_Session extends Session {
  var $classname = "AA_CP_Session";

  var $cookiename     = "";                ## defaults to classname
  var $magic          = "adwetdfgyr";      ## ID seed
  var $mode           = "get";          ## We propagate session IDs in URL
##  var $fallback_mode  
  var $lifetime       = 0;                 ## 0 = do session cookies, else minutes
  var $that_class     = "AA_CT_Sql"; ## name of data storage container
  var $gc_probability = 5;  
  var $auto_init; # auto init 
  var $allowcache     = "no"; # Control caching of session pages, if set to no (also the default), the page is not cached under HTTP/1.1 or HTTP/1.0; if set to public , the page is publically cached under HTTP/1.1 and HTTP/1.0; if set to private , the page is privately cached under HTTP/1.1 and not cached under HTTP/1.0 
  var $allowcache_expire = 1;    # When caching is allowed, the pages can be cached for this many minutes. 

  function start($sid = "") {
    global $HTTP_COOKIE_VARS, $HTTP_GET_VARS, $HTTP_HOST, $HTTPS;
   	$name = $this->that_class;
	$this->that = new $name;
	$this->that->ac_start();
	
	$this->name = $this->cookiename==""?$this->classname:$this->cookiename;
	
	if (   isset($this->fallback_mode)
      && ( "get" == $this->fallback_mode ) 
      && ( "cookie" == $this->mode )
      && ( ! isset($HTTP_COOKIE_VARS[$this->name]) ) ) {
 
      if ( isset($HTTP_GET_VARS[$this->name]) ) {
        $this->mode = $this->fallback_mode;
      } else {
        header("Status: 302 Moved Temporarily");
        $this->get_id($sid);
        $this->mode = $this->fallback_mode;
        if( isset($HTTPS) && $HTTPS == 'on' ){
          ## You will need to fix suexec as well, if you use Apache and CGI PHP
          $PROTOCOL='https';
        } else {
          $PROTOCOL='http';
        }
          header("Location: ". $PROTOCOL. "://". $HTTP_HOST.$this->self_url());
        exit;
      }
    }

    # Allowing a limited amount of caching, as suggested by
    # Padraic Renaghan on phplib@shonline.de.
    # Note that in HTTP/1.1 the Cache-Control headers override the Expires
    # headers and HTTP/1.0 ignores headers it does not recognize (e.g,
    # Cache-Control). Mulitple Cache-Control directives are split into 
    # mulitple headers to better support MSIE 4.x.
    switch ($this->allowcache) {

      case "public":
        $exp_gmt = gmdate("D, d M Y H:i:s", time() + $this->allowcache_expire * 60) . " GMT";
        $mod_gmt = gmdate("D, d M Y H:i:s", getlastmod()) . " GMT";				
        header("Expires: " . $exp_gmt);
        header("Last-Modified: " . $mod_gmt);
        header("Cache-Control: public");
        header("Cache-Control: max-age=" . $this->allowcache_expire * 60);
        break;

      case "private":
        $mod_gmt = gmdate("D, d M Y H:i:s", getlastmod()) . " GMT";
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . $mod_gmt);
        header("Cache-Control: private");
        header("Cache-Control: max-age=" . $this->allowcache_expire * 60);
        break;

      default:
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache");
        header("Cache-Control: must-revalidate");
        header("Pragma: no-cache");
        break;
    }

    $this->get_id($sid);
 
    $this->thaw();

    ## Garbage collect, if necessary
    srand(time());
    if ((rand()%100) < $this->gc_probability) {
      $this->gc();
    }
  }

}

class AA_SL_Session extends Session {
  var $classname = "AA_SL_Session";

  var $cookiename     = "";                ## defaults to classname
  var $magic          = "adwetdfgyr";      ## ID seed
  var $mode           = "get";          ## We propagate session IDs with cookies
  var $fallback_mode  = "get";
  var $lifetime       = 0;                 ## 0 = do session cookies, else minutes
  var $that_class     = "AA_CT_Sql"; ## name of data storage container
  var $gc_probability = 5;  

   function MyUrl($SliceID, $Encap=false, $noquery=false) {
      global $HTTP_HOST, $HTTPS, $SCRIPT_NAME, $REDIRECT_SCRIPT_NAME;
      if (isset($HTTPS) && $HTTPS == 'on') {
         ## You will need to fix suexec as well, if you use Apache and CGI PHP
         $PROTOCOL='https';
      } else {
         $PROTOCOL='http';
      }
      ## PHP used in CGI mode and ./configure --enable-force-cgi-redirect
      if (isset($REDIRECT_SCRIPT_NAME)) {
         $foo = $PROTOCOL. "://". $HTTP_HOST.$REDIRECT_SCRIPT_NAME;
      } else {
         $foo = $PROTOCOL. "://". $HTTP_HOST.$SCRIPT_NAME;
      }
      switch ($this->mode) {
         case "get":
           if (!$noquery) {
              $foo .= "?slice_id=$SliceID";
              $foo .= ($Encap?"":"&encap=false");
              $foo .= "&".urlencode($this->name)."=".$this->id;
           }
           break;
         default:
           break;
      }
      return $foo;
   }
}

#class Example_CT_Shm extends CT_Shm {
#  var $max_sessions   = 500;               ## number of maximum sessions
#  var $shm_key        = 0x123754;          ## unique shm identifier
#  var $shm_size       = 64000;             ## size of segment
#}

#class Example_CT_Ldap extends CT_Ldap {
#	var $ldap_host = "localhost";
#	var $ldap_port = 389;
#	var $basedn    = "dc=your-domain, dc=com";
#	var $rootdn    = "cn=root, dc=your-domain, dc=com";
#	var $rootpw    = "secret";
#	var $objclass  = "phplibdata";
#}

#class Example_CT_Dbm extends CT_Dbm {
#	var $dbm_file  = "must_exist.dbm";
#}

?>
