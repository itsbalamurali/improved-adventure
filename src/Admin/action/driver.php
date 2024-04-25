<?php



include_once '../../common.php';
global $userObj;
$ip = $_SERVER['REMOTE_ADDR'] ?: '';
$date = date('Y-m-d');
$AUTH_OBJ->checkMemberAuthentication();
$reload = $_SERVER['REQUEST_URI'];
$urlparts = explode('?', $reload);
$parameters = $urlparts[1];
$id = $_REQUEST['id'] ?? '';
$iDriverId = $_REQUEST['iDriverId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';
// print_R($_REQUEST);die;
// Start make deleted
$hardDelete = 0; // 0-Soft Delete,1-Hard Delete
if ('192.168.1.131' === $_SERVER['HTTP_HOST'] || 'mobileappsdemo.com' === $_SERVER['HTTP_HOST'] || 'webprojectsdemo.com' === $_SERVER['HTTP_HOST'] || '192.168.1.141' === $_SERVER['HTTP_HOST']) {
    $hardDelete = 1; // 0-Soft Delete,1-Hard Delete
}
$allTables = getAllTableArray();
// Added By HJ On 03-03-2020 For Check Driver Service/Vehicle When Active As Per Discuss With KS Sir Start
if (('active' === strtolower($status) || 'active' === strtolower($statusVal)) && SITE_TYPE !== 'Demo') {
    if ('' !== $iDriverId) {
        $driverIds = $iDriverId;
    } else {
        $driverIds = $checkbox;
    }
    // $whereType = " AND eType='Ride'";
    // if ($APP_TYPE == 'Ride-Delivery-UberX') {
    //    $whereType = " AND (eType='Ride' OR eType='UberX')";
    // } elseif ($APP_TYPE == 'UberX') {
    //    $whereType = " AND eType='UberX'";
    // }
    // $sql = "SELECT register_driver.iDriverId from register_driver LEFT JOIN driver_vehicle on driver_vehicle.iDriverId=register_driver.iDriverId WHERE driver_vehicle.eStatus='Active' AND driver_vehicle.vCarType != '' AND register_driver.iDriverId IN (" . $driverIds . ") GROUP BY register_driver.iDriverId";
    $sql = "SELECT register_driver.iDriverId from register_driver LEFT JOIN driver_vehicle on driver_vehicle.iDriverId=register_driver.iDriverId WHERE driver_vehicle.eStatus='Active' AND driver_vehicle.vCarType != '' AND register_driver.iDriverId IN (".$driverIds.") GROUP BY register_driver.iDriverId
            UNION
            SELECT register_driver.iDriverId from register_driver LEFT JOIN bidding_driver_service on bidding_driver_service.iDriverId=register_driver.iDriverId WHERE bidding_driver_service.vBiddingId!= '' AND register_driver.iDriverId IN (".$driverIds.") GROUP BY register_driver.iDriverId
            UNION
            SELECT register_driver.iDriverId from register_driver LEFT JOIN driver_services_video_consult_charges on driver_services_video_consult_charges.iDriverId=register_driver.iDriverId WHERE driver_services_video_consult_charges.eStatus='Active' AND driver_services_video_consult_charges.eApproved = 'Yes' AND register_driver.iDriverId IN (".$driverIds.')  GROUP BY register_driver.iDriverId';

    $Data = $obj->MySQLSelect($sql);
    $serviceDriverArr = [];
    for ($h = 0; $h < count($Data); ++$h) {
        $serviceDriverArr[] = $Data[$h]['iDriverId'];
    }
    $explodeData = explode(',', $driverIds);
    // echo count($serviceDriverArr)."====".count($explodeData);die;
    // echo "<pre>";print_r($serviceDriverArr);die;
    if (count($serviceDriverArr) !== count($explodeData)) {
        $_SESSION['success'] = '3';
        if ('Ride-Delivery-UberX' === $APP_TYPE) {
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' status can not be activated because either '.$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' has not added any vehicle or his added vehicle is not activated yet or not selected any services. Please try again after adding and activating the vehicle/services.';
        } elseif ('UberX' === $APP_TYPE) {
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' status can not be activated because either '.$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' has not selected any services. Please try again after adding and activating the services.';
        } else {
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' status can not be activated because either '.$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' has not added any vehicle or his added vehicle is not activated yet. Please try again after adding and activating the vehicle.';
        }
        header('Location:'.$tconfig['tsite_url_main_admin'].$LOCATION_FILE_ARRAY['DRIVER.PHP'].'?'.$parameters);

        exit;
    }
}
// Added By HJ On 03-03-2020 For Check Driver Service/Vehicle When Active As Per Discuss With KS Sir End
if (('Deleted' === $statusVal || 'delete' === $method) && ('' !== $iDriverId || '' !== $checkbox)) {
    if (!$userObj->hasPermission('delete-providers')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete '.strtolower($langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']);
    } else {
        // Added By Hasmukh On 05-10-2018 For Solved Bug Start
        if ('' !== $iDriverId) {
            $driverIds = $iDriverId;
        } else {
            $driverIds = $checkbox;
        }
        // Added By Hasmukh On 05-10-2018 For Solved Bug End
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE register_driver SET eStatus = 'Deleted', vPhone = concat(vPhone, '(Deleted)') WHERE iDriverId IN (".$driverIds.')';
            $obj->sql_query($query);
            $explodeId = explode(',', $driverIds);
            for ($i = 0; $i < count($explodeId); ++$i) {
                // Insert status log on user_log table
                $queryIn = 'INSERT INTO user_status_logs SET iUserId = '.$explodeId[$i].", eUserType = 'driver', dDate = '".$date."', eStatus = 'Deleted', iUpdatedBy = ".$_SESSION['sess_iAdminUserId'].", vIP = '".$ip."'";
                $obj->sql_query($queryIn);
                if (1 === $hardDelete) {
                    removedDriverData($explodeId[$i]);
                }
            }
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $_SESSION['success'] = '2';
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].$LOCATION_FILE_ARRAY['DRIVER.PHP'].'?'.$parameters);

    exit;
}
// End make deleted
// Start Change single Status
$rquery = "SELECT rd.iCompanyId,c.eStatus FROM register_driver as rd LEFT JOIN company as c on c.iCompanyId=rd.iCompanyId WHERE rd.iDriverId = '".$iDriverId."'";
$drvcdata = $obj->MySQLSelect($rquery);
$cStatus = $drvcdata[0]['eStatus'];
if ('' !== $iDriverId && '' !== $status) {
    if (!$userObj->hasPermission('update-status-providers')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to update status '.strtolower($langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']);
    } else {
        if (SITE_TYPE !== 'Demo') {
            if ('Inactive' === $cStatus && 'active' === strtolower($status)) {
                $_SESSION['success'] = 3;
                $_SESSION['var_msg'] = $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' status can not be activated because company is not activated. Please try again after activating the company.';
            } else {
                // --------------------- driver deleted duplicate check --------------------
                $checkUserDeleted = $obj->MySQLSelect("SELECT vPhone FROM register_driver WHERE eStatus = 'Deleted' AND iDriverId='".$iDriverId."'");
                if (!empty($checkUserDeleted)) {
                    $mobile = clearPhone($checkUserDeleted[0]['vPhone']);
                    $checkUserDeleted = $obj->MySQLSelect("SELECT vPhone FROM register_driver WHERE eStatus != 'Deleted' AND vPhone='".$mobile."' AND iDriverId !='".$iDriverId."' ");
                    if (!empty($checkUserDeleted)) {
                        $_SESSION['success'] = 2;
                        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_ADMIN_NOT_ABLE_ACTIVE_TEXT'];
                        header('Location:'.$tconfig['tsite_url_main_admin'].$LOCATION_FILE_ARRAY['DRIVER.PHP'].'?'.$parameters);

                        exit;
                    }
                    $query = "UPDATE register_driver SET vPhone = '".$mobile."' WHERE iDriverId = '".$iDriverId."'";
                    $checkUserDeleted = $obj->MySQLSelect($query);
                }
                // --------------------- driver deleted duplicate check --------------------
                $query = "UPDATE register_driver SET eStatus = '".$status."' WHERE iDriverId = '".$iDriverId."'";
                $obj->sql_query($query);
                // Insert status log on user_log table
                $queryIn = 'INSERT INTO user_status_logs SET iUserId = '.$iDriverId.", eUserType = 'driver', dDate = '".$date."', eStatus = '".$status."', iUpdatedBy = ".$_SESSION['sess_iAdminUserId'].", vIP = '".$ip."'";
                $obj->sql_query($queryIn);
                $_SESSION['success'] = '1';
                if ('Active' === $status) {
                    $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_ACTIVATE_MSG'];
                } else {
                    $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INACTIVATE_MSG'];
                }
            }
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].$LOCATION_FILE_ARRAY['DRIVER.PHP'].'?'.$parameters);

    exit;
}
// End Change single Status
$rquery1 = 'SELECT rd.iCompanyId,c.eStatus,rd.iDriverId FROM register_driver as rd LEFT JOIN company as c on c.iCompanyId=rd.iCompanyId WHERE rd.iDriverId IN ('.$driverIds.") AND c.eStatus='Active'";
$drvmultidata1 = $obj->MySQLSelect($rquery1);
$serviceDriverCompArr1 = [];
for ($h = 0; $h < count($drvmultidata1); ++$h) {
    $serviceDriverCompArr1[] = $drvmultidata1[$h]['iDriverId'];
}
$explodeData = explode(',', $driverIds);
// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (!$userObj->hasPermission('update-status-providers')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to update status '.strtolower($langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']);
    } else {
        if (count($serviceDriverCompArr1) !== count($explodeData)) {
            $_SESSION['success'] = 3;
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' status can not be activated because company is not activated. Please try again after activating the company.';
        } else {
            if (SITE_TYPE !== 'Demo') {
                $query = "UPDATE register_driver SET eStatus = '".$statusVal."' WHERE iDriverId IN (".$checkbox.')';
                $obj->sql_query($query);
                $checkbox = explode(',', $checkbox);
                for ($i = 0; $i < count($checkbox); ++$i) {
                    // Insert status log on user_log table
                    $queryIn = 'INSERT INTO user_status_logs SET iUserId = '.$checkbox[$i].", eUserType = 'driver', dDate = '".$date."', eStatus = '".$statusVal."', iUpdatedBy = ".$_SESSION['sess_iAdminUserId'].", vIP = '".$ip."'";
                    $obj->sql_query($queryIn);
                }
                $_SESSION['success'] = '1';
                $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
            } else {
                $_SESSION['success'] = 2;
            }
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].$LOCATION_FILE_ARRAY['DRIVER.PHP'].'?'.$parameters);

    exit;
}
/* if ($method == 'reset' && $iDriverId != '') {

  if(SITE_TYPE !='Demo'){

  $query = "UPDATE register_driver SET vCreditCard='NULL',iTripId='0',vTripStatus='NONE',vStripeToken='',vStripeCusId='' WHERE iDriverId = '" . $iDriverId . "'";

  $obj->sql_query($query);

  $_SESSION['success'] = '1';

  $_SESSION['var_msg'] =  $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].'Reset successfully';

  }

  else{

  $_SESSION['success'] = '2';

  }

  header("Location:".$tconfig["tsite_url_main_admin"]."driver.php"); exit;

  } */
