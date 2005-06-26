<?php
/**
 * Form displayed in popup window sending emails to user/group_of_users
 *
 * @version $Id$
 * @author Honza Malik <honza.malik@ecn.cz>
 * @copyright Copyright (C) 1999, 2000 Association for Progressive Communications
 * @param $items[] - OLD VERSION : array of selected users (in reader management slice)
 * @param $chb[] - array of selected users (in reader management slice)
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

require_once "../include/init_page.php3";
require_once $GLOBALS['AA_INC_PATH']. "formutil.php3";
require_once $GLOBALS['AA_INC_PATH']. "searchbar.class.php3";
require_once $GLOBALS["AA_BASE_PATH"]."modules/alerts/util.php3";
require_once $GLOBALS['AA_INC_PATH']. "varset.php3";

$searchbar = new searchbar();   // mainly for bookmarks
$items=$chb;

if ( !$send ) {               // for the first time - directly from item manager
    $sess->register('r_wm_state');
    unset($r_wm_state);       // clear if it was filled
    $r_wm_state['items'] = $items;
    $lang = get_mgettext_lang();
    $html = 1;
} else {
    $items     = $r_wm_state['items'];  // session variable holds selected items fmom item manager
    if ( $send ) {    // we really want to send email - so store template
        do {
            // --- write the e-mail template to the table ---
            $varset = new Cvarset();
            $description     = 'Bulk email '.now() . ' '. $auth->auth['uid'];
            $owner_module_id = q_pack_id($slice_id);
            $type            = 'bulk email';
            ValidateInput("subject",     _m("Subject"),            $subject,     $err, true,  "text");
            ValidateInput("body",        _m("Body"),               $body,        $err, true,  "text");
            ValidateInput("header_from", _m("From (email)"),       $header_from, $err, true, "text");
            ValidateInput("reply_to",    _m("Reply to (email)"),   $reply_to,    $err, false, "text");
            ValidateInput("errors_to",   _m("Errors to (email)"),  $errors_to,   $err, false, "text");
            ValidateInput("sender",      _m("Sender (email)"),     $sender,      $err, false, "text");
            ValidateInput("lang",        _m("Language (charset)"), $lang,        $err, false, "text");
            ValidateInput("html",        _m("Use HTML"),           $html,        $err, false, "number");

            if ( count($err) > 1) break;

            $varset->addglobals( array('description', 'subject', 'body',
                                       'header_from', 'reply_to', 'errors_to', 'sender',
                                       'lang', 'owner_module_id', 'type'),
                                 'quoted');
            $varset->add('html', 'number', $html);

            $SQL = "INSERT email ". $varset->makeINSERT();
            if ( !$db->tquery($SQL)) {
                $err["DB"] = MsgErr( _m("Can't change slice settings") );
                break;    // not necessary - we have set the halt_on_error
            }

            // --- write the e-mail template to the table - end ---

            $mail_id = get_last_insert_id($db, 'email');  // get mail template id

            if ( !is_numeric($mail_id) )  {
                $err["mail"] = MsgErr( _m("No template set (which is strange - template was just written to the database") );
                break;
            }

            // --- send emails
            if ( $group == 'testuser') {
                $mails_sent  = send_mail_from_table($mail_id, $testemail);
                $users_count = 1;
            } else {
                // get reader's zids
                $zids = getZidsFromGroupSelect($group, $items, $searchbar);
                // following functionality could be extend by adding third
                // parameter $recipient (for testing e-mail)
                $mails_sent  = send_mail_to_reader($mail_id, $zids);
                $users_count = $zids->count();
            }
            $Msg = MsgOK(_m("Email sucessfully sent (Users: %1, Emails sent (valid e-mails...): %2)",
                                               array($users_count, $mails_sent)));

            if ((string)$group == (string)"sel_item") {
                $sel = "LIST";
            } elseif ((string)$group == (string)"testuser") {
                $sel = "TEST";
            } else {
                $sel = get_if($group,"0");  // bookmarks groups are identified by numbers
            }
            writeLog("EMAIL_SENT",array($users_count, $mails_sent),$sel);
            // remove temporary email template from database
            // TODO - store the tamplate and allow user to reuse it
            $SQL = "DELETE FROM email WHERE id='$mail_id'";
            if ( !$db->tquery($SQL)) {
                $err["DB"] = MsgErr( _m("Can't delete email template") );
                break;    // not necessary - we have set the halt_on_error
            }
        } while (false);
    }
}

HtmlPageBegin();   // Print HTML start page tags (html begin, encoding, style sheet, but no title)

echo '
  <link rel=StyleSheet href="'.$AA_INSTAL_PATH.'tabledit.css" type="text/css"  title="TableEditCSS">
  <title>'.  _m("Write email to users") .'</title>';
 IncludeManagerJavascript();
echo '
</head>
<body>
  <h1>'. _m("Bulk Email Wizard") .'</h1>
  <form name=mailform>';

PrintArray($err);
echo $Msg;

$slice = new slice($slice_id);

FrmTabCaption( (is_array($items) ? _m("Recipients") : ( _m("Stored searches for ").$slice->name()) ));

$messages['view_items']     = _m("View Recipients");
$messages['selected_items'] = _m('Selected users');
$additional[]               = array( 'text'    => '<input type="text" name="testemail" value="'.$testemail.'" size="80"> '._m('Test email address'),
                                     'varname' => 'testuser');
FrmItemGroupSelect( $items, $searchbar, 'users', $messages, $additional);

FrmTabSeparator( _m('Write the email') );

FrmInputText(  'subject',     _m('Subject'),           dequote($subject),     254, 80, true);
FrmTextarea(   'body',        _m('Body'),              dequote($body),         12, 80, true);
FrmInputText(  'header_from', _m('From (email)'),      dequote($header_from), 254, 80, true);
FrmInputText(  'reply_to',    _m('Reply to (email)'),  dequote($reply_to),    254, 80, false);
FrmInputText(  'errors_to',   _m('Errors to (email)'), dequote($errors_to),   254, 80, false);
FrmInputText(  'sender',      _m('Sender (email)'),    dequote($sender),      254, 80, false);
FrmInputSelect('lang',        _m('Language (charset)'), GetEmailLangs(),            $lang, true);
FrmInputSelect('html',        _m('Use HTML'),           array(_m('no'), _m('yes')), $html, true);

FrmTabEnd(array( 'send' =>array('type'=>'submit', 'value'=>_m('Send')),
                 'close'=>array('type'=>'button', 'value'=>_m('Close'), 'add'=>'onclick="window.close()"')),
          $sess, $slice_id);

// list selected items to special form - used by manager.js to show items (recipients)
echo "\n  </form>";
FrmItemListForm($items);
echo "\n  </body>\n</html>";
page_close();
?>