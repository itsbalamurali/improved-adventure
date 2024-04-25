<?php



include_once '../../common.php';

$reload = $_SERVER['REQUEST_URI'];

$urlparts = explode('?', $reload);
$parameters = $urlparts[1];

$id = $_REQUEST['id'] ?? '';
$iDriverSubscriptionPlanId = $_REQUEST['iDriverSubscriptionPlanId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';

// Start make deleted
if ('delete' === $method && '' !== $iDriverSubscriptionPlanId) {
    if (!$userObj->hasPermission('delete-driver-subscription')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete record';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $qur2 = "UPDATE driver_subscription_plan SET eStatus = 'Deleted' WHERE iDriverSubscriptionPlanId = '".$iDriverSubscriptionPlanId."'";
            $res2 = $obj->sql_query($qur2);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $_SESSION['success'] = '2';
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'driver_subscription.php?'.$parameters);

    exit;
}
// End make deleted

// Start Change single Status
if ('' !== $iDriverSubscriptionPlanId) {
    if (!$userObj->hasPermission('update-status-driver-subscription')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to update status';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE driver_subscription_plan SET eStatus = '".$status."' WHERE iDriverSubscriptionPlanId = '".$iDriverSubscriptionPlanId."'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            if ('Active' === $status) {
                $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_ACTIVATE_MSG'];
            } elseif ('Inactive' === $status) {
                $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INACTIVATE_MSG'];
            }
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'driver_subscription.php?'.$parameters);

    exit;
}
// End Change single Status

// Start Change single Status
/*if ($iCompanyId != '' && $status == 'Inactive') {
    if(!$userObj->hasPermission('update-status-company')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You have not permission to change status of Company';
    }else{
        if(SITE_TYPE !='Demo'){
            $qur1 = "UPDATE register_driver SET register_driver.iCompanyId=1 WHERE register_driver.iCompanyId=$iCompanyId ";
            $res1 = $obj->sql_query($qur1);

            $qur3 = "UPDATE driver_vehicle SET driver_vehicle.iCompanyId=1 WHERE driver_vehicle.iCompanyId=$iCompanyId ";
            $res3 = $obj->sql_query($qur3);

            if($res1==1) {
                $query = "UPDATE company SET eStatus = '" . $status . "' WHERE iCompanyId = '" . $iCompanyId . "'";
                $obj->sql_query($query);
            }

            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Company inactivated successfully.';

        }
        else{
                $_SESSION['success']=2;
        }
    }
    header("Location:".$tconfig["tsite_url_main_admin"]."company.php?".$parameters);
    exit;
}*/
// End Change single Status

// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (!$userObj->hasPermission('update-status-driver-subscription')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to update status';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE driver_subscription_plan SET eStatus = '".$statusVal."' WHERE iDriverSubscriptionPlanId IN (".$checkbox.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'driver_subscription.php?'.$parameters);

    exit;
}
// End Change All Selected Status
