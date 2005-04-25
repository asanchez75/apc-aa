<!--

  Strangely, IE sucks with or without the DOCTYPE switch.
  I thought it would only suck without it.

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

   Spell Checker Plugin for HTMLArea-3.0
   Implementation by Mihai Bazon.  Sponsored by www.americanbible.org

   htmlArea v3.0 - Copyright (c) 2003 interactivetools.com, inc.
   This notice MUST stay intact for use (see license.txt).

   A free WYSIWYG editor replacement for <textarea> fields.
   For full source code and docs, visit http://www.interactivetools.com/

   Version 3.0 developed by Mihai Bazon for InteractiveTools.
         http://dynarch.com/mishoo

   $Id$

-->
<?php
/* changed for APC-AA by pavelji@ecn.cz - we need constants.php3 */
include "../../../../include/constants.php3";
?>

<html xmlns="http://www.w3.org/1999/xhtml">

  <head>
    <title>Spell Checker</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script type="text/javascript" src="spell-check-ui.js"></script>

    <style type="text/css">
      html, body { height: 100%; margin: 0px; padding: 0px; background-color: #fff;
      color: #000; }
      a:link, a:visited { color: #00f; text-decoration: none; }
      a:hover { color: #f00; text-decoration: underline; }

      table { background-color: ButtonFace; color: ButtonText;
      font-family: tahoma,verdana,sans-serif; font-size: 11px; }

      iframe { background-color: #fff; color: #000; }

      .controls { width: 13em; }
      .controls .sectitle { /* background-color: #736c6c; color: #fff;
      border-top: 1px solid #000; border-bottom: 1px solid #fff; */
      text-align: center;
      font-weight: bold; padding: 2px 4px; }
      .controls .secbody { margin-bottom: 10px; }

      button, select { font-family: tahoma,verdana,sans-serif; font-size: 11px; }
      button { width: 6em; padding: 0px; }

      input, select { font-family: fixed,"andale mono",monospace; }

      #v_currentWord { color: #f00; font-weight: bold; font-size: 120%; }
      #statusbar { padding: 7px 0px 0px 5px; }
      #status { font-weight: bold; }
    </style>

  </head>

  <body onload="initDocument()">

<?php /* changed for APC-AA by pavelji@ecn.cz - get path for spellchecker script */ ?>
    <form style="display: none;" action="<?php echo AA_HTMLAREA_SPELL_CGISCRIPT; ?>spell-check-logic.cgi"
        method="post" target="framecontent"
        accept-charset="utf-8"
        ><input type="hidden" name="content" id="f_content"
        /><input type="hidden" name="dictionary" id="f_dictionary"
        /><input type="hidden" name="init" id="f_init" value="1"
        /></form>

    <table style="height: 100%; width: 100%; border-collapse: collapse;" cellspacing="0" cellpadding="0">
      <tr>
        <td colspan="2" style="height: 1em; padding: 2px;">
          <div style="float: right; padding: 2px;"><span>Dictionary</span>
            <select id="v_dictionaries" style="width: 10em"></select>
            <button id="b_recheck">Re-check</button>
          </div>
          <span id="status">Please wait.  Calling spell checker.</span>
        </td>
      </tr>
      <tr>
        <td valign="top" class="controls">
          <div class="sectitle">Original word</div>
          <div class="secbody" id="v_currentWord" style="text-align: center">pliz weit ;-)</div>
          <div class="sectitle">Replace with</div>
          <div class="secbody">
            <input type="text" id="v_replacement" style="width: 94%; margin-left: 3%;" /><br />
            <div style="text-align: center; margin-top: 2px;">
              <button id="b_replace">Replace</button><button
                id="b_replall">Replace all</button><br /><button
                id="b_ignore">Ignore</button><button
                id="b_ignall">Ignore all</button>
            </div>
          </div>
          <div class="sectitle">Suggestions</div>
          <div class="secbody">
            <select size="11" style="width: 94%; margin-left: 3%;" id="v_suggestions"></select>
          </div>
        </td>

        <td>
          <iframe src="about:blank" width="100%" height="100%"
            id="i_framecontent" name="framecontent"></iframe>
        </td>
      </tr>
      <tr>
        <td style="height: 1em;" colspan="2">
          <div style="padding: 4px 2px 2px 2px; float: right;">
            <button id="b_ok">OK</button>
            <button id="b_cancel">Cancel</button>
          </div>
          <div id="statusbar"></div>
        </td>
      </tr>
    </table>

  </body>

</html>
