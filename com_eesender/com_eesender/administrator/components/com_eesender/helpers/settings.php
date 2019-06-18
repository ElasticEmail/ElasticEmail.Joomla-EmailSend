<?php
/**
 * @author ElasticEmail
 * @date: 2019-04-10
 *
 * @copyright  Copyright (C) 2010-2019 elasticemail.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die('Restricted access');
    try {
        
        $accountAPI = new EEsenderAccount($params['apikey']);
        $statusToSendEmailAPI = new EEsenderAccount($params['apikey']);
        $error = null;
        $account = $accountAPI->getDetails();
        
    $statusToSendEmail = $statusToSendEmailAPI->GetAccountAbilityToSendEmail();
    
    } catch (EEsenderExceptions $e) {
       $error = $e->getMessage();
        $account = array();
        $statusToSendEmail = array();
        throw new Exception("Your Account is under revision. Try to contact our support team or check in settings if your API key is valid", 500);
    }

    $accountstatus = '';
    if (isset($account->data->statusnumber)) {
        if ($account->data->statusnumber > 0) {
            $accountstatus = $account->data->statusnumber;
        } else {
            $accountstatus = 'Please conect to Elastic Email API';
        }
    }

    if (isset($account->data->email)) {
        $params->set('ee_accountemail', $account->data->email);
    }

    $accountdailysendlimit = '';
    if (isset($account->data->actualdailysendlimit)) {
        $accountdailysendlimit = $account->data->actualdailysendlimit;
    }

    if (isset($account->data->publicaccountid)) {
        $this->publicid = $account->data->publicaccountid;
        $params->set('ee_publicaccountid', $this->publicid);
    }

    if (isset($account->data->enablecontactfeatures)) {
        $params->set('ee_enablecontactfeatures', $account->data->enablecontactfeatures);
    }

    if (isset($account->data->requiresemailcredits)) {
        $requiresemailcredits = $account->data->requiresemailcredits;
    }

    if (isset($account->data->emailcredits)) {
        $emailcredits = $account->data->emailcredits;
    }

    if (isset($account->data->requiresemailcredits)) {
        $requiresemailcredits = $account->data->requiresemailcredits;
    }

    if (isset($account->data->issub)) {
        $issub = $account->data->issub;
    }

  



$apikey =JComponentHelper::getParams('com_eesender')->get('apikey');
if (empty($apikey) === false) {
 $apikey_val = substr($apikey, 0, 15) . '***************';
}

function test_before_saving(){
    $params = JComponentHelper::getParams('com_eesender');
    $appl = JFactory::getApplication();
    $params->set('ee_emailtype', JFactory::getApplication()->input->getString('ee_emailtype'));
    $params->set('ee_enable', JFactory::getApplication()->input->getString('ee_enable'));
    $unhashed = substr($params->get('apikey'), 0, 15) . '***************';
    $apiKey_login = JFactory::getApplication()->input->getString('ee_apikey');

    if( $apiKey_login == $unhashed)
    {
        $apiKey_login = $params->get('apikey');
    }
  
    try{
    $account = new EEsenderAccount($apiKey_login);
    }catch(EEsenderExceptions $e){
        return $appl->enqueueMessage(JText::_('COM_EESENDER_SERVER_RESPONSE') . ": <pre>" . "None API key" . '</pre>', 'error');
    }
    
    
    $user = $account->getDetails();
    
    if($user->success != 'true'){
       return $appl->enqueueMessage(JText::_('COM_EESENDER_SERVER_RESPONSE') . ": <pre>" . "Wrong API key. Please input correct Elastic Email API key" . '</pre>', 'error');

        
    }
    

    
    
    $db = JFactory::getDbo();
    $prefix = $db->getPrefix();
    $query = $db->getQuery(true);
    $query->select($db->quoteName(array('extension_id', 'name', 'params')));
    $query->from($db->quoteName($prefix.'extensions'));
    $query->where($db->quoteName('name') .' LIKE \'com_eesender\'');

    
    
    $db->setQuery($query);
    $accountAPI = new EEsenderAccount($params['apikey']);
    $account = $accountAPI->getDetails();
    

    $results = $db->loadObjectList();
       $db_params = json_decode($results[0]->params, true); 
       $db_params['apikey'] = $apiKey_login;
       $db_params['ee_enable'] = JFactory::getApplication()->input->getString('ee_enable');
       $db_params['ee_emailtype'] = JFactory::getApplication()->input->getString('ee_emailtype');
       $db_params['username'] =$account->data->email;
        $db_params = json_encode($db_params);
        

    $query = $db->getQuery(true);
    $query = $db->getQuery(true);
    $query->clear();

    $fields = array(
    $db->quoteName('params') . ' = ' . $db->quote($db_params),
    );     
    $conditions = array( 
    $db->quoteName('name') . ' = ' . $db->quote("com_eesender")
    );

    $query->update($db->quoteName($prefix.'extensions'))
      ->set($fields)
      ->where($conditions);

    $db->setQuery($query);


    try
    {
    $db->execute();
    }
    catch (RuntimeException $e)
    {
    $e->getMessage();
    }
    header("Refresh: 0");
    return $appl->enqueueMessage(JText::_('COM_EESENDER_SERVER_RESPONSE') . ": <pre>" . "Options Saved" . '</pre>', 'message');
}

