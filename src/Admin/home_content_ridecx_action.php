<?php
include_once '../common.php';

if ('Yes' === $THEME_OBJ->isRideCXv2ThemeActive()) {
    header('Location: home_content_ridecxv2_action.php?'.http_build_query($_GET));

    exit;
}

$sql_vehicle_category_table_name = getVehicleCategoryTblName();

$id = $_REQUEST['id'] ?? '';
$success = $_REQUEST['success'] ?? 0;
$action = ('' !== $id) ? 'Edit' : 'Add';
$backlink = $_POST['backlink'] ?? '';
$previousLink = '';
// $previousLink = isset($_POST['backlink']) ? $_POST['backlink'] : '';

$message_print_id = $id;
$vCode = $_POST['vCode'] ?? '';
$var_msg = $_REQUEST['var_msg'] ?? '';

if (isset($_REQUEST['goback'])) {
    $goback = $_REQUEST['goback'];
}

$eStatus_check = $_POST['eStatus'] ?? 'off';
$eStatus = ('on' === $eStatus_check) ? 'Active' : 'Inactive';

$cubexthemeonh = 0;
if ('Yes' === $THEME_OBJ->isRideCXThemeActive() || 'Yes' === $THEME_OBJ->isRideCXv2ThemeActive()) {
    $cubexthemeonh = 1;
}

