<?php
include_once '../common.php';
$id = $_REQUEST['id'] ?? ''; // iUniqueId
$parentid = $_REQUEST['parentid'] ?? 0;
if (!$userObj->hasPermission('edit-bidding-category')) {
    $userObj->redirect();
}
$default_lang = $LANG_OBJ->FetchSystemDefaultLang();
$success = $_REQUEST['success'] ?? '';
$action = ('' !== $id) ? 'Edit' : 'Add';
$script = 'bidding';
$tbl_name = 'bidding_service';
$db_master = $obj->MySQLSelect('SELECT * FROM `language_master` ORDER BY `iDispOrder`');
$count_all = count($db_master);
$vCategoryName = $_POST['vCategoryName'] ?? '';
$eStatus = $_POST['eStatus'] ?? 'Active';
$fCommission = $_POST['fCommission'] ?? '0';
$iDisplayOrder = $_POST['iDisplayOrder'] ?? '';
$thumb = new thumbnail();
if (isset($_POST['submit'])) { // form submit
    if (SITE_TYPE === 'Demo') {
        $_SESSION['success'] = 2;
        header('Location:bidding_service_category.php');

        exit;
    }
    $i = $iDisplayOrder;
    $temp_order = $_REQUEST['oldDisplayOrder'];
    if ($temp_order > $iDisplayOrder) {
        for ($i = $temp_order - 1; $i >= $iDisplayOrder; --$i) {
            $obj->sql_query("UPDATE bidding_service SET iDisplayOrder = '".($i + 1)."' WHERE iDisplayOrder = '".$i."' AND iParentId = '{$parentid}'");
        }
    } elseif ($temp_order < $iDisplayOrder) {
        for ($i = $temp_order + 1; $i <= $iDisplayOrder; ++$i) {
            $obj->sql_query("UPDATE bidding_service SET iDisplayOrder = '".($i - 1)."' WHERE iDisplayOrder = '".$i."' AND iParentId = '{$parentid}'");
            $obj->sql_query($sql1);
        }
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
            $var_msg = 'Not valid image extension of .jpg, .jpeg, .gif, .png, .bmp';
        }
        $image_info = getimagesize($_FILES['vImage']['tmp_name']);
        $image_width = $image_info[0];
        $image_height = $image_info[1];

        if (1 === $flag_error) {
            $_SESSION['success'] = '3';
            $_SESSION['var_msg'] = $var_msg;
            header('Location:bidding_service_category.php');

            exit;
        }
        $Photo_Gallery_folder = $tconfig['tsite_upload_images_bidding_path'];
        if (!is_dir($Photo_Gallery_folder)) {
            mkdir($Photo_Gallery_folder, 0777);
            chmod($Photo_Gallery_folder, 0777);
        }
        $img = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, '', 'jpg,png,gif,jpeg,bmp');
        $vImage = $img[0];
        //  $image_update .= "vIconImage = '" . $vImage . "',";
        $query_p['vImage'] = $vImage;
        if (!empty($_POST['vImage_old']) && file_exists($Photo_Gallery_folder.$_POST['vImage_old'])) {
            unlink($Photo_Gallery_folder.$_POST['vImage_old']);
        }
    }
    $image_object1 = $_FILES['vImage1']['tmp_name'];
    $image_name1 = $_FILES['vImage1']['name'];
    if ('' !== $image_name1) {
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
            header('Location:bidding_service_category.php');

            exit;
        }
        $Photo_Gallery_folder = $tconfig['tsite_upload_images_bidding_path'];
        if (!is_dir($Photo_Gallery_folder)) {
            mkdir($Photo_Gallery_folder, 0777);
            chmod($Photo_Gallery_folder, 0777);
        }
        sleep(2);
        $img1 = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object1, $image_name1, '', 'jpg,png,gif,jpeg,bmp');
        $vImage1 = $img1[0];
        $query_p['vImage1'] = $vImage1;
        if (!empty($_POST['vImage1_old']) && file_exists($Photo_Gallery_folder.$_POST['vImage1_old']) && SITE_TYPE !== 'Demo') {
            unlink($Photo_Gallery_folder.$_POST['vImage_old']);
        }
    }
    for ($i = 0; $i < count($db_master); ++$i) {
        $vCategoryName = '';
        if (isset($_POST['vTitle_'.$db_master[$i]['vCode']])) {
            $vCategoryName = $_POST['vTitle_'.$db_master[$i]['vCode']];
        }
        $vCategoryNameArr['vTitle_'.$db_master[$i]['vCode']] = $vCategoryName;
    }
    $jsonCategoryName = getJsonFromAnArr($vCategoryNameArr);
    for ($i = 0; $i < count($db_master); ++$i) {
        $tDescription = '';
        if (isset($_POST['tDescription_'.$db_master[$i]['vCode']])) {
            $tDescription = $_POST['tDescription_'.$db_master[$i]['vCode']];
        }
        $tDescriptionArr['tDescription_'.$db_master[$i]['vCode']] = $tDescription;
    }
    $jsonDescription = getJsonFromAnArr($tDescriptionArr);
    $query_p['iParentId'] = $_POST['iParentId'];
    $query_p['vTitle'] = $jsonCategoryName;
    $query_p['tDescription'] = $jsonDescription;
    $query_p['eStatus'] = $eStatus;
    $query_p['iDisplayOrder'] = $iDisplayOrder;
    $query_p['fCommission'] = $fCommission;
    if ('' !== $id) {
        $where = " iBiddingId = '{$id}'";
        $obj->MySQLQueryPerform($tbl_name, $query_p, 'update', $where);
    } else {
        $obj->MySQLQueryPerform($tbl_name, $query_p, 'insert');
    }
    // $obj->sql_query($query);
    if ('' !== $id) {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
    } else {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INSERT_MSG'];
    }
    header("Location:bidding_master_category.php?parentid={$parentid}");

    exit;
}
// for Edit
$userEditDataArr = [];
$vDescriptionArr = [];
if ('Edit' === $action) {
    $bidding = $BIDDING_OBJ->getbidding('admin', $id);
    $vCategoryName = json_decode($bidding['vTitle_json'], true);
    foreach ($vCategoryName as $key => $value) {
        $userEditDataArr[$key] = $value;
    }
    $vDescription = json_decode($bidding['tDescription_json'], true);
    foreach ($vDescription as $key => $value) {
        $vDescriptionArr[$key] = $value;
    }
    $parentid = $bidding['iParentId'];
    $vIconImage = $bidding['vImage'];
    $vIconImage1 = $bidding['vImage1'];
    $eStatus = $bidding['eStatus'];
    $iDisplayOrder = $bidding['iDisplayOrder'];
    $fCommission = $bidding['fCommission'];
}
$bidding = $BIDDING_OBJ->getBiddingMaster('admin');
$EN_available = $LANG_OBJ->checkLanguageExist();
$db_master = $LANG_OBJ->getLangDataDefaultFirst($db_master);
$maxDisplayOrderData = $obj->MySQLSelect("SELECT max(iDisplayOrder) as maxDisplayOrder FROM {$tbl_name} WHERE iParentId = '{$parentid}'");
$maxDisplayOrder = $maxDisplayOrderData[0]['maxDisplayOrder'];
if ('Add' === $action) {
    ++$maxDisplayOrder;
    $iDisplayOrder = $maxDisplayOrder;
}
?>
<!DOCTYPE html>
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD-->

