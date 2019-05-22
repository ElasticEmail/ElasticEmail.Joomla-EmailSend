<?php
/**
 * @author ElasticEmail
 * @date: 2019-04-10
 *
 * @copyright  Copyright (C) 2010-2019 elasticemail.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die('Restricted access');
JHtml::stylesheet('media/com_eesender/css/ees_admin.css');
$params = JComponentHelper::getParams('com_eesender');

function test_before_saving(){
    
    $appl = JFactory::getApplication();
    try{
    $account = new EEsenderAccount($_POST['apikey']);
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
       $db_params['apikey'] = $_POST['apikey'];
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


    header("Refresh: 0");
    return $appl->enqueueMessage(JText::_('COM_EESENDER_SERVER_RESPONSE') . ": <pre>" . "Options Saved" . '</pre>', 'message');
}


if(isset($_POST['apikey'])){
    test_before_saving();
}





?>
<div class="eemail egrid">
    <div class="erow">
        <div class="espan10 eoffset1 text-center">
            <br/><br/>
            <p style="font-weight:bold;font-size:15px">
                <?php 
                if($params->get('apikey')){
                    echo JText::_('COM_EESENDER_OLD_VER');
                    ?></p><?php
                }else{
                echo JText::_('COM_EESENDER_NOCONFIG');
                ?></p>
                <br/>
                <p><?php echo JText::sprintf('COM_EESENDER_GET_API_KEY', 'https://elasticemail.com/account'); ?></p>
                <?php
                }
                ?>
            
            
            <br/>
            <p><?php echo JText::sprintf('COM_EESENDER_SUPPORT', 'https://help.elasticemail.com'); ?></p>
            <p>

            <?php if(!$params->get('apikey')){ ?>

            <form class="config-form" method="POST" action="#">
                <div>
                    <input name="apikey" id='apikey' placeholder="Enter Your APIkey" value="" class="form-control">
                    </div>
                
            <input type="submit" name="submit" value="Register with this API key" class="ee_button">
            </form>
            <?php } ?>
        </div>
    </div>
    <div class="erow">
        <div class="espan8 eoffset2 ">
            <?php echo eesenderHelperUtility::footer(); ?>
        </div>
    </div>
</div>

