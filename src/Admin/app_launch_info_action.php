<?php
include_once '../common.php';

require_once TPATH_CLASS.'Imagecrop.class.php';

$option = $_REQUEST['option'] ?? 'Passenger';
$queryString = '';
if (isset($option) && !empty($option)) {
    $queryString = 'option='.$option;
    $eUserType = $option;
    $script = 'app_launch_info_'.$option;
    $edit_permission = 'edit-'.strtolower($option).'-app-launch-info';
}

if (!$userObj->hasPermission($edit_permission)) {
    $userObj->redirect();
}
$default_lang = $LANG_OBJ->FetchSystemDefaultLang();
$id = $_REQUEST['id'] ?? ''; // iUniqueId
$success = $_REQUEST['success'] ?? '';
$action = ('' !== $id) ? 'Edit' : 'Add';
// $temp_gallery = $tconfig["tpanel_path"];
$tbl_name = 'app_launch_info';
// $script = 'app_launch_info';
// fetch all lang from language_master table
$sql = 'SELECT * FROM `language_master` ORDER BY `iDispOrder`';
$db_master = $obj->MySQLSelect($sql);
$count_all = count($db_master);
$tTitle = $_POST['tTitle'] ?? '';
$eStatus = $_POST['eStatus'] ?? 'Inactive';
// $eUserType = isset($_POST['eUserType']) ? $_POST['eUserType'] : $option;
$thumb = new thumbnail();
$iDisplayOrder = $_POST['iDisplayOrder'] ?? '';
$temp_order = $_POST['temp_order'] ?? '';

