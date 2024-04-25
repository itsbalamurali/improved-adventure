<?php



include_once '../../common.php';

$reload = $_SERVER['REQUEST_URI'];

$urlparts = explode('?', $reload);
$parameters = $urlparts[1];

$id = $_REQUEST['id'] ?? '';
$iHelpsId = $_REQUEST['iHelpsId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';

$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';
// echo '<pre>'; print_r($_REQUEST); echo '</pre>'; die;
// Start faqs deleted
if ('delete' === $method && '' !== $iHelpsId) {
    if (SITE_TYPE !== 'Demo') {
        // $query = "UPDATE faqs SET eStatus = 'Deleted' WHERE iFaqId = '" . $iFaqId . "'";
        $query = "DELETE FROM helps WHERE iHelpsId = '".$iHelpsId."'";
        $obj->sql_query($query);
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
    } else {
        $_SESSION['success'] = '2';
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'helps.php?'.$parameters);

    exit;
}
// End faqs deleted

// Start Change single Status
if ('' !== $iHelpsId && '' !== $status) {
    if (SITE_TYPE !== 'Demo') {
        $query = "UPDATE helps SET eStatus = '".$status."' WHERE iHelpsId = '".$iHelpsId."'";
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
    header('Location:'.$tconfig['tsite_url_main_admin'].'helps.php?'.$parameters);
    echo 'test';

    exit;

    exit;
}
// End Change single Status

// Start Change All Deleted Selected Status
// echo '<pre>'; print_r($_REQUEST); echo '</pre>'; die;

if ('' !== $checkbox && 'Deleted' === $statusVal) {
    if (SITE_TYPE !== 'Demo') {
        $query = 'DELETE FROM helps WHERE iHelpsId IN ('.$checkbox.')'; // die;

        $obj->sql_query($query);
        $status = 'deleted';
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        header('Location:'.$tconfig['tsite_url_main_admin'].'helps.php?'.$parameters);

        exit;
    }
    $_SESSION['success'] = 2;
    header('Location:'.$tconfig['tsite_url_main_admin'].'helps.php?'.$parameters);

    exit;
}
// End Change All Deleted Selected Status

// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (SITE_TYPE !== 'Demo') {
        $query = "UPDATE helps SET eStatus = '".$statusVal."' WHERE iHelpsId IN (".$checkbox.')';
        $obj->sql_query($query);
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
    } else {
        $_SESSION['success'] = 2;
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'helps.php?'.$parameters);

    exit;
}
// End Change All Selected Status
