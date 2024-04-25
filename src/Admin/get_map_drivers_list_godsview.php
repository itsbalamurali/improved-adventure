<?php



include_once '../common.php';
header('Content-type: text/html; charset=utf-8');

$intervalmins = INTERVAL_SECONDS; // Added By HJ On 13-03-2020 Which is Defined In configuration_variables.php

$type = $_REQUEST['type'];
$cmpMinutes = ceil((fetchtripstatustimeMAXinterval() + $intervalmins) / 60);
$str_date = @date('Y-m-d H:i:s', strtotime('-'.$cmpMinutes.' minutes'));
$ssql = " AND rd.eStatus='Active' AND rd.eIsBlocked='No'";
$option = $_REQUEST['option'] ?? '';
$eLadiesRide = $_REQUEST['eLadiesRide'] ?? '';
$eHandicaps = $_REQUEST['eHandicaps'] ?? '';
$page = $_REQUEST['page'] ?? '1';

$per_page = 10;
$pagecount = $page - 1;
$start_limit = $pagecount * $per_page;
$next_page = $page + 1;

if (empty($_REQUEST['page'])) {
    if ('Yes' === $eLadiesRide) {
        $ssql .= " AND (rd.eFemaleOnlyReqAccept = 'Yes' OR rd.eGender = 'Female')";
    }
    if ('Yes' === $eHandicaps) {
        $ssql .= " AND dv.eHandiCapAccessibility = 'Yes'";
    }
    if ('' !== $type) {
        if ('Available' === $type) {
            $ssql .= " AND rd.vAvailability = '".$type."' AND rd.vTripStatus != 'Active' AND rd.tLocationUpdateDate > '{$str_date}'";
        } else {
            $ssql .= " AND rd.vTripStatus = '".$type."' ";
        }
    }

    // $sql = "SELECT rd.iDriverId,CONCAT(rd.vName,' ',rd.vLastName) AS fullname ,rd.vEmail,rd.iCompanyId, rd.vLatitude,rd.vLongitude,rd.vServiceLoc,rd.vAvailability,rd.vTripStatus,rd.tLastOnline, rd.vImage, rd.vCode, rd.vPhone, dv.vCarType,rd.tLocationUpdateDate FROM register_driver AS rd LEFT JOIN driver_vehicle AS dv ON dv.iDriverVehicleId=rd.iDriverVehicleId WHERE rd.vLatitude !='' AND rd.vLongitude !='' ".$ssql." LIMIT $start_limit,$per_page";
    $sql = "SELECT rd.iDriverId,CONCAT(rd.vName,' ',rd.vLastName) AS fullname ,rd.vEmail,rd.iCompanyId, rd.vLatitude,rd.vLongitude,rd.vServiceLoc,rd.vAvailability,rd.vTripStatus,rd.tLastOnline, rd.vImage, rd.vCode, rd.vPhone, dv.vCarType,rd.tLocationUpdateDate FROM register_driver AS rd LEFT JOIN driver_vehicle AS dv ON dv.iDriverVehicleId=rd.iDriverVehicleId WHERE rd.vLatitude !='' AND rd.vLongitude !='' ".$ssql;
    $db_records = $obj->MySQLSelect($sql);
    // echo "<pre>"; print_r($db_records); die;
    $markers = [];
    foreach ($db_records as $key => $value) {
        $DriverId = $value['iDriverId'];
        $marker = [];
        $time = time();
        $last_online_time = strtotime($value['tLastOnline']);
        $time_difference = $time - $last_online_time;
        $vTripStatus = $value['vTripStatus'];
        // echo $value['fullname']."==".$vTripStatus."===".$value['vAvailability']."====".$value['tLocationUpdateDate']."====".$str_date.'<br>';
        if ('UberX' === $APP_TYPE) {
            // if($value['vAvailability'] == "Available") {
            if ('Active' !== $vTripStatus && 'Available' === $value['vAvailability'] && $value['tLocationUpdateDate'] > $str_date) {
                $statusIcon = $tconfig['tsite_url'].'webimages/upload/mapmarker/male-green.png';
            } elseif ('Active' === $value['vAvailability']) {
                $statusIcon = $tconfig['tsite_url'].'webimages/upload/mapmarker/male-red.png';
            } elseif ('Arrived' === $value['vAvailability']) {
                $statusIcon = $tconfig['tsite_url'].'webimages/upload/mapmarker/male-blue.png';
            } elseif ('On Going Trip' === $value['vAvailability']) {
                $statusIcon = $tconfig['tsite_url'].'webimages/upload/mapmarker/male-yellow.png';
            } elseif ('Not Available' === $value['vAvailability']) {
                $statusIcon = $tconfig['tsite_url'].'webimages/upload/mapmarker/male-gray.png';
            } else {
                $statusIcon = $tconfig['tsite_url'].'webimages/upload/mapmarker/male-gray.png';
            }
        } else {
            // if($value['vAvailability'] == "Available") {
            if ('Active' !== $vTripStatus && 'Available' === $value['vAvailability'] && $value['tLocationUpdateDate'] > $str_date) {
                $statusIcon = $tconfig['tsite_url'].'webimages/upload/mapmarker/available_pin.png';
            } elseif ('Active' === $value['vAvailability'] || 'Active' === $vTripStatus) {
                $statusIcon = $tconfig['tsite_url'].'webimages/upload/mapmarker/enroute_pin.png';
            } elseif ('Arrived' === $value['vAvailability'] || 'Arrived' === $vTripStatus) {
                $statusIcon = $tconfig['tsite_url'].'webimages/upload/mapmarker/reached_pin.png';
            } elseif ('On Going Trip' === $value['vAvailability'] || 'On Going Trip' === $vTripStatus) {
                $statusIcon = $tconfig['tsite_url'].'webimages/upload/mapmarker/started_pin.png';
            } elseif ('Not Available' === $value['vAvailability']) {
                $statusIcon = $tconfig['tsite_url'].'webimages/upload/mapmarker/offline_pin.png';
            } else {
                $statusIcon = $tconfig['tsite_url'].'webimages/upload/mapmarker/offline_pin.png';
            }
        }
        $location = [
            'lat' => $value['vLatitude'],
            'lng' => $value['vLongitude'],
            'icon' => $statusIcon,
            // 'image' => $value['vImageDriver'],
            'address' => $value['vServiceLoc'],
            'status' => $value['vAvailability'],
            'car_type' => $value['vCarType'],
        ];
        if ('NONE' !== $value['vImage'] && '' !== $value['vImage'] && file_exists($tconfig['tsite_upload_images_driver_path'].'/'.$value['iDriverId'].'/2_'.$value['vImage'])) {
            $DriverImage = $tconfig['tsite_upload_images_driver'].'/'.$value['iDriverId'].'/2_'.$value['vImage'];
        } else {
            $DriverImage = $tconfig['tsite_url'].'assets/img/profile-user-img.png';
        }
        if ('Active' === $vTripStatus) {
            $value['vAvailability'] = $vTripStatus;
        } elseif ('Arrived' === $vTripStatus) {
            $value['vAvailability'] = $vTripStatus;
        } elseif ('On Going Trip' === $vTripStatus) {
            $value['vAvailability'] = $vTripStatus;
        } elseif ('Active' !== $vTripStatus && 'Available' === $value['vAvailability'] && $value['tLocationUpdateDate'] > $str_date) {
            $value['vAvailability'] = 'Available';
        } else {
            $value['vAvailability'] = 'Not Available';
        }
        // if($value['vAvailability'] == "Available") {
        if ('Active' !== $vTripStatus && 'Available' === $value['vAvailability'] && $value['tLocationUpdateDate'] > $str_date) {
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
        $marker['image'] = $DriverImage;
        $marker['id'] = $value['iDriverId'];
        $marker['status_icon'] = $statusIcon;
        $marker['fullname'] = mb_convert_encoding(clearName(ucfirst($value['fullname'])), 'utf-8', 'auto');
        $marker['email'] = utf8_encode(clearEmail($value['vEmail']));
        $marker['phone'] = utf8_encode($value['vCode'].clearPhone($value['vPhone']));
        $marker['location'] = $location;
        $sql = 'SELECT t.iTripId  FROM  register_driver d LEFT JOIN trips t  ON t.iDriverId = d.iDriverId WHERE t.iDriverId ='.$DriverId." AND (t.iActive = 'Active' OR t.iActive = 'On Going Trip' OR t.iActive = 'Arrived') AND d.eStatus = 'Active' AND (d.vTripStatus = 'Active' OR d.vTripStatus = 'On Going Trip' OR d.vTripStatus = 'Arrived') ORDER BY t.iTripId DESC  limit 1";
        $db_dtrip = $obj->MySQLSelect($sql);
        $iTripId = '';
        if (count($db_dtrip) > 0) {
            $iTripId = $db_dtrip[0]['iTripId'];
            // $TripId = encrypt($iTripId);
            $TripId = base64_encode(base64_encode($iTripId));
        }
        if (empty($iTripId)) {
            $marker['trip'] = '';
        } else {
            $marker['trip'] = $tconfig['tsite_url_main_admin']."map_tracking.php?iTripId={$TripId}";
        }
        $markers[] = $marker;
    }
    $main_location = [];
    if ('' !== $option) {
        $ssql = "SELECT  tLatitude,tLongitude FROM location_master WHERE eStatus = 'Active' AND eFor = 'VehicleType' AND iLocationId=".$option.'';
    } else {
        $ssql = "SELECT  tLatitude,tLongitude FROM location_master WHERE eStatus = 'Active' AND eFor = 'VehicleType' ORDER BY `iLocationId` ASC ";
    }
    $db_latlong = $obj->MySQLSelect($ssql);
    $count = count($db_latlong);
    if ($count > 0) {
        $Latitudes = explode(',', $db_latlong[0]['tLatitude']);
        $Longitudes = explode(',', $db_latlong[0]['tLongitude']);
        for ($i = 0; $i < count($Latitudes) - 1; ++$i) {
            $all = [];
            $all['Latitude'] = $Latitudes[$i];
            $all['Longitude'] = $Longitudes[$i];
            $main_location[] = $all;
        }
    }
    $returnArr['Action'] = '0';
    $returnArr['markers'] = $markers;
    $returnArr['main_location'] = $main_location;
    // $returnArr['newStatus'] = $newStatus;
}
$returnArr['page'] = $next_page;
// echo "<pre>"; print_r($returnArr); die;
echo json_encode($returnArr, JSON_UNESCAPED_UNICODE);

exit;
