<?php



include_once '../../common.php';

$AUTH_OBJ->checkMemberAuthentication();
$reload = $_SERVER['REQUEST_URI'];
$urlparts = explode('?', $reload);
$parameters = $urlparts[1];
$id = $_REQUEST['id'] ?? '';
$iAdvertBannerId = $_REQUEST['iAdvertBannerId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';
// echo "<pre>";
// print_r($_REQUEST);die;
// Start make deleted
$tableName = 'advertise_banners';
$redirectUrl = $tconfig['tsite_url_main_admin'].'advertise_banners.php?'.$parameters;
if (('Deleted' === $statusVal || 'delete' === $method) && ('' !== $iAdvertBannerId || '' !== $checkbox)) {
    if (!$userObj->hasPermission('delete-advertise-banner')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete record';
    } else {
        // Added By Hasmukh On 12-10-2018 For Solved Bug Start
        if ('' !== $iAdvertBannerId) {
            $catIds = $iAdvertBannerId;
        } else {
            $catIds = $checkbox;
        }
        // Added By Hasmukh On 12-10-2018 For Solved Bug End
        if (SITE_TYPE !== 'Demo') {
            $getImages = $obj->MySQLSelect('SELECT vBannerImage FROM  '.$tableName.' WHERE iAdvertBannerId IN ('.$catIds.')');
            $query = 'DELETE FROM  '.$tableName.' WHERE iAdvertBannerId IN ('.$catIds.')';
            $obj->sql_query($query);
            for ($g = 0; $g < count($getImages); ++$g) {
                $img_path = $tconfig['tsite_upload_advertise_banner_path'].'/'.$getImages[$g]['vBannerImage'];
                unlink($img_path);
            }
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $_SESSION['success'] = '2';
        }
    }
    header('Location:'.$redirectUrl);

    exit;
}

// Start Change single Status
if ('' !== $iAdvertBannerId && '' !== $status) {
    if (!$userObj->hasPermission('update-status-advertise-banner')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of record';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = 'UPDATE '.$tableName." SET eStatus = '".$status."' WHERE iAdvertBannerId = '".$iAdvertBannerId."'";
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
    header('Location:'.$redirectUrl);

    exit;
}
// End Change single Status
// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (!$userObj->hasPermission('update-status-advertise-banner')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of record';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = 'UPDATE '.$tableName." SET eStatus = '".$statusVal."' WHERE iAdvertBannerId IN (".$checkbox.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$redirectUrl);

    exit;
}
