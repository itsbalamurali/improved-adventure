<?php
include_once '../common.php';

if ('RIDE' === strtoupper($APP_TYPE) || 'RIDE-DELIVERY' === strtoupper($APP_TYPE) || 'YES' === strtoupper($THEME_OBJ->isPXCProThemeActive())) {
    include_once 'manage_app_home_screen_other.php';

    exit;
}

require_once TPATH_CLASS.'Imagecrop.class.php';
$id = $_REQUEST['id'] ?? ''; // iUniqueId
if (!$userObj->hasPermission('manage-app-home-screen-view')) {
    $userObj->redirect();
}
$default_lang = $LANG_OBJ->FetchSystemDefaultLang();
$script = 'ManageAppHomePage';
$tbl_name = 'app_home_screen_view';
$db_master = $obj->MySQLSelect('SELECT * FROM `language_master` ORDER BY `iDispOrder`');
$count_all = count($db_master);
if (isset($_POST['submit'])) { // form submit
    if (SITE_TYPE === 'Demo') {
        $_SESSION['success'] = 2;
        header('Location:manage_app_home_screen.php');

        exit;
    }
    if ('Edit' === $action && !$userObj->hasPermission('edit-app-home-screen-view')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to update App Home Screen View.';
        header('Location:app_home_screen_view.php');

        exit;
    }
    // if($CSRF_OBJ->validate()) {
    for ($i = 0; $i < count($db_master); ++$i) {
        $vIntroTitle = '';
        if (isset($_POST['vIntroTitle_'.$db_master[$i]['vCode']])) {
            $vIntroTitle = $_POST['vIntroTitle_'.$db_master[$i]['vCode']];
        }
        $vIntroTitleArr['vTitle_'.$db_master[$i]['vCode']] = $vIntroTitle;
        $vIntroSubTitle = '';
        if (isset($_POST['vIntroSubTitle_'.$db_master[$i]['vCode']])) {
            $vIntroSubTitle = $_POST['vIntroSubTitle_'.$db_master[$i]['vCode']];
        }
        $vIntroSubTitleArr['vSubtitle_'.$db_master[$i]['vCode']] = $vIntroSubTitle;
        $vOtherServiceTitle = '';
        if (isset($_POST['vOtherServiceTitle_'.$db_master[$i]['vCode']])) {
            $vOtherServiceTitle = $_POST['vOtherServiceTitle_'.$db_master[$i]['vCode']];
        }
        $vOtherServiceTitleArr['vTitle_'.$db_master[$i]['vCode']] = $vOtherServiceTitle;
        $vOtherServiceSubTitle = '';
        if (isset($_POST['vOtherServiceSubTitle_'.$db_master[$i]['vCode']])) {
            $vOtherServiceSubTitle = $_POST['vOtherServiceSubTitle_'.$db_master[$i]['vCode']];
        }
        $vOtherServiceSubTitleArr['vSubtitle_'.$db_master[$i]['vCode']] = $vOtherServiceSubTitle;

        $vBidTitle = '';
        if (isset($_POST['vBidTitle_'.$db_master[$i]['vCode']])) {
            $vBidTitle = $_POST['vBidTitle_'.$db_master[$i]['vCode']];
        }
        $vBidTitleArr['vTitle_'.$db_master[$i]['vCode']] = $vBidTitle;
        $vBidSubTitle = '';
        if (isset($_POST['vBidSubTitle_'.$db_master[$i]['vCode']])) {
            $vBidSubTitle = $_POST['vBidSubTitle_'.$db_master[$i]['vCode']];
        }
        $vBidSubTitleArr['vSubtitle_'.$db_master[$i]['vCode']] = $vBidSubTitle;

        $vBuySellTitle = '';
        if (isset($_POST['vBuySellTitle_'.$db_master[$i]['vCode']])) {
            $vBuySellTitle = $_POST['vBuySellTitle_'.$db_master[$i]['vCode']];
        }
        $vBuySellTitleArr['vTitle_'.$db_master[$i]['vCode']] = $vBuySellTitle;
        $vTrackServiceTitle = '';
        if (isset($_POST['vTrackServiceTitle_'.$db_master[$i]['vCode']])) {
            $vTrackServiceTitle = $_POST['vTrackServiceTitle_'.$db_master[$i]['vCode']];
        }
        $vTrackServiceTitleArr['vTitle_'.$db_master[$i]['vCode']] = $vTrackServiceTitle;
        $vMedicalServiceTitle = '';
        if (isset($_POST['vMedicalServiceTitle_'.$db_master[$i]['vCode']])) {
            $vMedicalServiceTitle = $_POST['vMedicalServiceTitle_'.$db_master[$i]['vCode']];
        }
        $vMedicalServiceTitleArr['vTitle_'.$db_master[$i]['vCode']] = $vMedicalServiceTitle;
        $vNearbyServiceTitle = '';
        if (isset($_POST['vNearbyServiceTitle_'.$db_master[$i]['vCode']])) {
            $vNearbyServiceTitle = $_POST['vNearbyServiceTitle_'.$db_master[$i]['vCode']];
        }
        $vNearbyServiceTitleArr['vTitle_'.$db_master[$i]['vCode']] = $vNearbyServiceTitle;

        $vIntroBuyTitle = '';
        if (isset($_POST['vIntroBuyTitle_'.$db_master[$i]['vCode']])) {
            $vIntroBuyTitle = $_POST['vIntroBuyTitle_'.$db_master[$i]['vCode']];
        }
        $vIntroBuyTitleArr['vTitle_'.$db_master[$i]['vCode']] = $vIntroBuyTitle;
        $vIntroBuySubTitle = '';
        if (isset($_POST['vIntroBuySubTitle_'.$db_master[$i]['vCode']])) {
            $vIntroBuySubTitle = $_POST['vIntroBuySubTitle_'.$db_master[$i]['vCode']];
        }
        $vIntroBuySubTitleArr['vSubtitle_'.$db_master[$i]['vCode']] = $vIntroBuySubTitle;
    }
    $jsonIntroTitle = getJsonFromAnArr($vIntroTitleArr);
    $jsonIntroSubTitle = getJsonFromAnArr($vIntroSubTitleArr);
    $Data_update_intro = [];
    $Data_update_intro['vTitle'] = $jsonIntroTitle;
    $Data_update_intro['vSubtitle'] = $jsonIntroSubTitle;
    $where = " eViewType = 'IntroView' ";
    $obj->MySQLQueryPerform($tbl_name, $Data_update_intro, 'update', $where);

    $jsonIntroBuyTitle = getJsonFromAnArr($vIntroBuyTitleArr);
    $jsonIntroBuySubTitle = getJsonFromAnArr($vIntroBuySubTitleArr);
    $Data_update_introBuy = [];
    $Data_update_introBuy['vTitle'] = $jsonIntroBuyTitle;
    $Data_update_introBuy['vSubtitle'] = $jsonIntroBuySubTitle;
    $where = " eViewType = 'IntroViewBuy' ";
    $obj->MySQLQueryPerform($tbl_name, $Data_update_introBuy, 'update', $where);

    if ($MODULES_OBJ->isRideFeatureAvailable() || $MODULES_OBJ->isDeliveryFeatureAvailable() || $MODULES_OBJ->isEnableAnywhereDeliveryFeature() || $MODULES_OBJ->isDeliverAllFeatureAvailable() || $MODULES_OBJ->isEnableVideoConsultingService() || $MODULES_OBJ->isUberXFeatureAvailable()) {
        $Data_update_grid = [];
        $Data_update_grid['tGridServices'] = implode(',', $_POST['grid_service']);
        // $Data_update_grid['iDisplayOrder'] = $_POST['iDisplayOrderGridView'];
        $where = " eViewType = 'GridView' ";
        $obj->MySQLQueryPerform($tbl_name, $Data_update_grid, 'update', $where);
    }

    if ($MODULES_OBJ->isEnableBiddingServices()) {
        $Data_update_bid = [];
        $jsonBidTitle = getJsonFromAnArr($vBidTitleArr);
        $jsonBidSubTitle = getJsonFromAnArr($vBidSubTitleArr);
        $Data_update_bid['vTitle'] = $jsonBidTitle;
        $Data_update_bid['vSubtitle'] = $jsonBidSubTitle;

        $where = " eServiceType = 'Bidding' ";
        $obj->MySQLQueryPerform($tbl_name, $Data_update_bid, 'update', $where);
    }
    if ($MODULES_OBJ->isUberXFeatureAvailable()) {
        $jsonOtherServiceTitle = getJsonFromAnArr($vOtherServiceTitleArr);
        $jsonOtherServiceSubTitle = getJsonFromAnArr($vOtherServiceSubTitleArr);
        $Data_update_otherservice = [];
        if ('Yes' === $_POST['saveOtherServices']) {
            $iVehicleCategoryIdArr = $_POST['iVehicleCategoryId'];
            $iVehicleCategoryIdValArr = $_POST['iVehicleCategoryIdVal'];
            $iDisplayOrderOtherServiceArr = $_POST['iDisplayOrderOtherServiceArr'];
            $vImageOldArr = $_POST['vOtherServiceImageOld'];
            $db_data_ufx = $obj->MySQLSelect("SELECT tServiceDetails FROM {$tbl_name} WHERE eServiceType = 'UberX' ");
            $tServiceDetails = [];
            if (!empty($db_data_ufx[0]['tServiceDetails'])) {
                $tServiceDetails = json_decode($db_data_ufx[0]['tServiceDetails'], true);
                foreach ($tServiceDetails as $serviceDetail) {
                    if (!in_array($serviceDetail['iVehicleCategoryId'], $iVehicleCategoryIdArr, true)) {
                        $tServiceDetails['iVehicleCategoryId_'.$serviceDetail['iVehicleCategoryId']]['eStatus'] = 'Inactive';
                    }
                }
            }
            foreach ($iVehicleCategoryIdArr as $iVehicleCategoryId) {
                $orderKey = array_search($iVehicleCategoryId, $_POST['iVehicleCategoryIdVal'], true);
                $iDisplayOrderService = $iDisplayOrderOtherServiceArr[$orderKey];
                $vImage = '';
                $image_object = $_FILES['vOtherServiceImage']['tmp_name'][$orderKey];
                $image_name = $_FILES['vOtherServiceImage']['name'][$orderKey];
                if ('' !== $image_name) {
                    $filecheck = basename($_FILES['vOtherServiceImage']['name'][$orderKey]);
                    $fileextarr = explode('.', $filecheck);
                    $ext = strtolower($fileextarr[count($fileextarr) - 1]);
                    $flag_error = 0;
                    if ('jpg' !== $ext && 'gif' !== $ext && 'png' !== $ext && 'jpeg' !== $ext && 'bmp' !== $ext) {
                        $flag_error = 1;
                        $var_msg = 'Not valid image extension of .jpg, .jpeg, .gif, .png, .bmp';
                    }
                    if (1 === $flag_error) {
                        $_SESSION['success'] = '3';
                        $_SESSION['var_msg'] = $var_msg;
                        header('Location:manage_app_home_screen_new.php');

                        exit;
                    }
                    $Photo_Gallery_folder = $tconfig['tsite_upload_app_home_screen_images_path'].'AppHomeScreen/';
                    if (!is_dir($Photo_Gallery_folder)) {
                        mkdir($Photo_Gallery_folder, 0777);
                        chmod($Photo_Gallery_folder, 0777);
                    }
                    $img = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, '', 'jpg,png,gif,jpeg,bmp');
                    $vImage = $img[0];
                    if (!empty($vImageOldArr[$orderKey]) && file_exists($Photo_Gallery_folder.$vImageOldArr[$orderKey])) {
                        unlink($Photo_Gallery_folder.$vImageOldArr[$orderKey]);
                    }
                }
                $tServiceDetails['iVehicleCategoryId_'.$iVehicleCategoryId]['iVehicleCategoryId'] = $iVehicleCategoryId;
                $tServiceDetails['iVehicleCategoryId_'.$iVehicleCategoryId]['iDisplayOrder'] = $iDisplayOrderService;
                $tServiceDetails['iVehicleCategoryId_'.$iVehicleCategoryId]['eStatus'] = 'Active';
                if (!empty($vImage)) {
                    $tServiceDetails['iVehicleCategoryId_'.$iVehicleCategoryId]['vImage'] = $vImage;
                } else {
                    $tServiceDetails['iVehicleCategoryId_'.$iVehicleCategoryId]['vImage'] = $vImageOldArr[$orderKey];
                }
            }
            // echo "<pre>"; print_r($tServiceDetails); exit;
            $Data_update_otherservice['tServiceDetails'] = json_encode($tServiceDetails, JSON_UNESCAPED_UNICODE);
        }
        $Data_update_otherservice['vTitle'] = $jsonOtherServiceTitle;
        $Data_update_otherservice['vSubtitle'] = $jsonOtherServiceSubTitle;
        // $Data_update_otherservice['iDisplayOrder'] = $_POST['iDisplayOrderOtherService'];
        $where = " eServiceType = 'UberX' ";
        $obj->MySQLQueryPerform($tbl_name, $Data_update_otherservice, 'update', $where);
    }
    /*$Data_update_promote = array();
    $Data_update_promote['iDisplayOrder'] = $_POST['iDisplayOrderPromoBannerView'];
    $where = " eServiceType = 'PromotionalBanner' ";
    $obj->MySQLQueryPerform($tbl_name, $Data_update_promote, 'update', $where);
    if ($MODULES_OBJ->isEnableAnywhereDeliveryFeature('Yes', 'Genie')) {
        $Data_update_genie = array();
        $Data_update_genie['iDisplayOrder'] = $_POST['iDisplayOrderGenieBannerView'];
        $where = " eServiceType = 'Genie' ";
        $obj->MySQLQueryPerform($tbl_name, $Data_update_genie, 'update', $where);
    }
    if ($MODULES_OBJ->isEnableVideoConsultingService()) {
        $Data_update_vc = array();
        $Data_update_vc['iDisplayOrder'] = $_POST['iDisplayOrderVCBannerView'];
        $where = " eServiceType = 'VideoConsult' ";
        $obj->MySQLQueryPerform($tbl_name, $Data_update_vc, 'update', $where);
    }
    if ($MODULES_OBJ->isEnableBiddingServices()) {
        $Data_update_bidding = array();
        $Data_update_bidding['iDisplayOrder'] = $_POST['iDisplayOrderBidBannerView'];
        $where = " eServiceType = 'Bidding' ";
        $obj->MySQLQueryPerform($tbl_name, $Data_update_bidding, 'update', $where);
    }
    if ($MODULES_OBJ->isEnableRideShareService()) {
        $Data_update_rideshare = array();
        $Data_update_rideshare['iDisplayOrder'] = $_POST['iDisplayOrderRideShare'];
        $where = " eServiceType = 'RideShare' ";
        $obj->MySQLQueryPerform($tbl_name, $Data_update_rideshare, 'update', $where);
    }
    if ($MODULES_OBJ->isEnableTrackAnyServiceFeature()) {
        $jsonTrackServiceTitle = getJsonFromAnArr($vTrackServiceTitleArr);
        $Data_update_trackservice = array();
        $Data_update_trackservice['vTitle'] = $jsonTrackServiceTitle;
        $Data_update_trackservice['iDisplayOrder'] = $_POST['iDisplayOrderTrackService'];
        $where = " eServiceType = 'TrackAnyService' ";
        $obj->MySQLQueryPerform($tbl_name, $Data_update_trackservice, 'update', $where);
    }*/

    if ($MODULES_OBJ->isEnableRentItemService() || $MODULES_OBJ->isEnableRentCarsService() || $MODULES_OBJ->isEnableRentEstateService()) {
        $jsonBuySellTitle = getJsonFromAnArr($vBuySellTitleArr);
        $Data_update_buysell = [];
        $Data_update_buysell['vTitle'] = $jsonBuySellTitle;
        // $Data_update_buysell['iDisplayOrder'] = $_POST['iDisplayOrderBuySell'];
        $where = " eServiceType = 'BuySell' ";
        $obj->MySQLQueryPerform($tbl_name, $Data_update_buysell, 'update', $where);
    }

    if ($MODULES_OBJ->isEnableMedicalServices()) {
        $jsonMedicalServiceTitle = getJsonFromAnArr($vMedicalServiceTitleArr);
        $Data_update_medicalservice = [];
        $db_data_ms = $obj->MySQLSelect("SELECT tServiceDetails FROM {$tbl_name} WHERE eServiceType = 'MedicalServices' ");
        $tServiceDetails = [];
        if (!empty($db_data_ms[0]['tServiceDetails'])) {
            $tServiceDetails = json_decode($db_data_ms[0]['tServiceDetails'], true);
        }
        if ('Yes' === $_POST['saveBookServiceMS']) {
            $iVehicleCategoryIdArr = $_POST['iVehicleCategoryIdBS'];
            $iVehicleCategoryIdValArr = $_POST['iVehicleCategoryIdValBS'];
            $iDisplayOrderOtherServiceArr = $_POST['iDisplayOrderBookServiceMSArr'];
            $vImageOldArr = $_POST['vBookServiceMSImageOld'];
            if (isset($tServiceDetails['BookService'])) {
                foreach ($tServiceDetails['BookService'] as $serviceDetail) {
                    if (!in_array($serviceDetail['iVehicleCategoryId'], $iVehicleCategoryIdArr, true)) {
                        $tServiceDetails['BookService']['iVehicleCategoryId_'.$serviceDetail['iVehicleCategoryId']]['eStatus'] = 'Inactive';
                    }
                }
            }
            foreach ($iVehicleCategoryIdArr as $iVehicleCategoryId) {
                $orderKey = array_search($iVehicleCategoryId, $_POST['iVehicleCategoryIdValBS'], true);
                $iDisplayOrderService = $iDisplayOrderOtherServiceArr[$orderKey];
                $vImage = '';
                $image_object = $_FILES['vBookServiceMSImage']['tmp_name'][$orderKey];
                $image_name = $_FILES['vBookServiceMSImage']['name'][$orderKey];
                if ('' !== $image_name) {
                    $filecheck = basename($_FILES['vBookServiceMSImage']['name'][$orderKey]);
                    $fileextarr = explode('.', $filecheck);
                    $ext = strtolower($fileextarr[count($fileextarr) - 1]);
                    $flag_error = 0;
                    if ('jpg' !== $ext && 'gif' !== $ext && 'png' !== $ext && 'jpeg' !== $ext && 'bmp' !== $ext) {
                        $flag_error = 1;
                        $var_msg = 'Not valid image extension of .jpg, .jpeg, .gif, .png, .bmp';
                    }
                    if (1 === $flag_error) {
                        $_SESSION['success'] = '3';
                        $_SESSION['var_msg'] = $var_msg;
                        header('Location:manage_app_home_screen_new.php');

                        exit;
                    }
                    $Photo_Gallery_folder = $tconfig['tsite_upload_app_home_screen_images_path'].'AppHomeScreen/';
                    if (!is_dir($Photo_Gallery_folder)) {
                        mkdir($Photo_Gallery_folder, 0777);
                        chmod($Photo_Gallery_folder, 0777);
                    }
                    $img = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, '', 'jpg,png,gif,jpeg,bmp');
                    $vImage = $img[0];
                    if (!empty($vImageOldArr[$orderKey]) && file_exists($Photo_Gallery_folder.$vImageOldArr[$orderKey])) {
                        unlink($Photo_Gallery_folder.$vImageOldArr[$orderKey]);
                    }
                }
                $tServiceDetails['BookService']['iVehicleCategoryId_'.$iVehicleCategoryId]['iVehicleCategoryId'] = $iVehicleCategoryId;
                $tServiceDetails['BookService']['iVehicleCategoryId_'.$iVehicleCategoryId]['iDisplayOrder'] = $iDisplayOrderService;
                $tServiceDetails['BookService']['iVehicleCategoryId_'.$iVehicleCategoryId]['eStatus'] = 'Active';
                if (!empty($vImage)) {
                    $tServiceDetails['BookService']['iVehicleCategoryId_'.$iVehicleCategoryId]['vImage'] = $vImage;
                } else {
                    $tServiceDetails['BookService']['iVehicleCategoryId_'.$iVehicleCategoryId]['vImage'] = $vImageOldArr[$orderKey];
                }
            }
            // echo "<pre>"; print_r($tServiceDetails); exit;
            $Data_update_medicalservice['tServiceDetails'] = json_encode($tServiceDetails, JSON_UNESCAPED_UNICODE);
        }
        if ('Yes' === $_POST['saveVideoConsultMS']) {
            $iVehicleCategoryIdArr = $_POST['iVehicleCategoryIdVC'];
            $iVehicleCategoryIdValArr = $_POST['iVehicleCategoryIdValVC'];
            $iDisplayOrderOtherServiceArr = $_POST['iDisplayOrderVideoConsultMSArr'];
            $vImageOldArr = $_POST['vVideoConsultMSImageOld'];
            if (isset($tServiceDetails['VideoConsult'])) {
                foreach ($tServiceDetails['VideoConsult'] as $serviceDetail) {
                    if (!in_array($serviceDetail['iVehicleCategoryId'], $iVehicleCategoryIdArr, true)) {
                        $tServiceDetails['VideoConsult']['iVehicleCategoryId_'.$serviceDetail['iVehicleCategoryId']]['eStatus'] = 'Inactive';
                    }
                }
            }
            foreach ($iVehicleCategoryIdArr as $iVehicleCategoryId) {
                $orderKey = array_search($iVehicleCategoryId, $_POST['iVehicleCategoryIdValVC'], true);
                $iDisplayOrderService = $iDisplayOrderOtherServiceArr[$orderKey];
                $vImage = '';
                $image_object = $_FILES['vVideoConsultMSImage']['tmp_name'][$orderKey];
                $image_name = $_FILES['vVideoConsultMSImage']['name'][$orderKey];
                if ('' !== $image_name) {
                    $filecheck = basename($_FILES['vVideoConsultMSImage']['name'][$orderKey]);
                    $fileextarr = explode('.', $filecheck);
                    $ext = strtolower($fileextarr[count($fileextarr) - 1]);
                    $flag_error = 0;
                    if ('jpg' !== $ext && 'gif' !== $ext && 'png' !== $ext && 'jpeg' !== $ext && 'bmp' !== $ext) {
                        $flag_error = 1;
                        $var_msg = 'Not valid image extension of .jpg, .jpeg, .gif, .png, .bmp';
                    }
                    if (1 === $flag_error) {
                        $_SESSION['success'] = '3';
                        $_SESSION['var_msg'] = $var_msg;
                        header('Location:manage_app_home_screen_new.php');

                        exit;
                    }
                    $Photo_Gallery_folder = $tconfig['tsite_upload_app_home_screen_images_path'].'AppHomeScreen/';
                    if (!is_dir($Photo_Gallery_folder)) {
                        mkdir($Photo_Gallery_folder, 0777);
                        chmod($Photo_Gallery_folder, 0777);
                    }
                    $img = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, '', 'jpg,png,gif,jpeg,bmp');
                    $vImage = $img[0];
                    if (!empty($vImageOldArr[$orderKey]) && file_exists($Photo_Gallery_folder.$vImageOldArr[$orderKey])) {
                        unlink($Photo_Gallery_folder.$vImageOldArr[$orderKey]);
                    }
                }
                $tServiceDetails['VideoConsult']['iVehicleCategoryId_'.$iVehicleCategoryId]['iVehicleCategoryId'] = $iVehicleCategoryId;
                $tServiceDetails['VideoConsult']['iVehicleCategoryId_'.$iVehicleCategoryId]['iDisplayOrder'] = $iDisplayOrderService;
                $tServiceDetails['VideoConsult']['iVehicleCategoryId_'.$iVehicleCategoryId]['eStatus'] = 'Active';
                if (!empty($vImage)) {
                    $tServiceDetails['VideoConsult']['iVehicleCategoryId_'.$iVehicleCategoryId]['vImage'] = $vImage;
                } else {
                    $tServiceDetails['VideoConsult']['iVehicleCategoryId_'.$iVehicleCategoryId]['vImage'] = $vImageOldArr[$orderKey];
                }
            }
            // echo "<pre>"; print_r($tServiceDetails); exit;
            $Data_update_medicalservice['tServiceDetails'] = json_encode($tServiceDetails, JSON_UNESCAPED_UNICODE);
        }
        if ('Yes' === $_POST['saveMoreServiceMS']) {
            $iVehicleCategoryIdArr = $_POST['iVehicleCategoryIdMS'];
            $iVehicleCategoryIdValArr = $_POST['iVehicleCategoryIdValMS'];
            $iDisplayOrderOtherServiceArr = $_POST['iDisplayOrderMoreServiceMSArr'];
            $vImageOldArr = $_POST['vMoreServiceMSImageOld'];
            if (isset($tServiceDetails['MoreService'])) {
                foreach ($tServiceDetails['MoreService'] as $serviceDetail) {
                    if (!in_array($serviceDetail['iVehicleCategoryId'], $iVehicleCategoryIdArr, true)) {
                        $tServiceDetails['MoreService']['iVehicleCategoryId_'.$serviceDetail['iVehicleCategoryId']]['eStatus'] = 'Inactive';
                    }
                }
            }
            foreach ($iVehicleCategoryIdArr as $iVehicleCategoryId) {
                $orderKey = array_search($iVehicleCategoryId, $_POST['iVehicleCategoryIdValMS'], true);
                $iDisplayOrderService = $iDisplayOrderOtherServiceArr[$orderKey];
                $vImage = '';
                $image_object = $_FILES['vMoreServiceMSImage']['tmp_name'][$orderKey];
                $image_name = $_FILES['vMoreServiceMSImage']['name'][$orderKey];
                if ('' !== $image_name) {
                    $filecheck = basename($_FILES['vMoreServiceMSImage']['name'][$orderKey]);
                    $fileextarr = explode('.', $filecheck);
                    $ext = strtolower($fileextarr[count($fileextarr) - 1]);
                    $flag_error = 0;
                    if ('jpg' !== $ext && 'gif' !== $ext && 'png' !== $ext && 'jpeg' !== $ext && 'bmp' !== $ext) {
                        $flag_error = 1;
                        $var_msg = 'Not valid image extension of .jpg, .jpeg, .gif, .png, .bmp';
                    }
                    if (1 === $flag_error) {
                        $_SESSION['success'] = '3';
                        $_SESSION['var_msg'] = $var_msg;
                        header('Location:manage_app_home_screen_new.php');

                        exit;
                    }
                    $Photo_Gallery_folder = $tconfig['tsite_upload_app_home_screen_images_path'].'AppHomeScreen/';
                    if (!is_dir($Photo_Gallery_folder)) {
                        mkdir($Photo_Gallery_folder, 0777);
                        chmod($Photo_Gallery_folder, 0777);
                    }
                    $img = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, '', 'jpg,png,gif,jpeg,bmp');
                    $vImage = $img[0];
                    if (!empty($vImageOldArr[$orderKey]) && file_exists($Photo_Gallery_folder.$vImageOldArr[$orderKey])) {
                        unlink($Photo_Gallery_folder.$vImageOldArr[$orderKey]);
                    }
                }
                $tServiceDetails['MoreService']['iVehicleCategoryId_'.$iVehicleCategoryId]['iVehicleCategoryId'] = $iVehicleCategoryId;
                $tServiceDetails['MoreService']['iVehicleCategoryId_'.$iVehicleCategoryId]['iDisplayOrder'] = $iDisplayOrderService;
                $tServiceDetails['MoreService']['iVehicleCategoryId_'.$iVehicleCategoryId]['eStatus'] = 'Active';
                if (!empty($vImage)) {
                    $tServiceDetails['MoreService']['iVehicleCategoryId_'.$iVehicleCategoryId]['vImage'] = $vImage;
                } else {
                    $tServiceDetails['MoreService']['iVehicleCategoryId_'.$iVehicleCategoryId]['vImage'] = $vImageOldArr[$orderKey];
                }
            }
            // echo "<pre>"; print_r($tServiceDetails); exit;
            $Data_update_medicalservice['tServiceDetails'] = json_encode($tServiceDetails, JSON_UNESCAPED_UNICODE);
        }
        $Data_update_medicalservice['vTitle'] = $jsonMedicalServiceTitle;
        // $Data_update_medicalservice['iDisplayOrder'] = $_POST['iDisplayOrderMedicalService'];
        $where = " eServiceType = 'MedicalServices' ";
        $obj->MySQLQueryPerform($tbl_name, $Data_update_medicalservice, 'update', $where);
    }
    if ($MODULES_OBJ->isEnableNearByService()) {
        $jsonNearbyServiceTitle = getJsonFromAnArr($vNearbyServiceTitleArr);
        $Data_update_nearbyservice = [];
        if ('Yes' === $_POST['saveNearbyServices']) {
            $iCategoryIdArr = $_POST['iCategoryId'];
            $iCategoryIdValArr = $_POST['iCategoryIdVal'];
            $iDisplayOrderNearbyArr = $_POST['iDisplayOrderNearbyArr'];
            $vImageOldArr = $_POST['vNearbyImageOld'];
            $db_data_nearby = $obj->MySQLSelect("SELECT tServiceDetails FROM {$tbl_name} WHERE eServiceType = 'NearBy' ");
            $tServiceDetails = [];
            if (!empty($db_data_nearby[0]['tServiceDetails'])) {
                $tServiceDetails = json_decode($db_data_nearby[0]['tServiceDetails'], true);
                foreach ($tServiceDetails as $serviceDetail) {
                    if (!in_array($serviceDetail['iCategoryId'], $iCategoryIdArr, true)) {
                        $tServiceDetails['iCategoryId_'.$serviceDetail['iCategoryId']]['eStatus'] = 'Inactive';
                    }
                }
            }
            foreach ($iCategoryIdArr as $iCategoryId) {
                $orderKey = array_search($iCategoryId, $_POST['iCategoryIdVal'], true);
                $iDisplayOrderService = $iDisplayOrderNearbyArr[$orderKey];
                $vImage = '';
                $image_object = $_FILES['vNearbyImage']['tmp_name'][$orderKey];
                $image_name = $_FILES['vNearbyImage']['name'][$orderKey];
                if ('' !== $image_name) {
                    $filecheck = basename($_FILES['vNearbyImage']['name'][$orderKey]);
                    $fileextarr = explode('.', $filecheck);
                    $ext = strtolower($fileextarr[count($fileextarr) - 1]);
                    $flag_error = 0;
                    if ('jpg' !== $ext && 'gif' !== $ext && 'png' !== $ext && 'jpeg' !== $ext && 'bmp' !== $ext) {
                        $flag_error = 1;
                        $var_msg = 'Not valid image extension of .jpg, .jpeg, .gif, .png, .bmp';
                    }
                    if (1 === $flag_error) {
                        $_SESSION['success'] = '3';
                        $_SESSION['var_msg'] = $var_msg;
                        header('Location:manage_app_home_screen_new.php');

                        exit;
                    }
                    $Photo_Gallery_folder = $tconfig['tsite_upload_app_home_screen_images_path'].'AppHomeScreen/';
                    if (!is_dir($Photo_Gallery_folder)) {
                        mkdir($Photo_Gallery_folder, 0777);
                        chmod($Photo_Gallery_folder, 0777);
                    }
                    $img = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, '', 'jpg,png,gif,jpeg,bmp');
                    $vImage = $img[0];
                    if (!empty($vImageOldArr[$orderKey]) && file_exists($Photo_Gallery_folder.$vImageOldArr[$orderKey])) {
                        unlink($Photo_Gallery_folder.$vImageOldArr[$orderKey]);
                    }
                }
                $tServiceDetails['iCategoryId_'.$iCategoryId]['iCategoryId'] = $iCategoryId;
                $tServiceDetails['iCategoryId_'.$iCategoryId]['iDisplayOrder'] = $iDisplayOrderService;
                $tServiceDetails['iCategoryId_'.$iCategoryId]['eStatus'] = 'Active';
                if (!empty($vImage)) {
                    $tServiceDetails['iCategoryId_'.$iCategoryId]['vImage'] = $vImage;
                } else {
                    $tServiceDetails['iCategoryId_'.$iCategoryId]['vImage'] = $vImageOldArr[$orderKey];
                }
            }
            // echo "<pre>"; print_r($tServiceDetails); exit;
            $Data_update_nearbyservice['tServiceDetails'] = json_encode($tServiceDetails, JSON_UNESCAPED_UNICODE);
        }
        $Data_update_nearbyservice['vTitle'] = $jsonNearbyServiceTitle;
        // $Data_update_nearbyservice['iDisplayOrder'] = $_POST['iDisplayOrderNearbyService'];
        $where = " eServiceType = 'NearBy' ";
        $obj->MySQLQueryPerform($tbl_name, $Data_update_nearbyservice, 'update', $where);
    }
    $_SESSION['success'] = '1';
    $_SESSION['var_msg'] = 'App Home Screen View updated successfully.';
    header('Location:manage_app_home_screen.php');

    exit;
    /*} else {
        $_SESSION['success'] = 2;
        $_SESSION['var_msg'] = "CSRF token mismatch. Please refresh page and try again.";

        header("Location:manage_app_home_screen.php");
        exit();
    }*/
}
if (isset($_POST['eMedicalServiceCatEdit'])) {
    $medical_service_details = $obj->MySQLSelect("SELECT tCategoryDetails FROM {$master_service_category_tbl} WHERE eType = 'MedicalServices' ");
    $tCategoryDetails = $medical_service_details[0]['tCategoryDetails'];
    $Data_Update = [];
    $eMedicalServiceCatEdit = $_POST['eMedicalServiceCatEdit'];
    $vImage = '';
    $image_object = $_FILES['vImage']['tmp_name'];
    $image_name = $_FILES['vImage']['name'];
    if ('' !== $image_name) {
        $filecheck = basename($_FILES['vImage']['name']);
        $fileextarr = explode('.', $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        if ('jpg' !== $ext && 'gif' !== $ext && 'png' !== $ext && 'jpeg' !== $ext && 'bmp' !== $ext) {
            $flag_error = 1;
            $var_msg = 'Not valid image extension of .jpg, .jpeg, .gif, .png, .bmp';
        }
        $image_info = getimagesize($_FILES['vImage']['tmp_name']);
        $image_width = $image_info[0];
        $image_height = $image_info[1];
        if (1 === $flag_error) {
            $_SESSION['success'] = '3';
            $_SESSION['var_msg'] = $var_msg;
            header('Location:master_service_category.php');

            exit;
        }
        $Photo_Gallery_folder = $tconfig['tsite_upload_app_home_screen_images_path'];
        if (!is_dir($Photo_Gallery_folder)) {
            mkdir($Photo_Gallery_folder, 0777);
            chmod($Photo_Gallery_folder, 0777);
        }
        $img = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, '', 'jpg,png,gif,jpeg,bmp');
        $vImage = $img[0];
        if (!empty($_POST['vImage_old']) && file_exists($Photo_Gallery_folder.$_POST['vImage_old'])) {
            unlink($Photo_Gallery_folder.$_POST['vImage_old']);
        }
    }
    if (!empty($tCategoryDetails)) {
        $tCategoryDetails = json_decode($tCategoryDetails, true);
    } else {
        $tCategoryDetails = [];
    }
    if ('BookService' === $eMedicalServiceCatEdit) {
        if (!empty($vImage)) {
            $tCategoryDetails['BookService']['vImage'] = $vImage;
        }
        $tCategoryDetails['BookService']['vTextColor'] = $_POST['vTextColor'];
        $tCategoryDetails['BookService']['vBgColor'] = $_POST['vBgColor'];
    } elseif ('VideoConsult' === $eMedicalServiceCatEdit) {
        if (!empty($vImage)) {
            $tCategoryDetails['VideoConsult']['vImage'] = $vImage;
        }
        $tCategoryDetails['VideoConsult']['vTextColor'] = $_POST['vTextColor'];
        $tCategoryDetails['VideoConsult']['vBgColor'] = $_POST['vBgColor'];
    } else {
        if (!empty($vImage)) {
            $tCategoryDetails['MoreService']['vImage'] = $vImage;
        }
        $tCategoryDetails['MoreService']['vTextColor'] = $_POST['vTextColor'];
        $tCategoryDetails['MoreService']['vBgColor'] = $_POST['vBgColor'];
    }
    $Data_Update['tCategoryDetails'] = json_encode($tCategoryDetails, JSON_UNESCAPED_UNICODE);
    $where = " eType = 'MedicalServices' ";
    $obj->MySQLQueryPerform($master_service_category_tbl, $Data_Update, 'update', $where);
    $_SESSION['success'] = '1';
    $_SESSION['var_msg'] = 'App Home Screen View updated successfully.';
    header('Location:manage_app_home_screen.php');

    exit;
}

