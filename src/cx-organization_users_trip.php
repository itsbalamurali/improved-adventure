<?
include_once('common.php');

$script = "Organization-Users-Trips";
$tbl_name = 'register_driver';
$AUTH_OBJ->checkMemberAuthentication();
$abc = 'organization';
$url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
setRole($abc, $url);
$action = (isset($_REQUEST['action']) ? $_REQUEST['action'] : '');

$ssql = '';

if ($action != '') {
    $startDate = $_REQUEST['startDate'];
    $endDate = $_REQUEST['endDate'];
    $searchRider = isset($_REQUEST['searchRider']) ? $_REQUEST['searchRider'] : '';
    $dateRange = isset($_REQUEST['dateRange']) ? $_REQUEST['dateRange'] : '';    

    if ($startDate != '') {
        $ssql .= " AND Date(t.tTripRequestDate) >='" . $startDate . "'";
    }
    if ($endDate != '') {
        $ssql .= " AND Date(t.tTripRequestDate) <='" . $endDate . "'";
    }
    if ($searchRider != '') {
        $ssql .= " AND t.iUserId ='" . $searchRider . "'";
    }
}

$sql = "SELECT t.*,u.vName, u.vLastName,t.ePaymentBy,t.tEndDate, t.tTripRequestDate,t.fWalletDebit, t.vRideNo, t.iActive,d.vAvgRating, t.fOutStandingAmount, t.iFare, d.iDriverId, t.tSaddress, t.tDaddress,t.fTripGenerateFare, t.iRentalPackageId,t.eType, t.eHailTrip, t.fHotelCommision, d.vName AS name, d.vLastName AS lname,t.eCarType, t.vTimezone, t.iTripId,vt.vVehicleType_" . $_SESSION['sess_lang'] . " as vVehicleType,vt.vRentalAlias_" . $_SESSION['sess_lang'] . " as vRentalVehicleTypeName, t.fCommision,t.fTripGenerateFare,t.fTipPrice, t.fCancellationFare, t.eCancelled,t.vTripPaymentMode FROM register_driver d RIGHT JOIN trips t ON d.iDriverId = t.iDriverId LEFT JOIN vehicle_type vt ON vt.iVehicleTypeId = t.iVehicleTypeId LEFT JOIN  register_user u ON t.iUserId = u.iUserId WHERE t.iOrganizationId = '" . $_SESSION['sess_iUserId'] . "'" . $ssql . " ORDER BY t.iTripId DESC";
$db_trip = $obj->MySQLSelect($sql);


/* echo "<pre>";
  print_r($db_trip);
  exit(); */

$sql = "SELECT * FROM  organization WHERE iOrganizationId='" . $_SESSION['sess_iUserId'] . "'";
$dbOrganization = $obj->MySQLSelect($sql);

$sql = "SELECT fThresholdAmount, Ratio, vName, vSymbol FROM currency WHERE vName='" . $dbOrganization[0]['vCurrency'] . "'";
$dbOrganizationRatio = $obj->MySQLSelect($sql);

$orgCursymbol = $dbOrganizationRatio[0]['vSymbol'];
$orgCurRatio = $dbOrganizationRatio[0]['Ratio'];
$orgCurName = $dbOrganizationRatio[0]['vName'];
//$tripcurthholsamt=$dbOrganizationRatio[0]['fThresholdAmount'];

$sql = "SELECT UP.iUserId,CONCAT(RU.vName,' ',RU.vLastName) AS riderName,RU.vEmail AS vEmail FROM user_profile UP LEFT JOIN register_user RU ON UP.iUserId=RU.iUserId  WHERE UP.eStatus != 'Deleted' AND iOrganizationId='" . $_SESSION['sess_iOrganizationId'] . "' order by RU.vName";

