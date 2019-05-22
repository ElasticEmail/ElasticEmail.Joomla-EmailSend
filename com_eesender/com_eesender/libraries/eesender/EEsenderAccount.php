<?php
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
