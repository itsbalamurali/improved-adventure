<?php





include_once 'common.php';
$cpass = $_REQUEST['cpass'] ?? '';

$iCompanyId = $_SESSION['sess_iCompanyId'];
$iDriverId = $_SESSION['sess_iUserId'];
$iUserId = $_SESSION['sess_iUserId'];
$iOrganizationId = $_SESSION['sess_iOrganizationId'];

if ('rider' === $_SESSION['sess_user']) {
    $tbl = 'register_user';
    $where = " WHERE `iUserId` = '".$iUserId."'";
}
if ('driver' === $_SESSION['sess_user']) {
    $tbl = 'register_driver';
    $where = " WHERE `iDriverId` = '".$iDriverId."'";
}
if ('company' === $_SESSION['sess_user']) {
    $tbl = 'company';
    $where = " WHERE `iCompanyId` = '".$iCompanyId."'";
}

if ('organization' === $_SESSION['sess_user']) {
    $tbl = 'organization';
    $where = " WHERE `iOrganizationId` = '".$iOrganizationId."'";
}

$sql = "SELECT vPassword FROM {$tbl} {$where}";
$db_login = $obj->MySQLSelect($sql);

$hash = $db_login[0]['vPassword'];
$checkValid = $AUTH_OBJ->VerifyPassword($cpass, $hash);
echo $checkValid;

exit;
