<?php



include_once '../common.php';

$iLocationId = $_REQUEST['iLocationId'] ?? '';
$selected = $_REQUEST['selected'] ?? '';
$to = $_REQUEST['to'] ?? '';
$iFromlocationId = $_REQUEST['iFromlocationId'] ?? '';

if (isset($iLocationId)) {
    if ('' !== $iLocationId) {
        /*if($to==1) {
            $sql = "SELECT iCountryId FROM  `location_master` WHERE 1=1 AND eStatus = 'Active' AND iLocationId = '" . $iLocationId . "' AND eFor = 'FlyStation'";
            $db_data = $obj->MySQLSelect($sql);

            $sqlFrom = "SELECT iCountryId FROM  `location_master` WHERE 1=1 AND eStatus = 'Active' AND iLocationId = '" . $iFromlocationId . "' AND eFor = 'FlyStation'";
            $db_dataFrom = $obj->MySQLSelect($sqlFrom);
        } else {
            $sql = "SELECT iCountryId FROM  `location_master` WHERE 1=1 AND eStatus = 'Active' AND iLocationId = '" . $iLocationId . "' AND eFor = 'FlyStation'";
            $db_data = $obj->MySQLSelect($sql);
        }*/

        $sql1 = "SELECT tCentroidLattitude,tCentroidLongitude FROM  `location_master` WHERE 1=1 AND eStatus = 'Active' AND iLocationId = {$iFromlocationId} AND eFor = 'FlyStation'";
        $db_data_vehicle = $obj->MySQLSelect($sql1);
        $latlong = [$db_data_vehicle[0]['tCentroidLattitude'], $db_data_vehicle[0]['tCentroidLongitude']];
        $vtype = FetchVehicleTypeFromGeoLocation($latlong);
        $vtypeArr = explode(',', $vtype);

        $sql1to = "SELECT tCentroidLattitude,tCentroidLongitude FROM  `location_master` WHERE 1=1 AND eStatus = 'Active' AND iLocationId = {$iLocationId} AND eFor = 'FlyStation'";
        $db_data_vehicleto = $obj->MySQLSelect($sql1to);
        $latlongto = [$db_data_vehicleto[0]['tCentroidLattitude'], $db_data_vehicleto[0]['tCentroidLongitude']];
        $vtypeto = FetchVehicleTypeFromGeoLocation($latlongto);
        $vtypeArrto = explode(',', $vtypeto);

        // print_R($vtypeArr);
        // print_R($vtypeArrto);
        $result = array_intersect($vtypeArr, $vtypeArrto);

        if (!empty($result)) {
            $ilocation_id = implode("','", $result);
        }
        // echo $ilocation_id;exit;

        // print_R($db_data); exit;
        if (!empty($ilocation_id)) {
            // $iCountryId = $db_data[0]['iCountryId'];
            // if (!empty($iCountryId)) {
            /*$sql1 = "SELECT iLocationId FROM  `location_master` WHERE 1=1 AND eStatus = 'Active' AND iCountryId IN('" . $iCountryId . "', '".$db_dataFrom[0]['iCountryId']."') AND eFor = 'VehicleType'";
            $db_data_vehicle = $obj->MySQLSelect($sql1);
            foreach ($db_data_vehicle as $key => $value) {
                $iLocationId_array[] = $value['iLocationId'];
            }
            $ilocation_id = '';
            if (!empty($iLocationId_array)) {
                $ilocation_id = implode("','", $iLocationId_array);
            }
            */
            // $ilocation_id = $iLocationId;
            if ('Ride-Delivery' === $APP_TYPE) {
                include_once '../include/ride-delivery/ajax_get_vehicletype_fixfare_admin1.php';
            } elseif ('Ride-Delivery-UberX' === $APP_TYPE) {
                include_once '../include/ride-delivery-uberx/ajax_get_vehicletype_fixfare_admin2.php';
            } else {
                if (!empty($ilocation_id)) {
                    $sql2 = "SELECT lm.vLocationName,vt.iLocationId,vt.vVehicleType,vt.iVehicleTypeId FROM  `vehicle_type` as vt LEFT JOIN location_master as lm on lm.iLocationId = vt.iLocationid  WHERE (vt.iLocationid = '-1' OR vt.iLocationid IN ('".$ilocation_id."')) AND vt.eStatus='Active' AND vt.eType = '".$APP_TYPE."' AND vt.ePoolStatus = 'No'";
                } else {
                    $sql2 = "SELECT lm.vLocationName,vt.iLocationId,vt.vVehicleType,vt.iVehicleTypeId FROM  `vehicle_type` as vt LEFT JOIN location_master as lm on lm.iLocationId = vt.iLocationid  WHERE (vt.iLocationid = '-1') AND vt.eStatus='Active' AND vt.eType = '".$APP_TYPE."' AND vt.ePoolStatus = 'No'";
                }
            }
            $sql2 .= ' AND eFly = 1';
            // echo $sql2;exit;
            /*if (!empty($ilocation_id)) {
                $sql2 = "SELECT lm.vLocationName,vt.iLocationId,vt.vVehicleType,vt.iVehicleTypeId FROM  `vehicle_type` as vt LEFT JOIN location_master as lm on lm.iLocationId = vt.iLocationid  WHERE (vt.iLocationid = '-1' OR vt.iLocationid IN ('" . $ilocation_id . "')) AND vt.eStatus='Active' AND vt.eType = '".$APP_TYPE."' AND eFly = 1 AND vt.ePoolStatus = 'No'";
            } else {
                $sql2 = "SELECT lm.vLocationName,vt.iLocationId,vt.vVehicleType,vt.iVehicleTypeId FROM  `vehicle_type` as vt LEFT JOIN location_master as lm on lm.iLocationId = vt.iLocationid  WHERE (vt.iLocationid = '-1') AND vt.eStatus='Active' AND vt.eType = '".$APP_TYPE."' AND eFly = 1 AND vt.ePoolStatus = 'No'";
            }*/
            // echo $sql2;exit;
            $db_select_data = $obj->MySQLSelect($sql2);
            $cons = "<option value=''>Select Vehicle Type</option>";
            foreach ($db_select_data as $k => $val) {
                if (!empty($val['vLocationName'])) {
                    $cons .= "<option value='".$val['iVehicleTypeId']."'";
                    if ($val['iVehicleTypeId'] === $selected) {
                        $cons .= ' selected';
                    }
                    $cons .= '>'.$val['vVehicleType'].' ('.$val['vLocationName'].')'.'</option>';
                } else {
                    $cons .= "<option value='".$val['iVehicleTypeId']."'";
                    if ($val['iVehicleTypeId'] === $selected) {
                        $cons .= ' selected';
                    }
                    $cons .= '>'.$val['vVehicleType'].'</option>';
                }
                // }
            }
        }
    } else {
        $cons = "<option value=''>Select Vehicle Type</option>";
    }
    echo $cons;

    exit;
}
