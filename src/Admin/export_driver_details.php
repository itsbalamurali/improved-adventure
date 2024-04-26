<?php



include_once '../common.php';

$tbl_name = 'trips';

// echo "<pre>"; print_r($_REQUEST); exit;
// -----------------------------------------------
if (('' !== $_REQUEST['startDate']) && ('' !== $_REQUEST['endDate'])) {
    $startDate = $_REQUEST['startDate'];
    $endDate = $_REQUEST['endDate'];
}

//  	$startDate = Date('Y-m-d');
//  	$endDate = Date('Y-m-d');

  $tdate = date('d') - 1;
$mdate = date('d');
if (isset($startDate, $endDate)) {
    $startdate = $startDate;
    $enddate = $endDate;

    $order_status = explode(',', $status_array);
}

//		$startdate = date("Y-m-d",mktime(0,0,0,date("m"),date("d")-$tdate,date("Y")));
//		$enddate = date("Y-m-d",mktime(0,0,0,date("m")+1,date("d")-$mdate,date("Y")));

// -----------------------------------------------------
$action = $_REQUEST['action'];
$fdate = $_REQUEST['startDate'];
$tdate = $_REQUEST['endDate'];
$iCompanyId = $_REQUEST['iCompanyId'];
$iDriverId = $_REQUEST['iDriverId'];
$iUserId = $_REQUEST['iUserId'];
$vTripPaymentMode = $_REQUEST['vTripPaymentMode'];
$eDriverPaymentStatus = $_REQUEST['eDriverPaymentStatus'];

$ssql = '';

if ('' !== $action) {
    if ('' !== $startDate) {
        $ssql .= " AND Date(tEndDate) >='".$startDate."'";
    }
    if ('' !== $endDate) {
        $ssql .= " AND Date(tEndDate) <='".$endDate."'";
    }
    if ('' !== $iCompanyId) {
        if ('' !== $iDriverId) {
            $ssql .= " AND tr.iDriverId = '".$iDriverId."' AND rd.iCompanyId = '".$iCompanyId."'";
        } else {
            $sql = "select iDriverId from register_driver WHERE iCompanyId = '".$iCompanyId."' ";
            $db_driver2 = $obj->MySQLSelect($sql);
            if (count($db_driver2) > 0) {
                for ($i = 0; $i < count($db_driver2); ++$i) {
                    $id .= $db_driver2[$i]['iDriverId'].',';
                }
                $id = rtrim($id, ',');
                $ssql .= " AND tr.iDriverId IN({$id})";
            } else {
                $ssql .= " AND tr.iDriverId = ''";
            }
        }
    } else {
        if ('' !== $iDriverId) {
            $ssql .= " AND tr.iDriverId = '".$iDriverId."'";
        }
    }

    if ('' !== $iUserId) {
        $ssql .= " AND tr.iUserId = '".$iUserId."'";
    }

    if ('' !== $vTripPaymentMode) {
        if ('Mbirr' === $vTripPaymentMode) {
            $ssql .= " AND tr.vTripPaymentMode = 'Cash' AND eMBirr = 'Yes'";
        } else {
            $ssql .= " AND tr.vTripPaymentMode = '".$vTripPaymentMode."'";
        }
    }
    if ('' !== $eDriverPaymentStatus) {
        $ssql .= " AND tr.eDriverPaymentStatus = '".$eDriverPaymentStatus."'";
    }
}

// $sql_admin = "SELECT * from trips WHERE 1=1 ".$ssql." ORDER BY iTripId DESC";
$sql_admin = 'SELECT tr.*,c.vCompany FROM trips AS tr LEFT JOIN register_driver AS rd ON tr.iDriverId = rd.iDriverId LEFT JOIN company as c ON rd.iCompanyId = c.iCompanyId  WHERE 1 '.$ssql.' ORDER BY tr.iTripId DESC';
$db_trip = $obj->MySQLSelect($sql_admin);
// echo "<pre>";print_r($db_trip); exit;
$tot_fare = 0.00;
$tot_site_commission = 0.00;
$tot_promo_discount = 0.00;
$tot_driver_refund = 0.00;

$header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'].' No'."\t";
$header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Name'."\t";
$header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'].' Date'."\t";
$header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'].' Address'."\t";
$header .= 'Total Fare'."\t";
$header .= 'Site Commission'."\t";
$header .= 'Promo Code Discount'."\t";
$header .= 'Wallet Debit'."\t";
$header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' pay Amount'."\t";
$header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'].' Status'."\t";
$header .= 'Payment method'."\t";
$header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Payment Status';

