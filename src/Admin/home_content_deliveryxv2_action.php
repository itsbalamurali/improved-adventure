<?php

include_once '../common.php';

$id = $_REQUEST['id'] ?? '';

$success = $_REQUEST['success'] ?? 0;

$action = ('' !== $id) ? 'Edit' : 'Add';

$backlink = $_POST['backlink'] ?? '';

$previousLink = $_POST['backlink'] ?? '';

$message_print_id = $id;

$vCode = $_POST['vCode'] ?? '';

$tbl_name = getAppTypeWiseHomeTable();

// $tbl_name = 'homecontent_deliveryx';

$script = 'home_content';

$header_first_label = $_POST['header_first_label'] ?? '';

$third_sec_desc = $_POST['third_sec_desc'] ?? '';

$third_mid_desc_two1 = $_POST['third_mid_desc_two1'] ?? '';

$third_mid_image_three1 = $_POST['third_mid_image_three1'] ?? '';

$home_banner_left_image = $_POST['home_banner_left_image'] ?? '';

$header_second_label = $_POST['header_second_label'] ?? '';

$third_mid_desc_two = $_POST['third_mid_desc_two'] ?? '';

$home_banner_right_image = $_POST['home_banner_right_image'] ?? '';

$third_sec_title = $_POST['third_sec_title'] ?? '';

$third_mid_title_one = $_POST['third_mid_title_one'] ?? '';

$third_mid_desc_three = $_POST['third_mid_desc_three'] ?? '';

$third_mid_image_two = $_POST['third_mid_image_two'] ?? '';

$third_mid_title_two = $_POST['third_mid_title_two'] ?? '';

$third_mid_desc_one = $_POST['third_mid_desc_one'] ?? '';

$third_mid_image_three = $_POST['third_mid_image_three'] ?? '';

$third_mid_title_one1 = $_POST['third_mid_title_one1'] ?? '';

$taxi_app_right_desc = $_POST['taxi_app_right_desc'] ?? '';

$taxi_app_bg_img = $_POST['taxi_app_bg_img'] ?? '';

$mobile_app_right_title = $_POST['mobile_app_right_title'] ?? '';

$mobile_app_right_desc = $_POST['mobile_app_right_desc'] ?? '';

$taxi_app_left_img = $_POST['taxi_app_left_img'] ?? '';

$third_mid_title_three1 = $_POST['third_mid_title_three1'] ?? '';

$third_mid_title_three = $_POST['third_mid_title_three'] ?? '';

$third_mid_desc_three1 = $_POST['third_mid_desc_three1'] ?? '';

$mobile_app_bg_img1 = $_POST['mobile_app_bg_img1'] ?? '';

$third_mid_title_two1 = $_POST['third_mid_title_two1'] ?? '';

$third_mid_desc_two1 = $_POST['third_mid_desc_two1'] ?? '';

// echo '<prE>'; print_R($_REQUEST); exit;

$eStatus_check = $_POST['eStatus'] ?? 'off';

$eStatus = ('on' === $eStatus_check) ? 'Active' : 'Inactive';