if (1 === $cubexthemeonh) {
    $script = 'homecontent_cubejekx';
    $tbl_name = getAppTypeWiseHomeTable();

    $header_first_label = $third_sec_desc = $third_mid_desc_two1 = $home_banner_left_image = $mobile_app_right_title = $mobile_app_right_desc = $taxi_app_left_img = $manual_order_first_label = $manual_order_second_label = $manual_order_button_label = $manual_order_desc = '';
    $iLanguageMasId = 0;

    if (empty($vCode)) {
        $sql = "SELECT hc.vCode, lm.iLanguageMasId FROM {$tbl_name} as hc LEFT JOIN language_master as lm on lm.vCode = hc.vCode  WHERE lm.iLanguageMasId = '".$id."'";
        $db_data = $obj->MySQLSelect($sql);
        $vCode = $db_data[0]['vCode'];
        $iLanguageMasId = $db_data[0]['iLanguageMasId'];
    }

    $earnBusinessDetailsquery = "SELECT learnServiceCatSection,lbusinessServiceCatSection FROM {$tbl_name} where vCode='".$vCode."'";
    $earnBusinessData = $obj->MySQLSelect($earnBusinessDetailsquery);

    if (empty($earnBusinessData[0]['learnServiceCatSection'])) {
        $earnDetails['earn']['title'] = 'Earn';
        $earnDetails['earn']['subtitle'] = 'Earn Handsome Commission';
        $earnDetails['earn']['desc'] = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled.";
        $earnDetails['earn']['images'] = 'earn.svg';

        $earnDetailsJson = $obj->SqlEscapeString(json_encode($earnDetails));
        $query = 'UPDATE `'.$tbl_name."` SET
        `learnServiceCatSection` = '".$earnDetailsJson."' WHERE `vCode` = '".$vCode."'";
        $id = $obj->sql_query($query);
    }

    if (empty($earnBusinessData[0]['lbusinessServiceCatSection'])) {
        $businessDetails['business']['title'] = 'Business';
        $businessDetails['business']['subtitle'] = 'Business';
        $businessDetails['business']['desc'] = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled.";
        $businessDetails['business']['images'] = 'business.svg';

        $businessDetailsJson = $obj->SqlEscapeString(json_encode($businessDetails));
        $query = 'UPDATE `'.$tbl_name."` SET
        `lbusinessServiceCatSection` = '".$businessDetailsJson."' WHERE `vCode` = '".$vCode."'";
        $id = $obj->sql_query($query);
    }

    $img_arr = $_FILES;
    // print_R($_FILES);
    // exit;
    // $img_arr['call_section_img'] = $_FILES['call_section_img'];
    if (!empty($img_arr)) {
        if (SITE_TYPE === 'Demo') {
            // header("Location:home_action.php?success=2");
            header('Location:home_content_ridecx.php?id='.$id.'&success=2');

            exit;
        }
        // $img_arr['call_section_img'] = $_FILES['call_section_img'];
        foreach ($img_arr as $key => $value) {
            if ('vHomepageLogo' === $key) {
                continue;
            }
            if (!empty($value['name'])) {
                $img_path = $tconfig['tsite_upload_apptype_page_images_panel'];
                // $temp_gallery = $img_path . '/';
                $image_object = $value['tmp_name'];
                $img_name = explode('.', $value['name']);
                $image_name = $img_name[0].'_'.strtotime(date('H:i:s')).'.'.$img_name[1];

                $second_gen_img = 0;
                if ('general_section_img_sec' === $key) {
                    $second_gen_img = 1;
                }
                if ('how_it_work_section_img' === $key) {
                    $key = 'lHowitworkSection';
                } elseif ('download_section_img' === $key) {
                    $key = 'lDownloadappSection';
                } elseif ('secure_section_img' === $key) {
                    $key = 'lSecuresafeSection';
                } elseif ('call_section_img' === $key) {
                    $key = 'lCalltobookSection';
                } elseif ('general_section_img' === $key) {
                    $key = 'lGeneralBannerSection';
                } elseif ('general_section_img_sec' === $key) {
                    $key = 'lGeneralBannerSection';
                } elseif ('calculate_section_img' === $key) {
                    $key = 'lCalculateSection';
                }

                $check_file_query = 'SELECT '.$key." FROM {$tbl_name} where vCode='".$vCode."'";
                $check_file = $obj->MySQLSelect($check_file_query);
                $sectionData = json_decode($check_file[0][$key], true);

                if (1 === $second_gen_img) {
                    if ('' !== $message_print_id && '' !== $sectionData['img_sec']) {
                        $check_file = $img_path.$template.'/'.$sectionData['img_sec'];
                        if ('' !== $check_file && file_exists($check_file)) {
                            @unlink($check_file);
                        }
                    }
                } else {
                    if ('' !== $message_print_id && '' !== $sectionData['img']) {
                        $check_file = $img_path.$template.'/'.$sectionData['img'];
                        if ('' !== $check_file && file_exists($check_file)) {
                            @unlink($check_file);
                        }
                    }
                }

                $Photo_Gallery_folder = $img_path.$template.'/';
                if (!is_dir($Photo_Gallery_folder)) {
                    mkdir($Photo_Gallery_folder, 0777);
                }

                $img = $UPLOAD_OBJ->GeneralFileUploadHome($Photo_Gallery_folder, $image_object, $image_name, '', 'png,jpg,jpeg,gif', $vCode);

                if ('1' === $img[2]) {
                    $_SESSION['success'] = '0';
                    $_SESSION['var_msg'] = $img[1];
                    header('location:'.$backlink);
                }

                if (!empty($img[0])) {
                    if (1 === $second_gen_img) {
                        $sectionData['img_sec'] = $img[0];
                    } else {
                        $sectionData['img'] = $img[0];
                    }
                    $sectionDatajson = getJsonFromAnArr($sectionData);
                    $sql = 'UPDATE '.$tbl_name.' SET '.$key." = '".$sectionDatajson."' WHERE `vCode` = '".$vCode."'";
                    $obj->sql_query($sql);
                }
            }
        }
    }

    if (isset($_POST['submit'])) {
        $check_file_query = "SELECT lHowitworkSection,lSecuresafeSection,lDownloadappSection,lCalltobookSection,lGeneralBannerSection,lCalculateSection FROM {$tbl_name} where vCode='".$vCode."'";
        $check_file = $obj->MySQLSelect($check_file_query);

        $sectionData = json_decode($check_file[0]['lHowitworkSection'], true);
        $how_it_work_section_arr['title'] = $_POST['how_it_work_section_title'] ?? '';
        $how_it_work_section_arr['desc'] = $_POST['how_it_work_section_desc'] ?? '';
        $how_it_work_section_arr['img'] = $sectionData['img'] ?? '';
        $how_it_work_section = getJsonFromAnArr($how_it_work_section_arr);

        $sectionData = json_decode($check_file[0]['lDownloadappSection'], true);
        $download_section_arr['title'] = $_POST['download_section_title'] ?? '';
        $download_section_arr['subtitle'] = $_POST['download_section_sub_title'] ?? '';
        $download_section_arr['desc'] = $_POST['download_section_desc'] ?? '';
        $download_section_arr['img'] = $sectionData['img'] ?? '';
        $download_section = getJsonFromAnArr($download_section_arr);

        $sectionData = json_decode($check_file[0]['lSecuresafeSection'], true);
        $secure_section_arr['title'] = $_POST['secure_section_title'] ?? '';
        $secure_section_arr['desc'] = $_POST['secure_section_desc'] ?? '';
        $secure_section_arr['img'] = $sectionData['img'] ?? '';
        $secure_section = getJsonFromAnArr($secure_section_arr);

        $sectionData = json_decode($check_file[0]['lCalltobookSection'], true);
        $call_section_arr['title'] = $_POST['call_section_title'] ?? '';
        $call_section_arr['desc'] = $_POST['call_section_desc'] ?? '';
        $call_section_arr['img'] = $sectionData['img'] ?? '';
        $call_section = getJsonFromAnArr($call_section_arr);

        $sectionData = json_decode($check_file[0]['lGeneralBannerSection'], true);
        $general_section_arr['title'] = $_POST['general_section_title'] ?? '';
        $general_section_arr['desc'] = $_POST['general_section_desc'] ?? '';
        $general_section_arr['img'] = $sectionData['img'] ?? '';
        $general_section_arr['img_sec'] = $sectionData['img_sec'] ?? '';
        $general_section = getJsonFromAnArr($general_section_arr);

        $sectionData = json_decode($check_file[0]['lCalculateSection'], true);
        $calculate_section_arr['menu_title'] = $_POST['calculate_section_menu_title'] ?? '';
        $calculate_section_arr['title'] = $_POST['calculate_section_title'] ?? '';
        $calculate_section_arr['desc'] = $_POST['calculate_section_desc'] ?? '';
        $calculate_section_arr['img'] = $sectionData['img'] ?? '';
        $calculate_section_arr = !(empty($sectionData)) ? array_merge($sectionData, $calculate_section_arr) : $calculate_section_arr;
        $calculate_section = getJsonFromAnArr($calculate_section_arr);
    }
}

