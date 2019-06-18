<?php
/**
 * @author ElasticEmail
 * @date: 2019-04-10
 *
 * @copyright  Copyright (C) 2010-2019 elasticemail.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die('Restricted access');

class EEsenderAccount extends EEsenderRequest {

    public function __construct($apikey) {
        parent::__construct($apikey);
        //Other init option;
    }
    
    public function getDetails(){
        $rs_xml = $this->request('/account/load');
        return $this->parseXML($rs_xml);
    }
    public function GetAccountAbilityToSendEmail() {
        $rs_xml = $this->request('account/getaccountabilitytosendemail');
        return $this->parseXML($rs_xml);
    }

   
}
