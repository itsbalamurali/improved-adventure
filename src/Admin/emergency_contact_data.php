<?php

include_once '../common.php';

if (!$userObj->hasPermission('view-sos-request-report')) {
    $userObj->redirect();
}

$script = 'emergency_contact_data';

$tableName = 'emergency_contact_data';

// Start Sorting

$isAjax = $_REQUEST['isAjax'] ?? 'No';

$usertype = $_REQUEST['usertype'] ?? '';

$tripid = $_REQUEST['tripid'] ?? '';

$message = $_REQUEST['message'] ?? '';

if ('Yes' === $isAjax && !empty($usertype) && !empty($tripid)) {
    // $selectcontactData = $obj->MySQLSelect("SELECT ue.vName, ue.vPhone, ecd.tMessage FROM `emergency_contact_data` ecd

    // LEFT JOIN user_emergency_contact ue ON ue.iEmergencyId = ecd.`iEmergencyId`

    // WHERE ecd.iTripId = $tripid and ecd.vFromUserType = '$usertype' group by ecd.iEmergencyId");

    $selectcontactData = $obj->MySQLSelect("SELECT vContactName as vName, vContactPhone as vPhone, tMessage FROM emergency_contact_data WHERE iTripId = {$tripid} and vFromUserType = '{$usertype}' group by iEmergencyId");

    if (!empty($message)) {
        echo clearGeneralText($selectcontactData[0]['tMessage']);

        exit;
    }

    $data = "<table class='table table-striped table-bordered table-hover'>";

    if (!empty($selectcontactData)) {
        $data .= '<tr><th>Name</th><th>Phone</th></tr>';

        foreach ($selectcontactData as $key => $value) {
            $data .= '<tr><td>'.clearName($value['vName']).'</td><td>'.clearPhone($value['vPhone']).'</td></tr>';
        }
    } else {
        $data .= "<tr><td colspan='2'>No Records Found</td></tr>";
    }

    $data .= '</table>';

    echo $data;

    exit;
}

$sortby = $_REQUEST['sortby'] ?? 0;

$order = $_REQUEST['order'] ?? '';

$ord = ' ORDER BY iEmergencyContactId DESC';

if (1 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY vFirstname ASC';
    } else {
        $ord = ' ORDER BY vFirstname DESC';
    }
}

if (2 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY vEmail ASC';
    } else {
        $ord = ' ORDER BY vEmail DESC';
    }
}

// End Sorting

$cmp_ssql = '';

// Start Search Parameters

$searchDriver = $_REQUEST['searchDriver'] ?? '';

$searchRider = $_REQUEST['searchRider'] ?? '';

$serachTripNo = $_REQUEST['serachTripNo'] ?? '';

$startDate = $_REQUEST['startDate'] ?? '';

$endDate = $_REQUEST['endDate'] ?? '';

$ssql = '';

if ('' !== $startDate) {
    $ssql .= " AND Date(ecd.tRequestDate) >='".$startDate."'";
}

if ('' !== $endDate) {
    $ssql .= " AND Date(ecd.tRequestDate) <='".$endDate."'";
}

if ('' !== $serachTripNo) {
    $ssql .= " AND t.vRideNo ='".$serachTripNo."'";
}

if ('' !== $searchRider && '' !== $searchDriver) {
    $ssql .= " AND ((ecd.iUserId ='".$searchRider."' AND ecd.vFromUserType = 'Passenger') OR (ecd.iDriverId ='".$searchDriver."' AND ecd.vFromUserType = 'Driver'))";
} elseif ('' !== $searchDriver) {
    $ssql .= " AND (ecd.iDriverId ='".$searchDriver."' AND ecd.vFromUserType = 'Driver')";
} elseif ('' !== $searchRider) {
    $ssql .= " AND (ecd.iUserId ='".$searchRider."' AND ecd.vFromUserType = 'Passenger')";
}

// End Search Parameters

// Pagination Start

$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page

