<?php
include_once '../common.php';
$tbl_name = 'register_driver';
if (!$userObj->hasPermission('manage-cancelled-trip-job-report')) {
    $userObj->redirect();
}
$script = 'CancelledTrips';
/*
$sql = "SELECT iDriverId,CONCAT(vName,' ',vLastName) AS driverName,vEmail from register_driver WHERE eStatus != 'Deleted' order by vName";
$db_drivers = $obj->MySQLSelect($sql);*/
// data for select fields
// Start Sorting
$sortby = $_REQUEST['sortby'] ?? 0;
$order = $_REQUEST['order'] ?? '';
$ord = ' ORDER BY t.iTripId DESC';
if (1 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY t.tTripRequestDate ASC';
    } else {
        $ord = ' ORDER BY t.tTripRequestDate DESC';
    }
}
if (2 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY t.eCancelledBy ASC';
    } else {
        $ord = ' ORDER BY t.eCancelledBy DESC';
    }
}
if (3 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY t.vCancelReason ASC';
    } else {
        $ord = ' ORDER BY t.vCancelReason DESC';
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
        $ord = ' ORDER BY t.eType ASC';
    } else {
        $ord = ' ORDER BY t.eType DESC';
    }
}
// End Sorting
// Start Search Parameters
$ssql = '';
$action = $_REQUEST['action'] ?? '';
$iDriverId = $_REQUEST['iDriverId'] ?? '';
$startDate = $_REQUEST['startDate'] ?? '';
$serachTripNo = $_REQUEST['serachTripNo'] ?? '';
$endDate = $_REQUEST['endDate'] ?? '';
$vStatus = $_REQUEST['vStatus'] ?? '';
$eType = $_REQUEST['eType'] ?? '';
if ('search' === $action) {
    if ('' !== $startDate) {
        $ssql .= " AND Date(t.tTripRequestDate) >='".$startDate."'";
    }
    if ('' !== $endDate) {
        $ssql .= " AND Date(t.tTripRequestDate) <='".$endDate."'";
    }
    if ('' !== $iDriverId) {
        $ssql .= " AND t.iDriverId ='".$iDriverId."'";
    }
    if ('' !== $serachTripNo) {
        $ssql .= " AND t.vRideNo ='".$serachTripNo."'";
    }
    if ('' !== $eType) {
        if ('Yes' === $ENABLE_MULTI_VIEW_IN_SINGLE_DELIVERY) {
            if ('Deliver' === $eType) {
                $ssql .= " AND ((t.eType ='Multi-Delivery' AND (SELECT COUNT(iTripDeliveryLocationId) FROM trips_delivery_locations as tdl WHERE tdl.iTripId=t.iTripId)=1) OR t.eType ='Deliver')";
            } elseif ('Multi-Delivery' === $eType) {
                $ssql .= " AND (t.eType ='Multi-Delivery' AND (SELECT COUNT(iTripDeliveryLocationId) FROM trips_delivery_locations as tdl WHERE tdl.iTripId=t.iTripId)>1)";
            }
        } else {
            if ('Fly' === $eType) {
                $ssql .= ' AND t.iFromStationId > 0 AND t.iToStationId > 0';
            } elseif ('Ride' === $eType) {
                $ssql .= " AND t.eType ='".$eType."' AND t.iRentalPackageId = 0 AND t.eHailTrip = 'No' AND t.iFromStationId = 0 AND t.iToStationId = 0 ";
            } elseif ('RentalRide' === $eType) {
                $ssql .= " AND t.eType ='Ride' AND t.iRentalPackageId > 0";
            } elseif ('HailRide' === $eType) {
                $ssql .= " AND t.eType ='Ride' AND t.eHailTrip = 'Yes'";
            } elseif ('Pool' === $eType) {
                $ssql .= " AND t.eType ='Ride' AND t.ePoolRide = 'Yes'";
            } else {
                $ssql .= " AND t.eType ='".$eType."' ";
            }
        }
    }
    // echo $ssql;die;
}
$locations_where = '';
if (count($userObj->locations) > 0) {
    $locations = implode(', ', $userObj->locations);
    $ssql .= " AND EXISTS(SELECT * FROM vehicle_type WHERE t.iVehicleTypeId = vehicle_type.iVehicleTypeId AND vehicle_type.iLocationid IN(-1, {$locations}))";
}
$ufxEnable = $MODULES_OBJ->isUberXFeatureAvailable() ? 'Yes' : 'No'; // add function to modules availibility
$rideEnable = $MODULES_OBJ->isRideFeatureAvailable() ? 'Yes' : 'No';
$deliveryEnable = $MODULES_OBJ->isDeliveryFeatureAvailable() ? 'Yes' : 'No';
$deliverallEnable = $MODULES_OBJ->isDeliverAllFeatureAvailable() ? 'Yes' : 'No';
if ('Yes' !== $ufxEnable) {
    $ssql .= " AND t.eType != 'UberX'";
}
if (!$MODULES_OBJ->isAirFlightModuleAvailable()) {
    $ssql .= " AND t.iFromStationId = '0' AND t.iToStationId = '0'";
}
if ('Yes' !== $rideEnable) {
    $ssql .= " AND t.eType != 'Ride'";
}
if ('Yes' !== $deliveryEnable) {
    $ssql .= " AND t.eType != 'Deliver' AND t.eType != 'Multi-Delivery'";
}
$trp_ssql = '';
if (SITE_TYPE === 'Demo') {
    $trp_ssql = " And t.tTripRequestDate > '".WEEK_DATE."'";
}
// $ufxEnable = $MODULES_OBJ->isUfxFeatureAvailable();
// if($ufxEnable != "Yes") {
//  $ssql .= " AND eType != 'UberX'";
// }
// Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT COUNT(t.iTripId) AS Total FROM trips t LEFT JOIN register_driver d ON d.iDriverId = t.iDriverId
    WHERE 1=1 AND (t.iActive='Canceled' OR t.eCancelled='yes') AND t.eSystem = 'General' {$ssql} {$trp_ssql}";
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
// commented on 15-05-2020  and added cr.vTitle_EN as cancel_reason_title to get reason
// $sql = "SELECT t.iFromStationId,t.iToStationId,t.ePoolRide,t.tTripRequestDate ,t.tEndDate,t.eCancelled,t.vCancelReason,t.vCancelComment,t.eHailTrip,d.iDriverId, t.tSaddress,t.vRideNo,t.eCancelledBy,t.tDaddress, t.fWalletDebit,t.eCarType,t.iTripId,t.iActive, t.eType ,CONCAT(d.vName,' ',d.vLastName) AS dName,t.fCancellationFare FROM trips t LEFT JOIN register_driver d ON d.iDriverId = t.iDriverId WHERE 1=1 AND (t.iActive='Canceled' OR t.eCancelled='yes') AND t.eSystem = 'General' $ssql $trp_ssql $ord LIMIT $start, $per_page";
$sql = "SELECT t.ePoolRide,t.tTripRequestDate ,t.tEndDate,t.eCancelled,t.vCancelReason,t.vCancelComment,t.eHailTrip,d.iDriverId, t.tSaddress,t.vRideNo,t.eCancelledBy,t.tDaddress, t.fWalletDebit,t.eCarType,t.iTripId,t.iActive, t.eType ,CONCAT(d.vName,' ',d.vLastName) AS dName,t.fCancellationFare,cr.vTitle_EN as cancel_reason_title FROM trips t LEFT JOIN register_driver d ON d.iDriverId = t.iDriverId LEFT JOIN cancel_reason cr ON cr.iCancelReasonId = t.iCancelReasonId WHERE 1=1 AND (t.iActive='Canceled' OR t.eCancelled='yes') AND t.eSystem = 'General' {$ssql} {$trp_ssql} {$ord} LIMIT {$start}, {$per_page}";
$db_trip = $obj->MySQLSelect($sql);
// echo "<pre>";
// print_r($db_trip);die;
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
$monday = date('Y-m-d', strtotime('monday this week'));
$sunday = date('Y-m-d', strtotime('sunday this week'));
$Pmonday = date('Y-m-d', strtotime('monday this week -1 week'));
$Psunday = date('Y-m-d', strtotime('sunday this week -1 week'));
?>
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title><?php echo $SITE_NAME; ?> | Cancelled <?php echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']; ?> </title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta content="" name="keywords"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <?php include_once 'global_files.php'; ?>
    <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet"/>
