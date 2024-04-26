<?php



include_once '../../common.php';

$reload = $_SERVER['REQUEST_URI'];

$urlparts = explode('?', $reload);
$parameters = $urlparts[1];

$id = $_REQUEST['id'] ?? '';
$iBackupId = $_REQUEST['iBackupId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';
// echo "<pre>"; print_r($_REQUEST);
// Start make deleted
if ('delete' === $method && '' !== $iBackupId) {
    if (!$userObj->hasPermission('delete-db-backup')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete DB backup';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "delete from  backup_database WHERE iBackupId = '".$iBackupId."'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $_SESSION['success'] = '2';
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'backup.php?'.$parameters);

    exit;
}
// End make deleted
// Start Change single Status
if ('' !== $iBackupId && '' !== $status) {
    if (!$userObj->hasPermission('delete-db-backup')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete DB backup';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE backup_database SET eStatus = '".$status."' WHERE iBackupId = '".$iBackupId."'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            if ('Active' === $status) {
                $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_ACTIVATE_MSG'];
            } else {
                $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INACTIVATE_MSG'];
            }
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'backup.php?'.$parameters);

    exit;
}
// End Change single Status
// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (!$userObj->hasPermission('delete-db-backup')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete DB backup';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE backup_database SET eStatus = '".$statusVal."' WHERE iBackupId IN (".$checkbox.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'backup.php?'.$parameters);

    exit;
}
// End Change All Selected Status
// if ($iBackupId != '' && $status != '') {
//    if (SITE_TYPE != 'Demo') {
//        $query = "UPDATE backup_database SET eStatus = '" . $status . "' WHERE iBackupId = '" . $iBackupId . "'";
//        $obj->sql_query($query);
//        $_SESSION['success'] = '1';
//        $_SESSION['var_msg'] = "Admin " . $status . " Successfully.";
//        header("Location:".$tconfig["tsite_url_main_admin"]."admin.php?".$parameters);
//        exit;
//    } else {
//        $_SESSION['success']=2;
//        header("Location:".$tconfig["tsite_url_main_admin"]."admin.php?".$parameters);
//        exit;
//    }
// }
