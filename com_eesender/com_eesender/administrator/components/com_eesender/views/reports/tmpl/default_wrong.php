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
        </div>
    </div>
    
</div>