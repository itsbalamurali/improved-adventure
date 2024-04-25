<?php



include_once '../common.php';

$intervalmins = INTERVAL_SECONDS;
$keyword = $_REQUEST['keyword'] ?? '';
$iVehicleTypeId = $_REQUEST['iVehicleTypeId'] ?? '';
$vCountry = $_REQUEST['vCountry'] ?? '';
$AppeType = $_REQUEST['AppeType'] ?? '';
$orderId = $_REQUEST['orderId'] ?? '';
$requestsent = $_REQUEST['requestsent'] ?? '';
$cancelOrderDriver = $_REQUEST['cancelOrderDriver'] ?? '';
$_REQUEST['eSystem'] = 'DeliverAll';
if (!empty($requestsent)) {
    // $where = " iOrderId  = '" . $orderId . "'";
    // $Data_update_orders['iDriverId'] = $driverid;
    // $id = $obj->MySQLQueryPerform("orders", $Data_update_orders, 'update', $where);
    $sql = "SELECT iUserId,iOrderId,iCompanyId FROM orders where iOrderId = {$orderId}";
    $db_records = $obj->MySQLSelect($sql);

    $sql_general = "SELECT iUserId,tSessionId FROM register_user WHERE tSessionId!='' AND vFirebaseDeviceToken!='' ORDER BY iUserId ASC limit 1";
    $db_generalrecords = $obj->MySQLSelect($sql_general);

    $sql_company = 'SELECT iGcmRegId FROM company WHERE iCompanyId = '.$db_records[0]['iCompanyId'];
    $db_company = $obj->MySQLSelect($sql_company);

    $dataArray = [];
    $dataArray['tSessionId'] = $db_generalrecords[0]['tSessionId'];
    $dataArray['iUserId'] = $db_records[0]['iUserId'];
    $dataArray['GeneralMemberId'] = $db_generalrecords[0]['iUserId'];
    $dataArray['vDeviceToken'] = $db_company[0]['iGcmRegId'];
    $dataArray['iOrderId'] = $db_records[0]['iOrderId'];
    $dataArray['eSystem'] = 'DeliverAll';
    echo json_encode($dataArray);

    exit;
}
if (!empty($cancelOrderDriver)) { // it takes list of driver how many driver have cancel order
    $CancelOrderDriver = $obj->MySQLSelect("SELECT DISTINCT(o.iDriverId),CONCAT(d.vName,' ',d.vLastName) as driverName, d.vEmail, CONCAT(d.vCode,' ',d.vPhone) as driverphone FROM order_driver_log o INNER JOIN register_driver d ON o.iDriverId = d.iDriverId WHERE iOrderId = ".$orderId);
    if (!empty($CancelOrderDriver)) {
        $driverList = "<table class='table table-bordered' width='100%' align='center'>";
        $driverList .= '<tr>';
        $driverList .= '<td>'.$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." Name</td><td>E-mail</td><td width='30%'>Phone number</td>";
        $driverList .= '</tr>';
        foreach ($CancelOrderDriver as $key => $value) {
            $driverList .= '<tr>';
            $driverList .= '<td>'.clearName($value['driverName']).'</td><td>'.clearEmail($value['vEmail']).'</td><td>'.clearPhone($value['driverphone']).'</td>';
            $driverList .= '</tr>';
        }
        $driverList .= '</table>';
        $returnData['Action'] = 1;
        $returnData['message'] = $driverList;
        echo json_encode($returnData);

        exit;
    }
    $returnData['Action'] = 0;
    $returnData['message'] = '<h1>'.$langage_lbl_admin['LBL_NO_DRIVERS_FOUND'].'</h1>';
    echo json_encode($returnData);

    exit;
}
if (!empty($orderId)) { // it takes driver list when select manual assign driver.
    $sql = "SELECT iUserId,iOrderId,iCompanyId,iUserAddressId FROM orders where iOrderId = {$orderId}";
    $db_order = $obj->MySQLSelect($sql);

    $Data_cab_requestcompany = $obj->MySQLSelect('SELECT vCompany,vRestuarantLocation,vRestuarantLocationLat,vRestuarantLocationLong,vCaddress,eDriverOption FROM company WHERE iCompanyId = '.$db_order[0]['iCompanyId']);

    $iUserAddressId = $db_order[0]['iUserAddressId'];
    $iUserId = $db_order[0]['iUserId'];
    $UserSelectedAddressArr = FetchMemberAddressData($iUserId, 'Passenger', $iUserAddressId);

    $PickUpAddress = $Data_cab_requestcompany[0]['vRestuarantLocation'];
    $DestAddress = $UserSelectedAddressArr['UserAddress'];
    $PickUpLatitude = $Data_cab_requestcompany[0]['vRestuarantLocationLat'];
    $PickUpLongitude = $Data_cab_requestcompany[0]['vRestuarantLocationLong'];
    $DestLatitude = $UserSelectedAddressArr['vLatitude'];
    $DestLongitude = $UserSelectedAddressArr['vLongitude'];
    $eDriverType = $Data_cab_requestcompany[0]['eDriverOption'];
    $address_data['PickUpAddress'] = $PickUpAddress;
    $address_data['DropOffAddress'] = $DestAddress;
    $address_data['eDriverType'] = $eDriverType;
    $address_data['iCompanyId'] = $db_order[0]['iCompanyId'];
    $address_data['iOrderId'] = $orderId;

    $online_drivers = FetchAvailableDrivers($PickUpLatitude, $PickUpLongitude, $address_data, 'Yes', 'No', 'No', '', $DestLatitude, $DestLongitude, $iUserId);
    $dbDrivers = [];
    if (!empty($online_drivers['DriverList']) && count($online_drivers['DriverList']) > 0) {
        foreach ($online_drivers['DriverList'] as $key => $value) {
            $online_drivers['DriverList'][$key]['FULLNAME'] = $value['vName'].' '.$value['vLastName'];
        }

        $distance = array_column($online_drivers['DriverList'], 'distance');

        array_multisort($distance, SORT_ASC, $online_drivers['DriverList']);
        $dbDrivers = $online_drivers['DriverList'];
        // echo "<pre>"; print_r($dbDrivers); exit;
        $con = '<ul>';
        foreach ($dbDrivers as $key => $value) {
            if ('Available' === $value['vAvailability']) {
                $statusIcon = '../assets/img/green-icon.png';
            } elseif ('Active' === $value['vAvailability']) {
                $statusIcon = '../assets/img/red.png';
            } elseif ('On Going Trip' === $value['vAvailability']) {
                $statusIcon = '../assets/img/yellow.png';
            } elseif ('Arrived' === $value['vAvailability']) {
                $statusIcon = '../assets/img/blue.png';
            } else {
                $statusIcon = '../assets/img/offline-icon.png';
            }
            $con .= '<li onclick="putDriverId('.$value['iDriverId'].');"><input type="radio" name="driverid" value='.$value['iDriverId'].'>'.clearName($value['FULLNAME']).' <b>'.clearPhone($value['vPhone']).'</b></li>';
        }
        $con .= '</ul>';
        $returnArr['Action'] = 1;
        $returnArr['message'] = $con;
        echo json_encode($returnArr);

        exit;
    }

    $returnArr['Action'] = 0;
    $returnArr['message'] = $langage_lbl['LBL_NO_DRIVERS_FOUND'];
    echo json_encode($returnArr);

    exit;
}
