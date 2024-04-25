<?php
include_once '../common.php';
if (!$userObj->hasPermission('manage-user-reward-report')) {
    $userObj->redirect();
}
$script = 'UserRewardReport';
function cleanNumber($num)
{
    return str_replace(',', '', $num);
}
// data for select fields
$db_curr_mst = $obj->MySQLSelect("select vSymbol from currency where eDefault='Yes'");
$vSymbol = '$';
if (count($db_curr_mst) > 0) {
    $vSymbol = $db_curr_mst[0]['vSymbol'];
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
$startDate = $_REQUEST['startDate'] ?? '';
$endDate = $_REQUEST['endDate'] ?? '';
$eType = $_REQUEST['eType'] ?? '';
if ('search' === $action) {
    if ('' !== $startDate) {
        $ssql .= " AND Date(tr.tTripRequestDate) >='".$startDate."'";
    }
    if ('' !== $endDate) {
        $ssql .= " AND Date(tr.tTripRequestDate) <='".$endDate."'";
    }
    if ('' !== $serachTripNo) {
        if (str_contains($serachTripNo, ',')) {
            $serachTripNoArr = str_replace(',', "','", $serachTripNo);
            $ssql .= " AND tr.vRideNo IN ('".$serachTripNoArr."')";
        } else {
            $ssql .= " AND tr.vRideNo ='".$serachTripNo."'";
        }
    }

    if ('' !== $searchRider) {
        $ssql .= " AND tr.iUserId ='".$searchRider."'";
    }
}

$ssql .= " AND tr.eType = 'Ride'";

$trp_ssql = '';
if (SITE_TYPE === 'Demo') {
    $trp_ssql = " And tr.tTripRequestDate > '".WEEK_DATE."'";
}

// Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
// Added By HJ On 30-07-2020 For Get Order Data Of Driver Start - As Per Discuss With KS Sir

$etypeSql = " AND tr.eSystem = 'General'";

// Added By HJ On 30-07-2020 For Get Order Data Of Driver End As Per Discuss With KS Sir
$sql = "SELECT tr.fUserRewardsCoins,tr.ePayWallet,u.iBalance,tr.fTax1,tr.fTax2,tr.iFare,tr.fTripGenerateFare,tr.fCommision,tr.vTripPaymentMode,( SELECT COUNT(tr.iTripId) FROM trips AS tr LEFT JOIN user_wallet AS u ON u.iTripId = tr.iTripId WHERE (tr.iActive ='Finished' OR (tr.iActive ='Canceled' AND tr.iFare > 0) OR (tr.iActive ='Canceled' AND tr.fWalletDebit > 0 AND tr.iFare = 0)) AND (tr.fUserRewardsCoins > 0 AND u.tDescription LIKE '%LBL_REWARD_AMOUNT_CREDITED_USER%') {$etypeSql} {$ssql} {$trp_ssql}) AS Total FROM trips AS tr LEFT JOIN register_user AS ru ON tr.iUserId = ru.iUserId LEFT JOIN user_wallet AS u ON u.iTripId = tr.iTripId WHERE(tr.iActive ='Finished' OR (tr.iActive ='Canceled' AND tr.iFare > 0) OR (tr.iActive ='Canceled' AND tr.fWalletDebit > 0 AND tr.iFare = 0)) AND (tr.fUserRewardsCoins > 0 AND u.tDescription LIKE '%LBL_REWARD_AMOUNT_CREDITED_USER%')  {$etypeSql} {$ssql} {$trp_ssql}"; // OR u.tDescription LIKE '%LBL_REWARD_AMOUNT_CREDITED_USER%'
$totalData = $obj->MySQLSelect($sql);
// Added By HJ On 08-08-2019 For Get Driver Wallet Debit Amount Start As Per Discuss With KS Sir

$total_results = $totalData[0]['Total'];
$total_pages = ceil($total_results / $per_page); // total pages we going to have
$show_page = 1;
$start = 0;
$end = $per_page;
// -------------if page is setcheck------------------//
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
$sql = "SELECT tr.fUserRewardsCoins,tr.fCancellationFare,tr.iFromStationId,tr.iToStationId,tr.ePayWallet,tr.iFare, tr.fTax1,tr.fTax2,tr.iOrganizationId,tr.ePoolRide,tr.iTripId,tr.fHotelCommision,tr.vRideNo,tr.iDriverId,tr.iUserId,tr.tTripRequestDate,tr.tStartDate,tr.tEndDate, tr.eType, tr.eHailTrip,tr.fTripGenerateFare,tr.fCommision, tr.fDiscount, tr.fWalletDebit, tr.fTipPrice,tr.eDriverPaymentStatus,tr.ePaymentCollect,tr.vTripPaymentMode,tr.iActive,tr.fOutStandingAmount, tr.iRentalPackageId,u.iBalance,concat(ru.vName,' ',ru.vLastName) as riderName,tr.vTimeZone FROM trips AS tr LEFT JOIN register_user AS ru ON tr.iUserId = ru.iUserId LEFT JOIN user_wallet AS u ON u.iTripId = tr.iTripId WHERE (tr.iActive ='Finished' OR (tr.iActive ='Canceled' AND tr.iFare > 0) OR (tr.iActive ='Canceled' AND tr.fWalletDebit > 0 AND tr.iFare = 0)) AND (tr.fUserRewardsCoins > 0 AND u.tDescription LIKE '%LBL_REWARD_AMOUNT_CREDITED_USER%') {$etypeSql} {$ssql} {$trp_ssql} {$ord} LIMIT {$start}, {$per_page}"; // OR u.tDescription LIKE '%LBL_REWARD_AMOUNT_CREDITED_USER%'
$db_trip = $obj->MySQLSelect($sql);

$endRecord = count($db_trip);
// for total records sum

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
?>
<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title><?php echo $SITE_NAME; ?> | User Reward Report</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <?php include_once 'global_files.php'; ?>
    <style>
        .setteled-class {
            background-color: #bddac5
        }
    </style>
</head>
<!-- END  HEAD-->
<!-- BEGIN BODY-->
<body class="padTop53">
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
                        <h2>User Reward Report</h2>
                    </div>
                </div>
                <hr/>
            </div>
            <?php include 'valid_msg.php'; ?>
            <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">
                <div class="Posted-date mytrip-page payment-report">
                    <input type="hidden" name="action" value="search"/>
                    <span>
                        <a style="cursor:pointer"
                           onClick="return todayDate('dp4', 'dp5');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Today']; ?></a>
                        <a style="cursor:pointer"
                           onClick="return yesterdayDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Yesterday']; ?></a>
                        <a style="cursor:pointer"
                           onClick="return currentweekDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Current_Week']; ?></a>
                        <a style="cursor:pointer"
                           onClick="return previousweekDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Previous_Week']; ?></a>
                        <a style="cursor:pointer"
                           onClick="return currentmonthDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Current_Month']; ?></a>
                        <a style="cursor:pointer"
                           onClick="return previousmonthDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Previous Month']; ?></a>
                        <a style="cursor:pointer"
                           onClick="return currentyearDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Current_Year']; ?></a>
                        <a style="cursor:pointer"
                           onClick="return previousyearDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Previous_Year']; ?></a>
                    </span>
                    <span>
                        <input type="text" id="dp4" name="startDate" placeholder="From Date"
                               class="form-control" value="" readonly=""
                               style="cursor:default; background-color: #fff"/>
                        <input type="text" id="dp5" name="endDate" placeholder="To Date" class="form-control"
                               value="" readonly="" style="cursor:default; background-color: #fff"/>
                        <div class="col-lg-3 select001">
                            <select class="form-control filter-by-text" name='searchRider' data-text="Select <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?>"  id="searchRider">
                                <option value=""> Select <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?></option>
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <input type="text" id="serachTripNo" name="serachTripNo"
                                   placeholder="<?php echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']; ?> Number"
                                   class="form-control search-trip001" value="<?php echo $serachTripNo; ?>"/>
                        </div>
                    </span>
                </div>

                <div class="tripBtns001">
                    <b>
                        <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search"/>
                        <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href = 'user_reward_report.php'"/>
                    </b>
                </div>
            </form>
            <div class="table-list">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <form name="_list_form" id="_list_form" class="_list_form" method="post"
                                  action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <input type="hidden" name="iTripId" id="iTripId" value="">
                                <table class="table table-bordered" id="dataTables-example123">
                                    <thead>
                                    <tr>
                                        <th style="text-align:center;" width="15%"><?php echo $langage_lbl_admin['LBL_RIDE_NO_ADMIN']; ?> </th>
                                        <th width="15%">
                                            <a href="javascript:void(0);" onClick="Redirect(1,<?php
                                            if ('1' === $sortby) {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)"><?php echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']; ?> <?php
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
                                        <th width="15%" style="text-align:center;"> Coin Earned </th>
                                        <th style="text-align:right;" width="15%">Amount Transferred to Wallet</th>
                                        <th style="text-align:right;" width="15%">Date of Coins Earned </th>
                                        <th style="text-align:center;" width="15%">View Invoice</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $set_unsetarray = [];
$tot_fare = $totalCoins = 0;
if (count($db_trip) > 0) {
    for ($i = 0; $i < count($db_trip); ++$i) {
        $iTripId = $db_trip[$i]['iTripId'];
        $eTypenew = $db_trip[$i]['eType'];

        $systemTimeZone = date_default_timezone_get();
        if ($db_trip[$i]['fCancellationFare'] > 0 && '' !== $db_trip[$i]['vTimeZone']) {
            $dBookingDate = converToTz($db_trip[$i]['tEndDate'], $db_trip[$i]['vTimeZone'], $systemTimeZone);
        } elseif ('' !== $db_trip[$i]['tStartDate'] && '0000-00-00 00:00:00' !== $db_trip[$i]['tStartDate'] && '' !== $db_trip[$i]['vTimeZone']) {
            $dBookingDate = $db_trip[$i]['tStartDate'];
        } else {
            if (!empty($db_trip[$i]['tStartDate']) && '0000-00-00 00:00:00' !== $db_trip[$i]['tStartDate']) {
                $dBookingDate = $db_trip[$i]['tStartDate'];
            } else {
                $dBookingDate = $db_trip[$i]['tTripRequestDate'];
            }
        }
        $tot_fare += $db_trip[$i]['iBalance'];
        $totalCoins += $db_trip[$i]['fUserRewardsCoins'];
        $link_page = 'invoice.php?iTripId='.$db_trip[$i]['iTripId'];
        ?>
                                            <tr class="gradeA">
                                                <td align="center">
                                                    <a href="<?php echo $link_page; ?>" target="_blank" style="text-decoration: underline;"><?php echo $db_trip[$i]['vRideNo']; ?></a>
                                                </td>

                                                <td> <?php if ($userObj->hasPermission('view-users')) { ?><a href="javascript:void(0);"
                                                   onClick="show_rider_details('<?php echo $db_trip[$i]['iUserId']; ?>')"
                                                   style="text-decoration: underline;"> <?php echo clearName($db_trip[$i]['riderName']); ?></a><?php } ?>
                                                </td>

                                                <td align="center"><?php echo $db_trip[$i]['fUserRewardsCoins']; ?></td>
                                                <td align="right">
                                                    <?php if ('' !== $db_trip[$i]['iBalance'] && 0 !== $db_trip[$i]['iBalance']) {
                                                        $totFareHtml = formateNumAsPerCurrency($db_trip[$i]['iBalance'], '');
                                                    } else {
                                                        $totFareHtml = '-';
                                                    }
        echo $totFareHtml;
        ?>
                                                </td>

                                                <td align="right"><?php echo DateTime($dBookingDate, '7'); ?></td>
                                                <?php if ($userObj->hasPermission('view-invoice')) { ?>
                                                    <td align="center" width="10%">
                                                        <?php if (('Finished' === $db_trip[$i]['iActive'] && 'Yes' === $db_trip[$i]['eCancelled']) || ($db_trip[$i]['fCancellationFare'] > 0) || ('Canceled' === $db_trip[$i]['iActive'] && $db_trip[$i]['fWalletDebit'] > 0)) { ?>
                                                            <button class="btn btn-primary"
                                                                    onclick='return !window.open("<?php echo $link_page; ?>", "_blank")'
                                                                    ;">
                                                            <i class="icon-th-list icon-white">
                                                                <b>View Invoice</b>
                                                            </i>
                                                            </button>
                                                            <div style="font-size: 12px;">Cancelled</div>
                                                        <?php } elseif ('Finished' === $db_trip[$i]['iActive']) { ?>
                                                            <button class="btn btn-primary"
                                                                    onclick='return !window.open("<?php echo $link_page; ?>", "_blank")'
                                                                    ;">
                                                            <i class="icon-th-list icon-white">
                                                                <b>View Invoice</b>
                                                            </i>
                                                            </button>
                                                            <?php
                                                        } else {
                                                            if ('Active' === $db_trip[$i]['iActive'] || 'On Going Trip' === $db_trip[$i]['iActive'] || 'Inactive' === $db_trip[$i]['iActive'] || ('Arrived' === $db_trip[$i]['iActive'] && !empty($db_trip[$i]['iFromStationId']) && !empty($db_trip[$i]['iToStationId']))) {
                                                                if ('UberX' === $APP_TYPE || 'Ride-Delivery-UberX' === $APP_TYPE) {
                                                                    echo 'On Job';
                                                                } else {
                                                                    echo 'On Ride';
                                                                }
                                                                ?>
                                                                <br/> <!-- Commented By HJ On 11-01-2019 As Per Discuss withQA BM  -->
                                                                <?php if ('Inactive' === $db_trip[$i]['iActive']) { ?>
                                                                    Pending
                                                                    <i
                                                                            class="fa fa-exclamation-circle pending-trip"
                                                                            data-toggle="tooltip"
                                                                            data-placement="bottom"
                                                                            data-original-title='This trip is in pending state as the driver is on another trip. The driver will start this trip after completing the previous ones. This is a "Back-to-back Trips" feature where driver can get new ride requests while he is near to complete existing trip.'></i>
                                                                <?php } else { ?>

                                                                <?php } ?>
                                                                <?php if ('Multi-Delivery' === $db_trip[$i]['eType'] || 'Deliver' === $db_trip[$i]['eType']) { ?>
                                                                    <br/>
                                                                    <br/>
                                                                    <button class="btn btn-primary"
                                                                            onclick='return !window.open("<?php echo $link_page; ?>", "_blank")'
                                                                            ;">
                                                                    <i class="icon-th-list icon-white">
                                                                        <b>View Invoice
                                                                        </b>
                                                                    </i>
                                                                    </button><?php } ?>
                                                                <?php
                                                            } elseif ('Canceled' === $db_trip[$i]['iActive'] && ($db_trip[$i]['iCancelReasonId'] > 0 || '' !== $db_trip[$i]['vCancelReason'])) {
                                                                ?>
                                                                <a href="javascript:void(0);" class="btn btn-info"
                                                                   data-toggle="modal"
                                                                   data-target="#uiModal1_<?php echo $db_trip[$i]['iTripId']; ?>">
                                                                    Cancel Reason
                                                                </a>
                                                                <?php
                                                            } elseif ('Canceled' === $db_trip[$i]['iActive'] && $db_trip[$i]['fWalletDebit'] < 0) {
                                                                echo 'Cancelled';
                                                            } else {
                                                                echo $db_trip[$i]['iActive'];
                                                            }
                                                        }
                                                    ?>
                                                    </td>
                                                <?php } ?>
                                            </tr>
                                        <?php }
    } else { ?>
                                            <tr class="gradeA">
                                                <td colspan="17" style="text-align:center;">No Payment Details Found.</td>
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
            <div class="row">
                <div class="col-lg-6 col-lg-offset-6">
                    <div class="admin-notes">
                        <h4>Summary:</h4>
                        <ul>
                            <li><strong>Total Reward Amount: </strong><?php echo formateNumAsPerCurrency(cleanNumber($tot_fare), ''); ?></li>

                            <li><strong>Total Coins: </strong><?php echo cleanNumber($totalCoins); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<div class="modal fade " id="detail_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                    <!--<i aria-hidden="true" class="fa fa-building-o" style="margin:2px 5px 0 2px;"></i>-->
                    <i style="margin:2px 5px 0 2px;">
                        <img src="images/rider-icon.png" alt="">
                    </i>
                    <?php echo $langage_lbl_admin['LBL_RIDER']; ?> Details
                    <button type="button" class="close" data-dismiss="modal">x</button>
                </h4>
            </div>
            <div class="modal-body" style="max-height: 450px;overflow: auto;">
                <div id="imageIcons">
                    <div align="center">
                        <img src="default.gif">
                        <br/>
                        <span>Retrieving details,please Wait...</span>
                    </div>
                </div>
                <div id="rider_detail"></div>
            </div>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>
<link rel="stylesheet" href="../assets/plugins/datepicker/css/datepicker.css"/>

<script src="../assets/plugins/datepicker/js/bootstrap-datepicker.js"></script>
<?php include_once 'searchfunctions.php'; ?>
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
        $("#dp5").click(function () {
            $('#dp5').datepicker('show');
            $('#dp4').datepicker('hide');
        });
        $("#dp4").click(function () {
            $('#dp4').datepicker('show');
            $('#dp5').datepicker('hide');
        });
        if ('<?php echo $startDate; ?>' != '') {
            $("#dp4").val('<?php echo $startDate; ?>');
            $("#dp4").datepicker('update', '<?php echo $startDate; ?>');
        }
        if ('<?php echo $endDate; ?>' != '') {
            $("#dp5").datepicker('update', '<?php echo $endDate; ?>');
            $("#dp5").val('<?php echo $endDate; ?>');
        }
    });

    function todayDate() {
        $("#dp4").val('<?php echo $Today; ?>');
        $("#dp5").val('<?php echo $Today; ?>');
    }

    function reset() {
        location.reload();
    }

    function yesterdayDate() {
        $("#dp4").val('<?php echo $Yesterday; ?>');
        $("#dp4").datepicker('update', '<?php echo $Yesterday; ?>');
        $("#dp5").datepicker('update', '<?php echo $Yesterday; ?>');
        $("#dp4").change();
        $("#dp5").change();
        $("#dp5").val('<?php echo $Yesterday; ?>');
    }

    function currentweekDate(dt, df) {
        $("#dp4").val('<?php echo $monday; ?>');
        $("#dp4").datepicker('update', '<?php echo $monday; ?>');
        $("#dp5").datepicker('update', '<?php echo $sunday; ?>');
        $("#dp5").val('<?php echo $sunday; ?>');
    }

    function previousweekDate(dt, df) {
        $("#dp4").val('<?php echo $Pmonday; ?>');
        $("#dp4").datepicker('update', '<?php echo $Pmonday; ?>');
        $("#dp5").datepicker('update', '<?php echo $Psunday; ?>');
        $("#dp5").val('<?php echo $Psunday; ?>');
    }

    function currentmonthDate(dt, df) {
        $("#dp4").val('<?php echo $currmonthFDate; ?>');
        $("#dp4").datepicker('update', '<?php echo $currmonthFDate; ?>');
        $("#dp5").datepicker('update', '<?php echo $currmonthTDate; ?>');
        $("#dp5").val('<?php echo $currmonthTDate; ?>');
    }

    function previousmonthDate(dt, df) {
        $("#dp4").val('<?php echo $prevmonthFDate; ?>');
        $("#dp4").datepicker('update', '<?php echo $prevmonthFDate; ?>');
        $("#dp5").datepicker('update', '<?php echo $prevmonthTDate; ?>');
        $("#dp5").val('<?php echo $prevmonthTDate; ?>');
    }

    function currentyearDate(dt, df) {
        $("#dp4").val('<?php echo $curryearFDate; ?>');
        $("#dp4").datepicker('update', '<?php echo $curryearFDate; ?>');
        $("#dp5").datepicker('update', '<?php echo $curryearTDate; ?>');
        $("#dp5").val('<?php echo $curryearTDate; ?>');
    }

    function previousyearDate(dt, df) {
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
</script>
</body>
<!-- END BODY-->
</html>