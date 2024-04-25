<?php
include_once('../common.php');
require_once(TPATH_CLASS . "/Imagecrop.class.php");

$eServiceType = isset($_GET['eServiceType']) ? $_GET['eServiceType'] : "";
$homepagepermission = isset($_REQUEST['homepage']) ? $_REQUEST['homepage'] : "";
$commonTxt = '';

if ($eServiceType == 'Ride') {
    $commonTxt = 'taxi-service';
}
if ($eServiceType == 'Deliver') {
    $commonTxt = 'parcel-delivery';
}
if ($eServiceType == 'DeliverAll') {
    $commonTxt = 'deliverall';
}
if ($eServiceType == 'VideoConsult') {
    $commonTxt = 'video-consultation';
}
if ($eServiceType == 'UberX') {
    $commonTxt = 'uberx';
}
if ($eServiceType == 'Genie') {
    $commonTxt = 'genie-delivery';
}
if ($eServiceType == 'Runner') {
    $commonTxt = 'runner-delivery';
}

if ($eServiceType == 'MedicalServices') {
    $commonTxt = 'medical';
}
$view = "view-service-category-" . $commonTxt;
$update = "update-service-category-" . $commonTxt;
$updateStatus = "update-status-service-category-" . $commonTxt;
$create = "create-service-category-" . $commonTxt;
if ($eServiceType == 'Runner' || $eServiceType == 'Genie') {
    $view = "view-service-content-" . $commonTxt;
    $update = "update-service-content-" . $commonTxt;
    $updateStatus = "update-status-service-content-" . $commonTxt;
    $create = "create-service-content-" . $commonTxt;
}

if ($homepagepermission == "1") {
    $create = $updateStatus = $update = $view = "manage-home-page-content";
   /* $update = $view;
    $updateStatus = $view;
    $create = $view;*/
}


if (!$userObj->hasPermission($view)) {
    $userObj->redirect();
}



$thumb = new thumbnail();
$form = "";
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
$descEnable = 0;
$required_rule = "accept='image/*'";
$vId = $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$sub_action = isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '';
$sub_cid = isset($_REQUEST['sub_cid']) ? $_REQUEST['sub_cid'] : '';
$sql = "SELECT vName,vSymbol FROM currency WHERE eDefault = 'Yes'";
$db_currency = $obj->MySQLSelect($sql);
$goback = $iServiceIdEdit = 0;
$message_print_id = $id;
$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : 0;
$var_msg = isset($_REQUEST['var_msg']) ? $_REQUEST['var_msg'] : "";
$action = ($id != '') ? 'Edit' : 'Add';
$actionSave = ($id != '') ? 'Update' : 'Add';
$backlink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
$previousLink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
$banner_lang = isset($_REQUEST['banner_lang']) ? $_REQUEST['banner_lang'] : $default_lang;

$sql_vehicle_category_table_name = getVehicleCategoryTblName();
$tbl_name = $sql_vehicle_category_table_name;
$script = 'VehicleCategory';
$db_master = $obj->MySQLSelect("SELECT * FROM `language_master` WHERE eStatus = 'Active'  ORDER BY `iDispOrder`");
$count_all = count($db_master);
$cubexthemeon = 0;
if ($THEME_OBJ->isProThemeActive() == "Yes" && $eServiceType != '' ) {
   $script = 'VehicleCategory_'.$eServiceType;
   if($eServiceType == 'Deliver' && !$MODULES_OBJ->isEnableRideDeliveryV1()){
       $script = 'VehicleCategory_'.$vId;
   }

}

//if($THEME_OBJ->isCubexThemeActive()=='Yes') {
if ($THEME_OBJ->isXThemeActive() == 'Yes') {
    $cubexthemeon = 1;
}
//if($THEME_OBJ->isServiceXThemeActive()=='Yes' && $THEME_OBJ->isRideDeliveryXThemeActive()=='Yes') {
//    $cubexthemeon = 0; //do 0 becoz homapage tab is not shown for this when cubexthemeon is 1 and sub_cid is also empty
//}
//$cubexthemeon = 0;
$homepage_cubejekx = 1;
if ($THEME_OBJ->isDeliveryKingXv2ThemeActive() == "Yes" || $THEME_OBJ->isCubeXv2ThemeActive() == 'Yes' || $THEME_OBJ->isCubeJekXv3ThemeActive() == 'Yes' || $THEME_OBJ->isCubeJekXv3ProThemeActive() == 'Yes' || $THEME_OBJ->isCubeJekXThemeActive() == 'Yes' || $THEME_OBJ->isCubeJekXv2ThemeActive() == 'Yes' || $THEME_OBJ->isDeliveryKingThemeActive() == 'Yes' || $THEME_OBJ->isServiceXThemeActive() == 'Yes' || $THEME_OBJ->isServiceXv2ThemeActive() == 'Yes') {
    if (isset($_REQUEST['homepage']) && $_REQUEST['homepage'] == 1) {
        $homepage_cubejekx = 1;
    } else {
        $homepage_cubejekx = 2;
    }
}

