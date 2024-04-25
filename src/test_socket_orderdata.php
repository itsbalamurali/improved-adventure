<?php
include_once('common.php');

$PUBSUB_TECHNIQUE = "SocketCluster";


$iOrderId = isset($_REQUEST["iOrderId"]) ? $_REQUEST["iOrderId"] : '';
$vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
$UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger'; // Passenger, Driver , Company

//$iOrderId = '963';
//$vTimeZone = '';
//$iUserId = '';
//$UserType = '';
      
if ($UserType == "Passenger") { 
    $tblname = "register_user";
    $iMemberId = 'ord.iUserId';
    $UserDetailsArr = getUserCurrencyLanguageDetails($iUserId, $iOrderId);
    $Ratio = $UserDetailsArr['Ratio'];
    $currencySymbol = $UserDetailsArr['currencySymbol'];
    $vLang = $UserDetailsArr['vLang'];
    $NotInStatusCode = "12";
    $fields = "concat(vName,' ',vLastName) as drivername,vImgName AS vImage";
} else if ($UserType == "Driver") {
    $tblname = "register_driver";
    $iMemberId = 'ord.iDriverId';
    $UserDetailsArr = getDriverCurrencyLanguageDetails($iUserId, $iOrderId);
    $Ratio = $UserDetailsArr['Ratio'];
    $currencySymbol = $UserDetailsArr['currencySymbol'];
    $vLang = $UserDetailsArr['vLang'];
    $NotInStatusCode = "12";
    $fields = "concat(vName,' ',vLastName) as drivername,vImage";
} else {
    $tblname = "company";
    $iMemberId = 'ord.iCompanyId';
    $UserDetailsArr = getCompanyCurrencyLanguageDetails($iUserId, $iOrderId);
    $Ratio = $UserDetailsArr['Ratio'];
    $currencySymbol = $UserDetailsArr['currencySymbol'];
    $vLang = $UserDetailsArr['vLang'];
    $NotInStatusCode = "1,2,12";
    $fields = "concat(vName,' ',vLastName) as drivername,vImage";
}

/* $getUserImgData  = $obj->MySQLSelect("SELECT $fields FROM ".$tblname." AS ord WHERE $iMemberId='".$iUserId."'");
  $driverName = $imgaeName = "";
  if(count($getUserImgData) > 0){
  $driverName = $getUserImgData[0]['drivername'];
  $imgaeName = $getUserImgData[0]['vImage'];
  } */
$iServiceId = get_value('orders', 'iServiceId', 'iOrderId', $iOrderId, '', 'true');
$languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
$LBL_ITEMSLBL_ITEMS = $languageLabelsArr['LBL_ITEMSLBL_ITEMS'];
$LBL_ITEMSLBL_ITEM = $languageLabelsArr['LBL_ITEMSLBL_ITEM'];
$LBL_RESTAURANT_TXT = $languageLabelsArr['LBL_RESTAURANT_TXT'];
$LBL_VEHICLE_DRIVER_TXT_FRONT = $languageLabelsArr['LBL_VEHICLE_DRIVER_TXT_FRONT'];
$sql = "SELECT os.vStatus_" . $vLang . " as vStatus,os.vStatus_Track_" . $vLang . " as vStatus_Track,osl.dDate,osl.iStatusCode,ord.iUserId,ord.iCompanyId,ord.iDriverId,ord.iStatusCode as OrderCurrentStatusCode,ord.iUserAddressId,ord.vOrderNo,ord.tOrderRequestDate,ord.fNetTotal,ord.iOrderId FROM order_status_logs as osl LEFT JOIN order_status as os ON osl.iStatusCode = os.iStatusCode LEFT JOIN orders as ord ON osl.iOrderId=ord.iOrderId WHERE osl.iOrderId = '" . $iOrderId . "' AND osl.iStatusCode NOT IN(" . $NotInStatusCode . ") ORDER BY osl.iStatusCode ASC";
$OrderStatus = $obj->MySQLSelect($sql);

