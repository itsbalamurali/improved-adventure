<?php
include_once('../common.php');
 
if (!$userObj->hasPermission('manage-cancellation-payment-report')) {
    $userObj->redirect();
}

$script = 'Cancellation_Payment_Report';

function cleanNumber($num) {
    return str_replace(',', '', $num);
}

//data for select fields
$sql = "SELECT iCompanyId,vCompany,vEmail FROM company WHERE eStatus != 'Deleted' AND eSystem = 'General' order by vCompany";
$db_company = $obj->MySQLSelect($sql);

$sql = "SELECT iDriverId,CONCAT(vName,' ',vLastName) AS driverName,vEmail FROM register_driver WHERE eStatus != 'Deleted' order by vName";
$db_drivers = $obj->MySQLSelect($sql);

$sql = "SELECT iUserId,CONCAT(vName,' ',vLastName) AS riderName,vEmail FROM register_user WHERE eStatus != 'Deleted' order by vName";
$db_rider = $obj->MySQLSelect($sql);

$ufxEnable = $MODULES_OBJ->isUberXFeatureAvailable() ? "Yes" : "No"; //add function to modules availibility
$rideEnable = $MODULES_OBJ->isRideFeatureAvailable() ? "Yes" : "No";
$deliveryEnable = $MODULES_OBJ->isDeliveryFeatureAvailable() ? "Yes" : "No";
$deliverallEnable = $MODULES_OBJ->isDeliverAllFeatureAvailable() ? "Yes" : "No";
//data for select fields
//Start Sorting
$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
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
        $ord = " ORDER BY trp.tTripRequestDate ASC";
    else
        $ord = " ORDER BY trp.tTripRequestDate DESC";
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

if ($sortby == 6) {
    if ($order == 0)
        $ord = " ORDER BY trp.eType ASC";
    else
        $ord = " ORDER BY trp.eType DESC";
}
//End Sorting
// Start Search Parameters
$ssql = '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$searchCompany = isset($_REQUEST['searchCompany']) ? $_REQUEST['searchCompany'] : '';
$searchDriver = isset($_REQUEST['searchDriver']) ? $_REQUEST['searchDriver'] : '';
$searchRider = isset($_REQUEST['searchRider']) ? $_REQUEST['searchRider'] : '';
$serachTripNo = isset($_REQUEST['serachTripNo']) ? $_REQUEST['serachTripNo'] : '';
$searchDriverPayment = isset($_REQUEST['searchDriverPayment']) ? $_REQUEST['searchDriverPayment'] : '';
$searchPaymentType = isset($_REQUEST['searchPaymentType']) ? $_REQUEST['searchPaymentType'] : '';
$startDate = isset($_REQUEST['startDate']) ? $_REQUEST['startDate'] : '';
$endDate = isset($_REQUEST['endDate']) ? $_REQUEST['endDate'] : '';
$eType = isset($_REQUEST['eType']) ? $_REQUEST['eType'] : '';
$searchPaymentByUser = isset($_REQUEST['searchPaymentByUser']) ? $_REQUEST['searchPaymentByUser'] : '';
/* if($searchPaymentByUser == ''){
  $searchPaymentByUser = 'Yes';
  } */
if ($action == 'search') {
    if ($startDate != '') {
        $ssql .= " AND Date(trp.tTripRequestDate) >='" . $startDate . "'";
    }
    if ($endDate != '') {
        $ssql .= " AND Date(trp.tTripRequestDate) <='" . $endDate . "'";
    }
    if ($serachTripNo != '') {
        $ssql .= " AND trp.vRideNo ='" . $serachTripNo . "'";
    }
    if ($searchCompany != '') {
        $ssql .= " AND rd.iCompanyId ='" . $searchCompany . "'";
    }
    if ($searchDriver != '') {
        $ssql .= " AND tr.iDriverId ='" . $searchDriver . "'";
    }
    if ($searchRider != '') {
        $ssql .= " AND tr.iUserId ='" . $searchRider . "'";
    }
    if ($searchDriverPayment != '') {
        $ssql .= " AND tr.ePaidToDriver ='" . $searchDriverPayment . "'";
    }
    if ($searchPaymentType != '') {
        $ssql .= " AND tr.vTripPaymentMode ='" . $searchPaymentType . "'";
    }
    if ($eType != '') {
        if ($eType == 'Ride') {
            $ssql .= " AND trp.eType ='" . $eType . "' AND trp.iRentalPackageId = 0 AND trp.eHailTrip = 'No' ";
        } elseif ($eType == 'RentalRide') {
            $ssql .= " AND trp.eType ='Ride' AND trp.iRentalPackageId > 0";
        } elseif ($eType == 'HailRide') {
            $ssql .= " AND trp.eType ='Ride' AND trp.eHailTrip = 'Yes'";
        } else {
            $ssql .= " AND trp.eType ='" . $eType . "' ";
        }
        //$ssql .= " AND trp.eType ='" . $eType . "'";
    }
}

if ($searchPaymentByUser != '') {
    $ssql .= " AND tr.ePaidByPassenger ='" . $searchPaymentByUser . "'";
}
$trp_ssql = "";