$db_rider = $obj->MySQLSelect($sql);

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
if ($host_system == 'cubetaxiplus') {
    $canceled_icon = "canceled-invoice.png";
    $invoice_icon = "driver-view-icon.png";
} else if ($host_system == 'ufxforall') {
    $canceled_icon = "ufxforall-canceled-invoice.png";
    $invoice_icon = "ufxforall-driver-view-icon.png";
} else if ($host_system == 'uberridedelivery4') {
    $canceled_icon = "ride-delivery-canceled-invoice.png";
    $invoice_icon = "ride-delivery-driver-view-icon.png";
} else if ($host_system == 'uberdelivery4') {
    $canceled_icon = "delivery-canceled-invoice.png";
    $invoice_icon = "delivery-driver-view-icon.png";
} else {
    $invoice_icon = "driver-view-icon.png";
    $canceled_icon = "canceled-invoice.png";
}
?>
<!DOCTYPE html>
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title><?= $SITE_NAME ?> | <?= $langage_lbl['LBL_HEADER_TRIPS_TXT']; ?></title>
        <!-- Default Top Script and css -->
        <?php include_once("top/top_script.php"); ?>

        <!-- <link href="assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" /> -->
        <!-- End: Default Top Script and css-->
    </head>
<body id="wrapper">
    <!-- home page -->
    <!-- home page -->
    <?php if($template!='taxishark'){?>
    <div id="main-uber-page">
    <?php } ?>
        <!-- Left Menu -->
    <?php include_once("top/left_menu.php");?>
    <!-- End: Left Menu-->
        <!-- Top Menu -->
        <?php include_once("top/header_topbar.php");?>
        <!-- End: Top Menu-->
        <!-- First Section -->
        <?php include_once("top/header.php");?>
        <!-- End: First Section -->
