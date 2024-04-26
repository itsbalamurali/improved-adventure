<?php



include_once '../../common.php';

$reload = $_SERVER['REQUEST_URI'];
$urlparts = explode('?', $reload);
$parameters = $urlparts[1];
$id = $_REQUEST['id'] ?? '';
$cuisineId = $_REQUEST['cuisineId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';
// print_r($_REQUEST);die;
// Start make deleted
if (('Deleted' === $statusVal || 'delete' === $method) && ('' !== $cuisineId || '' !== $checkbox)) {
    /*    $checkRestaurant = "SELECT count(iCompanyId) as TotalRes FROM company_cuisine WHERE cuisineId = '" . $cuisineId . "'";
      $ResData=$obj->MySQLSelect($checkRestaurant); */
    if (!$userObj->hasPermission('delete-item-type')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete item type';
    } else {
        // Added By Hasmukh On 05-10-2018 For Solved Bug Start
        if ('' !== $cuisineId) {
            $itemTypeIds = $cuisineId;
        } else {
            $itemTypeIds = $checkbox;
        }
        // Added By Hasmukh On 05-10-2018 For Solved Bug End
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE cuisine SET eStatus = 'Deleted' WHERE cuisineId IN (".$itemTypeIds.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $_SESSION['success'] = '2';
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'cuisine.php?'.$parameters);

    exit;
}
// End make deleted
// Start Change single Status
if ('' !== $cuisineId && '' !== $status) {
    if (!$userObj->hasPermission('update-status-item-type')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of item type';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE cuisine SET eStatus = '".$status."' WHERE cuisineId = '".$cuisineId."'";
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
    header('Location:'.$tconfig['tsite_url_main_admin'].'cuisine.php?'.$parameters);

    exit;
}
// End Change single Status
// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (!$userObj->hasPermission('update-status-item-type')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of item type(s)';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE cuisine SET eStatus = '".$statusVal."' WHERE cuisineId IN (".$checkbox.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'cuisine.php?'.$parameters);

    exit;
}
// End Change All Selected Status
