<?php



use MongoDB\BSON\ObjectID;

include_once '../common.php';
global $userObj;

if (!$userObj->hasPermission('view-providers')) {
    $userObj->redirect();
}

$iServiceOid = $_REQUEST['iServiceOid'] ?? '';
if ('' !== $iServiceOid) {
    $searchQuery['_id'] = new ObjectID($iServiceOid);
}
$status = $_REQUEST['status'];
$row_id = $_REQUEST['row_id'];
$countActiveServices = $_REQUEST['countActiveServices'];
$DbName = TSITE_DB;
// $DbName = "PlacesDataCollection";
$TableName = 'auth_master_accounts_places';
$data_drv = $obj->fetchAllRecordsFromMongoDBWithDBName($DbName, $TableName, $searchQuery);

if ('' !== $iServiceOid && '' !== $data_drv) {
    $activeServiceArry = explode(',', $data_drv[0]['vActiveServices']);
    $availablesearvices = explode(',', $data_drv[0]['vAvailableServices']);
    $key = array_search('PlaceDetails', $availablesearvices, true);
    if (false !== $key) {
        unset($availablesearvices[$key]);
        $PlaceDetails = 'Y';
    }
    $html = '';
    if (count($availablesearvices) > 0) {
        $html = '<form name="service_config_frm" id="service_config_frm" method="post">';
        foreach ($availablesearvices as $key => $availableService) {
            $checked = '';
            if (in_array($availableService, $activeServiceArry, true)) {
                $checked = ' checked';
            }
            $html .= '<label><input type="checkbox" '.$checked.' name="selectedval[]" value="'.$availableService.'" /> &nbsp;'.$availableService.'</label></br>';
        }
        if ('Y' === $PlaceDetails) {
            $html .= '<input type="hidden" name="selectedval[]" value="PlaceDetails" />';
        }
        $html .= '<input type="hidden" name="iServiceOid" value="'.$iServiceOid.'">';
        $html .= '<input type="hidden" id="row_id" value="'.$row_id.'">';
        $html .= '<input type="hidden" id="status" value="'.$status.'">';
        $html .= '<input type="hidden" id="countActiveServices" value="'.$countActiveServices.'">';
        if (SITE_TYPE !== 'Demo') {
            $html .= '<br><button type="button" onClick="update_service_config()" class="btn btn-success no-cursor btn-sm">Update</button>';
        }
        $html .= '</form>';
    }
} else {
    $html .= '<p>No service available.</p>';
}
echo $html;

exit;
