<?php



include_once '../common.php';

$keyword = $_REQUEST['keyword'] ?? '';
$iVehicleTypeId = $_REQUEST['iVehicleTypeId'] ?? '';
$type = $_REQUEST['type'] ?? '';
$cmpMinutes = ceil((fetchtripstatustimeMAXinterval() + 60) / 60);
$str_date = @date('Y-m-d H:i:s', strtotime('-'.$cmpMinutes.' minutes'));

$ssql = " AND rd.eStatus='Active'";
if ('' !== $keyword) {
    $ssql .= " AND CONCAT(rd.vName,' ',rd.vLastName) like '%{$keyword}%'";
}
if ('' !== $type) {
    if ('Available' === $type) {
        $ssql .= " AND rd.vAvailability = '".$type."' AND rd.vTripStatus != 'Active' AND rd.tLocationUpdateDate > '{$str_date}'";
    } else {
        $ssql .= " AND rd.vTripStatus = '".$type."' ";
    }
}
$sql = "SELECT rd.iDriverId,rd.vEmail,rd.iCompanyId, CONCAT(rd.vName,' ',rd.vLastName) AS FULLNAME,rd.vLatitude,rd.vLongitude,rd.vServiceLoc,rd.vAvailability,rd.vTripStatus,rd.tLastOnline, rd.vImage, rd.vCode, rd.vPhone, dv.vCarType,rd.tLocationUpdateDate FROM register_driver AS rd LEFT JOIN driver_vehicle AS dv ON dv.iDriverVehicleId=rd.iDriverVehicleId WHERE rd.vLatitude !='' AND rd.vLongitude !='' ".$ssql;
$db_records = $obj->MySQLSelect($sql);
// echo"<pre>";print_R($db_records);die;

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
        if ('Active' === $vTripStatus) {
            $db_records[$i]['vAvailability'] = $vTripStatus;
            $dbDrivers[$i] = $db_records[$i];
        } elseif ('Arrived' === $vTripStatus) {
            $db_records[$i]['vAvailability'] = $vTripStatus;
            $dbDrivers[$i] = $db_records[$i];
        } elseif ('On Going Trip' === $vTripStatus) {
            $db_records[$i]['vAvailability'] = $vTripStatus;
            $dbDrivers[$i] = $db_records[$i];
        } elseif ('Active' !== $vTripStatus && 'Available' === $db_records[$i]['vAvailability'] && $db_records[$i]['tLocationUpdateDate'] > $str_date) {
            $db_records[$i]['vAvailability'] = 'Available';
            $dbDrivers[$i] = $db_records[$i];
        } else {
            $db_records[$i]['vAvailability'] = 'Not Available';
            $dbDrivers[$i] = $db_records[$i];
        }
        /*	if($db_records[$i]['vAvailability'] == "Available"){
                $db_records[$i]['vAvailability'] = "Available";
                $dbDrivers[$i] = $db_records[$i];
            }else{

              if($vTripStatus == 'Active' || $vTripStatus == 'On Going Trip' || $vTripStatus == 'Arrived'){
                 $db_records[$i]['vAvailability'] = $vTripStatus;
                 $dbDrivers[$i] = $db_records[$i];
              }else{
                  $dbDrivers[$i] = $db_records[$i];
                $db_records[$i]['vAvailability'] = "Not Available";
              }
            }*/
    }
}
/* echo "<pre>";
 print_r($dbDrivers); die;*/
// marker Add
$con = '';
foreach ($dbDrivers as $key => $value) {
    // if($value['vAvailability'] != "Not Available") {
    if ('Available' === $value['vAvailability']) {
        $statusIcon = '../assets/img/green-icon.png';
    } elseif ('Arrived' === $value['vAvailability']) {
        $statusIcon = '../assets/img/blue.png';
    } elseif ('Active' === $value['vAvailability']) {
        $statusIcon = '../assets/img/red.png';
    } elseif ('On Going Trip' === $value['vAvailability']) {
        $statusIcon = '../assets/img/yellow.png';
    } elseif ('Not Available' === $value['vAvailability']) {
        $statusIcon = '../assets/img/offline-icon.png';
    } else {
        $statusIcon = '../assets/img/offline-icon.png';
    }
    $con .= '<li onclick="showPopupDriver('.$value['iDriverId'].'); " ><label class="map-tab-img"><label class="map-tab-img1"><img src="'.$value['vImageDriver'].'"></label><img src="'.$statusIcon.'"></label><p class="driver_'.$value['iDriverId'].'">'.clearName($value['FULLNAME']).' <b>+'.$value['vCode'].clearPhone($value['vPhone']).'</b></p></li>';
    // }
}
echo $con;

exit;
