<?php



include_once '../common.php';

$keyword = $_REQUEST['keyword'] ?? '';
$iVehicleTypeId = $_REQUEST['iVehicleTypeId'] ?? '';
$vCountry = $_REQUEST['vCountry'] ?? '';
$dBooking_date = $_REQUEST['dBooking_date'] ?? '';
$AppeType = $_REQUEST['AppeType'] ?? '';
$map_driver = $_REQUEST['map_driver'] ?? 0;

$cmpMinutes = ceil((fetchtripstatustimeMAXinterval() + 60) / 60);
$str_date = @date('Y-m-d H:i:s', strtotime('-'.$cmpMinutes.' minutes'));
$ssql = " AND rd.eStatus='Active'";
if ('' !== $keyword) {
    $ssql .= " AND CONCAT(rd.vName,' ',rd.vLastName) like '%{$keyword}%'";
}
$eLadiesRide = $_REQUEST['eLadiesRide'] ?? '';
$eHandicaps = $_REQUEST['eHandicaps'] ?? 'No';
$eChildSeat = $_REQUEST['eChildSeat'] ?? 'No';
$eWheelChair = $_REQUEST['eWheelChair'] ?? 'No';
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
$sess_iCompanyId = $_REQUEST['sess_iCompanyId'] ?? '';
if ('' !== $sess_iCompanyId) {
    $ssql .= " AND rd.iCompanyId = '".$sess_iCompanyId."'";
}

/*$passengerLat = isset($_REQUEST['lattitude']) ? $_REQUEST['lattitude'] : '';
$passengerLon = isset($_REQUEST['longitude']) ? $_REQUEST['longitude'] : '';
$passengerDestLat = isset($_REQUEST['toLat']) ? $_REQUEST['toLat'] : '';
$passengerDestLon = isset($_REQUEST['toLong']) ? $_REQUEST['toLong'] : '';

$PickUpAddress = 'Mondeal Square, Prahlad Nagar, Ahmedabad, Gujarat, India';
$address_data['PickUpAddress'] = $PickUpAddress;
if ($AppeType == "UberX" && $scheduleDate != "") {
        $Check_Driver_UFX = "Yes";
        $sdate = explode(" ", $scheduleDate);
        $shour = explode("-", $sdate[1]);
        $shour1 = $shour[0];
        $Check_Date_Time = $sdate[0] . " " . $shour1 . ":00:00";
    } else if ($AppeType == "UberX" && $SERVICE_PROVIDER_FLOW == "Provider") {
        $Check_Driver_UFX = "Yes";
        $Check_Date_Time = "";
    } else {
        $Check_Driver_UFX = "No";
        $Check_Date_Time = "";
    }

    $eFemaleDriverRequestWeb = '';
   $DataArr =  FetchAvailableDrivers($passengerLat, $passengerLon, $address_data, "Yes", "No", "No", "", $passengerDestLat, $passengerDestLon);
//$DataArr = FetchAvailableDrivers($passengerLat, $passengerLon, $address_data, "No", "No", $Check_Driver_UFX, $Check_Date_Time, $passengerDestLat, $passengerDestLon, $AppeType, $eFemaleDriverRequestWeb);
print_R($DataArr); exit;*/
// echo $dBooking_date;die;
if ('UberX' === $AppeType && !empty($dBooking_date)) {
    $vday = date('l', strtotime($dBooking_date));
    $curr_hour = date('H', strtotime($dBooking_date));
    $next_hour = $curr_hour + 01;
    if ('00' === $curr_hour) {
        $curr_hour = '12';
        $next_hour = '01';
    }
    $selected_time = $curr_hour.'-'.$next_hour;
    $ssql .= "AND vDay LIKE '%".$vday."%' AND dmt.vAvailableTimes LIKE '%".$selected_time."%'";
}
if ('UberX' === $AppeType) {
    $sql = "SELECT rd.iDriverId,rd.vEmail,rd.iCompanyId, CONCAT(rd.vName,' ',rd.vLastName) AS FULLNAME,rd.vLatitude,rd.vLongitude,rd.vServiceLoc,rd.vAvailability,rd.vTripStatus,rd.tLastOnline, rd.vImage, rd.vCode, rd.vPhone,rd.tLocationUpdateDate FROM register_driver AS rd RIGHT JOIN driver_manage_timing  AS dmt ON rd.iDriverId = dmt.iDriverId  WHERE rd.vLatitude !='' AND rd.vLongitude !='' ".$ssql.' GROUP BY dmt.iDriverId';
    // echo $sql;die;
    $db_records = $obj->MySQLSelect($sql);

    // Added By HJ On 08-01-2020 For Optimized Code Start
    $dbvehicle_records = $obj->MySQLSelect("SELECT vCarType,iDriverId FROM `driver_vehicle` WHERE eType='UberX'");
    $vCarTypeArr = [];
    for ($g = 0; $g < count($dbvehicle_records); ++$g) {
        $vCarTypeArr[$dbvehicle_records[$g]['iDriverId']] = $dbvehicle_records[$g]['vCarType'];
    }
    // Added By HJ On 08-01-2020 For Optimized Code End
    foreach ($db_records as $key => $value) {
        $vCartypeIds = '';
        if (isset($vCarTypeArr[$value['iDriverId']])) {
            $vCartypeIds = $vCarTypeArr[$value['iDriverId']];
        }
        $db_records[$key]['vCarType'] = $vCartypeIds;
    }
} else {
    $sql = "SELECT rd.iDriverId,rd.vEmail,rd.iCompanyId, CONCAT(rd.vName,' ',rd.vLastName) AS FULLNAME,rd.vLatitude,rd.vLongitude,rd.vServiceLoc,rd.vAvailability,rd.vTripStatus,rd.tLastOnline,rd.tLocationUpdateDate, rd.vImage, rd.vCode, rd.vPhone, dv.vCarType FROM register_driver AS rd LEFT JOIN driver_vehicle AS dv ON dv.iDriverVehicleId=rd.iDriverVehicleId WHERE rd.vLatitude !='' AND rd.vLongitude !='' ".$ssql;
    $db_records = $obj->MySQLSelect($sql);
}