// $sql = "SELECT COUNT(iEmergencyContactId) AS Total FROM $tableName AS c WHERE 1 = 1 $ssql";

$sql = "SELECT iEmergencyContactId FROM `emergency_contact_data` ecd

LEFT JOIN register_user u ON u.iUserId = ecd.`iUserId`

LEFT JOIN register_driver d ON d.iDriverId = ecd.`iDriverId`

WHERE 1 = 1 {$ssql} GROUP BY ecd.iEmergencyId";

$totalData = $obj->MySQLSelect($sql);

$totalData[0]['Total'] = count($totalData);

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

$sql = "SELECT CONCAT(u.vName,' ',u.vLastName) as userName, CONCAT(d.vName,' ',d.vLastName) as driverName, u.vEmail as useremail, d.vEmail as driveremail,CONCAT('(+',u.vPhoneCode,') ',u.vPhone) as userphone, CONCAT('(+',d.vCode,')',d.vPhone) as driverphone, ecd.iEmergencyId,ecd.vFromUserType,ecd.iTripId,t.vRideNo,t.eType,ecd.tRequestDate,ecd.iUserId,ecd.iDriverId FROM `emergency_contact_data` ecd

LEFT JOIN trips t ON t.iTripId = ecd.`iTripId`

LEFT JOIN register_user u ON u.iUserId = ecd.`iUserId`

LEFT JOIN register_driver d ON d.iDriverId = ecd.`iDriverId`

WHERE 1 = 1 {$ssql} GROUP BY ecd.iEmergencyId {$ord} LIMIT {$start}, {$per_page}";

$data_drv = $obj->MySQLSelect($sql);

$endRecord = count($data_drv);

$var_filter = '';

foreach ($_REQUEST as $key => $val) {
    if ('tpages' !== $key && 'page' !== $key) {
        $var_filter .= "&{$key}=".stripslashes($val);
    }
}

$reload = $_SERVER['PHP_SELF'].'?tpages='.$tpages.$var_filter;

$systemTimeZone = date_default_timezone_get();

