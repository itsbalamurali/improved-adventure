<?php



include '../common.php';
$id = $_REQUEST['id'] ?? '';

if ('' !== $id && '-1' !== $id) {
    $sql = "select iCountryId from location_master where iLocationId='{$id}'";
    $data_con = $obj->MySQLSelect($sql);

    $sql = "select eUnit from country where iCountryId='".$data_con[0]['iCountryId']."'";
    $data_unit = $obj->MySQLSelect($sql);

    $eUnit = $data_unit[0]['eUnit'];
} else {
    $eUnit = $DEFAULT_DISTANCE_UNIT;
}
echo $eUnit;

exit;
