<?php



include_once '../../common.php';

$reload = $_SERVER['REQUEST_URI'];

$urlparts = explode('?', $reload);
$parameters = $urlparts[1];

$id = $_REQUEST['id'] ?? '';
$masknum_id = $_REQUEST['masknum_id'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';

// Start Masking Number deleted
if ('delete' === $method && '' !== $masknum_id) {
    if (SITE_TYPE !== 'Demo') {
        $query = "DELETE FROM masking_numbers WHERE masknum_id = '".$masknum_id."'";
        $obj->sql_query($query);
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
    } else {
        $_SESSION['success'] = '2';
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'masking_numbers.php?'.$parameters);

    exit;
}
// End Masking Number deleted

// Start Change single Status
if ('' !== $masknum_id && '' !== $status) {
    if (SITE_TYPE !== 'Demo') {
        $query = "UPDATE masking_numbers SET eStatus = '".$status."' WHERE masknum_id = '".$masknum_id."'";
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
    header('Location:'.$tconfig['tsite_url_main_admin'].'masking_numbers.php?'.$parameters);

    exit;
}
// End Change single Status

// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (SITE_TYPE !== 'Demo') {
        if ('Deleted' === $statusVal) {
            $query = 'DELETE FROM masking_numbers WHERE masknum_id IN ('.$checkbox.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $query = "UPDATE masking_numbers SET eStatus = '".$statusVal."' WHERE masknum_id IN (".$checkbox.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        }
    } else {
        $_SESSION['success'] = 2;
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'masking_numbers.php?'.$parameters);

    exit;
}
// End Change All Selected Status