if ('reset' === $method && '' !== $iDriverId) {
    $q = "SELECT iTripId,vTripStatus FROM register_driver WHERE iDriverId = '".$iDriverId."'";
    $drvdata = $obj->MySQLSelect($q);
    if (!empty($drvdata) && '0' !== $drvdata[0]['iTripId']) {
        $sql = "SELECT iTripId,iActive,iDriverId,iUserId FROM trips WHERE iTripId = '".$drvdata[0]['iTripId']."'";
        $TripData = $obj->MySQLSelect($sql);
        // user
        $userquery = "SELECT iTripId,vTripStatus FROM register_user WHERE iUserId = '".$TripData[0]['iUserId']."'";
        $useData = $obj->MySQLSelect($userquery);
        if ('On Going Trip' === $TripData[0]['iActive']) {
            // driver
            $query = "UPDATE register_driver SET vTripStatus='Not Active' WHERE iDriverId = '".$iDriverId."'";
            $obj->sql_query($query);
            // trip
            $query1 = "UPDATE trips SET iActive='Finished',tEndDate = NOW() WHERE iTripId = '".$drvdata[0]['iTripId']."'";
            $obj->sql_query($query1);
            // rating
            $checkrate = "SELECT `iRatingId` FROM `ratings_user_driver` WHERE iTripId = '".$drvdata[0]['iTripId']."' AND eUserType='Driver' AND vRating1 != '' ";
            $TripRateDatadriver = $obj->MySQLSelect($checkrate);
            if (!empty($TripRateDatadriver)) {
                $rateq = "UPDATE ratings_user_driver SET vRating1='0.0' WHERE iTripId = '".$drvdata[0]['iTripId']."' AND eUserType='Driver'";
                $obj->sql_query($rateq);
            } else {
                $rateq = "INSERT INTO `ratings_user_driver`(`iTripId`, `vRating1`, `tDate`, `eUserType`, `vMessage`) VALUES ('".$drvdata[0]['iTripId']."','0.0',NOW(),'Driver','')";
                $obj->sql_query($rateq);
            }
            // rating
            if ($useData[0]['iTripId'] === $TripData[0]['iTripId']) {
                // user
                $uquery = "UPDATE register_user SET vTripStatus='Not Active' WHERE iUserId = '".$TripData[0]['iUserId']."'";
                $obj->sql_query($uquery);
                // rating
                $checkrate = "SELECT `iRatingId` FROM `ratings_user_driver` WHERE iTripId = '".$drvdata[0]['iTripId']."' AND eUserType='Passenger' AND vRating1 != '' ";
                $TripRateDatapass = $obj->MySQLSelect($checkrate);
                if (!empty($TripRateDatapass)) {
                    $rateq = "UPDATE ratings_user_driver SET vRating1='0.0' WHERE iTripId = '".$drvdata[0]['iTripId']."' AND eUserType='Passenger'";
                    $obj->sql_query($rateq);
                } else {
                    $rateq = "INSERT INTO `ratings_user_driver`(`iTripId`, `vRating1`, `tDate`, `eUserType`, `vMessage`) VALUES ('".$drvdata[0]['iTripId']."','0.0',NOW(),'Passenger','')";
                    $obj->sql_query($rateq);
                }
            }
        } elseif ('Active' === $TripData[0]['iActive']) {
            // driver
            $aquery = "UPDATE register_driver SET vTripStatus='Cancelled' WHERE iDriverId = '".$iDriverId."'";
            $obj->sql_query($aquery);
            // trip
            $qu1 = "UPDATE trips SET iActive = 'Canceled',tEndDate = NOW(),eCancelled = 'Yes', eCancelledBy='Driver', vCancelReason='Status Reset By Admin' WHERE iTripId = '".$drvdata[0]['iTripId']."'";
            $obj->sql_query($qu1);
            // user
            if ($useData[0]['iTripId'] === $TripData[0]['iTripId']) {
                // user
                $uquery = "UPDATE register_user SET vTripStatus='Cancelled' WHERE iUserId = '".$TripData[0]['iUserId']."'";
                $obj->sql_query($uquery);
            }
        } else {
            if ('Canceled' === $TripData[0]['iActive']) {
                // Driver
                if ('Cancelled' !== $drvdata[0]['vTripStatus'] && $drvdata[0]['iTripId'] === $TripData[0]['iTripId']) {
                    $dquery = "UPDATE register_driver SET vTripStatus='Cancelled' WHERE iDriverId = '".$iDriverId."'";
                    $obj->sql_query($dquery);
                }
                // Rider
                if ('Cancelled' !== $useData[0]['vTripStatus'] && $useData[0]['iTripId'] === $TripData[0]['iTripId']) {
                    $rquery = "UPDATE register_user SET vTripStatus='Cancelled' WHERE iUserId = '".$TripData[0]['iUserId']."'";
                    $obj->sql_query($rquery);
                }
            } else {
                // Driver
                if ($drvdata[0]['iTripId'] === $TripData[0]['iTripId']) {
                    $query = "UPDATE register_driver SET vTripStatus='Not Active' WHERE iDriverId = '".$iDriverId."'";
                    $obj->sql_query($query);
                    // rating
                    $checkrate = "SELECT `iRatingId` FROM `ratings_user_driver` WHERE iTripId = '".$drvdata[0]['iTripId']."' AND eUserType='Driver' AND vRating1 != '' ";
                    $TripRateDatadriver = $obj->MySQLSelect($checkrate);
                    if (!empty($TripRateDatadriver)) {
                        $rateq = "UPDATE ratings_user_driver SET vRating1='0.0' WHERE iTripId = '".$drvdata[0]['iTripId']."' AND eUserType='Driver'";
                        $obj->sql_query($rateq);
                    } else {
                        $rateq = "INSERT INTO `ratings_user_driver`(`iTripId`, `vRating1`, `tDate`, `eUserType`, `vMessage`) VALUES ('".$drvdata[0]['iTripId']."','0.0',NOW(),'Driver','')";
                        $obj->sql_query($rateq);
                    }
                }
                // Rider
                if ($useData[0]['iTripId'] === $TripData[0]['iTripId']) {
                    // user
                    $uquery = "UPDATE register_user SET vTripStatus='Not Active' WHERE iUserId = '".$TripData[0]['iUserId']."'";
                    $obj->sql_query($uquery);
                    // rating
                    $checkrate = "SELECT `iRatingId` FROM `ratings_user_driver` WHERE iTripId = '".$drvdata[0]['iTripId']."' AND eUserType='Passenger' AND vRating1 != '' ";
                    $TripRateDatapass = $obj->MySQLSelect($checkrate);
                    if (!empty($TripRateDatapass)) {
                        $rateq = "UPDATE ratings_user_driver SET vRating1='0.0' WHERE iTripId = '".$drvdata[0]['iTripId']."' AND eUserType='Passenger'";
                        $obj->sql_query($rateq);
                    } else {
                        $rateq = "INSERT INTO `ratings_user_driver`(`iTripId`, `vRating1`, `tDate`, `eUserType`, `vMessage`) VALUES ('".$drvdata[0]['iTripId']."','0.0',NOW(),'Passenger','')";
                        $obj->sql_query($rateq);
                    }
                }
            }
        }
    }
    /*    $query = "UPDATE register_driver SET vCreditCard='',iTripId='0',vTripStatus='NONE',vStripeToken='',vStripeCusId='' WHERE iDriverId = '" . $iDriverId . "'";

      $obj->sql_query($query); */
    $_SESSION['success'] = '1';
    $_SESSION['var_msg'] = $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].'reset successfully';
    header('Location:'.$tconfig['tsite_url_main_admin'].$LOCATION_FILE_ARRAY['DRIVER.PHP']);

    exit;
}
// End Change All Selected Status
// Added By Hasmukh On 05-12-2018 For Hard Remove Driver all Data Start
function removedDriverData($driverId): void
{
    global $obj, $allTables;
    // echo "<pre>";
    $tripIds = $cabIds = $cabRequestIds = '';
    $deleteTableArr = [];
    $deleteTableArr[] = ['table' => 'register_driver', 'field' => 'iDriverId', 'ids' => $driverId];
    $deleteTableArr[] = ['table' => 'driver_doc', 'field' => 'iDriverId', 'ids' => $driverId];
    $deleteTableArr[] = ['table' => 'driver_location_airport', 'field' => 'iDriverId', 'ids' => $driverId];
    $deleteTableArr[] = ['table' => 'driver_log_report', 'field' => 'iDriverId', 'ids' => $driverId];
    $deleteTableArr[] = ['table' => 'driver_manage_timing', 'field' => 'iDriverId', 'ids' => $driverId];
    $deleteTableArr[] = ['table' => 'driver_preferences', 'field' => 'iDriverId', 'ids' => $driverId];
    $deleteTableArr[] = ['table' => 'driver_request', 'field' => 'iDriverId', 'ids' => $driverId];
    $deleteTableArr[] = ['table' => 'driver_manage_timing', 'field' => 'iDriverId', 'ids' => $driverId];
    $deleteTableArr[] = ['table' => 'driver_vehicle', 'field' => 'iDriverId', 'ids' => $driverId];
    $deleteTableArr[] = ['table' => 'home_driver', 'field' => 'iDriverId', 'ids' => $driverId];
    $deleteTableArr[] = ['table' => 'passenger_requests', 'field' => 'iDriverId', 'ids' => $driverId];
    $deleteTableArr[] = ['table' => 'driver_subscription_details', 'field' => 'iDriverId', 'ids' => $driverId];
    $getTrips = $obj->MySQLSelect("SELECT iTripId FROM trips WHERE iDriverId='".$driverId."'");
    for ($t = 0; $t < count($getTrips); ++$t) {
        $tripIds .= ",'".$getTrips[$t]['iTripId']."'";
    }
    if ('' !== $tripIds) {
        $tripIds = trim($tripIds, ',');
        $deleteTableArr[] = ['table' => 'trips', 'field' => 'iTripId', 'ids' => $tripIds];
        $deleteTableArr[] = ['table' => 'temp_trips_delivery_locations', 'field' => 'iTripId', 'ids' => $tripIds];
        $deleteTableArr[] = ['table' => 'temp_trip_order_details', 'field' => 'iTripId', 'ids' => $tripIds];
        $deleteTableArr[] = ['table' => 'trips_delivery_locations', 'field' => 'iTripId', 'ids' => $tripIds];
        $deleteTableArr[] = ['table' => 'trips_locations', 'field' => 'iTripId', 'ids' => $tripIds];
        $deleteTableArr[] = ['table' => 'trip_call_masking', 'field' => 'iTripId', 'ids' => $tripIds];
        $deleteTableArr[] = ['table' => 'trip_delivery_fields', 'field' => 'iTripId', 'ids' => $tripIds];
        $deleteTableArr[] = ['table' => 'trip_destinations', 'field' => 'iTripId', 'ids' => $tripIds];
        $deleteTableArr[] = ['table' => 'trip_help_detail', 'field' => 'iTripId', 'ids' => $tripIds];
        $deleteTableArr[] = ['table' => 'trip_messages', 'field' => 'iTripId', 'ids' => $tripIds];
        $deleteTableArr[] = ['table' => 'trip_order_details', 'field' => 'iTripId', 'ids' => $tripIds];
        $deleteTableArr[] = ['table' => 'trip_outstanding_amount', 'field' => 'iTripId', 'ids' => $tripIds];
        $deleteTableArr[] = ['table' => 'trip_status_messages', 'field' => 'iTripId', 'ids' => $tripIds];
        $deleteTableArr[] = ['table' => 'trip_times', 'field' => 'iTripId', 'ids' => $tripIds];
        $deleteTableArr[] = ['table' => 'driver_user_messages', 'field' => 'iTripId', 'ids' => $tripIds];
        $deleteTableArr[] = ['table' => 'ratings_user_driver', 'field' => 'iTripId', 'ids' => $tripIds];
        $deleteTableArr[] = ['table' => 'user_wallet', 'field' => 'iTripId', 'ids' => $tripIds];
        $deleteTableArr[] = ['table' => 'payments', 'field' => 'iTripId', 'ids' => $tripIds];
        $deleteTableArr[] = ['table' => 'driver_request', 'field' => 'iTripId', 'ids' => $tripIds];
    }
    $getCabBooking = $obj->MySQLSelect("SELECT iCabBookingId FROM cab_booking WHERE iDriverId='".$driverId."'");
    for ($c = 0; $c < count($getCabBooking); ++$c) {
        $cabIds .= ",'".$getCabBooking[$c]['iCabBookingId']."'";
    }
    if ('' !== $cabIds || $driverId > 0) {
        $cabIds = trim($cabIds, ',');
        $cabIdWhere = '1 AND ';
        if ('' !== $cabIds) {
            $deleteTableArr[] = ['table' => 'cab_booking', 'field' => 'iCabBookingId', 'ids' => $cabIds];
            $cabIdWhere = " iCabBookingId IN({$cabIds}) OR";
        }
        $getCabRequest = $obj->MySQLSelect("SELECT iCabRequestId FROM cab_request_now WHERE {$cabIdWhere} iDriverId='".$driverId."'");
        for ($r = 0; $r < count($getCabRequest); ++$r) {
            $cabRequestIds .= ",'".$getCabRequest[$r]['iCabRequestId']."'";
        }
        if ('' !== $cabRequestIds) {
            $cabRequestIds = trim($cabRequestIds, ',');
            $deleteTableArr[] = ['table' => 'cab_request_now', 'field' => 'iCabRequestId', 'ids' => $cabRequestIds];
        }
    }
    for ($j = 0; $j < count($deleteTableArr); ++$j) {
        $idsW = $deleteTableArr[$j]['ids'];
        $checkTable = checkTableExists($deleteTableArr[$j]['table'], $allTables);
        if ('1' === $checkTable) {
            // echo "DELETE FROM " . $deleteTableArr[$j]['table'] . " WHERE " . $deleteTableArr[$j]['field'] . " IN($idsW)<br>";
            $obj->sql_query('DELETE FROM '.$deleteTableArr[$j]['table'].' WHERE '.$deleteTableArr[$j]['field']." IN({$idsW})");
        }
    }
}
// Added By Hasmukh On 05-12-2018 For Hard Remove Driver all Data End
