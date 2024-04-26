<?php
include_once '../common.php';
$id = $_REQUEST['id'] ?? '';
$iVehicleCategoryId = $_REQUEST['iVehicleCategoryId'] ?? 0;
$url = "home_content_cubejekx_action.php?id={$id}";
if ('Yes' === $THEME_OBJ->isServiceXThemeActive()) {
    if (!empty($_SERVER['HTTP_REFERER'])) {
        $url = $_SERVER['HTTP_REFERER'];
    } else {
        $url = "home_content_servicex_action.php?id={$id}";
    }
}
$success = $_REQUEST['success'] ?? 0;
$backlink = $_POST['backlink'] ?? '';
$previousLink = '';
$eFor = 'MoreService';
$message_print_id = $id;
$vCode = $_POST['vCode'] ?? '';
$script = 'homecontentotherservices';
$var_msg = $_REQUEST['var_msg'] ?? '';
if (isset($_REQUEST['goback'])) {
    $goback = $_REQUEST['goback'];
}
if (isset($_REQUEST['back_link']) && !empty($_REQUEST['back_link'])) {
    $backlink = $_REQUEST['back_link'];
}
$eStatus_check = $_POST['eStatus'] ?? 'off';
$eStatus = ('on' === $eStatus_check) ? 'Active' : 'Inactive';
$tbl_name = getContentCMSHomeTable();
$sql = "SELECT count(id) as cnt FROM {$tbl_name} WHERE eFor = '".$eFor."' AND `iVehicleCategoryId` = '".$iVehicleCategoryId."'";
$db_efordata = $obj->MySQLSelect($sql);
$action = (!empty($db_efordata[0]['cnt'])) ? 'Edit' : 'Add';
$sql = "SELECT vCode,vTitle FROM language_master WHERE iLanguageMasId = '".$id."'";
$db_data = $obj->MySQLSelect($sql);
$vCode = $db_data[0]['vCode'];
$title = $db_data[0]['vTitle'];
if (empty($db_efordata[0]['cnt'])) {
    if (ENABLE_DYNAMIC_CREATE_PAGE === 'Yes') {
        $idNew = $obj->sql_query("INSERT INTO {$tbl_name}

     (lBannerSection,lHowitworkSection,lSecuresafeSection,lDownloadappSection,lCalltobookSection,lEarnSection,lServiceSection,eFor,iVehicleCategoryId)

     SELECT lBannerSection,lHowitworkSection,lSecuresafeSection,lDownloadappSection,lCalltobookSection,lEarnSection,lServiceSection,eFor,{$iVehicleCategoryId}

     FROM

     {$tbl_name} WHERE

     eFor = '".$eFor."' and iVehicleCategoryId = 0;");
    } else {
        $q_enter = "INSERT INTO {$tbl_name} SET `eFor` = '".$eFor."', `iVehicleCategoryId` = '".$iVehicleCategoryId."'";
        $obj->sql_query($q_enter);
    }
    $db_efordata[0]['cnt'] = 1;
    $action = (!empty($db_efordata[0]['cnt'])) ? 'Edit' : 'Add';
}
$img_arr = $_FILES;
if (!empty($img_arr)) {
    if (SITE_TYPE === 'Demo') {
        header("Location: {$url}&success=2");

        exit;
    }
    foreach ($img_arr as $key => $value) {
        if (!empty($value['name'])) {
            $img_path = $tconfig['tsite_upload_apptype_page_images_panel'];
            $image_object = $value['tmp_name'];
            $img_name = explode('.', $value['name']);
            $image_name = $img_name[0].'_'.strtotime(date('H:i:s')).'.'.$img_name[1];
            if (ENABLE_DYNAMIC_CREATE_PAGE === 'Yes') {
                $img_str = 'img_';
                if ('how_it_work_section_img' === $key) {
                    $key = 'lHowitworkSection';
                } elseif ('banner_section_img' === $key) {
                    $key = 'lBannerSection';
                }
            } else {
                $img_str = 'img_';
                if ('how_it_work_section_img' === $key) {
                    $key = 'lHowitworkSection';
                } elseif ('banner_section_img' === $key) {
                    $key = 'lBannerSection';
                }
            }
            $check_file_query = 'SELECT '.$key." FROM {$tbl_name} where eFor='".$eFor."' AND iVehicleCategoryId = {$iVehicleCategoryId}";
            $check_file = $obj->MySQLSelect($check_file_query);
            $sectionData = json_decode($check_file[0][$key], true);
            if ('' !== $message_print_id && '' !== $sectionData[$img_str.$vCode]) {
                $check_file = $img_path.$template.'/'.$sectionData[$img_str.$vCode];
                if ('' !== $check_file && file_exists($check_file)) {
                    if (ENABLE_DYNAMIC_CREATE_PAGE === 'Yes') {
                    } else {
                        @unlink($check_file); // why unlink removed reason is written in 25-03-2021
                    }
                }
            }
            $Photo_Gallery_folder = $img_path.$template.'/';
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
            }
            $img = $UPLOAD_OBJ->GeneralFileUploadHome($Photo_Gallery_folder, $image_object, $image_name, '', 'png,jpg,jpeg,gif,svg', $vCode);
            if ('1' === $img[2]) {
                $_SESSION['success'] = '0';
                $_SESSION['var_msg'] = $img[1];
                header('location:'.$backlink);
            }
            if (!empty($img[0])) {
                $sectionData[$img_str.$vCode] = $img[0];
                $sectionDatajson = getJsonFromAnArr($sectionData);
                $sql = 'UPDATE '.$tbl_name.' SET '.$key." = '".$sectionDatajson."' WHERE eFor='".$eFor."' AND iVehicleCategoryId = {$iVehicleCategoryId}";
                $obj->sql_query($sql);
            }
        }
    }
}
if (isset($_POST['submit'])) {
    if (ENABLE_DYNAMIC_CREATE_PAGE === 'Yes') {
        $check_file_query = "SELECT lBannerSection, lHowitworkSection FROM {$tbl_name} where eFor='".$eFor."' AND iVehicleCategoryId = {$iVehicleCategoryId}";
    } else {
        $check_file_query = "SELECT lBannerSection, lHowitworkSection FROM {$tbl_name} where eFor='".$eFor."' AND iVehicleCategoryId = {$iVehicleCategoryId}";
    }
    $check_file = $obj->MySQLSelect($check_file_query);
    $sectionData = json_decode($check_file[0]['lBannerSection'], true);
    $banner_section_arr['title_'.$vCode] = $_POST['banner_section_title'] ?? '';
    $banner_section_arr['desc_'.$vCode] = $_POST['banner_section_desc'] ?? '';
    $banner_section_arr['img_'.$vCode] = $sectionData['img_'.$vCode] ?? '';
    $banner_section_arr = !(empty($sectionData)) ? array_merge($sectionData, $banner_section_arr) : $banner_section_arr;
    $banner_section = getJsonFromAnArrWithoutClean($banner_section_arr); // addslashes because double quotes stored after slashes so while getting data no problem
    $sectionData = json_decode($check_file[0]['lHowitworkSection'], true);
    $how_it_work_section_arr['menu_title_'.$vCode] = $_POST['how_it_work_section_menu_title'] ?? '';
    $how_it_work_section_arr['title_'.$vCode] = $_POST['how_it_work_section_title'] ?? '';
    $how_it_work_section_arr['desc_'.$vCode] = $_POST['how_it_work_section_desc'] ?? '';
    $how_it_work_section_arr['img_'.$vCode] = $sectionData['img_'.$vCode] ?? '';
    $how_it_work_section_arr = !(empty($sectionData)) ? array_merge($sectionData, $how_it_work_section_arr) : $how_it_work_section_arr;
    $how_it_work_section = getJsonFromAnArrWithoutClean($how_it_work_section_arr);
}
if (isset($_POST['submit'])) {
    if (SITE_TYPE === 'Demo') {
        header("Location: {$url}&success=2");

        exit;
    }
    $update_array = [];
    $update_array['lBannerSection'] = $banner_section;
    $update_array['lHowitworkSection'] = $how_it_work_section;
    $update_array['eFor'] = $eFor;
    $where = "`eFor` = '".$eFor."' AND `iVehicleCategoryId` = ".$iVehicleCategoryId.'';
    $dataResult = $obj->MySQLSelect("select iVehicleCategoryId FROM {$tbl_name} WHERE {$where}");
    if (!empty($dataResult[0])) {
        $obj->MySQLQueryPerform($tbl_name, $update_array, 'update', $where);
    } else {
        $update_array['iVehicleCategoryId'] = $iVehicleCategoryId;
        $obj->MySQLQueryPerform($tbl_name, $update_array, 'insert');
    }

    $configUpdateData = [];
    if (isset($_POST['ANDROID_APP_LINK']) && !empty($_POST['ANDROID_APP_LINK'])) {
        $configUpdateData = [];
        $configUpdateData['vValue'] = $_POST['ANDROID_APP_LINK'];
        $where = "vName = 'ANDROID_APP_LINK'";
        $obj->MySQLQueryPerform('configurations', $configUpdateData, 'update', $where);
    }
    if (isset($_POST['IPHONE_APP_LINK']) && !empty($_POST['IPHONE_APP_LINK'])) {
        $configUpdateData = [];
        $configUpdateData['vValue'] = $_POST['IPHONE_APP_LINK'];
        $where = "vName = 'IPHONE_APP_LINK'";
        $obj->MySQLQueryPerform('configurations', $configUpdateData, 'update', $where);
    }

    if ('Add' === $action) {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INSERT_MSG'];
    } else {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
    }
    header('location:'.$backlink);

    exit;
}
// for Edit
if ('Edit' === $action) {
    $sql = "SELECT * FROM {$tbl_name} WHERE eFor = '".$eFor."' AND iVehicleCategoryId = {$iVehicleCategoryId}";
    $db_data = $obj->MySQLSelect($sql);
    if (count($db_data) > 0) {
        foreach ($db_data as $key => $value) {
            $banner_section = json_decode($value['lBannerSection'], true);
            $how_it_work_section = (array) json_decode($value['lHowitworkSection']);
        }
    }
}
?>
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title>Admin | Other Services Home Content <?php echo $action; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet"/>
    <?php include_once 'global_files.php'; ?>
    <!-- On OFF switch -->
    <link href="../assets/css/jquery-ui.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css"/>
    <style>
        .body-div.innersection {

            box-shadow: -1px -2px 73px 2px #dedede;

            float: none;

        }

        .innerbg_image {

            width: auto;

            margin: 10px 0;

            height: 150px;

        }

        .notes {

            font-weight: 700;

            font-style: italic;

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
                    <h2><?php echo $action; ?> Other Services Home Content (<?php echo $title; ?>)</h2>
                    <a href="<?php echo $url; ?>" class="back_link">
                        <input type="button" value="Back to Listing" class="add-btn">
                    </a>
                </div>
            </div>
            <?php include 'valid_msg.php'; ?>
            <hr/>
            <div class="body-div">
                <div class="form-group">
                    <?php if (1 === $success) { ?>
                        <div class="alert alert-success alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <?php echo $langage_lbl_admin['LBL_Record_Updated_successfully']; ?>
                        </div>
                        <br/>
                    <?php } elseif (2 === $success) { ?>
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <?php echo $langage_lbl_admin['LBL_EDIT_DELETE_RECORD']; ?>
                        </div>
                        <br/>
                    <?php } ?>
                    <form method="post" name="_home_content_form" id="_home_content_form" action=""
                          enctype='multipart/form-data'>
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="hidden" name="vCode" value="<?php echo $vCode; ?>">
                        <input type="hidden" name="previousLink" id="previousLink"
                               value="<?php echo $previousLink; ?>"/>
                        <input type="hidden" name="backlink" id="backlink" value="<?php echo $url; ?>"/>
                        <div class="body-div innersection">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h3>Banner section</h3>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Title
                                            <span class="red"> *</span>
                                        </label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="banner_section_title"
                                               id="banner_section_title"
                                               value="<?php echo $banner_section['title_'.$vCode]; ?>" placeholder="Title">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Description</label>
                                    </div>
                                    <div class="col-lg-12">

                                        <textarea class="form-control ckeditor" rows="10" name="banner_section_desc"
                                                  id="banner_section_desc"
                                                  placeholder="Description"><?php echo $banner_section['desc_'.$vCode]; ?></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Background Image</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <?php if ('' !== $banner_section['img_'.$vCode]) { ?>
                                            <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=300&src='.$tconfig['tsite_upload_apptype_page_images'].$template.'/'.$banner_section['img_'.$vCode]; ?>"
                                                 class="innerbg_image"/>
                                        <?php } ?>
                                        <input type="file" class="form-control FilUploader" name="banner_section_img"
                                               id="banner_section_img" accept=".png,.jpg,.jpeg,.gif,.svg">
                                        <br/>
                                        <span class="notes">[Note: For Better Resolution Upload only image size of 1900px * 605px.]</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="body-div innersection">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h3>How It work section</h3>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Menu Title</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="how_it_work_section_menu_title"
                                               id="how_it_work_section_menu_title"
                                               value="<?php echo $how_it_work_section['menu_title_'.$vCode]; ?>"
                                               placeholder="Menu Title">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Title
                                            <span class="red"> *</span>
                                        </label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="how_it_work_section_title"
                                               id="how_it_work_section_title"
                                               value="<?php echo $how_it_work_section['title_'.$vCode]; ?>"
                                               placeholder="Title">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Description</label>
                                    </div>
                                    <div class="col-lg-12">

                                        <textarea class="form-control ckeditor" rows="10"
                                                  name="how_it_work_section_desc" id="how_it_work_section_desc"
                                                  placeholder="Description"><?php echo $how_it_work_section['desc_'.$vCode]; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!----------------------download section------------------------->

                        <div class="body-div innersection download-section">
                            <div class="form-group">
                                <div class="row ">
                                    <div class="col-lg-12 download-section-header">
                                        <h3>Download Section</h3>
                                        <p>Data will be changed in all other pages as this is a common section.
                                        </p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="col-lg-12">
                                            <label>Title
                                                <span class="red"> *</span>
                                            </label>
                                        </div>
                                        <div class="col-lg-12 Download_section_title">
                                            <input readonly type="text" class="form-control"
                                                   name="Download_section_title"
                                                   id="Download_section_title"
                                                   value="<?php echo $langage_lbl_admin['LBL_DOWNLOAD_ANDROID_IOS_APPS_TXT']; ?>"
                                                   placeholder="Title">

                                            <button onClick="languageLabel('LBL_DOWNLOAD_ANDROID_IOS_APPS_TXT')"
                                                    type="button" class="btn btn-info" data-toggle="tooltip"
                                                    data-original-title="Edit" onclick="editCategoryName('Edit')">
                                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                            </button>
                                        </div>

                                    </div>
                                    <div class="col-lg-4">
                                        <div class="col-lg-12">
                                            <label>Iphone App Link
                                                <span class="red"> *</span>
                                            </label>
                                        </div>
                                        <div class="col-lg-12">
                                            <input type="text" class="form-control" name="IPHONE_APP_LINK"
                                                   id="IPHONE_APP_LINK"
                                                   value="<?php echo $IPHONE_APP_LINK; ?>"
                                                   placeholder="Title" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="col-lg-12">
                                            <label>Android App Link
                                                <span class="red"> *</span>
                                            </label>
                                        </div>
                                        <div class="col-lg-12">
                                            <input type="text" class="form-control" name="ANDROID_APP_LINK"
                                                   id="ANDROID_APP_LINK"
                                                   value="<?php echo $ANDROID_APP_LINK; ?>"
                                                   placeholder="Title" required/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!----------------------download section------------------------->
                        <div class="row">
                            <div class="col-lg-12">
                                <input type="submit" class=" btn btn-default" name="submit" id="submit"
                                       value="<?php echo $action; ?> Home Content">
                                <input type="reset" value="Reset" class="btn btn-default">
                                <a href="<?php echo $url; ?>" class="btn btn-default back_link">Cancel</a>
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
<?php include_once 'footer.php'; ?>
<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
<script src="../assets/plugins/ckeditor/ckeditor.js"></script>
<script src="../assets/plugins/ckeditor/config.js"></script>
<script>
    CKEDITOR.replace('ckeditor', {
        allowedContent: {
            i: {
                classes: 'fa*'
            },
            span: true
        }
    });
</script>
<script
        $(document).ready(function () {
        var referrer;
    <?php if (1 === $goback) { ?>
        alert(
'<?php echo $var_msg; ?>');
window.location.href = "<?php echo $url; ?>";
<?php } ?>
if ($("#previousLink").val() == "") { //alert('pre1');
referrer = document.referrer;
} else {
referrer = $("#previousLink").val();
}
if (referrer == "") {
referrer = "<?php echo $url; ?>";
} else {
referrer = "<?php echo $url; ?>";
}
$(".back_link").attr('href', referrer);
});
/**
* This will reset the CKEDITOR using the input[type=reset] clicks.
*/
$(function () {
if (typeof CKEDITOR != 'undefined') {
$('form').on('reset', function (e) {
if ($(CKEDITOR.instances).length) {
for (var key in CKEDITOR.instances) {
var instance = CKEDITOR.instances[key];
if ($(instance.element.$).closest('form').attr('name') == $(e.target).attr('name')) {
instance.setData(instance.element.$.defaultValue);
}
}
}
});
}
});
$(".FilUploader").change(function () {
var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'svg'];
if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
alert("Only formats are allowed : " + fileExtension.join(', '));
$(this).val('');
return false;
}
});
function deleteIcon(ele) {
var id = $(ele).attr('data-id');
$('#removeidmodel').val(id);
$('#service_icon_modal').modal('show');
return false;
}
$(".action_modal_submit").unbind().click(function () {
var id = $('#removeidmodel').val();
$('#removeidmodel').val('');
$('#removeIconFrom_' + id).click();
return true;
});
</script>
</body>
<!-- END BODY-->
</html>

