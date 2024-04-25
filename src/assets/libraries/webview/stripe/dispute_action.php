<?php
// include_once('init.php');
include_once('../../../../common.php');
// 	ini_set('display_errors', 1);
// error_reporting(E_ALL);
// You can find your endpoint's secret in your webhook settings
/*
include_once('stripe/stripe-php/init.php');
// You can find your endpoint's secret in your webhook settings
$endpoint_secret = 'whsec_OCq1EDZfleCh5bm1LaTqURuqHwcmN8q1';

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, $endpoint_secret
    );
} catch(\UnexpectedValueException $e) {
    // Invalid payload
    http_response_code(400);
    exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
    // Invalid signature
    http_response_code(400);
    exit();
}

if ($event->type == "charge.dispute.created") {
    // $intent = $event->data->object;
    $data = array('payload' => $payload, 'sig_header' => $sig_header);
    mail('hemant.verma@v3cube.in', "Dispute Created", json_encode($data));
    http_response_code(200);
    exit();
}
*/

$payload = $_REQUEST['payload'];
$payload = stripslashes(str_replace("\\n", "", $payload));
$data = json_decode($payload, true);
$payment_id = $data['data']['object']['charge'];
addOrderOutstandingAmount($payment_id);
