<?php
include_once '../common.php';

if (!$userObj->hasPermission('manage-home-page-content')) {
    $userObj->redirect();
}

$sql_vehicle_category_table_name = getVehicleCategoryTblName();
$iLanguageId = $id = $_REQUEST['id'] ?? '';

if (empty($id)) {
    $sql = "SELECT iLanguageMasId FROM language_master WHERE vCode = '".$default_lang."'";
    $language_master = $obj->MySQLSelect($sql);
    $iLanguageId = $id = $language_master[0]['iLanguageMasId'];
}

$success = $_REQUEST['success'] ?? 0;
$action = ('' !== $id) ? 'Edit' : 'Add';
$backlink = $_POST['backlink'] ?? '';
$previousLink = '';
// $previousLink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
$message_print_id = $id;
$vCode = $_POST['vCode'] ?? '';
$tbl_name = 'homecontent';
$var_msg = $_REQUEST['var_msg'] ?? '';
$third_mid_image_three1 = $third_mid_title_three1 = $third_mid_title_three = $third_mid_desc_three1 = $mobile_app_bg_img1 = $third_mid_desc_one1 = '';
if (isset($_REQUEST['goback'])) {
    $goback = $_REQUEST['goback'];
}
$eStatus_check = $_POST['eStatus'] ?? 'off';
$eStatus = ('on' === $eStatus_check) ? 'Active' : 'Inactive';
if (isset($_POST['removeicon']) && 'remove' === $_POST['removeicon']) {
    if (SITE_TYPE === 'Demo') {
        header('Location:home_content_prosp_action.php?id='.$id.'&success=2');

        exit;
    }
    $_POST['removeicon'] = '';
    $removeiconid = $_POST['removeid'] ?? 0;
    $img_path = $tconfig['tsite_upload_home_page_service_images_panel'];
    $temp_gallery = $img_path.'/';
    $check_file_query = 'SELECT vHomepageLogoOurServices FROM '.$sql_vehicle_category_table_name." where iVehicleCategoryId='".$removeiconid."'";
    $check_file = $obj->MySQLSelect($check_file_query);
    if (!empty($check_file[0]['vHomepageLogoOurServices'])) {
        $check_file = $img_path.'/'.$check_file[0]['vHomepageLogoOurServices'];
        if ('' !== $check_file && file_exists($check_file)) {
            @unlink($check_file);
        }
        $sql = 'UPDATE '.$sql_vehicle_category_table_name." SET vHomepageLogoOurServices='' WHERE iVehicleCategoryId = '".$removeiconid."'";
        $obj->sql_query($sql);
    }
    $_SESSION['success'] = '1';
    $_SESSION['var_msg'] = $langage_lbl_admin['LBL_ICON_RMOVE_SUCESSFULLY'];
}
$script = 'homecontent';
$tbl_name = getAppTypeWiseHomeTable();
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
if (!empty($img_arr)) {
    if (SITE_TYPE === 'Demo') {
        header('Location:home_content_prosp_action.php?id='.$id.'&success=2');

        exit;
    }
    foreach ($img_arr as $key => $value) {
        if ('vHomepageLogoOurServices' === $key) {
            continue;
        }
        if (!empty($value['name'])) {
            $img_path = $tconfig['tsite_upload_apptype_page_images_panel'];
            // $temp_gallery = $img_path . '/';
            $image_object = $value['tmp_name'];
            $img_name = explode('.', $value['name']);
            $image_name = strtotime(date('H:i:s')).'.'.$img_name[count($img_name) - 1];
            sleep(1);
            $second_gen_img = $second_down_img = $second_reg_img = 0;
            if ('general_section_img_sec' === $key) {
                $second_gen_img = 1;
            }
            if ('download_section_img_first' === $key) {
                $second_down_img = 1;
            }
            if ('download_section_img_sec' === $key) {
                $second_down_img = 2;
            }
            if ('download_section_img_third' === $key) {
                $second_down_img = 3;
            }
            if ('register_section_img_first' === $key) {
                $second_reg_img = 1;
            }
            if ('register_section_img_sec' === $key) {
                $second_reg_img = 2;
            }
            $img_str = 'img_';
            // if ($key == 'how_it_work_section_img') $key = 'lHowitworkSection';
            if ('download_section_img' === $key || 'download_section_img_first' === $key || 'download_section_img_sec' === $key || 'download_section_img_third' === $key) {
                $key = 'lDownloadappSection';
            } elseif ('secure_section_img' === $key) {
                $key = 'lSecuresafeSection';
            } elseif ('call_section_img' === $key) {
                $key = 'lCalltobookSection';
            } elseif ('general_section_img' === $key) {
                $key = 'lGeneralBannerSection';
            } elseif ('general_section_img_sec' === $key) {
                $key = 'lGeneralBannerSection';
            } elseif ('register_section_img_first' === $key || 'register_section_img_sec' === $key) {
                $key = 'lBookServiceSection';
            }
            for ($i = 1; $i <= 4; ++$i) {
                if ($key === 'how_it_work_section_hiw_img'.$i) {
                    $key = 'lHowitworkSection';
                    $img_str = 'hiw_img'.$i.'_';
                }
            }

            for ($i = 1; $i <= 2; ++$i) {
                if ($key === 'call_section_img_'.$i) {
                    $key = 'lCalltobookSection';
                    $img_str = 'img'.$i.'_';
                }
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
            } elseif (1 === $second_down_img) {
                if ('' !== $message_print_id && '' !== $sectionData['img_first']) {
                    $check_file = $img_path.$template.'/'.$sectionData['img_first'];
                    if ('' !== $check_file && file_exists($check_file)) {
                        @unlink($check_file);
                    }
                }
            } elseif (2 === $second_down_img) {
                if ('' !== $message_print_id && '' !== $sectionData['img_sec']) {
                    $check_file = $img_path.$template.'/'.$sectionData['img_sec'];
                    if ('' !== $check_file && file_exists($check_file)) {
                        @unlink($check_file);
                    }
                }
            } elseif (3 === $second_down_img) {
                if ('' !== $message_print_id && '' !== $sectionData['img_third']) {
                    $check_file = $img_path.$template.'/'.$sectionData['img_third'];
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
                if ('' !== $message_print_id && '' !== $sectionData[$img_str.$vCode]) {
                    $check_file = $img_path.$template.'/'.$sectionData[$img_str.$vCode];
                    if ('' !== $check_file && file_exists($check_file)) {
                        if (ENABLE_DYNAMIC_CREATE_PAGE === 'Yes') {
                        } else {
                            @unlink($check_file); // why unlink removed reason is written in 25-03-2021
                        }
                    }
                }
                /*if ($message_print_id != "" && $sectionData['img'] != '') {
                    $check_file = $img_path . $template . '/' . $sectionData['img'];
                    if ($check_file != '' && file_exists($check_file)) {
                        @unlink($check_file);
                    }
                }*/
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
                } elseif (1 === $second_down_img) {
                    $sectionData['img_first'] = $img[0];
                } elseif (2 === $second_down_img) {
                    $sectionData['img_sec'] = $img[0];
                } elseif (3 === $second_down_img) {
                    $sectionData['img_third'] = $img[0];
                } elseif (1 === $second_reg_img) {
                    $sectionData['img_first'] = $img[0];
                } elseif (2 === $second_reg_img) {
                    $sectionData['img_sec'] = $img[0];
                } else {
                    if ('' !== $img_str) {
                        $sectionData[$img_str.$vCode] = $img[0];
                    }
                    // $sectionData['img'] = $img[0];
                }
                $sectionDatajson = getJsonFromAnArrWithoutClean($sectionData);
                $where = " vCode = '".$vCode."'";
                $Update[$key] = $sectionDatajson;
                $obj->MySQLQueryPerform($tbl_name, $Update, 'update', $where);
                // $sql = "UPDATE " . $tbl_name . " SET " . $key . " = '" . $sectionDatajson . "' WHERE `vCode` = '" . $vCode . "'";
                // $obj->sql_query($sql);
            }
        }
    }
}
if (isset($_POST['submit'])) {
    function dataready($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        $order = [
            'rn',
            '\\r\\n',
            '\\n',
            '\\r',
            '<p>&nbsp;</p>',
        ];
        $replace = [
            ' ',
            ' ',
            ' ',
            ' ',
            '',
        ];

        return str_replace($order, $replace, $data);
    }
    $check_file_query = "SELECT lHowitworkSection,lSecuresafeSection,lDownloadappSection,lCalltobookSection,lGeneralBannerSection,lCalculateSection,lBookServiceSection FROM {$tbl_name} where vCode='".$vCode."'";
    $check_file = $obj->MySQLSelect($check_file_query);
    $sectionData = json_decode($check_file[0]['lHowitworkSection'], true);
    $how_it_work_section_arr['title'] = $_POST['how_it_work_section_title'] ?? '';
    $how_it_work_section_arr['subtitle'] = $_POST['how_it_work_section_subtitle'] ?? '';
    $how_it_work_section_arr['desc'] = $_POST['how_it_work_section_desc'] ?? '';
    $how_it_work_section_arr['desc'] = dataready($how_it_work_section_arr['desc']);
    $how_it_work_section_arr['img'] = $sectionData['img'] ?? '';
    $how_it_work_section_arr = !(empty($sectionData)) ? array_merge($sectionData, $how_it_work_section_arr) : $how_it_work_section_arr;
    // For How it works Added By PJ 25 Sep 2019
    for ($i = 1; $i <= 4; ++$i) {
        $how_it_work_section_arr['hiw_title'.$i.'_'.$vCode] = $_POST['how_it_work_section_hiw_title'.$i] ?? '';
        $how_it_work_section_arr['hiw_desc'.$i.'_'.$vCode] = $_POST['how_it_work_section_hiw_desc'.$i] ?? '';
    }
    // ----------------------------------------
    $how_it_work_section = getJsonFromAnArrWithoutClean($how_it_work_section_arr);

    $sectionData = json_decode($check_file[0]['lDownloadappSection'], true);
    $download_section_arr['title'] = $_POST['download_section_title'] ?? '';
    $download_section_arr['subtitle'] = $_POST['download_section_sub_title'] ?? '';
    $download_section_arr['desc'] = $_POST['download_section_desc'] ?? '';
    $download_section_arr['img'] = $sectionData['img'] ?? '';
    $download_section_arr['img_first'] = $sectionData['img_first'] ?? '';
    $download_section_arr['img_sec'] = $sectionData['img_sec'] ?? '';
    $download_section_arr['img_third'] = $sectionData['img_third'] ?? '';
    $download_section = getJsonFromAnArrWithoutClean($download_section_arr);
    $sectionData = json_decode($check_file[0]['lSecuresafeSection'], true);
    $secure_section_arr['title'] = $_POST['secure_section_title'] ?? '';
    $secure_section_arr['desc'] = $_POST['secure_section_desc'] ?? '';
    $secure_section_arr['img'] = $sectionData['img'] ?? '';
    $secure_section = getJsonFromAnArrWithoutClean($secure_section_arr);
    $sectionData = json_decode($check_file[0]['lCalltobookSection'], true);
    $call_section_arr['title1'] = $_POST['call_section_title_1'] ?? '';
    $call_section_arr['desc1'] = $_POST['call_section_desc_1'] ?? '';
    $call_section_arr['img1_'.$vCode] = $sectionData['img1_'.$vCode] ?? '';
    $call_section_arr['title2'] = $_POST['call_section_title_2'] ?? '';
    $call_section_arr['desc2'] = $_POST['call_section_desc_2'] ?? '';
    $call_section_arr['img2_'.$vCode] = $sectionData['img2_'.$vCode] ?? '';
    $call_section = getJsonFromAnArrWithoutClean($call_section_arr);
    $sectionData = json_decode($check_file[0]['lGeneralBannerSection'], true);
    $general_section_arr['title'] = $_POST['general_section_title'] ?? '';
    $general_section_arr['desc'] = isset($_POST['general_section_desc']) ? dataready($_POST['general_section_desc']) : '';
    $general_section_arr['img'] = $sectionData['img'] ?? '';
    $general_section_arr['img_sec'] = $sectionData['img_sec'] ?? '';
    $general_section = getJsonFromAnArrWithoutClean($general_section_arr);
    // $lServiceSection = isset($_POST['lServiceSection']) ? $_POST['lServiceSection'] : '';

    $sectionData = json_decode($check_file[0]['lServiceSection'], true);
    $lServiceSection_arr['title'] = $_POST['lServiceSection_title'] ?? '';
    $lServiceSection_arr['subtitle'] = $_POST['lServiceSection_subtitle'] ?? '';
    $lServiceSection = getJsonFromAnArrWithoutClean($lServiceSection_arr);

    if (isset($_POST['bookHomeIcon'])) {
        $booking_ids = implode(',', array_keys($_POST['bookHomeIcon']));
    } else {
        $booking_ids = '';
    }
    $sectionData = json_decode($check_file[0]['lCalculateSection'], true);
    $book_section_arr['title'] = $_POST['book_section_title'] ?? '';
    $book_section_arr['subtitle'] = $_POST['book_section_subtitle'] ?? '';
    $book_section = getJsonFromAnArrWithoutClean($book_section_arr);
    $sectionData = json_decode($check_file[0]['lBookServiceSection'], true);
    $register_section_arr['main_title'] = $_POST['register_section_main_title'] ?? '';
    $register_section_arr['main_subtitle'] = $_POST['register_section_main_subtitle'] ?? '';
    $register_section_arr['main_desc'] = $_POST['register_section_main_desc'] ?? '';
    $register_section_arr['title_first'] = $_POST['register_section_title_first'] ?? '';
    $register_section_arr['title_sec'] = $_POST['register_section_title_sec'] ?? '';

    $register_section_arr['desc_first'] = $_POST['register_section_desc_first'] ?? '';
    $register_section_arr['desc_sec'] = $_POST['register_section_desc_sec'] ?? '';

    $register_section_arr['img_first'] = $sectionData['img_first'] ?? '';
    $register_section_arr['img_sec'] = $sectionData['img_sec'] ?? '';
    $register_section = getJsonFromAnArrWithoutClean($register_section_arr);
    $lSafeSection = $_POST['lSafeSection'] ?? '';
}
if (isset($_POST['catlogo'])) {
    if (SITE_TYPE === 'Demo') {
        header('Location:home_content_prosp_action.php?id='.$id.'&success=2');

        exit;
    }
    if (isset($_FILES['vHomepageLogoOurServices']) && '' !== $_FILES['vHomepageLogoOurServices']['name']) {
        $filecheck = basename($_FILES['vHomepageLogoOurServices']['name']);
        $fileextarr = explode('.', $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        $data = getimagesize($_FILES['vHomepageLogoOurServices']['tmp_name']);
        $width = $data[0];
        $height = $data[1];
        if (1 === $flag_error) {
            $_SESSION['success'] = '';
            $_SESSION['var_msg'] = '';
            header('Location:home_content_prosp_action.php?id='.$id.'&var_msg='.$var_msg.'&goback=1');

            exit;
        }
    }
    $vacategoryid = $_POST['aid'] ?? '';
    $img_arr = $_FILES['vHomepageLogoOurServices'];
    //    if($cubexthemeonh != 1) {
    if (!empty($img_arr)) {
        // foreach ($img_arr as $key => $value) {
        if (!empty($img_arr['name'])) {
            $img_path = $tconfig['tsite_upload_home_page_service_images_panel'];
            // $temp_gallery = $img_path . '/';
            $image_object = $img_arr['tmp_name'];
            $image_name = $img_arr['name'];
            $check_file_query = 'SELECT '.$key.' FROM '.$sql_vehicle_category_table_name." where iVehicleCategoryId='".$vacategoryid."'";
            $check_file = $obj->MySQLSelect($check_file_query);
            if ('' !== $message_print_id) {
                $check_file = $img_path.'/'.$check_file[0][$key];
                if ('' !== $check_file && file_exists($check_file[0][$key])) {
                    // @unlink($check_file);
                }
            }
            $Photo_Gallery_folder = $img_path.'/';
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
            }
            // echo $Photo_Gallery_folder."===".$image_object."*****".$image_name;exit;
            $img = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, '', 'png,jpg,jpeg,gif');
            // $img = $UPLOAD_OBJ->GeneralFileUploadHome($Photo_Gallery_folder,$image_object,$image_name,'','png,jpg,jpeg,gif','');
            if ('1' === $img[2]) {
                $_SESSION['success'] = '0';
                $_SESSION['var_msg'] = $img[1];
                header('location:'.$backlink);
            }
            if (!empty($img[0])) {
                $sql = 'UPDATE '.$sql_vehicle_category_table_name.' SET '.$key." = '".$img[0]."' WHERE iVehicleCategoryId = '".$vacategoryid."'";
                $obj->sql_query($sql);
                $_SESSION['success'] = '1';
                $_SESSION['var_msg'] = $img[1];
            } else {
                $_SESSION['success'] = '0';
                $_SESSION['var_msg'] = $img[1];
            }
        }
        // }
    }
    // }
}
if (isset($_POST['submit'])) {
    if (SITE_TYPE === 'Demo') {
        // header("Location:home_action.php?success=2");
        header('Location:homepage_content.php?id='.$id.'&success=2');

        exit;
    }

    if (!$userObj->hasPermission('edit-home-page-content')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to update Home page content.';
        header('Location:homepage_content.php?id='.$id);

        exit;
    }

    $where = " vCode = '".$vCode."'";
    // $Update['booking_ids'] = $booking_ids;
    $Update['lHowitworkSection'] = $how_it_work_section;
    $Update['lSecuresafeSection'] = $secure_section;
    $Update['lDownloadappSection'] = $download_section;
    $Update['lServiceSection'] = $lServiceSection;
    $Update['lGeneralBannerSection'] = $general_section;
    $Update['lCalculateSection'] = $book_section;
    $Update['lBookServiceSection'] = $register_section;
    $Update['lSafeSection'] = $lSafeSection;
    if ($parent_ufx_catid > 0) {
        $Update['lCalltobookSection'] = $call_section;
    }
    $id = $obj->MySQLQueryPerform($tbl_name, $Update, 'update', $where);
    // header("Location:make_action.php?id=".$id.'&success=1');
    if ('Add' === $action) {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INSERT_MSG'];
    } else {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
    }

    header('location: homepage_content.php?id='.$iLanguageId);

    exit;
}
// for Edit
if ('Edit' === $action) {
    $sql = "SELECT hc.*,lm.vTitle FROM {$tbl_name} as hc LEFT JOIN language_master as lm on lm.vCode = hc.vCode  WHERE lm.iLanguageMasId = '".$iLanguageId."'";

    // $sql = "SELECT hc.*,lm.vTitle FROM homecontent as hc LEFT JOIN language_master as lm on lm.vCode = hc.vCode  WHERE hc.id = '" . $id . "'";
    $db_data = $obj->MySQLSelect($sql);
    $vLabel = $id;
    if (count($db_data) > 0) {
        foreach ($db_data as $key => $value) {
            $vCode = $value['vCode'];
            $eStatus = $value['eStatus'];
            $title = $value['vTitle'];
            $booking_ids = $value['booking_ids'];
            $how_it_work_section = (array) json_decode($value['lHowitworkSection']);
            if (str_contains($how_it_work_section['desc'], 'icon')) {
                // $count = strpos($how_it_work_section['desc'], "proc_ico");
                $imgCount = substr_count($how_it_work_section['desc'], 'proc_ico');
                // $imgCount = 4;
                $tsiteUrl = $tconfig['tsite_url'];
                for ($g = 1; $g <= $imgCount; ++$g) {
                    $imgUrl = $tsiteUrl.'assets/img/apptype/'.$template.'/icon'.$g.'.jpg';
                    // echo $imgUrl."<br>";
                    $how_it_work_section['desc'] = str_replace('icon'.$g.'.jpg', $imgUrl, $how_it_work_section['desc']);
                }
            }
            $secure_section = json_decode($value['lSecuresafeSection'], true);
            $download_section = json_decode($value['lDownloadappSection'], true);
            $call_section = json_decode($value['lCalltobookSection'], true);
            $general_section = json_decode($value['lGeneralBannerSection'], true);
            $learnServiceCatSection = json_decode($earnBusinessData[0]['learnServiceCatSection'], true);
            $lbusinessServiceCatSection = json_decode($earnBusinessData[0]['lbusinessServiceCatSection'], true);
            $lServiceSection = json_decode($value['lServiceSection'], true);
            $lSafeSection = $value['lSafeSection']; // not store in lServiceSection in json format bcoz for old theme then problem so store in new field and which is already exist..
            $book_section = json_decode($value['lCalculateSection'], true);
            $register_section = json_decode($value['lBookServiceSection'], true);
        }
    }
}
$catquery = 'SELECT vHomepageLogoOurServices,iVehicleCategoryId,vHomepageLogo,vCategory_'.$default_lang." as vCatName FROM {$sql_vehicle_category_table_name} WHERE iParentId = 0 and eStatus != 'Deleted' ORDER BY iDisplayOrderHomepage";
$vcatdata = $obj->MySQLSelect($catquery);

