<?php
/**
 * @author ElasticEmail
 * @date: 2019-04-10
 *
 * @copyright  Copyright (C) 2010-2019 elasticemail.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die('Restricted access');
class EEsenderRequest {

    public $returnArray = false;
    private $api_key = null;
    private static $host = 'https://api.elasticemail.com/v2/';
           
    public function __construct($apikey) {
        if (!$apikey) {
            throw new EEsenderExceptions('You must provide a ElasticEmail API key');
        }
        $this->api_key = $apikey;
        
    }
    /**
     * 
     * @param type $target
     * @param array $data
     * @param type $method
     * @return type
     * @throws EEsenderExceptions
     */
    public function request($target, array $data = array(), $method = "post") {
        if (!isset($data['noauth'])) {
            $data['api_key'] = $this->api_key;
        
        }
        $url = self::$host . $target;
        $http = JHTTPFactory::getHttp();
        try{
        $query = $http->post($url,$data);
        }
        catch(Exception $e){
            $params = JComponentHelper::getParams('com_eesender');
            $params->set('ee_error', 'true');
            
           throw new Exception("API not responding, try again later", 500);  
        }
        
        $params = JComponentHelper::getParams('com_eesender');
        $params->set('ee_error', 'false');
        return $query->body;
    }
    /**
     * 
     * @param type $xmlstring
     * @return type
     */
    
    public function parseXML($xmlstring) {
        
        $array = json_decode($xmlstring, $this->returnArray);
        return ($array);
    }
    /**
     * Convert Local date time to Server (UTC) date time
     * @param type $datetime
     * @param type $format
     * @return type
     */
    public function localToUTC($datetime, $format = 'Y-m-d h:i:s A') {
        $datetime_obj = new DateTime($datetime);
        $datetime_obj->setTimezone(new DateTimeZone('UTC'));
        return $datetime_obj->format($format);
    }

}
