<?php



include_once '../../common.php';

$reload = $_SERVER['REQUEST_URI'];
$urlparts = explode('?', $reload);
$parameters = $urlparts[1];
$id = $_REQUEST['id'] ?? '';
$iMakeId = $_REQUEST['iMakeId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';
// echo "<pre>"; print_r($_REQUEST);
// Start make deleted

if (('Deleted' === $statusVal || 'delete' === $method) && ('' !== $iMakeId || '' !== $checkbox)) {
    $checkUsedMake = count(checkUsedMake($iMakeId));
    if ($checkUsedMake > 0) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_MAKE_DELETE_ERROR_MSG'];
        header('Location:'.$tconfig['tsite_url_main_admin'].'make.php?'.$parameters);

        exit;
    }

    if (!$userObj->hasPermission('delete-vehicle-make')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete make';
    } else {
        // Added By Hasmukh On 05-10-2018 For Solved Bug Start
        if ('' !== $iMakeId) {
            $makeIds = $iMakeId;
        } else {
            $makeIds = $checkbox;
        }
        // Added By Hasmukh On 05-10-2018 For Solved Bug End
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE make SET eStatus = 'Deleted' WHERE iMakeId IN (".$makeIds.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $_SESSION['success'] = '2';
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'make.php?'.$parameters);

    exit;
}
// End make deleted
// Start Change single Status
if ('' !== $iMakeId && '' !== $status) {
    if (!$userObj->hasPermission('update-status-vehicle-make')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of make';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE make SET eStatus = '".$status."' WHERE iMakeId = '".$iMakeId."'";
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
    header('Location:'.$tconfig['tsite_url_main_admin'].'make.php?'.$parameters);
    echo 'test';

    exit;

    exit;
}
// End Change single Status
// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (!$userObj->hasPermission('update-status-vehicle-make')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of make';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE make SET eStatus = '".$statusVal."' WHERE iMakeId IN (".$checkbox.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'make.php?'.$parameters);

    exit;
}
// End Change All Selected Status
// if ($iMakeId != '' && $status != '') {
//    if (SITE_TYPE != 'Demo') {
//        $query = "UPDATE make SET eStatus = '" . $status . "' WHERE iMakeId = '" . $iMakeId . "'";
//        $obj->sql_query($query);
//        $_SESSION['success'] = '1';
//        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
//        header("Location:".$tconfig["tsite_url_main_admin"]."make.php?".$parameters);
//        exit;
//    } else {
//        $_SESSION['success']=2;
//        header("Location:".$tconfig["tsite_url_main_admin"]."make.php?".$parameters);
//        exit;
//    }
// }
