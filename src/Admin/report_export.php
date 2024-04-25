<?php



include_once '../common.php';
$section = $_REQUEST['section'] ?? '';
$sortby = $_REQUEST['sortby'] ?? 0;
$order = $_REQUEST['order'] ?? '';
$startDate = $_REQUEST['startDate'] ?? '';
$endDate = $_REQUEST['endDate'] ?? '';
$iCompanyId = $_REQUEST['searchCompany'] ?? '';
$iDriverId = $_REQUEST['searchDriver'] ?? '';
$iUserId = $_REQUEST['searchRider'] ?? '';
$serachTripNo = $_REQUEST['serachTripNo'] ?? '';
$vTripPaymentMode = $_REQUEST['searchPaymentType'] ?? '';
$eDriverPaymentStatus = $_REQUEST['searchDriverPayment'] ?? '';
$promocode = $_REQUEST['promocode'] ?? '';
$ssql = $header = '';
$time = time();
$hotelPanel = $MODULES_OBJ->isEnableHotelPanel();
$kioskPanel = $MODULES_OBJ->isEnableKioskPanel();
function cleanData(&$str): void
{
    $str = preg_replace("/\t/", '\\t', $str);
    $str = preg_replace("/\r?\n/", '\\n', $str);
    if (strstr($str, '"')) {
        $str = '"'.str_replace('"', '""', $str).'"';
    }
}

if ('outstanding_amount' === $section) {
    function cleanNumber($num)
    {
        return str_replace(',', '', $num);
    }

    // data for select fields
    // Start Sorting
    $sortby = $_REQUEST['sortby'] ?? 0;
    $order = $_REQUEST['order'] ?? '';
    $ord = ' ORDER BY tr.iTripId DESC';
    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY ru.vName ASC';
        } else {
            $ord = ' ORDER BY ru.vName DESC';
        }
    }
    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY tr.iFare ASC, o.fSubTotal ASC';
        } else {
            $ord = ' ORDER BY tr.iFare DESC, o.fSubTotal DESC';
        }
    }
    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY toa.fPendingAmount ASC';
        } else {
            $ord = ' ORDER BY toa.fPendingAmount DESC';
        }
    }
    // End Sorting
    // Start Search Parameters
    $ssql = '';
    $ssqlsearchSettle = '';
    $action = $_REQUEST['action'] ?? '';
    $searchRider = $_REQUEST['searchRider'] ?? '';
    $eType = $_REQUEST['eType'] ?? '';
    $searchSettleUnsettle = $_REQUEST['searchSettleUnsettle'] ?? '';
    $searchSettleUnsettlePagination = $searchSettleUnsettle;
    $ssql = '';
    if ('1' === $searchSettleUnsettle) {
        $ssql1 = " AND toa.ePaidByPassenger ='No' ";
    } elseif ('0' === $searchSettleUnsettle) {
        $ssql1 = " AND toa.ePaidByPassenger ='Yes' ";
    }
    if ('search' === $action) {
        if ('' !== $searchRider) {
            $ssql .= " AND toa.iUserId ='".$searchRider."'";
        }
    }
    $trp_ssql = ' ORDER BY riderName ASC';
    if ('1' === $searchSettleUnsettle) {
        $sql = "SELECT toa.*,concat(ru.vName,' ',ru.vLastName) as riderName,ru.vCurrencyPassenger as userCurrency, cur.Ratio as currencyRatio, toa.iUserId, SUM(toa.fPendingAmount) as allSum, (SELECT SUM(toa1.fPendingAmount) FROM trip_outstanding_amount as toa1 WHERE toa1.iUserId=toa.iUserId AND toa1.vTripPaymentMode != 'Organization' AND toa1.ePaidByPassenger ='No' AND toa1.iUserId != '') as Remaining, concat(ru.vName,' ',ru.vLastName) as riderName from trip_outstanding_amount AS toa LEFT JOIN register_user AS ru ON toa.iUserId = ru.iUserId LEFT JOIN currency as cur ON cur.vName=ru.vCurrencyPassenger WHERE toa.iUserId > 0 AND toa.iUserId != '' AND toa.vTripPaymentMode != 'Organization' AND ru.vName!='NULL' {$ssql} GROUP BY toa.iUserId   HAVING remaining >0 {$trp_ssql}";
    } elseif ('0' === $searchSettleUnsettle) {
        $sql = "SELECT toa.*,concat(ru.vName,' ',ru.vLastName) as riderName,ru.vCurrencyPassenger as userCurrency,cur.Ratio as currencyRatio, SUM(toa.fPendingAmount) AS allSum, (SUM(toa.fPendingAmount)-(SELECT SUM(toa1.fPendingAmount) FROM trip_outstanding_amount as toa1 WHERE toa1.iUserId=toa.iUserId AND toa1.vTripPaymentMode != 'Organization' AND toa1.ePaidByPassenger ='Yes' AND toa1.iUserId != ''))as Remaining, (SELECT SUM(toa1.fPendingAmount) FROM trip_outstanding_amount as toa1 WHERE toa1.iUserId=toa.iUserId AND toa1.vTripPaymentMode != 'Organization' AND toa1.ePaidByPassenger ='Yes' AND toa1.iUserId != '') as PaidData,concat(ru.vName,' ',ru.vLastName) as riderName from trip_outstanding_amount AS toa LEFT JOIN register_user AS ru ON toa.iUserId = ru.iUserId LEFT JOIN currency as cur ON cur.vName=ru.vCurrencyPassenger WHERE toa.iUserId > 0 AND toa.iUserId != '' AND toa.vTripPaymentMode != 'Organization' AND ru.vName!='NULL' {$ssql} GROUP BY toa.iUserId HAVING allSum=PaidData {$trp_ssql}";
    } else {
        $sql = "SELECT toa.iUserId,COUNT(toa.iTripOutstandId) AS Total,ru.vCurrencyPassenger as userCurrency,cur.Ratio as currencyRatio, SUM(toa.fPendingAmount) AS allSum,(SUM(toa.fPendingAmount)-(SELECT (CASE WHEN ISNULL(SUM(toa1.fPendingAmount)) THEN 0 ELSE SUM(toa1.fPendingAmount) END) FROM trip_outstanding_amount as toa1 WHERE toa1.iUserId=toa.iUserId AND toa1.vTripPaymentMode != 'Organization' AND toa1.ePaidByPassenger ='Yes' AND toa1.iUserId != '')) as Remaining, concat(ru.vName,' ',ru.vLastName) as riderName from trip_outstanding_amount AS toa LEFT JOIN register_user AS ru ON toa.iUserId = ru.iUserId LEFT JOIN orders AS o ON o.iOrderId = toa.iOrderId  LEFT JOIN trips AS tr ON tr.iTripId = toa.iTripId LEFT JOIN currency as cur ON cur.vName=ru.vCurrencyPassenger WHERE toa.iUserId > 0 AND toa.iUserId != '' AND ru.vName != '' AND ru.vName != 'NULL' AND toa.vTripPaymentMode != 'Organization' {$ssql1} {$ssql} GROUP BY toa.iUserId  {$ssql}  {$trp_ssql}";
    }
    $db_trip = $obj->MySQLSelect($sql);
    $var_filter = '';
    foreach ($_REQUEST as $key => $val) {
        if (('tpages' !== $key) && ('page' !== $key) && ('searchSettleUnsettle' === $key) || ('action' === $key)) {
            $var_filter .= "&{$key}=".stripslashes($val);
        }
    }
    $header .= 'User Name'."\t";
    // $header .= "Total Amount" . "\t";
    // $header .= "Total Outstanding Amount" . "\t";
    $header .= 'Outstanding Amount'."\t";
    if (count($db_trip) > 0) {
        $AllTotalprice = 0;
        $AllTotalPending = $AllTotalRemainingPending = 0;
        for ($i = 0; $i < count($db_trip); ++$i) {
            // $fPendingAmount = $db_trip[$i]['allSum'];
            $fPendingAmount = ($db_trip[$i]['allSum'] / $db_trip[$i]['currencyRatio']);
            $remainingPendingAmount = ('' !== $db_trip[$i]['Remaining']) ? ($db_trip[$i]['Remaining'] / $db_trip[$i]['currencyRatio']) : '0';
            $userCurrency = $db_trip[$i]['userCurrency'];
            $AllTotalPending += $fPendingAmount;
            $AllTotalRemainingPending += $remainingPendingAmount;
            $data .= $db_trip[$i]['riderName']."\t";
            // $data .= formateNumAsPerCurrency(cleanNumber($fPendingAmount),'') . "\t";
            $data .= formateNumAsPerCurrency(cleanNumber($remainingPendingAmount), '')."\n";
        }
        $data .= "\n";
        $data .= 'TOTAL'."\t";
        // $data .= formateNumAsPerCurrency(cleanNumber($totalAllFare),'') . "\t";
        // $data .= formateNumAsPerCurrency(cleanNumber($AllTotalPending),'') . "\t";
        $data .= formateNumAsPerCurrency(cleanNumber($AllTotalRemainingPending), '')."\t";
    }
    $data = str_replace("\r", '', $data);
    ob_clean();
    header('Content-type: application/octet-stream; charset=utf-8');
    header('Content-Disposition: attachment; filename=outstanding_amount_reports.xls');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "{$header}\n{$data}";

    exit;
}
if ('org_outstanding_amount' === $section) {
    function cleanNumber($num)
    {
        return str_replace(',', '', $num);
    }

    // Start Sorting
    $sortby = $_REQUEST['sortby'] ?? 0;
    $order = $_REQUEST['order'] ?? '';
    // End Sorting
    // Start Search Parameters
    $ssql = '';
    $ssqlsearchSettle = '';
    $action = $_REQUEST['action'] ?? '';
    $searchOrganization = $_REQUEST['searchOrganization'] ?? '';
    $eType = $_REQUEST['eType'] ?? '';
    $searchSettleUnsettle = $_REQUEST['searchSettleUnsettle'] ?? '1';
    // $searchPaidby = $_REQUEST['searchPaidby'] ?? '';
    $searchPaidby = 'org';
    // echo $searchPaidby;exit;
    $searchSettleUnsettlePagination = $searchSettleUnsettle;
    $ssql = '';
    if ('org' === $searchPaidby) {
        if ('1' === $searchSettleUnsettle) {
            // $ssql1 = " AND toa1.ePaidByOrganization ='No' ";
            $ssql1 = "AND (toa1.eBillGenerated ='No' AND toa1.ePaidByOrganization ='No')";
        } elseif ('0' === $searchSettleUnsettle) {
            // $ssql1 = " AND toa1.ePaidByOrganization ='Yes' ";
            $ssql1 = "AND (toa1.eBillGenerated ='Yes' OR toa1.ePaidByOrganization ='Yes')";
        } elseif ('-1' === $searchSettleUnsettle) {
            // $ssql1 = " AND toa1.ePaidByOrganization ='Yes' ";
            $ssql1 = "AND (toa1.eBillGenerated ='Yes' OR toa1.ePaidByOrganization ='Yes') ";
        }
    } else {
        if ('1' === $searchSettleUnsettle) {
            $ssql1 = "AND toa1.ePaidByPassenger ='No' ";
        } elseif ('0' === $searchSettleUnsettle) {
            $ssql1 = "AND toa1.ePaidByPassenger ='Yes' ";
        } elseif ('-1' === $searchSettleUnsettle) {
            $ssql1 = "AND toa1.ePaidByPassenger ='Yes' ";
        }
    }
    if ('' !== $searchOrganization) {
        $ssql .= "AND toa.iOrganizationId ='".$searchOrganization."'";
    }
    $sqlPaidby = $sqlPaidbysub = '';
    if ('org' === $searchPaidby) {
        $sqlPaidbysub = "AND toa1.vTripPaymentMode = 'Organization'";
        $sqlPaidby = "AND toa.vTripPaymentMode = 'Organization'";
    } else {
        $sqlPaidbysub = " AND toa1.vTripPaymentMode != 'Organization'";
        $sqlPaidby = "AND toa.vTripPaymentMode != 'Organization'";
    }
    $trp_ssql = 'ORDER BY org.vCompany ASC';
    // Pagination Start
    $per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
    if ('1' === $searchSettleUnsettle) {
        $sql = "SELECT org.vCompany, toa.iOrganizationId,COUNT(toa.iTripOutstandId) AS Total, SUM(toa.fPendingAmount) as allSum, (SELECT SUM(toa1.fPendingAmount) FROM trip_outstanding_amount as toa1 WHERE toa1.iOrganizationId=toa.iOrganizationId {$sqlPaidbysub} {$ssql1} AND toa1.iOrganizationId != '') as Remaining from trip_outstanding_amount AS toa LEFT JOIN organization org ON org.iOrganizationId = toa.iOrganizationId WHERE toa.iOrganizationId > 0 AND toa.iOrganizationId != '' {$sqlPaidby} {$ssql} GROUP BY toa.iOrganizationId HAVING remaining>0 {$trp_ssql}";
    } elseif ('0' === $searchSettleUnsettle) {
        $sql = "SELECT org.vCompany, toa.iOrganizationId,COUNT(toa.iTripOutstandId) AS Total,SUM(toa.fPendingAmount) AS allSum, (SUM(toa.fPendingAmount)-(SELECT SUM(toa1.fPendingAmount) FROM trip_outstanding_amount as toa1 WHERE toa1.iOrganizationId=toa.iOrganizationId {$sqlPaidbysub} {$ssql1} AND toa1.iOrganizationId != ''))as Remaining, (SELECT SUM(toa1.fPendingAmount) FROM trip_outstanding_amount as toa1 WHERE toa1.iOrganizationId=toa.iOrganizationId {$sqlPaidbysub} {$ssql1} AND toa1.iOrganizationId != '') as PaidData from trip_outstanding_amount AS toa LEFT JOIN organization org ON org.iOrganizationId = toa.iOrganizationId WHERE toa.iOrganizationId > 0 AND toa.iOrganizationId != '' {$sqlPaidby} {$ssql} GROUP BY toa.iOrganizationId HAVING allSum=PaidData {$trp_ssql}";
    } else {
        $sql = "SELECT org.vCompany, toa.iOrganizationId,COUNT(toa.iTripOutstandId) AS Total,SUM(toa.fPendingAmount) AS allSum, (SUM(toa.fPendingAmount)-(SELECT (CASE WHEN ISNULL(SUM(toa1.fPendingAmount)) THEN 0 ELSE SUM(toa1.fPendingAmount) END) FROM trip_outstanding_amount as toa1 WHERE toa1.iOrganizationId=toa.iOrganizationId {$sqlPaidbysub} {$ssql1} AND toa1.iOrganizationId != '')) as Remaining from trip_outstanding_amount AS toa LEFT JOIN trips AS tr ON tr.iTripId = toa.iTripId LEFT JOIN organization org ON org.iOrganizationId = toa.iOrganizationId WHERE toa.iOrganizationId > 0 AND toa.iOrganizationId != '' {$sqlPaidby} GROUP BY toa.iOrganizationId ORDER BY org.vCompany ASC";
    }
    $db_trip = $obj->MySQLSelect($sql);
    $var_filter = '';
    foreach ($_REQUEST as $key => $val) {
        if (('tpages' !== $key) && ('page' !== $key) && ('searchSettleUnsettle' === $key) || ('action' === $key)) {
            $var_filter .= "&{$key}=".stripslashes($val);
        }
    }
    $header .= 'Organization Name'."\t";
    // $header .= "Total Amount" . "\t";
    // $header .= "Total Outstanding Amount" . "\t";
    $header .= 'Outstanding Amount'."\t";
    if (count($db_trip) > 0) {
        $AllTotalprice = 0;
        $AllTotalPending = $AllTotalRemainingPending = 0;
        for ($i = 0; $i < count($db_trip); ++$i) {
            $fPendingAmount = $db_trip[$i]['allSum'];
            // $fPendingAmount = ($db_trip[$i]['allSum'] / $db_trip[$i]['currencyRatio']);
            // $remainingPendingAmount = ($db_trip[$i]['Remaining'] != '') ? ($db_trip[$i]['Remaining'] / $db_trip[$i]['currencyRatio'] ) :'0';
            $remainingPendingAmount = '' !== $db_trip[$i]['Remaining'] ? $db_trip[$i]['Remaining'] : '0';
            $AllTotalPending += $fPendingAmount;
            $AllTotalRemainingPending += $remainingPendingAmount;
            $data .= $db_trip[$i]['vCompany']."\t";
            // $data .= formateNumAsPerCurrency(cleanNumber($fPendingAmount),'') . "\t";
            $data .= formateNumAsPerCurrency(cleanNumber($remainingPendingAmount), '')."\n";
        }
        $data .= "\n";
        $data .= 'TOTAL'."\t";
        // $data .= formateNumAsPerCurrency(cleanNumber($totalAllFare),'') . "\t";
        // $data .= formateNumAsPerCurrency(cleanNumber($AllTotalPending),'') . "\t";
        $data .= formateNumAsPerCurrency(cleanNumber($AllTotalRemainingPending), '')."\t";
    }
    $data = str_replace("\r", '', $data);
    ob_clean();
    header('Content-type: application/octet-stream; charset=utf-8');
    header('Content-Disposition: attachment; filename=outstanding_amount_reports.xls');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "{$header}\n{$data}";

    exit;
}
if ('driver_payment' === $section) {
    $eType = $_REQUEST['eType'] ?? '';
    $ord = ' ORDER BY tr.iTripId DESC';
    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY rd.vName ASC';
        } else {
            $ord = ' ORDER BY rd.vName DESC';
        }
    }
    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY ru.vName ASC';
        } else {
            $ord = ' ORDER BY ru.vName DESC';
        }
    }
    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY tr.tTripRequestDate ASC';
        } else {
            $ord = ' ORDER BY tr.tTripRequestDate DESC';
        }
    }
    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY d.vName ASC';
        } else {
            $ord = ' ORDER BY d.vName DESC';
        }
    }
    if (5 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY u.vName ASC';
        } else {
            $ord = ' ORDER BY u.vName DESC';
        }
    }
    if (6 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY tr.eType ASC';
        } else {
            $ord = ' ORDER BY tr.eType DESC';
        }
    }
    $ssql = '';
    if ('' !== $startDate) {
        $ssql .= " AND Date(tTripRequestDate) >='".$startDate."'";
    }
    if ('' !== $endDate) {
        $ssql .= " AND Date(tTripRequestDate) <='".$endDate."'";
    }
    if ('' !== $serachTripNo) {
        $ssql .= " AND tr.vRideNo ='".$serachTripNo."'";
    }
    if ('' !== $iCompanyId) {
        $ssql .= " AND rd.iCompanyId = '".$iCompanyId."'";
    }
    if ('' !== $iDriverId) {
        $ssql .= " AND tr.iDriverId = '".$iDriverId."'";
    }
    if ('' !== $iUserId) {
        $ssql .= " AND tr.iUserId = '".$iUserId."'";
    }
    if ('' !== $vTripPaymentMode) {
        $ssql .= " AND tr.vTripPaymentMode = '".$vTripPaymentMode."'";
    }
    if ('' !== $eDriverPaymentStatus) {
        $ssql .= " AND tr.eDriverPaymentStatus = '".$eDriverPaymentStatus."'";
    }
    if ('' !== $eType) {
        if ('Fly' === $eType) {
            $ssql .= ' AND tr.iFromStationId > 0 AND tr.iToStationId > 0';
        } elseif ('Ride' === $eType) {
            $ssql .= " AND tr.eType ='".$eType."' AND tr.iRentalPackageId = 0 AND tr.eHailTrip = 'No' AND  tr.iFromStationId = 0 AND tr.iToStationId = 0 ";
        } elseif ('RentalRide' === $eType) {
            $ssql .= " AND tr.eType ='Ride' AND tr.iRentalPackageId > 0";
        } elseif ('HailRide' === $eType) {
            $ssql .= " AND tr.eType ='Ride' AND tr.eHailTrip = 'Yes'";
        } elseif ('Pool' === $eType) {
            $ssql .= " AND tr.eType ='Ride' AND tr.ePoolRide = 'Yes'";
        } else {
            $ssql .= " AND tr.eType ='".$eType."' ";
        }
    }
    $ufxEnable = $MODULES_OBJ->isUberXFeatureAvailable() ? 'Yes' : 'No'; // add function to modules availibility
    $rideEnable = $MODULES_OBJ->isRideFeatureAvailable() ? 'Yes' : 'No';
    $deliveryEnable = $MODULES_OBJ->isDeliveryFeatureAvailable() ? 'Yes' : 'No';
    $deliverallEnable = $MODULES_OBJ->isDeliverAllFeatureAvailable() ? 'Yes' : 'No';
    if ('Yes' !== $ufxEnable) {
        $ssql .= " AND tr.eType != 'UberX'";
    }
    if (!$MODULES_OBJ->isAirFlightModuleAvailable()) {
        $ssql .= " AND tr.iFromStationId = '0' AND tr.iToStationId = '0'";
    }
    if ('Yes' !== $rideEnable && 'Yes' !== $deliverallEnable) {
        $ssql .= " AND tr.eType != 'Ride'";
    }
    if ('Yes' !== $deliveryEnable) {
        $ssql .= " AND tr.eType != 'Deliver' AND tr.eType != 'Multi-Delivery'";
    }
    // global $userObj;
    $locations_where = '';
    if (count($userObj->locations) > 0) {
        $locations = implode(', ', $userObj->locations);
        $ssql .= " AND EXISTS(SELECT * FROM vehicle_type WHERE tr.iVehicleTypeId = vehicle_type.iVehicleTypeId AND vehicle_type.iLocationid IN(-1, {$locations}))";
    }
    $trp_ssql = '';
    if (SITE_TYPE === 'Demo') {
        $trp_ssql = " And tr.tTripRequestDate > '".WEEK_DATE."'";
    }
    $db_organization = $obj->MySQLSelect('SELECT iOrganizationId,vCompany AS driverName,vEmail FROM organization order by vCompany');
    $orgNameArr = [];
    for ($g = 0; $g < count($db_organization); ++$g) {
        $orgNameArr[$db_organization[$g]['iOrganizationId']] = $db_organization[$g]['driverName'];
    }
    $etypeSql = " AND tr.eSystem = 'General'";
    if ('Yes' === $deliverallEnable) {
        $etypeSql = " AND (tr.eSystem = 'General' OR tr.eSystem = 'DeliverAll') AND tr.iServiceId = '0'";
    }
    $sql = "SELECT tr.fCancellationFare,tr.iFromStationId,tr.iToStationId,tr.ePayWallet,tr.iFare, tr.fTax1,tr.fTax2,tr.iOrganizationId,tr.ePoolRide,tr.iTripId,tr.fHotelCommision,tr.vRideNo,tr.iDriverId,tr.iUserId,tr.tTripRequestDate, tr.eType, tr.eHailTrip,tr.fTripGenerateFare,tr.fCommision, tr.fDiscount, tr.fWalletDebit, tr.fTipPrice,tr.eDriverPaymentStatus,tr.ePaymentCollect,tr.vTripPaymentMode,tr.iActive,tr.fOutStandingAmount, tr.iRentalPackageId,c.vCompany,concat(rd.vName,' ',rd.vLastName) as drivername,concat(ru.vName,' ',ru.vLastName) as riderName,tr.vTimeZone FROM trips AS tr LEFT JOIN register_driver AS rd ON tr.iDriverId = rd.iDriverId LEFT JOIN register_user AS ru ON tr.iUserId = ru.iUserId LEFT JOIN company as c ON rd.iCompanyId = c.iCompanyId WHERE (tr.iActive ='Finished' OR (tr.iActive ='Canceled' AND tr.iFare > 0) OR (tr.iActive ='Canceled' AND tr.fWalletDebit > 0 AND tr.iFare = 0)) {$etypeSql} {$ssql} {$trp_ssql} {$ord}";
    // echo $sql;die;
    $db_trip = $obj->MySQLSelect($sql);
    $driver_payment = $total_tip = $tot_fare = $tot_site_commission = $tot_hotel_commision = $tot_promo_discount = $tot_driver_refund = $tot_wallentPayment = $tot_outstandingAmount = $tot_ifare = $tot_tax = 0.00;
    // Added By HJ On 08-08-2019 For Get Driver Wallet Debit Amount Start As Per Discuss With KS Sir
    $tripWalletArr = [];
    $getWalletData = $obj->MySQLSelect("SELECT iBalance,iTripId FROM user_wallet WHERE eType ='Debit' AND iTripId > 0");
    for ($d = 0; $d < count($getWalletData); ++$d) {
        $tripWalletArr[$getWalletData[$d]['iTripId']] = $getWalletData[$d]['iBalance'];
    }
    // Added By HJ On 08-08-2019 For Get Driver Wallet Debit Amount End As Per Discuss With KS Sir
    // echo "<pre>";print_r($tripWalletArr);die;
    $enableCashReceivedCol = $enableTipCol = [];
    foreach ($db_trip as $dtps) {
        $fTipPrice = $dtps['fTipPrice'];
        // Added By HJ On 25-05-2019 As Per Discuss With KS Also Given Confirmation After Checked By Her Start
        if ('Cash' === $dtps['vTripPaymentMode']) {
            $enableCashReceivedCol[] = 1;
        }
        // Added By HJ On 25-05-2019 As Per Discuss With KS Also Given Confirmation After Checked By Her Start
        if ($fTipPrice > 0) {
            $enableTipCol[] = 1;
        }
    }
    $endRecord = count($db_trip);
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
    $monday = date('Y-m-d', strtotime('sunday this week -1 week'));
    $sunday = date('Y-m-d', strtotime('saturday this week'));
    $Pmonday = date('Y-m-d', strtotime('sunday this week -2 week'));
    $Psunday = date('Y-m-d', strtotime('saturday this week -1 week'));
    $generalConfigPaymentArr = $CONFIG_OBJ->getGeneralVarAll_Payment_Array();
    // echo "<pre>";print_r($generalConfigPaymentArr);die;
    $SYSTEM_PAYMENT_FLOW = 'Method-1';
    if (isset($generalConfigPaymentArr['SYSTEM_PAYMENT_FLOW'])) {
        $SYSTEM_PAYMENT_FLOW = $generalConfigPaymentArr['SYSTEM_PAYMENT_FLOW'];
    }
    $ufxEnable = $MODULES_OBJ->isUfxFeatureAvailable(); // Added By HJ On 28-11-2019 For Check UberX Service Status
    $hotelPanel = $MODULES_OBJ->isEnableHotelPanel();
    $kioskPanel = $MODULES_OBJ->isEnableKioskPanel();
    /*if ($APP_TYPE != 'UberX' && $APP_TYPE != 'Delivery') {
        $header .= $langage_lbl_admin['LBL_TRIP_TYPE_TXT_ADMIN'] . "\t";
    }*/
    $header .= $langage_lbl_admin['LBL_RIDE_NO_ADMIN']."\t";
    $header .= $langage_lbl_admin['LBL_TRIP_TYPE_TXT_ADMIN']."\t";
    $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']."\t";
    $header .= $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']."\t";
    $header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'].' Date'."\t";
    $header .= 'A=Total Fare'."\t";
    $nxtChar = 'B';
    if (in_array(1, $enableCashReceivedCol, true)) {
        $header .= $nxtChar.'=Cash Received'."\t";
        $nxtChar = 'C';
    }
    $header .= $nxtChar.'=Commission Amount'."\t";
    $nxtChar = 'C';
    if ('C' === $nxtChar) {
        $nxtChar = 'D';
    }
    $header .= $nxtChar.'=Total Tax'."\t";
    $nxtChar = 'D';
    if ('D' === $nxtChar) {
        $nxtChar = 'E';
    }
    // added by SP for changes as per the report on 28-06-2019 start
    //    $header .= $nxtChar . "=Promo Code Discount" . "\t";
    //    $nxtChar = "E";
    //    if ($nxtChar == "E") {
    //        $nxtChar = "F";
    //    }
    //    $header .= $nxtChar . "=Wallet Debit" . "\t";
    //    $nxtChar = "F";
    //    if ($nxtChar == "F") {
    //        $nxtChar = "G";
    //    }
    if (in_array(1, $enableTipCol, true)) {
        $tipAmt = $nxtChar;
        $header .= $nxtChar.'=Tip'."\t";
    }
    $nxtChar = 'E';
    if ('E' === $nxtChar) {
        $nxtChar = 'F';
    }
    $outAmt = $nxtChar;
    $header .= $nxtChar.'='.$langage_lbl_admin['LBL_TRIP_TXT_ADMIN'].' Outstanding Amount'."\t";
    $nxtChar = 'F';
    if ('F' === $nxtChar) {
        $nxtChar = 'G';
    }
    $bookAmt = '';
    if ($hotelPanel > 0 || $kioskPanel > 0) {
        $bookAmt = $nxtChar;
    }
    $header .= $nxtChar.'=Booking Fees'."\t";
    $nxtChar = 'G';
    if ('G' === $nxtChar) {
        $nxtChar = 'H';
    }
    $ppAmt = $nxtChar;
    //    $header .= $nxtChar . "=Trip Outstanding Amount" . "\t";
    //    $nxtChar = "H";
    //    if ($nxtChar == "H") {
    //        $nxtChar = "I";
    //    }
    //    $header .= $nxtChar . "=Booking Fees  " . "\t";
    //    $nxtChar = "I";
    //    if ($nxtChar == "I") {
    //        $nxtChar = "J";
    //    }
    // added by SP for changes as per the report on 28-06-2019 end
    $header .= $nxtChar.'='.$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' pay / Take Amount'."\t";
    $header .= 'Site Earning'."\t";
    $header .= $langage_lbl_admin['LBL_RIDE_TXT_ADMIN'].' Status'."\t";
    $header .= 'Payment method'."\t";
    $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Payment Status';
    $driver_payment = $total_tip = $tot_fare = $tot_site_commission = $tot_hotel_commision = $tot_promo_discount = $tot_driver_refund = $tot_wallentPayment = $tot_outstandingAmount = $tot_ifare = $tot_tax = $totSiteEarning = $TotaliNewFare = 0.00;
    for ($i = 0; $i < count($db_trip); ++$i) {
        if ('Unsettelled' === $db_trip[$i]['eDriverPaymentStatus']) {
            $db_trip[$i]['eDriverPaymentStatus'] = 'Unsettled';
        } elseif ('Settelled' === $db_trip[$i]['eDriverPaymentStatus']) {
            $db_trip[$i]['eDriverPaymentStatus'] = 'Settled';
        }
        $iTripId = $db_trip[$i]['iTripId'];
        // echo "<pre>";print_r($db_trip);die;
        $iFare = $iFareOrg = setTwoDecimalPoint($db_trip[$i]['iFare']);
        $totTax = setTwoDecimalPoint($db_trip[$i]['fTax1'] + $db_trip[$i]['fTax2']);
        $orgName = '';
        if (isset($orgNameArr[$db_trip[$i]['iOrganizationId']]) && '' !== $orgNameArr[$db_trip[$i]['iOrganizationId']]) {
            $orgName = '('.$orgNameArr[$db_trip[$i]['iOrganizationId']].')';
            $iFare = 0;
        }
        $poolTxt = '';
        if ('Yes' === $db_trip[$i]['ePoolRide']) {
            $poolTxt = ' (Pool)';
        }
        $totalfare = setTwoDecimalPoint($db_trip[$i]['fTripGenerateFare']);
        $site_commission = setTwoDecimalPoint($db_trip[$i]['fCommision']);
        $promocodediscount = setTwoDecimalPoint($db_trip[$i]['fDiscount']);
        $wallentPayment = setTwoDecimalPoint($db_trip[$i]['fWalletDebit']);
        $fOutStandingAmount = setTwoDecimalPoint($db_trip[$i]['fOutStandingAmount']);
        $fHotelCommision = setTwoDecimalPoint($db_trip[$i]['fHotelCommision']);
        $fTipPrice = setTwoDecimalPoint($db_trip[$i]['fTipPrice']);
        $tipPayment = 0;
        $siteEarning = $site_commission + $totTax + $fOutStandingAmount + $fHotelCommision; // Added By HJ On 26-09-2020 As Per Discuss With KS Sir = Total = C+D+F+G
        // echo $iTripId."===>"."(" . $totalfare . "+" . $fTipPrice . ")-(" . $site_commission . "+" . $totTax . "+" . $fOutStandingAmount . "+" . $fHotelCommision ."+".$iFare. ")<br>";
        // Added By HJ On 25-05-2019 As Per Discuss With KS Also Given Confirmation After Checked By Her Start
        $driver_payment = $dispay_driver_payment = setTwoDecimalPoint(($totalfare + $fTipPrice) - ($site_commission + $totTax + $fOutStandingAmount + $fHotelCommision));
        if ('Cash' === $db_trip[$i]['vTripPaymentMode']) {
            $driver_payment = $dispay_driver_payment = setTwoDecimalPoint($driver_payment - $iFare);
            $tot_ifare += $iFare;
        }
        // echo "<pre>";print_r($db_trip);die;
        // Added By HJ On 26-09-2020 For Display Canceled Trip Calculation As Per Discuss With KS sir Start
        if ('Canceled' === $db_trip[$i]['iActive']) {
            $iFare = $db_trip[$i]['fCancellationFare'] - $iFare;
            $driver_payment = $iFareOrg - ($site_commission + $totTax + $fOutStandingAmount + $fHotelCommision);
            if ('Cash' === $db_trip[$i]['vTripPaymentMode'] || 'Organization' === $db_trip[$i]['vTripPaymentMode']) {
                $iFare = 0;
                $driver_payment = ($iFareOrg + $wallentPayment) - ($site_commission + $totTax + $fOutStandingAmount + $fHotelCommision);
                $driver_payment = $dispay_driver_payment = setTwoDecimalPoint($driver_payment - $iFare);
            }
        }
        // Added By HJ On 26-09-2020 For Display Canceled Trip Calculation As Per Discuss With KS sir End
        // echo $iTripId . "===>" .$driver_payment."<br>";
        // Added By HJ On 08-08-2019 For Check Driver Wallet Debit Amount Start As Per Discuss With KS Sir
        $driverDebitAmt = 0;
        if (isset($tripWalletArr[$iTripId]) && $tripWalletArr[$iTripId] > 0) {
            $driverDebitAmt = setTwoDecimalPoint($tripWalletArr[$iTripId]);
            // echo $driverDebitAmt."+".$driver_payment."<br>";die;
            // $driver_payment = $dispay_driver_payment = setTwoDecimalPoint($driverDebitAmt + $driver_payment);
            // /echo setTwoDecimalPoint($driver_payment);die;
        }
        // echo $iTripId . "===>" .$driver_payment."<br>";
        // Added By HJ On 08-08-2019 For Check Driver Wallet Debit Amount End As Per Discuss With KS End
        // Added By HJ On 25-05-2019 As Per Discuss With KS Also Given Confirmation After Checked By Her Start
        $set_unsetarray[] = $db_trip[$i]['eDriverPaymentStatus'];
        $eTypenew = $db_trip[$i]['eType'];
        if ('Ride' === $eTypenew) {
            $trip_type = 'Ride';
        } elseif ('UberX' === $eTypenew) {
            $trip_type = 'Other Services';
        } else {
            $trip_type = 'Delivery';
        }
        if (!empty($db_trip[$i]['iFromStationId']) && !empty($db_trip[$i]['iToStationId'])) {
            $trip_type = 'Fly';
        }
        $trip_type .= $poolTxt;
        if ('Yes' === $db_trip[$i]['eHailTrip'] && $db_trip[$i]['iRentalPackageId'] > 0) {
            $tripTypeTxt = 'Rental '.$trip_type.'<br/> ( Hail )';
        } elseif ($db_trip[$i]['iRentalPackageId'] > 0) {
            $tripTypeTxt = 'Rental '.$trip_type;
        } elseif ('Yes' === $db_trip[$i]['eHailTrip']) {
            $tripTypeTxt = 'Hail '.$trip_type;
        } else {
            $tripTypeTxt = $trip_type;
        }
        $tot_fare += $totalfare;
        $tot_site_commission += $site_commission;
        $tot_hotel_commision += $fHotelCommision;
        $tot_promo_discount += $promocodediscount;
        $tot_wallentPayment += $wallentPayment;
        $tot_tax += $totTax;
        $total_tip += $fTipPrice;
        $tot_driver_refund += $driver_payment;
        $totSiteEarning += $siteEarning; // Added By HJ On 26-09-2020 As Per Discuss With KS Sir = Total = C+D+F+G
        // echo $iTripId . "===>" .$driver_payment."<br>";
        $tot_outstandingAmount += $fOutStandingAmount;
        $data .= $db_trip[$i]['vRideNo']."\t".$tripTypeTxt."\t";
        // if ($APP_TYPE != 'UberX' && $APP_TYPE != 'Delivery') {
        //    if ($db_trip[$j]['eHailTrip'] == "Yes" && $db_trip[$j]['iRentalPackageId'] > 0) {
        //        //$data .= "Rental " . $trip_type . " ( Hail )" . "\t";
        //        $data .= $db_trip[$j]['vRideNo'] . "  " . "Rental " . $trip_type . " ( Hail )" . "\t";
        //    } else if ($db_trip[$j]['iRentalPackageId'] > 0) {
        //        //$data .= "Rental " . $trip_type . "\t";
        //        $data .= $db_trip[$j]['vRideNo'] . "  " . "Rental " . $trip_type . "\t";
        //    } else if ($db_trip[$j]['eHailTrip'] == "Yes") {
        //        //$data .= "Hail " . $trip_type . "\t";
        //        $data .= $db_trip[$j]['vRideNo'] . "  " . "Hail " . $trip_type . "\t";
        //    } else {
        //        $data .= $db_trip[$j]['vRideNo'] . "  " . $trip_type . "\t";
        //    }
        // } else {
        //    $data .= $db_trip[$j]['vRideNo'] . "  " . $trip_type . "\t";
        // }
        // $data .= $db_trip[$j]['vRideNo'] . "\t";
        $data .= clearName($db_trip[$i]['drivername'])."\t";
        $data .= clearName($db_trip[$i]['riderName'])."\t";
        $systemTimeZone = date_default_timezone_get();
        $db_trip[$i]['tTripRequestDate'] = converToTz($db_trip[$i]['tTripRequestDate'], $db_trip[$i]['vTimeZone'], $systemTimeZone);
        $data .= DateTime($db_trip[$i]['tTripRequestDate'])."\t";
        // $data .= ($totalfare > 0) ? trip_currency($totalfare) . "\t" : "- \t";
        if ('' !== $db_trip[$i]['fTripGenerateFare'] && 0 !== $db_trip[$i]['fTripGenerateFare']) {
            $totFareHtml = formateNumAsPerCurrency($db_trip[$i]['fTripGenerateFare'], '');
        } else {
            $totFareHtml = '-';
        }
        $data .= $totFareHtml."\t";
        if (in_array(1, $enableCashReceivedCol, true)) {
            if ('Card' !== $db_trip[$i]['vTripPaymentMode']) {
                // $data .= ($iFare > 0) ? trip_currency($iFare) . "\t" : "".trip_currency(0)." \t";
                $data .= ($iFare > 0) ? formateNumAsPerCurrency($iFare, '')."\t" : ''.trip_currency(0)." \t";
                $TotaliNewFare += $totalfare;
            } else {
                $data .= "- \t";
            }
        }
        // $data .= ($site_commission > 0) ? trip_currency($site_commission) . "\t" : "- \t";
        if ('' !== $db_trip[$i]['fCommision'] && 0 !== $db_trip[$i]['fCommision']) {
            $data .= formateNumAsPerCurrency($db_trip[$i]['fCommision'], '')."\t";
        } else {
            $data .= "- \t";
        }
        // $data .= ($site_commission > 0) ? formateNumAsPerCurrency($site_commission,'') . "\t" : "- \t";
        // $data .= ($totTax > 0) ? trip_currency($totTax) . "\t" : "- \t";
        $data .= formateNumAsPerCurrency($totTax, '')."\t";
        // added by SP for changes as per the report on 28-06-2019 start
        // $data .= ($promocodediscount > 0) ? trip_currency($promocodediscount) . "\t" : "- \t";
        // $data .= ($wallentPayment > 0) ? trip_currency($wallentPayment) . "\t" : "- \t";
        // added by SP for changes as per the report on 28-06-2019 end
        if (in_array(1, $enableTipCol, true)) {
            $data .= ('0' !== $db_trip[$i]['fTipPrice']) ? formateNumAsPerCurrency($db_trip[$i]['fTipPrice'], '')."\t" : "- \t";
            // $data .= ($fTipPrice > 0) ? trip_currency($fTipPrice) . "\t" : "- \t";
        }
        // $data .= ($fOutStandingAmount > 0) ? trip_currency($fOutStandingAmount) . "\t" : "- \t";
        $data .= ('' !== $db_trip[$i]['fOutStandingAmount'] && 0 !== $db_trip[$i]['fOutStandingAmount']) ? formateNumAsPerCurrency($db_trip[$i]['fOutStandingAmount'], '')."\t" : "- \t";
        if ($hotelPanel > 0 || $kioskPanel > 0) {
            $data .= ('' !== $db_trip[$i]['fHotelCommision'] && 0 !== $db_trip[$i]['fHotelCommision']) ? formateNumAsPerCurrency($db_trip[$i]['fHotelCommision'], '')."\t" : "- \t";
        }
        $data .= ('' !== $driver_payment && 0 !== $driver_payment) ? formateNumAsPerCurrency($driver_payment, '')."\t" : "- \t";
        $data .= ('' !== $siteEarning && 0 !== $siteEarning) ? formateNumAsPerCurrency($siteEarning, '')."\t" : "- \t";
        $data .= $db_trip[$i]['iActive']."\t";
        if ('Card' === $db_trip[$i]['vTripPaymentMode'] && 'Yes' === $db_trip[$i]['ePayWallet']) {
            $data .= $langage_lbl_admin['LBL_WALLET_TXT']."\t";
        } else {
            $data .= $db_trip[$i]['vTripPaymentMode'].$orgName."\t";
        }
        // $data .= $paymentmode . "\t";
        if ('Settelled' === $db_trip[$i]['eDriverPaymentStatus']) {
            $data .= 'Settled'."\n";
        } elseif ('Unsettelled' === $db_trip[$i]['eDriverPaymentStatus']) {
            $data .= 'Unsettled'."\n";
        } else {
            $data .= $db_trip[$i]['eDriverPaymentStatus']."\n";
        }
    }
    $data .= "Total\t";
    $data .= "--\t";
    $data .= "--\t";
    $data .= "--\t";
    $data .= "--\t";
    $data .= formateNumAsPerCurrency($tot_fare, '')."\t";
    if (in_array(1, $enableCashReceivedCol, true)) {
        $data .= formateNumAsPerCurrency($TotaliNewFare, '')."\t";
    }
    $data .= formateNumAsPerCurrency($tot_site_commission, '')."\t";
    $data .= formateNumAsPerCurrency($tot_tax, '')."\t";
    // $data .= trip_currency($tot_promo_discount) . "\t";
    // $data .= trip_currency($tot_wallentPayment) . "\t";
    if (in_array(1, $enableTipCol, true)) {
        $data .= formateNumAsPerCurrency($total_tip, '')."\t";
    }
    $data .= formateNumAsPerCurrency($tot_outstandingAmount, '')."\t";
    if ($hotelPanel > 0 || $kioskPanel > 0) {
        $data .= formateNumAsPerCurrency($tot_hotel_commision, '')."\t";
    }
    $data .= formateNumAsPerCurrency($tot_driver_refund, '')."\t";
    $data .= formateNumAsPerCurrency($totSiteEarning, '')."\t";
    $data .= "--\t";
    $data .= "--\t";
    $data .= "--\t";
    // $data .= "--\t";
    /* $data .= "\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tTotal Fare\t" . trip_currency($tot_fare) . "\n";
      $data .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tTotal Platform Fees\t" . trip_currency($tot_site_commission) . "\n";
      $data .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tTotal Promo Discount\t" . trip_currency($tot_promo_discount) . "\n";
      $data .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tTotal Wallet Debit\t" . trip_currency($tot_wallentPayment) . "\n";
      if ($ENABLE_TIP_MODULE == "Yes") {
      $data .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tTotal Tip Amount\t" . trip_currency($total_tip) . "\n";
      //$data .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tTotal Driver Payment\t" . trip_currency($tot_driver_refund+$total_tip) . "\n";
      $data .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tTotal Trip Outstanding Amount\t" . trip_currency($tot_outstandingAmount) . "\n";
      $data .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tTotal Booking Fees\t" . trip_currency($tot_hotel_commision) . "\n";
      $data .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tTotal " . $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'] . " Payment\t" . trip_currency($tot_driver_refund) . "\n";
      } else {
      $data .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tTotal Trip Outstanding Amount\t" . trip_currency($tot_outstandingAmount) . "\n";
      $data .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tTotal Booking Fees\t" . trip_currency($tot_hotel_commision) . "\n";
      $data .= "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\tTotal " . $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'] . " Payment\t" . trip_currency($tot_driver_refund) . "\n";
      } */
    $data = str_replace("\r", '', $data);
    ob_clean();
    header('Content-type: application/octet-stream; charset=utf-8');
    header('Content-Disposition: attachment; filename=payment_reports.xls');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "{$header}\n{$data}";

    exit;
}

