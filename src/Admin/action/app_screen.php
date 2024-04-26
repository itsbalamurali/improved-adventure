<?php



include_once '../../common.php';

$AUTH_OBJ->checkMemberAuthentication();
$reload = $_SERVER['REQUEST_URI'];
$urlparts = explode('?', $reload);
$parameters = $urlparts[1];
$id = $_REQUEST['id'] ?? '';
$lPage_id = $_REQUEST['lPage_id'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';

$tableName = 'app_screen_master';
$redirectUrl = $tconfig['tsite_url_main_admin'].'app_screen.php?'.$parameters;
if (('Deleted' === $statusVal || 'delete' === $method) && ('' !== $lPage_id || '' !== $checkbox)) {
    if (!$userObj->hasPermission('delete-app-screen')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete record';
    } else {
        // Added By Hasmukh On 12-10-2018 For Solved Bug Start
        if ('' !== $lPage_id) {
            $catIds = $lPage_id;
        } else {
            $catIds = $checkbox;
        }
        // Added By Hasmukh On 12-10-2018 For Solved Bug End
        if (SITE_TYPE !== 'Demo') {
            $getImages = $obj->MySQLSelect('SELECT vScreenImage FROM  '.$tableName.' WHERE lPage_id IN ('.$catIds.')');
            $query = 'DELETE FROM  '.$tableName.' WHERE lPage_id IN ('.$catIds.')';
            $obj->sql_query($query);
            for ($g = 0; $g < count($getImages); ++$g) {
                $img_path = $tconfig['tsite_upload_manage_app_screen'].'/'.$getImages[$g]['vScreenImage'];
                unlink($img_path);
            }
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $_SESSION['success'] = '2';
        }
    }
    header('Location:'.$redirectUrl);

    exit;
}

// Start Change single Status
if ('' !== $lPage_id && '' !== $status) {
    if (!$userObj->hasPermission('update-status-app-screen')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of record';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = 'UPDATE '.$tableName." SET eStatus = '".$status."' WHERE lPage_id = '".$lPage_id."'";
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
    header('Location:'.$redirectUrl);

    exit;
}
// End Change single Status
// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (!$userObj->hasPermission('update-status-app-screen')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of record';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = 'UPDATE '.$tableName." SET eStatus = '".$statusVal."' WHERE lPage_id IN (".$checkbox.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$redirectUrl);

    exit;
}
