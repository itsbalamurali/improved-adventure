<?php
include_once '../common.php';

$id = $_REQUEST['id'] ?? '';
if ('Yes' === $THEME_OBJ->isProThemeActive()) {
    header("location:business_content_action.php?id={$id}");

    exit;
}

$url = "home_content_cubejekx_action.php?id={$id}";
if ('Yes' === $THEME_OBJ->isCubexThemeActive() || 'Yes' === $THEME_OBJ->isCubeXv2ThemeActive()) {
    // $url = "home_content_business.php";
    $url = "home_content_cubex_action.php?id={$id}";
}

$success = $_REQUEST['success'] ?? 0;
// $action = ($id != '') ? 'Edit' : 'Add';
$backlink = $_POST['backlink'] ?? '';
$previousLink = '';
$eFor = 'Business';

$message_print_id = $id;
$vCode = $_POST['vCode'] ?? '';
$script = 'homecontentbusiness';
$var_msg = $_REQUEST['var_msg'] ?? '';

if (isset($_REQUEST['goback'])) {
    $goback = $_REQUEST['goback'];
}

$eStatus_check = $_POST['eStatus'] ?? 'off';
$eStatus = ('on' === $eStatus_check) ? 'Active' : 'Inactive';

// $tbl_name = 'content_cubex_details';
$tbl_name = getContentCMSHomeTable();

$sql = "SELECT count(id) as cnt FROM {$tbl_name} WHERE eFor = '".$eFor."'";
$db_efordata = $obj->MySQLSelect($sql);
$action = (!empty($db_efordata[0]['cnt'])) ? 'Edit' : 'Add';

if (empty($db_efordata[0]['cnt'])) {
    $q_enter = "INSERT INTO {$tbl_name} SET `eFor` = '".$eFor."'";
    $obj->sql_query($q_enter);
    $db_efordata[0]['cnt'] = 1;
}

$sql = "SELECT vCode,vTitle FROM language_master WHERE iLanguageMasId = '".$id."'";
$db_data = $obj->MySQLSelect($sql);
$vCode = $db_data[0]['vCode'];
$title = $db_data[0]['vTitle'];

$img_arr = $_FILES;