if (isset($_POST['submit']) && 'submit' === $_POST['submit']) {
    $required = 'required';
} elseif (isset($_POST['catlogo']) && 'catlogo' === $_POST['catlogo']) {
    $required = '';
}
$book_data = $obj->MySQLSelect("SELECT booking_ids FROM {$tbl_name} WHERE vCode = '".$_SESSION['sess_lang']."'");
$vcatdata_first = getSeviceCategoryDataForHomepage($book_data[0]['booking_ids'], 0, 1, 'Yes');
$vcatdata_sec = getSeviceCategoryDataForHomepage($book_data[0]['booking_ids'], 1, 1, 'Yes');
$vcatdata_main = array_merge($vcatdata_first, $vcatdata_sec);
$vcatdata_main = array_unique($vcatdata_main, SORT_REGULAR);

$sql = 'SELECT * FROM `language_master` ORDER BY `iDispOrder`';
$db_master = $obj->MySQLSelect($sql);

?>
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title>Admin | Manage Web Home Page</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet"/>
    <?php include_once 'global_files.php'; ?>
    <!-- On OFF switch -->
    <link href="../assets/css/jquery-ui.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css"/>
    <link rel="stylesheet" href="css/fancybox.css" />
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

        /*icon*/

        .grid-services-section {
            display: block;
            border: 1px solid #aaaaaa;
            padding: 15px 15px 0;
            background-color: #f5f5f5;
        }

        .grid-service {
            display: inline-block;
            border: 1px solid #cccccc;
            padding: 10px 15px;
            font-size: 16px;
            margin-right: 15px;
        }

        .grid-service, .grid-service label {
            font-weight: 500;
            margin-bottom: 0;
            cursor: grab;
            background-color: #ffffff;
        }

        .grid-service input[type="checkbox"] {
            display: none;
        }

        .grid-service.active {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .show-help-section {
            position: relative;
        }

        .show-help-section span {
            position: absolute;
            visibility: hidden;
            height: 0;
        }

        .show-help-img {
            margin-right: 15px;
            cursor: pointer;
        }

        .show-help-img:hover + span {
            visibility: visible;
            top: 0;
            left: auto;
            z-index: 999;
            height: 400px;
            -webkit-box-shadow: 0px 0px 15px -2px rgba(0, 0, 0, 0.75);
            -moz-box-shadow: 0px 0px 15px -2px rgba(0, 0, 0, 0.75);
            box-shadow: 0px 0px 15px -2px rgba(0, 0, 0, 0.75);
        }

        .show-help-section span img {
            width: auto;
            height: 100%;
        }

        .ui-sortable-handle {
            margin-bottom: 15px;
            width: auto !important;
            height: auto !important;
        }

        tr.Our_Service_Section  {
            cursor: pointer;
        }

        .languageSelection{
            display: flex;
        }

        .languageSelection  p{
            margin: 0;
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
                    <div class="col-lg-8" >
                        <h2><?php echo $action; ?> Home Content (<?php echo $title; ?>)</h2>
                    </div>
                    <div class="col-lg-4 languageSelection">
                        <div class="col-lg-6" style="text-align: end;margin: auto;">
                            <p style="margin: 0; font-weight:700;" >Select Language:</p>
                        </div>
                        <select onchange="language_wise_page(this);" name="language" id="language"
                                class="form-control">
                            <?php
                            foreach ($db_master as $dm) {
                                $selected = '';
                                if ($dm['iLanguageMasId'] === $id) {
                                    $selected = 'selected';
                                }
                                ?>
                                <option <?php echo $selected; ?>
                                        value="<?php echo $dm['iLanguageMasId']; ?>"><?php echo $dm['vTitle']; ?> </option>
                            <?php }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php
            include 'valid_msg.php';
?>
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
                        <input type="hidden" name="backlink" id="backlink" value="home_content_cubejekx.php"/>
                        <!-- Start Home icons area-->
                        <div class="body-div innersection">
                            <div class="form-group general_section">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h3 class="show-help-section">
                                            General Banner Section
                                            <i class="fa fa-question-circle show-help-img" data-fancybox data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/web_home_page_help_image/home_1.png"></i>

                                        </h3>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Title</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="general_section_title"
                                               id="general_section_title" value="<?php echo $general_section['title']; ?>"
                                               placeholder="Title">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Description</label>
                                    </div>
                                    <div class="col-lg-12">
                                        <textarea class="form-control ckeditor" rows="10" name="general_section_desc"
                                                  id="general_section_desc"
                                                  placeholder="Description"><?php echo $general_section['desc']; ?></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>First Image</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <?php if ('' !== $general_section['img_sec']) { ?>
                                            <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=300&src='.$tconfig['tsite_upload_apptype_page_images'].$template.'/'.$general_section['img_sec']; ?>"
                                                 class="innerbg_image"/>
                                        <?php } ?>
                                        <input type="file" class="form-control FilUploader"
                                               name="general_section_img_sec" id="general_section_img_sec"
                                               accept=".png,.jpg,.jpeg,.gif">
                                        <br/>
                                        <span class="notes">[Note: For Better Resolution Upload only image size of 609px * 547px.]</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="body-div innersection">
                            <div class="form-group general_section">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h3 class="show-help-section">
                                            Service Section
                                            <i class="fa fa-question-circle show-help-img" data-fancybox data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/web_home_page_help_image/home_1.png"></i>

                                        </h3>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Service Title 1</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="call_section_title_1" id="call_section_title_1" value="<?php echo $call_section['title1']; ?>" placeholder="Title">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Service Description 1</label>
                                    </div>
                                    <div class="col-lg-12">
                                        <textarea class="form-control ckeditor" rows="5" name="call_section_desc_1" id="call_section_desc_1" placeholder="Description"><?php echo $call_section['desc1']; ?></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Service Image 1</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <?php if ('' !== $call_section['img1_'.$vCode]) { ?>
                                            <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=300&src='.$tconfig['tsite_upload_apptype_page_images'].$template.'/'.$call_section['img1_'.$vCode]; ?>" class="innerbg_image"/>
                                        <?php } ?>
                                        <input type="file" class="form-control FilUploader" name="call_section_img_1" id="call_section_img_1" accept=".png,.jpg,.jpeg,.gif">
                                        <br/>
                                        <span class="notes">[Note: For Better Resolution Upload only image size of 805px X 493px.]</span>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Service Title 2</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="call_section_title_2" id="call_section_title_2" value="<?php echo $call_section['title2']; ?>" placeholder="Title">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Service Description 2</label>
                                    </div>
                                    <div class="col-lg-12">
                                        <textarea class="form-control ckeditor" rows="5" name="call_section_desc_2" id="call_section_desc_2" placeholder="Description"><?php echo $call_section['desc2']; ?></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Service Image 2</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <?php if ('' !== $call_section['img2_'.$vCode]) { ?>
                                            <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=300&src='.$tconfig['tsite_upload_apptype_page_images'].$template.'/'.$call_section['img2_'.$vCode]; ?>" class="innerbg_image"/>
                                        <?php } ?>
                                        <input type="file" class="form-control FilUploader" name="call_section_img_2" id="call_section_img_2" accept=".png,.jpg,.jpeg,.gif">
                                        <br/>
                                        <span class="notes">[Note: For Better Resolution Upload only image size of 805px X 493px.]</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- End Home icons area-->
                        <div class="body-div innersection">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h3 class="show-help-section">
                                            How It work section
                                            <i class="fa fa-question-circle show-help-img" data-fancybox data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/web_home_page_help_image/home_4.png"></i>
                                        </h3>
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
                                               value="<?php echo $how_it_work_section['title']; ?>" placeholder="Title"
                                               required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>SubTitle
                                            <span class="red"> *</span>
                                        </label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="how_it_work_section_subtitle"
                                               id="how_it_work_section_subtitle"
                                               value="<?php echo $how_it_work_section['subtitle']; ?>"
                                               placeholder="SubTitle" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Description</label>
                                    </div>
                                    <div class="col-lg-12">
                                        <?php for ($i = 1; $i <= 4; ++$i) { ?>
                                            <div class="col-lg-3">
                                                <!-- Title -->
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label>Block Title <?php echo $i; ?></label>
                                                    </div>
                                                    <div class="col-lg-11">
                                                        <input type="text" class="form-control"
                                                               name="how_it_work_section_hiw_title<?php echo $i; ?>"
                                                               id="how_it_work_section_hiw_title<?php echo $i; ?>"
                                                               value="<?php echo $how_it_work_section['hiw_title'.$i.'_'.$vCode]; ?>"
                                                               placeholder="Title">
                                                    </div>
                                                </div>
                                                <!-- Description  -->
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label>Block Description <?php echo $i; ?></label>
                                                    </div>
                                                    <div class="col-lg-11">
                                                        <textarea class="form-control"
                                                                  name="how_it_work_section_hiw_desc<?php echo $i; ?>"
                                                                  id="how_it_work_section_hiw_desc<?php echo $i; ?>"
                                                                  value="<?php echo $how_it_work_section['hiw_desc'.$i.'_'.$vCode]; ?>"
                                                                  placeholder="Description"
                                                                  rows="3"><?php echo $how_it_work_section['hiw_desc'.$i.'_'.$vCode]; ?></textarea>
                                                    </div>
                                                </div>
                                                <!-- Image  -->
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label>Block Image <?php echo $i; ?></label>
                                                    </div>
                                                    <div class="col-lg-11">
                                                        <?php if ('' !== $how_it_work_section['hiw_img'.$i.'_'.$vCode]) { ?>
                                                            <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=200&src='.$tconfig['tsite_upload_apptype_page_images'].$template.'/'.$how_it_work_section['hiw_img'.$i.'_'.$vCode]; ?>"
                                                                 class="innerbg_image"/ style="max-height:100px;">
                                                        <?php } ?>
                                                        <input type="file" class="form-control FilUploader"
                                                               name="how_it_work_section_hiw_img<?php echo $i; ?>"
                                                               id="how_it_work_section_hiw_img<?php echo $i; ?>"
                                                               accept=".png,.jpg,.jpeg,.gif,.svg">
                                                        <br/>
                                                        <span class="notes">[Note: For Better Resolution Upload only image size of 50px * 50px.]</span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <!-- <textarea class="form-control ckeditor" rows="10"
                                                  name="how_it_work_section_desc" id="how_it_work_section_desc"
                                                  placeholder="Description"><?php echo $how_it_work_section['desc']; ?></textarea> -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="body-div innersection">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h3 class="show-help-section">
                                            Register section
                                            <i class="fa fa-question-circle show-help-img" data-fancybox data-src="<?php echo $tconfig['tsite_url_main_admin']; ?>img/web_home_page_help_image/home_5.png"></i>
                                        </h3>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Title
                                            <span class="red"> *</span>
                                        </label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="register_section_main_title"
                                               id="register_section_main_title"
                                               value="<?php echo $register_section['main_title']; ?>" placeholder="Title"
                                               required>
                                    </div>
                                </div>
                                <!--  <div class="row">
                                    <div class="col-lg-12">
                                        <label>Subtitle
                                            <span class="red"> *</span>
                                        </label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control"
                                               name="register_section_main_subtitle"
                                               id="register_section_main_subtitle"
                                               value="<?php echo $register_section['main_subtitle']; ?>"
                                               placeholder="Subtitle" required>
                                    </div>
                                </div> -->
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Description
                                            <span class="red"> *</span>
                                        </label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="register_section_main_desc"
                                               id="register_section_main_desc"
                                               value="<?php echo $register_section['main_desc']; ?>"
                                               placeholder="Description" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Image Title 1
                                            <span class="red"> *</span>
                                        </label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="register_section_title_first"
                                               id="register_section_title_first"
                                               value="<?php echo $register_section['title_first']; ?>" placeholder="Title"
                                               required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Image Desc 1</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="register_section_desc_first"
                                               id="register_section_desc_first"
                                               value="<?php echo $register_section['desc_first']; ?>" placeholder="Description"
                                               >
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Image 1</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <?php if ('' !== $register_section['img_first']) { ?>
                                            <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=300&src='.$tconfig['tsite_upload_apptype_page_images'].$template.'/'.$register_section['img_first']; ?>"
                                                 class="innerbg_image"/>
                                        <?php } ?>
                                        <input type="file" class="form-control FilUploader"
                                               name="register_section_img_first" id="register_section_img_first"
                                               accept=".png,.jpg,.jpeg,.gif">
                                        <br/>
                                        <span class="notes">[Note: For Better Resolution Upload only image size of 450px * 520px.]</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Image Title 2
                                            <span class="red"> *</span>
                                        </label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="register_section_title_sec"
                                               id="register_section_title_sec"
                                               value="<?php echo $register_section['title_sec']; ?>" placeholder="Title"
                                               required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Image Desc 2</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="register_section_desc_sec"
                                               id="register_section_desc_sec"
                                               value="<?php echo $register_section['desc_sec']; ?>" placeholder="Description"
                                               >
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Image 2</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <?php if ('' !== $register_section['img_sec']) { ?>
                                            <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=300&src='.$tconfig['tsite_upload_apptype_page_images'].$template.'/'.$register_section['img_sec']; ?>"
                                                 class="innerbg_image"/>
                                        <?php } ?>
                                        <input type="file" class="form-control FilUploader"
                                               name="register_section_img_sec" id="register_section_img_sec"
                                               accept=".png,.jpg,.jpeg,.gif">
                                        <br/>
                                        <span class="notes">[Note: For Better Resolution Upload only image size of 450px * 520px.]</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <?php if ($userObj->hasPermission('edit-home-page-content')) { ?>
                                    <input type="submit" class=" btn btn-default" name="submit" id="submit"
                                       value="<?php echo $action; ?> Home Content">
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
    </div>
</div>

<?php include_once 'footer.php'; ?>
<script type="text/javascript" src="js/fancybox.umd.js"></script>
<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
<script src="../assets/plugins/ckeditor/ckeditor.js"></script>
<script src="../assets/plugins/ckeditor/config.js"></script>

</script>
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
        var checked = 0;
        $('input[id="vehicleHomeIcon"]:checked').each(function () {
            checked = 1;
        });
        if (checked == 1) {
            $(".general_section").hide();
        } else {
            $(".general_section").show();
        }
        $('input[id="vehicleHomeIcon"]').click(function () {
            checked = 0;
            $('input[id="vehicleHomeIcon"]:checked').each(function () {
                checked = 1;
            });
            if (checked == 1) {
                $(".general_section").hide();
            } else {
                $(".general_section").show();
            }
        });
    });
</script>
<script>
    $(document).ready(function () {
        var referrer;
        <?php if (1 === $goback) { ?>
        alert('<?php echo $var_msg; ?>');
        //history.go(-1);
        window.location.href = "home_content_prosp_action.php?id=<?php echo $id; ?>";


        <?php } ?>
        if ($("#previousLink").val() == "") { //alert('pre1');
            referrer = document.referrer;
            // alert(referrer);
        } else { //alert('pre2');
            referrer = $("#previousLink").val();
        }
        if (referrer == "") {
            referrer = "home_content_cubejekx.php";
        } else { //alert('hi');
            //$("#backlink").val(referrer);
            referrer = "home_content_cubejekx.php";
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
    //it is bcoz when enter press in any input textbox, then two form so submit remove form and it will delete first icon so enter key disabled it.
    $('form input').keydown(function (e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            return false;
        }
    });


    //drag and drop

    $(function () {

        $("#Our_Service_Section tbody").sortable({
            items: "tr:not(.ui-state-disabled1)",
            revert: true,
            cursor: 'move',
            update: function (event, ui) {
                console.log('update');
                getIdsOfvehicleCategory();
            },//end update
            containment: "parent"

        });

        $("#earn-row").disableSelection();

    });


    $(function () {
        $("#btn1").on('click', function () {
            $("#Our_Service_Section tbody").sortable("cancel");
        });
    });
    function getIdsOfvehicleCategory() {
        var values = [];
        $('.Our_Service_Section').each(function (index) {
            values.push($(this).attr("id").replace("vehicleCategory", ""));
        });
        $('#lServiceSection_vehicleCategory_Ordering').val(values);
    }


    function language_wise_page(sel) {
        $("#loaderIcon").show();
        var url = window.location.href;
        url = new URL(url);
        url.searchParams.set("id", sel.value);
        window.location.href = url.href;
    }
    //drag and drop
</script>
</body>
<!-- END BODY-->
</html>