if (isset($_POST['submit'])) {
    if (SITE_TYPE === 'Demo') {
        // header("Location:home_action.php?success=2");
        header('Location:home_content_ridecx.php?id='.$id.'&success=2');

        exit;
    }

    $q = 'INSERT INTO ';
    $where = '';
    if ('' !== $id) {
        $q = 'UPDATE ';
        $where = " WHERE `vCode` = '".$vCode."'";
    }
    // $call_section = $obj->SqlEscapeString($call_section);

    $query = $q.' `'.$tbl_name."` SET
    `lGeneralBannerSection` = '".$general_section."',
    `lHowitworkSection` = '".$how_it_work_section."',
	`lSecuresafeSection` = '".$secure_section."',
	`lDownloadappSection` = '".$download_section."',
    `lCalculateSection` = '".$calculate_section."',
	`lCalltobookSection` = '".$call_section."'".$where; // die;
    $obj->sql_query($query);
    $id = ('' !== $id) ? $id : $obj->GetInsertId();
    // header("Location:make_action.php?id=".$id.'&success=1');
    if ('Add' === $action) {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INSERT_MSG'];
    } else {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
    }
    header('location:'.$backlink);
}
// for Edit
if ('Edit' === $action) {
    $sql = "SELECT hc.*,lm.vTitle FROM {$tbl_name} as hc LEFT JOIN language_master as lm on lm.vCode = hc.vCode  WHERE lm.iLanguageMasId = '".$id."'";
    // $sql = "SELECT hc.*,lm.vTitle FROM homecontent as hc LEFT JOIN language_master as lm on lm.vCode = hc.vCode  WHERE hc.id = '" . $id . "'";
    $db_data = $obj->MySQLSelect($sql);
    $vLabel = $id;
    if (count($db_data) > 0) {
        foreach ($db_data as $key => $value) {
            $vCode = $value['vCode'];
            $title = $value['vTitle'];
            $eStatus = $value['eStatus'];
            $general_section = json_decode($value['lGeneralBannerSection'], true);
            $how_it_work_section = (array) json_decode($value['lHowitworkSection']);
            $secure_section = json_decode($value['lSecuresafeSection'], true);
            $download_section = json_decode($value['lDownloadappSection'], true);
            $call_section = json_decode($value['lCalltobookSection'], true);
            $calculate_section = json_decode($value['lCalculateSection'], true);
        }
    }
}

$catquery = 'SELECT iVehicleCategoryId,vHomepageLogo,vCategory_EN FROM  `'.$sql_vehicle_category_table_name."` WHERE iParentId = 0 and eStatus = 'Active' ORDER BY iDisplayOrderHomepage";
$vcatdata = $obj->MySQLSelect($catquery);

