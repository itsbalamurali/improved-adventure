<?php



include_once '../../common.php';

$AUTH_OBJ->checkMemberAuthentication();

$reload = $_SERVER['REQUEST_URI'];

$urlparts = explode('?', $reload);
$parameters = $urlparts[1];

$eServiceType = $_GET['eServiceType'] ?? '';
if ('Deliver' === $eServiceType) {
    $commonTxt = 'parcel-delivery';
}
if ('VideoConsult' === $eServiceType) {
    $commonTxt = 'video-consultation';
}
if ('UberX' === $eServiceType) {
    $commonTxt = 'uberx';
}

$delete = 'delete-service-category-'.$commonTxt;

$id = $_REQUEST['id'] ?? '';
$iVehicleCategoryId = $_REQUEST['iVehicleCategoryId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';
$sub_cid = $_REQUEST['sub_cid'] ?? '';

$iServiceIdEdit = $_REQUEST['iServiceIdEdit'] ?? '';
// Start make deleted //Added By SP start
if (('Deleted' === $statusVal || 'delete' === $method) && ('' !== $sub_cid || '' !== $checkbox)) {
    if (!$userObj->hasPermission($delete)) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete service category';
    } else {
        if ('' !== $iVehicleCategoryId) {
            $typeIds = $iVehicleCategoryId;
        } else {
            $typeIds = $checkbox;
        }

        if (SITE_TYPE !== 'Demo') {
            $sql = 'SELECT count(iVehicleTypeId) as total_type FROM vehicle_type WHERE iVehicleCategoryId IN('.$typeIds.") and eStatus!='Deleted'";

            $data_cat = $obj->MySQLSelect($sql);

            if ($data_cat[0]['total_type'] > 0) {
                $_SESSION['success'] = '3';

                $_SESSION['var_msg'] = 'This category have service type so you can not delete this category. Please delete service type than after delete this category.';
            } else {
                $query = "UPDATE vehicle_category SET eStatus ='Deleted' WHERE iVehicleCategoryId IN (".$typeIds.") AND iParentId = '".$sub_cid."'";

                $obj->sql_query($query);

                $_SESSION['success'] = '1';
                $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
            }
        } else {
            $_SESSION['success'] = '2';
        }
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'vehicle_sub_category.php?'.$parameters);

    exit;
}
// Added By SP end
/*if ($method == 'delete' && $sub_cid != '') {
    if(SITE_TYPE !='Demo'){

            $sql = "SELECT count(iVehicleTypeId) as total_type FROM vehicle_type WHERE iVehicleCategoryId = '".$iVehicleCategoryId."'";

            $data_cat = $obj->MySQLSelect($sql);

            if($data_cat[0]['total_type'] > 0){

                $_SESSION['success'] = '3';

                $_SESSION['var_msg'] = 'This category have service type so you can not delete this category. Please delete service type than after delete this category.';

            } else {

                $query = "DELETE FROM vehicle_category WHERE iVehicleCategoryId = '" . $iVehicleCategoryId . "' AND iParentId = '" . $sub_cid . "'";

                $obj->sql_query($query);

                $_SESSION['success'] = '1';

                $_SESSION['var_msg'] = 'Service Sub Category deleted successfully.';
            }
    }
    else{
            $_SESSION['success'] = '2';
    }
    header("Location:".$tconfig["tsite_url_main_admin"]."vehicle_sub_category.php?".$parameters); exit;
}*/

// Start Change single Status
if ('' !== $iVehicleCategoryId && '' !== $status) {
    if (SITE_TYPE !== 'Demo') {
        $query = "UPDATE vehicle_category SET eStatus = '".$status."' WHERE iVehicleCategoryId = '".$iVehicleCategoryId."' AND iParentId = '".$sub_cid."'";
        $obj->sql_query($query);
        if ($iServiceIdEdit > 0 && '' !== $iServiceIdEdit) {
            $query = "UPDATE service_categories SET eStatus = '".$status."' WHERE iServiceId = '".$iServiceIdEdit."'";
            $obj->sql_query($query);
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
    header('Location:'.$tconfig['tsite_url_main_admin'].'vehicle_sub_category.php?'.$parameters);

    exit;
}
// End Change single Status

// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (SITE_TYPE !== 'Demo') {
        if ('Deleted' === $statusVal) {
            /*$query = "DELETE FROM vehicle_category WHERE iVehicleCategoryId IN (" . $checkbox . ") AND iParentId = '" . $sub_cid . "'";
            $obj->sql_query($query);
             $_SESSION['success'] = '1';
             $_SESSION['var_msg'] = 'Service Sub Category(s) Deleted successfully.';*/
        } else {
            $query = "UPDATE vehicle_category SET eStatus = '".$statusVal."' WHERE iVehicleCategoryId IN (".$checkbox.") AND iParentId = '".$sub_cid."'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        }
    } else {
        $_SESSION['success'] = 2;
    }

    header('Location:'.$tconfig['tsite_url_main_admin'].'vehicle_sub_category.php?'.$parameters);

    exit;
}