if (isset($_POST['submit'])) {
    $img_arr = $_FILES;

    if (!empty($img_arr)) {
        foreach ($img_arr as $key => $value) {
            if ('safe_section_img_first' === $key || 'safe_section_img_sec' === $key || 'safe_section_img_third' === $key || 'safe_section_img_four' === $key || 'register_section_img_first' === $key || 'register_section_img_sec' === $key) {
                if ('safe_section_img_first' === $key || 'register_section_img_first' === $key) {
                    $second_reg_img = 1;
                }

                if ('safe_section_img_sec' === $key || 'register_section_img_sec' === $key) {
                    $second_reg_img = 2;
                }

                if ('safe_section_img_third' === $key) {
                    $second_reg_img = 3;
                }

                if ('safe_section_img_four' === $key) {
                    $second_reg_img = 4;
                }

                if (!empty($value['name'])) {
                    $img_path = $tconfig['tsite_upload_apptype_page_images_panel'];

                    $image_object = $value['tmp_name'];

                    $img_name = explode('.', $value['name']);

                    $image_name = strtotime(date('H:i:s')).'.'.$img_name[1];

                    sleep(1);

                    if ('how_it_work_section_img' === $key) {
                        $key = 'lHowitworkSection';
                    } elseif ('register_section_img_first' === $key || 'register_section_img_sec' === $key) {
                        $key = 'lBookServiceSection';
                    } elseif ('safe_section_img_first' === $key || 'safe_section_img_sec' === $key || 'safe_section_img_third' === $key || 'safe_section_img_four' === $key) {
                        $key = 'lSecuresafeSection';
                    }

                    $check_file_query = 'SELECT '.$key." FROM {$tbl_name} where vCode='".$vCode."'";

                    $check_file = $obj->MySQLSelect($check_file_query);

                    $sectionData = json_decode($check_file[0][$key], true);

                    if (1 === $second_reg_img) {
                        if ('' !== $message_print_id && '' !== $sectionData['img_first']) {
                            $check_file = $img_path.$template.'/'.$sectionData['img_first'];

                            if ('' !== $check_file && file_exists($check_file)) {
                                @unlink($check_file);
                            }
                        }
                    } elseif (2 === $second_reg_img) {
                        if ('' !== $message_print_id && '' !== $sectionData['img_sec']) {
                            $check_file = $img_path.$template.'/'.$sectionData['img_sec'];

                            if ('' !== $check_file && file_exists($check_file)) {
                                @unlink($check_file);
                            }
                        }
                    } elseif (3 === $second_reg_img) {
                        if ('' !== $message_print_id && '' !== $sectionData['img_third']) {
                            $check_file = $img_path.$template.'/'.$sectionData['img_third'];

                            if ('' !== $check_file && file_exists($check_file)) {
                                @unlink($check_file);
                            }
                        }
                    } elseif (3 === $second_reg_img) {
                        if ('' !== $message_print_id && '' !== $sectionData['img_four']) {
                            $check_file = $img_path.$template.'/'.$sectionData['img_four'];

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

                    $img = $UPLOAD_OBJ->GeneralFileUploadHome($Photo_Gallery_folder, $image_object, $image_name, '', 'svg,png,jpg,jpeg,gif', $vCode);

                    if ('1' === $img[2]) {
                        $_SESSION['success'] = '0';

                        $_SESSION['var_msg'] = $img[1];

                        header('location:'.$backlink);
                    }

                    if (!empty($img[0])) {
                        if (1 === $second_reg_img) {
                            $sectionData['img_first'] = $img[0];
                        } elseif (2 === $second_reg_img) {
                            $sectionData['img_sec'] = $img[0];
                        } elseif (3 === $second_reg_img) {
                            $sectionData['img_third'] = $img[0];
                        } elseif (4 === $second_reg_img) {
                            $sectionData['img_four'] = $img[0];
                        } else {
                            $sectionData['img'] = $img[0];
                        }

                        $sectionDatajson = getJsonFromAnArrWithoutClean($sectionData);

                        // $sql = "UPDATE " . $tbl_name . " SET " . $key . " = '" . $sectionDatajson . "' WHERE `vCode` = '" . $vCode . "'";

                        // $obj->sql_query($sql);

                        $sectionDataUpdate = [];

                        $sectionDataUpdate[$key] = $sectionDatajson;

                        $where = " vCode = '".$vCode."'";

                        $id = $obj->MySQLQueryPerform($tbl_name, $sectionDataUpdate, 'update', $where);
                    }
                }
            } else {
                if (!empty($value['name'])) {
                    $img_path = $tconfig['tsite_upload_apptype_page_images_panel'];

                    $temp_gallery = $img_path.'/';

                    $image_object = $value['tmp_name'];

                    $image_name = $value['name'];

                    $check_file_query = 'SELECT '.$key.' FROM '.$tbl_name." where vCode='".$vCode."'";

                    $check_file = $obj->MySQLSelect($check_file_query);

                    /** 		        if ($message_print_id != "") {.
                      }

                      } */
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
                        $sql = 'UPDATE '.$tbl_name.' SET '.$key." = '".$img[0]."' WHERE `vCode` = '".$vCode."'";

                        $obj->sql_query($sql);
                    }
                }
            }
        }
    }

    $check_file_query = "SELECT lSecuresafeSection,lBookServiceSection  FROM {$tbl_name} where vCode='".$vCode."'";

    $check_file = $obj->MySQLSelect($check_file_query);

    $sectionData = json_decode($check_file[0]['lSecuresafeSection'], true);

    $safe_section_arr['title'] = $_POST['safe_section_title'] ?? '';

    $safe_section_arr['desc'] = $_POST['safe_section_desc'] ?? '';

    $safe_section_arr['title_first'] = $_POST['safe_section_title_first'] ?? '';

    $safe_section_arr['desc_first'] = $_POST['safe_section_desc_first'] ?? '';

    $safe_section_arr['title_sec'] = $_POST['safe_section_title_sec'] ?? '';

    $safe_section_arr['desc_sec'] = $_POST['safe_section_desc_sec'] ?? '';

    $safe_section_arr['title_third'] = $_POST['safe_section_title_third'] ?? '';

    $safe_section_arr['desc_third'] = $_POST['safe_section_desc_third'] ?? '';

    $safe_section_arr['title_four'] = $_POST['safe_section_title_four'] ?? '';

    $safe_section_arr['desc_four'] = $_POST['safe_section_desc_four'] ?? '';

    $safe_section_arr['img_first'] = $sectionData['img_first'] ?? '';

    $safe_section_arr['img_sec'] = $sectionData['img_sec'] ?? '';

    $safe_section_arr['img_third'] = $sectionData['img_third'] ?? '';

    $safe_section_arr['img_four'] = $sectionData['img_four'] ?? '';

    $safe_section = getJsonFromAnArrWithoutClean($safe_section_arr);

    $sectionData = json_decode($check_file[0]['lBookServiceSection'], true);

    $register_section_arr['main_title'] = $_POST['register_section_main_title'] ?? '';

    $register_section_arr['main_subtitle'] = $_POST['register_section_main_subtitle'] ?? '';

    $register_section_arr['main_desc'] = $_POST['register_section_main_desc'] ?? '';

    $register_section_arr['title_first'] = $_POST['register_section_title_first'] ?? '';

    $register_section_arr['title_sec'] = $_POST['register_section_title_sec'] ?? '';

    $register_section_arr['img_first'] = $sectionData['img_first'] ?? '';

    $register_section_arr['img_sec'] = $sectionData['img_sec'] ?? '';

    $register_section = getJsonFromAnArrWithoutClean($register_section_arr);

    $where = " vCode = '".$vCode."'";

    $Update['header_first_label'] = $header_first_label;

    $Update['third_sec_desc'] = $third_sec_desc;

    $Update['third_mid_desc_two1'] = $third_mid_desc_two1;

    $Update['third_mid_desc_one'] = $third_mid_desc_one;

    $Update['header_second_label'] = $header_second_label;

    $Update['third_mid_desc_two'] = $third_mid_desc_two;

    $Update['third_sec_title'] = $third_sec_title;

    $Update['third_mid_title_one'] = $third_mid_title_one;

    $Update['third_mid_desc_three'] = $third_mid_desc_three;

    $Update['third_mid_title_two'] = $third_mid_title_two;

    $Update['third_mid_title_three'] = $third_mid_title_three;

    $Update['third_mid_title_one1'] = $third_mid_title_one1;

    $Update['taxi_app_right_desc'] = $taxi_app_right_desc;

    $Update['mobile_app_right_title'] = $mobile_app_right_title;

    $Update['mobile_app_right_desc'] = $mobile_app_right_desc;

    $Update['third_mid_title_three1'] = $third_mid_title_three1;

    $Update['third_mid_desc_three1'] = $third_mid_desc_three1;

    $Update['third_mid_title_two1'] = $third_mid_title_two1;

    $Update['third_mid_desc_two1'] = $third_mid_desc_two1;

    $Update['lSecuresafeSection'] = $safe_section;

    $Update['lBookServiceSection'] = $register_section;

    /*$third_mid_title_two1 = isset($_POST['third_mid_title_two1']) ? $_POST['third_mid_title_two1'] : '';

    $third_mid_desc_two1 = isset($_POST['third_mid_desc_two1']) ? $_POST['third_mid_desc_two1'] : '';*/

    $id = $obj->MySQLQueryPerform($tbl_name, $Update, 'update', $where);

    /* $q = "INSERT INTO ";

     $where = '';

     if ($id != '') {

         $q = "UPDATE ";

         $where = " WHERE `vCode` = '" . $vCode . "'";

     }

     $query = $q . " `" . $tbl_name . "` SET

     `header_first_label` = '" . $header_first_label . "',

     `third_sec_desc` = '" . $third_sec_desc . "',

     `third_mid_desc_two1` = '" . $third_mid_desc_two1 . "',

     `third_mid_desc_one` = '" . $third_mid_desc_one . "',

     `header_second_label` = '" . $header_second_label . "',

     `third_mid_desc_two` = '" . $third_mid_desc_two . "',

     `third_sec_title` = '" . $third_sec_title . "',

     `third_mid_title_one` = '" . $third_mid_title_one . "',

     `third_mid_desc_three` = '" . $third_mid_desc_three . "',

     `third_mid_title_two` = '" . $third_mid_title_two . "',

     `third_mid_title_three` = '" . $third_mid_title_three . "',

     `third_mid_title_one1` = '" . $third_mid_title_one1 . "',

     `taxi_app_right_desc` = '" . $taxi_app_right_desc . "',

     `mobile_app_right_title` = '" . $mobile_app_right_title . "',

     `mobile_app_right_desc` = '" . $mobile_app_right_desc . "',

     `third_mid_title_three1` = '" . $third_mid_title_three1 . "',

     `third_mid_desc_three1` = '" . $third_mid_desc_three1 . "'" . $where; //die;

     $obj->sql_query($query);





     $id = ($id != '') ? $id : $obj->GetInsertId(); */

    // header("Location:make_action.php?id=".$id.'&success=1');

    if ('Add' === $action) {
        $_SESSION['success'] = '1';

        $_SESSION['var_msg'] = 'Home Content Insert Successfully.';
    } else {
        $_SESSION['success'] = '1';

        $_SESSION['var_msg'] = 'Home Content Updated Successfully.';
    }
    header('location:home_content_deliveryxv2_action.php?iVehicleCategoryId='.$iVehicleCategoryId.'&id='.$id);
    // header("location:" . $backlink);
}

// for Edit

if ('Edit' === $action) {
    $sql = 'SELECT hc.*,lm.vTitle FROM '.$tbl_name." as hc LEFT JOIN language_master as lm on lm.vCode = hc.vCode  WHERE hc.id = '".$id."'";

    $db_data = $obj->MySQLSelect($sql);

    $vLabel = $id;

    if (count($db_data) > 0) {
        foreach ($db_data as $key => $value) {
            $third_mid_image_two1 = $value['third_mid_image_two1'];

            $vCode = $value['vCode'];

            $header_first_label = $value['header_first_label'];

            $third_sec_desc = $value['third_sec_desc'];

            $third_mid_desc_two1 = $value['third_mid_desc_two1'];

            $third_mid_desc_one = $value['third_mid_desc_one'];

            $home_banner_left_image = $value['home_banner_left_image'];

            $header_second_label = $value['header_second_label'];

            $third_mid_desc_two = $value['third_mid_desc_two'];

            $home_banner_right_image = $value['home_banner_right_image'];

            $third_mid_image_three1 = $value['third_mid_image_three1'];

            $third_sec_title = $value['third_sec_title'];

            $third_mid_title_one = $value['third_mid_title_one'];

            $third_mid_desc_three = $value['third_mid_desc_three'];

            $third_mid_image_two = $value['third_mid_image_two'];

            $third_mid_title_two = $value['third_mid_title_two'];

            $third_mid_title_three = $value['third_mid_title_three'];

            $third_mid_image_three = $value['third_mid_image_three'];

            $third_mid_title_one1 = $value['third_mid_title_one1'];

            $taxi_app_right_desc = $value['taxi_app_right_desc'];

            $taxi_app_bg_img = $value['taxi_app_bg_img'];

            $mobile_app_right_title = $value['mobile_app_right_title'];

            $mobile_app_right_desc = $value['mobile_app_right_desc'];

            $taxi_app_left_img = $value['taxi_app_left_img'];

            $third_mid_title_three1 = $value['third_mid_title_three1'];

            $third_mid_desc_three1 = $value['third_mid_desc_three1'];

            $mobile_app_bg_img1 = $value['mobile_app_bg_img1'];

            $eStatus = $value['eStatus'];

            $title = $value['vTitle'];

            $third_mid_title_two1 = $value['third_mid_title_two1'];

            $third_mid_desc_two1 = $value['third_mid_desc_two1'];

            $third_mid_image_three1 = $value['third_mid_image_three1'];

            $safe_section = json_decode($value['lSecuresafeSection'], true);

            $register_section = json_decode($value['lBookServiceSection'], true);
        }
    }
}

?>

<!DOCTYPE html><!--[if IE 8]>

<html lang="en" class="ie8"> <![endif]-->

<!--[if IE 9]>

<html lang="en" class="ie9"> <![endif]-->

<!--[if !IE]><!-->

<html lang="en"> <!--<![endif]-->

<!-- BEGIN HEAD-->

<head>

    <meta charset="UTF-8"/>

    <title>Admin | Home Content <?php echo $action; ?></title>

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

                    <h2><?php echo $action; ?>  Home Content (<?php echo $title; ?>)</h2>

                    <a href="home_content_deliveryxv2.php" class="back_link">

                        <input type="button" value="Back to Listing" class="add-btn">

                    </a>

                </div>

            </div>

            <hr/>

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

                        <input type="hidden" name="backlink" id="backlink" value="homecontent.php"/>

                        <!-- Start Home Header area-->

                        <div class="body-div innersection">

                            <div class="form-group">

                                <div class="row">

                                    <div class="col-lg-12">

                                        <label>Home First Section Title<span class="red"> *</span></label>

                                    </div>

                                    <div class="col-lg-6">

                                        <input type="text" class="form-control" name="header_first_label" id="header_first_label" value="<?php echo $header_first_label; ?>" placeholder="Home First Section Title" >

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-lg-12">

                                        <label>Home First Section Description<span class="red"> *</span></label>

                                    </div>

                                    <div class="col-lg-12">

                                        <textarea class="form-control ckeditor" rows="10" name="third_sec_desc" id="third_sec_desc" placeholder="Home First Section Description" ><?php echo $third_sec_desc; ?></textarea>

                                    </div>

                                </div>



                                <div class="row">

                                    <div class="col-lg-12">

                                        <label>Home First Left DeliveryAll Image<span class="red"> *</span></label>

                                    </div>

                                    <div class="col-lg-6">

                                        <?php // if($third_mid_image_three1 != '') {?>

                                        <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$third_mid_image_three1; ?>" class="innerbg_image"/>

                                        <?php // }?>

                                        <input type="file" class="form-control fileuploader" name="third_mid_image_three1" id="third_mid_image_three1" accept=".png,.jpg,.jpeg,.gif">

                                        <br/>

                                        <span class="notes">[Note: For Better Resolution Upload only image size of 1920px * 844px.]</span>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="body-div innersection">

                            <div class="form-group">

                                <div class="row">

                                    <div class="col-lg-12"><h3>How It Works</h3></div>

                                </div>

                                <div class="row">

                                    <div class="col-lg-12">

                                        <label>Menu Title</label>

                                    </div>

                                    <div class="col-lg-6">

                                        <input type="text" class="form-control" name="safe_section_title" id="safe_section_menu_title" value="<?php echo $safe_section['title']; ?>" placeholder="Menu Title">

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-lg-12">

                                        <label>Main Description</label>

                                    </div>

                                    <div class="col-lg-6">

                                        <textarea class="form-control" rows="5" name="safe_section_desc"><?php echo $safe_section['desc']; ?></textarea>

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-lg-12"><h4>Global Platform service Data</h4></div>

                                    <div class="col-lg-3">

                                        <div class="row">

                                            <div class="col-lg-12">

                                                <label>Title#1</label>

                                            </div>

                                            <div class="col-lg-11">

                                                <input type="text" class="form-control" name="safe_section_title_first" value="<?php echo $safe_section['title_first']; ?>" placeholder="Title">

                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-lg-12">

                                                <label>Description#1</label>

                                            </div>

                                            <div class="col-lg-11">

                                                <textarea class="form-control" rows="5" name="safe_section_desc_first" placeholder="Description"><?php echo $safe_section['desc_first']; ?></textarea>

                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-lg-12">

                                                <label>Image#1</label>

                                            </div>

                                            <div class="col-lg-11">

                                                <?php if ('' !== $safe_section['img_first']) { ?>

                                                    <!-- <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$safe_section['img_first']; ?>" class="innerbg_image"/> -->

                                                    <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=300&src='.$tconfig['tsite_upload_apptype_page_images'].$template.'/'.$safe_section['img_first']; ?>" class="innerbg_image"/>

                                                <?php } ?>

                                                <input type="file" class="form-control" name="safe_section_img_first" value="<?php echo $safe_section['img_first']; ?>">

                                                <span class="notes">[Note: For Better Resolution Upload only image size of 78px * 78px.]</span>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-lg-3">

                                        <div class="row">

                                            <div class="col-lg-12">

                                                <label>Title#2</label>

                                            </div>

                                            <div class="col-lg-11">

                                                <input type="text" class="form-control" name="safe_section_title_sec" value="<?php echo $safe_section['title_sec']; ?>" placeholder="Title">

                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-lg-12">

                                                <label>Description#2</label>

                                            </div>

                                            <div class="col-lg-11">

                                                <textarea class="form-control" rows="5" name="safe_section_desc_sec" placeholder="Description"><?php echo $safe_section['desc_sec']; ?></textarea>

                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-lg-12">

                                                <label>Image#2</label>

                                            </div>

                                            <div class="col-lg-11">

                                                <?php if ('' !== $safe_section['img_sec']) { ?>

                                                    <!-- <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$safe_section['img_sec']; ?>" class="innerbg_image"/> -->

                                                    <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=300&src='.$tconfig['tsite_upload_apptype_page_images'].$template.'/'.$safe_section['img_sec']; ?>" class="innerbg_image"/>

                                                <?php } ?>

                                                <input type="file" class="form-control" name="safe_section_img_sec" value="<?php echo $safe_section['img_sec']; ?>">

                                                <span class="notes">[Note: For Better Resolution Upload only image size of 78px * 78px.]</span>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-lg-3">

                                        <div class="row">

                                            <div class="col-lg-12">

                                                <label>Title#3</label>

                                            </div>

                                            <div class="col-lg-11">

                                                <input type="text" class="form-control" name="safe_section_title_third" value="<?php echo $safe_section['title_third']; ?>" placeholder="Title">

                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-lg-12">

                                                <label>Description#3</label>

                                            </div>

                                            <div class="col-lg-11">

                                                <textarea class="form-control" rows="5" name="safe_section_desc_third" placeholder="Description"><?php echo $safe_section['desc_third']; ?></textarea>

                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-lg-12">

                                                <label>Image#3</label>

                                            </div>

                                            <div class="col-lg-11">

                                                <?php if ('' !== $safe_section['img_third']) { ?>

                                                    <!--  <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$safe_section['img_third_'.$vCode]; ?>" class="innerbg_image"/> -->

                                                    <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=300&src='.$tconfig['tsite_upload_apptype_page_images'].$template.'/'.$safe_section['img_third']; ?>" class="innerbg_image"/>

                                                <?php } ?>

                                                <input type="file" class="form-control" name="safe_section_img_third" value="<?php echo $safe_section['img_third']; ?>">

                                                <span class="notes">[Note: For Better Resolution Upload only image size of 78px * 78px.]</span>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-lg-3">

                                        <div class="row">

                                            <div class="col-lg-12">

                                                <label>Title#4</label>

                                            </div>

                                            <div class="col-lg-11">

                                                <input type="text" class="form-control" name="safe_section_title_four" value="<?php echo $safe_section['title_four']; ?>" placeholder="Title">

                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-lg-12">

                                                <label>Description#4</label>

                                            </div>

                                            <div class="col-lg-11">

                                                <textarea class="form-control" rows="5" name="safe_section_desc_four" placeholder="Description"><?php echo $safe_section['desc_four']; ?></textarea>

                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-lg-12">

                                                <label>Image#4</label>

                                            </div>

                                            <div class="col-lg-11">

                                                <?php if ('' !== $safe_section['img_third']) { ?>

                                                    <!--  <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$safe_section['img_four_'.$vCode]; ?>" class="innerbg_image"/> -->

                                                    <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=300&src='.$tconfig['tsite_upload_apptype_page_images'].$template.'/'.$safe_section['img_four']; ?>" class="innerbg_image"/>

                                                <?php } ?>

                                                <input type="file" class="form-control" name="safe_section_img_four" value="<?php echo $safe_section['img_four']; ?>">

                                                <span class="notes">[Note: For Better Resolution Upload only image size of 78px * 78px.]</span>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-lg-11">

                                        <hr>

                                    </div>

                                </div>

                            </div>

                        </div>



                        <div class="body-div innersection">

                            <div class="form-group">

                                <div class="row">

                                    <div class="col-lg-12">

                                        <label>Home First Section First Text<span class="red"> *</span></label>

                                    </div>

                                    <div class="col-lg-6">

                                        <input type="text" class="form-control" name="third_mid_title_two" id="third_mid_title_two" value="<?php echo $third_mid_title_two; ?>" placeholder="Home Forth Section First Text" >

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-lg-12">

                                        <label>Home First Section Second Text<span class="red"> *</span></label>

                                    </div>

                                    <div class="col-lg-12">

                                        <textarea class="form-control ckeditor" rows="10" name="third_mid_desc_one" id="third_mid_desc_one" placeholder=">Home First Section Second Button Description" ><?php echo $third_mid_desc_one; ?></textarea>

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-lg-12">

                                        <label>Home First Section Image<span class="red"> *</span></label>

                                    </div>

                                    <div class="col-lg-6">

                                        <?php if ('' !== $third_mid_image_three) { ?>

                                            <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$third_mid_image_three; ?>" class="innerbg_image"/>

                                        <?php } ?>

                                        <input type="file" class="form-control fileuploader" name="third_mid_image_three" id="third_mid_image_three" accept=".png,.jpg,.jpeg,.gif">

                                        <br/>

                                        <span class="notes">[Note: For Better Resolution Upload only image size of 30px * 28px.]</span>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="body-div innersection">

                            <div class="form-group">

                                <div class="row">

                                    <div class="col-lg-12">

                                        <label>Home Second Section Title<span class="red"> *</span></label>

                                    </div>

                                    <div class="col-lg-6">

                                        <input type="text" class="form-control" name="third_mid_title_one1" id="third_mid_title_one1" value="<?php echo $third_mid_title_one1; ?>" placeholder="Home Fifth Section Title" >

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-lg-12">

                                        <label>Home Second Section Description<span class="red"> *</span></label>

                                    </div>

                                    <div class="col-lg-12">

                                        <textarea class="form-control ckeditor" rows="10" name="taxi_app_right_desc" id="taxi_app_right_desc" placeholder="Home Fifth Section Description" ><?php echo $taxi_app_right_desc; ?></textarea>

                                    </div>

                                </div>



                                <div class="row">

                                    <div class="col-lg-12">

                                        <label>Home Second Section Banner Image<span class="red"> *</span></label>

                                    </div>

                                    <div class="col-lg-6">

                                        <?php if ('' !== $taxi_app_bg_img) { ?>

                                            <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$taxi_app_bg_img; ?>" class="innerbg_image"/>

                                        <?php } ?>

                                        <input type="file" class="form-control fileuploader" name="taxi_app_bg_img" id="taxi_app_bg_img" accept=".png,.jpg,.jpeg,.gif">

                                        <br/>

                                        <span class="notes">[Note: For Better Resolution Upload only image size of 1027px * 520px.]</span>

                                    </div>

                                </div>

                            </div>

                        </div>



                        <div class="body-div innersection">

                            <div class="form-group">

                                <div class="row">

                                    <div class="col-lg-12">

                                        <h3>Register section</h3>

                                    </div>

                                </div>

                                <div  class="row">

                                    <div class="col-lg-12">

                                        <label>Title<span class="red"> *</span></label>

                                    </div>

                                    <div class="col-lg-6">

                                        <input type="text" class="form-control" name="register_section_main_title" id="register_section_main_title" value="<?php echo $register_section['main_title']; ?>" placeholder="Title" >

                                    </div>

                                </div>

                                <div style="display:none;" class="row">

                                    <div class="col-lg-12">

                                        <label>Subtitle<span class="red"> *</span></label>

                                    </div>

                                    <div class="col-lg-6">

                                        <input type="text" class="form-control" name="register_section_main_subtitle" id="register_section_main_subtitle" value="<?php echo $register_section['main_subtitle']; ?>" placeholder="Subtitle" >

                                    </div>

                                </div>

                                <div  class="row">

                                    <div class="col-lg-12">

                                        <label>Description<span class="red"> *</span></label>

                                    </div>

                                    <div class="col-lg-6">

                                        <input type="text" class="form-control" name="register_section_main_desc" id="register_section_main_desc" value="<?php echo $register_section['main_desc']; ?>" placeholder="Description" >

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-lg-12">

                                        <label>Image Title 1<span class="red"> *</span></label>

                                    </div>

                                    <div class="col-lg-6">

                                        <input type="text" class="form-control" name="register_section_title_first" id="register_section_title_first" value="<?php echo $register_section['title_first']; ?>" placeholder="Title" >

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-lg-12">

                                        <label>Image 1</label>

                                    </div>

                                    <div class="col-lg-6">

                                        <?php if ('' !== $register_section['img_first']) { ?>

                                            <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=300&src='.$tconfig['tsite_upload_apptype_page_images'].$template.'/'.$register_section['img_first']; ?>" class="innerbg_image"/>

                                        <?php } ?>

                                        <input type="file" class="form-control FilUploader" name="register_section_img_first" id="register_section_img_first" accept=".png,.jpg,.jpeg,.gif">

                                        <br/>

                                        <span class="notes">[Note: For Better Resolution Upload only image size of 450px * 520px.]</span>

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-lg-12">

                                        <label>Image Title 2<span class="red"> *</span></label>

                                    </div>

                                    <div class="col-lg-6">

                                        <input type="text" class="form-control" name="register_section_title_sec" id="register_section_title_sec" value="<?php echo $register_section['title_sec']; ?>" placeholder="Title" >

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-lg-12">

                                        <label>Image 2</label>

                                    </div>

                                    <div class="col-lg-6">

                                        <?php if ('' !== $register_section['img_sec']) { ?>

                                            <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=300&src='.$tconfig['tsite_upload_apptype_page_images'].$template.'/'.$register_section['img_sec']; ?>" class="innerbg_image"/>

                                        <?php } ?>

                                        <input type="file" class="form-control FilUploader" name="register_section_img_sec" id="register_section_img_sec" accept=".png,.jpg,.jpeg,.gif">

                                        <br/>

                                        <span class="notes">[Note: For Better Resolution Upload only image size of 450px * 520px.]</span>

                                    </div>

                                </div>

                            </div>

                        </div><!-- /*--------------------- general_section --------------------*/-->

                        <!-- End Home Header area-->

                        <div class="row" style="display: none;">

                            <div class="col-lg-12">

                                <label>Status</label>

                            </div>

                            <div class="col-lg-6">

                                <div class="make-switch" data-on="success" data-off="warning">

                                    <input type="checkbox" name="eStatus" <?php echo ('' !== $id && 'Inactive' === $eStatus) ? '' : 'checked'; ?>/>

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-lg-12">

                                <input type="submit" class=" btn btn-default" name="submit" id="submit" value="<?php echo $action; ?> Home Content">

                                <input type="reset" value="Reset" class="btn btn-default">

                                <!-- 									<a href="javascript:void(0);" onclick="reset_form('_home_content_form');" class="btn btn-default">Reset</a> -->

                                <a href="home_content_deliveryxv2.php" class="btn btn-default back_link">Cancel</a>

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

        if ($("#previousLink").val() == "") { //alert('pre1');

            referrer = document.referrer;

            // alert(referrer);

        } else { //alert('pre2');

            referrer = $("#previousLink").val();

        }



        if (referrer == "") {

            referrer = "home_content_deliveryxv2.php";

        } else { //alert('hi');

            $("#backlink").val(referrer);

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

    $(".fileuploader").change(function () {

        var fileExtension = ['jpeg', 'jpg', 'png', 'gif'];

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