<?php
include_once '../common.php';

require_once TPATH_CLASS.'Imagecrop.class.php';
$mId = $id = $_REQUEST['id'] ?? ''; // iUniqueId
$tbl_name = $master_service_category_tbl;
$sql = 'SELECT eType FROM '.$tbl_name." WHERE iMasterServiceCategoryId = '".$mId."'";
$permissionQuery = $obj->MySQLSelect($sql);
$titleTxt = ' Master Service Category';
if ('Ride' === $permissionQuery[0]['eType']) {
    $commonTxt .= 'taxi-service';
    $titleTxt = 'Taxi Service';
}
if ('Deliver' === $permissionQuery[0]['eType']) {
    $commonTxt .= 'parcel-delivery';
    $titleTxt = 'Parcel Delivery';
}
if ('DeliverAll' === $permissionQuery[0]['eType']) {
    $commonTxt .= 'deliverall';
    $titleTxt = 'Store Delivery';
}
if ('VideoConsult' === $permissionQuery[0]['eType']) {
    $commonTxt .= 'video-consultation';
    $titleTxt = 'Video Consultation';
}
if ('Bidding' === $permissionQuery[0]['eType']) {
    $commonTxt .= 'bidding';
    $titleTxt = 'Bidding';
}
if ('UberX' === $permissionQuery[0]['eType']) {
    $commonTxt .= 'uberx';
    $titleTxt = 'On-Demand Service';
}
if ('RentEstate' === $permissionQuery[0]['eType']) {
    $commonTxt .= 'rentestate';
    $titleTxt = 'Buy, Sell & Rent Real Estate';
}
if ('RentCars' === $permissionQuery[0]['eType']) {
    $commonTxt .= 'rentcars';
    $titleTxt = 'Buy,Sell & Rent Cars';
}
if ('RentItem' === $permissionQuery[0]['eType']) {
    $commonTxt .= 'rentitem';
    $titleTxt = 'Buy,Sell & Rent Items';
}
if ('MedicalServices' === $permissionQuery[0]['eType']) {
    $commonTxt .= 'medical';
    $titleTxt = 'Medical Services';
}
if ('RideShare' === $permissionQuery[0]['eType']) {
    $commonTxt .= 'rideshare';
    $titleTxt = 'Ride Share';
}
if ('TrackAnyService' === $permissionQuery[0]['eType']) {
    $commonTxt .= 'trackanyservice';
    $titleTxt = 'Tracking Service';
}
if ('TrackService' === $permissionQuery[0]['eType']) {
    $commonTxt .= 'trackservice';
    $titleTxt = 'Tracking Service';
}
if ('NearBy' === $permissionQuery[0]['eType']) {
    $commonTxt .= 'nearby';
    $titleTxt = 'NearBy';
}
$view = 'view-service-content-'.$commonTxt;
$update = 'update-service-content-'.$commonTxt;
$updateStatus = 'update-status-service-content-'.$commonTxt;
if (!$userObj->hasPermission($view) || empty($id)) {
    $userObj->redirect();
}
$default_lang = $LANG_OBJ->FetchSystemDefaultLang();
$success = $_REQUEST['success'] ?? '';
$action = ('' !== $id) ? 'Edit' : 'Add';
$script = 'VehicleCategory';
$db_master = $obj->MySQLSelect('SELECT * FROM `language_master` ORDER BY `iDispOrder`');
$count_all = count($db_master);
$eStatus = $_POST['eStatus'] ?? 'Inactive';
$vTextColor = $_POST['vTextColor'] ?? '#ffffff';
$vBgColor = $_POST['vBgColor'] ?? '#ffffff';
$banner_lang = $_REQUEST['banner_lang'] ?? $default_lang;
$thumb = new thumbnail();
if (isset($_POST['submit'])) { // form submit
    if (SITE_TYPE === 'Demo') {
        $_SESSION['success'] = 2;
        header('Location:master_service_category.php');

        exit;
    }
    $eType = $_POST['eType'];
    if (in_array($eType, ['VideoConsult', 'Bidding', 'RideShare', 'TrackService'], true) || 'Yes' === $THEME_OBJ->isPXCProThemeActive()) {
        $db_data_master = $obj->MySQLSelect('SELECT vCategoryImage FROM '.$tbl_name." WHERE iMasterServiceCategoryId = '".$id."'");
        $tCatImagesArr = [];
        if (!empty($db_data_master[0]['vCategoryImage'])) {
            $tCatImagesArr = json_decode($db_data_master[0]['vCategoryImage'], true);
        } else {
            foreach ($db_master as $dbvalue) {
                $tCatImagesArr['vCategoryImage_'.$dbvalue['vCode']] = '';
            }
        }
    }
    $Data_Update = [];
    $image_object = $_FILES['vImage1']['tmp_name'];
    $image_name = $_FILES['vImage1']['name'];
    if ('' !== $image_name) {
        $filecheck = basename($_FILES['vImage1']['name']);
        $fileextarr = explode('.', $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        if ('jpg' !== $ext && 'gif' !== $ext && 'png' !== $ext && 'jpeg' !== $ext && 'bmp' !== $ext) {
            $flag_error = 1;
            $var_msg = 'Not valid image extension of .jpg, .jpeg, .gif, .png, .bmp';
        }
        $image_info = getimagesize($_FILES['vImage1']['tmp_name']);
        $image_width = $image_info[0];
        $image_height = $image_info[1];
        if (1 === $flag_error) {
            $_SESSION['success'] = '3';
            $_SESSION['var_msg'] = $var_msg;
            header('Location:master_service_category_action.php?id='.$mId);

            exit;
        }
        $Photo_Gallery_folder = $tconfig['tsite_upload_app_home_screen_images_path'];
        if (!is_dir($Photo_Gallery_folder)) {
            mkdir($Photo_Gallery_folder, 0777);
            chmod($Photo_Gallery_folder, 0777);
        }
        $img = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, '', 'jpg,png,gif,jpeg,bmp');
        $vImage = $img[0];
        if (in_array($eType, ['VideoConsult', 'Bidding', 'RideShare', 'TrackService'], true) || 'Yes' === $THEME_OBJ->isPXCProThemeActive()) {
            $tCatImagesArr['vCategoryImage_'.$banner_lang] = $vImage;
            $Data_Update['vCategoryImage'] = json_encode($tCatImagesArr);
            if ($banner_lang === $default_lang) {
                $Data_Update['vIconImage1'] = $vImage;
            }
        } else {
            $Data_Update['vIconImage1'] = $vImage;
        }
        if (!empty($_POST['vImage1_old']) && file_exists($Photo_Gallery_folder.$_POST['vImage1_old']) && !in_array($eType, ['VideoConsult', 'Bidding', 'RideShare', 'TrackService'], true)) {
            if (SITE_TYPE !== 'Demo') {
                unlink($Photo_Gallery_folder.$_POST['vImage1_old']);
            }
        }
    }
    for ($i = 0; $i < count($db_master); ++$i) {
        $vCategoryName = '';
        if (isset($_POST['vCategoryName_'.$db_master[$i]['vCode']])) {
            $vCategoryName = $_POST['vCategoryName_'.$db_master[$i]['vCode']];
        }
        $vCategoryNameArr['vCategoryName_'.$db_master[$i]['vCode']] = $vCategoryName;
        $vCategoryDesc = '';
        if (isset($_POST['vCategoryDesc_'.$db_master[$i]['vCode']])) {
            $vCategoryDesc = $_POST['vCategoryDesc_'.$db_master[$i]['vCode']];
        }
        $vCategoryDescArr['vCategoryDesc_'.$db_master[$i]['vCode']] = $vCategoryDesc;
    }
    $jsonCategoryName = getJsonFromAnArr($vCategoryNameArr);
    if (in_array($eType, ['RideShare', 'TrackService'], true)) {
        $jsonCategoryDesc = getJsonFromAnArr($vCategoryDescArr);
        $Data_Update['vCategoryDesc'] = $jsonCategoryDesc;
    }
    $Data_Update['vCategoryName'] = $jsonCategoryName;
    if ($userObj->hasPermission($updateStatus)) {
        $status = $Data_Update['eStatus'] = $eStatus;
        $ssql = getMasterServiceCategoryQuery($eType, 'Yes');
        if (!in_array($eType, ['Bidding', 'MedicalServices', 'TrackService', 'TrackAnyService', 'RideShare', 'RentEstate', 'RentCars', 'RentItem', 'NearBy'], true)) {
            $sql_vehicle_category_table_name = getVehicleCategoryTblName();
            $vehicle_category_data = $obj->MySQLSelect('SELECT vc.iVehicleCategoryId,vc.vBannerImage, vc.vLogo,vc.vListLogo1,vc.vCategory_'.$default_lang.' as vCategory, vc.eStatus, vc.iDisplayOrder,vc.eCatType,  (select count(iVehicleCategoryId) from '.$sql_vehicle_category_table_name." where iParentId = vc.iVehicleCategoryId AND eStatus != 'Deleted') as SubCategories FROM ".$sql_vehicle_category_table_name." as vc WHERE eStatus != 'Deleted' AND vc.iParentId='0' {$ssql}");
            foreach ($vehicle_category_data as $vehicle_category) {
                $statusNew = $status;
                if ('Active' === $status) {
                    $checkLog = $obj->MySQLSelect("SELECT eStatus FROM vehicle_category_status_log WHERE iVehicleCategoryId = '".$vehicle_category['iVehicleCategoryId']."'");
                    if (!empty($checkLog) && $checkLog > 0) {
                        $statusNew = $checkLog[0]['eStatus'];
                    }
                }
                $obj->sql_query("UPDATE vehicle_category SET eStatus = '".$statusNew."' WHERE iVehicleCategoryId = '".$vehicle_category['iVehicleCategoryId']."'");
                $vehicle_category_new = $obj->MySQLSelect("SELECT iServiceId FROM vehicle_category WHERE iVehicleCategoryId = '".$vehicle_category['iVehicleCategoryId']."'");
                if ($vehicle_category_new[0]['iServiceId'] > 0) {
                    $obj->sql_query("UPDATE service_categories SET eStatus = '{$statusNew}' WHERE iServiceId = '".$vehicle_category_new[0]['iServiceId']."'");
                }
            }
            if ('Ride' === $eType) {
                $statusVal = 'Active' === $status ? 'Yes' : 'No';
                $obj->sql_query("UPDATE configurations SET vValue = '{$statusVal}', eAdminDisplay = '{$statusVal}' WHERE vName = 'ENABLE_CORPORATE_PROFILE'");
            }
        }
    }
    $Data_Update['vTextColor'] = $vTextColor;
    $Data_Update['vBgColor'] = $vBgColor;
    if ('' !== $id) {
        $where = " iMasterServiceCategoryId = '".$id."'";
        $id = $obj->MySQLQueryPerform($tbl_name, $Data_Update, 'update', $where);
    } else {
        $id = $obj->MySQLQueryPerform($tbl_name, $Data_Update, 'insert');
    }
    $obj->sql_query($query);
    if ('Ride' === $eType) {
        $config_val = 'Active' === $eStatus ? 'Yes' : 'No';
        $obj->sql_query("UPDATE configurations SET vValue = '{$config_val}' WHERE vName = 'ENABLE_CORPORATE_PROFILE' ");
        $oCache->flushData();
        $GCS_OBJ->updateGCSData();
    }
    if ('' !== $id) {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
    } else {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INSERT_MSG'];
    }
    if (in_array($eType, ['VideoConsult', 'Bidding', 'RideShare', 'TrackService'], true) || 'Yes' === $THEME_OBJ->isPXCProThemeActive()) {
        header('Location:master_service_category_action.php?id='.$mId.'&banner_lang='.$banner_lang);
    } else {
        header('Location:master_service_category_action.php?id='.$mId);
    }

    exit;
}
$display_banner = $display = '';
// for Edit
$userEditDataArr = [];
if ('Edit' === $action) {
    $sql = 'SELECT * FROM '.$tbl_name." WHERE iMasterServiceCategoryId = '".$id."'";
    $db_data = $obj->MySQLSelect($sql);
    if (count($db_data) > 0) {
        $vCategoryName = json_decode($db_data[0]['vCategoryName'], true);
        foreach ($vCategoryName as $key => $value) {
            $userEditDataArr[$key] = $value;
        }
        $vCategoryDesc = json_decode($db_data[0]['vCategoryDesc'], true);
        foreach ($vCategoryDesc as $key => $value) {
            $userEditDataArr[$key] = $value;
        }
        $vIconImage1 = $db_data[0]['vIconImage1'];
        $vBgImage = $db_data[0]['vBgImage'];
        $vTextColor = $db_data[0]['vTextColor'];
        $vBgColor = $db_data[0]['vBgColor'];
        $eStatus = $db_data[0]['eStatus'];
        $eType = $db_data[0]['eType'];
        if (in_array($eType, ['VideoConsult', 'Bidding', 'RideShare', 'TrackService'], true) || 'Yes' === $THEME_OBJ->isPXCProThemeActive()) {
            $tCatImages = json_decode($db_data[0]['vCategoryImage'], true);
            $vIconImage1 = $tCatImages['vCategoryImage_'.$banner_lang];
        }
    }
}
$script = 'mVehicleCategory_'.$eType;
$EN_available = $LANG_OBJ->checkLanguageExist();
$db_master = $LANG_OBJ->getLangDataDefaultFirst($db_master);
?>
<!DOCTYPE html>
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title>Admin | <?php echo $action; ?> <?php echo $titleTxt; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet"/>
    <?php include_once 'global_files.php'; ?>
    <!-- On OFF switch -->
    <link href="../assets/css/jquery-ui.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css"/>
    <style type="text/css">
        .admin-notes ul li {
            padding-bottom: 0;
            font-size: 13px;
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
                    <h2><?php echo $action; ?> <?php echo $titleTxt; ?></h2>
                    <?php if ('No' === $THEME_OBJ->isCubeJekXv3ProThemeActive()) { ?>
                        <a href="master_service_category.php">
                            <input type="button" value="Back to Listing " class="add-btn">
                        </a>
                    <?php } ?>
                </div>
            </div>
            <hr/>
            <div class="body-div">
                <div class="form-group">
                    <?php if (0 === $success && !empty($_REQUEST['var_msg'])) { ?>
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <?php echo $_REQUEST['var_msg']; ?>
                        </div>
                        <br/>
                    <?php } ?>

                    <?php if (1 === $success) { ?>
                        <div class="alert alert-success alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <?php echo $langage_lbl_admin['LBL_Record_Updated_successfully']; ?>
                        </div>
                        <br/>
                    <?php } ?>

                    <?php if (2 === $success) { ?>
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <?php echo $langage_lbl_admin['LBL_EDIT_DELETE_RECORD']; ?>
                        </div>
                        <br/>
                    <?php } ?>

                    <?php include 'valid_msg.php'; ?>
                    <form method="post" action="" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="hidden" name="eType" value="<?php echo $eType; ?>">
                        <?php if (in_array($eType, ['VideoConsult', 'Bidding', 'RideShare', 'TrackService'], true) || 'Yes' === $THEME_OBJ->isPXCProThemeActive()) {
                            if (count($db_master) > 0) { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Select Language for Banner</label>
                                    </div>
                                    <div class="col-lg-4">
                                        <select class="form-control" name="banner_lang" id="banner_lang">
                                            <?php foreach ($db_master as $db_lang) { ?>
                                                <option value="<?php echo $db_lang['vCode']; ?>" <?php echo $banner_lang === $db_lang['vCode'] ? 'selected' : ''; ?>><?php echo $db_lang['vTitle_EN'].' ('.$db_lang['vCode'].')'; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <input type="hidden" name="banner_lang" value="<?php echo $default_lang; ?>">
                            <?php }
                            } ?>
                        <div class="row">
                            <input type="hidden" name="vImage1_old" value="<?php echo $vIconImage1; ?>">
                            <div class="col-lg-12">
                                <?php if (in_array($eType, ['Ride', 'Deliver', 'DeliverAll'], true) && 'No' === $THEME_OBJ->isPXCProThemeActive()) { ?>
                                    <label>
                                        Icon <?php echo ('' === $vIconImage1) ? '<span class="red"> *</span>' : ''; ?></label>
                                <?php } elseif (in_array($eType, ['RentItem', 'RentCars', 'RentEstate'], true)) { ?>
                                    <label>
                                        Image <?php echo ('' === $vIconImage1) ? '<span class="red"> *</span>' : ''; ?></label>
                                <?php } elseif (in_array($eType, ['VideoConsult', 'Bidding', 'RideShare', 'TrackService'], true) || 'Yes' === $THEME_OBJ->isPXCProThemeActive()) { ?>
                                    <label>
                                        Banner <?php echo ('' === $vIconImage1) ? '<span class="red"> *</span>' : ''; ?></label>
                                <?php } ?>
                            </div>
                            <div class="col-lg-4">
                                <?php if ('' !== $vIconImage1) { ?>
                                    <?php if (!in_array($eType, ['MedicalServices', 'NearBy', 'UberX'], true)) { ?>
                                        <div class="marginbottom-10">
                                            <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=150&src='.$tconfig['tsite_upload_app_home_screen_images'].$vIconImage1; ?>">
                                        </div>
                                        <div class="marginbottom-10">
                                            <input type="file" class="form-control" name="vImage1" id="vImage1"
                                                   value=""/>
                                        </div>
                                    <?php }
                                    } else { ?>
                                    <div class="marginbottom-10">
                                        <input type="file" class="form-control" name="vImage1" id="vImage1" value=""
                                               required/>
                                    </div>
                                <?php } ?>
                                <div style="margin: 0">
                                    <?php if (in_array($eType, ['Ride', 'Deliver', 'DeliverAll'], true) && 'No' === $THEME_OBJ->isPXCProThemeActive()) { ?>
                                        [Note: Recommended dimension for banner image(.png) is 360px X 360px.]
                                    <?php } elseif (in_array($eType, ['RentItem', 'RentCars', 'RentEstate'], true)) { ?>
                                        <strong>Note: Recommended dimension for Upload image(.png) is 1050px X 450px.
                                        </strong>
                                    <?php } elseif (in_array($eType, ['VideoConsult', 'Bidding', 'RideShare', 'TrackService'], true) || 'Yes' === $THEME_OBJ->isPXCProThemeActive()) { ?>
                                        <?php if (in_array($eType, ['VideoConsult', 'Bidding'], true)) { ?>
                                            <strong>Note: Recommended dimension to Upload image (png/jpeg) is 1650px X
                                                900px.
                                            </strong>
                                        <?php } else {
                                            if ('Yes' === $THEME_OBJ->isPXCProThemeActive()) { ?>
                                                <strong>Note: Recommended dimension to Upload image(png/jpeg) is 3350px X
                                                990px.
                                            </strong>
                                            <?php } else { ?>
                                            <strong>Note: Recommended dimension to Upload image(png/jpeg) is 1050px X
                                                520px.
                                            </strong>
                                        <?php }
                                            } ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <?php if (!in_array($eType, [], true)) { ?>
                            <?php if (count($db_master) > 1) { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Category Name</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text"
                                               class="form-control <?php echo ('' === $id) ? 'readonly-custom' : ''; ?>"
                                               id="vCategoryName_Default" name="vCategoryName_Default"
                                               value="<?php echo $userEditDataArr['vCategoryName_'.$default_lang]; ?>"
                                               data-originalvalue="<?php echo $userEditDataArr['vCategoryName_'.$default_lang]; ?>"
                                               readonly="readonly"
                                               required <?php if ('' === $id) { ?> onclick="editCategoryName('Add')" <?php } ?>>
                                    </div>
                                    <?php if ('' !== $id) { ?>
                                        <div class="col-lg-2">
                                            <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                    data-original-title="Edit" onclick="editCategoryName('Edit')">
                                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                            </button>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="modal fade" id="Category_Modal" tabindex="-1" role="dialog"
                                     aria-hidden="true"
                                     data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="modal_action"></span>
                                                    Category Name
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vCategoryName_')">x
                                                    </button>
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                    for ($i = 0; $i < $count_all; ++$i) {
                                                        $vCode = $db_master[$i]['vCode'];
                                                        $vTitle = $db_master[$i]['vTitle'];
                                                        $eDefault = $db_master[$i]['eDefault'];
                                                        $vValue = 'vCategoryName_'.$vCode;
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
                                                                   name="<?php echo $vValue; ?>"
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
                                                                                onClick="getAllLanguageCode('vCategoryName_', 'EN');">
                                                                            Convert To All Language
                                                                        </button>
                                                                    </div>
                                                                <?php }
                                                                    } else {
                                                                        if ($vCode === $default_lang) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type="button" name="allLanguage"
                                                                                id="allLanguage" class="btn btn-primary"
                                                                                onClick="getAllLanguageCode('vCategoryName_', '<?php echo $default_lang; ?>');">
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
                                                            onclick="saveCategoryName()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                    <button type="button" class="btn btn-danger btn-ok"
                                                            data-dismiss="modal"
                                                            onclick="resetToOriginalValue(this, 'vCategoryName_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                </div>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                </div>
                                <?php if (in_array($eType, [], true)) { ?>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label>Category Description</label>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <input type="text"
                                                   class="form-control <?php echo ('' === $id) ? 'readonly-custom' : ''; ?>"
                                                   id="vCategoryDesc_Default" name="vCategoryDesc_Default"
                                                   value="<?php echo $userEditDataArr['vCategoryDesc_'.$default_lang]; ?>"
                                                   data-originalvalue="<?php echo $userEditDataArr['vCategoryDesc_'.$default_lang]; ?>"
                                                   readonly="readonly"
                                                   required <?php if ('' === $id) { ?> onclick="editCategoryDesc('Add')" <?php } ?>>
                                        </div>
                                        <?php if ('' !== $id) { ?>
                                            <div class="col-lg-2">
                                                <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                        data-original-title="Edit" onclick="editCategoryDesc('Edit')">
                                                    <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                                </button>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="modal fade" id="CategoryDesc_Modal" tabindex="-1" role="dialog"
                                         aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content nimot-class">
                                                <div class="modal-header">
                                                    <h4>
                                                        <span id="desc_modal_action"></span>
                                                        Category Description
                                                        <button type="button" class="close" data-dismiss="modal"
                                                                onclick="resetToOriginalValue(this, 'vCategoryDesc_')">x
                                                        </button>
                                                    </h4>
                                                </div>
                                                <div class="modal-body">
                                                    <?php
                                    for ($i = 0; $i < $count_all; ++$i) {
                                        $vCode = $db_master[$i]['vCode'];
                                        $vTitle = $db_master[$i]['vTitle'];
                                        $eDefault = $db_master[$i]['eDefault'];
                                        $vValue = 'vCategoryDesc_'.$vCode;
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
                                                                                    id="allLanguage"
                                                                                    class="btn btn-primary"
                                                                                    onClick="getAllLanguageCode('vCategoryDesc_', 'EN');">
                                                                                Convert To All Language
                                                                            </button>
                                                                        </div>
                                                                    <?php }
                                                    } else {
                                                        if ($vCode === $default_lang) { ?>
                                                                        <div class="col-md-3 col-sm-3">
                                                                            <button type="button" name="allLanguage"
                                                                                    id="allLanguage"
                                                                                    class="btn btn-primary"
                                                                                    onClick="getAllLanguageCode('vCategoryDesc_', '<?php echo $default_lang; ?>');">
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
                                                        <button type="button" class="save"
                                                                style="margin-left: 0 !important"
                                                                onclick="saveCategoryDesc()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                        <button type="button" class="btn btn-danger btn-ok"
                                                                data-dismiss="modal"
                                                                onclick="resetToOriginalValue(this, 'vCategoryDesc_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                    </div>
                                                </div>
                                                <div style="clear:both;"></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } else { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Category Name</label>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control" id="vCategoryName_<?php echo $default_lang; ?>"
                                               name="vCategoryName_<?php echo $default_lang; ?>"
                                               value="<?php echo $userEditDataArr['vCategoryName_'.$default_lang]; ?>">
                                    </div>
                                </div>
                                <?php if (in_array($eType, ['RideShare', 'TrackService'], true)) { ?>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label>Category Description</label>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <input type="text" class="form-control"
                                                   id="vCategoryDesc_<?php echo $default_lang; ?>"
                                                   name="vCategoryDesc_<?php echo $default_lang; ?>"
                                                   value="<?php echo $userEditDataArr['vCategoryDesc_'.$default_lang]; ?>">
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>

                        <?php if (in_array($eType, ['RentItem', 'RentEstate', 'RentCars'], true)) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title Color</label>
                                </div>
                                <div class="col-md-1 col-sm-1">
                                    <input type="color" id="TextColor" class="form-control" value="<?php echo $vTextColor; ?>"/>
                                    <input type="hidden" name="vTextColor" id="vTextColor" value="<?php echo $vTextColor; ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Background Color</label>
                                </div>
                                <div class="col-md-1 col-sm-1">
                                    <input type="color" id="BgColor" class="form-control" value="<?php echo $vBgColor; ?>"/>
                                    <input type="hidden" name="vBgColor" id="vBgColor" value="<?php echo $vBgColor; ?>">
                                </div>
                            </div>
                        <?php } ?>



                        <?php if ($userObj->hasPermission($updateStatus)) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Status</label>
                                </div>
                                <div class="col-lg-6">
                                    <div class="make-switch" data-on="success" data-off="warning">
                                        <input type="checkbox"
                                               name="eStatus" <?php echo ('' !== $id && 'Inactive' === $eStatus) ? '' : 'checked'; ?>
                                               value="Active"/>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <?php if ($userObj->hasPermission($update)) { ?>
                                    <input type="submit" class="save btn-info" name="submit" id="submit"
                                           value="Update <?php echo $titleTxt; ?>" style="margin-right: 10px">
                                <?php } ?>

                                <?php if ('No' === $THEME_OBJ->isCubeJekXv3ProThemeActive()) { ?>
                                    <a href="master_service_category.php" class="btn btn-default back_link">Cancel</a>
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
<!--END MAIN WRAPPER -->
<div class="row loding-action" id="loaderIcon" style="display:none;">
    <div align="center">
        <img src="default.gif">
        <span>Language Translation is in Process. Please Wait...</span>
    </div>
</div>
<?php include_once 'footer.php'; ?>
<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
<script type="text/javascript">
    function editCategoryName(action) {

        $('#modal_action').html(action);

        $('#Category_Modal').modal('show');

    }


    function saveCategoryName() {

        if ($('#vCategoryName_<?php echo $default_lang; ?>').val() == "") {

            $('#vCategoryName_<?php echo $default_lang; ?>_error').show();

            $('#vCategoryName_<?php echo $default_lang; ?>').focus();

            clearInterval(langVar);

            langVar = setTimeout(function () {

                $('#vCategoryName_<?php echo $default_lang; ?>_error').hide();

            }, 5000);

            return false;

        }


        $('#vCategoryName_Default').val($('#vCategoryName_<?php echo $default_lang; ?>').val());

        $('#vCategoryName_Default').closest('.row').removeClass('has-error');

        $('#vCategoryName_Default-error').remove();

        $('#Category_Modal').modal('hide');

    }


    function editCategoryDesc(action) {

        $('#desc_modal_action').html(action);

        $('#CategoryDesc_Modal').modal('show');

    }


    function saveCategoryDesc() {

        if ($('#vCategoryDesc_<?php echo $default_lang; ?>').val() == "") {

            $('#vCategoryDesc_<?php echo $default_lang; ?>_error').show();

            $('#vCategoryDesc_<?php echo $default_lang; ?>').focus();

            clearInterval(langVar);

            langVar = setTimeout(function () {

                $('#vCategoryDesc_<?php echo $default_lang; ?>_error').hide();

            }, 5000);

            return false;

        }


        $('#vCategoryDesc_Default').val($('#vCategoryDesc_<?php echo $default_lang; ?>').val());

        $('#vCategoryDesc_Default').closest('.row').removeClass('has-error');

        $('#vCategoryDesc_Default-error').remove();

        $('#CategoryDesc_Modal').modal('hide');

    }


    $("#TextColor").on("input", function () {

        var color = $(this).val();

        $('#vTextColor').val(color);

    });


    $("#BgColor").on("input", function () {

        var color = $(this).val();

        $('#vBgColor').val(color);

    });


    $('#banner_lang').change(function () {

        var curr_lang = $(this).val();

        window.location.href = '<?php echo $tconfig['tsite_url_main_admin']; ?>master_service_category_action.php?id=<?php echo $id; ?>&banner_lang=' + curr_lang;

    });

</script>
</body>
<!-- END BODY-->
</html>