<section class="profile-section my-trips">
    <div class="profile-section-inner">
        <div class="profile-caption">
            <div class="page-heading">
                <h1><?php echo $langage_lbl['LBL_ORGANIZATION_USER_TRIPS_WEB']; ?></h1>
            </div>
            
            <form class="tabledata-filter-block filter-form" name="search"  method="post" onSubmit="return checkvalid()">
                <input type="hidden" name="action" value="search" />
                <div class="filters-column mobile-full">
                    <label><?= $langage_lbl['LBL_COMPANY_TRIP_SEARCH_RIDES_POSTED_BY_TIME_PERIOD']; ?></label>
                    <select id="timeSelect" name="dateRange">
                                    <option value=""><?= $langage_lbl['LBL_SELECT_TXT'] ?></option>
                                    <option value="today" <?php if($dateRange == 'today'){echo 'selected'; }?> ><?= $langage_lbl['LBL_COMPANY_TRIP_Today']; ?></option>
                                    <option value="yesterday" <?php if($dateRange == 'yesterday'){echo 'selected'; }?> ><?= $langage_lbl['LBL_COMPANY_TRIP_Yesterday']; ?></option>
                                    <option value="currentWeek" <?php if($dateRange == 'currentWeek'){echo 'selected'; }?> ><?= $langage_lbl['LBL_COMPANY_TRIP_Current_Week']; ?></option>
                                    <option value="previousWeek" <?php if($dateRange == 'previousWeek'){echo 'selected'; }?> ><?= $langage_lbl['LBL_COMPANY_TRIP_Previous_Week']; ?></option>
                                    <option value="currentMonth" <?php if($dateRange == 'currentMonth'){echo 'selected'; }?> ><?= $langage_lbl['LBL_COMPANY_TRIP_Current_Month']; ?></option>
                                    <option value="previousMonth" <?php if($dateRange == 'previousMonth'){echo 'selected'; }?> ><?= $langage_lbl['LBL_COMPANY_TRIP_Previous Month']; ?></option>
                                    <option value="currentYear" <?php if($dateRange == 'currentYear'){echo 'selected'; }?> ><?= $langage_lbl['LBL_COMAPNY_TRIP_Current_Year']; ?></option>
                                    <option value="previousYear" <?php if($dateRange == 'previousYear'){echo 'selected'; }?> ><?= $langage_lbl['LBL_Previous_Year']; ?></option>

                    </select>
                </div>
                <div class="filters-column mobile-half">
                    <label><?= $langage_lbl['LBL_MYTRIP_FROM_DATE'] ?></label>
                    <input type="text" id="dp4" name="startDate" placeholder="<?= $langage_lbl['LBL_MYTRIP_FROM_DATE']; ?>" class="form-control" value="" readonly style="cursor:default; background-color: #fff"/>
                    <i class="icon-cal" id="from-date"></i>
                </div>
                <div class="filters-column mobile-half">
                    <label><?= $langage_lbl['LBL_MYTRIP_TO_DATE'] ?></label>
                    <input type="text" id="dp5" name="endDate" placeholder="<?= $langage_lbl['LBL_MYTRIP_TO_DATE']; ?>" class="form-control" value="" readonly style="cursor:default; background-color: #fff"/>
                    <i class="icon-cal" id="to-date"></i>
                </div>

                <div class="filters-column mobile-full">
                    <select class="form-control filter-by-text select-side" name ='searchRider' data-text="Select <?php echo $langage_lbl['LBL_PASSANGER_TXT_ADMIN']; ?>" id="searchRider">
                        <option value=""><?php echo $langage_lbl['LBL_SELECT_TXT']." ".$langage_lbl['LBL_PASSANGER_TXT_ADMIN']; ?></option>

                        <?php foreach ($db_rider as $dbr) { ?>
                            <option value="<?php echo $dbr['iUserId']; ?>" <?php
                            if ($searchRider == $dbr['iUserId']) {
                                echo "selected";
                            }
                            ?>><?php echo clearName($dbr['riderName']); ?> - ( <?php echo clearEmail($dbr['vEmail']); ?> )</option>
                                <?php } ?>
                    </select>                     
                </div>
                <div class="filters-column mobile-full">
                    <button class="driver-trip-btn"><?= $langage_lbl['LBL_COMPANY_TRIP_Search']; ?></button>
                    <!-- <button onClick="reset();" class="driver-trip-btn"><?= $langage_lbl['LBL_MYTRIP_RESET']; ?></button> -->
                    <a href="users-trip" class="gen-btn"><?= $langage_lbl['LBL_MYTRIP_RESET']; ?></a>
                </div>
            </form>

        </div>
    </div>
