<?php



include_once '../../common.php';

$reload = $_SERVER['REQUEST_URI'];

$urlparts = explode('?', $reload);
$parameters = $urlparts[1];

$iAirportLocationId = $_REQUEST['iAirportLocationId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';

// Start Location deleted
if ('delete' === $method && '' !== $iAirportLocationId) {
    if (SITE_TYPE !== 'Demo') {
        $query = "DELETE FROM airport_location_master WHERE iAirportLocationId = '".$iAirportLocationId."'";
        $obj->sql_query($query);
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
    } else {
        $_SESSION['success'] = '2';
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'location-airport.php?'.$parameters);

    exit;
}
// End Location deleted

// Start Change single Status
if ('' !== $iAirportLocationId && '' !== $status) {
    if (SITE_TYPE !== 'Demo') {
        $query = "UPDATE airport_location_master SET eStatus = '".$status."' WHERE iAirportLocationId = '".$iAirportLocationId."'";
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
    header('Location:'.$tconfig['tsite_url_main_admin'].'location-airport.php?'.$parameters);

    exit;
}
// End Change single Status

// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (SITE_TYPE !== 'Demo') {
        if ('Deleted' === $statusVal) {
            $query = 'DELETE FROM airport_location_master WHERE iAirportLocationId IN ('.$checkbox.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $query = "UPDATE airport_location_master SET eStatus = '".$statusVal."' WHERE iAirportLocationId IN (".$checkbox.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        }
    } else {
        $_SESSION['success'] = 2;
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'location-airport.php?'.$parameters);

    exit;
}
// End Change All Selected Status
