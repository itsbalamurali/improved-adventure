<?php



include_once '../../common.php';

$reload = $_SERVER['REQUEST_URI'];
$urlparts = explode('?', $reload);
$parameters = $urlparts[1];
$id = $_REQUEST['id'] ?? '';
$iStateId = $_REQUEST['iStateId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';
// echo "<pre>"; print_r($_REQUEST);
// Start state deleted
if (('Deleted' === $statusVal || 'delete' === $method) && ('' !== $iStateId || '' !== $checkbox)) {
    if (!$userObj->hasPermission('delete-state')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete state';
    } else {
        // Added By Hasmukh On 05-10-2018 For Solved Bug Start
        if ('' !== $iStateId) {
            $stateIds = $iStateId;
        } else {
            $stateIds = $checkbox;
        }
        // Added By Hasmukh On 05-10-2018 For Solved Bug End
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE state SET eStatus = 'Deleted' WHERE iStateId IN (".$stateIds.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $_SESSION['success'] = '2';
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'state.php?'.$parameters);

    exit;
}
// End state deleted
// Start Change single Status
if ('' !== $iStateId && '' !== $status) {
    if (!$userObj->hasPermission('update-status-state')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of state';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE state SET eStatus = '".$status."' WHERE iStateId = '".$iStateId."'";
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
    header('Location:'.$tconfig['tsite_url_main_admin'].'state.php?'.$parameters);

    exit;
}
// End Change single Status
// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (!$userObj->hasPermission('update-status-state')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of state';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE state SET eStatus = '".$statusVal."' WHERE iStateId IN (".$checkbox.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'state.php?'.$parameters);

    exit;
}
// End Change All Selected Status
