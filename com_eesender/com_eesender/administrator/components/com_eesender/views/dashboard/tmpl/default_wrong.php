<?php
/**
 * @author ElasticEmail
 * @date: 2019-04-10
 *
 * @copyright  Copyright (C) 2010-2019 elasticemail.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die('Restricted access');
JHtml::stylesheet('media/COM_EESENDER/css/eedashboard.css');


function test_before_saving(){
    
    $appl = JFactory::getApplication();
    try{
    $account = new EEsenderAccount(JFactory::getApplication()->input->getString('apikey'));
    $user = $account->getDetails();
    }
    catch(EEsenderExceptions $e){
        return $appl->enqueueMessage(JText::_('COM_EESENDER_SERVER_RESPONSE') . ": <pre>" . "Wrong API key. Try Again." . '</pre>', 'error');
    }

    if($user->success != 'true'){
       return $appl->enqueueMessage(JText::_('COM_EESENDER_SERVER_RESPONSE') . ": <pre>" . "Server not responding. Please try again with correct API key." . '</pre>', 'error');

        
    }

    
    
    $db = JFactory::getDbo();
    $prefix = $db->getPrefix();
    $query = $db->getQuery(true);
    $query->select($db->quoteName(array('extension_id', 'name', 'params')));
    $query->from($db->quoteName($prefix.'extensions'));
    $query->where($db->quoteName('name') .' LIKE \'com_eesender\'');

    
    $db->setQuery($query);

    
    $results = $db->loadObjectList();
       $db_params = json_decode($results[0]->params, true); 
       $db_params['apikey'] = JFactory::getApplication()->input->getString('apikey');
       $db_params['username'] = $user->data->username;
       $db_params['ee_enable'] = 'yes';
    
        $db_params = json_encode($db_params);
    

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

    $query->clear();
    $db = JFactory::getDbo();
    $prefix = $db->getPrefix();
    $query = $db->getQuery(true);
    $fields = array(
        $db->quoteName('enabled') . ' = ' . $db->quote('1'),
        );     
        $conditions = array( 
        $db->quoteName('name') . ' = ' . $db->quote("plg_system_eesender")
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

    $url = 'https://api.elasticemail.com/v2/contact/add';
    $post = array(
                  'publicAccountID' => 'd0bcb758-a55c-44bc-927c-34f48d5db864',
                  'email' => json_decode($db_params)->username,
                  'publicListID' => '8e85d689-69ff-4486-9374-f50d611cb4b6',
                  'firstName' => 'A',
                  'lastName' => '',
                );

 
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
    
    header("Refresh: 0");
    return $appl->enqueueMessage(JText::_('COM_EESENDER_SERVER_RESPONSE') . ": <pre>" . "Options Saved" . '</pre>', 'message');
}


if(isset($_POST['apikey'])){
    test_before_saving();
}



?>
<div class="eemail egrid">
    <div class="erow">
        <div class="espan8 eoffset2 text-center" style="font-size:1.2em">
            <h1 style="font-weight: bold; color:#ff0000;font-size:2em"><?php echo JText::_('COM_EESENDER_NOCONNECTTED')?></h1>
            <p><?php echo JText::sprintf('COM_EESENDER_ERROR_MSG', 'supportteam@elasticemail.com')?></p>
            <br/>
            <p><?php echo JText::_("COM_EESENDER_ERROR_MSG_SENDER_OFF") ?></p>
            <br/>
            <br/>
            <br/>
            <form class="config-form" method="POST" action="#">
                <div>
                    <input name="apikey" id='apikey' placeholder="Enter Your APIkey" value="" class="form-control">
                    </div>
                
            <input type="submit" name="submit" value="Register with this API key" class="ee_button">
            </form>
        </div>
    </div>
    
</div>