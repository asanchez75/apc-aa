<?
// MLX MultiLingual eXtension for APC ActionApps
// ver.: 0.2
// http://mimo.gn.apc.org/mlx
// mimo/at/gn.apc.org

/** name of column in table slice **/
define ('MLX_SLICEDB_COLUMN','mlxctrl'); // 

/** deprecated: using mlxctrl instead
    the field type in the Control Slice whose name sets the 
    field in the item in Content Slice that links to the Control Item **/
//define ('MLX_ITEM2MLX_FIELD','lang_code.......'); 
define ('MLX_CTRLIDFIELD','mlxctrl.........');


/** what field type stores <lang> => <item id> info 
   ('text' means anything beginning with 'mlxctrl' **/
define ('MLX_LANG2ID_TYPE','mlxctrl');

define ('MLX_TRACE',0);

/** HTML defines **/
define ('MLX_HTML_TABHD',"\n"
."<tr>\n"
."  <td colspan='5'>\n"
."    <table width='100%' bgcolor='".COLOR_TABTITBG."' border='0' cellspacing='0' cellpadding='0'>\n"
."    <tbody>\n"
."    <tr>\n"
."      <td colspan='3'>\n"
."        <table bgcolor='".COLOR_TABTITBG."' border='0' cellspacing='0' cellpadding='0'>\n"
."        <tbody>\n"
."        <tr>\n"
."          <td >&nbsp;<b>MLX Language Control</b></td>\n"
."        </tr>\n"
."        <tr>\n"
."          <td><table border='0' cellspacing='0' cellpadding='1'>\n"
."            <tr>\n");

define ('MLX_HTML_TABFT',"\n"
."            </tr>\n"
."            </table>\n"
."          </td>\n"
."        </tr>\n"
."        </tbody>\n"
."        </table>\n"
."      </td>\n"
."    </tr>\n"
."    </tbody>\n"
."    </table>\n"
."  </td>\n"
."</tr>\n");

function __mlx_dbg($v,$label="")
{
	echo "<pre>$label\n";
	if(is_array($v))
		print_r($v);
	else
		print($v);
	echo "</pre>";
}
function __mlx_trace($v)
{
	if(MLX_TRACE) {
		echo "<pre>";
		if(is_array($v))
			print_r($v);
		else
			print($v."\n");
		echo "</pre>";
	}
}
function __mlx_fatal($msg) {
	global $err;
	global $sess;
	global $slice_id;
	if(is_array($msg))
		$msg = implode("<br>",$msg);
	$err["MLX"] = MsgErr($msg);
	MsgPage(con_url($sess->url(self_base() ."index.php3"),
		"slice_id=$slice_id"),
		$err, "standalone");
	die;
}