if (isset($_POST['eMasterServiceCatType'])) {
    $eMasterServiceCatType = $_POST['eMasterServiceCatType'];

    $service_details = $obj->MySQLSelect("SELECT tCategoryDetails FROM {$master_service_category_tbl} WHERE eType = '{$eMasterServiceCatType}' ");
    $tCategoryDetails = $service_details[0]['tCategoryDetails'];

    $Data_Update = [];

    $vImageRideSharePublish = '';
    $image_object = $_FILES['vImageRideSharePublish']['tmp_name'];
    $image_name = $_FILES['vImageRideSharePublish']['name'];

    if ('' !== $image_name) {
        $filecheck = basename($_FILES['vImageRideSharePublish']['name']);
        $fileextarr = explode('.', $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        if ('jpg' !== $ext && 'gif' !== $ext && 'png' !== $ext && 'jpeg' !== $ext && 'bmp' !== $ext) {
            $flag_error = 1;
            $var_msg = 'Not valid image extension of .jpg, .jpeg, .gif, .png, .bmp';
        }
        $image_info = getimagesize($_FILES['vImageRideSharePublish']['tmp_name']);
        $image_width = $image_info[0];
        $image_height = $image_info[1];

        if (1 === $flag_error) {
            $_SESSION['success'] = '3';
            $_SESSION['var_msg'] = $var_msg;
            header('Location:master_service_category.php');

            exit;
        }
        $Photo_Gallery_folder = $tconfig['tsite_upload_app_home_screen_images_path'];
        if (!is_dir($Photo_Gallery_folder)) {
            mkdir($Photo_Gallery_folder, 0777);
            chmod($Photo_Gallery_folder, 0777);
        }
        $img = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, '', 'jpg,png,gif,jpeg,bmp');
        $vImageRideSharePublish = $img[0];

        if (!empty($_POST['vImageRideSharePublish_old']) && file_exists($Photo_Gallery_folder.$_POST['vImageRideSharePublish_old'])) {
            unlink($Photo_Gallery_folder.$_POST['vImageRideSharePublish_old']);
        }
    }

    $vImageRideShareBook = '';
    $image_object = $_FILES['vImageRideShareBook']['tmp_name'];
    $image_name = $_FILES['vImageRideShareBook']['name'];

    if ('' !== $image_name) {
        $filecheck = basename($_FILES['vImageRideShareBook']['name']);
        $fileextarr = explode('.', $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        if ('jpg' !== $ext && 'gif' !== $ext && 'png' !== $ext && 'jpeg' !== $ext && 'bmp' !== $ext) {
            $flag_error = 1;
            $var_msg = 'Not valid image extension of .jpg, .jpeg, .gif, .png, .bmp';
        }
        $image_info = getimagesize($_FILES['vImageRideShareBook']['tmp_name']);
        $image_width = $image_info[0];
        $image_height = $image_info[1];

        if (1 === $flag_error) {
            $_SESSION['success'] = '3';
            $_SESSION['var_msg'] = $var_msg;
            header('Location:master_service_category.php');

            exit;
        }
        $Photo_Gallery_folder = $tconfig['tsite_upload_app_home_screen_images_path'];
        if (!is_dir($Photo_Gallery_folder)) {
            mkdir($Photo_Gallery_folder, 0777);
            chmod($Photo_Gallery_folder, 0777);
        }
        $img = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, '', 'jpg,png,gif,jpeg,bmp');
        $vImageRideShareBook = $img[0];

        if (!empty($_POST['vImageRideShareBook_old']) && file_exists($Photo_Gallery_folder.$_POST['vImageRideShareBook_old'])) {
            unlink($Photo_Gallery_folder.$_POST['vImageRideShareBook_old']);
        }
    }

    $vImageRideShareMyRides = '';
    $image_object = $_FILES['vImageRideShareMyRides']['tmp_name'];
    $image_name = $_FILES['vImageRideShareMyRides']['name'];

    if ('' !== $image_name) {
        $filecheck = basename($_FILES['vImageRideShareMyRides']['name']);
        $fileextarr = explode('.', $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        if ('jpg' !== $ext && 'gif' !== $ext && 'png' !== $ext && 'jpeg' !== $ext && 'bmp' !== $ext) {
            $flag_error = 1;
            $var_msg = 'Not valid image extension of .jpg, .jpeg, .gif, .png, .bmp';
        }
        $image_info = getimagesize($_FILES['vImageRideShareMyRides']['tmp_name']);
        $image_width = $image_info[0];
        $image_height = $image_info[1];

        if (1 === $flag_error) {
            $_SESSION['success'] = '3';
            $_SESSION['var_msg'] = $var_msg;
            header('Location:master_service_category.php');

            exit;
        }
        $Photo_Gallery_folder = $tconfig['tsite_upload_app_home_screen_images_path'];
        if (!is_dir($Photo_Gallery_folder)) {
            mkdir($Photo_Gallery_folder, 0777);
            chmod($Photo_Gallery_folder, 0777);
        }
        $img = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, '', 'jpg,png,gif,jpeg,bmp');
        $vImageRideShareMyRides = $img[0];

        if (!empty($_POST['vImageRideShareMyRides_old']) && file_exists($Photo_Gallery_folder.$_POST['vImageRideShareMyRides_old'])) {
            unlink($Photo_Gallery_folder.$_POST['vImageRideShareMyRides_old']);
        }
    }

    if (!empty($tCategoryDetails)) {
        $tCategoryDetails = json_decode($tCategoryDetails, true);
    } else {
        $tCategoryDetails = [];
    }

    if (!empty($vImageRideSharePublish)) {
        $tCategoryDetails['RideSharePublish']['vImage'] = $vImageRideSharePublish;
    }

    if (!empty($vImageRideShareBook)) {
        $tCategoryDetails['RideShareBook']['vImage'] = $vImageRideShareBook;
    }

    if (!empty($vImageRideShareMyRides)) {
        $tCategoryDetails['RideShareMyRides']['vImage'] = $vImageRideShareMyRides;
    }

    $Data_Update['tCategoryDetails'] = json_encode($tCategoryDetails, JSON_UNESCAPED_UNICODE);
    $where = " eType = 'RideShare' ";
    $obj->MySQLQueryPerform($master_service_category_tbl, $Data_Update, 'update', $where);

    $_SESSION['success'] = '1';
    $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
    header('Location:manage_app_home_screen.php');

    exit;
}

