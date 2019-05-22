
<?php
/**
 * @author ElasticEmail
 * @date: 2019-04-10
 *
 * @copyright  Copyright (C) 2010-2019 elasticemail.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');
$http = JHttpFactory::getHttp(null, array ("curl", "stream"));
$params = JComponentHelper::getParams('com_eesender');
require_once(JPATH_BASE.'/components/com_eesender/helpers/settings.php');

if(isset($_POST['ee_apikey'])){

test_before_saving();
 
}

JHtml::stylesheet('media/com_eesender/css/bootstrap-grid.min.css');
JHtml::stylesheet('media/com_eesender/css/ees_admin.css');
JHtml::script('media/com_eesender/js/jquery.min.js');
JHtml::script('media/com_eesender/js/ees_scripts.js');
?>

<html><body>
<div id="eewp_plugin" class="row eewp_container" style="margin-right: 0px; margin-left: 0px;">
   
<div class="col-12 col-md-12 col-lg-7 ee_line">
        <div class="ee_header">
            <div class="ee_logo">
            <img src=" <?php echo JUri::root().'/media/com_eesender/img/icon.png'; ?> "></div>
            <div class="ee_pagetitle">
                <h1>General Settings</h1>
            </div>
        </div>
        <h4 class="ee_h4">
            Welcome to Elastic Email Joomla Plugin!<br> From now on, you can send your emails in the fastest and most reliable way!<br>
            Just one quick step and you will be ready to rock your subscribers' inbox.<br><br>
            Fill in the details about the main configuration of Elastic Email connections.        </h4>
        <form method="post" action= "#">
            
            <table class="form-table">
                <tbody>
                    <tr> <th scope="row">Select mailer:</th> <!-- Select mailer -->
                        <td>
                            <div style="margin-bottom:15px;">
                                <label>
                                <input id="radio1" type="radio" name="ee_enable" value="yes" <?php if($params->get('ee_enable') != 'no' || !$params->get('ee_enable')){
                                    echo 'checked="checked"'; } ?>><span>Send all Joomla emails via Elastic Email API.</span>
                                <label></label>
                                </label>
                            </div style="margin-bottom:15px;">
                                <label>
                                <input id="radio2" type="radio" name="ee_enable" value="no" <?php if($params->get('ee_enable') == 'no'){
                                echo 'checked ="checked"' ;
                            } ?>><span>Use the defaults Joomla function to send emails.</span>
                                <label></label>
                                </label>
                        </td>
                        </tr>
                        <tr>
                 <th scope="row">Elastic Email API Key:</th> <!--Elastic Email API Key -->
                            <td>
                            <input type="text" id="title" name="ee_apikey" value="<?php echo $apikey_val; ?>" style="width:280px">
                           
                            </td>
                        </tr>
                 <tr><th scope="row">Email type:</th> <!-- Email Type -->
                        <td>
                            <div style="margin-bottom:15px;">
                            <label>
                                <input  id="radio3" type="radio" name="ee_emailtype" value="marketing" <?php if($params->get('ee_emailtype') != 'transactional' ){
                                    echo 'checked="checked"'; } ?>>
                                <span>Marketing</span><label></label>
                            </label>
                        </div>
                        <label>
                            <input id="radio4" type="radio" name="ee_emailtype" value="transactional" <?php if($params->get('ee_emailtype') == 'transactional'){
                                echo 'checked ="checked"' ;
                            } ?> >
                            <span>Transactional</span><label></label>
                        </label>
                    </td>
                     </tr>
                 </tbody>
                </table>
            <table class="form-table">
                <tbody>
                    <tr valign="top" class="content-table2"> <!-- Connection Test -->
                        <th scope="row">Connection Test:</th>
                        <td> <span class="<?= (empty($error) === true) ? 'ee_success' : 'ee_error' ?>">
                                <?= (empty($error) === true) ? 'Connected' : 'Connection error, check your API key. ' . '<a href="https://elasticemail.com/support/user-interface/settings/smtp-api/" target="_blank">' . 'Read more'. '</a>' ?>
                            </span></td>
                     </tr>
                    <tr valign="top" class="content-table2"> <!--Account status -->
                        <th scope="row">Account status:</th>
                        <td>
                        <?php
                            if (isset($accountstatus)) {
                                if ($accountstatus == 1) {
                                    $accountstatusname = '<span class="ee_account-status-active">' . 'Active' . '</span>';
                                } else {
                                    $accountstatusname = '<span class="ee_account-status-deactive">' . 'Please conect to Elastic Email API or complete the profile' . ' <a href="https://elasticemail.com/account/#/account/profile">' . __('Complete your profile', 'elastic-email-sender') . '</a>' . __(' or connect to Elastic Email API to start using the plugin.', 'elastic-email-sender') . '</span>';
                                }
                                 } else {
                                     $accountstatusname = '<span class="ee_account-status-deactive">' . 'Please conect to Elastic Email API or complete the profile'. ' <a href="https://elasticemail.com/account/#/account/profile">' . __('Complete your profile','elastic-email-sender') . '</a>' . __(' or connect to Elastic Email API to start using the plugin.', 'elastic-email-sender') . '</span>';
                                }
                                echo $accountstatusname;
                        ?>
                        </td>
                     </tr>

                    <tr valign="top" class="content-table2"> <!--Account daily limit -->
                        <th scope="row">Account daily limit:</th>
                            <td>
                                 <?php
                                 if (isset($accountdailysendlimit)) {
                                    echo $accountdailysendlimit;
                                 } else {
                                     echo ' -------';
                                 }
                                    ?>                  
                            </td>
                        </tr>

                    <tr valign="top" class="content-table2"> <!--Email Credits -->
                        <th scope="row">Email Credits:</th>
                        <td>
                        <?php 
                         if (isset($issub) || isset($requiresemailcredits) || isset($emailcredits)) {
                             if ($emailcredits != 0) {
                                if ($issub == false || $requiresemailcredits == false) {
                                echo $emailcredits;
                                    }
                            }
                         } ?> 
                        </td>
                        </tr>                 
                    <tr valign="top" class="content-table2"> <!--Credit status -->
                        <th scope="row">Credit status:</th>
                        <td>
                                <?php
                                if ($statusToSendEmail->data !== NULL) {
                                    if ($statusToSendEmail->data == 1) {
                                        $getaccountabilitytosendemail_single = '<span style="color: red;">' . 'Account doesn\'t have enough credits'. '</span>';
                                    } elseif ($statusToSendEmail->data == 2) {
                                        $getaccountabilitytosendemail_single = '<span style="color: orange;">' . 'Account can send e-mails but only without the attachments'. '</span>';
                                    } elseif ($statusToSendEmail->data == 3) {
                                        $getaccountabilitytosendemail_single = '<span style="color: red;">' . 'Daily Send Limit Exceeded' . '</span>';
                                    } elseif ($statusToSendEmail->data == 4) {
                                        $getaccountabilitytosendemail_single = '<span style="color: green;">' . 'Account is ready to send e-mails' . '</span>';
                                    } else {
                                        $getaccountabilitytosendemail_single = '<span style="color: red;">' . 'Check the account configuration' . '</span>';
                                    }
                                } else {
                                    $getaccountabilitytosendemail_single = '---';
                                }
                                ?>
                                <?php echo '<span>' . $getaccountabilitytosendemail_single . '</span>'; ?>
                        </td>
                        </tr>

                </tbody>
             </table>
            <p class="submit"><input type="submit" name="submit" id="submit" class="ee_button" value="Save Changes"></p>       
         </form>


                <!-- add link -->
        <h4>
            Want to use this plugin in a different language version? <a href="https://support.elasticemail.com/">Let us know or help us translate it!</a>
        </h4>
        <div class="">
            <h4 class="ee_h4footer">
                Share your experience of using Elastic Email Joomla Plugin by <a href="https://support.elasticemail.com">sendig us Your opinion</a> Thanks!            </h4>
            </div>
 </div>
           
           
</body>
<div class='text-center col-lg-5'><?php echo eesenderHelperUtility::marketing(); ?></div>

</html>