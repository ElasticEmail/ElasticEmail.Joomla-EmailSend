<?php

/**
 * @author ElasticEmail
 * @date: 2019-04-10
 *
 * @copyright  Copyright (C) 2010-2019 elasticemail.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
/**
 * @package     Joomla.Platform
 * @subpackage  Mail
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_PLATFORM') or die;

jimport('phpmailer.phpmailer');

//Loaded EEmail libraries
require_once JPATH_LIBRARIES . '/eesender/include.php';

/**
 * Email Class.  Provides a common interface to send email from the Joomla! Platform
 *
 * @package     Joomla.Platform
 * @subpackage  Mail
 * @since       11.1
 */
class JMail extends PHPMailer {

    /**
     * @var    array  JMail instances container.
     * @since  11.3
     */
    protected static $instances = array();

    /**
     * @var    string  Charset of the message.
     * @since  11.1
     */
    public $CharSet = 'utf-8';

    /**
     * Constructor
     *
     * @since   11.1
     */
    public function __construct() {
        // PHPMailer has an issue using the relative path for its language files
        $this->SetLanguage('joomla', JPATH_PLATFORM . '/phpmailer/language/');
        
        //Init EE MAIL;
        $params = JComponentHelper::getParams('com_eesender');
        
        $this->eemail = new EEsenderSender($params->get('apikey'));
        
        // Initialize the logger class
        jimport('joomla.error.log');
        $date = JFactory::getDate()->format('Y_m');

        // Add the logger.
        JLog::addLogger(
                array(
                    'text_file' => 'plg_system_eesender.log.' . $date . '.php'
                )
        );
    }

    /**
     * Returns the global email object, only creating it
     * if it doesn't already exist.
     *
     * NOTE: If you need an instance to use that does not have the global configuration
     * values, use an id string that is not 'Joomla'.
     *
     * @param   string  $id  The id string for the JMail instance [optional]
     *
     * @return  JMail  The global JMail object
     *
     * @since   11.1
     */
    public static function getInstance($id = 'Joomla') {
        if (empty(self::$instances[$id])) {
            self::$instances[$id] = new JMail;
        }

        return self::$instances[$id];
    }