if (isset($_POST['submit'])) { // form submit
    if (SITE_TYPE === 'Demo') {
        $_SESSION['success'] = 2;
        header('Location:app_launch_info.php?'.$queryString);

        exit;
    }
    if ($temp_order > $iDisplayOrder) {
        for ($i = $temp_order; $i >= $iDisplayOrder; --$i) {
            $obj->sql_query('UPDATE '.$tbl_name.' SET iDisplayOrder = '.($i + 1)." WHERE eUserType = '{$option}' AND iDisplayOrder = ".$i);
        }
    } elseif ($temp_order < $iDisplayOrder) {
        for ($i = $temp_order; $i <= $iDisplayOrder; ++$i) {
            $obj->sql_query('UPDATE '.$tbl_name.' SET iDisplayOrder = '.($i - 1)." WHERE eUserType = '{$option}' AND iDisplayOrder = ".$i);
        }
    }
    $Data_update = [];
    $q = 'INSERT INTO ';
    $where = '';
    if ('' !== $id) {
        $q = 'UPDATE ';
        $where = " WHERE `iImageId` = '".$id."'";
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
            header('Location:app_launch_info.php'.(('' !== $sid) ? '?'.$sid : ''));

            exit;
            /*getPostForm($_POST,$var_msg,"banner_action.php?success=0&var_msg=".$var_msg);
            exit;*/
        }
        $Photo_Gallery_folder = $tconfig['tsite_upload_app_launch_images_path'].'/';
        if (!is_dir($Photo_Gallery_folder)) {
            mkdir($Photo_Gallery_folder, 0777);
            chmod($Photo_Gallery_folder, 0777);
        }
        $img = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, '', 'jpg,png,gif,jpeg');
        $vImage = $img[0];
        $image_update = "`vImage` = '".$vImage."',";
        $Data_update['vImage'] = $vImage;
    }
    for ($i = 0; $i < count($db_master); ++$i) {
        $tCategoryName = $tDescription = '';
        if (isset($_POST['tTitle_'.$db_master[$i]['vCode']])) {
            $tTitle = $_POST['tTitle_'.$db_master[$i]['vCode']];
        }
        if (isset($_POST['tSubtitle_'.$db_master[$i]['vCode']])) {
            $tSubtitle = htmlspecialchars($_POST['tSubtitle_'.$db_master[$i]['vCode']], ENT_IGNORE);
        }
        $tTitleArr['tTitle_'.$db_master[$i]['vCode']] = $tTitle;
        $tSubtitleArr['tSubtitle_'.$db_master[$i]['vCode']] = $tSubtitle;
    }
    $jsonTitle = getJsonFromAnArr($tTitleArr);
    // $jsonSubtitle = getJsonFromAnArr($tSubtitleArr);
    $jsonSubtitle = getJsonFromAnArr($tSubtitleArr);
    /*$query = $q ." `".$tbl_name."` SET
        `tTitle` = '".$jsonTitle."',
        `tSubtitle` = '".$jsonSubtitle."',
        $image_update
        `eStatus` = '".$eStatus."',
        `eUserType` = '".$eUserType."',
        `iDisplayOrder` = '".$iDisplayOrder."'"
    .$where;

    $obj->sql_query($query);*/
    $Data_update['tTitle'] = $jsonTitle;
    $Data_update['tSubtitle'] = $jsonSubtitle;
    $Data_update['eStatus'] = $eStatus;
    $Data_update['eUserType'] = $eUserType;
    $Data_update['iDisplayOrder'] = $iDisplayOrder;
    if ('' !== $id) {
        $where = " `iImageId` = '".$id."'";
        $obj->MySQLQueryPerform($tbl_name, $Data_update, 'update', $where);
    } else {
        $id = $obj->MySQLQueryPerform($tbl_name, $Data_update, 'insert');
    }
    if ('' !== $id) {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
    } else {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INSERT_MSG'];
    }

    $oCache->flushData();
    $GCS_OBJ->updateGCSData();

    header('Location:app_launch_info.php?'.$queryString);

    exit;
}
// $eUserType = "Passenger";
// for Edit
$userEditDataArr = [];
if ('Edit' === $action) {
    $sql = 'SELECT * FROM '.$tbl_name." WHERE iImageId = '".$id."'";
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
        $vImage = $db_data[0]['vImage'];
        $eStatus = $db_data[0]['eStatus'];
        $eUserType = $db_data[0]['eUserType'];
        $iDisplayOrder = $db_data[0]['iDisplayOrder'];
    }
}
$EN_available = $LANG_OBJ->checkLanguageExist();
$db_master = $LANG_OBJ->getLangDataDefaultFirst($db_master);
$temp = 1;
$dataArray = [];
$data_order = $obj->MySQLSelect("SELECT * FROM {$tbl_name} ORDER BY eUserType, iDisplayOrder");
foreach ($data_order as $value) {
    $dataArray[$value['eUserType']][] = $value['iDisplayOrder'];
}
if (!isset($dataArray['Passenger'])) {
    $dataArray['Passenger'] = [];
}
if (!isset($dataArray['Driver'])) {
    $dataArray['Driver'] = [];
}
if (!isset($dataArray['Company'])) {
    $dataArray['Company'] = [];
}
if (!isset($dataArray['General'])) {
    $dataArray['General'] = [];
}
// echo "<pre>"; print_r($dataArray['Passenger']); exit;
?>
<!DOCTYPE html>
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title>Admin | App Launch Images <?php echo $action; ?></title>
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
                    <h2><?php echo $action; ?> App Launch Image</h2>
                    <a href="app_launch_info.php?<?php echo $queryString; ?>">
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
                                    <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?w=400&MAX_HEIGHT=200&src='.$tconfig['tsite_upload_app_launch_images'].$vImage; ?>"
                                         style="width:200px;height:auto;">
                                    <input type="file" class="form-control" name="vImage" id="vImage" value="<?php echo $vImage; ?>"/>
                                <?php } else { ?>
                                    <input type="file" class="form-control" name="vImage" id="vImage" value="<?php echo $vImage; ?>" required/>
                                <?php } ?>
                                <b>[Note: Recommended dimension is 3350 * 2760.]</b>
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
                                    <label>Description</label>
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
                                                Description
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
                                                        <label>Description (<?php echo $vTitle; ?>
                                                            ) <?php echo $required_msg; ?></label>
                                                    </div>
                                                    <div class="<?php echo $page_title_class; ?> desc-block">
                                                        <input type="text" class="form-control subtitle-txt"
                                                               name="<?php echo $descVal; ?>" id="<?php echo $descVal; ?>"
                                                               value="<?php echo ${$descVal}; ?>"
                                                               data-originalvalue="<?php echo ${$descVal}; ?>"
                                                               placeholder="<?php echo $vTitle; ?> Value">
                                                        <div class="pull-left" style="margin-top: 5px">Preferred
                                                            characters 200
                                                        </div>
                                                        <!-- <div class="desc_counter pull-right" style="margin-top: 5px">
                                                            200/200
                                                        </div> -->
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
                        <?php } else { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title</label>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <input type="text" class="form-control" id="tTitle_<?php echo $default_lang; ?>"
                                           name="tTitle_<?php echo $default_lang; ?>"
                                           value="<?php echo $userEditDataArr['tTitle_'.$default_lang]; ?>" required>
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
                        <?php } ?>
                        <div style = "display: none" class="row">
                            <div class="col-lg-12">
                                <label>App Launch Image For</label>
                            </div>
                            <div class="col-lg-6">
                                <select name="eUserType" id="eUserType" class="form-control">
                                    <option value="Passenger" <?php echo ('Passenger' === $eUserType) ? 'selected' : ''; ?>><?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?></option>
                                    <option value="Driver" <?php echo ('Driver' === $eUserType) ? 'selected' : ''; ?>><?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?></option>
                                    <?php if ($MODULES_OBJ->isDeliverAllFeatureAvailable()) { ?>
                                        <option value="Company" <?php echo ('Company' === $eUserType) ? 'selected' : ''; ?>><?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?></option>
                                    <?php } ?>
                                    <option value="General" <?php echo ('General' === $eUserType) ? 'selected' : ''; ?>>General
                                    </option>
                                </select>
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
                                <input type="hidden" name="temp_order" id="temp_order" value="<?php echo $temp; ?>">
                                <select name="iDisplayOrder" id="iDisplayOrder" class="form-control">
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <input type="submit" class="save btn-info" name="submit" id="submit"
                                       value="<?php echo $action; ?> Image">
                                <a href="app_launch_info.php<?php echo ('' !== $sid) ? '?'.$sid : ''; ?>"
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

    $(document).ready(function () {
        $('.subtitle-txt').trigger('keyup');
        $('#eUserType').trigger('change');
    });


    // $(document).on('keyup', '.subtitle-txt', function (e) {
    //     var tval = $(this).val(),
    //         tlength = tval.length,
    //         set = 200,
    //         remain = parseInt(set - tlength);
    //     if (tlength > 0) {
    //         $(this).closest('.desc-block').find('.desc_counter').text(remain + "/200");
    //         if (remain <= 0) {
    //             // $(this).val((tval).substring(0, set));
    //             $(this).closest('.desc-block').find('.desc_counter').text("0/200");
    //             return false;
    //         }
    //     } else {
    //         $(this).closest('.desc-block').find('.desc_counter').text("200/200");
    //         return false;
    //     }
    // });


    $('#eUserType').change(function () {
        var user_type = $(this).val();
        var iDisplayOrder = '<?php echo $iDisplayOrder; ?>';
        var page_action = '<?php echo $action; ?>';

        var DisplayOrderArr = getUserTypeDisplayOrder(user_type);

        var select_html = "";

        for (var i = 0; i < DisplayOrderArr.length; i++) {
            var selected = "";
            if (DisplayOrderArr[i] == iDisplayOrder) {
                selected = "selected";
            }
            select_html += '<option value="' + DisplayOrderArr[i] + '" ' + selected + '>-- ' + DisplayOrderArr[i] + ' --</option>';
        }

        var last = iDisplayOrder;
        if (page_action == "Add") {
            var last = 0;
            if (DisplayOrderArr.length > 0) {
                var last = DisplayOrderArr[DisplayOrderArr.length - 1];
            }

            last = parseInt(last) + 1;
            select_html += '<option value="' + last + '" ' + selected + '>-- ' + last + ' --</option>';
        }
        // console.log(select_html);
        $('#iDisplayOrder').html(select_html);
        $('#temp_order').val(last);
    });

    function getUserTypeDisplayOrder(UserType) {
        var displayOrderArr = '<?php echo json_encode($dataArray); ?>';
        displayOrderArr = JSON.parse(displayOrderArr);
        if (UserType == "Passenger") {
            displayOrderArr = displayOrderArr.Passenger;
        } else if (UserType == "Driver") {
            displayOrderArr = displayOrderArr.Driver;
        } else if (UserType == "Company") {
            displayOrderArr = displayOrderArr.Company;
        } else {
            displayOrderArr = displayOrderArr.General;
        }

        // console.log(displayOrderArr);
        return displayOrderArr;
    }
</script>
</body>
<!-- END BODY-->
</html>