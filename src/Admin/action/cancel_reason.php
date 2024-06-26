<?php



include_once '../../common.php';

$reload = $_SERVER['REQUEST_URI'];

$urlparts = explode('?', $reload);
$parameters = $urlparts[1];

$id = $_REQUEST['id'] ?? '';
$iCancelReasonId = $_REQUEST['iCancelReasonId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';
// echo "<pre>"; print_r($_REQUEST);exit;
// die;
// Start make deleted

if ('delete' === $method && '' !== $iCancelReasonId) {
    if (SITE_TYPE !== 'Demo') {
        $query = "UPDATE register_driver SET eStatus = 'Deleted' WHERE iDriverId = '".$iDriverId."'";
        $obj->sql_query($query);
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
    } else {
        $_SESSION['success'] = '2';
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'cancel_reason.php?'.$parameters);

    exit;
}
// End make deleted

// Start Change single Status
if ('' !== $iCancelReasonId && '' !== $status) {
    if (!$userObj->hasPermission('delete-cancel-reasons')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete cancel reasons';
    } else {
        if (SITE_TYPE !== 'Demo') {
            // echo "sa";exit;
            $query = "UPDATE cancel_reason SET eStatus = '".$status."' WHERE iCancelReasonId = '".$iCancelReasonId."'";
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
    header('Location:'.$tconfig['tsite_url_main_admin'].'cancel_reason.php?'.$parameters);

    exit;
}
// End Change single Status

// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (!$userObj->hasPermission(['update-status-cancel-reasons', 'delete-cancel-reasons'])) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of cancel reasons';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE cancel_reason SET eStatus = '".$statusVal."' WHERE iCancelReasonId IN (".$checkbox.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'cancel_reason.php?'.$parameters);

    exit;
}
// End Change All Selected Status

// if ($iDriverId != '' && $status != '') {
//    if (SITE_TYPE != 'Demo') {
//        $query = "UPDATE register_driver SET eStatus = '" . $status . "' WHERE iDriverId = '" . $iDriverId . "'";
//        $obj->sql_query($query);
//        $_SESSION['success'] = '1';
//        $_SESSION['var_msg'] = "Driver " . $status . " Successfully.";
//        header("Location:".$tconfig["tsite_url_main_admin"]."driver.php?".$parameters);
//        exit;
//    } else {
//        $_SESSION['success']=2;
//        header("Location:".$tconfig["tsite_url_main_admin"]."driver.php?".$parameters);
//        exit;
//    }
// }
