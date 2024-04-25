<?php
include_once '../common.php';

if (!$userObj->hasPermission('manage-cancelled-order-report')) {
    $userObj->redirect();
}

$script = 'Cancelled Order Report';

function cleanNumber($num)
{
    return str_replace(',', '', $num);
}

// data for select fields
$sql = "SELECT iCompanyId,vCompany,vEmail FROM company WHERE eStatus != 'Deleted' AND eSystem='DeliverAll' order by vCompany";
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
$searchServiceType = $_REQUEST['searchServiceType'] ?? '';
$startDate = $_REQUEST['startDate'] ?? '';
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
    if ('' !== $searchServiceType && !in_array($searchServiceType, ['Genie', 'Runner', 'Anywhere'], true)) {
        $ssql .= " AND sc.iServiceId ='".$searchServiceType."' AND o.eBuyAnyService ='No'";
    }
    if ('Genie' === $searchServiceType) {
        $ssql .= " AND o.eBuyAnyService ='Yes' AND o.eForPickDropGenie = 'No' ";
    }
    if ('Runner' === $searchServiceType) {
        $ssql .= " AND o.eBuyAnyService ='Yes' AND o.eForPickDropGenie = 'Yes' ";
    }
    if ('' !== $searchPaymentType) {
        $ssql .= " AND o.ePaymentOption ='".$searchPaymentType."'";
    }
}

$trp_ssql = '';
if (SITE_TYPE === 'Demo') {
    $trp_ssql = " And o.tOrderRequestDate > '".WEEK_DATE."'";
}
$ssql .= ' AND sc.iServiceId IN('.$enablesevicescategory.')';

// Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql1 = 'SELECT o.iOrderId,o.vOrderNo,sc.vServiceName_'.$default_lang." as vServiceName,o.tOrderRequestDate,o.fTotalGenerateFare,o.fRestaurantPayAmount,o.fRestaurantPaidAmount,o.fDriverPaidAmount,o.eAdminPaymentStatus,o.ePaymentOption,CONCAT(d.vName,' ',d.vLastName) AS driverName,o.iStatusCode,os.vStatus,t.fDeliveryCharge as driverearning,o.fCancellationCharge,oa.fCancellationFare,oa.ePaidByPassenger,oa.vOrderAdjusmentId FROM orders AS o LEFT JOIN order_status as os on os.iStatusCode=o.iStatusCode LEFT JOIN trips as t ON t.iOrderId=o.iOrderId LEFT JOIN trip_outstanding_amount as oa on oa.iOrderId=o.iOrderId LEFT JOIN register_driver d ON d.iDriverId = o.iDriverId LEFT JOIN service_categories as sc on sc.iServiceid = o.iServiceid WHERE 1=1 AND o.iStatusCode IN ('7','8') {$ssql} {$trp_ssql}";
$totalData = $obj->MySQLSelect($sql1);
$total_results = count($totalData);
// $total_results = $totalData[0]['Total'];
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
$sql = 'SELECT o.iOrderId,o.vOrderNo,o.fTipAmount,sc.vServiceName_'.$default_lang." as vServiceName,o.tOrderRequestDate,o.fTotalGenerateFare,o.fRestaurantPayAmount,o.fRestaurantPaidAmount,o.fDriverPaidAmount,o.eAdminPaymentStatus,o.ePaymentOption,CONCAT(d.vName,' ',d.vLastName) AS driverName,o.iStatusCode,os.vStatus,t.fDeliveryCharge as driverearning,o.fCancellationCharge,oa.fCancellationFare,oa.ePaidByPassenger,oa.vOrderAdjusmentId,o.fDeliveryChargeCancelled,o.eBuyAnyService,o.eForPickDropGenie FROM orders AS o LEFT JOIN order_status as os on os.iStatusCode=o.iStatusCode LEFT JOIN trips as t ON t.iOrderId=o.iOrderId LEFT JOIN trip_outstanding_amount as oa on oa.iOrderId=o.iOrderId LEFT JOIN register_driver d ON d.iDriverId = o.iDriverId LEFT JOIN service_categories as sc on sc.iServiceid = o.iServiceid WHERE 1=1 AND o.iStatusCode IN ('7','8') {$ssql} {$trp_ssql} {$ord}";
$db_trip = $obj->MySQLSelect($sql);
// print_R($db_trip);die;
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

