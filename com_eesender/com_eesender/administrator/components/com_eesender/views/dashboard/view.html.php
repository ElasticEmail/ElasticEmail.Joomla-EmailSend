<?php

/**
 * @author ElasticEmail
 * @date: 2019-04-10
 *
 * @copyright  Copyright (C) 2010-2019 elasticemail.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die('Restricted access');

class eesenderViewDashboard extends JViewLegacy {

    public function display($tpl = null) {
        $appl = JFactory::getApplication();
        $params = JComponentHelper::getParams('com_eesender');
        $apikey = $params->get('apikey');
        
    
        if (!empty($apikey)) {
            try {
                $account = new EEsenderAccount($apikey);
                $user = $account->getDetails();
               
                
                if($user->success == false){
                    throw new Exception("Your account is under review or disabled, contact with Elastic Email support or check if you have valid API key in your component settings.", 500);  
                    $tpl = 'wrong';
                }
               
            } catch (Exception $e) {
                $appl->enqueueMessage(JText::_('COM_EESENDER_SERVER_RESPONSE') . ": <pre>" . $e->getMessage() . '</pre>', 'error');
                $tpl = 'wrong';
            }
        } else {
            $tpl = 'config';
        }
        
        if($user->data->statusformatted == 'UnderReview')
        {
            $tpl = 'wrong';
            $appl->enqueueMessage("<pre>Your account is under review or disabled, contact with Elastic Email support or check if you have valid API key in your component settings.</pre>","error");  
            
            
        }
        $enabled_old = $this->check_for_old_ver();
        if($enabled_old){
        
        foreach($enabled_old as $key => $list){
            
            
            if($list['type'] == 'library'){
                $list['name'] = 'EEmail';
            }
            if($list['type'] == 'component'){
                $list['name'] = 'EEmail Dashboard';
            }
            if($list['type'] == 'plugin'){
                $list['name'] = 'System - EEMailer';
            }
        $appl->enqueueMessage('<pre> You\'ve got still enabled old versions of Elastic Email <b>'.$list['type']. '</b> named: <b> '.$list['name'].' </b>  with ID:  <b>' .$list['extension_id']. '</b>  Please disable it, before you continue</pre>', 'warning');
        }
        $tpl = 'config';
        
        }
        
        $this->sidebar = $this->addToolbar();
        parent::display($tpl);
    }

    private function addToolbar() {
        JToolbarHelper::title(JText::_('COM_EESENDER_SENDER'));
        JToolbarHelper::preferences('com_eesender');
        return false;
    }

    private function check_for_old_ver(){
        $old_enabled =array();
        $db = JFactory::getDbo();
        $prefix = $db->getPrefix();
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('name', 'type', 'element','extension_id','enabled')));
        $query->from($db->quoteName($prefix.'extensions'));
        $query->where($db->quoteName('element') .' LIKE \'%eemail\'');

        $db->setQuery($query);
        
       $result = $db->loadObjectList();
      
       $db_params = json_encode($result, true);
        $res = json_decode($db_params, true);
       foreach($res as $key => $list){
          if($list['enabled'] == 1){
                array_push($old_enabled, $list);  
          }
       }
        return $old_enabled;
    }

}
