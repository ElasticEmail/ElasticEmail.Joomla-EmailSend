<?php

/**
 * @author ElasticEmail
 * @date: 2019-04-10
 *
 * @copyright  Copyright (C) 2010-2019 elasticemail.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class PlgSystemElasticEmail
 *
 * @since  0.9.0
 */
class PlgSystemEEsender extends JPlugin {

    /**
     * Here we will override the JMail class
     *
     * @return bool
     */
    public function onAfterInitialise() {
        $params = JComponentHelper::getParams('com_eesender');
        $apikey = $params->get('apikey');
       
        $lang = JFactory::getLanguage();
        $lang->load('plg_system_eesender.sys');
        if (!file_exists(JPATH_LIBRARIES . '/eesender/include.php')) {
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_EEMAIL_MISSING_LIBRARY'), 'warring');
            return false;
        }
        if (empty($apikey)){
            JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_EEMAIL_NOCONFIG'), 'error');
            return false;
        }
        $path = JPATH_ROOT . '/plugins/system/eesender/mailer/mail.php';
        JLoader::register('JMail', $path);
        JLoader::load('JMail');
        return true;
    }

}
