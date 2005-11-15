<?
/**
 * Show a list of images in a long horizontal table.
 * @author $Author$
 * @version $Id$
 * @package ImageManager
 */

require_once('config.inc.php');
require_once('ddt.php');
require_once('Classes/ImageManager.php');

// uncomment for debugging

// _ddtOn();

//default path is /
$relative = '/';
$manager = new ImageManager($IMConfig);

//process any file uploads
$manager->processUploads();

$manager->deleteFiles();

$refreshDir = false;
//process any directory functions
if($manager->deleteDirs() || $manager->processNewDir())
	$refreshDir = true;

//check for any sub-directory request
//check that the requested sub-directory exists
//and valid
if(isset($_REQUEST['dir']))
{
	$path = rawurldecode($_REQUEST['dir']);
	if($manager->validRelativePath($path))
		$relative = $path;
}


$manager = new ImageManager($IMConfig);


//get the list of files and directories
$list = $manager->getFiles($relative);


/* ================= OUTPUT/DRAW FUNCTIONS ======================= */

/**
 * Draw the files in an table.
 */
function drawFiles($list, &$manager)
{
	global $relative;
	global $IMConfig;

	foreach($list as $entry => $file) 
	{ 
		?>
		<td>

		<?php _ddt( __FILE__, __LINE__, "drawFiles(): relative is '" . $file['relative'] . "' thumbnail '" . $manager->getThumbnail($file['relative']) . "'" ); ?>

        <table width="100" cellpadding="0" cellspacing="0"><tr><td class="block" style="cursor: pointer;" onclick="selectImage('<? echo $file['relative'];?>', '<? echo $entry; ?>', <? echo $file['image'][0];?>, <? echo $file['image'][1]; ?>);" title="<?php echo $entry; ?> - <?php echo Files::formatSize($file['stat']['size']); ?>">

		<img src="<?php print $manager->getThumbnail($file['relative']); ?>" alt="<?php echo $entry; ?> - <?php echo Files::formatSize($file['stat']['size']); ?>"/>
		</td></tr><tr><td class="edit">
			<a href="<?php print $IMConfig['backend_url']; ?>__function=images&dir=<?php echo $relative; ?>&amp;delf=<?php echo rawurlencode($file['relative']);?>" title="Trash" onclick="return confirmDeleteFile('<?php echo $entry; ?>');"><img src="<?php print $IMConfig['base_url'];?>img/edit_trash.gif" height="15" width="15" alt="Trash"/></a><a href="javascript:;" title="Edit" onclick="editImage('<?php echo rawurlencode($file['relative']);?>');"><img src="<?php print $IMConfig['base_url'];?>img/edit_pencil.gif" height="15" width="15" alt="Edit"/></a>
		<?php if($file['image']){ echo $file['image'][0].'x'.$file['image'][1]; } else echo $entry;?>
		</td></tr></table></td> 
	  <?php 
	}//foreach
}//function drawFiles


/**
 * Draw the directory.
 */
function drawDirs($list, &$manager) 
{
    global $relative;
    global $IMConfig;

	foreach($list as $path => $dir) 
	{ 
        /* changed for APC-AA by honzam@ecn.cz - added two AA_CP_Session parameters below */
        ?>
		<td><table width="100" cellpadding="0" cellspacing="0"><tr><td class="block">
		<a href="images.php?AA_CP_Session=<?php echo $GLOBALS['AA_CP_Session']?>&amp;dir=<?php echo rawurlencode($path); ?>" onclick="updateDir('<?php echo $path; ?>')" title="<?php echo $dir['entry']; ?>"><img src="<?php print $IMConfig['base_url'];?>img/folder.gif" height="80" width="80" alt="<?php echo $dir['entry']; ?>" /></a>
		</td></tr><tr>
		<td class="edit">
			<a href="images.php?AA_CP_Session=<?php echo $GLOBALS['AA_CP_Session']?>&amp;dir=<?php echo $relative; ?>&amp;deld=<?php echo rawurlencode($path); ?>" title="Trash" onclick="return confirmDeleteDir('<?php echo $dir['entry']; ?>', <?php echo $dir['count']; ?>);"><img src="<?php print $IMConfig['base_url'];?>img/edit_trash.gif" height="15" width="15" alt="Trash"/></a>
			<?php echo $dir['entry']; ?>
		</td>
		</tr></table></td>
	  <?php 
	} //foreach
}//function drawDirs


/**
 * No directories and no files.
 */
function drawNoResults() 
{
?>
<table width="100%">
  <tr>
    <td class="noResult">No Images Found</td>
  </tr>
</table>
<?	
}

/**
 * No directories and no files.
 */
function drawErrorBase(&$manager) 
{
?>
<table width="100%">
  <tr>
    <td class="error">Invalid base directory: <?php echo $manager->config['images_dir']; ?></td>
  </tr>
</table>
<?	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
	<title>Image List</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="<?php print $IMConfig['base_url'];?>assets/imagelist.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
_backend_url = "<?php print $IMConfig['backend_url']; ?>";
</script>

<script type="text/javascript" src="<?php print $IMConfig['base_url'];?>assets/dialog.js"></script>
<script type="text/javascript">
/*<![CDATA[*/

	if(window.top)
		HTMLArea = window.top.HTMLArea;

	function hideMessage()
	{
		var topDoc = window.top.document;
		var messages = topDoc.getElementById('messages');
		if(messages)
			messages.style.display = "none";
	}

	init = function()
	{
		hideMessage();
		var topDoc = window.top.document;

<?php 
	//we need to refesh the drop directory list
	//save the current dir, delete all select options
	//add the new list, re-select the saved dir.
	if($refreshDir) 
	{ 
		$dirs = $manager->getDirs();
?>
		var selection = topDoc.getElementById('dirPath');
		var currentDir = selection.options[selection.selectedIndex].text;

		while(selection.length > 0)
		{	selection.remove(0); }
		
		selection.options[selection.length] = new Option("/","<?php echo rawurlencode('/'); ?>");	
		<?php foreach($dirs as $relative=>$fullpath) { ?>
		selection.options[selection.length] = new Option("<?php echo $relative; ?>","<?php echo rawurlencode($relative); ?>");		
		<?php } ?>
		
		for(var i = 0; i < selection.length; i++)
		{
			var thisDir = selection.options[i].text;
			if(thisDir == currentDir)
			{
				selection.selectedIndex = i;
				break;
			}
		}		
<?php } ?>
	}	

	function editImage(image) 
	{
		var url = "<?php print $IMConfig['backend_url']; ?>__function=editor&img="+image;
		Dialog(url, function(param) 
		{
			if (!param) // user must have pressed Cancel
				return false;
			else
			{
				return true;
			}
		}, null);		
	}

/*]]>*/
</script>
<script type="text/javascript" src="<?php print $IMConfig['base_url'];?>assets/images.js"></script>
</head>

<body>
<?php if ($manager->isValidBase() == false) { drawErrorBase($manager); } 
	elseif(count($list[0]) > 0 || count($list[1]) > 0) { ?>
<table>
	<tr>
	<?php drawDirs($list[0], $manager); ?>
	<?php drawFiles($list[1], $manager); ?>
	</tr>
</table>
<?php } else { drawNoResults(); } ?>
</body>
</html>