$def_timezone = $obj->MySQLSelect("SELECT vTimeZone FROM country WHERE vCountryCode = '".$DEFAULT_COUNTRY_CODE_WEB."'");

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

    <title><?php echo $SITE_NAME; ?> | SOS Requests</title>

    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <?php include_once 'global_files.php'; ?>

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

                            <h2>SOS Requests</h2>

                        </div>

                    </div>

                    <hr />

                </div>

                <?php include 'valid_msg.php'; ?>

                <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">

                    <div class="Posted-date mytrip-page payment-report">

                        <input type="hidden" name="action" value="search">

                        <h3>Search...</h3>
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

                            <input type="text" id="dp4" name="startDate" placeholder="From Date" class="form-control" value="" readonly="" style="cursor:default; background-color: #fff">

                            <input type="text" id="dp5" name="endDate" placeholder="To Date" class="form-control" value="" readonly="" style="cursor:default; background-color: #fff">

                            <div class="col-lg-3 select001">

                                <select class="form-control filter-by-text" name = 'searchRider' data-text="Select <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?>" id="searchRider">

                                    <option value="">Select <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?></option>

                                </select>

                            </div>

                            <div class="col-lg-3 select001">

                                <select class="form-control filter-by-text driver_container" name = 'searchDriver' data-text="Select <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?>" id="searchDriver">

                                    <option value="">Select <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?></option>

                                </select>

                            </div>

                        </span>

                    </div>

                    <div class="row payment-report payment-report1 payment-report2">

                        <div class="col-lg-2">

                            <input type="text" id="serachTripNo" name="serachTripNo" placeholder="<?php echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']; ?> Number" class="form-control search-trip001" value="<?php echo $serachTripNo; ?>"/>

                        </div>

                    </div>

                    <div class="tripBtns001"><b>

                        <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />

                        <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href = 'emergency_contact_data.php'"/>

                    </b></div>

                </form>

                <div class="table-list">

                    <div class="row">

                        <div class="col-lg-12">

                            <div style="clear:both;"></div>

                            <div class="table-responsive">

                                <form class="_list_form" id="_list_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

                                    <table class="table table-striped table-bordered table-hover">

                                        <thead>

                                            <tr>

                                                <th width="10%" style="text-align: center;">Booking No</th>

                                                <th width="30%">Contact Details</th>

                                                <th width="10%" style="text-align: center;">Emergency Contacts</th>

                                                <th width="10%" style="text-align: center;">Message</th>

                                                <th width="15%" style="text-align: center;">Date</th>

                                            </tr>

                                        </thead>

                                        <tbody>

                                            <?php

                                            if (!empty($data_drv)) {
                                                for ($i = 0; $i < count($data_drv); ++$i) {
                                                    $link_page = 'invoice.php';

                                                    if ('Multi-Delivery' === $data_drv[$i]['eType']) {
                                                        $link_page = 'invoice_multi_delivery.php';
                                                    }

                                                    ?>

                                                    <tr class="gradeA">

                                                        <td align="center"><!--<a href="invoice.php?iTripId=<?php echo $data_drv[$i]['iTripId']; ?>"><?php echo $data_drv[$i]['vRideNo']; ?></a>-->

                                                            <a href="<?php echo $link_page; ?>?iTripId=<?php echo $data_drv[$i]['iTripId']; ?>" target="_blank"><?php echo $data_drv[$i]['vRideNo']; ?></a>

                                                        </td>

                                                        <td>

                                                            <?php if ('Passenger' === $data_drv[$i]['vFromUserType']) {
                                                                if ($userObj->hasPermission('view-users')) { ?>

                                                                    <a href="javascript:void(0);" onClick="show_rider_details('<?php echo $data_drv[$i]['iUserId']; ?>')" style="text-decoration: underline;"><?php echo clearName($data_drv[$i]['userName']); ?></a>

                                                                    <?php echo ' ('.$langage_lbl_admin['LBL_RIDER'].')';
                                                                } else {
                                                                    echo clearName($data_drv[$i]['userName']).' ('.$langage_lbl_admin['LBL_RIDER'].')';
                                                                }

                                                                echo '<br>'.clearEmail($data_drv[$i]['useremail']).'<br>'.clearPhone($data_drv[$i]['userphone']);
                                                            } elseif ('Driver' === $data_drv[$i]['vFromUserType']) {
                                                                if ($userObj->hasPermission('view-providers')) { ?>

                                                                    <a href="javascript:void(0);" onClick="show_driver_details('<?php echo $data_drv[$i]['iDriverId']; ?>')" style="text-decoration: underline;"><?php echo clearName($data_drv[$i]['driverName']); ?></a><?php echo ' ('.$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].')';
                                                                } else {
                                                                    echo clearName($data_drv[$i]['driverName']).' ('.$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].')';
                                                                }

                                                                echo '<br>'.clearEmail($data_drv[$i]['driveremail']).'<br>'.clearPhone($data_drv[$i]['driverphone']);
                                                            } ?>

                                                        </td>

                                                        <td align="center"><a href="javascript:void(0);" onClick="show_details('<?php echo $data_drv[$i]['vFromUserType']; ?>','<?php echo $data_drv[$i]['iTripId']; ?>');" style="text-decoration: underline;">View</a></td>

                                                        <td align="center"><a href="javascript:void(0);" onClick="show_details('<?php echo $data_drv[$i]['vFromUserType']; ?>','<?php echo $data_drv[$i]['iTripId']; ?>','message');" style="text-decoration: underline;">View Message</a></td>

                                                        <td align="center"><?php $tRequestDate = converToTz($data_drv[$i]['tRequestDate'], $def_timezone[0]['vTimeZone'], $systemTimeZone);

                                                    echo date('d M, Y h:i A', strtotime($tRequestDate));

                                                    ?></td>

                                                </tr>

                                            <?php }
                                                } else { ?>

                                                <tr class="gradeA">

                                                    <td colspan="8"> No Records Found.</td>

                                                </tr>

                                            <?php } ?>

                                        </tbody>

                                    </table>

                                </form>

                                <?php include 'pagination_n.php'; ?>

                            </div>

                        </div> <!--TABLE-END-->

                    </div>

                </div>

                <div class="admin-notes">

                    <h4>Note:</h4>

                    <ul>

                        <li>This will list all kind of emergency/SOS requests sent from User to their emergency contact list during <?php echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']; ?>.</li>

                        <li>Users may ask for help in case of emergency while <?php echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']; ?> is in progress.</li>

                        <!-- <li>You can communicate with the users via mentioned contact details externally.</li> -->

                    </ul>

                </div>

            </div>

        </div>

        <!--END PAGE CONTENT -->

    </div>

    <!--END MAIN WRAPPER -->

    <div class="modal fade " id="detail_modal_user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >

        <div class="modal-dialog" >

            <div class="modal-content">

                <div class="modal-header">

                    <h4>

                        <!--<i aria-hidden="true" class="fa fa-building-o" style="margin:2px 5px 0 2px;"></i>-->

                        <i style="margin:2px 5px 0 2px;"><img src="images/rider-icon.png" alt=""></i>

                        <?php echo $langage_lbl_admin['LBL_RIDER']; ?> Details

                        <button type="button" class="close" data-dismiss="modal">x</button>

                    </h4>

                </div>

                <div class="modal-body" style="max-height: 450px;overflow: auto;">

                    <div id="imageIcons_user">

                        <div align="center">

                            <img src="default.gif"><br/>

                            <span>Retrieving details,please Wait...</span>

                        </div>

                    </div>

                    <div id="rider_detail" ></div>

                </div>

            </div>

        </div>

    </div>

    <div  class="modal fade" id="detail_modal_driver" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >

        <div class="modal-dialog" >

            <div class="modal-content">

                <div class="modal-header">

                    <h4>

                        <i style="margin:2px 5px 0 2px;"><img src="images/icon/driver-icon.png" alt=""></i>

                        <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> Details

                        <button type="button" class="close" data-dismiss="modal">x</button>

                    </h4>

                </div>

                <div class="modal-body" style="max-height: 450px;overflow: auto;">

                    <div id="imageIcons_driver" style="display:none">

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

    <form name="pageForm" id="pageForm" action="action/emergency_contact_data.php" method="post" >

        <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">

        <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">

        <input type="hidden" name="iCompanyId" id="iMainId01" value="" >

        <input type="hidden" name="eStatus" id="eStatus" value="<?php echo $eStatus; ?>" >

        <input type="hidden" name="status" id="status01" value="" >

        <input type="hidden" name="statusVal" id="statusVal" value="" >

        <input type="hidden" name="option" value="<?php echo $option; ?>" >

        <input type="hidden" name="keyword" value="<?php echo $keyword; ?>" >

        <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>" >

        <input type="hidden" name="order" id="order" value="<?php echo $order; ?>" >

        <input type="hidden" name="method" id="method" value="" >

    </form>

