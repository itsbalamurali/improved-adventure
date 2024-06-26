<?php



include_once '../../common.php';

$reload = $_SERVER['REQUEST_URI'];

$urlparts = explode('?', $reload);
$parameters = $urlparts[1];

$id = $_REQUEST['id'] ?? '';
$iLocatioId = $_REQUEST['iLocatioId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';
// print_R($_REQUEST);die;
// Start make deleted
if (('Deleted' === $statusVal || 'delete' === $method) && ('' !== $iLocatioId || '' !== $checkbox)) {
    if (!$userObj->hasPermission('delete-location-wise-fare')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete location wise fare';
    } else {
        // Added By Hasmukh On 12-10-2018 For Solved Bug Start
        if ('' !== $iLocatioId) {
            $locationIds = $iLocatioId;
        } else {
            $locationIds = $checkbox;
        }
        // Added By Hasmukh On 12-10-2018 For Solved Bug End
        if (SITE_TYPE !== 'Demo') {
            // $query = "DELETE FROM location_wise_fare WHERE iLocatioId IN (" . $locationIds . ")";
            $query = "UPDATE location_wise_fare SET eStatus = 'Deleted'  WHERE iLocatioId IN (".$locationIds.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $_SESSION['success'] = '2';
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'locationwise_fare.php?'.$parameters);

    exit;
}
// End make deleted
// Start Change single Status
if ('' !== $iLocatioId && '' !== $status) {
    if (!$userObj->hasPermission('update-status-location-wise-fare')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to update status of location wise fare';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE location_wise_fare SET eStatus = '".$status."' WHERE iLocatioId = '".$iLocatioId."'";
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
    header('Location:'.$tconfig['tsite_url_main_admin'].'locationwise_fare.php?'.$parameters);

    exit;
}
// End Change single Status
// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (!$userObj->hasPermission('update-status-location-wise-fare')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to update status of location wise fare';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE location_wise_fare SET eStatus = '".$statusVal."' WHERE iLocatioId IN (".$checkbox.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'locationwise_fare.php?'.$parameters);

    exit;
}
// End Change All Selected Status
