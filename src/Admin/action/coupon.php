<?php



include_once '../../common.php';

$AUTH_OBJ->checkMemberAuthentication();

$reload = $_SERVER['REQUEST_URI'];

$urlparts = explode('?', $reload);
$parameters = $urlparts[1];

$id = $_REQUEST['id'] ?? '';
$iCouponId = $_REQUEST['iCouponId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';
// echo "<pre>"; print_r($_REQUEST);
// echo "<pre>";print_r($_REQUEST);exit;
// Start make deleted
if ('delete' === $method && '' !== $iCouponId) {
    if (!$userObj->hasPermission('delete-promocode')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete promo code';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE coupon SET eStatus = 'Deleted' WHERE iCouponId = '".$iCouponId."'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $_SESSION['success'] = '2';
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'coupon.php?'.$parameters);

    exit;
}
// End make deleted

// Start make reset
if ('reset' === $method && '' !== $iCouponId) {
    if (SITE_TYPE !== 'Demo') {
        $query = "UPDATE coupon SET iTripId='0',vTripStatus='NONE',vCallFromDriver=' ' WHERE iCouponId = '".$iCouponId."'";
        $obj->sql_query($query);
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = 'Promo Code reset successfully.';
    } else {
        $_SESSION['success'] = '2';
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'coupon.php?'.$parameters);

    exit;
}
// End make reset

// Start Change single Status
if ('' !== $iCouponId && '' !== $status) {
    if (!$userObj->hasPermission('update-status-promocode')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of promo code';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE coupon SET eStatus = '".$status."' WHERE iCouponId = '".$iCouponId."'";
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
    header('Location:'.$tconfig['tsite_url_main_admin'].'coupon.php?'.$parameters);

    exit;
}
// End Change single Status

// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (!$userObj->hasPermission(['update-status-promocode', 'delete-promocode'])) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of promo code';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE coupon SET eStatus = '".$statusVal."' WHERE iCouponId IN (".$checkbox.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'coupon.php?'.$parameters);

    exit;
}
// End Change All Selected Status

// if ($iCouponId != '' && $status != '') {
//    if (SITE_TYPE != 'Demo') {
//        $query = "UPDATE coupon SET eStatus = '" . $status . "' WHERE iCouponId = '" . $iCouponId . "'";
//        $obj->sql_query($query);
//        $_SESSION['success'] = '1';
//        $_SESSION['var_msg'] = "Rider " . $status . " Successfully.";
//        header("Location:".$tconfig["tsite_url_main_admin"]."rider.php?".$parameters);
//        exit;
//    } else {
//        $_SESSION['success']=2;
//        header("Location:".$tconfig["tsite_url_main_admin"]."rider.php?".$parameters);
//        exit;
//    }
// }
