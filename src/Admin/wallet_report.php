<?php
include_once '../common.php';

if (!$userObj->hasPermission('manage-user-wallet-report')) {
    $userObj->redirect();
}

$script = 'Wallet Report';
$sess_iAdminUserId = $_SESSION['sess_iAdminUserId'] ?? '';

// data for select fields
$rdr_ssql = '';
if (SITE_TYPE === 'Demo') {
    $rdr_ssql = " And tRegistrationDate > '".WEEK_DATE."'";
}

/*$sql = "select iDriverId,CONCAT(vName,' ',vLastName) AS driverName,vEmail,vPhone,vCode from register_driver WHERE eStatus != 'Deleted' $rdr_ssql order by vName";
$db_drivers = $obj->MySQLSelect($sql);

$sql = "select iUserId,CONCAT(vName,' ',vLastName) AS riderName,vEmail,vPhone,vPhonecode from register_user WHERE eStatus != 'Deleted' AND (vEmail != '' OR vPhone != '')  AND eHail= 'No' $rdr_ssql order by vName";
$db_rider = $obj->MySQLSelect($sql);*/
// data for select fields

$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

// setRole($abc,$url);
$script = 'Wallet Report';

$action = ($_REQUEST['action'] ?? '');
$ssql = '';

/* $sql = "select * from register_driver WHERE 1 = 1 $rdr_ssql";
  $db_driver_disp = $obj->MySQLSelect($sql);

  $sql = "select * from register_user WHERE 1 = 1 $rdr_ssql";
  $db_rider_dis = $obj->MySQLSelect($sql); */

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
        $ssql .= " AND u.iUserId = '".$iDriverId."'";
    }
    if ('' !== $iUserId) {
        $ssql .= " AND u.iUserId = '".$iUserId."'";
    }

    if ('' !== $startDate) {
        $ssql .= " AND Date(u.dDate) >='".$startDate."'";
    }
    if ('' !== $endDate) {
        $ssql .= " AND Date(u.dDate) <='".$endDate."'";
    }

    if ($eUserType) {
        $ssql .= " AND u.eUserType = '".$eUserType."'";
    }
    if ('' !== $eFor) {
        $ssql .= " AND u.eFor = '".$eFor."'";
    }

    if ('' !== $Payment_type) {
        $ssql .= " AND u.eType = '".$Payment_type."'";
    }
}

if (isset($_POST['action']) && 'paymentmember' === $_POST['action']) {
    $eUserType = $_REQUEST['eUserType'];
    if ('Driver' === $eUserType) {
        $iUserId = $_REQUEST['iDriverId'];
    } else {
        $iUserId = $_REQUEST['iUserId'];
    }

    $iBalance = $_REQUEST['iBalance'];
    $eFor = $_REQUEST['eFor'];
    $eType = $_REQUEST['eType'];
    $iTripId = 0;
    $tDescription = '#LBL_AMOUNT_DEBIT#';
    // $tDescription = 'Amount debited';
    // $tDescription = ' Amount ' . $_REQUEST['iBalance'] . ' debited from your account for withdrawal request';
    $ePaymentStatus = 'Unsettelled';
    $dDate = date('Y-m-d H:i:s');

    $WALLET_OBJ->PerformWalletTransaction($iUserId, $eUserType, $iBalance, $eType, $iTripId, $eFor, $tDescription, $ePaymentStatus, $dDate);
    header('Location:wallet_report.php?'.$_SERVER['QUERY_STRING']);

    exit;
}

if (isset($_POST['action']) && 'addmoney' === $_POST['action']) {
    $eUserType = $_REQUEST['eUserType'];
    if ('Driver' === $eUserType) {
        $iUserId = $_REQUEST['iDriverId'];
    } else {
        $iUserId = $_REQUEST['iUserId'];
    }
    $iBalance = $_REQUEST['iBalance'];
    $eFor = $_REQUEST['eFor'];
    $eType = $_REQUEST['eType'];
    $iTripId = 0;
    $tDescription = '#LBL_AMOUNT_CREDIT_BY_ADMIN#';
    // $tDescription = 'Amount credited';
    // $tDescription = ' Amount ' . $_REQUEST['iBalance'] . ' credited into your account from administrator';
    $ePaymentStatus = 'Unsettelled';
    $dDate = date('Y-m-d H:i:s');
    if ('' !== $sess_iAdminUserId) {
        $WALLET_OBJ->PerformWalletTransaction($iUserId, $eUserType, $iBalance, $eType, $iTripId, $eFor, $tDescription, $ePaymentStatus, $dDate);
    }
    header('Location:wallet_report.php?'.$_SERVER['QUERY_STRING']);

    exit;
}

$sortby = $_REQUEST['sortby'] ?? 0;
$order = $_REQUEST['order'] ?? '';
$ord = ' ORDER BY dDate ASC'; // iUserWalletId DESC