// test if the slice is MLX enabled
function isMLXSlice($sliceobj)
{
	if(is_object($sliceobj))
		$lang_control = $sliceobj->getfield(MLX_SLICEDB_COLUMN);
	else //not an object 
		$lang_control = $sliceobj[MLX_SLICEDB_COLUMN];
	//hook other replies here
	return $lang_control;
}
class MLXCtrl
{
	var $itemid; /** itemid in MLX control slice **/
	var $translations; /** (0=>("EN"=>"itemid"),..) **/
	function MLXCtrl($itemid,$mlxObj) {
		$this->itemid = $itemid;
		$content = GetItemContent($itemid);
		$fields = $mlxObj->getCtrlFields();
		foreach( $fields as $v ) {
			if(isset($v[id]) && ( strpos($v[id],MLX_LANG2ID_TYPE)) === 0) {
				$this->translations[] = 
					array( $v[name] => $content[$itemid][$v[id]][0][value]);
			}
		}
	}
	function getDefLangName() { return key($this->translations[0]); }
	function &getFirstNonEmpty() {
		foreach($this->translations as $v) {
			if( ((list($itemid,)=array_values($v))) && $itemid)
				return $v;
		}
		return false;
	}

};
class MLX
{
//private:
	var $ctrlFields = 0;
//	var $linkField;
	var $slice = 0;
	var $langSlice = 0;
//public:	
	function MLX(&$slice) {
		$this->slice = $slice;
		$this->langSlice = $this->slice->getfield(MLX_SLICEDB_COLUMN);
		list($this->ctrlFields,) = GetSliceFields($this->langSlice);
	}
	function &getCtrlFields() { return $this->ctrlFields; }
	function update(&$content4id,$cntitemid,$action,$mlxl,$mlxid) {
		$insert = false;
		if(($action == "insert") && $mlxl && $mlxid) {
			$this->trace("Updating control data..");
			$id = $mlxid;
			$lang = $mlxl;
		} else if((!$content4id[MLX_CTRLIDFIELD][0][value]) || ($action == "insert")) {
		// create the meta data
			$this->trace("Creating new control data..");
			$id = new_id();
			$lang = $content4id['lang_code.......'][0][value];
			$insert = true;
		} else 
			$this->fatal("MLX update: duno what to do");
		reset($this->ctrlFields);
		while( list($k,$v) = each($this->ctrlFields) ) {
			if($v['name'] == $lang) {
				$content4mlxid["$k"] = array(array('value' => addslashes("$cntitemid")));
				break;
			}
		}
		if(empty($content4mlxid)) 
			$this->fatal("Creating the Control Language Data failed. "
				."Maybe you have to select a Language Code, "
				."or create the chosen Language Code in the Control Slice Fields."); 
	//      			echo "<pre>"; print_r($content4mlxid); echo "</pre>";
	//      			die;
		//$this->dbg($content4mlxid);
		$GLOBALS[errcheck] = 1;
		//$GLOBALS[debugsi] = 5;
		StoreItem($id,$this->langSlice,$content4mlxid,
			$this->ctrlFields,$insert,true,true);
		$content4id[MLX_CTRLIDFIELD][0][value] = "$id";
		$this->trace("done. id=$id");
	}
	function itemform($lang_control,$params,$content4id,$action,$lang,$mlxid)
	{
		global $DOCUMENT_URI;
		global $PHP_SELF;
		global $err;
		global $r_hidden;
		$mlxout = MLX_HTML_TABHD;
		$mlx_url = ($DOCUMENT_URI != "" ? $DOCUMENT_URI :
			$PHP_SELF . ($return_url ? "?return_url=".urlencode($return_url) : ''));
		$mlx_url .= "?".$this->getparamstring($params);
		switch($action) {
			case "update":
			case "edit":
				$lang = $content4id['lang_code.......'][0][value];
				$mlxid = $content4id[MLX_CTRLIDFIELD][0][value];
				if($mlxid == 1) //this is only here because of my stupid testimport
					return;
				break;
			case "insert":
			case "add":
			default:
				$mlxCtrl = new MLXCtrl($mlxid,$this);
				$defCntId = $mlxCtrl->getFirstNonEmpty();
				if($defCntId) {
					$tritemid = array_shift($defCntId);
					$itemcontent = GetItemContent($tritemid);
					$content4form = $itemcontent[$tritemid];
					foreach($this->slice->fields('record') as $slfield) {
						$kstr = 'v'.unpack_id($slfield[id]);
						unset($GLOBALS[$kstr]);
						unset($GLOBALS[$kstr."html"]);
						if(!$slfield[input_show])
							continue;
//						$this->dbg($slfield);
						$itcnt = $content4form[$slfield[id]];
						$GLOBALS[$kstr."html"] = ($v[0][flag]==65?1:0); //TODO fix
						if($slfield[multiple]) {
							foreach($itcnt as $vai)  
								$GLOBALS[$kstr][] = addslashes($vai[value]);
						} else {
							$GLOBALS[$kstr] = addslashes($itcnt[0][value]);
						}
					}
					//$this->dbg($GLOBALS);
				}
				$GLOBALS['v'. unpack_id(MLX_CTRLIDFIELD)]= "$mlxid";
				//check if lang is set, set to default from MLX
				if(!$lang)
					$GLOBALS['v'. unpack_id('lang_code.......')] = $mlxCtrl->getDefLangName();
				else
					$GLOBALS['v'. unpack_id('lang_code.......')] = $lang;
				//$this->dbg($GLOBALS['v'. unpack_id('lang_code.......')]);
				//$this->dbg(GetItemContent($mlxid));
				break;
		}
		foreach( $this->ctrlFields as $k => $v ) {
			$href = "";
			$modstr = "";
			$extra = "";
			// check if id begins with 'mlxctrl'
			if(!isset($v[id]) || ( strpos($v[id],MLX_LANG2ID_TYPE) !== 0) )
				continue;
			$mlxout .= "<td valign='top' class='tabs";
			if(($action == "edit") || ($action == "update")) 
				$langid = $this->getLangItem($v[name],$content4id,$content4mlxid);
			else if($mlxid) {
				$langid = $this->getLangItem($v[name],$mlxid,$content4mlxid);
			}
			if($lang != $v[name]) {
				if($langid) {
					$href = "id=$langid&edit=1&";
					$modstr = _m("Edit")." ";
					$mlxout .= "active mlx-edit";
				} else {
					$href = "add=1&mlxl=".$v[name]."&";
					$modstr = _m("Add")." ";
					$mlxout .= "active mlx-add";
				} 
				$href .= "mlxid=$mlxid&";
			} else
				$mlxout .= "nonactive' bgcolor='".COLOR_TABBG;
			if($langid) {
				$extra = "\n<span class='mlx-view'><a href='../slice.php3"
					."?slice_id=".$this->slice->unpackedid."&sh_itm=".$langid
					."&o=1&mlxl=".$v[name]."' target=_blank>("._m("view").")</a>"
					."</span>\n";
			}				
			$mlxout .= "'>&nbsp;&nbsp;";
			if($href) //TODO implement something that saves on switch
				$mlxout .= "<a href='$mlx_url$href'>";			
//				$mlxout .= "<a href='javascript:mlx_saveOnSwitch($mlx_url$href)'>";
			$mlxout .= $modstr.$v[name].($href?"</a>":"").$extra;
			$mlxout .= "&nbsp;&nbsp;</td>\n ";
			
		}
		//finally set hidden fields for AA
		$r_hidden[mlxid] = "$mlxid";
		$r_hidden[mlxl] = "$lang";
		
		return $mlxout.MLX_HTML_TABFT;//"\n</tr>\n</tbody>\n</table>\n<br>\n";
		//$r_hidden[mlxcontent] = $content4id;
	}
	// helper functions
	//protected:
	function getLangItem($lang,&$content4id,&$content4mlxid)
	{
		global $err;
		if(is_array($content4id))
			$mlxid = $content4id[MLX_CTRLIDFIELD][0][value];
		else
			$mlxid = $content4id;
		//mlxid should now hold the itemid of the mlx info
		if(!$mlxid)
			return false;
		if(empty($content4mlxid) && (isset($mlxid))) {
			$content = GetItemContent($mlxid);
			if( !$content )
				$this->fatal(_m("Bad item ID"));
			$content4mlxid = $content[$mlxid];
		} 
		if(empty($content4mlxid))
				$this->fatal(_m("No ID for MLX, "
					."set the lang_control field in the MLX slice "
					."(e.g. to text.......)"));
		foreach($this->ctrlFields as $v) {
			if($v['name'] == $lang)
				return $content4mlxid[$v['id']][0]['value'];
		}
		return false;
	}
	function getparamstring(&$params)
	{
		foreach($params as $k => $v) {
			$mlx_url .= "$k=";
			switch(gettype($v)) {
				case 'boolean':
					$mlx_url .= ($v)?"true":"false";
					break;
				default:
					$mlx_url .= $v;
			}
			$mlx_url .= "&";
		}
		return $mlx_url;
	}
	function fatal($msg) { __mlx_fatal($msg); }
	function dbg($v) { __mlx_dbg($v); }
	function trace($v) { __mlx_trace($v); }
};
class MLXView
{
	// the language code to default to
	var $language = array();
	// the mode to use: MLX  -> use defaulting: if lang not available fall back
	//                          to another translation of same article (MultiLingual)
	//                  ONLY -> show only items available in this language (like conds[lc]=lang)
	//                  ALL  -> show all articles regardless of language (like without MLX)
	var $mode = "MLX";
	var $supported_modes = array("MLX","ONLY","ALL");
	function MLXView($mlx) { 
		if($mlx) {
			$arr = explode("-",$mlx);
			foreach($arr as $av) {
				$av = strtoupper($av);
				if(in_array($av,$this->supported_modes))
					$this->mode = $av;
				else
					$this->language[] = $av;
			}
		}
	}
	function preQueryZIDs($ctrlSliceID,&$conds,&$slices)
	{
		switch($this->mode) {
			case 'ONLY': //add to conds
				$translations = $this->getPrioTranslationFields($ctrlSliceID);
				$value = key($translations);
				$conds[] = array('lang_code.......'=>$value,
					'value'=>$value,
					'operator'=>"=");
				break;
//			__mlx_dbg($conds,"conds");
			default:
				break;
		}
	}
	//-- type is p
	function postQueryZIDs(&$zidsObj,$ctrlSliceID,$slice_id, $conds, $sort, 
		$group_by,$type, $slices, $neverAllItems, $restrict_zids,
		$defaultCondsOperator,$nocache) 
	{
		global $pagecache, $QueryIDsCount, $debug;
		
		if($this->mode != "MLX")
			return;
			
		#create keystring from values, which exactly identifies resulting content
		$keystr = $this->mode.serialize($this->language).$ctrlSliceID.$slice_id
			. serialize($conds). serialize($sort)
			. $group_by. $type. serialize($slices). $neverAllItems
			. ((isset($restrict_zids) && is_object($restrict_zids)) ? 
				serialize($restrict_zids) : "")
			. $defaultCondsOperator;
		
		$cachestr = "slice_id=$ctrlSliceID";
		if ( $res = CachedSearch( !$nocache, $keystr, $cachestr )) {
			if(MLX_TRACE)
				__mlx_trace("using cache");
			$zidsObj->refill( $res->a );
			return;
		}
		
		$arr = array();
		foreach($zidsObj->a as $packedid) {
			$unpackedid = unpack_id128($packedid);
			$arr[(string)$unpackedid] = $packedid;
		}
		$translations = $this->getPrioTranslationFields($ctrlSliceID);
		$db = getDB();
		$db2 = getDB();
		reset($arr);
		while(list($upContId,$pContId) = each($arr)) {
			//__mlx_dbg($upContId,"ite");
			$sql = "SELECT `text` FROM `content`"
				." WHERE ( `item_id`='".$pContId."'"
				." AND `field_id`='".MLX_CTRLIDFIELD."')";
			$db->tquery($sql);
			while( $db->next_record() ) {
				//Remove
				if($db->Record[0] == 1)
					continue;
				$ctrlId = q_pack_id($db->Record[0]);
				//__mlx_dbg($ctrlId);
				$subsql = "SELECT `field_id`,`text` FROM `content`"
					." WHERE ( `item_id`='".$ctrlId."'"
					." AND `field_id` RLIKE '".MLX_LANG2ID_TYPE."')";
				$db2->tquery($subsql);
				unset($aMlxCtrl);
				while($db2->next_record()) { //get all translations
					$aMlxCtrl[(string)$db2->Record[0]] = $db2->Record[1];
				}
				$bFound = false;
				foreach($translations as $tr) {
					$fieldSearch = $aMlxCtrl[$tr];
					if(!$fieldSearch)
						continue;
					if($bFound) {
						//__mlx_dbg($fieldSearch,"unset");
						unset($arr[(string)$fieldSearch]);
					} else
						$bFound = true;
				}
				//__mlx_dbg($aMlxCtrl,"aMlxCtrl");
				
			}
			
		}
		//__mlx_dbg($arr,"return");
		freeDB($db2);
		freeDB($db);
		$QueryIDsCount = count($arr);
		$zidsObj->a = array_values($arr);
		if( !$nocache )
			$pagecache->store($keystr, serialize($zidsObj), $cachestr);
		
	}
	function getPrioTranslationFields($ctrlSliceID) {
		list($fields,) = GetSliceFields($ctrlSliceID);
		$translations = array();
		foreach( $fields as $v ) {
			if(isset($v[id]) && ( strpos($v[id],MLX_LANG2ID_TYPE)) === 0) {
				$tmptrans[(string)$v[name]] = $v[id];
			}
		}
		foreach( $this->language as $lang) {
			if(!$tmptrans[(string)$lang])
				continue;
			$translations[(string)$lang] = $tmptrans[(string)$lang];
			unset($tmptrans[(string)$lang]);
		}
		foreach( $tmptrans as $lang => $langfield)
			$translations[(string)$lang] = $langfield;
		return $translations;
	}	
}
class MLXEvents
{
	function MLXEvents()
	{
	}
	function itemsBeforeDelete(&$item_ids,$slice_id)
	{
		if(empty($item_ids))
			return;
		$sliceobj = new slice($slice_id);
		if(!IsMLXSlice($sliceobj)) {
			return;
		}
//		echo "<h2>called</h2><pre>";
//		print_r($item_ids);
//		print("$slice_id");
		// dont use this unless you use the special field type mlxctrl everywhere
		$rm_itemids = array();
		$db = getDB();
		foreach($item_ids as $itemid) {
			$db->query("SELECT item_id FROM content WHERE ( `field_id` "
	    			."RLIKE '".MLX_LANG2ID_TYPE."' AND "
	    			."`text`='".unpack_id($itemid)."')");
    			while( $db->next_record() )
        			$rm_itemids[] = $db->f("item_id");
			$db->query("DELETE FROM content WHERE ( `field_id` "
	    			."RLIKE '".MLX_LANG2ID_TYPE."' AND "
	    			."`text`='".unpack_id($itemid)."')");
	    	}
		foreach($rm_itemids as $itemid) {
			$db->query("SELECT * FROM content WHERE ("
				." `item_id`='".$itemid."' "
				." AND `field_id` "
				."RLIKE '".MLX_LANG2ID_TYPE."')");
			if($db->num_rows() === 0) {
				$db->query("DELETE FROM content WHERE "
					." `item_id`='".$itemid."' ");
				$db->query("DELETE FROM item WHERE "
					." `id`='".$itemid."' ");
			}
		}
		freeDB($db);
//		echo "</pre>";
	}
};

?>