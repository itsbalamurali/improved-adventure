<?php



// Driversubscription added by SP
include_once '../common.php';

$iDriverId = $_REQUEST['driverId'] ?? '';
$type = $_REQUEST['type'] ?? '';

$cont = '';

$DRIVER_SUBSCRIPTION_ENABLE = $CONFIG_OBJ->getConfigurations('configurations', 'DRIVER_SUBSCRIPTION_ENABLE');
if ('Yes' === $DRIVER_SUBSCRIPTION_ENABLE) {
    $sql_subscribe = "SELECT count(iDriverSubscriptionPlanId) as cnt FROM driver_subscription_details WHERE iDriverId = {$iDriverId} AND eSubscriptionStatus = 'Subscribed'";
    $db_subscribe = $obj->MySQLSelect($sql_subscribe);
    $subscribeCount = $db_subscribe[0]['cnt'];
    if (0 === $subscribeCount) {
        $cont = '1';
    }
}

echo $cont;

exit;
// Driversubscription added by SP
