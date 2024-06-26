<?php



include_once '../../common.php';

$reload = $_SERVER['REQUEST_URI'];

$urlparts = explode('?', $reload);
$parameters = $urlparts[1];

$id = $_REQUEST['id'] ?? '';
$iVisitId = $_REQUEST['iVisitId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';
// echo "<pre>"; print_r($_REQUEST);
// Start make deleted
if ('delete' === $method && '' !== $iVisitId) {
    if (!$userObj->hasPermission('delete-visit')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete visit location';
    } else {
        // if(SITE_TYPE !='Demo'){
        if (SITE_TYPE === 'Demo' && 'hotel' === $_SESSION['SessionUserType']) {
            $query = "UPDATE visit_address SET eStatus = 'Deleted' WHERE iVisitId = '".$iVisitId."'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } elseif (SITE_TYPE === 'Demo' && 'hotel' !== $_SESSION['SessionUserType']) {
            $_SESSION['success'] = '2';
        } else {
            $query = "UPDATE visit_address SET eStatus = 'Deleted' WHERE iVisitId = '".$iVisitId."'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        }

        // }
        // else{
        // 		$_SESSION['success'] = '2';
        // }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'visit.php?'.$parameters);

    exit;
}
// End make deleted

// Start Change single Status
if ('' !== $iVisitId && '' !== $status) {
    if (!$userObj->hasPermission('update-status-visit')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to update status of visit location';
    } else {
        // if(SITE_TYPE !='Demo'){
        if (SITE_TYPE === 'Demo' && 'hotel' === $_SESSION['SessionUserType']) {
            $query = "UPDATE visit_address SET eStatus = '".$status."' WHERE iVisitId = '".$iVisitId."'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            if ('Active' === $status) {
                $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_ACTIVATE_MSG'];
            } else {
                $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INACTIVATE_MSG'];
            }
        } elseif (SITE_TYPE === 'Demo' && 'hotel' !== $_SESSION['SessionUserType']) {
            $_SESSION['success'] = '2';
        } else {
            $query = "UPDATE visit_address SET eStatus = '".$status."' WHERE iVisitId = '".$iVisitId."'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            if ('Active' === $status) {
                $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_ACTIVATE_MSG'];
            } else {
                $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INACTIVATE_MSG'];
            }
        }

        // }
        // else{
        // 		$_SESSION['success']=2;
        // }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'visit.php?'.$parameters);
    echo 'test';

    exit;

    exit;
}
// End Change single Status

// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (!$userObj->hasPermission('update-status-visit')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to update status of visit location';
    } else {
        // if(SITE_TYPE !='Demo'){
        $query = "UPDATE visit_address SET eStatus = '".$statusVal."' WHERE iVisitId IN (".$checkbox.')';
        $obj->sql_query($query);
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        // }
        // else{
        // 	$_SESSION['success']=2;
        // }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'visit.php?'.$parameters);

    exit;
}
