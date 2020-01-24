
<?php
/**
 * @author ElasticEmail
 * @date: 2019-04-10
 *
 * @copyright  Copyright (C) 2010-2019 elasticemail.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');
$doc = JFactory::getDocument();
$http = JHttpFactory::getHttp(null, array ("curl", "stream"));
$params = JComponentHelper::getParams('com_eesender');

$doc -> addScriptOptions ( 'apikey', $params->get('apikey'));
$doc -> addScriptOptions ( 'username', $params->get('username'));

JHtml::stylesheet('media/com_eesender/css/bootstrap-grid.min.css');
JHtml::stylesheet('media/com_eesender/css/ees_admin.css');
JHtml::script('media/com_eesender/js/jquery.min.js');
JHtml::script('media/com_eesender/js/ees_scripts.js');
JHtml::script('media/com_eesender/js/ees_test.js');

?>

<html><body>
<div id="eewp_plugin" class="row eewp_container" style="margin-right: 0px; margin-left: 0px;">
   
<div class="col-12 col-md-12 col-lg-7 ee_line">
<div class="ee_header">
                <div class="ee_pagetitle">
                    <h1>Send Test</h1>
                    <p class="ee_p" style="max-width:60%; line-height: 21px; margin-top:46px; margin-bottom: 36px"> 
                        Sending this testing email will provide You with the necessary information about the ability to send emails from your account as well as email and contact status.
                        The email provided by You will be added to your All Contacts list, then the testing message will be send to this contact.
                        Be aware that sending this testing messages will have impact on your credits.
                    </p> 
                </div>
               
            </div>

            <div class="ee_send-test-container">

                <div class="form-group">
                    <input type="text" class="form-control" id="name" placeholder="Enter name">
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" id="emailAddress" placeholder="Enter email">
                    <p class="error-email test-error" id="invalid_email"></p>
                </div>
                <div class="form-group">
                    <textarea class="form-control" id="textArea" rows="3" placeholder="Enter content"></textarea>
                </div>

                <div class="form-group test-button-box">
                    <button id="sendTest" class="ee_button-test">Submit</button>
                </div>

            </div>
            <div class="ee_send-test-info">
                <p class="ee_p" id="send-test-log"></p>
                <p class="ee_p" id="test-status-error-msg"></p>
                <p class="ee_p" id="test-status"></p>
                <p class="ee_p" id="contact-test-status"></p>
                <div id="loader" class="loader hide"></div>
            </div>
</div>

           
           
</body>
<div class='text-center col-lg-5'><?php echo eesenderHelperUtility::marketing(); ?></div>

</html>
<script>
</script>