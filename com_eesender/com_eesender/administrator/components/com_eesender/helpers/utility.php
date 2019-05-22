<?php

/**
 * @author ElasticEmail
 * @date: 2019-04-10
 *
 * @copyright  Copyright (C) 2010-2019 elasticemail.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die('Restricted access');

class eesenderHelperUtility {

    /**
     * @return string
     */
    public static function footer() {
        $output = '<div class="efooter efontsmall">
           You are currently using: Elastic Email Sender ' . EEMAIL . ' v and Elastic Email libraries ' . EESENDERLIBRARIES . ' v<br/>
            Copyright ©2013-' . date('Y') . ' <a href="https://elasticemail.com">ElasticEmail</a>
        </div>';

        return $output;
    }

    public static function marketing(){
        $output = '<div class="col-12 col-md-12 col-lg-12 ee_marketing">
        <h2 class="ee_h2"> Let us help you send better emails! </h2>
        <h4 class="ee_footertext">
            If you are new to Elastic Email, feel free to visit our <a href="https://elasticemail.com">website</a> and find out how our comprehensive set of tools will help you reach your goals or get premium email marketing tools at a fraction of what you\'re paying now!
        </h4>
        <hr>
        <h4 class="ee_h4"> If you already use Elastic Email to send your emails, you can subscribe to our monthly updates to start receiving the latest email news, tips, best practices and more.</h4>
     
        <div id="ee-success" class="col-12 hide ee-form_success">
            <div class="col-12">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 13.229 13.229">
                    <path d="M6.615 0A6.606 6.606 0 0 0 0 6.615a6.606 6.606 0 0 0 6.615 6.614 6.606 6.606 0 0 0 6.614-6.614A6.606 6.606 0 0 0 6.615 0zm-.993 10.12L2.25 6.725l1.102-1.08 2.271 2.292 4.256-4.255 1.102 1.103z"
                          fill="#1b4"/>
                </svg>
            </div>
            <p style="margin-top: 10px; margin-bottom: 5px; font-weight: bold;"> Thank you for subscribing to our newsletter!</p>
            <p style="margin-top: 5px; margin-bottom: 0px;"> You will start receiving our email marketing newsletter, as soon as you confirm your subscription.</p>
            </div>
    
        <br/>
        <hr>
        <br/>
        <h2 class="ee_h2"> How we can help you?</h2>
        <h4 class="ee_h4"> If you would like to boost your email marketing campaigns or improve your email delivery, check out our helpful guides to get you started!</h4>
        <ul style="padding-left: 40px;">
            <li type="circle"><a href="https://elasticemail.com/resources/">Guides and resources</a></li>
            <li type="circle"><a href="https://elasticemail.com/resources/api//">Looking for code? Check our API</a></li>
            <li type="circle"><a href="https://elasticemail.com/contact/">Want to talk with a live person? Contact us</a></li>
        </ul>
        <br/>
        <h4 class="ee_h4">Remember that in case of any other questions or feedback, you can always contact our friendly <a href="http://elasticemail.com/help"> Support Team. </a></h4>
    
       
    </div>';

        return $output;
    }
}