</head>
<!-- END  HEAD-->
<!-- BEGIN BODY-->
<body class="padTop53 ">
<!-- MAIN WRAPPER -->
<div id="wrap">
    <?php include_once 'header.php'; ?>
    <?php include_once 'left_menu.php'; ?>
    <!--PAGE CONTENT -->
    <div id="content">
        <div class="inner">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Cancelled <?php echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']; ?></h2>
                </div>
            </div>
            <hr/>
            <div class="">
                <div class="table-list">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <?php include 'valid_msg.php'; ?>
                                <form name="frmsearch" id="frmsearch" action="javascript:void(0);" id="cancel_trip">
                                    <div class="Posted-date mytrip-page mytrip-page-select payment-report">
                                        <input type="hidden" name="action" value="search"/>
                                        <h3><?php echo $langage_lbl_admin['LBL_MYTRIP_SEARCH_RIDES_POSTED_BY_DATE']; ?></h3>
                                        <span>
                                                    <a onClick="return todayDate('dp4', 'dp5');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Today']; ?></a>
                                                    <a onClick="return yesterdayDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Yesterday']; ?></a>
                                                    <a onClick="return currentweekDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Current_Week']; ?></a>
                                                    <a onClick="return previousweekDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Previous_Week']; ?></a>
                                                    <a onClick="return currentmonthDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Current_Month']; ?></a>
                                                    <a onClick="return previousmonthDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Previous Month']; ?></a>
                                                    <a onClick="return currentyearDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Current_Year']; ?></a>
                                                    <a onClick="return previousyearDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Previous_Year']; ?></a>
                                                </span>
                                        <span>
                                                    <!-- changed by me -->
                                                    <input type="text" id="dp4" name="startDate" placeholder="From Date"
                                                           class="form-control" value="" readonly=""
                                                           style="cursor:default; background-color: #fff"/>
                                                    <input type="text" id="dp5" name="endDate" placeholder="To Date"
                                                           class="form-control" value="" readonly=""
                                                           style="cursor:default; background-color: #fff"/>
                                                    <div class="col-lg-2 select001">
                                                        <select class="form-control filter-by-text" name='iDriverId'
                                                                data-text="Select <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?>"
                                                                id="searchDriver">
                                                            <option value="">Select <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?></option>
                                                            <!-- <?php foreach ($db_drivers as $dbd) { ?>
                                                                <option value="<?php echo $dbd['iDriverId']; ?>" <?php
                                                                if ($iDriverId === $dbd['iDriverId']) {
                                                                    echo 'selected';
                                                                }
                                                                ?>><?php echo clearName($dbd['driverName']); ?> - ( <?php echo clearEmail($dbd['vEmail']); ?> )</option>
                                                                    <?php } ?> -->
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <input type="text" id="serachTripNo" name="serachTripNo"
                                                               placeholder="<?php echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']; ?> Number"
                                                               class="form-control search-trip001"
                                                               value="<?php echo $serachTripNo; ?>"/>
                                                    </div>
                                                </span>
                                    </div>
                                    <?php if ('Ride-Delivery-UberX' === $APP_TYPE || 'Ride-Delivery' === $APP_TYPE) { ?>
                                        <div class="Posted-date mytrip-page mytrip-page-select payment-report">
                                            <div class="col-lg-2">
                                                <select class="form-control" name='eType'>
                                                    <option value="">Service Type</option>
                                                    <?php if ('Yes' === $rideEnable) { ?>
                                                        <option value="Ride" <?php
                                                        if ('Ride' === $eType) {
                                                            echo 'selected';
                                                        }
                                                        ?>><?php echo $langage_lbl_admin['LBL_RIDE_TXT_ADMIN_SEARCH']; ?> </option>
                                                        <?php if ('Yes' === $ENABLE_HAIL_RIDES && 'Delivery' !== $APP_TYPE) { ?>
                                                            <option value="HailRide" <?php
                                                            if ('HailRide' === $eType) {
                                                                echo 'selected';
                                                            }
                                                            ?>>
                                                                Hail <?php echo $langage_lbl_admin['LBL_RIDE_TXT_ADMIN_SEARCH']; ?> </option>
                                                        <?php }
                                                        if (ENABLE_RENTAL_OPTION === 'Yes' && 'Delivery' !== $APP_TYPE) { ?>
                                                            <option value="RentalRide" <?php
                                                            if ('RentalRide' === $eType) {
                                                                echo 'selected';
                                                            }
                                                            ?>>
                                                                Taxi Rental
                                                            </option>
                                                        <?php }
                                                        }
                                        if ('Yes' === $deliveryEnable) { ?>
                                                        <option value="Deliver" <?php
                                            if ('Deliver' === $eType) {
                                                echo 'selected';
                                            }
                                            ?>>Delivery
                                                        </option>
                                                        <?php if (ENABLE_MULTI_DELIVERY === 'Yes') { ?>
                                                            <option value="Multi-Delivery" <?php
                                                if ('Multi-Delivery' === $eType) {
                                                    echo 'selected';
                                                }
                                                            ?>>Multi-Delivery
                                                            </option>
                                                        <?php }
                                                        }
                                        if (('UberX' === $APP_TYPE || 'Ride-Delivery-UberX' === $APP_TYPE) && 'Yes' === $ufxEnable) { ?>
                                                        <option value="UberX" <?php
                                            if ('UberX' === $eType) {
                                                echo 'selected';
                                            }
                                            ?>>Other Services
                                                        </option>
                                                    <?php }
                                        if ('Yes' === $rideEnable && 'SHARK' === $PACKAGE_TYPE) { ?>
                                                        <option value="Pool" <?php
                                            if ('Pool' === $eType) {
                                                echo 'selected';
                                            }
                                            ?>><?php echo 'Taxi '.$langage_lbl_admin['LBL_POOL']; ?> </option>
                                                    <?php }
                                        if ($MODULES_OBJ->isAirFlightModuleAvailable()) { ?>
                                                        <option value="Fly" <?php
                                            if ('Fly' === $eType) {
                                                echo 'selected';
                                            }
                                            ?>><?php echo $langage_lbl_admin['LBL_HEADER_RDU_FLY_RIDE']; ?> </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="tripBtns001">
                                        <b>
                                            <input type="submit" value="Search" class="btnalt button11" id="Search"
                                                   name="Search" title="Search"/>
                                            <input type="button" value="Reset" class="btnalt button11"
                                                   onClick="window.location.href = 'cancelled_trip.php'"/>
                                            <?php if (!empty($db_trip) && $userObj->hasPermission('export-cancelled-trip-job-report')) { ?>
                                                <button type="button" onClick="reportExportTypes('cancelled_trip')"
                                                        class="export-btn001">Export
                                                </button>
                                            <?php } ?>
                                        </b>
                                    </div>
                                </form>
                                <form name="_list_form" class="_list_form" id="_list_form" method="post"
                                      action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                    <table class="table table-striped table-bordered table-hover"
                                           id="dataTables-example">
                                        <thead>
                                        <tr>
                                            <?php if ('UberX' !== $APP_TYPE && 'Delivery' !== $APP_TYPE) { ?>
                                                <th width="10%" class="align-left">
                                                    <a href="javascript:void(0);" onClick="Redirect(5,<?php
                                                    if ('5' === $sortby) {
                                                        echo $order;
                                                    } else {
                                                        ?>0<?php } ?>)"><?php echo $langage_lbl_admin['LBL_TRIP_TYPE_TXT_ADMIN']; ?> <?php
                                                        if (5 === $sortby) {
                                                            if (0 === $order) {
                                                                ?>
                                                                <i class="fa fa-sort-amount-asc"
                                                                   aria-hidden="true"></i> <?php } else { ?>
                                                                <i class="fa fa-sort-amount-desc"
                                                                   aria-hidden="true"></i><?php
                                                                   }
                                                        } else {
                                                            ?>
                                                            <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                                </th>
                                            <?php } ?>
                                            <th>
                                                <a href="javascript:void(0);" onClick="Redirect(1,<?php
                                                if ('1' === $sortby) {
                                                    echo $order;
                                                } else {
                                                    ?>0<?php } ?>)"><?php echo $langage_lbl_admin['LBL_TRIP_DATE_ADMIN']; ?> <?php
                                                    if (1 === $sortby) {
                                                        if (0 === $order) {
                                                            ?>
                                                            <i class="fa fa-sort-amount-asc"
                                                               aria-hidden="true"></i> <?php } else { ?>
                                                            <i class="fa fa-sort-amount-desc"
                                                               aria-hidden="true"></i><?php
                                                               }
                                                    } else {
                                                        ?>
                                                        <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                            </th>
                                            <th>
                                                <a href="javascript:void(0);" onClick="Redirect(2,<?php
                                                if ('2' === $sortby) {
                                                    echo $order;
                                                } else {
                                                    ?>0<?php } ?>)">Cancel By <?php
                                                    if (2 === $sortby) {
                                                        if (0 === $order) {
                                                            ?>
                                                            <i class="fa fa-sort-amount-asc"
                                                               aria-hidden="true"></i> <?php } else { ?>
                                                            <i class="fa fa-sort-amount-desc"
                                                               aria-hidden="true"></i><?php
                                                               }
                                                    } else {
                                                        ?>
                                                        <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                            </th>
                                            <th width="12%">
                                                <a href="javascript:void(0);" onClick="Redirect(3,<?php
                                                if ('3' === $sortby) {
                                                    echo $order;
                                                } else {
                                                    ?>0<?php } ?>)">Cancel Reason <?php
                                                    if (3 === $sortby) {
                                                        if (0 === $order) {
                                                            ?>
                                                            <i class="fa fa-sort-amount-asc"
                                                               aria-hidden="true"></i> <?php } else { ?>
                                                            <i class="fa fa-sort-amount-desc"
                                                               aria-hidden="true"></i><?php
                                                               }
                                                    } else {
                                                        ?>
                                                        <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                            </th>
                                            <th width="12%">
                                                <a href="javascript:void(0);" onClick="Redirect(4,<?php
                                                if ('4' === $sortby) {
                                                    echo $order;
                                                } else {
                                                    ?>0<?php } ?>)"><?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?>
                                                    Name <?php
                                                    if (4 === $sortby) {
                                                        if (0 === $order) {
                                                            ?>
                                                            <i class="fa fa-sort-amount-asc"
                                                               aria-hidden="true"></i> <?php } else { ?>
                                                            <i class="fa fa-sort-amount-desc"
                                                               aria-hidden="true"></i><?php
                                                               }
                                                    } else {
                                                        ?>
                                                        <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                            </th>
                                            <th><?php echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']; ?> No</th>
                                            <th>Address</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        if (!empty($db_trip)) {
                                            for ($i = 0; $i < count($db_trip); ++$i) {
                                                $eTypenew = $db_trip[$i]['eType'];
                                                if ('Ride' === $eTypenew) {
                                                    $trip_type = 'Ride';
                                                } elseif ('UberX' === $eTypenew) {
                                                    $trip_type = 'Other Services';
                                                } elseif ('Multi-Delivery' === $eTypenew) {
                                                    $trip_type = 'Multi-Delivery';
                                                } else {
                                                    $trip_type = 'Delivery';
                                                }
                                                if ('Multi-Delivery' === $eTypenew && 'Yes' === $ENABLE_MULTI_VIEW_IN_SINGLE_DELIVERY) {
                                                    $db_deliveryloc = $obj->MySQLSelect('SELECT * FROM `trips_delivery_locations` WHERE `iTripId` = '.$db_trip[$i]['iTripId']);
                                                    if (1 === count($db_deliveryloc)) {
                                                        $trip_type = 'Delivery';
                                                    }
                                                }
                                                $poolTxt = '';
                                                if ('Yes' === $db_trip[$i]['ePoolRide']) {
                                                    $poolTxt = ' (Pool)';
                                                }
                                                if (!empty($db_trip[$i]['iFromStationId']) && !empty($db_trip[$i]['iToStationId'])) {
                                                    $trip_type = 'Fly';
                                                }
                                                $trip_type .= $poolTxt;
                                                // $vCancelReason = $db_trip[$i]['vCancelReason'];
                                                // $trip_cancel = ($vCancelReason != '') ? $vCancelReason : '--';
                                                // commented on 15-05-2020
                                                // $vCancelReason = $db_trip[$i]['vCancelReason'];
                                                // $trip_cancel = ($vCancelReason != '') ? $vCancelReason : '--';
                                                $vCancelReason = $db_trip[$i]['cancel_reason_title'];
                                                $trip_cancel = ('' !== $vCancelReason) ? $vCancelReason : $db_trip[$i]['vCancelReason'];
                                                $trip_cancel = ('' !== $trip_cancel) ? $trip_cancel : '---';
                                                $eCancelled = $db_trip[$i]['eCancelled'];
                                                // $CanceledBy = ($eCancelled == 'Yes' && $vCancelReason != '' )? 'Driver': 'Passenger';
                                                $CanceledBy = !empty($db_trip[$i]['eCancelledBy']) ? $db_trip[$i]['eCancelledBy'] : $langage_lbl_admin['LBL_ADMIN'];
                                                if ('Passenger' === $db_trip[$i]['eCancelledBy']) {
                                                    $CanceledBy = $langage_lbl_admin['LBL_RIDER'];
                                                } elseif ('Driver' === $db_trip[$i]['eCancelledBy']) {
                                                    $CanceledBy = $langage_lbl_admin['LBL_DRIVER'];
                                                }
                                                ?>
                                                <tr class="gradeA">
                                                    <?php if ('UberX' !== $APP_TYPE && 'Delivery' !== $APP_TYPE) { ?>
                                                        <td align="left">
                                                            <?php
                                                            if ('Yes' !== $db_trip[$i]['eHailTrip']) {
                                                                echo $trip_type;
                                                            } else {
                                                                echo $trip_type.' ( Hail )';
                                                            }
                                                        ?>
                                                        </td>
                                                    <?php } ?>
                                                    <td><?php echo DateTime($db_trip[$i]['tTripRequestDate'], 'no'); ?></td>
                                                    <td align="left">
                                                        <?php echo $CanceledBy; ?>
                                                    </td>
                                                    <td align="left">
                                                        <?php echo $trip_cancel; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($userObj->hasPermission('view-providers')) { ?>
                                                            <a href="javascript:void(0);"  onClick="show_driver_details('<?php echo $db_trip[$i]['iDriverId']; ?>')" style="text-decoration: underline;">
                                                        <?php } ?>
                                                           <?php echo clearName($db_trip[$i]['dName']); ?>
                                                        <?php if ($userObj->hasPermission('view-providers')) { ?>
                                                           </a>
                                                        <?php } ?>
                                                     </td>

                                                    <td>
                                                        <?php if ('Driver' === $CanceledBy && 'Finished' === $db_trip[$i]['iActive']) { ?>
                                                            <?php if ($userObj->hasPermission('view-invoice')) { ?>
                                                                <a href="javascript:void(0);"
                                                                   onclick='window.open("invoice.php?iTripId=<?php echo $db_trip[$i]['iTripId']; ?>", "_blank")'
                                                                   ;><?php echo $db_trip[$i]['vRideNo']; ?></a>
                                                            <?php } else { ?>
                                                                <?php echo $db_trip[$i]['vRideNo']; ?>
                                                            <?php } ?>
                                                        <?php } elseif ($db_trip[$i]['fCancellationFare'] > 0 & $userObj->hasPermission('view-invoice')) { ?>
                                                            <a href="javascript:void(0);"
                                                               onclick='window.open("invoice.php?iTripId=<?php echo $db_trip[$i]['iTripId']; ?>", "_blank")'
                                                               ;><?php echo $db_trip[$i]['vRideNo']; ?></a>
                                                        <?php } else { ?>
                                                            <?php echo $db_trip[$i]['vRideNo']; ?>
                                                        <?php } ?>
                                                    </td>
                                                    <td width="30%"
                                                        data-order="<?php echo $db_trip[$i]['iTripId']; ?>"><?php echo $db_trip[$i]['tSaddress'].' -> '.$db_trip[$i]['tDaddress']; ?></td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            ?>
                                            <tr class="gradeA">
                                                <td colspan="7" style="text-align:center;"> No Records Found.</td>
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
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<form name="pageForm" id="pageForm" action="" method="post">
    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
    <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
    <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>">
    <input type="hidden" name="order" id="order" value="<?php echo $order; ?>">
    <input type="hidden" name="action" value="<?php echo $action; ?>">
    <input type="hidden" name="serachTripNo" value="<?php echo $serachTripNo; ?>">
    <input type="hidden" name="iDriverId" value="<?php echo $iDriverId; ?>">
    <input type="hidden" name="startDate" value="<?php echo $startDate; ?>">
    <input type="hidden" name="endDate" value="<?php echo $endDate; ?>">
    <input type="hidden" name="vStatus" value="<?php echo $vStatus; ?>">
    <input type="hidden" name="eType" value="<?php echo $eType; ?>">
    <input type="hidden" name="method" id="method" value="">
</form>
<div class="modal fade" id="detail_modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                    <i style="margin:2px 5px 0 2px;">
                        <img src="images/icon/driver-icon.png" alt="">
                    </i>
                    <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> Details
                    <button type="button" class="close" data-dismiss="modal">x</button>
                </h4>
            </div>
            <div class="modal-body" style="max-height: 450px;overflow: auto;">
                <div id="imageIcons1">
                    <div align="center">
                        <img src="default.gif">
                        <br/>
                        <span>Retrieving details,please Wait...</span>
                    </div>
                </div>
                <div id="driver_detail"></div>
            </div>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>
<link rel="stylesheet" href="../assets/plugins/datepicker/css/datepicker.css"/>
<link rel="stylesheet" href="css/select2/select2.min.css"/>
<script src="js/plugins/select2.min.js"></script>
<script src="../assets/plugins/datepicker/js/bootstrap-datepicker.js"></script>
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

        if ('<?php echo $startDate; ?>' != '') {
            $("#dp4").val('<?php echo $startDate; ?>');
            $("#dp4").datepicker('update', '<?php echo $startDate; ?>');
        }
        if ('<?php echo $endDate; ?>' != '') {
            $("#dp5").datepicker('update', '<?php echo $endDate; ?>');
            $("#dp5").val('<?php echo $endDate; ?>');
        }

    });

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
    });*/
    $('body').on('keyup', '.select2-search__field', function () {
        $(".select2-container .select2-dropdown .select2-results .select2-results__options").addClass("hideoptions");
        if ($(".select2-results__options").is(".select2-results__message")) {
            $(".select2-container .select2-dropdown .select2-results .select2-results__options").removeClass("hideoptions");
        }
    });

    function formatDesign(item) {
        //console.log(item.text);
        /*if(item.text == 'Searching…'){
            console.log('item1');
           $(".select2-container .select2-dropdown .select2-results .select2-results__options").removeClass("hideoptions");
        }*/
        $(".select2-container .select2-dropdown .select2-results .select2-results__options").removeClass("hideoptions");
        if (!item.id) {
            return item.text;
        }
        //console.log(item);
        var selectionText = item.text.split("--");
        if (selectionText[2] != null && selectionText[1] != null) {
            var $returnString = $('<span>' + selectionText[0] + '</br>' + selectionText[1] + "</br>" + selectionText[2] + '</span>');
        } else if (selectionText[2] == null && selectionText[1] != null) {
            var $returnString = $('<span>' + selectionText[0] + '</br>' + selectionText[1] + '</span>');
        } else if (selectionText[2] != null && selectionText[1] == null) {
            var $returnString = $('<span>' + selectionText[0] + '</br>' + selectionText[2] + '</span>');
        }
        //$(".select2-container .select2-dropdown .select2-results .select2-results__options").removeClass("hideoptions");
        return $returnString;
    }

    function formatDesignnew(item) {
        if (!item.id) {
            return item.text;
        }
        var selectionText = item.text.split("--");
        return selectionText[0];
    }

    $(function () {
        $("select.filter-by-text#searchDriver").each(function () {
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
                            usertype: 'Driver',
                            company_id: $('#searchCompany option:selected').val()
                        }
                        //console.log(queryParameters);
                        return queryParameters;
                    },
                    processResults: function (data, params) {
                        //console.log(data);
                        params.page = params.page || 1;

                        if (data.length < 10) {
                            var more = false;
                        } else {
                            var more = (params.page * 10) <= data[0].total_count;
                        }

                        $(".select2-container .select2-dropdown .select2-results .select2-results__options").removeClass("hideoptions");

                        return {
                            results: $.map(data, function (item) {

                                if (item.Phoneno != '' && item.vEmail != '') {
                                    var textdata = item.fullName + "--" + "Email: " + item.vEmail + "--" + "Phone: +" + item.Phoneno;
                                } else if (item.Phoneno == '' && item.vEmail != '') {
                                    var textdata = item.fullName + "--" + "Email: " + item.vEmail;
                                } else if (item.Phoneno != '' && item.vEmail == '') {
                                    var textdata = item.fullName + "--" + "Phone: +" + item.Phoneno;
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
    var sId = '<?php echo $iDriverId; ?>';
    var sSelect = $('select.filter-by-text#searchDriver');
    if (sId != '') {
        // $.ajax({
        //     type: 'POST',
        //     dataType: "json",
        //     url: 'ajax_getdriver_detail_search.php?id=' + sId + '&usertype=Driver'
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
            'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_getdriver_detail_search.php?id=' + sId + '&usertype=Driver',
            'AJAX_DATA': "",
            'REQUEST_DATA_TYPE': 'json'
        };
        getDataFromAjaxCall(ajaxData, function (response) {
            if (response.action == "1") {
                var data = response.result;
                $.map(data, function (item) {
                    if (item.Phoneno != '' && item.vEmail != '') {
                        var textdata = item.fullName + "--" + "Email: " + item.vEmail + "--" + "Phone: +" + item.Phoneno;
                    } else if (item.Phoneno == '' && item.vEmail != '') {
                        var textdata = item.fullName + "--" + "Email: " + item.vEmail;
                    } else if (item.Phoneno != '' && item.vEmail == '') {
                        var textdata = item.fullName + "--" + "Phone: +" + item.Phoneno;
                    }
                    var textdata = item.fullName;
                    itemname = textdata;
                    itemid = item.id;
                });
                var option = new Option(itemname, itemid, true, true);
                sSelect.append(option).trigger('change');
            } else {
                console.log(response.result);
            }
        });
    }
</script>
</body>
<!-- END BODY-->
</html>