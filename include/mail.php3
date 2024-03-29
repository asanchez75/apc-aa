<?php
/**
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program (LICENSE); if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package   Include
 * @version   $Id$
 * @author    Jakub Ad�mek, Econnect
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Copyright (C) 1999, 2000 Association for Progressive Communications
 * @link      http://www.apc.org/ APC
 *
*/

// (c) Jakub Ad�mek, Econnect, December 2002

require_once AA_INC_PATH."item.php3";
require_once AA_INC_PATH."item_content.php3";
require_once AA_INC_PATH."slice.class.php3";
require_once AA_INC_PATH."stringexpand.php3";
require_once AA_INC_PATH."htmlMimeMail/htmlMimeMail.php";
require_once AA_INC_PATH."validate.php3";
require_once AA_INC_PATH."toexecute.class.php3";


class AA_Mailtemplate implements iEditable {
    protected $record;

    function __construct($id=null) {
        $this->record = array();
        if ($id and ctype_digit((string)$id)) {
            $template = AA_Metabase::getContent( array('table'=>'email'), new zids($id, 's') );
            if ($template[$id]) {
                $this->record = $template[$id];
            }
        }
    }

    public function getName()                                { return $this->record['description']; }
    public function getId()                                  { return $this->record['id']; }
    public function getOwnerId()                             { return $this->record['owner_module_id']; }
    public function getProperty($property_id, $default=null) { return $this->record[$property_id]; }

        /** iEditable method - adds Object's editable properties to the $form */
    public static function addFormrows($form) {
         AA_MetabaseTableEdit::defaultAddFormrows('email', $form);
    }

    public static function getClassProperties()  {
         return AA_MetabaseTableEdit::defaultGetClassProperties('email');
    }

    public static function load($id, $type=null) {
        return new AA_Mailtemplate($id);
    }


    /** iEditable method - creates Object from the form data */
    public static function factoryFromForm($oowner, $otype=null) {}
    /** iEditable method - save the object to the database */
    public        function save() {}
}

class AA_Mail extends htmlMimeMail {

    /** static getTemplate */
    function getTemplate($mail_id) {
        return GetTable2Array("SELECT * FROM email WHERE id = $mail_id", 'aa_first', 'aa_fields');
    }

    /** setFromTemplate function
     *  Prepares the mail for sending
     *  The e-mail template is taken from database and all aliases
     *  in the template are expanded acording tho $item
     * @param $mail_id
     * @param $item
     */
    function setFromTemplate($mail_id, $item=null) {
        global  $LANGUAGE_CHARSETS;
        // email has the templates in it
        if (! ( $record = AA_Mail::getTemplate($mail_id))) {
            return false;
        }
        // unalias all the template fields including errors_to ...
        foreach ( $record as $key => $value) {
            $record[$key] = AA_Stringexpand::unalias($value, "", $item);
        }
        $record["lang"] = $LANGUAGE_CHARSETS[$record["lang"]];
        return $this->setFromArray($record);
    }

    /** record array('subject','body','header_from','reply_to','errors_to','sender','lang','html','cc','bcc')  */
    function setFromArray($record) {
        // do not send empty emails
        if ( !strlen(trim($record["body"]))) {
            return false;
        }

        if ($record["html"]) {
            $this->setHtml( $record["body"], html2text($record["body"]));
        } else {
            $this->setText( html2text( nl2br($record["body"]) ));
        }
        $this->setSubject($record["subject"]);
        $this->setBasicHeaders($record, "");
        $this->setCharset(strlen($record["lang"]) ? $record["lang"] : 'utf-8');

        if ($record["cc"]) {
            $this->setCc($record["cc"]);
        }
        if ($record["bcc"]) {
            $this->setBcc($record["bcc"]);
        }

        if ($record['attachments']) {
            $attachs = ParamExplode($record['attachments']);
            foreach ($attachs as $attachment) {
                if ($attachment) {
                    $att_data = $this->getFile($attachment);
                }
                $this->addAttachment($att_data, basename(parse_url($attachment, PHP_URL_PATH)));
            }
        }
        return true;
    }

