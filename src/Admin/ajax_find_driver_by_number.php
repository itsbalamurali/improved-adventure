<?php



include_once '../common.php';

$getlan = $_REQUEST['getlan'] ?? '';
$getlng = $_REQUEST['getlng'] ?? '';
$googlekey = $_REQUEST['googlekey'] ?? '';
$limitdistance = $_REQUEST['limitdistance'] ?? '';

$str_date = @date('Y-m-d H:i:s', strtotime('-5 minutes'));
// $LIST_DRIVER_LIMIT_BY_DISTANCE = $CONFIG_OBJ->getConfigurations("configurations","LIST_DRIVER_LIMIT_BY_DISTANCE");
$DRIVER_REQUEST_METHOD = $CONFIG_OBJ->getConfigurations('configurations', 'DRIVER_REQUEST_METHOD');
$param = ('Time' === $DRIVER_REQUEST_METHOD) ? 'tOnline' : 'tLastOnline';

$sql = 'SELECT ROUND(( 3959 * acos( cos( radians('.$getlan.') ) * cos( radians( vLatitude ) ) * cos( radians( vLongitude ) - radians('.$getlng.') ) + sin( radians('.$getlan.") ) * sin( radians( vLatitude ) ) ) ),2) AS distance, register_driver.*  FROM `register_driver`
WHERE (vLatitude != '' AND vLongitude != '' AND vAvailability = 'Available' AND vTripStatus != 'Active' AND eStatus='active' AND tLastOnline > '{$str_date}') HAVING distance < ".$limitdistance.' ORDER BY `register_driver`.`'.$param.'` ASC';
$Data = $obj->MySQLSelect($sql);

$store = [];
for ($i = 0; $i < count($Data); ++$i) {
    $store[$i]['name'] = clearName($Data[$i]['vName'].' '.$Data[$i]['vLastName']);
    $store[$i]['add'] = $Data[$i]['vCaddress'];
    $store[$i]['lat'] = $Data[$i]['vLatitude'];
    $store[$i]['lag'] = $Data[$i]['vLongitude'];
    $store[$i]['iDriverId'] = $Data[$i]['iDriverId'];
}
echo json_encode($store);

exit;