$vCatNameHomepageArr = $vCatTitleHomepageArr = $vCatSloganHomepageArr = $lCatDescHomepage = $vCatDescbtnHomepage = $vServiceCatTitleHomepageArr = array();
if ($cubexthemeon == 1 && $action == 'Edit') {
    $getHomeDataQry = "SELECT vHomepageLogoOurServices,vHomepageLogo,vHomepageFullLogo,vHomepageBanner,vCatNameHomepage,vCatTitleHomepage,vCatSloganHomepage,lCatDescHomepage,vCatDescbtnHomepage,iDisplayOrderHomepage,vServiceCatTitleHomepage,vServiceHomepageBanner FROM " . $sql_vehicle_category_table_name . " where iVehicleCategoryId='" . $id . "'";
    $getHomeData = $obj->MySQLSelect($getHomeDataQry);
    $vHomepageLogo = $getHomeData[0]['vHomepageLogo'];
    $vHomepageFullLogo = $getHomeData[0]['vHomepageFullLogo'];
    $vHomepageLogoOurServices = $getHomeData[0]['vHomepageLogoOurServices'];
    $vHomepageBanner = $getHomeData[0]['vHomepageBanner'];
    $vServiceHomepageBanner = $getHomeData[0]['vServiceHomepageBanner'];
    foreach ($getHomeData as $key => $value) {
        $vCatNameHomepage = json_decode($value['vCatNameHomepage'], true);
        foreach ($vCatNameHomepage as $key1 => $value1) {
            $vCatNameHomepageArr[$key1] = $value1;
        }
        $vCatTitleHomepage = json_decode($value['vCatTitleHomepage'], true);
        foreach ($vCatTitleHomepage as $key2 => $value2) {
            $vCatTitleHomepageArr[$key2] = $value2;
        }
        $vCatSloganHomepage = json_decode($value['vCatSloganHomepage'], true);
        foreach ($vCatSloganHomepage as $key2 => $value2) {
            $vCatSloganHomepageArr[$key2] = $value2;
        }
        $lCatDescHomepage = json_decode($value['lCatDescHomepage'], true);
        foreach ($lCatDescHomepage as $key3 => $value3) {
            $lCatDescHomepageArr[$key3] = $value3;
        }
        $vCatDescbtnHomepage = json_decode($value['vCatDescbtnHomepage'], true);
        foreach ($vCatDescbtnHomepage as $key4 => $value4) {
            $vCatDescbtnHomepageArr[$key4] = $value4;
        }
        $vServiceCatTitleHomepage = json_decode($value['vServiceCatTitleHomepage'], true);
        foreach ($vServiceCatTitleHomepage as $key2 => $value2) {
            $vServiceCatTitleHomepageArr[$key2] = $value2;
        }
        $iDisplayOrderHomepage_db = $value['iDisplayOrderHomepage'];
    }
}
if ($cubexthemeon == 1) {
    /* to fetch max iDisplayOrder from table for insert */
    $select_order = $obj->MySQLSelect("SELECT count(iDisplayOrderHomepage) AS iDisplayOrderHomepage FROM " . $tbl_name . " WHERE iParentId = 0 AND eStatus =  'Active'");
    $iDisplayOrderHomepage = isset($select_order[0]['iDisplayOrderHomepage']) ? $select_order[0]['iDisplayOrderHomepage'] : 0;
    $iDisplayOrder_max_Homepage = $iDisplayOrderHomepage + 1; // Maximum order number
}
/************************************Homepage settings start***********************************************/
if (!empty($_POST['btnsubmit_homepage']) && $cubexthemeon == 1) {
    if (isset($_FILES['vHomepageLogo']) && $_FILES['vHomepageLogo']['name'] != "") {
        $filecheck = basename($_FILES['vHomepageLogo']['name']);
        $fileextarr = explode(".", $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        $data = getimagesize($_FILES['vHomepageLogo']['tmp_name']);
        /*$width = $data[0];
        $height = $data[1];
        if ($width != 360 && $height != 360) {
            $flag_error = 1;
            $var_msg = "Please Upload image only 512px * 512px";
        }*/
        if ($flag_error == 1) {
            $_SESSION['success'] = '';
            $_SESSION['var_msg'] = '';
            header("Location:vehicle_category_action.php?id=" . $id . "&var_msg=" . $var_msg . "&goback=1" . (!empty($eServiceType) ? '&eType=' . $eServiceType : ''));
            exit;
        }
    }
     if (isset($_FILES['vHomepageFullLogo']) && $_FILES['vHomepageFullLogo']['name'] != "") {
        $filecheck = basename($_FILES['vHomepageFullLogo']['name']);
        $fileextarr = explode(".", $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        $data = getimagesize($_FILES['vHomepageFullLogo']['tmp_name']);
        /*$width = $data[0];
        $height = $data[1];
        if ($width != 360 && $height != 360) {
            $flag_error = 1;
            $var_msg = "Please Upload image only 512px * 512px";
        }*/
        if ($flag_error == 1) {
            $_SESSION['success'] = '';
            $_SESSION['var_msg'] = '';
            header("Location:vehicle_category_action.php?id=" . $id . "&var_msg=" . $var_msg . "&goback=1" . (!empty($eServiceType) ? '&eType=' . $eServiceType : ''));
            exit;
        }
    }
    if (isset($_FILES['vHomepageLogoOurServices']) && $_FILES['vHomepageLogoOurServices']['name'] != "") {
        $filecheck = basename($_FILES['vHomepageLogoOurServices']['name']);
        $fileextarr = explode(".", $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        $data = getimagesize($_FILES['vHomepageLogoOurServices']['tmp_name']);
        /*$width = $data[0];
        $height = $data[1];
        if ($width != 360 && $height != 360) {
            $flag_error = 1;
            $var_msg = "Please Upload image only 512px * 512px";
        }*/
        if ($flag_error == 1) {
            $_SESSION['success'] = '';
            $_SESSION['var_msg'] = '';
            header("Location:vehicle_category_action.php?id=" . $id . "&var_msg=" . $var_msg . "&goback=1" . (!empty($eServiceType) ? '&eType=' . $eServiceType : ''));
            exit;
        }
    }
    if (isset($_FILES['vHomepageBanner']) && $_FILES['vHomepageBanner']['name'] != "") {
        $filecheck = basename($_FILES['vHomepageBanner']['name']);
        $fileextarr = explode(".", $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        $data = getimagesize($_FILES['vHomepageBanner']['tmp_name']);
        $width = $data[0];
        $height = $data[1];
        if ($flag_error == 1) {
            $_SESSION['success'] = '';
            $_SESSION['var_msg'] = '';
            header("Location:vehicle_category_action.php?id=" . $id . "&var_msg=" . $var_msg . "&goback=1" . (!empty($eServiceType) ? '&eType=' . $eServiceType : ''));
            exit;
        }
    }
    if (isset($_FILES['vServiceHomepageBanner']) && $_FILES['vServiceHomepageBanner']['name'] != "") {
        $filecheck = basename($_FILES['vServiceHomepageBanner']['name']);
        $fileextarr = explode(".", $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        $data = getimagesize($_FILES['vServiceHomepageBanner']['tmp_name']);
        $width = $data[0];
        $height = $data[1];
        if ($flag_error == 1) {
            $_SESSION['success'] = '';
            $_SESSION['var_msg'] = '';
            header("Location:vehicle_category_action.php?id=" . $id . "&var_msg=" . $var_msg . "&goback=1" . (!empty($eServiceType) ? '&eType=' . $eServiceType : ''));
            exit;
        }
    }
    $vacategoryid = $id;
    $img_arr = $_FILES;
    if (!empty($img_arr)) {
        foreach ($img_arr as $key => $value) {
            if (!empty($value['name'])) {
                $img_path = $tconfig["tsite_upload_home_page_service_images_panel"];
                $temp_gallery = $img_path . '/';
                $image_object = $value['tmp_name'];
                $image_name = $value['name'];
                $check_file_query = "SELECT " . $key . " FROM " . $sql_vehicle_category_table_name . " where iVehicleCategoryId='" . $vacategoryid . "'";
                $check_file = $obj->MySQLSelect($check_file_query);
                if ($message_print_id != "") {
                    $check_file = $img_path . '/' . $check_file[0][$key];
                    if ($check_file != '' && file_exists($check_file[0][$key])) {
                        @unlink($check_file);
                    }
                }
                $Photo_Gallery_folder = $img_path . '/';
                if (!is_dir($Photo_Gallery_folder)) {
                    mkdir($Photo_Gallery_folder, 0777);
                }
                $img = $UPLOAD_OBJ->UploadImage($Photo_Gallery_folder, $image_object, $image_name, '', 'png,jpg,jpeg,gif,svg');
                if ($img[2] == "1") {
                    $_SESSION['success'] = '0';
                    $_SESSION['var_msg'] = $img[1];
                    header("location:" . $backlink);
                }
                if (!empty($img[0])) {
                    $sql = "UPDATE " . $sql_vehicle_category_table_name . " SET " . $key . " = '" . $img[0] . "' WHERE iVehicleCategoryId = '" . $vacategoryid . "'";
                    $obj->sql_query($sql);
                    //$_SESSION['success'] = '1';
                    //$_SESSION['var_msg'] = $img[1];
                } else {
                    $_SESSION['success'] = '0';
                    $_SESSION['var_msg'] = $img[1];
                }
            }
        }
    }
    //$vCatNameHomepage_EN = isset($_POST['vCatNameHomepage_EN']) ? $_POST['vCatNameHomepage_EN'] : '';
    //$vCatTitleHomepage_EN = isset($_POST['vCatTitleHomepage_EN']) ? $_POST['vCatTitleHomepage_EN'] : '';
    //$lCatDescHomepage_EN = isset($_POST['lCatDescHomepage_EN']) ? $_POST['lCatDescHomepage_EN'] : '';
    //$vCatDescbtnHomepage_EN = isset($_POST['vCatDescbtnHomepage_EN']) ? $_POST['vCatDescbtnHomepage_EN'] : '';
    $iDisplayOrderHomepage = isset($_POST['iDisplayOrderHomepage']) ? $_POST['iDisplayOrderHomepage'] : $iDisplayOrderHomepage;
    $temp_orderHomepage = isset($_POST['temp_orderHomepage']) ? $_POST['temp_orderHomepage'] : "";
    if ($temp_orderHomepage == "1" && $action == "Add") {
        $temp_orderHomepage = $iDisplayOrder_max_Homepage;
    }
    if ($temp_orderHomepage > $iDisplayOrderHomepage) {
        for ($i = $temp_orderHomepage - 1; $i >= $iDisplayOrderHomepage; $i--) {
            $sql = "UPDATE " . $tbl_name . " SET iDisplayOrderHomepage = '" . ($i + 1) . "' WHERE iDisplayOrderHomepage = '" . $i . "'";
            $obj->sql_query($sql);
        }
    } else if ($temp_orderHomepage < $iDisplayOrderHomepage) {
        for ($i = $temp_orderHomepage + 1; $i <= $iDisplayOrderHomepage; $i++) {
            //echo "temp_orderHomepage:".$temp_orderHomepage."<br>"."iDisplayOrderHomepage:".$iDisplayOrderHomepage;
            $sql = "UPDATE " . $tbl_name . " SET iDisplayOrderHomepage = '" . ($i - 1) . "' WHERE iDisplayOrderHomepage = '" . $i . "'";
            $obj->sql_query($sql);
        }
    }
    $vCatNameHomepageArr = $vCatTitleHomepageArr = $vCatSloganHomepageArr = $lCatDescHomepageArr = $vServiceCatTitleHomepageArr = array();
    if ($count_all > 0) {
        for ($i = 0; $i < $count_all; $i++) {
            $tTypeDesc = "";
            if (isset($_POST['vCatNameHomepage_' . $db_master[$i]['vCode']])) {
                $tTypeDesc = $_POST['vCatNameHomepage_' . $db_master[$i]['vCode']];
            }
            $vCatNameHomepageArr["vCatNameHomepage_" . $db_master[$i]['vCode']] = $tTypeDesc;
            $tTypeDesc = "";
            if (isset($_POST['vCatTitleHomepage_' . $db_master[$i]['vCode']])) {
                $tTypeDesc = $_POST['vCatTitleHomepage_' . $db_master[$i]['vCode']];
            }
            $vCatTitleHomepageArr["vCatTitleHomepage_" . $db_master[$i]['vCode']] = $tTypeDesc;
            $tTypeDesc = "";
            if (isset($_POST['vCatSloganHomepage_' . $db_master[$i]['vCode']])) {
                $tTypeDesc = $_POST['vCatSloganHomepage_' . $db_master[$i]['vCode']];
            }
            $vCatSloganHomepageArr["vCatSloganHomepage_" . $db_master[$i]['vCode']] = $tTypeDesc;
            $tTypeDesc = "";
            if (isset($_POST['lCatDescHomepage_' . $db_master[$i]['vCode']])) {
                $tTypeDesc = $_POST['lCatDescHomepage_' . $db_master[$i]['vCode']];
            }
            $lCatDescHomepageArr["lCatDescHomepage_" . $db_master[$i]['vCode']] = $tTypeDesc;
            $tTypeDesc = "";
            if (isset($_POST['vCatDescbtnHomepage_' . $db_master[$i]['vCode']])) {
                $tTypeDesc = $_POST['vCatDescbtnHomepage_' . $db_master[$i]['vCode']];
            }
            $vCatDescbtnHomepageArr["vCatDescbtnHomepage_" . $db_master[$i]['vCode']] = $tTypeDesc;
            if (isset($_POST['vServiceCatTitleHomepage_' . $db_master[$i]['vCode']])) {
                $tTypeDesc = $_POST['vServiceCatTitleHomepage_' . $db_master[$i]['vCode']];
            }
            $vServiceCatTitleHomepageArr["vServiceCatTitleHomepage_" . $db_master[$i]['vCode']] = $tTypeDesc;
            if (isset($_POST['vServiceCatSubTitleHomepage_' . $db_master[$i]['vCode']])) {
                $tTypeDesc = $_POST['vServiceCatSubTitleHomepage_' . $db_master[$i]['vCode']];
            }
            $vServiceCatTitleHomepageArr["vServiceCatSubTitleHomepage_" . $db_master[$i]['vCode']] = $tTypeDesc;
        }
    }
    $vCatNameHomepage = $vCatTitleHomepage = $lCatDescHomepage = $vCatDescbtnHomepage = $vServiceCatTitleHomepage = '';
    if (count($vCatNameHomepageArr) > 0) {
        $vCatNameHomepage = getJsonFromAnArr($vCatNameHomepageArr);
    }
    if (count($vCatTitleHomepageArr) > 0) {
        $vCatTitleHomepage = getJsonFromAnArr($vCatTitleHomepageArr);
    }
    if (count($vCatSloganHomepageArr) > 0) {
        $vCatSloganHomepage = getJsonFromAnArr($vCatSloganHomepageArr);
    }
    if (count($lCatDescHomepageArr) > 0) {
        $lCatDescHomepage = getJsonFromAnArr($lCatDescHomepageArr);
    }
    if (count($vCatDescbtnHomepageArr) > 0) {
        $vCatDescbtnHomepage = getJsonFromAnArr($vCatDescbtnHomepageArr);
    }
    if (count($vServiceCatTitleHomepageArr) > 0) {
        $vServiceCatTitleHomepage = getJsonFromAnArr($vServiceCatTitleHomepageArr);
    }
    /*$q = "INSERT INTO ";
    $where = '';
    if ($id != '') {
        $q = "UPDATE ";
        $where = " WHERE `iVehicleCategoryId` = '" . $id . "'";
    }
    $query = $q . " `" . $tbl_name . "` SET
    `vCatNameHomepage` = '" . $vCatNameHomepage . "',
    `vCatTitleHomepage` = '" . $vCatTitleHomepage . "',
    `vCatSloganHomepage` = '" . $vCatSloganHomepage . "',
    `lCatDescHomepage` = '" . $lCatDescHomepage . "',
    `vCatDescbtnHomepage` = '" . $vCatDescbtnHomepage . "',
    `iDisplayOrderHomepage` = '" . $iDisplayOrderHomepage . "',
    `vServiceCatTitleHomepage` = '" . $vServiceCatTitleHomepage . "'" . $where;

    $obj->sql_query($query);


    $id = ($id != '') ? $id : $obj->GetInsertId();*/
    $Data_update_vc = array();
    $Data_update_vc['vCatNameHomepage'] = $vCatNameHomepage;
    $Data_update_vc['vCatTitleHomepage'] = $vCatTitleHomepage;
    $Data_update_vc['vCatSloganHomepage'] = $vCatSloganHomepage;
    $Data_update_vc['lCatDescHomepage'] = $lCatDescHomepage;
    $Data_update_vc['vCatDescbtnHomepage'] = $vCatDescbtnHomepage;
    $Data_update_vc['iDisplayOrderHomepage'] = $iDisplayOrderHomepage;
    $Data_update_vc['vServiceCatTitleHomepage'] = $vServiceCatTitleHomepage;
    if ($id != '') {
        $where = " `iVehicleCategoryId` = '" . $id . "'";
        $obj->MySQLQueryPerform($tbl_name, $Data_update_vc, 'update', $where);
    } else {
        $id = $obj->MySQLQueryPerform($tbl_name, $Data_update_vc, 'insert');
    }
    if ($action == "Add") {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INSERT_MSG'];
    } else {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
    }
    header("Location:" . $backlink);
    exit;
} else {
    /************************************Homepage settings end***********************************************/
    $vCategory_EN = isset($_POST['vCategory_EN']) ? $_POST['vCategory_EN'] : '';
    $tCategoryDesc_EN = isset($_POST['tCategoryDesc_EN']) ? $_POST['tCategoryDesc_EN'] : '';
    $eBeforeUpload = isset($_POST['eBeforeUpload']) ? $_POST['eBeforeUpload'] : '';
    $eAfterUpload = isset($_POST['eAfterUpload']) ? $_POST['eAfterUpload'] : '';
    $eStatus = isset($_POST['eStatus']) ? $_POST['eStatus'] : '';
    $iParentId = isset($_POST['vCategory']) ? $_POST['vCategory'] : '';
    $eShowType = isset($_POST['eShowType']) ? $_POST['eShowType'] : 'Icon';
    $eCatViewType = isset($_POST['eCatViewType']) ? $_POST['eCatViewType'] : 'Icon';
    $ePriceType = isset($_POST['ePriceType']) ? $_POST['ePriceType'] : 'Service';
    $eMaterialCommision = isset($_POST['eMaterialCommision']) ? $_POST['eMaterialCommision'] : 'No';
    $fCommision = isset($_POST['fCommision']) ? $_POST['fCommision'] : 0;
    $iCancellationTimeLimit = isset($_POST['iCancellationTimeLimit']) ? $_POST['iCancellationTimeLimit'] : '';
    $fCancellationFare = isset($_POST['fCancellationFare']) ? $_POST['fCancellationFare'] : '';
    $iWaitingFeeTimeLimit = isset($_POST['iWaitingFeeTimeLimit']) ? $_POST['iWaitingFeeTimeLimit'] : '';
    $fWaitingFees = isset($_POST['fWaitingFees']) ? $_POST['fWaitingFees'] : '';
    $vTitle_store = $vDesc_store = $descArr = $serviceNameArr = array();
    $eShowTerms = isset($_POST['eShowTerms']) ? $_POST['eShowTerms'] : 'No';
    $eProofUpload = isset($_POST['eProofUpload']) ? $_POST['eProofUpload'] : 'No';
    $eOTPCodeEnable = isset($_POST['eOTPCodeEnable']) ? $_POST['eOTPCodeEnable'] : 'No';
    $ePromoteBanner = isset($_POST['ePromoteBanner']) ? $_POST['ePromoteBanner'] : 'No';
    $eVideoConsultEnable = isset($_POST['eVideoConsultEnable']) ? $_POST['eVideoConsultEnable'] : 'No';
    $eVideoConsultServiceCharge = isset($_POST['eVideoConsultServiceCharge']) ? $_POST['eVideoConsultServiceCharge'] : '0';
    $fCommissionVideoConsult = isset($_POST['fCommissionVideoConsult']) ? $_POST['fCommissionVideoConsult'] : '0';
    if ($count_all > 0) {
        for ($i = 0; $i < $count_all; $i++) {
            $vValue = 'vCategory_' . $db_master[$i]['vCode'];
            $vValue_desc = 'tCategoryDesc_' . $db_master[$i]['vCode'];
            array_push($vTitle_store, $vValue);
            $$vValue = isset($_POST[$vValue]) ? $_POST[$vValue] : '';
            array_push($vDesc_store, $vValue_desc);
            $tCategoryDesc = $vValue = "";
            if (isset($_POST['vCategory_' . $db_master[$i]['vCode']])) {
                $vValue = $_POST['vCategory_' . $db_master[$i]['vCode']];
            }
            if (isset($_POST['tCategoryDesc_' . $db_master[$i]['vCode']])) {
                $tCategoryDesc = $_POST['tCategoryDesc_' . $db_master[$i]['vCode']];
            }
            $$vValue_desc = isset($_POST[$vValue_desc]) ? $_POST[$vValue_desc] : '';
            $descArr["vCategory_" . $db_master[$i]['vCode']] = $vValue;
            $serviceNameArr["vServiceName_" . $db_master[$i]['vCode']] = $vValue;
            $descArr["tCategoryDesc_" . $db_master[$i]['vCode']] = $tCategoryDesc;
        }
    }
    $sql = "select vCategory_" . $default_lang . ", iVehicleCategoryId, eCatType, eFor from " . $sql_vehicle_category_table_name . " where iVehicleCategoryId='" . $sub_cid . "'";
    $db_data1 = $obj->MySQLSelect($sql);
    /* to fetch max iDisplayOrder from table for insert */
    if ($sub_action == "sub_category") {
        $select_order = $obj->MySQLSelect("SELECT count(iVehicleCategoryId) AS iDisplayOrder FROM " . $tbl_name . " WHERE iParentId = '" . $sub_cid . "'");
        $iDisplayOrder = isset($select_order[0]['iDisplayOrder']) ? $select_order[0]['iDisplayOrder'] : 0;
        $iDisplayOrder = $iDisplayOrder + 1; // Maximum order number
    } else {
        $select_order = $obj->MySQLSelect("SELECT count(iVehicleCategoryId) AS iDisplayOrder FROM " . $tbl_name . " WHERE iParentId = 0");
        $iDisplayOrder = isset($select_order[0]['iDisplayOrder']) ? $select_order[0]['iDisplayOrder'] : 0;
        $iDisplayOrder = $iDisplayOrder + 1; // Maximum order number
    }
    $iDisplayOrder = isset($_POST['iDisplayOrder']) ? $_POST['iDisplayOrder'] : $iDisplayOrder;
    $tBannerButtonTextArr = $tDescriptionArr = $tListDescriptionArr = $tPromoteBannerTitleArr = array();
    if (isset($_REQUEST['goback'])) {
        $goback = $_REQUEST['goback'];
    }
    if (isset($_POST['btnsubmit'])) {

        if ($action == "Add" && !$userObj->hasPermission($create)) {
            $_SESSION['success'] = 3;
            $_SESSION['var_msg'] = 'You do not have permission to create service category.';
            header("Location:vehicle_category.php");
            exit;
        }
        if ($action == "Edit" && !$userObj->hasPermission($update)) {
            $_SESSION['success'] = 3;
            $_SESSION['var_msg'] = 'You do not have permission to create service category.';
            header("Location:vehicle_category.php");
            exit;
        }
        if (isset($_FILES['vLogo']) && $_FILES['vLogo']['name'] != "") {
            $filecheck = basename($_FILES['vLogo']['name']);
            $fileextarr = explode(".", $filecheck);
            $ext = strtolower($fileextarr[count($fileextarr) - 1]);
            $flag_error = 0;
            $data = getimagesize($_FILES['vLogo']['tmp_name']);
            $width = $data[0];
            $height = $data[1];
            if ($width <= 360 && $height <= 360) {
                //$flag_error = 1;
                //$var_msg = "Please Upload minimum image 512px * 512px";
            }
            if ($flag_error == 1) {
                //$form = $obg->getPostForm($_REQUEST,$var_msgs,"");
                //echo $sub_action;die;
                if ($action == "Add") {
                    if ($sub_action == "sub_category") {
                        header("Location:vehicle_category_action.php?sub_action=sub_category&sub_cid=" . $sub_cid . "&var_msg=" . $var_msg);
                        exit;
                    } else {
                        header("Location:vehicle_category_action.php?id=" . $id . "&var_msg=" . $var_msg . "&goback=1");
                        exit;
                    }
                } else {
                    if ($sub_action == "sub_category") {
                        header("Location:vehicle_category_action.php?id=" . $id . "&sub_action=sub_category&sub_cid=" . $sub_cid . "&var_msg=" . $var_msg);
                        exit;
                    } else {
                        header("Location:vehicle_category_action.php?id=" . $id . "&var_msg=" . $var_msg . "&goback=1");
                        exit;
                    }
                }
            }
        }

        if (isset($_FILES['vLogo2']) && $_FILES['vLogo2']['name'] != "") {
            $filecheck = basename($_FILES['vLogo2']['name']);
            $fileextarr = explode(".", $filecheck);
            $ext = strtolower($fileextarr[count($fileextarr) - 1]);
            $flag_error = 0;
            $data = getimagesize($_FILES['vLogo2']['tmp_name']);
            $width = $data[0];
            $height = $data[1];
            if ($width <= 360 && $height <= 360) {
                //$flag_error = 1;
                //$var_msg = "Please Upload minimum image 512px * 512px";
            }
            if ($flag_error == 1) {
                //$form = $obg->getPostForm($_REQUEST,$var_msgs,"");
                //echo $sub_action;die;
                if ($action == "Add") {
                    if ($sub_action == "sub_category") {
                        header("Location:vehicle_category_action.php?sub_action=sub_category&sub_cid=" . $sub_cid . "&var_msg=" . $var_msg);
                        exit;
                    } else {
                        header("Location:vehicle_category_action.php?id=" . $id . "&var_msg=" . $var_msg . "&goback=1");
                        exit;
                    }
                } else {
                    if ($sub_action == "sub_category") {
                        header("Location:vehicle_category_action.php?id=" . $id . "&sub_action=sub_category&sub_cid=" . $sub_cid . "&var_msg=" . $var_msg);
                        exit;
                    } else {
                        header("Location:vehicle_category_action.php?id=" . $id . "&var_msg=" . $var_msg . "&goback=1");
                        exit;
                    }
                }
            }
        }

        if (isset($_FILES['vHomepageLogoOurServices']) && $_FILES['vHomepageLogoOurServices']['name'] != "") {
            $filecheck = basename($_FILES['vHomepageLogoOurServices']['name']);
            $fileextarr = explode(".", $filecheck);
            $ext = strtolower($fileextarr[count($fileextarr) - 1]);
            $flag_error = 0;
            $data = getimagesize($_FILES['vHomepageLogoOurServices']['tmp_name']);
            $width = $data[0];
            $height = $data[1];
            if ($width <= 360 && $height <= 360) {
                //$flag_error = 1;
                //$var_msg = "Please Upload minimum image 512px * 512px";
            }
            if ($flag_error == 1) {
                //$form = $obg->getPostForm($_REQUEST,$var_msgs,"");
                //echo $sub_action;die;
                if ($action == "Add") {
                    if ($sub_action == "sub_category") {
                        header("Location:vehicle_category_action.php?sub_action=sub_category&sub_cid=" . $sub_cid . "&var_msg=" . $var_msg);
                        exit;
                    } else {
                        header("Location:vehicle_category_action.php?id=" . $id . "&var_msg=" . $var_msg . "&goback=1");
                        exit;
                    }
                } else {
                    if ($sub_action == "sub_category") {
                        header("Location:vehicle_category_action.php?id=" . $id . "&sub_action=sub_category&sub_cid=" . $sub_cid . "&var_msg=" . $var_msg);
                        exit;
                    } else {
                        header("Location:vehicle_category_action.php?id=" . $id . "&var_msg=" . $var_msg . "&goback=1");
                        exit;
                    }
                }
            }
        }
        if (isset($_FILES['vBannerImage']) && $_FILES['vBannerImage']['name'] != "") {
            $filecheck = basename($_FILES['vBannerImage']['name']);
            $fileextarr = explode(".", $filecheck);
            $ext = strtolower($fileextarr[count($fileextarr) - 1]);
            $flag_error = 0;
            if ($ext != "png" && $ext != "jpg" && $ext != "jpeg" && $ext != "gif" && $ext != "bmp") {
                $flag_error = 1;
                $var_msg = "You have selected wrong file format for Image. Valid formats are jpg,jpeg,gif,png,bmp.";
            }
            /*$data = getimagesize($_FILES['vBannerImage']['tmp_name']);
        $width = $data[0];
        $height = $data[1];*/
            if ($flag_error == 1) {
                if ($action == "Add") {
                    if ($sub_action == "sub_category") {
                        header("Location:vehicle_category_action.php?sub_action=sub_category&sub_cid=" . $sub_cid . "&var_msg=" . $var_msg);
                        exit;
                    } else {
                        header("Location:vehicle_category_action.php?id=" . $id . "&var_msg=" . $var_msg);
                        exit;
                    }
                } else {
                    if ($sub_action == "sub_category") {
                        header("Location:vehicle_category_action.php?id=" . $id . "&sub_action=sub_category&sub_cid=" . $sub_cid . "&var_msg=" . $var_msg);
                        exit;
                    } else {
                        header("Location:vehicle_category_action.php?id=" . $id . "&var_msg=" . $var_msg);
                        exit;
                    }
                }
            }
        }
        if (isset($_FILES['vPromoteBannerImage']) && $_FILES['vPromoteBannerImage']['name'] != "") {
            $filecheck = basename($_FILES['vPromoteBannerImage']['name']);
            $fileextarr = explode(".", $filecheck);
            $ext = strtolower($fileextarr[count($fileextarr) - 1]);
            $flag_error = 0;
            if ($ext != "png" && $ext != "jpg" && $ext != "jpeg" && $ext != "gif" && $ext != "bmp") {
                $flag_error = 1;
                $var_msg = "You have selected wrong file format for Image. Valid formats are jpg,jpeg,gif,png,bmp.";
            }
            if ($flag_error == 1) {
                if ($action == "Add") {
                    if ($sub_action == "sub_category") {
                        header("Location:vehicle_category_action.php?sub_action=sub_category&sub_cid=" . $sub_cid . "&var_msg=" . $var_msg);
                        exit;
                    } else {
                        header("Location:vehicle_category_action.php?id=" . $id . "&var_msg=" . $var_msg);
                        exit;
                    }
                } else {
                    if ($sub_action == "sub_category") {
                        header("Location:vehicle_category_action.php?id=" . $id . "&sub_action=sub_category&sub_cid=" . $sub_cid . "&var_msg=" . $var_msg);
                        exit;
                    } else {
                        header("Location:vehicle_category_action.php?id=" . $id . "&var_msg=" . $var_msg);
                        exit;
                    }
                }
            }
        }
        if (isset($_FILES['vListLogo']) && $_FILES['vListLogo']['name'] != "") {
            $filecheck = basename($_FILES['vListLogo']['name']);
            $fileextarr = explode(".", $filecheck);
            $ext = strtolower($fileextarr[count($fileextarr) - 1]);
            $flag_error = 0;
            if ($ext != "png" && $ext != "jpg" && $ext != "jpeg" && $ext != "gif" && $ext != "bmp") {
                $flag_error = 1;
                $var_msg = "You have selected wrong file format for Image. Valid formats are jpg,jpeg,gif,png,bmp.";
            }

            if ($flag_error == 1) {
                if ($action == "Add") {
                    if ($sub_action == "sub_category") {
                        header("Location:vehicle_category_action.php?sub_action=sub_category&sub_cid=" . $sub_cid . "&var_msg=" . $var_msg);
                        exit;
                    } else {
                        header("Location:vehicle_category_action.php?id=" . $id . "&var_msg=" . $var_msg);
                        exit;
                    }
                } else {
                    if ($sub_action == "sub_category") {
                        header("Location:vehicle_category_action.php?id=" . $id . "&sub_action=sub_category&sub_cid=" . $sub_cid . "&var_msg=" . $var_msg);
                        exit;
                    } else {
                        header("Location:vehicle_category_action.php?id=" . $id . "&var_msg=" . $var_msg);
                        exit;
                    }
                }
            }
        }
        if (isset($_FILES['vListLogo1']) && $_FILES['vListLogo1']['name'] != "") {
            $filecheck = basename($_FILES['vListLogo1']['name']);
            $fileextarr = explode(".", $filecheck);
            $ext = strtolower($fileextarr[count($fileextarr) - 1]);
            $flag_error = 0;
            if ($ext != "png" && $ext != "jpg" && $ext != "jpeg" && $ext != "gif" && $ext != "bmp") {
                $flag_error = 1;
                $var_msg = "You have selected wrong file format for Image. Valid formats are jpg,jpeg,gif,png,bmp.";
            }

            if ($flag_error == 1) {
                if ($action == "Add") {
                    if ($sub_action == "sub_category") {
                        header("Location:vehicle_category_action.php?sub_action=sub_category&sub_cid=" . $sub_cid . "&var_msg=" . $var_msg);
                        exit;
                    } else {
                        header("Location:vehicle_category_action.php?id=" . $id . "&var_msg=" . $var_msg);
                        exit;
                    }
                } else {
                    if ($sub_action == "sub_category") {
                        header("Location:vehicle_category_action.php?id=" . $id . "&sub_action=sub_category&sub_cid=" . $sub_cid . "&var_msg=" . $var_msg);
                        exit;
                    } else {
                        header("Location:vehicle_category_action.php?id=" . $id . "&var_msg=" . $var_msg);
                        exit;
                    }
                }
            }
        }
        if (isset($_FILES['vListLogo2']) && $_FILES['vListLogo2']['name'] != "") {
            $filecheck = basename($_FILES['vListLogo2']['name']);
            $fileextarr = explode(".", $filecheck);
            $ext = strtolower($fileextarr[count($fileextarr) - 1]);
            $flag_error = 0;
            if ($ext != "png" && $ext != "jpg" && $ext != "jpeg" && $ext != "gif" && $ext != "bmp") {
                $flag_error = 1;
                $var_msg = "You have selected wrong file format for Image. Valid formats are jpg,jpeg,gif,png,bmp.";
            }

            if ($flag_error == 1) {
                if ($action == "Add") {
                    if ($sub_action == "sub_category") {
                        header("Location:vehicle_category_action.php?sub_action=sub_category&sub_cid=" . $sub_cid . "&var_msg=" . $var_msg);
                        exit;
                    } else {
                        header("Location:vehicle_category_action.php?id=" . $id . "&var_msg=" . $var_msg);
                        exit;
                    }
                } else {
                    if ($sub_action == "sub_category") {
                        header("Location:vehicle_category_action.php?id=" . $id . "&sub_action=sub_category&sub_cid=" . $sub_cid . "&var_msg=" . $var_msg);
                        exit;
                    } else {
                        header("Location:vehicle_category_action.php?id=" . $id . "&var_msg=" . $var_msg);
                        exit;
                    }
                }
            }
        }

        if (isset($_FILES['vListLogo3']) && $_FILES['vListLogo3']['name'] != "") {
            $filecheck = basename($_FILES['vListLogo3']['name']);
            $fileextarr = explode(".", $filecheck);
            $ext = strtolower($fileextarr[count($fileextarr) - 1]);
            $flag_error = 0;
            if ($ext != "png" && $ext != "jpg" && $ext != "jpeg" && $ext != "gif" && $ext != "bmp") {
                $flag_error = 1;
                $var_msg = "You have selected wrong file format for Image. Valid formats are jpg,jpeg,gif,png,bmp.";
            }

            if ($flag_error == 1) {
                if ($action == "Add") {
                    if ($sub_action == "sub_category") {
                        header("Location:vehicle_category_action.php?sub_action=sub_category&sub_cid=" . $sub_cid . "&var_msg=" . $var_msg);
                        exit;
                    } else {
                        header("Location:vehicle_category_action.php?id=" . $id . "&var_msg=" . $var_msg);
                        exit;
                    }
                } else {
                    if ($sub_action == "sub_category") {
                        header("Location:vehicle_category_action.php?id=" . $id . "&sub_action=sub_category&sub_cid=" . $sub_cid . "&var_msg=" . $var_msg);
                        exit;
                    } else {
                        header("Location:vehicle_category_action.php?id=" . $id . "&var_msg=" . $var_msg);
                        exit;
                    }
                }
            }
        }

        if (SITE_TYPE == 'Demo') {
            if ($sub_action == "sub_category") {
                header("Location:vehicle_category_action.php?id=" . $id . "&sub_action=sub_category&sub_cid=" . $sub_cid . "&success=2");
                exit;
            } else {
                header("Location:vehicle_category_action.php?id=" . $id . "&success=2");
                exit;
            }
        }

        for ($d = 0; $d < count($db_master); $d++) {
            $tBannerButtonText = "Book Now";
            $tDescription = $tListDescription = $tPromoteBannerTitle = "";
            if (isset($_POST['tBannerButtonText_' . $db_master[$d]['vCode']])) {
                $tBannerButtonText = $_POST['tBannerButtonText_' . $db_master[$d]['vCode']];
            }
            if (isset($_POST['tDescription_' . $db_master[$d]['vCode']])) {
                $tDescription = $_POST['tDescription_' . $db_master[$d]['vCode']];
            }
            if (isset($_POST['tListDescription_' . $db_master[$d]['vCode']])) {
                $tListDescription = $_POST['tListDescription_' . $db_master[$d]['vCode']];
            }
            if (isset($_POST['tPromoteBannerTitle_' . $db_master[$d]['vCode']])) {
                $tPromoteBannerTitle = $_POST['tPromoteBannerTitle_' . $db_master[$d]['vCode']];
            }
            if ($tBannerButtonText == "") {
                $tBannerButtonText = "Book Now";
            }
            $tBannerButtonTextArr["tBannerButtonText_" . $db_master[$d]['vCode']] = $tBannerButtonText;
            $tDescriptionArr["tDescription_" . $db_master[$d]['vCode']] = $tDescription;
            $tListDescriptionArr["tListDescription_" . $db_master[$d]['vCode']] = $tListDescription;
            $tPromoteBannerTitleArr["tPromoteBannerTitle_" . $db_master[$d]['vCode']] = $tPromoteBannerTitle;
        }
        $tDescriptionArr = array();
        //echo "<pre>";print_r($tListDescriptionArr);die;
        if (count($vTitle_store) > 0) {
            $setlanguage = $setServiceLanguage = "";
            /*foreach ($descArr as $key => $value) {
                $setlanguage .= "`" . $key . "`= '" . $value . "',";
            }*/
            //Added By HJ On 09-01-2019 For Update Data Into service_categories Table When Upadte Vehicle Category As Per Discuss With KS Sir Start
            $iServiceIdEdit = $_POST['iServiceIdEdit'];
            if ($iServiceIdEdit > 0) {
                foreach ($serviceNameArr as $key1 => $value1) {
                    $setServiceLanguage .= "`" . $key1 . "`= '" . $value1 . "',";
                }
                $setImage = "";
                if (isset($_FILES['vBannerImage']) && $_FILES['vBannerImage']['name'] != "") {
                    $bannerImage = $_FILES['vBannerImage'];
                    $img_path = $tconfig["tsite_upload_service_categories_images_path"];
                    $temp_gallery = $img_path . '/';
                    $image_object = $bannerImage['tmp_name'];
                    $image_name = $bannerImage['name'];
                    $check_file_query = "SELECT vImage FROM service_categories where iServiceId='" . $iServiceIdEdit . "'";
                    $check_file = $obj->MySQLSelect($check_file_query);
                    if ($message_print_id != "") {
                        $check_file = $img_path . '/' . $check_file[0]['vImage'];
                        if ($check_file != '' && file_exists($check_file[0]['vImage'])) {
                            @unlink($check_file);
                        }
                    }
                    $Photo_Gallery_folder = $img_path . '/';
                    if (!is_dir($Photo_Gallery_folder)) {
                        mkdir($Photo_Gallery_folder, 0777);
                    }
                    $img = $UPLOAD_OBJ->UploadImage($Photo_Gallery_folder, $image_object, $image_name, '', 'png,jpg,jpeg,gif');
                    //$img = $UPLOAD_OBJ->GeneralFileUploadHome($Photo_Gallery_folder,$image_object,$image_name,'','png,jpg,jpeg,gif','');
                    if ($img[2] == "1") {
                        $_SESSION['success'] = '0';
                        $_SESSION['var_msg'] = $img[1];
                        header("location:" . $backlink);
                    }
                    if (!empty($img[0])) {
                        $setImage = ",vImage='" . $img[0] . "'";
                    }
                }
                
                //Added By HJ On 14-08-2019 For Update Category Description Start Comment On 14-08-2019 As Per Discuss With KS Sir
                //$jsonServiceDesc = $obj->cleanQuery(json_encode($tDescriptionArr));
                //`tBannerButtonText` = '" . $jsonBannerButtonText . "'"
                //$update_service = "UPDATE `service_categories` SET " . trim($setServiceLanguage, ",") . " $setImage,`tDescription`='" . $jsonServiceDesc . "' WHERE iServiceId=" . $iServiceIdEdit;
                //Added By HJ On 14-08-2019 For Update Category Description End Comment On 14-08-2019 As Per Discuss With KS Sir
                $update_service = "UPDATE `service_categories` SET eShowTerms = '" . $eShowTerms . "', " . trim($setServiceLanguage, ",") . " $setImage  ,`eStatus` = '" . $eStatus . "' WHERE iServiceId=" . $iServiceIdEdit;
                $obj->sql_query($update_service);
                // Added by HV on 12-10-2020 for 18+ age verfication
                if ($MODULES_OBJ->isEnableTermsServiceCategories()) {
                    $update_service = "UPDATE `service_categories` SET eShowTerms = '" . $eShowTerms . "' WHERE iServiceId=" . $iServiceIdEdit;
                    $obj->sql_query($update_service);
                }
                // Added by HV on 12-10-2020 for 18+ proof upload
                if ($MODULES_OBJ->isEnableProofUploadServiceCategories()) {
                    $eProofUpload = isset($_POST['eProofUpload']) ? $_POST['eProofUpload'] : "No";
                    $tProofNote = isset($_POST['tProofNote']) ? $_POST['tProofNote'] : "";
                    $tProofNoteDriver = isset($_POST['tProofNoteDriver']) ? $_POST['tProofNoteDriver'] : "";
                    $tProofNoteStore = isset($_POST['tProofNoteStore']) ? $_POST['tProofNoteStore'] : "";
                    $Data_proof['eProofUpload'] = $eProofUpload;
                    $Data_proof['tProofNote'] = $tProofNote;
                    $Data_proof['tProofNoteDriver'] = $tProofNoteDriver;
                    $Data_proof['tProofNoteStore'] = $tProofNoteStore;
                    $where_proof = " iServiceId = $iServiceIdEdit";
                    $obj->MySQLQueryPerform("service_categories", $Data_proof, 'update', $where_proof);
                }

                $oCache->flushData();
                $GCS_OBJ->updateGCSData();
            }
            $Data_update_vc = array();
            $Data_update_vc['eBeforeUpload'] = $eBeforeUpload;
            $Data_update_vc['eAfterUpload'] = $eAfterUpload;
            $Data_update_vc['eStatus'] = $eStatus;
            $Data_update_vc['iParentId'] = $iParentId;
            $Data_update_vc['ePriceType'] = $ePriceType;
            $Data_update_vc['eMaterialCommision'] = $eMaterialCommision;
            $Data_update_vc['fCommision'] = $fCommision;
            $Data_update_vc['iCancellationTimeLimit'] = $iCancellationTimeLimit;
            $Data_update_vc['fCancellationFare'] = $fCancellationFare;
            $Data_update_vc['iWaitingFeeTimeLimit'] = $iWaitingFeeTimeLimit;
            $Data_update_vc['fWaitingFees'] = $fWaitingFees;
            $Data_update_vc['iDisplayOrder'] = $iDisplayOrder;
            $Data_update_vc['eShowType'] = $eShowType;
            $Data_update_vc['eCatViewType'] = implode(",", $eCatViewType);
            $Data_update_vc['eOTPCodeEnable'] = $eOTPCodeEnable;
            $Data_update_vc['ePromoteBanner'] = $ePromoteBanner;
            $Data_update_vc['eVideoConsultEnable'] = $eVideoConsultEnable;
            $Data_update_vc['eVideoConsultServiceCharge'] = $eVideoConsultServiceCharge;
            $Data_update_vc['fCommissionVideoConsult'] = $fCommissionVideoConsult;

            if($iParentId == 3) {
                $Data_update_vc['eForMedicalService'] = "Yes";
            }

            foreach ($descArr as $key => $value) {
                $Data_update_vc[$key] = $value;
            }
            if ($id != '') {
                $where = " `iVehicleCategoryId` = '" . $id . "'";
                $obj->MySQLQueryPerform($tbl_name, $Data_update_vc, 'update', $where);
            } else {
                $id = $obj->MySQLQueryPerform($tbl_name, $Data_update_vc, 'insert');
            }
            if (($MODULES_OBJ->isEnableAppHomeScreenLayoutV2() || $MODULES_OBJ->isEnableAppHomeScreenLayoutV3()) && $ePromoteBanner == "Yes") {
                $obj->sql_query("UPDATE $tbl_name SET ePromoteBanner = 'No' WHERE iVehicleCategoryId != '$id'");
            }
        }
        if ($id > 0 && count($tBannerButtonTextArr) > 0) {
            $jsonBannerButtonText = getJsonFromAnArr($tBannerButtonTextArr);
            $jsonListDescription = getJsonFromAnArr($tListDescriptionArr);
            $q = "UPDATE ";
            $whereCondition = " WHERE `iVehicleCategoryId` = '" . $id . "'";
            $update_query = $q . " `" . $tbl_name . "` SET `tBannerButtonText` = '" . $jsonBannerButtonText . "',`tListDescription`='" . $jsonListDescription . "'" . $whereCondition;
            $obj->sql_query($update_query);
        }
        if ($id > 0 && count($tPromoteBannerTitleArr) > 0) {
            $jsonPromoteBannerTitle = getJsonFromAnArr($tPromoteBannerTitleArr);
            $whereCondition = "  `iVehicleCategoryId` = '" . $id . "'";
            $Data_update_promote['tPromoteBannerTitle'] = $jsonPromoteBannerTitle;
            $obj->MySQLQueryPerform($tbl_name, $Data_update_promote, 'update', $whereCondition);
        }
        if (isset($_FILES['vLogo']) && $_FILES['vLogo']['name'] != "") {
            $currrent_upload_time = time();
            $img_path = $tconfig["tsite_upload_images_vehicle_category_path"];
            $temp_gallery = $img_path . '/';
            $image_object = $_FILES['vLogo']['tmp_name'];
            $image_name = $_FILES['vLogo']['name'];
            $check_file_query = "select iVehicleCategoryId,vLogo from " . $sql_vehicle_category_table_name . " where iVehicleCategoryId=" . $id;
            $check_file = $obj->sql_query($check_file_query);
            if ($image_name != "") {
                if ($message_print_id != "") {
                    $check_file['vLogo'] = $check_file[0]['vLogo'];
                    $android_path = $img_path . '/' . $id . '/android';
                    $ios_path = $img_path . '/' . $id . '/ios';
                    if ($check_file['vLogo'] != '') {
                        @unlink($android_path . '/' . $check_file['vLogo']);
                        @unlink($android_path . '/mdpi_' . $check_file['vLogo']);
                        @unlink($android_path . '/hdpi_' . $check_file['vLogo']);
                        @unlink($android_path . '/xhdpi_' . $check_file['vLogo']);
                        @unlink($android_path . '/xxhdpi_' . $check_file['vLogo']);
                        @unlink($android_path . '/xxxhdpi_' . $check_file['vLogo']);
                        @unlink($ios_path . '/' . $check_file['vLogo']);
                        @unlink($ios_path . '/1x_' . $check_file['vLogo']);
                        @unlink($ios_path . '/2x_' . $check_file['vLogo']);
                        @unlink($ios_path . '/3x_' . $check_file['vLogo']);
                    }
                }
                $Photo_Gallery_folder = $img_path . '/' . $id . '/';
                $Photo_Gallery_folder_android = $Photo_Gallery_folder . 'android/';
                $Photo_Gallery_folder_ios = $Photo_Gallery_folder . 'ios/';
                if (!is_dir($Photo_Gallery_folder)) {
                    mkdir($Photo_Gallery_folder, 0777);
                    mkdir($Photo_Gallery_folder_android, 0777);
                    mkdir($Photo_Gallery_folder_ios, 0777);
                }
                $vVehicleType1 = str_replace(' ', '', $vCategory_ . $default_lang);
                $img = $UPLOAD_OBJ->GeneralImageUploadVehicleCategoryAndroid($image_object, $image_name, $Photo_Gallery_folder_android, $tconfig["tsite_upload_images_vehicle_category_size1_android"], $tconfig["tsite_upload_images_vehicle_category_size2_android"], $tconfig["tsite_upload_images_vehicle_category_size3_both"], $tconfig["tsite_upload_images_vehicle_category_size4_android"], '', '', 'Y', $tconfig["tsite_upload_images_vehicle_category_size5_both"], $Photo_Gallery_folder_android, $vVehicleType1, NULL);
                $img1 = $UPLOAD_OBJ->GeneralImageUploadVehicleCategoryIOS($image_object, $image_name, $Photo_Gallery_folder_ios, '', '', $tconfig["tsite_upload_images_vehicle_category_size3_both"], $tconfig["tsite_upload_images_vehicle_category_size5_both"], '', '', 'Y', $tconfig["tsite_upload_images_vehicle_category_size5_ios"], $Photo_Gallery_folder_ios, $vVehicleType1, NULL);
                $img_time = explode("_", $img);
                $filecheck = basename($_FILES['vLogo']['name']);
                $fileextarr = explode(".", $filecheck);
                $ext = strtolower($fileextarr[count($fileextarr) - 1]);
                $time_val = $img_time[0];
                $vImage = "ic_car_" . $vVehicleType1 . "_" . $time_val . "." . $ext;
                //$vImage = "ic_car_".$vVehicleType1.".png";
                $sql = "UPDATE " . $tbl_name . " SET `vLogo` = '" . $vImage . "' WHERE `iVehicleCategoryId` = '" . $id . "'";
                $obj->sql_query($sql);
            }
        }

        if (isset($_FILES['vLogo2']) && $_FILES['vLogo2']['name'] != "") {
            $currrent_upload_time = time();
            $img_path = $tconfig["tsite_upload_images_vehicle_category_path"];
            $temp_gallery = $img_path . '/';
            $image_object = $_FILES['vLogo2']['tmp_name'];
            $image_name = $_FILES['vLogo2']['name'];
            $check_file_query = "select iVehicleCategoryId,vLogo2 from " . $sql_vehicle_category_table_name . " where iVehicleCategoryId=" . $id;
            $check_file = $obj->sql_query($check_file_query);
            if ($image_name != "") {
                if ($message_print_id != "") {
                    $check_file['vLogo2'] = $check_file[0]['vLogo2'];
                    $android_path = $img_path . '/' . $id . '/android';
                    $ios_path = $img_path . '/' . $id . '/ios';
                    if ($check_file['vLogo2'] != '') {
                        @unlink($android_path . '/' . $check_file['vLogo2']);
                        @unlink($android_path . '/mdpi_' . $check_file['vLogo2']);
                        @unlink($android_path . '/hdpi_' . $check_file['vLogo2']);
                        @unlink($android_path . '/xhdpi_' . $check_file['vLogo2']);
                        @unlink($android_path . '/xxhdpi_' . $check_file['vLogo2']);
                        @unlink($android_path . '/xxxhdpi_' . $check_file['vLogo2']);
                        @unlink($ios_path . '/' . $check_file['vLogo2']);
                        @unlink($ios_path . '/1x_' . $check_file['vLogo2']);
                        @unlink($ios_path . '/2x_' . $check_file['vLogo2']);
                        @unlink($ios_path . '/3x_' . $check_file['vLogo2']);
                    }
                }
                $Photo_Gallery_folder = $img_path . '/' . $id . '/';
                $Photo_Gallery_folder_android = $Photo_Gallery_folder . 'android/';
                $Photo_Gallery_folder_ios = $Photo_Gallery_folder . 'ios/';
                if (!is_dir($Photo_Gallery_folder)) {
                    mkdir($Photo_Gallery_folder, 0777);
                    mkdir($Photo_Gallery_folder_android, 0777);
                    mkdir($Photo_Gallery_folder_ios, 0777);
                }
                $vVehicleType1 = str_replace(' ', '', $vCategory_ . $default_lang);
                $img = $UPLOAD_OBJ->GeneralImageUploadVehicleCategoryAndroid($image_object, $image_name, $Photo_Gallery_folder_android, $tconfig["tsite_upload_images_vehicle_category_size1_android"], $tconfig["tsite_upload_images_vehicle_category_size2_android"], $tconfig["tsite_upload_images_vehicle_category_size3_both"], $tconfig["tsite_upload_images_vehicle_category_size4_android"], '', '', 'Y', $tconfig["tsite_upload_images_vehicle_category_size5_both"], $Photo_Gallery_folder_android, $vVehicleType1, NULL);
                $img1 = $UPLOAD_OBJ->GeneralImageUploadVehicleCategoryIOS($image_object, $image_name, $Photo_Gallery_folder_ios, '', '', $tconfig["tsite_upload_images_vehicle_category_size3_both"], $tconfig["tsite_upload_images_vehicle_category_size5_both"], '', '', 'Y', $tconfig["tsite_upload_images_vehicle_category_size5_ios"], $Photo_Gallery_folder_ios, $vVehicleType1, NULL);
                $img_time = explode("_", $img);
                $filecheck = basename($_FILES['vLogo2']['name']);
                $fileextarr = explode(".", $filecheck);
                $ext = strtolower($fileextarr[count($fileextarr) - 1]);
                $time_val = $img_time[0];
                $vImage = "ic_car_" . $vVehicleType1 . "_" . $time_val . "." . $ext;
                //$vImage = "ic_car_".$vVehicleType1.".png";
                $sql = "UPDATE " . $tbl_name . " SET `vLogo2` = '" . $vImage . "' WHERE `iVehicleCategoryId` = '" . $id . "'";
                $obj->sql_query($sql);
            }
        }

        if (isset($_FILES['vHomepageLogoOurServices']) && $_FILES['vHomepageLogoOurServices']['name'] != "") {


            $currrent_upload_time = time();
            $img_path = $tconfig["tsite_upload_images_vehicle_category_path"];
            $temp_gallery = $img_path . '/';
            $image_object = $_FILES['vHomepageLogoOurServices']['tmp_name'];
            $image_name = $_FILES['vHomepageLogoOurServices']['name'];
            $check_file_query = "select iVehicleCategoryId,vHomepageLogoOurServices from " . $sql_vehicle_category_table_name . " where iVehicleCategoryId=" . $id;
            $check_file = $obj->sql_query($check_file_query);
            if ($image_name != "") {
                if ($message_print_id != "") {
                    $check_file['vHomepageLogoOurServices'] = $check_file[0]['vHomepageLogoOurServices'];
                    $android_path = $img_path . '/' . $id . '/android';
                    $ios_path = $img_path . '/' . $id . '/ios';
                    if ($check_file['vHomepageLogoOurServices'] != '') {


                        /*@unlink($android_path . '/' . $check_file['vHomepageLogoOurServices']);
                        @unlink($android_path . '/mdpi_' . $check_file['vHomepageLogoOurServices']);
                        @unlink($android_path . '/hdpi_' . $check_file['vHomepageLogoOurServices']);
                        @unlink($android_path . '/xhdpi_' . $check_file['vHomepageLogoOurServices']);
                        @unlink($android_path . '/xxhdpi_' . $check_file['vHomepageLogoOurServices']);
                        @unlink($android_path . '/xxxhdpi_' . $check_file['vHomepageLogoOurServices']);
                        @unlink($ios_path . '/' . $check_file['vHomepageLogoOurServices']);
                        @unlink($ios_path . '/1x_' . $check_file['vHomepageLogoOurServices']);
                        @unlink($ios_path . '/2x_' . $check_file['vHomepageLogoOurServices']);
                        @unlink($ios_path . '/3x_' . $check_file['vHomepageLogoOurServices']);*/
                    }
                }
                $Photo_Gallery_folder = $img_path . '/' . $id . '/';
                $Photo_Gallery_folder_android = $Photo_Gallery_folder . 'android/';
                $Photo_Gallery_folder_ios = $Photo_Gallery_folder . 'ios/';
                if (!is_dir($Photo_Gallery_folder)) {
                    mkdir($Photo_Gallery_folder, 0777);
                    mkdir($Photo_Gallery_folder_android, 0777);
                    mkdir($Photo_Gallery_folder_ios, 0777);
                }
                $vVehicleType1 = str_replace(' ', '', $vCategory_ . $default_lang);
                $img = $UPLOAD_OBJ->GeneralImageUploadVehicleCategoryAndroid($image_object, $image_name, $Photo_Gallery_folder_android, $tconfig["tsite_upload_images_vehicle_category_size1_android"], $tconfig["tsite_upload_images_vehicle_category_size2_android"], $tconfig["tsite_upload_images_vehicle_category_size3_both"], $tconfig["tsite_upload_images_vehicle_category_size4_android"], '', '', 'Y', $tconfig["tsite_upload_images_vehicle_category_size5_both"], $Photo_Gallery_folder_android, $vVehicleType1, NULL);
                $img1 = $UPLOAD_OBJ->GeneralImageUploadVehicleCategoryIOS($image_object, $image_name, $Photo_Gallery_folder_ios, '', '', $tconfig["tsite_upload_images_vehicle_category_size3_both"], $tconfig["tsite_upload_images_vehicle_category_size5_both"], '', '', 'Y', $tconfig["tsite_upload_images_vehicle_category_size5_ios"], $Photo_Gallery_folder_ios, $vVehicleType1, NULL);
                $img_time = explode("_", $img);
                $filecheck = basename($_FILES['vHomepageLogoOurServices']['name']);
                $fileextarr = explode(".", $filecheck);
                $ext = strtolower($fileextarr[count($fileextarr) - 1]);
                $time_val = $img_time[0];
                $vImage = "ic_car_" . $vVehicleType1 . "_" . $time_val . "." . $ext;
                //$vImage = "ic_car_".$vVehicleType1.".png";
                $sql = "UPDATE " . $tbl_name . " SET `vHomepageLogoOurServices` = '" . $vImage . "' WHERE `iVehicleCategoryId` = '" . $id . "'";
                $obj->sql_query($sql);
            }
        }
        if (isset($_FILES['vLogo1']) && $_FILES['vLogo1']['name'] != "") {
            $currrent_upload_time = time() + 10;
            $img_path = $tconfig["tsite_upload_images_vehicle_category_path"];
            $temp_gallery = $img_path . '/';
            $image_object = $_FILES['vLogo1']['tmp_name'];
            $image_name = $_FILES['vLogo1']['name'];
            $check_file_query = "select iVehicleCategoryId,vLogo1 from " . $sql_vehicle_category_table_name . " where iVehicleCategoryId=" . $id;
            $check_file = $obj->sql_query($check_file_query);
            if ($image_name != "") {
                if ($message_print_id != "") {
                    $check_file['vLogo1'] = $check_file[0]['vLogo1'];
                    $android_path = $img_path . '/' . $id . '/android';
                    $ios_path = $img_path . '/' . $id . '/ios';
                    if ($check_file['vLogo1'] != '') {
                        @unlink($android_path . '/' . $check_file['vLogo1']);
                        @unlink($android_path . '/mdpi_hover_' . $check_file['vLogo1']);
                        @unlink($android_path . '/hdpi_hover_' . $check_file['vLogo1']);
                        @unlink($android_path . '/xhdpi_hover_' . $check_file['vLogo1']);
                        @unlink($android_path . '/xxhdpi_hover_' . $check_file['vLogo1']);
                        @unlink($android_path . '/xxxhdpi_hover_' . $check_file['vLogo1']);
                        @unlink($ios_path . '/' . $check_file['vLogo1']);
                        @unlink($ios_path . '/1x_hover_' . $check_file['vLogo1']);
                        @unlink($ios_path . '/2x_hover_' . $check_file['vLogo1']);
                        @unlink($ios_path . '/3x_hover_' . $check_file['vLogo1']);
                    }
                }
                $Photo_Gallery_folder = $img_path . '/' . $id . '/';
                $Photo_Gallery_folder_android = $Photo_Gallery_folder . '/android/';
                $Photo_Gallery_folder_ios = $Photo_Gallery_folder . '/ios/';
                if (!is_dir($Photo_Gallery_folder)) {
                    mkdir($Photo_Gallery_folder, 0777);
                    mkdir($Photo_Gallery_folder_android, 0777);
                    mkdir($Photo_Gallery_folder_ios, 0777);
                }
                $vVehicleType1 = str_replace(' ', '', $vCategory_ . $default_lang);
                $img = $UPLOAD_OBJ->GeneralImageUploadVehicleCategoryAndroid($image_object, $image_name, $Photo_Gallery_folder_android, $tconfig["tsite_upload_images_vehicle_category_size1_android"], $tconfig["tsite_upload_images_vehicle_category_size2_android"], $tconfig["tsite_upload_images_vehicle_category_size3_both"], $tconfig["tsite_upload_images_vehicle_category_size4_android"], '', '', 'Y', $tconfig["tsite_upload_images_vehicle_type_size5_both"], $Photo_Gallery_folder_android, $vVehicleType1, "hover_");
                $img1 = $UPLOAD_OBJ->GeneralImageUploadVehicleCategoryIOS($image_object, $image_name, $Photo_Gallery_folder_ios, '', '', $tconfig["tsite_upload_images_vehicle_category_size3_both"], $tconfig["tsite_upload_images_vehicle_category_size5_both"], '', '', 'Y', $tconfig["tsite_upload_images_vehicle_category_size5_ios"], $Photo_Gallery_folder_ios, $vVehicleType1, "hover_");
                $img_time = explode("_", $img);
                $time_val = $img_time[0];
                $vImage1 = "ic_car_" . $vVehicleType1 . "_" . $time_val . ".png";
                //$vImage1 = "ic_car_".$vVehicleType1.".png";
                $sql = "UPDATE " . $tbl_name . " SET `vLogo1` = '" . $vImage1 . "' WHERE `iVehicleCategoryId` = '" . $id . "'";
                $obj->sql_query($sql);
            }
        }
        if (isset($_FILES['vBannerImage']) && $_FILES['vBannerImage']['name'] != "") {
            $currrent_upload_time = time();
            $img_path = $tconfig["tsite_upload_images_vehicle_category_path"];
            $temp_gallery = $img_path . '/';
            $image_object = $_FILES['vBannerImage']['tmp_name'];
            $image_name = $_FILES['vBannerImage']['name'];
            $data = getimagesize($_FILES['vBannerImage']['tmp_name']);
            $imgwidth = $data[0];
            $imgheight = $data[1];
            /* Calculate aspect ratio by dividing height by width */
            $aspectRatio = $imgwidth / $imgheight;
            $aspect = round($aspectRatio, 2);
            if ($aspect != "1.78") {
                echo "<script>alert('Please upload image with recommended dimensions and aspect ratio 16:9. Otherwise image will look stretched.');</script>";
            }
            if ($imgwidth < 2880) {
                echo "<script>alert('Your Image upload size is less than recommended. Image will look stretched.');</script>";
            }
            if ($imgheight > 2880) {
                echo "<script>alert('Uploaded image size is larger than recommended size, Image may take much time to load.');</script>";
            }

            if (strtoupper($APP_TYPE) == "RIDE-DELIVERY" || $THEME_OBJ->isPXCProThemeActive() == "Yes") {
                $db_data_master = $obj->MySQLSelect("SELECT vBannerImage FROM " . $sql_vehicle_category_table_name . " WHERE iVehicleCategoryId = '" . $id . "'");
                $vBannerImageArr = array();
                if (!empty($db_data_master[0]['vBannerImage'])) {
                    $vBannerImageArr = json_decode($db_data_master[0]['vBannerImage'], true);
                } else {
                    foreach ($db_master as $dbvalue) {
                        $vBannerImageArr['vBannerImage_' . $dbvalue['vCode']] = '';
                    }
                }
            }

            $check_file_query = "select iVehicleCategoryId,vBannerImage from " . $sql_vehicle_category_table_name . " where iVehicleCategoryId=" . $id;
            $check_file = $obj->sql_query($check_file_query);
            if ($image_name != "") {
                if ($message_print_id != "") {
                    $check_file['vBannerImage'] = $img_path . '/' . $id . '/' . $check_file[0]['vBannerImage'];
                    $android_path = $img_path . '/' . $id;
                    if ($check_file['vBannerImage'] != '' && file_exists($check_file['vBannerImage'])) {
                        @unlink($check_file['vBannerImage']);
                    }
                }
                $Photo_Gallery_folder = $img_path . '/' . $id . '/';
                $Photo_Gallery_folder_android = $Photo_Gallery_folder;
                if (!is_dir($Photo_Gallery_folder)) {
                    mkdir($Photo_Gallery_folder, 0777);
                    mkdir($Photo_Gallery_folder_android, 0777);
                    mkdir($Photo_Gallery_folder_ios, 0777);
                }
                $vVehicleType1 = str_replace(' ', '', $vCategory_ . $default_lang);
                $img = $UPLOAD_OBJ->GeneralImageUploadVehicleCategoryAndroid($image_object, $image_name, $Photo_Gallery_folder_android, '', '', '', '', '', '', 'Y', '', $Photo_Gallery_folder_android, $vVehicleType1, NULL);
                $img_time = explode("_", $img);
                $time_val = $img_time[0];
                $filecheck = basename($_FILES['vBannerImage']['name']);
                $fileextarr = explode(".", $filecheck);
                $ext = strtolower($fileextarr[count($fileextarr) - 1]);
                $vImage = "ic_car_" . $vVehicleType1 . "_" . $time_val . "." . $ext;

                if (strtoupper($APP_TYPE) == "RIDE-DELIVERY" || $THEME_OBJ->isPXCProThemeActive() == "Yes") {
                    $vBannerImageArr['vBannerImage_' . $banner_lang] = $vImage;
                    $vImage = json_encode($vBannerImageArr);
                }

                $sql = "UPDATE " . $tbl_name . " SET `vBannerImage` = '" . $vImage . "' WHERE `iVehicleCategoryId` = '" . $id . "'";
                $obj->sql_query($sql);
            }
        }
        if (isset($_FILES['vPromoteBannerImage']) && $_FILES['vPromoteBannerImage']['name'] != "") {
            $currrent_upload_time = time();
            $img_path = $tconfig["tsite_upload_images_vehicle_category_path"];
            $temp_gallery = $img_path . '/';
            $image_object = $_FILES['vPromoteBannerImage']['tmp_name'];
            $image_name = $_FILES['vPromoteBannerImage']['name'];
            $data = getimagesize($_FILES['vPromoteBannerImage']['tmp_name']);
            $imgwidth = $data[0];
            $imgheight = $data[1];
            /* Calculate aspect ratio by dividing height by width */
            $aspectRatio = $imgwidth / $imgheight;
            $aspect = round($aspectRatio, 2);
            if ($aspect != "1.78") {
                echo "<script>alert('Please upload image with recommended dimensions and aspect ratio 16:9. Otherwise image will look stretched.');</script>";
            }
            if ($imgwidth < 2880) {
                echo "<script>alert('Your Image upload size is less than recommended. Image will look stretched.');</script>";
            }
            if ($imgheight > 2880) {
                echo "<script>alert('Uploaded image size is larger than recommended size, Image may take much time to load.');</script>";
            }
            $check_file_query = "select iVehicleCategoryId,vPromoteBannerImage from " . $sql_vehicle_category_table_name . " where iVehicleCategoryId=" . $id;
            $check_file = $obj->sql_query($check_file_query);
            if ($image_name != "") {
                if ($message_print_id != "") {
                    $check_file['vPromoteBannerImage'] = $img_path . '/' . $id . '/' . $check_file[0]['vPromoteBannerImage'];
                    $android_path = $img_path . '/' . $id;
                    if ($check_file['vPromoteBannerImage'] != '' && file_exists($check_file['vPromoteBannerImage'])) {
                        @unlink($check_file['vPromoteBannerImage']);
                    }
                }
                $Photo_Gallery_folder = $img_path . '/' . $id . '/';
                $Photo_Gallery_folder_android = $Photo_Gallery_folder;
                if (!is_dir($Photo_Gallery_folder)) {
                    mkdir($Photo_Gallery_folder, 0777);
                    mkdir($Photo_Gallery_folder_android, 0777);
                    mkdir($Photo_Gallery_folder_ios, 0777);
                }
                $vVehicleType1 = str_replace(' ', '', $vCategory_ . $default_lang);
                $img = $UPLOAD_OBJ->GeneralImageUploadVehicleCategoryAndroid($image_object, $image_name, $Photo_Gallery_folder_android, '', '', '', '', '', '', 'Y', '', $Photo_Gallery_folder_android, $vVehicleType1, NULL);
                $img_time = explode("_", $img);
                $time_val = $img_time[0];
                $filecheck = basename($_FILES['vPromoteBannerImage']['name']);
                $fileextarr = explode(".", $filecheck);
                $ext = strtolower($fileextarr[count($fileextarr) - 1]);
                $vImage = "ic_car_" . $vVehicleType1 . "_" . $time_val . "." . $ext;
                $sql = "UPDATE " . $tbl_name . " SET `vPromoteBannerImage` = '" . $vImage . "' WHERE `iVehicleCategoryId` = '" . $id . "'";
                $obj->sql_query($sql);
            }
        }
        if (isset($_FILES['vListLogo']) && $_FILES['vListLogo']['name'] != "") {
            $currrent_upload_time = time();
            $img_path = $tconfig["tsite_upload_images_vehicle_category_path"];
            $temp_gallery = $img_path . '/';
            $image_object = $_FILES['vListLogo']['tmp_name'];
            $image_name = $_FILES['vListLogo']['name'];
            $data = getimagesize($_FILES['vListLogo']['tmp_name']);
            $imgwidth = $data[0];
            $imgheight = $data[1];
            /* Calculate aspect ratio by dividing height by width */
            $aspectRatio = $imgwidth / $imgheight;
            $aspect = round($aspectRatio, 2);
            if ($aspect != "1.78") {
                echo "<script>alert('Please upload image with recommended dimensions and aspect ratio 16:9. Otherwise image will look stretched.');</script>";
            }
            if ($imgwidth < 2880) {
                echo "<script>alert('Your Image upload size is less than recommended. Image will look stretched.');</script>";
            }
            if ($imgheight > 2880) {
                echo "<script>alert('Uploaded image size is larger than recommended size, Image may take much time to load.');</script>";
            }
            $check_file_query = "select iVehicleCategoryId,vListLogo from " . $sql_vehicle_category_table_name . " where iVehicleCategoryId=" . $id;
            $check_file = $obj->sql_query($check_file_query);
            if ($image_name != "") {
                if ($message_print_id != "") {
                    $check_file['vListLogo'] = $img_path . '/' . $id . '/' . $check_file[0]['vListLogo'];
                    $android_path = $img_path . '/' . $id;
                    if ($check_file['vListLogo'] != '' && file_exists($check_file['vListLogo'])) {
                        @unlink($check_file['vListLogo']);
                    }
                }
                $Photo_Gallery_folder = $img_path . '/' . $id . '/';
                $Photo_Gallery_folder_android = $Photo_Gallery_folder;
                if (!is_dir($Photo_Gallery_folder)) {
                    mkdir($Photo_Gallery_folder, 0777);
                    mkdir($Photo_Gallery_folder_android, 0777);
                    mkdir($Photo_Gallery_folder_ios, 0777);
                }
                $vVehicleType1 = str_replace(' ', '', $vCategory_ . $default_lang);
                $img = $UPLOAD_OBJ->GeneralImageUploadVehicleCategoryAndroid($image_object, $image_name, $Photo_Gallery_folder_android, '', '', '', '', '', '', 'Y', '', $Photo_Gallery_folder_android, $vVehicleType1, NULL);
                $img_time = explode("_", $img);
                $time_val = $img_time[0];
                $filecheck = basename($_FILES['vListLogo']['name']);
                $fileextarr = explode(".", $filecheck);
                $ext = strtolower($fileextarr[count($fileextarr) - 1]);
                $vImage = "ic_car_" . $vVehicleType1 . "_" . $time_val . "." . $ext;
                $sql = "UPDATE " . $tbl_name . " SET `vListLogo` = '" . $vImage . "' WHERE `iVehicleCategoryId` = '" . $id . "'";
                $obj->sql_query($sql);
            }
        }
        if (isset($_FILES['vListLogo1']) && $_FILES['vListLogo1']['name'] != "") {
            $currrent_upload_time = time();
            $img_path = $tconfig["tsite_upload_images_vehicle_category_path"];
            $temp_gallery = $img_path . '/';
            $image_object = $_FILES['vListLogo1']['tmp_name'];
            $image_name = $_FILES['vListLogo1']['name'];
            $data = getimagesize($_FILES['vListLogo1']['tmp_name']);
            $imgwidth = $data[0];
            $imgheight = $data[1];
            /* Calculate aspect ratio by dividing height by width */
            $aspectRatio = $imgwidth / $imgheight;
            $aspect = round($aspectRatio, 2);
            if ($aspect != "1.78") {
                echo "<script>alert('Please upload image with recommended dimensions and aspect ratio 16:9. Otherwise image will look stretched.');</script>";
            }
            if ($imgwidth < 2880) {
                echo "<script>alert('Your Image upload size is less than recommended. Image will look stretched.');</script>";
            }
            if ($imgheight > 2880) {
                echo "<script>alert('Uploaded image size is larger than recommended size, Image may take much time to load.');</script>";
            }
            $check_file_query = "select iVehicleCategoryId,vListLogo1 from " . $sql_vehicle_category_table_name . " where iVehicleCategoryId=" . $id;
            $check_file = $obj->sql_query($check_file_query);
            if ($image_name != "") {
                if ($message_print_id != "") {
                    $check_file['vListLogo1'] = $img_path . '/' . $id . '/' . $check_file[0]['vListLogo1'];
                    $android_path = $img_path . '/' . $id;
                    if ($check_file['vListLogo1'] != '' && file_exists($check_file['vListLogo1'])) {
                        @unlink($check_file['vListLogo1']);
                    }
                }
                $Photo_Gallery_folder = $img_path . '/' . $id . '/';
                $Photo_Gallery_folder_android = $Photo_Gallery_folder;
                if (!is_dir($Photo_Gallery_folder)) {
                    mkdir($Photo_Gallery_folder, 0777);
                    mkdir($Photo_Gallery_folder_android, 0777);
                    mkdir($Photo_Gallery_folder_ios, 0777);
                }
                $vVehicleType1 = str_replace(' ', '', $vCategory_ . $default_lang);
                $img = $UPLOAD_OBJ->GeneralImageUploadVehicleCategoryAndroid($image_object, $image_name, $Photo_Gallery_folder_android, '', '', '', '', '', '', 'Y', '', $Photo_Gallery_folder_android, $vVehicleType1, NULL);
                $img_time = explode("_", $img);
                $time_val = $img_time[0];
                $filecheck = basename($_FILES['vListLogo1']['name']);
                $fileextarr = explode(".", $filecheck);
                $ext = strtolower($fileextarr[count($fileextarr) - 1]);
                $vImage = "ic_car_" . $vVehicleType1 . "_" . $time_val . "." . $ext;
                $sql = "UPDATE " . $tbl_name . " SET `vListLogo1` = '" . $vImage . "' WHERE `iVehicleCategoryId` = '" . $id . "'";
                $obj->sql_query($sql);
            }
        }
        if (isset($_FILES['vListLogo2']) && $_FILES['vListLogo2']['name'] != "") {
            $currrent_upload_time = time();
            $img_path = $tconfig["tsite_upload_images_vehicle_category_path"];
            $temp_gallery = $img_path . '/';
            $image_object = $_FILES['vListLogo2']['tmp_name'];
            $image_name = $_FILES['vListLogo2']['name'];
            $data = getimagesize($_FILES['vListLogo2']['tmp_name']);
            $imgwidth = $data[0];
            $imgheight = $data[1];
            /* Calculate aspect ratio by dividing height by width */
            $aspectRatio = $imgwidth / $imgheight;
            $aspect = round($aspectRatio, 2);
            if ($aspect != "1.78") {
                echo "<script>alert('Please upload image with recommended dimensions and aspect ratio 16:9. Otherwise image will look stretched.');</script>";
            }
            if ($imgwidth < 2880) {
                echo "<script>alert('Your Image upload size is less than recommended. Image will look stretched.');</script>";
            }
            if ($imgheight > 2880) {
                echo "<script>alert('Uploaded image size is larger than recommended size, Image may take much time to load.');</script>";
            }
            $check_file_query = "select iVehicleCategoryId,vListLogo2 from " . $sql_vehicle_category_table_name . " where iVehicleCategoryId=" . $id;
            $check_file = $obj->sql_query($check_file_query);
            if ($image_name != "") {
                if ($message_print_id != "") {
                    $check_file['vListLogo2'] = $img_path . '/' . $id . '/' . $check_file[0]['vListLogo2'];
                    $android_path = $img_path . '/' . $id;
                    if ($check_file['vListLogo2'] != '' && file_exists($check_file['vListLogo2'])) {
                        // @unlink($check_file['vListLogo2']);
                    }
                }
                $Photo_Gallery_folder = $img_path . '/' . $id . '/';
                $Photo_Gallery_folder_android = $Photo_Gallery_folder;
                if (!is_dir($Photo_Gallery_folder)) {
                    mkdir($Photo_Gallery_folder, 0777);
                    mkdir($Photo_Gallery_folder_android, 0777);
                    mkdir($Photo_Gallery_folder_ios, 0777);
                }
                $vVehicleType1 = str_replace(' ', '', $vCategory_ . $default_lang);
                $img = $UPLOAD_OBJ->GeneralImageUploadVehicleCategoryAndroid($image_object, $image_name, $Photo_Gallery_folder_android, '', '', '', '', '', '', 'Y', '', $Photo_Gallery_folder_android, $vVehicleType1, NULL);
                $img_time = explode("_", $img);
                $time_val = $img_time[0];
                $filecheck = basename($_FILES['vListLogo2']['name']);
                $fileextarr = explode(".", $filecheck);
                $ext = strtolower($fileextarr[count($fileextarr) - 1]);
                $vImage = "ic_car_" . $vVehicleType1 . "_" . $time_val . "." . $ext;
                $sql = "UPDATE " . $tbl_name . " SET `vListLogo2` = '" . $vImage . "' WHERE `iVehicleCategoryId` = '" . $id . "'";
                $obj->sql_query($sql);
            }
        }

        if (isset($_FILES['vListLogo3']) && $_FILES['vListLogo3']['name'] != "") {
            $currrent_upload_time = time();
            $img_path = $tconfig["tsite_upload_images_vehicle_category_path"];
            $temp_gallery = $img_path . '/';
            $image_object = $_FILES['vListLogo3']['tmp_name'];
            $image_name = $_FILES['vListLogo3']['name'];
            $data = getimagesize($_FILES['vListLogo3']['tmp_name']);
            $imgwidth = $data[0];
            $imgheight = $data[1];
            /* Calculate aspect ratio by dividing height by width */
            $aspectRatio = $imgwidth / $imgheight;
            $aspect = round($aspectRatio, 2);
            if ($aspect != "1.78") {
                echo "<script>alert('Please upload image with recommended dimensions and aspect ratio 16:9. Otherwise image will look stretched.');</script>";
            }
            if ($imgwidth < 2880) {
                echo "<script>alert('Your Image upload size is less than recommended. Image will look stretched.');</script>";
            }
            if ($imgheight > 2880) {
                echo "<script>alert('Uploaded image size is larger than recommended size, Image may take much time to load.');</script>";
            }
            $check_file_query = "select iVehicleCategoryId,vListLogo3 from " . $sql_vehicle_category_table_name . " where iVehicleCategoryId=" . $id;
            $check_file = $obj->sql_query($check_file_query);
            if ($image_name != "") {
                if ($message_print_id != "") {
                    $check_file['vListLogo3'] = $img_path . '/' . $id . '/' . $check_file[0]['vListLogo3'];
                    $android_path = $img_path . '/' . $id;
                    if ($check_file['vListLogo3'] != '' && file_exists($check_file['vListLogo3'])) {
                        // @unlink($check_file['vListLogo2']);
                    }
                }
                $Photo_Gallery_folder = $img_path . '/' . $id . '/';
                $Photo_Gallery_folder_android = $Photo_Gallery_folder;
                if (!is_dir($Photo_Gallery_folder)) {
                    mkdir($Photo_Gallery_folder, 0777);
                    mkdir($Photo_Gallery_folder_android, 0777);
                    mkdir($Photo_Gallery_folder_ios, 0777);
                }
                $vVehicleType1 = str_replace(' ', '', $vCategory_ . $default_lang);
                $img = $UPLOAD_OBJ->GeneralImageUploadVehicleCategoryAndroid($image_object, $image_name, $Photo_Gallery_folder_android, '', '', '', '', '', '', 'Y', '', $Photo_Gallery_folder_android, $vVehicleType1, NULL);
                $img_time = explode("_", $img);
                $time_val = $img_time[0];
                $filecheck = basename($_FILES['vListLogo3']['name']);
                $fileextarr = explode(".", $filecheck);
                $ext = strtolower($fileextarr[count($fileextarr) - 1]);
                $vImage = "ic_car_" . $vVehicleType1 . "_" . $time_val . "." . $ext;
                $sql = "UPDATE " . $tbl_name . " SET `vListLogo3` = '" . $vImage . "' WHERE `iVehicleCategoryId` = '" . $id . "'";
                $obj->sql_query($sql);
            }
        }

        if ($action == "Add") {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INSERT_MSG'];
        } else {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        }
        // echo $backlink; exit;

        if ($THEME_OBJ->isCubeJekXv3ProThemeActive() && (in_array($eServiceType, ['Deliver', 'Genie', 'Runner'])) ) {

            $id = $vId;
        }else {
            header("Location:" . $backlink);
            exit;
        }
    }
}
// for Edit
$userEditDataArr = $db_data = $serviceDescArr = $tListDescriptionArr = array();
$MoreServicesMS = "No";
$eCatType = "ServiceProvider"; // Default Define ServiceProvider As Per Discuss with KS For Solved Mantis #11176
if ($action == 'Edit') {
    $sql = "SELECT * FROM " . $tbl_name . " WHERE iVehicleCategoryId = '" . $id . "'";
    $db_data = $obj->MySQLSelect($sql);
    //echo "<pre>";
    //print_r($db_data);die;
    $vLabel = $id;
    $getServiceDesc = $obj->MySQLSelect("SELECT iServiceId,tDescription FROM service_categories");
    for ($d = 0; $d < count($getServiceDesc); $d++) {
        $serviceDescArr[$getServiceDesc[$d]['iServiceId']] = $getServiceDesc[$d];
    }
    if (count($db_data) > 0) {
        $tBannerButtonText = json_decode($db_data[0]['tBannerButtonText'], true);
        foreach ($tBannerButtonText as $key => $value) {
            $userEditDataArr[$key] = $value;
        }
        $tListDescription = json_decode($db_data[0]['tListDescription'], true);
        foreach ($tListDescription as $key => $value) {
            $tListDescriptionArr[$key] = $value;
        }
        $tPromoteBannerTitle = json_decode($db_data[0]['tPromoteBannerTitle'], true);
        foreach ($tPromoteBannerTitle as $key => $value) {
            $userEditDataArr[$key] = $value;
        }
        //echo "<pre>";print_R($tListDescriptionArr);die;
        for ($i = 0; $i < count($db_master); $i++) {
            foreach ($db_data as $key => $value) {
                $vValue = 'vCategory_' . $db_master[$i]['vCode'];
                $$vValue = $value[$vValue];
                $vValue_desc = 'tCategoryDesc_' . $db_master[$i]['vCode'];
                $$vValue_desc = $value[$vValue_desc];
                $eBeforeUpload = $value['eBeforeUpload'];
                $eAfterUpload = $value['eAfterUpload'];
                $eStatus = $value['eStatus'];
                $iParentId = $value['iParentId'];
                $ePriceType = $value['ePriceType'];
                $eMaterialCommision = $value['eMaterialCommision'];
                $fCommision = $value['fCommision'];
                $eShowType = $value['eShowType'];
                $eCatViewType = explode(",", $value['eCatViewType']);
                //echo "<pre>";print_r($eCatViewType);die;
                $vLogo = $value['vLogo'];
                $vLogo2 = $value['vLogo2'];
                $iVehicleCategoryId = $value['iVehicleCategoryId'];
                $iDisplayOrder = $value['iDisplayOrder'];
                $vBannerImage = $value['vBannerImage'];
                $vPromoteBannerImage = $value['vPromoteBannerImage'];
                $vListLogo = $value['vListLogo'];
                $vListLogo1 = $value['vListLogo1'];
                $vListLogo2 = $value['vListLogo2'];
                $vListLogo3 = $value['vListLogo3'];
                $eCatType = $value['eCatType'];
                $iServiceIdEdit = $value['iServiceId'];
                $eFor = $value['eFor'];
                $iCancellationTimeLimit = ($value['iCancellationTimeLimit'] == 0) ? '' : $value['iCancellationTimeLimit'];
                $fCancellationFare = ($value['fCancellationFare'] == 0) ? '' : $value['fCancellationFare'];
                $iWaitingFeeTimeLimit = ($value['iWaitingFeeTimeLimit'] == 0) ? '' : $value['iWaitingFeeTimeLimit'];
                $fWaitingFees = ($value['fWaitingFees'] == 0) ? '' : $value['fWaitingFees'];
                $eOTPCodeEnable = $value['eOTPCodeEnable'];
                $ePromoteBanner = $value['ePromoteBanner'];
                $eVideoConsultEnable = $value['eVideoConsultEnable'];
                $eVideoConsultServiceCharge = $value['eVideoConsultServiceCharge'];
                $fCommissionVideoConsult = $value['fCommissionVideoConsult'];
                $eForMedicalService = $value['eForMedicalService'];
                $tMedicalServiceInfo = $value['tMedicalServiceInfo'];
                $vIconDetails = $value['vIconDetails'];

                if(strtoupper($APP_TYPE) == "RIDE-DELIVERY" || $THEME_OBJ->isPXCProThemeActive() == "Yes") {
                    $vBannerImageArr = json_decode($db_data[0]['vBannerImage'], true);
                    $vBannerImage = $vBannerImageArr['vBannerImage_' . $banner_lang];
                }
            }
        }
    }
    if ($iServiceIdEdit > 0) {
        $scsql = "select eShowTerms,eProofUpload,tProofNote,tProofNoteDriver,tProofNoteStore from service_categories WHERE iServiceId = " . $iServiceIdEdit;
        $scsqlData = $obj->MySQLSelect($scsql);
        $eShowTerms = $scsqlData[0]['eShowTerms'];
        $eProofUpload = $scsqlData[0]['eProofUpload'];
        $tProofNote = $scsqlData[0]['tProofNote'];
        $tProofNoteLang = json_decode($tProofNote, true);
        $tProofNoteLang = $tProofNoteLang['tProofNote_' . $default_lang];
        $tProofNoteDriver = $scsqlData[0]['tProofNoteDriver'];
        $tProofNoteDriverLang = json_decode($tProofNoteDriver, true);
        $tProofNoteDriverLang = $tProofNoteDriverLang['tProofNoteDriver_' . $default_lang];
        $tProofNoteStore = $scsqlData[0]['tProofNoteStore'];
        $tProofNoteStoreLang = json_decode($tProofNoteStore, true);
        $tProofNoteStoreLang = $tProofNoteStoreLang['tProofNoteStore_' . $default_lang];
    }
    if ($eForMedicalService == "Yes" && !empty($tMedicalServiceInfo)) {
        $tMedicalServiceInfoArr = json_decode($tMedicalServiceInfo, true);
        if ($tMedicalServiceInfoArr['MoreService'] == "Yes") {
            $MoreServicesMS = "Yes";
        }
    }
}
if (isset($serviceDescArr[$iServiceIdEdit]['tDescription']) && $serviceDescArr[$iServiceIdEdit]['tDescription'] != "") {
    $tDescription = (array)json_decode($serviceDescArr[$iServiceIdEdit]['tDescription']);
    foreach ($tDescription as $key1 => $value1) {
        $userEditDataArr[$key1] = $value1;
    }
}
if (in_array($eCatType, ['Genie', 'Runner', 'Anywhere'])) {
    $eCatTypeAlt = $eCatType;
    if ($eCatType == "Genie" || $eCatType == "Anywhere") {
        $eCatTypeAlt = "Genie";
    }
    $bannerBuyAnyServiceData = $obj->MySQLSelect("SELECT vImage FROM banners WHERE eBuyAnyService = '$eCatTypeAlt' AND eStatus = 'Active' AND vCode = '$default_lang' AND eFor = 'General' ORDER BY iFaqcategoryId LIMIT 3");
    if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV2()) {
        $PackageTypeBuyAnyServiceData = $obj->MySQLSelect("SELECT JSON_UNQUOTE(JSON_EXTRACT(tTitle, '$.tTitle_" . $default_lang . "')) as tTitle, vImage FROM genie_package_types WHERE eFor = '$eCatTypeAlt' AND eStatus = 'Active' ORDER BY iDisplayOrder LIMIT 5");
        if ($eCatTypeAlt == "Runner") {
            $appBannerData = $obj->MySQLSelect("SELECT vImage FROM banners WHERE eBuyAnyService = 'Runner' AND eFor = 'AppHomeScreen' AND eStatus = 'Active' AND vCode = '$default_lang'");
        }
    }
}
if ($eCatType == "MoreDelivery") {
    if ($eFor == "DeliveryCategory") {
        $eTypeBanner = "Deliver";
    } elseif ($eFor == "DeliverAllCategory") {
        $eTypeBanner = "DeliverAll";
    }
    $bannerDelivery = $obj->MySQLSelect("SELECT vImage FROM banners WHERE eType = '$eTypeBanner' AND eStatus = 'Active' AND vCode = '$default_lang' ORDER BY iFaqcategoryId LIMIT 3");

} elseif ($eServiceType == "UberX" && $iParentId == 0) {
    $bannerDelivery = $obj->MySQLSelect("SELECT vImage FROM banners WHERE eType = 'UberX' AND iVehicleCategoryId = '$id' AND eStatus = 'Active' AND vCode = '$default_lang' ORDER BY iFaqcategoryId LIMIT 3");
    $eFor = "UberX&iVehicleCategoryId=" . $id;
}
$promotionalBanner = $obj->MySQLSelect("SELECT vImage FROM banners WHERE eFor = 'Promotion' AND eStatus = 'Active' AND vCode = '$default_lang' AND iVehicleCategoryId = '$id' ");
$EN_available = $LANG_OBJ->checkLanguageExist();
$db_master = $LANG_OBJ->getLangDataDefaultFirst($db_master);
$stylehide = "";
if ($parent_ufx_catid > 0 && $iParentId == 0) { //added bcoz if parent category in homesanitization site not shown logo, order and status
    $stylehide = " style ='display:none'";
}
if ($MODULES_OBJ->isEnableAppHomeScreenLayout() && $THEME_OBJ->isDeliveryKingXv2ThemeActive() == "No") {
    if ($eCatType == "ServiceProvider") {
        $eCatViewType = array("Icon");
    } else {
        $eCatViewType = array("List");
    }
}
$display = "";
if ($THEME_OBJ->isCubeJekXv3ThemeActive() == "No" || $THEME_OBJ->isCubeJekXv3ProThemeActive() == "No" ) {
    $display = 'style="display:none"';
}

$enablePromoteBanner = "No";
if($MODULES_OBJ->isEnableAppHomeScreenLayoutV2() || $MODULES_OBJ->isEnableAppHomeScreenLayoutV3()) {
    $enablePromoteBanner = "Yes";
}
if(strtoupper($APP_TYPE) != "RIDE-DELIVERY-UBERX" || $THEME_OBJ->isPXCProThemeActive() == "Yes" || strtoupper(ONLYDELIVERALL) == "YES" || $THEME_OBJ->isProDeliveryKingThemeActive() == "Yes") {
    $enablePromoteBanner = "No";   
}
?>
<!DOCTYPE html><!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title>Admin | <?= $langage_lbl_admin['LBL_VEHICLE_CATEGORY_ADMIN']; ?> <?= $actionSave; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <link href="assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet"/>
    <?
    include_once('global_files.php');
    ?>
    <!-- On OFF switch -->
    <link href="../assets/css/jquery-ui.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css"/>
    <style type="text/css">
        .logo-preview-img {
            height: 100px;
            object-fit: cover;
            margin-bottom: 20px
        }

        #id_proof_note_lang .form-group {
            padding-bottom: 0;
        }

        .manage-banner-section .banner-img-block {
            justify-content: center;
        }
    </style>
</head>
<!-- END  HEAD-->
<!-- BEGIN BODY-->
<body class="padTop53 ">
<!-- MAIN WRAPPER -->
<div id="wrap">
    <?
    include_once('header.php');
    include_once('left_menu.php');
    ?>
    <!--PAGE CONTENT -->
    <div id="content">
        <div class="inner">
            <div class="row">
                <div class="col-lg-12">
                    <h2> <?php if ($sub_cid != "") { ?><?= "Sub " . $langage_lbl_admin['LBL_VEHICLE_CATEGORY_TXT_ADMIN']; ?>(<?php echo $db_data1[0]['vCategory_' . $default_lang] ?>)<?php } else { ?> <?= $langage_lbl_admin['LBL_VEHICLE_CATEGORY_TXT_ADMIN']; ?><?php } ?></h2>
                    <?php
                    if ($sub_cid != "" && $eServiceType != "MedicalServices") {
                        $redirect_back_page = 'vehicle_sub_category.php?sub_cid=' . $sub_cid . (!empty($eServiceType) ? '&eServiceType=' . $eServiceType : '');
                        if ($parent_ufx_catid != '0') { //added by SP on 05-10-2019 for changeredirect url change when direct sub category opened
                            // $redirect_back_page .= '&subcat=' . $sub_cid;
                        }
                    } else {
                        $redirect_back_page = 'vehicle_category.php' . (!empty($eServiceType) ? '?eType=' . $eServiceType : '');
                    }
                    if (isset($_REQUEST['homepage']) && $_REQUEST['homepage'] == 1 && !empty($_SERVER['HTTP_REFERER'])) {
                        $redirect_back_page = $_SERVER['HTTP_REFERER'];
                    }
                    if(strtoupper(ONLY_MEDICAL_SERVICE) == "YES") {
                        $redirect_back_page = 'vehicle_category.php';
                    }
                    ?>

                    <?php
                    if ($THEME_OBJ->isCubeJekXv3ProThemeActive() == 'No' || !in_array($eServiceType, ['Deliver','Genie','Runner']) || $sub_action == "sub_category") { ?>
                    <a href="<?php echo $redirect_back_page; ?>">
                        <input type="button" value="Back to Listing" class="add-btn">
                    </a>
                    <?php } ?>
                </div>
            </div>
            <hr/>
            <?= $form; ?>
            <div class="body-div">
                <div class="form-group">
                    <?php include('valid_msg.php'); ?>
                    
                    <?php if ($cubexthemeon == 1 && empty($sub_cid)) {
                    $activetab = 'general';
                    if (isset($_REQUEST['homepage']) && $_REQUEST['homepage'] == 1) {
                        $activetab = 'homepage';
                    }
                    if ($homepage_cubejekx == 0) {
                        ?>
                        <ul class="nav nav-tabs">
                            <li class="">
                                <a data-toggle="tab" href="#"></a>
                            </li>
                            <li class="<?php if ($activetab == 'general') { ?> active <?php } ?>">
                                <a data-toggle="tab" href="#Generalsettings">General</a>
                            </li>
                            <li class="<?php if ($activetab == 'homepage') { ?> active <?php } ?>">
                                <a data-toggle="tab" href="#Homepagesettings">Home page settings</a>
                            </li>
                        </ul>
                    <?php } ?>
                    <div class="tab-content">
                        <div id="Generalsettings"
                             class="tab-pane <?php if ($activetab == 'general') { ?> active <?php } ?>">
                            <?php } ?>
                            <div id="price1"></div>
                            <div id="price"></div>
                            <form id="vtype" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?= $id; ?>"/>
                                <input type="hidden" name="previousLink" id="previousLink"
                                       value="<?php echo $previousLink; ?>"/>
                                <input type="hidden" name="backlink" class="backlink"
                                       value="vehicle_category.php<?= !empty($eServiceType) ? '?eType=' . $eServiceType : '' ?>"/>
                                <?php if ($sub_action == "sub_category") { ?>
                                    <div class="row" style="display: none;">
                                        <div class="col-lg-12">
                                            <label>Parent Category :</label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <select class="form-control" name='vCategory' id='vCategory'>
                                                <?php for ($i = 0; $i < count($db_data1); $i++) { ?>
                                                    <option value="<?php echo $db_data1[$i]['iVehicleCategoryId'] ?>" <?= ($db_data1[$i]['iVehicleCategoryId'] == $iVehicleCategoryId) ? 'selected' : ''; ?>><?php echo $db_data1[$i]['vCategory_' . $default_lang]; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <input type="hidden" name="vCategory" value="0">
                                    <?php
                                }
                                if ($count_all > 0) { ?><?php if (count($db_master) > 1) { ?>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label>Category</label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <input type="text"
                                                   class="form-control <?= ($id == "") ? 'readonly-custom' : '' ?>"
                                                   id="vCategory_Default"
                                                   value="<?= $db_data[0]['vCategory_' . $default_lang]; ?>"
                                                   data-originalvalue="<?= $db_data[0]['vCategory_' . $default_lang]; ?>"
                                                   readonly="readonly" <?php if ($id == "") { ?> onclick="editCategory('Add')" <?php } ?>>
                                        </div>
                                        <?php if ($id != "") { ?>
                                            <div class="col-lg-2">
                                                <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                        data-original-title="Edit" onclick="editCategory('Edit')">
                                                    <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="modal fade" id="vCategory_Modal" tabindex="-1" role="dialog"
                                         aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content nimot-class">
                                                <div class="modal-header">
                                                    <h4>
                                                        <span id="category_action"></span> Category
                                                        <button type="button" class="close" data-dismiss="modal"
                                                                onclick="resetToOriginalValue(this, 'vCategory_')">x
                                                        </button>
                                                    </h4>
                                                </div>
                                                <div class="modal-body">
                                                    <?php
                                                    for ($i = 0; $i < $count_all; $i++) {
                                                        $vCode = $db_master[$i]['vCode'];
                                                        $vTitle = $db_master[$i]['vTitle'];
                                                        $eDefault = $db_master[$i]['eDefault'];
                                                        $vValue = 'vCategory_' . $vCode;
                                                        $required = ($eDefault == 'Yes') ? 'required' : '';
                                                        $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                        ?><?php
                                                        $page_title_class = 'col-lg-12';
                                                        if (count($db_master) > 1) {
                                                            if ($EN_available) {
                                                                if ($vCode == "EN") {
                                                                    $page_title_class = 'col-md-9 col-sm-9';
                                                                }
                                                            } else {
                                                                if ($vCode == $default_lang) {
                                                                    $page_title_class = 'col-md-9 col-sm-9';
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <label>Category (<?= $vTitle; ?>
                                                                    ) <?php echo $required_msg; ?></label>
                                                            </div>
                                                            <div class="<?= $page_title_class ?>">
                                                                <input type="text" class="form-control"
                                                                       name="<?= $vValue; ?>" id="<?= $vValue; ?>"
                                                                       value="<?= $$vValue; ?>"
                                                                       data-originalvalue="<?= $$vValue; ?>"
                                                                       placeholder="<?= $vTitle; ?> Value">
                                                                <div class="text-danger" id="<?= $vValue . '_error'; ?>"
                                                                     style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                            </div>
                                                            <?php
                                                            if (count($db_master) > 1) {
                                                                if ($EN_available) {
                                                                    if ($vCode == "EN") { ?>
                                                                        <div class="col-md-3 col-sm-3">
                                                                            <button type="button" name="allLanguage"
                                                                                    id="allLanguage"
                                                                                    class="btn btn-primary"
                                                                                    onClick="getAllLanguageCode('vCategory_', 'EN');">
                                                                                Convert To All Language
                                                                            </button>
                                                                        </div>
                                                                    <?php }
                                                                } else {
                                                                    if ($vCode == $default_lang) { ?>
                                                                        <div class="col-md-3 col-sm-3">
                                                                            <button type="button" name="allLanguage"
                                                                                    id="allLanguage"
                                                                                    class="btn btn-primary"
                                                                                    onClick="getAllLanguageCode('vCategory_', '<?= $default_lang ?>');">
                                                                                Convert To All Language
                                                                            </button>
                                                                        </div>
                                                                    <?php }
                                                                }
                                                            }
                                                            ?>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                <div class="modal-footer" style="margin-top: 0">
                                                    <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                        <strong><?= $langage_lbl['LBL_NOTE']; ?>
                                                            : </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?>
                                                    </h5>
                                                    <div class="nimot-class-but" style="margin-bottom: 0">
                                                        <button type="button" class="save" id="vCategory_btn"
                                                                style="margin-left: 0 !important"
                                                                onclick="saveCategory()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                        <button type="button" class="btn btn-danger btn-ok"
                                                                data-dismiss="modal"
                                                                onclick="resetToOriginalValue(this, 'vCategory_')">
                                                            Cancel
                                                        </button>
                                                    </div>
                                                </div>
                                                <div style="clear:both;"></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label>Category</label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <input type="text" class="form-control"
                                                   name="vCategory_<?= $default_lang; ?>"
                                                   id="vCategory_<?= $default_lang; ?>"
                                                   value="<?= $db_data[0]['vCategory_' . $default_lang]; ?>" required>
                                        </div>
                                    </div>
                                <?php } ?><?php if ($MODULES_OBJ->isEnableOTPVerificationUberX() && $eCatType == "ServiceProvider" && $sub_action != "sub_category") { ?>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label>Ask OTP/Confirmation code before starting Other Services
                                                <i class="icon-question-sign" data-placement="top" data-toggle="tooltip"
                                                   data-original-title="Ask OTP/Confirmation code before starting Other Services"></i></label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <div class="make-switch" data-on="success" data-off="warning">
                                                <input type="checkbox" id="eOTPCodeEnable"
                                                       name="eOTPCodeEnable" <?= ($id != '' && $eOTPCodeEnable == 'Yes') ? 'checked' : ''; ?>
                                                       value="Yes"/>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?><?php if ($eCatType == 'ServiceProvider' || $action == 'Add') {
                                    ?>
                                    <div class="row epricetype" style="display: none;">
                                        <div class="col-lg-12">
                                            <label>Price Based On
                                                <i class="icon-question-sign" data-placement="top" data-toggle="tooltip"
                                                   data-original-title="1. Service - Administrator will define Service Charge <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> - You want <?php echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']; ?> to Edit the charges defined by you. From the <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> Application, they can set their own service charges."></i></label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <select class="form-control" name='ePriceType' id='ePriceType'>
                                                <option value="Service"
                                                        <? if ('Service' == $db_data[0]['ePriceType']) { ?>selected<? } ?>>
                                                    Service ( Site Administrator will define the price)
                                                </option>
                                                <option value="Provider"
                                                        <? if ('Provider' == $db_data[0]['ePriceType']) { ?>selected<? } ?>><?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?>
                                                    ( <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> will set
                                                    their own price )
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row eMaterialCommision" style="display: none;">
                                        <div class="col-lg-12">
                                            <label>Commission On Material/Misc Fee
                                                <i class="icon-question-sign" data-placement="top" data-toggle="tooltip"
                                                   data-original-title="Apply commission on the extra materials used during service, apart from service charge."></i></label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <select class="form-control" name="eMaterialCommision"
                                                    id="eMaterialCommision">
                                                <option value="Yes"
                                                        <? if ("Yes" == $eMaterialCommision) { ?>selected<? } ?>>Yes
                                                </option>
                                                <option value="No"
                                                        <? if ("No" == $eMaterialCommision) { ?>selected<? } ?>>No
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <?php
                                }
                                    if (($sub_action == "sub_category" && $eCatType != "DeliverAll" && $eCatType != "ServiceProvider") || ($sub_action == "sub_category" && $eCatType == "ServiceProvider" && $SERVICE_PROVIDER_FLOW != "Provider")) {
                                        ?><?php if (count($db_master) > 1) { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Category Description</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea
                                                            class="form-control <?= ($id == "") ? 'readonly-custom' : '' ?>"
                                                            id="tCategoryDesc_Default"
                                                            readonly="readonly" <?php if ($id == "") { ?> onclick="editCategoryDescription('Add')" <?php } ?>
                                                            data-originalvalue="<?= $db_data[0]['tCategoryDesc_' . $default_lang]; ?>"><?= $db_data[0]['tCategoryDesc_' . $default_lang]; ?></textarea>
                                                </div>
                                                <?php if ($id != "") { ?>
                                                    <div class="col-lg-2">
                                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                                data-original-title="Edit"
                                                                onclick="editCategoryDescription('Edit')">
                                                            <span class="glyphicon glyphicon-pencil"
                                                                  aria-hidden="true"></span>
                                                        </button>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <div class="modal fade" id="tCategoryDesc_Modal" tabindex="-1" role="dialog"
                                                 aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="category_desc_action"></span> Category
                                                                Description
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                        onclick="resetToOriginalValue(this, 'tCategoryDesc_')">
                                                                    x
                                                                </button>
                                                            </h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php
                                                            for ($i = 0; $i < $count_all; $i++) {
                                                                $vCode = $db_master[$i]['vCode'];
                                                                $vTitle = $db_master[$i]['vTitle'];
                                                                $eDefault = $db_master[$i]['eDefault'];
                                                                $required = ($eDefault == 'Yes') ? 'required' : '';
                                                                $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                                $vValue_desc = 'tCategoryDesc_' . $vCode;
                                                                ?><?php
                                                                $page_title_class = 'col-lg-12';
                                                                if (count($db_master) > 1) {
                                                                    if ($EN_available) {
                                                                        if ($vCode == "EN") {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    } else {
                                                                        if ($vCode == $default_lang) {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <label>Category Description (<?= $vTitle; ?>
                                                                            ) <?php echo $required_msg; ?></label>
                                                                    </div>
                                                                    <div class="<?= $page_title_class ?>">
                                                                        <textarea class="form-control"
                                                                                  name="<?= $vValue_desc; ?>"
                                                                                  id="<?= $vValue_desc; ?>"
                                                                                  placeholder="<?= $vTitle; ?> Value"
                                                                                  data-originalvalue="<?= $$vValue_desc; ?>"><?= $$vValue_desc; ?></textarea>
                                                                        <div class="text-danger"
                                                                             id="<?= $vValue_desc . '_error'; ?>"
                                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                                    </div>
                                                                    <?php
                                                                    if (count($db_master) > 1) {
                                                                        if ($EN_available) {
                                                                            if ($vCode == "EN") { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button"
                                                                                            name="allLanguage"
                                                                                            id="allLanguage"
                                                                                            class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('tCategoryDesc_', 'EN');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        } else {
                                                                            if ($vCode == $default_lang) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button"
                                                                                            name="allLanguage"
                                                                                            id="allLanguage"
                                                                                            class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('tCategoryDesc_', '<?= $default_lang ?>');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="modal-footer" style="margin-top: 0">
                                                            <h5 class="text-left"
                                                                style="margin-bottom: 15px; margin-top: 0;">
                                                                <strong><?= $langage_lbl['LBL_NOTE']; ?>
                                                                    : </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?>
                                                            </h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save"
                                                                        id="tCategoryDesc_btn"
                                                                        style="margin-left: 0 !important"
                                                                        onclick="saveCategoryDescription()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok"
                                                                        data-dismiss="modal"
                                                                        onclick="resetToOriginalValue(this, 'tCategoryDesc_')">
                                                                    Cancel
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Category Description</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control"
                                                              name="tCategoryDesc_<?= $default_lang ?>"
                                                              id="tCategoryDesc_<?= $default_lang ?>"><?= $db_data[0]['tCategoryDesc_' . $default_lang]; ?></textarea>
                                                </div>
                                            </div>
                                        <?php } ?><?
                                    }
                                    if ($iServiceIdEdit > 0 && $descEnable == 1) {
                                        ?><?php if (count($db_master) > 1) { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Service Description</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea
                                                            class="form-control <?= ($id == "") ? 'readonly-custom' : '' ?>"
                                                            id="tDescription_Default"
                                                            readonly="readonly" <?php if ($id == "") { ?> onclick="editServiceDescription('Add')" <?php } ?>
                                                            data-originalvalue="<?= $db_data[0]['tDescription_' . $default_lang]; ?>"><?= $db_data[0]['tDescription_' . $default_lang]; ?></textarea>
                                                </div>
                                                <?php if ($id != "") { ?>
                                                    <div class="col-lg-2">
                                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                                data-original-title="Edit"
                                                                onclick="editServiceDescription('Edit')">
                                                            <span class="glyphicon glyphicon-pencil"
                                                                  aria-hidden="true"></span>
                                                        </button>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <div class="modal fade" id="tDescription_Modal" tabindex="-1" role="dialog"
                                                 aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="service_desc_action"></span> Service
                                                                Description
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                        onclick="resetToOriginalValue(this, 'tDescription_')">
                                                                    x
                                                                </button>
                                                            </h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php
                                                            for ($i = 0; $i < $count_all; $i++) {
                                                                $vCode = $db_master[$i]['vCode'];
                                                                $vTitle = $db_master[$i]['vTitle'];
                                                                $eDefault = $db_master[$i]['eDefault'];
                                                                $required = ($eDefault == 'Yes') ? 'required' : '';
                                                                $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                                $tDescription = 'tDescription_' . $vCode;
                                                                $serviceDescValue = "";
                                                                if (isset($userEditDataArr[$tDescription])) {
                                                                    $serviceDescValue = $userEditDataArr[$tDescription];
                                                                }
                                                                ?><?php
                                                                $page_title_class = 'col-lg-12';
                                                                if (count($db_master) > 1) {
                                                                    if ($EN_available) {
                                                                        if ($vCode == "EN") {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    } else {
                                                                        if ($vCode == $default_lang) {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <label>Service Description (<?= $vTitle; ?>
                                                                            ) <?php echo $required_msg; ?></label>
                                                                    </div>
                                                                    <div class="<?= $page_title_class ?>">
                                                                        <textarea class="form-control"
                                                                                  name="<?= $tDescription; ?>"
                                                                                  id="<?= $tDescription; ?>"
                                                                                  placeholder="<?= $vTitle; ?> Value"
                                                                                  data-originalvalue="<?= $serviceDescValue; ?>"><?= $serviceDescValue; ?></textarea>
                                                                        <div class="text-danger"
                                                                             id="<?= $tDescription . '_error'; ?>"
                                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                                    </div>
                                                                    <?php
                                                                    if (count($db_master) > 1) {
                                                                        if ($EN_available) {
                                                                            if ($vCode == "EN") { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button"
                                                                                            name="allLanguage"
                                                                                            id="allLanguage"
                                                                                            class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('tDescription_', 'EN');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        } else {
                                                                            if ($vCode == $default_lang) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button"
                                                                                            name="allLanguage"
                                                                                            id="allLanguage"
                                                                                            class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('tDescription_', '<?= $default_lang ?>');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="modal-footer" style="margin-top: 0">
                                                            <h5 class="text-left"
                                                                style="margin-bottom: 15px; margin-top: 0;">
                                                                <strong><?= $langage_lbl['LBL_NOTE']; ?>
                                                                    : </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?>
                                                            </h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" id="tDescription_btn"
                                                                        style="margin-left: 0 !important"
                                                                        onclick="saveServiceDescription()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok"
                                                                        data-dismiss="modal"
                                                                        onclick="resetToOriginalValue(this, 'tDescription_')">
                                                                    Cancel
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="col-lg-12">
                                                <label>Service Description</label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <textarea class="form-control" name="tDescription_<?= $default_lang ?>"
                                                          id="tDescription_<?= $default_lang ?>"><?= $db_data[0]['tDescription_' . $default_lang]; ?></textarea>
                                            </div>
                                        <?php } ?><?php
                                    }
                                }
                                ?>

                                <?php if ($MODULES_OBJ->isEnableOTPVerificationDeliverAll() && in_array($eCatType, ['Genie', 'Runner', 'Anywhere'])) { ?>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label>Ask OTP/Confirmation code at Products/Items Delivery
                                                <i class="icon-question-sign" data-placement="top" data-toggle="tooltip"
                                                   data-original-title="Ask OTP/Confirmation code at Products/Items Delivery"></i></label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <div class="make-switch" data-on="success" data-off="warning">
                                                <input type="checkbox" id="eOTPCodeEnable"
                                                       name="eOTPCodeEnable" <?= ($id != '' && $eOTPCodeEnable == 'Yes') ? 'checked' : ''; ?>
                                                       value="Yes"/>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php if (($eCatType == 'ServiceProvider' || $action == 'Add') && $sub_action != "sub_category" && $SERVICE_PROVIDER_FLOW == "Provider") {
                                    ?>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label> <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?>
                                                Cancellation Time Limit ( in minute )<span class="red"></span>
                                                <i class="icon-question-sign" data-placement="top" data-toggle="tooltip"
                                                   data-original-title="This is the timelimit based on which the <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?> would be charged if he/she cancel's the ride after the specified period limit."></i></label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <input type="text" class="form-control" name="iCancellationTimeLimit"
                                                   id="iCancellationTimeLimit" value="<?= $iCancellationTimeLimit; ?>"
                                                   onblur="checkblanktimelimit('iCancellationTimeLimit','fCancellationFare');">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label> <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?>
                                                Cancellation Charges (Price In <?= $db_currency[0]['vName'] ?>)<span
                                                        class="red"></span>
                                                <i class="icon-question-sign" data-placement="top" data-toggle="tooltip"
                                                   data-original-title="Below mentioned charges would be applied to the <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?>s when the <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?> cancel's the ride after the specific period of time."></i></label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <input type="text" class="form-control" name="fCancellationFare"
                                                   id="fCancellationFare" value="<?= $fCancellationFare; ?>"
                                                   onfocus="checkcancellationfare('iCancellationTimeLimit');">
                                            <!-- onchange="getpriceCheck_digit(this.value)" -->
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label> Waiting Time Limit ( in minute )<span class="red"></span>
                                                <i class="icon-question-sign" data-placement="top" data-toggle="tooltip"
                                                   data-original-title="Waiting charge will be applied if duration exceeds than the defined.
                                                   e.g.: Let's say that the 'Waiting Time Limit' has set to 5 Minutes. From the app, the '<?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?>' has marked as arrived and if the <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> has to wait for 8 minutes which is more than 5 minutes(Waiting Time Limit) then in that case the <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?> has to pay for the exceeded 3 minutes based on defined 'Waiting Charges' fees."></i></label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <input type="text" class="form-control" name="iWaitingFeeTimeLimit" min="1"
                                                   onkeypress="return isNumberKey(event)" id="iWaitingFeeTimeLimit"
                                                   value="<?= $iWaitingFeeTimeLimit; ?>"
                                                   onblur="checkblanktimelimit('iWaitingFeeTimeLimit','fWaitingFees');">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label> Waiting Charges (Price In <?= $db_currency[0]['vName'] ?>)<span
                                                        class="red"></span>
                                                <i class="icon-question-sign" data-placement="top" data-toggle="tooltip"
                                                   data-original-title="The defined charges would be applied to the invoice into the total fare when the <?php echo strtolower($langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']); ?> has to wait for more than the specific defined waiting time prior to starting the <?php echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']; ?>"></i></label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <input type="text" class="form-control" name="fWaitingFees" min="1"
                                                   onkeypress="return isNumberKey(event)" id="fWaitingFees"
                                                   value="<?= $fWaitingFees; ?>"
                                                   onfocus="checkcancellationfare('iWaitingFeeTimeLimit');">
                                        </div>
                                    </div>
                                    <?php
                                }
                                if (($eCatType == 'ServiceProvider' || $action == 'Add') && $sub_action != "sub_category") {
                                    ?>
                                    <div class="row" id="commisionperdiv">
                                        <div class="col-lg-12">
                                            <label>Commission Percentage on Waiting/Cancellation/Material - Charges
                                                <i class="icon-question-sign" data-placement="top" data-toggle="tooltip"
                                                   data-original-title="Enter the commission percentage for the waiting charge/ Cancellation charge/ Material charge."></i></label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <input class="form-control" min="1" onkeypress="return isNumberKey(event)"
                                                   type="text" name="fCommision" id="fCommision" required=""
                                                   value="<?= $fCommision; ?>"
                                                   onkeyup="this.value = minmax(this.value, 0, 100)"
                                                   placeholder="Commission Percentage On Material/Misc Fee">
                                        </div>
                                    </div>
                                <?php }
                                if ($sub_action != "sub_category" && ($APP_TYPE == 'Ride-Delivery-UberX' || $APP_TYPE == "UberX" || $THEME_OBJ->isProThemeActive() == "Yes")) { ?>
                                    <div class="row" id="CatViewType">
                                        <div class="col-lg-12">
                                            <label>Category View Type</label>
                                        </div>
                                        <?php if ($MODULES_OBJ->isEnableAppHomePageListView() && $sub_action != "sub_category") { ?>
                                            <div class="col-md-6 col-sm-6 ">
                                                <input id="iconcatview" name="eCatViewType[]" type="checkbox"
                                                       value="Icon" <?php
                                                if (in_array("Icon", $eCatViewType)) {
                                                    echo 'checked';
                                                }
                                                ?>>
                                                <label for="iconcatview">Icon</label>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input id="bannercatview" name="eCatViewType[]" type="checkbox"
                                                       value="Banner" <?php
                                                if (in_array("Banner", $eCatViewType)) {
                                                    echo 'checked';
                                                }
                                                ?>>
                                                <label for="bannercatview">Banner</label>
                                                <input id="listcatview" name="eCatViewType[]" type="checkbox"
                                                       value="List" <?php
                                                if (in_array("List", $eCatViewType)) {
                                                    echo 'checked';
                                                }
                                                ?>>
                                                <label for="listcatview">List</label>
                                            </div>
                                        <?php } elseif ($THEME_OBJ->isDeliveryKingXv2ThemeActive() == "Yes") { ?>
                                            <div class="col-md-6 col-sm-6 ">
                                                <input id="iconcatview" name="eCatViewType[]" type="radio"
                                                       value="Icon" <?php
                                                if (in_array("Icon", $eCatViewType)) {
                                                    echo 'checked';
                                                }
                                                ?> onchange="configCatHomePageImage();">
                                                <label for="iconcatview" style="cursor: pointer; margin-right: 30px">Icon</label>

                                                <input id="listcatview" name="eCatViewType[]" type="radio"
                                                       value="List" <?php
                                                if (in_array("List", $eCatViewType)) {
                                                    echo 'checked';
                                                }
                                                ?> onchange="configCatHomePageImage();">
                                                <label for="listcatview" style="cursor: pointer;">List</label>
                                            </div>
                                        <?php } else { ?>
                                            <div class="col-md-6 col-sm-6 ">
                                                <input checked="checked" id="r4" name="eShowType" type="radio"
                                                       value="Icon" <?php
                                                if ($eShowType == 'Icon') {
                                                    echo 'checked';
                                                }
                                                ?>>
                                                <label for="r4">Icon</label>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input id="r5" name="eShowType" type="radio" value="Banner" <?php
                                                if ($eShowType == 'Banner') {
                                                    echo 'checked';
                                                }
                                                ?>>
                                                <label for="r5">Banner</label>
                                                <input id="r6" name="eShowType" type="radio" value="Icon-Banner" <?php
                                                if ($eShowType == 'Icon-Banner') {
                                                    echo 'checked';
                                                }
                                                ?>>
                                                <label for="r6">Icon-Banner</label>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <?php if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV2() && !$MODULES_OBJ->isEnableAppHomeScreenLayoutV3()) { ?>
                                        <div class="row Icon imagebox">
                                            <div class="col-lg-12">
                                                <label>Logo</label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <? if (isset($vListLogo2) && $vListLogo2 != '') { ?>
                                                    <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=200&h=200&src=' . $tconfig['tsite_upload_images_vehicle_category'] . "/" . $id . "/" . $vListLogo2; ?>"
                                                         class="logo-preview-img">
                                                <? } else { ?>
                                                    <img src="../assets/img/placeholder-img.png"
                                                         class="logo-preview-img"/>
                                                <?php } ?>
                                                <input type="file" class="form-control"
                                                       name="vListLogo2" <?php echo $required_rule; ?> id="vListLogo2"
                                                       placeholder="" style="padding-bottom: 39px;">
                                                <br/>
                                                Note: Upload only png image size of 512px X 512px.
                                            </div>
                                        </div>
                                    <?php } if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV3()) {
                                        if($parent_ufx_catid == 0) { ?>
                                        
                                        <div class="row Icon imagebox">
                                            <div class="col-lg-12">
                                                <label>Icon</label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <? if (isset($vListLogo3) && $vListLogo3 != '') { ?>
                                                    <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=200&src=' . $tconfig['tsite_upload_images_vehicle_category'] . "/" . $id . "/" . $vListLogo3; ?>"
                                                         class="logo-preview-img">
                                                <? } else { ?>
                                                    <img src="../assets/img/placeholder-img.png"
                                                         class="logo-preview-img"/>
                                                <?php } ?>
                                                <input type="file" class="form-control"
                                                       name="vListLogo3" <?php echo $required_rule; ?> id="vListLogo3"
                                                       placeholder="">
                                                <br/>
                                                <?php if(strtoupper($APP_TYPE) == "RIDE" || strtoupper($APP_TYPE) == "RIDE-DELIVERY" || strtoupper($THEME_OBJ->isPXCProThemeActive()) == "YES") { ?>
                                                [Note: Recommended dimension for banner image(.png) is <?= $vIconDetails ?>.]
                                                <?php } else { ?>
                                                [Note: Recommended dimension for banner image(.png) is 512px X 512px.]
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } } else { ?>
                                        <div class="row Icon imagebox">
                                            <div class="col-lg-12">
                                                <label>Logo</label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <? if (isset($vLogo) && $vLogo != '') { ?>
                                                    <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=200&h=200&src=' . $tconfig['tsite_upload_images_vehicle_category'] . "/" . $id . "/ios/3x_" . $vLogo; ?>"
                                                         class="logo-preview-img">
                                                <? } else { ?>
                                                    <img src="../assets/img/placeholder-img.png"
                                                         class="logo-preview-img"/>
                                                <?php } ?>
                                                <input type="file" class="form-control"
                                                       name="vLogo" <?php echo $required_rule; ?> id="vLogo"
                                                       placeholder="" style="padding-bottom: 39px;">
                                                <br/>
                                                Note: Upload only png image size of 512px X 512px.
                                            </div>
                                        </div>
                                    <?php } ?><?php
                                    $displayBannerBottonText = 0;
                                    if ($sub_action == "sub_category" && $APP_TYPE == 'Ride-Delivery-UberX' && $db_data1[0]['eCatType'] == 'MoreDelivery' && $db_data1[0]['eFor'] == "DeliverAllCategory") {
                                        $displayBannerBottonText = 1;
                                    }
                                    if (($eShowType != "Icon" || $action == 'Add' || $eCatType != 'ServiceProvider') && $sub_action != "sub_category" && $APP_TYPE == 'Ride-Delivery-UberX') {
                                        $displayBannerBottonText = 1;
                                    }
                                    ?>
                                    <div <?= $display ?>>
                                        <?php if (count($db_master) > 1) { ?>
                                            <div class="row bannerbutton"
                                                 id="bannerbutton" <?= ($displayBannerBottonText == 1) ? 'style="display: block"' : 'style="display: none"' ?>>
                                                <div class="col-lg-12">
                                                    <label>Banner Button Text</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <input type="text"
                                                           class="form-control <?= ($id == "") ? 'readonly-custom' : '' ?>"
                                                           id="tBannerButtonText_Default"
                                                           value="<?= $userEditDataArr['tBannerButtonText_' . $default_lang]; ?>"
                                                           data-originalvalue="<?= $userEditDataArr['tBannerButtonText_' . $default_lang]; ?>"
                                                           readonly="readonly" <?php if ($id == "") { ?> onclick="editBannerButtonText('Add')" <?php } ?>>
                                                </div>
                                                <?php if ($id != "") { ?>
                                                    <div class="col-lg-2">
                                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                                data-original-title="Edit"
                                                                onclick="editBannerButtonText('Edit')">
                                                            <span class="glyphicon glyphicon-pencil"
                                                                  aria-hidden="true"></span>
                                                        </button>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <div class="modal fade" id="tBannerButtonText_Modal" tabindex="-1"
                                                 role="dialog" aria-hidden="true" data-backdrop="static"
                                                 data-keyboard="false">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="banner_button_action"></span> Banner Button
                                                                Text
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                        onclick="resetToOriginalValue(this, 'tBannerButtonText_')">
                                                                    x
                                                                </button>
                                                            </h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php
                                                            for ($i = 0; $i < $count_all; $i++) {
                                                                $vCode = $db_master[$i]['vCode'];
                                                                $vTitle = $db_master[$i]['vTitle'];
                                                                $eDefault = $db_master[$i]['eDefault'];
                                                                $required = ($eDefault == 'Yes') ? 'required' : '';
                                                                $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                                $tBannerButtonText = 'tBannerButtonText_' . $vCode;
                                                                $tBannerButtonTextdefault = 'tBannerButtonText_' . $default_lang;
                                                                ?><?php
                                                                $page_title_class = 'col-lg-12';
                                                                if (count($db_master) > 1) {
                                                                    if ($EN_available) {
                                                                        if ($vCode == "EN") {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    } else {
                                                                        if ($vCode == $default_lang) {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <label>Banner Button Text (<?= $vTitle; ?>
                                                                            ) <?php echo $required_msg; ?></label>
                                                                    </div>
                                                                    <div class="<?= $page_title_class ?>">
                                                                        <input type="text" class="form-control"
                                                                               name="<?= $tBannerButtonText; ?>"
                                                                               id="<?= $tBannerButtonText; ?>"
                                                                               value="<?= $userEditDataArr[$tBannerButtonText]; ?>"
                                                                               data-originalvalue="<?= $userEditDataArr[$tBannerButtonText]; ?>"
                                                                               placeholder="<?= $vTitle; ?> Value">
                                                                        <div class="text-danger"
                                                                             id="<?= $tBannerButtonText . '_error'; ?>"
                                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                                    </div>
                                                                    <?php
                                                                    if (count($db_master) > 1) {
                                                                        if ($EN_available) {
                                                                            if ($vCode == "EN") { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button"
                                                                                            name="allLanguage"
                                                                                            id="allLanguage"
                                                                                            class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('tBannerButtonText_', 'EN');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        } else {
                                                                            if ($vCode == $default_lang) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button"
                                                                                            name="allLanguage"
                                                                                            id="allLanguage"
                                                                                            class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('tBannerButtonText_', '<?= $default_lang ?>');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="modal-footer" style="margin-top: 0">
                                                            <h5 class="text-left"
                                                                style="margin-bottom: 15px; margin-top: 0;">
                                                                <strong><?= $langage_lbl['LBL_NOTE']; ?>
                                                                    : </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?>
                                                            </h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save"
                                                                        id="tBannerButtonText_btn"
                                                                        style="margin-left: 0 !important"
                                                                        onclick="saveBannerButtonText()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok"
                                                                        data-dismiss="modal"
                                                                        onclick="resetToOriginalValue(this, 'tBannerButtonText_')">
                                                                    Cancel
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Banner Button Text</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <input type="text" class="form-control"
                                                           name="tBannerButtonText_<?= $default_lang ?>"
                                                           id="tBannerButtonText_<?= $default_lang ?>"
                                                           value="<?= $userEditDataArr['tBannerButtonText_' . $default_lang]; ?>">
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <?php if($THEME_OBJ->isPXCProThemeActive() == "Yes" && $eServiceType != "Ride") { ?>
                                    <div class="row">
                                        <?php if (count($db_master) > 0) { ?>
                                        <div class="col-lg-12">
                                            <label>Select Language for Banner</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <select class="form-control" name="banner_lang" id="banner_lang">
                                                <?php foreach ($db_master as $db_lang) { ?>
                                                    <option value="<?= $db_lang['vCode'] ?>" <?= $banner_lang == $db_lang['vCode'] ? 'selected' : '' ?>><?= $db_lang['vTitle_EN'] . ' (' . $db_lang['vCode'] . ')' ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>  
                                        <?php } else { ?>
                                            <input type="hidden" name="banner_lang" value="<?= $default_lang ?>">
                                        <?php } ?>
                                    </div>
                                    <div class="row Banner imagebox">
                                        <div class="col-lg-12">
                                            <label>Banner</label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <? if ($vBannerImage != '') { ?>
                                                <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=400&src=' . $tconfig['tsite_upload_images_vehicle_category'] . "/" . $id . "/" . $vBannerImage; ?>"
                                                     style="margin-bottom: 10px;">
                                            <? } ?>
                                            <input type="file" class="form-control"
                                                   name="vBannerImage" <?php echo $required_rule; ?> id="vBannerImage"
                                                   placeholder="">
                                            <br/>
                                            <?php if($eServiceType == "DeliverAll" && $iServiceIdEdit > 1) { ?>
                                            Note: Recommended dimension for banner image is 1650px X 1077px.
                                            <?php } else { ?>
                                            Note: Recommended dimension for banner image is 3350px X 990px.
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    
                                    <?php if (in_array($eCatType, ['Genie', 'Runner', 'Anywhere'])) { 
                                            if ($eCatType == "Runner" && ($THEME_OBJ->isCubeJekXv3ThemeActive() == "Yes" || $THEME_OBJ->isCubeJekXv3ProThemeActive() == "No") && $THEME_OBJ->isProDeliveryKingThemeActive() == "No") { ?>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Banner (App Home Screen)</label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <div class="manage-banner-section">
                                                    <?php if (count($appBannerData) > 0) { ?>
                                                        <div class="banner-img-block">
                                                            <?php foreach ($appBannerData as $app_banner_img) { ?>
                                                                <div class="banner-img">
                                                                    <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=400&src=' . $tconfig['tsite_upload_images'] . $app_banner_img['vImage']; ?>">
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    <?php } else { ?>
                                                        <div class="no-banner">
                                                            No Banner Found.
                                                        </div>
                                                    <?php } ?>
                                                    <a href="<?= $tconfig['tsite_admin_url'] . 'app_banner.php?eBuyAnyService=' . $eCatType ?>&eFor=AppHomeScreen"
                                                       class="manage-banner-btn" target="_blank">Manage Banner for App
                                                        Home Screen</a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <?php if ( $THEME_OBJ->isCubeJekXv3ProThemeActive() == "No" ) { ?>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Rotating Banners (Inner Page)</label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <div class="manage-banner-section">
                                                    <?php if (count($bannerBuyAnyServiceData) > 0) { ?>
                                                        <div class="banner-img-block">
                                                            <?php foreach ($bannerBuyAnyServiceData as $banner_img) { ?>
                                                                <div class="banner-img">
                                                                    <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=400&src=' . $tconfig['tsite_upload_images'] . $banner_img['vImage']; ?>">
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    <?php } else { ?>
                                                        <div class="no-banner">
                                                            No Banners Found.
                                                        </div>
                                                    <?php } ?>
                                                    <a href="<?= $tconfig['tsite_admin_url'] . 'banner.php?eBuyAnyService=' . $eCatType ?>"
                                                       class="manage-banner-btn" target="_blank">Manage Banners for this
                                                        service</a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <?php if ($THEME_OBJ->isCubeJekXv3ThemeActive() == "Yes") { ?>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Package Types</label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <div class="manage-banner-section">
                                                    <?php if (count($PackageTypeBuyAnyServiceData) > 0) { ?>
                                                        <div class="banner-img-block">
                                                            <?php foreach ($PackageTypeBuyAnyServiceData as $package_img) { ?>
                                                                <div class="banner-img" style="width: 100px;">
                                                                    <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=100&src=' . $tconfig['tsite_upload_genie_package_type_images'] . $package_img['vImage']; ?>">
                                                                    <div style="margin-top: 5px"><?= $package_img['tTitle'] ?></div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    <?php } else { ?>
                                                        <div class="no-banner">
                                                            No Package Types found.
                                                        </div>
                                                    <?php } ?>
                                                    <a href="<?= $tconfig['tsite_admin_url'] . 'genie_package_type.php?eBuyAnyService=' . $eCatType ?>"
                                                       class="manage-banner-btn" target="_blank">Manage Package
                                                        Types for this service</a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?><?php } ?>
                                    
                                    <?php
                                    if (!$MODULES_OBJ->isEnableAppHomeScreenLayoutV2() || $THEME_OBJ->isDeliveryKingXv2ThemeActive() == "Yes" || $THEME_OBJ->isProDeliveryKingThemeActive() == "Yes" || ($MODULES_OBJ->isEnableMedicalServices() && $MoreServicesMS == "Yes")) {
                                        if (($MODULES_OBJ->isEnableAppHomePageListView() && $sub_action != "sub_category") || $THEME_OBJ->isDeliveryKingXv2ThemeActive() == "Yes" || $THEME_OBJ->isProDeliveryKingThemeActive() == "Yes" || ($MODULES_OBJ->isEnableMedicalServices() && $MoreServicesMS == "Yes")) { ?><?php if (count($db_master) > 1) { ?>
                                            <div class="row tListDescription" id="tListDescription">
                                                <div class="col-lg-12">
                                                    <label>List Description</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea
                                                            class="form-control <?= ($id == "") ? 'readonly-custom' : '' ?>"
                                                            id="tListDescription_Default"
                                                            readonly="readonly" <?php if ($id == "") { ?> onclick="editListDescription('Add')" <?php } ?>
                                                            data-originalvalue="<?= $tListDescriptionArr['tListDescription_' . $default_lang]; ?>"><?= $tListDescriptionArr['tListDescription_' . $default_lang]; ?></textarea>
                                                </div>
                                                <?php if ($id != "") { ?>
                                                    <div class="col-lg-2">
                                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                                data-original-title="Edit"
                                                                onclick="editListDescription('Edit')">
                                                            <span class="glyphicon glyphicon-pencil"
                                                                  aria-hidden="true"></span>
                                                        </button>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <div class="modal fade" id="tListDescription_Modal" tabindex="-1"
                                                 role="dialog" aria-hidden="true" data-backdrop="static"
                                                 data-keyboard="false">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="list_desc_action"></span> List Description
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                        onclick="resetToOriginalValue(this, 'tListDescription_')">
                                                                    x
                                                                </button>
                                                            </h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php
                                                            for ($i = 0; $i < $count_all; $i++) {
                                                                $vCode = $db_master[$i]['vCode'];
                                                                $vTitle = $db_master[$i]['vTitle'];
                                                                $eDefault = $db_master[$i]['eDefault'];
                                                                $required = ($eDefault == 'Yes') ? 'required' : '';
                                                                $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                                $tListDescription = 'tListDescription_' . $vCode;
                                                                $tListDescriptiondefault = 'tListDescription_' . $default_lang;
                                                                ?><?php
                                                                $page_title_class = 'col-lg-12';
                                                                if (count($db_master) > 1) {
                                                                    if ($EN_available) {
                                                                        if ($vCode == "EN") {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    } else {
                                                                        if ($vCode == $default_lang) {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <label>List Description (<?= $vTitle; ?>
                                                                            ) <?php echo $required_msg; ?></label>
                                                                    </div>
                                                                    <div class="<?= $page_title_class ?> desc-block">
                                                                        <textarea class="form-control"
                                                                                  name="<?= $tListDescription; ?>"
                                                                                  id="<?= $tListDescription; ?>"
                                                                                  placeholder="<?= $vTitle; ?> Value"
                                                                                  data-originalvalue="<?= $tListDescriptionArr[$tListDescription]; ?>"><?= $tListDescriptionArr[$tListDescription]; ?></textarea>
                                                                        <div class="desc_counter pull-right"
                                                                             style="margin-top: 5px">250/250
                                                                        </div>
                                                                        <div class="text-danger"
                                                                             id="<?= $tListDescription . '_error'; ?>"
                                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                                    </div>
                                                                    <?php
                                                                    if (count($db_master) > 1) {
                                                                        if ($EN_available) {
                                                                            if ($vCode == "EN") { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button"
                                                                                            name="allLanguage"
                                                                                            id="allLanguage"
                                                                                            class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('tListDescription_', 'EN');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        } else {
                                                                            if ($vCode == $default_lang) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button"
                                                                                            name="allLanguage"
                                                                                            id="allLanguage"
                                                                                            class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('tListDescription_', '<?= $default_lang ?>');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="modal-footer" style="margin-top: 0">
                                                            <h5 class="text-left"
                                                                style="margin-bottom: 15px; margin-top: 0;">
                                                                <strong><?= $langage_lbl['LBL_NOTE']; ?>
                                                                    : </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?>
                                                            </h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save"
                                                                        id="tListDescription_btn"
                                                                        style="margin-left: 0 !important"
                                                                        onclick="saveListDescription()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok"
                                                                        data-dismiss="modal"
                                                                        onclick="resetToOriginalValue(this, 'tListDescription_')">
                                                                    Cancel
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="row tListDescription" id="tListDescription">
                                                <div class="col-lg-12">
                                                    <label>List Description</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control"
                                                              name="tListDescription_<?= $default_lang ?>"
                                                              id="tListDescription_<?= $default_lang ?>"><?= $tListDescriptionArr['tListDescription_' . $default_lang]; ?></textarea>
                                                </div>
                                            </div>
                                        <?php } ?><?php
                                            if (!$MODULES_OBJ->isEnableAppHomeScreenLayoutV2()) {
                                                if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV1()) { ?>
                                                    <div class="row List imagebox">
                                                        <div class="col-lg-12">
                                                            <label>List - Logo</label>
                                                        </div>
                                                        <div class="col-md-6 col-sm-6">
                                                            <? if (isset($vListLogo1) && $vListLogo1 != '') { ?>
                                                                <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=400&src=' . $tconfig['tsite_upload_images_vehicle_category'] . "/" . $id . "/" . $vListLogo1; ?>"
                                                                     style="width:200px;">
                                                            <? } else { ?>
                                                                <img src="../assets/img/placeholder-img.png"
                                                                     class="logo-preview-img"/>
                                                            <?php } ?>
                                                            <input type="file" class="form-control"
                                                                   name="vListLogo1" <?php echo $required_rule; ?>
                                                                   id="vListLogo1" placeholder=""
                                                                   style="padding-bottom: 39px;">
                                                            <br/>
                                                            Note: Recommended dimension for banner image is 2880px X 1620px.
                                                        </div>
                                                    </div>
                                                <?php } else { ?>
                                                    <div class="row List imagebox">
                                                        <div class="col-lg-12">
                                                            <label>List - Logo</label>
                                                        </div>
                                                        <div class="col-md-6 col-sm-6">
                                                            <? if (isset($vListLogo) && $vListLogo != '') { ?>
                                                                <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=400&src=' . $tconfig['tsite_upload_images_vehicle_category'] . "/" . $id . "/" . $vListLogo; ?>"
                                                                     style="width:200px;">
                                                            <? } else { ?>
                                                                <img src="../assets/img/placeholder-img.png"
                                                                     class="logo-preview-img"/>
                                                            <?php } ?>
                                                            <input type="file" class="form-control"
                                                                   name="vListLogo" <?php echo $required_rule; ?>
                                                                   id="vListLogo" placeholder=""
                                                                   style="padding-bottom: 39px;">
                                                            <br/>
                                                            Note: Upload only png image size of 512px X 512px.
                                                        </div>
                                                    </div>
                                                <?php } ?><?php }
                                        }
                                    }
                                } else {
                                    if ($db_data1[0]['eCatType'] != 'MoreDelivery' || $THEME_OBJ->isPXCProThemeActive() == "Yes") { ?>

                                        <?php if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV3()) { ?>
                                            <div class="row Icon imagebox">
                                                <div class="col-lg-12" <?= $stylehide ?>>
                                                    <label>Logo</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6" <?= $stylehide ?>>
                                                    <? if ($vLogo2 != '') { ?>
                                                        <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=200&h=200&src=' . $tconfig['tsite_upload_images_vehicle_category'] . "/" . $id . "/ios/3x_" . $vLogo2; ?>"
                                                             style="width:100px;height:100px;">
                                                    <? } ?>
                                                    <input type="file" class="form-control" name="vLogo2" <?php echo $required_rule; ?> id="vLogo2" placeholder="" style="padding-bottom: 39px;">
                                                    <br/>
                                                    Note: Upload only png image size of 512px X 512px.
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="row Icon imagebox">
                                                <div class="col-lg-12" <?= $stylehide ?>>
                                                    <label>Logo</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6" <?= $stylehide ?>>
                                                    <? if ($vLogo != '') { ?>
                                                        <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=200&h=200&src=' . $tconfig['tsite_upload_images_vehicle_category'] . "/" . $id . "/ios/3x_" . $vLogo; ?>"
                                                             style="width:100px;height:100px;">
                                                    <? } ?>
                                                    <input type="file" class="form-control"
                                                           name="vLogo" <?php echo $required_rule; ?> id="vLogo"
                                                           placeholder="" style="padding-bottom: 39px;">
                                                    <br/>
                                                    Note: Upload only png image size of 512px X 512px.
                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php } else if (($db_data1[0]['eCatType'] == 'MoreDelivery' && $db_data1[0]['eFor'] == "DeliverAllCategory") || strtoupper($APP_TYPE) == "RIDE-DELIVERY") { ?>
                                        <?php if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV2()) { ?>
                                            <div class="row Icon imagebox">
                                                <div class="col-lg-12">
                                                    <label>Logo</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <? if (isset($vListLogo2) && $vListLogo2 != '') { ?>
                                                        <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=200&h=200&src=' . $tconfig['tsite_upload_images_vehicle_category'] . "/" . $id . "/" . $vListLogo2; ?>"
                                                             class="logo-preview-img">
                                                    <? } else { ?>
                                                        <img src="../assets/img/placeholder-img.png"
                                                             class="logo-preview-img"/>
                                                    <?php } ?>
                                                    <input type="file" class="form-control"
                                                           name="vListLogo2" <?php echo $required_rule; ?> id="vListLogo2"
                                                           placeholder="" style="padding-bottom: 39px;">
                                                    <br/>
                                                    Note: Upload only png image size of 512px X 512px.
                                                </div>
                                            </div>
                                        <?php } if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV3() && strtoupper($APP_TYPE) != "RIDE-DELIVERY") { ?>
                                            <div class="row Icon imagebox">
                                                <div class="col-lg-12">
                                                    <label>Logo</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <? if (isset($vListLogo3) && $vListLogo3 != '') { ?>
                                                        <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=200&h=200&src=' . $tconfig['tsite_upload_images_vehicle_category'] . "/" . $id . "/" . $vListLogo3; ?>"
                                                             class="logo-preview-img">
                                                    <? } else { ?>
                                                        <img src="../assets/img/placeholder-img.png"
                                                             class="logo-preview-img"/>
                                                    <?php } ?>
                                                    <input type="file" class="form-control"
                                                           name="vListLogo3" <?php echo $required_rule; ?> id="vListLogo3"
                                                           placeholder="" style="padding-bottom: 39px;">
                                                    <br/>
                                                    Note: Upload only png image size of 512px X 512px.
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <?php if(strtoupper($APP_TYPE) == "RIDE-DELIVERY") { ?>
                                            <div class="row imagebox">
                                                <div class="col-lg-12">
                                                    <label>Icon</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6" <?= $stylehide ?>>
                                                    <? if ($vLogo2 != '') { ?>
                                                        <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=200&src=' . $tconfig['tsite_upload_images_vehicle_category'] . "/" . $id . "/ios/" . $vLogo2; ?>" style="margin-bottom: 10px;">
                                                    <? } ?>
                                                    <input type="file" class="form-control" name="vLogo2" <?php echo $required_rule; ?> id="vLogo2" placeholder="">
                                                    <br/>
                                                    Note: Recommended dimension for icon is <?= $vIconDetails ?>.
                                                </div>
                                            </div>
                                            <div class="row">
                                                <?php if (count($db_master) > 0) { ?>
                                                <div class="col-lg-12">
                                                    <label>Select Language for Banner</label>
                                                </div>
                                                <div class="col-lg-6">
                                                    <select class="form-control" name="banner_lang" id="banner_lang">
                                                        <?php foreach ($db_master as $db_lang) { ?>
                                                            <option value="<?= $db_lang['vCode'] ?>" <?= $banner_lang == $db_lang['vCode'] ? 'selected' : '' ?>><?= $db_lang['vTitle_EN'] . ' (' . $db_lang['vCode'] . ')' ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>  
                                                <?php } else { ?>
                                                    <input type="hidden" name="banner_lang" value="<?= $default_lang ?>">
                                                <?php } ?>
                                            </div>
                                            <?php } ?>

                                            <input type="hidden" name="eShowType" value="Banner">
                                            <div class="row Banner imagebox">
                                                <div class="col-lg-12">
                                                    <label>Banner</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <? if ($vBannerImage != '') { ?>
                                                        <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=400&&src=' . $tconfig['tsite_upload_images_vehicle_category'] . "/" . $id . "/" . $vBannerImage; ?>"
                                                             style="margin-bottom: 10px;">
                                                    <? } ?>
                                                    <input type="file" class="form-control"
                                                           name="vBannerImage" <?php echo $required_rule; ?>
                                                           id="vBannerImage" placeholder="">
                                                    <br/>
                                                    Note: Recommended dimension for banner image is 3350px X 990px.
                                                </div>
                                            </div>
                                        <? }
                                    }
                                }
                                ?>
                                <input type="hidden" name="iServiceIdEdit" value="<?= $iServiceIdEdit; ?>">
                                <?php
                                if ($sub_action == "sub_category" && ($eCatType == 'ServiceProvider' || $action == 'Add')) {
                                    ?>
                                    <?php if (count($db_master) > 1 && $MoreServicesMS == "Yes") { ?>
                                        <div class="row tListDescription" id="tListDescription">
                                            <div class="col-lg-12">
                                                <label>List Description</label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <textarea
                                                        class="form-control <?= ($id == "") ? 'readonly-custom' : '' ?>"
                                                        id="tListDescription_Default"
                                                        readonly="readonly" <?php if ($id == "") { ?> onclick="editListDescription('Add')" <?php } ?>
                                                        data-originalvalue="<?= $tListDescriptionArr['tListDescription_' . $default_lang]; ?>"><?= $tListDescriptionArr['tListDescription_' . $default_lang]; ?></textarea>
                                            </div>
                                            <?php if ($id != "") { ?>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                            data-original-title="Edit"
                                                            onclick="editListDescription('Edit')">
                                                        <span class="glyphicon glyphicon-pencil"
                                                              aria-hidden="true"></span>
                                                    </button>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="modal fade" id="tListDescription_Modal" tabindex="-1" role="dialog"
                                             aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content nimot-class">
                                                    <div class="modal-header">
                                                        <h4>
                                                            <span id="list_desc_action"></span> List Description
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                    onclick="resetToOriginalValue(this, 'tListDescription_')">
                                                                x
                                                            </button>
                                                        </h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <?php
                                                        for ($i = 0; $i < $count_all; $i++) {
                                                            $vCode = $db_master[$i]['vCode'];
                                                            $vTitle = $db_master[$i]['vTitle'];
                                                            $eDefault = $db_master[$i]['eDefault'];
                                                            $required = ($eDefault == 'Yes') ? 'required' : '';
                                                            $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                            $tListDescription = 'tListDescription_' . $vCode;
                                                            $tListDescriptiondefault = 'tListDescription_' . $default_lang;
                                                            ?><?php
                                                            $page_title_class = 'col-lg-12';
                                                            if (count($db_master) > 1) {
                                                                if ($EN_available) {
                                                                    if ($vCode == "EN") {
                                                                        $page_title_class = 'col-md-9 col-sm-9';
                                                                    }
                                                                } else {
                                                                    if ($vCode == $default_lang) {
                                                                        $page_title_class = 'col-md-9 col-sm-9';
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <label>List Description (<?= $vTitle; ?>
                                                                        ) <?php echo $required_msg; ?></label>
                                                                </div>
                                                                <div class="<?= $page_title_class ?> desc-block">
                                                                    <textarea class="form-control"
                                                                              name="<?= $tListDescription; ?>"
                                                                              id="<?= $tListDescription; ?>"
                                                                              placeholder="<?= $vTitle; ?> Value"
                                                                              data-originalvalue="<?= $tListDescriptionArr[$tListDescription]; ?>"><?= $tListDescriptionArr[$tListDescription]; ?></textarea>
                                                                    <div class="desc_counter pull-right"
                                                                         style="margin-top: 5px">250/250
                                                                    </div>
                                                                    <div class="text-danger"
                                                                         id="<?= $tListDescription . '_error'; ?>"
                                                                         style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                                </div>
                                                                <?php
                                                                if (count($db_master) > 1) {
                                                                    if ($EN_available) {
                                                                        if ($vCode == "EN") { ?>
                                                                            <div class="col-md-3 col-sm-3">
                                                                                <button type="button" name="allLanguage"
                                                                                        id="allLanguage"
                                                                                        class="btn btn-primary"
                                                                                        onClick="getAllLanguageCode('tListDescription_', 'EN');">
                                                                                    Convert To All Language
                                                                                </button>
                                                                            </div>
                                                                        <?php }
                                                                    } else {
                                                                        if ($vCode == $default_lang) { ?>
                                                                            <div class="col-md-3 col-sm-3">
                                                                                <button type="button" name="allLanguage"
                                                                                        id="allLanguage"
                                                                                        class="btn btn-primary"
                                                                                        onClick="getAllLanguageCode('tListDescription_', '<?= $default_lang ?>');">
                                                                                    Convert To All Language
                                                                                </button>
                                                                            </div>
                                                                        <?php }
                                                                    }
                                                                }
                                                                ?>
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="modal-footer" style="margin-top: 0">
                                                        <h5 class="text-left"
                                                            style="margin-bottom: 15px; margin-top: 0;">
                                                            <strong><?= $langage_lbl['LBL_NOTE']; ?>
                                                                : </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?>
                                                        </h5>
                                                        <div class="nimot-class-but" style="margin-bottom: 0">
                                                            <button type="button" class="save" id="tListDescription_btn"
                                                                    style="margin-left: 0 !important"
                                                                    onclick="saveListDescription()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                            <button type="button" class="btn btn-danger btn-ok"
                                                                    data-dismiss="modal"
                                                                    onclick="resetToOriginalValue(this, 'tListDescription_')">
                                                                Cancel
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div style="clear:both;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } else {
                                        if ($MoreServicesMS == "Yes") { ?>
                                            <div class="row tListDescription" id="tListDescription">
                                                <div class="col-lg-12">
                                                    <label>List Description</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control"
                                                              name="tListDescription_<?= $default_lang ?>"
                                                              id="tListDescription_<?= $default_lang ?>"><?= $tListDescriptionArr['tListDescription_' . $default_lang]; ?></textarea>
                                                </div>
                                            </div>
                                        <?php }
                                    } ?>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label>Allow photo Upload before Job Starts
                                                <i class="icon-question-sign" data-placement="top" data-toggle="tooltip"
                                                   data-original-title="<?php echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']; ?> can upload the photo, how it looks before service"
                                                   ."></i>
                                            </label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <select class="form-control" name='eBeforeUpload' id='eBeforeUpload'
                                                    required>
                                                <option value="No"
                                                        <? if ('No' == $db_data[0]['eBeforeUpload']) { ?>selected<? } ?>>
                                                    No
                                                </option>
                                                <option value="Yes"
                                                        <? if ('Yes' == $db_data[0]['eBeforeUpload']) { ?>selected<? } ?>>
                                                    Yes
                                                </option>
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label>Allow photo Upload after Job Completes
                                                <i class="icon-question-sign" data-placement="top" data-toggle="tooltip"
                                                   data-original-title="<?php echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']; ?> can upload the photo, how it looks after service."></i></label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <select class="form-control" name='eAfterUpload' id='eAfterUpload' required>
                                                <option value="No"
                                                        <? if ('No' == $db_data[0]['eAfterUpload']) { ?>selected<? } ?>>
                                                    No
                                                </option>
                                                <option value="Yes"
                                                        <? if ('Yes' == $db_data[0]['eAfterUpload']) { ?>selected<? } ?>>
                                                    Yes
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <?php if ($MODULES_OBJ->isEnableVideoConsultingService('Yes')) { ?>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Enable Video Consultation for this service
                                                    <i class="icon-question-sign" data-placement="top"
                                                       data-toggle="tooltip"
                                                       data-original-title="Enable Video Consultation for this service"></i></label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <div class="make-switch" data-on="success" data-off="warning">
                                                    <input type="checkbox" id="eVideoConsultEnable"
                                                           name="eVideoConsultEnable" <?= ($id != '' && $eVideoConsultEnable == 'Yes') ? 'checked' : ''; ?>
                                                           value="Yes"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="vc_charge" <?php if ($eVideoConsultEnable == 'No') { ?> style="display: none;" <?php } ?>>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Video Consultation Service Charge<span class="red"> *</span></label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <input type="text" class="form-control"
                                                           name="eVideoConsultServiceCharge"
                                                           id="eVideoConsultServiceCharge"
                                                           value="<?= $eVideoConsultServiceCharge ?>">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Video Consultation Commission (%) <span class="red"> *</span>
                                                        <i class="icon-question-sign" data-placement="top"
                                                           data-toggle="tooltip"
                                                           data-original-title="Enter Commission percentage you want to earn from this service."></i></label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <input type="text" class="form-control"
                                                           name="fCommissionVideoConsult" id="fCommissionVideoConsult"
                                                           value="<?= $fCommissionVideoConsult ?>">
                                                </div>
                                            </div>
                                        </div>
                                    <?php }
                                }
                                ?>
                                <?php
                                if ($iServiceIdEdit > 0 && $MODULES_OBJ->isEnableTermsServiceCategories()) {
                                    ?>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label>Enable Age Verification Feature<span class="red"> *</span></label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <select class="form-control" name="eShowTerms" id="eShowTerms"
                                                    required="required">
                                                <option value="Yes" <?= ($eShowTerms == 'Yes') ? "selected" : "" ?>>
                                                    Yes
                                                </option>
                                                <option value="No" <?= ($eShowTerms == 'No') ? "selected" : "" ?>>No
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php
                                if ($iServiceIdEdit > 0 && $MODULES_OBJ->isEnableProofUploadServiceCategories()) {
                                    $proof_note_section_display = ($eProofUpload == "No") ? 'style="display: none"' : '';
                                    ?>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label>Enable ID Proof Upload for Age Verification Feature<span class="red"> *</span></label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <select class="form-control" name="eProofUpload" id="eProofUpload"
                                                    required="required" onchange="displayProofNoteSection(this);">
                                                <option value="Yes" <?= ($eProofUpload == 'Yes') ? "selected" : "" ?>>
                                                    Yes
                                                </option>
                                                <option value="No" <?= ($eProofUpload == 'No') ? "selected" : "" ?>>No
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="proof_note_section" <?= $proof_note_section_display ?>>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>ID Proof Note For User</label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <textarea id="tProofNote" class="form-control" rows="3"
                                                          readonly="readonly"><?= $tProofNoteLang ?></textarea>
                                                <textarea name="tProofNote" id="tProofNoteUserHidden"
                                                          style="display: none;"><?= $tProofNote ?></textarea>
                                            </div>
                                            <div class="col-lg-2">
                                                <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                        data-original-title="Edit" onclick="editProofNote('user')">
                                                    <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>ID Proof Note For Driver</label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <textarea id="tProofNoteDriver" class="form-control" rows="3"
                                                          readonly="readonly"><?= $tProofNoteDriverLang ?></textarea>
                                                <textarea name="tProofNoteDriver" id="tProofNoteDriverHidden"
                                                          style="display: none;"><?= $tProofNoteDriver ?></textarea>
                                            </div>
                                            <div class="col-lg-2">
                                                <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                        data-original-title="Edit" onclick="editProofNote('driver')">
                                                    <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>ID Proof Note For Store</label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <textarea id="tProofNoteStore" class="form-control" rows="3"
                                                          readonly="readonly"><?= $tProofNoteStoreLang ?></textarea>
                                                <textarea name="tProofNoteStore" id="tProofNoteStoreHidden"
                                                          style="display: none;"><?= $tProofNoteStore ?></textarea>
                                            </div>
                                            <div class="col-lg-2">
                                                <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                        data-original-title="Edit" onclick="editProofNote('store')">
                                                    <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="id_proof_note_lang" tabindex="-1" role="dialog"
                                         aria-hidden="true" data-backdrop="static">
                                        <div class="modal-dialog">
                                            <div class="modal-content nimot-class">
                                                <div class="modal-header">
                                                    <h4>
                                                        <span id="id_proof_note_lang_title"
                                                              style="text-transform: capitalize;"></span>
                                                        <button type="button" class="close" data-dismiss="modal">x
                                                        </button>
                                                    </h4>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="id_proof_note_for"
                                                           id="id_proof_note_for">
                                                    <?php
                                                    if ($count_all > 0) {
                                                        for ($i = 0; $i < $count_all; $i++) {
                                                            $vCode = $db_master[$i]['vCode'];
                                                            $vTitle = $db_master[$i]['vTitle'];
                                                            $eDefault = $db_master[$i]['eDefault'];
                                                            $vValue = 'tProofNoteValue_' . $vCode;
                                                            $vValueName = 'tProofNoteTitle_' . $vCode;
                                                            $required = ($eDefault == 'Yes') ? '' : '';
                                                            $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                            ?><? if ($vCode == $default_lang && count($db_master) > 1) { ?>
                                                                <div class="form-group row">
                                                                    <div class="col-md-12">
                                                                        <label><span id="<?= $vValueName ?>">ID Proof Note</span>
                                                                            (<?= $vTitle ?>)</label>
                                                                        <textarea class="form-control"
                                                                                  name="<?= $vValue; ?>"
                                                                                  id="<?= $vValue; ?>"
                                                                                  data-lang="<?= $vCode ?>"
                                                                                  placeholder="<?= $vTitle; ?> Value" <?= $required; ?>
                                                                                  rows="3"></textarea>
                                                                        <div class="text-danger"
                                                                             id="<?= $vValue . '_error'; ?>"
                                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                                    </div>
                                                                </div>
                                                            <?php } else { ?>
                                                                <div class="form-group row">
                                                                    <div class="col-md-12">
                                                                        <label>ID Proof Note (<?= $vTitle ?>)</label>
                                                                        <textarea class="form-control"
                                                                                  name="<?= $vValue; ?>"
                                                                                  id="<?= $vValue; ?>"
                                                                                  data-lang="<?= $vCode ?>"
                                                                                  placeholder="<?= $vTitle; ?> Value"
                                                                                  rows="3"></textarea>
                                                                    </div>
                                                                </div>
                                                            <?php }
                                                            if ($EN_available) {
                                                                if ($vCode == "EN") { ?>
                                                                    <div class="form-group">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('tProofNoteValue_', 'EN');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            } else {
                                                                if ($vCode == $default_lang) { ?>
                                                                    <div class="form-group">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('tProofNoteValue_', '<?= $default_lang ?>');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                            } ?><? //if($vCode == $default_lang  && count($db_master) > 1) {
                                                            ?>
                                                            <!--<div class="form-group">
                                                <button type="button" class="btn btn-primary" onclick="getAllLanguageCode('tProofNoteValue_', '<?= $default_lang ?>');">Convert To All Language</button>
                                            </div>-->
                                                            <?php //}
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <div class="modal-footer" style="margin-top: 0">
                                                    <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">
                                                        <strong><?= $langage_lbl['LBL_NOTE']; ?>
                                                            : </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?>
                                                    </h5>
                                                    <div class="nimot-class-but" style="margin-bottom: 0">
                                                        <button type="button" class="save" id="id_proof_note_lang_btn"
                                                                style="margin-left: 0 !important"
                                                                onclick="saveProofNote()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                        <button type="button" class="btn btn-danger btn-ok"
                                                                data-dismiss="modal">Cancel
                                                        </button>
                                                    </div>
                                                </div>
                                                <div style="clear:both;"></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php if ($enablePromoteBanner == "Yes" && (($action == "Add" && $sub_action != "sub_category") || ($action == "Edit") && $iParentId == 0)) { ?>
                                    <div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Promote this service as banner in Home Screen
                                                    <i class="icon-question-sign" data-placement="top"
                                                       data-toggle="tooltip"
                                                       data-original-title="This service will be shown as banner in app home screen. Enabling this will remove this feature from all other services if enabled."></i></label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <div class="make-switch" data-on="success" data-off="warning">
                                                    <input type="checkbox" id="ePromoteBanner"
                                                           name="ePromoteBanner" <?= ($id != '' && $ePromoteBanner == 'Yes') ? 'checked' : ''; ?>
                                                           value="Yes"/>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if (count($db_master) > 1) { ?>
                                            <div class="row PromoteBanner" <?= ($id != '' && $ePromoteBanner == 'No') ? 'style="display: none"' : ''; ?>>
                                                <div class="col-lg-12">
                                                    <label>Promotional Banner Title</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <input type="text"
                                                           class="form-control <?= ($id == "") ? 'readonly-custom' : '' ?>"
                                                           id="tPromoteBannerTitle_Default"
                                                           value="<?= $userEditDataArr['tPromoteBannerTitle_' . $default_lang]; ?>"
                                                           data-originalvalue="<?= $userEditDataArr['tPromoteBannerTitle_' . $default_lang]; ?>"
                                                           readonly="readonly" <?php if ($id == "") { ?> onclick="editPromoteBannerTitle('Add')" <?php } ?>>
                                                </div>
                                                <?php if ($id != "") { ?>
                                                    <div class="col-lg-2">
                                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                                data-original-title="Edit"
                                                                onclick="editPromoteBannerTitle('Edit')">
                                                            <span class="glyphicon glyphicon-pencil"
                                                                  aria-hidden="true"></span>
                                                        </button>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <div class="modal fade" id="tPromoteBannerTitle_Modal" tabindex="-1"
                                                 role="dialog" aria-hidden="true" data-backdrop="static"
                                                 data-keyboard="false">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="banner_button_action"></span> Promotional
                                                                Banner Title
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                        onclick="resetToOriginalValue(this, 'tPromoteBannerTitle_')">
                                                                    x
                                                                </button>
                                                            </h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php
                                                            for ($i = 0; $i < $count_all; $i++) {
                                                                $vCode = $db_master[$i]['vCode'];
                                                                $vTitle = $db_master[$i]['vTitle'];
                                                                $eDefault = $db_master[$i]['eDefault'];
                                                                $required = ($eDefault == 'Yes') ? 'required' : '';
                                                                $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                                $tPromoteBannerTitle = 'tPromoteBannerTitle_' . $vCode;
                                                                $tPromoteBannerTitledefault = 'tPromoteBannerTitle_' . $default_lang;
                                                                ?><?php
                                                                $page_title_class = 'col-lg-12';
                                                                if (count($db_master) > 1) {
                                                                    if ($EN_available) {
                                                                        if ($vCode == "EN") {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    } else {
                                                                        if ($vCode == $default_lang) {
                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <label>Promote Banner Title (<?= $vTitle; ?>
                                                                            ) <?php echo $required_msg; ?></label>
                                                                    </div>
                                                                    <div class="<?= $page_title_class ?>">
                                                                        <input type="text" class="form-control"
                                                                               name="<?= $tPromoteBannerTitle; ?>"
                                                                               id="<?= $tPromoteBannerTitle; ?>"
                                                                               value="<?= $userEditDataArr[$tPromoteBannerTitle]; ?>"
                                                                               data-originalvalue="<?= $userEditDataArr[$tPromoteBannerTitle]; ?>"
                                                                               placeholder="<?= $vTitle; ?> Value">
                                                                        <div class="text-danger"
                                                                             id="<?= $tPromoteBannerTitle . '_error'; ?>"
                                                                             style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                                    </div>
                                                                    <?php
                                                                    if (count($db_master) > 1) {
                                                                        if ($EN_available) {
                                                                            if ($vCode == "EN") { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button"
                                                                                            name="allLanguage"
                                                                                            id="allLanguage"
                                                                                            class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('tPromoteBannerTitle_', 'EN');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        } else {
                                                                            if ($vCode == $default_lang) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button"
                                                                                            name="allLanguage"
                                                                                            id="allLanguage"
                                                                                            class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('tPromoteBannerTitle_', '<?= $default_lang ?>');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="modal-footer" style="margin-top: 0">
                                                            <h5 class="text-left"
                                                                style="margin-bottom: 15px; margin-top: 0;">
                                                                <strong><?= $langage_lbl['LBL_NOTE']; ?>
                                                                    : </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?>
                                                            </h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save"
                                                                        id="tPromoteBannerTitle_btn"
                                                                        style="margin-left: 0 !important"
                                                                        onclick="savePromoteBannerTitle()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok"
                                                                        data-dismiss="modal"
                                                                        onclick="resetToOriginalValue(this, 'tPromoteBannerTitle_')">
                                                                    Cancel
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Promotional Banner Title</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <input type="text" class="form-control"
                                                           name="tPromoteBannerTitle_<?= $default_lang ?>"
                                                           id="tPromoteBannerTitle_<?= $default_lang ?>"
                                                           value="<?= $userEditDataArr['tPromoteBannerTitle_' . $default_lang]; ?>">
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <div class="row PromoteBanner" <?= ($id != '' && $ePromoteBanner == 'No') ? 'style="display: none"' : ''; ?>>
                                            <div class="col-lg-12">
                                                <label>Promotional Banner (App Home Screen)</label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <div class="manage-banner-section">
                                                    <?php if (count($promotionalBanner) > 0) { ?>
                                                        <div class="banner-img-block">
                                                            <?php foreach ($promotionalBanner as $promo_banner_img) { ?>
                                                                <div class="banner-img">
                                                                    <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=400&src=' . $tconfig['tsite_upload_images'] . $promo_banner_img['vImage']; ?>">
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    <?php } else { ?>
                                                        <div class="no-banner">
                                                            No Banner Found.
                                                        </div>
                                                    <?php } ?>
                                                    <a href="<?= $tconfig['tsite_admin_url'] . 'app_banner.php?iVehicleCategoryId=' . $id ?>&eFor=Promotion"
                                                       class="manage-banner-btn" target="_blank">Manage Promotional
                                                        Banner for App Home Screen</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if (($eCatType == "MoreDelivery" && $THEME_OBJ->isCubeJekXv3ProThemeActive() == 'No') || ($THEME_OBJ->isProSPThemeActive() == "Yes" && $iParentId == 0 && $parent_ufx_catid == 0)) { ?>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label>Rotating Banners (Inner Page)</label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <div class="manage-banner-section">
                                                <?php if (count($bannerDelivery) > 0) { ?>
                                                    <div class="banner-img-block">
                                                        <?php foreach ($bannerDelivery as $banner_img) { ?>
                                                            <div class="banner-img">
                                                                <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=400&src=' . $tconfig['tsite_upload_images'] . $banner_img['vImage']; ?>">
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                <?php } else { ?>
                                                    <div class="no-banner">
                                                        No Banners Found.
                                                    </div>
                                                <?php } ?>
                                                <a href="<?= $tconfig['tsite_admin_url'] . 'banner.php?eForDelivery=' . $eCatType . '&eFor=' . $eFor ?>"
                                                   class="manage-banner-btn" target="_blank">Manage Banners for this
                                                    service</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="row" <?= $stylehide ?>>
                                    <div class="col-lg-12">
                                        <label>Display Order</label>
                                    </div>
                                    <?php if ($sub_action == "sub_category") { ?>
                                        <div class="col-md-6 col-sm-6">
                                            <?
                                            $temp = 1;
                                            $query1 = $obj->MySQLSelect("SELECT count(iVehicleCategoryId) as maxnumber FROM " . $tbl_name . " WHERE iParentId = '" . $sub_cid . "' ORDER BY iDisplayOrder");
                                            $maxnum = isset($query1[0]['maxnumber']) ? $query1[0]['maxnumber'] : 0;
                                            $dataArray = array();
                                            for ($i = 1; $i <= $maxnum; $i++) {
                                                $dataArray[] = $i;
                                                $temp = $iDisplayOrder;
                                            }
                                            ?>
                                            <select name="iDisplayOrder" class="form-control">
                                                <? foreach ($dataArray as $arr): ?>
                                                    <option <?= $arr == $temp ? ' selected="selected"' : '' ?>
                                                            value="<?= $arr; ?>">
                                                        -- <?= $arr ?> --
                                                    </option>
                                                <? endforeach; ?>
                                                <? if ($action == "Add") { ?>
                                                    <option value="<?= $temp; ?>">-- <?= $temp ?> --</option>
                                                <? } ?>
                                            </select>
                                        </div>
                                    <?php } else { ?>
                                        <div class="col-md-6 col-sm-6">
                                            <?
                                            $temp = 1;
                                            $query1 = $obj->MySQLSelect("SELECT count(iVehicleCategoryId) as maxnumber FROM " . $tbl_name . " WHERE iParentId = 0 ORDER BY iDisplayOrder");
                                            $maxnum = isset($query1[0]['maxnumber']) ? $query1[0]['maxnumber'] : 0;
                                            $dataArray = array();
                                            for ($i = 1; $i <= $maxnum; $i++) {
                                                $dataArray[] = $i;
                                                $temp = $iDisplayOrder;
                                            }
                                            ?>
                                            <select name="iDisplayOrder" class="form-control">
                                                <? foreach ($dataArray as $arr): ?>
                                                    <option <?= $arr == $temp ? ' selected="selected"' : '' ?>
                                                            value="<?= $arr; ?>">
                                                        -- <?= $arr ?> --
                                                    </option>
                                                <? endforeach; ?>
                                                <? if ($action == "Add") { ?>
                                                    <option value="<?= $temp; ?>">-- <?= $temp ?> --</option>
                                                <? } ?>
                                            </select>
                                        </div>
                                    <?php } ?>
                                </div>
                                <?php if ($userObj->hasPermission($updateStatus)) { ?>
                                    <div class="row" <?= $stylehide ?>>
                                        <div class="col-lg-12">
                                            <label>Status<span class="red"> *</span></label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <select class="form-control" name='eStatus' id='eStatus' required>
                                                <option value="Active"
                                                        <? if ('Active' == $db_data[0]['eStatus']) { ?>selected<? } ?>>
                                                    Active
                                                </option>
                                                <option value="Inactive"
                                                        <? if ('Inactive' == $db_data[0]['eStatus']) { ?>selected<? } ?>>
                                                    Inactive
                                                </option>
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <?php if (($action == 'Edit' && $userObj->hasPermission($update)) || ($action == 'Add' && $userObj->hasPermission($create))) { ?>
                                            <input type="submit" class="save btn-info" name="btnsubmit" id="btnsubmit"
                                                   value="<?= $actionSave; ?>">
                                            <input type="reset" value="Reset" class="btn btn-default">
                                        <?php } ?>
                                        <?php if ($THEME_OBJ->isCubeJekXv3ProThemeActive() == 'No' || $eServiceType != 'Deliver') { ?>
                                       <!-- <a href="vehicle_category.php" class="btn btn-default back_link">Cancel</a>-->
                                        <?php } ?>
                                    </div>
                                </div>
                            </form>
                            <?php if ($cubexthemeon == 1 && empty($sub_cid)) { ?>
                        </div>
                        <div id="Homepagesettings"
                             class="tab-pane <?php if ($activetab == 'homepage') { ?> active <?php } ?>">
                            <form id="vtype" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?= $id; ?>"/>
                                <input type="hidden" name="previousLink" id="previousLink"
                                       value="<?php echo $previousLink; ?>"/>
                                <input type="hidden" name="backlink" class="backlink" value="vehicle_category.php"/>
                                <?php
                                if ($homepage_cubejekx == 0) {
                                    if ($count_all > 0) {
                                        for ($i = 0; $i < $count_all; $i++) {
                                            $vCode = $db_master[$i]['vCode'];
                                            $vTitle = $db_master[$i]['vTitle'];
                                            $vSlogan = $db_master[$i]['vSlogan'];
                                            $eDefault = $db_master[$i]['eDefault'];
                                            $vCatNameHomepageN = 'vCatNameHomepage_' . $vCode;
                                            $vCatTitleHomepageN = 'vCatTitleHomepage_' . $vCode;
                                            $vCatSloganHomepageN = 'vCatSloganHomepage_' . $vCode;
                                            $lCatDescHomepageN = 'lCatDescHomepage_' . $vCode;
                                            $vCatDescbtnHomepageN = 'vCatDescbtnHomepage_' . $vCode;
                                            $vServiceCatTitleHomepageN = 'vServiceCatTitleHomepage_' . $vCode;
                                            ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Name (<?= $vTitle; ?>)</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <input type="text" class="form-control"
                                                           name="<?= $vCatNameHomepageN; ?>"
                                                           id="<?= $vCatNameHomepageN; ?>"
                                                           value="<?= $vCatNameHomepageArr[$vCatNameHomepageN]; ?>"
                                                           placeholder="<?= $vTitle . " Value"; ?>">
                                                    <div class="text-danger" id="<?= $vCatNameHomepageN . '_error'; ?>"
                                                         style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                </div>
                                                <?
                                                if ($EN_available) {
                                                    if ($vCode == "EN") { ?>
                                                        <div class="form-group">
                                                            <button type="button" name="allLanguage" id="allLanguage"
                                                                    class="btn btn-primary"
                                                                    onClick="getAllLanguageCode('vCatNameHomepage_', 'EN');">
                                                                Convert To All Language
                                                            </button>
                                                        </div>
                                                    <?php }
                                                } else {
                                                    if ($vCode == $default_lang) { ?>
                                                        <div class="form-group">
                                                            <button type="button" name="allLanguage" id="allLanguage"
                                                                    class="btn btn-primary"
                                                                    onClick="getAllLanguageCode('vCatNameHomepage_', '<?= $default_lang ?>');">
                                                                Convert To All Language
                                                            </button>
                                                        </div>
                                                    <?php }
                                                }
                                                //if ($vCode == $default_lang && count($db_master) > 1) {
                                                ?>
                                                <!--<div class="col-md-6 col-sm-6">
                                        <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vCatNameHomepage_', '<?= $default_lang ?>');">Convert To All Language</button>
                                    </div>-->
                                                <?php
                                                // }
                                                ?>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Title (<?= $vTitle; ?>)</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <input type="text" class="form-control"
                                                           name='<?= $vCatTitleHomepageN; ?>'
                                                           id='<?= $vCatTitleHomepageN; ?>'
                                                           value="<?= $vCatTitleHomepageArr[$vCatTitleHomepageN]; ?>"
                                                           placeholder="<?= $vTitle . " Value"; ?>">
                                                    <div class="text-danger" id="<?= $vCatTitleHomepageN . '_error'; ?>"
                                                         style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                </div>
                                                <?
                                                if ($EN_available) {
                                                    if ($vCode == "EN") { ?>
                                                        <div class="form-group">
                                                            <button type="button" name="allLanguage" id="allLanguage"
                                                                    class="btn btn-primary"
                                                                    onClick="getAllLanguageCode('vCatTitleHomepage_', 'EN');">
                                                                Convert To All Language
                                                            </button>
                                                        </div>
                                                    <?php }
                                                } else {
                                                    if ($vCode == $default_lang) { ?>
                                                        <div class="form-group">
                                                            <button type="button" name="allLanguage" id="allLanguage"
                                                                    class="btn btn-primary"
                                                                    onClick="getAllLanguageCode('vCatTitleHomepage_', '<?= $default_lang ?>');">
                                                                Convert To All Language
                                                            </button>
                                                        </div>
                                                    <?php }
                                                }
                                                //if ($vCode == $default_lang && count($db_master) > 1) {
                                                ?>
                                                <!--<div class="col-md-6 col-sm-6">
                                        <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vCatTitleHomepage_', '<?= $default_lang ?>');">Convert To All Language</button>
                                    </div>-->
                                                <?php
                                                //}
                                                ?>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Slogan (<?= $vTitle; ?>)</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <input type="text" class="form-control"
                                                           name='<?= $vCatSloganHomepageN; ?>'
                                                           id='<?= $vCatSloganHomepageN; ?>'
                                                           value="<?= $vCatSloganHomepageArr[$vCatSloganHomepageN]; ?>"
                                                           placeholder="<?= $vSlogan . " Value"; ?>">
                                                    <div class="text-danger"
                                                         id="<?= $vCatSloganHomepageN . '_error'; ?>"
                                                         style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                </div>
                                                <?
                                                if ($EN_available) {
                                                    if ($vCode == "EN") { ?>
                                                        <div class="form-group">
                                                            <button type="button" name="allLanguage" id="allLanguage"
                                                                    class="btn btn-primary"
                                                                    onClick="getAllLanguageCode('vCatSloganHomepage_', 'EN');">
                                                                Convert To All Language
                                                            </button>
                                                        </div>
                                                    <?php }
                                                } else {
                                                    if ($vCode == $default_lang) { ?>
                                                        <div class="form-group">
                                                            <button type="button" name="allLanguage" id="allLanguage"
                                                                    class="btn btn-primary"
                                                                    onClick="getAllLanguageCode('vCatSloganHomepage_', '<?= $default_lang ?>');">
                                                                Convert To All Language
                                                            </button>
                                                        </div>
                                                    <?php }
                                                }
                                                //if ($vCode == $default_lang && count($db_master) > 1) {
                                                ?>
                                                <!--<div class="col-md-6 col-sm-6">
                                        <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vCatSloganHomepage_', '<?= $default_lang ?>');">Convert To All Language</button>
                                    </div>-->
                                                <?php
                                                //}
                                                ?>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Description (<?= $vTitle; ?>)</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control" name="<?= $lCatDescHomepageN; ?>"
                                                              id="<?= $lCatDescHomepageN; ?>"
                                                              placeholder="<?= $vTitle . " Value"; ?>"><?= $lCatDescHomepageArr[$lCatDescHomepageN]; ?></textarea>
                                                    <div class="text-danger" id="<?= $lCatDescHomepageN . '_error'; ?>"
                                                         style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                </div>
                                                <?
                                                if ($EN_available) {
                                                    if ($vCode == "EN") { ?>
                                                        <div class="form-group">
                                                            <button type="button" name="allLanguage" id="allLanguage"
                                                                    class="btn btn-primary"
                                                                    onClick="getAllLanguageCode('lCatDescHomepage_', 'EN');">
                                                                Convert To All Language
                                                            </button>
                                                        </div>
                                                    <?php }
                                                } else {
                                                    if ($vCode == $default_lang) { ?>
                                                        <div class="form-group">
                                                            <button type="button" name="allLanguage" id="allLanguage"
                                                                    class="btn btn-primary"
                                                                    onClick="getAllLanguageCode('lCatDescHomepage_', '<?= $default_lang ?>');">
                                                                Convert To All Language
                                                            </button>
                                                        </div>
                                                    <?php }
                                                }
                                                //if ($vCode == $default_lang && count($db_master) > 1) {
                                                ?>
                                                <!--<div class="col-md-6 col-sm-6">
                                        <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('lCatDescHomepage_', '<?= $default_lang ?>');">Convert To All Language</button>
                                    </div>-->
                                                <?php
                                                //}
                                                ?>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Button text (<?= $vTitle; ?>)</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <input type="text" class="form-control"
                                                           name='<?= $vCatDescbtnHomepageN; ?>'
                                                           id='<?= $vCatDescbtnHomepageN; ?>'
                                                           value="<?= $vCatDescbtnHomepageArr[$vCatDescbtnHomepageN]; ?>"
                                                           placeholder="<?= $vTitle . " Value"; ?>">
                                                    <div class="text-danger"
                                                         id="<?= $vCatDescbtnHomepageN . '_error'; ?>"
                                                         style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                </div>
                                                <?
                                                if ($EN_available) {
                                                    if ($vCode == "EN") { ?>
                                                        <div class="form-group">
                                                            <button type="button" name="allLanguage" id="allLanguage"
                                                                    class="btn btn-primary"
                                                                    onClick="getAllLanguageCode('vCatDescbtnHomepage_', 'EN');">
                                                                Convert To All Language
                                                            </button>
                                                        </div>
                                                    <?php }
                                                } else {
                                                    if ($vCode == $default_lang) { ?>
                                                        <div class="form-group">
                                                            <button type="button" name="allLanguage" id="allLanguage"
                                                                    class="btn btn-primary"
                                                                    onClick="getAllLanguageCode('vCatDescbtnHomepage_', '<?= $default_lang ?>');">
                                                                Convert To All Language
                                                            </button>
                                                        </div>
                                                    <?php }
                                                }
                                                //if ($vCode == $default_lang && count($db_master) > 1) {
                                                ?>
                                                <!--<div class="col-md-6 col-sm-6">
                                        <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vCatDescbtnHomepage_', '<?= $default_lang ?>');">Convert To All Language</button>
                                    </div>-->
                                                <?php
                                                //}
                                                ?>
                                            </div>
                                        <?php }
                                    } ?>
                                    <div class="row imagebox">
                                        <div class="col-lg-12">
                                            <label>Logo</label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <? if (isset($vHomepageLogo) && $vHomepageLogo != '') { ?>
                                                <!-- <img src="<?= $tconfig['tsite_upload_home_page_service_images'] . "/" . $vHomepageLogo; ?>" style="width:100px;height:100px;"> -->
                                                <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=200&h=200&src=' . $tconfig['tsite_upload_home_page_service_images'] . "/" . $vHomepageLogo; ?>"
                                                     style="width:100px;height:100px;">
                                            <? } ?>
                                            <input type="file" class="form-control"
                                                   name="vHomepageLogo" <?php echo $required_rule; ?> id="vHomepageLogo"
                                                   placeholder="" style="padding-bottom: 39px;">
                                            <br/>
                                            Note: Upload only png image size of 512px X 512px.
                                        </div>
                                    </div>
                                    <div class="row imagebox">
                                        <div class="col-lg-12">
                                            <label>Banner</label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <? if (isset($vHomepageBanner) && $vHomepageBanner != '') { ?>
                                                <!-- <img src="<?= $tconfig['tsite_upload_home_page_service_images'] . "/" . $vHomepageBanner; ?>" style="width:200px;"> -->
                                                <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=400&&src=' . $tconfig['tsite_upload_home_page_service_images'] . "/" . $vHomepageBanner; ?>"
                                                     style="width:200px;">
                                            <? } ?>
                                            <input type="file" class="form-control"
                                                   name="vHomepageBanner" <?php echo $required_rule; ?>
                                                   id="vHomepageBanner" placeholder="" style="padding-bottom: 39px;">
                                            <br/>
                                            Note: Recommended dimension for banner image is 2880px X 1620px.
                                        </div>
                                    </div>
                                <? }
                                if ($homepage_cubejekx != 0) { ?>
                                    <h3>Shown In Service Section</h3>
                                    <?php if (count($db_master) > 1) { ?>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Title (<?= $db_master[0]['vTitle']; ?>)</label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <input type="text"
                                                       class="form-control <?= ($id == "") ? 'readonly-custom' : '' ?>"
                                                       id="vServiceCatTitleHomepage_Default"
                                                       value="<?= $vServiceCatTitleHomepageArr['vServiceCatTitleHomepage_' . $default_lang]; ?>"
                                                       data-originalvalue="<?= $vServiceCatTitleHomepageArr['vServiceCatTitleHomepage_' . $default_lang]; ?>"
                                                       readonly="readonly" <?php if ($id == "") { ?> onclick="editServiceCatTitleHomepage('Add')" <?php } ?>>
                                            </div>
                                            <?php if ($id != "") { ?>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                            data-original-title="Edit"
                                                            onclick="editServiceCatTitleHomepage('Edit')">
                                                        <span class="glyphicon glyphicon-pencil"
                                                              aria-hidden="true"></span>
                                                    </button>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <? if ($THEME_OBJ->isMedicalServicev2ThemeActive() == 'Yes' || $THEME_OBJ->isDeliveryKingXv2ThemeActive() == 'Yes' || $THEME_OBJ->isCubeJekXv2ThemeActive() == 'Yes' || $THEME_OBJ->isCubeJekXv3ThemeActive() == 'Yes' || $THEME_OBJ->isCubeJekXv3ProThemeActive() == 'Yes' || $THEME_OBJ->isPXCProThemeActive() == "Yes") { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>SubTitle (<?= $db_master[0]['vTitle']; ?>)</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <input type="text"
                                                           class="form-control <?= ($id == "") ? 'readonly-custom' : '' ?>"
                                                           id="vServiceCatSubTitleHomepage_Default"
                                                           value="<?= $vServiceCatTitleHomepageArr['vServiceCatSubTitleHomepage_' . $default_lang]; ?>"
                                                           data-originalvalue="<?= $vServiceCatSubTitleHomepageArr['vServiceCatSubTitleHomepage_' . $default_lang]; ?>"
                                                           readonly="readonly" <?php if ($id == "") { ?> onclick="editServiceCatSubTitleHomepage('Add')" <?php } ?>>
                                                </div>
                                                <?php if ($id != "") { ?>
                                                    <div class="col-lg-2">
                                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                                data-original-title="Edit"
                                                                onclick="editServiceCatSubTitleHomepage('Edit')">
                                                            <span class="glyphicon glyphicon-pencil"
                                                                  aria-hidden="true"></span>
                                                        </button>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <? } ?>
                                        <div class="modal fade" id="ServiceCatTitle_Modal" tabindex="-1" role="dialog"
                                             aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content nimot-class">
                                                    <div class="modal-header">
                                                        <h4>
                                                            <span id="category_action"></span> Title
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                    onclick="resetToOriginalValue(this, 'vServiceCatTitleHomepage_')">
                                                                x
                                                            </button>
                                                        </h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <?php
                                                        for ($i = 0; $i < $count_all; $i++) {
                                                            $vCode = $db_master[$i]['vCode'];
                                                            $vTitle = $db_master[$i]['vTitle'];
                                                            $eDefault = $db_master[$i]['eDefault'];
                                                            $vValue = 'vServiceCatTitleHomepage_' . $vCode;
                                                            $$vValue = $vServiceCatTitleHomepageArr[$vValue];
                                                            $required = ($eDefault == 'Yes') ? 'required' : '';
                                                            $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                            ?><?php
                                                            $page_title_class = 'col-lg-12';
                                                            if (count($db_master) > 1) {
                                                                if ($EN_available) {
                                                                    if ($vCode == "EN") {
                                                                        $page_title_class = 'col-md-9 col-sm-9';
                                                                    }
                                                                } else {
                                                                    if ($vCode == $default_lang) {
                                                                        $page_title_class = 'col-md-9 col-sm-9';
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <label>Title (<?= $vTitle; ?>
                                                                        ) <?php echo $required_msg; ?></label>
                                                                </div>
                                                                <div class="<?= $page_title_class ?>">
                                                                    <input type="text" class="form-control"
                                                                           name="<?= $vValue; ?>" id="<?= $vValue; ?>"
                                                                           value="<?= $$vValue; ?>"
                                                                           data-originalvalue="<?= $$vValue; ?>"
                                                                           placeholder="<?= $vTitle; ?> Value">
                                                                    <div class="text-danger"
                                                                         id="<?= $vValue . '_error'; ?>"
                                                                         style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                                </div>
                                                                <?php
                                                                if (count($db_master) > 1) {
                                                                    if ($EN_available) {
                                                                        if ($vCode == "EN") { ?>
                                                                            <div class="col-md-3 col-sm-3">
                                                                                <button type="button" name="allLanguage"
                                                                                        id="allLanguage"
                                                                                        class="btn btn-primary"
                                                                                        onClick="getAllLanguageCode('vServiceCatTitleHomepage_', 'EN');">
                                                                                    Convert To All Language
                                                                                </button>
                                                                            </div>
                                                                        <?php }
                                                                    } else {
                                                                        if ($vCode == $default_lang) { ?>
                                                                            <div class="col-md-3 col-sm-3">
                                                                                <button type="button" name="allLanguage"
                                                                                        id="allLanguage"
                                                                                        class="btn btn-primary"
                                                                                        onClick="getAllLanguageCode('vServiceCatTitleHomepage_', '<?= $default_lang ?>');">
                                                                                    Convert To All Language
                                                                                </button>
                                                                            </div>
                                                                        <?php }
                                                                    }
                                                                }
                                                                ?>
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="modal-footer" style="margin-top: 0">
                                                        <h5 class="text-left"
                                                            style="margin-bottom: 15px; margin-top: 0;">
                                                            <strong><?= $langage_lbl['LBL_NOTE']; ?>
                                                                : </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?>
                                                        </h5>
                                                        <div class="nimot-class-but" style="margin-bottom: 0">
                                                            <button type="button" class="save" id="vCategory_btn"
                                                                    style="margin-left: 0 !important"
                                                                    onclick="saveServiceCatTitleHomepage()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                            <button type="button" class="btn btn-danger btn-ok"
                                                                    data-dismiss="modal"
                                                                    onclick="resetToOriginalValue(this, 'vServiceCatTitleHomepage_')">
                                                                Cancel
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div style="clear:both;"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal fade" id="ServiceCatSubTitle_Modal" tabindex="-1"
                                             role="dialog" aria-hidden="true" data-backdrop="static"
                                             data-keyboard="false">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content nimot-class">
                                                    <div class="modal-header">
                                                        <h4>
                                                            <span id="category_action"></span> SubTitle
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                    onclick="resetToOriginalValue(this, 'vServiceCatSubTitleHomepage_')">
                                                                x
                                                            </button>
                                                        </h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <?php
                                                        for ($i = 0; $i < $count_all; $i++) {
                                                            $vCode = $db_master[$i]['vCode'];
                                                            $vTitle = $db_master[$i]['vTitle'];
                                                            $eDefault = $db_master[$i]['eDefault'];
                                                            $vValue = 'vServiceCatSubTitleHomepage_' . $vCode;
                                                            $$vValue = $vServiceCatTitleHomepageArr[$vValue];
                                                            $required = ($eDefault == 'Yes') ? 'required' : '';
                                                            $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                            ?><?php
                                                            $page_title_class = 'col-lg-12';
                                                            if (count($db_master) > 1) {
                                                                if ($EN_available) {
                                                                    if ($vCode == "EN") {
                                                                        $page_title_class = 'col-md-9 col-sm-9';
                                                                    }
                                                                } else {
                                                                    if ($vCode == $default_lang) {
                                                                        $page_title_class = 'col-md-9 col-sm-9';
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <label>SubTitle (<?= $vTitle; ?>
                                                                        ) <?php echo $required_msg; ?></label>
                                                                </div>
                                                                <div class="<?= $page_title_class ?>">
                                                                    <input type="text" class="form-control"
                                                                           name="<?= $vValue; ?>" id="<?= $vValue; ?>"
                                                                           value="<?= $$vValue; ?>"
                                                                           data-originalvalue="<?= $$vValue; ?>"
                                                                           placeholder="<?= $vTitle; ?> Value">
                                                                    <div class="text-danger"
                                                                         id="<?= $vValue . '_error'; ?>"
                                                                         style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                                </div>
                                                                <?php
                                                                if (count($db_master) > 1) {
                                                                    if ($EN_available) {
                                                                        if ($vCode == "EN") { ?>
                                                                            <div class="col-md-3 col-sm-3">
                                                                                <button type="button" name="allLanguage"
                                                                                        id="allLanguage"
                                                                                        class="btn btn-primary"
                                                                                        onClick="getAllLanguageCode('vServiceCatSubTitleHomepage_', 'EN');">
                                                                                    Convert To All Language
                                                                                </button>
                                                                            </div>
                                                                        <?php }
                                                                    } else {
                                                                        if ($vCode == $default_lang) { ?>
                                                                            <div class="col-md-3 col-sm-3">
                                                                                <button type="button" name="allLanguage"
                                                                                        id="allLanguage"
                                                                                        class="btn btn-primary"
                                                                                        onClick="getAllLanguageCode('vServiceCatSubTitleHomepage_', '<?= $default_lang ?>');">
                                                                                    Convert To All Language
                                                                                </button>
                                                                            </div>
                                                                        <?php }
                                                                    }
                                                                }
                                                                ?>
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="modal-footer" style="margin-top: 0">
                                                        <h5 class="text-left"
                                                            style="margin-bottom: 15px; margin-top: 0;">
                                                            <strong><?= $langage_lbl['LBL_NOTE']; ?>
                                                                : </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?>
                                                        </h5>
                                                        <div class="nimot-class-but" style="margin-bottom: 0">
                                                            <button type="button" class="save" id="vCategory_btn"
                                                                    style="margin-left: 0 !important"
                                                                    onclick="saveServiceCatSubTitleHomepage()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                            <button type="button" class="btn btn-danger btn-ok"
                                                                    data-dismiss="modal"
                                                                    onclick="resetToOriginalValue(this, 'vServiceCatSubTitleHomepage_')">
                                                                Cancel
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div style="clear:both;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Title (<?= $db_master[0]['vTitle']; ?>)</label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <input type="text" class="form-control"
                                                       id="vServiceCatTitleHomepage_<?= $default_lang ?>"
                                                       name="vServiceCatTitleHomepage_<?= $default_lang ?>"
                                                       value="<?= $vServiceCatTitleHomepageArr['vServiceCatTitleHomepage_' . $default_lang]; ?>"
                                                       required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>SubTitle (<?= $db_master[0]['vSubTitle']; ?>)</label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <input type="text" class="form-control"
                                                       id="vServiceCatSubTitleHomepage_<?= $default_lang ?>"
                                                       name="vServiceCatSubTitleHomepage_<?= $default_lang ?>"
                                                       value="<?= $vServiceCatTitleHomepageArr['vServiceCatSubTitleHomepage_' . $default_lang]; ?>"
                                                       required>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="row imagebox">
                                        <?php if ($THEME_OBJ->isMedicalServicev2ThemeActive() == 'Yes' || $THEME_OBJ->isCubeJekXv3ThemeActive() == 'Yes' || $THEME_OBJ->isDeliveryKingXv2ThemeActive() == "Yes" || $THEME_OBJ->isCubeJekXv3ProThemeActive() == 'Yes' || $THEME_OBJ->isPXCProThemeActive() == "Yes") { ?>
												 
                                    <div class="row col-lg-12">
												<div class="col-lg-12">
													<label>Home page Our Services Image</label>
												</div>
												<div class="col-md-6 col-sm-6">
													<? if (isset($vHomepageFullLogo) && $vHomepageFullLogo != '') { ?>
														<!-- <img src="<?= $tconfig['tsite_upload_home_page_service_images'] . "/" . $vHomepageFullLogo; ?>" style="width:100px;height:100px;"> -->
														<img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=200&h=200&src=' . $tconfig['tsite_upload_home_page_service_images'] . "/" . $vHomepageFullLogo; ?>"
															 style="width:100px;height:100px;">
													<? } ?>
													<input type="file" class="form-control"
														   name="vHomepageFullLogo" <?php echo $required_rule; ?> id="vHomepageFullLogo"
														   placeholder="" style="padding-bottom: 39px;">
													<br/>
													Note: Upload only png image size of 512px X 512px.
												</div>
											</div>
                                    <div class="row col-lg-12">

                                             <div class="col-lg-12">
                                                <label>Home page Our Services icon</label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <? if (isset($vHomepageLogoOurServices) && $vHomepageLogoOurServices != '') { ?>
                                                    <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=100&src=' . $tconfig['tsite_upload_home_page_service_images'] . "/" . $vHomepageLogoOurServices; ?>"
                                                         style="width:100px;">
                                                <? } ?>
                                                <input type="file" class="form-control"
                                                       name="vHomepageLogoOurServices" <?php echo $required_rule; ?>
                                                       id="vHomepageLogoOurServices" placeholder=""
                                                       style="padding-bottom: 39px;">
                                                <br/>
                                                Note: Upload only png image size of 512px X 512px.
                                            </div>
											</div>
                                        <?php } else { ?><?php if ($THEME_OBJ->isCubeXv2ThemeActive() == 'No') { ?>
                                            <div class="col-lg-12">
                                                <label>Background Image</label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <? if (isset($vServiceHomepageBanner) && $vServiceHomepageBanner != '') { ?>
                                                    <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=400&&src=' . $tconfig['tsite_upload_home_page_service_images'] . "/" . $vServiceHomepageBanner; ?>"
                                                         style="width:200px;">
                                                <? } ?>
                                                <input type="file" class="form-control"
                                                       name="vServiceHomepageBanner" <?php echo $required_rule; ?>
                                                       id="vServiceHomepageBanner" placeholder=""
                                                       style="padding-bottom: 39px;">
                                                <br/>
                                                Note: Recommended dimension for banner image is 2880px X 1620px.
                                            </div>
                                        <?php } ?><?php } ?>
                                    </div>
                                <? }
                                if ($cubexthemeon == 1) { //becoz orderhomepage shown in cubex and cubejekx both
                                    ?>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label>Order</label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <input type="hidden" name="temp_orderHomepage" id="temp_orderHomepage"
                                                   value="<?= ($action == 'Edit') ? $iDisplayOrderHomepage_db : '1'; ?>">
                                            <?
                                            $display_numbers = ($action == "Add") ? $iDisplayOrder_max_Homepage : $iDisplayOrderHomepage;
                                            ?>
                                            <select name="iDisplayOrderHomepage" class="form-control">
                                                <? for ($i = 1; $i <= $display_numbers; $i++) { ?>
                                                    <option value="<?= $i ?>" <? if ($i == $iDisplayOrderHomepage_db) {
                                                        echo "selected";
                                                    } ?>> -- <?= $i ?> --
                                                    </option>
                                                <? } ?>
                                            </select>
                                        </div>
                                    </div>
                                <? } ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <?php if (($action == 'Edit' && $userObj->hasPermission($update)) || ($action == 'Add' && $userObj->hasPermission($create))) { ?>
                                            <input type="submit" class="save btn-info" name="btnsubmit_homepage"
                                                   id="btnsubmit_homepage" value="<?= $actionSave; ?>">
                                            <input type="reset" value="Reset" class="btn btn-default">
                                        <?php } ?>
                                      <!--  <a href="vehicle_category.php" class="btn btn-default back_link">Cancel</a>-->
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php } ?>
                </div>
            </div>
            <div style="clear:both;"></div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<div class="row loding-action" id="loaderIcon" style="display:none;">
    <div align="center">
        <img src="default.gif">
        <span>Language Translation is in Process. Please Wait...</span>
    </div>
</div>
<? include_once('footer.php'); ?>
<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
<link rel="stylesheet" type="text/css" media="screen"
      href="css/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
<script type="text/javascript" src="js/moment.min.js"></script>
<script>
    var myVar;
    $(document).ready(function () {
        var referrer;
        <?php if ($goback == 1) { ?>
        alert('<?php echo $var_msg; ?>');
        history.go(-1);
        <?php } ?>
        if ($("#previousLink").val() == "") {
            referrer = document.referrer;
            //alert(referrer);
        } else {
            referrer = $("#previousLink").val();
        }
        if (referrer == "") {
            referrer = "vehicles.php";
        } else {
            $(".backlink").val(referrer);
        }
        $(".back_link").attr('href', referrer);
        var commisionVal = $("#eMaterialCommision").val();
        //showHidePercentage(commisionVal);
    });
    $(function () {
        var value = $("#vCategory").val();
        if (value > 0) {
            $(".epricetype,.eMaterialCommision").hide();
        } else {
            $(".epricetype,.eMaterialCommision").show();
        }
    });
</script>
<script type="text/javascript">
    $("#vtype").submit(function (event) {
        <?php if ($MODULES_OBJ->isEnableAppHomePageListView() && $sub_action != "sub_category" && !$MODULES_OBJ->isEnableAppHomeScreenLayoutV1()) { ?>
        if ($("#iconcatview").prop("checked") == false && $("#bannercatview").prop("checked") == false && $("#listcatview").prop("checked") == false) {
            alert("Select at least one category view type");
            return false;
        }
        <?php } ?>

        <?php if($MODULES_OBJ->isEnableVideoConsultingService('Yes')) { ?>
        if ($('#eVideoConsultEnable').is(':checked')) {
            if ($('#eVideoConsultServiceCharge').val().trim() == "" || $('#eVideoConsultServiceCharge').val().trim() == 0) {
                alert("Please enter video consultation service charge.");
                return false;
            }
            if ($('#fCommissionVideoConsult').val().trim() == "" || $('#fCommissionVideoConsult').val().trim() == 0) {
                alert("Please enter video consultation commission.");
                return false;
            }
        }
        <?php } ?>
    });
    $(document).ready(function () {
        $('input[name="eShowType"]').click(function () {
            var inputValue = $(this).attr("value");
            var targetBox = $("." + inputValue);
            if (inputValue == "Icon-Banner") {
                $(".Icon,.Banner").show();
                $(".bannerbutton").hide();
            } else if (inputValue == "Banner") {
                $(".imagebox").not(targetBox).hide();
                $(targetBox).show();
                $(".bannerbutton").hide();
                <?php /*if($tBannerButtonTextdefault!='') { ?>
                        document.getElementById("<?php echo $tBannerButtonTextdefault; ?>").setAttribute('required', 'required');
                    <? }*/ ?>
            } else {
                $(".imagebox").not(targetBox).hide();
                $(targetBox).show();
                $(".bannerbutton").hide();
                <?php /*if($tBannerButtonTextdefault!='') { ?>
                        document.getElementById("<?php echo $tBannerButtonTextdefault; ?>").removeAttribute('required', 'required');
                    <? }*/ ?>
            }
        });
        $('input[name="eCatViewType[]"]').click(function () {
            configCatHomePageImage();
        });
        var checkvalue = $('input[name="eShowType"]:checked').val();
        if (typeof checkvalue === "undefined") {
            var checkvalue = $('input[name="eShowType"]').val();
        }
        <?php if (($MODULES_OBJ->isEnableAppHomePageListView() && $sub_action != "sub_category") || $THEME_OBJ->isDeliveryKingXv2ThemeActive() == "Yes") { ?>
        configCatHomePageImage();
        checkvalue = "";
        <?php } ?>
        if (checkvalue != '') {
            var targetBox1 = checkvalue;
            if (targetBox1 == 'Icon') {
                $(".Icon").show();
                $(".Banner").hide();
                //$("#vtype").attr('novalidate', 'novalidate');
                $(".bannerbutton").hide(); // Comment By HJ On 23-01-2019 For Prevent JS Error Like : An invalid form control with name='tBannerButtonText_EN' is not focusable.
                <?php if ($tBannerButtonTextdefault != '') { ?>
                document.getElementById("<?php echo $tBannerButtonTextdefault; ?>").removeAttribute('required', 'required');
                <? } ?>
            } else if (targetBox1 == 'Banner') {
                $(".Icon").hide();
                $(".Banner").show();
                $(".bannerbutton").hide();
                <?php /*if($tBannerButtonTextdefault!='') { ?>
                document.getElementById("<?php echo $tBannerButtonTextdefault; ?>").setAttribute('required', 'required');
            <? }*/ ?>
            } else if (targetBox1 == "Icon-Banner") {
                $(".Icon,.Banner").show();
                $(".bannerbutton").hide();
                <?php /*if($tBannerButtonTextdefault!='') { ?>
                        document.getElementById("<?php echo $tBannerButtonTextdefault; ?>").setAttribute('required', 'required');
                    <? }*/ ?>
            } else {
                $(".Icon").show();
                $(".Banner,.bannerbutton").hide();
                <?php /*if($tBannerButtonTextdefault!='') { ?>
                        document.getElementById("<?php echo $tBannerButtonTextdefault; ?>").removeAttribute('required', 'required');
                    <? }*/ ?>
            }
        }
        $('textarea:not([name^=tProofNoteValue_], #tProofNote, #tProofNoteDriver, #tProofNoteStore, #tProofNoteUserHidden, #tProofNoteDriverHidden, tProofNoteStoreHidden)').trigger('keyup');

        <?php if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV2()) { ?>
        $('#ePromoteBanner').trigger('change');
        <?php } ?>
        <?php if ($THEME_OBJ->isPXCProThemeActive() == "Yes") { ?>
        $('.Banner').show();
        <?php } ?>

        <?php if(strtoupper(ONLYDELIVERALL) == "YES") { ?>
            configCatHomePageImage();
        <?php } ?>
    });
    $(document).on("keypress keyup blur paste keydown", '#tProofNote, #tProofNoteDriver, #tProofNoteStore, #vCategory_Default, #tBannerButtonText_Default, #tListDescription_Default, #tCategoryDesc_Default, #tDescription_Default, #tPromoteBannerTitle_Default', function (event) {
        event.preventDefault();
    });
    $(document).on('keyup', 'textarea:not([name^=tProofNoteValue_], #tProofNote, #tProofNoteDriver, #tProofNoteStore, [name^=tCategoryDesc_], [name^=tDescription_], #tProofNoteUserHidden, #tProofNoteDriverHidden, #tProofNoteStoreHidden)', function (e) {
        var tval = $(this).val(),
            tlength = tval.length,
            set = 250,
            remain = parseInt(set - tlength);
        if (tlength > 0) {
            $(this).closest('.desc-block').find('.desc_counter').text(remain + "/250");
            if (remain <= 0) {
                $(this).val((tval).substring(0, set));
                $(this).closest('.desc-block').find('.desc_counter').text("0/250");
                return false;
            }
        } else {
            $(this).closest('.desc-block').find('.desc_counter').text("250/250");
            return false;
        }
    });

    function configCatHomePageImage() {
        var isEnableNewAppHomeScreenLayout = '<?= $MODULES_OBJ->isEnableAppHomeScreenLayout() ? 'Yes' : 'No' ?>';
        $(".Icon,.Banner,.List").hide();
        if ($("#iconcatview").prop("checked") == true || isEnableNewAppHomeScreenLayout == 'Yes') {
            $(".Icon").show();
        }
        if ($("#bannercatview").prop("checked") == true) {
            $(".Banner").show();
            $(".bannerbutton").hide();
        } else {
            $(".bannerbutton").hide();
        }
        if ($("#listcatview").prop("checked") == true) {
            $(".List,.tListDescription").show();
        } else {
            $(".tListDescription").hide();
        }

        <?php if ($MODULES_OBJ->isEnableAppHomeScreenLayout()) { ?>
        $('#CatViewType').hide();
        <?php } ?>

        <?php if ($THEME_OBJ->isDeliveryKingXv2ThemeActive() == "Yes") { ?>
        $('#CatViewType').show();
        <?php } ?>
    }

    function minmax(value, min, max) {
        if (parseInt(value) < min || isNaN(value)) {
            return 0;
        } else if (parseInt(value) > max) {
            return 100;
        } else {
            return value;
        }
    }

    function showHidePercentage(commision) {
        $("#commisionperdiv").hide();
        if (commision == "Yes") {
            $("#commisionperdiv").show();
        }
    }

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 47 && charCode < 58 || charCode == 46 || charCode == 127 || charCode == 8) {
            return true;
        }
        return false;
    }

    //added by SP 27-06-2019 start
    var timeLimit = $("#iCancellationTimeLimit").val();
    if (timeLimit != '' && timeLimit != null && timeLimit.trim() == '') {
        $("#fCancellationFare").val('');
    }
    var timeLimit = $("#iWaitingFeeTimeLimit").val();
    if (timeLimit != '' && timeLimit != null && timeLimit.trim() == '') {
        $("#fWaitingFees").val('');
    }

    function checkcancellationfare(idval) {
        var timeLimit = $("#" + idval).val();
        if (timeLimit.trim() == '') {
            document.getElementById(idval).focus();
            $("#" + idval).val('');
            return false;
        }
    }

    function checkblanktimelimit(idval, idcanval) {
        var timeLimit = $("#" + idval).val();
        if (timeLimit.trim() == '') {
            $("#" + idcanval).val('');
        }
    }

    //added by SP 27-06-2019 end
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('.logo-preview-img').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#vLogo").change(function () {
        readURL(this);
    });

    function editProofNote(user_type) {
        $('#id_proof_note_lang_title').text("ID Proof Note For " + user_type);
        $('#id_proof_note_for').val(user_type);
        if (user_type == "user") {
            var tProofNote = $('#tProofNoteUserHidden').text();
            var tProofNoteName = "tProofNote_";
        } else if (user_type == "driver") {
            var tProofNote = $('#tProofNoteDriverHidden').text();
            var tProofNoteName = "tProofNoteDriver_";
        } else {
            var tProofNote = $('#tProofNoteStoreHidden').text();
            var tProofNoteName = "tProofNoteStore_";
        }
        if (tProofNote.trim() != "") {
            tProofNote = JSON.parse(tProofNote);
            $('[name^=tProofNoteValue_]').each(function () {
                var lang_code = $(this).data('lang');
                $(this).val(tProofNote[tProofNoteName + lang_code]);
            });
        }
        $('#id_proof_note_lang .modal-body, #id_proof_note_lang textarea').animate({
            scrollTop: 0
        }, 'fast');
        $('#id_proof_note_lang').modal('show');
    }

    function saveProofNote() {
        if ($('#tProofNoteValue_<?= $default_lang ?>').val() == "") {
            $('#tProofNoteValue_<?= $default_lang ?>_error').show();
            $('#tProofNoteValue_<?= $default_lang ?>').focus();
            clearInterval(myVar);
            myVar = setTimeout(function () {
                $('#tProofNoteValue_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }
        var user_type = $('#id_proof_note_for').val();
        if (user_type == "user") {
            var tProofNoteMain = $('#tProofNote');
            var tProofNote = $('#tProofNoteUserHidden');
            var tProofNoteName = "tProofNote_";
            var tProofNoteNameLang = "tProofNote_<?= $default_lang ?>";
        } else if (user_type == "driver") {
            var tProofNoteMain = $('#tProofNoteDriver');
            var tProofNote = $('#tProofNoteDriverHidden');
            var tProofNoteName = "tProofNoteDriver_";
            var tProofNoteNameLang = "tProofNoteDriver_<?= $default_lang ?>";
        } else {
            var tProofNoteMain = $('#tProofNoteStore');
            var tProofNote = $('#tProofNoteStoreHidden');
            var tProofNoteName = "tProofNoteStore_";
            var tProofNoteNameLang = "tProofNoteStore_<?= $default_lang ?>";
        }
        jsonObj = {};
        $('[name^=tProofNoteValue_]').each(function () {
            var lang_code = $(this).data('lang');
            jsonObj[tProofNoteName + lang_code] = $(this).val();
        });
        tProofNoteMain.text(jsonObj[tProofNoteNameLang]);
        tProofNote.text(JSON.stringify(jsonObj));
        $('#id_proof_note_lang').modal('hide');
    }

    function displayProofNoteSection(elem) {
        if (elem.value == "Yes") {
            $('#proof_note_section').show();
        } else {
            $('#proof_note_section').hide();
        }
    }

    function editCategory(action) {
        $('#category_action').html(action);
        $('#vCategory_Modal').modal('show');
    }

    function saveCategory() {
        if ($('#vCategory_<?= $default_lang ?>').val() == "" || $('#vCategory_<?= $default_lang ?>').val().trim() == "") {
            $('#vCategory_<?= $default_lang ?>_error').show();
            $('#vCategory_<?= $default_lang ?>').focus();
            clearInterval(myVar);
            myVar = setTimeout(function () {
                $('#vCategory_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vCategory_Default').val($('#vCategory_<?= $default_lang ?>').val());
        $('#vCategory_Modal').modal('hide');
    }

    function editBannerButtonText(action) {
        $('#banner_button_action').html(action);
        $('#tBannerButtonText_Modal').modal('show');
    }

    function saveBannerButtonText() {
        if ($('#tBannerButtonText_<?= $default_lang ?>').val() == "") {
            $('#tBannerButtonText_<?= $default_lang ?>_error').show();
            $('#tBannerButtonText_<?= $default_lang ?>').focus();
            clearInterval(myVar);
            myVar = setTimeout(function () {
                $('#tBannerButtonText_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }
        $('#tBannerButtonText_Default').val($('#tBannerButtonText_<?= $default_lang ?>').val());
        $('#tBannerButtonText_Modal').modal('hide');
    }

    function editListDescription(action) {
        $('#list_desc_action').html(action);
        $('#tListDescription_Modal').modal('show');
    }

    function saveListDescription() {
        if ($('#tListDescription_<?= $default_lang ?>').val() == "") {
            $('#tListDescription_<?= $default_lang ?>_error').show();
            $('#tListDescription_<?= $default_lang ?>').focus();
            clearInterval(myVar);
            myVar = setTimeout(function () {
                $('#tListDescription_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }
        $('#tListDescription_Default').val($('#tListDescription_<?= $default_lang ?>').val());
        $('#tListDescription_Modal').modal('hide');
    }

    function editCategoryDescription(action) {
        $('#category_desc_action').html(action);
        $('#tCategoryDesc_Modal').modal('show');
    }

    function saveCategoryDescription() {
        if ($('#tCategoryDesc_<?= $default_lang ?>').val() == "") {
            $('#tCategoryDesc_<?= $default_lang ?>_error').show();
            $('#tCategoryDesc_<?= $default_lang ?>').focus();
            clearInterval(myVar);
            myVar = setTimeout(function () {
                $('#tCategoryDesc_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }
        $('#tCategoryDesc_Default').val($('#tCategoryDesc_<?= $default_lang ?>').val());
        $('#tCategoryDesc_Modal').modal('hide');
    }

    function editServiceDescription(action) {
        $('#service_desc_action').html(action);
        $('#tDescription_Modal').modal('show');
    }

    function saveServiceDescription() {
        if ($('#tDescription_<?= $default_lang ?>').val() == "") {
            $('#tDescription_<?= $default_lang ?>_error').show();
            $('#tDescription_<?= $default_lang ?>').focus();
            clearInterval(myVar);
            myVar = setTimeout(function () {
                $('#tDescription_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }
        $('#tDescription_Default').val($('#tDescription_<?= $default_lang ?>').val());
        $('#tDescription_Modal').modal('hide');
    }

    function editServiceCatTitleHomepage(action) {
        $('#service_desc_action').html(action);
        $('#ServiceCatTitle_Modal').modal('show');
    }

    function editServiceCatSubTitleHomepage(action) {
        $('#ServiceCatSubTitle_Modal').modal('show');
    }

    function saveServiceCatTitleHomepage() {
        if ($('#vServiceCatTitleHomepage_<?= $default_lang ?>').val() == "") {
            $('#vServiceCatTitleHomepage_<?= $default_lang ?>_error').show();
            $('#vServiceCatTitleHomepage_<?= $default_lang ?>').focus();
            clearInterval(myVar);
            myVar = setTimeout(function () {
                $('#vServiceCatTitleHomepage_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }
        $('#vServiceCatTitleHomepage_Default').val($('#vServiceCatTitleHomepage_<?= $default_lang ?>').val());
        $('#ServiceCatTitle_Modal').modal('hide');
    }

    function saveServiceCatSubTitleHomepage() {
        if ($('#vServiceCatSubTitleHomepage_<?= $default_lang ?>').val() == "") {
            $('#vServiceCatSubTitleHomepage_<?= $default_lang ?>_error').show();
            $('#vServiceCatSubTitleHomepage_<?= $default_lang ?>').focus();
            clearInterval(myVar);
            myVar = setTimeout(function () {
                $('#vServiceCatSubTitleHomepage_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }
        $('#vServiceCatSubTitleHomepage_Default').val($('#vServiceCatSubTitleHomepage_<?= $default_lang ?>').val());
        $('#ServiceCatSubTitle_Modal').modal('hide');
    }

    $('#ePromoteBanner').change(function () {
        if ($(this).is(":checked")) {
            $('.PromoteBanner').show();
        } else {
            $('.PromoteBanner').hide();
        }
    });

    function editPromoteBannerTitle(action) {
        $('#promote_banner_title_action').html(action);
        $('#tPromoteBannerTitle_Modal').modal('show');
    }

    function savePromoteBannerTitle() {
        if ($('#tPromoteBannerTitle_<?= $default_lang ?>').val() == "") {
            $('#tPromoteBannerTitle_<?= $default_lang ?>_error').show();
            $('#tPromoteBannerTitle_<?= $default_lang ?>').focus();
            clearInterval(myVar);
            myVar = setTimeout(function () {
                $('#tPromoteBannerTitle_<?= $default_lang ?>_error').hide();
            }, 5000);
            return false;
        }
        $('#tPromoteBannerTitle_Default').val($('#tPromoteBannerTitle_<?= $default_lang ?>').val());
        $('#tPromoteBannerTitle_Modal').modal('hide');
    }

    $('#eVideoConsultEnable').change(function () {
        if ($(this).is(':checked')) {
            $('#vc_charge').show();
        } else {
            $('#vc_charge').hide();
        }
    });

    $('#banner_lang').change(function () {
        var curr_lang = $(this).val();
        var url = window.location.href;
        url = url.replace(/&banner_lang=[A-Z]+/, '');
        window.location.href = url + '&banner_lang=' + curr_lang;
    });
</script>
</body>
<!-- END BODY-->
</html>