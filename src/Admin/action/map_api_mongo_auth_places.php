<?php

use MongoDB\BSON\ObjectID;

include_once '../../common.php';
global $userObj;
?>
<script src="<?php echo $tconfig['tsite_url']; ?>assets/plugins/jquery-2.0.3.min.js"></script>
<?php

$ip = $_SERVER['REMOTE_ADDR'] ?: '';
$date = date('Y-m-d');

$AUTH_OBJ->checkMemberAuthentication();
$reload = $_SERVER['REQUEST_URI'];
$urlparts = explode('?', $reload);
$parameters = $urlparts[1];
$id = $_REQUEST['id'] ?? '';
$iOid = $_REQUEST['iOid'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';

$DbName = TSITE_DB;
$TableName = 'auth_accounts_places';

$uniqueFieldName = '_id';
$uniqueFieldValue = trim($iOid);
$tempData['eStatus'] = $status;

if ('Active' === $status) {
    $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_ACTIVATE_MSG'];
} else {
    $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INACTIVATE_MSG'];
}

$searchQuerySid['vServiceId'] = (int) $id;
$AuthMaster_table = 'auth_master_accounts_places';
$TableNameAuthMaster = 'auth_master_accounts_places';
$requiredServicesAry = ['Geocoding', 'AutoComplete', 'Direction'];
$activeRecordsResult = $obj->fetchAllCollectionFromMongoDB($DbName, $TableNameAuthMaster);

foreach ($activeRecordsResult as $key => $activeRecordsResult) {
    if ('Active' === $activeRecordsResult['eStatus']) {
        $AllactiveServices[$key + 1] = $activeRecordsResult['vActiveServices'];
    }
}
unset($AllactiveServices[$id]);
$AllactiveServices = array_values($AllactiveServices);
for ($i = 0; $i <= count($AllactiveServices); ++$i) {
    $explodeData = explode(',', $AllactiveServices[$i]);
    foreach ($explodeData as $Row) {
        if ('' !== $Row) {
            $RowAry[] = $Row;
        }
    }
}
$result = array_diff($requiredServicesAry, $RowAry);

$data_by_serviceID = $obj->fetchAllRecordsFromMongoDBWithDBName($DbName, $AuthMaster_table, $searchQuerySid);

if ('delete' === $method) {
    if ('Active' === $data_by_serviceID[0]['eStatus']) {
        $activeoids = [];
        $serchactiveData['eStatus'] = 'Active';
        $serchactiveData['vServiceId'] = (int) $id;
        $serchactiveDataAry = $obj->fetchAllRecordsFromMongoDBWithDBName($DbName, $TableName, $serchactiveData);
        foreach ($serchactiveDataAry as $valAry) {
            $activeoids[] = $valAry['_id']['$oid'];
        }
        $remove_account_count = 0;
        if (in_array($iOid, $activeoids, true)) {
            $remove_account_count = 1;
        }
        $active_accounts = count($activeoids);
        if (($active_accounts - $remove_account_count) < 1) {
            confirmToDelete($result, $id, $tconfig['tsite_url_main_admin'], $iOid);
        } else {
            $DbName = TSITE_DB;
            $TableName = 'auth_accounts_places';
            $searchQuery = [];
            if ('' !== $iOid) {
                $searchQuery['_id'] = new ObjectID($iOid);
            }
            $deleted = $obj->deleteRecordsFromMongoDB($DbName, $TableName, $searchQuery);
            // $redirect2 = $adminUrl . "map_api_mongo_auth_places.php?id=".$id."";
            header('Location:'.$tconfig['tsite_url_main_admin'].'map_api_mongo_auth_places.php?id='.$id);

            exit;
        }
    }

    $DbName = TSITE_DB;
    $TableName = 'auth_accounts_places';
    $searchQuery = [];
    if ('' !== $iOid) {
        $searchQuery['_id'] = new ObjectID($iOid);
    }
    $deleted = $obj->deleteRecordsFromMongoDB($DbName, $TableName, $searchQuery);
    header('Location:'.$tconfig['tsite_url_main_admin'].'map_api_mongo_auth_places.php?'.$parameters);

    exit;
}

if ((('Inactive' === $statusVal) || ('Inactive' === $status)) && ('Active' === $data_by_serviceID[0]['eStatus'])) {
    // if ($data_by_serviceID[0]['eStatus'] == "Active") {
    $remove_account_count = 0;
    $serchactiveData['eStatus'] = 'Active';
    $serchactiveData['vServiceId'] = (int) $id;
    $serchactiveDataAry = $obj->fetchAllRecordsFromMongoDBWithDBName($DbName, $TableName, $serchactiveData);
    foreach ($serchactiveDataAry as $valAry) {
        $activeoids[] = $valAry['_id']['$oid'];
    }
    if (in_array($iOid, $activeoids, true)) {
        $remove_account_count = 1;
    }

    if ('' !== $checkbox) {
        $checkboxExplode = explode(',', $checkbox);
        $remove_account_count = count($checkboxExplode);
    }
    $serchactiveData['eStatus'] = 'Active';
    $serchactiveData['vServiceId'] = (int) $id;
    $serchactiveDataAry = $obj->fetchAllRecordsFromMongoDBWithDBName($DbName, $TableName, $serchactiveData);

    $active_accounts = count($serchactiveDataAry);
    $redirect = $tconfig['tsite_url_main_admin'].'map_api_mongo_auth_places.php?'.$parameters;
    // $active_accounts = 1;
    if (($active_accounts - $remove_account_count) < 1) {
        $info = [];
        $info['changestatusbyajx'] = 'Y';
        $info['checkbox'] = $checkbox;
        $info['parameters'] = $parameters;
        $info['ioid'] = $iOid;
        $info['vServiceId'] = (int) $id;

        $json_format = json_encode($info);
        echo "<script language='JavaScript' type='text/javascript' >
        function goback(){
            window.location.href ='{$redirect}';
        }
        var countResult = ".count($result).";
            if (confirm('Your service will be inactive. Do you like to inactive service?')) {
                if(countResult > 0){
                    alert('Keep atleast one service active.');
                    goback();
                }else{
                    $.ajax({
                            type: 'POST',
                            data: {info:'{$json_format}'},
                            url: 'map_api_mongo_auth_places_ajax.php',
                            success: function(msg){
                                window.location.href ='{$redirect}';
                            }
                        });
            }
        }else{
            window.location.href ='{$redirect}';
        }
        </script>";
    } else {
        if ('' !== $checkbox) {
            if ('' !== $statusVal) {
                $tempData['eStatus'] = $statusVal;
                $checkbox = explode(',', $checkbox);
                for ($i = 0; $i < count($checkbox); ++$i) {
                    $updated = $obj->updateRecordsToMongoDBWithDBNameById($DbName, $TableName, $uniqueFieldName, $checkbox[$i], $tempData);
                }
                header('Location:'.$tconfig['tsite_url_main_admin'].'map_api_mongo_auth_places.php?'.$parameters);

                exit;
            }
        } else {
            if ('' !== $uniqueFieldValue) {
                $updated = $obj->updateRecordsToMongoDBWithDBNameById($DbName, $TableName, $uniqueFieldName, $uniqueFieldValue, $tempData);
                header('Location:'.$tconfig['tsite_url_main_admin'].'map_api_mongo_auth_places.php?'.$parameters);

                exit;
            }
        }
    }
// }
} else {
    if ('' !== $checkbox) {
        if ('' !== $statusVal) {
            $tempData['eStatus'] = $statusVal;
            $checkbox = explode(',', $checkbox);
            for ($i = 0; $i < count($checkbox); ++$i) {
                $updated = $obj->updateRecordsToMongoDBWithDBNameById($DbName, $TableName, $uniqueFieldName, $checkbox[$i], $tempData);
            }
            header('Location:'.$tconfig['tsite_url_main_admin'].'map_api_mongo_auth_places.php?'.$parameters);

            exit;
        }
    } else {
        if ('' !== $uniqueFieldValue) {
            $updated = $obj->updateRecordsToMongoDBWithDBNameById($DbName, $TableName, $uniqueFieldName, $uniqueFieldValue, $tempData);
            header('Location:'.$tconfig['tsite_url_main_admin'].'map_api_mongo_auth_places.php?'.$parameters);

            exit;
        }
    }
}
function confirmToDelete($result, $id, $adminUrl, $iOid): void
{
    $redirect2 = $adminUrl.'map_api_mongo_auth_places.php?id='.$id.'';
    $info = [];
    $info['changestatusbyajx'] = 'Y';
    $info['deleteme'] = 'Y';
    $info['parameters'] = $parameters;
    $info['ioid'] = $iOid;
    $info['vServiceId'] = (int) $id;

    $json_format = json_encode($info);
    echo "<script language='JavaScript' type='text/javascript' >

        function gobackme(){
            window.location.href ='{$redirect2}';
        }
        var countResult = ".count($result).";

            if (confirm('Your service will be inactive. Do you like to inactive service?')) {
                if(countResult > 0){
                    alert('Keep atleast one service active.');
                    gobackme();
                }else{
                    $.ajax({
                            type: 'POST',
                            data: {info:'{$json_format}'},
                            url: 'map_api_mongo_auth_places_ajax.php',
                            success: function(msg){
								alert(msg);
                                window.location.href ='{$redirect2}';
                            }
                        });
            }
        }else{
            window.location.href ='{$redirect2}';
        }
        </script>";

    exit;
}