<head>
    <meta charset="UTF-8"/>
    <title>Admin | Bidding Service <?php echo $action; ?></title>
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
                    <h2><?php echo $action; ?> Bidding Service</h2>
                    <a href="bidding_master_category.php?parentid=<?php echo $parentid; ?>">
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
                    <form method="post" action="" enctype="multipart/form-data" id="bid_category_form">
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="hidden" name="parentid" value="<?php echo $parentid; ?>"/>

                        <?php if ($parentid > 0) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Parent category</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <select name="iParentId" class="form-control">
                                        <option value="">--Select--</option>
                                        <?php for ($i = 0; $i <= count($bidding) - 1; ++$i) {
                                            if ($bidding[$i]['iBiddingId'] === $BIDDING_OBJ->other_id) {
                                                continue;
                                            }
                                            ?>


                                            <option value="<?php echo $bidding[$i]['iBiddingId']; ?>" <?php echo $bidding[$i]['iBiddingId'] === $parentid ? 'selected' : ''; ?>>
                                                <?php echo $bidding[$i]['vTitle']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <input type="hidden" name="oldDisplayOrder" id="oldDisplayOrder"
                                           value="<?php echo $iDisplayOrder; ?>">
                                </div>
                            </div>
                        <?php } ?>

                        <?php if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV3() && 0 === $parentid) { ?>
                        <div class="row">
                            <input type="hidden" name="vImage1_old" value="<?php echo $vIconImage1; ?>">
                            <div class="col-lg-12">
                                <label>Icon
                                    <?php echo ('' === $vIconImage1) ? '<span class="red"> *</span>' : ''; ?></label>
                            </div>
                            <div class="col-lg-6">
                                <?php if ('' !== $vIconImage1) { ?>
                                    <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?w=400&src='.$tconfig['tsite_upload_images_bidding'].$vIconImage1; ?>"
                                         style="height:150px">
                                    <input type="file" class="form-control" name="vImage1" id="vImage1" value=""/>
                                <?php } else { ?>
                                    <input type="file" class="form-control" name="vImage1" id="vImage1" value="" required/>
                                <?php } ?>
                                <br>
                                <div>Note: Upload only png image size of 360px X 360px.</div>
                            </div>
                        </div>
                        <?php } else { ?>
                        <div class="row">
                            <input type="hidden" name="vImage_old" value="<?php echo $vIconImage; ?>">
                            <div class="col-lg-12">
                                <label>Icon
                                    <?php echo ('' === $vIconImage) ? '<span class="red"> *</span>' : ''; ?></label>
                            </div>
                            <div class="col-lg-6">
                                <?php if ('' !== $vIconImage) { ?>
                                    <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?w=400&src='.$tconfig['tsite_upload_images_bidding'].$vIconImage; ?>"
                                         style="height:150px">
                                    <input type="file" class="form-control" name="vImage" id="vImage" value=""/>
                                <?php } else { ?>
                                    <input type="file" class="form-control" name="vImage" id="vImage" value="" required/>
                                <?php } ?>
                                <br>
                                <div>Note: Upload only png image size of 360px X 360px.</div>
                            </div>
                        </div>
                        <?php } ?>

                        <?php if (count($db_master) > 1) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Title</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control <?php echo ('' === $id) ? 'readonly-custom' : ''; ?>"
                                           id="vTitle_Default" name="vTitle_Default"
                                           value="<?php echo $userEditDataArr['vTitle_'.$default_lang]; ?>"
                                           data-originalvalue="<?php echo $userEditDataArr['vTitle_'.$default_lang]; ?>"
                                           readonly="readonly"
                                           required <?php if ('' === $id) { ?> onclick="editCategoryName('Add')" <?php } ?>>
                                </div>
                                <?php if ('' !== $id) { ?>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                data-original-title="Edit" onclick="editCategoryName('Edit')"><span
                                                    class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="modal fade" id="Category_Modal" tabindex="-1" role="dialog" aria-hidden="true"
                                 data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="modal_action"></span> Title
                                                <button type="button" class="close" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vTitle_')">x
                                                </button>
                                            </h4>
                                        </div>

                                        <div class="modal-body">
                                            <?php
                                            for ($i = 0; $i < $count_all; ++$i) {
                                                $vCode = $db_master[$i]['vCode'];
                                                $vTitle = $db_master[$i]['vTitle'];
                                                $eDefault = $db_master[$i]['eDefault'];
                                                $vValue = 'vTitle_'.$vCode;
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
                                                             style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    if (count($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ('EN' === $vCode) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vTitle_', 'EN');">
                                                                        Convert To All
                                                                        Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                                            } else {
                                                                if ($vCode === $default_lang) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('vTitle_', '<?php echo $default_lang; ?>');">
                                                                        Convert
                                                                        To All Language
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
                                                </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?>
                                            </h5>
                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                        onclick="saveCategoryName()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'vTitle_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
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
                                    <input type="text" class="form-control" id="vTitle_<?php echo $default_lang; ?>"
                                           name="vTitle_<?php echo $default_lang; ?>"
                                           value="<?php echo $userEditDataArr['vTitle_'.$default_lang]; ?>">
                                </div>
                            </div>
                        <?php } ?>



                        <?php if (count($db_master) > 1) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Description</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control <?php echo ('' === $id) ? 'readonly-custom' : ''; ?>"
                                           id="tDescription_Default" name="tDescription_Default"
                                           value="<?php echo $vDescriptionArr['tDescription_'.$default_lang]; ?>"
                                           data-originalvalue="<?php echo $userEditDataArr['tDescription_'.$default_lang]; ?>"
                                           readonly="readonly" <?php if ('' === $id) { ?> onclick="editDescription('Add')" <?php } ?>>
                                </div>
                                <?php if ('' !== $id) { ?>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip"
                                                data-original-title="Edit" onclick="editDescription('Edit')"><span
                                                    class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
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
                                                <span id="tDescriptionmodal_action"></span> Description
                                                <button type="button" class="close" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'tDescription_')">x
                                                </button>
                                            </h4>
                                        </div>

                                        <div class="modal-body">
                                            <?php
                            for ($i = 0; $i < $count_all; ++$i) {
                                $vCode = $db_master[$i]['vCode'];
                                $vTitle = $db_master[$i]['vTitle'];
                                $eDefault = $db_master[$i]['eDefault'];
                                $vValue = 'tDescription_'.$vCode;
                                ${$vValue} = $vDescriptionArr[$vValue];
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
                                                    <div class="<?php echo $page_title_class; ?>">
                                                        <input type="text" class="form-control" name="<?php echo $vValue; ?>"
                                                               id="<?php echo $vValue; ?>" value="<?php echo ${$vValue}; ?>"
                                                               data-originalvalue="<?php echo ${$vValue}; ?>"
                                                               placeholder="<?php echo $vTitle; ?> Value">
                                                        <div class="text-danger" id="<?php echo $vValue.'_error'; ?>"
                                                             style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                    if (count($db_master) > 1) {
                                        if ($EN_available) {
                                            if ('EN' === $vCode) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('tDescription_', 'EN');">
                                                                        Convert To
                                                                        All Language
                                                                    </button>
                                                                </div>
                                                            <?php }
                                            } else {
                                                if ($vCode === $default_lang) { ?>
                                                                <div class="col-md-3 col-sm-3">
                                                                    <button type="button" name="allLanguage"
                                                                            id="allLanguage" class="btn btn-primary"
                                                                            onClick="getAllLanguageCode('tDescription_', '<?php echo $default_lang; ?>');">
                                                                        Convert
                                                                        To All Language
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
                                                </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?>
                                            </h5>
                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                <button type="button" class="save" style="margin-left: 0 !important"
                                                        onclick="saveDescription()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal"
                                                        onclick="resetToOriginalValue(this, 'tDescription_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                            </div>
                                        </div>

                                        <div style="clear:both;"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Description</label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="tDescription_<?php echo $default_lang; ?>"
                                           name="tDescription_<?php echo $default_lang; ?>"
                                           value="<?php echo $userEditDataArr['tDescription_'.$default_lang]; ?>">
                                </div>
                            </div>
                        <?php } ?>

                        <?php if (0 === $parentid) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Commission (%) <span class="red">*</span> <i class="icon-question-sign"
                                                                                        data-placement="top"
                                                                                        data-toggle="tooltip"
                                                                                        data-original-title="Enter Commission percentage you want to earn from this service"></i></label>
                                </div>
                                <div class="col-md-4 col-sm-4">
                                    <input type="text" class="form-control" id="fCommission" name="fCommission"
                                           value="<?php echo $fCommission; ?>">
                                    <b>Note :</b> Commission will be deducted
                                    from <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?>.
                                </div>
                            </div>
                        <?php } ?>

                        <div class="row">
                            <div class="col-lg-12">
                                <label>Display Order</label>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <select name="iDisplayOrder" class="form-control">
                                    <?php for ($i = 1; $i <= $maxDisplayOrder; ++$i) { ?>
                                        <option value="<?php echo $i; ?>" <?php echo $iDisplayOrder === $i ? 'selected' : ''; ?>>
                                            <?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                                <input type="hidden" name="oldDisplayOrder" id="oldDisplayOrder"
                                       value="<?php echo $iDisplayOrder; ?>">
                            </div>
                        </div>


                        <?php if ($id !== $BIDDING_OBJ->other_id) { ?>


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
                                <input type="submit" class="save btn-info" name="submit" id="submit"
                                       value="<?php echo $action.' '.(0 === $parentid ? 'Master Bidding Service' : 'Bidding Service'); ?>"
                                       style="margin-right: 10px">
                                <a href="bidding_master_category.php?parentid=<?php echo $parentid; ?>"
                                   class="btn btn-default back_link">Cancel</a>
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
        if ($('#vTitle_<?php echo $default_lang; ?>').val() == "") {
            $('#vTitle_<?php echo $default_lang; ?>_error').show();
            $('#vTitle_<?php echo $default_lang; ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#vTitle_<?php echo $default_lang; ?>_error').hide();
            }, 5000);
            return false;
        }
        $('#vTitle_Default').val($('#vTitle_<?php echo $default_lang; ?>').val());
        $('#vTitle_Default').closest('.row').removeClass('has-error');
        $('#vTitle_Default-error').remove();
        $('#Category_Modal').modal('hide');
    }

    function editDescription(action) {
        $('#tDescriptionmodal_action').html(action);
        $('#tDescription_Modal').modal('show');
    }

    function saveDescription() {
        if ($('#tDescription_<?php echo $default_lang; ?>').val() == "") {
            $('#tDescription_<?php echo $default_lang; ?>_error').show();
            $('#tDescription_<?php echo $default_lang; ?>').focus();
            clearInterval(langVar);
            langVar = setTimeout(function () {
                $('#tDescription_<?php echo $default_lang; ?>_error').hide();
            }, 5000);
            return false;
        }
        $('#tDescription_Default').val($('#tDescription_<?php echo $default_lang; ?>').val());
        $('#tDescription_Default').closest('.row').removeClass('has-error');
        $('#tDescription_Default-error').remove();
        $('#tDescription_Modal').modal('hide');
    }

    $('#iListMaxCount').keyup(function (e) {
        if (/\D/g.test(this.value)) {
            this.value = this.value.replace(/\D/g, '');
        }
    });
    $(document).ready(function () {
        $('#bid_category_form').validate({
            rules: {
                fCommission: {
                    required: true,
                    number: true
                }
            },
        });
    });
</script>
</body>
<!-- END BODY-->

</html>