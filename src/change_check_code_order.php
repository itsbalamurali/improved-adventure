<?php





include 'common.php';
$eUnit = $_REQUEST['eUnit'] ?? '';
if ('yes' === $eUnit) {
    $sql = "select vPhoneCode,eUnit,vCountryCode,vTimeZone from country where vCountry = '".$_REQUEST['id']."' OR iCountryId = '".$_REQUEST['id']."'";
    $db_data = $obj->MySQLSelect($sql);
    echo json_encode($db_data[0]);
} else {
    $sql = "select vPhoneCode from country where vCountry = '".$_REQUEST['id']."'";
    $db_data = $obj->MySQLSelect($sql);
    echo $db_data[0]['vPhoneCode'];

    exit;
}