if ('cancellation_driver_payment' === $section || 'cancellation_org_driver_payment' === $section) {
    $eType = $_REQUEST['eType'] ?? '';
    $searchPaymentByUser = $_REQUEST['searchPaymentByUser'] ?? '';
    $trp_ssql = '';
    if (SITE_TYPE === 'Demo') {
        $trp_ssql = " And trp.tTripRequestDate > '".WEEK_DATE."'";
    }
    $ord = ' ORDER BY tr.iTripId DESC';
    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY rd.vName ASC';
        } else {
            $ord = ' ORDER BY rd.vName DESC';
        }
    }
    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY ru.vName ASC';
        } else {
            $ord = ' ORDER BY ru.vName DESC';
        }
    }
    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY trp.tTripRequestDate ASC';
        } else {
            $ord = ' ORDER BY trp.tTripRequestDate DESC';
        }
    }
    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY d.vName ASC';
        } else {
            $ord = ' ORDER BY d.vName DESC';
        }
    }
    if (5 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY u.vName ASC';
        } else {
            $ord = ' ORDER BY u.vName DESC';
        }
    }
    if (6 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY trp.eType ASC';
        } else {
            $ord = ' ORDER BY trp.eType DESC';
        }
    }
    $ssql = '';
    $reportName = 'cancellation_payment_report';
    if ('cancellation_org_driver_payment' === $section) {
        $ssql .= " AND tr.ePaymentBy='Organization'";
        $reportName = 'org_cancellation_payment_report';
    }
    if ('' !== $startDate) {
        $ssql .= " AND Date(trp.tTripRequestDate) >='".$startDate."'";
    }
    if ('' !== $endDate) {
        $ssql .= " AND Date(trp.tTripRequestDate) <='".$endDate."'";
    }
    if ('' !== $serachTripNo) {
        $ssql .= " AND trp.vRideNo ='".$serachTripNo."'";
    }
    if ('' !== $iCompanyId) {
        $ssql .= " AND rd.iCompanyId ='".$iCompanyId."'";
    }
    if ('' !== $iDriverId) {
        $ssql .= " AND tr.iDriverId ='".$iDriverId."'";
    }
    if ('' !== $iUserId) {
        $ssql .= " AND tr.iUserId ='".$iUserId."'";
    }
    if ('' !== $eDriverPaymentStatus) {
        $ssql .= " AND tr.ePaidToDriver ='".$eDriverPaymentStatus."'";
    }
    if ('' !== $vTripPaymentMode) {
        $ssql .= " AND tr.vTripPaymentMode ='".$vTripPaymentMode."'";
    }
    if ('' !== $eType) {
        if ('Ride' === $eType) {
            $ssql .= " AND trp.eType ='".$eType."' AND trp.iRentalPackageId = 0 AND trp.eHailTrip = 'No' ";
        } elseif ('RentalRide' === $eType) {
            $ssql .= " AND trp.eType ='Ride' AND trp.iRentalPackageId > 0";
        } elseif ('HailRide' === $eType) {
            $ssql .= " AND trp.eType ='Ride' AND trp.eHailTrip = 'Yes'";
        } else {
            $ssql .= " AND trp.eType ='".$eType."' ";
        }
    }
    if ('' !== $searchPaymentByUser) {
        $ssql .= " AND tr.ePaidByPassenger ='".$searchPaymentByUser."'";
    }
    $locations_where = '';
    if (count($userObj->locations) > 0) {
        $locations = implode(', ', $userObj->locations);
        $ssql .= " AND EXISTS(SELECT * FROM vehicle_type WHERE trp.iVehicleTypeId = vehicle_type.iVehicleTypeId AND vehicle_type.iLocationid IN(-1, {$locations}))";
    }
    $sql_admin = "SELECT tr.iTripId,tr.iTripOutstandId,tr.fPendingAmount,tr.iDriverId,tr.iUserId, tr.fCommision, tr.fDriverPendingAmount, tr.fWalletDebit,tr.ePaidByPassenger,tr.ePaidToDriver,tr.vTripPaymentMode,trp.iRentalPackageId,trp.eType,trp.vRideNo,trp.tTripRequestDate, tr.vTripAdjusmentId,c.vCompany,concat(rd.vName,' ',rd.vLastName) as drivername,concat(ru.vName,' ',ru.vLastName) as riderName FROM trip_outstanding_amount AS tr LEFT JOIN register_driver AS rd ON tr.iDriverId = rd.iDriverId LEFT JOIN register_user AS ru ON tr.iUserId = ru.iUserId LEFT JOIN trips AS trp ON trp.iTripId = tr.iTripId  LEFT JOIN company as c ON rd.iCompanyId = c.iCompanyId WHERE 1 = 1 AND trp.eSystem = 'General' {$ssql} {$trp_ssql} {$ord}";
    $db_trip = $obj->MySQLSelect($sql_admin);
    /* echo "<pre>";
    print_r($db_trip);
    exit; */
    // echo $sql_admin;die;
    if ('UberX' !== $APP_TYPE && 'Delivery' !== $APP_TYPE) {
        $header .= $langage_lbl_admin['LBL_TRIP_TYPE_TXT_ADMIN']."\t";
    }
    $header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'].' No.'."\t";
    $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Name'."\t";
    $header .= $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'].' Name'."\t";
    $header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'].' Date'."\t";
    $header .= 'Total Cancellation Fees'."\t";
    $header .= 'Platform Fees'."\t";
    $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Pay Amount'."\t";
    $header .= 'Adjustment Booking No'."\t";
    $header .= 'Provider Payment Status'."\t";
    $driver_payment = $tot_site_commission = 0.00;
    for ($j = 0; $j < count($db_trip); ++$j) {
        $site_commission = $db_trip[$j]['fCommision'];
        $driver_payment = $db_trip[$j]['fDriverPendingAmount'];
        $tot_site_commission += $site_commission;
        $tot_driver_refund += $driver_payment;
        $paymentmode = $db_trip[$j]['vTripPaymentMode'];
        $eType = $db_trip[$j]['eType'];
        if ('Ride' === $eType) {
            $trip_type = 'Ride';
        } elseif ('UberX' === $eType) {
            $trip_type = 'Other Services';
        } elseif ('Deliver' === $eType || 'Multi-Delivery' === $eType) {
            $trip_type = 'Delivery';
        }
        $q = "SELECT vRideNo FROM trips WHERE iTripId = '".$db_trip[$j]['vTripAdjusmentId']."'";
        $db_bookingno = $obj->MySQLSelect($q);
        if ('UberX' !== $APP_TYPE && 'Delivery' !== $APP_TYPE) {
            if ('Yes' === $db_trip[$j]['eHailTrip'] && $db_trip[$j]['iRentalPackageId'] > 0) {
                $data .= 'Rental '.$trip_type.' ( Hail )'."\t";
            } elseif ($db_trip[$j]['iRentalPackageId'] > 0) {
                $data .= 'Rental '.$trip_type."\t";
            } elseif ('Yes' === $db_trip[$j]['eHailTrip']) {
                $data .= 'Hail '.$trip_type."\t";
            } else {
                $data .= $trip_type."\t";
            }
        }
        if ('' !== $db_bookingno[0]['vRideNo'] && 0 !== $db_bookingno[0]['vRideNo']) {
            $paymentstatus = 'Paid in Trip# '.$db_bookingno[0]['vRideNo'];
        } elseif ('No' === $db_trip[$j]['ePaidByPassenger']) {
            $paymentstatus = 'Not Paid';
        } else {
            $paymentstatus = 'Paid By Card';
        }
        $TotalCancelledprice = $db_trip[$j]['fPendingAmount'] > $db_trip[$j]['fWalletDebit'] ? $db_trip[$j]['fPendingAmount'] : $db_trip[$j]['fWalletDebit'];
        if ('No' === $db_trip[$j]['ePaidToDriver']) {
            $providerPaymentStatus = 'Unsettled';
        } else {
            $providerPaymentStatus = 'Settled';
        }
        $data .= $db_trip[$j]['vRideNo']."\t";
        $data .= clearName($db_trip[$j]['drivername'])."\t";
        $data .= clearName($db_trip[$j]['riderName'])."\t";
        $data .= date('d-m-Y', strtotime($db_trip[$j]['tTripRequestDate']))."\t";
        $data .= ('' !== $TotalCancelledprice && 0 !== $TotalCancelledprice) ? formateNumAsPerCurrency($TotalCancelledprice, '')."\t" : "- \t";
        $data .= ('' !== $db_trip[$j]['fCommision'] && 0 !== $db_trip[$j]['fCommision']) ? formateNumAsPerCurrency($db_trip[$j]['fCommision'], '')."\t" : "- \t";
        $data .= ('' !== $driver_payment && 0 !== $driver_payment) ? formateNumAsPerCurrency($driver_payment, '')."\t" : "- \t";
        // $data .= ($db_bookingno[0]['vRideNo'] != "" && $db_bookingno[0]['vRideNo'] != 0) ? $db_bookingno[0]['vRideNo'] . "\n" : "- \n";
        $data .= $paymentstatus."\t";
        $data .= $providerPaymentStatus."\n";
    }
    $data .= "\t\t\t\t\t\t\t\tTotal Platform Fees\t".formateNumAsPerCurrency($tot_site_commission, '')."\n";
    $data .= "\t\t\t\t\t\t\t\tTotal ".$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." Payment\t".formateNumAsPerCurrency($tot_driver_refund, '')."\n";
    $data = str_replace("\r", '', $data);
    ob_clean();
    header('Content-type: application/octet-stream; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$reportName.'.xls');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "{$header}\n{$data}";

    exit;
}
/* if ($section == 'driver_payment') {

  $trp_ssql = "";
  if (SITE_TYPE == 'Demo') {
  $trp_ssql = " And tr.tTripRequestDate > '" . WEEK_DATE . "'";
  }

  $ord = ' ORDER BY tr.iTripId DESC';

  if ($sortby == 1) {
  if ($order == 0)
  $ord = " ORDER BY rd.vName ASC";
  else
  $ord = " ORDER BY rd.vName DESC";
  }

  if ($sortby == 2) {
  if ($order == 0)
  $ord = " ORDER BY ru.vName ASC";
  else
  $ord = " ORDER BY ru.vName DESC";
  }

  if ($sortby == 3) {
  if ($order == 0)
  $ord = " ORDER BY tr.tStartDate ASC";
  else
  $ord = " ORDER BY tr.tStartDate DESC";
  }

  if ($sortby == 4) {
  if ($order == 0)
  $ord = " ORDER BY d.vName ASC";
  else
  $ord = " ORDER BY d.vName DESC";
  }

  if ($sortby == 5) {
  if ($order == 0)
  $ord = " ORDER BY u.vName ASC";
  else
  $ord = " ORDER BY u.vName DESC";
  }

  $ssql = "";
  if ($startDate != '') {
  $ssql .= " AND Date(tTripRequestDate) >='" . $startDate . "'";
  }
  if ($endDate != '') {
  $ssql .= " AND Date(tTripRequestDate) <='" . $endDate . "'";
  }
  if ($iCompanyId != '') {
  $ssql .= " AND rd.iCompanyId = '" . $iCompanyId . "'";
  }
  if ($iDriverId != '') {
  $ssql .= " AND tr.iDriverId = '" . $iDriverId . "'";
  }

  if ($iUserId != '') {
  $ssql .= " AND tr.iUserId = '" . $iUserId . "'";
  }
  if ($serachTripNo != '') {
  $ssql .= " AND tr.vRideNo ='" . $serachTripNo . "'";
  }

  if ($vTripPaymentMode != '') {
  $ssql .= " AND tr.vTripPaymentMode = '" . $vTripPaymentMode . "'";
  }
  if ($eDriverPaymentStatus != '') {
  $ssql .= " AND tr.eDriverPaymentStatus = '" . $eDriverPaymentStatus . "'";
  }
  //$sql_admin = "SELECT * from trips WHERE 1=1 ".$ssql." ORDER BY iTripId DESC";
  $sql_admin = "SELECT tr.iTripId,tr.vRideNo,tr.iDriverId,tr.iUserId,tr.tTripRequestDate,tr.fTripGenerateFare,tr.fCommision, tr.fDiscount, tr.fWalletDebit, tr.fTipPrice,tr.eDriverPaymentStatus,tr.ePaymentCollect,tr.vTripPaymentMode,tr.iActive,c.vCompany,concat(rd.vName,' ',rd.vLastName) as drivername,concat(ru.vName,' ',ru.vLastName) as riderName FROM trips AS tr
  LEFT JOIN register_driver AS rd ON tr.iDriverId = rd.iDriverId
  LEFT JOIN register_user AS ru ON tr.iUserId = ru.iUserId
  LEFT JOIN company as c ON rd.iCompanyId = c.iCompanyId
  WHERE 1=1 $ssql $trp_ssql $ord";
  $db_trip = $obj->MySQLSelect($sql_admin);
  //    echo "<pre>";print_r($db_trip); exit;

  $header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']." No." . "\t";
  $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." Name" . "\t";
  $header .= $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']." Name" . "\t";
  $header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']." Date" . "\t";
  $header .= "Total Fare" . "\t";
  $header .= "Platform Fees" . "\t";
  $header .= "Promo Code Discount" . "\t";
  $header .= "Wallet Debit" . "\t";
  if ($ENABLE_TIP_MODULE == "Yes") {
  $header .= "Tip" . "\t";
  }
  $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." pay Amount" . "\t";
  $header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']." Status" . "\t";
  $header .= "Payment method" . "\t";
  $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." Payment Status";

  $tot_fare = 0.00;
  $tot_site_commission = 0.00;
  $tot_promo_discount = 0.00;
  $tot_driver_refund = 0.00;
  $tot_wallentPayment = 0.00;
  $total_tip = 0.00;

  for ($j = 0; $j < count($db_trip); $j++) {
  $driver_payment = 0.00;

  $totalfare = $db_trip[$j]['fTripGenerateFare'];
  $site_commission = $db_trip[$j]['fCommision'];
  $promocodediscount = $db_trip[$j]['fDiscount'];
  $wallentPayment = $db_trip[$j]['fWalletDebit'];
  $fTipPrice = $db_trip[$j]['fTipPrice'];
  $driver_payment = $totalfare - $site_commission;

  $tot_fare = $tot_fare + $totalfare;
  $tot_site_commission = $tot_site_commission + $site_commission;
  $tot_promo_discount = $tot_promo_discount + $promocodediscount;
  $tot_wallentPayment = $tot_wallentPayment + $wallentPayment;
  $total_tip = $total_tip + $fTipPrice;
  $tot_driver_refund = $tot_driver_refund + $driver_payment;

  if ($db_trip[$j]['eMBirr'] == "Yes") {
  $paymentmode = "M-birr";
  } else {
  $paymentmode = $db_trip[$j]['vTripPaymentMode'];
  }

  $data .= $db_trip[$j]['vRideNo'] . "\t";
  $data .= clearName($db_trip[$j]['drivername']) . "\t";
  $data .= clearName($db_trip[$j]['riderName']) . "\t";
  $data .= date('d-m-Y', strtotime($db_trip[$j]['tTripRequestDate'])) . "\t";
  $data .= ($db_trip[$j]['fTripGenerateFare'] != "" && $db_trip[$j]['fTripGenerateFare'] != 0) ? trip_currency($db_trip[$j]['fTripGenerateFare']) . "\t" : "- \t";
  $data .= ($db_trip[$j]['fCommision'] != "" && $db_trip[$j]['fCommision'] != 0) ? trip_currency($db_trip[$j]['fCommision']) . "\t" : "- \t";
  $data .= ($db_trip[$j]['fDiscount'] != "" && $db_trip[$j]['fDiscount'] != 0) ? trip_currency($db_trip[$j]['fDiscount']) . "\t" : "- \t";
  $data .= ($db_trip[$j]['fWalletDebit'] != "" && $db_trip[$j]['fWalletDebit'] != 0) ? trip_currency($db_trip[$j]['fWalletDebit']) . "\t" : "- \t";
  if ($ENABLE_TIP_MODULE == "Yes") {
  $data .= ($db_trip[$j]['fTipPrice'] != "" && $db_trip[$j]['fTipPrice'] != 0) ? trip_currency($db_trip[$j]['fTipPrice']) . "\t" : "- \t";
  }
  $data .= ($driver_payment != "" && $driver_payment != 0) ? trip_currency($driver_payment) . "\t" : "- \t";
  $data .= $db_trip[$j]['iActive'] . "\t";
  $data .= $paymentmode . "\t";
  $data .= $db_trip[$j]['eDriverPaymentStatus'] . "\n";
  }
  $data .= "\n\t\t\t\t\t\t\t\t\tTotal Fare\t" . trip_currency($tot_fare) . "\n";
  $data .= "\t\t\t\t\t\t\t\t\tTotal Platform Fees\t" . trip_currency($tot_site_commission) . "\n";
  $data .= "\t\t\t\t\t\t\t\t\tTotal Promo Discount\t" . trip_currency($tot_promo_discount) . "\n";
  $data .= "\t\t\t\t\t\t\t\t\tTotal Wallet Debit\t" . trip_currency($tot_wallentPayment) . "\n";
  if ($ENABLE_TIP_MODULE == "Yes") {
  $data .= "\t\t\t\t\t\t\t\t\tTotal ".$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." Payment\t" . trip_currency($total_tip) . "\n";
  $data .= "\t\t\t\t\t\t\t\t\tTotal ".$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." Payment\t" . trip_currency($tot_driver_refund+$total_tip) . "\n";
  }else {
  $data .= "\t\t\t\t\t\t\t\t\tTotal ".$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." Payment\t" . trip_currency($tot_driver_refund) . "\n";
  }
  $data = str_replace("\r", "", $data);
  #echo "<br>".$data; exit;
  ob_clean();
  header("Content-type: application/octet-stream; charset=utf-8");
  header("Content-Disposition: attachment; filename=payment_reports.xls");
  header("Pragma: no-cache");
  header("Expires: 0");
  print "$header\n$data";
  exit;
  } */
