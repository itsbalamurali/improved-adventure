<?php



include_once '../../common.php';
global $userObj;

$ip = $_SERVER['REMOTE_ADDR'] ?: '';
$date = date('Y-m-d');
$AUTH_OBJ->checkMemberAuthentication();
$reload = $_SERVER['REQUEST_URI'];
$urlparts = explode('?', $reload);
$parameters = $urlparts[1];
$id = $_REQUEST['id'] ?? '';
$iMongoName = $_REQUEST['iMongoName'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';

$DbName = TSITE_DB;
$TableName = 'auth_master_accounts_places';
$uniqueFieldName = 'vServiceName';
$uniqueFieldValue = $iMongoName;
$tempData['eStatus'] = $status;

if ('Active' === $status) {
    $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_ACTIVATE_MSG'];
} elseif ('Inactive' === $status) {
    $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INACTIVATE_MSG'];
} else {
    $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
}

if ('' !== $checkbox) {
    if ('' !== $statusVal) {
        $tempData['eStatus'] = $statusVal;
        $checkbox = explode(',', $checkbox);
        for ($i = 0; $i < count($checkbox); ++$i) {
            if ('Delete' !== $statusVal) {
                $updated = $obj->updateRecordsToMongoDBWithDBName($DbName, $TableName, $uniqueFieldName, $checkbox[$i], $tempData);
            } else {
                $obj->deleteRecordsFromMongoDB($DbName, $TableName, [$uniqueFieldName => $checkbox[$i]]);
            }
        }
        header('Location:'.$tconfig['tsite_url_main_admin'].'map_api_setting.php?'.$parameters);

        exit;
    }
} else {
    if ('' !== $uniqueFieldValue) {
        if ('Delete' !== $status) {
            $updated = $obj->updateRecordsToMongoDBWithDBName($DbName, $TableName, $uniqueFieldName, $uniqueFieldValue, $tempData);
            header('Location:'.$tconfig['tsite_url_main_admin'].'map_api_setting.php?'.$parameters);

            exit;
        }

        $obj->deleteRecordsFromMongoDB($DbName, $TableName, [$uniqueFieldName => $uniqueFieldValue]);
        header('Location:'.$tconfig['tsite_url_main_admin'].'map_api_setting.php?'.$parameters);

        exit;
    }
}
