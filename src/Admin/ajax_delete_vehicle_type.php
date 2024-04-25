<?php



include_once '../common.php';

$iVehicleTypeId = $_REQUEST['id'] ?? '';
if ('' !== $iVehicleTypeId) {
    $sql = 'select vCarType from driver_vehicle';
    $db_model = $obj->MySQLSelect($sql);

    $store = [];
    for ($i = 0; $i < count($db_model); ++$i) {
        $abc = explode(',', $db_model[$i]['vCarType']);
        $flag = true;
        if (in_array($iVehicleTypeId, $abc, true)) {
            $flag = true;
            echo $flag;
        } else {
            $flag = false;
            echo $flag;

            exit;
        }
    }
}