// echo "<pre>";print_r($db_records);die;
if (!empty($db_records)) {
    $dbDrivers = [];
    for ($i = 0; $i < count($db_records); ++$i) {
        $newArray = [];
        $newArray = explode(',', $db_records[$i]['vCarType']);
        $vTripStatus = $db_records[$i]['vTripStatus'];
        if ('Active' !== $vTripStatus && 'On Going Trip' !== $vTripStatus && 'Arrived' !== $vTripStatus) {
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
    }
}
// echo "<pre>";print_r($map_driver);die;
// marker Add
if (1 === $map_driver) {
    $locations = [];
    // if($type != "") {
    // }
    // marker Add

    $markerPath = $tconfig['tsite_url'].'webimages/upload/mapmarker/';

    if ('UberX' === $eType) {
        $markerPath .= 'UberX/';
    } elseif ('Fly' === $eType) {
        $markerPath .= 'Fly/';
    } else {
        if (!empty($iVehicleTypeId)) {
            $sql_vehicle_type = "SELECT eIconType FROM vehicle_type WHERE iVehicleTypeId = {$iVehicleTypeId}";
            $db_vehicle_type = $obj->MySQLSelect($sql_vehicle_type);
            $iconFolder = $db_vehicle_type[0]['eIconType'];
            $markerPath = $markerPath.$iconFolder.'/';
        }
    }

    foreach ($dbDrivers as $key => $value) {
        if ('UberX' === $APP_TYPE || 'UberX' === $eType) {
            $statusIcon = !empty($value['vImageDriver']) ? $value['vImageDriver'] : $tconfig['tsite_url'].'assets/img/profile-user-img.png';
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
        $locations[] = [
            'google_map' => [
                'lat' => $value['vLatitude'],
                'lng' => $value['vLongitude'],
            ],
            'location_icon' => $statusIcon,
            'location_address' => $value['vServiceLoc'],
            'location_image' => $value['vImageDriver'],
            'location_mobile' => clearPhone($value['vCode'].$value['vPhone']),
            'location_ID' => clearEmail($value['vEmail']),
            'location_name' => $value['fullname'],
            'location_type' => $value['vAvailability'],
            'location_online_status' => $value['vAvailability'],
            'location_carType' => $value['vCarType'],
            'location_driverId' => $value['iDriverId'],
        ];
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
        $statusIcon = $tconfig['tsite_url'].'booking/img/green-icon.png';
    } elseif ('Active' === $value['vAvailability']) {
        $statusIcon = $tconfig['tsite_url'].'booking/img/red.png';
    } elseif ('On Going Trip' === $value['vAvailability']) {
        $statusIcon = $tconfig['tsite_url'].'booking/img/yellow.png';
    } elseif ('Arrived' === $value['vAvailability']) {
        $statusIcon = $tconfig['tsite_url'].'booking/img/blue.png';
    } else {
        $statusIcon = $tconfig['tsite_url'].'booking/img/offline-icon.png';
    }
    if ('Yes' === $THEME_OBJ->isXThemeActive()) {
        $con .= '<li onclick="showPopupDriver('.$value['iDriverId'].');"><label class="map-tab-img"><label class="map-tab-img1"><img src="'.$value['vImageDriver'].'"></label><img src="'.$statusIcon.'"></label><p class="driver_'.$value['iDriverId'].'">'.clearName($value['FULLNAME']).' <b>+'.clearMobile($value['vCode'].$value['vPhone']).'</b></p><button type="button" href="javascript:void(0)" class="assign-driverbtn gen-btn xs-small-btn" onClick=\'checkUserBalance('.$value['iDriverId'].');\'>'.$langage_lbl_admin['LBL_ASSIGN_DRIVER_BUTTON'].'</button></li>';
    } else {
        $con .= '<li onclick="showPopupDriver('.$value['iDriverId'].');"><label class="map-tab-img"><label class="map-tab-img1"><img src="'.$value['vImageDriver'].'"></label><img src="'.$statusIcon.'"></label><p class="driver_'.$value['iDriverId'].'">'.clearName($value['FULLNAME']).' <b>+'.clearMobile($value['vCode'].$value['vPhone']).'</b></p><a href="javascript:void(0)" class="btn btn-success assign-driverbtn" onClick=\'checkUserBalance('.$value['iDriverId'].');\'>'.$langage_lbl_admin['LBL_ASSIGN_DRIVER_BUTTON'].'</a></li>';
    }
}
echo $con;

exit;
