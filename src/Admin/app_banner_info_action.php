<?php
include_once '../common.php';

require_once TPATH_CLASS.'Imagecrop.class.php';
if (!$MODULES_OBJ->isEnableRideDeliveryV1() || !$userObj->hasPermission('manage-app-banner-info')) {
    $userObj->redirect();
}
$default_lang = $LANG_OBJ->FetchSystemDefaultLang();
$id = $_REQUEST['id'] ?? ''; // iUniqueId
$success = $_REQUEST['success'] ?? '';
$action = ('' !== $id) ? 'Edit' : 'Add';
// $temp_gallery = $tconfig["tpanel_path"];
$tbl_name = 'app_banner_info';
$script = 'app_banner_info';
// fetch all lang from language_master table
$sql = 'SELECT * FROM `language_master` ORDER BY `iDispOrder`';
$db_master = $obj->MySQLSelect($sql);
$count_all = count($db_master);
$tTitle = $_POST['tTitle'] ?? '';
$eStatus = $_POST['eStatus'] ?? 'Inactive';
$vTextColor = $_POST['vTextColor'] ?? '#ffffff';
$vButtonTextColor = $_POST['vButtonTextColor'] ?? '#ffffff';
$vButtonBgColor = $_POST['vButtonBgColor'] ?? '#000000';
$iVehicleCategoryId = $_POST['iVehicleCategoryId'] ?? '';
$thumb = new thumbnail();
// to fetch max iDisplayOrder from table for insert
$select_order = $obj->MySQLSelect('SELECT MAX(iDisplayOrder) AS iDisplayOrder FROM '.$tbl_name);
$iDisplayOrder = $select_order[0]['iDisplayOrder'] ?? 0;
++$iDisplayOrder; // Maximum order number
$iDisplayOrder = $_POST['iDisplayOrder'] ?? $iDisplayOrder;
$temp_order = $_POST['temp_order'] ?? '';
if (isset($_POST['submit'])) { // form submit
    if (SITE_TYPE === 'Demo') {
        $_SESSION['success'] = 2;
        header('Location:app_banner_info.php');

        exit;
    }
    if ($temp_order > $iDisplayOrder) {
        for ($i = $temp_order; $i >= $iDisplayOrder; --$i) {
            $obj->sql_query('UPDATE '.$tbl_name.' SET iDisplayOrder = '.($i + 1).' WHERE iDisplayOrder = '.$i);
        }
    } elseif ($temp_order < $iDisplayOrder) {
        for ($i = $temp_order; $i <= $iDisplayOrder; ++$i) {
            $obj->sql_query('UPDATE '.$tbl_name.' SET iDisplayOrder = '.($i - 1).' WHERE iDisplayOrder = '.$i);
        }
    }
    $q = 'INSERT INTO ';
    $where = '';
    if ('' !== $id) {
        $q = 'UPDATE ';
        $where = " WHERE `iBannerId` = '".$id."'";
    }
    $image_object = $_FILES['vImage']['tmp_name'];
    $image_name = $_FILES['vImage']['name'];
    $image_update = '';
    if ('' !== $image_name) {
        $filecheck = basename($_FILES['vImage']['name']);
        $fileextarr = explode('.', $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        if ('jpg' !== $ext && 'gif' !== $ext && 'png' !== $ext && 'jpeg' !== $ext && 'bmp' !== $ext) {
            $flag_error = 1;
            $var_msg = 'Not valid image extension of .jpg, .jpeg, .gif, .png';
        }
        $image_info = getimagesize($_FILES['vImage']['tmp_name']);
        $image_width = $image_info[0];
        $image_height = $image_info[1];
        /*if($image_width > 2900){
            $flag_error = 1;
            $var_msg = "Image Size is too Large.Please Upload Proper Dimension Image.";
        }*/
        /*if($_FILES['vImage']['size'] > 1048576){
            $flag_error = 1;
            $var_msg = "Image Size is too Large";
        }*/
        if (1 === $flag_error) {
            // echo $tconfig['tsite_url'];
            $_SESSION['success'] = '3';
            $_SESSION['var_msg'] = $var_msg;
            header('Location:app_banner_info.php'.(('' !== $sid) ? '?'.$sid : ''));

            exit;
            /*getPostForm($_POST,$var_msg,"banner_action.php?success=0&var_msg=".$var_msg);
            exit;*/
        }
        $Photo_Gallery_folder = $tconfig['tsite_upload_app_banner_images_path'].'/';
        if (!is_dir($Photo_Gallery_folder)) {
            mkdir($Photo_Gallery_folder, 0777);
            chmod($Photo_Gallery_folder, 0777);
        }
        $img = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, '', 'jpg,png,gif,jpeg');
        $vImage = $img[0];
        $image_update = "`vImage` = '".$vImage."',";
    }
    for ($i = 0; $i < count($db_master); ++$i) {
        $tTitle = $tSubtitle = $tButtonText = '';
        if (isset($_POST['tTitle_'.$db_master[$i]['vCode']])) {
            $tTitle = $_POST['tTitle_'.$db_master[$i]['vCode']];
        }
        if (isset($_POST['tSubtitle_'.$db_master[$i]['vCode']])) {
            $tSubtitle = $_POST['tSubtitle_'.$db_master[$i]['vCode']];
        }
        if (isset($_POST['tButtonText_'.$db_master[$i]['vCode']])) {
            $tButtonText = $_POST['tButtonText_'.$db_master[$i]['vCode']];
        }
        $tTitleArr['tTitle_'.$db_master[$i]['vCode']] = $tTitle;
        $tSubtitleArr['tSubtitle_'.$db_master[$i]['vCode']] = $tSubtitle;
        $ButtonTextArr['tButtonText_'.$db_master[$i]['vCode']] = $tButtonText;
    }
    $jsonTitle = getJsonFromAnArr($tTitleArr);
    $jsonSubtitle = getJsonFromAnArr($tSubtitleArr);
    $jsonButtonText = getJsonFromAnArr($ButtonTextArr);
    $query = $q.' `'.$tbl_name."` SET
            `tTitle` = '".$jsonTitle."',
            `tSubtitle` = '".$jsonSubtitle."',
            `tButtonText` = '".$jsonButtonText."',
            {$image_update}
            `eStatus` = '".$eStatus."',
            `vTextColor` = '".$vTextColor."',
            `vButtonTextColor` = '".$vButtonTextColor."',
            `vButtonBgColor` = '".$vButtonBgColor."',
            `iDisplayOrder` = '".$iDisplayOrder."',
            `iVehicleCategoryId` = '".$iVehicleCategoryId."'".$where;
    $obj->sql_query($query);
    if ('' !== $id) {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
    } else {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INSERT_MSG'];
    }
    header('Location:app_banner_info.php');

    exit;
}
// for Edit
$userEditDataArr = [];
if ('Edit' === $action) {
    $sql = 'SELECT * FROM '.$tbl_name." WHERE iBannerId = '".$id."'";
    $db_data = $obj->MySQLSelect($sql);
    if (count($db_data) > 0) {
        $tTitle = json_decode($db_data[0]['tTitle'], true);
        foreach ($tTitle as $key => $value) {
            $userEditDataArr[$key] = $value;
        }
        $tSubtitle = json_decode($db_data[0]['tSubtitle'], true);
        foreach ($tSubtitle as $key4 => $value4) {
            $userEditDataArr[$key4] = $value4;
        }
        $tButtonText = json_decode($db_data[0]['tButtonText'], true);
        foreach ($tButtonText as $key5 => $value5) {
            $userEditDataArr[$key5] = $value5;
        }
        $vImage = $db_data[0]['vImage'];
        $vTextColor = $db_data[0]['vTextColor'];
        $vButtonTextColor = $db_data[0]['vButtonTextColor'];
        $vButtonBgColor = $db_data[0]['vButtonBgColor'];
        $eStatus = $db_data[0]['eStatus'];
        $iDisplayOrder = $db_data[0]['iDisplayOrder'];
        $iVehicleCategoryId = $db_data[0]['iVehicleCategoryId'];
    }
}
$display_order = [];
$sqlall = 'SELECT iDisplayOrder FROM '.$tbl_name;
$db_data_all = $obj->MySQLSelect($sqlall);
foreach ($db_data_all as $d) {
    $display_order[] = $d['iDisplayOrder'];
}
$max_usage_order = max($display_order) + 1;
$EN_available = $LANG_OBJ->checkLanguageExist();
$db_master = $LANG_OBJ->getLangDataDefaultFirst($db_master);
$sql_vehicle_category_table_name = getVehicleCategoryTblName();
$ServiceData = $obj->MySQLSelect('SELECT iVehicleCategoryId,iParentId,vCategory_'.$default_lang.' as vCategory FROM '.$sql_vehicle_category_table_name." WHERE eStatus='Active' AND iParentId='0' ORDER BY iDisplayOrder");
?>
<!DOCTYPE html>
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title>Admin | App Banner Info <?php echo $action; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet"/>
    <?php include_once 'global_files.php'; ?>
    <!-- On OFF switch -->
    <link href="../assets/css/jquery-ui.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css"/>
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
                    <h2><?php echo $action; ?> App Banner Info</h2>
                    <a href="app_banner_info.php<?php echo ('' !== $sid) ? '?'.$sid : ''; ?>">
                        <input type="button" value="Back to Listing" class="add-btn">
                    </a>
                </div>
            </div>
            <hr/>
            <div class="body-div">
                <div class="form-group">
                    <?php if (0 === $success && '' !== $_REQUEST['var_msg']) { ?>
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
                    <form method="post" action="" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="hidden" name="vImage_old" value="<?php echo $vImage; ?>">
                        <div class="row">
                            <div class="col-lg-12">
                                <label>Image<?php echo ('' === $vImage) ? '<span class="red"> *</span>' : ''; ?></label>
                            </div>
                            <div class="col-lg-6">
                                <?php if ('' !== $vImage) { ?>
                                    <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?w=400&h=200&src='.$tconfig['tsite_upload_app_banner_images'].$vImage; ?>"
                                         style="width:200px;height:100px;">
                                    <input type="file" name="vImage" id="vImage" value="<?php echo $vImage; ?>"/>
                                <?php } else { ?>
                                    <input type="file" name="vImage" id="vImage" value="<?php echo $vImage; ?>" required/>
                                <?php } ?>
                                <b>[Note: Recommended dimension is 2880 * 1620.]</b>
                            </div>
                        </div>
                        <?php if (count($db_master) > 1) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title</label>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <input type="text" class="form-control <?php echo ('' === $id) ? 'readonly-custom' : ''; ?>"
                                           id="tTitle_Default" name="tTitle_Default"
                                           value="<?php echo $userEditDataArr['tTitle_'.$default_lang]; ?>"
                                           data-originalvalue="<?php echo $userEditDataArr['tTitle_'.$default_lang]; ?>"
                                           readonly="readonly"
                                           required <?php if ('' === $id) { ?> onclick="editTitle('Add')" <?php } ?>>
                                </div>
                                <?php if ('' !== $id) { ?>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                data-original-title="Edit" onclick="editTitle('Edit')">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="modal fade" id="Title_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                 data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="modal_action"></span>
                                                Title
                                                <button type="button" class="close" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'tTitle_')">x
                                                </button>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                                            for ($i = 0; $i < $count_all; ++$i) {
                                                $vCode = $db_master[$i]['vCode'];
                                                $vTitle = $db_master[$i]['vTitle'];
                                                $eDefault = $db_master[$i]['eDefault'];
                                                $vValue = 'tTitle_'.$vCode;
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
                                                                            onClick="getAllLanguageCode('tTitle_', 'EN');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                            } else {
                                                                if ($vCode === $default_lang) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('tTitle_', '<?php echo $default_lang; ?>');">
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
                                                        onclick="saveTitle()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'tTitle_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
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
                                <div class="col-md-6 col-sm-6">
                                    <input type="text" class="form-control <?php echo ('' === $id) ? 'readonly-custom' : ''; ?>"
                                           id="tSubtitle_Default" name="tSubtitle_Default"
                                           value="<?php echo $userEditDataArr['tSubtitle_'.$default_lang]; ?>"
                                           data-originalvalue="<?php echo $userEditDataArr['tSubtitle_'.$default_lang]; ?>"
                                           readonly="readonly"
                                           required <?php if ('' === $id) { ?> onclick="editSubtitle('Add')" <?php } ?>>
                                </div>
                                <?php if ('' !== $id) { ?>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                data-original-title="Edit" onclick="editSubtitle('Edit')">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="modal fade" id="Subtitle_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                 data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="modal_action"></span>
                                                Subtitle
                                                <button type="button" class="close" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'tSubtitle_')">x
                                                </button>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                            for ($i = 0; $i < $count_all; ++$i) {
                                $vCode = $db_master[$i]['vCode'];
                                $vTitle = $db_master[$i]['vTitle'];
                                $eDefault = $db_master[$i]['eDefault'];
                                $descVal = 'tSubtitle_'.$vCode;
                                ${$descVal} = $userEditDataArr['tSubtitle_'.$vCode];
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
                                                    <div class="<?php echo $page_title_class; ?> desc-block">
                                                        <input type="text" class="form-control subtitle-txt"
                                                               name="<?php echo $descVal; ?>" id="<?php echo $descVal; ?>"
                                                               value="<?php echo ${$descVal}; ?>"
                                                               data-originalvalue="<?php echo ${$descVal}; ?>"
                                                               placeholder="<?php echo $vTitle; ?> Value">
                                                        <div class="pull-left" style="margin-top: 5px">Preferred
                                                            characters 250
                                                        </div>
                                                        <div class="desc_counter pull-right" style="margin-top: 5px">
                                                            250/250
                                                        </div>
                                                        <div class="text-danger" id="<?php echo $descVal.'_error'; ?>"
                                                             style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                    </div>
                                                    <?php
                                    if (count($db_master) > 1) {
                                        if ($EN_available) {
                                            if ('EN' === $vCode) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('tSubtitle_', 'EN');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                            } else {
                                                if ($vCode === $default_lang) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('tSubtitle_', '<?php echo $default_lang; ?>');">
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
                                                        onclick="saveSubtitle()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'tSubtitle_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                            </div>
                                        </div>
                                        <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Button Text</label>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <input type="text" class="form-control <?php echo ('' === $id) ? 'readonly-custom' : ''; ?>"
                                           id="tButtonText_Default" name="tButtonText_Default"
                                           value="<?php echo $userEditDataArr['tButtonText_'.$default_lang]; ?>"
                                           data-originalvalue="<?php echo $userEditDataArr['tButtonText_'.$default_lang]; ?>"
                                           readonly="readonly"
                                           required <?php if ('' === $id) { ?> onclick="editButtonText('Add')" <?php } ?>>
                                </div>
                                <?php if ('' !== $id) { ?>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                data-original-title="Edit" onclick="editButtonText('Edit')">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="modal fade" id="ButtonText_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                 data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="modal_action"></span>
                                                Button Text
                                                <button type="button" class="close" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'tButtonText_')">x
                                                </button>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                            for ($i = 0; $i < $count_all; ++$i) {
                                $vCode = $db_master[$i]['vCode'];
                                $vTitle = $db_master[$i]['vTitle'];
                                $eDefault = $db_master[$i]['eDefault'];
                                $descVal = 'tButtonText_'.$vCode;
                                ${$descVal} = $userEditDataArr['tButtonText_'.$vCode];
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
                                                        <label>Button Text (<?php echo $vTitle; ?>
                                                            ) <?php echo $required_msg; ?></label>
                                                    </div>
                                                    <div class="<?php echo $page_title_class; ?> desc-block">
                                                        <input type="text" class="form-control subtitle-txt"
                                                               name="<?php echo $descVal; ?>" id="<?php echo $descVal; ?>"
                                                               value="<?php echo ${$descVal}; ?>"
                                                               data-originalvalue="<?php echo ${$descVal}; ?>"
                                                               placeholder="<?php echo $vTitle; ?> Value">
                                                        <div class="pull-left" style="margin-top: 5px">Preferred
                                                            characters 250
                                                        </div>
                                                        <div class="desc_counter pull-right" style="margin-top: 5px">
                                                            250/250
                                                        </div>
                                                        <div class="text-danger" id="<?php echo $descVal.'_error'; ?>"
                                                             style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                    </div>
                                                    <?php
                                    if (count($db_master) > 1) {
                                        if ($EN_available) {
                                            if ('EN' === $vCode) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('tButtonText_', 'EN');">
                                                                        Convert To All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                            } else {
                                                if ($vCode === $default_lang) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('tButtonText_', '<?php echo $default_lang; ?>');">
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
                                                        onclick="saveButtonText()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'tButtonText_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
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
                                <div class="col-md-6 col-sm-6">
                                    <input type="text" class="form-control" id="tTitle_<?php echo $default_lang; ?>"
                                           name="tTitle_<?php echo $default_lang; ?>"
                                           value="<?php echo $userEditDataArr['tTitle_'.$default_lang]; ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Subtitle</label>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <input type="text" class="form-control" id="tSubtitle_<?php echo $default_lang; ?>"
                                           name="tSubtitle_<?php echo $default_lang; ?>"
                                           value="<?php echo $userEditDataArr['tSubtitle_'.$default_lang]; ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Button Text</label>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <input type="text" class="form-control" id="tButtonText_<?php echo $default_lang; ?>"
                                           name="tButtonText_<?php echo $default_lang; ?>"
                                           value="<?php echo $userEditDataArr['tButtonText_'.$default_lang]; ?>">
                                </div>
                            </div>
                        <?php } ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <label>Service Category</label>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <select class="form-control" name="iVehicleCategoryId" id="iVehicleCategoryId">
                                    <?php foreach ($ServiceData as $Data) { ?>
                                        <option value="<?php echo $Data['iVehicleCategoryId']; ?>" <?php echo $iVehicleCategoryId === $Data['iVehicleCategoryId'] ? 'selected' : ''; ?>><?php echo $Data['vCategory']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <label>Title & Subtitle Text Color</label>
                            </div>
                            <div class="col-md-1 col-sm-1">
                                <input type="color" id="TextColor" class="form-control" value="<?php echo $vTextColor; ?>"/>
                                <input type="hidden" name="vTextColor" id="vTextColor" value="<?php echo $vTextColor; ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <label>Button Text Color</label>
                            </div>
                            <div class="col-md-1 col-sm-1">
                                <input type="color" id="btnTextColor" class="form-control"
                                       value="<?php echo $vButtonTextColor; ?>"/>
                                <input type="hidden" name="vButtonTextColor" id="vButtonTextColor"
                                       value="<?php echo $vButtonTextColor; ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <label>Button Background Color</label>
                            </div>
                            <div class="col-md-1 col-sm-1">
                                <input type="color" id="btnBgBolor" class="form-control"
                                       value="<?php echo $vButtonBgColor; ?>"/>
                                <input type="hidden" name="vButtonBgColor" id="vButtonBgColor"
                                       value="<?php echo $vButtonBgColor; ?>">
                            </div>
                        </div>
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
                        <div class="row">
                            <div class="col-lg-12">
                                <label>Order</label>
                            </div>
                            <div class="col-lg-6">
                                <?php
                                $temp = 1;
$dataArray = [];
$query1 = 'SELECT DISTINCT iDisplayOrder FROM '.$tbl_name.' ORDER BY iDisplayOrder';
$data_order = $obj->MySQLSelect($query1);
foreach ($data_order as $value) {
    $dataArray[] = $value['iDisplayOrder'];
    $temp = $iDisplayOrder;
}
?>
                                <input type="hidden" name="temp_order" id="temp_order" value="<?php echo $temp; ?>">
                                <select name="iDisplayOrder" class="form-control">
                                    <?php foreach ($dataArray as $arr) { ?>
                                        <option <?php echo $arr === $temp ? ' selected="selected"' : ''; ?> value="<?php echo $arr; ?>">
                                            -- <?php echo $arr; ?> --
                                        </option>
                                    <?php } ?>
                                    <?php if ('Add' === $action) { ?>
                                        <option value="<?php echo $temp; ?>">
                                            -- <?php echo $temp; ?> --
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <?php if (('Edit' === $action && $userObj->hasPermission('edit-app-banner-info')) || ('Add' === $action && $userObj->hasPermission('create-app-banner-info'))) { ?>
                                    <input type="submit" class="save btn-info" name="submit" id="submit"
                                           value="<?php echo $action; ?> Banner">
                                <?php } ?>
                                <a href="app_banner_info.php<?php echo ('' !== $sid) ? '?'.$sid : ''; ?>"
                                   class="btn btn-default back_link">Cancel
                                </a>
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
    function editTitle(action) {
        $('#modal_action').html(action);
        $('#Title_Modal').modal('show');
    }

    function saveTitle() {
        if ($('#tTitle_<?php echo $default_lang; ?>').val() == "") {
            $('#tTitle_<?php echo $default_lang; ?>_error').show();
            $('#tTitle_<?php echo $default_lang; ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#tTitle_<?php echo $default_lang; ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#tTitle_Default').val($('#tTitle_<?php echo $default_lang; ?>').val());
        $('#tTitle_Default').closest('.row').removeClass('has-error');
        $('#tTitle_Default-error').remove();
        $('#Title_Modal').modal('hide');
    }

    function editSubtitle(action) {
        $('#modal_action').html(action);
        $('#Subtitle_Modal').modal('show');
    }

    function saveSubtitle() {
        if ($('#tSubtitle_<?php echo $default_lang; ?>').val() == "") {
            $('#tSubtitle_<?php echo $default_lang; ?>_error').show();
            $('#tSubtitle_<?php echo $default_lang; ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#tSubtitle_<?php echo $default_lang; ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#tSubtitle_Default').val($('#tSubtitle_<?php echo $default_lang; ?>').val());
        $('#Subtitle_Modal').modal('hide');
    }

    function editButtonText(action) {
        $('#modal_action').html(action);
        $('#ButtonText_Modal').modal('show');
    }

    function saveButtonText() {
        if ($('#tButtonText_<?php echo $default_lang; ?>').val() == "") {
            $('#tButtonText_<?php echo $default_lang; ?>_error').show();
            $('#tButtonText_<?php echo $default_lang; ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#tButtonText_<?php echo $default_lang; ?>_error').hide();
            }, 5000);
            return false;
        }

        $('#tButtonText_Default').val($('#tButtonText_<?php echo $default_lang; ?>').val());
        $('#ButtonText_Modal').modal('hide');
    }

    $(document).ready(function () {
        $('.subtitle-txt').trigger('keyup');
    });


    $(document).on('keyup', '.subtitle-txt', function (e) {
        var tval = $(this).val(),
            tlength = tval.length,
            set = 250,
            remain = parseInt(set - tlength);
        if (tlength > 0) {
            $(this).closest('.desc-block').find('.desc_counter').text(remain + "/250");
            if (remain <= 0) {
                // $(this).val((tval).substring(0, set));
                $(this).closest('.desc-block').find('.desc_counter').text("0/250");
                return false;
            }
        } else {
            $(this).closest('.desc-block').find('.desc_counter').text("250/250");
            return false;
        }
    });

    $("#btnTextColor, #btnBgBolor, #TextColor").on("input", function () {
        var color = $(this).val();
        if ($(this).attr('id') == "btnTextColor") {
            $('#vButtonTextColor').val(color);
        } else if ($(this).attr('id') == "btnBgBolor") {
            $('#vButtonBgColor').val(color);
        } else {
            $('#vTextColor').val(color);
        }
    });
</script>
</body>
<!-- END BODY-->
</html>