if ('driver_payment_report' === $section) {
    $script = 'Deliverall Driver Payment Report';
    function cleanNumber($num)
    {
        return str_replace(',', '', $num);
    }

    // data for select fields
    $sql = "select iUserId,CONCAT(vName,' ',vLastName) AS riderName,vEmail from register_user WHERE eStatus != 'Deleted' order by vName";
    $db_rider = $obj->MySQLSelect($sql);
    // data for select fields
    // Start Sorting
    $sortby = $_REQUEST['sortby'] ?? 0;
    $order = $_REQUEST['order'] ?? '';
    $ord = ' ORDER BY tr.iTripId DESC';
    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY rd.vName ASC';
        } else {
            $ord = ' ORDER BY rd.vName DESC';
        }
    }
    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY ru.vName ASC';
        } else {
            $ord = ' ORDER BY ru.vName DESC';
        }
    }
    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY o.tOrderRequestDate ASC';
        } else {
            $ord = ' ORDER BY o.tOrderRequestDate DESC';
        }
    }
    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY d.vName ASC';
        } else {
            $ord = ' ORDER BY d.vName DESC';
        }
    }
    if (5 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY u.vName ASC';
        } else {
            $ord = ' ORDER BY u.vName DESC';
        }
    }
    // End Sorting
    // Start Search Parameters
    $ssql = '';
    $action = $_REQUEST['action'] ?? '';
    $searchCompany = $_REQUEST['searchCompany'] ?? '';
    $searchDriver = $_REQUEST['searchDriver'] ?? '';
    $searchRider = $_REQUEST['searchRider'] ?? '';
    $serachTripNo = $_REQUEST['serachTripNo'] ?? '';
    $searchDriverPayment = $_REQUEST['searchDriverPayment'] ?? '';
    $searchPaymentType = $_REQUEST['searchPaymentType'] ?? '';
    $searchServiceType = $_REQUEST['searchServiceType'] ?? '';
    $startDate = $_REQUEST['startDate'] ?? '';
    $endDate = $_REQUEST['endDate'] ?? '';
    if ('search' === $action) {
        if ('' !== $startDate) {
            $ssql .= " AND Date(o.tOrderRequestDate) >='".$startDate."'";
        }
        if ('' !== $endDate) {
            $ssql .= " AND Date(o.tOrderRequestDate) <='".$endDate."'";
        }
        if ('' !== $serachTripNo) {
            $ssql .= " AND o.vOrderNo ='".$serachTripNo."'";
        }
        if ('' !== $searchCompany) {
            $ssql .= " AND rd.iCompanyId ='".$searchCompany."'";
        }
        if ('' !== $searchDriver) {
            $ssql .= " AND o.iDriverId ='".$searchDriver."'";
        }
        if ('' !== $searchRider) {
            $ssql .= " AND tr.iUserId ='".$searchRider."'";
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
        if ('' !== $searchDriverPayment) {
            $ssql .= " AND tr.eDriverPaymentStatus ='".$searchDriverPayment."'";
        }
        if ('' !== $searchPaymentType) {
            $ssql .= " AND tr.vTripPaymentMode ='".$searchPaymentType."'";
        }
    }
    $trp_ssql = '';
    if (SITE_TYPE === 'Demo') {
        $trp_ssql = " And o.tOrderRequestDate > '".WEEK_DATE."'";
    }
    // Pagination Start
    $per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
    $sql = 'SELECT tr.fDeliveryCharge,o.fTipAmount,tr.vTripPaymentMode,sc.vServiceName_'.$default_lang." as vServiceName,o.fDriverPaidAmount, o.iStatusCode, odcd.fDeliveryCharge as fCustomDeliveryCharge,vt.fDeliveryCharge as fDeliveryChargeVehicle, o.fTipAmount, o.eBuyAnyService,o.ePaymentOption, o.iOrderId, ( SELECT COUNT(tr.iTripId) FROM trips AS tr LEFT JOIN orders as o on o.iOrderId=tr.iOrderId LEFT JOIN register_driver AS rd ON tr.iDriverId = rd.iDriverId LEFT JOIN register_user AS ru ON tr.iUserId = ru.iUserId LEFT JOIN company as c ON rd.iCompanyId = c.iCompanyId WHERE 1=1 AND o.iStatusCode = 6 AND tr.eSystem = 'Deliverall' {$ssql} {$trp_ssql}) AS Total FROM trips AS tr LEFT JOIN orders as o on o.iOrderId=tr.iOrderId LEFT JOIN register_driver AS rd ON tr.iDriverId = rd.iDriverId LEFT JOIN register_user AS ru ON tr.iUserId = ru.iUserId LEFT JOIN company as c ON rd.iCompanyId = c.iCompanyId LEFT JOIN service_categories as sc on sc.iServiceid = o.iServiceid LEFT JOIN order_delivery_charge_details as odcd ON odcd.iOrderId = tr.iOrderId LEFT JOIN vehicle_type as vt ON vt.iVehicleTypeId = tr.iVehicleTypeId WHERE  1 = 1 AND o.iStatusCode = 6 AND tr.eSystem = 'Deliverall' {$ssql} {$trp_ssql}";
    $totalData = $obj->MySQLSelect($sql);
    $tot_driver_payment = 0.00;
    $total_driver_earning = 0.00;
    foreach ($totalData as $dtps) {
        $site_commission = $dtps['fDeliveryCharge'];
        $subtotal = 0;
        if ('Yes' === $dtps['eBuyAnyService'] && 'Card' === $dtps['ePaymentOption']) {
            $order_buy_anything = $obj->MySQLSelect("SELECT * FROM order_items_buy_anything WHERE iOrderId = '".$dtps['iOrderId']."'");
            if (count($order_buy_anything) > 0) {
                foreach ($order_buy_anything as $oItem) {
                    if ('Yes' === $oItem['eConfirm']) {
                        $subtotal += $oItem['fItemPrice'];
                    }
                }
            }
        }
        if ('7' === $dtps['iStatusCode'] || '8' === $dtps['iStatusCode']) {
            $fDriverPaidAmount = $dtps['fDriverPaidAmount'];
        } else {
            $fDriverPaidAmount = $dtps['fDeliveryCharge'];
            // $fDriverPaidAmount = $fDriverPaidAmount - ($fDriverPaidAmount - ($dtps['fCustomDeliveryCharge'] + $dtps['fDeliveryChargeVehicle']));
            $fDriverPaidAmount = $fDriverPaidAmount + $dtps['fTipAmount'] + $subtotal;
        }
        $tot_driver_payment += cleanNumber($site_commission);
        $total_driver_earning += cleanNumber($fDriverPaidAmount);
    }
    $total_results = $totalData[0]['Total'];
    $total_pages = ceil($total_results / $per_page); // total pages we going to have
    $show_page = 1;
    // -------------if page is setcheck------------------//
    if (isset($_GET['page'])) {
        $show_page = $_GET['page'];             // it will telles the current page
        if ($show_page > 0 && $show_page <= $total_pages) {
            $start = ($show_page - 1) * $per_page;
            $end = $start + $per_page;
        } else {
            // error - show first set of results
            $start = 0;
            $end = $per_page;
        }
    } else {
        // if page isn't set, show first set of results
        $start = 0;
        $end = $per_page;
    }
    // display pagination
    $page = isset($_GET['page']) ? (int) ($_GET['page']) : 0;
    $tpages = $total_pages;
    if ($page <= 0) {
        $page = 1;
    }
    // Pagination End
    $sql = 'SELECT tr.iTripId,o.fTipAmount,o.iOrderId,o.vOrderNo,sc.vServiceName_'.$default_lang." as vServiceName,o.iCompanyId,o.iDriverId,o.fDriverPaidAmount,o.iStatusCode,o.iUserId,o.tOrderRequestDate,tr.fDeliveryCharge, tr.eDriverPaymentStatus,tr.vTripPaymentMode,os.vStatus,c.vCompany,concat(rd.vName,' ',rd.vLastName) as drivername,concat(ru.vName,' ',ru.vLastName) as riderName,CONCAT(ru.vPhoneCode,' ',ru.vPhone)  as user_phone,CONCAT(rd.vCode,' ',rd.vPhone) as driver_phone,CONCAT(c.vCode,' ',c.vPhone) as resturant_phone, odcd.fDeliveryCharge as fCustomDeliveryCharge,vt.fDeliveryCharge as fDeliveryChargeVehicle, o.fTipAmount,o.eBuyAnyService,o.ePaymentOption,o.eForPickDropGenie FROM trips AS tr LEFT JOIN register_driver AS rd ON tr.iDriverId = rd.iDriverId LEFT JOIN orders as o on o.iOrderId=tr.iOrderId LEFT JOIN order_status as os on os.iStatusCode = o.iStatusCode LEFT JOIN register_user AS ru ON tr.iUserId = ru.iUserId LEFT JOIN company as c ON o.iCompanyId = c.iCompanyId LEFT JOIN service_categories as sc on sc.iServiceid = o.iServiceid LEFT JOIN order_delivery_charge_details as odcd ON odcd.iOrderId = tr.iOrderId LEFT JOIN vehicle_type as vt ON vt.iVehicleTypeId = tr.iVehicleTypeId  WHERE 1 = 1 AND o.iStatusCode = 6 AND tr.eSystem = 'Deliverall' {$ssql} {$trp_ssql} GROUP BY tr.iTripId {$ord} ";
    $db_trip = $obj->MySQLSelect($sql);
    $endRecord = count($db_trip);
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
    $monday = date('Y-m-d', strtotime('sunday this week -1 week'));
    $sunday = date('Y-m-d', strtotime('saturday this week'));
    $Pmonday = date('Y-m-d', strtotime('sunday this week -2 week'));
    $Psunday = date('Y-m-d', strtotime('saturday this week -1 week'));
    $sql = "select iDriverId,CONCAT(vName,' ',vLastName) AS driverName,vEmail from register_driver WHERE iDriverId ='".$searchDriver."'  order by vName";
    $db_drivers = $obj->MySQLSelect($sql);
    $catdata = serviceCategories;
    $allservice_cat_data = json_decode($catdata, true);
    // Added By HJ On 26-08-2019 For Changed Word Of Card As Per Bug - 225 server 6736 Start
    $cardText = 'Card';
    if ('Method-2' === $SYSTEM_PAYMENT_FLOW || 'Method-3' === $SYSTEM_PAYMENT_FLOW) {
        $cardText = 'Wallet';
    }
    $header = $data = '';
    if (count($allservice_cat_data) > 1) {
        $header .= 'Service type'."\t";
    }
    $header .= 'Order No#'."\t";
    $header .= 'Order Date'."\t";
    $header .= 'Driver'."\t";
    $header .= 'User'."\t";
    $header .= 'Restaurant Name'."\t";
    $header .= 'Driver Pay Amount'."\t";
    $header .= 'Order Status'."\t";
    $header .= 'Payment method'."\t";
    $header .= 'Driver Payment Status'."\t";
    if (count($db_trip) > 0) {
        for ($i = 0; $i < count($db_trip); ++$i) {
            $subtotal = 0;
            if ('Yes' === $db_trip[$i]['eBuyAnyService']) {
                $db_trip[$i]['vServiceName'] = $langage_lbl_admin['LBL_OTHER_DELIVERY'];
                if ('Yes' === $db_trip[$i]['eForPickDropGenie']) {
                    $db_trip[$i]['vServiceName'] = $langage_lbl_admin['LBL_RUNNER'];
                }
                if ('Card' === $db_trip[$i]['ePaymentOption']) {
                    $order_buy_anything = $obj->MySQLSelect("SELECT * FROM order_items_buy_anything WHERE iOrderId = '".$db_trip[$i]['iOrderId']."'");
                    if (count($order_buy_anything) > 0) {
                        foreach ($order_buy_anything as $oItem) {
                            if ('Yes' === $oItem['eConfirm']) {
                                $subtotal += $oItem['fItemPrice'];
                            }
                        }
                    }
                }
            }
            $class_setteled = '';
            if ('Settelled' === $db_trip[$i]['eDriverPaymentStatus']) {
                $class_setteled = 'setteled-class';
            }
            $site_commission = $db_trip[$i]['fDeliveryCharge'];
            $set_unsetarray[] = $db_trip[$i]['eDriverPaymentStatus'];
            if ('7' === $db_trip[$i]['iStatusCode'] || '8' === $db_trip[$i]['iStatusCode']) {
                $driverEarning = $db_trip[$i]['fDriverPaidAmount'];
            } else {
                $driverEarning = $db_trip[$i]['fDeliveryCharge'];
                // $driverEarning = $driverEarning - ($driverEarning - ($db_trip[$i]['fCustomDeliveryCharge'] + $db_trip[$i]['fDeliveryChargeVehicle']));
                $driverEarning = $driverEarning + $db_trip[$i]['fTipAmount'] + $subtotal;
            }
            if ('' !== $db_trip[$i]['driver_phone']) {
                $vdrivername = clearName($db_trip[$i]['drivername']);
                $vdrivername .= '   ';
                $vdrivername .= '  Phone: +'.clearPhone($db_trip[$i]['driver_phone']);
            } else {
                $vdrivername .= clearName($db_trip[$i]['drivername']);
            }
            if ('' !== $db_trip[$i]['user_phone']) {
                $vRiderName = clearName($db_trip[$i]['riderName']);
                $vRiderName .= '   ';
                $vRiderName .= '  Phone: +'.clearPhone($db_trip[$i]['user_phone']);
            } else {
                $vRiderName = clearName($db_trip[$i]['riderName']);
            }
            if ('' !== $db_trip[$i]['resturant_phone']) {
                $vCompany = clearName($db_trip[$i]['vCompany']);
                $vCompany .= '    ';
                $vCompany .= '  Phone: +'.clearPhone($db_trip[$i]['resturant_phone']);
            } else {
                $vCompany = clearName($db_trip[$i]['vCompany']);
            }
            $vTripPaymentMode = $db_trip[$i]['vTripPaymentMode'];
            if ('Card' === $db_trip[$i]['vTripPaymentMode']) {
                $vTripPaymentMode = $cardText;
            }
            if (count($allservice_cat_data) > 1) {
                $data .= $db_trip[$i]['vServiceName']."\t";
            }
            $data .= $db_trip[$i]['vOrderNo']."\t";
            $data .= DateTime($db_trip[$i]['tOrderRequestDate'])."\t";
            $data .= $vdrivername."\t";
            $data .= $vRiderName."\t";
            $data .= $vCompany."\t";
            if ($db_trip[$i]['fTipAmount'] > 0) {
                $data .= formateNumAsPerCurrency($driverEarning, '').' (Including Driver Tip: '.formateNumAsPerCurrency($db_trip[$i]['fTipAmount'], '').")\t";
            } else {
                $data .= formateNumAsPerCurrency($driverEarning, '')."\t";
            }
            $data .= $db_trip[$i]['vStatus']."\t";
            $data .= $vTripPaymentMode."\t";
            $data .= $db_trip[$i]['eDriverPaymentStatus']."\n";
            // $data .= $db_trip[$i]['eRestaurantPaymentStatus'] . "\n";
        }
        $data .= "\n\n\n";
        $data .= 'Total Driver Payment : '.formateNumAsPerCurrency($total_driver_earning, '')."\n";
        $data = str_replace("\r", '', $data);
    }
    $timenow = time();
    $filename = 'driver_payment_report_'.$timenow.'.xls';
    ob_clean();
    header('Content-type: application/octet-stream; charset=utf-8');
    header("Content-Disposition: attachment; filename={$filename}");
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "{$header}\n{$data}";

    exit;
}
if ('store_payment_report' === $section) {
    $script = 'Restaurant Payment Report';
    $eSystem = " AND eSystem = 'DeliverAll'";
    function cleanNumber($num)
    {
        return str_replace(',', '', $num);
    }

    $catdata = serviceCategories;
    $allservice_cat_data = json_decode($catdata, true);
    // data for select fields
    $sql = "select iCompanyId,vCompany,vEmail from company WHERE eStatus != 'Deleted' {$eSystem} order by vCompany";
    $db_company = $obj->MySQLSelect($sql);
    // data for select fields
    // Start Sorting
    $sortby = $_REQUEST['sortby'] ?? 0;
    $order = $_REQUEST['order'] ?? '';
    $ord = ' ORDER BY o.iOrderId DESC';
    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY c.vCompany ASC';
        } else {
            $ord = ' ORDER BY c.vCompany DESC';
        }
    }
    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY rd.vName ASC';
        } else {
            $ord = ' ORDER BY rd.vName DESC';
        }
    }
    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY ru.vName ASC';
        } else {
            $ord = ' ORDER BY ru.vName DESC';
        }
    }
    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY o.tOrderRequestDate ASC';
        } else {
            $ord = ' ORDER BY o.tOrderRequestDate DESC';
        }
    }
    if (5 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY o.ePaymentOption ASC';
        } else {
            $ord = ' ORDER BY o.ePaymentOption DESC';
        }
    }
    // End Sorting
    // Start Search Parameters
    $ssql = '';
    $action = $_REQUEST['action'] ?? '';
    $searchCompany = $_REQUEST['searchCompany'] ?? '';
    $serachOrderNo = $_REQUEST['serachOrderNo'] ?? '';
    $searchRestaurantPayment = $_REQUEST['searchRestaurantPayment'] ?? '';
    $searchPaymentType = $_REQUEST['searchPaymentType'] ?? '';
    $startDate = $_REQUEST['startDate'] ?? '';
    $searchServiceType = $_REQUEST['searchServiceType'] ?? '';
    $endDate = $_REQUEST['endDate'] ?? '';
    $eType = $_REQUEST['eType'] ?? '';
    if ('search' === $action) {
        if ('' !== $startDate) {
            $ssql .= " AND Date(o.tOrderRequestDate) >='".$startDate."'";
        }
        if ('' !== $endDate) {
            $ssql .= " AND Date(o.tOrderRequestDate) <='".$endDate."'";
        }
        if ('' !== $serachOrderNo) {
            $ssql .= " AND o.vOrderNo ='".$serachOrderNo."'";
        }
        if ('' !== $searchCompany) {
            $ssql .= " AND c.iCompanyId ='".$searchCompany."'";
        }
        if ('' !== $searchRestaurantPayment) {
            $ssql .= " AND o.eRestaurantPaymentStatus ='".$searchRestaurantPayment."'";
        }
        if ('' !== $searchServiceType) {
            $ssql .= " AND sc.iServiceId ='".$searchServiceType."'";
        }
        if ('' !== $searchPaymentType) {
            $ssql .= " AND o.ePaymentOption ='".$searchPaymentType."'";
        }
    }
    $trp_ssql = '';
    if (SITE_TYPE === 'Demo') {
        $trp_ssql = " And o.tOrderRequestDate > '".WEEK_DATE."'";
    }
    // Pagination Start
    $per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
    $sql = 'SELECT o.iOrderId,o.vOrderNo,o.fTipAmount,sc.vServiceName_'.$default_lang." as vServiceName,o.iCompanyId,o.iDriverId,o.iUserId,o.fRestaurantPayAmount,o.fRestaurantPaidAmount,o.fOutStandingAmount,o.tOrderRequestDate,o.fTotalGenerateFare,o.fDeliveryCharge,o.fOffersDiscount,o.fCommision,o.eRestaurantPaymentStatus,o.ePaymentOption,o.iStatusCode,os.vStatus ,( SELECT COUNT(o.iOrderId) FROM orders AS o LEFT JOIN register_driver AS rd ON o.iDriverId = rd.iDriverId LEFT JOIN register_user AS ru ON o.iUserId = ru.iUserId LEFT JOIN company as c ON o.iCompanyId = c.iCompanyId WHERE 1=1  AND (o.iStatusCode = '6' OR o.fRestaurantPayAmount > 0) {$ssql} {$trp_ssql}) AS Total FROM orders AS o LEFT JOIN register_driver AS rd ON o.iDriverId = rd.iDriverId LEFT JOIN register_user AS ru ON o.iUserId = ru.iUserId LEFT JOIN company AS c ON o.iCompanyId = c.iCompanyId LEFT JOIN order_status AS os ON os.iStatusCode=o.iStatusCode LEFT JOIN service_categories as sc on sc.iServiceid = o.iServiceid WHERE IF(o.eTakeaway = 'Yes' && os.iStatusCode = 6, os.eTakeaway='Yes', os.eTakeaway != 'Yes') AND (o.iStatusCode = '6') {$ssql} {$trp_ssql}";
    // OR o.fRestaurantPayAmount > 0
    $totalData = $obj->MySQLSelect($sql);
    $tot_order_amount = 0.00;
    $tot_site_commission = 0.00;
    $tot_delivery_charges = 0.00;
    $tot_offer_discount = 0.00;
    $tot_restaurant_payment = 0.00;
    $expected_rest_payment = 0.00;
    $tot_outstanding_amount = 0.00;
    foreach ($totalData as $dtps) {
        $totalfare = $dtps['fTotalGenerateFare'];
        $fOffersDiscount = $dtps['fOffersDiscount'];
        $fDeliveryCharge = $dtps['fDeliveryCharge'];
        $site_commission = $dtps['fCommision'];
        $totaltipamount = $dtps['fTipAmount'];
        $fRestaurantPayAmount = $dtps['fRestaurantPayAmount'];
        $fRestaurantPaidAmount = $dtps['fRestaurantPaidAmount'];
        $fOutStandingAmount = $dtps['fOutStandingAmount'];
        if ('7' === $dtps['iStatusCode'] || '8' === $dtps['iStatusCode']) {
            $fRestexpectedearning = $fRestaurantPayAmount;
        } else {
            $fRestexpectedearning = $totalfare - cleanNumber($site_commission) - cleanNumber($fOffersDiscount) - cleanNumber($fDeliveryCharge) - cleanNumber($fOutStandingAmount);
        }
        if ('7' === $dtps['iStatusCode'] || '8' === $dtps['iStatusCode']) {
            $restaurant_payment = $fRestaurantPaidAmount;
        } else {
            $restaurant_payment = $totalfare - cleanNumber($site_commission) - cleanNumber($fOffersDiscount) - cleanNumber($fDeliveryCharge) - cleanNumber($fOutStandingAmount) - cleanNumber($totaltipamount);
        }
        $tot_order_amount += cleanNumber($totalfare);
        $tot_offer_discount += cleanNumber($fOffersDiscount);
        $tot_delivery_charges += cleanNumber($fDeliveryCharge);
        $tot_site_commission += cleanNumber($site_commission);
        $expected_rest_payment += cleanNumber($fRestexpectedearning);
        $tot_restaurant_payment += cleanNumber($restaurant_payment);
        $tot_outstanding_amount += cleanNumber($fOutStandingAmount);
        $tot_tip_amount = $totaltipamount + cleanNumber($totaltipamount);
    }
    $total_results = $totalData[0]['Total'];
    $total_pages = ceil($total_results / $per_page); // total pages we going to have
    $show_page = 1;
    // -------------if page is setcheck------------------//
    if (isset($_GET['page'])) {
        $show_page = $_GET['page'];             // it will telles the current page
        if ($show_page > 0 && $show_page <= $total_pages) {
            $start = ($show_page - 1) * $per_page;
            $end = $start + $per_page;
        } else {
            // error - show first set of results
            $start = 0;
            $end = $per_page;
        }
    } else {
        // if page isn't set, show first set of results
        $start = 0;
        $end = $per_page;
    }
    // display pagination
    $page = isset($_GET['page']) ? (int) ($_GET['page']) : 0;
    $tpages = $total_pages;
    if ($page <= 0) {
        $page = 1;
    }
    // Pagination End
    $sql = 'SELECT o.iOrderId,o.vOrderNo,o.fTipAmount,o.iCompanyId,sc.vServiceName_'.$default_lang." as vServiceName,o.iDriverId,o.iUserId,o.tOrderRequestDate,o.fRestaurantPayAmount,o.fRestaurantPaidAmount,o.fOutStandingAmount,o.fTotalGenerateFare,o.fDeliveryCharge,o.fOffersDiscount,o.fCommision,o.eRestaurantPaymentStatus,o.ePaymentOption,o.iStatusCode,os.vStatus,c.vCompany,concat(rd.vName,' ',rd.vLastName) as drivername,concat(ru.vName,' ',ru.vLastName) as riderName,CONCAT(ru.vPhoneCode,' ',ru.vPhone)  as user_phone,CONCAT(rd.vCode,' ',rd.vPhone) as driver_phone,CONCAT(c.vCode,' ',c.vPhone) as resturant_phone FROM orders AS o LEFT JOIN register_driver AS rd ON o.iDriverId = rd.iDriverId LEFT JOIN register_user AS ru ON o.iUserId = ru.iUserId LEFT JOIN company as c ON o.iCompanyId = c.iCompanyId LEFT JOIN order_status as os on os.iStatusCode=o.iStatusCode LEFT JOIN service_categories as sc on sc.iServiceid = o.iServiceid WHERE IF(o.eTakeaway = 'Yes' && os.iStatusCode = 6, os.eTakeaway='Yes', os.eTakeaway != 'Yes') AND (o.iStatusCode = '6') {$ssql} {$trp_ssql} {$ord} "; // LIMIT $start, $per_page
    // OR o.fRestaurantPayAmount > 0
    $db_trip = $obj->MySQLSelect($sql);
    $endRecord = count($db_trip);
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
    $monday = date('Y-m-d', strtotime('sunday this week -1 week'));
    $sunday = date('Y-m-d', strtotime('saturday this week'));
    $Pmonday = date('Y-m-d', strtotime('sunday this week -2 week'));
    $Psunday = date('Y-m-d', strtotime('saturday this week -1 week'));
    $catdata = serviceCategories;
    $allservice_cat_data = json_decode($catdata, true);
    // Added By HJ On 26-08-2019 For Changed Word Of Card As Per Bug - 225 server 6736 Start
    $cardText = 'Card';
    if ('Method-2' === $SYSTEM_PAYMENT_FLOW || 'Method-3' === $SYSTEM_PAYMENT_FLOW) {
        $cardText = 'Wallet';
    }
    if (count($allservice_cat_data) > 1) {
        $header .= 'Service Type'."\t";
    }
    $header .= 'Order No#'."\t";
    $header .= 'Restaurant'."\t";
    $header .= 'Driver'."\t";
    $header .= 'User'."\t";
    $header .= 'Order Date'."\t";
    $header .= 'A=Total Order Amount'."\t";
    $header .= 'B=Site Commission'."\t";
    $header .= 'C=Delivery Charges'."\t";
    $header .= 'D=Offer Amount'."\t";
    $header .= 'E=Outstanding Amount'."\t";
    $header .= 'F=Tip Amount'."\t";
    $header .= 'G=A-B-C-D-E-F Final Restaurant Pay Amount'."\t";
    $header .= 'Order Status'."\t";
    $header .= 'Payment method'."\t";
    $header .= 'Restaurant Payment Status'."\t";
    if (count($db_trip) > 0) {
        for ($i = 0; $i < count($db_trip); ++$i) {
            $class_setteled = '';
            if ('Settled' === $db_trip[$i]['eRestaurantPaymentStatus']) {
                $class_setteled = 'setteled-class';
            }
            $totalfare = $db_trip[$i]['fTotalGenerateFare'];
            $site_commission = $db_trip[$i]['fCommision'];
            $fOffersDiscount = $db_trip[$i]['fOffersDiscount'];
            $fDeliveryCharge = $db_trip[$i]['fDeliveryCharge'];
            $fOutStandingAmount = $db_trip[$i]['fOutStandingAmount'];
            $fTipAmount = $db_trip[$i]['fTipAmount'];
            //     /* if($db_trip[$i]['iStatusCode'] == '7' || $db_trip[$i]['iStatusCode'] == '8') {
            //       $expectedpaymentamount  = $db_trip[$i]['fRestaurantPayAmount'];
            //       } else {
            //       $expectedpaymentamount = $totalfare - cleanNumber($site_commission) - cleanNumber($fOffersDiscount) - cleanNumber($fDeliveryCharge);
            //       } */
            if ('7' === $db_trip[$i]['iStatusCode'] || '8' === $db_trip[$i]['iStatusCode']) {
                $restaurant_payment = $db_trip[$i]['fRestaurantPaidAmount'];
            } else {
                $restaurant_payment = $totalfare - cleanNumber($site_commission) - cleanNumber($fOffersDiscount) - cleanNumber($fDeliveryCharge) - cleanNumber($fOutStandingAmount) - cleanNumber($fTipAmount);
            }
            $set_unsetarray[] = $db_trip[$i]['eRestaurantPaymentStatus'];
            if (!empty($db_trip[$i]['drivername'])) {
                $drivername = $db_trip[$i]['drivername'];
            } else {
                $drivername = '--';
            }
            if ('' !== $db_trip[$i]['resturant_phone']) {
                $vCompany = clearCmpName($db_trip[$i]['vCompany']);
                $vCompany .= '    '.'  ';
                $vCompany .= '   Phone: +'.clearPhone($db_trip[$i]['resturant_phone']);
            } else {
                $vCompany = clearCmpName($db_trip[$i]['vCompany']);
            }
            if ('' !== $db_trip[$i]['driver_phone']) {
                $vDriverName = clearName($drivername);
                $vDriverName .= '   ';
                $vDriverName .= ' Phone: +'.clearPhone($db_trip[$i]['driver_phone']);
            } else {
                $vDriverName = clearName($drivername);
            }
            if ('' !== $db_trip[$i]['user_phone']) {
                $vRiderName = clearName($db_trip[$i]['riderName']);
                $vRiderName .= '    ';
                $vRiderName .= ' Phone: +'.clearPhone($db_trip[$i]['user_phone']);
            } else {
                $vRiderName = clearName($db_trip[$i]['riderName']);
            }
            if ('' !== $db_trip[$i]['fTotalGenerateFare'] && 0 !== $db_trip[$i]['fTotalGenerateFare']) {
                $vfTotalGenerateFare = formateNumAsPerCurrency($db_trip[$i]['fTotalGenerateFare'], '');
            } else {
                $vfTotalGenerateFare = '-';
            }
            if ('' !== $db_trip[$i]['fCommision'] && 0 !== $db_trip[$i]['fCommision']) {
                $vfCommision = formateNumAsPerCurrency($db_trip[$i]['fCommision'], '');
            } else {
                $vfCommision = '-';
            }
            if ('' !== $db_trip[$i]['fDeliveryCharge'] && 0 !== $db_trip[$i]['fDeliveryCharge']) {
                $vfDeliveryCharge = formateNumAsPerCurrency($db_trip[$i]['fDeliveryCharge'], '');
            } else {
                $vfDeliveryCharge = '-';
            }
            if ('' !== $db_trip[$i]['fOffersDiscount'] && 0 !== $db_trip[$i]['fOffersDiscount']) {
                $vfOffersDiscount = formateNumAsPerCurrency($db_trip[$i]['fOffersDiscount'], '');
            } else {
                $vfOffersDiscount = '-';
            }
            if ('' !== $db_trip[$i]['fOutStandingAmount'] && 0 !== $db_trip[$i]['fOutStandingAmount']) {
                $vfOutStandingAmount = formateNumAsPerCurrency($db_trip[$i]['fOutStandingAmount'], '');
            } else {
                $vfOutStandingAmount = '-';
            }
            if ('' !== $db_trip[$i]['fTipAmount'] && 0 !== $db_trip[$i]['fTipAmount']) {
                $vfTipAmount = formateNumAsPerCurrency($db_trip[$i]['fTipAmount'], '');
            } else {
                $vfTipAmount = '-';
            }
            if ('' !== $restaurant_payment && 0 !== $restaurant_payment) {
                $vrestaurant_payment = formateNumAsPerCurrency($restaurant_payment, '');
            } else {
                $vrestaurant_payment = '-';
            }
            $ePaymentOption = $db_trip[$i]['ePaymentOption'];
            if ('Card' === $db_trip[$i]['ePaymentOption']) {
                $ePaymentOption = $cardText;
            }
            if (count($allservice_cat_data) > 1) {
                $data .= $db_trip[$i]['vServiceName']."\t";
            }
            $data .= $db_trip[$i]['vOrderNo']."\t";
            $data .= $vCompany."\t";
            $data .= $vDriverName."\t";
            $data .= $vRiderName."\t";
            $data .= DateTime($db_trip[$i]['tOrderRequestDate'])."\t";
            $data .= $vfTotalGenerateFare."\t";
            $data .= $vfCommision."\t";
            $data .= $vfDeliveryCharge."\t";
            $data .= $vfOffersDiscount."\t";
            $data .= $vfOutStandingAmount."\t";
            $data .= $vfTipAmount."\t";
            $data .= $vrestaurant_payment."\t";
            $data .= $db_trip[$i]['vStatus']."\t";
            $data .= $ePaymentOption."\t";
            $data .= $db_trip[$i]['eRestaurantPaymentStatus']."\n";
        }
        $data .= "\n\n\n";
        $data .= 'Total Fare : '.formateNumAsPerCurrency($tot_order_amount, '')."\n";
        $data .= 'Total Site Commission : '.formateNumAsPerCurrency($tot_site_commission, '')."\n";
        $data .= 'Total Delivery Charges : '.formateNumAsPerCurrency($tot_delivery_charges, '')."\n";
        $data .= 'Total Offer Amount : '.formateNumAsPerCurrency($tot_offer_discount, '')."\n";
        $data .= 'Total Outstanding Amount : '.formateNumAsPerCurrency($tot_outstanding_amount, '')."\n";
        $data .= 'Total '.$langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN'].' Payment: '.formateNumAsPerCurrency($tot_restaurant_payment, '')."\n";
        $data = str_replace("\r", '', $data);
    }
    $timenow = time();
    $filename = 'restaurant_payment_report_'.$timenow.'.xls';
    ob_clean();
    header('Content-type: application/octet-stream; charset=utf-8');
    header("Content-Disposition: attachment; filename={$filename}");
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "{$header}\n{$data}";

    exit;
}
if ('driver_log_report' === $section) {
    $dlp_ssql = '';
    $ord = ' ORDER BY dlr.iDriverLogId DESC';
    // Start Sorting
    $sortby = $_REQUEST['sortby'] ?? 0;
    $order = $_REQUEST['order'] ?? '';
    $ord = ' ORDER BY dlr.iDriverLogId DESC';
    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY rd.vName ASC';
        } else {
            $ord = ' ORDER BY rd.vName DESC';
        }
    }
    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY rd.vEmail ASC';
        } else {
            $ord = ' ORDER BY rd.vEmail DESC';
        }
    }
    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY dlr.dLoginDateTime ASC';
        } else {
            $ord = ' ORDER BY dlr.dLoginDateTime DESC';
        }
    }
    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY dlr.dLogoutDateTime ASC';
        } else {
            $ord = ' ORDER BY dlr.dLogoutDateTime DESC';
        }
    }
    // Start Search Parameters
    $ssql = '';
    $iDriverId = $_REQUEST['iDriverId'] ?? '';
    $startDate = $_REQUEST['startDate'] ?? '';
    $endDate = $_REQUEST['endDate'] ?? '';
    $vEmail = $_REQUEST['vEmail'] ?? '';
    if ('' !== $startDate && '' !== $endDate) {
        $search_startDate = $startDate.' 00:00:00';
        $search_endDate = $endDate.' 23:59:00';
        $ssql .= " AND dlr.dLoginDateTime BETWEEN '".$search_startDate."' AND '".$search_endDate."'";
    }
    if ('' !== $iDriverId) {
        $ssql .= " AND rd.iDriverId = '".$iDriverId."'";
    }
    if ('' !== $vEmail) {
        $ssql .= " AND rd.vEmail = '".$vEmail."'";
    }
    // $sql_admin = "SELECT * from dlips WHERE 1=1 ".$ssql." ORDER BY iDriverLogId DESC";
    $sql = "SELECT rd.vName, rd.vLastName, rd.vEmail, dlr.dLoginDateTime, dlr.dLogoutDateTime
						FROM driver_log_report AS dlr
						LEFT JOIN register_driver AS rd ON rd.iDriverId = dlr.iDriverId where 1=1 AND rd.eStatus != 'Deleted' {$ssql} {$ord}";
    $db_dlip = $obj->MySQLSelect($sql);
    // echo "<pre>";print_r($db_dlip); exit;
    $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Name'."\t";
    $header .= 'Email'."\t";
    $header .= 'Log DateTime'."\t";
    $header .= 'Logout TimeDate'."\t";
    $header .= 'Total Hours Login'."\t";
    for ($j = 0; $j < count($db_dlip); ++$j) {
        $dstart = $db_dlip[$j]['dLoginDateTime'];
        if ('0000-00-00 00:00:00' === $db_dlip[$j]['dLogoutDateTime'] || '' === $db_dlip[$j]['dLogoutDateTime']) {
            $dLogoutDateTime = '--';
            $totalTimecount = '--';
        } else {
            $dLogoutDateTime = $db_dlip[$j]['dLogoutDateTime'];
            $totalhours = get_left_days_jobsave($dLogoutDateTime, $dstart);
            $totalTimecount = mediaTimeDeFormater($totalhours);
        }
        $data .= clearName($db_dlip[$j]['vName'].'  '.$db_dlip[$j]['vLastName'])."\t";
        $data .= clearEmail($db_dlip[$j]['vEmail'])."\t";
        $data .= DateTime($db_dlip[$j]['dLoginDateTime'])."\t";
        $data .= DateTime($db_dlip[$j]['dLogoutDateTime'])."\t";
        $data .= $totalTimecount."\n";
    }
    ob_clean();
    header('Content-type: application/octet-sdleam; charset=utf-8');
    header('Content-Disposition: attachment; filename= driver_log_report.xls');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "{$header}\n{$data}";

    exit;
}
if ('cancelled_trip' === $section) {
    $dlp_ssql = '';
    if (SITE_TYPE === 'Demo') {
        $dlp_ssql = " And dl.dLoginDateTime > '".WEEK_DATE."'";
    }
    // Start Sorting
    $sortby = $_REQUEST['sortby'] ?? 0;
    $order = $_REQUEST['order'] ?? '';
    $ord = ' ORDER BY t.iTripId DESC';
    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY t.tStartDate ASC';
        } else {
            $ord = ' ORDER BY t.tStartDate DESC';
        }
    }
    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY t.eCancelled ASC';
        } else {
            $ord = ' ORDER BY t.eCancelled DESC';
        }
    }
    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY d.vName ASC';
        } else {
            $ord = ' ORDER BY d.vName DESC';
        }
    }
    if (5 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY t.eType ASC';
        } else {
            $ord = ' ORDER BY t.eType DESC';
        }
    }
    // End Sorting
    // Start Search Parameters
    $ssql = '';
    $action = $_REQUEST['action'] ?? '';
    $iDriverId = $_REQUEST['iDriverId'] ?? '';
    $startDate = $_REQUEST['startDate'] ?? '';
    $serachTripNo = $_REQUEST['serachTripNo'] ?? '';
    $endDate = $_REQUEST['endDate'] ?? '';
    $vStatus = $_REQUEST['vStatus'] ?? '';
    $eType = $_REQUEST['eType'] ?? '';
    if ('search' === $action) {
        if ('' !== $startDate) {
            $ssql .= " AND Date(t.tTripRequestDate) >='".$startDate."'";
        }
        if ('' !== $endDate) {
            $ssql .= " AND Date(t.tTripRequestDate) <='".$endDate."'";
        }
        if ('' !== $iDriverId) {
            $ssql .= " AND t.iDriverId ='".$iDriverId."'";
        }
        if ('' !== $serachTripNo) {
            $ssql .= " AND t.vRideNo ='".$serachTripNo."'";
        }
        if ('' !== $eType) {
            $ssql .= " AND t.eType ='".$eType."'";
        }
    }
    $locations_where = '';
    if (count($userObj->locations) > 0) {
        $locations = implode(', ', $userObj->locations);
        $ssql .= " AND EXISTS(SELECT * FROM vehicle_type WHERE trips.iVehicleTypeId = vehicle_type.iVehicleTypeId AND vehicle_type.iLocationid IN(-1, {$locations}))";
    }
    $sql_admin = "SELECT t.tTripRequestDate,t.tStartDate ,t.tEndDate,t.eHailTrip,t.eCancelled,t.vCancelReason,t.vCancelComment,d.iDriverId, t.tSaddress,t.vRideNo,t.eType,t.eCancelledBy, t.tDaddress, t.fWalletDebit,t.eCarType,t.iTripId,t.iActive ,CONCAT(d.vName,' ',d.vLastName) AS dName FROM trips t LEFT JOIN register_driver d ON d.iDriverId = t.iDriverId
