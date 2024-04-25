<?php
include_once '../common.php';

include_once '../include/features/include_auto_credit_driver.php';
$default_lang = $LANG_OBJ->FetchSystemDefaultLang();
$order_type = $_REQUEST['type'] ?? '';
$script = 'processing' === $order_type ? 'Processing Orders' : 'All Orders';
$eSystem = " AND eSystem = 'DeliverAll'";
if ('processing' === $order_type && !$userObj->hasPermission('view-processing-orders')) {
    $userObj->redirect();
} elseif ('processing' !== $order_type && !$userObj->hasPermission('view-all-orders')) {
    $userObj->redirect();
}
$os_ssql = '';
if (!$MODULES_OBJ->isEnableAnywhereDeliveryFeature()) {
    $os_ssql .= " AND eBuyAnyService = 'No'";
}
if (!$MODULES_OBJ->isTakeAwayEnable()) {
    $os_ssql .= " AND eTakeaway != 'Yes'";
}
$orderStatus = $obj->MySQLSelect("select iOrderStatusId,vStatus,iStatusCode from order_status WHERE 1 = 1 {$os_ssql} GROUP BY iStatusCode");
$processing_status_array = ['1', '2', '4', '5'];
$all_status_array = ['1', '2', '4', '5', '6', '7', '8', '9', '11', '12'];
if ($MODULES_OBJ->isEnableAnywhereDeliveryFeature()) {
    $processing_status_array = ['1', '2', '4', '5', '13', '14'];
    $all_status_array = ['1', '2', '4', '5', '6', '7', '8', '9', '11', '12', '13', '14'];
}
if (isset($_REQUEST['iStatusCode']) && '' !== $_REQUEST['iStatusCode']) {
    $all_status_array = [$_REQUEST['iStatusCode']];
}
if ('processing' === $order_type) {
    $iStatusCode = '('.implode(',', $processing_status_array).')';
} else {
    $iStatusCode = '('.implode(',', $all_status_array).')';
    $langage_lbl_admin['LBL_PROCESSING_ORDERS'] = $langage_lbl_admin['LBL_ALL_ORDER'];
}
// Start Sorting
$sortby = $_REQUEST['sortby'] ?? 0;
$order = $_REQUEST['order'] ?? '';
$promocode = $_REQUEST['promocode'] ?? '';
$ord = ' ORDER BY o.iOrderId DESC';
if (1 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY o.tOrderRequestDate ASC';
    } else {
        $ord = ' ORDER BY o.tOrderRequestDate DESC';
    }
}
if (2 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY riderName ASC';
    } else {
        $ord = ' ORDER BY riderName DESC';
    }
}
if (3 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY c.vCompany ASC';
    } else {
        $ord = ' ORDER BY c.vCompany DESC';
    }
}
if (4 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY driverName ASC';
    } else {
        $ord = ' ORDER BY driverName DESC';
    }
}
// End Sorting
// Start Search Parameters
$ssql = '';
$action = $_REQUEST['action'] ?? '';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$searchStore = $_REQUEST['searchStore'] ?? '';
$searchDriver = $_REQUEST['searchDriver'] ?? '';
$searchUser = $_REQUEST['searchUser'] ?? '';
$searchServiceType = $_REQUEST['searchServiceType'] ?? '';
$searchOrderNo = $_REQUEST['searchOrderNo'] ?? '';
$startDate = $_REQUEST['startDate'] ?? '';
$endDate = $_REQUEST['endDate'] ?? '';
$vStatus = $_REQUEST['vStatus'] ?? '';
$searchOrderStatus = $_REQUEST['searchOrderStatus'] ?? '';
if (isset($_REQUEST['iStatusCode']) && '' !== $_REQUEST['iStatusCode']) {
    $searchOrderStatus = $_REQUEST['iStatusCode'];
}
if ('' !== $startDate) {
    $ssql .= " AND Date(o.tOrderRequestDate) >='".$startDate."'";
}
if ('' !== $endDate) {
    $ssql .= " AND Date(o.tOrderRequestDate) <='".$endDate."'";
}
if ('' !== $searchOrderNo) {
    $ssql .= " AND o.vOrderNo ='".$searchOrderNo."'";
}
if ('' !== $searchStore) {
    $ssql .= " AND c.iCompanyId ='".$searchStore."'";
}
if ('' !== $searchDriver) {
    $ssql .= " AND d.iDriverId ='".$searchDriver."'";
}
if ('' !== $searchUser) {
    $ssql .= " AND o.iUserId ='".$searchUser."'";
}
if ('' !== $searchServiceType && !in_array($searchServiceType, ['Genie', 'Runner', 'Anywhere'], true)) {
    $ssql .= " AND sc.iServiceId ='".$searchServiceType."' AND o.eBuyAnyService ='No'";
}
if ('Genie' === $searchServiceType) {
    $ssql .= " AND o.eBuyAnyService ='Yes' AND o.eForPickDropGenie = 'No' ";
}
if ('Runner' === $searchServiceType) {
    $ssql .= " AND o.eBuyAnyService ='Yes' AND o.eForPickDropGenie = 'Yes' ";
}
if ('' !== $searchOrderStatus) {
    $ssql .= " AND o.iStatusCode ='".$searchOrderStatus."'";
}
$trp_ssql = '';
if (SITE_TYPE === 'Demo') {
    $trp_ssql = " And o.tOrderRequestDate > '".WEEK_DATE."'";
}
if (!empty($promocode) && isset($promocode)) {
    $ssql .= " AND o.vCouponCode LIKE '".$promocode."' AND o.iStatusCode=6";
}
$ssql .= ' AND (c.iServiceId IN('.$enablesevicescategory.") OR c.eBuyAnyService = 'Yes') ";
// Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT COUNT(o.iOrderId) AS Total FROM orders o LEFT JOIN register_driver d ON d.iDriverId = o.iDriverId LEFT JOIN  register_user u ON u.iUserId = o.iUserId LEFT JOIN company c ON c.iCompanyId=o.iCompanyId LEFT JOIN order_status as os on os.iStatusCode = o.iStatusCode LEFT JOIN service_categories as sc on sc.iServiceId=o.iServiceId WHERE IF(o.eTakeaway = 'Yes' && os.iStatusCode = 6, os.eTakeaway='Yes', os.eTakeaway != 'Yes') AND IF(o.eBuyAnyService = 'Yes' && os.iStatusCode IN (1,4,13,14), os.eBuyAnyService = 'Yes', os.eBuyAnyService = 'No') AND o.iStatusCode IN {$iStatusCode} {$ssql} {$trp_ssql}";
$totalData = $obj->MySQLSelect($sql);
$total_results = $totalData[0]['Total'];
// total pages we going to have
$total_pages = ceil($total_results / $per_page);
$show_page = 1;
$start = 0;
$end = $per_page;
// -------------if page is setcheck------------------//
if (isset($_GET['page'])) {
    // it will telles the current page
    $show_page = $_GET['page'];
    if ($show_page > 0 && $show_page <= $total_pages) {
        $start = ($show_page - 1) * $per_page;
        $end = $start + $per_page;
    }
}
// display pagination
$page = isset($_GET['page']) ? (int) ($_GET['page']) : 0;
$tpages = $total_pages;
if ($page <= 0) {
    $page = 1;
}
// Pagination End
$sql = 'SELECT o.fTotalGenerateFare,o.iOrderId, o.fSubTotal,o.iServiceId,sc.vServiceName_'.$default_lang." as vServiceName,o.fOffersDiscount,o.fCommision,o.fDeliveryCharge,o.iStatusCode,o.vTimeZone,o.vOrderNo,o.iUserId,o.iUserAddressId,u.vCountry,o.dDeliveryDate,o.tOrderRequestDate,o.ePayWallet,o.ePaymentOption,o.tOrderRequestDate,o.fNetTotal,os.vStatus ,CONCAT(u.vName,' ',u.vLastName) AS riderName,o.iDriverId,o.iCompanyId, CONCAT(d.vName,' ',d.vLastName) AS driverName,c.vCompany,c.eAutoaccept,(select count(orddetail.iOrderId) from order_details as orddetail where orddetail.iOrderId = o.iOrderId) as TotalItem,CONCAT('<b>Phone: </b> +',u.vPhoneCode,' ',u.vPhone)  as user_phone,CONCAT('<b>Phone: </b> +',d.vCode,' ',d.vPhone) as driver_phone,CONCAT('<b>Phone: </b> +',c.vCode,' ',c.vPhone) as resturant_phone,o.eTakeaway,o.fTipAmount,o.eBuyAnyService,o.eForPickDropGenie,o.eCancelledbyDriver,o.vCancelReasonDriver, o.eCancelledBy, o.eAskCodeToUser, o.vRandomCode, o.eOrderplaced_by, o.vName as KioskUserName, o.tKioskUserDetails, c.vCode FROM orders o LEFT JOIN register_driver d ON d.iDriverId = o.iDriverId LEFT JOIN  register_user u ON u.iUserId = o.iUserId LEFT JOIN company c ON c.iCompanyId = o.iCompanyId LEFT JOIN order_status as os on os.iStatusCode = o.iStatusCode LEFT JOIN service_categories as sc on sc.iServiceId = o.iServiceId WHERE IF(o.eTakeaway = 'Yes' && os.iStatusCode = 6, os.eTakeaway='Yes', os.eTakeaway != 'Yes') AND IF(o.eBuyAnyService = 'Yes' && os.iStatusCode IN (1,4,13,14), os.eBuyAnyService = 'Yes', os.eBuyAnyService = 'No') AND o.iStatusCode IN {$iStatusCode} {$ssql} {$trp_ssql} {$ord} LIMIT {$start}, {$per_page}";
// echo $sql;exit;
$DBProcessingOrders = $obj->MySQLSelect($sql);
// echo "<pre>";print_r($sql);die;
$endRecord = count($DBProcessingOrders);
$var_filter = '';
foreach ($_REQUEST as $key => $val) {
    if ('tpages' !== $key && 'page' !== $key) {
        $var_filter .= "&{$key}=".stripslashes($val);
    }
}
$reload = $_SERVER['PHP_SELF'].'?tpages='.$tpages.$var_filter;
$Today = date('Y-m-d');
$tdate = date('d') - 1;
$mdate = date('d');
$Yesterday = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));
$curryearFDate = date('Y-m-d', mktime(0, 0, 0, '1', '1', date('Y')));
$curryearTDate = date('Y-m-d', mktime(0, 0, 0, '12', '31', date('Y')));
$prevyearFDate = date('Y-m-d', mktime(0, 0, 0, '1', '1', date('Y') - 1));
$prevyearTDate = date('Y-m-d', mktime(0, 0, 0, '12', '31', date('Y') - 1));
$currmonthFDate = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - $tdate, date('Y')));
$currmonthTDate = date('Y-m-d', mktime(0, 0, 0, date('m') + 1, date('d') - $mdate, date('Y')));
$prevmonthFDate = date('Y-m-d', mktime(0, 0, 0, date('m') - 1, date('d') - $tdate, date('Y')));
$prevmonthTDate = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - $mdate, date('Y')));
$monday = date('Y-m-d', strtotime('monday this week'));
$sunday = date('Y-m-d', strtotime('sunday this week'));
$Pmonday = date('Y-m-d', strtotime('monday this week -1 week'));
$Psunday = date('Y-m-d', strtotime('sunday this week -1 week'));
if ('cancel' === $action && '' !== $hdn_del_id) {
    if (SITE_TYPE === 'Demo') {
        $_SESSION['success'] = 2;
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_EDIT_DELETE_RECORD'];
        echo "<script>location.href='allorders.php?type=".$order_type."'</script>";

        exit;
    }
    $_REQUEST['eSystem'] = 'DeliverAll';
    $vCancelReason = $_REQUEST['cancel_reason'] ?? '';
    $fCancellationCharge = $_REQUEST['fCancellationCharge'] ?? '';
    $fDeliveryCharge = $_REQUEST['fDeliveryCharge'] ?? '';
    $fRestaurantPayAmount = $_REQUEST['fRestaurantPayAmount'] ?? '';
    $iUserId = $_REQUEST['iUserId'] ?? '';
    $iDriverId = $_REQUEST['iDriverId'] ?? '';
    $iTripId = $_REQUEST['iTripId'] ?? '';
    $iCompanyId = $_REQUEST['iCompanyId'] ?? '';
    $vIP = get_client_ip();
    $wallet_data = $obj->MySQLSelect("SELECT fWalletDebit,iUserId,vOrderNo,ePaymentOption,fNetTotal FROM orders WHERE iOrderId = '".$hdn_del_id."'");
    $userCurData = $obj->MySQLSelect("SELECT rd.vCurrencyPassenger,cu.vSymbol FROM register_user as rd LEFT JOIN currency as cu ON rd.vCurrencyDriver = cu.vName WHERE iUserId = '".$iUserId."'");
    $currencySymbol = $userCurData[0]['vSymbol'];
    $currencycode = $userCurData[0]['vCurrencyPassenger'];
    $userCurrencyRatio = get_value('currency', 'Ratio', 'vName', $currencycode, '', 'true');
    if ('' === $currencySymbol || null === $currencySymbol) {
        $currencySymbol = get_value('currency', 'vSymbol', 'eDefault', 'Yes', '', 'true');
    }
    if ('' === $currencycode || null === $currencycode) {
        $userCurrencyRatio = get_value('currency', 'Ratio', 'eDefault', 'Yes', '', 'true');
    }
    // $fCancellationChargeCur = $currencySymbol . setTwoDecimalPoint($fCancellationCharge * $userCurrencyRatio);
    $fCancellationChargeCur = formateNumAsPerCurrency($fCancellationCharge * $userCurrencyRatio, $currencycode);
    $wallet_data = $obj->MySQLSelect("SELECT fWalletDebit,iUserId,vOrderNo,ePaymentOption,fNetTotal,vCouponCode,iServiceId FROM orders WHERE iOrderId = '".$hdn_del_id."'");
    $iServiceId = $wallet_data[0]['iServiceId'];
    if ($wallet_data[0]['fWalletDebit'] > 0) {
        $iUserId = $wallet_data[0]['iUserId'];
        $iBalance = $wallet_data[0]['fWalletDebit'];
        $vOrderNo = $wallet_data[0]['vOrderNo'];
        $eFor = 'Deposit';
        $eType = 'Credit';
        $tDescription = '#LBL_CREDITED_BOOKING_DL#'.$vOrderNo;
        $ePaymentStatus = 'Unsettelled';
        $dDate = date('Y-m-d H:i:s');
        $eUserType = 'Rider';
        $WALLET_OBJ->PerformWalletTransaction($iUserId, $eUserType, $iBalance, $eType, $hdn_del_id, $eFor, $tDescription, $ePaymentStatus, $dDate);
    }
    // added by SP on 27-06-2020, promocode usage limit increase..bcz it is done only when order finished..so when cancel that order then other user use it..that is wrong so put it...
    $vCouponCode = $wallet_data[0]['vCouponCode'];
    // echo $vCouponCode;exit;
    if ('' !== $vCouponCode) {
        $coupon_result = $obj->MySQLSelect("SELECT iUsed, iUsageLimit from coupon WHERE vCouponCode = '".$vCouponCode."'");
        // print_R($coupon_result); exit;
        $noOfCouponUsed = $coupon_result[0]['iUsed'];
        $iUsageLimit = $coupon_result[0]['iUsageLimit'];
        $where = " vCouponCode = '".$vCouponCode."'";
        $data_coupon['iUsed'] = $noOfCouponUsed + 1;
        $obj->MySQLQueryPerform('coupon', $data_coupon, 'update', $where);
        // # Check Coupon Code Usage Limit , Send Email to Admin if Usage  Limit is over ##
        $UpdatedCouponUsedNo = $noOfCouponUsed + 1;
        if ($iUsageLimit === $UpdatedCouponUsedNo) {
            $maildata['vCouponCode'] = $vCouponCode;
            $maildata['iUsageLimit'] = $iUsageLimit;
            $maildata['COMPANY_NAME'] = $COMPANY_NAME;
            $mail = $COMM_MEDIA_OBJ->SendMailToMember('COUPON_LIMIT_COMPLETED_TO_ADMIN', $maildata);
        }
        // # Check Coupon Code Usage Limit , Send Email to Admin if Usage  Limit is over ##
    }
    // added by SP end
    $obj->sql_query("UPDATE orders SET iStatusCode = '8' , eCancelledBy= 'Admin' ,fCancellationCharge = '".$fCancellationCharge."',fRestaurantPayAmount = '".$fRestaurantPayAmount."' ,vCancelReason='".$vCancelReason."' WHERE iOrderId = '".$hdn_del_id."'");
    // $lquery = "INSERT INTO `order_status_logs`(`iOrderId`, `iStatusCode`, `dDate`, `vIp`) VALUES ('" . $hdn_del_id . "','8',Now(),'" . $vIP . "')";
    $currdate = date('Y-m-d H:i:s');
    $obj->sql_query("INSERT INTO `order_status_logs`(`iOrderId`, `iStatusCode`, `dDate`, `vIp`) VALUES ('".$hdn_del_id."','8','".$currdate."','".$vIP."')");
    // if($wallet_data[0]['ePaymentOption'] != 'Card' &&  $wallet_data[0]['fNetTotal'] > 0 ){
    if ($fCancellationCharge > 0 && 'Card' !== $wallet_data[0]['ePaymentOption']) {
        $query_trip_outstanding_amount = "INSERT INTO `trip_outstanding_amount`(`iOrderId`, `iTripId`, `iUserId`, `iDriverId`,`iCompanyId`,`fCancellationFare`,`fPendingAmount`) VALUES ('".$hdn_del_id."','".$iTripId."','".$iUserId."','".$iDriverId."','".$iCompanyId."','".$fCancellationCharge."','".$fCancellationCharge."')";
        $last_insert_id = $obj->MySQLInsert($query_trip_outstanding_amount);
        $db_curr = $obj->MySQLSelect("SELECT * FROM currency WHERE eStatus = 'Active'");
        $where = "iTripOutstandId = '".$last_insert_id."'";
        for ($i = 0; $i < count($db_curr); ++$i) {
            $data_currency_ratio['fRatio_'.$db_curr[$i]['vName']] = $db_curr[$i]['Ratio'];
            $obj->MySQLQueryPerform('trip_outstanding_amount', $data_currency_ratio, 'update', $where);
        }
    }
    // added by PM for Auto credit wallet driver on 25-01-2020 start
    if ($MODULES_OBJ->isAutoCreditToDriverModuleAvailable()) {
        $data = [];
        $data['iOrderId'] = $hdn_del_id;
        $data['fDeliveryCharge'] = $fDeliveryCharge;
        AutoCreditWalletDriver($data);
    }
    // added by PM for Auto credit wallet driver on 25-01-2020 end
    $tipOrderData = $obj->MySQLSelect("SELECT fTipAmount FROM orders WHERE iOrderId = '{$hdn_del_id}'");
    $fDeliveryCharge -= $tipOrderData[0]['fTipAmount'];
    $obj->sql_query("UPDATE trips SET  fDeliveryCharge ='".$fDeliveryCharge."' WHERE iOrderId = '".$hdn_del_id."'");
    // # Send Notification To User  ##
    $MessageUser = 'OrderCancelByAdmin';
    $user_data_order = $obj->MySQLSelect("SELECT ru.iUserId,ru.iGcmRegId,ru.eDeviceType,ru.tSessionId,ru.iAppVersion,ru.vLang,ord.vOrderNo,ord.iOrderId,ru.eAppTerminate,ru.eDebugMode,ru.eHmsDevice FROM orders as ord LEFT JOIN register_user as ru ON ord.iUserId=ru.iUserId where ord.iOrderId = '".$hdn_del_id."'");
    $vLangCodeuser = $user_data_order[0]['vLang'];
    $vOrderNoUser = $user_data_order[0]['vOrderNo'];
    $iOrderIdUser = $user_data_order[0]['iOrderId'];
    $iUserIdNew = $user_data_order[0]['iUserId'];
    if ('' === $vLangCodeuser || null === $vLangCodeuser) {
        $vLangCodeuser = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
    }
    $languageLabelsArrUser = $LANG_OBJ->FetchLanguageLabels($vLangCodeuser, '1', $iServiceId);
    $vTitleReasonMessage = ('' !== $vCancelReason) ? $vCancelReason : '';
    $alertMsgUser = $languageLabelsArrUser['LBL_CANCEL_ORDER_ADMIN_TXT'].' #'.$vOrderNoUser.' '.$languageLabelsArrUser['LBL_REASON_TXT'].' '.$vTitleReasonMessage;
    $message_arrUser = [];
    $message_arrUser['Message'] = $MessageUser;
    $message_arrUser['iOrderId'] = $iOrderIdUser;
    $message_arrUser['vOrderNo'] = $vOrderNoUser;
    $message_arrUser['vTitle'] = $alertMsgUser;
    $message_arrUser['tSessionId'] = $user_data_order[0]['tSessionId'];
    $message_arrUser['eSystem'] = 'DeliverAll';
    // added by SP on 02-02-2021 for custom notification
    $message_arrUser['CustomNotification'] = $MODULES_OBJ->isEnableCustomNotification() ? 'Yes' : 'No';
    // these two btn CustomViewBtn,CustomTrackDetails whether shown in app or not
    $message_arrUser['CustomViewBtn'] = 'No';
    $message_arrUser['CustomTrackDetails'] = 'No';
    $customNotiArray = GetCustomNotificationDetails($iOrderIdUser, $message_arrUser, $vLangCodeuser);
    // title and sub description shown in custom notification
    $message_arrUser['CustomTitle'] = $customNotiArray[0]['vCurrentStatus'];
    $message_arrUser['CustomSubTitle'] = $customNotiArray[0]['vCurrentStatus_Track'];
    $message_arrUser['CustomMessage'] = $customNotiArray;
    $iAppVersionUser = $user_data_order[0]['iAppVersion'];
    $eDeviceTypeUser = $user_data_order[0]['eDeviceType'];
    $iGcmRegIdUser = $user_data_order[0]['iGcmRegId'];
    $tSessionIdUser = $user_data_order[0]['tSessionId'];
    $eAppTerminateUser = $user_data_order[0]['eAppTerminate'];
    $eDebugModeUser = $user_data_order[0]['eDebugMode'];
    $eHmsDeviceUser = $user_data_order[0]['eHmsDevice'];
    $channelNameUser = 'PASSENGER_'.$iUserIdNew;
    $generalDataArr = [];
    $generalDataArr[] = [
        'eDeviceType' => $eDeviceTypeUser,
        'deviceToken' => $iGcmRegIdUser,
        'alertMsg' => $alertMsgUser,
        'eAppTerminate' => $eAppTerminateUser,
        'eDebugMode' => $eDebugModeUser,
        'eHmsDevice' => $eHmsDeviceUser,
        'message' => $message_arrUser,
        'channelName' => $channelNameUser,
    ];
    $EVENT_MSG_OBJ->send(['GENERAL_DATA' => $generalDataArr], RN_USER);
    // # Send Notification To User ##
    // # Send Notification To Restaurant  ##
    $Message = 'OrderCancelByAdmin';
    $Resdata_order = $obj->MySQLSelect("select c.iCompanyId,c.iGcmRegId,c.eDeviceType,c.tSessionId,c.iAppVersion,c.vLang,o.vOrderNo,c.eAppTerminate,c.eDebugMode,c.eHmsDevice from orders as o LEFT JOIN company as c ON o.iCompanyId=c.iCompanyId where o.iOrderId = '".$hdn_del_id."'");
    $ResLangCode = $Resdata_order[0]['vLang'];
    $ResOrderNo = $Resdata_order[0]['vOrderNo'];
    $iCompanyId = $Resdata_order[0]['iCompanyId'];
    if ('' === $ResLangCode || null === $ResLangCode) {
        $ResLangCode = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
    }
    $ResTitleReasonMessage = ('' !== $vCancelReason) ? $vCancelReason : '';
    $languageLabelsArrRes = $LANG_OBJ->FetchLanguageLabels($ResLangCode, '1', $iServiceId);
    $ResAlertMsg = $languageLabelsArrRes['LBL_CANCEL_ORDER_ADMIN_TXT'].' #'.$ResOrderNo.' '.$languageLabelsArrRes['LBL_REASON_TXT'].' '.$ResTitleReasonMessage;
    $message_arr_res = [];
    $message_arr_res['Message'] = $Message;
    $message_arr_res['iOrderId'] = $hdn_del_id;
    $message_arr_res['vOrderNo'] = $ResOrderNo;
    $message_arr_res['eSystem'] = 'DeliverAll';
    $message_arr_res['vTitle'] = $ResAlertMsg;
    $message_arr_res['tSessionId'] = $Resdata_order[0]['tSessionId'];
    $restaurantmessage = json_encode($message_arr_res, JSON_UNESCAPED_UNICODE);
    $iAppVersion = $Resdata_order[0]['iAppVersion'];
    $eDeviceType = $Resdata_order[0]['eDeviceType'];
    $iGcmRegId = $Resdata_order[0]['iGcmRegId'];
    $tSessionId = $Resdata_order[0]['tSessionId'];
    $eAppTerminate = $Resdata_order[0]['eAppTerminate'];
    $eDebugMode = $Resdata_order[0]['eDebugMode'];
    $eHmsDevice = $Resdata_order[0]['eHmsDevice'];
    $RestaurantchannelName = 'COMPANY_'.$iCompanyId;
    $generalDataArr = [];
    $generalDataArr[] = [
        'eDeviceType' => $eDeviceType,
        'deviceToken' => $iGcmRegId,
        'alertMsg' => $ResAlertMsg,
        'eAppTerminate' => $eAppTerminate,
        'eDebugMode' => $eDebugMode,
        'eHmsDevice' => $eHmsDevice,
        'message' => $message_arr_res,
        'channelName' => $RestaurantchannelName,
    ];
    $EVENT_MSG_OBJ->send(['GENERAL_DATA' => $generalDataArr], RN_COMPANY);
    // # Send Notification To Restaurant  ##
    // # Send Notification To Driver ##
    $OrdersData = $obj->MySQLSelect("select * from order_status_logs where iOrderId = '".$hdn_del_id."' AND iStatusCode = '4'");
    if (count($OrdersData) > 0) {
        $Message = 'OrderCancelByAdmin';
        $drv_data_order = $obj->MySQLSelect("select d.iDriverId,d.iGcmRegId,d.eDeviceType,d.tSessionId,d.iAppVersion,d.vLang,o.vOrderNo,d.eAppTerminate,d.eDebugMode,d.eHmsDevice from orders as o LEFT JOIN register_driver as d ON o.iDriverId=d.iDriverId where o.iOrderId = '".$hdn_del_id."'");
        $drvLangCode = $drv_data_order[0]['vLang'];
        $drvOrderNo = $drv_data_order[0]['vOrderNo'];
        $iDriverId = $drv_data_order[0]['iDriverId'];
        $obj->sql_query("UPDATE register_driver SET vTripStatus = 'Cancelled' WHERE iDriverId = '".$iDriverId."'");
        $obj->sql_query("UPDATE trips SET iActive = 'Canceled' WHERE iOrderId = '".$hdn_del_id."'");
        if ($MODULES_OBJ->isEnableAcceptMultipleOrders()) {
            $orderIds = $obj->MySQLSelect("SELECT COUNT(iOrderId) as order_ids FROM trips WHERE (iActive = 'Active' OR iActive = 'On Going Trip') AND iDriverId = '{$iDriverId}' AND eSystem = 'DeliverAll'");
            if (!empty($orderIds[0]['order_ids']) && $orderIds[0]['order_ids'] > 0) {
                $obj->sql_query("UPDATE register_driver SET vTripStatus = 'On Going Trip', vAvailability = 'Not Available' WHERE iDriverId = '{$iDriverId}'");
            }
        }
        if ('' === $drvLangCode || null === $drvLangCode) {
            $drvLangCode = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
        }
        $drvTitleReasonMessage = ('' !== $vCancelReason) ? $vCancelReason : '';
        $languageLabelsArrDrv = $LANG_OBJ->FetchLanguageLabels($drvLangCode, '1', $iServiceId);
        $drvAlertMsg = $languageLabelsArrDrv['LBL_CANCEL_ORDER_ADMIN_TXT'].' #'.$drvOrderNo.' '.$languageLabelsArrDrv['LBL_REASON_TXT'].' '.$drvTitleReasonMessage;
        $message_arr_res = [];
        $message_arr_res['Message'] = $Message;
        $message_arr_res['iOrderId'] = $hdn_del_id;
        $message_arr_res['vOrderNo'] = $drvOrderNo;
        $message_arr_res['eSystem'] = 'DeliverAll';
        $message_arr_res['vTitle'] = $drvAlertMsg;
        $message_arr_res['tSessionId'] = $drv_data_order[0]['tSessionId'];
        $drvmessage = json_encode($message_arr_res, JSON_UNESCAPED_UNICODE);
        $logChk = [];
        if ($MODULES_OBJ->isEnableCancelDriverOrder()) {
            $logChk = $obj->MySQLSelect("SELECT * FROM order_driver_log WHERE `iOrderId` = '".$hdn_del_id."' AND `iDriverId` = '".$iDriverId."'");
        }
        $alertSendAllowed = true;
        if (empty($logChk)) {
            $iAppVersion = $drv_data_order[0]['iAppVersion'];
            $eDeviceType = $drv_data_order[0]['eDeviceType'];
            $iGcmRegId = $drv_data_order[0]['iGcmRegId'];
            $tSessionId = $drv_data_order[0]['tSessionId'];
            $eAppTerminate = $drv_data_order[0]['eAppTerminate'];
            $eDebugMode = $drv_data_order[0]['eDebugMode'];
            $eHmsDevice = $drv_data_order[0]['eHmsDevice'];
            $DriverchannelName = 'DRIVER_'.$iDriverId;
            $generalDataArr = [];
            $generalDataArr[] = [
                'eDeviceType' => $eDeviceType,
                'deviceToken' => $iGcmRegId,
                'alertMsg' => $drvAlertMsg,
                'eAppTerminate' => $eAppTerminate,
                'eDebugMode' => $eDebugMode,
                'eHmsDevice' => $eHmsDevice,
                'message' => $message_arr_res,
                'channelName' => $DriverchannelName,
            ];
            $EVENT_MSG_OBJ->send(['GENERAL_DATA' => $generalDataArr], RN_PROVIDER);
        }
    }
    // # Send Notification To Driver ##
    $bookind_detail = $obj->MySQLSelect('SELECT tOrderRequestDate,vOrderNo,iUserId,iDriverId,iCompanyId FROM orders WHERE iOrderId='.$hdn_del_id);
    $tOrderRequestDateMail = $vOrderNoMail = '';
    if (count($bookind_detail) > 0) {
        $tOrderRequestDateMail = $bookind_detail[0]['tOrderRequestDate'];
        $vOrderNoMail = $bookind_detail[0]['vOrderNo'];
    }
    $driver_db = $obj->MySQLSelect('SELECT vName,vLastName,vEmail,iDriverVehicleId,vPhone,vcode,vLang FROM register_driver WHERE iDriverId='.$iDriverId);
    $vDriverEmail = $driverFullname = $vPhone = $vcode = $vLang = '';
    if (count($driver_db) > 0) {
        $vPhone = $driver_db[0]['vPhone'];
        $vcode = $driver_db[0]['vcode'];
        $vLang = $driver_db[0]['vLang'];
        $driverFullname = $driver_db[0]['vName'].' '.$driver_db[0]['vLastName'];
        $vDriverEmail = $driver_db[0]['vEmail'];
    }
    $user_detail = $obj->MySQLSelect("SELECT vName,vLastName,vEmail,iUserId,vPhone,vPhoneCode,vLang FROM register_user WHERE iUserId = '".$iUserId."'");
    $vPhone1 = $vcode1 = $vLang1 = $vEmail1 = $userFullname = '';
    if (count($user_detail) > 0) {
        $vPhone1 = $user_detail[0]['vPhone'];
        $vcode1 = $user_detail[0]['vPhoneCode'];
        $vLang1 = $user_detail[0]['vLang'];
        $vEmail1 = $user_detail[0]['vEmail'];
        $userFullname = $user_detail[0]['vName'].' '.$user_detail[0]['vLastName'];
    }
    $comapny_detail = $obj->MySQLSelect("select vCompany,vEmail,vPhone,vcode,vLang,vRestuarantLocation from company where iCompanyId='".$iCompanyId."'");
    $vLang2 = $default_lang;
    $vPhone = $vcode = $vCompany = $vEmail2 = $vSourceAddresss = '';
    if (count($comapny_detail) > 0) {
        $vPhone = $comapny_detail[0]['vPhone'];
        $vcode = $comapny_detail[0]['vcode'];
        $vLang2 = $comapny_detail[0]['vLang'];
        $vCompany = $comapny_detail[0]['vCompany'];
        $vEmail2 = $comapny_detail[0]['vEmail'];
        $vSourceAddresss = $comapny_detail[0]['vRestuarantLocation'];
    }
    // added by SP for emailissue on 3-7-2019 start
    $Data['ProjectName'] = $SITE_NAME;
    $Data['vOrderNo'] = $vOrderNoMail;
    $Data['MSG'] = $vCancelReason;
    $Data['Charge'] = $fCancellationChargeCur;
    if (0 !== $iDriverId && $iDriverId > 0) {
        $Data['vEmail'] = $vDriverEmail;
        $Data['UserName'] = $driverFullname;
        $return = $COMM_MEDIA_OBJ->SendMailToMember('MANUAL_CANCEL_ORDER_ADMIN_TO_DRIVER_COMPANY', $Data);
    }
    $Data['vEmail'] = $vEmail2;
    $Data['UserName'] = $vCompany;
    $return1 = $COMM_MEDIA_OBJ->SendMailToMember('MANUAL_CANCEL_ORDER_ADMIN_TO_DRIVER_COMPANY', $Data);
    $Data['vEmail'] = $vEmail1;
    $Data['UserName'] = $userFullname;
    $return1 = $COMM_MEDIA_OBJ->SendMailToMember('MANUAL_CANCEL_ORDER_ADMIN_TO_RIDER', $Data);
    // added by SP for Mail on 3-7-2019 end
    $Booking_Date = @date('d-m-Y', strtotime($tOrderRequestDateMail));
    $Booking_Time = @date('H:i:s', strtotime($tOrderRequestDateMail));
    $maildata['vDriver'] = $driverFullname;
    $maildata['dBookingdate'] = $Booking_Date;
    $maildata['dBookingtime'] = $Booking_Time;
    $maildata['vBookingNo'] = $vOrderNoMail;
    $maildata1['vRider'] = $userFullname;
    $maildata1['dBookingdate'] = $Booking_Date;
    $maildata1['dBookingtime'] = $Booking_Time;
    $maildata1['vBookingNo'] = $vOrderNoMail;
    $maildataCompany['vCompany'] = $vCompany;
    $maildataCompany['dBookingdate'] = $Booking_Date;
    $maildataCompany['dBookingtime'] = $Booking_Time;
    $maildataCompany['vBookingNo'] = $vOrderNoMail;
    if (0 !== $iDriverId && $iDriverId > 0) {
        $message_layout = $COMM_MEDIA_OBJ->GetSMSTemplate('DRIVER_SEND_MESSAGE_JOB_CANCEL', $maildata1, '', $vLang);
    }
    if (0 !== $iUserId && $iUserId > 0) {
        $message_layout = $COMM_MEDIA_OBJ->GetSMSTemplate('USER_SEND_MESSAGE_JOB_CANCEL', $maildata, '', $vLang1);
    }

    if ($iCompanyId > 0) {
        $message_layout = $COMM_MEDIA_OBJ->GetSMSTemplate('SEND_MESSAGE_JOB_CANCEL_BY_ADMIN', $maildataCompany, '', $vLang2);
    }

    echo "<script>location.href='allorders.php?type=".$order_type."'</script>";
}
$catdata = serviceCategories;
$allservice_cat_data = json_decode($catdata, true);
$manualAssignDriver = $MODULES_OBJ->isEnableManualAssignProvider();
$cancelDriverOrder = $MODULES_OBJ->isEnableCancelDriverOrder() ? 'Yes' : 'No';
// reassign driver - for that reset current driver
$Datadriver = $obj->MySQLSelect("SELECT tSessionId,iDriverId FROM `register_driver` WHERE tSessionId != '' LIMIT 1");
if (empty($Datadriver)) {
    $Datadriver = $obj->MySQLSelect('SELECT tSessionId,iDriverId FROM `register_driver` WHERE 1 LIMIT 1');
    if (!empty($Datadriver)) {
        $Data_update_passenger = [];
        $whereCondition = ' iDriverId = '.$Datadriver[0]['iDriverId'];
        $Data_update_passenger['tSessionId'] = $Datadriver[0]['tSessionId'] = session_id().time();
        $obj->MySQLQueryPerform('register_driver', $Data_update_passenger, 'update', $whereCondition);
    }
}
$driversessionid = $Datadriver[0]['tSessionId'];
$driverId = $Datadriver[0]['iDriverId'];
?>
<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8" />
    <title><?php echo $SITE_NAME; ?> | <?php echo $langage_lbl_admin['LBL_PROCESSING_ORDERS']; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <?php include_once 'global_files.php'; ?>
    <link rel="stylesheet" href="../assets/css/modal_alert.css" />
    <style>
        ul#driver_main_list li {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding: 0 0 10px 0;
            align-items: center;
        }
        ul#driver_main_list ul {
            padding: 0;
            margin: 0;
        }
        ul#driver_main_list {
            padding: 0;
            margin: 0;
        }
        ul#driver_main_list li b {
            padding: 8px 15px;
        }

        .custom-model-footer {
            padding-bottom: 25px;
        }
    </style>
    <script type="text/javascript">var eType = '';</script>