</section>
<section class="profile-earning">
    <div class="profile-earning-inner">
    <div class="table-holder">
        <table id="my-trips-data" class="ui celled table custom-table" style="width:100%">
            <thead>
                <tr>
                    <th><?= $langage_lbl['LBL_TRIP_JOB_TYPE_FRONT']; ?></th>
                    <th width="17%"><?= $langage_lbl['LBL_MYTRIP_RIDE_NO_TXT']; ?></th>
                    <th><?= $langage_lbl['LBL_Pick_Up']; ?></th>
                    <th><?= $langage_lbl['LBL_COMPANY_TRIP_DRIVER']; ?> / <?= $langage_lbl['LBL_COMPANY_TRIP_RIDER']; ?></th>
                    
                    <th><?= $langage_lbl['LBL_COMPANY_TRIP_Trip_Date']; ?></th>
                    <th><?= $langage_lbl['LBL_COMPANY_TRIP_FARE_TXT']; ?></th>
                    <!--<th><?= $langage_lbl['LBL_COMPANY_TRIP_Car_Type']; ?></th>-->
                    <th><?= $langage_lbl['LBL_PAYMENT_BY_TXT'] ?></th>
                    <th><?= $langage_lbl['LBL_COMPANY_TRIP_View_Invoice']; ?></th>
                </tr>
            </thead>
                <tbody>
                    <?
                    for ($i = 0; $i < count($db_trip); $i++) {
                        $eType = $db_trip[$i]['eType'];
                        //echo "<pre>";
                        //print_r($db_trip);
                        //die;
                        //$link_page = "organization_invoice.php";
                        $poolTxt = "";
                        if ($db_trip[$i]['ePoolRide'] == "Yes") {
                             $poolTxt = " (".$langage_lbl['LBL_POOL'].")";
                        }
                        $link_page = "invoice.php";
                        if ($eType == 'Ride') {
                            $trip_type = $langage_lbl['LBL_RIDE'];
                        } else if ($eType == 'UberX') {
                            $trip_type = $langage_lbl['LBL_OTHER'];
                        } else if ($eType == 'Multi-Delivery') {
                            $trip_type = $langage_lbl['LBL_MULTI_DELIVERY'];
                            $link_page = "invoice_multi_delivery.php";
                        } else {
                            $trip_type = $langage_lbl['LBL_DELIVERY'];
                        }
                        $trip_type .= $poolTxt;
                        $systemTimeZone = date_default_timezone_get();
                        if ($db_trip[$i]['tTripRequestDate'] != "" && $db_trip[$i]['vTimeZone'] != "") {
                            $dBookingDate = converToTz($db_trip[$i]['tTripRequestDate'], $db_trip[$i]['vTimeZone'], $systemTimeZone);
                        } else {
                            $dBookingDate = $db_trip[$i]['tTripRequestDate'];
                        }
                        ?>
                        <tr class="gradeA">

                            <?php if ($APP_TYPE != 'UberX' && $APP_TYPE != 'Delivery') { ?>
                                <td >
                                    <?
                                    if ($db_trip[$i]['eHailTrip'] == "Yes" && $db_trip[$i]['iRentalPackageId'] > 0) {
                                        echo $langage_lbl['LBL_RENTAL_CATEGORY_TXT']." ".$trip_type . "<br/> ( ".$langage_lbl['LBL_HAIL']." )";
                                    } else if ($db_trip[$i]['iRentalPackageId'] > 0) {
                                        echo $langage_lbl['LBL_RENTAL_CATEGORY_TXT']." ".$trip_type;
                                    } else if ($db_trip[$i]['eHailTrip'] == "Yes") {
                                        echo $langage_lbl['LBL_HAIL']." " . $trip_type;
                                    } else {
                                        if (!empty($db_trip[$i]['iFromStationId']) && !empty($db_trip[$i]['iToStationId'])) {
                                            $trip_type = 'Fly';
                                        }
                                        echo $trip_type;
                                    }

                                    /*                                      if($db_trip[$i]['eHailTrip'] != "Yes"){
                                      echo $trip_type;
                                      }else{
                                      echo "Hail ".$trip_type;
                                      } */
                                    ?></td>
                            <?php } ?>

                            <td align="center"><?= $db_trip[$i]['vRideNo']; ?></td>

                            <?php if ($APP_TYPE == 'UberX') { ?>
                                 <td width="25%"><div class="lableCombineData"><label><?= $langage_lbl['LBL_Pick_Up']; ?></label><span><?= $db_trip[$i]['tSaddress']; ?></span></div></td>
                                <?php
                            } else {
                                if (!empty($db_trip[$i]['tDaddress'])) {
                                    ?>
                                    <td width="25%"><div class="lableCombineData">
                                        <label><?= $langage_lbl['LBL_Pick_Up']; ?></label><span><?= $db_trip[$i]['tSaddress'] ?> </span>
                                        <label><?= $langage_lbl['LBL_DROP_AT'] ?></label><span> <?= $db_trip[$i]['tDaddress']; ?></span></div>
                                    </td>
                                <?php } else { ?>
                                    <td width="25%"><div class="lableCombineData"> <label><?= $langage_lbl['LBL_Pick_Up']; ?></label><span><?= $db_trip[$i]['tSaddress']; ?></span></div></td>
                                    <?php
                                }
                            }
                            ?> 
                            <td>
                                <div class="lableCombineData">
                                    <label><?= $langage_lbl['LBL_PROVIDER'] ?></label><span><?= clearName($db_trip[$i]['name'] . " " . $db_trip[$i]['lname']); ?></span>
                                    <label><?= $langage_lbl['LBL_RIDER'] ?></label><span><?= clearName($db_trip[$i]['vName'] . " " . $db_trip[$i]['vLastName']); ?></span>
                                </div>
                                

                            <td data-order="<?= $db_trip[$i]['iTripId'] ?>"><?= date('d-M-Y', strtotime($dBookingDate)); ?></td>
                            <td align="center" style="white-space:  nowrap;">

                                <?php
                                /* if($db_trip[$i]['fCancellationFare'] > 0 || ($db_trip[$i]['iActive'] == "Canceled" && $db_trip[$i]['fWalletDebit'] > 0)){

                                  $total_main_price = $db_trip[$i]['fCancellationFare'];
                                  } else { */

                                /* $total_main_price = ($db_trip[$i]['fTripGenerateFare'] + $db_trip[$i]['fTipPrice'] - $db_trip[$i]['fCommision']- $db_trip[$i]['fTax2']-$db_trip[$i]['fTax1'] - $db_trip[$i]['fOutStandingAmount']- $db_trip[$i]['fHotelCommision']); */

                                $total_main_price = $db_trip[$i]['fTripGenerateFare'] - $db_trip[$i]['fDiscount']-$db_trip[$i]['fWalletDebit'];
                                $fare = trip_currency_payment($total_main_price, $db_trip[$i]['fRatio_' . $orgCurName]);
                                //}
                                ?>
                                <?php //trip_currency($total_main_price); ?>

                                <?php //echo $orgCursymbol . ' ' . $fare; 
                                echo formateNumAsPerCurrency($fare,$orgCurName);
                                ?>

                            </td>

                                <!--<td align="center">
                            <?php
                            if ($db_trip[$i]['iRentalPackageId'] > 0) {
                                echo $db_trip[$i]['vRentalVehicleTypeName'];
                            } else {
                                echo $db_trip[$i]['vVehicleType'];
                            }
                            ?>-->
                            <!-- <?= $db_trip[$i]['vVehicleType']; ?> 
                        </td>-->

                            <td>
                            <?php
                            if($db_trip[$i]['vTripPaymentMode']=='Cash') {
                                $tripPaymentMode = $langage_lbl['LBL_CASH_TXT'];
                            } else if($db_trip[$i]['vTripPaymentMode']=='Card') {
                                $tripPaymentMode = $langage_lbl['LBL_CARD'];
                            } else if($db_trip[$i]['vTripPaymentMode']=='Organization') {
                                $tripPaymentMode = $langage_lbl['LBL_ORGANIZATION'];
                            } else {
                                $tripPaymentMode = $db_trip[$i]['vTripPaymentMode'];
                            }
                            if($db_trip[$i]['ePaymentBy']=='Passenger') {
                                $ePaymentBy = $langage_lbl['LBL_PASSANGER_TXT_ADMIN'];
                            } else if($db_trip[$i]['ePaymentBy']=='Organization') {
                                $ePaymentBy = $langage_lbl['LBL_ORGANIZATION'];
                            } else {
                                $ePaymentBy = $db_trip[$i]['ePaymentBy'];
                            }
                            
                            ?>
                                <?php 
                                // echo $db_trip[$i]['ePaymentBy'];
                                echo ($db_trip[$i]['ePaymentBy'] == "Passenger") ? $langage_lbl['LBL_PASSANGER_TXT_ADMIN'] . " (" . $tripPaymentMode . ")" : $ePaymentBy; ?>
                                    
                                </td>

                            <?php if ($db_trip[$i]['iActive'] == 'Canceled' && $db_trip[$i]['fTripGenerateFare'] <= 0) { ?>
                                <td class="center">
                                    <img src="assets/img/<?php echo $canceled_icon; ?>" title="<?= $langage_lbl['LBL_MYTRIP_CANCELED_TXT']; ?>">
                                </td>
                            <?php } else if (($db_trip[$i]['iActive'] == 'Finished' && $db_trip[$i]['eCancelled'] == "Yes") || ($db_trip[$i]['iActive'] == 'Canceled' && $db_trip[$i]['fTripGenerateFare'] > 0)) { ?>

                                <td align="center" width="10%">
                                    <a target = "_blank" href="<?= $link_page ?>?iTripId=<?= base64_encode(base64_encode($db_trip[$i]['iTripId'])) ?>">
                                        <img alt="" src="assets/img/<?php echo $invoice_icon; ?>">
                                    </a>
                                    <div style="font-size: 12px;"><?= $langage_lbl['LBL_CANCELED_TXT'] ?></div>
                                </td>

                            <? } else { ?>  
                                <td align="center" width="10%">
                                    <a target = "_blank" href="<?= $link_page ?>?iTripId=<?= base64_encode(base64_encode($db_trip[$i]['iTripId'])) ?>">
                                        <img alt="" src="assets/img/<?php echo $invoice_icon; ?>">
                                    </a>
                                </td>
                            <?php } ?>
                        </tr>
                    <? } ?>     
                </tbody>
        </table>
    </div>
    </div>