WHERE 1=1 And t.iActive='Canceled' {$ssql} {$trp_ssql} {$ord} ";
    $db_dlip = $obj->MySQLSelect($sql_admin);
    // echo "<pre>";print_r($db_dlip); exit;
    if ('UberX' !== $APP_TYPE && 'Delivery' !== $APP_TYPE) {
        $header .= $langage_lbl_admin['LBL_TRIP_TYPE_TXT_ADMIN']."\t";
    }
    $header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'].' Date'."\t";
    $header .= 'Cancel By'."\t";
    $header .= 'Cancel Reason'."\t";
    $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Name'."\t";
    $header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'].' No'."\t";
    $header .= 'Address'."\t";
    for ($j = 0; $j < count($db_dlip); ++$j) {
        $eType = $db_dlip[$j]['eType'];
        if ('Ride' === $eType) {
            $trip_type = 'Ride';
        } elseif ('UberX' === $eType) {
            $trip_type = 'Other Services';
        } elseif ('Deliver' === $eType) {
            $trip_type = 'Delivery';
        }
        $vCancelReason = $db_dlip[$j]['vCancelReason'];
        $trip_cancel = ('' !== $vCancelReason) ? $vCancelReason : '--';
        $eCancelled = $db_dlip[$j]['eCancelled'];
        // $CanceledBy = ($eCancelled == 'Yes' && $vCancelReason != '' ) ? $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'] : $langage_lbl_admin['LBL_RIDER'];
        $CanceledBy = $db_dlip[$j]['eCancelledBy']; // added by SP on 28-06-2019
        if ('UberX' !== $APP_TYPE && 'Delivery' !== $APP_TYPE) {
            if ('Yes' !== $db_dlip[$j]['eHailTrip']) {
                $data .= $trip_type."\t";
            } else {
                $data .= $trip_type.' ( Hail )'."\t";
            }
        }
        $data .= DateTime($db_dlip[$j]['tTripRequestDate'], 'no')."\t";
        $data .= $CanceledBy."\t";
        $data .= $trip_cancel."\t";
        $data .= clearName($db_dlip[$j]['dName'])."\t";
        $data .= $db_dlip[$j]['vRideNo']."\t";
        $str = '';
        if ('' !== $db_dlip[$j]['tDaddress']) {
            $str = ' -> '.$db_dlip[$j]['tDaddress'];
        }
        // $data .= $db_dlip[$j]['tSaddress'].$str;
        $string = $db_dlip[$j]['tSaddress'].$str;
        $data .= str_replace(["\n", "\r", "\r\n", "\n\r"], ' ', $string);
        $data .= "\n";
    }
    // echo "<pre>";print_r($data);exit;
    ob_clean();
    header('Content-type: application/octet-sdleam; charset=utf-8');
    header('Content-Disposition: attachment; filename=cancelled_trip.xls');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "{$header}\n{$data}";

    exit;
}
if ('ride_acceptance_report' === $section) {
    // Start Sorting
    $sortby = $_REQUEST['sortby'] ?? 0;
    $order = $_REQUEST['order'] ?? '';
    $ord = ' ORDER BY rs.iDriverRequestId DESC';
    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY rd.vName ASC';
        } else {
            $ord = ' ORDER BY rd.vName DESC';
        }
    }
    // End Sorting
    // Start Search Parameters
    $ssql = '';
    $action = $_REQUEST['action'] ?? '';
    $iDriverId = $_REQUEST['iDriverId'] ?? '';
    $startDate = $_REQUEST['startDate'] ?? '';
    $endDate = $_REQUEST['endDate'] ?? '';
    $date1 = $startDate.' '.'00:00:00';
    $date2 = $endDate.' '.'23:59:59';
    if ('' !== $startDate && '' !== $endDate) {
        $ssql .= " AND rs.tDate between '{$date1}' and '{$date2}'";
    }
    if ('' !== $iDriverId) {
        $ssql .= " AND rd.iDriverId = '".$iDriverId."'";
    }
    $chk_str_date = @date('Y-m-d H:i:s', strtotime('-'.$RIDER_REQUEST_ACCEPT_TIME.' second'));
    $sql_admin = "SELECT rd.iDriverId , rd.vLastName ,rd.vName ,
        COUNT(case when rs.eStatus = 'Accept' then 1 else NULL end) `Accept` ,
        COUNT(case when rs.eStatus != '' then 1 else NULL  end) `Total Request` ,
        COUNT(case when (rs.eStatus  = 'Decline' AND rs.eAcceptAttempted  = 'No') then 1 else NULL end) `Decline` ,
        COUNT(case when rs.eAcceptAttempted  = 'Yes' then 1 else NULL end) `Missed` ,
        COUNT(case when ((rs.eStatus  = 'Timeout' OR rs.eStatus  = 'Received') AND rs.eAcceptAttempted  = 'No' AND  rs.dAddedDate < '".$chk_str_date."')  then 1 else NULL end) `Timeout`,
        COUNT(case when ((rs.eStatus  = 'Timeout' OR rs.eStatus  = 'Received') AND rs.eAcceptAttempted  = 'No' AND rs.dAddedDate > '".$chk_str_date."' ) then 1 else NULL end) `inprocess`
        FROM driver_request rs left join register_driver rd on rd.iDriverId=rs.iDriverId
        WHERE 1=1 {$ssql} GROUP by rs.iDriverId {$ord} ";
    /*
      $sql_admin = "SELECT rd.iDriverId , rd.vLastName ,rd.vName ,
      COUNT(case when rs.eStatus = 'Accept' then 1 else NULL end) `Accept` ,
      COUNT(case when rs.eStatus != '' then 1 else NULL  end) `Total Request` ,
      COUNT(case when rs.eStatus  = 'Decline' then 1 else NULL end) `Decline` ,
      COUNT(case when rs.eStatus  = 'Timeout' then 1 else NULL end) `Timeout`
      FROM register_driver rd
      left join driver_request rs on rd.iDriverId=rs.iDriverId
      WHERE 1=1 $ssql GROUP by rs.iDriverId $ord "; */
    $db_dlip = $obj->MySQLSelect($sql_admin);
    // echo "<pre>";print_r($db_dlip); exit;
    $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Name'."\t";
    $header .= 'Total '.$langage_lbl_admin['LBL_TRIP_TXT_ADMIN'].' Requests'."\t";
    $header .= 'Requests Accepted'."\t";
    $header .= 'Requests Decline'."\t";
    $header .= 'Requests Timeout'."\t";
    $header .= 'Missed Attempts'."\t";
    $header .= 'In Process Request'."\t";
    $header .= 'Acceptance Percentage'."\t";
    $total_trip_req = '';
    $total_trip_acce_req = '';
    $total_trip_dec_req = '';
    for ($j = 0; $j < count($db_dlip); ++$j) {
        $sql_acp = "SELECT COUNT(case when t.eCancelled = 'Yes' then 1 else NULL end) `Cancel` , COUNT(case when t.eCancelled != '' then 1 else NULL  end) `Finish` FROM trips t  where t.iDriverId='".$db_dlip[$j]['iDriverId']."'";
        $db_acp = $obj->MySQLSelect($sql_acp);
        $Accept = $db_dlip[$j]['Accept'];
        $tAccept += $Accept;
        $Request = $db_dlip[$j]['Total Request'];
        $tRequest += $Request;
        $Decline = $db_dlip[$j]['Decline'];
        $tDecline += $Decline;
        $Timeout = $db_dlip[$j]['Timeout'];
        $tTimeout += $Timeout;
        $Cancel = $db_acp[0]['Cancel'];
        $tCancel += $Cancel;
        $missed = $db_dlip[$j]['Missed'];
        $tmissed += $missed;
        $inprocess = $db_dlip[$j]['inprocess'];
        $tinprocess += $inprocess;
        $Finish = $db_acp[0]['Finish'];
        $tFinish += $Finish;
        $aceptance_percentage = (100 * $Accept) / $Request;
        $data .= clearName($db_dlip[$j]['vName'].' '.$db_dlip[$j]['vLastName'])."\t";
        $data .= $Request."\t";
        $data .= $Accept."\t";
        $data .= $Decline."\t";
        $data .= $Timeout."\t";
        $data .= $missed."\t";
        $data .= $inprocess."\t";
        $data .= round($aceptance_percentage, 2).' %'."\n";
    }
    ob_clean();
    header('Content-type: application/octet-sdleam; charset=utf-8');
    header('Content-Disposition: attachment; filename=ride_acceptance_report.xls');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "{$header}\n{$data}";

    exit;
}
if ('driver_trip_detail' === $section) {
    // Start Sorting
    $sortby = $_REQUEST['sortby'] ?? 0;
    $order = $_REQUEST['order'] ?? '';
    $ord = ' ORDER BY t.tStartdate DESC';
    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY t.tStartDate ASC';
        } else {
            $ord = ' ORDER BY t.tStartDate DESC';
        }
    }
    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY d.vName ASC';
        } else {
            $ord = ' ORDER BY d.vName DESC';
        }
    }
    // End Sorting
    $cmp_ssql = '';
    if (SITE_TYPE === 'Demo') {
        $cmp_ssql = " And t.tStartDate > '".WEEK_DATE."'";
    }
    // Start Search Parameters
    $ssql = '';
    $action = $_REQUEST['action'] ?? '';
    $iDriverId = $_REQUEST['iDriverId'] ?? '';
    $startDate = $_REQUEST['startDate'] ?? '';
    $serachTripNo = $_REQUEST['serachTripNo'] ?? '';
    $endDate = $_REQUEST['endDate'] ?? '';
    $date1 = $startDate.' '.'00:00:00';
    $date2 = $endDate.' '.'23:59:59';
    if ('' !== $startDate) {
        $ssql .= " AND Date(t.tStartDate) >='".$startDate."'";
    }
    if ('' !== $endDate) {
        $ssql .= " AND Date(t.tStartDate) <='".$endDate."'";
    }
    if ('' !== $iDriverId) {
        $ssql .= " AND d.iDriverId = '".$iDriverId."'";
    }
    if ('' !== $serachTripNo) {
        $ssql .= " AND t.vRideNo ='".$serachTripNo."'";
    }
    $locations_where = '';
    if (count($userObj->locations) > 0) {
        $locations = implode(', ', $userObj->locations);
        $ssql .= " AND vt.iLocationid IN(-1, {$locations})";
    }
    $sql_admin = "SELECT u.vName, u.vLastName, d.vAvgRating,t.fGDtime,t.tStartdate,t.tEndDate, t.tTripRequestDate, t.iFare, d.iDriverId, t.tSaddress,t.vRideNo, t.tDaddress, d.vName AS name,c.vName AS comp,c.vCompany, d.vLastName AS lname,t.eCarType,t.iTripId,vt.vVehicleType,t.iActive FROM register_driver d RIGHT JOIN trips t ON d.iDriverId = t.iDriverId LEFT JOIN vehicle_type vt ON vt.iVehicleTypeId = t.iVehicleTypeId LEFT JOIN  register_user u ON t.iUserId = u.iUserId JOIN company c ON c.iCompanyId=d.iCompanyId
			     WHERE 1=1 AND t.iActive = 'Finished' AND t.eCancelled='No' {$ssql} {$cmp_ssql} {$ord} ";
    $db_dlip = $obj->MySQLSelect($sql_admin);
    // echo "<pre>";print_r($db_dlip); exit;
    $header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'].'  No'."\t";
    $header .= 'Address'."\t";
    $header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'].'  Date'."\t";
    $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']."\t";
    $header .= 'Estimated Time'."\t";
    $header .= 'Actual Time'."\t";
    $header .= 'Variance'."\t";
    for ($j = 0; $j < count($db_dlip); ++$j) {
        $data .= $db_dlip[$j]['vRideNo']."\t";
        $data .= $db_dlip[$j]['tSaddress'].' -> '.$db_dlip[$j]['tDaddress']."\t";
        $data .= DateTime($db_dlip[$j]['tStartdate'])."\t";
        $data .= clearName($db_dlip[$j]['name'].' '.$db_dlip[$j]['lname'])."\t";
        $ans = set_hour_min($db_dlip[$j]['fGDtime']);
        if (0 !== $ans['hour']) {
            $ans1 = $ans['hour'].' Hours '.$ans['minute'].' Minutes';
        } else {
            $ans1 = '';
            if (0 !== $ans['minute']) {
                $ans1 .= $ans['minute'].' Minutes ';
            }
            $ans1 .= $ans['second'].' Seconds';
        }
        $data .= $ans1."\t";
        $a = strtotime($db_dlip[$j]['tStartdate']);
        $b = strtotime($db_dlip[$j]['tEndDate']);
        $diff_time = ($b - $a);
        // $diff_time=$diff_time*1000;
        $ans_diff = set_hour_min($diff_time);
        // print_r($ans);exit;
        if (0 !== $ans_diff['hour']) {
            $ans_diff12 = $ans_diff['hour'].' Hours '.$ans_diff['minute'].' Minutes';
        } else {
            $ans_diff12 = '';
            if (0 !== $ans_diff['minute']) {
                $ans_diff12 .= $ans_diff['minute'].' Minutes ';
            }
            $ans_diff12 .= $ans_diff['second'].' Seconds';
        }
        $data .= $ans_diff12."\t";
        $ori_time = $db_dlip[$j]['fGDtime'];
        $tak_time = $diff_time;
        $ori_diff = $ori_time - $tak_time;
        echo $ans_ori = set_hour_min(abs($ori_diff));
        if (0 !== $ans_ori['hour']) {
            $ans2 .= $ans_ori['hour'].' Hours '.$ans_ori['minute'].' Minutes';
            if ($ori_diff < 0) {
                $ans2 .= ' Late';
            } else {
                $ans2 .= ' Early';
            }
        } else {
            $ans2 = '';
            if (0 !== $ans_ori['minute']) {
                $ans2 .= $ans_ori['minute'].' Minutes ';
            }
            $ans2 .= $ans_ori['second'].' Seconds';
            if ($ori_diff < 0) {
                $ans2 .= ' Late';
            } else {
                $ans2 .= ' Early';
            }
        }
        $data .= $ans2."\n";
    }
    ob_clean();
    header('Content-type: application/octet-sdleam; charset=utf-8');
    header('Content-Disposition: attachment; filename=driver_trip_detail.xls');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "{$header}\n{$data}";

    exit;
}
if ('wallet_report' === $section) {
    $action = ($_REQUEST['action'] ?? '');
    $ssql = '';
    if ('' !== $action) {
        $startDate = $_REQUEST['startDate'];
        $endDate = $_REQUEST['endDate'];
        $eUserType = $_REQUEST['eUserType'];
        $eFor = $_REQUEST['searchBalanceType'];
        $Payment_type = $_REQUEST['searchPaymentType'];
        if ('Driver' === $eUserType) {
            $iDriverId = $_REQUEST['iDriverId'];
            $iUserId = '';
            $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($iDriverId, $eUserType);
        }
        if ('Rider' === $eUserType) {
            $iUserId = $_REQUEST['iUserId'];
            $iDriverId = '';
            $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($iUserId, $eUserType);
        }
        if ('' !== $iDriverId) {
            $ssql .= " AND iUserId = '".$iDriverId."'";
        }
        if ('' !== $iUserId) {
            $ssql .= " AND iUserId = '".$iUserId."'";
        }
        if ('' !== $startDate) {
            $ssql .= " AND Date(dDate) >='".$startDate."'";
        }
        if ('' !== $endDate) {
            $ssql .= " AND Date(dDate) <='".$endDate."'";
        }
        if ($eUserType) {
            $ssql .= " AND eUserType = '".$eUserType."'";
        }
        if ('' !== $eFor) {
            $ssql .= " AND eFor = '".$eFor."'";
        }
        if ('' !== $Payment_type) {
            $ssql .= " AND eType = '".$Payment_type."'";
        }
    }
    $sortby = $_REQUEST['sortby'] ?? 0;
    $order = $_REQUEST['order'] ?? '';
    // $ord = ' ORDER BY iUserWalletId DESC';
    $ord = ' ORDER BY dDate ASC';
    $sql_admin = "SELECT * From user_wallet where 1=1 {$ssql} {$ord} ";
    $db_dlip = $obj->MySQLSelect($sql_admin);
    $header .= 'Transaction Date'."\t";
    $header .= 'Description'."\t";
    $header .= 'Transaction ID'."\t";
    $header .= $langage_lbl_admin['LBL_TRIP_NO_ADMIN']."\t";
    $header .= 'Amount'."\t";
    $header .= 'Purpose'."\t";
    $header .= 'Balance Type'."\t";
    $header .= 'Balance'."\t";
    for ($j = 0; $j < count($db_dlip); ++$j) {
        if ('Credit' === $db_dlip[$j]['eType']) {
            $db_dlip[$j]['currentbal'] = $prevbalance + $db_dlip[$j]['iBalance'];
        } else {
            $db_dlip[$j]['currentbal'] = $prevbalance - $db_dlip[$j]['iBalance'];
        }
        $prevbalance = $db_dlip[$j]['currentbal'];
        if ($db_dlip[$j]['iTripId'] > 0) {
            $sql_query = 'SELECT * FROM `trips` WHERE iTripId ='.$db_dlip[$j]['iTripId'];
            $db_result_trips = $obj->MySQLSelect($sql_query);
            $ride_number = $db_result_trips[0]['vRideNo'];
        } else {
            $ride_number = '--';
        }
        $data .= DateTime($db_dlip[$j]['dDate'])."\t";
        $pat = '/\#([^\"]*?)\#/';
        preg_match($pat, $db_dlip[$j]['tDescription'], $tDescription_value);
        $tDescription_translate = $langage_lbl_admin[$tDescription_value[1]];
        $row_tDescription = str_replace($tDescription_value[0], $tDescription_translate, $db_dlip[$j]['tDescription']);
        if ('Transfer' === $db_dlip[$j]['eFor']) {
            if (preg_match($pat, $row_tDescription, $tDescription_value_new)) {
                $tDescription_translate_second = $langage_lbl_admin[$tDescription_value_new[1]];
                $row_tDescription1 = str_replace($tDescription_value_new[0], $tDescription_translate_second, $row_tDescription);
            } else {
                $row_tDescription1 = $row_tDescription;
            }
            if (preg_match($pat, $row_tDescription1, $tDescription_value_other)) {
                $tDescription_translate_last = $langage_lbl_admin[$tDescription_value_other[1]];
                $row_tDescriptionNew = str_replace($tDescription_value_other[0], $tDescription_translate_last, $row_tDescription1);
            } else {
                $row_tDescriptionNew = $row_tDescription1;
            }
        }
        if ('Transfer' === $db_dlip[$j]['eFor']) {
            $data .= $row_tDescriptionNew."\t";
        } else {
            $data .= $row_tDescription."\t";
        }
        $data .= $db_dlip[$j]['tPaymentUserID']."\t";
        $data .= $ride_number."\t";
        // $data .= formateNumAsPerCurrency($db_dlip[$j]['iBalance'],'') . "\t";
        $data .= formateNumAsPerCurrency($db_dlip[$j]['iBalance'], '')."\t";
        $data .= $db_dlip[$j]['eFor']."\t";
        $data .= $db_dlip[$j]['eType']."\t";
        // $data .= formateNumAsPerCurrency($db_dlip[$j]['currentbal'],'') . "\n";
        $data .= formateNumAsPerCurrency($db_dlip[$j]['currentbal'], '')."\n";
    }
    // added by SP on 28-06-2019
    $data .= "\n\t\t\t\t\t\tTotal Balance:\t";
    // $data .= formateNumAsPerCurrency($user_available_balance,'')."\n";
    $data .= formateNumAsPerCurrency($user_available_balance, '')."\n";
    ob_clean();
    header('Content-type: application/octet-sdleam; charset=utf-8');
    header('Content-Disposition: attachment; filename=wallet_report.xls');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "{$header}\n{$data}";

    exit;
}
if ('cab_booking' === $section) {
    $action = ($_REQUEST['action'] ?? '');
    $sortby = $_REQUEST['sortby'] ?? 0;
    $order = $_REQUEST['order'] ?? '';
    $option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : '';
    $keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : '';
    $eType = $_REQUEST['eType'] ?? '';
    $eStatus = $_REQUEST['eStatus'] ?? '';
    $ord = ' ORDER BY cb.iCabBookingId DESC';
    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY ru.vName ASC';
        } else {
            $ord = ' ORDER BY ru.vName DESC';
        }
    }
    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY cb.dBooking_date ASC';
        } else {
            $ord = ' ORDER BY cb.dBooking_date DESC';
        }
    }
    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY cb.vSourceAddresss ASC';
        } else {
            $ord = ' ORDER BY cb.vSourceAddresss DESC';
        }
    }
    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY cb.tDestAddress ASC';
        } else {
            $ord = ' ORDER BY cb.tDestAddress DESC';
        }
    }
    if (5 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY cb.eStatus ASC';
        } else {
            $ord = ' ORDER BY cb.eStatus DESC';
        }
    }
    if (6 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY cb.vBookingNo ASC';
        } else {
            $ord = ' ORDER BY cb.vBookingNo DESC';
        }
    }
    if (7 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY cb.eType ASC';
        } else {
            $ord = ' ORDER BY cb.eType DESC';
        }
    }
    $adm_ssql = '';
    if (SITE_TYPE === 'Demo') {
        $adm_ssql = " And cb.dAddredDate > '".WEEK_DATE."'";
    }
    if ('RentalRide' === $eType) {
        $eType_new = 'Ride';
        $sql11 = ' AND cb.iRentalPackageId > 0';
    } else {
        $eType_new = $eType;
        $sql11 = 'AND cb.iRentalPackageId = 0';
    }
    $ssql = '';
    if ('' !== $keyword) {
        if ('' !== $option) {
            if ('' !== $eType_new) {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".clean($keyword)."%' AND cb.eType = '".clean($eType_new)."' {$sql11}";
            } else {
                $ssql .= ' AND '.stripslashes($option)." LIKE '%".clean($keyword)."%' {$sql11}";
            }
        } else {
            if ('' !== $eType_new) {
                $ssql .= " AND (CONCAT(ru.vName,' ',ru.vLastName) LIKE '%".clean($keyword)."%' OR cb.tDestAddress LIKE '%".clean($keyword)."%' OR cb.vSourceAddresss  LIKE '%".clean($keyword)."%' OR cb.vBookingNo LIKE '".clean($keyword)."' OR cb.eStatus LIKE '%".clean($keyword)."%') AND cb.eType = '".clean($eType_new)."' {$sql11}";
            } else {
                $ssql .= " AND (CONCAT(ru.vName,' ',ru.vLastName) LIKE '%".clean($keyword)."%' OR cb.tDestAddress LIKE '%".clean($keyword)."%' OR cb.vSourceAddresss  LIKE '%".clean($keyword)."%' OR cb.vBookingNo LIKE '".clean($keyword)."' OR cb.eStatus LIKE '%".clean($keyword)."%') {$sql11}";
            }
        }
    } elseif ('' !== $eType_new && '' === $keyword) {
        $ssql .= " AND cb.eType = '".clean($eType_new)."' {$sql11}";
    } elseif ('cb.eStatus' === $option && !empty($eStatus)) {
        if ('Expired' === $eStatus) { // changed by me
            $ssql .= " AND ((cb.eStatus LIKE '%Pending%' or cb.eStatus LIKE '%Accepted%') AND DATE( NOW( ) ) >= DATE_ADD( DATE( cb.dBooking_date ) , INTERVAL 10 MINUTE )) ".$sql11;
        } elseif ('Completed' === $eStatus) {
            $ssql .= " AND ((cb.eStatus LIKE '%Completed%') AND DATE( NOW( ) ) >= DATE_ADD( DATE( cb.dBooking_date ) , INTERVAL 10 MINUTE )) ".$sql11;
        } else {
            $ssql .= " AND cb.eStatus LIKE '%".clean($eStatus)."%' ".$sql11;
        }
    }
    $hotelQuery = '';
    if ('hotel' === $_SESSION['SessionUserType']) {
        $iHotelBookingId = $_SESSION['sess_iAdminUserId'];
        $hotelQuery = " And cb.eBookingFrom = 'Hotel' AND cb.iHotelBookingId = '".$iHotelBookingId."'";
    }
    $locations_where = '';
    if (count($userObj->locations) > 0) {
        $locations = implode(', ', $userObj->locations);
        $ssql .= " AND vt.iLocationid IN(-1, {$locations})";
    }
    $sql = "SELECT cb.*,CONCAT(ru.vName,' ',ru.vLastName) as rider,CONCAT(rd.vName,' ',rd.vLastName) as driver,vt.vVehicleType,vt.vRentalAlias_".$default_lang." as vRentalVehicleTypeName FROM cab_booking as cb LEFT JOIN register_user as ru on ru.iUserId=cb.iUserId LEFT JOIN register_driver as rd on rd.iDriverId=cb.iDriverId LEFT JOIN vehicle_type as vt on vt.iVehicleTypeId=cb.iVehicleTypeId WHERE 1=1 {$ssql} {$adm_ssql} {$hotelQuery} {$ord}";
    $data_drv = $obj->MySQLSelect($sql);
    // /changed by me start
    if ('Completed' === $eStatus) {
        foreach ($data_drv as $key_com => $val_com) {
            $sql_trip = 'select iActive, eCancelledBy from trips where iTripId='.$data_drv[$key_com]['iTripId'];
            $data_trip = $obj->MySQLSelect($sql_trip);
            if (!empty($data_trip)) {
                if ('Canceled' === $data_trip[0]['iActive'] && 'Driver' === $data_trip[0]['eCancelledBy']) {
                } else {
                    $cabbookingid[] = $val_com['iCabBookingId'];
                }
            }
        }
        $cabbookingid_implode = implode(',', $cabbookingid);
        $ssql .= " AND cb.iCabBookingId IN({$cabbookingid_implode})";
        $sql = "SELECT cb.*,CONCAT(ru.vName,' ',ru.vLastName) as rider,CONCAT(rd.vName,' ',rd.vLastName) as driver,vt.vVehicleType,vt.vRentalAlias_".$default_lang." as vRentalVehicleTypeName FROM cab_booking as cb LEFT JOIN register_user as ru on ru.iUserId=cb.iUserId LEFT JOIN register_driver as rd on rd.iDriverId=cb.iDriverId LEFT JOIN vehicle_type as vt on vt.iVehicleTypeId=cb.iVehicleTypeId WHERE 1=1 {$ssql} {$adm_ssql} {$hotelQuery} {$ord}";
        $data_drv = $obj->MySQLSelect($sql);
    }
    // /changed by me end
    if ('Ride-Delivery' === $APP_TYPE || 'Ride-Delivery-UberX' === $APP_TYPE) {
        $header .= $langage_lbl_admin['LBL_TRIP_TYPE_TXT_ADMIN']."\t";
    }
    if ($hotelPanel > 0 || $kioskPanel > 0) {
        $header .= "Booked By\t";
    }
    $header .= $langage_lbl_admin['LBL_MYTRIP_RIDE_NO']."\t";
    $header .= $langage_lbl_admin['LBL_RIDERS_ADMIN']."\t";
    $header .= 'Date'."\t";
    $header .= 'Expected Source Location'."\t";
    if ('UberX' !== $APP_TYPE) {
        $header .= 'Expected Destination Location'."\t";
    }
    $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']."\t";
    $header .= 'Status'."\t";
    for ($j = 0; $j < count($data_drv); ++$j) {
        $eType = $data_drv[$j]['eType'];
        if ('Ride' === $eType_new && $data_drv[$j]['iRentalPackageId'] > 0) {
            $trip_type = 'Rental Ride';
        } elseif ('Ride' === $eType) {
            $trip_type = 'Ride';
        } elseif ('UberX' === $eType) {
            $trip_type = 'Other Services';
        } elseif ('Deliver' === $eType) {
            $trip_type = 'Delivery';
        }
        if ('' !== $data_drv[$j]['eBookingFrom']) {
            $eBookingFrom = $data_drv[$j]['eBookingFrom'];
        } else {
            $eBookingFrom = $langage_lbl_admin['LBL_RIDER'];
        }
        $systemTimeZone = date_default_timezone_get();
        if ('' !== $data_drv[$j]['dBooking_date'] && '' !== $data_drv[$j]['vTimeZone']) {
            $dBookingDate = converToTz($data_drv[$j]['dBooking_date'], $data_drv[$j]['vTimeZone'], $systemTimeZone);
        } else {
            $dBookingDate = $data_drv[$j]['dBooking_date'];
        }
        if ('Ride-Delivery' === $APP_TYPE || 'Ride-Delivery-UberX' === $APP_TYPE) {
            $data .= $trip_type."\t";
        }
        if ($hotelPanel > 0 || $kioskPanel > 0) {
            $data .= $eBookingFrom."\t";
        }
        $data .= clearName($data_drv[$j]['vBookingNo'])."\t";
        $data .= clearName($data_drv[$j]['rider'])."\t";
        $data .= DateTime($dBookingDate)."\t";
        $string = $data_drv[$j]['vSourceAddresss'];
        $data .= str_replace(["\n", "\r", "\r\n", "\n\r"], ' ', $string)."\t";
        if ('UberX' !== $APP_TYPE) {
            $string1 = $data_drv[$j]['tDestAddress'];
            $data .= str_replace(["\n", "\r", "\r\n", "\n\r"], ' ', $string1)."\t";
        }
        // Driver Details
        if ('Yes' === $data_drv[$j]['eAutoAssign'] && $data_drv[$j]['iRentalPackageId'] > 0) {
            $data .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].': Auto Assign ( Vehicle Type : '.$data_drv[$j]['vRentalVehicleTypeName'].' )'."\t";
        } elseif ('Yes' === $data_drv[$j]['eAutoAssign'] && 'Deliver' === $data_drv[$j]['eType'] && 0 === $data_drv[$j]['iDriverId'] && 'Cancel' !== $data_drv[$j]['eStatus'] && 'Multi' === $APP_DELIVERY_MODE) {
            $data .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].': Auto Assign ( Vehicle Type : '.$data_drv[$j]['vVehicleType'].' )'."\t";
        } elseif ('Yes' === $data_drv[$j]['eAutoAssign'] && 0 === $data_drv[$j]['iDriverId']) {
            $data .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' : Auto Assign ( Car Type : '.$data_drv[$j]['vVehicleType'].' )'."\t";
        } elseif ('Pending' === $data_drv[$j]['eStatus'] && (strtotime($data_drv[$j]['dBooking_date']) > strtotime(date('Y-m-d'))) && 0 === $data_drv[$j]['iDriverId']) {
            $data .= '( '.$langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'].' : '.$data_drv[$j]['vVehicleType'].' )'."\t";
        } elseif ('Driver' === $data_drv[$j]['eCancelBy'] && 'Cancel' === $data_drv[$j]['eStatus'] && 0 === $data_drv[$j]['iDriverId']) {
            $data .= '( '.$langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'].' : '.$data_drv[$j]['vVehicleType'].')'."\t";
        } elseif ('' !== $data_drv[$j]['driver'] && '0' !== $data_drv[$j]['driver']) {
            $data .= clearName($data_drv[$j]['driver']).'( '.$langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'].' :'.$data_drv[$j]['vVehicleType'].')'."\t";
        } else {
            $data .= '( '.$langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'].' : '.$data_drv[$j]['vVehicleType'].')'."\t";
        }
        // Status
        $setcurrentTime = strtotime(date('Y-m-d H:i:s'));
        $bookingdate = date('Y-m-d H:i', strtotime('+30 minutes', strtotime($data_drv[$j]['dBooking_date'])));
        $bookingdatecmp = strtotime($bookingdate);
        if ('Assign' === $data_drv[$j]['eStatus'] && $bookingdatecmp > $setcurrentTime) {
            $data .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Assigned'."\n";
        } elseif ('Accepted' === $data_drv[$j]['eStatus']) {
            $data .= $data_drv[$j]['eStatus']."\n";
        } elseif ('Declined' === $data_drv[$j]['eStatus']) {
            $data .= $data_drv[$j]['eStatus']."\n";
        } else {
            $sql = 'select iActive, eCancelledBy from trips where iTripId='.$data_drv[$j]['iTripId'];
            $data_stat = $obj->MySQLSelect($sql);
            if ($data_stat) {
                for ($d = 0; $d < count($data_stat); ++$d) {
                    if ('Canceled' === $data_stat[$d]['iActive']) {
                        $eCancelledBy = ('Passenger' === $data_stat[$d]['eCancelledBy']) ? $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'] : $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];
                        $data .= 'Canceled By '.$eCancelledBy."\n";
                    } elseif ('Finished' === $data_stat[$d]['iActive'] && 'Driver' === $data_stat[$d]['eCancelledBy']) {
                        $data .= 'Canceled By '.$eCancelledBy."\n";
                    } else {
                        $data .= $data_stat[$d]['iActive']."\n";
                    }
                }
            } else {
                if ('Cancel' === $data_drv[$j]['eStatus']) {
                    if ('Driver' === $data_drv[$j]['eCancelBy']) {
                        $data .= 'Canceled By '.$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']."\n";
                    } elseif ('Rider' === $data_drv[$j]['eCancelBy']) {
                        $data .= 'Canceled By '.$langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']."\n";
                    } else {
                        $data .= 'Canceled By Admin'."\n";
                    }
                } else {
                    if ('Pending' === $data_drv[$j]['eStatus'] && $bookingdatecmp > $setcurrentTime) {
                        $data .= $data_drv[$j]['eStatus']."\n";
                    } else {
                        $data .= 'Expired'."\n";
                    }
                }
            }
        }
    }
    ob_clean();
    header('Content-type: application/octet-sdleam; charset=utf-8');
    header('Content-Disposition: attachment; filename=ScheduledBookings.xls');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "{$header}\n{$data}";

    exit;
}
if ('triplist' === $section) {
    $action = ($_REQUEST['action'] ?? '');
    $sortby = $_REQUEST['sortby'] ?? 0;
    $order = $_REQUEST['order'] ?? '';
    $option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : '';
    $keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : '';
    $eType = $_REQUEST['eType'] ?? '';
    $searchCompany = $_REQUEST['searchCompany'] ?? '';
    $searchDriver = $_REQUEST['searchDriver'] ?? '';
    $searchRider = $_REQUEST['searchRider'] ?? '';
    $serachTripNo = $_REQUEST['serachTripNo'] ?? '';
    $startDate = $_REQUEST['startDate'] ?? '';
    $endDate = $_REQUEST['endDate'] ?? '';
    $vStatus = $_REQUEST['vStatus'] ?? '';
    $method = $_REQUEST['method'] ?? '';
    $iTripId = $_REQUEST['iTripId'] ?? '';
    $vehilceTypeArr = [];
    $getVehicleTypes = $obj->MySQLSelect('SELECT iVehicleTypeId,vVehicleType_'.$default_lang.' AS vehicleType FROM vehicle_type WHERE 1=1');
    for ($r = 0; $r < count($getVehicleTypes); ++$r) {
        $vehilceTypeArr[$getVehicleTypes[$r]['iVehicleTypeId']] = $getVehicleTypes[$r]['vehicleType'];
    }
    $ssql = '';
    $ord = ' ORDER BY t.iTripId DESC';
    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY t.eType ASC';
        } else {
            $ord = ' ORDER BY t.eType DESC';
        }
    }
    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY t.tTripRequestDate ASC';
        } else {
            $ord = ' ORDER BY t.tTripRequestDate DESC';
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
            $ord = ' ORDER BY d.vName ASC';
        } else {
            $ord = ' ORDER BY d.vName DESC';
        }
    }
    if (5 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY u.vName ASC';
        } else {
            $ord = ' ORDER BY u.vName DESC';
        }
    }
    // End Sorting
    // Start Search Parameters
    if ('' !== $startDate) {
        $ssql .= " AND Date(t.tTripRequestDate) >='".$startDate."'";
    }
    if ('' !== $endDate) {
        $ssql .= " AND Date(t.tTripRequestDate) <='".$endDate."'";
    }
    if ('' !== $serachTripNo) {
        $ssql .= " AND t.vRideNo ='".$serachTripNo."'";
    }
    if ('' !== $searchCompany) {
        $ssql .= " AND d.iCompanyId ='".$searchCompany."'";
    }
    if ('' !== $searchDriver) {
        $ssql .= " AND t.iDriverId ='".$searchDriver."'";
    }
    if ('' !== $searchRider) {
        $ssql .= " AND t.iUserId ='".$searchRider."'";
    }
    if ('onRide' === $vStatus) {
        $ssql .= " AND (t.iActive = 'On Going Trip' OR t.iActive = 'Active') AND t.eCancelled='No'";
    } elseif ('cancel' === $vStatus) {
        $ssql .= " AND (t.iActive = 'Canceled' OR t.eCancelled='yes')";
    } elseif ('complete' === $vStatus) {
        $ssql .= " AND t.iActive = 'Finished' AND t.eCancelled='No'";
    }
    if ('' !== trim($promocode)) {
        $ssql .= " AND t.vCouponCode LIKE '".$promocode."' AND t.iActive !='Canceled'";
    }
    if (count($userObj->locations) > 0) {
        $locations = implode(', ', $userObj->locations);
        $ssql .= " AND vt.iLocationid IN(-1, {$locations}) ";
    }
    if ('' !== $eType) {
        if ('Ride' === $eType) {
            $ssql .= " AND t.eType ='".$eType."' AND t.iRentalPackageId = 0 AND t.eHailTrip = 'No' ";
            $ssql .= ' AND  t.iFromStationId = 0 AND t.iToStationId = 0 ';
        } elseif ('RentalRide' === $eType) {
            $ssql .= " AND t.eType ='Ride' AND t.iRentalPackageId > 0";
        } elseif ('HailRide' === $eType) {
            $ssql .= " AND t.eType ='Ride' AND t.eHailTrip = 'Yes'";
        } elseif ('Pool' === $eType) {
            $ssql .= " AND t.eType ='Ride' AND t.ePoolRide = 'Yes'";
        } elseif ('Fly' === $eType) {
            $ssql .= " AND t.eType ='Ride' AND t.iFromStationId != 0 AND t.iToStationId != 0 ";
        } elseif ('Deliver' === $eType) {
            $ssql .= " AND t.eType ='Multi-Delivery' HAVING totalDeliveryTrips = 1";
        } elseif ('Multi-Delivery' === $eType) {
            $ssql .= " AND t.eType ='".$eType."' HAVING totalDeliveryTrips > 1";
        } else {
            $ssql .= " AND t.eType ='".$eType."' ";
        }
    }

    $trp_ssql = '';
    if (SITE_TYPE === 'Demo') {
        $trp_ssql = " And t.tTripRequestDate > '".WEEK_DATE."'";
    }
    $hotelQuery = '';
    if ('hotel' === $_SESSION['SessionUserType']) {
        /* $sql1 = "SELECT * FROM hotel where iAdminId = '".$_SESSION['sess_iAdminUserId']."'";

          $hoteldata = $obj->MySQLSelect($sql1); */
        $iHotelBookingId = $_SESSION['sess_iAdminUserId'];
        $hotelQuery = " AND (t.eBookingFrom = 'Hotel' || t.eBookingFrom = 'Kiosk') AND t.iHotelBookingId = '".$iHotelBookingId."'";
    }
    $sql = "SELECT t.iFromStationId, t.iToStationId,t.ePoolRide,t.tStartDate,t.tEndDate, t.tTripRequestDate,t.eBookingFrom,t.vCancelReason,t.vCancelComment,t.iCancelReasonId, t.eHailTrip, t.iUserId, t.iFare, t.eType, d.iDriverId, t.tSaddress, t.vRideNo, t.tDaddress,  t.fWalletDebit, t.eCarType, t.iTripId, t.iActive, t.fCancellationFare, t.eCancelledBy, t.eCancelled, t.iRentalPackageId , CONCAT(u.vName,' ',u.vLastName) AS riderName, CONCAT(d.vName,' ',d.vLastName) AS driverName, d.vAvgRating,t.vDeliveryConfirmCode, c.vCompany, vt.vVehicleType_{$default_lang} as vVehicleType, vt.vRentalAlias_{$default_lang} as vRentalVehicleTypeName,t.tVehicleTypeData, t.fTax1, t.fTax2, (SELECT COUNT(tl.iTripDeliveryLocationId) AS Total FROM trips_delivery_locations as tl WHERE 1=1 AND tl.iActive='Finished' AND t.iTripId=tl.iTripId) as totalDeliveryTrips FROM trips t LEFT JOIN register_driver d ON d.iDriverId = t.iDriverId LEFT JOIN vehicle_type vt ON vt.iVehicleTypeId = t.iVehicleTypeId LEFT JOIN  register_user u ON t.iUserId = u.iUserId LEFT JOIN company c ON c.iCompanyId=d.iCompanyId WHERE 1=1 AND t.eSystem = 'General' {$ssql} {$trp_ssql} {$hotelQuery} {$ord} ";
    $db_trip = $obj->MySQLSelect($sql);
    if ('UberX' !== $APP_TYPE && 'Delivery' !== $APP_TYPE) {
        if ('hotel' !== $_SESSION['SessionUserType']) {
            $header .= $langage_lbl_admin['LBL_TRIP_TYPE_TXT_ADMIN']."\t";
        }
    }
    if ($hotelPanel > 0 || $kioskPanel > 0) {
        $header .= "Booked By\t";
    }
    $header .= $langage_lbl_admin['LBL_TRIP_NO_ADMIN']."\t";
    $header .= 'Address'."\t";
    $header .= $langage_lbl_admin['LBL_TRIP_DATE_ADMIN']."\t";
    if (isset($_SESSION['SessionUserType']) && 'hotel' !== $_SESSION['SessionUserType']) {
        $header .= 'Company'."\t";
    }
    $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']."\t";
    $header .= $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']."\t";
    $header .= $langage_lbl_admin['LBL_DRIVER_TRIP_FARE_TXT']."\t";
    $header .= 'Type'."\t";
    $header .= 'Status'."\t";
    for ($i = 0; $i < count($db_trip); ++$i) {
        $poolTxt = '';
        if ('Yes' === $db_trip[$i]['ePoolRide']) {
            $poolTxt = ' (Pool)';
        }
        $eTypenew = $db_trip[$i]['eType'];
        $link_page = 'invoice.php';
        if ('Ride' === $eTypenew) {
            $trip_type = 'Ride';
        } elseif ('UberX' === $eTypenew) {
            $trip_type = 'Other Services';
        } elseif ('Multi-Delivery' === $eTypenew && $db_trip[$i]['totalDeliveryTrips'] > 1) {
            $trip_type = 'Multi-Delivery';
            $link_page = 'invoice_multi_delivery.php';
        } else {
            $trip_type = 'Delivery';
        }
        $trip_type .= $poolTxt;
        if ('' !== $db_trip[$i]['eBookingFrom']) {
            $eBookingFrom = $db_trip[$i]['eBookingFrom'];
        } else {
            $eBookingFrom = $langage_lbl_admin['LBL_RIDER'];
        }
        if ('UberX' !== $APP_TYPE && 'Delivery' !== $APP_TYPE) {
            if ('hotel' !== $_SESSION['SessionUserType']) {
                if ('Yes' === $db_trip[$i]['eHailTrip'] && $db_trip[$i]['iRentalPackageId'] > 0) {
                    $data .= 'Rental '.$trip_type.' ( Hail )'."\t";
                } elseif ($db_trip[$i]['iRentalPackageId'] > 0) {
                    $data .= 'Rental '.$trip_type."\t";
                } elseif ('Yes' === $db_trip[$i]['eHailTrip']) {
                    $data .= 'Hail '.$trip_type."\t";
                } else {
                    if (!empty($db_trip[$i]['iFromStationId']) && !empty($db_trip[$i]['iToStationId'])) {
                        $trip_type = 'Fly';
                    }
                    $data .= $trip_type."\t";
                }
            }
        }
        if ($hotelPanel > 0 || $kioskPanel > 0) {
            $data .= $eBookingFrom."\t";
        }
        $data .= $db_trip[$i]['vRideNo']."\t";
        $string = $db_trip[$i]['tSaddress'];
        if ('UberX' !== $APP_TYPE && !empty($db_trip[$i]['tDaddress'])) {
            $string .= ' -> '.$db_trip[$i]['tDaddress'];
        }
        $data .= str_replace(["\n", "\r", "\r\n", "\n\r"], ' ', $string)."\t";
        $data .= date('d-F-Y', strtotime($db_trip[$i]['tTripRequestDate']))."\t";
        if (isset($_SESSION['SessionUserType']) && 'hotel' !== $_SESSION['SessionUserType']) {
            $data .= clearCmpName($db_trip[$i]['vCompany'])."\t";
        }
        $data .= clearName($db_trip[$i]['driverName'])."\t";
        $data .= clearName($db_trip[$i]['riderName'])."\t";
        if ($db_trip[$i]['fCancellationFare'] > 0) {
            $db_trip[$i]['fCancellationFare'] = $db_trip[$i]['fCancellationFare'] + $db_trip[$i]['fTax1'] + $db_trip[$i]['fTax2'];
            $data .= formateNumAsPerCurrency($db_trip[$i]['fCancellationFare'], '')."\t";
        } else {
            $data .= formateNumAsPerCurrency($db_trip[$i]['iFare'] + $db_trip[$i]['fWalletDebit'], '')."\t";
        }
        if (isset($db_trip[$i]['tVehicleTypeData']) && '' !== $db_trip[$i]['tVehicleTypeData'] && '' === $vehicleTypeName) {
            $viewService = 1;
            $seriveJson = json_decode($db_trip[$i]['tVehicleTypeData'], true);
            $service_name = '';
            $c = 1;
            foreach ($seriveJson as $servc) {
                if ($c < count($seriveJson)) {
                    $new_line = "\n";
                } else {
                    $new_line = '';
                }
                $service_name .= $vehilceTypeArr[$servc['iVehicleTypeId']].$new_line;
                ++$c;
            }
            $data .= '"'.$service_name.'"'."\t";
        } else {
            if ($db_trip[$i]['iRentalPackageId'] > 0) {
                $data .= $db_trip[$i]['vRentalVehicleTypeName']."\t";
            } else {
                $data .= $db_trip[$i]['vVehicleType']."\t";
            }
        }
        if (('Finished' === $db_trip[$i]['iActive'] && 'Yes' === $db_trip[$i]['eCancelled']) || ($db_trip[$i]['fCancellationFare'] > 0) || ('Canceled' === $db_trip[$i]['iActive'] && $db_trip[$i]['fWalletDebit'] > 0)) {
            $data .= 'Cancelled'."\n";
        } elseif ('Finished' === $db_trip[$i]['iActive']) {
            $data .= 'Finished'."\n";
        } else {
            if ('Active' === $db_trip[$i]['iActive'] || 'On Going Trip' === $db_trip[$i]['iActive']) {
                if ('UberX' === $APP_TYPE || 'Ride-Delivery-UberX' === $APP_TYPE) {
                    $data .= 'On Job'."\n";
                } else {
                    $data .= 'On Ride'."\n";
                }
                if (!empty($db_trip[$i]['vDeliveryConfirmCode'])) {
                    $data .= '<div style="margin-top:15px;">Delivery Confirmation Code: '.$db_trip[$i]['vDeliveryConfirmCode'].'</div>'."\n";
                }
            } elseif ('Canceled' === $db_trip[$i]['iActive'] && ($db_trip[$i]['iCancelReasonId'] > 0 || '' !== $db_trip[$i]['vCancelReason'])) {
                if ($db_trip[$i]['iCancelReasonId'] > 0) {
                    $cancelreasonarray = getCancelReason($db_trip[$i]['iCancelReasonId'], $default_lang);
                    $db_trip[$i]['vCancelReason'] = $cancelreasonarray['vCancelReason'];
                } else {
                    $db_trip[$i]['vCancelReason'] = $db_trip[$i]['vCancelReason'];
                }
                $stringReason = stripcslashes($db_trip[$i]['vCancelReason'].' '.$db_trip[$i]['vCancelComment'])."\n Cancel By: ".stripcslashes($db_trip[$i]['eCancelledBy']);
                $data .= 'Cancel Reason: '.str_replace(["\n", "\r", "\r\n", "\n\r"], ' ', $stringReason)."\n";
            } elseif ('Canceled' === $db_trip[$i]['iActive'] && $db_trip[$i]['fWalletDebit'] < 0) {
                $data .= 'Cancelled'."\n";
            } else {
                $data .= $db_trip[$i]['iActive']."\n";
            }
        }
    }
    ob_clean();
    header('Content-type: application/octet-sdleam; charset=utf-8');
    header('Content-Disposition: attachment; filename=triplist.xls');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "{$header}\n{$data}";

    exit;
}
// Added By Hasmukh On 10-10-2018 For Export Organization Report Data csv from Screen Start
if ('organization_payment' === $section) {
    // Start Sorting
    $sortby = $_REQUEST['sortby'] ?? 0;
    $order = $_REQUEST['order'] ?? '';
    $searchOrganization = $_REQUEST['searchDriver'] ?? '';
    $action = ($_REQUEST['action'] ?? '');
    $ord = ' ORDER BY tr.iTripId DESC';
    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY rd.vName ASC';
        } else {
            $ord = ' ORDER BY rd.vName DESC';
        }
    }
    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY ru.vName ASC';
        } else {
            $ord = ' ORDER BY ru.vName DESC';
        }
    }
    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY tr.tTripRequestDate ASC';
        } else {
            $ord = ' ORDER BY tr.tTripRequestDate DESC';
        }
    }
    if (4 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY d.vName ASC';
        } else {
            $ord = ' ORDER BY d.vName DESC';
        }
    }
    if (5 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY u.vName ASC';
        } else {
            $ord = ' ORDER BY u.vName DESC';
        }
    }
    if (6 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY tr.eType ASC';
        } else {
            $ord = ' ORDER BY tr.eType DESC';
        }
    }
    // End Sorting
    if ('search' === $action) {
        if ('' !== $startDate) {
            $ssql .= " AND Date(tr.tTripRequestDate) >='".$startDate."'";
        }
        if ('' !== $endDate) {
            $ssql .= " AND Date(tr.tTripRequestDate) <='".$endDate."'";
        }
        if ('' !== $serachTripNo) {
            $ssql .= " AND tr.vRideNo ='".$serachTripNo."'";
        }
        if ('' !== $searchOrganization) {
            $ssql .= " AND tr.iOrganizationId ='".$searchOrganization."'";
        }
        if ('' !== $searchUser) {
            $ssql .= " AND tr.iUserId ='".$iUserId."'";
        }
        if ('' !== $searchDriverPayment) {
            $ssql .= " AND tr.eOrganizationPaymentStatus ='".$eDriverPaymentStatus."'";
        }
        if ('' !== $searchPaymentType) {
            $ssql .= " AND tr.vTripPaymentMode ='".$vTripPaymentMode."'";
        }
        if ('' !== $eType) {
            if ('Ride' === $eType) {
                $ssql .= " AND tr.eType ='".$eType."' AND tr.iRentalPackageId = 0 AND tr.eHailTrip = 'No' ";
            } elseif ('RentalRide' === $eType) {
                $ssql .= " AND tr.eType ='Ride' AND tr.iRentalPackageId > 0";
            } elseif ('HailRide' === $eType) {
                $ssql .= " AND tr.eType ='Ride' AND tr.eHailTrip = 'Yes'";
            } else {
                $ssql .= " AND tr.eType ='".$eType."' ";
            }
        }
    }
    $trp_ssql = $header = $data = '';
    if (SITE_TYPE === 'Demo') {
        $trp_ssql = " And tr.tTripRequestDate > '".WEEK_DATE."'";
    }
    // Pagination Start
    $org_sql = 'SELECT iOrganizationId,vCompany AS driverName,vEmail FROM organization order by vCompany';
    $db_organization = $obj->MySQLSelect($org_sql);
    $orgNameArr = [];
    for ($g = 0; $g < count($db_organization); ++$g) {
        $orgNameArr[$db_organization[$g]['iOrganizationId']] = $db_organization[$g]['driverName'];
    }
    $per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
    $sql = "SELECT tr.iTripId,tr.fHotelCommision,tr.vRideNo,tr.iDriverId,tr.iOrganizationId,tr.iUserId,tr.tTripRequestDate, tr.eType, tr.eHailTrip,tr.fTripGenerateFare,tr.fCommision, tr.fDiscount, tr.fWalletDebit, tr.fTipPrice,tr.eOrganizationPaymentStatus,tr.ePaymentCollect,tr.vTripPaymentMode,tr.iActive,tr.fOutStandingAmount, tr.iRentalPackageId,c.vCompany,concat(rd.vName,' ',rd.vLastName) as drivername,concat(ru.vName,' ',ru.vLastName) as riderName FROM trips AS tr LEFT JOIN register_driver AS rd ON tr.iDriverId = rd.iDriverId LEFT JOIN register_user AS ru ON tr.iUserId = ru.iUserId LEFT JOIN company as c ON rd.iCompanyId = c.iCompanyId WHERE  if(tr.iActive ='Canceled',if(tr.vTripPaymentMode='Card',1=1,0),1=1) AND tr.iActive ='Finished' AND tr.iOrganizationId >0 AND tr.eSystem='General' {$ssql} {$trp_ssql} {$ord}";
    // echo "<pre>";
    $totalData = $obj->MySQLSelect($sql);
    if ('UberX' !== $APP_TYPE && 'Delivery' !== $APP_TYPE) {
        $header .= $langage_lbl_admin['LBL_TRIP_TYPE_TXT_ADMIN']."\t";
    }
    // echo "<pre>";  print_r($sql);
    $header .= $langage_lbl_admin['LBL_RIDE_NO_ADMIN'].".\t";
    // $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'] . "\t";
    $header .= $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']."\t";
    $header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'].' Date'."\t";
    $header .= 'A=Total Fare'."\t";
    $header .= 'B=Platform Fees'."\t";
    $header .= 'C= Promo Code Discount'."\t";
    $header .= 'D = Wallet Debit'."\t";
    if ('Yes' === $ENABLE_TIP_MODULE) {
        $header .= 'E = Tip'."\t";
    }
    $header .= 'F = Trip Outstanding Amount'."\t";
    $header .= 'G = Booking Fees  '."\t";
    $header .= $langage_lbl_admin['LBL_ORGANIZATION'].' pay Amount'."\t";
    $header .= $langage_lbl_admin['LBL_RIDE_TXT_ADMIN'].' Status'."\t";
    $header .= 'Payment method'."\t";
    $header .= $langage_lbl_admin['LBL_ORGANIZATION'].' Payment Status';
    // echo "<pre>";
    $total_tip = $tot_fare = $tot_site_commission = $tot_promo_discount = $tot_driver_refund = $tot_wallentPayment = $tot_outstandingAmount = $tot_hotel_commision = 0.00;
    for ($j = 0; $j < count($totalData); ++$j) {
        $orfName = '';
        if (isset($orgNameArr[$totalData[$j]['iOrganizationId']]) && '' !== $orgNameArr[$totalData[$j]['iOrganizationId']]) {
            $orfName = '('.$orgNameArr[$totalData[$j]['iOrganizationId']].')';
        }
        $totalfare = trip_currency_payment($totalData[$j]['fTripGenerateFare']);
        $site_commission = trip_currency_payment($totalData[$j]['fCommision']);
        $promocodediscount = trip_currency_payment($totalData[$j]['fDiscount']);
        $wallentPayment = trip_currency_payment($totalData[$j]['fWalletDebit']);
        $fTipPrice = trip_currency_payment($totalData[$j]['fTipPrice']);
        $fOutStandingAmount = trip_currency_payment($totalData[$j]['fOutStandingAmount']);
        $hotel_commision = trip_currency_payment($totalData[$j]['fHotelCommision']);
        if ('Cash' === $totalData[$j]['vTripPaymentMode']) {
            // $driver_payment = ($promocodediscount+$wallentPayment)-($site_commission+$fOutStandingAmount+$hotel_commision);
        }
        // $driver_payment = ($fTipPrice+$totalfare)-($site_commission+$fOutStandingAmount+$hotel_commision);
        // $driver_payment = $totalfare - $site_commission + $fTipPrice - $fOutStandingAmount - $hotel_commision;

        $driver_payment = ($fTipPrice + $totalfare) - ($site_commission + $fOutStandingAmount + $hotel_commision);
        $class_setteled = '';
        if ('Settelled' === $totalData[$j]['eOrganizationPaymentStatus']) {
            $class_setteled = 'setteled-class';
        }
        $tot_fare += $totalfare;
        $tot_site_commission += $site_commission;
        $tot_hotel_commision += $hotel_commision;
        $tot_promo_discount += $promocodediscount;
        $tot_wallentPayment += $wallentPayment;
        $total_tip += $fTipPrice;
        $tot_driver_refund += $driver_payment;
        $cashPayment = $site_commission;
        $cardPayment = $totalfare - $site_commission;
        $tot_outstandingAmount += $fOutStandingAmount;
        $eType = $totalData[$j]['eType'];
        if ('Ride' === $eType) {
            $trip_type = 'Ride';
        } elseif ('UberX' === $eType) {
            $trip_type = 'Other Services';
        } elseif ('Deliver' === $eType) {
            $trip_type = 'Delivery';
        }
        if ('UberX' !== $APP_TYPE && 'Delivery' !== $APP_TYPE) {
            if ('Yes' === $totalData[$j]['eHailTrip'] && $totalData[$j]['iRentalPackageId'] > 0) {
                $data .= 'Rental '.$trip_type.' ( Hail )'."\t";
            } elseif ($totalData[$j]['iRentalPackageId'] > 0) {
                $data .= 'Rental '.$trip_type."\t";
            } elseif ('Yes' === $totalData[$j]['eHailTrip']) {
                $data .= 'Hail '.$trip_type."\t";
            } else {
                $data .= $trip_type."\t";
            }
        }
        $data .= $totalData[$j]['vRideNo']."\t";
        // $data .= clearName($totalData[$j]['drivername']) . "\t";
        $data .= clearName($totalData[$j]['riderName'])."\t";
        $data .= DateTime($totalData[$j]['tTripRequestDate'])."\t";
        $data .= ('' !== $totalfare && 0 !== $totalfare) ? $totalfare."\t" : "- \t";
        $data .= ('' !== $site_commission && 0 !== $site_commission) ? $site_commission."\t" : "- \t";
        $data .= ('' !== $promocodediscount && 0 !== $promocodediscount) ? $promocodediscount."\t" : "- \t";
        $data .= ('' !== $wallentPayment && 0 !== $wallentPayment) ? $wallentPayment."\t" : "- \t";
        if ('Yes' === $ENABLE_TIP_MODULE) {
            $data .= ('' !== $fTipPrice && 0 !== $fTipPrice) ? $fTipPrice."\t" : "- \t";
        }
        $data .= ('' !== $fOutStandingAmount && 0 !== $fOutStandingAmount) ? $fOutStandingAmount."\t" : "- \t";
        $data .= ('' !== $hotel_commision && 0 !== $hotel_commision) ? $hotel_commision."\t" : "- \t";
        $data .= ('' !== $totalfare && 0 !== $totalfare) ? $totalfare."\t" : "- \t";
        $data .= $totalData[$j]['iActive']."\t";
        $data .= $totalData[$j]['vTripPaymentMode'].' '.$orfName."\t";
        $data .= $totalData[$j]['eOrganizationPaymentStatus']."\n";
    }
    $data .= "\n\t\t\t\t\t\t\t\t\t\t\tTotal Fare\t".setTwoDecimalValue($tot_fare)."\n";
    $data .= "\t\t\t\t\t\t\t\t\t\t\tTotal Platform Fees\t".setTwoDecimalValue($tot_site_commission)."\n";
    $data .= "\t\t\t\t\t\t\t\t\t\t\tTotal Promo Discount\t".setTwoDecimalValue($tot_promo_discount)."\n";
    $data .= "\t\t\t\t\t\t\t\t\t\t\tTotal Wallet Debit\t".setTwoDecimalValue($tot_wallentPayment)."\n";
    if ('Yes' === $ENABLE_TIP_MODULE) {
        $data .= "\t\t\t\t\t\t\t\t\t\t\tTotal Tip Amount\t".setTwoDecimalValue($total_tip)."\n";
        $data .= "\t\t\t\t\t\t\t\t\t\t\tTotal Trip Outstanding Amount\t".setTwoDecimalValue($tot_outstandingAmount)."\n";
        $data .= "\t\t\t\t\t\t\t\t\t\t\tTotal Booking Fees\t".setTwoDecimalValue($tot_hotel_commision)."\n";
        $data .= "\t\t\t\t\t\t\t\t\t\t\tTotal Total Payment Amount\t".setTwoDecimalValue($tot_driver_refund)."\n";
    } else {
        $data .= "\t\t\t\t\t\t\t\t\t\t\tTotal Trip Outstanding Amount\t".setTwoDecimalValue($tot_outstandingAmount)."\n";
        $data .= "\t\t\t\t\t\t\t\t\t\t\tTotal Total Payment Amount Payment\t".setTwoDecimalValue($tot_driver_refund)."\n";
    }
    $data = str_replace("\r", '', $data);
    // echo $data;die;
    ob_clean();
    header('Content-type: application/octet-stream; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$time.'_organization_payment.xls');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "{$header}\n{$data}";

    exit;
}
if ('trips_statistics_report' === $section) {
    // echo "<pre>";
    $date1 = $startDate.' '.'00:00:00';
    $date2 = $endDate.' '.'23:59:59';
    if ('' !== $startDate && '' !== $endDate) {
        $ssql .= "TR.tTripRequestDate between '{$date1}' and '{$date2}'";
    }
    $totalData = $obj->MySQLSelect("SELECT iActive,DATE_FORMAT(TR.tTripRequestDate, '%Y-%m-%d') AS REQUEST_DATE FROM trips TR WHERE {$ssql} ORDER BY tTripRequestDate DESC");
    $finalTripArr = [];
    for ($r = 0; $r < count($totalData); ++$r) {
        $date = $totalData[$r]['REQUEST_DATE'];
        $tripStatus = $totalData[$r]['iActive'];
        $finalTripArr[$date]['date'] = $date;
        if (isset($finalTripArr[$date]['total'])) {
            ++$finalTripArr[$date]['total'];
        } else {
            $finalTripArr[$date]['total'] = 1;
        }
        if (isset($finalTripArr[$date][$tripStatus])) {
            ++$finalTripArr[$date][$tripStatus];
        } else {
            $finalTripArr[$date][$tripStatus] = 1;
        }
    }
    $trp_ssql = $header = $data = '';
    $header .= "Trip Date.\t";
    $header .= "Total Trips\t";
    $header .= "Active Trips\t";
    $header .= "Ongoing Trips\t";
    $header .= "Completed Trips\t";
    $header .= "Cancelled Trips\t";
    // echo "<pre>";
    $totTrips = $totCompleted = $totCancelled = $totOngoing = $totActive = 0;
    foreach ($finalTripArr as $key => $val) {
        $totalTrips = $cancelledTrips = $completedTrips = $ongoingTrips = $activeTrips = 0;
        $tripDate = $val['date'];
        if (isset($val['total']) && $val['total'] > 0) {
            $totalTrips = $val['total'];
        }
        $totTrips += $totalTrips;
        if (isset($val['Active']) && $val['Active'] > 0) {
            $activeTrips = $val['Active'];
        }
        $totActive += $activeTrips;
        if (isset($val['Finished']) && $val['Finished'] > 0) {
            $completedTrips = $val['Finished'];
        }
        $totCompleted += $completedTrips;
        if (isset($val['Canceled']) && $val['Canceled'] > 0) {
            $cancelledTrips = $val['Canceled'];
        }
        $totCancelled += $cancelledTrips;
        if (isset($val['On Going Trip']) && $val['On Going Trip'] > 0) {
            $ongoingTrips = $val['On Going Trip'];
        }
        $totOngoing += $ongoingTrips;
        $data .= $tripDate."\t";
        $data .= $totalTrips."\t";
        $data .= $activeTrips."\t";
        $data .= $ongoingTrips."\t";
        $data .= $completedTrips."\t";
        $data .= $cancelledTrips."\n";
    }
    $data .= "Total\t";
    $data .= $totTrips."\t";
    $data .= $totActive."\t";
    $data .= $totOngoing."\t";
    $data .= $totCompleted."\t";
    $data .= $totCancelled."\n";
    $data = str_replace("\r", '', $data);
    ob_clean();
    header('Content-type: application/octet-stream; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$time.'_trips_statistics_report.xls');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "{$header}\n{$data}";

    exit;
}
// Added By Hasmukh On 10-10-2018 For Export Organization Report Data csv from Screen End
function setTwoDecimalValue($amount)
{
    return number_format($amount, 2);
}

if ('insurance_report' === $section) {
    $eType = $_REQUEST['eType'] ?? '';
    $eAddedFor = $_REQUEST['eAddedFor'] ?? 'Available';
    $trp_ssql = '';
    if (SITE_TYPE === 'Demo') {
        $trp_ssql = " And dir.dStartDate > '".WEEK_DATE."'";
    }
    $ord = ' ORDER BY dir.iInsuranceReportId DESC';
    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY tr.vRideNo ASC';
        } else {
            $ord = ' ORDER BY tr.vRideNo DESC';
        }
    }
    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY rd.vName ASC';
        } else {
            $ord = ' ORDER BY rd.vName DESC';
        }
    }
    if (3 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY rd.vEmail ASC';
        } else {
            $ord = ' ORDER BY rd.vEmail DESC';
        }
    }
    if (5 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY dir.dStartDate ASC';
        } else {
            $ord = ' ORDER BY dir.dStartDate DESC';
        }
    }
    if (6 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY dir.dEndDate ASC';
        } else {
            $ord = ' ORDER BY dir.dEndDate DESC';
        }
    }
    $ssql = '';
    if ('' !== $startDate) {
        $ssql .= " AND Date(dir.dStartDate) >='".$startDate."'";
    }
    if ('' !== $endDate) {
        $ssql .= " AND Date(dir.dStartDate) <='".$endDate."'";
    }
    if ('' !== $iDriverId) {
        $ssql .= " AND dir.iDriverId = '".$iDriverId."'";
    }
    if ('' !== $serachTripNo) {
        $ssql .= " AND tr.vRideNo ='".$serachTripNo."'";
    }
    $sql = "SELECT dir.`iInsuranceReportId`, dir.`iDriverId`, dir.`iTripId`,dir.vDistance, dir.`dStartDate`, dir.`dEndDate`, dir.`tStartLat`, dir.`tStartLong`, dir.`tStartLocation`, dir.`tEndLat`, dir.`tEndLong`, dir.`tEndLocation`, dir.`eAddedFor`,tr.vRideNo,tr.eType,tr.fDistance, concat(rd.vName,' ',rd.vLastName) as drivername,rd.vEmail as driveremail,concat('+',rd.vCode,rd.vPhone) as driverphone FROM driver_insurance_report AS dir
	LEFT JOIN trips AS tr ON tr.iTripId = dir.iTripId
	LEFT JOIN register_driver AS rd ON rd.iDriverId = dir.iDriverId where 1=1 and eAddedFor='{$eAddedFor}' {$ssql} {$trp_ssql} {$ord}";
    $db_trip = $obj->MySQLSelect($sql);
    // echo "<pre>".$data;print_r($db_trip);exit;
    $header .= $langage_lbl_admin['LBL_TRIP_TXT'].' Number'."\t";
    $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']."Name \t";
    $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Email'."\t";
    $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Phone'."\t";
    if ('Accept' === $eAddedFor) {
        $header .= $langage_lbl_admin['LBL_TRIP_TXT'].' Accepted Time'."\t";
        $header .= $langage_lbl_admin['LBL_TRIP_TXT'].' Start/Cancel Time'."\t";
        $header .= 'Approx Distance Travelled'."\t";
    } elseif ('Trip' === $eAddedFor) {
        $header .= $langage_lbl_admin['LBL_TRIP_TXT'].' Start Time'."\t";
        $header .= $langage_lbl_admin['LBL_TRIP_TXT'].' End Time'."\t";
        $header .= 'Distance Travelled'."\t";
    } else {
        $header .= 'Online Time'."\t";
        $header .= $langage_lbl_admin['LBL_TRIP_TXT'].' Accepted/Offline Time'."\t";
        $header .= 'Approx Distance Travelled'."\t";
    }
    $header .= 'Time Taken to Distance Travelled'."\t";
    for ($j = 0; $j < count($db_trip); ++$j) {
        $vRideNo = ('' !== $db_trip[$j]['vRideNo']) ? $db_trip[$j]['vRideNo'] : '---';
        $data .= $vRideNo."\t";
        $data .= clearName($db_trip[$j]['drivername'])."\t";
        $data .= clearEmail($db_trip[$j]['driveremail'])."\t";
        $data .= clearPhone($db_trip[$j]['driverphone'])."\t";
        $data .= DateTime($db_trip[$j]['dStartDate'])."\t";
        $data .= DateTime($db_trip[$j]['dEndDate'])."\t";
        $distance_tot = ('Trip' === $eAddedFor) ? $db_trip[$j]['fDistance'] : $db_trip[$j]['vDistance'];
        $distance_tot = ('' === $distance_tot) ? '0' : $distance_tot;
        $vDistance = number_format($distance_tot, 2);
        if ('Miles' === $DEFAULT_DISTANCE_UNIT) {
            $vDistance1 = str_replace(',', '', $vDistance);
            $vDistance = number_format($vDistance1 * 0.621_371, 2);
        }
        $data .= $vDistance.' '.$DEFAULT_DISTANCE_UNIT."\t";
        $a = strtotime($db_trip[$j]['dStartDate']);
        $b = strtotime($db_trip[$j]['dEndDate']);
        $diff_time = ($b - $a);
        $ans_diff = set_hour_min($diff_time);
        // echo "<pre>";print_r($ans_diff);//exit;
        $data_time_txt = '';
        if (0 !== $ans_diff['hour']) {
            $data_time_txt = $ans_diff['hour'].' Hours '.$ans_diff['minute'].' Minutes'."\t";
        } else {
            if (0 !== $ans_diff['minute']) {
                $data_time_txt .= $ans_diff['minute'].' Minutes ';
            }
            if ($ans_diff['second'] < 0) {
                $data_time_txt .= '---'."\t";
            } else {
                $data_time_txt .= $ans_diff['second'].' Seconds'."\t";
            }
        }
        $data .= $data_time_txt."\n";
    }
    $data = str_replace("\r", '', $data);
    // echo "<pre>".$data;print_r($data);exit;
    $filename = 'insurance_'.$eAddedFor.'_report.xls';
    ob_clean();
    header('Content-type: application/octet-stream; charset=utf-8');
    header("Content-Disposition: attachment; filename={$filename}");
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "{$header}\n{$data}";

    exit;
}

