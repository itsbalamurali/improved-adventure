<?php



include_once '../../common.php';

$reload = $_SERVER['REQUEST_URI'];
$urlparts = explode('?', $reload);
$parameters = $urlparts[1];
$id = $_REQUEST['id'] ?? '';
$iHelpDetailId = $_REQUEST['iHelpDetailId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';

$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';
// echo '<pre>'; print_r($_REQUEST); echo '</pre>'; die;
// Start faqs deleted
if (('Deleted' === $statusVal || 'delete' === $method) && ('' !== $iHelpDetailId || '' !== $checkbox)) {
    if (!$userObj->hasPermission('delete-help-detail')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete help detail';
    } else {
        // Added By Hasmukh On 05-10-2018 For Solved Bug Start
        if ('' !== $iHelpDetailId) {
            $helpDetailIds = $iHelpDetailId;
        } else {
            $helpDetailIds = $checkbox;
        }
        // Added By Hasmukh On 05-10-2018 For Solved Bug End
        if (SITE_TYPE !== 'Demo') {
            // $query = "UPDATE faqs SET eStatus = 'Deleted' WHERE iFaqId = '" . $iFaqId . "'";
            $query = 'DELETE FROM help_detail WHERE iHelpDetailId IN ('.$helpDetailIds.')';
            // echo $query;die;
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $_SESSION['success'] = '2';
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'help_detail.php?'.$parameters);

    exit;
}
// End faqs deleted
// Start Change single Status
if ('' !== $iHelpDetailId && '' !== $status) {
    if (!$userObj->hasPermission('update-status-help-detail')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of help detail';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE help_detail SET eStatus = '".$status."' WHERE iHelpDetailId = '".$iHelpDetailId."'";
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
    header('Location:'.$tconfig['tsite_url_main_admin'].'help_detail.php?'.$parameters);
    echo 'test';

    exit;

    exit;
}
// End Change single Status
// Start Change All Deleted Selected Status
// echo '<pre>'; print_r($_REQUEST); echo '</pre>'; die;

if ('' !== $checkbox && 'Deleted' === $statusVal) {
    if (!$userObj->hasPermission('delete-help-detail')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete help detail';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = 'DELETE FROM help_detail WHERE iHelpDetailId IN ('.$checkbox.')'; // die;
            $obj->sql_query($query);
            $status = 'deleted';
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'help_detail.php?'.$parameters);

    exit;
    // header("Location:".$tconfig["tsite_url_main_admin"]."faq.php?".$parameters);
}
// End Change All Deleted Selected Status
// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (!$userObj->hasPermission('update-status-help-detail')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of help detail';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE help_detail SET eStatus = '".$statusVal."' WHERE iHelpDetailId IN (".$checkbox.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'help_detail.php?'.$parameters);

    exit;
}
// End Change All Selected Status