</section>
    <!-- home page end-->
    <!-- footer part -->
    <?php include_once('footer/footer_home.php');?>

    <div style="clear:both;"></div>
     <?php if($template!='taxishark'){?>
     </div>
     <?php } ?>
    <!-- footer part end -->
<!-- Footer Script -->
<?php include_once('top/footer_script.php');?>
<script src="assets/js/jquery-ui.min.js"></script>
<script src="assets/plugins/dataTables/jquery.dataTables.js"></script>


<script type="text/javascript">
      if($('#my-trips-data').length > 0) {
        $('#my-trips-data').DataTable({"oLanguage": langData});
    }
    



    $(document).on('change','#timeSelect',function(e){
        e.preventDefault();
        
        var timeSelect = $(this).val();
        
        if(timeSelect == 'today'){ todayDate('dp4', 'dp5') }
        if(timeSelect == 'yesterday'){yesterdayDate('dFDate', 'dTDate')}
        if(timeSelect == 'currentWeek'){currentweekDate('dFDate', 'dTDate')}
        if(timeSelect == 'previousWeek'){previousweekDate('dFDate', 'dTDate')}
        if(timeSelect == 'currentMonth'){currentmonthDate('dFDate', 'dTDate')}
        if(timeSelect == 'previousMonth'){previousmonthDate('dFDate', 'dTDate')}
        if(timeSelect == 'currentYear'){currentyearDate('dFDate', 'dTDate')}
        if(timeSelect == 'previousYear'){previousyearDate('dFDate', 'dTDate')}

    });