if ('trackingTripList' === $section) {
    $startDate = ($_REQUEST['startDate'] ?? '');
    $endDate = ($_REQUEST['endDate'] ?? '');
    $ord = ' ORDER BY t.iTrackServiceTripId DESC';
    if ('' !== $startDate) {
        $ssql .= " AND Date(t.dStartDate) >='".$startDate."'";
    }
    if ('' !== $endDate) {
        $ssql .= " AND Date(t.dStartDate) <='".$endDate."'";
    }

    $sql = "SELECT t.iTrackServiceTripId,d.vName,d.vLastName,t.tStartLocation,t.tEndLocation,t.dStartDate,t.eTripStatus,t.eTripType
        FROM track_service_trips t LEFT JOIN register_driver d ON d.iDriverId = t.iDriverId
        WHERE 1=1  {$ssql}  {$ord}";

    $db_trip = $obj->MySQLSelect($sql);
    $header = $data = '';
    $header .= 'Pickup Location'."\t";
    $header .= 'Dropoff  Location'."\t";
    $header .= 'Provider'."\t";
    $header .= 'Trip Date'."\t";
    $header .= 'Trip Type'."\t";
    $header .= 'Status'."\t";
    for ($j = 0; $j < count($db_trip); ++$j) {
        $data .= $db_trip[$j]['tStartLocation']."\t";
        $data .= $db_trip[$j]['tEndLocation']."\t";
        $data .= $db_trip[$j]['vName'].' '.$db_trip[$j]['vLastName']."\t";
        $data .= DateTime($db_trip[$j]['dStartDate'], '21')."\t";
        $data .= $db_trip[$j]['eTripType']."\t";
        $data .= $db_trip[$j]['eTripStatus']."\t";
        $data .= "\n";
    }

    ob_clean();
    header('Content-type: application/octet-sdleam; charset=utf-8');
    header('Content-Disposition: attachment; filename=Tracking company list.xls');
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "{$header}\n{$data}";

    exit;
}

