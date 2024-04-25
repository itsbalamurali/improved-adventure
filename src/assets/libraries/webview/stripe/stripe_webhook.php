<?php
include_once('init.php');
include_once('../../../../common.php');
// You can find your endpoint's secret in your webhook settings
$endpoint_secret = 'whsec_OCq1EDZfleCh5bm1LaTqURuqHwcmN8q1';
$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, $endpoint_secret
    );
} catch (\UnexpectedValueException $e) {
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
    
    $data = json_decode($payload, true);
    $payment_id = $data['data']['object']['charge'];
    addOrderOutstandingAmount($payment_id);
    http_response_code(200);
    exit();
}

if ($event->type == "charge.succeeded") {
    $data = array('payload' => $payload, 'sig_header' => $sig_header);
    $data = json_decode($payload, true);

    if (isset($data['data']['object']['metadata']['iOrderId']) && !empty($data['data']['object']['metadata']['iOrderId'])) {
        $tPaymentUserID = $data['data']['object']['payment_intent'];
        $iOrderId = isset($data['data']['object']['metadata']['iOrderId']) ? $data['data']['object']['metadata']['iOrderId'] : '';

        $payment_data = $obj->MySQLSelect("SELECT iPaymentId FROM payments WHERE tPaymentUserID = '$tPaymentUserID' ");

        if(!empty($payment_data) && count($payment_data) > 0) {         
            if(!empty($iOrderId)) {
                $orderData = $obj->MySQLSelect("SELECT * FROM orders WHERE iOrderId = '$iOrderId' ");   

                if($orderData[0]['eProcessed'] == "No") {
                    $where = " tPaymentUserID = '$tPaymentUserID'";
                    $pay_data['iOrderId'] = $data['data']['object']['metadata']['iOrderId'];
                    $id = $obj->MySQLQueryPerform("payments", $pay_data, 'update', $where);

                    $userData = $obj->MySQLSelect("SELECT tSessionId FROM register_user WHERE iUserId = '" . $orderData[0]['iUserId'] . "' ");  

                    $dataArr = array(
                        'GeneralMemberId' => $orderData[0]['iUserId'],
                        'GeneralUserType' => 'Passenger',
                        'tSessionId' => $userData[0]['tSessionId'],
                        'iUserId' => $orderData[0]['iUserId'],
                        'iOrderId' => $iOrderId,
                        'type' => "CaptureCardPaymentOrder",
                        'iServiceId' => $orderData[0]['iServiceId'],
                        'vPayMethod' => "Instant",
                        'eSystem' => "DeliverAll",
                        'tPaymentId' => $tPaymentUserID,
                        'payStatus' => 'succeeded'
                    );
                    callCaptureCardPaymentOrder($dataArr);
                }
            }
        }
    }

    
}

http_response_code(200);
exit();
?>