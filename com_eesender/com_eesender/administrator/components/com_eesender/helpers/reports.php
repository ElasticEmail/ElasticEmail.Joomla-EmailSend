<?php


if (isset($_POST['daterange'])) {
    $daterangeselect = $_POST['daterange'];
    if ($daterangeselect === 'last-mth') {
        $from = date('c', strtotime('-30 days'));
        $to = date('c');
    }
    if ($daterangeselect === 'last-wk') {
        $from = date('c', strtotime('-7 days'));
        $to = date('c');
    }
    if ($daterangeselect === 'last-2wk') {
        $from = date('c', strtotime('-14 days'));
        $to = date('c');
    }
} else {
    $from = date('c', strtotime('-30 days'));
    $to = date('c');
}

$channelName = null;
$interval = null;
$transactionID = null;

try {
    $LogAPI = new EEsenderReports($params->get('apikey'), $params->get('username'));
    $error = null;
    $LogAPI_json = $LogAPI->Summary($from, $to, $channelName, $interval, $transactionID);      
    $total = $LogAPI_json->data->logstatussummary->emailtotal; 
    $delivered = $LogAPI_json->data->logstatussummary->delivered;
    $opened = $LogAPI_json->data->logstatussummary->opened;
    $bounced = $LogAPI_json->data->logstatussummary->bounced;
    $clicked = $LogAPI_json->data->logstatussummary->clicked;
    $unsubscribed = $LogAPI_json->data->logstatussummary->unsubscribed;

}

catch (EEsenderException $e) {
    $error = $e->getMessage();
    $LogList = array();
}