    /**
     * Send the mail
     *
     * @return  mixed  True if successful; JError if using legacy tree (no exception thrown in that case).
     *
     * @since   11.1
     * @throws  RuntimeException
     */
    public function phpMailerSend() {
        if (JFactory::getConfig()->get('mailonline', 1)) {
            if (($this->Mailer == 'mail') && !function_exists('mail')) {
                if (class_exists('JError')) {
                    return JError::raiseNotice(500, JText::_('JLIB_MAIL_FUNCTION_DISABLED'));
                } else {
                    throw new RuntimeException(sprintf('%s::Send mail not enabled.', get_class($this)));
                }
            }

            @$result = parent::Send();

            if ($result == false) {
                if (class_exists('JError')) {
                    $result = JError::raiseNotice(500, JText::_($this->ErrorInfo));
                } else {
                    throw new RuntimeException(sprintf('%s::Send failed: "%s".', get_class($this), $this->ErrorInfo));
                }
            }

            return $result;
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('JLIB_MAIL_FUNCTION_OFFLINE'));

            return false;
        }
    }

    public function Send() {
        $params = JComponentHelper::getParams('com_eesender');
        
        if($params->get('ee_enable') == 'yes'){
        return $this->eeMailSend();
        }else{
            return $this->phpMailerSend();
        }
    }

    /**
     * Set the email sender
     *
     * @param   mixed  $from  email address and Name of sender
     *                        <code>array([0] => email Address, [1] => Name)</code>
     *                        or as a string
     *
     * @return  JMail  Returns this object for chaining.
     *
     * @since   11.1
     * @throws  UnexpectedValueException
     */
    public function setSender($from) {
        if (is_array($from)) {
            // If $from is an array we assume it has an address and a name
            if (isset($from[2])) {
                // If it is an array with entries, use them
                $this->SetFrom(JMailHelper::cleanLine($from[0]), JMailHelper::cleanLine($from[1]), (bool) $from[2]);
            } else {
                $this->SetFrom(JMailHelper::cleanLine($from[0]), JMailHelper::cleanLine($from[1]));
            }
        } elseif (is_string($from)) {
            // If it is a string we assume it is just the address
            $this->SetFrom(JMailHelper::cleanLine($from));
        } else {
            // If it is neither, we log a message and throw an exception
            JLog::add(JText::sprintf('JLIB_MAIL_INVALID_EMAIL_SENDER', $from), JLog::WARNING, 'jerror');
            throw new UnexpectedValueException(sprintf('Invalid email Sender: %s, JMail::setSender(%s)', $from));
        }

        return $this;
    }

    /**
     * Set the email subject
     *
     * @param   string  $subject  Subject of the email
     *
     * @return  JMail  Returns this object for chaining.
     *
     * @since   11.1
     */
    public function setSubject($subject) {
        $this->Subject = JMailHelper::cleanLine($subject);

        return $this;
    }

    /**
     * Set the email body
     *
     * @param   string  $content  Body of the email
     *
     * @return  JMail  Returns this object for chaining.
     *
     * @since   11.1
     */
    public function setBody($content) {
        /*
         * Filter the Body
         * TODO: Check for XSS
         */
        $this->Body = JMailHelper::cleanText($content);

        return $this;
    }

    /**
     * Add recipients to the email.
     *
     * @param   mixed   $recipient  Either a string or array of strings [email address(es)]
     * @param   mixed   $name       Either a string or array of strings [name(s)]
     * @param   string  $method     The parent method's name.
     *
     * @return  JMail  Returns this object for chaining.
     *
     * @since   11.1
     * @throws  InvalidArgumentException
     */
    protected function add($recipient, $name = '', $method = 'AddAddress') {
        // If the recipient is an array, add each recipient... otherwise just add the one
        if (is_array($recipient)) {
            if (is_array($name)) {
                $combined = array_combine($recipient, $name);

                if ($combined === false) {
                    throw new InvalidArgumentException("The number of elements for each array isn't equal.");
                }

                foreach ($combined as $recipientEmail => $recipientName) {
                    $recipientEmail = JMailHelper::cleanLine($recipientEmail);
                    $recipientName = JMailHelper::cleanLine($recipientName);
                    call_user_func('parent::' . $method, $recipientEmail, $recipientName);
                }
            } else {
                $name = JMailHelper::cleanLine($name);

                foreach ($recipient as $to) {
                    $to = JMailHelper::cleanLine($to);
                    call_user_func('parent::' . $method, $to, $name);
                }
            }
        } else {
            $recipient = JMailHelper::cleanLine($recipient);
            call_user_func('parent::' . $method, $recipient, $name);
        }

        return $this;
    }

    /**
     * Add recipients to the email
     *
     * @param   mixed  $recipient  Either a string or array of strings [email address(es)]
     * @param   mixed  $name       Either a string or array of strings [name(s)]
     *
     * @return  JMail  Returns this object for chaining.
     *
     * @since   11.1
     */
    public function addRecipient($recipient, $name = '') {
        $this->add($recipient, $name, 'AddAddress');

        return $this;
    }

    /**
     * Add carbon copy recipients to the email
     *
     * @param   mixed  $cc    Either a string or array of strings [email address(es)]
     * @param   mixed  $name  Either a string or array of strings [name(s)]
     *
     * @return  JMail  Returns this object for chaining.
     *
     * @since   11.1
     */
    public function addCC($cc, $name = '') {
        // If the carbon copy recipient is an array, add each recipient... otherwise just add the one
        if (isset($cc)) {
            $this->add($cc, $name, 'AddCC');
        }

        return $this;
    }

    /**
     * Add blind carbon copy recipients to the email
     *
     * @param   mixed  $bcc   Either a string or array of strings [email address(es)]
     * @param   mixed  $name  Either a string or array of strings [name(s)]
     *
     * @return  JMail  Returns this object for chaining.
     *
     * @since   11.1
     */
    public function addBCC($bcc, $name = '') {
        // If the blind carbon copy recipient is an array, add each recipient... otherwise just add the one
        if (isset($bcc)) {
            $this->add($bcc, $name, 'AddBCC');
        }

        return $this;
    }

    /**
     * Add file attachments to the email
     *
     * @param   mixed  $attachment  Either a string or array of strings [filenames]
     * @param   mixed  $name        Either a string or array of strings [names]
     * @param   mixed  $encoding    The encoding of the attachment
     * @param   mixed  $type        The mime type
     *
     * @return  JMail  Returns this object for chaining.
     *
     * @since   12.2
     * @throws  InvalidArgumentException
     */
    public function addAttachment($attachment, $name = '', $encoding = 'base64', $type = 'application/octet-stream', $disposition = 'attachment') {
        // If the file attachments is an array, add each file... otherwise just add the one
        if (isset($attachment)) {
            if (is_array($attachment)) {
                if (!empty($name) && count($attachment) != count($name)) {
                    throw new InvalidArgumentException("The number of attachments must be equal with the number of name");
                }

                foreach ($attachment as $key => $file) {
                    if (!empty($name)) {
                        parent::AddAttachment($file, $name[$key], $encoding, $type);
                    } else {
                        parent::AddAttachment($file, $name, $encoding, $type);
                    }
                }
            } else {
                parent::AddAttachment($attachment, $name, $encoding, $type);
            }
        }

        return $this;
    }

    /**
     * Add Reply to email address(es) to the email
     *
     * @param   mixed  $replyto  Either a string or array of strings [email address(es)]
     * @param   mixed  $name     Either a string or array of strings [name(s)]
     *
     * @return  JMail  Returns this object for chaining.
     *
     * @since   11.1
     */
    public function addReplyTo($replyto, $name = '') {
        $this->add($replyto, $name, 'AddReplyTo');

        return $this;
    }

    /**
     * Sets message type to HTML
     *
     * @param   boolean  $ishtml  Boolean true or false.
     *
     * @return  JMail  Returns this object for chaining.
     *
     * @since   12.3
     */
    public function isHtml($ishtml = true) {
        parent::IsHTML($ishtml);

        return $this;
    }

    /**
     * Use sendmail for sending the email
     *
     * @param   string  $sendmail  Path to sendmail [optional]
     *
     * @return  boolean  True on success
     *
     * @since   11.1
     */
    public function useSendmail($sendmail = null) {
        $this->Sendmail = $sendmail;

        if (!empty($this->Sendmail)) {
            $this->IsSendmail();

            return true;
        } else {
            $this->IsMail();

            return false;
        }
    }

    /**
     * Use SMTP for sending the email
     *
     * @param   string   $auth    SMTP Authentication [optional]
     * @param   string   $host    SMTP Host [optional]
     * @param   string   $user    SMTP Username [optional]
     * @param   string   $pass    SMTP Password [optional]
     * @param   string   $secure  Use secure methods
     * @param   integer  $port    The SMTP port
     *
     * @return  boolean  True on success
     *
     * @since   11.1
     */
    public function useSMTP($auth = null, $host = null, $user = null, $pass = null, $secure = null, $port = 25) {
        $this->SMTPAuth = $auth;
        $this->Host = $host;
        $this->Username = $user;
        $this->Password = $pass;
        $this->Port = $port;

        if ($secure == 'ssl' || $secure == 'tls') {
            $this->SMTPSecure = $secure;
        }

        if (($this->SMTPAuth !== null && $this->Host !== null && $this->Username !== null && $this->Password !== null) || ($this->SMTPAuth === null && $this->Host !== null)) {
            $this->IsSMTP();

            return true;
        } else {
            $this->IsMail();

            return false;
        }
    }

    /**
     * Function to send an email
     *
     * @param   string   $from         From email address
     * @param   string   $fromName     From name
     * @param   mixed    $recipient    Recipient email address(es)
     * @param   string   $subject      email subject
     * @param   string   $body         Message body
     * @param   boolean  $mode         false = plain text, true = HTML
     * @param   mixed    $cc           CC email address(es)
     * @param   mixed    $bcc          BCC email address(es)
     * @param   mixed    $attachment   Attachment file name(s)
     * @param   mixed    $replyTo      Reply to email address(es)
     * @param   mixed    $replyToName  Reply to name(s)
     *
     * @return  boolean  True on success
     *
     * @since   11.1
     */
    public function sendMail($from, $fromName, $recipient, $subject, $body, $mode = false, $cc = null, $bcc = null, $attachment = null, $replyTo = null, $replyToName = null) {
        $this->setSubject($subject);
        $this->setBody($body);

        // Are we sending the email as HTML?
        if ($mode) {
            $this->IsHTML(true);
        }

        $this->addRecipient($recipient);
        $this->addCC($cc);
        $this->addBCC($bcc);
        $this->addAttachment($attachment);

        // Take care of reply email addresses
        if (is_array($replyTo)) {
            $numReplyTo = count($replyTo);

            for ($i = 0; $i < $numReplyTo; $i++) {
                $this->addReplyTo($replyTo[$i], $replyToName[$i]);
            }
        } elseif (isset($replyTo)) {
            $this->addReplyTo($replyTo, $replyToName);
        }

        // Add sender to replyTo only if no replyTo received
        $autoReplyTo = (empty($this->ReplyTo)) ? true : false;
        $this->setSender(array($from, $fromName, $autoReplyTo));

        return $this->Send();
    }

    /**
     * Sends mail to administrator for approval of a user submission
     *
     * @param   string  $adminName   Name of administrator
     * @param   string  $adminEmail  Email address of administrator
     * @param   string  $email       [NOT USED TODO: Deprecate?]
     * @param   string  $type        Type of item to approve
     * @param   string  $title       Title of item to approve
     * @param   string  $author      Author of item to approve
     * @param   string  $url         A URL to included in the mail
     *
     * @return  boolean  True on success
     *
     * @since   11.1
     */
    public function sendAdminMail($adminName, $adminEmail, $email, $type, $title, $author, $url = null) {
        $subject = JText::sprintf('JLIB_MAIL_USER_SUBMITTED', $type);

        $message = sprintf(JText::_('JLIB_MAIL_MSG_ADMIN'), $adminName, $type, $title, $author, $url, $url, 'administrator', $type);
        $message .= JText::_('JLIB_MAIL_MSG') . "\n";

        $this->addRecipient($adminEmail);
        $this->setSubject($subject);
        $this->setBody($message);

        return $this->Send();
    }

    public function eeMailSend() {
        //Create Email instances and set From, From Name and subject of Email
        $message = new EEsenderMessage($this->From, $this->FromName, $this->Subject);
        //Add Atachments
        $attachments = $this->GetAttachments();
        //$attachment = [path, filename, name, encoding, type, isStringAttachment, attachmnet, 0?]
        
        foreach ($attachments as $attachment) {
            // isStringAttachment
            if ($attachment[5] === false) {
                $message->addAttachment($attachment[0], $attachment[1]);
            }
        }
        
        //Merge BCC with the To recipients, ElasticEmail will send an invidual mail to everyone
        $recipients = array_merge($this->to, $this->bcc);
        foreach ($recipients as $recipient) {
            $message->addRecipient($recipient[0]);
        }
        // Take care of reply email addresses
        if (is_array($this->ReplyTo) && count($this->ReplyTo) > 0) {

            $replyTo = array_shift($this->ReplyTo);
            $message->setReplyTo($replyTo[0], $replyTo[1]);
        } else {

            $message->setReplyTo($this->From, $this->FromName);
        }
        //TODO: Merge with EE template mockup
        // we always send the mail in HTML format
        // so if joomla is sending it in text/plain, then we need to make some modifications to it
        if ($this->ContentType == 'text/plain') {
            $message->body_html = $this->Body;

            $this->Body = nl2br(htmlspecialchars($this->Body));

            // Replace multiple spaces with single spaces
            $this->Body = preg_replace('/\s\s+/', ' ', $this->Body);

            // Replace URLs with <a href...> elements
            $this->Body = $this->makeClickableUrls($this->Body);
        }

        $message->body_html = $this->Body;
        //TODO: Add support channel management
        //$message->channel = chanel_name
        //Set charset for message
        $message->charset = $this->CharSet;
      
        //Note: Encoding was seting by EE data account
        //$message->encodingtype = $this->Encoding
        //Send Message
       
        try {
            $transactionID = $this->eemail->sendMail($message);
        } catch (EEsenderExceptions $exp) {
            //Wrtie error log
            $this->writeToLog($exp->getMessage() . " => " . serialize($message));
            return false;
        }
        //Future: Add option to save all critical email forever in joomla database, end Error not send email 
        return true;
    }

    /**
     * Function that will search plain-text for urls in it and will add the
     * html <a> tag.
     *
     * @param $text
     *
     * @return mixed
     */
    private function makeClickableUrls($value,  $protocols = array('https', 'mail'), array $attributes = array()) {

        
        // Link attributes
        $attr = '';
        foreach ($attributes as $key => $val) {
            $attr = ' ' . $key . '="' . htmlentities($val) . '"';
        }
        
        $links = array();
        
        
        // Extract existing links and tags
        $value = preg_replace_callback('~(<a .*?>.*?</a>|<.*?>)~i', function ($match) use (&$links) { return '<' . array_push($links, $match[1]) . '>'; }, $value);
        
        // Extract text links for each protocol
        foreach ((array)$protocols as $protocol) {
            switch ($protocol) {
                case 'http':
                case 'https':   $value = preg_replace_callback('~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) { if ($match[1]) $protocol = $match[1]; $link = $match[2] ?: $match[3]; return '<' . array_push($links, "<a $attr href=\"$protocol://$link\">$link</a>") . '>'; }, $value); break;
                case 'mail':    $value = preg_replace_callback('~([^\s<]+?@[^\s<]+?\.[^\s<]+)(?<![\.,:])~',
                 function ($match) use (&$links, $attr) { 
                    preg_match('/&lt;([\s\S]*?)&gt;/s', $match[1], $preg_matches);
                    $match[1] = $preg_matches? $preg_matches[1] : $match[1];
                    return '<' . array_push($links, "<a $attr href=\"mailto:{$match[1]}\">{$match[1]}</a>") . '>'; }, $value); break;
                case 'twitter': $value = preg_replace_callback('~(?<!\w)[@#](\w++)~', function ($match) use (&$links, $attr) { return '<' . array_push($links, "<a $attr href=\"https://twitter.com/" . ($match[0][0] == '@' ? '' : 'search/%23') . $match[1]  . "\">{$match[0]}</a>") . '>'; }, $value); break;
                default:        $value = preg_replace_callback('~' . preg_quote($protocol, '~') . '://([^\s<]+?)(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) { return '<' . array_push($links, "<a $attr href=\"$protocol://{$match[1]}\">{$match[1]}</a>") . '>'; }, $value); break;
            }
        }
        
        // Insert all link
        return preg_replace_callback('/<(\d+)>/', function ($match) use (&$links) { return $links[$match[1] - 1]; }, $value);
    }
    

    private function writeToLog($message) {
        JLog::add(JText::sprintf($message), JLog::WARNING, 'com_eesender');
    }

}
