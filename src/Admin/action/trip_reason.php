<?php



include_once '../../common.php';
define('TRIP_REASON', 'trip_reason');

$AUTH_OBJ->checkMemberAuthentication();
$reload = $_SERVER['REQUEST_URI'];
$urlparts = explode('?', $reload);
$parameters = $urlparts[1];
$id = $_REQUEST['id'] ?? '';
$iTripReasonId = $_REQUEST['iTripReasonId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';
// echo "<pre>"; print_r($_REQUEST); die;
// Start make deleted
if (('Deleted' === $statusVal || 'delete' === $method) && ('' !== $iTripReasonId || '' !== $checkbox)) {
    if (!$userObj->hasPermission('delete-trip-reason-taxi-service')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete trip reason';
    } else {
        // Added By Hasmukh On 05-10-2018 For Solved Bug Start
        if ('' !== $iTripReasonId) {
            $reasonIds = $iTripReasonId;
        } else {
            $reasonIds = $checkbox;
        }
        // Added By Hasmukh On 05-10-2018 For Solved Bug End
        if (SITE_TYPE !== 'Demo') {
            $query = 'UPDATE '.TRIP_REASON." SET eStatus = 'deleted' WHERE iTripReasonId IN (".$reasonIds.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $_SESSION['success'] = '2';
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'trip_reason.php?'.$parameters);

    exit;
}
// End make deleted
// Start Change single Status
if ('' !== $iTripReasonId && '' !== $status) {
    if (!$userObj->hasPermission('update-status-trip-reason-taxi-service')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of trip reason';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = 'UPDATE '.TRIP_REASON." SET eStatus = '".$status."' WHERE iTripReasonId = '".$iTripReasonId."'";
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
    header('Location:'.$tconfig['tsite_url_main_admin'].'trip_reason.php?'.$parameters);

    exit;
}
// End Change single Status
// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (!$userObj->hasPermission('update-status-trip-reason-taxi-service')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of trip reason';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = 'UPDATE '.TRIP_REASON." SET eStatus = '".$statusVal."' WHERE iTripReasonId IN (".$checkbox.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'trip_reason.php?'.$parameters);

    exit;
}
// End Change All Selected Status