if (isset($_POST['submit']) && 'submit' === $_POST['submit']) {
    $required = 'required';
} elseif (isset($_POST['catlogo']) && 'catlogo' === $_POST['catlogo']) {
    $required = '';
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title>Admin | Home Content <?php echo $action; ?></title>
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
                            <h2><?php echo $action; ?> Home Content (<?php echo $title; ?>)</h2>
                            <a href="home_content_ridecx.php" class="back_link">
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
                                <input type="hidden" name="backlink" id="backlink" value="home_content_ridecx.php"/>

                                <div class="body-div innersection">
                                    <div class="form-group general_section">
                                        <div class="row"><div class="col-lg-12"><h3>General Banner Section</h3></div></div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Title</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="general_section_title"  id="general_section_title" value="<?php echo $general_section['title']; ?>" placeholder="Title">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Description</label>
                                            </div>
                                            <div class="col-lg-12">
                                                <textarea class="form-control ckeditor" rows="10" name="general_section_desc"  id="general_section_desc"  placeholder="Description"><?php echo $general_section['desc']; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>First Image(Background image)</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <?php if ('' !== $general_section['img']) { ?>
                                                    <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$general_section['img']; ?>" class="innerbg_image"/>
                                                <?php } ?>
                                                <input type="file" class="form-control FilUploader" name="general_section_img"  id="general_section_img" accept=".png,.jpg,.jpeg,.gif">
                                                <br/>
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 609px * 547px.]</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Second Image</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <?php if ('' !== $general_section['img_sec']) { ?>
                                                    <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$general_section['img_sec']; ?>" class="innerbg_image"/>
                                                <?php } ?>
                                                <input type="file" class="form-control FilUploader" name="general_section_img_sec"  id="general_section_img_sec" accept=".png,.jpg,.jpeg,.gif">
                                                <br/>
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 609px * 547px.]</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="body-div innersection">
                                    <div class="form-group">
                                        <div class="row"><div class="col-lg-12"><h3>How It work section</h3></div></div>
                                        <div class="row">

                                            <div class="col-lg-12">
                                                <label>Title<span class="red"> *</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="how_it_work_section_title"  id="how_it_work_section_title" value="<?php echo $how_it_work_section['title']; ?>" placeholder="Title" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Description</label>
                                            </div>
                                            <div class="col-lg-12">
                                                <textarea class="form-control ckeditor" rows="10" name="how_it_work_section_desc"  id="how_it_work_section_desc"  placeholder="Description"><?php echo $how_it_work_section['desc']; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Image</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <?php if ('' !== $how_it_work_section['img']) { ?>
                                                    <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$how_it_work_section['img']; ?>" class="innerbg_image"/>
                                                <?php } ?>
                                                <input type="file" class="form-control FilUploader" name="how_it_work_section_img"  id="how_it_work_section_img" accept=".png,.jpg,.jpeg,.gif">
                                                <br/>
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 493px * 740px.]</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="body-div innersection">
                                    <div class="form-group">
                                        <div class="row"><div class="col-lg-12"><h3>Download Section</h3></div></div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Title<span class="red"> *</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="download_section_title"  id="download_section_title" value="<?php echo $download_section['title']; ?>" placeholder="Title" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Subtitle<span class="red"> *</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="download_section_sub_title"  id="download_section_sub_title" value="<?php echo $download_section['subtitle']; ?>" placeholder="Subtitle" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Description</label>
                                            </div>
                                            <div class="col-lg-12">
                                                <textarea class="form-control ckeditor" rows="10" name="download_section_desc"  id="download_section_desc"  placeholder="Description"><?php echo $download_section['desc']; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Image</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <?php if ('' !== $download_section['img']) { ?>
                                                    <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$download_section['img']; ?>" class="innerbg_image"/>
                                                <?php } ?>
                                                <input type="file" class="form-control FilUploader" name="download_section_img"  id="download_section_img" accept=".png,.jpg,.jpeg,.gif">
                                                <br/>
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 1920px * 405px.]</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="body-div innersection">
                                    <div class="form-group">
                                       <div class="row"><div class="col-lg-12"><h3>Calculate Section</h3></div></div>
                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Menu Title</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <input type="text" class="form-control" name="calculate_section_menu_title"  id="calculate_section_menu_title" value="<?php echo $calculate_section['menu_title']; ?>" placeholder="Menu Title">
                                           </div>
                                       </div>
                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Title<span class="red"> *</span></label>
                                           </div>
                                           <div class="col-lg-6">
                                               <input type="text" class="form-control" name="calculate_section_title"  id="calculate_section_title" value="<?php echo $calculate_section['title']; ?>" placeholder="Title" required>
                                           </div>
                                       </div>
                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Description</label>
                                           </div>
                                           <div class="col-lg-12">
                                               <textarea class="form-control ckeditor" rows="10" name="calculate_section_desc"  id="calculate_section_desc"  placeholder="Description"><?php echo $calculate_section['desc']; ?></textarea>
                                           </div>
                                       </div>
                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Image</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <?php if ('' !== $calculate_section['img']) { ?>
                                                   <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$calculate_section['img']; ?>" class="innerbg_image"/>
                                               <?php } ?>
                                               <input type="file" class="form-control FilUploader" name="calculate_section_img"  id="calculate_section_img" accept=".png,.jpg,.jpeg,.gif,.svg">
                                               <br/>
                                               <span class="notes">[Note: For Better Resolution Upload only image size of 860px * 445px.]</span>
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
                                                <input type="text" class="form-control" name="secure_section_title"  id="secure_section_title" value="<?php echo $secure_section['title']; ?>" placeholder="Title" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Description</label>
                                            </div>
                                            <div class="col-lg-12">
                                                <textarea class="form-control ckeditor" rows="10" name="secure_section_desc"  id="secure_section_desc"  placeholder="Description"><?php echo $secure_section['desc']; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Image</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <?php if ('' !== $secure_section['img']) { ?>
                                                    <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$secure_section['img']; ?>" class="innerbg_image" />
                                                <?php } ?>
                                                <input type="file" class="form-control FilUploader" name="secure_section_img"  id="secure_section_img" accept=".png,.jpg,.jpeg,.gif">
                                                <br/>
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 564px * 570px.]</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="body-div innersection">
                                    <div class="form-group">
                                        <div class="row"><div class="col-lg-12"><h3>Call Section</h3></div></div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Title<span class="red"> *</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="call_section_title"  id="call_section_title" value="<?php echo $call_section['title']; ?>" placeholder="Title" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Description</label>
                                                <h5>[Note: Please use #SUPPORT_PHONE# predefined tags to display the support phone value. Please go to Settings >> General section to change the values of above predefined tags.]</h5>
                                            </div>
                                            <div class="col-lg-12">
                                                <textarea class="form-control ckeditor" rows="10" name="call_section_desc"  id="call_section_desc"  placeholder="Description"><?php echo $call_section['desc']; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Image</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <?php if ('' !== $call_section['img']) { ?>
                                                    <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$call_section['img']; ?>" class="innerbg_image"/>
                                                <?php } ?>
                                                <input type="file" class="form-control FilUploader" name="call_section_img"  id="call_section_img" accept=".png,.jpg,.jpeg,.gif">
                                                <br/>
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 609px * 547px.]</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <input type="submit" class=" btn btn-default" name="submit" id="submit" value="<?php echo $action; ?> Home Content">
                                        <!--<input type="reset" value="Reset" class="btn btn-default">-->
                                        <!-- 									<a href="javascript:void(0);" onclick="reset_form('_home_content_form');" class="btn btn-default">Reset</a> -->
                                        <a href="home_content_ridecx.php" class="btn btn-default back_link">Cancel</a>
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
                    window.location.href = "home_content_ridecx_action.php?id=<?php echo $id; ?>";