$settlementorderid = $_REQUEST['settlementorderid'] ?? '';
if ('settelled' === $action && '' !== $settlementorderid) {
    $fDriverPaidAmount = $_REQUEST['fDeliveryCharge'] ?? '';
    $fRestaurantPaidAmount = $_REQUEST['fRestaurantPayAmount'] ?? '';

    $query = "UPDATE orders SET fRestaurantPaidAmount = '".$fRestaurantPaidAmount."' ,fDriverPaidAmount='".$fDriverPaidAmount."',eAdminPaymentStatus = 'Settled',eRestaurantPaymentStatus = 'Settled' WHERE iOrderId = '".$settlementorderid."'";
    $obj->sql_query($query);

    $tQuery = "UPDATE trips SET eDriverPaymentStatus = 'Settled' WHERE iOrderId = '".$settlementorderid."'";
    $obj->sql_query($tQuery);
    echo "<script>location.href='cancelled_report.php'</script>";
}
$catdata = serviceCategories;
$allservice_cat_data = json_decode($catdata, true);
// Added By HJ On 26-08-2019 For Changed Word Of Card As Per Bug - 225 server 6736 Start
$cardText = 'Card';
if ('Method-2' === $SYSTEM_PAYMENT_FLOW || 'Method-3' === $SYSTEM_PAYMENT_FLOW) {
    $cardText = 'Wallet';
}
// Added By HJ On 26-08-2019 For Changed Word Of Card As Per Bug - 225 server 6736 End

$header = $data = '';
if (count($allservice_cat_data) > 1) {
    $header .= 'Service type'."\t";
}
$header .= $langage_lbl_admin['LBL_RIDE_NO_ADMIN_DL'].'#'."\t";
$header .= $langage_lbl_admin['LBL_RIDE_TXT_ADMIN_DL'].' Date'."\t";
$header .= 'PayOut To '.$langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']."\t";
$header .= 'Payout to '.$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']."\t";
$header .= 'Cancellation Charges For '.$langage_lbl_admin['LBL_RIDER']."\t";
$header .= $langage_lbl_admin['LBL_RIDE_TXT_ADMIN_DL'].' Status'."\t";
$header .= 'Payment method'."\t";
$header .= 'Action'."\t";

if (count($db_trip) > 0) {
    for ($i = 0; $i < count($db_trip); ++$i) {
        $payment_to_driver = GetDriverPayment($db_trip[$i]['iOrderId']);

        if (count($allservice_cat_data) > 1) {
            if ('Yes' === $db_trip[$i]['eBuyAnyService']) {
                $db_trip[$i]['vServiceName'] = $langage_lbl_admin['LBL_OTHER_DELIVERY'];
                if ('Yes' === $db_trip[$i]['eForPickDropGenie']) {
                    $db_trip[$i]['vServiceName'] = $langage_lbl_admin['LBL_RUNNER'];
                }
            }
            $data .= $db_trip[$i]['vServiceName']."\t";
        }

        $data .= $db_trip[$i]['vOrderNo']."\t";

        $data .= DateTime($db_trip[$i]['tOrderRequestDate'])."\t";

        if ('No' === $db_trip[$i]['eBuyAnyService']) {
            $data .= 'Actual Amount : '.formateNumAsPerCurrency($db_trip[$i]['fRestaurantPayAmount'], '').' ,You Paid : '.formateNumAsPerCurrency($db_trip[$i]['fRestaurantPaidAmount'], '');
        } else {
            $data .= '';
        }
        $data .= "\t";

        if (0 === $payment_to_driver) {
            $data .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' not Assign';
        } else {
            $data .= 'Actual Amount : '.formateNumAsPerCurrency($db_trip[$i]['driverearning'] + $db_trip[$i]['fTipAmount'], '').' , You Paid : '.formateNumAsPerCurrency($db_trip[$i]['fDriverPaidAmount'], '');
        }
        $data .= "\t";

        if ('No' === $db_trip[$i]['eBuyAnyService']) {
            $data .= formateNumAsPerCurrency($db_trip[$i]['fCancellationCharge'], '');
        } else {
            $data .= formateNumAsPerCurrency($db_trip[$i]['fDeliveryChargeCancelled'], '');
        }
        if ('Cash' === $db_trip[$i]['ePaymentOption'] && 'Yes' === $db_trip[$i]['ePaidByPassenger']) {
            $data .= ' ( Paid In Order No# : '.$db_trip[$i]['vOrderAdjusmentId'].' )';
        } elseif ('Cash' === $db_trip[$i]['ePaymentOption']) {
            $data .= ' ( Outstanding )';
        } elseif ('Card' === $db_trip[$i]['ePaymentOption']) {
            $data .= ' ( Paid )';
        }
        $data .= "\t";

        $data .= $db_trip[$i]['vStatus'];
        $data .= "\t";

        $ePaymentOption = $db_trip[$i]['ePaymentOption'];
        if ('Card' === $db_trip[$i]['ePaymentOption']) {
            $ePaymentOption = $cardText;
        }

        $data .= $ePaymentOption;
        $data .= "\t";

        if ('Settled' === $db_trip[$i]['eAdminPaymentStatus']) {
            $data .= 'Setteled';
        } else {
            $data .= 'Unsetteled';
        }

        $data .= "\n";
    }
}

$data = str_replace("\r", '', $data);

ob_clean();

header('Content-type: application/octet-stream');

header('Content-Disposition: attachment; filename=payment_reports.xls');

header('Pragma: no-cache');

header('Expires: 0');

echo "{$header}\n{$data}";

exit;
// added by SP on 28-06-2019 end
?>

