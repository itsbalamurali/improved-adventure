<?php
include_once '../common.php';
if (!$userObj->hasPermission('view-trip-trackservice')) {
    $userObj->redirect();
}
$default_lang = $LANG_OBJ->FetchSystemDefaultLang();
$script = 'TrackServiceTrips';
$rdr_ssql = '';
if (SITE_TYPE === 'Demo') {
    $rdr_ssql = " And tRegistrationDate > '".WEEK_DATE."'";
}
$sortby = $_REQUEST['sortby'] ?? 0;
$order = $_REQUEST['order'] ?? '';
$promocode = $_REQUEST['promocode'] ?? '';
$ord = ' ORDER BY t.iTrackServiceTripId DESC';
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
$ssql = '';
$action = $_REQUEST['action'] ?? '';
$searchCompany = $_REQUEST['searchCompany'] ?? '';
$searchDriver = $_REQUEST['searchDriver'] ?? '';
$searchRider = $_REQUEST['searchRider'] ?? '';
$serachTripNo = $_REQUEST['serachTripNo'] ?? '';
$startDate = $_REQUEST['startDate'] ?? '';
$endDate = $_REQUEST['endDate'] ?? '';
$vStatus = $_REQUEST['vStatus'] ?? '';
$eType = $_REQUEST['eType'] ?? '';
$method = $_REQUEST['method'] ?? '';
$iTripId = $_REQUEST['iTripId'] ?? '';
if ('' !== $startDate) {
    $ssql .= " AND Date(t.dStartDate) >='".$startDate."'";
}
if ('' !== $endDate) {
    $ssql .= " AND Date(t.dStartDate) <='".$endDate."'";
}

if ('' !== $serachTripNo) {
    $ssql .= " AND t.vRideNo ='".$serachTripNo."'";
}
if ('' !== $searchCompany) {
    $ssql .= " AND t.iCompanyId ='".$searchCompany."'";
}
if ('' !== $searchDriver) {
    $ssql .= " AND t.iDriverId ='".$searchDriver."'";
}
if ('' !== $searchRider) {
    // $ssql .= " AND t.iUserId ='" . $searchRider . "'";
    $ssql .= ' AND FIND_IN_SET('.$searchRider.',t.iUserIds)';
}
if ('onRide' === $vStatus) {
    $ssql .= " AND (t.eTripStatus = 'On Going Trip' OR t.eTripStatus = 'Active') AND t.eCancelled='No'";
} elseif ('cancel' === $vStatus) {
    $ssql .= " AND (t.eTripStatus = 'Cancelled')";
} elseif ('complete' === $vStatus) {
    $ssql .= " AND t.eTripStatus = 'Finished'";
} elseif ('Onboarding' === $vStatus) {
    $ssql .= " AND t.eTripStatus = 'Onboarding'";
} elseif ('Active' === $vStatus) {
    $ssql .= " AND t.eTripStatus = 'Active'";
}

$per_page = $DISPLAY_RECORD_NUMBER;
$totalData = $obj->MySQLSelect("SELECT COUNT(t.iTrackServiceTripId) AS Total FROM track_service_trips t WHERE 1=1  {$ssql}");