</script>

        <script type="text/javascript">
                                        $(document).ready(function () {
                                            $("#dp4").datepicker({
                                                dateFormat: "yy-mm-dd",
                                                changeYear: true,
                                                changeMonth: true,
                                                yearRange: "-100:+10"
                                            });
                                            $("#dp5").datepicker({
                                                dateFormat: "yy-mm-dd",
                                                changeYear: true,
                                                changeMonth: true,
                                                yearRange: "-100:+10"
                                            });
                                            if ('<?= $startDate ?>' != '') {
                                                $("#dp4").val('<?= $startDate ?>');
                                                $("#dp4").datepicker('refresh');
                                            }
                                            if ('<?= $endDate ?>' != '') {
                                                $("#dp5").val('<?= $endDate; ?>');
                                                $("#dp5").datepicker('refresh');
                                            }
<?php if ($APP_TYPE == 'UberX' || $APP_TYPE == 'Delivery') { ?>
                                                $('#dataTables-example').DataTable({
                                                    "oLanguage": langData,
                                                    "order": [[4, "desc"]]
                                                });
<?php } else { ?>
                                                $('#dataTables-example').DataTable({
                                                    "oLanguage": langData,
                                                    "order": [[5, "desc"]]
                                                });
<?php } ?>
                                            //$('#dataTables-example').dataTable();
                                            // formInit();
                                        });

                                        function reset_frm() {

                                            //document.getElementById("action").value = "";
                                            // $('#search')[0].reset();
                                            // $("#searchRider").val('');
                                            // location.reload();
                                            window.location.href = window.location.href;
                                        }
                                        function todayDate()
                                        {
                                            $("#dp4").val('<?= $Today; ?>');
                                            $("#dp5").val('<?= $Today; ?>');
                                        }
                                        function yesterdayDate()
                                        {
                                            $("#dp4").val('<?= $Yesterday; ?>');
                                            $("#dp5").val('<?= $Yesterday; ?>');
                                            $("#dp4").datepicker('refresh');
                                            $("#dp5").datepicker('refresh');
                                        }
                                        function currentweekDate(dt, df)
                                        {
                                            $("#dp4").val('<?= $monday; ?>');
                                            $("#dp5").val('<?= $sunday; ?>');
                                            $("#dp4").datepicker('refresh');
                                            $("#dp5").datepicker('refresh');
                                        }
                                        function previousweekDate(dt, df)
                                        {
                                            $("#dp4").val('<?= $Pmonday; ?>');
                                            $("#dp5").val('<?= $Psunday; ?>');
                                            $("#dp4").datepicker('refresh');
                                            $("#dp5").datepicker('refresh');
                                        }
                                        function currentmonthDate(dt, df)
                                        {
                                            $("#dp4").val('<?= $currmonthFDate; ?>');
                                            $("#dp5").val('<?= $currmonthTDate; ?>');
                                            $("#dp4").datepicker('refresh');
                                            $("#dp5").datepicker('refresh');
                                        }
                                        function previousmonthDate(dt, df)
                                        {
                                            $("#dp4").val('<?= $prevmonthFDate; ?>');
                                            $("#dp5").val('<?= $prevmonthTDate; ?>');
                                            $("#dp4").datepicker('refresh');
                                            $("#dp5").datepicker('refresh');
                                        }
                                        function currentyearDate(dt, df)
                                        {
                                            $("#dp4").val('<?= $curryearFDate; ?>');
                                            $("#dp5").val('<?= $curryearTDate; ?>');
                                            $("#dp4").datepicker('refresh');
                                            $("#dp5").datepicker('refresh');
                                        }
                                        function previousyearDate(dt, df)
                                        {
                                            $("#dp4").val('<?= $prevyearFDate; ?>');
                                            $("#dp5").val('<?= $prevyearTDate; ?>');
                                            $("#dp4").datepicker('refresh');
                                            $("#dp5").datepicker('refresh');
                                        }
                                        function checkvalid() {
                                            if ($("#dp5").val() < $("#dp4").val()) {
                                                //bootbox.alert("<h4>From date should be lesser than To date.</h4>");
                                                bootbox.dialog({
                                                    message: "<h4><?php echo addslashes($langage_lbl['LBL_FROM_TO_DATE_ERROR_MSG']); ?></h4>",
                                                    buttons: {
                                                        danger: {
                                                            label: "<?= $langage_lbl['LBL_OK'] ?>",
                                                            className: "btn-danger"
                                                        }
                                                    }
                                                });
                                                return false;
                                            }
                                        }
        </script>

        <script type="text/javascript">
            $(document).ready(function () {
                $("[name='dataTables-example_length']").each(function () {
                    $(this).wrap("<em class='select-wrapper'></em>");
                    $(this).after("<em class='holder'></em>");
                });
                $("[name='dataTables-example_length']").change(function () {
                    var selectedOption = $(this).find(":selected").text();
                    $(this).next(".holder").text(selectedOption);
                }).trigger('change');
            })
        </script>

<!-- End: Footer Script -->
</body>
</html>