$userEditDataArr = $db_data_arr = [];
$db_data = $obj->MySQLSelect("SELECT * FROM {$tbl_name}");
foreach ($db_data as $db_value) {
    $ViewType = !empty($db_value['eServiceType']) ? $db_value['eServiceType'] : $db_value['eViewType'];
    $db_data_arr[$ViewType] = $db_value;
}
$vIntroTitleArr = json_decode($db_data_arr['IntroView']['vTitle'], true);
foreach ($vIntroTitleArr as $key => $value) {
    $key = str_replace('vTitle_', 'vIntroTitle_', $key);
    $userEditDataArr[$key] = $value;
}
$vIntroSubTitleArr = json_decode($db_data_arr['IntroView']['vSubtitle'], true);
foreach ($vIntroSubTitleArr as $key => $value) {
    $key = str_replace('vSubtitle_', 'vIntroSubTitle_', $key);
    $userEditDataArr[$key] = $value;
}

$vIntroBuyTitleArr = json_decode($db_data_arr['IntroViewBuy']['vTitle'], true);
foreach ($vIntroBuyTitleArr as $key => $value) {
    $key = str_replace('vTitle_', 'vIntroBuyTitle_', $key);
    $userEditDataArr[$key] = $value;
}
$vIntroBuySubTitleArr = json_decode($db_data_arr['IntroViewBuy']['vSubtitle'], true);
foreach ($vIntroBuySubTitleArr as $key => $value) {
    $key = str_replace('vSubtitle_', 'vIntroBuySubTitle_', $key);
    $userEditDataArr[$key] = $value;
}

