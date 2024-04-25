<?php



include_once '../common.php';

$keyword = $_REQUEST['keyword'] ?? '';
$iVehicleTypeId = $_REQUEST['iVehicleTypeId'] ?? '';
$vCountry = $_REQUEST['vCountry'] ?? '';
$dBooking_date = $_REQUEST['dBooking_date'] ?? '';
$AppeType = $_REQUEST['AppeType'] ?? '';

$cmpMinutes = ceil((fetchtripstatustimeMAXinterval() + 60) / 60);
$str_date = @date('Y-m-d H:i:s', strtotime('-'.$cmpMinutes.' minutes'));
$ssql = " AND rd.eStatus='Active'";
if ('' !== $keyword) {
    $ssql .= " AND CONCAT(rd.vName,' ',rd.vLastName) like '%{$keyword}%'";
}
$eLadiesRide = $_REQUEST['eLadiesRide'] ?? '';
$eHandicaps = $_REQUEST['eHandicaps'] ?? '';
$eChildSeat = $_REQUEST['eChildSeat'] ?? '';
$eWheelChair = $_REQUEST['eWheelChair'] ?? '';
if ('Yes' === $eLadiesRide) {
    $ssql .= " AND (rd.eFemaleOnlyReqAccept = 'Yes' OR rd.eGender = 'Female')";
}
if ('Yes' === $eHandicaps) {
    $ssql .= " AND dv.eHandiCapAccessibility = 'Yes'";
}
if ('Yes' === $eChildSeat) {
    $ssql .= " AND dv.eChildSeatAvailable = 'Yes'";
}
if ('Yes' === $eWheelChair) {
    $ssql .= " AND dv.eWheelChairAvailable = 'Yes'";
}
if (!empty($vCountry)) {
    $ssql .= " AND rd.vCountry LIKE '".$vCountry."'";
}
if ('UberX' === $AppeType && !empty($dBooking_date)) {
    $vday = date('l', strtotime($dBooking_date));
    $curr_hour = date('H', strtotime($dBooking_date));
    $next_hour = $curr_hour + 01;
    $next_hour = sprintf('%02d', $next_hour);
    if ('00' === $curr_hour) {
        $curr_hour = '12';
        $next_hour = '01';
    }
    $selected_time = $curr_hour.'-'.$next_hour;
    $ssql .= "AND vDay LIKE '%".$vday."%' AND dmt.vAvailableTimes LIKE '%".$selected_time."%'";
}
if ('UberX' === $AppeType) {
    $sql = "SELECT rd.iDriverId,rd.vEmail,rd.iCompanyId, CONCAT(rd.vName,' ',rd.vLastName) AS FULLNAME,rd.vLatitude,rd.vLongitude,rd.vServiceLoc,rd.vAvailability,rd.vTripStatus,rd.tLastOnline, rd.vImage, rd.vCode, rd.vPhone,rd.tLocationUpdateDate FROM register_driver AS rd RIGHT JOIN driver_manage_timing  AS dmt ON rd.iDriverId = dmt.iDriverId  WHERE rd.vLatitude !='' AND rd.vLongitude !='' ".$ssql.' GROUP BY dmt.iDriverId';
    $db_records = $obj->MySQLSelect($sql);
    foreach ($db_records as $key => $value) {
        $sql_vehicle = "SELECT vCarType FROM `driver_vehicle` WHERE iDriverId = '".$value['iDriverId']."' AND eType='UberX'";
        $dbvehicle_records = $obj->MySQLSelect($sql_vehicle);
        $db_records[$key]['vCarType'] = $dbvehicle_records[0]['vCarType'];
    }
} else {
    $sql = "SELECT rd.iDriverId,rd.vEmail,rd.iCompanyId, CONCAT(rd.vName,' ',rd.vLastName) AS FULLNAME,rd.vLatitude,rd.vLongitude,rd.vServiceLoc,rd.vAvailability,rd.vTripStatus,rd.tLastOnline,rd.tLocationUpdateDate, rd.vImage, rd.vCode, rd.vPhone, dv.vCarType FROM register_driver AS rd LEFT JOIN driver_vehicle AS dv ON dv.iDriverVehicleId=rd.iDriverVehicleId WHERE rd.vLatitude !='' AND rd.vLongitude !='' ".$ssql;
    $db_records = $obj->MySQLSelect($sql);
}
/* echo "<pre>";
  print_r($db_records); die; */

/*
 * $enableCommisionDeduct = $MODULES_OBJ->autoDeductDriverCommision("Ride"); // Added By HJ On 16-10-2020 For get Auto Deduct Driver Commision Configuration As Per eSystem
 * if($enableCommisionDeduct == 'Yes') {
  $j=0;
  for($i=0;$i<count($db_records);$i++){
  $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($db_records[$i]['iDriverId'],"Driver");
  if($user_available_balance > $WALLET_MIN_BALANCE){
  $db_records_new[$j] = $db_records[$i];
  $db_records_new[$j]['user_available_balance'] = $user_available_balance;
  $j++;
  }
  }
  $db_records = $db_records_new;
  } */
// echo "<pre>";print_r($db_records);

