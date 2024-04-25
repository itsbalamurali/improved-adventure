<?php



use MongoDB\BSON\ObjectID;

include_once '../common.php';

global $userObj;

require_once TPATH_CLASS.'/Imagecrop.class.php';

$uid = $_REQUEST['usageOrder'];
$sid = $_REQUEST['sid'];
$actionv = $_REQUEST['actionv'];
$id = $_REQUEST['id'];

if ('' !== $uid && '' === $_REQUEST['map_api_setting']) {
    $searchQueryNew['vUsageOrder'] = (int) $uid;
}
if ('' !== $uid && '' !== $_REQUEST['map_api_setting']) {
    $searchQueryNew['vUsageOrder'] = $uid;
}
if ('' !== $sid && '' === $_REQUEST['map_api_setting']) {
    $searchQueryNew['vServiceId'] = (int) $sid;
}
if ('' !== $id && '' === $_REQUEST['map_api_setting']) {
    $searchQueryNew['_id']['$ne'] = new ObjectID($id);
}
if ('' !== $sid && '' !== $_REQUEST['map_api_setting']) {
    $searchQueryNew['vServiceId']['$ne'] = (int) $sid;
    //	$searchQueryNew['vServiceId']['$ne'] = $sid;
}

$DbName = TSITE_DB;

if ('' !== $_REQUEST['map_api_setting']) {
    $TableName = 'auth_master_accounts_places';
} else {
    $TableName = 'auth_accounts_places';
}
$data_drv = $obj->fetchAllRecordsFromMongoDBWithDBName($DbName, $TableName, $searchQueryNew);

echo count($data_drv);

exit;