    /** sendLater function
     *  Send prepared e-mail to adresses specified in the $to array.
     *  The e-mail is queued it AA_Toexecute queue before sending (not imediate)
     * @param $to
     */
    function sendLater($to) {
        static $send_mails_total = 0;

        $toexecute = new AA_Toexecute;

        $tos  = array_unique(AA_Validate::filter((array)$to, 'email'));
        $sent = 0;
        foreach ($tos as $to) {

            // 2 minutes for each 20 e-mails
            if ( ($sent % 20) == 0 ) {
                @set_time_limit( 120 );
            }

            // first two mails in the script send directly (for better UX)
            if (++$send_mails_total < 3) {
                $this->send(array($to));
                ++$sent;
            } else {
                // Yes, two nested arrays - mail->send() accepts array($to) and
                // all parameters to later must be in another only one array
                if ( $toexecute->later($this, array(array($to)), 'send_mail') ) {
                    ++$sent;
                }
            }
        }
        return $sent;
    }

    /** setBasicHeaders function
     *  This function fits a record from the @c email table.
     * @param $record
     * @param $default
     */
    function setBasicHeaders($record=array(), $default=array()) {
        $headers = array (
            "From"        => "header_from",
            "Reply-To"    => "reply_to",
            "Errors-To"   => "errors_to"
            );
        foreach ( $headers as $header => $field) {
            if ($record[$field]) {
                $this->setHeader($header, $record[$field]);
            }
            elseif ($default[$field]) {
                $this->setHeader($header, $default[$field]);
            }
        }
        // bounces are going to errors_to (if defined) or ...
        $return_path = ( $record['sender']    ? $record['sender'] :
                        ( $record['header_from'] ? $record['header_from'] :
                          ERROR_REPORTING_EMAIL));
        $extracted_return_path = $this->extractEmails($return_path);
        if (isset($extracted_return_path[0])) {
            $this->setReturnPath($extracted_return_path[0]);
        }
    }

    /** Extracts e-mail from the string:
     *  Econnect <info@ecn.cz>  -> info@ecn.cz
     */
    function extractEmails($string){
        preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $string, $matches);
        return $matches[0];
    }

    /** _encodeHeader function
     *  header encoding does not seem to work correctly
     * @param $input
     * @param $charset
     */
    //maybe fixed in new version 2.5.2 - trying to use default - Honza 15.3.2009
    //function _encodeHeader($input, $charset = 'ISO-8859-1') {
    //    return $input;
    //}

    /** setCharset function
     * @param $charset
     */
    function setCharset($charset) {
        $this->setHeadCharset($charset);
        $this->setHtmlCharset($charset);
        $this->setTextCharset($charset);
    }

    /** toexecutelater function
     *  Toexecutelater - special function called from AA_Toexecute class
     *  - used for queued tasks (runed form cron)
     * @param $to
     */
    function toexecutelater($to) {
        return $this->send($to);
    }

    /** AA_Mail::sendTemplate function
     *  Sends mail defined in e-mail template id $mail_id to all e-mails listed
     *  in $to (array or string) and unalias aliases according to $item
     * @param $mail_id
     * @param $to         array()
     * @param $item
     *
     * Static function called as AA_Mail::sendTemplate($mail_id,$to,$item=null)
     */
     function sendTemplate($mail_id, $to, $item=null, $later=true) {
        // email has the templates in it
        $mail = new AA_Mail;
        if (!$mail->setFromTemplate($mail_id, $item)) {
            return false;
        }

        if ($later) {
            return $mail->sendLater($to);
        }

        $tos  = array_unique(array_map('trim', is_array($to) ? $to : array($to)));
        return $mail->send($tos);
    }

    /** sendToReader function
     *  Sends mail defined in e-mail template id $mail_id to all zids (Readers).
     *  Mail template is unaliased using aliases and data form item identified by
     *  $zids (often Reader item). The recipients are Reders itself, by default.
     * @param $mail_id
     * @param $zids
     */
     function sendToReader($mail_id, $zids) {
        $mail_count = 0;
        for ( $i=0, $ino=$zids->count(); $i<$ino; ++$i) {
            $item = AA_Item::getItem($zids->longids($i));
            $to   = $item->getval(FIELDID_EMAIL);
            $mail_count += AA_Mail::sendTemplate($mail_id, $to, $item);
        }
        return $mail_count;
    }
};

