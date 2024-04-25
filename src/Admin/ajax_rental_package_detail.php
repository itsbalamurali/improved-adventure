<?php



include_once '../common.php';

$iRentalPackageId = $_REQUEST['iRentalPackageId'] ?? '';
$iVehicleTypeId = $_REQUEST['iVehicleTypeId'] ?? '';
$action = $_REQUEST['action'] ?? '';
$id = $_REQUEST['id'] ?? '';

$fPrice = $_POST['fPrice'] ?? '';
$fKiloMeter = $_POST['fKiloMeter'] ?? '';
$fHour = $_POST['fHour'] ?? '';
$fPricePerKM = $_POST['fPricePerKM'] ?? '';
$fPricePerHour = $_POST['fPricePerHour'] ?? '';

$tbl_name = 'rental_package';
$sql = 'SELECT * FROM '.$tbl_name." WHERE iRentalPackageId = '".$iRentalPackageId."' AND iVehicleTypeId='".$iVehicleTypeId."'";
$db_data = $obj->MySQLSelect($sql);

if (count($db_data) > 0) {
    echo json_encode($db_data[0]);

    exit;
}
echo 0;

exit;
