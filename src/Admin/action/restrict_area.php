<?php



include_once '../../common.php';

$reload = $_SERVER['REQUEST_URI'];

$urlparts = explode('?', $reload);
$parameters = $urlparts[1];

$id = $_REQUEST['id'] ?? '';
$iRestrictedNegativeId = $_REQUEST['iRestrictedNegativeId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';
// echo "<pre>"; print_r($_REQUEST);
// Start make deleted
if ('delete' === $method && '' !== $iRestrictedNegativeId) {
    if (!$userObj->hasPermission('delete-restricted-area')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete restricted area';
    } else {
        if (SITE_TYPE !== 'Demo') {
            // $query = "DELETE FROM restricted_negative_area WHERE iRestrictedNegativeId = '" . $iRestrictedNegativeId . "'";
            $query = "UPDATE restricted_negative_area SET eStatus = 'Deleted' WHERE iRestrictedNegativeId = '".$iRestrictedNegativeId."'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $_SESSION['success'] = '2';
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'restricted_area.php?'.$parameters);

    exit;
}
// End make deleted

// Start Change single Status
if ('' !== $iRestrictedNegativeId && '' !== $status) {
    if (!$userObj->hasPermission('update-status-restricted-area')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of restricted area';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE restricted_negative_area SET eStatus = '".$status."' WHERE iRestrictedNegativeId = '".$iRestrictedNegativeId."'";
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
    header('Location:'.$tconfig['tsite_url_main_admin'].'restricted_area.php?'.$parameters);

    exit;
}
// End Change single Status

// Start Change All Selected Status+

if ('' !== $checkbox && '' !== $statusVal) {
    if (!$userObj->hasPermission(['update-status-restricted-area', 'delete-restricted-area'])) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of restricted area';
    } else {
        if (SITE_TYPE !== 'Demo') {
            // echo $statusVal; die;
            if ('Deleted' === $statusVal) {
                // $query = "DELETE FROM restricted_negative_area WHERE iRestrictedNegativeId IN (" . $checkbox . ")";
                $query = "UPDATE restricted_negative_area SET eStatus = '".$statusVal."' WHERE iRestrictedNegativeId IN (".$checkbox.')';
            } else {
                $query = "UPDATE restricted_negative_area SET eStatus = '".$statusVal."' WHERE iRestrictedNegativeId IN (".$checkbox.')';
            }
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'restricted_area.php?'.$parameters);

    exit;
}