if ('PublishedRides' === $section) {
    $eStatus = $_REQUEST['eStatus'] ?? '';
    $startDate = $_REQUEST['startDate'] ?? '';
    $endDate = $_REQUEST['endDate'] ?? '';
    $ord = ' ORDER BY pr.iPublishedRideId  DESC';
    $ssql_date = '';
    if ('' !== $searchRider) {
        $ssql .= " AND pr.iUserId = {$searchRider} ";
    }

    if ('' !== $eStatus) {
        if ('PastRides' === $eStatus) {
            $ssql_date = " AND pr.dStartDate < '".date('Y-m-d H:i:s')."' AND pr.eStatus = 'Active' ";
        } elseif ('Active' === $eStatus) {
            $ssql_date = " AND pr.dStartDate >= '".date('Y-m-d H:i:s')."' AND pr.eStatus = 'Active'";
        } else {
            $ssql .= "AND pr.eStatus = '{$eStatus}' ";
        }
    }

    if ('' !== $searchRideNo) {
        $ssql .= " AND pr.vPublishedRideNo = {$searchRideNo} ";
    }

    /*if ($startDate != '') {

        $ssql .= " AND Date(pr.dStartDate) >='" . $startDate . "'";
    }
    if ($endDate != '') {

        $ssql .= " AND Date(pr.dStartDate) <='" . $endDate . "'";
    }*/

    if ('' !== $startDate) {
        $ssql .= " AND Date(pr.dAddedDate) >='".$startDate."'";
    }
    if ('' !== $endDate) {
        $ssql .= " AND Date(pr.dAddedDate) <='".$endDate."'";
    }
    $sql = "SELECT pr.*, pr.iUserId AS driver_Id , CONCAT(riderDriver.vName,' ',riderDriver.vLastName) AS driver_Name FROM published_rides pr
         JOIN register_user riderDriver  ON (riderDriver.iUserId = pr.iUserId)
         WHERE 1=1 {$ssql} {$ssql_date} {$ord}";

    $db_trip = $obj->MySQLSelect($sql);

    $header = 'Ride No.'."\t";
    $header .= 'Published By'."\t";
    $header .= 'Ride Start Time'."\t";
    $header .= 'Ride End Time'."\t";
    $header .= 'Duration'."\t";
    $header .= 'Start Location'."\t";
    $header .= 'End Location'."\t";
    $header .= 'Published Date'."\t";
    $header .= 'Price Per Seat'."\t";
    $header .= 'Total seats'."\t";
    $header .= 'Occupied Seats'."\t";
    $header .= 'Status'."\t";

    for ($i = 0; $i < count($db_trip); ++$i) {
        if ($db_trip[$i]['iBookedSeats'] > 0 && 'Cancelled' !== $db_trip[$i]['eStatus']) {
            $iBookedSeats = $db_trip[$i]['iBookedSeats'];
        } else {
            $iBookedSeats = '-';
        }

        if (strtotime($db_trip[$i]['dStartDate']) > strtotime(date('Y-m-d H:i:s')) || 'Cancelled' === $db_trip[$i]['eStatus']) {
            $eStatus = $db_trip[$i]['eStatus'];
        } else {
            $eStatus = '-';
        }
        $time = $RIDE_SHARE_OBJ->convertSecToMin(floor($db_trip[$i]['fDuration']));
        $data .= $db_trip[$i]['vPublishedRideNo']."\t";
        $data .= $db_trip[$i]['driver_Name']."\t";
        $data .= date('M d, Y  h:i A', strtotime($db_trip[$i]['dStartDate']))."\t";
        $data .= date('M d, Y  h:i A', strtotime($db_trip[$i]['dEndDate']))."\t";
        $data .= $time."\t";
        $data .= $db_trip[$i]['tStartLocation']."\t";
        $data .= $db_trip[$i]['tEndLocation']."\t";
        $data .= date('M d, Y h:i A', strtotime($db_trip[$i]['dAddedDate']))."\t";
        $data .= formateNumAsPerCurrency($db_trip[$i]['fPrice'], '')."\t";
        $data .= $db_trip[$i]['iAvailableSeats']."\t";
        $data .= $iBookedSeats."\t";
        $data .= $eStatus."\n";
    }

    $data = str_replace("\r", '', $data);

    // echo "<pre>".$data;print_r($data);exit;
    $filename = 'PublishedRides'.$eAddedFor.'_report.xls';
    ob_clean();
    header('Content-type: application/octet-stream; charset=utf-8');
    header("Content-Disposition: attachment; filename={$filename}");
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "{$header}\n{$data}";

    exit;
}

