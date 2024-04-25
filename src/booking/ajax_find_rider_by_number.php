<?php



include_once '../common.php';
if (!empty($_SESSION['sess_iAdminUserId'])) {
} else {
    $AUTH_OBJ->checkMemberAuthentication();
}

$phone = $_REQUEST['phone'] ?? '';
$phoneCode = $_REQUEST['phoneCode'] ?? '';
$vehicleId = $_REQUEST['vehicleId'] ?? '';
if ('' !== $phone) {
    $phonQr = '';
    if ('' !== $phoneCode) {
        $phonQr = " AND vPhoneCode='".$phoneCode."'";
    }
    $sql = "select vName,vLastName,vEmail,iUserId,eStatus from register_user where vPhone = '".$phone."' AND eStatus = 'Active' {$phonQr} LIMIT 1";
    $db_model = $obj->MySQLSelect($sql);
    $cont = '';
    for ($i = 0; $i < count($db_model); ++$i) {
        if ('rider' === $_SESSION['sess_user']) {
            $cont .= $db_model[$i]['vName'].':';
            $cont .= $db_model[$i]['vLastName'].':';
            $cont .= $db_model[$i]['vEmail'].':';
        } else {
            $cont .= clearName(' '.$db_model[$i]['vName']).':';
            $cont .= clearName(' '.$db_model[$i]['vLastName']).':';
            $cont .= clearEmail(' '.$db_model[$i]['vEmail']).':';
        }
        // $cont .= $db_model[$i]['vLastName'].":";
        // $cont .= $db_model[$i]['vEmail'].":";
        $cont .= $db_model[$i]['iUserId'].':';
        $cont .= $db_model[$i]['eStatus'].':';
        $cont .= $db_model[$i]['vEmail'];
    }
    echo $cont;

    exit;
}
if (isset($_REQUEST['vehicleId'])) {
    if ('' !== $vehicleId) {
        $sql = "select iBaseFare,fPricePerKM,fPricePerMin,iMinFare from vehicle_type where iVehicleTypeId = '".$vehicleId."' LIMIT 1";
        $db_model = $obj->MySQLSelect($sql);
        $cont = '';
        for ($i = 0; $i < count($db_model); ++$i) {
            $cont .= $db_model[$i]['iBaseFare'].':';
            $cont .= $db_model[$i]['fPricePerKM'].':';
            $cont .= $db_model[$i]['fPricePerMin'].':';
            $cont .= $db_model[$i]['iMinFare'];
        }
    } else {
        $cont = '';
        $cont .= 0.00;
        $cont .= ':';
        $cont .= 0.00;
        $cont .= ':';
        $cont .= 0.00;
        $cont .= ':';
        $cont .= 0.00;
    }
    echo $cont;

    exit;
}
