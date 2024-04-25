<?php



include_once '../../common.php';

$reload = $_SERVER['REQUEST_URI'];

$urlparts = explode('?', $reload);
$parameters = $urlparts[1];

$id = $_REQUEST['id'] ?? '';
$iDriverId = $_REQUEST['iDriverId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';
// echo "<pre>"; print_r($_REQUEST);
// Start make deleted
if ('delete' === $method && '' !== $iDriverId) {
    if (SITE_TYPE !== 'Demo') {
        $query = "delete from home_driver WHERE iDriverId = '".$iDriverId."'";
        $obj->sql_query($query);
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
    } else {
        $_SESSION['success'] = '2';
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'home_driver.php?'.$parameters);

    exit;
}
// End make deleted

// Start Change single Status
if ('' !== $iDriverId && '' !== $status) {
    if (SITE_TYPE !== 'Demo') {
        $query = "UPDATE home_driver SET eStatus = '".$status."' WHERE iDriverId = '".$iDriverId."'";
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
    header('Location:'.$tconfig['tsite_url_main_admin'].'home_driver.php?'.$parameters);

    exit;
}
// End Change single Status

// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (SITE_TYPE !== 'Demo') {
        if ('Deleted' === $statusVal) {
            $query = 'delete from home_driver WHERE iDriverId IN ('.$checkbox.')';
        } else {
            $query = "UPDATE home_driver SET eStatus = '".$statusVal."' WHERE iDriverId IN (".$checkbox.')';
        }
        $obj->sql_query($query);
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
    } else {
        $_SESSION['success'] = 2;
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'home_driver.php?'.$parameters);

    exit;
}
// End Change All Selected Status