if ('PublishedRidesBooking' === $section) {
    $option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : '';
    $keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : '';
    $searchDate = $_REQUEST['searchDate'] ?? '';
    $eStatus = $_REQUEST['eStatus'] ?? '';
    $searchRider = $_REQUEST['searchRider'] ?? '';
    $searchDriver = $_REQUEST['searchDriver'] ?? '';
    $startDate = $_REQUEST['startDate'] ?? '';
    $endDate = $_REQUEST['endDate'] ?? '';
    $isRideShare = $_REQUEST['rideShare'] ?? '';

    $ssql = '';
    if ('' !== $searchRider) {
        $ssql .= " AND rsb.iUserId = {$searchRider} ";
    }
    if ('' !== $searchDriver) {
        $ssql .= " AND pr.iUserId = {$searchDriver} ";
    }
    if ('' !== $eStatus) {
        $ssql .= " AND rsb.eStatus = '{$eStatus}' ";
    }
    if (isset($iPublishedRideId) && !empty($iPublishedRideId)) {
        $ssql .= ' AND rsb.iPublishedRideId = '.$iPublishedRideId." AND rsb.eStatus = 'Approved'";
    }

    if ('' !== $startDate) {
        $ssql .= " AND Date(rsb.dBookingDate) >='".$startDate."'";
    }
    if ('' !== $endDate) {
        $ssql .= " AND Date(rsb.dBookingDate) <='".$endDate."'";
    }

    $sql = "SELECT  CONCAT(riderDriver.vName,' ',riderDriver.vLastName) AS driver_Name,  CONCAT(riderUser.vName,' ',riderUser.vLastName) AS  rider_Name,
                riderUser.vImgName as rider_ProfileImg, riderUser.iUserId as rider_iUserId, riderDriver.iUserId as driver_iUserId,
                pr.tStartLocation,pr.tStartLat,pr.tStartLong,pr.tEndLocation,pr.tEndLat,pr.tEndLong,pr.dStartDate,pr.dStartDate,pr.dEndDate,pr.tPriceRatio,
                pr.tEndCity,pr.tStartCity,rsb.dBookingDate,pr.fDuration,

                rsb.iPublishedRideId,rsb.eStatus,rsb.fTotal,rsb.iBookedSeats,pr.tDriverDetails,rsb.iBookingId,rsb.iCancelReasonId,rsb.tCancelReason,rsb.vBookingNo
                FROM ride_share_bookings rsb
                JOIN published_rides pr ON (pr.iPublishedRideId = rsb.iPublishedRideId)
                JOIN register_user riderUser  ON (riderUser.iUserId = rsb.iUserId)
                JOIN register_user riderDriver  ON (riderDriver.iUserId = pr.iUserId)  WHERE 1=1 {$ssql} {$ord}";

    $db_trip = $obj->MySQLSelect($sql);

    $header = 'Booking No.'."\t";
    $header .= 'Booked By'."\t";
    $header .= 'Published By'."\t";
    $header .= 'Ride Start Time'."\t";
    $header .= 'Ride End Time'."\t";
    $header .= 'Start Location'."\t";
    $header .= 'End Location'."\t";
    $header .= 'Booked Seats'."\t";
    $header .= 'Booking Date'."\t";
    $header .= 'Booking Status'."\t";

    for ($i = 0; $i < count($db_trip); ++$i) {
        $time = $RIDE_SHARE_OBJ->convertSecToMin(floor($db_trip[$i]['fDuration']));
        $data .= $db_trip[$i]['vBookingNo']."\t";
        $data .= $db_trip[$i]['rider_Name']."\t";
        $data .= $db_trip[$i]['driver_Name']."\t";
        $data .= date('M d, Y  h:i A', strtotime($db_trip[$i]['dStartDate']))."\t";
        $data .= date('M d, Y  h:i A', strtotime($db_trip[$i]['dEndDate']))."\t";
        $data .= $db_trip[$i]['tStartLocation']."\t";
        $data .= $db_trip[$i]['tEndLocation']."\t";
        $data .= $db_trip[$i]['iBookedSeats']."\t";
        $data .= date('M d, Y  h:i A', strtotime($db_trip[$i]['dBookingDate']))."\t";
        $data .= $db_trip[$i]['eStatus']."\n";
    }

    $data = str_replace("\r", '', $data);

    // echo "<pre>".$data;print_r($data);exit;
    $filename = 'PublishedRidesBooking_report.xls';
    ob_clean();
    header('Content-type: application/octet-stream; charset=utf-8');
    header("Content-Disposition: attachment; filename={$filename}");
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "{$header}\n{$data}";

    exit;
}