/* echo  SITE_TYPE;
exit; */
if (SITE_TYPE == 'Demo') {
    $trp_ssql = " And trp.tTripRequestDate > '" . WEEK_DATE . "'";
}
$org_sql = "SELECT iOrganizationId,vCompany AS driverName,vEmail FROM organization order by vCompany";
$db_organization = $obj->MySQLSelect($org_sql);
$orgNameArr = array();
for ($g = 0; $g < count($db_organization); $g++) {
    $orgNameArr[$db_organization[$g]['iOrganizationId']] = $db_organization[$g]['driverName'];
}
// echo "<pre>"; print_r($orgNameArr); exit;
//Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page

$sql = "SELECT tr.iTripId,tr.ePaidByWallet,tr.iTripOutstandId,tr.iDriverId,tr.iUserId, tr.fCommision, tr.fDriverPendingAmount,tr.ePaidByPassenger,tr.ePaidToDriver,tr.vTripPaymentMode,trp.eType,trp.vRideNo,trp.tTripRequestDate,trp.iRentalPackageId,c.vCompany,concat(rd.vName,' ',rd.vLastName) as drivername,concat(ru.vName,' ',ru.vLastName) as riderName FROM trip_outstanding_amount AS tr LEFT JOIN register_driver AS rd ON tr.iDriverId = rd.iDriverId LEFT JOIN register_user AS ru ON tr.iUserId = ru.iUserId LEFT JOIN trips AS trp ON trp.iTripId = tr.iTripId  LEFT JOIN company as c ON rd.iCompanyId = c.iCompanyId WHERE 1 = 1 AND tr.fDriverPendingAmount > 0 AND trp.eSystem = 'General' $ssql $trp_ssql ";

/* echo $sql;
exit; */

$totalData = $obj->MySQLSelect($sql);

$driver_payment = $tot_site_commission = 0.00;
foreach ($totalData as $dtps) {
    $driver_payment = $dtps['fDriverPendingAmount'];
    $site_commission = $dtps['fCommision'];
    $tot_site_commission = $tot_site_commission + cleanNumber($site_commission);
    $tot_driver_refund = $tot_driver_refund + cleanNumber($driver_payment);
}
$total_results = count($totalData);
$total_pages = ceil($total_results / $per_page); //total pages we going to have
$show_page = 1;

//-------------if page is setcheck------------------//
$start = 0;
$end = $per_page;
if (isset($_GET['page'])) {
    $show_page = $_GET['page'];             //it will telles the current page
    if ($show_page > 0 && $show_page <= $total_pages) {
        $start = ($show_page - 1) * $per_page;
        $end = $start + $per_page;
    }
}

// display pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
$tpages = $total_pages;
if ($page <= 0)
    $page = 1;
//Pagination End
$sql = "SELECT trp.iOrganizationId,tr.iTripId,tr.ePaidByWallet,tr.iTripOutstandId,tr.iDriverId,tr.iUserId, tr.fCommision, tr.fPendingAmount, tr.fDriverPendingAmount, tr.fWalletDebit,tr.ePaidByPassenger,tr.ePaidToDriver,tr.vTripPaymentMode,trp.iRentalPackageId,trp.eType,trp.vRideNo,trp.tTripRequestDate,tr.vTripAdjusmentId,c.vCompany,concat(rd.vName,' ',rd.vLastName) as drivername,concat(ru.vName,' ',ru.vLastName) as riderName, tr.iBiddingPostId, tr.vBidAdjusmentId FROM trip_outstanding_amount AS tr LEFT JOIN register_driver AS rd ON tr.iDriverId = rd.iDriverId LEFT JOIN register_user AS ru ON tr.iUserId = ru.iUserId LEFT JOIN trips AS trp ON trp.iTripId = tr.iTripId  LEFT JOIN company as c ON rd.iCompanyId = c.iCompanyId WHERE  1 = 1 AND tr.fDriverPendingAmount > 0 AND trp.eSystem = 'General' $ssql $trp_ssql $ord LIMIT $start, $per_page";
$db_trip = $obj->MySQLSelect($sql);

$endRecord = count($db_trip);
$var_filter = "";
foreach ($_REQUEST as $key => $val) {
    if ($key != "tpages" && $key != 'page')
        $var_filter .= "&$key=" . stripslashes($val);
}

$reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages . $var_filter;
$Today = Date('Y-m-d');
$tdate = date("d") - 1;
$mdate = date("d");
$Yesterday = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));

$curryearFDate = date("Y-m-d", mktime(0, 0, 0, '1', '1', date("Y")));
$curryearTDate = date("Y-m-d", mktime(0, 0, 0, "12", "31", date("Y")));
$prevyearFDate = date("Y-m-d", mktime(0, 0, 0, '1', '1', date("Y") - 1));
$prevyearTDate = date("Y-m-d", mktime(0, 0, 0, "12", "31", date("Y") - 1));

$currmonthFDate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - $tdate, date("Y")));
$currmonthTDate = date("Y-m-d", mktime(0, 0, 0, date("m") + 1, date("d") - $mdate, date("Y")));
$prevmonthFDate = date("Y-m-d", mktime(0, 0, 0, date("m") - 1, date("d") - $tdate, date("Y")));
$prevmonthTDate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - $mdate, date("Y")));

$monday = date('Y-m-d', strtotime('monday this week'));
$sunday = date('Y-m-d', strtotime('sunday this week'));
$Pmonday = date('Y-m-d', strtotime('monday this week -1 week'));
$Psunday = date('Y-m-d', strtotime('sunday this week -1 week'));

