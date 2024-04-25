<?php



include_once '../../common.php';

$date = date('Y-m-d');
$ip = $_SERVER['REMOTE_ADDR'] ?: '';

$reload = $_SERVER['REQUEST_URI'];
$urlparts = explode('?', $reload);
$parameters = $urlparts[1];
$id = $_REQUEST['id'] ?? '';
$iCompanyId = $_REQUEST['iCompanyId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';
// Start make deleted
$adminUrl = $tconfig['tsite_url_main_admin'];
// print_R($iCompanyId);die;
if ('' !== $statusVal && ('eAutoaccept' === $method || 'eAvailable' === $method)) {
    // echo "UPDATE company SET $method = '" . $statusVal . "' WHERE iCompanyId IN (" . $iCompanyId . ")";die;
    $obj->sql_query("UPDATE company SET {$method} = '".$statusVal."' WHERE iCompanyId IN (".$iCompanyId.')');
    if ($iCompanyId > 0) {
        $successtype = '1';
        $successMsg = $langage_lbl_admin['LBL_DISABLE_AUTO_ACCEPT_ORDER_TXT'];
        if ('Yes' === $statusVal) {
            $successtype = '1';
            $successMsg = $langage_lbl_admin['LBL_AUTO_ACCEPT_ORDER_TXT'];
        }
        if ('eAvailable' === $method) {
            $successtype = '1';
            $successMsg = $langage_lbl_admin['LBL_INFO_UPDATED_TXT'];
        }
        $_SESSION['success'] = $successtype;
        $_SESSION['var_msg'] = $successMsg;
    } else {
        $_SESSION['success'] = '2';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_ERROR_OCCURED'];
    }
    $data['status'] = '1';
    echo json_encode($data);

    exit;
}
if (('Deleted' === $statusVal || 'delete' === $method) && ('' !== $iCompanyId || '' !== $checkbox)) {
    if (!$userObj->hasPermission('delete-store')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete '.strtolower($langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']);
    } else {
        // Added By Hasmukh On 05-10-2018 For Solved Bug Start
        if ('' !== $iCompanyId) {
            $storeIds = $iCompanyId;
        } else {
            $storeIds = $checkbox;
        }
        // Added By Hasmukh On 05-10-2018 For Solved Bug End
        if (SITE_TYPE !== 'Demo') {
            $qur2 = "UPDATE company SET eStatus = 'Deleted'  , vPhone = concat(vPhone, '(Deleted)')  WHERE iCompanyId IN (".$storeIds.')';
            $res2 = $obj->sql_query($qur2);

            $storeIds = explode(',', $storeIds);
            for ($i = 0; $i < count($storeIds); ++$i) {
                // Insert status log on user_log table
                $queryIn = 'INSERT INTO user_status_logs SET iUserId = '.$storeIds[$i].", eUserType = 'store', dDate = '".$date."', eStatus = 'Deleted', iUpdatedBy = ".$_SESSION['sess_iAdminUserId'].", vIP = '".$ip."'";
                $obj->sql_query($queryIn);
            }

            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $_SESSION['success'] = '2';
        }
    }
    header('Location:'.$adminUrl.'store.php?'.$parameters);

    exit;
}
// End make deleted
// Start Change single Status
if ('' !== $iCompanyId && '' !== $status) {
    if (!$userObj->hasPermission('update-status-store')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of '.strtolower($langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']);
    } else {
        if (SITE_TYPE !== 'Demo') {
            // --------------------- store deleted duplicate check --------------------
            $checkUserDeleted = $obj->MySQLSelect("SELECT vPhone FROM company WHERE eStatus = 'Deleted' AND iCompanyId='".$iCompanyId."'");

            if (!empty($checkUserDeleted)) {
                $mobile = clearPhone($checkUserDeleted[0]['vPhone']);
                $checkUserDeleted = $obj->MySQLSelect("SELECT vPhone FROM company WHERE eStatus != 'Deleted' AND vPhone='".$mobile."' AND iCompanyId !='".$iCompanyId."' ");

                if (!empty($checkUserDeleted)) {
                    $_SESSION['success'] = 2;
                    $_SESSION['var_msg'] = $langage_lbl_admin['LBL_ADMIN_NOT_ABLE_ACTIVE_TEXT'];
                    header('Location:'.$tconfig['tsite_url_main_admin'].'store.php?'.$parameters);

                    exit;
                }

                $query = "UPDATE company SET vPhone = '".$mobile."' WHERE iCompanyId = '".$iCompanyId."'";
                $checkUserDeleted = $obj->MySQLSelect($query);
            }

            // --------------------- store deleted duplicate check --------------------

            $acceptSql = '';
            if ('Active' !== $status) {
                $acceptSql = " ,eAutoaccept = 'No', eAvailable = 'No'";
            }
            $query = "UPDATE company SET eStatus = '".$status."' {$acceptSql} WHERE iCompanyId = '".$iCompanyId."'";
            $obj->sql_query($query);

            // Insert status log on user_log table
            $queryIn = 'INSERT INTO user_status_logs SET iUserId = '.$iCompanyId.", eUserType = 'store', dDate = '".$date."', eStatus = '".$status."', iUpdatedBy = ".$_SESSION['sess_iAdminUserId'].", vIP = '".$ip."'";
            $obj->sql_query($queryIn);

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
    header('Location:'.$adminUrl.'store.php?'.$parameters);

    exit;
}
// End Change single Status
// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (!$userObj->hasPermission('update-status-store')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of '.strtolower($langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']);
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE company SET eStatus = '".$statusVal."' WHERE iCompanyId IN (".$checkbox.')';
            $obj->sql_query($query);

            $checkbox = explode(',', $checkbox);
            for ($i = 0; $i < count($checkbox); ++$i) {
                // Insert status log on user_log table
                $queryIn = 'INSERT INTO user_status_logs SET iUserId = '.$checkbox[$i].", eUserType = 'store', dDate = '".$date."', eStatus = '".$statusVal."', iUpdatedBy = ".$_SESSION['sess_iAdminUserId'].", vIP = '".$ip."'";
                $obj->sql_query($queryIn);
            }

            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$adminUrl.'store.php?'.$parameters);

    exit;
}
// End Change All Selected Status
