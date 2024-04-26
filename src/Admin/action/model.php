<?php



include_once '../../common.php';

$reload = $_SERVER['REQUEST_URI'];
$urlparts = explode('?', $reload);
$parameters = $urlparts[1];
$id = $_REQUEST['id'] ?? '';
$iModelId = $_REQUEST['iModelId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';
// echo "<pre>"; print_r($_REQUEST);die;
// Start model deleted
if (('Deleted' === $statusVal || 'delete' === $method) && ('' !== $iModelId || '' !== $checkbox)) {
    $checkUsedModel = count(checkUsedModel($iMakeId));
    if ($checkUsedModel > 0) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_MODEL_DELETE_ERROR_MSG'];
        header('Location:'.$tconfig['tsite_url_main_admin'].'model.php?'.$parameters);

        exit;
    }

    if (!$userObj->hasPermission('delete-vehicle-model')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete model';
    } else {
        // Added By Hasmukh On 05-10-2018 For Solved Bug Start
        if ('' !== $iModelId) {
            $modalIds = $iModelId;
        } else {
            $modalIds = $checkbox;
        }
        // Added By Hasmukh On 05-10-2018 For Solved Bug End
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE model SET eStatus = 'Deleted' WHERE iModelId IN (".$modalIds.')';
            // echo $query;die;
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $_SESSION['success'] = '2';
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'model.php?'.$parameters);

    exit;
}
// End model deleted
// Start Change single Status
if ('' !== $iModelId && '' !== $status) {
    if (!$userObj->hasPermission('update-status-vehicle-model')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of model';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE model SET eStatus = '".$status."' WHERE iModelId = '".$iModelId."'";
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
    header('Location:'.$tconfig['tsite_url_main_admin'].'model.php?'.$parameters);

    exit;
}
// End Change single Status
// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (!$userObj->hasPermission('update-status-vehicle-model')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of model';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE model SET eStatus = '".$statusVal."' WHERE iModelId IN (".$checkbox.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'model.php?'.$parameters);

    exit;
}
// End Change All Selected Status
// if ($iModelId != '' && $status != '') {
//    if (SITE_TYPE != 'Demo') {
//        $query = "UPDATE model SET eStatus = '" . $status . "' WHERE iModelId = '" . $iModelId . "'";
//        $obj->sql_query($query);
//        $_SESSION['success'] = '1';
//        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
//        header("Location:".$tconfig["tsite_url_main_admin"]."model.php?".$parameters);
//        exit;
//    } else {
//        $_SESSION['success']=2;
//        header("Location:".$tconfig["tsite_url_main_admin"]."model.php?".$parameters);
//        exit;
//    }
// }
