<?php
/**
 * @author ElasticEmail
 * @date: 2019-04-10
 *
 * @copyright  Copyright (C) 2010-2019 elasticemail.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die('Restricted access');

class EEsenderMessage {

    /**
     * 
     * @param type $from
     * @param type $fromName
     * @param type $subject
     */
    public function __construct($from = '', $fromName = '', $subject = '') {
        $this->from = $from;
        $this->from_name = $fromName;
        $this->subject = $subject;
    }

    /**
     * Set the From and FromName properties
     * @param string $email
     * @param string $name
     * @param int $auto Also set Reply-To
     * @return boolean
     */
    public function setFrom($email, $name = '', $auto = 1) {
        $this->from = $email;
        $this->from_name = $name;
    }

    public function setReplyTo($email, $name) {
        $this->reply_to = $email;
        $this->reply_to_name = $name;
    }

    /**
     * @param type $reciptien
     * @return boolean
     */
    public function addRecipient($reciptien) {
        if (array_search($reciptien, $this->recipients) === false) {
            array_push($this->recipients, $reciptien);
            return true;
        }
        return false;
    }

    public function removeRecipient($reciptien) {
        $index = array_search($reciptien, $this->recipients);
        if ($index !== false) {
            unset($this->recipients[$index]);
            return true;
        }
        return false;
    }

    public function clearAllRecipient() {
        $this->recipients = array();
    }

    public function getRecipients() {
        return join(';', $this->recipients);
    }

    /**
     * Add Atachments to uploaded list
     * @param type $filePath
     * @param type $fileName
     */
    public function addAttachment($filesPath, $fileName = '') {
        if (empty($fileName)) {
            $array_paths = explode('/', $$filesPath);
            $fileName = array_pop($array_paths);
            
        }
        if (!isset($this->attachments[$fileName])) {
            $this->attachments[$fileName] = realpath($filesPath);
        }
       
    }
   

    public function __toArray() {
        return call_user_func('get_object_vars', $this);
    }

    public $to,
            $from,
            $from_name,
            $subject,
            $body_html,
            $body_txt,
            $reply_to = null,
            $reply_to_name = null,
            $channel = null,
            $charset = null,
            $encodingtype = null,
            $template = null,
            $lists = null,
            $attachments = array(),
            $data_source = null; 
    private $recipients = array();

}