$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = 'SELECT COUNT(iUserWalletId) AS Total From user_wallet where 1=1 '.$ssql;
$totalData = $obj->MySQLSelect($sql);
$total_results = $totalData[0]['Total'];
$total_pages = ceil($total_results / $per_page); // total pages we going to have
$show_page = 1;
// -------------if page is setcheck------------------//
$start = 0;
$end = $per_page;
if (isset($_GET['page'])) {
    $show_page = $_GET['page'];             // it will telles the current page
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
$db_result = [];
if ('search' === $action) {
    $sql = "SELECT u.iUserWalletId,u.iUserId,u.eUserType,u.iBalance,u.eType,u.iTripId,u.eFor,u.tDescription,u.ePaymentStatus,u.dDate,p.tPaymentUserID From user_wallet as u LEFT JOIN payments as p ON p.iUserWalletId = u.iUserWalletId WHERE 1=1 {$ssql} {$ord}"; // LIMIT $start,$per_page
    $db_result = $obj->MySQLSelect($sql);
    $endRecord = count($db_result);
}

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
?>
<!DOCTYPE html>
<html lang="en">
    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title><?php echo $SITE_NAME; ?> | Wallet Report</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
<?php include_once 'global_files.php'; ?>
        <style type="text/css">
            .hideoptions { display:none;}
            .nimot-class .add-ibalance {
                width: 100%;
            }

            .modal-body {
                padding-right: 20px;
            }

            #iLimitmsg {
                margin-top: 5px;
            }

            .inner {
                padding-bottom: 25px
            }
            @media (min-width: 576px) {
                .modal-sm {
                    max-width: 300px;
                }
            }

        </style>
    </head>
    <!-- END  HEAD-->

    <!-- BEGIN BODY-->
    <body class="padTop53 " >
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
                                <h2>Wallet Report</h2>
                                <!--<input type="button" id="" value="ADD A DRIVER" class="add-btn">-->
                            </div>
                        </div>
                        <hr />
                    </div>
