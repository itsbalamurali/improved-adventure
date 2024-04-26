<?php
include_once '../common.php';

$id = $_REQUEST['id'] ?? '';
$success = $_REQUEST['success'] ?? 0;
$action = ('' !== $id) ? 'Edit' : 'Add';
$backlink = $_POST['backlink'] ?? '';
$previousLink = '';

$message_print_id = $id;
$vCode = $_POST['vCode'] ?? '';
$var_msg = $_REQUEST['var_msg'] ?? '';

if (isset($_REQUEST['goback'])) {
    $goback = $_REQUEST['goback'];
}

$eStatus_check = $_POST['eStatus'] ?? 'off';
$eStatus = ('on' === $eStatus_check) ? 'Active' : 'Inactive';

$cubexthemeonh = 0;
if ('Yes' === $THEME_OBJ->isRideCXv2ThemeActive()) {
    $cubexthemeonh = 1;
}

if (1 === $cubexthemeonh) {
    $script = 'homecontent_cubejekx';
    $tbl_name = getAppTypeWiseHomeTable();

    $iLanguageMasId = 0;

    if (empty($vCode)) {
        $sql = "SELECT hc.vCode, lm.iLanguageMasId FROM {$tbl_name} as hc LEFT JOIN language_master as lm on lm.vCode = hc.vCode  WHERE lm.iLanguageMasId = '".$id."'";
        $db_data = $obj->MySQLSelect($sql);
        $vCode = $db_data[0]['vCode'];
        $iLanguageMasId = $db_data[0]['iLanguageMasId'];
    }

    $img_arr = $_FILES;
    /*
        echo"<pre>";

        print_R($_POST);print_R($_FILES);die;*/

    // $img_arr['call_section_img'] = $_FILES['call_section_img'];
    if (!empty($img_arr)) {
        if (SITE_TYPE === 'Demo') {
            // header("Location:home_action.php?success=2");
            header('Location:home_content_ridecx.php?id='.$id.'&success=2');

            exit;
        }
        // $img_arr['call_section_img'] = $_FILES['call_section_img'];
        foreach ($img_arr as $key => $value) {
            // if($key == 'vHomepageLogo') continue;
            if (!empty($value['name'])) {
                $img_path = $tconfig['tsite_upload_apptype_page_images_panel'];
                // $temp_gallery = $img_path . '/';
                $image_object = $value['tmp_name'];
                $img_name = explode('.', $value['name']);
                $image_name = $img_name[0].'_'.strtotime(date('H:i:s')).'.'.$img_name[1];

                if ('safety_section_img_first' === $key) {
                    $second_safety_img = 1;
                }
                if ('safety_section_img_sec' === $key) {
                    $second_safety_img = 2;
                }

                if ('register_section_img_first' === $key) {
                    $second_reg_img = 1;
                }
                if ('register_section_img_sec' === $key) {
                    $second_reg_img = 2;
                }

                if ('register_section_img_first' === $key) {
                    $img_str = 'img_first_';
                } elseif ('register_section_img_sec' === $key) {
                    $img_str = 'img_sec_';
                } elseif ('safety_section_img_first' === $key) {
                    $img_str = 'img_first_';
                } elseif ('safety_section_img_sec' === $key) {
                    $img_str = 'img_sec_';
                } else {
                    $img_str = 'img';
                }

                if ('how_it_work_section_img' === $key) {
                    $key = 'lHowitworkSection';
                } elseif ('travel_section_img' === $key) {
                    $key = 'lTravelSection';
                } elseif ('pool_section_img' === $key) {
                    $key = 'lPoolSection';
                } elseif ('call_section_img' === $key) {
                    $key = 'lCalltobookSection';
                } elseif ('general_section_img' === $key) {
                    $key = 'lGeneralBannerSection';
                } elseif ('general_section_img_sec' === $key) {
                    $key = 'lGeneralBannerSection';
                } elseif ('register_section_img_first' === $key || 'register_section_img_sec' === $key) {
                    $key = 'lRegisterSection';
                } elseif ('safety_section_img_first' === $key || 'safety_section_img_sec' === $key) {
                    $key = 'lSecuresafeSection';
                }

                // For How it works Added By PJ
                for ($i = 1; $i <= 4; ++$i) {
                    if ($key === 'how_it_work_section_hiw_img'.$i) {
                        $key = 'lHowitworkSection';
                        $img_str = 'hiw_img'.$i;
                    }
                }

                $check_file_query = 'SELECT '.$key." FROM {$tbl_name} where vCode='".$vCode."'";
                $check_file = $obj->MySQLSelect($check_file_query);
                $sectionData = json_decode($check_file[0][$key], true);

                if (1 === $second_safety_img) {
                    if ('' !== $message_print_id && '' !== $sectionData['img_first']) {
                        $check_file = $img_path.$template.'/'.$sectionData['img_first'];
                        if ('' !== $check_file && file_exists($check_file)) {
                            @unlink($check_file);
                        }
                    }
                } elseif (2 === $second_safety_img) {
                    if ('' !== $message_print_id && '' !== $sectionData['img_sec']) {
                        $check_file = $img_path.$template.'/'.$sectionData['img_sec'];
                        if ('' !== $check_file && file_exists($check_file)) {
                            @unlink($check_file);
                        }
                    }
                } elseif (1 === $second_reg_img) {
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
                } else {
                    /** if ($message_print_id != "" && $sectionData['img']!='') {.
                        }
                    }  */
                    if ('' !== $message_print_id && '' !== $sectionData[$img_str]) {
                        $check_file = $img_path.$template.'/'.$sectionData[$img_str];

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
                // print_R($img);die;
                if ('1' === $img[2]) {
                    $_SESSION['success'] = '0';
                    $_SESSION['var_msg'] = $img[1];
                    header('location:'.$backlink);
                }

                if (!empty($img[0])) {
                    if (1 === $second_gen_img) {
                        $sectionData['img_sec'] = $img[0];
                    } elseif (1 === $second_safety_img) {
                        $sectionData['img_first'] = $img[0];
                    } elseif (2 === $second_safety_img) {
                        $sectionData['img_sec'] = $img[0];
                    } elseif (1 === $second_reg_img) {
                        $sectionData['img_first'] = $img[0];
                    } elseif (2 === $second_reg_img) {
                        $sectionData['img_sec'] = $img[0];
                    } else {
                        // $sectionData['img'] = $img[0];
                        $sectionData[$img_str] = $img[0];
                    }
                    $sectionDatajson = $obj->getJsonFromAnArr($sectionData);

                    echo $sql = 'UPDATE '.$tbl_name.' SET '.$key." = '".$sectionDatajson."' WHERE `vCode` = '".$vCode."'";
                    echo '<br/>';
                    $obj->sql_query($sql);
                }
            }
        }
    }

    if (isset($_POST['submit'])) {
        $check_file_query = "SELECT lHowitworkSection,lTravelSection,lSecuresafeSection,lPoolSection,lCalltobookSection,lGeneralBannerSection,lCalculateSection,lRegisterSection FROM {$tbl_name} where vCode='".$vCode."'";
        $check_file = $obj->MySQLSelect($check_file_query);

        $sectionData = json_decode($check_file[0]['lGeneralBannerSection'], true);
        $general_section_arr['title'] = $_POST['general_section_title'] ?? '';
        $general_section_arr['desc'] = $_POST['general_section_desc'] ?? '';
        $general_section_arr['img'] = $sectionData['img'] ?? '';
        $general_section_arr['img_sec'] = $sectionData['img_sec'] ?? '';
        $general_section_arr = !(empty($sectionData)) ? array_merge($sectionData, $general_section_arr) : $general_section_arr;
        $general_section = $obj->getJsonFromAnArr($general_section_arr);

        $sectionData = json_decode($check_file[0]['lHowitworkSection'], true);
        $how_it_work_section_arr['title'] = $_POST['how_it_work_section_title'] ?? '';
        $how_it_work_section_arr['desc'] = $_POST['how_it_work_section_desc'] ?? '';
        $how_it_work_section_arr['img'] = $sectionData['img'] ?? '';
        $how_it_work_section_arr = !(empty($sectionData)) ? array_merge($sectionData, $how_it_work_section_arr) : $how_it_work_section_arr;

        // For How it works Added By PJ 25 Sep 2019
        for ($i = 1; $i <= 4; ++$i) {
            $how_it_work_section_arr['hiw_title'.$i] = $_POST['how_it_work_section_hiw_title'.$i] ?? '';
            $how_it_work_section_arr['hiw_desc'.$i] = $_POST['how_it_work_section_hiw_desc'.$i] ?? '';
        }

        $how_it_work_section = $obj->getJsonFromAnArr($how_it_work_section_arr);

        $sectionData = json_decode($check_file[0]['lTravelSection'], true);
        $travel_section_arr['title'] = $_POST['travel_section_title'] ?? '';
        $travel_section_arr['desc'] = $_POST['travel_section_desc'] ?? '';
        $travel_section_arr['img'] = $sectionData['img'] ?? '';
        $travel_section = $obj->getJsonFromAnArr($travel_section_arr);

        $sectionData = json_decode($check_file[0]['lPoolSection'], true);
        // $pool_section_arr['menu_title'] = isset($_POST['pool_section_menu_title']) ? $_POST['pool_section_menu_title'] : '';
        $pool_section_arr['title'] = $_POST['pool_section_title'] ?? '';
        $pool_section_arr['desc'] = $_POST['pool_section_desc'] ?? '';
        $pool_section_arr['img'] = $sectionData['img'] ?? '';
        $pool_section_arr = !(empty($sectionData)) ? array_merge($sectionData, $pool_section_arr) : $pool_section_arr;
        $pool_section = $obj->getJsonFromAnArr($pool_section_arr);

        $sectionData = json_decode($check_file[0]['lSecuresafeSection'], true);
        $safety_section_arr['main_title'] = $_POST['safety_section_main_title'] ?? '';
        $safety_section_arr['main_subtitle'] = $_POST['safety_section_main_subtitle'] ?? '';
        $safety_section_arr['main_desc'] = $_POST['safety_section_main_desc'] ?? '';
        $safety_section_arr['title_first'] = $_POST['safety_section_title_first'] ?? '';
        $safety_section_arr['title_sec'] = $_POST['safety_section_title_sec'] ?? '';
        $safety_section_arr['safety_section_description_first'] = $_POST['safety_section_description_first'] ?? '';
        $safety_section_arr['safety_section_description_second'] = $_POST['safety_section_description_second'] ?? '';

        $safety_section_arr['img_first'] = $sectionData['img_first'] ?? '';
        $safety_section_arr['img_sec'] = $sectionData['img_sec'] ?? '';
        $safety_section = $obj->getJsonFromAnArr($safety_section_arr);

        $sectionData = json_decode($check_file[0]['lCalltobookSection'], true);
        $call_section_arr['title'] = $_POST['call_section_title'] ?? '';
        $call_section_arr['desc'] = $_POST['call_section_desc'] ?? '';
        $call_section_arr['img'] = $sectionData['img'] ?? '';
        $call_section = $obj->getJsonFromAnArr($call_section_arr);

        $sectionData = json_decode($check_file[0]['lRegisterSection'], true);
        $register_section_arr['main_title'] = $_POST['register_section_main_title'] ?? '';
        $register_section_arr['main_subtitle'] = $_POST['register_section_main_subtitle'] ?? '';
        $register_section_arr['main_desc'] = $_POST['register_section_main_desc'] ?? '';
        $register_section_arr['title_first'] = $_POST['register_section_title_first'] ?? '';
        $register_section_arr['title_sec'] = $_POST['register_section_title_sec'] ?? '';
        $register_section_arr['img_first'] = $sectionData['img_first'] ?? '';
        $register_section_arr['img_sec'] = $sectionData['img_sec'] ?? '';
        $register_section = $obj->getJsonFromAnArr($register_section_arr);
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
    `lTravelSection` = '".$travel_section."',
    `lPoolSection` = '".$pool_section."',
    `lRegisterSection` = '".$register_section."',
	`lSecuresafeSection` = '".$safety_section."',
	`lCalltobookSection` = '".$call_section."'".$where;
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
    $db_data = $obj->MySQLSelect($sql);
    $vLabel = $id;
    if (count($db_data) > 0) {
        foreach ($db_data as $key => $value) {
            $vCode = $value['vCode'];
            $title = $value['vTitle'];
            $eStatus = $value['eStatus'];
            $general_section = json_decode($value['lGeneralBannerSection'], true);
            $how_it_work_section = (array) json_decode($value['lHowitworkSection']);
            $safety_section = json_decode($value['lSecuresafeSection'], true);
            $call_section = json_decode($value['lCalltobookSection'], true);
            // $calculate_section = json_decode($value['lCalculateSection'],true);
            $travel_section = json_decode($value['lTravelSection'], true);
            $pool_section = json_decode($value['lPoolSection'], true);
            $register_section = json_decode($value['lRegisterSection'], true);
        }
    }
}

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
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 609px * 1903px.]</span>
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

                                        <!-- How It Works Blocks -->
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <h3>How It Works Blocks</h3>
                                                <p>(Note : Title and Description are required for show this blocks on page..)</p>
                                                <hr/>
                                            </div>

                                            <?php for ($i = 1; $i <= 4; ++$i) { ?>
                                                <div class="col-lg-3">
                                                    <!-- Title -->
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Block Title <?php echo $i; ?></label>
                                                        </div>
                                                        <div class="col-lg-11">
                                                            <input type="text" class="form-control" name="how_it_work_section_hiw_title<?php echo $i; ?>"  id="how_it_work_section_hiw_title<?php echo $i; ?>" value="<?php echo $how_it_work_section['hiw_title'.$i]; ?>" placeholder="Title">
                                                        </div>
                                                    </div>

                                                    <!-- Description  -->
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Block Description <?php echo $i; ?></label>
                                                        </div>
                                                        <div class="col-lg-11">
                                                            <textarea class="form-control" name="how_it_work_section_hiw_desc<?php echo $i; ?>"  id="how_it_work_section_hiw_desc<?php echo $i; ?>" value="<?php echo $how_it_work_section['hiw_desc'.$i]; ?>" placeholder="Description" rows="3"><?php echo $how_it_work_section['hiw_desc'.$i]; ?></textarea>
                                                        </div>
                                                    </div>

                                                    <!-- Image  -->
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Block Image <?php echo $i; ?></label>
                                                        </div>
                                                        <div class="col-lg-11">
                                                            <?php if ('' !== $how_it_work_section['hiw_img'.$i]) { ?>
                                                               <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=200&src='.$tconfig['tsite_upload_apptype_page_images'].$template.'/'.$how_it_work_section['hiw_img'.$i]; ?>" class="innerbg_image"/ style="max-height:100px;">

                                                           <?php } ?>
                                                           <input type="file" class="form-control FilUploader" name="how_it_work_section_hiw_img<?php echo $i; ?>"  id="how_it_work_section_hiw_img<?php echo $i; ?>" accept=".png,.jpg,.jpeg,.gif,.svg">
                                                           <br/>
                                                           <span class="notes">[Note: For Better Resolution Upload only image size of 50px * 50px.]</span>
                                                       </div>
                                                   </div>
                                               </div>
                                           <?php } ?>
                                       </div>
                                       <!-- How It Works Blocks End -->
                                    </div>
                                </div>
                                <div class="body-div innersection">
                                    <div class="form-group">
                                        <div class="row"><div class="col-lg-12"><h3>Travel Section</h3></div></div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Title<span class="red"> *</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="travel_section_title"  id="travel_section_title" value="<?php echo $travel_section['title']; ?>" placeholder="Title" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Description</label>
                                            </div>
                                            <div class="col-lg-12">
                                                <textarea class="form-control ckeditor" rows="10" name="travel_section_desc"  id="travel_section_desc"  placeholder="Description"><?php echo $travel_section['desc']; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Image</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <?php if ('' !== $travel_section['img']) { ?>
                                                    <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$travel_section['img']; ?>" class="innerbg_image" />
                                                <?php } ?>
                                                <input type="file" class="form-control FilUploader" name="travel_section_img"  id="travel_section_img" accept=".png,.jpg,.jpeg,.gif">
                                                <br/>
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 564px * 570px.]</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="body-div innersection">
                                    <div class="form-group">
                                       <div class="row"><div class="col-lg-12"><h3>Pool Section</h3></div></div>
                                      <!--  <div class="row">
                                           <div class="col-lg-12">
                                               <label>Menu Title</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <input type="text" class="form-control" name="pool_section_menu_title"  id="pool_section_menu_title" value="<?php echo $pool_section['menu_title']; ?>" placeholder="Menu Title">
                                           </div>
                                       </div> -->
                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Title<span class="red"> *</span></label>
                                           </div>
                                           <div class="col-lg-6">
                                               <input type="text" class="form-control" name="pool_section_title"  id="pool_section_title" value="<?php echo $pool_section['title']; ?>" placeholder="Title" required>
                                           </div>
                                       </div>
                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Description</label>
                                           </div>
                                           <div class="col-lg-12">
                                               <textarea class="form-control ckeditor" rows="10" name="pool_section_desc"  id="pool_section_desc"  placeholder="Description"><?php echo $pool_section['desc']; ?></textarea>
                                           </div>
                                       </div>
                                       <div class="row">
                                           <div class="col-lg-12">
                                               <label>Image</label>
                                           </div>
                                           <div class="col-lg-6">
                                               <?php if ('' !== $pool_section['img']) { ?>
                                                   <img src="<?php echo $tconfig['tsite_upload_apptype_page_images'].$template.'/'.$pool_section['img']; ?>" class="innerbg_image"/>
                                               <?php } ?>
                                               <input type="file" class="form-control FilUploader" name="pool_section_img"  id="pool_section_img" accept=".png,.jpg,.jpeg,.gif,.svg">
                                               <br/>
                                               <span class="notes">[Note: For Better Resolution Upload only image size of 860px * 445px.]</span>
                                           </div>
                                       </div>
                                    </div>
                                 </div>

                                <div class="body-div innersection">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <h3>Safety section</h3>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Title<span class="red"> *</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="safety_section_main_title" id="safety_section_main_title" value="<?php echo $safety_section['main_title']; ?>" placeholder="Title" required>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Description<span class="red"> *</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <textarea class="form-control ckeditor" rows="10" name="safety_section_main_desc"  id="safety_section_main_desc"  placeholder="Description"><?php echo $safety_section['main_desc']; ?></textarea>
                                               <!--  <input type="text" class="form-control" name="safety_section_main_desc" id="safety_section_main_desc" value="<?php echo $safety_section['main_desc']; ?>" placeholder="Description" required> -->
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Image Title 1<span class="red"> *</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="safety_section_title_first" id="safety_section_title_first" value="<?php echo $safety_section['title_first']; ?>" placeholder="Title" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Image 1</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <?php if ('' !== $safety_section['img_first']) { ?>
                                                    <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=300&src='.$tconfig['tsite_upload_apptype_page_images'].$template.'/'.$safety_section['img_first']; ?>" class="innerbg_image" />
                                                <?php } ?>
                                                <input type="file" class="form-control FilUploader" name="safety_section_img_first" id="safety_section_img_first" accept=".png,.jpg,.jpeg,.gif">
                                                <br />
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 450px * 520px.]</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Image Description 1<span class="red"> *</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <textarea class="form-control ckeditor" rows="10" name="safety_section_description_first"  id="safety_section_description_first"  placeholder="Description"><?php echo $safety_section['safety_section_description_first']; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-lg-12">
                                                <label>Image Title 2<span class="red"> *</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="safety_section_title_sec" id="safety_section_title_sec" value="<?php echo $safety_section['title_sec']; ?>" placeholder="Title" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Image 2</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <?php if ('' !== $safety_section['img_sec']) { ?>
                                                    <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=300&src='.$tconfig['tsite_upload_apptype_page_images'].$template.'/'.$safety_section['img_sec']; ?>" class="innerbg_image" />
                                                <?php } ?>
                                                <input type="file" class="form-control FilUploader" name="safety_section_img_sec" id="safety_section_img_sec" accept=".png,.jpg,.jpeg,.gif">
                                                <br />
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 450px * 520px.]</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Image Description 2<span class="red"> *</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <textarea class="form-control ckeditor" rows="10" name="safety_section_description_second"  id="safety_section_description_second"  placeholder="Description"><?php echo $safety_section['safety_section_description_second']; ?></textarea>
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

                                <div class="body-div innersection">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <h3>Register Section</h3>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Title<span class="red"> *</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="register_section_main_title" id="register_section_main_title" value="<?php echo $register_section['main_title']; ?>" placeholder="Title" required>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Description<span class="red"> *</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <textarea class="form-control ckeditor" rows="10" name="register_section_main_desc"  id="register_section_main_desc"  placeholder="Description"><?php echo $register_section['main_desc']; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Image Title 1<span class="red"> *</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="register_section_title_first" id="register_section_title_first" value="<?php echo $register_section['title_first']; ?>" placeholder="Title" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Image 1</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <?php if ('' !== $register_section['img_first']) { ?>
                                                    <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=300&src='.$tconfig['tsite_upload_apptype_page_images'].$template.'/'.$register_section['img_first']; ?>" class="innerbg_image" />
                                                <?php } ?>
                                                <input type="file" class="form-control FilUploader" name="register_section_img_first" id="register_section_img_first" accept=".png,.jpg,.jpeg,.gif">
                                                <br />
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 450px * 520px.]</span>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-lg-12">
                                                <label>Image Title 2<span class="red"> *</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="register_section_title_sec" id="register_section_title_sec" value="<?php echo $register_section['title_sec']; ?>" placeholder="Title" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Image 2</label>
                                            </div>
                                            <div class="col-lg-6">
                                                <?php if ('' !== $register_section['img_sec']) { ?>
                                                    <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=300&src='.$tconfig['tsite_upload_apptype_page_images'].$template.'/'.$register_section['img_sec']; ?>" class="innerbg_image" />
                                                <?php } ?>
                                                <input type="file" class="form-control FilUploader" name="register_section_img_sec" id="register_section_img_sec" accept=".png,.jpg,.jpeg,.gif">
                                                <br />
                                                <span class="notes">[Note: For Better Resolution Upload only image size of 450px * 520px.]</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <input type="submit" class=" btn btn-default" name="submit" id="submit" value="<?php echo $action; ?> Home Content">
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
            window.location.href = "home_content_ridecxv2_action.php?id=<?php echo $id; ?>";
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