$tot_fare = 0.00;
$tot_site_commission = 0.00;
$tot_promo_discount = 0.00;
$tot_driver_refund = 0.00;
$tot_wallentPayment = 0.00;

for ($j = 0; $j < count($db_trip); ++$j) {
    $driver_payment = 0.00;
    $sq = "select concat(vName,' ',vLastName) as drivername from register_driver where iDriverId='".$db_trip[$j]['iDriverId']."'";
    $name = $obj->MySQLSelect($sq);
    $db_trip[$j]['drivername'] = clearName($name[0]['drivername']);
    $totalfare = $db_trip[$j]['fTripGenerateFare'];
    $site_commission = $db_trip[$j]['fCommision'];
    $promocodediscount = $db_trip[$j]['fDiscount'];
    $wallentPayment = $db_trip[$j]['fWalletDebit'];
    $driver_payment = $totalfare + $promocodediscount - $site_commission;

    $tot_fare += $totalfare;
    $tot_site_commission += $site_commission;
    $tot_promo_discount += $promocodediscount;
    $tot_wallentPayment += $wallentPayment;
    $tot_driver_refund += $driver_payment;
    if ('Yes' === $db_trip[$j]['eMBirr']) {
        $paymentmode = 'M-birr';
    } else {
        $paymentmode = $db_trip[$j]['vTripPaymentMode'];
    }

    $data .= $db_trip[$j]['vRideNo']."\t";
    $data .= $db_trip[$j]['drivername']."\t";
    $data .= date('d-m-Y', strtotime($db_trip[$j]['tTripRequestDate']))."\t";
    $data .= $db_trip[$j]['tSaddress'].' -> '.$db_trip[$j]['tDaddress']."\t";
    // $data .= trip_currency($db_trip[$j]['iFare'],$db_trip[$j]['fRatioDriver'],$db_trip[$j]['vCurrencyDriver'])."\t";
    // $data .= trip_currency($db_trip[$j]['fCommision'],$db_trip[$j]['fRatioDriver'],$db_trip[$j]['vCurrencyDriver'])."\t";
    // $data .= trip_currency($db_trip[$j]['fDiscount'],$db_trip[$j]['fRatioDriver'],$db_trip[$j]['vCurrencyDriver'])."\t";
    $data .= ('' !== $db_trip[$j]['fTripGenerateFare'] && 0 !== $db_trip[$j]['fTripGenerateFare']) ? formateNumAsPerCurrency($db_trip[$j]['fTripGenerateFare'], '')."\t" : "- \t";
    $data .= ('' !== $db_trip[$j]['fCommision'] && 0 !== $db_trip[$j]['fCommision']) ? formateNumAsPerCurrency($db_trip[$j]['fCommision'], '')."\t" : "- \t";
    $data .= ('' !== $db_trip[$j]['fDiscount'] && 0 !== $db_trip[$j]['fDiscount']) ? formateNumAsPerCurrency($db_trip[$j]['fDiscount'], '')."\t" : "- \t";
    $data .= ('' !== $db_trip[$j]['fWalletDebit'] && 0 !== $db_trip[$j]['fWalletDebit']) ? formateNumAsPerCurrency($db_trip[$j]['fWalletDebit'], '')."\t" : "- \t";
    $data .= ('' !== $driver_payment && 0 !== $driver_payment) ? formateNumAsPerCurrency($driver_payment, '')."\t" : "- \t";
    $data .= $db_trip[$j]['iActive']."\t";
    $data .= $paymentmode."\t";
    $data .= $db_trip[$j]['eDriverPaymentStatus']."\n";
}
$data .= "\t\t\t\t\t\t\t\t\tTotal Fare\t".formateNumAsPerCurrency($tot_fare, '')."\n";
$data .= "\t\t\t\t\t\t\t\t\tTotal Site Commission\t".formateNumAsPerCurrency($tot_site_commission, '')."\n";
$data .= "\t\t\t\t\t\t\t\t\tTotal Promo Discount\t".formateNumAsPerCurrency($tot_promo_discount, '')."\n";
$data .= "\t\t\t\t\t\t\t\t\tTotal Wallet Debit\t".formateNumAsPerCurrency($tot_wallentPayment, '')."\n";
$data .= "\t\t\t\t\t\t\t\t\tTotal Driver Payment\t".formateNumAsPerCurrency($tot_driver_refund, '')."\n";
$data = str_replace("\r", '', $data);
// echo "<br>".$data; exit;
ob_clean();
header('Content-type: application/octet-stream');
header('Content-Disposition: attachment; filename=payment_reports.xls');
header('Pragma: no-cache');
header('Expires: 0');
echo "{$header}\n{$data}";

exit;