<?php include_once 'footer.php'; ?>

<script src="../assets/js/modal_alert.js"></script>

<link rel="stylesheet" href="../assets/css/modal_alert.css" />

<link rel="stylesheet" href="../assets/plugins/datepicker/css/datepicker.css" />

<script src="../assets/js/jquery-ui.min.js"></script>

<script src="../assets/plugins/datepicker/js/bootstrap-datepicker.js"></script>

<?php include_once 'searchfunctions.php'; ?>

<script>

    var startDate;

    var endDate;

    $('#dp4').datepicker()

    .on('changeDate', function (ev) {

        startDate = new Date(ev.date);

        if (endDate != null) {

            if (ev.date.valueOf() < endDate.valueOf()) {

                $('#alert').show().find('strong').text('The start date can not be greater then the end date');

            } else {

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

            } else {

                $('#alert').hide();

                $('#endDate').text($('#dp5').data('date'));

            }

        }

        $('#dp5').datepicker('hide');

    });

    $(document).ready(function () {

        $('#usertype_options').hide();

        $('#option').each(function () {

            if (this.value == 'eUserType') {

                $('#usertype_options').show();

                $('.searchform').hide();

            }

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

    $('#option').change(function () {

        if ($('#option').val() == 'eUserType') {

            $('#usertype_options').show();

            $("input[name=keyword]").val("");

            $('.searchform').hide();

        } else {

            $('#usertype_options').hide();

            $("#estatus_value").val("");

            $('.searchform').show();

        }

    });

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

    $('.entypo-export').click(function (e) {

        e.stopPropagation();

        var $this = $(this).parent().find('div');

        $(".openHoverAction-class div").not($this).removeClass('active');

        $this.toggleClass('active');

    });



    $(document).on("click", function (e) {

        if ($(e.target).is(".openHoverAction-class,.show-moreOptions,.entypo-export") === false) {

            $(".show-moreOptions").removeClass("active");

        }

    });



    function show_details(usertype,tripid,message="") {

        if (message!="") {

            var ajaxData = {

                'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>emergency_contact_data.php',

                'AJAX_DATA': {"isAjax": 'Yes', "usertype": usertype, "tripid": tripid,"message": message},

                'REQUEST_DATA_TYPE': 'html'

            };

            getDataFromAjaxCall(ajaxData, function(response) {

                if(response.action == "1") {

                    var data = response.result;

                    show_alert("Message",data,"Ok","","",function (btn_id) {}, true,true,true);

                }

                else {

                    console.log(response.result);

                }

            });

        } else {


            var ajaxData = {

                'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>emergency_contact_data.php',

                'AJAX_DATA': {"isAjax": 'Yes', "usertype": usertype, "tripid": tripid},

                'REQUEST_DATA_TYPE': 'html'

            };

            getDataFromAjaxCall(ajaxData, function(response) {

                if(response.action == "1") {

                    var data = response.result;

                    show_alert("Contact Details",data,"Ok","","",function (btn_id) {}, true,true,true);

                }

                else {

                    console.log(response.result);

                }

            });

        }

    }

    function show_driver_details(driverid) {

        $("#driver_detail").html('');

        $("#imageIcons_driver").show();

        $("#detail_modal_driver").modal('show');

        if (driverid != "") {

            var ajaxData = {

                'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_driver_details.php',

                'AJAX_DATA': {"iDriverId": driverid,"editTrip":"No"},

                'REQUEST_DATA_TYPE': 'html'

            };

            getDataFromAjaxCall(ajaxData, function(response) {

                if(response.action == "1") {

                    var data = response.result;

                    $("#driver_detail").html(data);

                    $("#imageIcons_driver").hide();

                }

                else {

                    console.log(response.result);

                    $("#imageIcons_driver").hide();

                }

            });

        }

    }

    function show_rider_details(userid) {

        $("#rider_detail").html('');

        $("#imageIcons_user").show();

        $("#detail_modal_user").modal('show');

        if (userid != "") {

            var ajaxData = {

                'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_rider_details.php',

                'AJAX_DATA': {"iUserId": userid,"editTrip":"No"},

                'REQUEST_DATA_TYPE': 'html'

            };

            getDataFromAjaxCall(ajaxData, function(response) {

                if(response.action == "1") {

                    var data = response.result;

                    $("#rider_detail").html(data);

                    $("#imageIcons_user").hide();

                } else {

                    $("#imageIcons_user").hide();

                }

            });

        }

    }

function todayDate() {
    $("#dp4").val('<?php echo $Today; ?>');
    $("#dp5").val('<?php echo $Today; ?>');
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
</script>

    </body>

    <!-- END BODY-->

    </html>