</head>
<!-- END  HEAD-->
<!-- BEGIN BODY-->
<body class="padTop53 ">
<!-- Main LOading -->
<!-- MAIN WRAPPER -->
<div id="wrap">
    <?php include_once 'header.php'; ?>
    <?php include_once 'left_menu.php'; ?>
    <!--PAGE CONTENT -->
    <div id="content">
        <div class="inner">
            <div id="add-hide-show-div">
                <div class="row">
                    <div class="col-lg-12">
                        <h2><?php echo $script; ?> </h2>
                    </div>
                </div>
                <hr/>
            </div>
            <?php include 'valid_msg.php'; ?>
            <!--  Search Form Start  -->
            <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">
                <div class="Posted-date mytrip-page payment-report">
                    <input type="hidden" name="action" value="search"/>
                    <input type="hidden" name="type" value="<?php echo $order_type; ?>"/>
                    <h3>Search <?php echo $langage_lbl_admin['LBL_PROCESSING_ORDERS']; ?> ...</h3>
                    <span>
                <a onClick="return todayDate('dp4', 'dp5');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Today']; ?></a>
                <a onClick="return yesterdayDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Yesterday']; ?></a>
                <a onClick="return currentweekDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Current_Week']; ?></a>
                <a onClick="return previousweekDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Previous_Week']; ?></a>
                <a onClick="return currentmonthDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Current_Month']; ?></a>
                <a onClick="return previousmonthDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Previous Month']; ?></a>
                <a onClick="return currentyearDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Current_Year']; ?></a>
                <a onClick="return previousyearDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Previous_Year']; ?></a>
            </span>
                    <span>
                <input type="text" id="dp4" name="startDate" placeholder="From Date" class="form-control" value=""
                       readonly="" style="cursor:default;background-color: #fff"/>
                <input type="text" id="dp5" name="endDate" placeholder="To Date" class="form-control" value=""
                       readonly="" style="cursor:default;background-color: #fff"/>
                <div class="col-lg-2 select001">
                    <select class="form-control filter-by-text" name="searchStore" id="searchStore"
                            data-text="Select <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?>">
                        <option value="">Select <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?></option>
                    </select>
                </div>
                <div class="col-lg-2 select001">
                    <select class="form-control filter-by-text" name="searchUser"
                            data-text="Select <?php echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']; ?>" id="searchUser">
                        <option value="">Select <?php echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']; ?></option>
                    </select>
                </div>
            </span>
                </div>
                <div class="mytrip-page payment-report payment-report1">
            <span>
                <div class="col-lg-2 select001" style="padding-right:15px;">
                    <select class="form-control filter-by-text driver_container" name='searchDriver'
                            data-text="Select <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?>" id="searchDriver">
                        <option value="">Select <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?></option>
                    </select>
                </div>
                <?php if (count($allservice_cat_data) > 1) { ?>
                    <div class="col-lg-2 select001" style="padding-right:15px;">
                        <select class="form-control filter-by-text-search " name="searchServiceType"
                                data-text="Select Serivce Type">
                            <option value="">Select <?php echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']; ?></option>
                            <?php foreach ($allservice_cat_data as $value) { ?>
                                <option value="<?php echo $value['iServiceId']; ?>" <?php
                                if ($searchServiceType === $value['iServiceId']) {
                                    echo 'selected';
                                }
                                ?>><?php echo clearName($value['vServiceName']); ?></option>
                            <?php } ?>
                            <?php if ($MODULES_OBJ->isEnableAnywhereDeliveryFeature()) { ?>
                                <option value="Genie" <?php
                                if ('Genie' === $searchServiceType) {
                                    echo 'selected';
                                }
                                ?>><?php echo clearName($langage_lbl_admin['LBL_OTHER_DELIVERY']); ?></option>
                                <option value="Runner" <?php
                                if ('Runner' === $searchServiceType) {
                                    echo 'selected';
                                }
                                ?>><?php echo clearName($langage_lbl_admin['LBL_RUNNER']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                <?php } ?>
                <div class="col-lg-2" style="padding-right:15px;">
                    <input type="text" id="searchOrderNo" name="searchOrderNo"
                           placeholder="<?php echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN_DL']; ?> Number"
                           class="form-control search-trip001" value="<?php echo $searchOrderNo; ?>"/>
                </div>


                <div class="col-lg-2 select001">
                    <select class="form-control filter-by-text-search" name="searchOrderStatus"
                            data-text="Select Order Status">
                        <option value="">Select Order Status</option>
                        <?php foreach ($orderStatus as $value) { ?>
                            <option value="<?php echo $value['iStatusCode']; ?>" <?php
                            if ($searchOrderStatus === $value['iStatusCode']) {
                                echo 'selected';
                            }
                            ?>><?php echo clearName($value['vStatus']); ?></option>
                        <?php } ?>
                    </select>
                </div>


            </span>
                </div>
                <div class="tripBtns001">
                    <b>
                        <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search"
                               title="Search"/>
                        <input type="button" value="Reset" class="btnalt button11"
                               onClick="window.location.href = 'allorders.php?type=<?php echo $order_type; ?>'"/>
                    </b>
                </div>
            </form>
            <!-- Search Form End -->
            <div class="table-list">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <form class="_list_form" id="_list_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <?php if (count($allservice_cat_data) > 1) { ?>
                                            <th class="text-center">Serivce Type</th>
                                        <?php } ?>
                                        <th class="text-center"><?php echo $langage_lbl_admin['LBL_ORDER_NO_ADMIN']; ?>#</th>
                                        <th class="text-center">
                                            <a href="javascript:void(0);" onClick="Redirect(1,<?php
                                            if ('1' === $sortby) {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)"><?php echo $langage_lbl_admin['LBL_TRIP_DATE_ADMIN_DL']; ?> <?php
                                                if (1 === $sortby) {
                                                    if (0 === $order) {
                                                        ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                           }
                                                } else {
                                                    ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th>
                                            <a href="javascript:void(0);" onClick="Redirect(2,<?php
                                            if ('2' === $sortby) {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)"><?php echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']; ?>
                         Name <?php
                                                if (2 === $sortby) {
                                                    if (0 === $order) {
                                                        ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                           }
                                                } else {
                                                    ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th>
                                            <a href="javascript:void(0);" onClick="Redirect(3,<?php
                                            if ('3' === $sortby) {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)"><?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?>
                                                Name <?php
                                                if (3 === $sortby) {
                                                    if (0 === $order) {
                                                        ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                           }
                                                } else {
                                                    ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th>
                                            <a href="javascript:void(0);" onClick="Redirect(4,<?php
                                            if ('4' === $sortby) {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)">Delivery <?php
                                                echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];
if (4 === $sortby) {
    if (0 === $order) {
        ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                           }
} else {
    ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th class="text-right">Order Total</th>
                                        <!--<th>Service Type</th>-->
                                        <th class="text-center">Order Status</th>
                                        <th class="text-center">Payment Mode</th>
                                        <?php if ($userObj->hasPermission(['cancel-processing-orders', 'view-order-invoice'])) { ?>

                                        <th class="text-center">Action</th>
                                        <?php } ?>
                                    </tr>

                                    </thead>
                                    <tbody>
                                    <?php
                                    if (!empty($DBProcessingOrders)) {
                                        $systemTimeZone = date_default_timezone_get();
                                        $db_records = $obj->MySQLSelect("SELECT iOrderId,count(CASE WHEN eStatus = 'Accept' THEN iDriverId END) as total_accept,max(tDate) as ttDate,count(iOrderId) as corder  FROM driver_request   WHERE 1 = 1 GROUP BY iOrderId ORDER BY  `tDate` DESC");
                                        $orderDataArr = [];
                                        for ($r = 0; $r < count($db_records); ++$r) {
                                            $orderDataArr[$db_records[$r]['iOrderId']] = $db_records[$r];
                                        }
                                        for ($i = 0; $i < $endRecord; ++$i) {
                                            $vTimeZone = $DBProcessingOrders[$i]['vTimeZone'];
                                            if (empty($vTimeZone)) {
                                                $vTimeZone = $systemTimeZone;
                                            }
                                            $Ordersdate = $DBProcessingOrders[$i]['tOrderRequestDate'];
                                            if ('undefined' !== $vTimeZone) {
                                                $Ordersdate = converToTz($Ordersdate, $systemTimeZone, $vTimeZone);
                                            }
                                            $futureDate = strtotime($Ordersdate) + (60 * 5);
                                            $date = date('Y-m-d H:i:s');
                                            $currentDate = strtotime($date);
                                            $futurenewDate = date('Y-m-d H:i:s', strtotime($futureDate));
                                            $iOrderId = $DBProcessingOrders[$i]['iOrderId'];
                                            $iDriverId = $DBProcessingOrders[$i]['iDriverId'];
                                            $iOrderStatusCode = $DBProcessingOrders[$i]['iStatusCode'];
                                            $eAutoaccept = 'No';
                                            if (isset($DBProcessingOrders[$i]['eAutoaccept'])) {
                                                $eAutoaccept = $DBProcessingOrders[$i]['eAutoaccept'];
                                            }
                                            // Added By HJ On 13-02-2020 For Display Paymen Type Start
                                            $paymentType = ucwords($DBProcessingOrders[$i]['ePaymentOption']);
                                            if ('Method-2' === $SYSTEM_PAYMENT_FLOW || 'Method-3' === $SYSTEM_PAYMENT_FLOW) {
                                                if (0 === $DBProcessingOrders[$i]['fNetTotal'] && 'Yes' === $DBProcessingOrders[$i]['ePayWallet']) {
                                                    $paymentType = ucwords($langage_lbl_admin['LBL_WALLET_TXT']);
                                                } elseif ($DBProcessingOrders[$i]['fNetTotal'] > 0 && 'Yes' === $DBProcessingOrders[$i]['ePayWallet']) {
                                                    $paymentType = ucwords(strtolower($langage_lbl_admin['LBL_CASH_CAPS']));
                                                }
                                            } else {
                                                if (isset($DBProcessingOrders[$i]['fNetTotal']) > 0 && 'Yes' === $DBProcessingOrders[$i]['ePayWallet']) {
                                                    if ('CARD' === strtoupper($DBProcessingOrders[$i]['ePaymentOption'])) {
                                                        // $paymentType = ucwords($langage_lbl_admin["LBL_CARD_CAPS"]) . "-" . ucwords($langage_lbl_admin['LBL_WALLET_TXT']);//commented by SP bc of solving issue to be fixed 1312
                                                        $paymentType = ucwords(strtolower($langage_lbl_admin['LBL_CARD_CAPS']));
                                                    } elseif ('CASH' === strtoupper($DBProcessingOrders[$i]['ePaymentOption'])) {
                                                        // $paymentType = ucwords($langage_lbl_admin["LBL_CASH_CAPS"]) . "-" . ucwords($langage_lbl_admin['LBL_WALLET_TXT']);//commented by SP bc of solving issue to be fixed 1312
                                                        $paymentType = ucwords(strtolower($langage_lbl_admin['LBL_CASH_CAPS']));
                                                    }
                                                }
                                            }
                                            // Added By HJ On 13-02-2020 For Display Paymen Type End
                                            if (count($allservice_cat_data) > 1) {
                                                $vServiceName = $DBProcessingOrders[$i]['vServiceName'];
                                            }
                                            if ('Yes' === $DBProcessingOrders[$i]['eBuyAnyService']) {
                                                $vServiceName = $langage_lbl_admin['LBL_OTHER_DELIVERY'];
                                                if ('Yes' === $DBProcessingOrders[$i]['eForPickDropGenie']) {
                                                    $vServiceName = $langage_lbl_admin['LBL_RUNNER'];
                                                }
                                                if (in_array($DBProcessingOrders[$i]['iStatusCode'], [
                                                    1,
                                                    2,
                                                ], true)) {
                                                    $DBProcessingOrders[$i]['vStatus'] = $langage_lbl_admin['LBL_ORDER_PLACED'];
                                                }
                                            }
                                            ?>
                                            <tr class="gradeA">
                                                <?php if (count($allservice_cat_data) > 1) { ?>
                                                    <td class="text-center"><?php echo $vServiceName; ?></td>
                                                <?php } ?>
                                                <?php if ($userObj->hasPermission('view-order-invoice')) { ?>
                                                    <td class="text-center">
                                                        <a href="order_invoice.php?iOrderId=<?php echo $DBProcessingOrders[$i]['iOrderId']; ?>"
                                                           target="_blank"><?php echo $DBProcessingOrders[$i]['vOrderNo']; ?></a>
                                                        <?php echo 'Yes' === $DBProcessingOrders[$i]['eTakeaway'] ? '<br><span>'.$langage_lbl['LBL_TAKE_AWAY'].'</span>' : ('Kiosk' === $DBProcessingOrders[$i]['eOrderplaced_by'] ? '<br><span>'.$langage_lbl['LBL_DINE_IN_TXT'].'</span>' : ''); ?>
                                                    </td>
                                                <?php } else { ?>
                                                    <td class="text-center"><?php echo $DBProcessingOrders[$i]['vOrderNo']; ?></td>
                                                <?php } ?>
                                                <td class="text-center">
                                                    <?php echo DateTime(converToTz($DBProcessingOrders[$i]['tOrderRequestDate'], $vTimeZone, $systemTimeZone), 'yes'); ?>
                                                </td>
                                                <td>
                                                    <?php if ('Kiosk' === $DBProcessingOrders[$i]['eOrderplaced_by']) { ?>
                                                        <?php echo clearName($DBProcessingOrders[$i]['KioskUserName']); ?>
                            <br>
                                                    <?php } else { ?>
                                                        <?php if ($userObj->hasPermission('view-users')) { ?><a href="javascript:void(0);" onClick="show_rider_details('<?php echo $DBProcessingOrders[$i]['iUserId']; ?>')" style="text-decoration: underline;"><?php } ?><?php echo clearName($DBProcessingOrders[$i]['riderName']); ?><?php if ($userObj->hasPermission('view-users')) { ?></a><?php } ?>
                                                        <br>

                                                    <?php } ?>
                                                    <?php
                                                    if ('Kiosk' === $DBProcessingOrders[$i]['eOrderplaced_by']) {
                                                        $tKioskUserDetails = json_decode($DBProcessingOrders[$i]['tKioskUserDetails'], true);
                                                        if (!empty($tKioskUserDetails['userMobile'])) {
                                                            $userMobile = '<b>Phone:</b> +'.$DBProcessingOrders[$i]['vCode'].' '.$tKioskUserDetails['userMobile'];
                                                            echo clearPhone($userMobile);
                                                        }
                                                    }
                                            if (!empty($DBProcessingOrders[$i]['user_phone'])) {
                                                echo clearPhone($DBProcessingOrders[$i]['user_phone']);
                                            }
                                            ?>
                                                </td>
                        <td> <?php if ($userObj->hasPermission('view-store')) { ?><a href="javascript:void(0);" onClick="show_store_details('<?php echo $DBProcessingOrders[$i]['iCompanyId']; ?>')" style="text-decoration: underline;"><?php } ?><?php echo clearCmpName($DBProcessingOrders[$i]['vCompany']); ?><?php if ($userObj->hasPermission('view-store')) { ?></a><?php } ?>
                        </br>
                                                    <?php
                                            if (!empty($DBProcessingOrders[$i]['resturant_phone'])) {
                                                echo clearPhone($DBProcessingOrders[$i]['resturant_phone']);
                                            }
                                            ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php

                                            if ('Yes' === $cancelDriverOrder && 'Yes' === $DBProcessingOrders[$i]['eCancelledbyDriver']) { ?>
                                                        <button href="#" onclick="viewCancelOrderDrivers(this);"
                                                                class="btn btn-info" data-id="<?php echo $iOrderId; ?>"
                                                                type="button">View
                                                            Cancelled <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?></button>
                                                    <?php } else {
                                                        if (!empty($DBProcessingOrders[$i]['driverName']) && !empty($DBProcessingOrders[$i]['driver_phone'])) {  ?>

                                                    <?php if ($userObj->hasPermission('view-providers')) { ?><a href="javascript:void(0);" onClick="show_driver_details('<?php echo $DBProcessingOrders[$i]['iDriverId']; ?>')" style="text-decoration: underline;"><?php } ?><?php echo clearName($DBProcessingOrders[$i]['driverName']); ?><?php if ($userObj->hasPermission('view-providers')) { ?></a> <?php } ?><br>
                                                    <?php }
                                                        if (!empty($DBProcessingOrders[$i]['driver_phone'])) {
                                                            echo clearPhone($DBProcessingOrders[$i]['driver_phone']);
                                                        }
                                                    }
                                            if ((('Yes' === $ENABLE_AUTO_ACCEPT_STORE_ORDER && 'Yes' === $eAutoaccept) || ('Yes' === $cancelDriverOrder && 'Yes' === $DBProcessingOrders[$i]['eCancelledbyDriver'])) && 'No' === $DBProcessingOrders[$i]['eTakeaway'] && 'Kiosk' !== $DBProcessingOrders[$i]['eOrderplaced_by']) {
                                                $currentdate = @date('Y-m-d H:i:s');
                                                $total_accept = $corder = $cabbook = 0;
                                                $checkdate = $tDate = '';
                                                $vCountry = $DBProcessingOrders[$i]['vCountry'];
                                                if ((2 === $iOrderStatusCode && $iDriverId <= 0) || ('Yes' === $cancelDriverOrder && 'Yes' === $DBProcessingOrders[$i]['eCancelledbyDriver'] && 4 === $iOrderStatusCode) || (1 === $iOrderStatusCode && 'Yes' === $DBProcessingOrders[$i]['eBuyAnyService'])) {
                                                    echo '<div class="clearfix"></div>';
                                                    if (isset($orderDataArr[$iOrderId])) {
                                                        $tDate = $orderDataArr[$iOrderId]['ttDate'];
                                                        $corder = $orderDataArr[$iOrderId]['corder'];
                                                        $total_accept = $orderDataArr[$iOrderId]['total_accept'];
                                                    }
                                                    $checkdate = date('Y-m-d H:i:s', strtotime('+'.$RIDER_REQUEST_ACCEPT_TIME.' seconds', strtotime($tDate)));
                                                    if (0 === $corder && $manualAssignDriver > 0) {
                                                        ?>
                                                                <button href="#" onclick="openDriverModal(this);"
                                                                        class="btn btn-info"
                                                                        data-country="<?php echo $vCountry; ?>"
                                                                        data-id="<?php echo $iOrderId; ?>" type="button"
                                                                        style="margin-top: 10px">Assign to
                                                                    the <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?></button>
                                                                <?php
                                                    } else {
                                                        $currentdate = date('Y-m-d H:i:s');
                                                        $time1 = strtotime($currentdate);
                                                        $time2 = strtotime($checkdate);
                                                        if (0 === $total_accept && $time1 <= $time2) {
                                                            ?>
                                                                    <button href="#" onclick="openDriverModal(this);"
                                                                            class="btn btn-info break-line"
                                                                            data-country="<?php echo $vCountry; ?>"
                                                                            data-id="<?php echo $iOrderId; ?>" type="button"
                                                                            style="margin-top: 10px">Please wait
                                                                        for <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?>
                                                                        accept request
                                                                    </button>
                                                                <?php } elseif (($manualAssignDriver > 0) || ('Yes' === $cancelDriverOrder && 'Yes' === $DBProcessingOrders[$i]['eCancelledbyDriver'] && 4 === $iOrderStatusCode)) { ?>
                                                                    <button href="#" onclick="openDriverModal(this);"
                                                                            class="btn btn-info"
                                                                            data-country="<?php echo $vCountry; ?>"
                                                                            data-id="<?php echo $iOrderId; ?>" type="button"
                                                                            style="margin-top: 10px">Assign to
                                                                        the <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?></button>
                                                                    <?php
                                                                }
                                                    }
                                                }
                                            }
                                            ?>
                                                    <?php // reassign driver - for that reset current driver
                                            if ('YES' === strtoupper($REASSIGN_DRIVER_AFTER_ACCEPTING_REQUEST)) {
                                                if (4 === $iOrderStatusCode && $iDriverId > 0 && 'No' === $DBProcessingOrders[$i]['eTakeaway'] && 'No' === $DBProcessingOrders[$i]['eCancelledbyDriver']) { ?>
                                                            <br>
                                <button href="#" onclick="openResetDriverTypeModal(this);"
                                class="btn btn-info"
                                data-country="<?php echo $vCountry; ?>"
                                data-id="<?php echo $iOrderId; ?>"
                                data-drivertype="<?php echo $DBProcessingOrders[$i]['eDriverOption']; ?>"
                                type="button"
                                style="margin-top: 10px"><?php echo $langage_lbl_admin['LBL_RESET_DRIVER']; ?></button>
                                                    <?php }
                                                if (2 === $iOrderStatusCode && 'No' === $DBProcessingOrders[$i]['eTakeaway'] && 'No' === $DBProcessingOrders[$i]['eCancelledbyDriver'] && 0 === $manualAssignDriver) { ?>
                                                            <br>
                                <button href="#" onclick="openDriverModal(this);"
                                class="btn btn-info"
                                data-country="<?php echo $vCountry; ?>"
                                data-id="<?php echo $iOrderId; ?>"
                                data-drivertype="<?php echo $DBProcessingOrders[$i]['eDriverOption']; ?>"
                                type="button" style="margin-top: 10px">Assign to
                                 the <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?></button>
                                                    <?php }
                                                } ?>
                                                </td>
                                                <td class="text-right"><?php echo formateNumAsPerCurrency($DBProcessingOrders[$i]['fTotalGenerateFare'], ''); ?></td>
                                                <!--<td class="text-center"><?php echo $DBProcessingOrders[$i]['vServiceName']; ?></td>-->
                                                <td class="text-center">
                                                    <?php if ('Yes' === $cancelDriverOrder && 'Yes' === $DBProcessingOrders[$i]['eCancelledbyDriver'] && 4 === $DBProcessingOrders[$i]['iStatusCode']) { ?>
                                                        <p><?php echo $langage_lbl_admin['LBL_CANCELLED_BY_DRIVER']; ?></p>
                                                        <button type="button" onclick="viewDriverCancelReason(this);"
                                                                class="btn btn-info"
                                                                data-reason="<?php echo $DBProcessingOrders[$i]['vCancelReasonDriver']; ?>"><?php echo $langage_lbl_admin['LBL_VIEW_REASON']; ?></button>
                                                    <?php } else { ?>
                                                        <?php echo $DBProcessingOrders[$i]['vStatus']; ?>
                                                        <?php if ($MODULES_OBJ->isEnableOTPVerificationDeliverAll() && 5 === $DBProcessingOrders[$i]['iStatusCode'] && 'Yes' === $DBProcessingOrders[$i]['eAskCodeToUser']) {
                                                            echo '<br><br>'.$langage_lbl['LBL_OTP_TXT'].' '.$DBProcessingOrders[$i]['vRandomCode'];
                                                        } ?>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-center"><?php echo $paymentType; ?></td>

                                                <?php if ($userObj->hasPermission(['cancel-processing-orders', 'view-order-invoice'])) { ?>


                                                <td class="text-center">
                                                    <?php if (in_array($DBProcessingOrders[$i]['iStatusCode'], $processing_status_array, true)) { ?>
                                                        <?php if ($userObj->hasPermission('cancel-processing-orders')) { ?>
                                                        <a href="#"
                                                           data-target="#delete_form<?php echo $DBProcessingOrders[$i]['iOrderId']; ?>"
                                                           class=" custom-order btn btn-info" data-toggle="modal"
                                                           data-id="<?php echo $DBProcessingOrders[$i]['iOrderId']; ?>">Cancel
                                                            Order
                                                        </a>
                                                        <div id="delete_form<?php echo $DBProcessingOrders[$i]['iOrderId']; ?>"
                                                             class="modal fade delete_form text-left" role="dialog">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close"
                                                                                data-dismiss="modal">x
                                                                        </button>
                                                                        <h4 class="modal-title">Cancel Order</h4>
                                                                    </div>
                                                                    <form role="form" name="delete_form"
                                                                          id="delete_form1" method="post" action=""
                                                                          class="margin0">
                                                                        <div class="modal-body">
                                                                            <div class="form-group"  style="display: inline-block;"> <!--col-lg-12-->
                                                                                <label class="col-lg-4 control-label">
                                                                                    Cancellation Reason
                                                                                    <span class="red">*</span>
                                                                                </label>
                                                                                <div class="col-lg-7">
                                                                                    <textarea name="cancel_reason"
                                                                                              id="cancel_reason<?php echo $DBProcessingOrders[$i]['iOrderId']; ?>"
                                                                                              rows="4" cols="40"
                                                                                              required="required"></textarea>
                                                                                    <div class="cnl_error error red"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group" style="display: inline-block;"> <!--col-lg-12-->
                                                                                <label class="col-lg-4 control-label">
                                                                                    Cancellation Charges To Apply For
                                                                                    User
                                                                                    <span class="red">*</span>
                                                                                </label>
                                                                                <div class="col-lg-8">
                                                                                    <input type="fCancellationCharge"
                                                                                           name="fCancellationCharge"
                                                                                           id="fCancellationCharge<?php echo $DBProcessingOrders[$i]['iOrderId']; ?>"
                                                                                           required="required"
                                                                                           value="<?php echo 12 === $DBProcessingOrders[$i]['iStatusCode'] ? 0 : $MIN_ORDER_CANCELLATION_CHARGES; ?>" <?php if (12 === $DBProcessingOrders[$i]['iStatusCode']) {
                                                                                               ?> disabled="disabled" <?php } ?> >
                                                                                    <div class="cancelcharge_error error red"></div>
                                                                                </div>
                                                                            </div>
                                                                            <!-- <div class="form-group col-lg-12" style="display: inline-block;">

                                                                            <label class="col-lg-4 control-label">Payment To Driver<span class="red">*</span></label>

                                                                            <?php $payment_to_driver = GetDriverPayment($DBProcessingOrders[$i]['iOrderId']); ?>

                                                                            <div class="col-lg-7"> -->

                                                                            <input type="hidden" name="fDeliveryCharge"
                                                                                   id="fDeliveryCharge"
                                                                                   value="<?php echo $payment_to_driver; ?>">
                                                                            <!-- <?php if (0 === $payment_to_driver) { ?>
                                                                        <?php } else { ?>
                                                                        <?php $DBProcessingOrders[$i]['driverName']; ?>
                                                                                           <?php } ?>
                                                                        </div>
                                                                        </div> -->
                                                                            <!-- <div class="form-group col-lg-12" style="display: inline-block;">
                                                                            <label class="col-lg-4 control-label">Payment To Restaurant<span class="red">*</span></label>-->
                                                                            <?php $payment_to_restaurant = GetStorePayment($DBProcessingOrders[$i]['iOrderId']); ?>
                                                                            <!-- <div class="col-lg-7">  -->
                                                                            <input type="hidden"
                                                                                   name="fRestaurantPayAmount"
                                                                                   id="fRestaurantPayAmount"
                                                                                   value="<?php echo $payment_to_restaurant; ?>">
                                                                            <!--  </div>
                                                                            </div> -->
                                                                            <div class="form-group col-lg-12 "> <!--col-md-offset-4-->
                                    <!-- <p>Order Subtotal : <?php echo formateNumAsPerCurrency($DBProcessingOrders[$i]['fSubTotal'], ''); ?></p>
                                    <p>Restaurant Discount :
                                    <?php echo formateNumAsPerCurrency($DBProcessingOrders[$i]['fOffersDiscount'], ''); ?>
                                    </p>
                                    <p>Site Commision :
                                    <?php echo formateNumAsPerCurrency($DBProcessingOrders[$i]['fCommision'], ''); ?></p>
                                    <p>Delivery Charge : <?php echo formateNumAsPerCurrency($DBProcessingOrders[$i]['fDeliveryCharge'], ''); ?>
                                    </p> -->
                                                                                <?php if ('No' === $DBProcessingOrders[$i]['eBuyAnyService']) { ?>
                                                                                    <p>
                                                                                        Expected <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?>
                                                                                        Payout:
                                                                                        <?php if ('12' === $DBProcessingOrders[$i]['iStatusCode']) {
                                                                                            echo ' -- ';
                                                                                        } else {
                                                                                            // chk here is statuscode 1 then store payout amt not shown..so in braces it shown store is not confirmed order
                                                                                            if (1 === $DBProcessingOrders[$i]['iStatusCode']) {
                                                                                                echo '- ('.$langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN'].' has not confirmed order)';
                                                                                            } else {
                                                                                                echo formateNumAsPerCurrency($payment_to_restaurant, '');
                                                                                            }
                                                                                        } ?></p>
                                                                                <?php } ?>
                                                                                <?php if ($payment_to_driver > 0) { ?>
                                                                                    <p>
                                                                                        Expected <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?>
                                                                                        Payout: <?php echo formateNumAsPerCurrency($payment_to_driver, ''); ?></p>
                                                                                <?php } ?>
                                                                                <p>Expected Site Commission:
                                                                                    <?php if ('12' === $DBProcessingOrders[$i]['iStatusCode']) {
                                                                                        echo ' -- ';
                                                                                    } else {
                                                                                        echo formateNumAsPerCurrency($DBProcessingOrders[$i]['fCommision'], '');
                                                                                    } ?></p>
                                                                            </div>
                                                                            <input type="hidden" name="hdn_del_id"
                                                                                   id="hdn_del_id"
                                                                                   value="<?php echo $DBProcessingOrders[$i]['iOrderId']; ?>">
                                                                            <input type="hidden" name="iUserId"
                                                                                   id="iUserId"
                                                                                   value="<?php echo $DBProcessingOrders[$i]['iUserId']; ?>">
                                                                            <input type="hidden" name="iDriverId"
                                                                                   id="iDriverId"
                                                                                   value="<?php echo $DBProcessingOrders[$i]['iDriverId']; ?>">
                                                                            <input type="hidden" name="iCompanyId"
                                                                                   id="iCompanyId"
                                                                                   value="<?php echo $DBProcessingOrders[$i]['iCompanyId']; ?>">
                                                                            <input type="hidden"
                                                                                   id="order_total<?php echo $DBProcessingOrders[$i]['iOrderId']; ?>"
                                                                                   value="<?php echo $DBProcessingOrders[$i]['fNetTotal']; ?>">
                                                                            <input type="hidden"
                                                                                   id="order_total_display<?php echo $DBProcessingOrders[$i]['iOrderId']; ?>"
                                                                                   value="<?php echo formateNumAsPerCurrency($DBProcessingOrders[$i]['fNetTotal'], ''); ?>">
                                                                            <input type="hidden" name="type" id="type"
                                                                                   value="<?php echo $order_type; ?>">
                                                                            <input type="hidden" name="action"
                                                                                   id="action" value="cancel">
                                                                            <div class="form-group col-lg-12">
                                                                                <label class="control-label">Notes:
                                                                                </label>
                                                                                <p>
                                                                                    1. Set the cancellation charges as
                                                                                    per the Order and Delivery status.
                                                                                    Also, the expected payouts shown
                                                                                    here are just for the your review to
                                                                                    check how much to pay if the order
                                                                                    will be delivered.
                                                                                </p>
                                                                                <p>2. If this order contains any wallet
                                                                                    settlement then wallet amount will
                                                                                    be refunded back
                                                                                    to <?php echo $langage_lbl_admin['LBL_RIDER']; ?>
                                                                                    's wallet as soon as you mark this
                                                                                    order as 'CANCEL'.
                                                                                </p>
                                                                                <p> 3. cancellation charges is not
                                                                                    applicable on status "Payment not
                                                                                    initiated"
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="submit" class="btn btn-info"
                                                                                    id="cnl_booking1"
                                                                                    onclick="return cancelBooking('<?php echo $DBProcessingOrders[$i]['iOrderId']; ?>');"
                                                                                    title="Cancel Booking">Cancel Order
                                                                            </button>
                                                                            <button type="button"
                                                                                    class="btn btn-default"
                                                                                    data-dismiss="modal"
                                                                                    id="close_model">Close
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                <!-- Modal content-->
                                                            </div>
                                                        </div>
                                                        <!-- Modal -->
                         <script>
                            $('#delete_form<?php echo $DBProcessingOrders[$i]['iOrderId']; ?>').on('show.bs.modal', function () {
                                $("#fCancellationCharge<?php echo $DBProcessingOrders[$i]['iOrderId']; ?>").val("<?php echo 12 === $DBProcessingOrders[$i]['iStatusCode'] ? 0 : $MIN_ORDER_CANCELLATION_CHARGES; ?>");
                                $("#fDeliveryCharge").val("<?php echo $payment_to_driver; ?>");
                                $("#fRestaurantPayAmount").val("<?php echo $payment_to_restaurant; ?>");

                                $(".cancelcharge_error").html("");
                                $(".cnl_error").html("");
                            });
                         </script>
                        <?php } else {
                            echo '--';
                        } ?>
                     <?php } else { ?>
                        <?php if ($userObj->hasPermission('view-order-invoice')) { ?>
            <a class="btn btn-primary"
            href="order_invoice.php?iOrderId=<?php echo $DBProcessingOrders[$i]['iOrderId']; ?>"
            target="_blank">
                                <i class="fa fa-th-list"></i>
                <b>View Invoice</b>
                            </a>
                        <?php } ?>

                     <?php } ?>

                 </td>
                                                <?php }  ?>
             </tr>
             <div class="clear"></div>
             <?php
                                        }
                                    } else {
                                        ?>
        <tr class="gradeA">
            <td colspan="10"> No Records Found.</td>
        </tr>
     <?php } ?>
                        </tbody>
                        </table>
                    </form>
                    <?php include 'pagination_n.php'; ?>
                </div>
            </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>
<div class="row loding-action" id="loaderIcon" style="display:none;">
    <div align="center">
        <img src="default.gif">
    </div>
</div>

<!--END MAIN WRAPPER -->

<div class="modal fade dddelete_form text-left" id="DriverResetTypeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h4 class="modal-title"><?php echo $langage_lbl_admin['LBL_RESET_DRIVER']; ?></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="iOrderIdResetType" id="iOrderIdResetType" value="" >
                <input type="hidden" name="vCountryResetType" id="vCountryResetType" value="" >
                <!--<input type="hidden" name="iCompanyIdManual" id="iCompanyIdManual" value="" >
                <input type="hidden" name="iGcmRegIdManual" id="iGcmRegIdManual" value="" >-->
                <div class="container">
                    <p><?php echo $langage_lbl_admin['LBL_RESET_PROVIDER_TXT']; ?></p>
                </div>
                <div class="form-group" id="resetconfirmshow" style="display: inline-block; margin-top: 20px;">
                   <input type="button" onclick="openResetDriverModal()" name="submit" value="Confirm"
           class="btn btn-primary" >
                   <input type="button" name="cancel" value="Cancel" class="btn btn-primary" data-dismiss="modal">
                </div>
                <div class="" id="resetconfirmhide" style="width:100%; display: none;">
                    <div align="center">
                        <img src="default.gif">
                        <span>Please Wait...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
                         <div id="assign_driver_modal" class="modal fade dddelete_form text-left" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">x</button>
                                        <h4 class="modal-title">Assign to the <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?></h4>
                                    </div>
                                    <div class="map-popup" style="display:none" id="driver_popup"></div>
                                    <div class="modal-body">
                                        <input type="hidden" name="iOrderId" id="iOrderIdManual" value="" >
                                        <input type="hidden" name="vCountry" id="vCountryManual" value="" >

                                            <div class="form-group col-lg-12">
                                                <span class="auto_assign001">
                                                    <input type="radio" name="eAutoAssign" id="eAutoAssign"
                            onclick="changedData('1');" checked
                            value="Yes" >&nbsp;Auto Assign <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?>
                                                </span>

                                                <label class="optional">Or</label>
                                                <span class="auto_assign001">
                        <input type="radio" name="eAutoAssign" onclick="changedData('2');"
                        id="eAutoAssign1"
                        value="No" >&nbsp;Manual Assign <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?>
                                                </span>
                                            </div>
                                            <div class="form-group col-lg-12" style="display: inline-block;">
                                                <p id="driverSet001"></p>
                        </span>
                                                <ul id="driver_main_list" class="order_list_d" style="display:none;">

                                                </ul>
                    <div class="" id="imageIcons" style="width:100%; display: none;">
                                                    <div align="center">
                                                        <img src="default.gif">
                                                        <span>Retrieving <?php echo $langage_lbl_admin['LBL_DIVER']; ?> list.Please Wait...</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="iDriverId" id="iDriverId" value="" class="form-control">

                                        <!--</form>-->
                                    </div>
                                    <div class="modal-footer" style="margin-top: 0; text-align: left;">
                                        <div class="nimot-class-but" style="margin-bottom: 0">
                                            <button type="button" class="btn btn-primary" id="send_req_btn" style="margin-left: 0 !important"
                        onclick="send_req_btn()">Send Request to
                        the <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                         </div>
                         <form name="pageForm" id="pageForm" action="" method="post" >
                            <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
                            <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
                            <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>" >
                            <input type="hidden" name="order" id="order" value="<?php echo $order; ?>" >
                            <input type="hidden" name="action" value="<?php echo $action; ?>" >
                            <input type="hidden" name="searchStore" value="<?php echo $searchStore; ?>" >
                            <input type="hidden" name="searchDriver" value="<?php echo $searchDriver; ?>" >
                            <input type="hidden" name="searchUser" value="<?php echo $searchUser; ?>" >
                            <input type="hidden" name="searchServiceType" value="<?php echo $searchServiceType; ?>" >
                            <input type="hidden" name="searchOrderStatus" value="<?php echo $searchOrderStatus; ?>" >
                            <input type="hidden" name="searchOrderNo" value="<?php echo $searchOrderNo; ?>" >
                            <input type="hidden" name="startDate" value="<?php echo $startDate; ?>" >
                            <input type="hidden" name="endDate" value="<?php echo $endDate; ?>" >
                            <input type="hidden" name="vStatus" value="<?php echo $vStatus; ?>" >
                            <input type="hidden" name="method" id="method" value="" >
                         </form>

<div data-backdrop="static" data-keyboard="false" class="modal fade" id="is_dltSngl_modal12" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Cancel Order ?</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure to Cancel this Order?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Not Now</button>
                <a class="btn btn-success btn-ok action_modal_submit">Yes</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade " id="detail_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4><i style="margin:2px 5px 0 2px;"><img src="images/rider-icon.png" alt=""></i><?php echo $langage_lbl_admin['LBL_RIDER']; ?> Details<button type="button" class="close" data-dismiss="modal">x</button>
                </h4>
            </div>
            <div class="modal-body" style="max-height: 450px;overflow: auto;">
                <div class = "imageIcons" id="imageIcons">
                    <div align="center">
                        <img src="default.gif"><br/>
                        <span>Retrieving details,please Wait...</span>
                    </div>
                </div>
                <div id="rider_detail"></div>
            </div>
        </div>
    </div>
</div>


<div  class="modal fade" id="detail_modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                    <i style="margin:2px 5px 0 2px;"><img src="images/icon/driver-icon.png" alt=""></i><?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> Details<button type="button" class="close" data-dismiss="modal">x</button>
                </h4>
            </div>
            <div class="modal-body" style="max-height: 450px;overflow: auto;">
                <div id="imageIcons1" style="display:none">
                    <div align="center">
                        <img src="default.gif"><br/>
                        <span>Retrieving details,please Wait...</span>
                    </div>
                </div>
                <div id="driver_detail"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="detail_modal4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                    <i aria-hidden="true" class="fa fa-building-o" style="margin:2px 5px 0 2px;"></i><?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?> Details <button type="button" class="close" data-dismiss="modal">x</button>
                </h4>
            </div>
            <div class="modal-body" style="max-height: 450px;overflow: auto;">
                <div id="imageIcons4" style="display:none">
                    <div align="center">
                        <img src="default.gif">
                        <br/>
                        <span>Retrieving details,please Wait...</span>
                    </div>
                </div>
                <div id="store_detail"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade " id="detail_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4><i style="margin:2px 5px 0 2px;"><img src="images/rider-icon.png" alt=""></i><?php echo $langage_lbl_admin['LBL_RIDER']; ?> Details<button type="button" class="close" data-dismiss="modal">x</button>
                </h4>
            </div>
            <div class="modal-body" style="max-height: 450px;overflow: auto;">
                <div id="imageIcons">
                    <div align="center">
                        <img src="default.gif"><br/>
                        <span>Retrieving details,please Wait...</span>
                    </div>
                </div>
                <div id="rider_detail"></div>
            </div>
        </div>
    </div>
</div>


<div  class="modal fade" id="detail_modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                    <i style="margin:2px 5px 0 2px;"><img src="images/icon/driver-icon.png" alt=""></i><?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> Details<button type="button" class="close" data-dismiss="modal">x</button>
                </h4>
            </div>
            <div class="modal-body" style="max-height: 450px;overflow: auto;">
                <div id="imageIcons1" style="display:none">
                    <div align="center">
                        <img src="default.gif"><br/>
                        <span>Retrieving details,please Wait...</span>
                    </div>
                </div>
                <div id="driver_detail"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="detail_modal4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                    <i aria-hidden="true" class="fa fa-building-o" style="margin:2px 5px 0 2px;"></i><?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?> Details <button type="button" class="close" data-dismiss="modal">x</button>
                </h4>
            </div>
            <div class="modal-body" style="max-height: 450px;overflow: auto;">
                <div id="imageIcons4" style="display:none">
                    <div align="center">
                        <img src="default.gif">
                        <br/>
                        <span>Retrieving details,please Wait...</span>
                    </div>
                </div>
                <div id="store_detail"></div>
            </div>
        </div>
    </div>
</div>
<?php include_once 'functions.php'; ?>
<?php include_once 'footer.php'; ?>
                         <link rel="stylesheet" href="../assets/plugins/datepicker/css/datepicker.css" />
                         <link rel="stylesheet" href="css/select2/select2.min.css" />
                         <script src="js/plugins/select2.min.js"></script>
                         <script src="../assets/js/jquery-ui.min.js"></script>
                         <script src="../assets/plugins/datepicker/js/bootstrap-datepicker.js"></script>
                         <script src="../assets/js/modal_alert.js"></script>
                         <script>
                            function setDriverListing(vCountry, orderId) {
                                iVehicleTypeId = '';
                                keyword = '';
                                eLadiesRide = 'No';
                                eHandicaps = 'No';
                                eType = '';
        $('#imageIcons').show();
                                $('#driver_main_list').html("");

                                var ajaxData = {
                                    'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>get_available_driver_list_order.php',
                                    'AJAX_DATA': {
                                    vCountry: vCountry,
                                    type: '',
                                    iVehicleTypeId: iVehicleTypeId,
                                    keyword: keyword,
                                    eLadiesRide: eLadiesRide,
                                    eHandicaps: eHandicaps,
                                    AppeType: eType,
                                    orderId: orderId
                                    },
                                    'REQUEST_DATA_TYPE': 'json'
                                };
                                getDataFromAjaxCall(ajaxData, function(response) {
                                    if(response.action == "1") {
                                        var dataHtml2 = response.result;
                $('#imageIcons').hide();
                                        $('#driver_main_list').html('');
                                        if(dataHtml2.Action == 1) {
                                            if (dataHtml2.message != "") {
                                                $('#driver_main_list').show();
                                                $('#driver_main_list').html(dataHtml2.message);
                                                if ($("#eAutoAssign").is(':checked')) {
                                                            //$("input:radio").attr('disabled', 'disabled');
                                                }
                                            }

                                            $('#send_req_btn').prop('disabled', false);
                } else {
                                            $('#driver_main_list').html('<h4 style="margin:25px 0 0 15px"><?php echo $langage_lbl_admin['LBL_NO_DRIVERS_FOUND']; ?></h4>');
                                            $('#driver_main_list').show();
                                            $('#send_req_btn').prop('disabled', true);
                                        }
            } else {
                                        console.log(response.result);
                                    }
                                });
                            }
                            function openDriverModal(elem) {
                                var radioValue = $("input[name='eAutoAssign']:checked").val();
                                if (radioValue == "No") {
                                    $("#eAutoAssign1").prop("checked", false);
                                    $("#eAutoAssign").prop("checked", true);
                                    changedData("1");
                                }
                                var orderId = $(elem).attr("data-id");
                                var country = $(elem).attr("data-country");
                                $("#iOrderIdManual").val(orderId);
                                $("#vCountryManual").val(country);
                                $('#assign_driver_modal').modal({
                                    show: 'true'
                                });
                            }
                            function changedData(type) {
                                var country = $("#vCountryManual").val();
                                var orderId = $("#iOrderIdManual").val();
                                if (type == "1") {
                                    $("#driver_main_list").hide();
                                    $("#driverSet001").hide();
                                    $('#send_req_btn').prop('disabled', false);
                                } else {
                                    $("#driver_main_list").show();
                                    $("#driverSet001").show();
                                    $('#send_req_btn').prop('disabled', true);
                                    setDriverListing(country, orderId);
                                }
                            }
                            $('#dp4').datepicker()
                            .on('changeDate', function (ev) {
                                if (ev.date.valueOf() < endDate.valueOf()) {
                                    $('#alert').show().find('strong').text('The start date can not be greater then the end date');
                                } else {
                                    $('#alert').hide();
                                    startDate = new Date(ev.date);
                                    $('#startDate').text($('#dp4').data('date'));
                                }
                                $('#dp4').datepicker('hide');
                            });
                            $('#dp5').datepicker()
                            .on('changeDate', function (ev) {
                                if (ev.date.valueOf() < startDate.valueOf()) {
                                    $('#alert').show().find('strong').text('The end date can not be less then the start date');
                                } else {
                                    $('#alert').hide();
                                    endDate = new Date(ev.date);
                                    $('#endDate').text($('#dp5').data('date'));
                                }
                                $('#dp5').datepicker('hide');
                            });

                            $(document).ready(function () {
                                if ('<?php echo $startDate; ?>' != '') {
                                    $("#dp4").val('<?php echo $startDate; ?>');
                                    $("#dp4").datepicker('update', '<?php echo $startDate; ?>');
                                }
                                if ('<?php echo $endDate; ?>' != '') {
                                    $("#dp5").datepicker('update', '<?php echo $endDate; ?>');
                                    $("#dp5").val('<?php echo $endDate; ?>');
                                }

                            });

                            function setRideStatus(actionStatus) {
                                window.location.href = "trip.php?type=" + actionStatus;
                            }
                            function todayDate() {
                                $("#dp4").val('<?php echo $Today; ?>');
                                $("#dp5").val('<?php echo $Today; ?>');
                            }
                            function reset() {
                                location.reload();

                            }
                            function yesterdayDate()   {
                                $("#dp4").val('<?php echo $Yesterday; ?>');
                                $("#dp4").datepicker('update', '<?php echo $Yesterday; ?>');
                                $("#dp5").datepicker('update', '<?php echo $Yesterday; ?>');
                                $("#dp4").change();
                                $("#dp5").change();
                                $("#dp5").val('<?php echo $Yesterday; ?>');
                            }
                            function currentweekDate(dt, df)  {
                                $("#dp4").val('<?php echo $monday; ?>');
                                $("#dp4").datepicker('update', '<?php echo $monday; ?>');
                                $("#dp5").datepicker('update', '<?php echo $sunday; ?>');
                                $("#dp5").val('<?php echo $sunday; ?>');
                            }
                            function previousweekDate(dt, df)   {
                                $("#dp4").val('<?php echo $Pmonday; ?>');
                                $("#dp4").datepicker('update', '<?php echo $Pmonday; ?>');
                                $("#dp5").datepicker('update', '<?php echo $Psunday; ?>');
                                $("#dp5").val('<?php echo $Psunday; ?>');
                            }
                            function currentmonthDate(dt, df)    {
                                $("#dp4").val('<?php echo $currmonthFDate; ?>');
                                $("#dp4").datepicker('update', '<?php echo $currmonthFDate; ?>');
                                $("#dp5").datepicker('update', '<?php echo $currmonthTDate; ?>');
                                $("#dp5").val('<?php echo $currmonthTDate; ?>');
                            }
                            function previousmonthDate(dt, df)  {
                                $("#dp4").val('<?php echo $prevmonthFDate; ?>');
                                $("#dp4").datepicker('update', '<?php echo $prevmonthFDate; ?>');
                                $("#dp5").datepicker('update', '<?php echo $prevmonthTDate; ?>');
                                $("#dp5").val('<?php echo $prevmonthTDate; ?>');
                            }
                            function currentyearDate(dt, df)  {
                                $("#dp4").val('<?php echo $curryearFDate; ?>');
                                $("#dp4").datepicker('update', '<?php echo $curryearFDate; ?>');
                                $("#dp5").datepicker('update', '<?php echo $curryearTDate; ?>');
                                $("#dp5").val('<?php echo $curryearTDate; ?>');
                            }
                            function previousyearDate(dt, df)  {
                                $("#dp4").val('<?php echo $prevyearFDate; ?>');
                                $("#dp4").datepicker('update', '<?php echo $prevyearFDate; ?>');
                                $("#dp5").datepicker('update', '<?php echo $prevyearTDate; ?>');
                                $("#dp5").val('<?php echo $prevyearTDate; ?>');
                            }
                            $("#Search").on('click', function () {
                                if ($("#dp5").val() < $("#dp4").val()) {
                                    alert("From date should be lesser than To date.")
                                    return false;
                                } else {
                                    var action = $("#_list_form").attr('action');
                                    var formValus = $("#frmsearch").serialize();
                                    window.location.href = action + "?" + formValus;
                                }
                            });
                            $(function () {
                                $("select.filter-by-text-search").each(function () {
                                    $(this).select2({
                                        placeholder: $(this).attr('data-text'),
                                        allowClear: true
                                                                    }); //theme: 'classic'
                                });
                            });
                            </script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.custom-order').on('click', function () {
            var order_id = $(this).data('id');
            (function () {
                var template = null
                $('#delete_form' + order_id).on('show.bs.modal', function (event) {
                    if (template == null) {
                        template = $(this).html()
                    } else {
                        $(this).html(template)
                    }
                });
            })();
        });
    });


