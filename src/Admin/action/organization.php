<?php



include_once '../../common.php';

$reload = $_SERVER['REQUEST_URI'];
$urlparts = explode('?', $reload);
$parameters = $urlparts[1];
$id = $_REQUEST['id'] ?? '';
$iOrganizationId = $_REQUEST['iOrganizationId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';
$sql = 'select * from organization where  iOrganizationId= '.$iOrganizationId.'';
$getOrganizationData = $obj->MySQLSelect($sql);
$vCompany = $getOrganizationData[0]['vCompany'];
$orgEmail = $getOrganizationData[0]['vEmail'];
// echo "<pre>"; print_r($_REQUEST); die;
// Start make deleted
if (('Deleted' === $statusVal || 'delete' === $method) && ('' !== $iOrganizationId || '' !== $checkbox)) {
    if (!$userObj->hasPermission('delete-company')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete company';
    } else {
        // Added By Hasmukh On 15-10-2018 For Solved Bug Start
        if ('' !== $iOrganizationId) {
            $orgIds = $iOrganizationId;
        } else {
            $orgIds = $checkbox;
        }
        if (SITE_TYPE !== 'Demo') {
            $getOrgUserSql = 'SELECT iUserProfileId FROM user_profile WHERE iOrganizationId IN ('.$orgIds.')';
            $userData = $obj->MySQLSelect($getOrgUserSql);
            $qur2 = " UPDATE organization SET eStatus = 'Deleted' WHERE iOrganizationId IN (".$orgIds.')';
            $res2 = $obj->sql_query($qur2);
            // echo "<pre>";
            // print_r($userData);die;
            for ($u = 0; $u < count($userData); ++$u) {
                $iUserProfileId = $userData[$u]['iUserProfileId'];
                $query = "UPDATE user_profile  SET eStatus = 'Terminate'  WHERE iUserProfileId = '".$iUserProfileId."'";
                $obj->sql_query($query);
            }
            // Added By Hasmukh On 15-10-2018 For Solved Bug End
            // }
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $_SESSION['success'] = '2';
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'organization.php?'.$parameters);

    exit;
}
// End make deleted
// Start Change single Status
if ('' !== $iOrganizationId && '' !== $status) {
    if (!$userObj->hasPermission('update-status-company')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of company';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE organization SET eStatus = '".$status."' WHERE iOrganizationId = '".$iOrganizationId."'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            if ('Active' === $status) {
                // Use for Send Email to Organization
                $maildata['Organization_Name'] = $vCompany;
                $maildata['organization_email'] = $orgEmail;
                $maildata['Profile_Status'] = $status;
                $maildata['Company_Name'] = $COMPANY_NAME;
                $COMM_MEDIA_OBJ->SendMailToMember('ADMIN_UPDATE_USERPROFILESTATUS_TO_ORGANIZATION', $maildata);
                // Use for Send Email to Organization
                $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_ACTIVATE_MSG'];
            } else {
                $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INACTIVATE_MSG'];
            }
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'organization.php?'.$parameters);

    exit;
}
// End Change single Status
// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (!$userObj->hasPermission(['update-status-company', 'delete-company'])) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of company';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE organization SET eStatus = '".$statusVal."' WHERE iOrganizationId IN (".$checkbox.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'organization.php?'.$parameters);

    exit;
}
// End Change All Selected Status
