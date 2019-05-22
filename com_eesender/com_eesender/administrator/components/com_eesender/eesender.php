<?php

/**
 * @author ElasticEmail
 * @date: 2019-04-10
 *
 * @copyright  Copyright (C) 2010-2019 elasticemail.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Access check. 
if (!JFactory::getUser()->authorise('core.manage', 'com_eesender')) {
    throw new Exception(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// import joomla controller library
jimport('joomla.application.component.controller');
//Loaded Helpers
JLoader::discover('eesenderHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/');
//Include ElasticEmail Libiary
if (file_exists(JPATH_LIBRARIES . '/eesender/include.php')) {
    require_once JPATH_LIBRARIES . '/eesender/include.php';
} else {
    throw new Exception(JText::_('PLG_SYSTEM_EEMAIL_MISSING_LIBRARY'), 500);
}

// Let us load the necessary langs
//CompojoomLanguage::load('com_eesender', JPATH_ADMINISTRATOR);

$input = JFactory::getApplication()->input;

// Get an instance of the controller prefixed by eesender
$controller = JControllerLegacy::getInstance('eesender');

// Get the task and  Perform the Request task
$controller->execute($input->getCmd('task'));

// Redirect if set by the controller
$controller->redirect();