function cancelBooking(orderId) {
    var cancel_reason = $('#cancel_reason' + orderId).val();
    var cancelcharge = $('#fCancellationCharge' + orderId).val();
    var order_total = $('#order_total' + orderId).val();
    var order_total_display = $('#order_total_display' + orderId).val();
    if (cancel_reason == '') {
        $(".cnl_error").html("This Field is required.");
        return false;
    } else if (cancelcharge == '') {
        $(".cancelcharge_error").html("This Field is required.");
        return false;
    } else if (parseFloat(cancelcharge) > parseFloat(order_total)) {
        $(".cancelcharge_error").html("Cancellation charge cannot be greater than total order amount (" + order_total_display + ").");
        return false;
    } else {
        var confierm = confirm("Are you sure to Cancel this Order?");
        if (confierm == true) {
            $(".loader-default").show(); // Added By HJ On 20-08-2019 For Display Loader when cancel order
            $(".cnl_error").html("");
            $(".cancelcharge_error").html("");
            $("#delete_form" + orderId).submit();
        } else {
            return false;
        }
    }
}

$('body').on('keyup', '.select2-search__field', function () {
    $(".select2-container .select2-dropdown .select2-results .select2-results__options").addClass("hideoptions");
    if ($(".select2-results__options").is(".select2-results__message")) {
        $(".select2-container .select2-dropdown .select2-results .select2-results__options").removeClass("hideoptions");
    }
});

