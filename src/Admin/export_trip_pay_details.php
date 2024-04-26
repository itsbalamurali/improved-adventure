<?php



include_once '../common.php';
// ini_set('display_errors',1);
// error_reporting(E_ALL);
$tbl_name = 'trips';

$abc = 'admin,company';

// echo "<pre>"; print_r($_REQUEST); exit;
// -----------------------------------------------

$action = $_REQUEST['action'];
$ssql = '';
if (!empty($_REQUEST['prev_start'])) {
    $startDate = date('Y-m-d', strtotime($_REQUEST['prev_start']));
} else {
    $startDate = '';
}

if (!empty($_REQUEST['prev_end'])) {
    $endDate = date('Y-m-d', strtotime($_REQUEST['prev_end']));
} else {
    $endDate = '';
}

$iCountryCode = $_REQUEST['prev_country'];

$ssl = '';

$actionType = $_REQUEST['vStatus'];

if ('onRide' === $actionType) {
    $ssl = " AND (t.iActive = 'On Going Trip' OR t.iActive = 'Active') AND t.eCancelled='No'";
} elseif ('cancel' === $actionType) {
    $ssl = " AND t.iActive = 'Canceled' OR t.eCancelled='yes'";
} elseif ('complete' === $actionType) {
    $ssl = " AND t.iActive = 'Finished' AND t.eCancelled='No'";
} else {
    $ssl = '';
}

if ('' !== $action && 'export' === $action) {
    // echo "come"; die;
    if (!empty($startDate)) {
        $ssql .= " AND Date(t.tStartDate) >='".$startDate."'";
    }
    if (!empty($endDate)) {
        $ssql .= " AND Date(t.tEndDate) <='".$endDate."'";
    }

    $sql = 'SELECT u.vName, u.vLastName, d.vAvgRating, t.tStartDate ,t.tEndDate, t.tTripRequestDate,t.vCancelReason,t.vCancelComment,t.fDiscount,t.fWalletDebit,t.vTripPaymentMode,t.iFare,t.eType,d.iDriverId, t.tSaddress,t.vRideNo, t.tDaddress, t.fWalletDebit , d.vName AS name,c.vName AS comp,c.vCompany, d.vLastName AS lname,t.eCarType,t.iTripId,vt.vVehicleType,t.iActive
  FROM register_driver d
  RIGHT JOIN trips t ON d.iDriverId = t.iDriverId
  LEFT JOIN vehicle_type vt ON vt.iVehicleTypeId = t.iVehicleTypeId
  LEFT JOIN  register_user u ON t.iUserId = u.iUserId JOIN company c ON c.iCompanyId=d.iCompanyId
  WHERE 1=1'.$ssql.$ssl.'
  ORDER BY t.iTripId DESC';
    /*echo "<pre>";
    echo $sql;*/
    $db_trip = $obj->MySQLSelect($sql);

    // echo "<pre>";print_r($db_trip);die;
    $header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'].' Type'."\t";
    $header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'].' No.'."\t";
    $header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'].' Date'."\t";
    $header .= 'Company'."\t";
    $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']."\t";
    $header .= $langage_lbl_admin['LBL_RIDER']."\t";
    $header .= 'Fare'."\t";
    $header .= 'Discount'."\t";
    $header .= 'Wallet Payment'."\t";
    $header .= 'Cash Payment'."\t";
    $header .= 'Card Payment'."\t";

    for ($j = 0; $j < count($db_trip); ++$j) {
        $eType = $db_trip[$j]['eType'];
        $trip_type = ('Ride' === $eType) ? 'Ride' : 'Delivery';
        $data .= $trip_type."\t";
        $data .= $db_trip[$j]['vRideNo']."\t";
        $data .= ('0000-00-00 00:00:00' === $db_trip[$j]['tEndDate']) ? date('d-F-Y', strtotime($db_trip[$j]['tTripRequestDate'])) : date('d-F-Y', strtotime($db_trip[$j]['tEndDate']));
        $data .= "\t";
        $data .= clearCmpName($db_trip[$j]['vCompany'])."\t";
        $data .= clearName($db_trip[$j]['name'].' '.$db_trip[$j]['lname'])."\t";
        $data .= clearName($db_trip[$j]['vName'].' '.$db_trip[$j]['vLastName'])."\t";
        $ifareamount = $db_trip[$j]['iFare'] + $db_trip[$j]['fWalletDebit'];
        $data .= formateNumAsPerCurrency($ifareamount, '');
        $data .= "\t";
        $data .= formateNumAsPerCurrency($db_trip[$j]['fDiscount'], '')."\t";
        $data .= formateNumAsPerCurrency($db_trip[$j]['fWalletDebit'], '')."\t";
        $data .= ('Cash' === $db_trip[$j]['vTripPaymentMode']) ? formateNumAsPerCurrency($db_trip[$j]['iFare'], '') : '0';
        $data .= "\t";
        $data .= ('Card' === $db_trip[$j]['vTripPaymentMode']) ? formateNumAsPerCurrency($db_trip[$j]['iFare'], '') : '0';
        $data .= "\n";
    }
}
$data = str_replace("\r", '', $data);
// echo "<br>".$data; exit;
ob_clean();
header('Content-type: application/octet-stream');
header('Content-Disposition: attachment; filename=trip_detail_reports.xls');
header('Pragma: no-cache');
header('Expires: 0');
echo "{$header}\n{$data}";

exit;