$eDisplayDottedLine = "No";
$eDisplayRouteLine = "No";
if (count($OrderStatus) > 0) { 
    $returnArr['Action'] = "1";
    $UserSelectedAddressArr = FetchMemberAddressData($OrderStatus[0]['iUserId'], "Passenger", $OrderStatus[0]['iUserAddressId']);
    $sql = "SELECT concat(vName,' ',vLastName) as drivername,vImage from  register_driver WHERE iDriverId ='" . $OrderStatus[0]['iDriverId'] . "'";
    $driverdetail = $obj->MySQLSelect($sql);
    $drivername = $driverdetail[0]['drivername'];
    $imgaeName = $driverdetail[0]['vImage'];
    if ($drivername == "" || $drivername == NULL) {
        //$drivername = "Delivery Driver";
        $drivername = $LBL_VEHICLE_DRIVER_TXT_FRONT;
    }
    $OrderPickedUpDate = "";
    $CheckOtherStatusCode = "Yes";
    $companyfields = "vCompany,vRestuarantLocation,vRestuarantLocationLat,vRestuarantLocationLong,vCaddress";
    $Data_cab_requestcompany = get_value('company', $companyfields, 'iCompanyId', $OrderStatus[0]['iCompanyId']);
    // echo "<pre>";print_r($Data_cab_requestcompany);
    $serverTimeZone = date_default_timezone_get();
    
    for ($i = 0; $i < count($OrderStatus); $i++) {
        $OrderStatusCode .= $OrderStatus[$i]['iStatusCode'] . ",";
        $dDate = $OrderStatus[$i]['dDate']; 
        //$dDate = converToTz($dDate, $vTimeZone, $serverTimeZone);
        $OrderStatus[$i]['dDate'] = $dDate; 
        $OrderStatus[$i]['driverName'] = $drivername;
        $OrderStatus[$i]['driverImage'] = $imgaeName;
        $iStatusCode = $OrderStatus[0]['OrderCurrentStatusCode'];
        if ($iStatusCode == 1 || $iStatusCode == 2 || $iStatusCode == 8 || $iStatusCode == 8) {
            $eDisplayDottedLine = "Yes";
            $eDisplayRouteLine = "No";
        }
        if ($iStatusCode == 5) {
            $eDisplayDottedLine = "No";
            $eDisplayRouteLine = "Yes";
            $OrderPickedUpDate = $OrderStatus[$i]['dDate'];
        }
        $OrderStatus[$i]['eShowCallImg'] = "No";
        $StatusCodeLogwise = $OrderStatus[$i]['iStatusCode'];
        if ($StatusCodeLogwise == 5) {
            $OrderStatus[$i]['eShowCallImg'] = "Yes";
        }

        $OrderStatus[$i]['vStatus_Track'] = str_replace("#DriverName#", $drivername, $OrderStatus[$i]['vStatus_Track']);
        $OrderStatus[$i]['vStatus_Track'] = str_replace("#STORE#", $LBL_RESTAURANT_TXT, $OrderStatus[$i]['vStatus_Track']);
        $OrderStatus[$i]['vStatus'] = str_replace("#STORE#", $LBL_RESTAURANT_TXT, $OrderStatus[$i]['vStatus']);
        $OrderStatus[$i]['eCompleted'] = "Yes";
        if ($iStatusCode == 8 || $iStatusCode == 9) {
            $CheckOtherStatusCode = "No";
        }
    }
    if ($CheckOtherStatusCode == "Yes" && $UserType == "Passenger") {
        $OrderStatusCode = substr($OrderStatusCode, 0, -1);
        $OrderStatusCode = $OrderStatusCode . ",7,8,9,11,12";
        $sql = "SELECT vStatus_" . $vLang . " as vStatus,vStatus_Track_" . $vLang . " as vStatus_Track,iStatusCode FROM order_status WHERE iStatusCode NOT IN(" . $OrderStatusCode . ") ORDER BY iDisplayOrder ASC";
        $OrderStatusNotExist = $obj->MySQLSelect($sql);
        for ($i = 0; $i < count($OrderStatusNotExist); $i++) {
            $OrderStatusNotExist[$i]['vStatus'] = $OrderStatusNotExist[$i]['vStatus'];
            $OrderStatusNotExist[$i]['vStatus_Track'] = str_replace("#DriverName#", $drivername, $OrderStatusNotExist[$i]['vStatus_Track']);
            $OrderStatusNotExist[$i]['vStatus_Track'] = str_replace("#STORE#", $LBL_RESTAURANT_TXT, $OrderStatusNotExist[$i]['vStatus_Track']);
            $OrderStatusNotExist[$i]['vStatus'] = str_replace("#STORE#", $LBL_RESTAURANT_TXT, $OrderStatusNotExist[$i]['vStatus']);
            $OrderStatusNotExist[$i]['dDate'] = "";
            $OrderStatusNotExist[$i]['iStatusCode'] = $OrderStatusNotExist[$i]['iStatusCode'];
            $OrderStatusNotExist[$i]['iUserId'] = $OrderStatus[0]['iUserId'];
            $OrderStatusNotExist[$i]['iCompanyId'] = $OrderStatus[0]['iCompanyId'];
            $OrderStatusNotExist[$i]['iDriverId'] = $OrderStatus[0]['iDriverId'];
            $OrderStatusNotExist[$i]['OrderCurrentStatusCode'] = $OrderStatus[0]['OrderCurrentStatusCode'];
            $OrderStatusNotExist[$i]['iUserAddressId'] = $OrderStatus[0]['iUserAddressId'];
            $OrderStatusNotExist[$i]['vOrderNo'] = $OrderStatus[0]['vOrderNo'];
            $OrderStatusNotExist[$i]['tOrderRequestDate'] = $OrderStatus[0]['tOrderRequestDate'];
            $OrderStatusNotExist[$i]['fNetTotal'] = $OrderStatus[0]['fNetTotal'];
            $OrderStatusNotExist[$i]['eShowCallImg'] = $OrderStatus[0]['eShowCallImg'];
            $OrderStatusNotExist[$i]['eCompleted'] = "No";
            array_push($OrderStatus, $OrderStatusNotExist[$i]);
        }
    }
    foreach ($OrderStatus as $k => $v) {
        $Data_name['iStatusCode'][$k] = $v['iStatusCode'];
    }
    array_multisort($Data_name['iStatusCode'], SORT_ASC, $OrderStatus); //Added By HJ ON 3-1-2019 For Sort BY iStatusCode

    $returnArr['message'] = $OrderStatus;
    $fNetTotal = $OrderStatus[0]['fNetTotal'];
    $fNetTotal = round($fNetTotal * $Ratio, 2);
    $returnArr['fNetTotal'] = $currencySymbol . " " . formatnum($fNetTotal);
    $returnArr['vOrderNo'] = $OrderStatus[0]['vOrderNo'];
    $TotalOrderItems = getTotalOrderDetailItemsCount($iOrderId);
    $returnArr['TotalOrderItems'] = ($TotalOrderItems > 1) ? $TotalOrderItems . " " . $LBL_ITEMSLBL_ITEMS : $TotalOrderItems . " " . $LBL_ITEMSLBL_ITEM;
    $tOrderRequestDate = $OrderStatus[0]['tOrderRequestDate'];
    //$tOrderRequestDate = converToTz($tOrderRequestDate, $vTimeZone, $serverTimeZone);
    $returnArr['tOrderRequestDate'] = $tOrderRequestDate;
    $returnArr['OrderCurrentStatusCode'] = $OrderStatus[0]['OrderCurrentStatusCode'];
    $returnArr['PassengerLat'] = $UserSelectedAddressArr['vLatitude'];
    $returnArr['PassengerLong'] = $UserSelectedAddressArr['vLongitude'];
    $returnArr['DeliveryAddress'] = $UserSelectedAddressArr['UserAddress'];
    $returnArr['vCompany'] = $Data_cab_requestcompany[0]['vCompany'];
    $returnArr['CompanyLat'] = $Data_cab_requestcompany[0]['vRestuarantLocationLat'];
    $returnArr['CompanyLong'] = $Data_cab_requestcompany[0]['vRestuarantLocationLong'];
    $returnArr['CompanyAddress'] = $Data_cab_requestcompany[0]['vRestuarantLocation'];
    $returnArr['iDriverId'] = $OrderStatus[0]['iDriverId'];
    $returnArr['eDisplayDottedLine'] = $eDisplayDottedLine;
    $returnArr['eDisplayRouteLine'] = $eDisplayRouteLine;
    $returnArr['OrderPickedUpDate'] = $OrderPickedUpDate;
    $returnArr['iServiceId'] = $iServiceId;
    if ($OrderStatus[0]['iDriverId'] > 0) {
        $Data_cab_driverlatlong = get_value('register_driver', 'vLatitude,vLongitude,vCode,vPhone', 'iDriverId', $OrderStatus[0]['iDriverId']);
        $returnArr['DriverLat'] = $Data_cab_driverlatlong[0]['vLatitude'];
        $returnArr['DriverLong'] = $Data_cab_driverlatlong[0]['vLongitude'];
        $returnArr['DriverPhone'] = '+' . $Data_cab_driverlatlong[0]['vCode'] . $Data_cab_driverlatlong[0]['vPhone'];
    } else {
        $returnArr['DriverLat'] = "";
        $returnArr['DriverLong'] = "";
        $returnArr['DriverPhone'] = "";
    }
} else {
    $returnArr['Action'] = "0";
    $returnArr['message'] = "LBL_NO_DATA_AVAIL";
}

$obj->MySQLClose();
echo json_encode($returnArr, JSON_UNESCAPED_UNICODE);
exit;
?>