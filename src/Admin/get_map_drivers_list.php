<?php



include_once '../common.php';
header('Content-Type: text/html; charset=utf-8');

$type = $_REQUEST['type'];
$cmpMinutes = ceil((fetchtripstatustimeMAXinterval() + 60) / 60);
$str_date = @date('Y-m-d H:i:s', strtotime('-'.$cmpMinutes.' minutes'));
$ssql = " AND rd.eStatus='Active'";
$eLadiesRide = $_REQUEST['eLadiesRide'] ?? 'No';
$eHandicaps = $_REQUEST['eHandicaps'] ?? 'No';
$eChildSeat = $_REQUEST['eChildSeat'] ?? 'No';
$eWheelChair = $_REQUEST['eWheelChair'] ?? 'No';
$createRequest = $_REQUEST['createRequest'] ?? 'No';
$vCountry = $_REQUEST['vCountry'] ?? '';

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
if ('' !== $type) {
    if ('Available' === $type) {
        $ssql .= " AND rd.vAvailability = '".$type."' AND rd.vTripStatus != 'Active' AND rd.tLocationUpdateDate > '{$str_date}'";
    } else {
        $ssql .= " AND rd.vTripStatus = '".$type."' ";
    }
}
$innerJoinTrip = '';
if ('Yes' === $createRequest && '' === $type) {
    $innerJoinTrip = ' LEFT JOIN trips tr ON tr.iDriverId=dv.iDriverId';
    $ssql .= " AND (rd.vAvailability = 'Not Available' OR rd.vAvailability = 'Available') AND (rd.vTripStatus = 'Not Active' OR rd.vTripStatus = 'Cancelled' OR rd.vTripStatus = 'Finished')";
}
$sql = "SELECT rd.iDriverId,CONCAT(rd.vName,' ',rd.vLastName) AS fullname ,rd.vEmail,rd.iCompanyId, rd.vLatitude,rd.vLongitude,rd.vServiceLoc,rd.vAvailability,rd.vTripStatus,rd.tLastOnline, rd.vImage, rd.vCode, rd.vPhone, dv.vCarType,rd.tLocationUpdateDate FROM register_driver AS rd LEFT JOIN driver_vehicle AS dv ON dv.iDriverVehicleId=rd.iDriverVehicleId WHERE rd.vLatitude !='' AND rd.vLongitude !='' ".$ssql;
$db_records = $obj->MySQLSelect($sql);
// echo $sql;die;
// echo "<pre>"; print_r($db_records); die;

for ($i = 0; $i < count($db_records); ++$i) {
    if ('NONE' !== $db_records[$i]['vImage'] && '' !== $db_records[$i]['vImage']) {
        $DriverImage = $tconfig['tsite_upload_images_driver'].'/'.$db_records[$i]['iDriverId'].'/2_'.$db_records[$i]['vImage'];
    } else {
        $DriverImage = $tconfig['tsite_url'].'assets/img/profile-user-img.png';
    }
    $db_records[$i]['vImageDriver'] = $DriverImage;
    $time = time();
    $last_online_time = strtotime($db_records[$i]['tLastOnline']);
    $time_difference = $time - $last_online_time;
    $vTripStatus = $db_records[$i]['vTripStatus'];
    if ('Active' === $vTripStatus) {
        $db_records[$i]['vAvailability'] = $vTripStatus;
    } elseif ('Arrived' === $vTripStatus) {
        $db_records[$i]['vAvailability'] = $vTripStatus;
    } elseif ('On Going Trip' === $vTripStatus) {
        $db_records[$i]['vAvailability'] = $vTripStatus;
    } elseif ('Active' !== $vTripStatus && 'Available' === $db_records[$i]['vAvailability'] && $db_records[$i]['tLocationUpdateDate'] > $str_date) {
        $db_records[$i]['vAvailability'] = 'Available';
    } else {
        $db_records[$i]['vAvailability'] = 'Not Available';
    }
    $db_records[$i]['fullname'] = clearName($db_records[$i]['fullname']);
    $db_records[$i]['vEmail'] = clearEmail($db_records[$i]['vEmail']);
    $db_records[$i]['vPhone'] = clearPhone($db_records[$i]['vPhone']);
}
$locations = [];
// marker Add
foreach ($db_records as $key => $value) {
    $markerPath = $tconfig['tsite_url'].'webimages/upload/mapmarker/';

    if ('YES' === strtoupper(DELIVERALL)) {
        if ('Available' === $value['vAvailability']) {
            $statusIcon = $tconfig['tsite_url'].'webimages/upload/mapmarker/available_pin.png';
        } elseif ('Active' === $value['vAvailability']) {
            $statusIcon = $tconfig['tsite_url'].'webimages/upload/mapmarker/enroute_pin.png';
        } elseif ('Arrived' === $value['vAvailability']) {
            $statusIcon = $tconfig['tsite_url'].'webimages/upload/mapmarker/reached_pin.png';
        } elseif ('On Going Trip' === $value['vAvailability']) {
            $statusIcon = $tconfig['tsite_url'].'webimages/upload/mapmarker/started_pin.png';
        } elseif ('Not Available' === $value['vAvailability']) {
            $statusIcon = $tconfig['tsite_url'].'webimages/upload/mapmarker/offline_pin.png';
        } else {
            $statusIcon = $tconfig['tsite_url'].'webimages/upload/mapmarker/offline_pin.png';
        }
    } elseif ('UberX' === $APP_TYPE) {
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
