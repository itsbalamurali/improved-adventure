<?php



include_once '../common.php';
if (!empty($_SESSION['sess_iAdminUserId'])) {
}

// $AUTH_OBJ->checkMemberAuthentication();

$vCountryCode = $_REQUEST['vCountryCode'] ?? '';
if ('' !== $vCountryCode) {
    $sql = "SELECT eEnableToll,iCountryId FROM  `country` WHERE vCountryCode = '".$vCountryCode."'";
    $data = $obj->MySQLSelect($sql);
    echo $data[0]['eEnableToll'];

    exit;
}
