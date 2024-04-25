<?php



include_once '../../common.php';

$AUTH_OBJ->checkMemberAuthentication();

$reload = $_SERVER['REQUEST_URI'];

$urlparts = explode('?', $reload);
$parameters = $urlparts[1];

$id = $_REQUEST['id'] ?? '';
$iOrderStatusId = $_REQUEST['iOrderStatusId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';

// echo "<pre>"; print_r($_REQUEST); die;

// Start make deleted
if ('delete' === $method && '' !== $iOrderStatusId) {
    if (!$userObj->hasPermission('delete-order-status')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete order status';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "DELETE FROM order_status WHERE iOrderStatusId ='".$iOrderStatusId."'";
            echo $query;

            exit;
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $_SESSION['success'] = '2';
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'order_status.php?'.$parameters);

    exit;
}
// End make deleted

// Start Change All Selected Status
if ('' !== $checkbox && 'Deleted' === $statusVal) {
    if (!$userObj->hasPermission('delete-order-status')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete order status';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = 'DELETE FROM order_status WHERE iOrderStatusId IN ('.$checkbox.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'order_status.php?'.$parameters);

    exit;
}
// End Change All Selected Status

// if ($iDriverId != '' && $status != '') {
//    if (SITE_TYPE != 'Demo') {
//        $query = "UPDATE register_driver SET eStatus = '" . $status . "' WHERE iDriverId = '" . $iDriverId . "'";
//        $obj->sql_query($query);
//        $_SESSION['success'] = '1';
//        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
//        header("Location:".$tconfig["tsite_url_main_admin"]."rider.php?".$parameters);
//        exit;
//    } else {
//        $_SESSION['success']=2;
//        header("Location:".$tconfig["tsite_url_main_admin"]."rider.php?".$parameters);
//        exit;
//    }
// }