<?php include 'valid_msg.php'; ?>
                    <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post" >
                        <div class="Posted-date mytrip-page payment-report">
                            <input type="hidden" name="action" value="search" />
                            <h3>Search by Date...</h3>
                            <span>
                                <a style="cursor:pointer" onClick="return todayDate('dp4', 'dp5');"><?php echo $langage_lbl['LBL_MYTRIP_Today']; ?></a>
                                <a style="cursor:pointer" onClick="return yesterdayDate('dFDate', 'dTDate');"><?php echo $langage_lbl['LBL_MYTRIP_Yesterday']; ?></a>
                                <a style="cursor:pointer" onClick="return currentweekDate('dFDate', 'dTDate');"><?php echo $langage_lbl['LBL_MYTRIP_Current_Week']; ?></a>
                                <a style="cursor:pointer" onClick="return previousweekDate('dFDate', 'dTDate');"><?php echo $langage_lbl['LBL_MYTRIP_Previous_Week']; ?></a>
                                <a style="cursor:pointer" onClick="return currentmonthDate('dFDate', 'dTDate');"><?php echo $langage_lbl['LBL_MYTRIP_Current_Month']; ?></a>
                                <a style="cursor:pointer" onClick="return previousmonthDate('dFDate', 'dTDate');"><?php echo $langage_lbl['LBL_MYTRIP_Previous Month']; ?></a>
                                <a style="cursor:pointer" onClick="return currentyearDate('dFDate', 'dTDate');"><?php echo $langage_lbl['LBL_MYTRIP_Current_Year']; ?></a>
                                <a style="cursor:pointer" onClick="return previousyearDate('dFDate', 'dTDate');"><?php echo $langage_lbl['LBL_MYTRIP_Previous_Year']; ?></a>
                            </span>
                            <span>
                                <input type="text" id="dp4" name="startDate" placeholder="From Date" class="form-control" value="" readonly=""style="cursor:default; background-color: #fff" />
                                <input type="text" id="dp5" name="endDate" placeholder="To Date" class="form-control" value="" readonly="" style="cursor:default; background-color: #fff"/>
                                <div class="col-lg-3 select001">
                                    <select class="form-control" name='eUserType' id="eUserType" data-text="Select Rider" onChange="return show_hide_user_type(this.value);">
                                        <option value="">Search By User type</option>
                                        <option value="Driver" <?php if ('Driver' === $eUserType) { ?>selected <?php } ?> > <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> </option>
                                        <option value="Rider" <?php if ('Rider' === $eUserType) { ?>selected <?php } ?>> <?php echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']; ?> </option>
                                    </select>
                                </div>
                                <div class="col-lg-3 select001 showhide-box001" id="sec_driver">
                                    <select class="form-control filter-by-text" name = 'iDriverId' id="searchDriver" data-text="Select <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?>">
                                        <option value="">Select <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?></option>

                                    </select>
                                </div>
                                <div class="col-lg-3 select001 showhide-box001" id="sec_rider">
                                    <select class="form-control filter-by-text" name = 'iUserId' id="searchRider" data-text="Select <?php echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']; ?>">
                                        <option value="">Select <?php echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']; ?></option>
                                    </select>
                                </div>
                            </span>
                        </div>

                        <div class="row payment-report payment-report1">
                            <div class="col-lg-3">
                                <select class="form-control" name='searchPaymentType' data-text="Select Rider">
                                    <option value="">Search By Payment type</option>
                                    <option value="Credit" <?php if ('Credit' === $Payment_type) { ?>selected <?php } ?> >Credit</option>
                                    <option value="Debit" <?php if ('Debit' === $Payment_type) { ?>selected <?php } ?> >Debit</option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <select class="form-control" name='searchBalanceType' data-text="Select Rider">
                                    <option value="">Search By Balance Type</option>
                                    <option value="Deposit" <?php if ('Deposit' === $eFor) { ?>selected <?php } ?>>Deposit</option>
                                    <?php if (ONLYDELIVERALL === 'Yes') { ?>
                                    <option value="Booking" <?php if ('Booking' === $eFor) { ?>selected <?php } ?>>Order</option>
                                    <?php } elseif (DELIVERALL === 'Yes') { ?>
                                        <option value="Booking" <?php if ('Booking' === $eFor) { ?>selected <?php } ?>>Booking/Order</option>
                                    <?php } else { ?>
                                    <option value="Booking" <?php if ('Booking' === $eFor) { ?>selected <?php } ?>>Booking</option>
                                    <?php } ?>
                                    <option value="Refund" <?php if ('Refund' === $eFor) { ?>selected <?php } ?>>Refund</option>
                                    <option value="Withdrawl" <?php if ('Withdrawl' === $eFor) { ?>selected <?php } ?>>Withdrawal</option>
                                    <option value="Charges" <?php if ('Charges' === $eFor) { ?>selected <?php } ?>>Charges</option>
                                    <?php if ('Yes' === $REFERRAL_SCHEME_ENABLE) { ?>
                                    <option value="Referrer"<?php if ('Referrer' === $eFor) { ?>selected <?php } ?>>Referral</option>
                                    <?php } ?>
                                    <!-- added by SP For Gopay -->
                                    <?php if ($MODULES_OBJ->isGojekGopayModuleAvailable()) { ?>
                                    <option value="Transfer"<?php if ('Transfer' === $eFor) { ?>selected <?php } ?>>Transfer</option>
                                    <?php } ?>
                                    <?php if ($MODULES_OBJ->isDriverSubscriptionModuleAvailable()) { ?>
                                    <option value="Subscription"<?php if ('Subscription' === $eFor) { ?>selected <?php } ?>>Subscription</option> <!-- added by HJ For Subscription -->
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="tripBtns001"><b>
                                <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
                                <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href = 'wallet_report.php'"/>
                                <?php if (count($db_result) > 0) { ?>
                                    <button type="button" onClick="reportExportTypes('wallet_report')" class="export-btn001" >Export</button></b>
                            <?php } ?>
                            </b>
                        </div>
                    </form>
                    <div class="table-list">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive" <?php if ('search' !== $action) { ?>style="display:none;"<?php } else { ?> <?php } ?>>
                                    <form class="_list_form" id="_list_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <table class="table table-striped table-bordered table-hover" >
                                            <thead>
                                                <tr>
                                                    <th width="8%" style="text-align:center;">Transaction Date</th>
                                                    <th width="15%">Description</th>
                                                    <th width="8%">Transaction ID</th>
                                                    <th width="6%" style="text-align:center;"><?php echo $langage_lbl_admin['LBL_TRIP_NO_ADMIN']; ?></th>
                                                    <th width="10%" style="text-align:center;" >Amount</th>
                                                    <th width="4%" style="text-align:center;">Purpose</th>
                                                    <th width="4%" style="text-align:center;">Balance Type</th>
                                                    <th width="10%" style="text-align:center;">Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if (count($db_result) > 0) {
                                                    $prevbalance = 0;
                                                    $db_result_trips = $obj->MySQLSelect('SELECT vRideNo,iTripId FROM `trips` WHERE 1=1');
                                                    $tripIdAssArr = [];
                                                    for ($f = 0; $f < count($db_result_trips); ++$f) {
                                                        $tripIdAssArr[$db_result_trips[$f]['iTripId']] = $db_result_trips[$f]['vRideNo'];
                                                    }
                                                    // echo "<pre>";print_r($tripIdAssArr);die;
                                                    for ($i = 0; $i < count($db_result); ++$i) {
                                                        if ('Credit' === $db_result[$i]['eType']) {
                                                            $db_result[$i]['currentbal'] = $prevbalance + $db_result[$i]['iBalance'];
                                                        } else {
                                                            $db_result[$i]['currentbal'] = $prevbalance - $db_result[$i]['iBalance'];
                                                        }

                                                        $prevbalance = $db_result[$i]['currentbal'];
                                                        $ride_number = '--';
                                                        if (isset($tripIdAssArr[$db_result[$i]['iTripId']])) {
                                                            $ride_number = $tripIdAssArr[$db_result[$i]['iTripId']];
                                                        }
                                                        ?>
                                                        <tr class="gradeA">
                                                            <td align="center"><?php echo DateTime($db_result[$i]['dDate']); ?></td>
                                                            <td>
                                                                <?php
                                                                $pat = '/\#([^\"]*?)\#/';
                                                        preg_match($pat, $db_result[$i]['tDescription'], $tDescription_value);
                                                        $tDescription_translate = $langage_lbl[$tDescription_value[1]];
                                                        $row_tDescription = str_replace($tDescription_value[0], $tDescription_translate, $db_result[$i]['tDescription']);

                                                        // added by SP on 12-11-2020 for transfer description converted from label.
                                                        if ('Transfer' === $db_result[$i]['eFor']) {
                                                            if (preg_match($pat, $row_tDescription, $tDescription_value_new)) {
                                                                $tDescription_translate_second = $langage_lbl[$tDescription_value_new[1]];
                                                                $row_tDescription1 = str_replace($tDescription_value_new[0], $tDescription_translate_second, $row_tDescription);
                                                            } else {
                                                                $row_tDescription1 = $row_tDescription;
                                                            }
                                                            if (preg_match($pat, $row_tDescription1, $tDescription_value_other)) {
                                                                $tDescription_translate_last = $langage_lbl[$tDescription_value_other[1]];
                                                                $row_tDescriptionNew = str_replace($tDescription_value_other[0], $tDescription_translate_last, $row_tDescription1);
                                                            } else {
                                                                $row_tDescriptionNew = $row_tDescription1;
                                                            }
                                                        }

                                                        if ('Deposit' === $db_result[$i]['eFor'] && str_contains($row_tDescription, '#TRIP_NUMBER#')) {
                                                            $row_tDescription = str_replace('#TRIP_NUMBER#', $ride_number, $row_tDescription);
                                                        }

                                                        if ('Transfer' === $db_result[$i]['eFor']) {
                                                            echo $row_tDescriptionNew;
                                                        } else {
                                                            echo $row_tDescription;
                                                        }

                                                        ?>
                                                                <!-- <?php echo str_replace('withdrawl', 'withdrawal', $db_result[$i]['tDescription']); ?> --></td>
                                                                <!-- <td>$ <?php echo $db_result[$i]['iBalance']; ?></td>-->
                                                            <td  align="center" style="word-break: break-all;"><?php echo $db_result[$i]['tPaymentUserID']; ?></td>
                                                            <td align="center"><?php echo $ride_number; ?></td>
                                                            <td align="center"><?php echo formateNumAsPerCurrency($db_result[$i]['iBalance'], ''); ?></td>


                                                            <td align="center"><?php echo str_replace('Withdrawl', 'Withdrawal', $db_result[$i]['eFor']); ?></td>
                                                            <td align="center"><?php echo $db_result[$i]['eType']; ?></td>
                                                            <!-- <td class="center">$ <?php echo $db_result[$i]['currentbal']; ?></td>-->

                                                            <td align="center"><?php echo formateNumAsPerCurrency($db_result[$i]['currentbal'] < 0 ? 0 : $db_result[$i]['currentbal'], ''); ?></td>
                                                        </tr>
    <?php } ?>
                                                    <tr class="gradeA">
                                                        <td colspan="7" align="right"><b>Total Balance</b></td>
                                                        <!--<td rowspan="1" colspan="1" align="center" class="center">$ <?php echo $user_available_balance; ?> </td> -->
                                                        <td rowspan="1" colspan="2" align="center"><?php echo formateNumAsPerCurrency($user_available_balance, ''); ?></td>
                                                    </tr>
<?php } else { ?>
                                                    <tr class="gradeA">
                                                        <td colspan="13" style="text-align:center;"> No Details Found.</td>
                                                    </tr>
<?php } ?>
                                            </tbody>
                                        </table>
                                    </form>
<?php // include('pagination_n.php');?>
                                </div>
                                <div class="singlerow-login-log wallet-report" <?php if ('search' !== $action) { ?>style="display:none;"<?php } else { ?> <?php } ?>>
                                    <span>
                                        <?php if ($userObj->hasPermission('payment-member')) { ?>
                                            <a href="javascript:void(0);" onClick="open_paymentmember();" class="add-btn">Payment To member</a>
                                        <?php } ?>
                                <?php if ($userObj->hasPermission('add-wallet-balance')) { ?>
                                            <a style="text-align: center;margin-left:10px;" class="btn btn-danger" data-toggle="modal" onclick="open_addmonery_popup();">ADD MONEY</a></span> </div>
<?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<!--- start popup-->
<div  class="modal fade" id="uiModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-sm" >
        <div class="modal-content nimot-class">
            <div class="modal-header">
                <h4>
                    <?php echo $langage_lbl['LBL_WITHDRAW_REQUEST']; ?>
                    <button type="button" class="close" data-dismiss="modal">x</button>
                </h4>
            </div>

            <div class="modal-body">
               <form class="form-horizontal" id="payment_member" method="POST" enctype="multipart/form-data" action="" name="payment_member">
                    <input type="hidden" id="action" name="action" value="paymentmember">
                    <input type="hidden"  name="eTransRequest" id="eTransRequest" value="">
                    <input type="hidden"  name="eType" id="eType" value="Debit">
                    <input type="hidden"  name="eFor" id="eFor" value="Withdrawl">
                    <input type="hidden"  name="iDriverId" id="iDriverId" value="<?php echo $iDriverId; ?>">
                    <input type="hidden"  name="iUserId" id="iUserId" value="<?php echo $iUserId; ?>">
                    <input type="hidden"  name="eUserType" id="eUserType" value="<?php echo $eUserType; ?>">
                    <input type="hidden"  name="User_Available_Balance" id="User_Available_Balance" value="<?php echo $user_available_balance; ?>">

                    <div class="row">
                        <div class="col-lg-12">
                            <label><?php echo $langage_lbl['LBL_ENTER_AMOUNT']; ?></label>
                        </div>
                        <div class="col-lg-12">
                            <input type="text" name="iBalance" class="form-control iBalance add-ibalance" id="payment_member_balance" onKeyup="checkzero(this.value);">
                            <div id="iLimitmsg"></div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer" style="margin-top: 0">
                <div class="nimot-class-but" style="margin-bottom: 0">
                    <button type="button" class="save" style="margin-left: 0 !important" id="pay_member" onClick="check_payment_member();"><?php echo $langage_lbl_admin['LBL_Save']; ?></button>
                    <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" ><?php echo $langage_lbl_admin['LBL_CANCEL_TXT']; ?></button>
                </div>
            </div>

            <div style="clear:both;"></div>
        </div>
    </div>
</div>

<!--- end popup -->
<!--- start popup-->
<div  class="modal fade" id="Addmoney" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-sm" >
        <div class="modal-content nimot-class">
            <div class="modal-header">
                <h4>
                    <?php echo $langage_lbl['LBL_ADD_MONEY']; ?>
                    <button type="button" class="close" data-dismiss="modal">x</button>
                </h4>
            </div>

            <div class="modal-body">
               <form class="form-horizontal" id="add_money_frm" method="POST" enctype="multipart/form-data" action="" name="add_money_frm">
                    <input type="hidden" id="action" name="action" value="addmoney">
                    <input type="hidden"  name="eTransRequest" id="eTransRequest" value="">
                    <input type="hidden"  name="eType" id="eType" value="Credit">
                    <input type="hidden"  name="eFor" id="eFor" value="Deposit">
                    <input type="hidden"  name="iDriverId" id="iDriverId" value="<?php echo $iDriverId; ?>">
                    <input type="hidden"  name="iUserId" id="iUserId" value="<?php echo $iUserId; ?>">
                    <input type="hidden"  name="eUserType" id="eUserType" value="<?php echo $eUserType; ?>">
                    <input type="hidden"  name="User_Available_Balance" id="User_Available_Balance" value="<?php echo $user_available_balance; ?>">

                    <div class="row">
                        <div class="col-lg-12">
                            <label><?php echo $langage_lbl['LBL_ENTER_AMOUNT']; ?></label>
                        </div>
                        <div class="col-lg-12">
                            <input type="text" name="iBalance" id="iBalance" class="form-control iBalance add-ibalance" onKeyup="checkzero(this.value);">
                            <div id="iLimitmsg"></div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer" style="margin-top: 0">
                <div class="nimot-class-but" style="margin-bottom: 0">
                    <button type="button" class="save" style="margin-left: 0 !important" id="add_money" onClick="check_add_money();"><?php echo $langage_lbl['LBL_Save']; ?></button>
                    <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" ><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                </div>
            </div>

            <div style="clear:both;"></div>
        </div>
    </div>
</div>

<!--- end popup -->
<?php include_once 'footer.php'; ?>

<form name="pageForm" id="pageForm" action="action/payment_report.php" method="post" >
    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
    <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
    <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>" >
    <input type="hidden" name="order" id="order" value="<?php echo $order; ?>" >
    <input type="hidden" name="action" value="<?php echo $action; ?>" >
    <input type="hidden" name="eUserType" value="<?php echo $eUserType; ?>" >
    <input type="hidden" name="iDriverId" value="<?php echo $iDriverId; ?>" >
    <input type="hidden" name="iUserId" value="<?php echo $iUserId; ?>" >
    <input type="hidden" name="searchBalanceType" value="<?php echo $eFor; ?>" >
    <input type="hidden" name="searchPaymentType" value="<?php echo $Payment_type; ?>" >
    <input type="hidden" name="startDate" value="<?php echo $startDate; ?>" >
    <input type="hidden" name="endDate" value="<?php echo $endDate; ?>" >
    <input type="hidden" name="method" id="method" value="" >
</form>

<link rel="stylesheet" href="../assets/plugins/datepicker/css/datepicker.css" />
<link rel="stylesheet" href="css/select2/select2.min.css" />
<script src="js/plugins/select2.min.js"></script>
<script src="../assets/plugins/datepicker/js/bootstrap-datepicker.js"></script>
<script>

              $('#dp4').datepicker().on('changeDate', function (ev) {
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
                $('#dp5').datepicker().on('changeDate', function (ev) {
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
                    $("#dp5").click(function(){
                         $('#dp5').datepicker('show');
                         $('#dp4').datepicker('hide');
                    });

                    $("#dp4").click(function(){
                         $('#dp4').datepicker('show');
                         $('#dp5').datepicker('hide');
                    });
                    //$('#iDriverId').hide();
/*                    var eusertype = $("#eUserType").val();
                    if (eusertype == "") {
                      $('.singlerow-login-log').hide();
                    } else {
                      $('.singlerow-login-log').show();
                    }*/
                    if ('<?php echo $startDate; ?>' != '') {
                      $("#dp4").val('<?php echo $startDate; ?>');
                      $("#dp4").datepicker('update', '<?php echo $startDate; ?>');
                    }
                    if ('<?php echo $endDate; ?>' != '') {
                      $("#dp5").datepicker('update', '<?php echo $endDate; ?>');
                      $("#dp5").val('<?php echo $endDate; ?>');
                    }
              });
              function todayDate()
              {
                  $("#dp4").val('<?php echo $Today; ?>');
                  $("#dp5").val('<?php echo $Today; ?>');
              }
              function yesterdayDate()
              {
                  $("#dp4").val('<?php echo $Yesterday; ?>');
                  $("#dp4").datepicker('update', '<?php echo $Yesterday; ?>');
                  $("#dp5").datepicker('update', '<?php echo $Yesterday; ?>');
                  $("#dp4").change();
                  $("#dp5").change();
                  $("#dp5").val('<?php echo $Yesterday; ?>');
              }
              function currentweekDate(dt, df)
              {
                  $("#dp4").val('<?php echo $monday; ?>');
                  $("#dp4").datepicker('update', '<?php echo $monday; ?>');
                  $("#dp5").datepicker('update', '<?php echo $sunday; ?>');
                  $("#dp5").val('<?php echo $sunday; ?>');
              }
              function previousweekDate(dt, df)
              {
                  $("#dp4").val('<?php echo $Pmonday; ?>');
                  $("#dp4").datepicker('update', '<?php echo $Pmonday; ?>');
                  $("#dp5").datepicker('update', '<?php echo $Psunday; ?>');
                  $("#dp5").val('<?php echo $Psunday; ?>');
              }
              function currentmonthDate(dt, df)
              {
                  $("#dp4").val('<?php echo $currmonthFDate; ?>');
                  $("#dp4").datepicker('update', '<?php echo $currmonthFDate; ?>');
                  $("#dp5").datepicker('update', '<?php echo $currmonthTDate; ?>');
                  $("#dp5").val('<?php echo $currmonthTDate; ?>');
              }
              function previousmonthDate(dt, df)
              {
                  $("#dp4").val('<?php echo $prevmonthFDate; ?>');
                  $("#dp4").datepicker('update', '<?php echo $prevmonthFDate; ?>');
                  $("#dp5").datepicker('update', '<?php echo $prevmonthTDate; ?>');
                  $("#dp5").val('<?php echo $prevmonthTDate; ?>');
              }
              function currentyearDate(dt, df)
              {
                  $("#dp4").val('<?php echo $curryearFDate; ?>');
                  $("#dp4").datepicker('update', '<?php echo $curryearFDate; ?>');
                  $("#dp5").datepicker('update', '<?php echo $curryearTDate; ?>');
                  $("#dp5").val('<?php echo $curryearTDate; ?>');
              }
              function previousyearDate(dt, df)
              {
                  $("#dp4").val('<?php echo $prevyearFDate; ?>');
                  $("#dp4").datepicker('update', '<?php echo $prevyearFDate; ?>');
                  $("#dp5").datepicker('update', '<?php echo $prevyearTDate; ?>');
                  $("#dp5").val('<?php echo $prevyearTDate; ?>');
              }

              function redirectpaymentpage(url)
              {
                  //$("#frmsearch").reset();
                  document.getElementById("action").value = '';
                  document.getElementById("frmsearch").reset();
                  window.location = url;
              }

              function getCheckCount(frmpayment)
              {
                  var x = 0;
                  var threasold_value = 0;
                  for (i = 0; i < frmpayment.elements.length; i++)
                  {
                      if (frmpayment.elements[i].checked == true)
                      {
                          x++;
                      }
                  }
                  return x;
              }


              function Paytodriver() {
                  y = getCheckCount(document.frmpayment);

                  if (y > 0)
                  {
                      ans = confirm("Are you sure you want to Pay To <?php echo $langage_lbl['LBL_DRIVER_TXT_ADMIN']; ?>?");
                      if (ans == false)
                      {
                          return false;
                      }
                      $("#ePayDriver").val('Yes');
                      document.frmbooking.submit();
                  } else {
                      alert("Select Trip/Job for Pay To <?php echo $langage_lbl['LBL_DRIVER_TXT_ADMIN']; ?>");
                      return false;
                  }
              }

              function exportlist() {
                  document.search.action = "export_driver_details.php";
                  document.search.submit();
              }


                 $("#Search").on('click', function () {
                    var eusertype = $("#eUserType").val();
                    var username_driver = $("#searchDriver").val();
                    var username_rider = $("#searchRider").val();

                    if (eusertype == "") {
                        alert("Please Select Usertype ");
                        return false;
                    }
                    if (eusertype == "Driver" && username_driver == "") {
                        alert("Please Select <?php echo $langage_lbl['LBL_DRIVER_TXT_ADMIN']; ?> name");
                        return false;
                    }
                    if (eusertype == "Rider" && username_rider == "") {
                        alert("Please Select <?php echo $langage_lbl['LBL_RIDER']; ?> name");
                        return false;
                    }
                    if ($("#dp5").val() < $("#dp4").val()) {
                        alert("From date should be lesser than To date.")
                        return false;
                    } else {
                        var action = $("#_list_form").attr('action');
                        var formValus = $("#frmsearch").serialize();
                        window.location.href = action + "?" + formValus;
                    }
                });

              function open_paymentmember() {
                  $('#uiModal').modal('show');
              }
              function open_addmonery_popup() {
                  $('#Addmoney').modal('show');
              }

            $('#iBalance').keypress(function (e) {
              if (e.which == 13) {
                check_payment_member();
                return false;
              }
            });



          function check_payment_member() {
              var maxamount = document.getElementById("User_Available_Balance").value;
              var requestamount = document.getElementById("payment_member_balance").value;
              if (requestamount == '') {
                  alert("Please Enter Withdraw Amount");
                  return false;
              } else if (requestamount == 0) {
                    alert("You Can Not Enter Zero Number");
                    return false;
              }else if (parseFloat(requestamount) > parseFloat(maxamount)) {
                  alert("Please Enter Withdraw Amount Less Than " + maxamount);
                  return false;
              } else {
                //$("#pay_member").val('Please wait ...').attr('disabled','disabled');
                $(".loader-default").show(); // Added By HJ On 20-08-2019 For Display Loader when cancel order
                $('#payment_member').submit();
              }
                //document.payment_member.submit();

          }

                $(document).ready(function() {
                    $("#add_money_frm").bind("keypress", function (e) {
                        if (e.keyCode == 13) {
                            e.preventDefault();
                            return false;
                        }
                    });
                });

              function check_add_money() {
                var iBalance = $( "#iBalance" ).val();
                if (iBalance == '') {
                    alert("Please Enter Amount");
                    return false;
                } else if (iBalance == 0) {
                    alert("You Can Not Enter Zero Number");
                    return false;
                } else {
                    //$("#add_money").val('Please wait ...').attr('disabled','disabled');
                    $(".loader-default").show(); // Added By HJ On 20-08-2019 For Display Loader when cancel order
                    $('#add_money_frm').submit();
                }
                //document.add_money_frm.submit();
              }

        $(".iBalance").keydown(function (e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                (e.keyCode == 65 && e.ctrlKey === true) ||
                (e.keyCode == 67 && e.ctrlKey === true) ||
                (e.keyCode == 88 && e.ctrlKey === true) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                    return;
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

        function show_hide_user_type(username) {
            if (username == "Driver") {
                $('#sec_driver').show();
                $('#sec_rider').hide();
                //$('select.filter-by-text#searchDriver').val(null).trigger('change');
                 $('select.filter-by-text#searchRider').val(null).trigger('change');
            } else if (username == "Rider") {
                $('#sec_rider').show();
                $('#sec_driver').hide();
                 $('select.filter-by-text#searchDriver').val(null).trigger('change');
                //$('select.filter-by-text#searchRider').val(null).trigger('change');
            } else {
                $('#sec_driver').hide();
                $('#sec_rider').hide();
                $('select.filter-by-text#searchDriver').val(null).trigger('change');
                $('select.filter-by-text#searchRider').val(null).trigger('change');
            }
        }
        $('body').on('keyup', '.select2-search__field', function() {
            $(".select2-container .select2-dropdown .select2-results .select2-results__options").addClass("hideoptions");
            // console.log('item12');
            // console.log($( ".select2-results__options" ).is( ".select2-results__message" ) );
            if ( $( ".select2-results__options" ).is( ".select2-results__message" ) ) {
            //if($( ".select2-results__option" ).hasClass( "select2-results__message" )){
               // console.log('item');
               $(".select2-container .select2-dropdown .select2-results .select2-results__options").removeClass("hideoptions");
            }
        });
        function formatDesign(item) {
            //console.log(item.text);
            /*if(item.text == 'Searching�'){
                console.log('item1');
               $(".select2-container .select2-dropdown .select2-results .select2-results__options").removeClass("hideoptions");
            }
*/
            $(".select2-container .select2-dropdown .select2-results .select2-results__options").removeClass("hideoptions");
            if (!item.id) {
                return item.text;
            }
            //console.log(item);
            var selectionText = item.text.split("--");
            if(selectionText[2] != null && selectionText[1] != null){
                var $returnString =  $('<span>'+selectionText[0] + '</br>' + selectionText[1] + "</br>" + selectionText[2]+'</span>');
            } else if(selectionText[2] == null && selectionText[1] != null){
                var $returnString =  $('<span>'+selectionText[0] + '</br>' + selectionText[1] + '</span>');
            } else if(selectionText[2] != null && selectionText[1] == null){
                var $returnString =  $('<span>'+selectionText[0] + '</br>' + selectionText[2] + '</span>');
            }
            //$(".select2-container .select2-dropdown .select2-results .select2-results__options").removeClass("hideoptions");
            return $returnString;
        };

        function formatDesignnew(item){
            if (!item.id) {
                return item.text;
            }
            var selectionText = item.text.split("--");
            return selectionText[0];
        }
        $(function () {
            $("select.filter-by-text").each(function () {
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
                                usertype:document.getElementById('eUserType').value
                            }
                            //console.log(queryParameters);
                            return queryParameters;
                        },
                        processResults: function (data, params) {
                            //console.log(data);
                            params.page = params.page || 1;
                            if(data.length < 10){
                                var more = false;
                            } else {
                                var more = (params.page * 10) <= data[0].total_count;
                            }
                            $(".select2-container .select2-dropdown .select2-results .select2-results__options").removeClass("hideoptions");
                            return {
                                results: $.map(data, function (item) {
                                    if(item.Phoneno != '' && item.vEmail != ''){
                                        var textdata = item.fullName  + "--" + "Email: " + item.vEmail + "--" + "Phone: +" + item.Phoneno;
                                    } else if(item.Phoneno == '' && item.vEmail != ''){
                                        var textdata = item.fullName  + "--" + "Email: " + item.vEmail;
                                    } else if(item.Phoneno != '' && item.vEmail == ''){
                                        var textdata = item.fullName  + "--" + "Phone: +" + item.Phoneno;
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
        if(document.getElementById('eUserType').value == 'Driver'){
            var sId = '<?php echo $iDriverId; ?>';
            var sSelect = $('select.filter-by-text#searchDriver');
            $('select.filter-by-text#searchRider').val(null).trigger('change');
        } else {
            var sId = '<?php echo $iUserId; ?>';
            var sSelect = $('select.filter-by-text#searchRider');
            $('select.filter-by-text#searchDriver').val(null).trigger('change');
        }
        var itemname;
        var itemid;
        if(sId != ''){
            // $.ajax({
            //     type: 'POST',
            //     dataType: "json",
            //     url: 'ajax_getdriver_detail_search.php?id=' + sId + '&usertype='+document.getElementById('eUserType').value
            // }).then(function (data) {
            //     // create the option and append to Select2
            //     $.map(data, function (item) {
            //         if(item.Phoneno != '' && item.vEmail != ''){
            //             var textdata = item.fullName  + "--" + "Email: " + item.vEmail + "--" + "Phone: +" + item.Phoneno;
            //         } else if(item.Phoneno == '' && item.vEmail != ''){
            //             var textdata = item.fullName  + "--" + "Email: " + item.vEmail;
            //         } else if(item.Phoneno != '' && item.vEmail == ''){
            //             var textdata = item.fullName  + "--" + "Phone: +" + item.Phoneno;
            //         }
            //         var textdata = item.fullName;
            //         itemname = textdata;
            //         itemid = item.id;
            //     });
            //     var option = new Option(itemname, itemid, true, true);
            //     sSelect.append(option).trigger('change');
            // });

            var ajaxData = {
                'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_getdriver_detail_search.php?id=' + sId + '&usertype='+document.getElementById('eUserType').value,
                'AJAX_DATA': "",
                'REQUEST_DATA_TYPE': 'json'
            };
            getDataFromAjaxCall(ajaxData, function(response) {
                if(response.action == "1") {
                    var data = response.result;
                    $.map(data, function (item) {
                        if(item.Phoneno != '' && item.vEmail != ''){
                            var textdata = item.fullName  + "--" + "Email: " + item.vEmail + "--" + "Phone: +" + item.Phoneno;
                        } else if(item.Phoneno == '' && item.vEmail != ''){
                            var textdata = item.fullName  + "--" + "Email: " + item.vEmail;
                        } else if(item.Phoneno != '' && item.vEmail == ''){
                            var textdata = item.fullName  + "--" + "Phone: +" + item.Phoneno;
                        }
                        var textdata = item.fullName;
                        itemname = textdata;
                        itemid = item.id;
                    });
                    var option = new Option(itemname, itemid, true, true);
                    sSelect.append(option).trigger('change');
                }
                else {
                    console.log(response.result);
                }
            });
        }
 function checkzero(userlimit)
{
    if(userlimit != ""){
        if (userlimit == 0)
        {
            $('#iLimitmsg').html('<span class="red">You Can Not Enter Zero Number</span>');
        } else if(userlimit <= 0) {
          $('#iLimitmsg').html('<span class="red">You Can Not Enter Negative Number</span>');
      } else {
         $('#iLimitmsg').html('');
        }
    } else{
         $('#iLimitmsg').html('');
    }

}
 function checkzeroAdd(userlimit)
{
    if(userlimit != ""){
        if (userlimit == 0)
        {
            $('#iLimitmsgadd').html('<span class="red">You Can Not Enter Zero Number</span>');
        } else if(userlimit <= 0) {
          $('#iLimitmsgadd').html('<span class="red">You Can Not Enter Negative Number</span>');
      } else {
         $('#iLimitmsgadd').html('');
        }
    } else{
         $('#iLimitmsgadd').html('');
    }

}
</script>
<?php if ('' !== $action) { ?>
    <script>
        usertype = document.getElementById('eUserType').value;
        if (usertype == "Driver") {
            $('#sec_driver').show();
        } else {
            $('#sec_rider').hide();
        }
        show_hide_user_type(usertype);
    </script>
<?php } else { ?>
    <script>
        $('#sec_rider').hide();
        $('#sec_driver').hide();
    </script>
<?php } ?>
</body>
<!-- END BODY-->
</html>
