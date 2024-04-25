<?php
include_once '../common.php';
$tbl_name = 'trips';
if (!$userObj->hasPermission('manage-store-payment')) {
    $userObj->redirect();
}
$script = 'Restaurant Payment Report';
$eSystem = " AND eSystem = 'DeliverAll'";
$action = $_REQUEST['action1'] ?? '';
$searchCompany = $_REQUEST['searchCompany'] ?? '';
$searchServiceType = $_REQUEST['searchServiceType'] ?? '';
$startDate = $_REQUEST['startDate'] ?? '';
$endDate = $_REQUEST['endDate'] ?? '';
// data for select fields
$ssqlsc = ' AND iServiceId IN('.$enablesevicescategory.')';
$sql = "select iCompanyId,vCompany,vEmail from company WHERE eStatus != 'Deleted' {$eSystem} {$ssqlsc} order by vCompany";
$db_company = $obj->MySQLSelect($sql);
// Start Sorting
$sortby = $_REQUEST['sortby'] ?? 0;
$order = $_REQUEST['order'] ?? '';
$ord = ' ORDER BY c.iCompanyId DESC';
if (2 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY c.vCompany ASC';
    } else {
        $ord = ' ORDER BY c.vCompany DESC';
    }
}
if (3 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY c.vAcctHolderName ASC';
    } else {
        $ord = ' ORDER BY c.vAcctHolderName DESC';
    }
}
if (4 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY c.vBankName ASC';
    } else {
        $ord = ' ORDER BY c.vBankName DESC';
    }
}
// End Sorting
// Start Search Parameters
$ssql = $ssql1 = $ssql2 = '';
if ('search' === $action) {
    if ('' !== $startDate) {
        $ssql .= " AND Date(o.tOrderRequestDate) >='".$startDate."'";
        $ssql2 .= " AND Date(o.tOrderRequestDate) >='".$startDate."'";
    }
    if ('' !== $endDate) {
        $ssql .= " AND Date(o.tOrderRequestDate) <='".$endDate."'";
        $ssql2 .= " AND Date(o.tOrderRequestDate) <='".$endDate."'";
    }
    if ('' !== $searchCompany) {
        $ssql1 .= " AND c.iCompanyId ='".$searchCompany."'";
    }
    if ('' !== $searchServiceType) {
        $ssql .= " AND sc.iServiceId ='".$searchServiceType."'";
        $ssql2 .= " AND o.iServiceId ='".$searchServiceType."'";
    }
}
// Select dates
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
$ssql .= ' AND sc.iServiceId IN('.$enablesevicescategory.')';
$ssql2 .= ' AND o.iServiceId IN('.$enablesevicescategory.')';
$per_page = $DISPLAY_RECORD_NUMBER;
$sql = 'SELECT c.iCompanyId,o.eRestaurantPaymentStatus,sc.vServiceName_'.$default_lang." as vServiceName,c.vCompany,c.vPaymentEmail,c.vAcctHolderName,c.vAcctNo,c.vBankName,c.vBankLocation,c.vSwiftCode,CONCAT(c.vCode,' ',c.vPhone) as resturant_phone FROM company as c LEFT JOIN orders as o on o.iCompanyId= c.iCompanyId LEFT JOIN service_categories as sc on sc.iServiceid = o.iServiceid WHERE o.eRestaurantPaymentStatus='Unsettled' {$ssql} {$ssql1} GROUP BY c.iCompanyId";
$totalData = $obj->MySQLSelect($sql);
// $total_results = $totalData[0]['Total'];
$total_results = count($totalData);
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
// echo "<PRE>"; print_R($_REQUEST);
$sql = 'SELECT c.iCompanyId,o.eRestaurantPaymentStatus,sc.vServiceName_'.$default_lang." as vServiceName,c.vCompany,c.vPaymentEmail,c.vAcctHolderName,c.vAcctNo,c.vBankName,c.vBankLocation,c.vSwiftCode,CONCAT(c.vCode,' ',c.vPhone) as resturant_phone FROM company as c LEFT JOIN orders as o on o.iCompanyId= c.iCompanyId LEFT JOIN service_categories as sc on sc.iServiceid = o.iServiceid WHERE o.eRestaurantPaymentStatus='Unsettled' AND o.eBuyAnyService = 'No' {$ssql} {$ssql1} GROUP BY c.iCompanyId {$ord}";
// exit;
$db_payment = $obj->MySQLSelect($sql);
$endRecord = count($db_payment);
$var_filter = '';
foreach ($_REQUEST as $key => $val) {
    if ('tpages' !== $key && 'page' !== $key) {
        $var_filter .= "&{$key}=".stripslashes($val);
    }
}
$reload = $_SERVER['PHP_SELF'].'?tpages='.$tpages.$var_filter;
$tripCompanyIdArr = array_column($db_payment, 'iCompanyId');
$storeTransferAmtArr = getTransforAmountbyRestaurant($tripCompanyIdArr, $ssql2);
// echo "<pre>";print_r($db_payment);die;
$storeExpectedAmtArr = CalculateStoreExpectedAmount($tripCompanyIdArr, $ssql2);
for ($i = 0; $i < count($db_payment); ++$i) {
    $iCompanyId = $db_payment[$i]['iCompanyId'];
    $transferAmount = $expectedAmount = 0;
    if (isset($storeTransferAmtArr[$iCompanyId])) {
        $transferAmount = $storeTransferAmtArr[$iCompanyId];
    }
    if (isset($storeExpectedAmtArr[$iCompanyId])) {
        $expectedAmount = $storeExpectedAmtArr[$iCompanyId];
    }
    $db_payment[$i]['transferAmount'] = $transferAmount;
    $db_payment[$i]['expectedAmount'] = $expectedAmount;
    // $db_payment[$i]['transferAmount'] = getTransforAmountbyRestaurant($db_payment[$i]['iCompanyId'],$ssql2);
    // $db_payment[$i]['expectedAmount'] = CalculateStoreExpectedAmount($db_payment[$i]['iCompanyId'],$ssql2);
}
$catdata = serviceCategories;
$allservice_cat_data = json_decode($catdata, true);
$header = $data = '';
if (count($allservice_cat_data) > 1) {
    $header .= 'Service type'."\t";
}
$header .= $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN'].' Name'."\t";
$header .= $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN'].' Account Name'."\t";
$header .= 'Bank Name'."\t";
$header .= 'Account Number'."\t";
$header .= 'Sort Code'."\t";
$header .= 'Final Amount Pay to '.$langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']."\t";
$header .= $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN'].' Payment Status';
if (count($db_payment) > 0) {
    for ($i = 0; $i < count($db_payment); ++$i) {
        if (count($allservice_cat_data) > 1) {
            $data .= $db_payment[$i]['vServiceName']."\t";
        }
        if ('' !== $db_payment[$i]['resturant_phone']) {
            $data .= clearName($db_payment[$i]['vCompany']).',';
            $data .= 'Phone: +'.clearPhone($db_payment[$i]['resturant_phone'])."\t";
        } else {
            $data .= clearName($db_payment[$i]['vCompany'])."\t";
        }
        $data .= ('' !== $db_payment[$i]['vAcctHolderName']) ? clearName($db_payment[$i]['vAcctHolderName']) : '---';
        $data .= "\t";
        $data .= ('' !== $db_payment[$i]['vBankName']) ? clearName($db_payment[$i]['vBankName']) : '---';
        $data .= "\t";
        $data .= ('' !== $db_payment[$i]['vAcctNo']) ? clearName($db_payment[$i]['vAcctNo']) : '---';
        $data .= "\t";
        $data .= ('' !== $db_payment[$i]['vSwiftCode']) ? clearName($db_payment[$i]['vSwiftCode']) : '---';
        $data .= "\t";
        // $data .= ($db_payment[$i]['expectedAmount'] > 0) ? formateNumAsPerCurrency($db_payment[$i]['expectedAmount'],'') : '---';
        // $data .= "\t";
        $data .= ($db_payment[$i]['transferAmount'] > 0) ? formateNumAsPerCurrency($db_payment[$i]['transferAmount'], '') : '---';
        $data .= "\t";
        $data .= $db_payment[$i]['eRestaurantPaymentStatus'];
        $data .= "\n";
    }
}
$data = str_replace("\r", '', $data);
ob_clean();
// header("Content-type: application/octet-stream");
header('Content-Type: application/xls');

header('Content-Disposition: attachment; filename=payment_reports.xls');
header('Pragma: no-cache');
header('Expires: 0');
echo "{$header}\n{$data}";

exit;
// added by SP on 28-06-2019 end
?>

