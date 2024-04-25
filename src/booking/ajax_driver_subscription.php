<?php



// Driversubscription added by SP
include_once '../common.php';

include_once '../include/features/include_driver_subscription.php';
if (!empty($_SESSION['sess_iAdminUserId'])) {
} else {
    $AUTH_OBJ->checkMemberAuthentication();
}

$iDriverId = $_REQUEST['driverId'] ?? '';
$type = $_REQUEST['type'] ?? '';

$cont = '';

if ($MODULES_OBJ->isDriverSubscriptionModuleAvailable()) {
    $returnSubStatus = 0;
    $returnSubStatus = checkDriverSubscribed($iDriverId);
    if (1 === $returnSubStatus) {
        $cont = '1';
    }
    if (2 === $returnSubStatus) {
        $cont = '1';
    }

    //	$curdate = date("Y-m-d H:i:s");
    //	$sql_subscribe = "SELECT count(iDriverSubscriptionPlanId) as cnt FROM driver_subscription_details WHERE iDriverId = $iDriverId AND (eSubscriptionStatus = 'Subscribed' or (eSubscriptionStatus = 'Cancelled' AND tExpiryDate>= '$curdate'))";
    //    $db_subscribe = $obj->MySQLSelect($sql_subscribe);
    //    $subscribeCount = $db_subscribe[0]['cnt'];
    //    if($subscribeCount==0) {
    //        $cont = "1";
    //    }
}
echo $cont;

exit;
