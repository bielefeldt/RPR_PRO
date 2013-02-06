<?php
//http://rprv2beta/campaign_monitor/add/list.php?api_k=c3e7b2524fd54e9238fd6a0a097c0aa7&c_id=b17e9d6f9f3730b986f6e8828c98637e&title=TEST

if(isset($_GET["api_k"])) { $api_k = $_GET["api_k"]; }
if(isset($_GET["title"])) { $list_title = $_GET["title"]; }
if(isset($_GET["c_id"])) { $list_client_id = $_GET["c_id"]; }
require_once '../csrest_lists.php';

$wrap = new CS_REST_Lists(NULL, $api_k);

$result = $wrap->create($list_client_id, array(
    'Title' => $list_title,
    'UnsubscribePage' => $list_title.' unsubscribe page',
    'ConfirmedOptIn' => true,
    'ConfirmationSuccessPage' => $list_title.' confirmation success page',
    'UnsubscribeSetting' => CS_REST_LIST_UNSUBSCRIBE_SETTING_ALL_CLIENT_LISTS
));

echo "Result of POST /api/v3/lists/{clientID}\n<br />";
if($result->was_successful()) {
    echo "Created with ID\n<br />".$result->response;
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
    echo '</pre>';
}