/** html2text function
 *  Strips the HTML tags and lot more to get a plain text mail version.
 *   Replaces URL links by the link in compound brackets behind the linked text.
 *   Removes diacritics.
 * @param $html
 */
function html2text($html) {

    $html = html_entity_decode(str_ireplace(array('&nbsp;','&ensp;','&emsp;','&thinsp;'), array(' ',' ',' ',' '), $html));

    // Strip diacritics
    // $html = strtr( $html, "������������������������������ة����ݮ",
    //                       "aacdeeilnoorstuuuyzAACDEEILNOORSTUUUYZ");

    // Replace URL references <a href="http://xy">Link</a> => Link {http://xy}
    /* We can't directly use preg_replace, because it would find the first <a href
       and the last </a>. */
    $ahref       = "<[ \t]*a[ \t][^>]*href[ \t]*=[ \t]*[\"\\']([^\"\\']*)[\"\\'][^>]*>";
    $html_ahrefs = array();
    preg_match_all("'$ahref'si", $html, $html_ahrefs);
    $html_parts = preg_split("'$ahref'si", $html);

    reset($html_parts);
    reset($html_ahrefs[0]);
    $matches = array();
    // Take the first part before any <a href>
    list(, $html) = each ($html_parts);
    while (list(, $html_part) = each($html_parts)) {
        list(, $html_ahref) = each($html_ahrefs[0]);
        preg_match ( "'$ahref(.*)</[ \t]*a[ \t]*>(.*)'si", $html_ahref. $html_part , $matches);
        if ( $matches[1] == $matches[2] ) {
            $html .= $matches[1]. $matches[3];
        } else {
            $html .= $matches[2]. ' {'. $matches[1] .'}'. $matches[3];
        }
    }

    $search = array (
        // Strip out leading white space
        "'[\r\n][ \t]+'",
        "'[\r\n]*'",
        "'<hr>'si",
        "'</tr>'si",
        "'</table>'si",
        // If the previous commands added too much whitespace, delete it
        "'\\n\\n\\n+'si",
        "'<br[^>]{0,2}>'si",   // <br> as well as <br />
        "'</p>'si",
        "'</h[1-9]>'si",
        // Strip out javascript, style and head
        "'<head[^>]*?>.*?</head>'si",
        "'<script[^>]*?>.*?</script>'si",
        "'<style[^>]*?>.*?</style>'si",
        // Strip out html tags
        "'<[\/\!]*?[^<>]*?>'si");

   $replace = array (
        // Strip out leading white space
        "",
        "",
        "\n------------------------------------------------------------\n",
        "\n",
        "\n",
        // If the previous commands added too much whitespace, delete it
        "\n\n",
        "\n",   // <br> as well as <br />
        "\n\n",
        "\n\n",
        // Strip out javascript, style and head
        "",
        "",
        "",
        // Strip out html tags
        "");

        // Replace html entities - removed - now done by html_entity_decode() above
        // "'&(quot|#34);'i" => '"',
        // "'&(amp|#38);'i"  => '&',
        // "'&(lt|#60);'i"   => '<',
        // "'&(gt|#62);'i"   => '>',
        // "'&(nbsp|#160);'i"=> ' ',
        // evaluate as php
        //"'&#(\d+);'e"     => "chr(\\1)");

    $html = preg_replace($search, $replace, $html);

    return $html;
}

?>