$iDisplayOrderPromoBannerView = $db_data_arr['PromotionalBanner']['iDisplayOrder'];
$EN_available = $LANG_OBJ->checkLanguageExist();
$db_master = $LANG_OBJ->getLangDataDefaultFirst($db_master);
$max_display_order = 1;
if ($MODULES_OBJ->isEnableAnywhereDeliveryFeature('Yes', 'Genie')) {
    $iDisplayOrderGenieBannerView = $db_data_arr['Genie']['iDisplayOrder'];
    ++$max_display_order;
}
if ($MODULES_OBJ->isEnableVideoConsultingService()) {
    $iDisplayOrderVCBannerView = $db_data_arr['VideoConsult']['iDisplayOrder'];
    ++$max_display_order;
}
if ($MODULES_OBJ->isEnableBiddingServices()) {
    $iDisplayOrderBidBannerView = $db_data_arr['Bidding']['iDisplayOrder'];
    ++$max_display_order;

    $vBidTitleArr = json_decode($db_data_arr['Bidding']['vTitle'], true);
    foreach ($vBidTitleArr as $key => $value) {
        $key = str_replace('vTitle_', 'vBidTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $vBidSubTitleArr = json_decode($db_data_arr['Bidding']['vSubtitle'], true);
    foreach ($vBidSubTitleArr as $key => $value) {
        $key = str_replace('vSubtitle_', 'vBidSubTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    //   $iDisplayBidService = $db_data_arr['Bid']['iDisplayOrder'];
}
if ($MODULES_OBJ->isUberXFeatureAvailable()) {
    ++$max_display_order;
    $vOtherServiceTitleArr = json_decode($db_data_arr['UberX']['vTitle'], true);
    foreach ($vOtherServiceTitleArr as $key => $value) {
        $key = str_replace('vTitle_', 'vOtherServiceTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $vOtherServiceSubTitleArr = json_decode($db_data_arr['UberX']['vSubtitle'], true);
    foreach ($vOtherServiceSubTitleArr as $key => $value) {
        $key = str_replace('vSubtitle_', 'vOtherServiceSubTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $iDisplayOrderOtherService = $db_data_arr['UberX']['iDisplayOrder'];
    $tServiceDetails = $db_data_arr['UberX']['tServiceDetails'];
    $tServiceDetailsArr = [];
    if (!empty($tServiceDetails)) {
        $tServiceDetailsArr = json_decode($tServiceDetails, true);
    }
    $ufxData = $obj->MySQLSelect("SELECT iVehicleCategoryId, vCategory_{$default_lang} as vCategoryName FROM ".getVehicleCategoryTblName()." WHERE eCatType = 'ServiceProvider' AND eVideoConsultEnable = 'No' AND iParentId='0' AND eStatus = 'Active' ORDER BY vCategoryName");
}
if ($MODULES_OBJ->isRideFeatureAvailable() || $MODULES_OBJ->isDeliveryFeatureAvailable() || $MODULES_OBJ->isEnableAnywhereDeliveryFeature() || $MODULES_OBJ->isDeliverAllFeatureAvailable() || $MODULES_OBJ->isEnableVideoConsultingService() || $MODULES_OBJ->isEnableBiddingServices() || $MODULES_OBJ->isUberXFeatureAvailable()) {
    ++$max_display_order;
    $tGridServices = explode(',', $db_data_arr['GridView']['tGridServices']);
    $tGridServices = array_map(static fn ($val) => '#grid_service_'.$val, $tGridServices);
    $tGridServicesStr = json_encode($tGridServices);
    $iDisplayOrderGridView = $db_data_arr['GridView']['iDisplayOrder'];
}
if ($MODULES_OBJ->isEnableRentItemService() || $MODULES_OBJ->isEnableRentCarsService() || $MODULES_OBJ->isEnableRentEstateService()) {
    ++$max_display_order;
    $vBuySellTitleArr = json_decode($db_data_arr['BuySell']['vTitle'], true);
    foreach ($vBuySellTitleArr as $key => $value) {
        $key = str_replace('vTitle_', 'vBuySellTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $iDisplayOrderBuySell = $db_data_arr['BuySell']['iDisplayOrder'];
}
if ($MODULES_OBJ->isEnableRideShareService()) {
    ++$max_display_order;
    $iDisplayOrderRideShare = $db_data_arr['RideShare']['iDisplayOrder'];
    $ride_share_details = $obj->MySQLSelect("SELECT tCategoryDetails FROM {$master_service_category_tbl} WHERE eType = 'RideShare' ");
    $vImageOld['RideShare']['RideSharePublish'] = $vImageOld['RideShare']['RideShareBook'] = $vImageOld['RideShare']['RideShareMyRides'] = '';
    $ServiceTitle['RideShare']['RideSharePublish'] = $langage_lbl_admin['LBL_RIDE_SHARE_PUBLISH_TXT'];
    $ServiceTitle['RideShare']['RideShareBook'] = $langage_lbl_admin['LBL_RIDE_SHARE_BOOK_TXT'];
    $ServiceTitle['RideShare']['RideShareMyRides'] = $langage_lbl_admin['LBL_RIDE_SHARE_MY_RIDES_TXT'];
    $ServiceTitle['RideShare']['RideSharePublishLabel'] = 'LBL_RIDE_SHARE_PUBLISH_TXT';
    $ServiceTitle['RideShare']['RideShareBookLabel'] = 'LBL_RIDE_SHARE_BOOK_TXT';
    $ServiceTitle['RideShare']['RideShareMyRidesLabel'] = 'LBL_RIDE_SHARE_MY_RIDES_TXT';

    $tCategoryDetails = $ride_share_details[0]['tCategoryDetails'];
    if (!empty($tCategoryDetails)) {
        $tCategoryDetails = json_decode($tCategoryDetails, true);
        $vImageOld['RideShare']['RideSharePublish'] = $tCategoryDetails['RideSharePublish']['vImage'];
        $vImageOld['RideShare']['RideShareBook'] = $tCategoryDetails['RideShareBook']['vImage'];
        $vImageOld['RideShare']['RideShareMyRides'] = $tCategoryDetails['RideShareMyRides']['vImage'];
    }
}
if ($MODULES_OBJ->isEnableMedicalServices()) {
    ++$max_display_order;
    $vMedicalServiceTitleArr = json_decode($db_data_arr['MedicalServices']['vTitle'], true);
    foreach ($vMedicalServiceTitleArr as $key => $value) {
        $key = str_replace('vTitle_', 'vMedicalServiceTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $iDisplayOrderMedicalService = $db_data_arr['MedicalServices']['iDisplayOrder'];
    $medicalServiceDataArr = $obj->MySQLSelect("SELECT vc.iParentId,vc.iVehicleCategoryId,vc.vCategory_{$default_lang} as vCategoryName, vc.eStatus, vc.iDisplayOrder,vc.eCatType,vc.eForMedicalService, vc.eVideoConsultEnable, vc.tMedicalServiceInfo, (select count(iVehicleCategoryId) FROM ".getVehicleCategoryTblName()." WHERE vc.iParentId = vc.iVehicleCategoryId AND eStatus = 'Active') as SubCategories FROM ".getVehicleCategoryTblName()." as vc WHERE eStatus = 'Active' AND (vc.iParentId='0' OR vc.iParentId = '3') AND eForMedicalService = 'Yes' AND iVehicleCategoryId != 297 ORDER BY iDisplayOrder ASC");
    $OnDemandServicesArr = $VideoConsultServicesArr = $MoreServicesArr = [];
    foreach ($medicalServiceDataArr as $medicalService) {
        if (!empty($medicalService['tMedicalServiceInfo'])) {
            $tMedicalServiceInfoArr = json_decode($medicalService['tMedicalServiceInfo'], true);
            $medicalServiceData = $medicalService;
            if ('Yes' === $tMedicalServiceInfoArr['BookService']) {
                $medicalServiceData['ms_display_order'] = $tMedicalServiceInfoArr['iDisplayOrderBS'];
                $medicalServiceDataBS = [];
                $medicalServiceDataBS = $medicalServiceData;
                $medicalServiceDataBS['eVideoConsultEnable'] = 'No';
                $OnDemandServicesArr[] = $medicalServiceDataBS;
            }
            if ('Yes' === $medicalService['eVideoConsultEnable'] && 'Yes' === $tMedicalServiceInfoArr['VideoConsult']) {
                $medicalServiceData['ms_display_order'] = $tMedicalServiceInfoArr['iDisplayOrderVC'];
                $VideoConsultServicesArr[] = $medicalServiceData;
            }
            if ('Yes' === $tMedicalServiceInfoArr['MoreService']) {
                $medicalServiceData['ms_display_order'] = $tMedicalServiceInfoArr['iDisplayOrderMS'];
                $medicalServiceDataMS = [];
                $medicalServiceDataMS = $medicalServiceData;
                $medicalServiceDataMS['eVideoConsultEnable'] = 'No';
                $MoreServicesArr[] = $medicalServiceDataMS;
            }
        }
    }
    $ms_display_order = array_column($OnDemandServicesArr, 'ms_display_order');
    array_multisort($ms_display_order, SORT_ASC, $OnDemandServicesArr);
    $ms_display_order = array_column($VideoConsultServicesArr, 'ms_display_order');
    array_multisort($ms_display_order, SORT_ASC, $VideoConsultServicesArr);
    $ms_display_order = array_column($MoreServicesArr, 'ms_display_order');
    array_multisort($ms_display_order, SORT_ASC, $MoreServicesArr);
    $tServiceDetailsMS = $db_data_arr['MedicalServices']['tServiceDetails'];
    $tServiceDetailsMSArr = [];
    if (!empty($tServiceDetailsMS)) {
        $tServiceDetailsMSArr = json_decode($tServiceDetailsMS, true);
    }
    $MEDICAL_SERVICES_ARR = [['ServiceTitle' => $langage_lbl_admin['LBL_ON_DEMAND_MEDICAL_SERVICES_TITLE'], 'ServiceDesc' => $langage_lbl_admin['LBL_ON_DEMAND_MEDICAL_SERVICES_DESC'], 'ManageServiceKey' => 'BookService', 'ManageServiceSuffix' => 'BS', 'ModalKey' => 'BookServiceModal', 'ModalManageServiceKey' => 'ms_bookservice_modal', 'HiddenInput' => 'saveBookServiceMS', 'ServiceTitleLabel' => 'LBL_ON_DEMAND_MEDICAL_SERVICES_TITLE', 'ServiceDescLabel' => 'LBL_ON_DEMAND_MEDICAL_SERVICES_DESC', 'ServicesArr' => $OnDemandServicesArr], ['ServiceTitle' => $langage_lbl_admin['LBL_VIDEO_CONSULT_MEDICAL_SERVICES_TITLE'], 'ServiceDesc' => $langage_lbl_admin['LBL_VIDEO_CONSULT_MEDICAL_SERVICES_DESC'], 'ManageServiceKey' => 'VideoConsult', 'ManageServiceSuffix' => 'VC', 'ModalKey' => 'VideoConsultModal', 'ModalManageServiceKey' => 'ms_videoconsult_modal', 'HiddenInput' => 'saveVideoConsultMS', 'ServiceTitleLabel' => 'LBL_VIDEO_CONSULT_MEDICAL_SERVICES_TITLE', 'ServiceDescLabel' => 'LBL_VIDEO_CONSULT_MEDICAL_SERVICES_DESC', 'ServicesArr' => $VideoConsultServicesArr], ['ServiceTitle' => $langage_lbl_admin['LBL_MEDICAL_MORE_SERVICES_TITLE'], 'ServiceDesc' => $langage_lbl_admin['LBL_MEDICAL_MORE_SERVICES_DESC'], 'ManageServiceKey' => 'MoreService', 'ManageServiceSuffix' => 'MS', 'ModalKey' => 'MoreServiceModal', 'ModalManageServiceKey' => 'ms_moreservice_modal', 'HiddenInput' => 'saveMoreServiceMS', 'ServiceTitleLabel' => 'LBL_MEDICAL_MORE_SERVICES_TITLE', 'ServiceDescLabel' => 'LBL_MEDICAL_MORE_SERVICES_DESC', 'ServicesArr' => $MoreServicesArr]];
    $TextColor['BookService'] = $TextColor['VideoConsult'] = $TextColor['MoreService'] = '#000000';
    $BgColor['BookService'] = $BgColor['VideoConsult'] = $BgColor['MoreService'] = '#ffffff';
    $vImageOld['BookService'] = $vImageOld['VideoConsult'] = $vImageOld['MoreService'] = '';
    $medical_service_details = $obj->MySQLSelect("SELECT tCategoryDetails FROM {$master_service_category_tbl} WHERE eType = 'MedicalServices' ");
    if (!empty($medical_service_details)) {
        $tCategoryDetails = $medical_service_details[0]['tCategoryDetails'];
        if (!empty($tCategoryDetails)) {
            $tCategoryDetails = json_decode($tCategoryDetails, true);
            $TextColor['BookService'] = $tCategoryDetails['BookService']['vTextColor'];
            $BgColor['BookService'] = $tCategoryDetails['BookService']['vBgColor'];
            $vImageOld['BookService'] = $tCategoryDetails['BookService']['vImage'];
            $TextColor['VideoConsult'] = $tCategoryDetails['VideoConsult']['vTextColor'];
            $BgColor['VideoConsult'] = $tCategoryDetails['VideoConsult']['vBgColor'];
            $vImageOld['VideoConsult'] = $tCategoryDetails['VideoConsult']['vImage'];
            $TextColor['MoreService'] = $tCategoryDetails['MoreService']['vTextColor'];
            $BgColor['MoreService'] = $tCategoryDetails['MoreService']['vBgColor'];
            $vImageOld['MoreService'] = $tCategoryDetails['MoreService']['vImage'];
        }
    }
}
if ($MODULES_OBJ->isEnableTrackAnyServiceFeature()) {
    ++$max_display_order;
    $vTrackServiceTitleArr = json_decode($db_data_arr['TrackAnyService']['vTitle'], true);
    foreach ($vTrackServiceTitleArr as $key => $value) {
        $key = str_replace('vTitle_', 'vTrackServiceTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $iDisplayOrderTrackService = $db_data_arr['TrackAnyService']['iDisplayOrder'];
}
if ($MODULES_OBJ->isEnableNearByService()) {
    ++$max_display_order;
    $vNearbyServiceTitleArr = json_decode($db_data_arr['NearBy']['vTitle'], true);
    foreach ($vNearbyServiceTitleArr as $key => $value) {
        $key = str_replace('vTitle_', 'vNearbyServiceTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
    $iDisplayOrderNearbyService = $db_data_arr['NearBy']['iDisplayOrder'];
    $tServiceDetailsNearby = $db_data_arr['NearBy']['tServiceDetails'];
    $tServiceDetailsNearbyArr = [];
    if (!empty($tServiceDetailsNearby)) {
        $tServiceDetailsNearbyArr = json_decode($tServiceDetailsNearby, true);
    }
    $nearbyData = $NEARBY_OBJ->getNearByCategory('webservice', '', '', '', $default_lang);
}
$master_service_categories = $obj->MySQLSelect("SELECT JSON_UNQUOTE(JSON_EXTRACT(vCategoryName, '$.vCategoryName_".$default_lang."')) as vCategoryName, eType, iMasterServiceCategoryId FROM {$master_service_category_tbl} WHERE eStatus = 'Active'");
$MasterCategoryArr = [];
foreach ($master_service_categories as $mCategory) {
    $MasterCategoryArr[$mCategory['eType']] = $mCategory;
}
$promotionalBanner = $obj->MySQLSelect('SELECT iVehicleCategoryId FROM '.getVehicleCategoryTblName()." WHERE ePromoteBanner = 'Yes' AND eStatus = 'Active' ");
if (!empty($promotionalBanner) && count($promotionalBanner) > 0) {
    $promotionalCategoryId = $promotionalBanner[0]['iVehicleCategoryId'];
} else {
    $promotionalBanner = $obj->MySQLSelect('SELECT iVehicleCategoryId FROM '.getVehicleCategoryTblName()." AND eStatus = 'Active' ORDER BY iDisplayOrder LIMIT 1");
    $promotionalCategoryId = $promotionalBanner[0]['iVehicleCategoryId'];
}
$display_section = 'style="display: none"';

$hide_display_order = 'Yes';
?>
<!DOCTYPE html>
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title>Admin | Manage App Home Screen</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet"/>
    <?php include_once 'global_files.php'; ?>
    <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css"/>
    <link href="../assets/css/jquery-ui.css" rel="stylesheet"/>
    <link rel="stylesheet" href="css/fancybox.css"/>
    <style>
        .grid-services-section {
            display: block;
            border: 1px solid #aaaaaa;
            padding: 15px 15px 0;
            background-color: #f5f5f5;
        }

        .grid-service-content {
            display: inline-block;
            margin-right: 15px;
        }

        .grid-service {
            display: inline-block;
            border: 1px solid #cccccc;
            padding: 4px 15px;
            font-size: 16px;
            margin-right: 5px;
        }

        .grid-service, .grid-service label {
            font-weight: 500;
            margin-bottom: 0;
            cursor: grab;
            background-color: #ffffff;
        }

        .grid-service input[type="checkbox"] {
            display: none;
        }

        .grid-service.active {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .grid-service-content a {
            vertical-align: top;
        }


        .show-help-img {
            margin-right: 15px;
            cursor: pointer;
        }

        .ui-sortable-handle {
            margin-bottom: 15px;
            width: auto !important;
            height: auto !important;
        }
    </style>
</head>
<!-- END  HEAD-->
<!-- BEGIN BODY-->
<body class="padTop53 ">
<!-- MAIN WRAPPER -->
<div id="wrap">
    <?php include_once 'header.php'; ?>
    <?php include_once 'left_menu.php'; ?>
    <!--PAGE CONTENT -->
    <div id="content">
        <div class="inner">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Manage App Home Screen</h2>
                </div>
            </div>
            <hr/>
            <?php include 'valid_msg.php'; ?>
            <div class="body-div">
                <div class="form-group">
                    <form method="post" action="" enctype="multipart/form-data">
                        <?php // = $CSRF_OBJ->insertHiddenToken();?>
                        <h3 class="show-help-section">
                            <i class="fa fa-caret-right"></i>
                            Introduction View
                            <i class="fa fa-question-circle show-help-img" data-fancybox
                               data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/IntroView.png"></i>
                        </h3>
                        <?php if (count($db_master) > 1) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vIntroTitle_Default"
                                           name="vIntroTitle_Default"
                                           value="<?php echo $userEditDataArr['vIntroTitle_'.$default_lang]; ?>"
                                           data-originalvalue="<?php echo $userEditDataArr['vIntroTitle_'.$default_lang]; ?>"
                                           readonly="readonly" required>
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                            data-original-title="Edit" onclick="editIntroTitle('Edit')">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="modal fade" id="IntroTitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                 data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="intro_title_modal_action"></span>
                                                Title
                                                <button type="button" class="close" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vIntroTitle_')">x
                                                </button>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                                            for ($i = 0; $i < $count_all; ++$i) {
                                                $vCode = $db_master[$i]['vCode'];
                                                $vTitle = $db_master[$i]['vTitle'];
                                                $eDefault = $db_master[$i]['eDefault'];
                                                $vValue = 'vIntroTitle_'.$vCode;
                                                ${$vValue} = $userEditDataArr[$vValue];
                                                $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                                ?>
                                                <?php
                                                $page_title_class = 'col-lg-12';
                                                if (count($db_master) > 1) {
                                                    if ($EN_available) {
                                                        if ('EN' === $vCode) {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    } else {
                                                        if ($vCode === $default_lang) {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    }
                                                }
                                                ?>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label>Title (<?php echo $vTitle; ?>
                                                            ) <?php echo $required_msg; ?></label>
                                                    </div>
                                                    <div class="<?php echo $page_title_class; ?>">
                                                        <input type="text" class="form-control" name="<?php echo $vValue; ?>"
                                                               id="<?php echo $vValue; ?>" value="<?php echo ${$vValue}; ?>"
                                                               data-originalvalue="<?php echo ${$vValue}; ?>"
                                                               placeholder="<?php echo $vTitle; ?> Value">
                                                        <div class="text-danger" id="<?php echo $vValue.'_error'; ?>"
                                                             style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                    </div>
                                                    <?php
                                                    if (count($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ('EN' === $vCode) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vIntroTitle_', 'EN');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                            } else {
                                                                if ($vCode === $default_lang) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vIntroTitle_', '<?php echo $default_lang; ?>');">
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
                                                <strong><?php echo $langage_lbl['LBL_NOTE']; ?>:
                                                </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                        onclick="saveIntroTitle()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vIntroTitle_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                            </div>
                                        </div>
                                        <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Subtitle</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vIntroSubTitle_Default"
                                           name="vIntroSubTitle_Default"
                                           value="<?php echo $userEditDataArr['vIntroSubTitle_'.$default_lang]; ?>"
                                           data-originalvalue="<?php echo $userEditDataArr['vIntroSubTitle_'.$default_lang]; ?>"
                                           readonly="readonly" required>
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                            data-original-title="Edit" onclick="editIntroSubTitle('Edit')">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="modal fade" id="IntroSubTitle_Modal" tabindex="-1" role="dialog"
                                 aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="intro_subtitle_modal_action"></span>
                                                Title
                                                <button type="button" class="close" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vIntroSubTitle_')">x
                                                </button>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                            for ($i = 0; $i < $count_all; ++$i) {
                                $vCode = $db_master[$i]['vCode'];
                                $vTitle = $db_master[$i]['vTitle'];
                                $eDefault = $db_master[$i]['eDefault'];
                                $vValue = 'vIntroSubTitle_'.$vCode;
                                ${$vValue} = $userEditDataArr[$vValue];
                                $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                ?>
                                                <?php
                                $page_title_class = 'col-lg-12';
                                if (count($db_master) > 1) {
                                    if ($EN_available) {
                                        if ('EN' === $vCode) {
                                            $page_title_class = 'col-md-9 col-sm-9';
                                        }
                                    } else {
                                        if ($vCode === $default_lang) {
                                            $page_title_class = 'col-md-9 col-sm-9';
                                        }
                                    }
                                }
                                ?>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label>Subtitle (<?php echo $vTitle; ?>
                                                            ) <?php echo $required_msg; ?></label>
                                                    </div>
                                                    <div class="<?php echo $page_title_class; ?>">
                                                        <input type="text" class="form-control" name="<?php echo $vValue; ?>"
                                                               id="<?php echo $vValue; ?>" value="<?php echo ${$vValue}; ?>"
                                                               data-originalvalue="<?php echo ${$vValue}; ?>"
                                                               placeholder="<?php echo $vTitle; ?> Value">
                                                        <div class="text-danger" id="<?php echo $vValue.'_error'; ?>"
                                                             style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                    </div>
                                                    <?php
                                    if (count($db_master) > 1) {
                                        if ($EN_available) {
                                            if ('EN' === $vCode) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vIntroSubTitle_', 'EN');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                            } else {
                                                if ($vCode === $default_lang) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vIntroSubTitle_', '<?php echo $default_lang; ?>');">
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
                                                <strong><?php echo $langage_lbl['LBL_NOTE']; ?>:
                                                </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                        onclick="saveIntroSubTitle()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vIntroSubTitle_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                            </div>
                                        </div>
                                        <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vIntroTitle_<?php echo $default_lang; ?>"
                                           name="vIntroTitle_<?php echo $default_lang; ?>"
                                           value="<?php echo $userEditDataArr['vIntroTitle_'.$default_lang]; ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Subtitle</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vIntroSubTitle_<?php echo $default_lang; ?>"
                                           name="vIntroSubTitle_<?php echo $default_lang; ?>"
                                           value="<?php echo $userEditDataArr['vIntroSubTitle_'.$default_lang]; ?>">
                                </div>
                            </div>
                        <?php } ?>
                        <hr/>
                        <h3 class="show-help-section">
                            <i class="fa fa-caret-right"></i>
                            Banner View - General Banners
                            <i class="fa fa-question-circle show-help-img" data-fancybox
                               data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/GeneralBanners.png"></i>
                        </h3>
                        <div class="row">
                            <div class="col-lg-12">
                                <a href="<?php echo $tconfig['tsite_url_main_admin']; ?>banner.php" class="btn btn-info"
                                   target="_blank">Manage Banners
                                </a>
                            </div>
                        </div>
                        <?php if ($MODULES_OBJ->isRideFeatureAvailable() || $MODULES_OBJ->isDeliveryFeatureAvailable() || $MODULES_OBJ->isEnableAnywhereDeliveryFeature() || $MODULES_OBJ->isDeliverAllFeatureAvailable() || $MODULES_OBJ->isEnableVideoConsultingService() || $MODULES_OBJ->isEnableBiddingServices() || $MODULES_OBJ->isUberXFeatureAvailable()) { ?>
                            <hr/>
                            <h3 class="show-help-section">
                                <i class="fa fa-caret-right"></i>
                                Grid View
                                <i class="fa fa-question-circle show-help-img" data-fancybox
                                   data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/GridView.png"></i>
                            </h3>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Services</label>
                                </div>
                                <div class="col-lg-12">
                                    <div class="grid-services-section" id="grid-services-section">
                                        <?php if ($MODULES_OBJ->isRideFeatureAvailable()) { ?>
                                            <div class="grid-service-content" id="grid_service_Ride">
                                                <div class="grid-service">
                                                    <input type="checkbox" name="grid_service[]" id="grid_service_1"
                                                           value="Ride" checked>
                                                    <label for="grid_service_1"><?php echo $MasterCategoryArr['Ride']['vCategoryName']; ?></label>
                                                </div>
                                                <a href="<?php echo $tconfig['tsite_url_main_admin']; ?>master_service_category_action.php?id=<?php echo $MasterCategoryArr['Ride']['iMasterServiceCategoryId']; ?>"
                                                   class="btn btn-info" target="_blank" data-toggle="tooltip"
                                                   title="Edit <?php echo $MasterCategoryArr['Ride']['vCategoryName']; ?>">
                                                    <i class="glyphicon glyphicon-pencil"></i>
                                                </a>
                                            </div>
                                        <?php }
                                        if ($MODULES_OBJ->isDeliveryFeatureAvailable()) { ?>
                                            <div class="grid-service-content" id="grid_service_Deliver">
                                                <div class="grid-service">
                                                    <input type="checkbox" name="grid_service[]" id="grid_service_2"
                                                           value="Deliver" checked>
                                                    <label for="grid_service_2"><?php echo $MasterCategoryArr['Deliver']['vCategoryName']; ?></label>
                                                </div>
                                                <a href="<?php echo $tconfig['tsite_url_main_admin']; ?>master_service_category_action.php?id=<?php echo $MasterCategoryArr['Deliver']['iMasterServiceCategoryId']; ?>"
                                                   class="btn btn-info" target="_blank" data-toggle="tooltip"
                                                   title="Edit <?php echo $MasterCategoryArr['Deliver']['vCategoryName']; ?>">
                                                    <i class="glyphicon glyphicon-pencil"></i>
                                                </a>
                                            </div>
                                        <?php }
                                        if ($MODULES_OBJ->isDeliverAllFeatureAvailable()) { ?>
                                            <div class="grid-service-content" id="grid_service_DeliverAll">
                                                <div class="grid-service">
                                                    <input type="checkbox" name="grid_service[]" id="grid_service_3"
                                                           value="DeliverAll" checked>
                                                    <label for="grid_service_3"><?php echo $MasterCategoryArr['DeliverAll']['vCategoryName']; ?></label>
                                                </div>
                                                <a href="<?php echo $tconfig['tsite_url_main_admin']; ?>master_service_category_action.php?id=<?php echo $MasterCategoryArr['DeliverAll']['iMasterServiceCategoryId']; ?>"
                                                   class="btn btn-info" target="_blank" data-toggle="tooltip"
                                                   title="Edit <?php echo $MasterCategoryArr['DeliverAll']['vCategoryName']; ?>">
                                                    <i class="glyphicon glyphicon-pencil"></i>
                                                </a>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <?php if ('No' === $hide_display_order) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Display Order</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <select class="form-control" name="iDisplayOrderGridView"
                                            id="iDisplayOrderGridView">
                                        <?php for ($i = 1; $i <= $max_display_order; ++$i) { ?>
                                            <option value="<?php echo $i; ?>" <?php echo $i === $iDisplayOrderGridView ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php } ?>
                        <?php } ?>

                        <?php if ($MODULES_OBJ->isEnableAnywhereDeliveryFeature('Yes', 'Genie')) { ?>
                            <hr/>
                            <h3 class="show-help-section">
                                <i class="fa fa-caret-right"></i>
                                Banner View - Delivery Genie / Runner
                                <i class="fa fa-question-circle show-help-img" data-fancybox
                                   data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/DeliveryGenieRunner.png"></i>
                            </h3>
                            <div class="row">
                                <div class="col-lg-12">
                                    <a href="<?php echo $tconfig['tsite_url_main_admin']; ?>app_banner.php?eBuyAnyService=Genie&eFor=AppHomeScreen"
                                       class="btn btn-info" target="_blank">Manage Banner
                                    </a>
                                </div>
                            </div>

                            <?php if ('No' === $hide_display_order) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Display Order</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <select class="form-control" name="iDisplayOrderGenieBannerView"
                                            id="iDisplayOrderGenieBannerView">
                                        <?php for ($i = 1; $i <= $max_display_order; ++$i) { ?>
                                            <option value="<?php echo $i; ?>" <?php echo $i === $iDisplayOrderGenieBannerView ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php } ?>
                        <?php } ?>


                        <?php if ($MODULES_OBJ->isEnableBiddingServices()) { ?>
                            <hr/>
                            <h3 class="show-help-section">
                                <i class="fa fa-caret-right"></i>
                                Service List View - Service Bid
                                <i class="fa fa-question-circle show-help-img" data-fancybox
                                   data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/ServiceBidBanner.png"></i>
                            </h3>

                            <?php if (count($db_master) > 1) { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vBidTitle_Default"
                                               name="vBidTitle_Default"
                                               value="<?php echo $userEditDataArr['vBidTitle_'.$default_lang]; ?>"
                                               data-originalvalue="<?php echo $userEditDataArr['vBidTitle_'.$default_lang]; ?>"
                                               readonly="readonly" required>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                data-original-title="Edit" onclick="editBidTitle('Edit')">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="modal fade" id="BidTitle_Modal" tabindex="-1" role="dialog"
                                     aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="Bid_title_modal_action"></span>
                                                    Title
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vBidTitle_')">
                                                        x
                                                    </button>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                for ($i = 0; $i < $count_all; ++$i) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];
                                                    $vValue = 'vBidTitle_'.$vCode;
                                                    ${$vValue} = $userEditDataArr[$vValue];
                                                    $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                                    ?>
                                                    <?php
                                                    $page_title_class = 'col-lg-12';
                                                    if (count($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ('EN' === $vCode) {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        } else {
                                                            if ($vCode === $default_lang) {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Title (<?php echo $vTitle; ?>
                                                                ) <?php echo $required_msg; ?></label>
                                                        </div>
                                                        <div class="<?php echo $page_title_class; ?>">
                                                            <input type="text" class="form-control"
                                                                   name="<?php echo $vValue; ?>" id="<?php echo $vValue; ?>"
                                                                   value="<?php echo ${$vValue}; ?>"
                                                                   data-originalvalue="<?php echo ${$vValue}; ?>"
                                                                   placeholder="<?php echo $vTitle; ?> Value">
                                                            <div class="text-danger" id="<?php echo $vValue.'_error'; ?>"
                                                                 style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                        </div>
                                                        <?php
                                                        if (count($db_master) > 1) {
                                                            if ($EN_available) {
                                                                if ('EN' === $vCode) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vBidTitle_', 'EN');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                                } else {
                                                                    if ($vCode === $default_lang) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vBidTitle_', '<?php echo $default_lang; ?>');">
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
                                                    <strong><?php echo $langage_lbl['LBL_NOTE']; ?>:
                                                    </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                <div class="nimot-class-but" style="margin-bottom: 0">
                                                    <button type="button" class="save" style="margin-left: 0 !important"
                                                            onclick="saveBidTitle()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                    <button type="button" class="btn btn-danger btn-ok"
                                                            data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vBidTitle_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Subtitle</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vBidSubTitle_Default"
                                               name="vBidSubTitle_Default"
                                               value="<?php echo $userEditDataArr['vBidSubTitle_'.$default_lang]; ?>"
                                               data-originalvalue="<?php echo $userEditDataArr['vBidSubTitle_'.$default_lang]; ?>"
                                               readonly="readonly" required>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                data-original-title="Edit" onclick="editBidSubTitle('Edit')">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="modal fade" id="BidSubTitle_Modal" tabindex="-1" role="dialog"
                                     aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="Bid_subtitle_modal_action"></span>
                                                    Title
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vBidSubTitle_')">
                                                        x
                                                    </button>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                for ($i = 0; $i < $count_all; ++$i) {
                                    $vCode = $db_master[$i]['vCode'];
                                    $vTitle = $db_master[$i]['vTitle'];
                                    $eDefault = $db_master[$i]['eDefault'];
                                    $vValue = 'vBidSubTitle_'.$vCode;
                                    ${$vValue} = $userEditDataArr[$vValue];
                                    $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                    ?>
                                                    <?php
                                    $page_title_class = 'col-lg-12';
                                    if (count($db_master) > 1) {
                                        if ($EN_available) {
                                            if ('EN' === $vCode) {
                                                $page_title_class = 'col-md-9 col-sm-9';
                                            }
                                        } else {
                                            if ($vCode === $default_lang) {
                                                $page_title_class = 'col-md-9 col-sm-9';
                                            }
                                        }
                                    }
                                    ?>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Subtitle (<?php echo $vTitle; ?>
                                                                ) <?php echo $required_msg; ?></label>
                                                        </div>
                                                        <div class="<?php echo $page_title_class; ?>">
                                                            <input type="text" class="form-control"
                                                                   name="<?php echo $vValue; ?>" id="<?php echo $vValue; ?>"
                                                                   value="<?php echo ${$vValue}; ?>"
                                                                   data-originalvalue="<?php echo ${$vValue}; ?>"
                                                                   placeholder="<?php echo $vTitle; ?> Value">
                                                            <div class="text-danger" id="<?php echo $vValue.'_error'; ?>"
                                                                 style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                        </div>
                                                        <?php
                                        if (count($db_master) > 1) {
                                            if ($EN_available) {
                                                if ('EN' === $vCode) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vBidSubTitle_', 'EN');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                } else {
                                                    if ($vCode === $default_lang) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vBidSubTitle_', '<?php echo $default_lang; ?>');">
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
                                                    <strong><?php echo $langage_lbl['LBL_NOTE']; ?>:
                                                    </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                <div class="nimot-class-but" style="margin-bottom: 0">
                                                    <button type="button" class="save" style="margin-left: 0 !important"
                                                            onclick="saveBidSubTitle()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                    <button type="button" class="btn btn-danger btn-ok"
                                                            data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vBidSubTitle_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control"
                                               id="vBidTitle_<?php echo $default_lang; ?>"
                                               name="vBidTitle_<?php echo $default_lang; ?>"
                                               value="<?php echo $userEditDataArr['vBidTitle_'.$default_lang]; ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Subtitle</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control"
                                               id="vBidSubTitle_<?php echo $default_lang; ?>"
                                               name="vBidSubTitle_<?php echo $default_lang; ?>"
                                               value="<?php echo $userEditDataArr['vBidSubTitle_'.$default_lang]; ?>">
                                    </div>
                                </div>
                            <?php } ?>
							<div class="row">
                                <div class="col-lg-12">
                                    <div class="admin-notes" style="margin: 0">
                                        <ul>
                                            <li class="show-help-section">Services
                                                <br>
												<a href="<?php echo $tconfig['tsite_url_main_admin']; ?>bidding_master_category.php"  class="btn btn-info" target="_blank">Manage Services</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

						<!--	<h3 class="show-help-section">
                                <i class="fa fa-caret-right"></i>
                                Banner View - Service Bid
                                <i class="fa fa-question-circle show-help-img" data-fancybox
                                   data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/ServiceBidBanner.png"></i>
                            </h3>
                            <div class="row">
                                <div class="col-lg-12">
                                    <a href="<?php echo $tconfig['tsite_url_main_admin']; ?>master_service_category_action.php?id=<?php echo $MasterCategoryArr['Bidding']['iMasterServiceCategoryId']; ?>"
                                       class="btn btn-info" target="_blank">Manage Content
                                    </a>
                                </div>
                            </div>

                            <?php if ('No' === $hide_display_order) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Display Order</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <select class="form-control" name="iDisplayOrderBidBannerView"
                                            id="iDisplayOrderBidBannerView">
                                        <?php for ($i = 1; $i <= $max_display_order; ++$i) { ?>
                                            <option value="<?php echo $i; ?>" <?php echo $i === $iDisplayOrderBidBannerView ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php } ?>
							-->

                        <?php } ?>

                        <?php if ($MODULES_OBJ->isEnableVideoConsultingService()) { ?>
                            <hr/>
                            <h3 class="show-help-section">
                                <i class="fa fa-caret-right"></i>
                                Banner View - Video Consultation
                                <i class="fa fa-question-circle show-help-img" data-fancybox
                                   data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/VideoConsultBanner.png"></i>
                            </h3>
                            <div class="row">
                                <div class="col-lg-12">
                                    <a href="<?php echo $tconfig['tsite_url_main_admin']; ?>master_service_category_action.php?id=<?php echo $MasterCategoryArr['VideoConsult']['iMasterServiceCategoryId']; ?>"
                                       class="btn btn-info" target="_blank">Manage Content
                                    </a>
                                </div>
                            </div>

                            <?php if ('No' === $hide_display_order) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Display Order</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <select class="form-control" name="iDisplayOrderVCBannerView"
                                            id="iDisplayOrderVCBannerView">
                                        <?php for ($i = 1; $i <= $max_display_order; ++$i) { ?>
                                            <option value="<?php echo $i; ?>" <?php echo $i === $iDisplayOrderVCBannerView ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php } ?>
                        <?php } ?>

                        <?php if ($MODULES_OBJ->isUberXFeatureAvailable()) { ?>
                            <hr/>
                            <h3 class="show-help-section">
                                <i class="fa fa-caret-right"></i>
                                Service List View - On-Demand Services
                                <i class="fa fa-question-circle show-help-img" data-fancybox
                                   data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/OnDemandServices.png"></i>
                            </h3>
                            <?php if (count($db_master) > 1) { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vOtherServiceTitle_Default"
                                               name="vOtherServiceTitle_Default"
                                               value="<?php echo $userEditDataArr['vOtherServiceTitle_'.$default_lang]; ?>"
                                               data-originalvalue="<?php echo $userEditDataArr['vOtherServiceTitle_'.$default_lang]; ?>"
                                               readonly="readonly" required>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                data-original-title="Edit" onclick="editOtherServiceTitle('Edit')">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="modal fade" id="OtherServiceTitle_Modal" tabindex="-1" role="dialog"
                                     aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="otherservice_title_modal_action"></span>
                                                    Title
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vOtherServiceTitle_')">
                                                        x
                                                    </button>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                for ($i = 0; $i < $count_all; ++$i) {
                                    $vCode = $db_master[$i]['vCode'];
                                    $vTitle = $db_master[$i]['vTitle'];
                                    $eDefault = $db_master[$i]['eDefault'];
                                    $vValue = 'vOtherServiceTitle_'.$vCode;
                                    ${$vValue} = $userEditDataArr[$vValue];
                                    $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                    ?>
                                                    <?php
                                    $page_title_class = 'col-lg-12';
                                    if (count($db_master) > 1) {
                                        if ($EN_available) {
                                            if ('EN' === $vCode) {
                                                $page_title_class = 'col-md-9 col-sm-9';
                                            }
                                        } else {
                                            if ($vCode === $default_lang) {
                                                $page_title_class = 'col-md-9 col-sm-9';
                                            }
                                        }
                                    }
                                    ?>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Title (<?php echo $vTitle; ?>
                                                                ) <?php echo $required_msg; ?></label>
                                                        </div>
                                                        <div class="<?php echo $page_title_class; ?>">
                                                            <input type="text" class="form-control"
                                                                   name="<?php echo $vValue; ?>" id="<?php echo $vValue; ?>"
                                                                   value="<?php echo ${$vValue}; ?>"
                                                                   data-originalvalue="<?php echo ${$vValue}; ?>"
                                                                   placeholder="<?php echo $vTitle; ?> Value">
                                                            <div class="text-danger" id="<?php echo $vValue.'_error'; ?>"
                                                                 style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                        </div>
                                                        <?php
                                        if (count($db_master) > 1) {
                                            if ($EN_available) {
                                                if ('EN' === $vCode) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vOtherServiceTitle_', 'EN');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                } else {
                                                    if ($vCode === $default_lang) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vOtherServiceTitle_', '<?php echo $default_lang; ?>');">
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
                                                    <strong><?php echo $langage_lbl['LBL_NOTE']; ?>:
                                                    </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                <div class="nimot-class-but" style="margin-bottom: 0">
                                                    <button type="button" class="save" style="margin-left: 0 !important"
                                                            onclick="saveOtherServiceTitle()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                    <button type="button" class="btn btn-danger btn-ok"
                                                            data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vOtherServiceTitle_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Subtitle</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vOtherServiceSubTitle_Default"
                                               name="vOtherServiceSubTitle_Default"
                                               value="<?php echo $userEditDataArr['vOtherServiceSubTitle_'.$default_lang]; ?>"
                                               data-originalvalue="<?php echo $userEditDataArr['vOtherServiceSubTitle_'.$default_lang]; ?>"
                                               readonly="readonly" required>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                data-original-title="Edit" onclick="editOtherServiceSubTitle('Edit')">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="modal fade" id="OtherServiceSubTitle_Modal" tabindex="-1" role="dialog"
                                     aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="otherservice_subtitle_modal_action"></span>
                                                    Title
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vOtherServiceSubTitle_')">
                                                        x
                                                    </button>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                for ($i = 0; $i < $count_all; ++$i) {
                                    $vCode = $db_master[$i]['vCode'];
                                    $vTitle = $db_master[$i]['vTitle'];
                                    $eDefault = $db_master[$i]['eDefault'];
                                    $vValue = 'vOtherServiceSubTitle_'.$vCode;
                                    ${$vValue} = $userEditDataArr[$vValue];
                                    $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                    ?>
                                                    <?php
                                    $page_title_class = 'col-lg-12';
                                    if (count($db_master) > 1) {
                                        if ($EN_available) {
                                            if ('EN' === $vCode) {
                                                $page_title_class = 'col-md-9 col-sm-9';
                                            }
                                        } else {
                                            if ($vCode === $default_lang) {
                                                $page_title_class = 'col-md-9 col-sm-9';
                                            }
                                        }
                                    }
                                    ?>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Subtitle (<?php echo $vTitle; ?>
                                                                ) <?php echo $required_msg; ?></label>
                                                        </div>
                                                        <div class="<?php echo $page_title_class; ?>">
                                                            <input type="text" class="form-control"
                                                                   name="<?php echo $vValue; ?>" id="<?php echo $vValue; ?>"
                                                                   value="<?php echo ${$vValue}; ?>"
                                                                   data-originalvalue="<?php echo ${$vValue}; ?>"
                                                                   placeholder="<?php echo $vTitle; ?> Value">
                                                            <div class="text-danger" id="<?php echo $vValue.'_error'; ?>"
                                                                 style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                        </div>
                                                        <?php
                                        if (count($db_master) > 1) {
                                            if ($EN_available) {
                                                if ('EN' === $vCode) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vOtherServiceSubTitle_', 'EN');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                } else {
                                                    if ($vCode === $default_lang) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vOtherServiceSubTitle_', '<?php echo $default_lang; ?>');">
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
                                                    <strong><?php echo $langage_lbl['LBL_NOTE']; ?>:
                                                    </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                <div class="nimot-class-but" style="margin-bottom: 0">
                                                    <button type="button" class="save" style="margin-left: 0 !important"
                                                            onclick="saveOtherServiceSubTitle()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                    <button type="button" class="btn btn-danger btn-ok"
                                                            data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vOtherServiceSubTitle_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control"
                                               id="vOtherServiceTitle_<?php echo $default_lang; ?>"
                                               name="vOtherServiceTitle_<?php echo $default_lang; ?>"
                                               value="<?php echo $userEditDataArr['vOtherServiceTitle_'.$default_lang]; ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Subtitle</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control"
                                               id="vOtherServiceSubTitle_<?php echo $default_lang; ?>"
                                               name="vOtherServiceSubTitle_<?php echo $default_lang; ?>"
                                               value="<?php echo $userEditDataArr['vOtherServiceSubTitle_'.$default_lang]; ?>">
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="admin-notes" style="margin: 0">
                                        <ul>
                                            <li class="show-help-section">Services
                                                <br>
                                                <button type="button" class="btn btn-info" data-toggle="modal"
                                                        data-target="#other_services_modal">Manage Services
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="modal fade" id="other_services_modal" tabindex="-1" role="dialog"
                                     aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    On-Demand Services
                                                    <button type="button" class="close" data-dismiss="modal">x</button>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <p>
                                                    <strong>Note:</strong>
                                                    Enable any 3 service categories from below list to be shown on App
                                                    home screen. All other service categories will be shown under more.
                                                    <br>
                                                    Icons uploaded will only be shown on App home screen and not under
                                                    more section.
                                                </p>
                                                <input type="hidden" name="saveOtherServices" id="saveOtherServices"
                                                       value="No">
                                                <table class="table table-striped table-bordered table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th style="text-align: center;">Icon</th>
                                                        <th>Service Category</th>
                                                        <th>Display Order</th>
                                                        <th>Upload Icon</th>
                                                        <th>Status</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                    foreach ($ufxData as $ufxService) {
                                        $vServiceImg = '';
                                        $vServiceStatus = '';
                                        $vServiceImgOld = '';
                                        $vServiceDisplay = 'style="display: none"';
                                        $vServiceDisplayOrder = '1';
                                        if (isset($tServiceDetailsArr['iVehicleCategoryId_'.$ufxService['iVehicleCategoryId']])) {
                                            $tServiceDetails = $tServiceDetailsArr['iVehicleCategoryId_'.$ufxService['iVehicleCategoryId']];
                                            if (!empty($tServiceDetails['vImage'])) {
                                                $vServiceImg = $tconfig['tsite_url'].'resizeImg.php?w=50&src='.$tconfig['tsite_upload_app_home_screen_images'].'AppHomeScreen/'.$tServiceDetails['vImage'];
                                            }
                                            $vServiceImgOld = $tServiceDetails['vImage'];
                                            if ('Active' === $tServiceDetails['eStatus']) {
                                                $vServiceStatus = 'checked';
                                                $vServiceDisplay = '';
                                                $vServiceDisplayOrder = $tServiceDetails['iDisplayOrder'];
                                            }
                                        }
                                        ?>
                                                        <tr>
                                                            <td style="text-align: center; vertical-align: middle;">
                                                                <?php if (!empty($vServiceImg)) { ?>
                                                                    <img src="<?php echo $vServiceImg; ?>">
                                                                <?php } else { ?>
                                                                    --
                                                                <?php } ?>
                                                            </td>
                                                            <td style="vertical-align: middle;"><?php echo $ufxService['vCategoryName']; ?></td>
                                                            <td>
                                                                <select class="form-control"
                                                                        name="iDisplayOrderOtherServiceArr[]" <?php echo $vServiceDisplay; ?>>
                                                                    <?php for ($disp_order = 1; $disp_order <= count($ufxData); ++$disp_order) { ?>
                                                                        <option value="<?php echo $disp_order; ?>" <?php echo $vServiceDisplayOrder === $disp_order ? 'selected' : ''; ?>><?php echo $disp_order; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="file" class="form-control"
                                                                       name="vOtherServiceImage[]" <?php echo $vServiceDisplay; ?>>
                                                                <input type="hidden" class="form-control"
                                                                       name="vOtherServiceImageOld[]"
                                                                       value="<?php echo $vServiceImgOld; ?>">
                                                            </td>
                                                            <td>
                                                                <div class="make-switch" data-on="success"
                                                                     data-off="warning">
                                                                    <input type="checkbox" name="iVehicleCategoryId[]"
                                                                           value="<?php echo $ufxService['iVehicleCategoryId']; ?>" <?php echo $vServiceStatus; ?> />
                                                                </div>
                                                                <input type="hidden" name="iVehicleCategoryIdVal[]"
                                                                       value="<?php echo $ufxService['iVehicleCategoryId']; ?>">
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="modal-footer" style="text-align: left">
                                                <button type="button" class="btn btn-default"
                                                        onclick="saveOnDemandServices('Yes')">Save
                                                </button>
                                                <button type="button" class="btn btn-default"
                                                        onclick="saveOnDemandServices('No')">Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if ('No' === $hide_display_order) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Display Order</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <select class="form-control" name="iDisplayOrderOtherService"
                                            id="iDisplayOrderOtherService">
                                        <?php for ($i = 1; $i <= $max_display_order; ++$i) { ?>
                                            <option value="<?php echo $i; ?>" <?php echo $i === $iDisplayOrderOtherService ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php } ?>
                        <?php } ?>
                        <hr/>
                        <h3 class="show-help-section">
                            <i class="fa fa-caret-right"></i>
                            Promotional Banner View
                            <i class="fa fa-question-circle show-help-img" data-fancybox
                               data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/PromotionalBanner.png"></i>
                        </h3>
                        <div class="row">
                            <div class="col-lg-12">
                                <a href="<?php echo $tconfig['tsite_url_main_admin']; ?>app_banner.php?iVehicleCategoryId=<?php echo $promotionalCategoryId; ?>&eFor=Promotion"
                                   class="btn btn-info" target="_blank">Manage Banner
                                </a>
                            </div>
                        </div>
                        <?php if ('No' === $hide_display_order) { ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <label>Display Order</label>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <select class="form-control" name="iDisplayOrderPromoBannerView"
                                        id="iDisplayOrderPromoBannerView">
                                    <?php for ($i = 1; $i <= $max_display_order; ++$i) { ?>
                                        <option value="<?php echo $i; ?>" <?php echo $i === $iDisplayOrderPromoBannerView ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <?php } ?>



                        <?php if ($MODULES_OBJ->isEnableRentItemService() || $MODULES_OBJ->isEnableRentCarsService() || $MODULES_OBJ->isEnableRentEstateService()) { ?>
                            <hr/>

							 <h3 class="show-help-section">
                            <i class="fa fa-caret-right"></i>
                            Introduction View - Buy, Sell & Rent
                            <i class="fa fa-question-circle show-help-img" data-fancybox
                               data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/IntroView.png"></i>
                        </h3>
                        <?php if (count($db_master) > 1) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vIntroBuyTitle_Default"
                                           name="vIntroBuyTitle_Default"
                                           value="<?php echo $userEditDataArr['vIntroBuyTitle_'.$default_lang]; ?>"
                                           data-originalvalue="<?php echo $userEditDataArr['vIntroBuyTitle_'.$default_lang]; ?>"
                                           readonly="readonly" required>
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                            data-original-title="Edit" onclick="editIntroBuyTitle('Edit')">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="modal fade" id="IntroBuyTitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                 data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="introBuy_title_modal_action"></span>
                                                Title
                                                <button type="button" class="close" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vIntroBuyTitle_')">x
                                                </button>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                                            for ($i = 0; $i < $count_all; ++$i) {
                                                $vCode = $db_master[$i]['vCode'];
                                                $vTitle = $db_master[$i]['vTitle'];
                                                $eDefault = $db_master[$i]['eDefault'];
                                                $vValue = 'vIntroBuyTitle_'.$vCode;
                                                ${$vValue} = $userEditDataArr[$vValue];
                                                $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                                ?>
                                                <?php
                                                $page_title_class = 'col-lg-12';
                                                if (count($db_master) > 1) {
                                                    if ($EN_available) {
                                                        if ('EN' === $vCode) {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    } else {
                                                        if ($vCode === $default_lang) {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    }
                                                }
                                                ?>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label>Title (<?php echo $vTitle; ?>
                                                            ) <?php echo $required_msg; ?></label>
                                                    </div>
                                                    <div class="<?php echo $page_title_class; ?>">
                                                        <input type="text" class="form-control" name="<?php echo $vValue; ?>"
                                                               id="<?php echo $vValue; ?>" value="<?php echo ${$vValue}; ?>"
                                                               data-originalvalue="<?php echo ${$vValue}; ?>"
                                                               placeholder="<?php echo $vTitle; ?> Value">
                                                        <div class="text-danger" id="<?php echo $vValue.'_error'; ?>"
                                                             style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                    </div>
                                                    <?php
                                                    if (count($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ('EN' === $vCode) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vIntroBuyTitle_', 'EN');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                            } else {
                                                                if ($vCode === $default_lang) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vIntroBuyTitle_', '<?php echo $default_lang; ?>');">
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
                                                <strong><?php echo $langage_lbl['LBL_NOTE']; ?>:
                                                </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                        onclick="saveIntroBuyTitle()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vIntroBuyTitle_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                            </div>
                                        </div>
                                        <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Subtitle</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vIntroBuySubTitle_Default"
                                           name="vIntroBuySubTitle_Default"
                                           value="<?php echo $userEditDataArr['vIntroBuySubTitle_'.$default_lang]; ?>"
                                           data-originalvalue="<?php echo $userEditDataArr['vIntroBuySubTitle_'.$default_lang]; ?>"
                                           readonly="readonly" required>
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                            data-original-title="Edit" onclick="editIntroBuySubTitle('Edit')">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="modal fade" id="IntroBuySubTitle_Modal" tabindex="-1" role="dialog"
                                 aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="introBuy_subtitle_modal_action"></span>
                                                Title
                                                <button type="button" class="close" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vIntroBuySubTitle_')">x
                                                </button>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                            for ($i = 0; $i < $count_all; ++$i) {
                                $vCode = $db_master[$i]['vCode'];
                                $vTitle = $db_master[$i]['vTitle'];
                                $eDefault = $db_master[$i]['eDefault'];
                                $vValue = 'vIntroBuySubTitle_'.$vCode;
                                ${$vValue} = $userEditDataArr[$vValue];
                                $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                ?>
                                                <?php
                                $page_title_class = 'col-lg-12';
                                if (count($db_master) > 1) {
                                    if ($EN_available) {
                                        if ('EN' === $vCode) {
                                            $page_title_class = 'col-md-9 col-sm-9';
                                        }
                                    } else {
                                        if ($vCode === $default_lang) {
                                            $page_title_class = 'col-md-9 col-sm-9';
                                        }
                                    }
                                }
                                ?>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label>Subtitle (<?php echo $vTitle; ?>
                                                            ) <?php echo $required_msg; ?></label>
                                                    </div>
                                                    <div class="<?php echo $page_title_class; ?>">
                                                        <input type="text" class="form-control" name="<?php echo $vValue; ?>"
                                                               id="<?php echo $vValue; ?>" value="<?php echo ${$vValue}; ?>"
                                                               data-originalvalue="<?php echo ${$vValue}; ?>"
                                                               placeholder="<?php echo $vTitle; ?> Value">
                                                        <div class="text-danger" id="<?php echo $vValue.'_error'; ?>"
                                                             style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                    </div>
                                                    <?php
                                    if (count($db_master) > 1) {
                                        if ($EN_available) {
                                            if ('EN' === $vCode) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vIntroBuySubTitle_', 'EN');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                            } else {
                                                if ($vCode === $default_lang) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vIntroBuySubTitle_', '<?php echo $default_lang; ?>');">
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
                                                <strong><?php echo $langage_lbl['LBL_NOTE']; ?>:
                                                </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                        onclick="saveIntroBuySubTitle()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vIntroBuySubTitle_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                            </div>
                                        </div>
                                        <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vIntroBuyTitle_<?php echo $default_lang; ?>"
                                           name="vIntroBuyTitle_<?php echo $default_lang; ?>"
                                           value="<?php echo $userEditDataArr['vIntroBuyTitle_'.$default_lang]; ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Subtitle</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vIntroBuySubTitle_<?php echo $default_lang; ?>"
                                           name="vIntroBuySubTitle_<?php echo $default_lang; ?>"
                                           value="<?php echo $userEditDataArr['vIntroBuySubTitle_'.$default_lang]; ?>">
                                </div>
                            </div>
                        <?php } ?>
                        <hr/>

                            <h3 class="show-help-section">
                                <i class="fa fa-caret-right"></i>
                                Banner View - Buy, Sell & Rent
                                <i class="fa fa-question-circle show-help-img" data-fancybox
                                   data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/BuySellRent.png"></i>
                            </h3>
                            <?php if (count($db_master) > 1) { ?>
                                <div class="row" <?php echo $display_section; ?>>
                                    <div class="col-lg-12">
                                        <label>Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vBuySellTitle_Default"
                                               name="vBuySellTitle_Default"
                                               value="<?php echo $userEditDataArr['vBuySellTitle_'.$default_lang]; ?>"
                                               data-originalvalue="<?php echo $userEditDataArr['vBuySellTitle_'.$default_lang]; ?>"
                                               readonly="readonly" required>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                data-original-title="Edit" onclick="editBuySellTitle('Edit')">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="modal fade" id="BuySellTitle_Modal" tabindex="-1" role="dialog"
                                     aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="buysell_title_modal_action"></span>
                                                    Title
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vBuySellTitle_')">x
                                                    </button>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                for ($i = 0; $i < $count_all; ++$i) {
                                    $vCode = $db_master[$i]['vCode'];
                                    $vTitle = $db_master[$i]['vTitle'];
                                    $eDefault = $db_master[$i]['eDefault'];
                                    $vValue = 'vBuySellTitle_'.$vCode;
                                    ${$vValue} = $userEditDataArr[$vValue];
                                    $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                    ?>
                                                    <?php
                                    $page_title_class = 'col-lg-12';
                                    if (count($db_master) > 1) {
                                        if ($EN_available) {
                                            if ('EN' === $vCode) {
                                                $page_title_class = 'col-md-9 col-sm-9';
                                            }
                                        } else {
                                            if ($vCode === $default_lang) {
                                                $page_title_class = 'col-md-9 col-sm-9';
                                            }
                                        }
                                    }
                                    ?>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Title (<?php echo $vTitle; ?>
                                                                ) <?php echo $required_msg; ?></label>
                                                        </div>
                                                        <div class="<?php echo $page_title_class; ?>">
                                                            <input type="text" class="form-control"
                                                                   name="<?php echo $vValue; ?>" id="<?php echo $vValue; ?>"
                                                                   value="<?php echo ${$vValue}; ?>"
                                                                   data-originalvalue="<?php echo ${$vValue}; ?>"
                                                                   placeholder="<?php echo $vTitle; ?> Value">
                                                            <div class="text-danger" id="<?php echo $vValue.'_error'; ?>"
                                                                 style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                        </div>
                                                        <?php
                                        if (count($db_master) > 1) {
                                            if ($EN_available) {
                                                if ('EN' === $vCode) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vBuySellTitle_', 'EN');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                } else {
                                                    if ($vCode === $default_lang) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vBuySellTitle_', '<?php echo $default_lang; ?>');">
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
                                                    <strong><?php echo $langage_lbl['LBL_NOTE']; ?>:
                                                    </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                <div class="nimot-class-but" style="margin-bottom: 0">
                                                    <button type="button" class="save" style="margin-left: 0 !important"
                                                            onclick="saveBuySellTitle()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                    <button type="button" class="btn btn-danger btn-ok"
                                                            data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vBuySellTitle_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vBuySellTitle_<?php echo $default_lang; ?>"
                                               name="vBuySellTitle_<?php echo $default_lang; ?>"
                                               value="<?php echo $userEditDataArr['vBuySellTitle_'.$default_lang]; ?>">
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="admin-notes" style="margin: 0">
                                        <ul>
                                            <?php if ($MODULES_OBJ->isEnableRentEstateService()) { ?>
                                                <li class="show-help-section">Buy & Sell Real Estate
                                                    <br>
                                                    <a href="<?php echo $tconfig['tsite_url_main_admin']; ?>master_service_category_action.php?id=<?php echo $MasterCategoryArr['RentEstate']['iMasterServiceCategoryId']; ?>"
                                                       class="btn btn-info" target="_blank">Manage Content
                                                    </a>
                                                </li>
                                            <?php }
                                            if ($MODULES_OBJ->isEnableRentCarsService()) { ?>
                                                <li class="show-help-section">Buy, Sell & Rent Cars
                                                    <br>
                                                    <a href="<?php echo $tconfig['tsite_url_main_admin']; ?>master_service_category_action.php?id=<?php echo $MasterCategoryArr['RentCars']['iMasterServiceCategoryId']; ?>"
                                                       class="btn btn-info" target="_blank">Manage Content
                                                    </a>
                                                </li>
                                            <?php }
                                            if ($MODULES_OBJ->isEnableRentItemService()) { ?>
                                                <li class="show-help-section">Buy, Sell & Rent General Items
                                                    <br>
                                                    <a href="<?php echo $tconfig['tsite_url_main_admin']; ?>master_service_category_action.php?id=<?php echo $MasterCategoryArr['RentItem']['iMasterServiceCategoryId']; ?>"
                                                       class="btn btn-info" target="_blank">Manage Content
                                                    </a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <?php if ('No' === $hide_display_order) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Display Order</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <select class="form-control" name="iDisplayOrderBuySell" id="iDisplayOrderBuySell">
                                        <?php for ($i = 1; $i <= $max_display_order; ++$i) { ?>
                                            <option value="<?php echo $i; ?>" <?php echo $i === $iDisplayOrderBuySell ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php } ?>
                        <?php } ?>

                        <?php if ($MODULES_OBJ->isEnableRideShareService()) { ?>
                            <hr/>
                            <h3 class="show-help-section">
                                <i class="fa fa-caret-right"></i>
                                Banner View - Ride Sharing/Car Pool
                                <i class="fa fa-question-circle show-help-img" data-fancybox
                                   data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/RideShare.png"></i>
                            </h3>
                            <div class="row">
                                <div class="col-lg-12">
                                    <a href="<?php echo $tconfig['tsite_url_main_admin']; ?>master_service_category_action.php?id=<?php echo $MasterCategoryArr['RideShare']['iMasterServiceCategoryId']; ?>"
                                       class="btn btn-info" target="_blank" style="margin-right: 15px;">Manage Content
                                    </a>

                                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#RideShareModal">Edit Details</button>


                                </div>
                            </div>

                            <?php if ('No' === $hide_display_order) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Display Order</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <select class="form-control" name="iDisplayOrderRideShare"
                                            id="iDisplayOrderRideShare">
                                        <?php for ($i = 1; $i <= $max_display_order; ++$i) { ?>
                                            <option value="<?php echo $i; ?>" <?php echo $i === $iDisplayOrderRideShare ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php } ?>
                        <?php } ?>

                        <?php if ($MODULES_OBJ->isEnableMedicalServices()) { ?>
                            <hr/>
                            <h3 class="show-help-section">
                                <i class="fa fa-caret-right"></i>
                                Service Banner View - Medical Services
                                <i class="fa fa-question-circle show-help-img" data-fancybox
                                   data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/MedicalServices.png"></i>
                            </h3>
                            <?php if (count($db_master) > 1) { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vMedicalServiceTitle_Default"
                                               name="vMedicalServiceTitle_Default"
                                               value="<?php echo $userEditDataArr['vMedicalServiceTitle_'.$default_lang]; ?>"
                                               data-originalvalue="<?php echo $userEditDataArr['vMedicalServiceTitle_'.$default_lang]; ?>"
                                               readonly="readonly" required>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                data-original-title="Edit" onclick="editMedicalServiceTitle('Edit')">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="modal fade" id="MedicalServiceTitle_Modal" tabindex="-1" role="dialog"
                                     aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="medicalservice_title_modal_action"></span>
                                                    Title
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vMedicalServiceTitle_')">
                                                        x
                                                    </button>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                for ($i = 0; $i < $count_all; ++$i) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];
                                                    $vValue = 'vMedicalServiceTitle_'.$vCode;
                                                    ${$vValue} = $userEditDataArr[$vValue];
                                                    $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                                    ?>
                                                    <?php
                                                    $page_title_class = 'col-lg-12';
                                                    if (count($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ('EN' === $vCode) {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        } else {
                                                            if ($vCode === $default_lang) {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Title (<?php echo $vTitle; ?>
                                                                ) <?php echo $required_msg; ?></label>
                                                        </div>
                                                        <div class="<?php echo $page_title_class; ?>">
                                                            <input type="text" class="form-control"
                                                                   name="<?php echo $vValue; ?>" id="<?php echo $vValue; ?>"
                                                                   value="<?php echo ${$vValue}; ?>"
                                                                   data-originalvalue="<?php echo ${$vValue}; ?>"
                                                                   placeholder="<?php echo $vTitle; ?> Value">
                                                            <div class="text-danger" id="<?php echo $vValue.'_error'; ?>"
                                                                 style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                        </div>
                                                        <?php
                                                        if (count($db_master) > 1) {
                                                            if ($EN_available) {
                                                                if ('EN' === $vCode) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vMedicalServiceTitle_', 'EN');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                                } else {
                                                                    if ($vCode === $default_lang) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vMedicalServiceTitle_', '<?php echo $default_lang; ?>');">
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
                                                    <strong><?php echo $langage_lbl['LBL_NOTE']; ?>:
                                                    </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                <div class="nimot-class-but" style="margin-bottom: 0">
                                                    <button type="button" class="save" style="margin-left: 0 !important"
                                                            onclick="saveMedicalServiceTitle()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                    <button type="button" class="btn btn-danger btn-ok"
                                                            data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vMedicalServiceTitle_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control"
                                               id="vMedicalServiceTitle_<?php echo $default_lang; ?>"
                                               name="vMedicalServiceTitle_<?php echo $default_lang; ?>"
                                               value="<?php echo $userEditDataArr['vMedicalServiceTitle_'.$default_lang]; ?>">
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="admin-notes" style="margin: 0">
                                        <ul>
                                            <?php foreach ($MEDICAL_SERVICES_ARR as $MEDICAL_SERVICE) { ?>
                                                <li class="show-help-section">
                                                    <?php echo $MEDICAL_SERVICE['ServiceTitle']; ?>
                                                    <div>
                                                        <button type="button" class="btn btn-info" data-toggle="modal"
                                                                data-target="#<?php echo $MEDICAL_SERVICE['ModalKey']; ?>"
                                                                style="margin-right: 15px;">Edit Details
                                                        </button>
                                                        <button type="button" class="btn btn-info" data-toggle="modal"
                                                                data-target="#<?php echo $MEDICAL_SERVICE['ModalManageServiceKey']; ?>">
                                                            Manage Services
                                                        </button>
                                                        <div class="modal fade"
                                                             id="<?php echo $MEDICAL_SERVICE['ModalManageServiceKey']; ?>"
                                                             tabindex="-1" role="dialog" aria-hidden="true"
                                                             data-backdrop="static" data-keyboard="false">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content nimot-class">
                                                                    <div class="modal-header">
                                                                        <h5>
                                                                            Medical Services
                                                                            - <?php echo $MEDICAL_SERVICE['ServiceTitle']; ?>
                                                                            <button type="button" class="close"
                                                                                    data-dismiss="modal">x
                                                                            </button>
                                                                        </h5>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>
                                                                            <strong>Note:</strong>
                                                                            Enable
                                                                            any <?php echo 'MoreService' !== $MEDICAL_SERVICE['ManageServiceKey'] ? '2' : '3'; ?>
                                                                            service categories from below list to be
                                                                            shown on App home
                                                                            screen. <?php if ('MoreService' !== $MEDICAL_SERVICE['ManageServiceKey']) { ?>All other service categories will be shown under more. <?php } ?>
                                                                            <br>
                                                                            Icons uploaded will only be shown on App
                                                                            home screen and not under more section.
                                                                        </p>
                                                                        <input type="hidden"
                                                                               name="<?php echo $MEDICAL_SERVICE['HiddenInput']; ?>"
                                                                               id="<?php echo $MEDICAL_SERVICE['HiddenInput']; ?>"
                                                                               value="No">
                                                                        <table class="table table-striped table-bordered table-hover">
                                                                            <thead>
                                                                            <tr>
                                                                                <th style="text-align: center;">Icon
                                                                                </th>
                                                                                <th>Service Category</th>
                                                                                <th>Display Order</th>
                                                                                <th>Upload Icon</th>
                                                                                <th>Status</th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                            <?php
                                                            foreach ($MEDICAL_SERVICE['ServicesArr'] as $ServiceMS) {
                                                                $vServiceImg = '';
                                                                $vServiceStatus = '';
                                                                $vServiceImgOld = '';
                                                                $vServiceDisplay = 'style="display: none"';
                                                                $vServiceDisplayOrder = '1';
                                                                if (isset($tServiceDetailsMSArr[$MEDICAL_SERVICE['ManageServiceKey']])) {
                                                                    $tServiceDetails = $tServiceDetailsMSArr[$MEDICAL_SERVICE['ManageServiceKey']]['iVehicleCategoryId_'.$ServiceMS['iVehicleCategoryId']];
                                                                    if (!empty($tServiceDetails['vImage'])) {
                                                                        $vServiceImg = $tconfig['tsite_url'].'resizeImg.php?w=50&src='.$tconfig['tsite_upload_app_home_screen_images'].'AppHomeScreen/'.$tServiceDetails['vImage'];
                                                                    }
                                                                    $vServiceImgOld = $tServiceDetails['vImage'];
                                                                    if ('Active' === $tServiceDetails['eStatus']) {
                                                                        $vServiceStatus = 'checked';
                                                                        $vServiceDisplay = '';
                                                                        $vServiceDisplayOrder = $tServiceDetails['iDisplayOrder'];
                                                                    }
                                                                }
                                                                ?>
                                                                                <tr>
                                                                                    <td style="text-align: center; vertical-align: middle;">
                                                                                        <?php if (!empty($vServiceImg)) { ?>
                                                                                            <img src="<?php echo $vServiceImg; ?>">
                                                                                        <?php } else { ?>
                                                                                            --
                                                                                        <?php } ?>
                                                                                    </td>
                                                                                    <td style="vertical-align: middle;"><?php echo $ServiceMS['vCategoryName']; ?></td>
                                                                                    <td>
                                                                                        <select class="form-control"
                                                                                                name="iDisplayOrder<?php echo $MEDICAL_SERVICE['ManageServiceKey']; ?>MSArr[]" <?php echo $vServiceDisplay; ?>>
                                                                                            <?php for ($disp_order = 1; $disp_order <= count($OnDemandServicesArr); ++$disp_order) { ?>
                                                                                                <option value="<?php echo $disp_order; ?>" <?php echo $vServiceDisplayOrder === $disp_order ? 'selected' : ''; ?>><?php echo $disp_order; ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </td>
                                                                                    <td>
                                                                                        <input type="file"
                                                                                               class="form-control"
                                                                                               name="v<?php echo $MEDICAL_SERVICE['ManageServiceKey']; ?>MSImage[]" <?php echo $vServiceDisplay; ?>>
                                                                                        <input type="hidden"
                                                                                               class="form-control"
                                                                                               name="v<?php echo $MEDICAL_SERVICE['ManageServiceKey']; ?>MSImageOld[]"
                                                                                               value="<?php echo $vServiceImgOld; ?>">
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="make-switch"
                                                                                             data-on="success"
                                                                                             data-off="warning">
                                                                                            <input type="checkbox"
                                                                                                   name="iVehicleCategoryId<?php echo $MEDICAL_SERVICE['ManageServiceSuffix']; ?>[]"
                                                                                                   value="<?php echo $ServiceMS['iVehicleCategoryId']; ?>" <?php echo $vServiceStatus; ?> />
                                                                                        </div>
                                                                                        <input type="hidden"
                                                                                               name="iVehicleCategoryIdVal<?php echo $MEDICAL_SERVICE['ManageServiceSuffix']; ?>[]"
                                                                                               value="<?php echo $ServiceMS['iVehicleCategoryId']; ?>">
                                                                                    </td>
                                                                                </tr>
                                                                            <?php } ?>
                                                                            </tbody>
                                                                        </table>
                                                                        <a href="<?php echo $tconfig['tsite_url_main_admin']; ?>vehicle_category.php?eType=MedicalServices"
                                                                           class="btn btn-info" target="_blank">Click
                                                                            here to add more services
                                                                        </a>
                                                                    </div>
                                                                    <div class="modal-footer" style="text-align: left">
                                                                        <button type="button" class="btn btn-default"
                                                                                onclick="saveMS<?php echo $MEDICAL_SERVICE['ManageServiceKey']; ?>('Yes')">
                                                                            Save
                                                                        </button>
                                                                        <button type="button" class="btn btn-default"
                                                                                onclick="saveMS<?php echo $MEDICAL_SERVICE['ManageServiceKey']; ?>('No')">
                                                                            Cancel
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <?php if ('No' === $hide_display_order) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Display Order</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <select class="form-control" name="iDisplayOrderMedicalService"
                                            id="iDisplayOrderMedicalService">
                                        <?php for ($i = 1; $i <= $max_display_order; ++$i) { ?>
                                            <option value="<?php echo $i; ?>" <?php echo $i === $iDisplayOrderMedicalService ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php } ?>
                        <?php } ?>

                        <?php if ($MODULES_OBJ->isEnableTrackAnyServiceFeature()) { ?>
                            <hr/>
                            <h3 class="show-help-section">
                                <i class="fa fa-caret-right"></i>
                                Service Banner View - Track Your Members
                                <i class="fa fa-question-circle show-help-img" data-fancybox
                                   data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/TrackService.png"></i>
                            </h3>
                            <div class="row">
                                <div class="col-lg-12">
                                    <a href="<?php echo $tconfig['tsite_url_main_admin']; ?>master_service_category_action.php?id=<?php echo $MasterCategoryArr['TrackAnyService']['iMasterServiceCategoryId']; ?>"
                                       class="btn btn-info" target="_blank">Manage Content
                                    </a>
                                </div>
                            </div>
                            <?php if (count($db_master) > 1) { ?>
                                <div class="row" <?php echo $display_section; ?>>
                                    <div class="col-lg-12">
                                        <label>Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vTrackServiceTitle_Default"
                                               name="vTrackServiceTitle_Default"
                                               value="<?php echo $userEditDataArr['vTrackServiceTitle_'.$default_lang]; ?>"
                                               data-originalvalue="<?php echo $userEditDataArr['vTrackServiceTitle_'.$default_lang]; ?>"
                                               readonly="readonly" required>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                data-original-title="Edit" onclick="editTrackServiceTitle('Edit')">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="modal fade" id="TrackServiceTitle_Modal" tabindex="-1" role="dialog"
                                     aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="trackservice_title_modal_action"></span>
                                                    Title
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vTrackServiceTitle_')">
                                                        x
                                                    </button>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                for ($i = 0; $i < $count_all; ++$i) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];
                                                    $vValue = 'vTrackServiceTitle_'.$vCode;
                                                    ${$vValue} = $userEditDataArr[$vValue];
                                                    $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                                    ?>
                                                    <?php
                                                    $page_title_class = 'col-lg-12';
                                                    if (count($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ('EN' === $vCode) {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        } else {
                                                            if ($vCode === $default_lang) {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Title (<?php echo $vTitle; ?>
                                                                ) <?php echo $required_msg; ?></label>
                                                        </div>
                                                        <div class="<?php echo $page_title_class; ?>">
                                                            <input type="text" class="form-control"
                                                                   name="<?php echo $vValue; ?>" id="<?php echo $vValue; ?>"
                                                                   value="<?php echo ${$vValue}; ?>"
                                                                   data-originalvalue="<?php echo ${$vValue}; ?>"
                                                                   placeholder="<?php echo $vTitle; ?> Value">
                                                            <div class="text-danger" id="<?php echo $vValue.'_error'; ?>"
                                                                 style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                        </div>
                                                        <?php
                                                        if (count($db_master) > 1) {
                                                            if ($EN_available) {
                                                                if ('EN' === $vCode) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vTrackServiceTitle_', 'EN');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                                } else {
                                                                    if ($vCode === $default_lang) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vTrackServiceTitle_', '<?php echo $default_lang; ?>');">
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
                                                    <strong><?php echo $langage_lbl['LBL_NOTE']; ?>:
                                                    </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                <div class="nimot-class-but" style="margin-bottom: 0">
                                                    <button type="button" class="save" style="margin-left: 0 !important"
                                                            onclick="saveTrackServiceTitle()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                    <button type="button" class="btn btn-danger btn-ok"
                                                            data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vTrackServiceTitle_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control"
                                               id="vTrackServiceTitle_<?php echo $default_lang; ?>"
                                               name="vTrackServiceTitle_<?php echo $default_lang; ?>"
                                               value="<?php echo $userEditDataArr['vTrackServiceTitle_'.$default_lang]; ?>">
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if ('No' === $hide_display_order) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Display Order</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <select class="form-control" name="iDisplayOrderTrackService"
                                            id="iDisplayOrderTrackService">
                                        <?php for ($i = 1; $i <= $max_display_order; ++$i) { ?>
                                            <option value="<?php echo $i; ?>" <?php echo $i === $iDisplayOrderTrackService ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php } ?>
                        <?php } ?>

                        <?php if ($MODULES_OBJ->isEnableNearByService()) { ?>
                            <hr/>
                            <h3 class="show-help-section">
                                <i class="fa fa-caret-right"></i>
                                Service List View - Nearby Services
                                <i class="fa fa-question-circle show-help-img" data-fancybox
                                   data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/Nearby.png"></i>
                            </h3>
                            <?php if (count($db_master) > 1) { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vNearbyServiceTitle_Default"
                                               name="vNearbyServiceTitle_Default"
                                               value="<?php echo $userEditDataArr['vNearbyServiceTitle_'.$default_lang]; ?>"
                                               data-originalvalue="<?php echo $userEditDataArr['vNearbyServiceTitle_'.$default_lang]; ?>"
                                               readonly="readonly" required>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                data-original-title="Edit" onclick="editNearbyServiceTitle('Edit')">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="modal fade" id="NearbyServiceTitle_Modal" tabindex="-1" role="dialog"
                                     aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="nearbyservice_title_modal_action"></span>
                                                    Title
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vNearbyServiceTitle_')">
                                                        x
                                                    </button>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                for ($i = 0; $i < $count_all; ++$i) {
                                    $vCode = $db_master[$i]['vCode'];
                                    $vTitle = $db_master[$i]['vTitle'];
                                    $eDefault = $db_master[$i]['eDefault'];
                                    $vValue = 'vNearbyServiceTitle_'.$vCode;
                                    ${$vValue} = $userEditDataArr[$vValue];
                                    $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                    ?>
                                                    <?php
                                    $page_title_class = 'col-lg-12';
                                    if (count($db_master) > 1) {
                                        if ($EN_available) {
                                            if ('EN' === $vCode) {
                                                $page_title_class = 'col-md-9 col-sm-9';
                                            }
                                        } else {
                                            if ($vCode === $default_lang) {
                                                $page_title_class = 'col-md-9 col-sm-9';
                                            }
                                        }
                                    }
                                    ?>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Title (<?php echo $vTitle; ?>
                                                                ) <?php echo $required_msg; ?></label>
                                                        </div>
                                                        <div class="<?php echo $page_title_class; ?>">
                                                            <input type="text" class="form-control"
                                                                   name="<?php echo $vValue; ?>" id="<?php echo $vValue; ?>"
                                                                   value="<?php echo ${$vValue}; ?>"
                                                                   data-originalvalue="<?php echo ${$vValue}; ?>"
                                                                   placeholder="<?php echo $vTitle; ?> Value">
                                                            <div class="text-danger" id="<?php echo $vValue.'_error'; ?>"
                                                                 style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                        </div>
                                                        <?php
                                        if (count($db_master) > 1) {
                                            if ($EN_available) {
                                                if ('EN' === $vCode) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vNearbyServiceTitle_', 'EN');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                } else {
                                                    if ($vCode === $default_lang) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vNearbyServiceTitle_', '<?php echo $default_lang; ?>');">
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
                                                    <strong><?php echo $langage_lbl['LBL_NOTE']; ?>:
                                                    </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                <div class="nimot-class-but" style="margin-bottom: 0">
                                                    <button type="button" class="save" style="margin-left: 0 !important"
                                                            onclick="saveNearbyServiceTitle()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                    <button type="button" class="btn btn-danger btn-ok"
                                                            data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vNearbyServiceTitle_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Title</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control"
                                               id="vNearbyServiceTitle_<?php echo $default_lang; ?>"
                                               name="vNearbyServiceTitle_<?php echo $default_lang; ?>"
                                               value="<?php echo $userEditDataArr['vNearbyServiceTitle_'.$default_lang]; ?>">
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="admin-notes" style="margin: 0">
                                        <ul>
                                            <li class="show-help-section">Nearby Categories
                                                <br>
                                                <button type="button" class="btn btn-info" data-toggle="modal"
                                                        data-target="#nearby_services_modal">Manage Categories
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="modal fade" id="nearby_services_modal" tabindex="-1" role="dialog"
                                     aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    Nearby Categories
                                                    <button type="button" class="close" data-dismiss="modal">x</button>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <p>
                                                    <strong>Note:</strong>
                                                    Enable any 3 nearby categories from below list to be shown on App
                                                    home screen. All other nearby categories will be shown under more.
                                                    <br>
                                                    Icons uploaded will only be shown on App home screen and not under
                                                    more section.
                                                </p>
                                                <input type="hidden" name="saveNearbyServices" id="saveNearbyServices"
                                                       value="No">
                                                <table class="table table-striped table-bordered table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th style="text-align: center;">Icon</th>
                                                        <th>Nearby Category</th>
                                                        <th>Display Order</th>
                                                        <th>Upload Icon</th>
                                                        <th>Status</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                    foreach ($nearbyData as $nearByArr) {
                                        $vServiceImg = '';
                                        $vServiceStatus = '';
                                        $vServiceImgOld = '';
                                        $vServiceDisplay = 'style="display: none"';
                                        $vServiceDisplayOrder = '1';
                                        if (isset($tServiceDetailsNearbyArr['iCategoryId_'.$nearByArr['iCategoryId']])) {
                                            $tServiceDetails = $tServiceDetailsNearbyArr['iCategoryId_'.$nearByArr['iCategoryId']];
                                            if (!empty($tServiceDetails['vImage'])) {
                                                $vServiceImg = $tconfig['tsite_url'].'resizeImg.php?w=50&src='.$tconfig['tsite_upload_app_home_screen_images'].'AppHomeScreen/'.$tServiceDetails['vImage'];
                                            }
                                            $vServiceImgOld = $tServiceDetails['vImage'];
                                            if ('Active' === $tServiceDetails['eStatus']) {
                                                $vServiceStatus = 'checked';
                                                $vServiceDisplay = '';
                                                $vServiceDisplayOrder = $tServiceDetails['iDisplayOrder'];
                                            }
                                        }
                                        ?>
                                                        <tr>
                                                            <td style="text-align: center; vertical-align: middle;">
                                                                <?php if (!empty($vServiceImg)) { ?>
                                                                    <img src="<?php echo $vServiceImg; ?>">
                                                                <?php } else { ?>
                                                                    --
                                                                <?php } ?>
                                                            </td>
                                                            <td style="vertical-align: middle;"><?php echo $nearByArr['vCategory']; ?></td>
                                                            <td>
                                                                <select class="form-control"
                                                                        name="iDisplayOrderNearbyArr[]" <?php echo $vServiceDisplay; ?>>
                                                                    <?php for ($disp_order = 1; $disp_order <= count($nearbyData); ++$disp_order) { ?>
                                                                        <option value="<?php echo $disp_order; ?>" <?php echo $vServiceDisplayOrder === $disp_order ? 'selected' : ''; ?>><?php echo $disp_order; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="file" class="form-control"
                                                                       name="vNearbyImage[]" <?php echo $vServiceDisplay; ?>>
                                                                <input type="hidden" class="form-control"
                                                                       name="vNearbyImageOld[]"
                                                                       value="<?php echo $vServiceImgOld; ?>">
                                                            </td>
                                                            <td>
                                                                <div class="make-switch" data-on="success"
                                                                     data-off="warning">
                                                                    <input type="checkbox" name="iCategoryId[]"
                                                                           value="<?php echo $nearByArr['iCategoryId']; ?>" <?php echo $vServiceStatus; ?> />
                                                                </div>
                                                                <input type="hidden" name="iCategoryIdVal[]"
                                                                       value="<?php echo $nearByArr['iCategoryId']; ?>">
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="modal-footer" style="text-align: left">
                                                <button type="button" class="btn btn-default"
                                                        onclick="saveServicesNearby('Yes')">Save
                                                </button>
                                                <button type="button" class="btn btn-default"
                                                        onclick="saveServicesNearby('No')">Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if ('No' === $hide_display_order) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Display Order</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <select class="form-control" name="iDisplayOrderNearbyService"
                                            id="iDisplayOrderNearbyService">
                                        <?php for ($i = 1; $i <= $max_display_order; ++$i) { ?>
                                            <option value="<?php echo $i; ?>" <?php echo $i === $iDisplayOrderNearbyService ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php } ?>
                        <?php } ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <?php if ($userObj->hasPermission('edit-app-home-screen-view')) { ?>
                                <input type="submit" class="save btn-info" name="submit" id="submit" value="Save"
                                       style="margin-right: 10px">

                                <?php } ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>
<?php if ($MODULES_OBJ->isEnableMedicalServices()) {
    foreach ($MEDICAL_SERVICES_ARR as $MEDICAL_SERVICE) { ?>
        <div class="modal fade" id="<?php echo $MEDICAL_SERVICE['ModalKey']; ?>" tabindex="-1" role="dialog" aria-hidden="true"
             data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content nimot-class">
                    <div class="modal-header">
                        <h5>
                            Medical Services - <?php echo $MEDICAL_SERVICE['ServiceTitle']; ?>
                            <button type="button" class="close" data-dismiss="modal">x</button>
                        </h5>
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="hidden" name="eMedicalServiceCatEdit" id="eMedicalServiceCatEdit"
                                       value="<?php echo $MEDICAL_SERVICE['ManageServiceKey']; ?>">
                                <div class="row pb-10">
                                    <div class="col-lg-12">
                                        <label style="font-size: 13px">Title</label>
                                    </div>
                                    <div class="col-lg-10">
                                        <input type="text" class="form-control"
                                               value="<?php echo $MEDICAL_SERVICE['ServiceTitle']; ?>" readonly disabled>
                                    </div>
                                    <div class="col-lg-2">
                                        <a href="<?php echo $tconfig['tsite_url_main_admin'].'languages.php?option=&keyword='.$MEDICAL_SERVICE['ServiceTitleLabel']; ?>"
                                           class="btn btn-info" target="_blank">
                                            <i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="row pb-10">
                                    <div class="col-lg-12">
                                        <label style="font-size: 13px">Description</label>
                                    </div>
                                    <div class="col-lg-10">
                                        <input type="text" class="form-control"
                                               value="<?php echo $MEDICAL_SERVICE['ServiceDesc']; ?>" readonly disabled>
                                    </div>
                                    <div class="col-lg-2">
                                        <a href="<?php echo $tconfig['tsite_url_main_admin'].'languages.php?option=&keyword='.$MEDICAL_SERVICE['ServiceDescLabel']; ?>"
                                           class="btn btn-info" target="_blank">
                                            <i class="glyphicon glyphicon-pencil" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="row pb-10">
                                    <div class="col-lg-12">
                                        <label style="font-size: 13px">Image</label>
                                    </div>
                                    <div class="col-lg-12">
                                        <?php if (!empty($vImageOld[$MEDICAL_SERVICE['ManageServiceKey']])) { ?>
                                            <div class="marginbottom-10">
                                                <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=100&src='.$tconfig['tsite_upload_app_home_screen_images'].$vImageOld[$MEDICAL_SERVICE['ManageServiceKey']]; ?>">
                                            </div>
                                        <?php } ?>
                                        <input type="file" class="form-control" name="vImage">
                                        <input type="hidden" class="form-control" name="vImage_old"
                                               value="<?php echo $vImageOld[$MEDICAL_SERVICE['ManageServiceKey']]; ?>">
                                    </div>
                                    <div class="col-lg-12">
                                        <strong style="font-size: 13px">Note: Upload only png image size of 360px X
                                            360px.
                                        </strong>
                                    </div>
                                </div>
                                <div class="row pb-10">
                                    <div class="col-lg-12">
                                        <label style="font-size: 13px">Text Color</label>
                                    </div>
                                    <div class="col-md-3 col-sm-3">
                                        <input type="color" class="form-control TextColor"
                                               value="<?php echo $TextColor[$MEDICAL_SERVICE['ManageServiceKey']]; ?>"/>
                                        <input type="hidden" name="vTextColor" data-id="vTextColor"
                                               value="<?php echo $TextColor[$MEDICAL_SERVICE['ManageServiceKey']]; ?>">
                                    </div>
                                </div>
                                <div class="row pb-10">
                                    <div class="col-lg-12">
                                        <label style="font-size: 13px">Background Color</label>
                                    </div>
                                    <div class="col-md-3 col-sm-3">
                                        <input type="color" class="form-control BgColor"
                                               value="<?php echo $BgColor[$MEDICAL_SERVICE['ManageServiceKey']]; ?>"/>
                                        <input type="hidden" name="vBgColor" data-id="vBgColor"
                                               value="<?php echo $BgColor[$MEDICAL_SERVICE['ManageServiceKey']]; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer"
                             style="text-align: left">
                            <button type="submit" name="submitbtn" class="btn btn-default">Save</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php }
    } ?>

<?php if ($MODULES_OBJ->isEnableRideShareService()) { ?>
<div class="modal fade" id="RideShareModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content nimot-class">
            <div class="modal-header">
                <h4>
                    Ride Sharing/Car Pool
                    <button type="button" class="close" data-dismiss="modal">x</button>
                </h4>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" name="eMasterServiceCatType" id="eMasterServiceCatType" value="RideShare">

                        <div class="row pb-10">
                            <div class="col-lg-12">
                                <label style="font-size: 13px">Image</label>
                            </div>
                            <div class="col-lg-12 marginbottom-10">
                                <?php if (!empty($vImageOld['RideShare']['RideSharePublish'])) { ?>
                                <div class="marginbottom-10">
                                    <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=100&src='.$tconfig['tsite_upload_app_home_screen_images'].$vImageOld['RideShare']['RideSharePublish']; ?>">
                                </div>
                                <?php } ?>
                                <input type="file" class="form-control" name="vImageRideSharePublish">
                                <input type="hidden" class="form-control" name="vImageRideSharePublish_old" value="<?php echo $vImageOld['RideShare']['RideSharePublish']; ?>">
                            </div>
                            <div class="col-lg-12">
                                <strong>Note: Upload only png image size of 360px X 360px.</strong>
                            </div>
                        </div>

                        <div class="row pb-10">
                            <div class="col-lg-12">
                                <label style="font-size: 13px">Title</label>
                            </div>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" value="<?php echo $ServiceTitle['RideShare']['RideSharePublish']; ?>" readonly disabled>
                            </div>
                            <div class="col-lg-2">
                                <a href="<?php echo $tconfig['tsite_url_main_admin'].'languages.php?option=&keyword='.$ServiceTitle['RideShare']['RideSharePublishLabel']; ?>" class="btn btn-info" target="_blank"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                            </div>
                        </div>
                        <hr />
                        <div class="row pb-10">
                            <div class="col-lg-12">
                                <label style="font-size: 13px">Image</label>
                            </div>
                            <div class="col-lg-12 marginbottom-10">
                                <?php if (!empty($vImageOld['RideShare']['RideShareBook'])) { ?>
                                <div class="marginbottom-10">
                                    <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=100&src='.$tconfig['tsite_upload_app_home_screen_images'].$vImageOld['RideShare']['RideShareBook']; ?>">
                                </div>
                                <?php } ?>
                                <input type="file" class="form-control" name="vImageRideShareBook">
                                <input type="hidden" class="form-control" name="vImageRideShareBook_old" value="<?php echo $vImageOld['RideShare']['RideShareBook']; ?>">
                            </div>
                            <div class="col-lg-12">
                                <strong>Note: Upload only png image size of 360px X 360px.</strong>
                            </div>
                        </div>

                        <div class="row pb-10">
                            <div class="col-lg-12">
                                <label style="font-size: 13px">Title</label>
                            </div>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" value="<?php echo $ServiceTitle['RideShare']['RideShareBook']; ?>" readonly disabled>
                            </div>
                            <div class="col-lg-2">
                                <a href="<?php echo $tconfig['tsite_url_main_admin'].'languages.php?option=&keyword='.$ServiceTitle['RideShare']['RideShareBookLabel']; ?>" class="btn btn-info" target="_blank"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                            </div>
                        </div>

                        <hr />
                        <div class="row pb-10">
                            <div class="col-lg-12">
                                <label style="font-size: 13px">Image</label>
                            </div>
                            <div class="col-lg-12 marginbottom-10">
                                <?php if (!empty($vImageOld['RideShare']['RideShareMyRides'])) { ?>
                                <div class="marginbottom-10">
                                    <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=100&src='.$tconfig['tsite_upload_app_home_screen_images'].$vImageOld['RideShare']['RideShareMyRides']; ?>">
                                </div>
                                <?php } ?>
                                <input type="file" class="form-control" name="vImageRideShareMyRides">
                                <input type="hidden" class="form-control" name="vImageRideShareMyRides_old" value="<?php echo $vImageOld['RideShare']['RideShareMyRides']; ?>">
                            </div>
                            <div class="col-lg-12">
                                <strong>Note: Upload only png image size of 360px X 360px.</strong>
                            </div>
                        </div>

                        <div class="row pb-10">
                            <div class="col-lg-12">
                                <label style="font-size: 13px">Title</label>
                            </div>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" value="<?php echo $ServiceTitle['RideShare']['RideShareMyRides']; ?>" readonly disabled>
                            </div>
                            <div class="col-lg-2">
                                <a href="<?php echo $tconfig['tsite_url_main_admin'].'languages.php?option=&keyword='.$ServiceTitle['RideShare']['RideShareMyRidesLabel']; ?>" class="btn btn-info" target="_blank"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="text-align: left">
                    <button type="submit" name="submitbtn" class="btn btn-default">Save
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>

<!--END MAIN WRAPPER -->
<div class="row loding-action" id="loaderIcon" style="display:none;">
    <div>
        <img src="default.gif">
        <span>Language Translation is in Process. Please Wait...</span>
    </div>
</div>
<?php include_once 'footer.php'; ?>
<script type="text/javascript" src="js/fancybox.umd.js"></script>
<script type="text/javascript" src="../assets/js/jquery-ui.min.js"></script>
<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
<script type="text/javascript">
    $(function () {
        sortDom('<?php echo $tGridServicesStr; ?>');

        $("#grid-services-section").sortable({
            containment: ".grid-services-section",
            dropOnEmpty: true
        });
    });

    function editIntroTitle(action) {
        $('#intro_title_modal_action').html(action);
        $('#IntroTitle_Modal').modal('show');
    }
    function editIntroBuyTitle(action) {
        $('#introBuy_title_modal_action').html(action);
        $('#IntroBuyTitle_Modal').modal('show');
    }


    function saveIntroBuyTitle() {
        if ($('#vIntroBuyTitle_<?php echo $default_lang; ?>').val() == "") {
            $('#vIntroBuyTitle_<?php echo $default_lang; ?>_error').show();
            $('#vIntroBuyTitle_<?php echo $default_lang; ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vIntroBuyTitle_<?php echo $default_lang; ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vIntroBuyTitle_Default').val($('#vIntroBuyTitle_<?php echo $default_lang; ?>').val());
        $('#vIntroBuyTitle_Default').closest('.row').removeClass('has-error');
        $('#vIntroBuyTitle_Default-error').remove();
        $('#IntroBuyTitle_Modal').modal('hide');
    }


    function saveIntroTitle() {
        if ($('#vIntroTitle_<?php echo $default_lang; ?>').val() == "") {
            $('#vIntroTitle_<?php echo $default_lang; ?>_error').show();
            $('#vIntroTitle_<?php echo $default_lang; ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vIntroTitle_<?php echo $default_lang; ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vIntroTitle_Default').val($('#vIntroTitle_<?php echo $default_lang; ?>').val());
        $('#vIntroTitle_Default').closest('.row').removeClass('has-error');
        $('#vIntroTitle_Default-error').remove();
        $('#IntroTitle_Modal').modal('hide');
    }

    function editIntroSubTitle(action) {
        $('#intro_subtitle_modal_action').html(action);
        $('#IntroSubTitle_Modal').modal('show');
    }

    function editIntroBuySubTitle(action) {
        $('#introBuy_subtitle_modal_action').html(action);
        $('#IntroBuySubTitle_Modal').modal('show');
    }

    function saveIntroBuySubTitle() {
        if ($('#vIntroBuySubTitle_<?php echo $default_lang; ?>').val() == "") {
            $('#vIntroBuySubTitle_<?php echo $default_lang; ?>_error').show();
            $('#vIntroBuySubTitle_<?php echo $default_lang; ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vIntroBuySubTitle_<?php echo $default_lang; ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vIntroBuySubTitle_Default').val($('#vIntroBuySubTitle_<?php echo $default_lang; ?>').val());
        $('#vIntroBuySubTitle_Default').closest('.row').removeClass('has-error');
        $('#vIntroBuySubTitle_Default-error').remove();
        $('#IntroBuySubTitle_Modal').modal('hide');
    }

    function saveIntroSubTitle() {
        if ($('#vIntroSubTitle_<?php echo $default_lang; ?>').val() == "") {
            $('#vIntroSubTitle_<?php echo $default_lang; ?>_error').show();
            $('#vIntroSubTitle_<?php echo $default_lang; ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vIntroSubTitle_<?php echo $default_lang; ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vIntroSubTitle_Default').val($('#vIntroSubTitle_<?php echo $default_lang; ?>').val());
        $('#vIntroSubTitle_Default').closest('.row').removeClass('has-error');
        $('#vIntroSubTitle_Default-error').remove();
        $('#IntroSubTitle_Modal').modal('hide');
    }

    function editBuySellTitle(action) {
        $('#buysell_title_modal_action').html(action);
        $('#BuySellTitle_Modal').modal('show');
    }

    function saveBuySellTitle() {
        if ($('#vBuySellTitle_<?php echo $default_lang; ?>').val() == "") {
            $('#vBuySellTitle_<?php echo $default_lang; ?>_error').show();
            $('#vBuySellTitle_<?php echo $default_lang; ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vBuySellTitle_<?php echo $default_lang; ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vBuySellTitle_Default').val($('#vBuySellTitle_<?php echo $default_lang; ?>').val());
        $('#vBuySellTitle_Default').closest('.row').removeClass('has-error');
        $('#vBuySellTitle_Default-error').remove();
        $('#BuySellTitle_Modal').modal('hide');
    }

    function editTrackServiceTitle(action) {
        $('#trackservice_title_modal_action').html(action);
        $('#TrackServiceTitle_Modal').modal('show');
    }

    function saveTrackServiceTitle() {
        if ($('#vTrackServiceTitle_<?php echo $default_lang; ?>').val() == "") {
            $('#vTrackServiceTitle_<?php echo $default_lang; ?>_error').show();
            $('#vTrackServiceTitle_<?php echo $default_lang; ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vTrackServiceTitle_<?php echo $default_lang; ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vTrackServiceTitle_Default').val($('#vTrackServiceTitle_<?php echo $default_lang; ?>').val());
        $('#vTrackServiceTitle_Default').closest('.row').removeClass('has-error');
        $('#vTrackServiceTitle_Default-error').remove();
        $('#TrackServiceTitle_Modal').modal('hide');
    }

    function editOtherServiceTitle(action) {
        $('#otherservice_title_modal_action').html(action);
        $('#OtherServiceTitle_Modal').modal('show');
    }
    function saveOtherServiceTitle() {
        if ($('#vOtherServiceTitle_<?php echo $default_lang; ?>').val() == "") {
            $('#vOtherServiceTitle_<?php echo $default_lang; ?>_error').show();
            $('#vOtherServiceTitle_<?php echo $default_lang; ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vOtherServiceTitle_<?php echo $default_lang; ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vOtherServiceTitle_Default').val($('#vOtherServiceTitle_<?php echo $default_lang; ?>').val());
        $('#vOtherServiceTitle_Default').closest('.row').removeClass('has-error');
        $('#vOtherServiceTitle_Default-error').remove();
        $('#OtherServiceTitle_Modal').modal('hide');
    }

    function editOtherServiceSubTitle(action) {
        $('#otherservice_subtitle_modal_action').html(action);
        $('#OtherServiceSubTitle_Modal').modal('show');
    }

    function saveOtherServiceSubTitle() {
        if ($('#vOtherServiceSubTitle_<?php echo $default_lang; ?>').val() == "") {
            $('#vOtherServiceSubTitle_<?php echo $default_lang; ?>_error').show();
            $('#vOtherServiceSubTitle_<?php echo $default_lang; ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vOtherServiceSubTitle_<?php echo $default_lang; ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vOtherServiceSubTitle_Default').val($('#vOtherServiceSubTitle_<?php echo $default_lang; ?>').val());
        $('#vOtherServiceSubTitle_Default').closest('.row').removeClass('has-error');
        $('#vOtherServiceSubTitle_Default-error').remove();
        $('#OtherServiceSubTitle_Modal').modal('hide');
    }

    function editBidTitle(action) {
        $('#Bid_title_modal_action').html(action);
        $('#BidTitle_Modal').modal('show');
    }

    function saveBidTitle() {
        if ($('#vBidTitle_<?php echo $default_lang; ?>').val() == "") {
            $('#vBidTitle_<?php echo $default_lang; ?>_error').show();
            $('#vBidTitle_<?php echo $default_lang; ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vBidTitle_<?php echo $default_lang; ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vBidTitle_Default').val($('#vBidTitle_<?php echo $default_lang; ?>').val());
        $('#vBidTitle_Default').closest('.row').removeClass('has-error');
        $('#vBidTitle_Default-error').remove();
        $('#BidTitle_Modal').modal('hide');
    }


    function editBidSubTitle(action) {
        $('#Bid_subtitle_modal_action').html(action);
        $('#BidSubTitle_Modal').modal('show');
    }

    function saveBidSubTitle() {
        if ($('#vBidSubTitle_<?php echo $default_lang; ?>').val() == "") {
            $('#vBidSubTitle_<?php echo $default_lang; ?>_error').show();
            $('#vBidSubTitle_<?php echo $default_lang; ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vBidSubTitle_<?php echo $default_lang; ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vBidSubTitle_Default').val($('#vBidSubTitle_<?php echo $default_lang; ?>').val());
        $('#vBidSubTitle_Default').closest('.row').removeClass('has-error');
        $('#vBidSubTitle_Default-error').remove();
        $('#BidSubTitle_Modal').modal('hide');
    }


    function editMedicalServiceTitle(action) {
        $('#medicalservice_title_modal_action').html(action);
        $('#MedicalServiceTitle_Modal').modal('show');
    }

    function saveMedicalServiceTitle() {
        if ($('#vMedicalServiceTitle_<?php echo $default_lang; ?>').val() == "") {
            $('#vMedicalServiceTitle_<?php echo $default_lang; ?>_error').show();
            $('#vMedicalServiceTitle_<?php echo $default_lang; ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vMedicalServiceTitle_<?php echo $default_lang; ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vMedicalServiceTitle_Default').val($('#vMedicalServiceTitle_<?php echo $default_lang; ?>').val());
        $('#vMedicalServiceTitle_Default').closest('.row').removeClass('has-error');
        $('#vMedicalServiceTitle_Default-error').remove();
        $('#MedicalServiceTitle_Modal').modal('hide');
    }

    function editNearbyServiceTitle(action) {
        $('#nearbyservice_title_modal_action').html(action);
        $('#NearbyServiceTitle_Modal').modal('show');
    }

    function saveNearbyServiceTitle() {
        if ($('#vNearbyServiceTitle_<?php echo $default_lang; ?>').val() == "") {
            $('#vNearbyServiceTitle_<?php echo $default_lang; ?>_error').show();
            $('#vNearbyServiceTitle_<?php echo $default_lang; ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vNearbyServiceTitle_<?php echo $default_lang; ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vNearbyServiceTitle_Default').val($('#vNearbyServiceTitle_<?php echo $default_lang; ?>').val());
        $('#vNearbyServiceTitle_Default').closest('.row').removeClass('has-error');
        $('#vNearbyServiceTitle_Default-error').remove();
        $('#NearbyServiceTitle_Modal').modal('hide');
    }

    function sortDom(selectorArray) {
        selectorArray = JSON.parse(selectorArray);
        while (selectorArray.length) {
            let $el = $(selectorArray.pop());
            $el.parent().prepend($el);
        }
    }

    function saveOnDemandServices(eStatus) {
        $('#saveOtherServices').val(eStatus);
        $('#other_services_modal').modal('hide');
    }

    $('[name="iVehicleCategoryId[]"]').change(function (e) {
        if ($(this).is(':checked')) {
            if ($('[name="iVehicleCategoryId[]"]:checked').length > 3) {
                alert("You can only enable 3 service categories to be shown on App home screen.");
                $(this).prop('checked', false);
                e.stopPropagation();
                e.preventDefault();
            } else {
                $(this).closest('tr').find('select, input[type="file"]').show();
            }
        } else {
            $(this).closest('tr').find('select, input[type="file"]').hide();
            $(this).closest('tr').find('select').val('1');
            $(this).closest('tr').find('input[type="file"]').val('').bootstrapSwitch();
        }
    });

    function saveMSBookService(eStatus) {
        $('#saveBookServiceMS').val(eStatus);
        $('#ms_bookservice_modal').modal('hide');
    }

    function saveMSVideoConsult(eStatus) {
        $('#saveVideoConsultMS').val(eStatus);
        $('#ms_videoconsult_modal').modal('hide');
    }

    function saveMSMoreService(eStatus) {
        $('#saveMoreServiceMS').val(eStatus);
        $('#ms_moreservice_modal').modal('hide');
    }

    $('[name="iVehicleCategoryIdBS[]"]').change(function (e) {
        if ($(this).is(':checked')) {
            if ($('[name="iVehicleCategoryIdBS[]"]:checked').length > 2) {
                alert("You can only enable 2 service categories to be shown on App home screen.");
                $(this).prop('checked', false);
                e.stopPropagation();
                e.preventDefault();
            } else {
                $(this).closest('tr').find('select, input[type="file"]').show();
            }
        } else {
            $(this).closest('tr').find('select, input[type="file"]').hide();
            $(this).closest('tr').find('select').val('1');
            $(this).closest('tr').find('input[type="file"]').val('').bootstrapSwitch();
        }
    });

    $('[name="iVehicleCategoryIdVC[]"]').change(function (e) {
        if ($(this).is(':checked')) {
            if ($('[name="iVehicleCategoryIdVC[]"]:checked').length > 2) {
                alert("You can only enable 2 service categories to be shown on App home screen.");
                $(this).prop('checked', false);
                e.stopPropagation();
                e.preventDefault();
            } else {
                $(this).closest('tr').find('select, input[type="file"]').show();
            }
        } else {
            $(this).closest('tr').find('select, input[type="file"]').hide();
            $(this).closest('tr').find('select').val('1');
            $(this).closest('tr').find('input[type="file"]').val('').bootstrapSwitch();
        }
    });

    $('[name="iVehicleCategoryIdMS[]"]').change(function (e) {
        if ($(this).is(':checked')) {
            if ($('[name="iVehicleCategoryIdMS[]"]:checked').length > 3) {
                alert("You can only enable 3 service categories to be shown on App home screen.");
                $(this).prop('checked', false);
                e.stopPropagation();
                e.preventDefault();
            } else {
                $(this).closest('tr').find('select, input[type="file"]').show();
            }
        } else {
            $(this).closest('tr').find('select, input[type="file"]').hide();
            $(this).closest('tr').find('select').val('1');
            $(this).closest('tr').find('input[type="file"]').val('').bootstrapSwitch();
        }
    });

    function saveServicesNearby(eStatus) {
        $('#saveNearbyServices').val(eStatus);
        $('#nearby_services_modal').modal('hide');
    }

    $('[name="iCategoryId[]"]').change(function (e) {
        if ($(this).is(':checked')) {
            if ($('[name="iCategoryId[]"]:checked').length > 3) {
                alert("You can only enable 3 nearby categories to be shown on App home screen.");
                $(this).prop('checked', false);
                e.stopPropagation();
                e.preventDefault();
            } else {
                $(this).closest('tr').find('select, input[type="file"]').show();
            }
        } else {
            $(this).closest('tr').find('select, input[type="file"]').hide();
            $(this).closest('tr').find('select').val('1');
            $(this).closest('tr').find('input[type="file"]').val('').bootstrapSwitch();
        }
    });

    $(".TextColor").on("input", function () {
        var color = $(this).val();
        $(this).next().val(color);
    });

    $(".BgColor").on("input", function () {
        var color = $(this).val();
        $(this).next().val(color);
    });
</script>
</body>
<!-- END BODY-->
</html>