if (!empty($img_arr)) {
    if (SITE_TYPE === 'Demo') {
        header("Location: {$url}&success=2");

        exit;
    }
    foreach ($img_arr as $key => $value) {
        if (!empty($value['name'])) {
            $img_path = $tconfig['tsite_upload_apptype_page_images_panel'];
            // $temp_gallery = $img_path . '/';
            $image_object = $value['tmp_name'];
            $img_name = explode('.', $value['name']);
            $image_name = $img_name[0].'_'.strtotime(date('H:i:s')).'.'.$img_name[1];

            if ('download_section_img2' === $key) {
                $second_down_img = 1;
            } else {
                $second_down_img = 0;
            }

            if ('benefit_section_img_first' === $key || 'service_section_img_first' === $key) {
                $img_str = 'img_first_';
            } elseif ('benefit_section_img_sec' === $key || 'service_section_img_sec' === $key) {
                $img_str = 'img_sec_';
            } elseif ('benefit_section_img_third' === $key || 'service_section_img_third' === $key) {
                $img_str = 'img_third_';
            } elseif ('benefit_section_img_fourth' === $key || 'service_section_img_fourth' === $key) {
                $img_str = 'img_fourth_';
            } elseif ('benefit_section_img_fifth' === $key || 'service_section_img_fifth' === $key) {
                $img_str = 'img_fifth_';
            } elseif ('benefit_section_img_six' === $key || 'service_section_img_six' === $key) {
                $img_str = 'img_six_';
            } elseif ('benefit_section_img_seven' === $key || 'service_section_img_seven' === $key) {
                $img_str = 'img_seven_';
            } elseif ('benefit_section_img_eight' === $key || 'service_section_img_eight' === $key) {
                $img_str = 'img_eight_';
            } else {
                $img_str = 'img_';
            }

            if ('download_section_img' === $key) {
                $key = 'lDownloadappSection';
            } elseif ('download_section_img2' === $key) {
                $key = 'lDownloadappSection';
            } elseif ('secure_section_img' === $key) {
                $key = 'lSecuresafeSection';
            } elseif ('earn_section_img' === $key) {
                $key = 'lEarnSection';
            } elseif ('banner_section_img' === $key) {
                $key = 'lBannerSection';
            } elseif ('how_it_work_section_img' === $key) {
                $key = 'lHowitworkSection';
            } elseif ('benefit_section_img' === $key || 'benefit_section_img_first' === $key || 'benefit_section_img_sec' === $key || 'benefit_section_img_third' === $key || 'benefit_section_img_fourth' === $key || 'benefit_section_img_fifth' === $key || 'benefit_section_img_six' === $key || 'benefit_section_img_seven' === $key || 'benefit_section_img_eight' === $key) {
                $key = 'lBenefitSection';
            } elseif ('service_section_img' === $key || 'service_section_img_first' === $key || 'service_section_img_sec' === $key || 'service_section_img_third' === $key || 'service_section_img_fourth' === $key || 'service_section_img_fifth' === $key || 'service_section_img_six' === $key || 'service_section_img_seven' === $key || 'service_section_img_eight' === $key) {
                $key = 'lServiceSection';
            }

            // For How it works Added By PJ
            for ($i = 1; $i <= 6; ++$i) {
                if ($key === 'how_it_work_section_hiw_img'.$i) {
                    $key = 'lHowitworkSection';
                    $img_str = 'hiw_img'.$i.'_';
                }
            }

            $check_file_query = 'SELECT '.$key." FROM {$tbl_name} where eFor='".$eFor."'";
            $check_file = $obj->MySQLSelect($check_file_query);
            $sectionData = json_decode($check_file[0][$key], true);

            if (1 === $second_down_img) {
                if ('' !== $message_print_id && '' !== $sectionData['img2_'.$vCode]) {
                    $check_file = $img_path.'/'.$template.'/'.$sectionData['img2_'.$vCode];
                    if ('' !== $check_file && file_exists($check_file)) {
                        @unlink($check_file);
                    }
                }
            } else {
                if ('' !== $message_print_id && '' !== $sectionData[$img_str.$vCode]) {
                    $check_file = $img_path.'/'.$template.'/'.$sectionData[$img_str.$vCode];
                    if ('' !== $check_file && file_exists($check_file)) {
                        @unlink($check_file);
                    }
                }
            }

            $Photo_Gallery_folder = $img_path.'/'.$template.'/';
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
            }

            $img = $UPLOAD_OBJ->GeneralFileUploadHome($Photo_Gallery_folder, $image_object, $image_name, '', 'png,jpg,jpeg,gif,svg', $vCode);

            if ('1' === $img[2]) {
                $_SESSION['success'] = '0';
                $_SESSION['var_msg'] = $img[1];
                header('location:'.$backlink);
            }

            if (1 === $second_down_img) {
                if (!empty($img[0])) {
                    $sectionData['img2_'.$vCode] = $img[0];
                    $sectionDatajson = getJsonFromAnArr($sectionData);
                    $sql = 'UPDATE '.$tbl_name.' SET '.$key." = '".$sectionDatajson."' WHERE eFor='".$eFor."'";
                    $obj->sql_query($sql);
                }
            } else {
                if (!empty($img[0])) {
                    $sectionData[$img_str.$vCode] = $img[0];
                    $sectionDatajson = getJsonFromAnArr($sectionData);
                    $sql = 'UPDATE '.$tbl_name.' SET '.$key." = '".$sectionDatajson."' WHERE eFor='".$eFor."'";
                    $obj->sql_query($sql);
                }
            }
        }
    }
}
if (isset($_POST['submit'])) {
    $check_file_query = "SELECT lBannerSection,lHowitworkSection,lSecuresafeSection,lDownloadappSection,lServiceSection,lBenefitSection,lEarnSection FROM {$tbl_name} where eFor='".$eFor."'";
    $check_file = $obj->MySQLSelect($check_file_query);

    $sectionData = json_decode($check_file[0]['lBannerSection'], true);
    $banner_section_arr['title_'.$vCode] = $_POST['banner_section_title'] ?? '';
    $banner_section_arr['sub_title_'.$vCode] = $_POST['banner_section_sub_title'] ?? '';
    $banner_section_arr['desc_'.$vCode] = $_POST['banner_section_desc'] ?? '';
    $banner_section_arr['img_'.$vCode] = $sectionData['img_'.$vCode] ?? '';
    $banner_section_arr = !(empty($sectionData)) ? array_merge($sectionData, $banner_section_arr) : $banner_section_arr;
    $banner_section = getJsonFromAnArr($banner_section_arr); // addslashes because double quotes stored after slashes so while getting data no problem

    $sectionData = json_decode($check_file[0]['lEarnSection'], true);
    $earn_section_arr['title_'.$vCode] = $_POST['earn_section_title'] ?? '';
    $earn_section_arr['menu_title_'.$vCode] = $_POST['earn_section_menu_title'] ?? '';
    $earn_section_arr['desc_'.$vCode] = $_POST['earn_section_desc'] ?? '';
    $earn_section_arr['img_'.$vCode] = $sectionData['img_'.$vCode] ?? '';
    $earn_section_arr = !(empty($sectionData)) ? array_merge($sectionData, $earn_section_arr) : $earn_section_arr;
    $earn_section = getJsonFromAnArr($earn_section_arr);

    $sectionData = json_decode($check_file[0]['lHowitworkSection'], true);
    $how_it_work_section_arr['menu_title_'.$vCode] = $_POST['how_it_work_section_menu_title'] ?? '';
    $how_it_work_section_arr['title_'.$vCode] = $_POST['how_it_work_section_title'] ?? '';
    $how_it_work_section_arr['desc_'.$vCode] = $_POST['how_it_work_section_desc'] ?? '';
    $how_it_work_section_arr['img_'.$vCode] = $sectionData['img_'.$vCode] ?? '';
    $how_it_work_section_arr = !(empty($sectionData)) ? array_merge($sectionData, $how_it_work_section_arr) : $how_it_work_section_arr;

    // For How it works Added By PJ 25 Sep 2019
    for ($i = 1; $i <= 6; ++$i) {
        $how_it_work_section_arr['hiw_title'.$i.'_'.$vCode] = $_POST['how_it_work_section_hiw_title'.$i] ?? '';
        $how_it_work_section_arr['hiw_desc'.$i.'_'.$vCode] = $_POST['how_it_work_section_hiw_desc'.$i] ?? '';
    }
    // ----------------------------------------

    $how_it_work_section = getJsonFromAnArr($how_it_work_section_arr);

    $sectionData = json_decode($check_file[0]['lDownloadappSection'], true);
    $download_section_arr['menu_title_'.$vCode] = $_POST['download_section_menu_title'] ?? '';
    $download_section_arr['title_'.$vCode] = $_POST['download_section_title'] ?? '';
    $download_section_arr['desc_'.$vCode] = $_POST['download_section_desc'] ?? '';
    $download_section_arr['link1_'.$vCode] = $_POST['download_section_link1'] ?? '';
    $download_section_arr['link2_'.$vCode] = $_POST['download_section_link2'] ?? '';
    $download_section_arr['img_'.$vCode] = $sectionData['img_'.$vCode] ?? '';
    $download_section_arr['img2_'.$vCode] = $sectionData['img2_'.$vCode] ?? '';
    $download_section_arr = !(empty($sectionData)) ? array_merge($sectionData, $download_section_arr) : $download_section_arr;
    $download_section = getJsonFromAnArr($download_section_arr);

    $sectionData = json_decode($check_file[0]['lSecuresafeSection'], true);
    $secure_section_arr['menu_title_'.$vCode] = $_POST['secure_section_menu_title'] ?? '';
    $secure_section_arr['title_'.$vCode] = $_POST['secure_section_title'] ?? '';
    $secure_section_arr['desc_'.$vCode] = $_POST['secure_section_desc'] ?? '';
    $secure_section_arr['img_'.$vCode] = $sectionData['img_'.$vCode] ?? '';
    $secure_section_arr = !(empty($sectionData)) ? array_merge($sectionData, $secure_section_arr) : $secure_section_arr;
    $secure_section = getJsonFromAnArr($secure_section_arr);

    // Service Section start
    $sectionData = json_decode($check_file[0]['lServiceSection'], true);
    $service_section_arr['menu_title_'.$vCode] = $_POST['service_section_menu_title'] ?? '';
    $service_section_arr['title_first_'.$vCode] = $_POST['service_section_title_first'] ?? '';
    $service_section_arr['desc_first_'.$vCode] = $_POST['service_section_desc_first'] ?? '';
    $service_section_arr['title_sec_'.$vCode] = $_POST['service_section_title_sec'] ?? '';
    $service_section_arr['desc_sec_'.$vCode] = $_POST['service_section_desc_sec'] ?? '';

    $service_section_arr['title_third_'.$vCode] = $_POST['service_section_title_third'] ?? '';
    $service_section_arr['desc_third_'.$vCode] = $_POST['service_section_desc_third'] ?? '';

    $service_section_arr['img_first_'.$vCode] = $sectionData['img_first_'.$vCode] ?? '';
    $service_section_arr['img_sec_'.$vCode] = $sectionData['img_sec_'.$vCode] ?? '';
    $service_section_arr = !(empty($sectionData)) ? array_merge($sectionData, $service_section_arr) : $service_section_arr;
    $service_section = getJsonFromAnArr($service_section_arr);
    // Service Section end

    // Benefit Section start
    $sectionData = json_decode($check_file[0]['lBenefitSection'], true);
    $benefit_section_arr['menu_title_'.$vCode] = $_POST['benefit_section_menu_title'] ?? '';
    $benefit_section_arr['main_title_'.$vCode] = $_POST['benefit_section_main_title'] ?? '';
    $benefit_section_arr['main_desc_'.$vCode] = $_POST['benefit_section_main_desc'] ?? '';
    $benefit_section_arr['img_'.$vCode] = $sectionData['img_'.$vCode] ?? '';
    $benefit_section_arr['title_first_'.$vCode] = $_POST['benefit_section_title_first'] ?? '';
    $benefit_section_arr['desc_first_'.$vCode] = $_POST['benefit_section_desc_first'] ?? '';
    $benefit_section_arr['title_sec_'.$vCode] = $_POST['benefit_section_title_sec'] ?? '';
    $benefit_section_arr['desc_sec_'.$vCode] = $_POST['benefit_section_desc_sec'] ?? '';
    $benefit_section_arr['title_third_'.$vCode] = $_POST['benefit_section_title_third'] ?? '';
    $benefit_section_arr['desc_third_'.$vCode] = $_POST['benefit_section_desc_third'] ?? '';
    $benefit_section_arr['title_fourth_'.$vCode] = $_POST['benefit_section_title_fourth'] ?? '';
    $benefit_section_arr['desc_fourth_'.$vCode] = $_POST['benefit_section_desc_fourth'] ?? '';
    $benefit_section_arr['title_fifth_'.$vCode] = $_POST['benefit_section_title_fifth'] ?? '';
    $benefit_section_arr['desc_fifth_'.$vCode] = $_POST['benefit_section_desc_fifth'] ?? '';
    $benefit_section_arr['title_six_'.$vCode] = $_POST['benefit_section_title_six'] ?? '';
    $benefit_section_arr['desc_six_'.$vCode] = $_POST['benefit_section_desc_six'] ?? '';

    $benefit_section_arr['title_seven_'.$vCode] = $_POST['benefit_section_title_seven'] ?? '';
    $benefit_section_arr['desc_seven_'.$vCode] = $_POST['benefit_section_desc_seven'] ?? '';

    $benefit_section_arr['title_eight_'.$vCode] = $_POST['benefit_section_title_eight'] ?? '';
    $benefit_section_arr['desc_eight_'.$vCode] = $_POST['benefit_section_desc_eight'] ?? '';

    $benefit_section_arr['img_first_'.$vCode] = $sectionData['img_first_'.$vCode] ?? '';
    $benefit_section_arr['img_sec_'.$vCode] = $sectionData['img_sec_'.$vCode] ?? '';
    $benefit_section_arr['img_third_'.$vCode] = $sectionData['img_third_'.$vCode] ?? '';
    $benefit_section_arr['img_fourth_'.$vCode] = $sectionData['img_fourth_'.$vCode] ?? '';
    $benefit_section_arr['img_fifth_'.$vCode] = $sectionData['img_fifth_'.$vCode] ?? '';
    $benefit_section_arr['img_six_'.$vCode] = $sectionData['img_six_'.$vCode] ?? '';
    $benefit_section_arr = !(empty($sectionData)) ? array_merge($sectionData, $benefit_section_arr) : $benefit_section_arr;
    $benefit_section = getJsonFromAnArr($benefit_section_arr);
    // Benefit Section end
}

