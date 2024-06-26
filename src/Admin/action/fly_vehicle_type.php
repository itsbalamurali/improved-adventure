<?php



include_once '../../common.php';

$AUTH_OBJ->checkMemberAuthentication();

$reload = $_SERVER['REQUEST_URI'];

$urlparts = explode('?', $reload);
$parameters = $urlparts[1];

$id = $_REQUEST['id'] ?? '';
$iVehicleTypeId = $_REQUEST['iVehicleTypeId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';

// Start make deleted
if (('Deleted' === $statusVal || 'delete' === $method) && ('' !== $iVehicleTypeId || '' !== $checkbox)) {
    if (!$userObj->hasPermission('delete-fly-vehicle-type')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete fly vehicle type';
    } else {
        // Added By Hasmukh On 16-10-2018 For Solved Bug Start
        if ('' !== $iVehicleTypeId) {
            $typeIds = $iVehicleTypeId;
        } else {
            $typeIds = $checkbox;
        }
        // Added By Hasmukh On 16-10-2018 For Solved Bug End
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE vehicle_type SET eStatus ='Deleted' WHERE iVehicleTypeId IN (".$typeIds.')';
            // $query = "DELETE FROM vehicle_type WHERE iVehicleTypeId ='".$iVehicleTypeId."'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $_SESSION['success'] = '2';
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'fly_vehicle_type.php?'.$parameters);

    exit;
}
// End make deleted
// Start Change single Status
if ('' !== $iVehicleTypeId && '' !== $status) {
    if (!$userObj->hasPermission('update-status-vehicle-type')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to changed status of fly vehicle type';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE vehicle_type SET eStatus = '".$status."' WHERE iVehicleTypeId = '".$iVehicleTypeId."'";
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
    header('Location:'.$tconfig['tsite_url_main_admin'].'fly_vehicle_type.php?'.$parameters);

    exit;
}
// End Change single Status
// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (!$userObj->hasPermission('update-status-vehicle-type')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete fly vehicle type';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE vehicle_type SET eStatus = '".$statusVal."' WHERE iVehicleTypeId IN (".$checkbox.')';
            // $query = "DELETE FROM vehicle_type WHERE iVehicleTypeId IN (" . $checkbox . ")";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'fly_vehicle_type.php?'.$parameters);

    exit;
}
// End Change All Selected Status