function formatDesign(item) {
    $(".select2-container .select2-dropdown .select2-results .select2-results__options").removeClass("hideoptions");
    if (!item.id) {
        return item.text;
    }
    //console.log(item);
    var selectionText = item.text.split("--");
    if (selectionText[2] != null && selectionText[1] != null) {
        var $returnString = $('<span>' + selectionText[0] + '</br>' + selectionText[1] + "</br>" + selectionText[2] + '</span>');
    } else if (selectionText[2] == null && selectionText[1] != null) {
        var $returnString = $('<span>' + selectionText[0] + '</br>' + selectionText[1] + '</span>');
    } else if (selectionText[2] != null && selectionText[1] == null) {
        var $returnString = $('<span>' + selectionText[0] + '</br>' + selectionText[2] + '</span>');
    }
    //$(".select2-container .select2-dropdown .select2-results .select2-results__options").removeClass("hideoptions");
    return $returnString;
}

function formatDesignnew(item) {
    if (!item.id) {
        return item.text;
    }
    var selectionText = item.text.split("--");
    return selectionText[0];
}


function send_req_btn() {
    radioValue = $("input[name='eAutoAssign']:checked").val();
    if (radioValue == 'No') {
        driverid = $("#assign_driver_modal #iDriverId").val();
        if (driverid == "") {
            alert("<?php echo $langage_lbl_admin['LBL_MANUAL_STORE_VALIDATION_ATLEAST_ONE'].' '.$langage_lbl_admin['LBL_DRIVER_TXT']; ?>");
            return false;
        }
    }
    $("#loaderIcon").show();

    var ajaxData = {
        'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>get_available_driver_list_order.php',
        'AJAX_DATA': {orderId: $("#iOrderIdManual").val(), requestsent: 1},
        'REQUEST_DATA_TYPE': 'json'
    };
    getDataFromAjaxCall(ajaxData, function (response) {
        if (response.action == "1") {
            var dataHtml2 = response.result;
            $("#loaderIcon").hide();
            sendrequesttodriver(dataHtml2);
        } else {
            console.log(response.result);
            $("#loaderIcon").hide();
        }
    });
}

    function sendrequesttodriver(dataHtml2) {
        radioValue = $("input[name='eAutoAssign']:checked").val();
        if (radioValue=='No') {
            driverid = $("#assign_driver_modal #iDriverId").val();
        } else {
            driverid = '';
        }

        var sendrequestparam = {
            "tSessionId": dataHtml2.tSessionId,
            "GeneralMemberId": dataHtml2.GeneralMemberId,
            "GeneralUserType": 'Passenger',
            "type": 'sendRequestToDrivers',
            "iOrderId": $("#iOrderIdManual").val(),
            "eSystem": dataHtml2.eSystem,
            "vDeviceToken": dataHtml2.vDeviceToken,
            "iDriverIdWeb": driverid,
            "isFromAdmin": "Yes",
            "async_request": false
        };
        sendrequestparam = $.param(sendrequestparam);
        $("#loaderIcon").show();

        getDataFromApi(sendrequestparam, function(response) {
            var response = JSON.parse(response);

            if (response.action == "1") {
                var responseData = response.result;
                $("#loaderIcon").hide();
                if (responseData.Action == '1') {

                    alert("<?php echo $langage_lbl['LBL_REQUEST_SENT_TO_PROVIDER']; ?>");
                    $('#assign_driver_modal').modal('hide');
                } else {

                    alert("<?php echo $langage_lbl['LBL_NO_DRIVERS_FOUND']; ?>");

                    $("#request-loader001").hide();

                    $("#requ_title").hide();
                }
            } else {
                //console.log(response.result);
                $("#loaderIcon").hide();
            }
        });
    }

    function putDriverId(driverid) {
        $("#assign_driver_modal #iDriverId").val(driverid);
    }

    $(function () {
        $("select.filter-by-text#searchDriver").each(function () {
            $(this).select2({
                allowClear: true,
                placeholder: $(this).attr('data-text'),
                // minimumInputLength: 2,
                templateResult: formatDesign,
                templateSelection: formatDesignnew,
                ajax: {
                    url: 'ajax_getdriver_detail_search.php',
                    dataType: "json",
                    type: "POST",
                    async: true,
                    delay: 250,
                    // quietMillis:100,
                    data: function (params) {
                        // console.log(params);
                        var queryParameters = {
                            term: params.term,
                            page: params.page || 1,
                            usertype: 'Driver',
                            //company_id:$('#searchStore option:selected').val()
                        }
                        //console.log(queryParameters);
                        return queryParameters;
                    },
                    processResults: function (data, params) {
                        //console.log(data);
                        params.page = params.page || 1;

                        if (data.length < 10) {
                            var more = false;
                        } else {
                            var more = (params.page * 10) <= data[0].total_count;
                        }

                        $(".select2-container .select2-dropdown .select2-results .select2-results__options").removeClass("hideoptions");

                        return {
                            results: $.map(data, function (item) {

                                if (item.Phoneno != '' && item.vEmail != '') {
                                    var textdata = item.fullName + "--" + "Email: " + item.vEmail + "--" + "Phone: +" + item.Phoneno;
                                } else if (item.Phoneno == '' && item.vEmail != '') {
                                    var textdata = item.fullName + "--" + "Email: " + item.vEmail;
                                } else if (item.Phoneno != '' && item.vEmail == '') {
                                    var textdata = item.fullName + "--" + "Phone: +" + item.Phoneno;
                                }
                                return {
                                    text: textdata,
                                    id: item.id
                                }
                            }),
                            pagination: {
                                more: more
                            }
                        };

                    },
                    cache: false
                }
            }); //theme: 'classic'
        });
    });

    $(function () {
        $("select.filter-by-text#searchUser").each(function () {
            $(this).select2({
                allowClear: true,
                placeholder: $(this).attr('data-text'),
                // minimumInputLength: 2,
                templateResult: formatDesign,
                templateSelection: formatDesignnew,
                ajax: {
                    url: 'ajax_getdriver_detail_search.php',
                    dataType: "json",
                    type: "POST",
                    async: true,
                    delay: 250,
                    // quietMillis:100,
                    data: function (params) {
                        // console.log(params);
                        var queryParameters = {
                            term: params.term,
                            page: params.page || 1,
                            usertype: 'Rider'
                        }
                        //console.log(queryParameters);
                        return queryParameters;
                    },
                    processResults: function (data, params) {
                        //console.log(data);
                        params.page = params.page || 1;

                        if (data.length < 10) {
                            var more = false;
                        } else {
                            var more = (params.page * 10) <= data[0].total_count;
                        }

                        $(".select2-container .select2-dropdown .select2-results .select2-results__options").removeClass("hideoptions");

                        return {
                            results: $.map(data, function (item) {

                                if (item.Phoneno != '' && item.vEmail != '') {
                                    var textdata = item.fullName + "--" + "Email: " + item.vEmail + "--" + "Phone: +" + item.Phoneno;
                                } else if (item.Phoneno == '' && item.vEmail != '') {
                                    var textdata = item.fullName + "--" + "Email: " + item.vEmail;
                                } else if (item.Phoneno != '' && item.vEmail == '') {
                                    var textdata = item.fullName + "--" + "Phone: +" + item.Phoneno;
                                }
                                return {
                                    text: textdata,
                                    id: item.id
                                }
                            }),
                            pagination: {
                                more: more
                            }
                        };

                    },
                    cache: false
                }
            }); //theme: 'classic'
        });
    });

    $(function () {
        $("select.filter-by-text#searchStore").each(function () {
            $(this).select2({
                allowClear: true,
                placeholder: $(this).attr('data-text'),
                // minimumInputLength: 2,
                templateResult: formatDesign,
                templateSelection: formatDesignnew,
                ajax: {
                    url: 'ajax_getdriver_detail_search.php',
                    dataType: "json",
                    type: "POST",
                    async: true,
                    delay: 250,
                    // quietMillis:100,
                    data: function (params) {
                        // console.log(params);
                        var queryParameters = {
                            term: params.term,
                            page: params.page || 1,
                            usertype: 'Store'
                        }
                        //console.log(queryParameters);
                        return queryParameters;
                    },
                    processResults: function (data, params) {
                        //console.log(data);
                        params.page = params.page || 1;

                        if (data.length < 10) {
                            var more = false;
                        } else {
                            var more = (params.page * 10) <= data[0].total_count;
                        }

                        $(".select2-container .select2-dropdown .select2-results .select2-results__options").removeClass("hideoptions");

                        return {
                            results: $.map(data, function (item) {

                                if (item.Phoneno != '' && item.vEmail != '') {
                                    var textdata = item.fullName + "--" + "Email: " + item.vEmail + "--" + "Phone: +" + item.Phoneno;
                                } else if (item.Phoneno == '' && item.vEmail != '') {
                                    var textdata = item.fullName + "--" + "Email: " + item.vEmail;
                                } else if (item.Phoneno != '' && item.vEmail == '') {
                                    var textdata = item.fullName + "--" + "Phone: +" + item.Phoneno;
                                }
                                return {
                                    text: textdata,
                                    id: item.id
                                }
                            }),
                            pagination: {
                                more: more
                            }
                        };

                    },
                    cache: false
                }
            }); //theme: 'classic'
        });
    });
    // Fetch the preselected item, and add to the control
    var sId = '<?php echo $searchDriver; ?>';
    var sSelect = $('select.filter-by-text#searchDriver');
    var sIdRider = '<?php echo $searchUser; ?>';
    var sSelectRider = $('select.filter-by-text#searchUser');
    var sIdCompany = '<?php echo $searchStore; ?>';
    var sSelectCompany = $('select.filter-by-text#searchStore');
    var itemname;
    var itemid;
    if (sId != '') {

        var ajaxData = {
            'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_getdriver_detail_search.php?id=' + sId + '&usertype=Driver',
            'AJAX_DATA': "",
            'REQUEST_DATA_TYPE': 'json'
        };
        getDataFromAjaxCall(ajaxData, function (response) {
            if (response.action == "1") {
                var data = response.result;
                $.map(data, function (item) {
                    if (item.Phoneno != '' && item.vEmail != '') {
                        var textdata = item.fullName + "--" + "Email: " + item.vEmail + "--" + "Phone: +" + item.Phoneno;
                    } else if (item.Phoneno == '' && item.vEmail != '') {
                        var textdata = item.fullName + "--" + "Email: " + item.vEmail;
                    } else if (item.Phoneno != '' && item.vEmail == '') {
                        var textdata = item.fullName + "--" + "Phone: +" + item.Phoneno;
                    }
                    var textdata = item.fullName;
                    itemname = textdata;
                    itemid = item.id;
                });
                var option = new Option(itemname, itemid, true, true);
                sSelect.append(option).trigger('change');
            } else {
                console.log(response.result);
            }
        });
    }

    if (sIdRider != '') {

        var ajaxData = {
            'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_getdriver_detail_search.php?id=' + sIdRider + '&usertype=Rider',
            'AJAX_DATA': "",
            'REQUEST_DATA_TYPE': 'json'
        };
        getDataFromAjaxCall(ajaxData, function (response) {
            if (response.action == "1") {
                var data = response.result;
                $.map(data, function (item) {
                    if (item.Phoneno != '' && item.vEmail != '') {
                        var textdata = item.fullName + "--" + "Email: " + item.vEmail + "--" + "Phone: +" + item.Phoneno;
                    } else if (item.Phoneno == '' && item.vEmail != '') {
                        var textdata = item.fullName + "--" + "Email: " + item.vEmail;
                    } else if (item.Phoneno != '' && item.vEmail == '') {
                        var textdata = item.fullName + "--" + "Phone: +" + item.Phoneno;
                    }
                    var textdata = item.fullName;
                    itemname = textdata;
                    itemid = item.id;
                });
                var option = new Option(itemname, itemid, true, true);
                sSelectRider.append(option).trigger('change');
            } else {
                console.log(response.result);
            }
        });
    }

    if (sIdCompany != '') {

        var ajaxData = {
            'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_getdriver_detail_search.php?id=' + sIdCompany + '&usertype=Store',
            'AJAX_DATA': "",
            'REQUEST_DATA_TYPE': 'json'
        };
        getDataFromAjaxCall(ajaxData, function (response) {
            if (response.action == "1") {
                var data = response.result;
                $.map(data, function (item) {
                    if (item.Phoneno != '' && item.vEmail != '') {
                        var textdata = item.fullName + "--" + "Email: " + item.vEmail + "--" + "Phone: +" + item.Phoneno;
                    } else if (item.Phoneno == '' && item.vEmail != '') {
                        var textdata = item.fullName + "--" + "Email: " + item.vEmail;
                    } else if (item.Phoneno != '' && item.vEmail == '') {
                        var textdata = item.fullName + "--" + "Phone: +" + item.Phoneno;
                    }
                    var textdata = item.fullName;
                    itemname = textdata;
                    itemid = item.id;
                });
                var option = new Option(itemname, itemid, true, true);
                sSelectCompany.append(option);
            } else {
                console.log(response.result);
            }
        });
    }

    function viewDriverCancelReason(elem) {
        var CancelReasonDriver = '<strong><?php echo $langage_lbl_admin['LBL_CANCEL_REASON']; ?>: </strong>' + $(elem).data('reason');
        show_alert("", CancelReasonDriver, "", "", "<?php echo $langage_lbl_admin['LBL_BTN_OK_TXT']; ?>");
    }

    function viewCancelOrderDrivers(elem) {

        var ajaxData = {
            'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>get_available_driver_list_order.php',
            'AJAX_DATA': {cancelOrderDriver: 1, orderId: $(elem).data('id')},
            'REQUEST_DATA_TYPE': 'json'
        };
        getDataFromAjaxCall(ajaxData, function (response) {
            if (response.action == "1") {
                var dataHtml2 = response.result;
                if (dataHtml2.Action == 1) {
                    if (dataHtml2.message != "") {
                        show_alert("<?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> Details", dataHtml2.message, "", "", "<?php echo $langage_lbl_admin['LBL_BTN_OK_TXT']; ?>", undefined, true, true, true);
                    }
                } else {
                    show_alert("", dataHtml2.message, "", "", "<?php echo $langage_lbl_admin['LBL_BTN_OK_TXT']; ?>");
                }
            } else {
                console.log(response.result);
            }
        });
    }

    //reassign driver - for that reset current driver
    function openResetDriverTypeModal(elem) {
        var orderId = $(elem).attr("data-id");
        var country = $(elem).attr("data-country");
        var drivertypesel = $(elem).attr("data-drivertype");
        $('#DriverResetTypeModal').modal({
            show: 'true'
        });
        $("#iOrderIdResetType").val(orderId);
        $("#vCountryResetType").val(country);
    }

    function openResetDriverModal() {
        var orderId = $("#iOrderIdResetType").val();
        var vCountry = $("#vCountryResetType").val();
        $('#resetconfirmshow').hide();
        $('#resetconfirmhide').show();

        var resetreqparam = {
            "tSessionId": "<?php echo $driversessionid; ?>",
            "GeneralMemberId": "<?php echo $driverId; ?>",
            "GeneralUserType": 'Driver',
            "type": 'cancelDriverOrder',
            "iOrderId": orderId,
            "eSystem": "DeliverAll",
            "isFromAdmin": "Yes",
            "async_request": false
        };
        resetreqparam = $.param(resetreqparam);
        //$("#loaderIcon").show();

        getDataFromApi(resetreqparam, function(response) {
            var response = JSON.parse(response);
            //response_data = response.result;
            $('#resetconfirmshow').show();
            $('#resetconfirmhide').hide();
            if (response.Action == 1) {
                location.reload();
            } else {
                $('#resetconfirmshow').show();
                $('#resetconfirmhide').hide();
            }
        });
    }
</script>
</body>
<!-- END BODY-->
</html>