$total_results = $totalData[0]['Total'];
$total_pages = ceil($total_results / $per_page);
$show_page = 1;
$start = 0;
$end = $per_page;
if (isset($_GET['page'])) {
    $show_page = $_GET['page'];
    if ($show_page > 0 && $show_page <= $total_pages) {
        $start = ($show_page - 1) * $per_page;
        $end = $start + $per_page;
    }
}
$page = isset($_GET['page']) ? (int) ($_GET['page']) : 0;
$tpages = $total_pages;
if ($page <= 0) {
    $page = 1;
}
$sql = "SELECT t.iTrackServiceTripId,d.vName,d.vLastName,t.tStartLocation,t.tEndLocation,t.dStartDate,t.dAddedDate,t.eTripStatus,t.eTripType
        FROM track_service_trips t LEFT JOIN register_driver d ON d.iDriverId = t.iDriverId
        WHERE 1=1  {$ssql} {$trp_ssql} {$hotelQuery} {$ord} LIMIT {$start}, {$per_page}";
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
$vehilceTypeArr = [];
$sql_vehicle_category_table_name = getVehicleCategoryTblName();
$getVehicleTypes = $obj->MySQLSelect('SELECT iVehicleTypeId,vVehicleType_'.$default_lang.' AS vehicleType , vc.vCategory_'.$default_lang.' AS subService, vcc.vCategory_'.$default_lang.' AS Service FROM vehicle_type left join '.$sql_vehicle_category_table_name.' as vc on vehicle_type.iVehicleCategoryId = vc.iVehicleCategoryId left join '.$sql_vehicle_category_table_name.' as vcc on vc.iParentId = vcc.iVehicleCategoryId WHERE 1=1');
for ($r = 0; $r < count($getVehicleTypes); ++$r) {
    $vehilceTypeArr[$getVehicleTypes[$r]['iVehicleTypeId']] = $getVehicleTypes[$r]['vehicleType'];
    $vehilceTypeArr[$getVehicleTypes[$r]['iVehicleTypeId'].'_subService'] = $getVehicleTypes[$r]['subService'];
    $vehilceTypeArr[$getVehicleTypes[$r]['iVehicleTypeId'].'_service'] = $getVehicleTypes[$r]['Service'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title><?php echo $SITE_NAME; ?> | <?php echo $langage_lbl_admin['LBL_TRIPS_TXT_ADMIN']; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <?php include_once 'global_files.php'; ?>
    <link rel="stylesheet" href="../assets/css/modal_alert.css"/>
    <style type="text/css">
        .form-group .row {

            padding: 0;

        }

        .pending-trip {

            cursor: pointer;

            position: absolute;

            margin: 2px 0 0 5px;

        }
    </style>
</head>
<body class="padTop53 ">
<div id="wrap">
    <?php include_once 'header.php'; ?>

    <?php include_once 'left_menu.php'; ?>
    <div id="content">
        <div class="inner">
            <div id="add-hide-show-div">
                <div class="row">
                    <div class="col-lg-12">
                        <h2> <?php echo $langage_lbl_admin['LBL_TRIP']; ?> Report</h2>
                    </div>
                </div>
                <hr/>
            </div>
            <?php include 'valid_msg.php'; ?>
            <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">
                <div class="Posted-date mytrip-page">
                    <input type="hidden" name="action" value="search"/>
                    <h3>Search <?php echo $langage_lbl_admin['LBL_TRIP']; ?> ...</h3>
                    <span>
                        <a style="cursor:pointer" onClick="return todayDate('dp4', 'dp5');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Today']; ?></a>
                        <a style="cursor:pointer" onClick="return yesterdayDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Yesterday']; ?></a>
                        <a style="cursor:pointer" onClick="return currentweekDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Current_Week']; ?></a>
                        <a style="cursor:pointer" onClick="return previousweekDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Previous_Week']; ?></a>
                        <a style="cursor:pointer" onClick="return currentmonthDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Current_Month']; ?></a>
                        <a style="cursor:pointer" onClick="return previousmonthDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Previous Month']; ?></a>
                        <a style="cursor:pointer" onClick="return currentyearDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Current_Year']; ?></a>
                        <a style="cursor:pointer" onClick="return previousyearDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Previous_Year']; ?></a>
                    </span>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-lg-3">
                            <input type="text" id="dp4" name="startDate" placeholder="From Date" class="form-control" value="" readonly="" style="cursor:default; background-color: #fff"/>
                        </div>
                        <div class="col-lg-3">
                            <input type="text" id="dp5" name="endDate" placeholder="To Date" class="form-control" value="" readonly="" style="cursor:default; background-color: #fff"/>
                        </div>
                        <div class="col-lg-3">
                            <select class="form-control" name='vStatus'>
                                <option value="">All Status</option>
                                <option value="onRide" <?php
                                if ('onRide' === $vStatus) {
                                    echo 'selected';
                                }
?>>On Going <?php echo $langage_lbl_admin['LBL_RIDE_TXT_ADMIN']; ?> </option>
                                <option value="complete" <?php
if ('complete' === $vStatus) {
    echo 'selected';
}
?>>Completed
                                </option>
                                <option value="cancel" <?php
if ('cancel' === $vStatus) {
    echo 'selected';
}
?>>Cancelled
                                </option>
                                <option value="Onboarding" <?php
if ('Onboarding' === $vStatus) {
    echo 'selected';
}
?>>Onboarding
                                </option>
                                <option value="Active" <?php
if ('Active' === $vStatus) {
    echo 'selected';
}
?>>Active
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-lg-3">
                        <select class="form-control filter-by-text driver_container" name='searchDriver' data-text="Select <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?>" id="searchDriver">
                            <option value="">Select <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?></option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <select class="form-control filter-by-text" name='searchRider' data-text="Select <?php echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']; ?>" id="searchRider">
                            <option value="">Select <?php echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']; ?></option>
                        </select>
                    </div>
                    <input type="hidden" name="searchDriverHotel" id="searchDriverHotel" value="<?php echo $driverIdArrHotel; ?>">
                    <input type="hidden" name="searchRiderHotel" id="searchRiderHotel" value="<?php echo $userIdArrHotel; ?>">
                    <input type="hidden" name="trackingCompany" id="trackingCompany" value="1">

                </div>
                <div class="tripBtns001"><b>
                        <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search"/>
                        <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href = 'track_service_trips.php'"/>
                        <?php if (!empty($db_trip)) { ?>

                            <button type="button" onClick="reportExportTypes('trackingTripList')" class="export-btn001"
                                    style="float:none;">Export
                            </button>

                        <?php } ?>
                    </b>
                </div>
            </form>
            <div class="table-list">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <form class="_list_form" id="_list_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th width="50%">Pickup / Dropoff Location</th>
                                        <th><?php echo $langage_lbl_admin['LBL_COMPANY_TRIP_DRIVER']; ?></th>
                                        <th><?php echo $langage_lbl_admin['LBL_TRIP_DATE_TXT']; ?></th>
                                        <th><?php echo $langage_lbl_admin['LBL_TRACK_SERVICE_TRIP_TYPE_TXT']; ?></th>

                                        <th><?php echo $langage_lbl_admin['LBL_Status']; ?></th>
                                        <th><?php echo $langage_lbl_admin['LBL_TRACK_SERVICE_COMPANY_USER_LIST_WEB']; ?></th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
    if (!empty($db_trip)) {
        for ($i = 0; $i < count($db_trip); ++$i) {
            ?>
                                            <tr class="gradeA">
                                                <td>
                                                    <div class="lableCombineData">
                                                        <label><?php echo $langage_lbl_admin['LBL_Pick_Up']; ?></label>
                                                        <br> <span><?php echo $db_trip[$i]['tStartLocation']; ?> </span> <br>
                                                        <label><?php echo $langage_lbl_admin['LBL_DROP_AT']; ?></label>
                                                        <br> <span>  <?php if (empty($db_trip[$i]['tEndLocation'])) {
                                                            echo '---';
                                                        } else {
                                                            echo $db_trip[$i]['tEndLocation'];
                                                        } ?></span></div>
                                                </td>

                                                 <td>
                                                    <td><?php if ($userObj->hasPermission('view-providers')) { ?><a href="javascript:void(0);" onClick="show_driver_details('<?php echo $db_trip[$i]['iDriverId']; ?>')" style="text-decoration: underline;"><?php } ?><?php echo clearName($db_trip[$i]['vName'].' '.$db_trip[$i]['vLastName']); ?><?php if ($userObj->hasPermission('view-providers')) { ?></a> <?php } ?></td>
                                                </td>
                                                <td>
                                                    <span><?php if ('0000-00-00 00:00:00' === $db_trip[$i]['dStartDate']) {
                                                        echo DateTime($db_trip[$i]['dAddedDate'], '21');
                                                    } else {
                                                        echo DateTime($db_trip[$i]['dStartDate'], '21');
                                                    } ?></span>
                                                </td>

                                                <td>
                                                    <span><?php echo $db_trip[$i]['eTripType']; ?></span>
                                                </td>

                                                <td>
                                                    <span><?php echo $db_trip[$i]['eTripStatus']; ?></span>
                                                </td>
                                                <td >
                                                    <button class="btn btn-info" href="#" onclick="viewUserDetails(this);" class="btn btn-info" data-id="<?php echo $db_trip[$i]['iTrackServiceTripId']; ?>" type="button">
                                                        <?php echo $langage_lbl['LBL_VIEW_USER_DETAIL']; ?></button>
                                                </td>
                                            </tr>
                                            <?php
        }
    } else {
        ?>
                                        <tr class="gradeA">
                                            <td colspan="11"> No Records Found.</td>
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
</div>
<div class="row loding-action" id="loaderIcon" style="display:none;">
    <div align="center">
        <img src="default.gif">
    </div>
</div>
<form name="pageForm" id="pageForm" action="" method="post">
    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
    <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
    <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>">
    <input type="hidden" name="order" id="order" value="<?php echo $order; ?>">
    <input type="hidden" name="action" value="<?php echo $action; ?>">
    <input type="hidden" name="searchCompany" value="<?php echo $searchCompany; ?>">
    <input type="hidden" name="searchDriver" value="<?php echo $searchDriver; ?>">
    <input type="hidden" name="searchRider" value="<?php echo $searchRider; ?>">
    <input type="hidden" name="serachTripNo" value="<?php echo $serachTripNo; ?>">
    <input type="hidden" name="startDate" value="<?php echo $startDate; ?>">
    <input type="hidden" name="endDate" value="<?php echo $endDate; ?>">
    <input type="hidden" name="vStatus" value="<?php echo $vStatus; ?>">
    <input type="hidden" name="eType" value="<?php echo $eType; ?>">
    <input type="hidden" name="promocode" value="<?php echo $promocode; ?>">
    <input type="hidden" name="iTripId" id="iMainId01" value="">
    <input type="hidden" name="method" id="method" value="">
</form>
<div class="modal fade" id="service_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="float: left; width: 100%">
                <h4 id="servicetitle" class="pull-left">
                    <i style="margin:2px 5px 0 2px;">
                        <img src="images/icon/driver-icon.png" alt="">
                    </i> Service Details
                </h4>
                <button type="button" class="close pull-right" data-dismiss="modal">x</button>
            </div>
            <div class="modal-body" style="max-height: 450px;overflow: auto;">
                <div id="service_detail"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="detail_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                    <i style="margin:2px 5px 0 2px;">
                        <img src="images/icon/driver-icon.png" alt="">
                    </i> <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> Details
                    <button type="button" class="close" data-dismiss="modal">x</button>
                </h4>
            </div>
            <div class="modal-body" style="max-height: 450px;overflow: auto;">
                <div id="imageIcons" style="display:none">
                    <div align="center">
                        <img src="default.gif">
                        <br/> <span>Retrieving details,please Wait...</span>
                    </div>
                </div>
                <div id="driver_detail"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade " id="detail_modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                    <i style="margin:2px 5px 0 2px;">
                        <img src="images/rider-icon.png" alt="">
                    </i>
                    <?php echo $langage_lbl_admin['LBL_RIDER']; ?> Details
                    <button type="button" class="close" data-dismiss="modal">x</button>
                </h4>
            </div>
            <div class="modal-body" style="max-height: 450px;overflow: auto;">
                <div id="imageIcons1">
                    <div align="center">
                        <img src="default.gif">
                        <br/> <span>Retrieving details,please Wait...</span>
                    </div>
                </div>
                <div id="rider_detail"></div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'footer.php'; ?>
<link rel="stylesheet" href="../assets/plugins/datepicker/css/datepicker.css"/>
<script src="../assets/js/jquery-ui.min.js"></script>
<script src="../assets/plugins/datepicker/js/bootstrap-datepicker.js"></script>
<script src="../assets/js/modal_alert.js"></script>
<?php include_once 'searchfunctions.php'; ?>
<script>
    var startDate;
    var endDate;
    var typeArr = '<?php echo json_encode($vehilceTypeArr, JSON_HEX_APOS); ?>';
    $('#dp4').datepicker()
        .on('changeDate', function (ev) {
            startDate = new Date(ev.date);
            if (endDate != null) {
                if (ev.date.valueOf() < endDate.valueOf()) {
                    $('#alert').show().find('strong').text('The start date can not be greater then the end date');
                }
                else {
                    $('#alert').hide();
                    $('#startDate').text($('#dp4').data('date'));
                }
            }
            $('#dp4').datepicker('hide');
        });
    $('#dp5').datepicker()
        .on('changeDate', function (ev) {
            endDate = new Date(ev.date);
            if (startDate != null) {
                if (ev.date.valueOf() < startDate.valueOf()) {
                    $('#alert').show().find('strong').text('The end date can not be less then the start date');
                }
                else {
                    $('#alert').hide();
                    $('#endDate').text($('#dp5').data('date'));
                }
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

    function showServiceModal(elem) {
        var tripJson = JSON.parse($(elem).next("textarea").val().replace(/\s\s+/g, ' '));
        var rideNo = $(elem).attr("data-trip");
        var typeNameArr = JSON.parse(typeArr)
        var serviceHtml = "";
        var srno = 1;
        for (var g = 0; g < tripJson.length; g++) {
            serviceHtml += "<p>" + srno + ") " + typeNameArr[tripJson[g]['id']] + " (" + typeNameArr[tripJson[g]['id'] + "_service"] + " - " + typeNameArr[tripJson[g]['id'] + "_subService"] + ")<br>";
            if (tripJson[g]['eAllowQty'] == 'Yes') {
                serviceHtml += "<?php echo $langage_lbl_admin['LBL_QTY_TXT']; ?>: <b>" + [tripJson[g]['qty']] + "</b>";
            }
            serviceHtml += "</p>";
            srno++;
        }
        $("#service_detail").html(serviceHtml);
        $("#servicetitle").text("Service Details : " + rideNo);
        $("#service_modal").modal('show');
        return false;
    }

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
        }
        else {
            var action = $("#_list_form").attr('action');
            var formValus = $("#frmsearch").serialize();
            window.location.href = action + "?" + formValus;
        }
    });

    function show_driver_details(driverid) {

        $("#driver_detail").html('');

        $("#imageIcons").show();

        $("#detail_modal").modal('show');

        if (driverid != "") {

            var ajaxData = {

                'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_driver_details.php',

                'AJAX_DATA': "iDriverId=" + driverid,

                'REQUEST_DATA_TYPE': 'html'

            };

            getDataFromAjaxCall(ajaxData, function (response) {

                if (response.action == "1") {

                    var data = response.result;

                    $("#driver_detail").html(data);

                    $("#imageIcons").hide();

                }

                else {

                    console.log(response.result);

                    $("#imageIcons").hide();

                }

            });

        }

    }
    function show_rider_details(userid) {
        $("#rider_detail").html('');
        $("#imageIcons").show();
        $("#detail_modal1").modal('show');
        if (userid != "") {
            var ajaxData = {
                'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_rider_details.php',
                'AJAX_DATA': "iUserId=" + userid + "trackingCompany=1",
                'REQUEST_DATA_TYPE': 'html'
            };
            getDataFromAjaxCall(ajaxData, function (response) {
                if (response.action == "1") {
                    var data = response.result;
                    $("#rider_detail").html(response.result);
                    $("#imageIcons1").hide();
                }
                else {
                    console.log(response.result);
                    $("#imageIcons1").hide();
                }
            });
        }
    }

    function resetOnlyTripStatus(iAdminId) {
        $('#is_resetTrip_modal_trip').modal('show');
        $(".action_modal_submit").unbind().click(function () {
            var action = $("#pageForm").attr('action');
            var page = $("#pageId").val();
            $("#pageId01").val(page);
            $("#iMainId01").val(iAdminId);
            $("#method").val('reset');
            var formValus = $("#pageForm").serialize();
            window.location.href = action + "?" + formValus;
        });
    }

    function viewUserDetails(elem) {
        $('#loaderIcon').show();
        var ajaxData = {
            'URL': '<?php echo $tconfig['tsite_url']; ?>ajax-track_compnay_list.php',
            'AJAX_DATA': {tracking_company_trip_user_list: 1, tripId: $(elem).data('id')},
            'REQUEST_DATA_TYPE': 'json'
        };
        getDataFromAjaxCall(ajaxData, function (response) {
            $('#loaderIcon').hide();
            if (response.action == "1") {
                var dataHtml2 = response.result;
                if (dataHtml2.Action == 1) {
                    if (dataHtml2.message != "") {
                        console.log('9090');
                        show_alert("<?php echo $langage_lbl['LBL_TRACK_SERVICE_COMPANY_USER']; ?> Details", dataHtml2.message, "", "", "<?php echo $langage_lbl['LBL_BTN_OK_TXT']; ?>", undefined, true, true, true);
                    }
                }
                else {
                    show_alert("", dataHtml2.message, "", "", "<?php echo $langage_lbl['LBL_BTN_OK_TXT']; ?>");
                }
            }
            else {
                // console.log(response.result);
            }
        });
    }
</script>
</body>
</html>