<?php



include_once '../common.php';

$drivertxt = $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];
$iMemberId = $_REQUEST['iMemberId'] ?? '';
$vLatitude = $_REQUEST['vLatitude'] ?? '';
$vLongitude = $_REQUEST['vLongitude'] ?? '';
$vTimeZone = $_REQUEST['vTimeZone'] ?? '';

$sql = "SELECT tMessage as msg, iStatusId,iDriverId,iTripId FROM trip_status_messages WHERE iUserId='".$iMemberId."' AND eToUserType='Passenger' AND eReceived='Yes' ORDER BY iStatusId DESC LIMIT 1 ";
$msg = $obj->MySQLSelect($sql);
if (!empty($msg)) {
    $iDriverId = $msg[0]['iDriverId'];
    $iTripId = $msg[0]['iTripId'];
    $AllData = json_decode($msg[0]['msg'], true);

    if ('' !== $iTripId) {
        $sql = "SELECT register_driver.*,make.vMake,model.vTitle,driver_vehicle.vLicencePlate FROM register_driver LEFT JOIN driver_vehicle on driver_vehicle.iDriverVehicleId=register_driver.iDriverVehicleId LEFT JOIN make on make.iMakeId=driver_vehicle.iMakeId LEFT JOIN model on model.iModelId=driver_vehicle.iModelId WHERE register_driver.iTripId = '".$iTripId."'";

        $drivers = $obj->MySQLSelect($sql);
        $driver = $drivers[0];
        $iDriverVehicleId = $driver['iDriverVehicleId'];

        $sql2 = "SELECT * FROM vehicle_type WHERE iVehicleTypeId='".$iDriverVehicleId."'";
        $DriverVehicleData = $obj->MySQLSelect($sql2);
        $vLicencePlate = $DriverVehicleData[0]['vLicencePlate'];
        $driver['vehicleType'] = $DriverVehicleData[0]['vVehicleType_EN'];
    }

    echo '<div class="arriving-bottom-part-top"><h5>'.$drivertxt.' '.$driver['vName'].' '.$driver['vLastName'].' is arriving with car number '.$driver['vLicencePlate'].'. If you have any query then call on (<b> +'.$driver['vCode'].' '.$driver['vPhone'].'</b>).</h5></div>';

    exit;
}