//Added By HJ On 26-08-2019 For Changed Word Of Card As Per Bug - 225 server 6736 Start
$cardText = "Card";
if ($SYSTEM_PAYMENT_FLOW == 'Method-2' || $SYSTEM_PAYMENT_FLOW == 'Method-3') {
    $cardText = "Wallet";
}
//Added By HJ On 26-08-2019 For Changed Word Of Card As Per Bug - 225 server 6736 End
?>
<!DOCTYPE html>
<html lang="en">
    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title><?= $SITE_NAME ?> | Cancellation Payment Report</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <?php include_once('global_files.php'); ?>
        <style>
            .setteled-class{
                background-color:#bddac5
            }
        </style>
    </head>
    <!-- END  HEAD-->

    <!-- BEGIN BODY-->
    <body class="padTop53 " >
        <!-- Main LOading -->
        <!-- MAIN WRAPPER -->
        <div id="wrap">
            <?php include_once('header.php'); ?>
            <?php include_once('left_menu.php'); ?>
            <!--PAGE CONTENT -->
            <div id="content">
                <div class="inner">
                    <div id="add-hide-show-div">
                        <div class="row">
                            <div class="col-lg-12">
                                <h2>Cancellation Payment Report</h2>
                            </div>
                        </div>
                        <hr />
                    </div>
                    <?php include('valid_msg.php'); ?>
                    <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post" >
                        <div class="Posted-date mytrip-page payment-report">
                            <input type="hidden" name="action" value="search" />
                            <h3>Search <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?>...</h3>
                            <span>
                                <a style="cursor:pointer" onClick="return todayDate('dp4', 'dp5');"><?= $langage_lbl_admin['LBL_MYTRIP_Today']; ?></a>
                                <a style="cursor:pointer" onClick="return yesterdayDate('dFDate', 'dTDate');"><?= $langage_lbl_admin['LBL_MYTRIP_Yesterday']; ?></a>
                                <a style="cursor:pointer" onClick="return currentweekDate('dFDate', 'dTDate');"><?= $langage_lbl_admin['LBL_MYTRIP_Current_Week']; ?></a>
                                <a style="cursor:pointer" onClick="return previousweekDate('dFDate', 'dTDate');"><?= $langage_lbl_admin['LBL_MYTRIP_Previous_Week']; ?></a>
                                <a style="cursor:pointer" onClick="return currentmonthDate('dFDate', 'dTDate');"><?= $langage_lbl_admin['LBL_MYTRIP_Current_Month']; ?></a>
                                <a style="cursor:pointer" onClick="return previousmonthDate('dFDate', 'dTDate');"><?= $langage_lbl_admin['LBL_MYTRIP_Previous Month']; ?></a>
                                <a style="cursor:pointer" onClick="return currentyearDate('dFDate', 'dTDate');"><?= $langage_lbl_admin['LBL_MYTRIP_Current_Year']; ?></a>
                                <a style="cursor:pointer" onClick="return previousyearDate('dFDate', 'dTDate');"><?= $langage_lbl_admin['LBL_MYTRIP_Previous_Year']; ?></a>
                            </span> 
                            <span>
                                <input type="text" id="dp4" name="startDate" placeholder="From Date" class="form-control" value="" readonly="" style="cursor:default; background-color: #fff" />
                                <input type="text" id="dp5" name="endDate" placeholder="To Date" class="form-control" value="" readonly="" style="cursor:default; background-color: #fff"/>
                                <div class="col-lg-3 select001">
                                    <select class="form-control filter-by-text" name = 'searchCompany' data-text="Select Company" id="searchCompany">
                                        <option value="">Select Company</option>
                                        <!-- <?php foreach ($db_company as $dbc) { ?>
                                            <option value="<?php echo $dbc['iCompanyId']; ?>" <?php
                                            if ($searchCompany == $dbc['iCompanyId']) {
                                                echo "selected";
                                            }
                                            ?>><?php echo clearCmpName($dbc['vCompany']); ?> - ( <?php echo clearEmail($dbc['vEmail']); ?> )</option>
                                                <?php } ?> -->
                                    </select>
                                </div>
                                <div class="col-lg-3 select001">
                                    <select class="form-control filter-by-text driver_container" name = 'searchDriver' data-text="Select <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?>" id="searchDriver">
                                        <option value="">Select <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?></option>
                                        <!-- <?php foreach ($db_drivers as $dbd) { ?>
                                            <option value="<?php echo $dbd['iDriverId']; ?>" <?php
                                            if ($searchDriver == $dbd['iDriverId']) {
                                                echo "selected";
                                            }
                                            ?>><?php echo clearName($dbd['driverName']); ?> - ( <?php echo clearEmail($dbd['vEmail']); ?> )</option>
                                                <?php } ?> -->
                                    </select>
                                </div>
                            </span>
                        </div>

                        <div class="row payment-report payment-report1 payment-report2">
                            <div class="col-lg-3">
                                <select class="form-control filter-by-text" name = 'searchRider' data-text="Select <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?>" id="searchRider">
                                    <option value="">Select <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?></option>
                                    <!-- <?php foreach ($db_rider as $dbr) { ?>
                                        <option value="<?php echo $dbr['iUserId']; ?>" <?php
                                        if ($searchRider == $dbr['iUserId']) {
                                            echo "selected";
                                        }
                                        ?>><?php echo clearName($dbr['riderName']); ?> - ( <?php echo clearEmail($dbr['vEmail']); ?> )</option>
                                            <?php } ?> -->
                                </select>
                            </div>
                            <div class="col-lg-3" style="display: none;">
                                <select class="form-control" name='searchPaymentType' data-text="Select <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?>">
                                    <option value="">Select Payment Type</option>
                                    <option value="Cash" <? if ($searchPaymentType == "Cash") { ?>selected <? } ?>>Cash</option>
                                    <option value="Card" <? if ($searchPaymentType == "Card") { ?>selected <? } ?>><?= $cardText; ?></option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <select class="form-control" name='searchDriverPayment' data-text="Select <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?>">
                                    <option value="">Select <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> Payment Status</option>
                                    <option value="Yes" <?php if ($searchDriverPayment == "Yes") { ?>selected <?php } ?>>Settelled</option>
                                    <option value="No" <?php if ($searchDriverPayment == "No") { ?>selected <?php } ?>>Unsettelled</option>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <input type="text" id="serachTripNo" name="serachTripNo" placeholder="<?php echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']; ?> Number" class="form-control search-trip001" value="<?php echo $serachTripNo; ?>"/>
                            </div>
                        </div>

                        <div class="row payment-report payment-report1 payment-report2" style="margin-top: 21px;">
                            <?php if ($APP_TYPE == 'Ride-Delivery-UberX' || $APP_TYPE == 'Ride-Delivery') { ?>
                                <div class="col-lg-2">
                                    <select class="form-control" name = 'eType' >
                                        <option value="">Service Type</option>
                                        <?php if ($rideEnable=="Yes") { ?>
                                            <option value="Ride" <?php
                                            if ($eType == "Ride") {
                                                echo "selected";
                                            }
                                            ?>><?= $langage_lbl_admin['LBL_RIDE_TXT_ADMIN_SEARCH']; ?> </option>
                                                <?php if ($ENABLE_HAIL_RIDES == "Yes" && $APP_TYPE != 'Delivery') { ?>
                                            <option value="HailRide" <?php
                                            if ($eType == "HailRide") {
                                                echo "selected";
                                            }
                                            ?>> Hail <?= $langage_lbl_admin['LBL_RIDE_TXT_ADMIN_SEARCH']; ?> </option>
                                                <?php } if (ENABLE_RENTAL_OPTION == 'Yes' && $APP_TYPE != 'Delivery') { ?>
                                            <option value="RentalRide" <?php
                                            if ($eType == "RentalRide") {
                                                echo "selected";
                                            }

                                            ?>>Taxi Rental </option>
                                                <?php } } if ($deliveryEnable == "Yes") { ?>
                                            <option value="Deliver" <?php
                                            if ($eType == "Deliver") {
                                                echo "selected";
                                            }
                                            ?>>Delivery</option>
                                                <?php if (ENABLE_MULTI_DELIVERY == "Yes") { ?>
                                            <option value="Multi-Delivery" <?php
                                            if ($eType == "Multi-Delivery") {
                                                echo "selected";
                                            }
                                            ?>>Multi-Delivery</option>
                                                <?php } } if (($APP_TYPE == "UberX" || $APP_TYPE == "Ride-Delivery-UberX") && $ufxEnable == "Yes") { ?>
                                            <option value="UberX" <?php
                                            if ($eType == "UberX") {
                                                echo "selected";
                                            }
                                            ?>>Other Services</option>
                                                <?php } if ($rideEnable == "Yes" && $PACKAGE_TYPE == "SHARK") { ?>
                                            <option value="Pool" <?php
                                            if ($eType == "Pool") {
                                                echo "selected";
                                            }
                                            ?>><?php echo  "Taxi " . $langage_lbl_admin['LBL_POOL']; ?> </option>
                                                <?php } if ($MODULES_OBJ->isAirFlightModuleAvailable()) { ?>
                                            <option value="Fly" <?php
                                            if ($eType == "Fly") {
                                                echo "selected";
                                            }
                                            ?>><?php echo $langage_lbl_admin['LBL_HEADER_RDU_FLY_RIDE']; ?> </option>
                                                <? } ?>
                                    </select>
                                </div>
                            <?php } ?>
                            <div class="col-lg-2">
                                <select class="form-control" name='searchPaymentByUser' data-text="Paid By <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?>">
                                    <option value="">Select User Payment Status</option>
                                    <option value="Yes" <? if ($searchPaymentByUser == "Yes") { ?>selected <? } ?>>Paid By <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?> - Yes</option>
                                    <option value="No" <? if ($searchPaymentByUser == "No") { ?>selected <? } ?>>Paid By <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?> -No</option>
                                </select>
                            </div>
                        </div>
                        <div class="tripBtns001"><b>
                                <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
                                <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href = 'cancellation_payment_report.php'"/>
                                <?php if (count($db_trip) > 0) { ?>
                                    <button type="button" onClick="reportExportTypes('cancellation_driver_payment')" class="export-btn001" >Export</button></b>
                            <?php } ?>
                        </div>
                    </form>
                    <div class="table-list">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <form name="_list_form" id="_list_form" class="_list_form" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                                        <input type="hidden" id="actionpayment" name="actionpayment" value="pay_driver">
                                        <input type="hidden" name="iTripId" id="iTripId" value="">
                                        <input type="hidden" name="ePayDriver" id="ePayDriver" value="">
                                        <table class="table table-bordered" id="dataTables-example123" >
                                            <thead>
                                                <tr>
                                                    <?php if ($APP_TYPE != 'UberX' && $APP_TYPE != 'Delivery') { ?>
                                                        <th><a href="javascript:void(0);" onClick="Redirect(6,<?php
                                                            if ($sortby == '6') {
                                                                echo $order;
                                                            } else {
                                                                ?>0<?php } ?>)"><?php echo $langage_lbl_admin['LBL_TRIP_TYPE_TXT_ADMIN']; ?> <?php
                                                                   if ($sortby == 6) {
                                                                       if ($order == 0) {
                                                                           ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                                    }
                                                                } else {
                                                                    ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
                                                            <?php } ?>
                                                    <th>Cancelled <?php echo $langage_lbl_admin['LBL_RIDE_NO_ADMIN']; ?> </th>
                                                    <th><a href="javascript:void(0);" onClick="Redirect(1,<?php
                                                        if ($sortby == '1') {
                                                            echo $order;
                                                        } else {
                                                            ?>0<?php } ?>)"><?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> <?php
                                                               if ($sortby == 1) {
                                                                   if ($order == 0) {
                                                                       ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                                }
                                                            } else {
                                                                ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
                                                    <th><a href="javascript:void(0);" onClick="Redirect(2,<?php
                                                        if ($sortby == '2') {
                                                            echo $order;
                                                        } else {
                                                            ?>0<?php } ?>)"><?php echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']; ?> <?php
                                                               if ($sortby == 2) {
                                                                   if ($order == 0) {
                                                                       ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                                }
                                                            } else {
                                                                ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                                    </th>

                                                    <th><a href="javascript:void(0);" onClick="Redirect(3,<?php
                                                        if ($sortby == '3') {
                                                            echo $order;
                                                        } else {
                                                            ?>0<?php } ?>)"><?= $langage_lbl_admin['LBL_TRIP_DATE_ADMIN']; ?> <?php
                                                               if ($sortby == 3) {
                                                                   if ($order == 0) {
                                                                       ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                                }
                                                            } else {
                                                                ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
                                                    <th width="8%">Total Cancellation Fees</th>
                                                    <th style="text-align:right;">Platform Fees</th>
                                                    <th width="10%" style="text-align:right;"><?= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> Pay Amount</th>
                                                    <th width="10%">User Payment Status</th>
                                                    <th width="10%"><?= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> Payment Status</th> 
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?
                                                $set_unsetarray = $tripIdArr = $biddingIdArr = array();
                                                if (count($db_trip) > 0) {
                                                    $db_bookingno = $obj->MySQLSelect("SELECT iTripId,vRideNo FROM trips");
                                                    for ($d = 0; $d < count($db_bookingno); $d++) {
                                                        $tripIdArr[$db_bookingno[$d]['iTripId']] = $db_bookingno[$d]['vRideNo'];
                                                    }
                                                    $db_bookingno_bidding = $obj->MySQLSelect("SELECT iBiddingPostId, vBiddingPostNo FROM bidding_post");
                                                    for ($d = 0; $d < count($db_bookingno_bidding); $d++) {
                                                        $biddingIdArr[$db_bookingno_bidding[$d]['iBiddingPostId']] = $db_bookingno_bidding[$d]['vBiddingPostNo'];
                                                    }
                                                    // echo "<pre>";print_r($db_trip);die;
                                                    for ($i = 0; $i < count($db_trip); $i++) {
                                                        $class_setteled = "";
                                                        if ($db_trip[$i]['ePaidToDriver'] == 'Yes' && $db_trip[$i]['ePaidByPassenger'] == 'Yes') {
                                                            $class_setteled = "setteled-class";
                                                        }
                                                        $set_unsetarray[] = $db_trip[$i]['ePaidToDriver'];
                                                        $eTypenew = $db_trip[$i]['eType'];
                                                        if ($eTypenew == 'Ride') {
                                                            $trip_type = 'Ride';
                                                        } else if ($eTypenew == 'UberX') {
                                                            $trip_type = 'Other Services';
                                                        } else {
                                                            $trip_type = 'Delivery';
                                                        }
                                                        // echo "<pre>";print_r($db_trip);die;
                                                        $orgName = "Paid in Trip #";
                                                        $vTripAdjusmentId = $vTripAdjusmentIdTxt = "";
                                                        $vBidAdjusmentId = $vBidAdjusmentIdTxt = "";
                                                        $adjustId = 0;
                                                        if (isset($orgNameArr[$db_trip[$i]['iOrganizationId']]) && $orgNameArr[$db_trip[$i]['iOrganizationId']] != "") {
                                                            $orgName = $db_trip[$i]['vTripPaymentMode'] . "(" . $orgNameArr[$db_trip[$i]['iOrganizationId']] . ")";
                                                            $adjustId = 1;
                                                        }
                                                        
                                                        //$q = "SELECT vRideNo FROM trips WHERE iTripId = '" . $db_trip[$i]['vTripAdjusmentId'] . "'";
                                                        //$db_bookingno = $obj->MySQLSelect($q);
                                                        if (isset($tripIdArr[$db_trip[$i]['vTripAdjusmentId']]) && $tripIdArr[$db_trip[$i]['vTripAdjusmentId']] != "" && $tripIdArr[$db_trip[$i]['vTripAdjusmentId']] != $db_trip[$i]['vRideNo']) {
                                                            $vTripAdjusmentId = $vTripAdjusmentIdTxt = $tripIdArr[$db_trip[$i]['vTripAdjusmentId']];
                                                        }
                                                        if (isset($biddingIdArr[$db_trip[$i]['iBiddingPostId']]) && $biddingIdArr[$db_trip[$i]['iBiddingPostId']] == $db_trip[$i]['vBidAdjusmentId']) {
                                                            $vBidAdjusmentIdTxt = $biddingIdArr[$db_trip[$i]['iBiddingPostId']];
                                                            $adjustId = 1;
                                                        }
                                                        if ($adjustId > 0) {
                                                            $vTripAdjusmentIdTxt = "";
                                                        }
                                                        ?>
                                                        <tr class="gradeA <?= $class_setteled ?>">
                                                            <?php if ($APP_TYPE != 'UberX' && $APP_TYPE != 'Delivery') { ?> 
                                                                <td align="left">
                                                                    <?
                                                                    if ($db_trip[$i]['eHailTrip'] == "Yes" && $db_trip[$i]['iRentalPackageId'] > 0) {
                                                                        echo "Rental " . $trip_type . "<br/> ( Hail )";
                                                                    } else if ($db_trip[$i]['iRentalPackageId'] > 0) {
                                                                        echo "Rental " . $trip_type;
                                                                    } else if ($db_trip[$i]['eHailTrip'] == "Yes") {
                                                                        echo "Hail " . $trip_type;
                                                                    } else {
                                                                        echo $trip_type;
                                                                    }
                                                                    ?>
                                                                </td>
                                                            <?php } ?>

                                                            <td> 
                                                                <?php if ($userObj->hasPermission('view-invoice')) { ?>
                                                                    <a href="invoice.php?iTripId=<?= $db_trip[$i]['iTripId'] ?>" target="_blank">
                                                                    <?php } ?>
                                                                    <?= $db_trip[$i]['vRideNo']; ?>

                                                                    <?php if ($userObj->hasPermission('view-invoice')) { ?>   
                                                                    </a>
                                                                <?php } ?>

                                                            </td>

                                                            <td>     <?php if ($userObj->hasPermission('view-providers')) { ?><a href="javascript:void(0);" onClick="show_driver_details('<?= $db_trip[$i]['iDriverId']; ?>')" style="text-decoration: underline;"><?php } ?><?= clearName($db_trip[$i]['drivername']); ?><?php if ($userObj->hasPermission('view-providers')) { ?></a> <?php } ?><!-- <?= clearName($db_trip[$i]['drivername']); ?> --></td>

                                                            <td> <?php if ($userObj->hasPermission('view-users')) { ?><a href="javascript:void(0);" onClick="show_rider_details('<?= $db_trip[$i]['iUserId']; ?>')" style="text-decoration: underline;"><?php } ?><?= clearName($db_trip[$i]['riderName']); ?><?php if ($userObj->hasPermission('view-users')) { ?></a><?php } ?><!-- <?= clearName($db_trip[$i]['riderName']); ?> -->
                                                           </td>


																						
                                                            <td><?= DateTime($db_trip[$i]['tTripRequestDate']); ?></td>
                                                            <td><?
                                                                $TotalCancelledprice = $db_trip[$i]['fPendingAmount'] > $db_trip[$i]['fWalletDebit'] ? $db_trip[$i]['fPendingAmount'] : $db_trip[$i]['fWalletDebit'];
                                                                echo formateNumAsPerCurrency($TotalCancelledprice,'');
                                                                ?></td>
                                                            <td align="right"><?php
                                                                if ($db_trip[$i]['fCommision'] != "" && $db_trip[$i]['fCommision'] != 0) {
                                                                    echo formateNumAsPerCurrency($db_trip[$i]['fCommision'],'');
                                                                } else {
                                                                    echo '-';
                                                                }
                                                                ?></td>
                                                            </td>
                                                            <td align="right">
                                                                <?php
                                                                if ($db_trip[$i]['fDriverPendingAmount'] != "" && $db_trip[$i]['fDriverPendingAmount'] != 0) {
                                                                    echo formateNumAsPerCurrency($db_trip[$i]['fDriverPendingAmount'],'');
                                                                } else {
                                                                    echo '-';
                                                                }
                                                                ?>
                                                            <td><? if (!empty($vTripAdjusmentId)) { ?> <?= $orgName; ?> 
                                                                    <?php if ($userObj->hasPermission('view-invoice')) { ?>
                                                                        <a href="invoice.php?iTripId=<?= $db_trip[$i]['vTripAdjusmentId'] ?>" target="_blank">
                                                                        <?php } ?>
                                                                        <?= $vTripAdjusmentIdTxt; ?>

                                                                        <?php if ($userObj->hasPermission('view-invoice')) { ?>   
                                                                        </a>
                                                                    <?php } ?>
                                                                    <?
                                                                } else if (!empty($vBidAdjusmentIdTxt)) { echo $orgName;?> 
                                                                    <?php if ($userObj->hasPermission('view-invoice')) { ?>
                                                                        <a href="invoice_bids.php?iBiddingPostId=<?= $db_trip[$i]['iBiddingPostId'] ?>" target="_blank">
                                                                        <?php } ?>
                                                                        <?= $vBidAdjusmentIdTxt; ?>
                                                                        <?php if ($userObj->hasPermission('view-invoice')) { ?>   
                                                                        </a>
                                                                    <?php } ?>
                                                            <? } else if ($db_trip[$i]['ePaidByPassenger'] == 'No') {
                                                                    echo"<b>Not Paid</b>";
                                                                } else if ($db_trip[$i]['ePaidByPassenger'] == 'Yes' && ($db_trip[$i]['ePaidByWallet'] == 'Yes' || $db_trip[$i]['fWalletDebit'] > 0)) {
                                                                    echo"<b>Paid By Wallet</b>";
                                                                } else {
                                                                    echo"Paid By ".$cardText;
                                                                }
                                                                ?></td>

                                                            <td> 
                                                                <?
                                                                if ($db_trip[$i]['ePaidToDriver'] == 'Yes' && $db_trip[$i]['ePaidByPassenger'] == 'Yes') {
                                                                    echo "Settelled";
                                                                } else {
                                                                    echo "Unsettelled";
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?
                                                                if ($db_trip[$i]['ePaidToDriver'] == 'No') {
                                                                    ?>
                                                                    <input class="validate[required]" type="checkbox" value="<?= $db_trip[$i]['iTripId'] ?>" id="iTripId_<?= $db_trip[$i]['iTripId'] ?>" name="iTripId[]">
                                                                    <?
                                                                }
                                                                ?>
                                                            </td>
                                                        </tr>
                                                    <? } ?>
                                                    <tr class="gradeA">
                                                        <td colspan="9" align="right">Total Platform Fees</td>
                                                        <td colspan="2"  align="right" colspan="2"><?= formateNumAsPerCurrency($tot_site_commission,''); ?></td>
                                                    </tr>

                                                    <tr class="gradeA">
                                                        <td colspan="9" align="right">Total <?= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> Payment</td>
                                                        <td colspan="2" align="right" colspan="2"><?= formateNumAsPerCurrency($tot_driver_refund,''); ?></td>
                                                    </tr>

                                                    <?php if (in_array("No", $set_unsetarray)) { ?>
                                                        <tr class="gradeA">
                                                            <td colspan="11" align="right"><div class="row payment-report-button">
                                                                    <span style="margin-right: 15px;">
                                                                        <a onClick="PaytodriverforCancel()" href="javascript:void(0);"><button class="btn btn-primary" type="button">Mark As Settelled</button></a>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?
                                                    }
                                                } else {
                                                    ?>
                                                    <tr class="gradeA">
                                                        <td colspan="11" style="text-align:center;">No Payment Details Found.</td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </form>
                                    <?php include('pagination_n.php'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
            <!--END PAGE CONTENT -->
        </div>
        <!--END MAIN WRAPPER -->

        <form name="pageForm" id="pageForm" action="action/cancellation_payment_report.php" method="post" >
            <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
            <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
            <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>" >
            <input type="hidden" name="order" id="order" value="<?php echo $order; ?>" >
            <input type="hidden" name="action" value="<?php echo $action; ?>" >
            <input type="hidden" name="searchCompany" value="<?php echo $searchCompany; ?>" >
            <input type="hidden" name="searchDriver" value="<?php echo $searchDriver; ?>" >
            <input type="hidden" name="searchRider" value="<?php echo $searchRider; ?>" >
            <input type="hidden" name="serachTripNo" value="<?php echo $serachTripNo; ?>" >
            <input type="hidden" name="searchPaymentType" value="<?php echo $searchPaymentType; ?>" >
            <input type="hidden" name="searchDriverPayment" value="<?php echo $searchDriverPayment; ?>" >
            <input type="hidden" name="startDate" value="<?php echo $startDate; ?>" >
            <input type="hidden" name="endDate" value="<?php echo $endDate; ?>" >
            <input type="hidden" name="vStatus" value="<?php echo $vStatus; ?>" >
            <input type="hidden" name="eType" value="<?php echo $eType; ?>" >
            <input type="hidden" name="searchPaymentByUser" value="<?php echo $searchPaymentByUser; ?>" >
            <input type="hidden" name="method" id="method" value="" >
        </form>

        <?php include_once('footer.php'); ?>

        <div class="modal fade " id="detail_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4><i style="margin:2px 5px 0 2px;"><img src="images/rider-icon.png" alt=""></i><?= $langage_lbl_admin['LBL_RIDER']; ?> Details<button type="button" class="close" data-dismiss="modal">x</button>
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
                    <i style="margin:2px 5px 0 2px;"><img src="images/icon/driver-icon.png" alt=""></i><?= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'] ?> Details<button type="button" class="close" data-dismiss="modal">x</button>
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

        <link rel="stylesheet" href="../assets/plugins/datepicker/css/datepicker.css" />
        <!-- <link rel="stylesheet" href="css/select2/select2.min.css" />
        <script src="js/plugins/select2.min.js"></script> -->
        <script src="../assets/plugins/datepicker/js/bootstrap-datepicker.js"></script>
        <? include_once('searchfunctions.php'); ?>
        <script>
                $('#dp4').datepicker()
                        .on('changeDate', function (ev) {
                            var endDate = $('#dp5').val();
                            if (ev.date.valueOf() < endDate.valueOf()) {
                                $('#alert').show().find('strong').text('The start date can not be greater then the end date');
                            } else {
                                $('#alert').hide();
                                var startDate = new Date(ev.date);
                                $('#startDate').text($('#dp4').data('date'));
                            }
                            $('#dp4').datepicker('hide');
                        });
                $('#dp5').datepicker()
                        .on('changeDate', function (ev) {
                            var startDate = $('#dp4').val();
                            if (ev.date.valueOf() < startDate.valueOf()) {
                                $('#alert').show().find('strong').text('The end date can not be less then the start date');
                            } else {
                                $('#alert').hide();
                                var endDate = new Date(ev.date);
                                $('#endDate').text($('#dp5').data('date'));
                            }
                            $('#dp5').datepicker('hide');
                        });

                $(document).ready(function () {
                    $("#dp5").click(function () {
                        $('#dp5').datepicker('show');
                        $('#dp4').datepicker('hide');
                    });

                    $("#dp4").click(function () {
                        $('#dp4').datepicker('show');
                        $('#dp5').datepicker('hide');
                    });

                    if ('<?= $startDate ?>' != '') {
                        $("#dp4").val('<?= $startDate ?>');
                        $("#dp4").datepicker('update', '<?= $startDate ?>');
                    }
                    if ('<?= $endDate ?>' != '') {
                        $("#dp5").datepicker('update', '<?= $endDate; ?>');
                        $("#dp5").val('<?= $endDate; ?>');
                    }
                });

                function setRideStatus(actionStatus) {
                    window.location.href = "trip.php?type=" + actionStatus;
                }
                function todayDate() {
                    $("#dp4").val('<?= $Today; ?>');
                    $("#dp5").val('<?= $Today; ?>');
                }
                function reset() {
                    location.reload();
                }
                function yesterdayDate()
                {
                    $("#dp4").val('<?= $Yesterday; ?>');
                    $("#dp4").datepicker('update', '<?= $Yesterday; ?>');
                    $("#dp5").datepicker('update', '<?= $Yesterday; ?>');
                    $("#dp4").change();
                    $("#dp5").change();
                    $("#dp5").val('<?= $Yesterday; ?>');
                }
                function currentweekDate(dt, df)
                {
                    $("#dp4").val('<?= $monday; ?>');
                    $("#dp4").datepicker('update', '<?= $monday; ?>');
                    $("#dp5").datepicker('update', '<?= $sunday; ?>');
                    $("#dp5").val('<?= $sunday; ?>');
                }
                function previousweekDate(dt, df)
                {
                    $("#dp4").val('<?= $Pmonday; ?>');
                    $("#dp4").datepicker('update', '<?= $Pmonday; ?>');
                    $("#dp5").datepicker('update', '<?= $Psunday; ?>');
                    $("#dp5").val('<?= $Psunday; ?>');
                }
                function currentmonthDate(dt, df)
                {
                    $("#dp4").val('<?= $currmonthFDate; ?>');
                    $("#dp4").datepicker('update', '<?= $currmonthFDate; ?>');
                    $("#dp5").datepicker('update', '<?= $currmonthTDate; ?>');
                    $("#dp5").val('<?= $currmonthTDate; ?>');
                }
                function previousmonthDate(dt, df)
                {
                    $("#dp4").val('<?= $prevmonthFDate; ?>');
                    $("#dp4").datepicker('update', '<?= $prevmonthFDate; ?>');
                    $("#dp5").datepicker('update', '<?= $prevmonthTDate; ?>');
                    $("#dp5").val('<?= $prevmonthTDate; ?>');
                }
                function currentyearDate(dt, df)
                {
                    $("#dp4").val('<?= $curryearFDate; ?>');
                    $("#dp4").datepicker('update', '<?= $curryearFDate; ?>');
                    $("#dp5").datepicker('update', '<?= $curryearTDate; ?>');
                    $("#dp5").val('<?= $curryearTDate; ?>');
                }
                function previousyearDate(dt, df)
                {
                    $("#dp4").val('<?= $prevyearFDate; ?>');
                    $("#dp4").datepicker('update', '<?= $prevyearFDate; ?>');
                    $("#dp5").datepicker('update', '<?= $prevyearTDate; ?>');
                    $("#dp5").val('<?= $prevyearTDate; ?>');
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
                /*$(function () {
                    $("select.filter-by-text").each(function () {
                        $(this).select2({
                            placeholder: $(this).attr('data-text'),
                            allowClear: true
                        }); //theme: 'classic'
                    });
                });
                $('#searchCompany').change(function () {
                    var company_id = $(this).val(); //get the current value's option
                    $.ajax({
                        type: 'POST',
                        url: 'ajax_find_driver_by_company.php',
                        data: {'company_id': company_id},
                        cache: false,
                        success: function (data) {
                            $(".driver_container").html(data);
                        }
                    });
                });*/
				
        </script>
    </body>
    <!-- END BODY-->
</html>