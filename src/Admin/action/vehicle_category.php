<?php



include_once '../../common.php';
$MASTER_CATEGORY = $LOCATION_FILE_ARRAY['MASTER_CATEGORY'];
$AUTH_OBJ->checkMemberAuthentication();
$reload = $_SERVER['REQUEST_URI'];
$urlparts = explode('?', $reload);
$parameters = $urlparts[1];
$id = $_REQUEST['id'] ?? '';
$iVehicleCategoryId = $_REQUEST['iVehicleCategoryId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';
$eType = $_REQUEST['eType'] ?? '';
if ('Ride' === $eType) {
    $commonTxt .= 'taxi-service';
}
if ('DeliverAll' === $eType) {
    $commonTxt .= 'deliverall';
}
if ('VideoConsult' === $eType) {
    $commonTxt .= 'video-consultation';
}
if ('Bidding' === $eType) {
    $commonTxt .= 'bidding';
}
if ('UberX' === $eType) {
    $commonTxt .= 'uberx';
}
if ('RentEstate' === $eType) {
    $commonTxt .= 'rentestate';
}
if ('RentCars' === $eType) {
    $commonTxt .= 'rentcars';
}
if ('RentItem' === $eType) {
    $commonTxt .= 'rentitem';
}
if ('MedicalServices' === $eType) {
    $commonTxt .= 'medical';
}
if ('RideShare' === $eType) {
    $commonTxt .= 'rideshare';
}

$updateStatus = 'update-status-service-category-'.$commonTxt;
$delete = 'delete-service-category-'.$commonTxt;
// echo "<pre>"; print_r($_REQUEST);die;

$oCache->flushData();
// Start make deleted
if (('Deleted' === $statusVal || 'delete' === $method) && ('' !== $iVehicleCategoryId || '' !== $checkbox)) {
    if (!$userObj->hasPermission($delete)) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete record';
    } else {
        // Added By Hasmukh On 12-10-2018 For Solved Bug Start
        if ('' !== $iVehicleCategoryId) {
            $catIds = $iVehicleCategoryId;
        } else {
            $catIds = $checkbox;
        }
        // Added By Hasmukh On 12-10-2018 For Solved Bug End
        if (SITE_TYPE !== 'Demo') {
            /*$sql = "SELECT count(iVehicleCategoryId) as total_sub FROM vehicle_category WHERE iParentId = '" . $iVehicleCategoryId . "'";
            $data_cat = $obj->MySQLSelect($sql);
            if ($data_cat[0]['total_sub'] > 0) {
                $_SESSION['success'] = '3';
                $_SESSION['var_msg'] = 'This category have sub categories so you can not delete this category. Please delete sub category than after delete this category.';
            } else {*/
            // $query = "DELETE FROM  vehicle_category WHERE iVehicleCategoryId IN (" . $catIds . ")";
            $query = "UPDATE vehicle_category SET eStatus ='Deleted' WHERE iVehicleCategoryId IN (".$catIds.')';

            $obj->sql_query($query);

            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        // }
        } else {
            $_SESSION['success'] = '2';
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].$MASTER_CATEGORY.'?'.$parameters);

    exit;
}

// Start Change single Status
if ('' !== $iVehicleCategoryId && '' !== $status) {
    if (!$userObj->hasPermission($updateStatus)) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of record';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE vehicle_category SET eStatus = '".$status."' WHERE iVehicleCategoryId = '".$iVehicleCategoryId."'";
            $obj->sql_query($query);

            $vehicle_category_data = $obj->MySQLSelect("SELECT iServiceId FROM vehicle_category WHERE iVehicleCategoryId = '{$iVehicleCategoryId}'");
            if ($vehicle_category_data[0]['iServiceId'] > 0) {
                $obj->sql_query("UPDATE service_categories SET eStatus = '{$status}' WHERE iServiceId = '".$vehicle_category_data[0]['iServiceId']."'");
                $GCS_OBJ->updateGCSData();
            }

            $checkLog = $obj->MySQLSelect("SELECT iVehicleCategoryLogId FROM vehicle_category_status_log WHERE iVehicleCategoryId = '{$iVehicleCategoryId}'");

            if (!empty($checkLog) && $checkLog > 0) {
                $obj->sql_query("UPDATE vehicle_category_status_log SET eStatus = '{$status}' WHERE iVehicleCategoryLogId = '".$checkLog[0]['iVehicleCategoryLogId']."'");
            } else {
                $obj->sql_query("INSERT INTO vehicle_category_status_log SET iVehicleCategoryId = '{$iVehicleCategoryId}', eStatus = '{$status}'");
            }

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
    header('Location:'.$tconfig['tsite_url_main_admin'].$MASTER_CATEGORY.'?'.$parameters);

    exit;
}
// End Change single Status
// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (!$userObj->hasPermission($updateStatus)) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of record';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE vehicle_category SET eStatus = '".$statusVal."' WHERE iVehicleCategoryId IN (".$checkbox.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        } else {
            $_SESSION['success'] = 2;
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].$MASTER_CATEGORY.'?'.$parameters);

    exit;
}
