<?php
/**
 * @author ElasticEmail
 * @date: 2019-04-10
 *
 * @copyright  Copyright (C) 2010-2019 elasticemail.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die('Restricted access');

class EEsenderSender extends EEsenderRequest {
    private static $postbody, $boundary;
    public function __construct($apikey) {
        parent::__construct($apikey);
        //Other init option;
    }

    /**
     * http://elasticemail.com/api-documentation/send
     * @param EEmail $email
     * @return type
     */
    public function sendMail(EEsenderMessage $email) {
        $email->to = $email->getRecipients();
        if (empty($email->to)) {
            throw new EEsenderExceptions('Error: No recipient');
        }   
        $params = JComponentHelper::getParams('com_eesender');         
       
    
     
     return  $this->send_att($params->get('apikey'), $email->to, $email->from, $email->from_name, $email->subject, $email->body_html, $email->body_txt, $email->reply_to, $email->reply_to_name, $email->charset, $isTransactional = false, $email->lists, $email->attachments);              
                   


    }

   

    public function send_att($apikey = null, $to = null, $from = null, $from_name = null, $subject = null, $body_html = null, $body_text = null, $reply_to = null, $reply_to_name = null, $charset = 'utf-8', $isTransactional = false, $lists = null, $attachments = null )
    {
        $appl = JFactory::getApplication();
        $params = JComponentHelper::getParams('com_eesender');   
        $url = 'https://api.elasticemail.com/v2/email/send';
        $from = $params->get('username');
        try{
        
        $post = array('from' => $params->get('username'),
                      'fromName' => $from_name,
                      'apikey' => $apikey,
                      'subject' => $subject,
                      'bodyHtml' => $body_html,
                      'bodyText' => $body_text,
                      'channel' => 'Joomla Plugin Sender',
                      'to' => $to,
                      'isTransactional' => $isTransactional,
                      'replyTo' => $params->get('username'),
                        'replyToName' => $reply_to_name
                      );
        
        if(!empty($attachments)){
            $paths = array();
            foreach($attachments as $name => $path){
                $paths[] = $path;
                $names[] = $name;
                }

            foreach($paths as $no => $filepath){
                        
                    $filenameonly = explode("\\", $filepath);
                   
                    

                    $fname = $filenameonly[sizeof($filenameonly) - 1];
                    $file_extension = substr($fname, -4);
                    $sanitize_filename = substr_replace(preg_replace("/[^a-zA-Z0-9]/", "_", $names[$no]),"",-4);
                    
                    $some['file_'.$no] = new CurlFile($filepath,'application/octet-stream', $sanitize_filename . $file_extension);
                };

            $post = array_merge($post,$some);
        };

     
        $ch = curl_init();
            
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_SSL_VERIFYPEER => false
        ));
        
        $result=curl_exec($ch);
       


        if(json_decode($result)->success === false){
           
            $appl->enqueueMessage(JText::_('COM_EESENDER_SERVER_RESPONSE') . ": <pre>" . json_decode($result)->error . '</pre>', 'error');
        }
        
       
        
        }
            catch(Exception $ex){
                echo $ex->getMessage();
        }

    }
}
