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

# discedit.php3 - admin discussion comments
# expected  $item_id for comment's item_id
# optional  $mode for delete,hide or normal mode
#           $d_id
#           $h

require "../include/init_page.php3";

require $GLOBALS[AA_INC_PATH]."formutil.php3";
require $GLOBALS[AA_INC_PATH]."discussion.php3";
require $GLOBALS[AA_INC_PATH]."item.php3";
require $GLOBALS[AA_INC_PATH]."pagecache.php3";
require $GLOBALS[AA_INC_PATH]."msgpage.php3";

// get a headline of the item
function getHeadline($content4id) {
  if (!$content4id)
    return;
  if ($content4id["headline........"])
    return $content4id["headline........"][0][value];

  for ($i=1; $i<10; $i++)
    if ($content4id["headline.......".$i])
      return $content4id["headline.......".$i][0][value];
}

# check permission to edit discussion - you must be Editor, at least
if(!CheckPerms( $auth->auth["uid"], "slice", $slice_id, PS_EDIT_ALL_ITEMS)) {
  MsgPageMenu($sess->url(self_base())."index.php3", L_NO_PS_EDIT_ALL_ITEMS, "items");
  exit;
}  

$err["Init"] = "";          // error array (Init - just for initializing variable

// get discussion content and tree
$dcontent = GetDiscussionContent($item_id, "", "", false);
$tree = GetDiscussionTree($dcontent);

if ($mode == "hide" || $mode=="delete") {
  switch ($mode) {
    case "hide" :
      $h = ( $h ? 1 : 0 );
      $dcontent[$d_id]["d_state........."][0][value] = $h;

      $db->query("UPDATE discussion SET state='$h' WHERE id='".q_pack_id($d_id)."'");
      break;
    case "delete":
      DeleteNode($tree, $dcontent, $d_id);
      break;
  }
  updateDiscussionCount($item_id);        // update a count of the comments belong to the item

  $cache = new PageCache($db,CACHE_TTL,CACHE_PURGE_FREQ); # database changed -
  $cache->invalidateFor("slice_id=".$slice_id);  # invalidate old cached values

  $dcontent = GetDiscussionContent($item_id, "", "", false);       // refresh the content and the tree because of delete node
  $tree = GetDiscussionTree($dcontent);

}

$aliases = GetDiscussionAliases();
GetDiscussionThread($tree, "0", 0, $outcome);         // get array of images

HtmlPageBegin();   // Print HTML start page tags (html begin, encoding, style sheet, but no title)
?>
<TITLE><?php echo L_MAP_TIT;?></TITLE>

<SCRIPT Language="JavaScript"><!--
  function InitPage() {}
  function DeleteComment(id) {
    if( !confirm("<?php echo L_D_DELETE_COMMENT; ?>"))
       return
    var url="<?php echo $sess->url(con_url("./discedit.php3", "mode=delete")); ?>"
    document.location=url + "&d_id=" + escape(id) + "&item_id=" + "<?php echo $item_id; ?>"
  }
  // -->
</SCRIPT>
</HEAD>
<BODY>
<?php
  echo "<center>
        <H1><B>" . L_D_ADMIN . "</B></H1>";
  PrintArray($err);
  echo $Msg;

  $content = GetItemContent($item_id);
  $headline = getHeadline($content[$item_id]);
  ?>
  <form method="post" action=<?php echo $sess->url(self_base()."index.php3") ?> >
  <table width="95%" border="0" cellspacing="0" cellpadding="1" bgcolor="<?php echo COLOR_TABTITBG ?>" align="center">
  <tr><td class=tabtit><b>&nbsp;<?php 
     echo L_D_ITEM." $headline</b> (". 
           $content[$item_id]["disc_app........"][0][value]. "/".
           $content[$item_id]["disc_count......"][0][value]. ")" ?> </td></tr>
  <tr><td>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="<?php echo COLOR_TABBG ?>">
     <tr><td width="10">&nbsp;</td>
        <td><b><?php echo L_D_TOPIC ?></td>
        <td width="10">&nbsp;</td>
        <td><b><?php echo L_D_AUTHOR ?></td>
        <td width="10">&nbsp;</td>
        <td><b><?php echo L_D_DATE ?></td>
        <td width="10">&nbsp;</td>
        <td align="center"><b><?php echo L_D_ACTIONS ?></td>
        <td width="10">&nbsp;</td>
    </tr>
      <tr><td colspan=9>&nbsp;</td></tr>

 <?php
  $item = new item("","",$aliases,"","","");
  $i=0;
  if (!$outcome)
    echo "<tr><td colspan=9 align=center class=tabtxt>". L_D_NODISCUS ."<br><br></td></tr>";
  else {
    reset( $outcome );
    while (list($d_id, $images) = each($outcome)) {
//      echo ($i++ % 2) ? "<tr>" : "<tr bgcolor=".COLOR_BACKGROUND.">";

      $im="";
      while (list(,$img) = each($images)) {
        $im .= GetImageSrc($img);
      }
       $im2 = "<tr><td>&nbsp;</td>
                <td><table cellspacing=0 cellpadding=0 border=0>
                    <tr><td>".$im. PrintImg("blank.gif",2,21)."</td>
                        <td nowrap>_#SUBJECT#&nbsp;</td>
                    </tr>
                    </table>
                </td>
               <td>&nbsp;</td>
               <td nowrap>_#AUTHOR##</td>
               <td>&nbsp;</td>
               <td nowrap>_#DATE####</td>
               <td>&nbsp;</td>";
       $item->setformat($im2);
       $item->columns = $dcontent[$d_id];
       echo $item->get_item();

       echo "<td align=center>&nbsp;&nbsp;&nbsp;<a href=\"javascript:DeleteComment('".$d_id."')\"><SMALL>". L_D_DELETE ."</SMALL></a>";
       echo "&nbsp;<a href=". con_url($sess->url("discedit2.php3"),"d_id=".
            $d_id."&item_id=".$item_id) ."><SMALL>". L_D_EDIT ."</SMALL></a>";
       $s = ($h = !$dcontent[$d_id]["d_state........."][0][value]) ? L_D_HIDE : L_D_APPROVE;
       echo "&nbsp;<a href=" . con_url($sess->url("discedit.php3"), "mode=hide&h=".$h.
            "&d_id=".$d_id. "&item_id=".$item_id) ."><SMALL>". $s. "</SMALL></a></td>
            <td>&nbsp;</td>";
       echo "</tr>\n";
    }
  }
 ?>
  </table>
  </td></tr>
  <tr><td class=tabtit  align=center><input type="submit" value="<?php echo L_BACK ?>"></td></tr>
  </table>
  </form>
  </center>
<?php
echo "</body></html>";
page_close();
?>
