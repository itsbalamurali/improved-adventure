<?php
include_once '../common.php';

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

$sql_vehicle_category_table_name = getVehicleCategoryTblName();

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

    for ($i = 0; $i < count($db_master); ++$i) {
        $vRideServiceTitle = '';
        if (isset($_POST['vRideServiceTitle_'.$db_master[$i]['vCode']])) {
            $vRideServiceTitle = $_POST['vRideServiceTitle_'.$db_master[$i]['vCode']];
        }
        $vRideServiceTitleArr['vTitle_'.$db_master[$i]['vCode']] = $vRideServiceTitle;

        if ('RIDE' === strtoupper($APP_TYPE)) {
            $vBannerServiceTitle = '';
            if (isset($_POST['vBannerServiceTitle_'.$db_master[$i]['vCode']])) {
                $vBannerServiceTitle = $_POST['vBannerServiceTitle_'.$db_master[$i]['vCode']];
            }
            $vBannerServiceTitleArr['vTitle_'.$db_master[$i]['vCode']] = $vBannerServiceTitle;
        } elseif ('RIDE-DELIVERY' === strtoupper($APP_TYPE)) {
            $vDeliverServiceTitle = '';
            if (isset($_POST['vDeliverServiceTitle_'.$db_master[$i]['vCode']])) {
                $vDeliverServiceTitle = $_POST['vDeliverServiceTitle_'.$db_master[$i]['vCode']];
            }
            $vDeliverServiceTitleArr['vTitle_'.$db_master[$i]['vCode']] = $vDeliverServiceTitle;
        }

        $vOtherServiceTitle = '';
        if (isset($_POST['vOtherServiceTitle_'.$db_master[$i]['vCode']])) {
            $vOtherServiceTitle = $_POST['vOtherServiceTitle_'.$db_master[$i]['vCode']];
        }
        $vOtherServiceTitleArr['vTitle_'.$db_master[$i]['vCode']] = $vOtherServiceTitle;
    }

    $jsonRideServiceTitle = getJsonFromAnArr($vRideServiceTitleArr);
    $Data_update_rideservice = [];
    if ('Yes' === $_POST['saveRideServices']) {
        $iVehicleCategoryIdArr = $_POST['iVehicleCategoryId'];
        $iVehicleCategoryIdValArr = $_POST['iVehicleCategoryIdVal'];
        $iDisplayOrderRideArr = $_POST['iDisplayOrderRideArr'];
        $vImageOldArr = $_POST['vRideImageOld'];
        $db_data_ride = $obj->MySQLSelect("SELECT tServiceDetails FROM {$tbl_name} WHERE eServiceType = 'Ride' ");
        $tServiceDetails = [];
        if (!empty($db_data_ride[0]['tServiceDetails'])) {
            $tServiceDetails = json_decode($db_data_ride[0]['tServiceDetails'], true);
            foreach ($tServiceDetails as $serviceDetail) {
                if (!in_array($serviceDetail['iVehicleCategoryId'], $iVehicleCategoryIdArr, true)) {
                    $tServiceDetails['iVehicleCategoryId_'.$serviceDetail['iVehicleCategoryId']]['eStatus'] = 'Inactive';
                }
            }
        }
        foreach ($iVehicleCategoryIdArr as $iVehicleCategoryId) {
            $orderKey = array_search($iVehicleCategoryId, $_POST['iVehicleCategoryIdVal'], true);
            $iDisplayOrderService = $iDisplayOrderRideArr[$orderKey];
            $vImage = '';
            $image_object = $_FILES['vRideImage']['tmp_name'][$orderKey];
            $image_name = $_FILES['vRideImage']['name'][$orderKey];
            if ('' !== $image_name) {
                $filecheck = basename($_FILES['vRideImage']['name'][$orderKey]);
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
        $Data_update_rideservice['tServiceDetails'] = json_encode($tServiceDetails, JSON_UNESCAPED_UNICODE);
    }
    $Data_update_rideservice['vTitle'] = $jsonRideServiceTitle;
    $where = " eServiceType = 'Ride' ";
    $obj->MySQLQueryPerform($tbl_name, $Data_update_rideservice, 'update', $where);

    if ('RIDE' === strtoupper($APP_TYPE)) {
        $jsonBannerServiceTitle = getJsonFromAnArr($vBannerServiceTitleArr);
        $Data_update_bannerservice['vTitle'] = $jsonBannerServiceTitle;
        $where = " eServiceType = 'GeneralBanner' ";
        $obj->MySQLQueryPerform($tbl_name, $Data_update_bannerservice, 'update', $where);
    } elseif ('RIDE-DELIVERY' === strtoupper($APP_TYPE)) {
        $jsonDeliverServiceTitle = getJsonFromAnArr($vDeliverServiceTitleArr);
        $Data_update_deliverservice['vTitle'] = $jsonDeliverServiceTitle;
        $where = " eServiceType = 'Deliver' ";
        $obj->MySQLQueryPerform($tbl_name, $Data_update_deliverservice, 'update', $where);
    }

    $jsonOtherServiceTitle = getJsonFromAnArr($vOtherServiceTitleArr);
    $Data_update_otherservice['vTitle'] = $jsonOtherServiceTitle;
    $where = " eServiceType = 'Other' ";
    $obj->MySQLQueryPerform($tbl_name, $Data_update_otherservice, 'update', $where);

    $_SESSION['success'] = '1';
    $_SESSION['var_msg'] = 'App Home Screen View updated successfully.';
    header('Location:manage_app_home_screen.php');

    exit;
}

if (isset($_POST['eOtherServiceCatEdit'])) {
    $other_service_details = $obj->MySQLSelect("SELECT tServiceDetails FROM {$tbl_name} WHERE eServiceType = 'Other' ");
    $tServiceDetails = $other_service_details[0]['tServiceDetails'];
    $Data_Update = [];
    $eOtherServiceCatEdit = $_POST['eOtherServiceCatEdit'];
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
            header('Location:manage_app_home_screen.php');

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
    if (!empty($tServiceDetails)) {
        $tServiceDetails = json_decode($tServiceDetails, true);
    } else {
        $tServiceDetails = [];
    }
    if ('AddStop' === $eOtherServiceCatEdit) {
        if (!empty($vImage)) {
            $tServiceDetails['AddStop']['vImage'] = $vImage;
        }
        $tServiceDetails['AddStop']['iDisplayOrder'] = $_POST['iDisplayOrder'];
    } elseif ('ShareRide' === $eOtherServiceCatEdit) {
        if (!empty($vImage)) {
            $tServiceDetails['ShareRide']['vImage'] = $vImage;
        }
        $tServiceDetails['ShareRide']['iDisplayOrder'] = $_POST['iDisplayOrder'];
    } else {
        if (!empty($vImage)) {
            $tServiceDetails['TaxiPool']['vImage'] = $vImage;
        }
        $tServiceDetails['TaxiPool']['iDisplayOrder'] = $_POST['iDisplayOrder'];
    }
    $Data_Update['tServiceDetails'] = json_encode($tServiceDetails, JSON_UNESCAPED_UNICODE);
    $where = " eServiceType = 'Other' ";
    $obj->MySQLQueryPerform($tbl_name, $Data_Update, 'update', $where);
    $_SESSION['success'] = '1';
    $_SESSION['var_msg'] = 'App Home Screen View updated successfully.';
    header('Location:manage_app_home_screen.php');

    exit;
}

$userEditDataArr = $db_data_arr = [];
$db_data = $obj->MySQLSelect("SELECT * FROM {$tbl_name}");
foreach ($db_data as $db_value) {
    $ViewType = !empty($db_value['eServiceType']) ? $db_value['eServiceType'] : $db_value['eViewType'];
    $db_data_arr[$ViewType] = $db_value;
}

$EN_available = $LANG_OBJ->checkLanguageExist();
$db_master = $LANG_OBJ->getLangDataDefaultFirst($db_master);

$vRideServiceTitleArr = json_decode($db_data_arr['Ride']['vTitle'], true);
foreach ($vRideServiceTitleArr as $key => $value) {
    $key = str_replace('vTitle_', 'vRideServiceTitle_', $key);
    $userEditDataArr[$key] = $value;
}
$tServiceDetailsRide = $db_data_arr['Ride']['tServiceDetails'];
$tServiceDetailsRideArr = [];
if (!empty($tServiceDetailsRide)) {
    $tServiceDetailsRideArr = json_decode($tServiceDetailsRide, true);
}
$rideData = $obj->MySQLSelect('SELECT iVehicleCategoryId, iDisplayOrder,vCategory_'.$default_lang.' as vCategory FROM '.$sql_vehicle_category_table_name." WHERE eStatus='Active' AND eCatType IN ('Ride', 'Rental', 'RidePool', 'RideSchedule', 'MotoRide') ORDER BY iDisplayOrder, iVehicleCategoryId ASC ");

if ('RIDE' === strtoupper($APP_TYPE)) {
    $vBannerServiceTitleArr = json_decode($db_data_arr['GeneralBanner']['vTitle'], true);
    foreach ($vBannerServiceTitleArr as $key => $value) {
        $key = str_replace('vTitle_', 'vBannerServiceTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
} elseif ('RIDE-DELIVERY' === strtoupper($APP_TYPE)) {
    $vDeliverServiceTitleArr = json_decode($db_data_arr['Deliver']['vTitle'], true);
    foreach ($vDeliverServiceTitleArr as $key => $value) {
        $key = str_replace('vTitle_', 'vDeliverServiceTitle_', $key);
        $userEditDataArr[$key] = $value;
    }
}

$vOtherServiceTitleArr = json_decode($db_data_arr['Other']['vTitle'], true);
foreach ($vOtherServiceTitleArr as $key => $value) {
    $key = str_replace('vTitle_', 'vOtherServiceTitle_', $key);
    $userEditDataArr[$key] = $value;
}

$OTHER_SERVICES_ARR = [
    [
        'ServiceTitle' => $langage_lbl_admin['LBL_TAXI_ADD_A_STOP'],
        'ServiceDesc' => $langage_lbl_admin['LBL_TAXI_ADD_A_STOP_DESC'],
        'ManageServiceKey' => 'AddStop',
        'ModalKey' => 'AddStopModal',
        'HiddenInput' => 'saveAddStop',
        'ServiceTitleLabel' => 'LBL_TAXI_ADD_A_STOP',
        'ServiceDescLabel' => 'LBL_TAXI_ADD_A_STOP_DESC',
    ],
    [
        'ServiceTitle' => $langage_lbl_admin['LBL_SHARE_YOUR_RIDE_TITLE'],
        'ServiceDesc' => $langage_lbl_admin['LBL_SHARE_YOUR_RIDE_DESC'],
        'ManageServiceKey' => 'ShareRide',
        'ModalKey' => 'ShareRideModal',
        'HiddenInput' => 'saveShareRide',
        'ServiceTitleLabel' => 'LBL_SHARE_YOUR_RIDE_TITLE',
        'ServiceDescLabel' => 'LBL_SHARE_YOUR_RIDE_DESC',
    ],
    [
        'ServiceTitle' => $langage_lbl_admin['LBL_TAXI_POOL_TITLE'],
        'ServiceDesc' => $langage_lbl_admin['LBL_TAXI_POOL_DESC'],
        'ManageServiceKey' => 'TaxiPool',
        'ModalKey' => 'TaxiPoolModal',
        'HiddenInput' => 'saveTaxiPool',
        'ServiceTitleLabel' => 'LBL_TAXI_POOL_TITLE',
        'ServiceDescLabel' => 'LBL_TAXI_POOL_DESC',
    ],
];

$DisplayOrder['AddStop'] = $DisplayOrder['ShareRide'] = $DisplayOrder['TaxiPool'] = '1';
$vImageOld['AddStop'] = $vImageOld['ShareRide'] = $vImageOld['TaxiPool'] = '';
if (!empty($db_data_arr['Other']['tServiceDetails'])) {
    $tServiceDetailsArr = json_decode($db_data_arr['Other']['tServiceDetails'], true);
    $DisplayOrder['AddStop'] = $tServiceDetailsArr['AddStop']['iDisplayOrder'];
    $DisplayOrder['ShareRide'] = $tServiceDetailsArr['ShareRide']['iDisplayOrder'];
    $DisplayOrder['TaxiPool'] = $tServiceDetailsArr['TaxiPool']['iDisplayOrder'];

    $vImageOld['AddStop'] = $tServiceDetailsArr['AddStop']['vImage'];
    $vImageOld['ShareRide'] = $tServiceDetailsArr['ShareRide']['vImage'];
    $vImageOld['TaxiPool'] = $tServiceDetailsArr['TaxiPool']['vImage'];
}

if ('Yes' === $THEME_OBJ->isPXCProThemeActive()) {
    $master_service_categories = $obj->MySQLSelect("SELECT JSON_UNQUOTE(JSON_EXTRACT(vCategoryName, '$.vCategoryName_".$default_lang."')) as vCategoryName, eType, iMasterServiceCategoryId FROM {$master_service_category_tbl} WHERE eStatus = 'Active'");
    $MasterCategoryArr = [];
    foreach ($master_service_categories as $mCategory) {
        $MasterCategoryArr[$mCategory['eType']] = $mCategory;
    }

    $DELIVERALL_SERVICES_ARR = $obj->MySQLSelect("SELECT iVehicleCategoryId, vCategory_{$default_lang} as vCategory FROM {$sql_vehicle_category_table_name} WHERE iParentId = 0 AND eStatus = 'Active' AND iServiceId IN ({$enablesevicescategory}) ORDER BY iDisplayOrder ");
}
$isDeliveryFeatureAvailable = $MODULES_OBJ->isDeliveryFeatureAvailable('Yes') ? 'YES' : 'NO';
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
    <link rel="stylesheet" href="css/fancybox.css"/>
    <style type="text/css">
        .show-help-img {
            cursor: pointer;
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
                        <h3 class="show-help-section">
                            <i class="fa fa-caret-right"></i>
                            Service List View - Taxi Services
                            <?php if ('RIDE' === strtoupper($APP_TYPE)) { ?>
                                <i class="fa fa-question-circle show-help-img" data-fancybox data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/TaxiServices.png"></i>
                            <?php } elseif ('RIDE-DELIVERY' === strtoupper($APP_TYPE)) { ?>
                                <i class="fa fa-question-circle show-help-img" data-fancybox data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/TaxiServicesRD.png"></i>
                            <?php } else { ?>
                                <i class="fa fa-question-circle show-help-img" data-fancybox data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/TaxiServicesCX.png"></i>
                            <?php } ?>
                        </h3>
                        <?php if (count($db_master) > 1) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vRideServiceTitle_Default"
                                           name="vRideServiceTitle_Default"
                                           value="<?php echo $userEditDataArr['vRideServiceTitle_'.$default_lang]; ?>"
                                           data-originalvalue="<?php echo $userEditDataArr['vRideServiceTitle_'.$default_lang]; ?>"
                                           readonly="readonly" required>
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                            data-original-title="Edit" onclick="editRideServiceTitle('Edit')">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="modal fade" id="RideServiceTitle_Modal" tabindex="-1" role="dialog"
                                 aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="rideservice_title_modal_action"></span>
                                                Title
                                                <button type="button" class="close" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vRideServiceTitle_')">
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
                                                $vValue = 'vRideServiceTitle_'.$vCode;
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
                                                                            onClick="getAllLanguageCode('vRideServiceTitle_', 'EN');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                            } else {
                                                                if ($vCode === $default_lang) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vRideServiceTitle_', '<?php echo $default_lang; ?>');">
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
                                                        onclick="saveRideServiceTitle()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok"
                                                        data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vRideServiceTitle_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
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
                                           id="vRideServiceTitle_<?php echo $default_lang; ?>"
                                           name="vRideServiceTitle_<?php echo $default_lang; ?>"
                                           value="<?php echo $userEditDataArr['vRideServiceTitle_'.$default_lang]; ?>">
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
                                                    data-target="#ride_services_modal">Manage Services
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="modal fade" id="ride_services_modal" tabindex="-1" role="dialog"
                                 aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                Services
                                                <button type="button" class="close" data-dismiss="modal">x</button>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <p>
                                                <strong>Note:</strong>
                                                Enable any 4 services from below list to be shown on App
                                                home screen. All other services will be shown under "See All".
                                                <br>
                                                "See All" uploaded will only be shown on App home screen and not under
                                                more section.
                                            </p>
                                            <input type="hidden" name="saveRideServices" id="saveRideServices"
                                                   value="No">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                <tr>
                                                    <th style="text-align: center;">Icon</th>
                                                    <th>Service</th>
                                                    <th>Display Order</th>
                                                    <th>Upload Icon</th>
                                                    <th>Status</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                foreach ($rideData as $rideArr) {
                                    $vServiceImg = '';
                                    $vServiceStatus = '';
                                    $vServiceImgOld = '';
                                    $vServiceDisplay = 'style="display: none"';
                                    $vServiceDisplayOrder = '1';
                                    if (isset($tServiceDetailsRideArr['iVehicleCategoryId_'.$rideArr['iVehicleCategoryId']])) {
                                        $tServiceDetails = $tServiceDetailsRideArr['iVehicleCategoryId_'.$rideArr['iVehicleCategoryId']];
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
                                                        <td style="vertical-align: middle;"><?php echo $rideArr['vCategory']; ?></td>
                                                        <td>
                                                            <select class="form-control" name="iDisplayOrderRideArr[]" <?php echo $vServiceDisplay; ?>>
                                                                <?php for ($disp_order = 1; $disp_order <= count($rideData); ++$disp_order) { ?>
                                                                    <option value="<?php echo $disp_order; ?>" <?php echo $vServiceDisplayOrder === $disp_order ? 'selected' : ''; ?>><?php echo $disp_order; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="file" class="form-control" name="vRideImage[]" <?php echo $vServiceDisplay; ?>>
                                                            <input type="hidden" class="form-control" name="vRideImageOld[]" value="<?php echo $vServiceImgOld; ?>">
                                                        </td>
                                                        <td>
                                                            <div class="make-switch" data-on="success"
                                                                 data-off="warning">
                                                                <input type="checkbox" name="iVehicleCategoryId[]" value="<?php echo $rideArr['iVehicleCategoryId']; ?>" <?php echo $vServiceStatus; ?> />
                                                            </div>
                                                            <input type="hidden" name="iVehicleCategoryIdVal[]" value="<?php echo $rideArr['iVehicleCategoryId']; ?>">
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer" style="text-align: left">
                                            <button type="button" class="btn btn-default" onclick="saveServicesRide('Yes')">Save
                                            </button>
                                            <button type="button" class="btn btn-default" onclick="saveServicesRide('No')">Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ('RIDE' === strtoupper($APP_TYPE)) { ?>
                        <hr/>
                        <h3 class="show-help-section">
                            <i class="fa fa-caret-right"></i>
                            Banner View - General Banners
                            <i class="fa fa-question-circle show-help-img" data-fancybox
                               data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/GeneralBannersTaxi.png"></i>
                        </h3>
                        <?php if (count($db_master) > 1) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vBannerServiceTitle_Default"
                                           name="vBannerServiceTitle_Default"
                                           value="<?php echo $userEditDataArr['vBannerServiceTitle_'.$default_lang]; ?>"
                                           data-originalvalue="<?php echo $userEditDataArr['vBannerServiceTitle_'.$default_lang]; ?>"
                                           readonly="readonly" required>
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                            data-original-title="Edit" onclick="editBannerServiceTitle('Edit')">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="modal fade" id="BannerServiceTitle_Modal" tabindex="-1" role="dialog"
                                 aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="bannerservice_title_modal_action"></span>
                                                Title
                                                <button type="button" class="close" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vBannerServiceTitle_')">
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
                                                $vValue = 'vBannerServiceTitle_'.$vCode;
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
                                                                            onClick="getAllLanguageCode('vBannerServiceTitle_', 'EN');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                            } else {
                                                                if ($vCode === $default_lang) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vBannerServiceTitle_', '<?php echo $default_lang; ?>');">
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
                                                        onclick="saveBannerServiceTitle()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok"
                                                        data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vBannerServiceTitle_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
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
                                           id="vBannerServiceTitle_<?php echo $default_lang; ?>"
                                           name="vBannerServiceTitle_<?php echo $default_lang; ?>"
                                           value="<?php echo $userEditDataArr['vBannerServiceTitle_'.$default_lang]; ?>">
                                </div>
                            </div>
                        <?php } ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <a href="<?php echo $tconfig['tsite_url_main_admin']; ?>banner.php" class="btn btn-info"
                                   target="_blank">Manage Banners
                                </a>
                            </div>
                        </div>
                        <?php } elseif ('RIDE-DELIVERY' === strtoupper($APP_TYPE) || 'YES' === strtoupper($THEME_OBJ->isPXCProThemeActive()) || 'YES' === strtoupper($isDeliveryFeatureAvailable)) { ?>
                        <hr/>

                        <h3 class="show-help-section">
                            <i class="fa fa-caret-right"></i>
                            Banner View - Parcel Delivery
                            <?php if ('RIDE-DELIVERY' === strtoupper($APP_TYPE)) { ?>
                            <i class="fa fa-question-circle show-help-img" data-fancybox
                               data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/ParcelDeliveryBannerRD.png"></i>
                            <?php } else { ?>
                            <i class="fa fa-question-circle show-help-img" data-fancybox
                               data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/ParcelDeliveryBanner.png"></i>
                           <?php } ?>
                        </h3>
                        <?php if ('RIDE-DELIVERY' === strtoupper($APP_TYPE)) { ?>
                        <?php if (count($db_master) > 1) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vDeliverServiceTitle_Default"
                                           name="vDeliverServiceTitle_Default"
                                           value="<?php echo $userEditDataArr['vDeliverServiceTitle_'.$default_lang]; ?>"
                                           data-originalvalue="<?php echo $userEditDataArr['vDeliverServiceTitle_'.$default_lang]; ?>"
                                           readonly="readonly" required>
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip"
                                            data-original-title="Edit" onclick="editDeliverServiceTitle('Edit')">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="modal fade" id="DeliverServiceTitle_Modal" tabindex="-1" role="dialog"
                                 aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="deliverservice_title_modal_action"></span>
                                                Title
                                                <button type="button" class="close" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vDeliverServiceTitle_')">
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
                                $vValue = 'vDeliverServiceTitle_'.$vCode;
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
                                                                            onClick="getAllLanguageCode('vDeliverServiceTitle_', 'EN');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                            } else {
                                                if ($vCode === $default_lang) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vDeliverServiceTitle_', '<?php echo $default_lang; ?>');">
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
                                                        onclick="saveDeliverServiceTitle()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok"
                                                        data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vDeliverServiceTitle_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
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
                                    <input type="text" class="form-control" id="vDeliverServiceTitle_<?php echo $default_lang; ?>" name="vDeliverServiceTitle_<?php echo $default_lang; ?>" value="<?php echo $userEditDataArr['vDeliverServiceTitle_'.$default_lang]; ?>">
                                </div>
                            </div>
                        <?php } ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="admin-notes" style="margin: 0">
                                    <ul>
                                        <li class="show-help-section">Services
                                            <br>
                                            <a href="<?php echo $VehicleCategory['MoreDelivery']['sub_category_url']; ?>" class="btn btn-info" target="_blank">Manage Services
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php } else { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="admin-notes" style="margin: 0">
                                        <a href="<?php echo $tconfig['tsite_url_main_admin']; ?>master_service_category_action.php?id=<?php echo $MasterCategoryArr['Deliver']['iMasterServiceCategoryId']; ?>" class="btn btn-info" target="_blank">Manage Banner</a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php } ?>

                        <?php if ('YES' === strtoupper($THEME_OBJ->isPXCProThemeActive())) { ?>
                        <hr/>

                        <h3 class="show-help-section">
                            <i class="fa fa-caret-right"></i>
                            Banner View - Store Deliveries
                            <i class="fa fa-question-circle show-help-img" data-fancybox data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/StoreDeliveries.png"></i>
                        </h3>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="admin-notes" style="margin: 0">
                                    <ul>
                                        <?php foreach ($DELIVERALL_SERVICES_ARR as $DELIVERALL_SERVICE) { ?>
                                            <li class="show-help-section">
                                                <?php echo $DELIVERALL_SERVICE['vCategory']; ?>
                                                <div>
                                                    <a href="<?php echo $tconfig['tsite_url_main_admin'].'vehicle_category_action.php?id='.$DELIVERALL_SERVICE['iVehicleCategoryId']; ?>&eServiceType=DeliverAll" class="btn btn-info" target="_blank">Manage Banner</a>
                                                </div>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <hr/>
                        <h3 class="show-help-section">
                            <i class="fa fa-caret-right"></i>
                            Service List View - Other Services
                            <?php if ('RIDE' === strtoupper($APP_TYPE)) { ?>
                                <i class="fa fa-question-circle show-help-img" data-fancybox data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/OtherServicesTaxi.png"></i>
                            <?php } elseif ('RIDE-DELIVERY' === strtoupper($APP_TYPE)) { ?>
                                <i class="fa fa-question-circle show-help-img" data-fancybox data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/OtherServicesRD.png"></i>
                            <?php } else { ?>
                                <i class="fa fa-question-circle show-help-img" data-fancybox data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/app_home_screen_help_images/OtherServicesCX.png"></i>
                            <?php } ?>
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
                        <?php } else { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="vOtherServiceTitle_<?php echo $default_lang; ?>" name="vOtherServiceTitle_<?php echo $default_lang; ?>" value="<?php echo $userEditDataArr['vOtherServiceTitle_'.$default_lang]; ?>">
                                </div>
                            </div>
                        <?php } ?>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="admin-notes" style="margin: 0">
                                    <ul>
                                        <?php foreach ($OTHER_SERVICES_ARR as $OTHER_SERVICE) { ?>
                                            <li class="show-help-section">
                                                <?php echo $OTHER_SERVICE['ServiceTitle']; ?>
                                                <div>
                                                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#<?php echo $OTHER_SERVICE['ModalKey']; ?>" style="margin-right: 15px;">Edit Details
                                                    </button>
                                                </div>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>


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

<?php foreach ($OTHER_SERVICES_ARR as $OTHER_SERVICE) { ?>
<div class="modal fade" id="<?php echo $OTHER_SERVICE['ModalKey']; ?>" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content nimot-class">
            <div class="modal-header">
                <h5>
                    Other Services - <?php echo $OTHER_SERVICE['ServiceTitle']; ?>
                    <button type="button" class="close" data-dismiss="modal">x</button>
                </h5>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" name="eOtherServiceCatEdit" id="eOtherServiceCatEdit"
                               value="<?php echo $OTHER_SERVICE['ManageServiceKey']; ?>">
                        <div class="row pb-10">
                            <div class="col-lg-12">
                                <label style="font-size: 13px">Title</label>
                            </div>
                            <div class="col-lg-10">
                                <input type="text" class="form-control"
                                       value="<?php echo $OTHER_SERVICE['ServiceTitle']; ?>" readonly disabled>
                            </div>
                            <div class="col-lg-2">
                                <a href="<?php echo $tconfig['tsite_url_main_admin'].'languages.php?option=&keyword='.$OTHER_SERVICE['ServiceTitleLabel']; ?>"
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
                                       value="<?php echo $OTHER_SERVICE['ServiceDesc']; ?>" readonly disabled>
                            </div>
                            <div class="col-lg-2">
                                <a href="<?php echo $tconfig['tsite_url_main_admin'].'languages.php?option=&keyword='.$OTHER_SERVICE['ServiceDescLabel']; ?>"
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
                                <?php if (!empty($vImageOld[$OTHER_SERVICE['ManageServiceKey']])) { ?>
                                    <div class="marginbottom-10">
                                        <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=100&src='.$tconfig['tsite_upload_app_home_screen_images'].$vImageOld[$OTHER_SERVICE['ManageServiceKey']]; ?>">
                                    </div>
                                <?php } ?>
                                <input type="file" class="form-control" name="vImage">
                                <input type="hidden" class="form-control" name="vImage_old" value="<?php echo $vImageOld[$OTHER_SERVICE['ManageServiceKey']]; ?>">
                            </div>
                            <div class="col-lg-12">
                                <strong style="font-size: 13px">Note: Upload only png image size of 360px X 360px.
                                </strong>
                            </div>
                        </div>
                        <div class="row" style="padding-bottom: 0">
                            <div class="col-lg-12">
                                <label style="font-size: 13px">Display Order</label>
                            </div>
                            <div class="col-md-12 col-sm-12">
                                <select class="form-control" name="iDisplayOrder">
                                    <?php for ($k = 1; $k <= 3; ++$k) { ?>
                                    <option value="<?php echo $k; ?>" <?php echo $DisplayOrder[$OTHER_SERVICE['ManageServiceKey']] === $k ? 'selected' : ''; ?>><?php echo $k; ?></option>
                                    <?php } ?>
                                </select>
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
<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
<script type="text/javascript">
    function editRideServiceTitle(action) {
        $('#rideservice_title_modal_action').html(action);
        $('#RideServiceTitle_Modal').modal('show');
    }

    function saveRideServiceTitle() {
        if ($('#vRideServiceTitle_<?php echo $default_lang; ?>').val() == "") {
            $('#vRideServiceTitle_<?php echo $default_lang; ?>_error').show();
            $('#vRideServiceTitle_<?php echo $default_lang; ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vRideServiceTitle_<?php echo $default_lang; ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vRideServiceTitle_Default').val($('#vRideServiceTitle_<?php echo $default_lang; ?>').val());
        $('#vRideServiceTitle_Default').closest('.row').removeClass('has-error');
        $('#vRideServiceTitle_Default-error').remove();
        $('#RideServiceTitle_Modal').modal('hide');
    }

    <?php if ('RIDE' === strtoupper($APP_TYPE)) { ?>
    function editBannerServiceTitle(action) {
        $('#bannerservice_title_modal_action').html(action);
        $('#BannerServiceTitle_Modal').modal('show');
    }

    function saveBannerServiceTitle() {
        if ($('#vBannerServiceTitle_<?php echo $default_lang; ?>').val() == "") {
            $('#vBannerServiceTitle_<?php echo $default_lang; ?>_error').show();
            $('#vBannerServiceTitle_<?php echo $default_lang; ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vBannerServiceTitle_<?php echo $default_lang; ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vBannerServiceTitle_Default').val($('#vBannerServiceTitle_<?php echo $default_lang; ?>').val());
        $('#vBannerServiceTitle_Default').closest('.row').removeClass('has-error');
        $('#vBannerServiceTitle_Default-error').remove();
        $('#BannerServiceTitle_Modal').modal('hide');
    }

    <?php } elseif ('RIDE-DELIVERY' === strtoupper($APP_TYPE)) { ?>
    function editDeliverServiceTitle(action) {
        $('#deliverservice_title_modal_action').html(action);
        $('#DeliverServiceTitle_Modal').modal('show');
    }

    function saveDeliverServiceTitle() {
        if ($('#vDeliverServiceTitle_<?php echo $default_lang; ?>').val() == "") {
            $('#vDeliverServiceTitle_<?php echo $default_lang; ?>_error').show();
            $('#vDeliverServiceTitle_<?php echo $default_lang; ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vDeliverServiceTitle_<?php echo $default_lang; ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#vDeliverServiceTitle_Default').val($('#vDeliverServiceTitle_<?php echo $default_lang; ?>').val());
        $('#vDeliverServiceTitle_Default').closest('.row').removeClass('has-error');
        $('#vDeliverServiceTitle_Default-error').remove();
        $('#DeliverServiceTitle_Modal').modal('hide');
    }
    <?php } ?>

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

    function saveServicesOther(eStatus) {
        $('#saveOtherServices').val(eStatus);
        $('#other_services_modal').modal('hide');
    }

    function saveServicesRide(eStatus) {
        $('#saveRideServices').val(eStatus);
        $('#ride_services_modal').modal('hide');
    }

    $('[name="iVehicleCategoryId[]"]').change(function (e) {
        if ($(this).is(':checked')) {
            if ($('[name="iVehicleCategoryId[]"]:checked').length > 4) {
                alert("You can only enable 4 services to be shown on App home screen.");
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
</script>
</body>
<!-- END BODY-->
</html>