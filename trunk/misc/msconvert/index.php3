<?php
require_once "./../../include/config.php3";
require_once ($GLOBALS['AA_INC_PATH']."util.php3");
require_once ($GLOBALS['AA_INC_PATH']."files.class.php3");

if (!$view)     $view     = false;
if (!$encoding) $encoding = CONV_DEFAULTENCODING;
if (!$sysenc)   $sysenc   = CONV_SYSTEMENCODING;

$uploadpath = IMG_UPLOAD_PATH;

echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">
<HTML>
  <HEAD>
    <title>'. _m('Foreign Formats Convertor') .'</title>
    <meta http-equiv="Content-Type" content="text/html; charset='.$encoding.'">
  </HEAD>';

  if ($submit=="Import") {
      echo "<body onload=\"PasteHTML();\">";
      $view=false;
  } else {
      echo "<body>";
      $view=true;
  }

  echo '<h2 align="center">'._m('Action Apps PDF/DOC Convertor').'</h2>
  <p align="center">'._m('Experimental feature').'</p><br><br>';


if (!$userfile) {
    ?>
    <form method="post" action="" enctype="multipart/form-data">
    <input type="file" name="userfile"> <br>
    <input type="submit" value="Preview">
    <input type="submit" name="submit" value="Import">
    <?php
    if ($encoding) echo "<input type=\"hidden\" name=\"encoding\" value=\"$encoding\">";
    echo "</form>";
} else {
    $file_name=gensalt (20);
    $realname=$HTTP_POST_FILES['userfile']['name'];
    $stringoutput='';
    $dest_file = Files::uploadFile('userfile', $uploadpath, '', 'new', $file_name);
    if ($dest_file === false) {   // error
        $error = Files::lastErrMsg();
    }
    if ($error) die($GLOBALS["IMG_UPLOAD_PATH"]);
    if (!$error) {
        if (preg_match("/.doc/i",$realname)) {
            $safe_file_name=escapeshellarg ($file_name);
            $safe_encoding=escapeshellarg ($encoding);
            system ($CONV_HTMLFILTERS[".doc"]." $uploadpath$safe_file_name --charset=$safe_encoding --targetdir=$uploadpath $safe_file_name.html");
            $out=file("$uploadpath$file_name.html");
            unlink ("$uploadpath$file_name");
            unlink ("$uploadpath$file_name.html");
            $insidesection=false;
            $buffer=array();
            $output=array();
            while (list($linenum,$line)=each($out)){
                if (preg_match("/^<!--Section/",$line)) {
                    if ($insidesection==true) {
                        $output=array_merge ($output,$buffer);
                        $buffer=array();
                    } else $buffer=array();
                    $insidesection=true;
                } else {
                    $line=str_replace("&scaron;","�",$line);
                    $line=str_replace("&Scaron;","�",$line);
                    $buffer[]=$line;
                }
            }

        } elseif (preg_match("/.pdf/i",$realname)){
            $safe_file_name=escapeshellarg ($file_name);
            $command=$CONV_HTMLFILTERS[".pdf"]." -noframes -i $uploadpath$safe_file_name $uploadpath$safe_file_name.html";
            echo $command;
            exec($command);
            $out=file("$uploadpath$file_name.html");
            unlink ("$uploadpath$file_name");
            unlink ("$uploadpath$file_name.html");
            if (!$out) $out[]=" ";
            $insidesection=false;
            $buffer=array();
            $output=array();
            while (list($linenum,$line)=each($out)){
                if (preg_match("/body/i",$line)) {
                    if ($insidesection==true) {
                        $output=array_merge ($output,$buffer);
                        $buffer=array();
                    } else $buffer=array();
                    $insidesection=true;
                } else {
                    $buffer[]=$line;
                }
            }
        } elseif (preg_match("/.xls/i",$realname)){
            $safe_file_name=escapeshellarg ($file_name);
            $safe_encoding=escapeshellarg ($encoding);
            $safe_sysenc=escapeshellarg ($sysenc);
            $command=$CONV_HTMLFILTERS[".xls"]." -nh -fw $uploadpath$safe_file_name >> $uploadpath$safe_file_name.html";
            exec($command);
            $command=$CONV_HTMLFILTERS["iconv"]." -f $safe_sysenc -t $safe_encoding $uploadpath$safe_file_name.html >>$uploadpath$safe_file_name.html.coded";
            exec($command);
            $out=file("$uploadpath$file_name.html.coded");
            unlink ("$uploadpath$file_name.html.coded");
            unlink ("$uploadpath$file_name");
            unlink ("$uploadpath$file_name.html");
            if (!$out) $out[]=" ";
            $insidesection=true;
            $output=array();
            while (list($linenum,$line)=each($out)){
                if (preg_match("/<HR>/",$line)) {
                    $line=str_replace("<HR>","",$line);
                    $output[]=$line;
                    $insidesection=false;
                } else {
                    if ($insidesection==true) $output[]=$line;
                }
            }
        }
        if ($view)
        while (list($linenum,$line)=each($output)){
            echo "$line\n";
        }
        reset($output);
        echo "<form name=\"aform\">";
        echo "<input type=hidden name=content value=\"";
        while (list($linenum,$line)=each($output)){
            $stringoutput .= $line;
        }
        // we need to remove added background color, ...
        $stringoutput=preg_replace("/<\/?div.*>/i","",$stringoutput); // remove all DIVs
        $stringoutput=preg_replace("/ style=\".+\"/i","",$stringoutput); // remove all styles
        $stringoutput=htmlspecialchars($stringoutput);
        echo $stringoutput;
        echo "\"></form>";
        ?>
        <script>
        function PasteHTML() {
            window.opener.document.inputform.elements["<?php echo $inputid?>"].value = document.aform.content.value;
            window.opener.document.inputform.<?php echo $inputid?>html[0].checked = true;
            window.close();
        }
        </script>
        <a href='javascript: PasteHTML();'>Paste and close </a>
        <?php
    } else {
        // ERROR uploading...
        if ($my_uploader->errors) {
            while (list($key, $var) = each($my_uploader->errors)){
                echo $var . "<br>";
            }
        }
    }
}
?>

</body>
</html>