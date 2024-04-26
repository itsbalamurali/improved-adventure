<?php



include_once '../../common.php';
define('USER_PROFILE_MASTER', 'user_profile_master');

$AUTH_OBJ->checkMemberAuthentication();
$reload = $_SERVER['REQUEST_URI'];
$urlparts = explode('?', $reload);
$parameters = $urlparts[1];
$id = $_REQUEST['id'] ?? '';
$iUserProfileMasterId = $_REQUEST['iUserProfileMasterId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';
// echo "<pre>"; print_r($_REQUEST); die;
// Start make deleted
if (('Deleted' === $statusVal || 'delete' === $method) && ('' !== $iUserProfileMasterId || '' !== $checkbox)) {
    if (!$userObj->hasPermission('delete-profile-taxi-service')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete user profile';
    } else {
        // echo "<pre>";
        // Added By Hasmukh On 05-10-2018 For Solved Bug Start
        if ('' !== $iUserProfileMasterId) {
            $profileIds = $iUserProfileMasterId;
        } else {
            $profileIds = $checkbox;
        }
        $sql = "SELECT * FROM organization WHERE eStatus != 'Deleted' AND iUserProfileMasterId IN (".$profileIds.')';
        $orgProfile = $obj->MySQLSelect($sql);
        // print_R($orgProfile);die;
        if (count($orgProfile) > 0) {
            $_SESSION['success'] = '2';
            $_SESSION['var_msg'] = 'This profile is already accosiated with the organization, kindly delete organization first.';
        } else {
            // Added By Hasmukh On 05-10-2018 For Solved Bug End
            if (SITE_TYPE !== 'Demo') {
                $query = 'UPDATE '.USER_PROFILE_MASTER." SET eStatus = 'deleted' WHERE iUserProfileMasterId IN (".$profileIds.')';
                $obj->sql_query($query);
                $_SESSION['success'] = '1';
                $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
            } else {
                $_SESSION['success'] = '2';
            }
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'user_profile_master.php?'.$parameters);

    exit;
}
// End make deleted
// Start Change single Status
if ('' !== $iUserProfileMasterId && '' !== $status) {
    if (!$userObj->hasPermission('update-status-profile-taxi-service')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of user profile';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = 'UPDATE '.USER_PROFILE_MASTER." SET eStatus = '".$status."' WHERE iUserProfileMasterId = '".$iUserProfileMasterId."'";
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
    header('Location:'.$tconfig['tsite_url_main_admin'].'user_profile_master.php?'.$parameters);

    exit;
}
// End Change single Status
// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (!$userObj->hasPermission('update-status-profile-taxi-service')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of user profile';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = 'UPDATE '.USER_PROFILE_MASTER." SET eStatus = '".$statusVal."' WHERE iUserProfileMasterId IN (".$checkbox.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'user_profile_master.php?'.$parameters);

    exit;
}
// End Change All Selected Status