<?php } ?>
                if ($("#previousLink").val() == "") { //alert('pre1');
                    referrer = document.referrer;
                    // alert(referrer);
                } else { //alert('pre2');
                    referrer = $("#previousLink").val();
                }

                if (referrer == "") {
                    referrer = "home_content_ridecx.php";
                } else { //alert('hi');
                    //$("#backlink").val(referrer);
                    referrer = "home_content_ridecx.php";
                    // alert($("#backlink").val(referrer));
                }
                $(".back_link").attr('href', referrer);
                //alert($(".back_link").attr('href',referrer));
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
                var fileExtension = ['jpeg', 'jpg', 'png', 'gif'];
                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    alert("Only formats are allowed : " + fileExtension.join(', '));
                    $(this).val('');
                    return false;

                }
            });
             $('.entypo-export').click(function (e) {
                e.stopPropagation();
                var $this = $(this).parent().find('div');
                $(".openHoverAction-class div").not($this).removeClass('active');
                $this.toggleClass('active');
            });
            $(document).on("click", function (e) {
                if ($(e.target).is(".openHoverAction-class,.show-moreOptions,.entypo-export") === false) {
                    $(".show-moreOptions").removeClass("active");
                }
            });
        </script>
    </body>
    <!-- END BODY-->
</html>
