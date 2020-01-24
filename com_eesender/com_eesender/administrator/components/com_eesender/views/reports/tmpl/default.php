<?php
/**
 * @author ElasticEmail
 * @date: 2019-04-10
 *
 * @copyright  Copyright (C) 2010-2019 elasticemail.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die('Restricted access');

JHtml::stylesheet('media/com_eesender/css/bootstrap-grid.min.css');
JHtml::stylesheet('media/com_eesender/css/ees_admin.css');
JHtml::script('media/com_eesender/js/chart.min.js');
JHtml::script('media/com_eesender/js/jquery.min.js');
JHtml::script('media/com_eesender/js/ees_scripts.js');

$http = JHttpFactory::getHttp(null, array ("curl", "stream"));
$params = JComponentHelper::getParams('com_eesender');

require_once(JPATH_BASE.'/components/com_eesender/helpers/reports.php');


?>
<html><body>
<div id="eewp_plugin" class="row eewp_container" style="margin-right: 0px; margin-left: 0px;">
    <div class="col-12 col-md-12 col-lg-7 <?php
    if (empty($error) === TRUE) {
        echo 'ee_line';
    }
    ?>">
        <div class="ee_header">
            <div class="ee_logo">
            <img src=" <?php echo JUri::root().'/media/com_eesender/img/icon.png'; ?> "></div>
            
            <?php
            if (isset($_POST['daterange'])) {
                $daterangeselect = JFactory::getApplication()->input->getString('daterange');
                if ($daterangeselect === 'last-mth') $datarangename = ' - last month';
                if ($daterangeselect === 'last-wk') $datarangename = ' - last week';
                if ($daterangeselect === 'last-2wk') $datarangename = ' - last two weeks';
            } else {
                if ((empty($total) === true || $total === 0)) {
                    $datarangename = '';
                } else {
                    $datarangename = ' - last month';
                }
            }
            ?>
            <div class="ee_pagetitle">
                <h1> Reports <?php echo $datarangename; ?></h1>
                </div>
        
        </div>
        <?php

        if ((empty($total) === true || $total === 0)) {
            echo '
                            <div class="empty-chart" style="width:80%;">
                                <img src="/joomla/media/com_eesender/img/assets_images_template-empty.svg"/>
                                <p class="ee_p">No data to display. Send campaign to see results</p>
                            </div>';

        };

        if ((empty($error)) === TRUE && $total > 0) {
            ?>
        
            <div class="ee_select-form-box">
                <form name="form" id="daterange" action="" method="post">
                    Date range:
                    <select id="daterange-select" name="daterange" onchange="this.form.submit()">
                        <option> Select data range</option>
                        <option value="last-mth"> Last month</option>
                        <option value="last-wk"> Last week</option>
                        <option value="last-2wk"> Last two weeks </option>
                    </select>
                </form>
            </div>

            <?php
            if (!empty($info)) {
                echo $info;
            }
            ?>
            <div class="ee_reports-container">
  
                <div class="row">
                    <div class="col-12 col-md-2 text-center" style="padding:0 48px">
                        <p style="background: rgba(102, 163, 163, 0.2);"> Submitted </p>
                        <p><?php if (is_numeric($total)) : echo number_format($total); else: echo $total; endif; ?></p>
                    </div>
                    <div class="col-12 col-md-2 text-center" style="padding:0 48px">
                        <p style="background: rgba(0, 153, 255, 0.2);"> Delivered</p>
                        <p><?php if (is_numeric($delivered)) : echo number_format($delivered); else: echo $delivered; endif; ?></p>
                    </div>
                    <div class="col-12 col-md-2 text-center" style="padding:0 48px">
                        <p style="background: rgba(0, 128, 0, 0.2);"> Opened </p>
                        <p><?php if (is_numeric($opened)) : echo number_format($opened); else: echo $opened; endif; ?></p>
                    </div>
                    <div class="col-12 col-md-2 text-center" style="padding:0 48px">
                        <p style="background: rgba(255, 159, 64, 0.2);"> Clicked </p>
                        <p><?php if (is_numeric($clicked)) : echo number_format($clicked); else: echo $clicked; endif; ?></p>
                    </div>
                    <div class="col-12 col-md-2 text-center" style="padding:0 48px">
                        <p style="background: rgba(255, 162, 0, 0.2);"> Unsubscribed </p>
                        <p><?php if (is_numeric($unsubscribed)) : echo number_format($unsubscribed); else: echo $unsubscribed; endif; ?></p>
                    </div>
                    <div class="col-12 col-md-2 text-center" style="padding:0 48px">
                        <p style="background: rgba(255, 0, 0, 0.2);"> Bounced </p>
                        <p><?php if (is_numeric($bounced)) : echo number_format($bounced); else: echo $bounced; endif; ?></p>
                    </div>
                </div>


                <div class="ee_reports-list">
                    <div id="canvas-holder" style="width:80%;">
                        <canvas id="chart-area" />
                    </div>
                    <script>

                        var config = {
                            type: 'doughnut',
                            data: {
                                labels: [" Delivered ", " Opened ", " Clicked ", " Unsubscribed ", " Bounced "],
                                datasets: [{
                                        label: '# of Votes',
                                        data: [
                                            <?php if (is_numeric($delivered)) : echo $delivered; else: echo 100000; endif; ?>,
                                            <?php if (is_numeric($opened)) : echo $opened; else: echo 85000; endif; ?>,                                                 
                                            <?php if (is_numeric($clicked)) : echo $clicked; else: echo 95000; endif; ?>,   
                                            <?php if (is_numeric($unsubscribed)) : echo $unsubscribed; else: echo 4000; endif; ?>,
                                            <?php if (is_numeric($bounced)) : echo $bounced; else: echo 4000; endif; ?>],
                                        backgroundColor: ['rgba(0, 153, 255, 0.4)','rgba(0, 128, 0, 0.4)','rgba(255, 159, 64, 0.4)','rgba(255, 162, 0, 0.4)','rgba(255, 0, 0, 0.4)'],
                                        borderColor: ['rgba(241, 241, 241, 1)','rgba(241, 241, 241, 1)','rgba(241, 241, 241, 1)','rgba(241, 241, 241, 1)','rgba(241, 241, 241, 1)'],
                                        borderWidth: 1.5}]},
                            options: { responsive: true }
                        };
                        window.onload = function () {
                            var ctx = document.getElementById("chart-area").getContext("2d");
                            window.myPie = new Chart(ctx, config);
                        };
                    </script>
                </div>
            </div>
            <div class="ee_footer">
                <h4 class="ee_h4footer">
                    Share your experience of using Elastic Email Joomla Plugin by <a href="#"> rating us here.</a> Thanks!
                </h4>
            </div>
        <?php }?>
    </div> 
        
        

        </body>
        <div class='text-center col-lg-5'><?php echo eesenderHelperUtility::marketing(); ?></div>
        
        </html>