if ('RideSharePaymentReport' === $section) {
    $option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : '';
    $keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : '';
    $searchDate = $_REQUEST['searchDate'] ?? '';
    $eStatus = $_REQUEST['eStatus'] ?? '';
    $searchRider = $_REQUEST['searchRider'] ?? '';
    $searchDriver = $_REQUEST['searchDriver'] ?? '';
    $searchBookingNo = $_REQUEST['searchBookingNo'] ?? '';
    $searchRideNo = $_REQUEST['searchRideNo'] ?? '';
    $searchPaymentStatus = $_REQUEST['searchPaymentStatus'] ?? '';
    $startDate = $_REQUEST['startDate'] ?? '';
    $endDate = $_REQUEST['endDate'] ?? '';
    $endDate = isset($_REQUEST['rideShare']) ? $_REQUEST['endDate'] : '';
    $ord = ' ORDER BY pr.iPublishedRideId  DESC';

    if ('' !== $searchRider) {
        $ssql .= " AND rsb.iUserId = {$searchRider} ";
    }
    if ('' !== $searchDriver) {
        $ssql .= " AND pr.iUserId = {$searchDriver} ";
    }
    if ('' !== $searchBookingNo) {
        $ssql .= " AND rsb.vBookingNo IN ({$searchBookingNo}) ";
    }
    if ('' !== $searchRideNo) {
        $ssql .= " AND pr.vPublishedRideNo = {$searchRideNo} ";
    }
    if ('' !== $searchPaymentStatus) {
        $ssql .= " AND rsb.ePaymentStatus = '{$searchPaymentStatus}' ";
    }
    /*if ($startDate != '') {
        $ssql .= " AND Date(pr.dStartDate) >='" . $startDate . "'";
    }
    if ($endDate != '') {
        $ssql .= " AND Date(pr.dStartDate) <='" . $endDate . "'";
    }*/

    if ('' !== $startDate) {
        $ssql .= " AND Date(pr.dAddedDate) >='".$startDate."'";
    }
    if ('' !== $endDate) {
        $ssql .= " AND Date(pr.dAddedDate) <='".$endDate."'";
    }
    if (isset($iPublishedRideId) && !empty($iPublishedRideId)) {
        $ssql .= ' AND rsb.iPublishedRideId = '.$iPublishedRideId;
    }
    $ssql .= "AND  (rsb.eStatus = 'Approved' OR (rsb.eStatus = 'Cancelled' AND rsb.eCommissionDeduct = 'Yes') )";

    $sql = "SELECT  CONCAT(riderDriver.vName,' ',riderDriver.vLastName) AS driver_Name,  CONCAT(riderUser.vName,' ',riderUser.vLastName) AS  rider_Name,
                riderUser.vImgName as rider_ProfileImg, riderUser.iUserId as rider_iUserId, riderDriver.iUserId as driver_iUserId,
                pr.vPublishedRideNo,pr.tStartLocation,pr.tStartLat,pr.tStartLong,pr.tEndLocation,pr.tEndLat,pr.tEndLong,pr.dStartDate,pr.dStartDate,pr.dEndDate,pr.tPriceRatio,
                pr.tEndCity,pr.tStartCity,rsb.vBookingNo,rsb.dBookingDate,
                rsb.iPublishedRideId,rsb.eStatus,rsb.fTotal,rsb.iBookedSeats,pr.tDriverDetails,rsb.iBookingId,rsb.iCancelReasonId,rsb.tCancelReason, rsb.iBookingId , rsb.fBookingFee, rsb.ePaymentOption, rsb.ePaymentStatus
                FROM ride_share_bookings rsb
                JOIN published_rides pr ON (pr.iPublishedRideId = rsb.iPublishedRideId)
                JOIN register_user riderUser  ON (riderUser.iUserId = rsb.iUserId)
                JOIN register_user riderDriver  ON (riderDriver.iUserId = pr.iUserId)  WHERE 1=1   {$ssql} {$ord}";

    $data_drv = $obj->MySQLSelect($sql);

    $header = 'Booking No.'."\t";
    $header .= 'Ride No.'."\t";
    $header .= 'Published By'."\t";
    $header .= 'Booked By'."\t";
    $header .= 'Booking Date'."\t";
    $header .= 'Ride Start Time'."\t";
    $header .= 'Ride End Time'."\t";
    $header .= 'Booked Seats'."\t";
    $header .= 'Booking Fee / Site Earning'."\t";
    $header .= 'Total'."\t";
    $header .= 'Publisher payout / take amount Payment'."\t";
    $header .= 'Payment Method'."\t";
    $header .= 'Status'."\t";
    $header .= 'Settle'."\t";

    for ($i = 0; $i < count($data_drv); ++$i) {
        $Refunded = '';
        if ('Cancelled' === $data_drv[$i]['eStatus']) {
            $Refunded = '(Refunded)';
        }
        $time = $RIDE_SHARE_OBJ->convertSecToMin(floor($data_drv[$i]['fDuration']));
        $class_setteled = '';
        if ('Settled' === $data_drv[$i]['ePaymentStatus']) {
            $class_setteled = 'setteled-class';
        }
        $set_unsetarray[] = $data_drv[$i]['ePaymentStatus'];
        $driverAmount = $data_drv[$i]['fTotal'] - $data_drv[$i]['fBookingFee'];
        if ('Cash' === $data_drv[$i]['ePaymentOption']) {
            $driverAmount -= $data_drv[$i]['fTotal'];
        }

        $data .= $data_drv[$i]['vBookingNo']."\t";
        $data .= $data_drv[$i]['vPublishedRideNo']."\t";
        $data .= $data_drv[$i]['driver_Name']."\t";
        $data .= $data_drv[$i]['rider_Name']."\t";
        $data .= date('M d, Y  h:i A', strtotime($data_drv[$i]['dBookingDate']))."\t";
        $data .= date('M d, Y  h:i A', strtotime($data_drv[$i]['dStartDate']))."\t";
        $data .= date('M d, Y  h:i A', strtotime($data_drv[$i]['dEndDate']))."\t";
        $data .= $db_trip[$i]['iBookedSeats']."\t";
        $data .= formateNumAsPerCurrency($data_drv[$i]['fBookingFee'], '')."\t";
        $data .= formateNumAsPerCurrency($data_drv[$i]['fTotal'], '')."\t";
        if ('Cancelled' === $data_drv[$i]['eStatus']) {
            $data .= '-'."\t";
        } else {
            $data .= $generalobj->formateNumAsPerCurrency($driverAmount, '')."\t";
        }
        $data .= $data_drv[$i]['ePaymentOption']."\t";
        $data .= $data_drv[$i]['eStatus'].$Refunded."\t";
        $data .= $data_drv[$i]['ePaymentStatus']."\n";
    }

    $data = str_replace("\r", '', $data);

    // echo "<pre>".$data;print_r($data);exit;
    $filename = 'PublishedRideReport_report.xls';
    ob_clean();
    header('Content-type: application/octet-stream; charset=utf-8');
    header("Content-Disposition: attachment; filename={$filename}");
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "{$header}\n{$data}";

    exit;
}

if ('total_trip_details' === $section) {
    $ord = ' ORDER BY t.iTripId DESC';
    $sortby = $_REQUEST['sortby'] ?? 0;
    $order = $_REQUEST['order'] ?? '';
    if (1 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY t.eType ASC';
        } else {
            $ord = ' ORDER BY t.eType DESC';
        }
    }

    if (2 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY t.tStartDate ASC';
        } else {
            $ord = ' ORDER BY t.tStartDate DESC';
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
            $ord = ' ORDER BY d.vName ASC';
        } else {
            $ord = ' ORDER BY d.vName DESC';
        }
    }

    if (5 === $sortby) {
        if (0 === $order) {
            $ord = ' ORDER BY u.vName ASC';
        } else {
            $ord = ' ORDER BY u.vName DESC';
        }
    }
    $ssql = '';
    $action = $_REQUEST['action'] ?? '';
    $searchCompany = $_REQUEST['searchCompany'] ?? '';
    $searchDriver = $_REQUEST['searchDriver'] ?? '';
    $searchRider = $_REQUEST['searchRider'] ?? '';
    $serachTripNo = $_REQUEST['serachTripNo'] ?? '';
    $startDate = $_REQUEST['startDate'] ?? '';
    $endDate = $_REQUEST['endDate'] ?? '';
    $vStatus = $_REQUEST['vStatus'] ?? '';

    if ('search' === $action) {
        if ('' !== $startDate) {
            $ssql .= " AND Date(t.tStartDate) >='".$startDate."'";
        }
        if ('' !== $endDate) {
            $ssql .= " AND Date(t.tStartDate) <='".$endDate."'";
        }
        if ('' !== $serachTripNo) {
            $ssql .= " AND t.vRideNo ='".$serachTripNo."'";
        }
        if ('' !== $searchCompany) {
            $ssql .= " AND d.iCompanyId ='".$searchCompany."'";
        }
        if ('' !== $searchDriver) {
            $ssql .= " AND t.iDriverId ='".$searchDriver."'";
        }
        if ('' !== $searchRider) {
            $ssql .= " AND t.iUserId ='".$searchRider."'";
        }
        if ('onRide' === $vStatus) {
            $ssql .= " AND (t.iActive = 'On Going Trip' OR t.iActive = 'Active') AND t.eCancelled='No'";
        } elseif ('cancel' === $vStatus) {
            $ssql .= " AND (t.iActive = 'Canceled' OR t.eCancelled='yes')";
        } elseif ('complete' === $vStatus) {
            $ssql .= " AND t.iActive = 'Finished' AND t.eCancelled='No'";
        }
    }

    $trp_ssql = '';
    if (SITE_TYPE === 'Demo') {
        $trp_ssql = " And t.tStartDate > '".WEEK_DATE."'";
    }

    $sql = "SELECT t.fTax1,t.fTax2,t.tStartDate ,t.tEndDate, t.tTripRequestDate,t.vCancelReason,t.vCancelComment, t.iFare,t.eType,d.iDriverId, t.tSaddress,t.vRideNo, t.tDaddress, t.fTripGenerateFare,t.fCommision, t.fDiscount, t.fWalletDebit, t.fTipPrice,t.vTripPaymentMode, t.eCarType,t.iTripId,t.iActive ,CONCAT(u.vName,' ',u.vLastName) AS riderName, CONCAT(d.vName,' ',d.vLastName) AS driverName, d.vAvgRating, c.vCompany, vt.vVehicleType FROM trips t LEFT JOIN register_driver d ON d.iDriverId = t.iDriverId LEFT JOIN vehicle_type vt ON vt.iVehicleTypeId = t.iVehicleTypeId LEFT JOIN  register_user u ON t.iUserId = u.iUserId JOIN company c ON c.iCompanyId=d.iCompanyId WHERE 1=1 AND if(t.iActive ='Canceled',if(t.vTripPaymentMode='Card',1=1,0),1=1) AND t.iActive ='Finished' {$ssql} {$trp_ssql} {$ord}";
    $db_trip = $obj->MySQLSelect($sql);

    if ('UberX' !== $APP_TYPE) {
        $header = $langage_lbl_admin['LBL_TRIP_TYPE_TXT_ADMIN']."\t";
    }
    $header .= $langage_lbl_admin['LBL_TRIP_NO']."\t";
    $header .= $langage_lbl_admin['LBL_TRIP_DATE_ADMIN']."\t";
    $header .= 'Company'."\t";
    $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']."\t";
    $header .= $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']."\t";
    $header .= $langage_lbl_admin['LBL_DRIVER_TRIP_FARE_TXT']."\t";
    $header .= 'Platform Fees'."\t";
    $header .= 'Total Tax'."\t";
    $header .= $langage_lbl_admin['LBL_DRIVER_TRIP_DISCOUNT']."\t";
    $header .= $langage_lbl_admin['LBL_DRIVER_TRIP_WALLET']."\t";
    $header .= $langage_lbl_admin['LBL_TRIP_CASH_PAYMENT']."\t";
    $header .= $langage_lbl_admin['LBL_TRIP_CARD_PAYMENT']."\t";

    for ($i = 0; $i < count($db_trip); ++$i) {
        $poolTxt = '';
        if ('Yes' === $db_trip[$i]['ePoolRide']) {
            $poolTxt = ' (Pool)';
        }
        $eTypenew = $db_trip[$i]['eType'];
        if ('Ride' === $eTypenew) {
            $trip_type = 'Ride';
        } elseif ('UberX' === $eTypenew) {
            $trip_type = 'Other Services';
        } elseif ('Multi-Delivery' === $eTypenew) {
            $trip_type = 'Multi-Delivery';
            $link_page = 'invoice_multi_delivery.php';
        } else {
            $trip_type = 'Delivery';
        }
        $trip_type .= $poolTxt;

        $totTax = $db_trip[$i]['fTax1'] + $db_trip[$i]['fTax2'];
        if ('UberX' !== $APP_TYPE) {
            $data .= $trip_type."\t";
        }
        $data .= $db_trip[$i]['vRideNo']."\t";
        $data .= DateTime($db_trip[$i]['tStartDate'])."\t";
        $data .= clearCmpName($db_trip[$i]['vCompany'])."\t";
        $data .= clearName($db_trip[$i]['driverName'])."\t";
        $data .= $db_trip[$i]['riderName']."\t";
        if ('' !== $db_trip[$i]['fTripGenerateFare'] && 0 !== $db_trip[$i]['fTripGenerateFare']) {
            $data .= trip_currency($db_trip[$i]['fTripGenerateFare'])."\t";
        } else {
            $data .= '-'."\t";
        }

        if ('' !== $db_trip[$i]['fCommision'] && 0 !== $db_trip[$i]['fCommision']) {
            $data .= trip_currency($db_trip[$i]['fCommision'])."\t";
        } else {
            $data .= '-'."\t";
        }

        $data .= trip_currency($totTax)."\t";

        if ('' !== $db_trip[$i]['fDiscount'] && 0 !== $db_trip[$i]['fDiscount']) {
            $data .= trip_currency($db_trip[$i]['fDiscount'])."\t";
        } else {
            $data .= '-'."\t";
        }

        if ('' !== $db_trip[$i]['fWalletDebit'] && 0 !== $db_trip[$i]['fWalletDebit']) {
            $data .= trip_currency($db_trip[$i]['fWalletDebit'])."\t";
        } else {
            $data .= '-'."\t";
        }

        if ('Cash' === $db_trip[$i]['vTripPaymentMode']) {
            if ('' !== $db_trip[$i]['iFare'] && 0 !== $db_trip[$i]['iFare']) {
                $data .= trip_currency($db_trip[$i]['iFare'])."\t";
            } else {
                $data .= '-'."\t";
            }
        } else {
            $data .= '-'."\t";
        }

        if ('Card' === $db_trip[$i]['vTripPaymentMode']) {
            if ('' !== $db_trip[$i]['iFare'] && 0 !== $db_trip[$i]['iFare']) {
                $data .= trip_currency($db_trip[$i]['iFare'])."\n";
            } else {
                $data .= '-'."\n";
            }
        } else {
            $data .= '-'."\n";
        }
    }

    $data = str_replace("\r", '', $data);
    // echo "<pre>";print_r($data);exit;
    $filename = 'total_trip_detail_report.xls';
    // ob_clean();
    header('Content-type: application/octet-stream; charset=utf-8');
    header("Content-Disposition: attachment; filename={$filename}");
    header('Pragma: no-cache');
    header('Expires: 0');
    echo "{$header}\n{$data}";

    exit;
}
