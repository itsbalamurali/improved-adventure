<?php
include_once '../common.php';

if (!$userObj->hasPermission('manage-admin-earning')) {
    $userObj->redirect();
}

$script = 'Admin Payment_Report';
$eSystem = " AND eSystem = 'DeliverAll'";

function cleanNumber($num)
{
    return str_replace(',', '', $num);
}

// data for select fields
$ssqlsc = ' AND iServiceId IN('.$enablesevicescategory.')';
$sql = "select iCompanyId,vCompany,vEmail from company WHERE eStatus != 'Deleted' {$eSystem} {$ssqlsc} order by vCompany";
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
    if ('' !== $searchServiceType && !in_array($searchServiceType, ['Genie', 'Runner', 'Anywhere'], true)) {
        $ssql .= " AND sc.iServiceId ='".$searchServiceType."' AND o.eBuyAnyService ='No'";
    }
    if ('Genie' === $searchServiceType) {
        $ssql .= " AND o.eBuyAnyService ='Yes' AND o.eForPickDropGenie = 'No' ";
    }
    if ('Runner' === $searchServiceType) {
        $ssql .= " AND o.eBuyAnyService ='Yes' AND o.eForPickDropGenie = 'Yes' ";
    }
    if ('' !== $searchRestaurantPayment) {
        $ssql .= " AND o.eRestaurantPaymentStatus ='".$searchRestaurantPayment."'";
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
$sql1 = 'SELECT o.iOrderId,o.vOrderNo,o.iCompanyId,sc.vServiceName_'.$default_lang." as vServiceName,o.iDriverId,o.iUserId,o.tOrderRequestDate,o.fTotalGenerateFare,o.fDeliveryCharge,o.fOffersDiscount,o.fTax,o.fCommision,o.eRestaurantPaymentStatus,o.ePaymentOption,o.fOutStandingAmount,o.iStatusCode,os.vStatus,c.vCompany,concat(rd.vName,' ',rd.vLastName) as drivername,concat(ru.vName,' ',ru.vLastName) as riderName,CONCAT(ru.vPhoneCode,' ',ru.vPhone)  as user_phone,CONCAT(rd.vCode,' ',rd.vPhone) as driver_phone,CONCAT(c.vCode,' ',c.vPhone) as resturant_phone, o.fTipAmount, o.eBuyAnyService, o.ePaymentOption,o.eForPickDropGenie FROM orders AS o LEFT JOIN register_driver AS rd ON o.iDriverId = rd.iDriverId LEFT JOIN register_user AS ru ON o.iUserId = ru.iUserId LEFT JOIN company as c ON o.iCompanyId = c.iCompanyId LEFT JOIN order_status as os on os.iStatusCode=o.iStatusCode LEFT JOIN service_categories as sc on sc.iServiceid = o.iServiceid WHERE 1=1 AND IF(o.eTakeaway = 'Yes' && os.iStatusCode = 6, os.eTakeaway='Yes', os.eTakeaway != 'Yes') AND o.iStatusCode = '6' {$ssql} {$trp_ssql}";

$totalData = $obj->MySQLSelect($sql1);

$orderIdArr = $companyIdArr = $userIdArr = $driverIdArr = $tripDataArr = $companyDataArr = $userDataArr = $driverDataArr = [];
for ($g = 0; $g < count($totalData); ++$g) {
    $orderIdArr[] = $totalData[$g]['iOrderId'];
    $companyIdArr[] = $totalData[$g]['iCompanyId'];
    $userIdArr[] = $totalData[$g]['iUserId'];
    $driverIdArr[] = $totalData[$g]['iDriverId'];
}
// echo "<pre>";print_r($driverIdArr);die;
if (count($orderIdArr) > 0) {
    $orderIdArr = array_unique($orderIdArr, SORT_REGULAR);
    $implodeOrderIds = implode(',', $orderIdArr);
    $tripData = $obj->MySQLSelect("SELECT fDeliveryCharge,iOrderId FROM trips WHERE iOrderId IN ({$implodeOrderIds})");
    for ($t = 0; $t < count($tripData); ++$t) {
        $tripDataArr[$tripData[$t]['iOrderId']] = $tripData[$t];
    }
}
// echo "<pre>";print_r($tripDataArr);die;

// Added By HJ On 21-09-2020 For Optimize loop Query Start
$OrderItemBuyArr = [];
$order_buy_anything = $obj->MySQLSelect('SELECT eConfirm,fItemPrice,iOrderId FROM order_items_buy_anything');
for ($b = 0; $b < count($order_buy_anything); ++$b) {
    $OrderItemBuyArr[$order_buy_anything[$b]['iOrderId']][] = $order_buy_anything[$b];
}
// echo "<pre>";print_r($OrderItemBuyArr);die;
// echo "<pre>";print_r($driverDataArr);die;
$tot_order_amount = 0.00;
$tot_site_commission = 0.00;
$tot_delivery_charges = 0.00;
$tot_offer_discount = 0.00;
$tot_admin_payment = 0.00;
$tot_outstanding_amount = 0.00;
$tot_admin_tax = 0.00;
foreach ($totalData as $dtps) {
    $orderId = $dtps['iOrderId'];
    $totalfare = $dtps['fTotalGenerateFare'];
    $fOffersDiscount = $dtps['fOffersDiscount'];
    $fDeliveryCharge = $dtps['fDeliveryCharge'];
    $site_commission = $dtps['fCommision'];
    $fOutStandingAmount = $dtps['fOutStandingAmount'];
    $fTipAmount = $dtps['fTipAmount'];
    $fTax = $dtps['fTax'];

    $restaurant_payment = $totalfare - cleanNumber($site_commission) - cleanNumber($fOffersDiscount) - cleanNumber($fDeliveryCharge) - cleanNumber($fOutStandingAmount) - cleanNumber($fTax);
    $tripDelCharge = 0;
    if (isset($tripDataArr[$orderId])) {
        $tripDelCharge = $tripDataArr[$orderId]['fDeliveryCharge'];
    }

    $subtotal = 0;
    if ('Yes' === $dtps['eBuyAnyService'] && 'Card' === $dtps['ePaymentOption']) {
        // $order_buy_anything = $obj->MySQLSelect("SELECT * FROM order_items_buy_anything WHERE iOrderId = '" . $orderId . "'");
        $order_buy_anything = [];
        if (isset($OrderItemBuyArr[$orderId])) {
            $order_buy_anything = $OrderItemBuyArr[$orderId];
        }

        if (count($order_buy_anything) > 0) {
            foreach ($order_buy_anything as $oItem) {
                if ('Yes' === $oItem['eConfirm']) {
                    $subtotal += $oItem['fItemPrice'];
                }
            }
        }
    }

    $siteearnig = cleanNumber($site_commission) + cleanNumber($fDeliveryCharge) + cleanNumber($fOutStandingAmount) + cleanNumber($fTipAmount);
    $driverearning = $tripDelCharge + $dtps['fTipAmount'] + $subtotal;
    $adminearning = $siteearnig - cleanNumber($driverearning);

    if ('Yes' === $dtps['eBuyAnyService']) {
        $adminearning = cleanNumber($site_commission) + cleanNumber($fOutStandingAmount);
    }

    $tot_order_amount += cleanNumber($totalfare);
    $tot_offer_discount += cleanNumber($fOffersDiscount);
    $tot_delivery_charges += cleanNumber($fDeliveryCharge);
    $tot_site_commission += cleanNumber($site_commission);
    $tot_outstanding_amount += cleanNumber($fOutStandingAmount);
    $tot_admin_payment += cleanNumber($adminearning);
    $tot_admin_tax += cleanNumber($fTax);
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
$sql = 'SELECT o.iOrderId,o.vOrderNo,o.iCompanyId,sc.vServiceName_'.$default_lang." as vServiceName,o.iDriverId,o.iUserId,o.tOrderRequestDate,o.fTotalGenerateFare,o.fDeliveryCharge,o.fOffersDiscount,o.fCommision,o.fTax,o.eRestaurantPaymentStatus,o.ePaymentOption,o.fOutStandingAmount,o.iStatusCode,os.vStatus,c.vCompany,concat(rd.vName,' ',rd.vLastName) as drivername,concat(ru.vName,' ',ru.vLastName) as riderName,CONCAT(ru.vPhoneCode,' ',ru.vPhone)  as user_phone,CONCAT(rd.vCode,' ',rd.vPhone) as driver_phone,CONCAT(c.vCode,' ',c.vPhone) as resturant_phone, o.fTipAmount, o.eBuyAnyService, o.eForPickDropGenie FROM orders AS o LEFT JOIN register_driver AS rd ON o.iDriverId = rd.iDriverId LEFT JOIN register_user AS ru ON o.iUserId = ru.iUserId LEFT JOIN company as c ON o.iCompanyId = c.iCompanyId LEFT JOIN order_status as os on os.iStatusCode=o.iStatusCode LEFT JOIN service_categories as sc on sc.iServiceid = o.iServiceid WHERE 1=1 AND IF(o.eTakeaway = 'Yes' && os.iStatusCode = 6, os.eTakeaway='Yes', os.eTakeaway != 'Yes') AND o.iStatusCode = '6' {$ssql} {$trp_ssql} {$ord}";
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
// Added By HJ On 26-08-2019 For Changed Word Of Card As Per Bug - 225 server 6736 End

$header = $data = '';
if (count($allservice_cat_data) > 1) {
    $header .= 'Service type'."\t";
}
$header .= $langage_lbl_admin['LBL_RIDE_NO_ADMIN_DL'].'#'."\t";
$header .= $langage_lbl_admin['LBL_RIDE_TXT_ADMIN_DL'].' Date'."\t";
$header .= 'A=Total Order Amount'."\t";
$header .= 'B=Site Commision'."\t";
$header .= 'C=Delivery Charges'."\t";
$header .= 'D=OutStanding Amount'."\t";
$header .= 'E=Delivery Tip'."\t";
$header .= 'F=Tax'."\t";
$header .= 'G=Driver Pay Amount'."\t";
$header .= 'H=Admin Earning Amount'."\t";
$header .= $langage_lbl_admin['LBL_RIDE_TXT_ADMIN_DL'].' Status'."\t";
$header .= 'Payment method';

if (count($db_trip) > 0) {
    for ($i = 0; $i < count($db_trip); ++$i) {
        $iOrderId = $db_trip[$i]['iOrderId'];
        $totalfare = $db_trip[$i]['fTotalGenerateFare'];
        $site_commission = $db_trip[$i]['fCommision'];
        $fOffersDiscount = $db_trip[$i]['fOffersDiscount'];
        $fDeliveryCharge = $db_trip[$i]['fDeliveryCharge'];
        $fOutStandingAmount = $db_trip[$i]['fOutStandingAmount'];
        $fTipAmount = $db_trip[$i]['fTipAmount'];
        $fTax = $db_trip[$i]['fTax'];

        $restaurant_payment = $totalfare - cleanNumber($site_commission) - cleanNumber($fOffersDiscount) - cleanNumber($fDeliveryCharge) - cleanNumber($fOutStandingAmount);

        $set_unsetarray[] = $db_trip[$i]['eRestaurantPaymentStatus'];

        if (!empty($db_trip[$i]['drivername'])) {
            $drivername = $db_trip[$i]['drivername'];
        } else {
            $drivername = '--';
        }

        $subtotal = 0;
        if ('Yes' === $db_trip[$i]['eBuyAnyService'] && 'Card' === $db_trip[$i]['ePaymentOption']) {
            // $order_buy_anything = $obj->MySQLSelect("SELECT * FROM order_items_buy_anything WHERE iOrderId = '" . $db_trip[$i]['iOrderId'] . "'");
            $order_buy_anything = [];
            if (isset($OrderItemBuyArr[$iOrderId])) {
                $order_buy_anything = $OrderItemBuyArr[$iOrderId];
            }

            if (count($order_buy_anything) > 0) {
                foreach ($order_buy_anything as $oItem) {
                    if ('Yes' === $oItem['eConfirm']) {
                        $subtotal += $oItem['fItemPrice'];
                    }
                }
            }
        }
        $db_trip[$i]['item_subtotal'] = $subtotal;
        $driverearningnew = 0;
        if (isset($tripDataArr[$iOrderId])) {
            $driverearningnew = $tripDataArr[$iOrderId]['fDeliveryCharge'];
        }

        $siteearnig = cleanNumber($site_commission) + cleanNumber($fDeliveryCharge) + cleanNumber($fOutStandingAmount) + cleanNumber($fTipAmount) - cleanNumber($fTax);
        $driverearning = $driverearningnew + cleanNumber($fTipAmount) + $subtotal;
        $adminearning = $siteearnig - cleanNumber($driverearning);
        if ('Yes' === $db_trip[$i]['eBuyAnyService']) {
            $db_trip[$i]['vServiceName'] = $langage_lbl_admin['LBL_OTHER_DELIVERY'];
            if ('Yes' === $db_trip[$i]['eForPickDropGenie']) {
                $db_trip[$i]['vServiceName'] = $langage_lbl_admin['LBL_RUNNER'];
            }
            $adminearning = cleanNumber($site_commission) + cleanNumber($fOutStandingAmount);
        }

        if (count($allservice_cat_data) > 1) {
            $data .= $db_trip[$i]['vServiceName']."\t";
        }

        $data .= $db_trip[$i]['vOrderNo']."\t";

        $data .= DateTime($db_trip[$i]['tOrderRequestDate'])."\t";

        $data .= ('' !== $db_trip[$i]['fTotalGenerateFare'] && 0 !== $db_trip[$i]['fTotalGenerateFare']) ? formateNumAsPerCurrency($db_trip[$i]['fTotalGenerateFare'], '') : '---';
        $data .= "\t";

        $data .= ('' !== $db_trip[$i]['fCommision'] && 0 !== $db_trip[$i]['fCommision']) ? formateNumAsPerCurrency($db_trip[$i]['fCommision'], '') : '---';
        $data .= "\t";

        $data .= ('' !== $db_trip[$i]['fDeliveryCharge'] && 0 !== $db_trip[$i]['fDeliveryCharge']) ? formateNumAsPerCurrency($db_trip[$i]['fDeliveryCharge'], '') : '---';
        $data .= "\t";

        // $data .= ($db_trip[$i]['fOffersDiscount'] != "" && $db_trip[$i]['fOffersDiscount'] != 0) ? formateNumAsPerCurrency($db_trip[$i]['fOffersDiscount'], '') : '---'."\t";
        // $data .= "\t";

        $data .= ('' !== $db_trip[$i]['fOutStandingAmount'] && 0 !== $db_trip[$i]['fOutStandingAmount']) ? formateNumAsPerCurrency($db_trip[$i]['fOutStandingAmount'], '') : '---';
        $data .= "\t";
        $data .= ('' !== $db_trip[$i]['fTipAmount'] && 0 !== $db_trip[$i]['fTipAmount']) ? formateNumAsPerCurrency($db_trip[$i]['fTipAmount'], '') : '---';
        $data .= "\t";
        $data .= ('' !== $db_trip[$i]['fTax'] && 0 !== $db_trip[$i]['fTax']) ? formateNumAsPerCurrency($db_trip[$i]['fTax'], '') : '---';
        $data .= "\t";

        // $data .= ($restaurant_payment != "" && $restaurant_payment != 0) ? formateNumAsPerCurrency($restaurant_payment, '') : '---'."\t";
        // $data .= "\t";

        $data .= ('' !== $driverearningnew && 0 !== $driverearningnew) ? formateNumAsPerCurrency($driverearning, '') : '---';
        $data .= "\t";

        $data .= ('' !== $adminearning && 0 !== $adminearning) ? formateNumAsPerCurrency($adminearning, '') : '---';
        $data .= "\t";

        $data .= $db_trip[$i]['vStatus'];
        $data .= "\t";

        $ePaymentOption = $db_trip[$i]['ePaymentOption'];
        if ('Card' === $db_trip[$i]['ePaymentOption']) {
            $ePaymentOption = $cardText;
        }

        $data .= $ePaymentOption;

        $data .= "\n";
    }
}

$data .= "\t\t\t\t\t\t\t\t\t";
$data .= 'Total Fare: '."\t".formateNumAsPerCurrency($tot_order_amount, '')."\n";
$data .= "\t\t\t\t\t\t\t\t\t";
$data .= 'Total Site Commision: '."\t".formateNumAsPerCurrency($tot_site_commission, '')."\n";
$data .= "\t\t\t\t\t\t\t\t\t";
$data .= 'Total Tax: '."\t".formateNumAsPerCurrency($tot_admin_tax, '')."\n";
$data .= "\t\t\t\t\t\t\t\t\t";
$data .= 'Total Delivery Charges: '."\t".formateNumAsPerCurrency($tot_delivery_charges, '')."\n";
$data .= "\t\t\t\t\t\t\t\t\t";
$data .= 'Total Outstanding Amount: '."\t".formateNumAsPerCurrency($tot_outstanding_amount, '')."\n";
$data .= "\t\t\t\t\t\t\t\t\t";
$data .= 'Total Admin Earning  Payment: '."\t".formateNumAsPerCurrency($tot_admin_payment, '')."\n";
$data .= 'Admin Earning Amount: '."\nH = B + C + D + E - F - G"."\n\n";
if ($MODULES_OBJ->isEnableAnywhereDeliveryFeature()) {
    $data .= 'Admin Earning Amount for: '.$langage_lbl_admin['LBL_OTHER_DELIVERY'].'/'.$langage_lbl_admin['LBL_RUNNER']." Feature:\nH = B + D";
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