$dbDrivers = [];
for ($i = 0; $i < count($db_records); ++$i) {
    $newArray = [];
    $newArray = explode(',', $db_records[$i]['vCarType']);
    if ('' === $iVehicleTypeId || (!empty($newArray) && in_array($iVehicleTypeId, $newArray, true))) {
        if ('NONE' !== $db_records[$i]['vImage'] && '' !== $db_records[$i]['vImage'] && file_exists($tconfig['tsite_upload_images_driver_path'].'/'.$db_records[$i]['iDriverId'].'/2_'.$db_records[$i]['vImage'])) {
            $DriverImage = $tconfig['tsite_upload_images_driver'].'/'.$db_records[$i]['iDriverId'].'/2_'.$db_records[$i]['vImage'];
        } else {
            $DriverImage = $tconfig['tsite_url'].'assets/img/profile-user-img.png';
        }
        $db_records[$i]['vImageDriver'] = $DriverImage;
        $time = time();
        $last_online_time = strtotime($db_records[$i]['tLastOnline']);
        $time_difference = $time - $last_online_time;
        $vTripStatus = $db_records[$i]['vTripStatus'];
        if ('Active' !== $vTripStatus && 'Available' === $db_records[$i]['vAvailability'] && $db_records[$i]['tLocationUpdateDate'] > $str_date) {
            $db_records[$i]['vAvailability'] = 'Available';
            $dbDrivers[$i] = $db_records[$i];
        } else {
            if ('Active' === $vTripStatus || 'On Going Trip' === $vTripStatus || 'Arrived' === $vTripStatus) {
                $db_records[$i]['vAvailability'] = $vTripStatus;
            } else {
                $db_records[$i]['vAvailability'] = 'Not Available';
            }
            $dbDrivers[$i] = $db_records[$i];
        }
    }
}
// echo "<pre>"; print_r($dbDrivers); die;
// marker Add

$map = $_REQUEST['map'] ?? '';

if (1 === $map) {
    $locations = [];
    // marker Add
    foreach ($dbDrivers as $key => $value) {
        $markerPath = $tconfig['tsite_url'].'webimages/upload/mapmarker/';
        if ('UberX' === $APP_TYPE) {
            if ('Available' === $value['vAvailability']) {
                $statusIcon = $markerPath.'male-green.png';
            } elseif ('Active' === $value['vAvailability']) {
                $statusIcon = $markerPath.'male-red.png';
            } elseif ('Arrived' === $value['vAvailability']) {
                $statusIcon = $markerPath.'male-blue.png';
            } elseif ('On Going Trip' === $value['vAvailability']) {
                $statusIcon = $markerPath.'male-yellow.png';
            } elseif ('Not Available' === $value['vAvailability']) {
                $statusIcon = $markerPath.'male-gray.png';
            } else {
                $statusIcon = $markerPath.'male-gray.png';
            }
        } else {
            if ('Available' === $value['vAvailability']) {
                $statusIcon = $markerPath.'available.png';
            } elseif ('Active' === $value['vAvailability']) {
                $statusIcon = $markerPath.'enroute.png';
            } elseif ('Arrived' === $value['vAvailability']) {
                $statusIcon = $markerPath.'reached.png';
            } elseif ('On Going Trip' === $value['vAvailability']) {
                $statusIcon = $markerPath.'started.png';
            } elseif ('Not Available' === $value['vAvailability']) {
                $statusIcon = $markerPath.'offline.png';
            } else {
                $statusIcon = $markerPath.'offline.png';
            }
        }
        $locations[] = ['google_map' => ['lat' => $value['vLatitude'], 'lng' => $value['vLongitude']], 'location_icon' => $statusIcon, 'location_address' => $value['vServiceLoc'], 'location_image' => $value['vImageDriver'], 'location_mobile' => clearPhone($value['vCode'].$value['vPhone']), 'location_ID' => clearEmail($value['vEmail']), 'location_name' => $value['fullname'], 'location_type' => $value['vAvailability'], 'location_online_status' => $value['vAvailability'], 'location_carType' => $value['vCarType'], 'location_driverId' => $value['iDriverId']];
    }
    $returnArr['Action'] = '0';
    $returnArr['locations'] = $locations;
    $returnArr['db_records'] = $db_records;
    $returnArr['newStatus'] = $newStatus;
    // echo "<pre>"; print_r($returnArr); die;
    echo json_encode($returnArr);

    exit;
}
$con = '';
foreach ($dbDrivers as $key => $value) {
    if ('Available' === $value['vAvailability']) {
        $statusIcon = '../assets/img/green-icon.png';
    } elseif ('Active' === $value['vAvailability']) {
        $statusIcon = '../assets/img/red.png';
    } elseif ('On Going Trip' === $value['vAvailability']) {
        $statusIcon = '../assets/img/yellow.png';
    } elseif ('Arrived' === $value['vAvailability']) {
        $statusIcon = '../assets/img/blue.png';
    } else {
        $statusIcon = '../assets/img/offline-icon.png';
    }
    $con .= '<li onclick="showPopupDriver('.$value['iDriverId'].');"><label class="map-tab-img"><label class="map-tab-img1"><img src="'.$value['vImageDriver'].'"></label><img src="'.$statusIcon.'"></label><p class="driver_'.$value['iDriverId'].'">'.clearName($value['FULLNAME']).' <b>+'.clearPhone($value['vCode'].$value['vPhone']).'</b></p><a href="javascript:void(0)" class="btn btn-success assign-driverbtn" onClick=\'checkUserBalance('.$value['iDriverId'].');\'>'.$langage_lbl_admin['LBL_ASSIGN_DRIVER_BUTTON'].'</a></li>';
}
echo $con;

exit;
