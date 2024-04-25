<?php



// added by SP for popup showing on block driver start
include_once '../common.php';
if (!empty($_SESSION['sess_iAdminUserId'])) {
} else {
    $AUTH_OBJ->checkMemberAuthentication();
}

$iDriverId = $_REQUEST['driverId'] ?? '';
$eIsBlocked = 'No';

if ('SHARK' === strtoupper(PACKAGE_TYPE)) {
    include_once '../include/include_webservice_sharkfeatures.php';
    $BlockData = getBlockData('Driver', $iDriverId);
    if (!empty($BlockData) || '' !== $BlockData) {
        $eIsBlocked = 'Yes';
    }
}
// if($PACKAGE_TYPE == 'SHARK' && ($APP_TYPE == "Ride-Delivery" || $APP_TYPE == "Delivery" || $APP_TYPE == "Ride-Delivery-UberX")) {
//    $sql_blocked = "SELECT eIsBlocked FROM `register_driver` WHERE iDriverId ='" . $iDriverId . "'";
//    $db_blocked = $obj->MySQLSelect($sql_blocked);
//    $eIsBlocked = $db_blocked[0]['eIsBlocked'];
// }
echo $eIsBlocked;

exit;
// added by SP for popup showing on block driver start