if (isset($_POST['submit'])) {
    if (SITE_TYPE === 'Demo') {
        header("Location: {$url}&success=2");

        exit;
    }
    // $q = "INSERT INTO ";
    // $where = '';
    // if (!empty($db_efordata[0]['cnt'])) {
    $q = 'UPDATE ';
    $where = " WHERE `eFor` = '".$eFor."'";
    // }

    $query = $q.' `'.$tbl_name."` SET
	`lBannerSection` = '".$banner_section."',
	`lHowitworkSection` = '".$how_it_work_section."',
	`lSecuresafeSection` = '".$secure_section."',
	`lDownloadappSection` = '".$download_section."',
	`lServiceSection` = '".$service_section."',
	`lEarnSection` = '".$earn_section."',
	`lBenefitSection` = '".$benefit_section."',
   `eFor` = '".$eFor."'
   ".$where; // die;

    $obj->sql_query($query);
    // $id = (!empty($db_efordata[0]['cnt'])) ? $id : $obj->GetInsertId();
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

if ('Edit' === $action) {
    $sql = "SELECT * FROM {$tbl_name} WHERE eFor = '".$eFor."'";
    $db_data = $obj->MySQLSelect($sql);
    if (count($db_data) > 0) {
        foreach ($db_data as $key => $value) {
            $banner_section = json_decode($value['lBannerSection'], true);
            $how_it_work_section = (array) json_decode($value['lHowitworkSection']);
            $secure_section = json_decode($value['lSecuresafeSection'], true);
            $download_section = json_decode($value['lDownloadappSection'], true);
            $earn_section = json_decode($value['lEarnSection'], true);
            $service_section = json_decode($value['lServiceSection'], true);
            $benefit_section = json_decode($value['lBenefitSection'], true);
        }
    }
}
if (isset($_POST['submit']) && 'submit' === $_POST['submit']) {
    $required = 'required';
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title>Admin | Business Home Content <?php echo $action; ?></title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
        <?php include_once 'global_files.php'; ?>
        <!-- On OFF switch -->
        <link href="../assets/css/jquery-ui.css" rel="stylesheet" />
        <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
        <style>
            .body-div.innersection {
                box-shadow: -1px -2px 73px 2px #dedede;
                float: none;
            }
            .innerbg_image {
                width:auto;margin:10px 0;height: 150px;
            }
            .notes {
                font-weight: 700;font-style: italic;
            }
        </style>
    </head>
    <!-- END  HEAD-->
    <!-- BEGIN BODY-->
    <body class="padTop53 " >
        <!-- MAIN WRAPPER -->
        <div id="wrap">
            <?php include_once 'header.php'; ?>
            <?php include_once 'left_menu.php'; ?>
            <!--PAGE CONTENT -->
            <div id="content">
                <div class="inner">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2><?php echo $action; ?> Business Home Content (<?php echo $title; ?>)</h2>
                            <a href="<?php echo $url; ?>" class="back_link">
                                <input type="button" value="Back to Listing" class="add-btn">
                            </a>
                        </div>
                    </div>
                    <?php
                    include 'valid_msg.php';
?>
                    <hr />
                    <div class="body-div">
                        <div class="form-group">
                            <?php if (1 === $success) { ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                    <?php echo $langage_lbl_admin['LBL_Record_Updated_successfully']; ?>
                                </div><br/>
                            <?php } elseif (2 === $success) { ?>
                                <div class="alert alert-danger alert-dismissable">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                    <?php echo $langage_lbl_admin['LBL_EDIT_DELETE_RECORD']; ?>
                                </div><br/>
                            <?php } ?>
                            <form method="post" name="_home_content_form" id="_home_content_form" action="" enctype='multipart/form-data'>
                                <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                                <input type="hidden" name="vCode" value="<?php echo $vCode; ?>">
                                <input type="hidden" name="previousLink" id="previousLink" value="<?php echo $previousLink; ?>"/>
                                <input type="hidden" name="backlink" id="backlink" value="<?php echo $url; ?>"/>

                                 <div class="body-div innersection">
                                    <div class="form-group">
                                        <div class="row"><div class="col-lg-12"><h3>Banner section</h3></div></div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Title<span class="red"> *</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="banner_section_title"  id="banner_section_title" value="<?php echo $banner_section['title_'.$vCode]; ?>" placeholder="Title" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Sub Title<span class="red"> *</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="banner_section_sub_title"  id="banner_section_sub_title" value="<?php echo $banner_section['sub_title_'.$vCode]; ?>" placeholder="Sub Title" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Description</label>
                                            </div>
                                            <div class="col-lg-12">
                                                <textarea class="form-control ckeditor" rows="10" name="banner_section_desc"  id="banner_section_desc"  placeholder="Description"><?php echo $banner_section['desc_'.$vCode]; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Background Image</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <?php if ('' !== $banner_section['img_'.$vCode]) { ?>
                                                    <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$banner_section['img_'.$vCode]; ?>" class="innerbg_image"/>
                                                <?php } ?>
                                                <input type="file" class="form-control FilUploader" name="banner_section_img"  id="banner_section_img" accept=".png,.jpg,.jpeg,.gif,.svg">
                                                <br/>
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 1730px * 600px.]</span>
                                            </div>
                                        </div>
                                    </div>
                                 </div>

                                 <div class="body-div innersection">
                                    <div class="form-group">
                                        <div class="row"><div class="col-lg-12"><h3>How It work section</h3></div></div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Menu Title</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="how_it_work_section_menu_title"  id="how_it_work_section_menu_title" value="<?php echo $how_it_work_section['menu_title_'.$vCode]; ?>" placeholder="Menu Title">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Title<span class="red"> *</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="how_it_work_section_title"  id="how_it_work_section_title" value="<?php echo $how_it_work_section['title_'.$vCode]; ?>" placeholder="Title" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Description</label>
                                            </div>
                                            <div class="col-lg-12">
                                                <textarea class="form-control ckeditor" rows="10" name="how_it_work_section_desc"  id="how_it_work_section_desc"  placeholder="Description"><?php echo $how_it_work_section['desc_'.$vCode]; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Image</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <?php if ('' !== $how_it_work_section['img_'.$vCode]) { ?>
                                                    <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$how_it_work_section['img_'.$vCode]; ?>" class="innerbg_image"/>
                                                <?php } ?>
                                                <input type="file" class="form-control FilUploader" name="how_it_work_section_img"  id="how_it_work_section_img" accept=".png,.jpg,.jpeg,.gif,.svg">
                                                <br/>
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 450px * 535px.]</span>
                                            </div>
                                        </div>


                                        <!-- How It Works Blocks -->
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <h3>How It Works Blocks</h3>
                                                <p>(Note : Title and Description are required for show this blocks on page..)</p>
                                                <hr/>
                                            </div>

                                            <?php for ($i = 1; $i <= 6; ++$i) { ?>
                                                <div class="col-lg-4">
                                                    <!-- Title -->
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Block Title <?php echo $i; ?></label>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <input type="text" class="form-control" name="how_it_work_section_hiw_title<?php echo $i; ?>"  id="how_it_work_section_hiw_title<?php echo $i; ?>" value="<?php echo $how_it_work_section['hiw_title'.$i.'_'.$vCode]; ?>" placeholder="Title">
                                                        </div>
                                                    </div>

                                                    <!-- Description  -->
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Block Description <?php echo $i; ?></label>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <textarea class="form-control" name="how_it_work_section_hiw_desc<?php echo $i; ?>"  id="how_it_work_section_hiw_desc<?php echo $i; ?>" value="<?php echo $how_it_work_section['hiw_desc'.$i.'_'.$vCode]; ?>" placeholder="Description" rows="3"><?php echo $how_it_work_section['hiw_desc'.$i.'_'.$vCode]; ?></textarea>
                                                        </div>
                                                    </div>

                                                    <!-- Image  -->
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Block Image <?php echo $i; ?></label>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <?php if ('' !== $how_it_work_section['hiw_img'.$i.'_'.$vCode]) { ?>
                                                                <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$how_it_work_section['hiw_img'.$i.'_'.$vCode]; ?>" class="innerbg_image"/ style="max-height:100px;">
                                                            <?php } ?>
                                                            <input type="file" class="form-control FilUploader" name="how_it_work_section_hiw_img<?php echo $i; ?>"  id="how_it_work_section_hiw_img<?php echo $i; ?>" accept=".png,.jpg,.jpeg,.gif,.svg">
                                                            <br/>
                                                            <span class="notes">[Note: For Better Resolution Upload only image size of 40px * 40px.]</span>
                                                        </div>
                                                    </div>
                                                    <hr/>
                                                </div>

                                            <?php } ?>
                                        </div>
                                        <hr/>
                                        <!-- How It Works Blocks End -->
                                    </div>
                                 </div>

                                <div class="body-div innersection">
                                    <div class="form-group">
                                       <div class="row"><div class="col-lg-12"><h3>Profile Section</h3></div></div>

                                        <div class="row">
                                           <div class="col-lg-12">
                                               <label>Menu Title</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <input type="text" class="form-control" name="service_section_menu_title"  id="service_section_menu_title" value="<?php echo $service_section['menu_title_'.$vCode]; ?>" placeholder="Menu Title">
                                           </div>
                                        </div>


                                        <div class="row"><div class="col-lg-12"><h4>Profile Data</h4></div></div>
                                        <div class="row">
                                           <div class="col-lg-12">
                                               <label>Title#1</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <input type="text" class="form-control" name="service_section_title_first" value="<?php echo $service_section['title_first_'.$vCode]; ?>" placeholder="Main Title">
                                           </div>
                                        </div>
                                        <div class="row">
                                           <div class="col-lg-12">
                                               <label>Description#1</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <textarea class="form-control" rows="5" name="service_section_desc_first"  id="service_section_desc_first" placeholder="Description"><?php echo $service_section['desc_first_'.$vCode]; ?></textarea>
                                           </div>
                                        </div>

                                        <div class="row">
                                           <div class="col-lg-12">
                                               <label>Image#1</label>
                                           </div>
                                           <div class="col-lg-6">
                                                <?php if ('' !== $service_section['img_first_'.$vCode]) { ?>
                                                   <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$service_section['img_first_'.$vCode]; ?>" class="innerbg_image"/>
                                                <?php } ?>
                                               <input type="file" class="form-control" name="service_section_img_first" value="<?php echo $service_section['img_first_'.$vCode]; ?>">
                                               <span class="notes">[Note: For Better Resolution Upload only image size of 775px * 490px.]</span>
                                           </div>
                                        </div>

                                        <div class="row">
                                           <div class="col-lg-12">
                                               <label>Title#2</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <input type="text" class="form-control" name="service_section_title_sec" value="<?php echo $service_section['title_sec_'.$vCode]; ?>" placeholder="Title">
                                           </div>
                                        </div>
                                        <div class="row">
                                           <div class="col-lg-12">
                                               <label>Description#2</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <textarea class="form-control" rows="5" name="service_section_desc_sec"  id="service_section_desc_sec" placeholder="Description"><?php echo $service_section['desc_sec_'.$vCode]; ?></textarea>
                                           </div>
                                        </div>
                                        <div class="row">
                                           <div class="col-lg-12">
                                               <label>Image#2</label>
                                           </div>
                                           <div class="col-lg-6">
                                                <?php if ('' !== $service_section['img_sec_'.$vCode]) { ?>
                                                   <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$service_section['img_sec_'.$vCode]; ?>" class="innerbg_image"/>
                                                <?php } ?>
                                               <input type="file" class="form-control" name="service_section_img_sec" value="<?php echo $service_section['img_sec_'.$vCode]; ?>">
                                               <span class="notes">[Note: For Better Resolution Upload only image size of 775px * 490px.]</span>
                                           </div>
                                        </div>


                                        <div class="row">
                                           <div class="col-lg-12">
                                               <label>Title#3</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <input type="text" class="form-control" name="service_section_title_third" value="<?php echo $service_section['title_third_'.$vCode]; ?>" placeholder="Title">
                                           </div>
                                        </div>
                                        <div class="row">
                                           <div class="col-lg-12">
                                               <label>Description#3</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <textarea class="form-control" rows="5" name="service_section_desc_third"  id="service_section_desc_third" placeholder="Description"><?php echo $service_section['desc_third_'.$vCode]; ?></textarea>
                                           </div>
                                        </div>
                                        <div class="row">
                                           <div class="col-lg-12">
                                               <label>Image#3</label>
                                           </div>
                                           <div class="col-lg-6">
                                                <?php if ('' !== $service_section['img_third_'.$vCode]) { ?>
                                                   <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$service_section['img_third_'.$vCode]; ?>" class="innerbg_image"/>
                                                <?php } ?>
                                               <input type="file" class="form-control" name="service_section_img_third" value="<?php echo $service_section['img_third_'.$vCode]; ?>">
                                               <span class="notes">[Note: For Better Resolution Upload only image size of 775px * 490px.]</span>
                                           </div>
                                        </div>


                                    </div>
                                </div>



                                <div class="body-div innersection">
                                    <div class="form-group">
                                       <div class="row"><div class="col-lg-12"><h3>Benefit Section</h3></div></div>
                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Main Title<span class="red"> *</span></label>
                                           </div>
                                           <div class="col-lg-6">
                                               <input type="text" class="form-control" name="benefit_section_main_title"  id="benefit_section_main_title" value="<?php echo $benefit_section['main_title_'.$vCode]; ?>" placeholder="Main Title" required>
                                           </div>
                                       </div>

                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Menu Title</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <input type="text" class="form-control" name="benefit_section_menu_title"  id="benefit_section_menu_title" value="<?php echo $benefit_section['menu_title_'.$vCode]; ?>" placeholder="Menu Title">
                                           </div>
                                       </div>

                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Main Description</label>
                                           </div>
                                           <div class="col-lg-12">
                                               <textarea class="form-control ckeditor" rows="10" name="benefit_section_main_desc"  id="benefit_section_main_desc"  placeholder="Main Description"><?php echo $benefit_section['main_desc_'.$vCode]; ?></textarea>
                                           </div>
                                       </div>
                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Main Image</label>
                                           </div>
                                           <div class="col-lg-6">
                                                <?php if ('' !== $benefit_section['img_'.$vCode]) { ?>
                                                   <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$benefit_section['img_'.$vCode]; ?>" class="innerbg_image"/>
                                                <?php } ?>
                                               <input type="file" class="form-control" name="benefit_section_img" value="<?php echo $benefit_section['img_'.$vCode]; ?>" placeholder="Main Title">
                                           </div>
                                       </div>
                                       <div class="row"><div class="col-lg-12"><h4>Benefit Data</h4></div></div>
                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Title#1</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <input type="text" class="form-control" name="benefit_section_title_first" value="<?php echo $benefit_section['title_first_'.$vCode]; ?>" placeholder="Main Title">
                                           </div>
                                       </div>
                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Description#1</label>
                                           </div>
                                           <div class="col-lg-6">
                                                <textarea class="form-control" name="benefit_section_desc_first" rows="5" placeholder="Description"><?php echo $benefit_section['desc_first_'.$vCode]; ?></textarea>
                                           </div>
                                       </div>
                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Image#1</label>
                                           </div>
                                           <div class="col-lg-6">
                                                <?php if ('' !== $benefit_section['img_first_'.$vCode]) { ?>
                                                   <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$benefit_section['img_first_'.$vCode]; ?>" class="innerbg_image"/>
                                                <?php } ?>
                                               <input type="file" class="form-control" name="benefit_section_img_first" value="<?php echo $benefit_section['img_first_'.$vCode]; ?>" placeholder="Main Title">
                                               <span class="notes">[Note: For Better Resolution Upload only image size of 150px * 150px.]</span>
                                           </div>
                                       </div>

                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Title#2</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <input type="text" class="form-control" name="benefit_section_title_sec" value="<?php echo $benefit_section['title_sec_'.$vCode]; ?>" placeholder="Main Title">
                                           </div>
                                       </div>

                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Description#2</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <textarea class="form-control" name="benefit_section_desc_sec" rows="5" placeholder="Description"><?php echo $benefit_section['desc_sec_'.$vCode]; ?></textarea>
                                           </div>
                                       </div>
                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Image#2</label>
                                           </div>
                                           <div class="col-lg-6">
                                                <?php if ('' !== $benefit_section['img_sec_'.$vCode]) { ?>
                                                   <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$benefit_section['img_sec_'.$vCode]; ?>" class="innerbg_image"/>
                                                <?php } ?>
                                               <input type="file" class="form-control" name="benefit_section_img_sec" value="<?php echo $benefit_section['img_sec_'.$vCode]; ?>" placeholder="Main Title">
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 150px * 150px.]</span>
                                           </div>
                                       </div>

                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Title#3</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <input type="text" class="form-control" name="benefit_section_title_third" value="<?php echo $benefit_section['title_third_'.$vCode]; ?>" placeholder="Main Title">
                                           </div>
                                       </div>

                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Description#3</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <textarea class="form-control" name="benefit_section_desc_third" rows="5" placeholder="Description"><?php echo $benefit_section['desc_third_'.$vCode]; ?></textarea>
                                           </div>
                                       </div>
                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Image#3</label>
                                           </div>
                                           <div class="col-lg-6">
                                                <?php if ('' !== $benefit_section['img_third_'.$vCode]) { ?>
                                                   <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$benefit_section['img_third_'.$vCode]; ?>" class="innerbg_image"/>
                                                <?php } ?>
                                               <input type="file" class="form-control" name="benefit_section_img_third" value="<?php echo $benefit_section['img_third_'.$vCode]; ?>" placeholder="Main Title">
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 150px * 150px.]</span>
                                           </div>
                                       </div>

                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Title#4</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <input type="text" class="form-control" name="benefit_section_title_fourth" value="<?php echo $benefit_section['title_fourth_'.$vCode]; ?>" placeholder="Main Title">
                                           </div>
                                       </div>

                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Description#4</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <!--<input type="text" class="form-control" name="benefit_section_desc_fourth" value="<?php echo $benefit_section['desc_fourth_'.$vCode]; ?>" placeholder="Main Title">-->
                                               <textarea class="form-control" name="benefit_section_desc_fourth" rows="5" placeholder="Description"><?php echo $benefit_section['desc_fourth_'.$vCode]; ?></textarea>
                                           </div>
                                       </div>
                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Image#4</label>
                                           </div>
                                           <div class="col-lg-6">
                                                <?php if ('' !== $benefit_section['img_fourth_'.$vCode]) { ?>
                                                   <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$benefit_section['img_fourth_'.$vCode]; ?>" class="innerbg_image"/>
                                                <?php } ?>
                                               <input type="file" class="form-control" name="benefit_section_img_fourth" value="<?php echo $benefit_section['img_fourth_'.$vCode]; ?>" placeholder="Main Title">
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 150px * 150px.]</span>
                                           </div>
                                       </div>

                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Title#5</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <input type="text" class="form-control" name="benefit_section_title_fifth" value="<?php echo $benefit_section['title_fifth_'.$vCode]; ?>" placeholder="Main Title">
                                           </div>
                                       </div>

                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Description#5</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <!--<input type="text" class="form-control" name="benefit_section_desc_fifth" value="<?php echo $benefit_section['desc_fifth_'.$vCode]; ?>" placeholder="Main Title">-->
                                               <textarea class="form-control" name="benefit_section_desc_fifth" rows="5" placeholder="Description"><?php echo $benefit_section['desc_fifth_'.$vCode]; ?></textarea>
                                           </div>
                                       </div>
                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Image#5</label>
                                           </div>
                                           <div class="col-lg-6">
                                                <?php if ('' !== $benefit_section['img_fifth_'.$vCode]) { ?>
                                                   <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$benefit_section['img_fifth_'.$vCode]; ?>" class="innerbg_image"/>
                                                <?php } ?>
                                               <input type="file" class="form-control" name="benefit_section_img_fifth" value="<?php echo $benefit_section['img_fifth_'.$vCode]; ?>" placeholder="Main Title">
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 150px * 150px.]</span>
                                           </div>
                                       </div>

                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Title#6</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <input type="text" class="form-control" name="benefit_section_title_six" value="<?php echo $benefit_section['title_six_'.$vCode]; ?>" placeholder="Main Title">
                                           </div>
                                       </div>

                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Description#6</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <!--<input type="text" class="form-control" name="benefit_section_desc_six" value="<?php echo $benefit_section['desc_six_'.$vCode]; ?>" placeholder="Main Title">-->
                                               <textarea class="form-control" name="benefit_section_desc_six" rows="5" placeholder="Description"><?php echo $benefit_section['desc_six_'.$vCode]; ?></textarea>
                                           </div>
                                       </div>
                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Image#6</label>
                                           </div>
                                           <div class="col-lg-6">
                                                <?php if ('' !== $benefit_section['img_six_'.$vCode]) { ?>
                                                   <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$benefit_section['img_six_'.$vCode]; ?>" class="innerbg_image"/>
                                                <?php } ?>
                                               <input type="file" class="form-control" name="benefit_section_img_six" value="<?php echo $benefit_section['img_six_'.$vCode]; ?>" placeholder="Main Title">
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 150px * 150px.]</span>
                                           </div>
                                       </div>

                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Title#7</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <input type="text" class="form-control" name="benefit_section_title_seven" value="<?php echo $benefit_section['title_seven_'.$vCode]; ?>" placeholder="Main Title">
                                           </div>
                                       </div>

                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Description#7</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <!--<input type="text" class="form-control" name="benefit_section_desc_seven" value="<?php echo $benefit_section['desc_seven_'.$vCode]; ?>" placeholder="Main Title">-->
                                               <textarea class="form-control" name="benefit_section_desc_seven" rows="5" placeholder="Description"><?php echo $benefit_section['desc_seven_'.$vCode]; ?></textarea>
                                           </div>
                                       </div>
                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Image#7</label>
                                           </div>
                                           <div class="col-lg-6">
                                                <?php if ('' !== $benefit_section['img_seven_'.$vCode]) { ?>
                                                   <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$benefit_section['img_seven_'.$vCode]; ?>" class="innerbg_image"/>
                                                <?php } ?>
                                               <input type="file" class="form-control" name="benefit_section_img_seven" value="<?php echo $benefit_section['img_seven_'.$vCode]; ?>" placeholder="Main Title">
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 150px * 150px.]</span>
                                           </div>
                                       </div>


                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Title#8</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <input type="text" class="form-control" name="benefit_section_title_eight" value="<?php echo $benefit_section['title_eight_'.$vCode]; ?>" placeholder="Main Title">
                                           </div>
                                       </div>

                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Description#8</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <!--<input type="text" class="form-control" name="benefit_section_desc_eight" value="<?php echo $benefit_section['desc_eight_'.$vCode]; ?>" placeholder="Main Title">-->
                                               <textarea class="form-control" name="benefit_section_desc_eight" rows="5" placeholder="Description"><?php echo $benefit_section['desc_eight_'.$vCode]; ?></textarea>
                                           </div>
                                       </div>
                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Image#8</label>
                                           </div>
                                           <div class="col-lg-6">
                                                <?php if ('' !== $benefit_section['img_eight_'.$vCode]) { ?>
                                                   <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$benefit_section['img_eight_'.$vCode]; ?>" class="innerbg_image"/>
                                                <?php } ?>
                                               <input type="file" class="form-control" name="benefit_section_img_eight" value="<?php echo $benefit_section['img_eight_'.$vCode]; ?>" placeholder="Main Title">
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 150px * 150px.]</span>
                                           </div>
                                       </div>


                                    </div>
                                </div>

                                <div class="body-div innersection">
                                    <div class="form-group">
                                        <div class="row"><div class="col-lg-12"><h3>Download Section</h3></div></div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Menu Title</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="download_section_menu_title"  id="download_section_menu_title" value="<?php echo $download_section['menu_title_'.$vCode]; ?>" placeholder="Menu Title">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Title<span class="red"> *</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="download_section_title"  id="download_section_title" value="<?php echo $download_section['title_'.$vCode]; ?>" placeholder="Title" required>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Description</label>
                                            </div>
                                            <div class="col-lg-12">
                                                <textarea class="form-control ckeditor" rows="10" name="download_section_desc"  id="download_section_desc"  placeholder="Description"><?php echo $download_section['desc_'.$vCode]; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Images</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <?php if ('' !== $download_section['img_'.$vCode]) { ?>
                                                    <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$download_section['img_'.$vCode]; ?>" class="innerbg_image"/>
                                                <?php } ?>
                                                <input type="file" class="form-control FilUploader" name="download_section_img"  id="download_section_img" accept=".png,.jpg,.jpeg,.gif,.svg">
                                                <br/>
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 205px * 590px.]</span>
                                            </div>
                                            <div class="col-lg-6">
                                                <?php if ('' !== $download_section['img2_'.$vCode]) { ?>
                                                    <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$download_section['img2_'.$vCode]; ?>" class="innerbg_image"/>
                                                <?php } ?>
                                                <input type="file" class="form-control FilUploader" name="download_section_img2"  id="download_section_img2" accept=".png,.jpg,.jpeg,.gif,.svg">
                                                <br/>
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 205px * 590px.]</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Play store link for anroid</label>
                                                <h5>[Note: Please use #ANDROID_APP_LINK# predefined tags to display the Play store link value. Please go to Settings >> General section to change the values of above predefined tags.]</h5>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="download_section_link1"  id="download_section_link1" value="<?php echo $download_section['link1_'.$vCode]; ?>" placeholder="Play store link for anroid">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Play store link for ios</label>
                                                <h5>[Note: Please use #IPHONE_APP_LINK# predefined tags to display the Play store link value. Please go to Settings >> General section to change the values of above predefined tags.]</h5>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="download_section_link2"  id="download_section_link2" value="<?php echo $download_section['link2_'.$vCode]; ?>" placeholder="Play store link for ios">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="body-div innersection">
                                    <div class="form-group">
                                       <div class="row"><div class="col-lg-12"><h3>Earn Section</h3></div></div>

                                        <div class="row">
                                           <div class="col-lg-12">
                                               <label>Title<span class="red"> *</span></label>
                                           </div>
                                           <div class="col-lg-6">
                                               <input type="text" class="form-control" name="earn_section_title"  id="earn_section_title" value="<?php echo $earn_section['title_'.$vCode]; ?>" placeholder="Title" required>
                                           </div>
                                        </div>
                                        <div class="row">
                                           <div class="col-lg-12">
                                               <label>Menu Title</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <input type="text" class="form-control" name="earn_section_menu_title"  id="earn_section_menu_title" value="<?php echo $earn_section['menu_title_'.$vCode]; ?>" placeholder="Menu Title">
                                           </div>
                                        </div>

                                        <div class="row">
                                           <div class="col-lg-12">
                                               <label>Description</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <textarea class="form-control" rows="5" name="earn_section_desc" placeholder="Description"><?php echo $earn_section['desc_'.$vCode]; ?></textarea>
                                           </div>
                                        </div>

                                        <div class="row">
                                           <div class="col-lg-12">
                                               <label>Image</label>
                                           </div>
                                           <div class="col-lg-6">
                                                <?php if ('' !== $earn_section['img_'.$vCode]) { ?>
                                                   <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$earn_section['img_'.$vCode]; ?>" class="innerbg_image"/>
                                                <?php } ?>
                                               <input type="file" class="form-control" name="earn_section_img" value="<?php echo $earn_section['img_'.$vCode]; ?>">
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 745px * 595px.]</span>
                                           </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="body-div innersection">
                                    <div class="form-group">
                                       <div class="row"><div class="col-lg-12"><h3>Secure Section</h3></div></div>

                                        <div class="row">
                                           <div class="col-lg-12">
                                               <label>Title<span class="red"> *</span></label>
                                           </div>
                                           <div class="col-lg-6">
                                               <input type="text" class="form-control" name="secure_section_title"  id="secure_section_title" value="<?php echo $secure_section['title_'.$vCode]; ?>" placeholder="Title" required>
                                           </div>
                                        </div>

                                        <div class="row">
                                           <div class="col-lg-12">
                                               <label>Menu Title</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <input type="text" class="form-control" name="secure_section_menu_title"  id="secure_section_menu_title" value="<?php echo $secure_section['menu_title_'.$vCode]; ?>" placeholder="Menu Title">
                                           </div>
                                        </div>

                                        <div class="row">
                                           <div class="col-lg-12">
                                               <label>Description</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <textarea class="form-control" rows="5" name="secure_section_desc"  id="secure_section_desc" placeholder="Description"><?php echo $secure_section['desc_'.$vCode]; ?></textarea>
                                           </div>
                                        </div>

                                        <div class="row">
                                           <div class="col-lg-12">
                                               <label>Image</label>
                                           </div>
                                           <div class="col-lg-6">
                                                <?php if ('' !== $secure_section['img_'.$vCode]) { ?>
                                                   <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$secure_section['img_'.$vCode]; ?>" class="innerbg_image"/>
                                                <?php } ?>
                                               <input type="file" class="form-control" name="secure_section_img" value="<?php echo $secure_section['img_'.$vCode]; ?>">
                                               <span class="notes">[Note: For Better Resolution Upload only image size of 745px * 595px.]</span>
                                           </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- End Home Header area-->

                                <div class="row">
                                    <div class="col-lg-12">
                                        <input type="submit" class=" btn btn-default" name="submit" id="submit" value="<?php echo $action; ?> Home Content">
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
        <script>
            $(document).ready(function () {
                var referrer;
<?php if (1 === $goback) { ?>
                    alert('<?php echo $var_msg; ?>');
                    //history.go(-1);
                    window.location.href = "<?php echo $url; ?>";


<?php } ?>
                if ($("#previousLink").val() == "") { //alert('pre1');
                    referrer = document.referrer;
                } else {
                    referrer = $("#previousLink").val();
                }

                if (referrer == "") {
                    referrer = "<?php echo $url; ?>";
                } else { //alert('hi');
                    //$("#backlink").val(referrer);
                    referrer = "<?php echo $url; ?>";
                    // alert($("#backlink").val(referrer));
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

        </script>
    </body>
    <!-- END BODY-->
</html>
