<?php



include_once '../common.php';
if (!empty($_SESSION['sess_iAdminUserId'])) {
} else {
    $AUTH_OBJ->checkMemberAuthentication();
}

$iDriverId = $_REQUEST['driverId'] ?? '';

$sql = "SELECT iCompanyId FROM `register_driver` WHERE iDriverId ='".$iDriverId."'";
$db_companydata = $obj->MySQLSelect($sql);
$iCompanyId = $db_companydata[0]['iCompanyId'];

echo $iCompanyId;

exit;
