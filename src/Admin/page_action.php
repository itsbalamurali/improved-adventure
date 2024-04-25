<?php
include_once '../common.php';

require_once TPATH_CLASS.'Imagecrop.class.php';

$id = $_REQUEST['id'] ?? '';

$cubexthemeon = 'No';
if ('Yes' === $THEME_OBJ->isXThemeActive()) {
    $cubexthemeon = 'Yes';
}

if ('Yes' === $cubexthemeon) {
    if (1 === $id && !isset($_POST['submit'])) {
        header('Location:page_action.php?id=52');

        exit;
    }
}

$success = $_REQUEST['success'] ?? 0;
$action = ('' !== $id) ? 'Edit' : 'Add';

$tbl_name = 'pages';
$script = 'page';

$backlink = $_POST['backlink'] ?? '';
$previousLink = $_POST['backlink'] ?? '';

// fetch all lang from language_master table
// $sql = "SELECT * FROM `language_master` ORDER BY `iDispOrder`";
$sql = 'SELECT * FROM `language_master` ORDER BY `iDispOrder`';
$db_master = $obj->MySQLSelect($sql);
$count_all = count($db_master);

// set all variables with either post (when submit) either blank (when insert)
$iPageId = $_POST['iPageId'] ?? $id;
$vPageName = $_REQUEST['vPageName'] ?? '';
$vTitle = $_REQUEST['vTitle'] ?? '';
$tMetaKeyword = $_REQUEST['tMetaKeyword'] ?? '';
$tMetaDescription = $_REQUEST['tMetaDescription'] ?? '';
$vImage = $_POST['vImage'] ?? '';
$vImage1 = $_POST['vImage1'] ?? '';
$vImage2 = $_POST['vImage2'] ?? '';
$iOrderBy = $_POST['iOrderBy'] ?? ''; // added by SP for pages orderby,active/inactive functionality
$thumb = new thumbnail();

$pageArray = ['48', '50'];
$pageidCubexImage = ['48', '49', '50'];

$update_array = [];

if ('Yes' === $cubexthemeon && 1 === $iPageId) {
    $vPageName = $_REQUEST['vPageName_1'] ?? '';
    $vTitle = $_REQUEST['vTitle_1'] ?? '';
    $tMetaKeyword = $_REQUEST['tMetaKeyword_1'] ?? '';
    $tMetaDescription = $_REQUEST['tMetaDescription_1'] ?? '';
    $iOrderBy = $_POST['iOrderBy_1'] ?? '';
}

if ('Yes' === $cubexthemeon && 53 !== $iPageId) {
    // if(empty($template)) $template = 'Cubex';
    $Photo_Gallery_folder = $tconfig['tsite_upload_apptype_images_panel'].'/'.$template.'/';
    $images = $tconfig['tsite_upload_apptype_images'].$template.'/';
} else {
    $Photo_Gallery_folder = $tconfig['tsite_upload_page_images_panel'].'/';
    $images = $tconfig['tsite_upload_page_images'];
}

if ($count_all > 0) {
    for ($i = 0; $i < $count_all; ++$i) {
        $vPageTitle = 'vPageTitle_'.$db_master[$i]['vCode'];
        ${$vPageTitle} = $_POST[$vPageTitle] ?? '';
        $tPageDesc = 'tPageDesc_'.$db_master[$i]['vCode'];
        ${$tPageDesc} = $_POST[$tPageDesc] ?? '';

        if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
            $vPageSubTitle = 'vPageTitle_'.$db_master[$i]['vCode'];
            ${$vPageSubTitle} = $_POST[$vPageSubTitle] ?? '';

            $vUserPageTitle = 'vUserPageTitle_'.$db_master[$i]['vCode'];
            ${$vUserPageTitle} = $_POST[$vUserPageTitle] ?? '';
            $tUserPageDesc = 'tUserPageDesc_'.$db_master[$i]['vCode'];
            ${$tUserPageDesc} = $_POST[$tUserPageDesc] ?? '';

            $vProviderPageTitle = 'vProviderPageTitle_'.$db_master[$i]['vCode'];
            ${$vProviderPageTitle} = $_POST[$vProviderPageTitle] ?? '';
            $tProviderPageDesc = 'tProviderPageDesc_'.$db_master[$i]['vCode'];
            ${$tProviderPageDesc} = $_POST[$tProviderPageDesc] ?? '';

            $vCompanyPageTitle = 'vCompanyPageTitle_'.$db_master[$i]['vCode'];
            ${$vCompanyPageTitle} = $_POST[$vCompanyPageTitle] ?? '';
            $tCompanyPageDesc = 'tCompanyPageDesc_'.$db_master[$i]['vCode'];
            ${$tCompanyPageDesc} = $_POST[$tCompanyPageDesc] ?? '';

            $vRestaurantPageTitle = 'vRestaurantPageTitle_'.$db_master[$i]['vCode'];
            ${$vRestaurantPageTitle} = $_POST[$vRestaurantPageTitle] ?? '';
            $tRestaurantPageDesc = 'tRestaurantPageDesc_'.$db_master[$i]['vCode'];
            ${$tRestaurantPageDesc} = $_POST[$tRestaurantPageDesc] ?? '';

            $vOrgPageTitle = 'vOrgPageTitle_'.$db_master[$i]['vCode'];
            ${$vOrgPageTitle} = $_POST[$vOrgPageTitle] ?? '';
            $tOrgPageDesc = 'tOrgPageDesc_'.$db_master[$i]['vCode'];
            ${$tOrgPageDesc} = $_POST[$tOrgPageDesc] ?? '';

            $vTrackServicePageTitle = 'vTrackServicePageTitle_'.$db_master[$i]['vCode'];
            ${$vTrackServicePageTitle} = $_POST[$vTrackServicePageTitle] ?? '';
            $tTrackServicePageDesc = 'tTrackServicePageDesc_'.$db_master[$i]['vCode'];
            ${$tTrackServicePageDesc} = isset($_POST[$tTrackServicePageDesc]) ? $_POST[$tOrgPageDesc] : '';

            $vHotelPageTitle = 'vHotelPageTitle_'.$db_master[$i]['vCode'];
            ${$vHotelPageTitle} = $_POST[$vHotelPageTitle] ?? '';
            $tHotelPageDesc = 'tHotelPageDesc_'.$db_master[$i]['vCode'];
            ${$tHotelPageDesc} = $_POST[$tHotelPageDesc] ?? '';

            $vPageTitle = $vPageTitle;
            ${$vPageTitle} = getJsonFromAnArr(['user_pages' => ${$vUserPageTitle}, 'provider_pages' => ${$vProviderPageTitle}, 'company_pages' => ${$vCompanyPageTitle}, 'restaurant_pages' => ${$vRestaurantPageTitle}, 'org_pages' => ${$vOrgPageTitle}, 'trackservice_pages' => ${$vTrackServicePageTitle}, 'hotel_pages' => ${$vHotelPageTitle}]);

            $tPageDesc = $tPageDesc;
            ${$tPageDesc} = getJsonFromAnArr(['user_pages' => ${$tUserPageDesc}, 'provider_pages' => ${$tProviderPageDesc}, 'company_pages' => ${$tCompanyPageDesc}, 'restaurant_pages' => ${$tRestaurantPageDesc}, 'org_pages' => ${$tOrgPageDesc}, 'trackservice_pages' => ${$tTrackServicePageDesc}, 'hotel_pages' => ${$tHotelPageDesc}]);
        }

        if ('Yes' === $cubexthemeon && 52 === $iPageId) {
            $vPageSubTitle = 'vPageTitle_'.$db_master[$i]['vCode'];
            ${$vPageSubTitle} = $_POST[$vPageSubTitle] ?? '';

            $tPageSecDesc = 'tPageSecDesc_'.$db_master[$i]['vCode'];
            ${$tPageSecDesc} = $_POST[$tPageSecDesc] ?? '';
            $tPageThirdDesc = 'tPageThirdDesc_'.$db_master[$i]['vCode'];
            ${$tPageThirdDesc} = $_POST[$tPageThirdDesc] ?? '';
        }

        if ('Yes' === $cubexthemeon && 1 === $iPageId) {
            $vPageTitle = 'vPageTitle_'.$db_master[$i]['vCode'].'_1';
            ${$vPageTitle} = $_POST[$vPageTitle] ?? '';
            $tPageDesc = 'tPageDesc_'.$db_master[$i]['vCode'].'_1';
            ${$tPageDesc} = $_POST[$tPageDesc] ?? '';
        }
    }
}

if (isset($_POST['submit'])) {
    if ('Add' === $action && !$userObj->hasPermission('create-pages')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to create page.';
        header('Location:page.php');

        exit;
    }
    if ('Edit' === $action && !$userObj->hasPermission('edit-pages')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to update page.';
        header('Location:page.php');

        exit;
    }

    if (SITE_TYPE === 'Demo') {
        header('Location:page_action.php?id='.$iPageId.'&success=2');

        exit;
    }
    $vPageSubTitleArr = [];
    if (count($db_master) > 0) {
        $str = '';
        for ($i = 0; $i < count($db_master); ++$i) {
            if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                $vPageSubTitleArr['pageSubtitle_'.$db_master[$i]['vCode']] = $_REQUEST['vPageSubTitle'][$db_master[$i]['vCode']];

                $vUserPageTitle = 'vUserPageTitle_'.$db_master[$i]['vCode'];
                ${$vUserPageTitle} = $_REQUEST[$vUserPageTitle];
                $tUserPageDesc = 'tUserPageDesc_'.$db_master[$i]['vCode'];
                ${$tUserPageDesc} = $_REQUEST[$tUserPageDesc];

                $vProviderPageTitle = 'vProviderPageTitle_'.$db_master[$i]['vCode'];
                ${$vProviderPageTitle} = $_REQUEST[$vProviderPageTitle];
                $tProviderPageDesc = 'tProviderPageDesc_'.$db_master[$i]['vCode'];
                ${$tProviderPageDesc} = $_REQUEST[$tProviderPageDesc];

                $vCompanyPageTitle = 'vCompanyPageTitle_'.$db_master[$i]['vCode'];
                ${$vCompanyPageTitle} = $_REQUEST[$vCompanyPageTitle];
                $tCompanyPageDesc = 'tCompanyPageDesc_'.$db_master[$i]['vCode'];
                ${$tCompanyPageDesc} = $_REQUEST[$tCompanyPageDesc];

                $vRestaurantPageTitle = 'vRestaurantPageTitle_'.$db_master[$i]['vCode'];
                ${$vRestaurantPageTitle} = $_REQUEST[$vRestaurantPageTitle];
                $tRestaurantPageDesc = 'tRestaurantPageDesc_'.$db_master[$i]['vCode'];
                ${$tRestaurantPageDesc} = $_REQUEST[$tRestaurantPageDesc];

                $vOrgPageTitle = 'vOrgPageTitle_'.$db_master[$i]['vCode'];
                ${$vOrgPageTitle} = $_REQUEST[$vOrgPageTitle];
                $tOrgPageDesc = 'tOrgPageDesc_'.$db_master[$i]['vCode'];
                ${$tOrgPageDesc} = $_REQUEST[$tOrgPageDesc];

                $vTrackServicePageTitle = 'vTrackServicePageTitle_'.$db_master[$i]['vCode'];
                ${$vTrackServicePageTitle} = $_REQUEST[$vTrackServicePageTitle];
                $tTrackServicePageDesc = 'tTrackServicePageDesc_'.$db_master[$i]['vCode'];
                ${$tTrackServicePageDesc} = $_REQUEST[$tTrackServicePageDesc];

                $vHotelPageTitle = 'vHotelPageTitle_'.$db_master[$i]['vCode'];
                ${$vHotelPageTitle} = $_REQUEST[$vHotelPageTitle];
                $tHotelPageDesc = 'tHotelPageDesc_'.$db_master[$i]['vCode'];
                ${$tHotelPageDesc} = $_REQUEST[$tHotelPageDesc];

                $vPageTitle = 'vPageTitle_'.$db_master[$i]['vCode'];
                ${$vPageTitle} = getJsonFromAnArr(['user_pages' => ${$vUserPageTitle}, 'provider_pages' => ${$vProviderPageTitle}, 'company_pages' => ${$vCompanyPageTitle}, 'restaurant_pages' => ${$vRestaurantPageTitle}, 'org_pages' => ${$vOrgPageTitle}, 'trackservice_pages' => ${$vTrackServicePageTitle}, 'hotel_pages' => ${$vHotelPageTitle}]);

                $tPageDesc = 'tPageDesc_'.$db_master[$i]['vCode'];
                ${$tPageDesc} = getJsonFromAnArr(['user_pages' => ${$tUserPageDesc}, 'provider_pages' => ${$tProviderPageDesc}, 'company_pages' => ${$tCompanyPageDesc}, 'restaurant_pages' => ${$tRestaurantPageDesc}, 'org_pages' => ${$tOrgPageDesc}, 'trackservice_pages' => ${$tTrackServicePageDesc}, 'hotel_pages' => ${$tHotelPageDesc}]);

                $str .= ' '.$vPageTitle." = '".${$vPageTitle}."', ".$tPageDesc." = '".${$tPageDesc}."', ";

                $update_array[$vPageTitle] = ${$vPageTitle};
                $update_array[$tPageDesc] = ${$tPageDesc};
            } elseif ('Yes' === $cubexthemeon && 52 === $iPageId) {
                $vPageTitle = 'vPageTitle_'.$db_master[$i]['vCode'];
                ${$vPageTitle} = $_REQUEST[$vPageTitle];

                $vPageSubTitleArr['pageSubtitle_'.$db_master[$i]['vCode']] = $_REQUEST['vPageSubTitle'][$db_master[$i]['vCode']];

                $tPageDesc = 'tPageDesc_'.$db_master[$i]['vCode'];
                ${$tPageDesc} = $_REQUEST[$tPageDesc];
                $tPageSecDesc = 'tPageSecDesc_'.$db_master[$i]['vCode'];
                ${$tPageSecDesc} = $_REQUEST[$tPageSecDesc];
                $tPageThirdDesc = 'tPageThirdDesc_'.$db_master[$i]['vCode'];
                ${$tPageThirdDesc} = $_REQUEST[$tPageThirdDesc];

                ${$tPageDesc} = getJsonFromAnArr(['FirstDesc' => ${$tPageDesc}, 'SecDesc' => ${$tPageSecDesc}, 'ThirdDesc' => ${$tPageThirdDesc}]);

                $tPageDesc = 'tPageDesc_'.$db_master[$i]['vCode'];
                $str .= ' '.$vPageTitle." = '".${$vPageTitle}."', ".$tPageDesc." = '".${$tPageDesc}."', ";
                $update_array[$vPageTitle] = ${$vPageTitle};
                $update_array[$tPageDesc] = ${$tPageDesc};
            } elseif ('Yes' === $cubexthemeon && 1 === $iPageId) {
                $vPageTitlekey = 'vPageTitle_'.$db_master[$i]['vCode'];
                $vPageTitle = 'vPageTitle_'.$db_master[$i]['vCode'].'_1';
                ${$vPageTitle} = $_REQUEST[$vPageTitle] ?? '';
                $tPageDesckey = 'tPageDesc_'.$db_master[$i]['vCode'];
                $tPageDesc = 'tPageDesc_'.$db_master[$i]['vCode'].'_1';
                ${$tPageDesc} = $_REQUEST[$tPageDesc] ?? '';
                $str .= ' '.$vPageTitlekey." = '".${$vPageTitle}."', ".$tPageDesckey." = '".${$tPageDesc}."', ";
                $update_array[$vPageTitlekey] = ${$vPageTitle};
                $update_array[$tPageDesckey] = ${$tPageDesc};
            } else {
                $vPageTitle = 'vPageTitle_'.$db_master[$i]['vCode'];

                ${$vPageTitle} = $_REQUEST[$vPageTitle];

                $tPageDesc = 'tPageDesc_'.$db_master[$i]['vCode'];
                ${$tPageDesc} = $_REQUEST[$tPageDesc];

                $str .= ' '.$vPageTitle." = '".${$vPageTitle}."', ".$tPageDesc." = '".${$tPageDesc}."', ";

                $update_array[$vPageTitle] = ${$vPageTitle};
                $update_array[$tPageDesc] = ${$tPageDesc};
            }
        }
    }

    if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
        $vPageSubTitle = getJsonFromAnArr($vPageSubTitleArr);
        $str .= " pageSubtitle = '".$vPageSubTitle."', ";

        $update_array['pageSubtitle'] = $vPageSubTitle;
    }
    if ('Yes' === $cubexthemeon && 52 === $iPageId) {
        $vPageSubTitle = getJsonFromAnArr($vPageSubTitleArr);
        $str .= " pageSubtitle = '".$vPageSubTitle."', ";

        $update_array['pageSubtitle'] = $vPageSubTitle;
    }

    $image_object = $_FILES['vImage']['tmp_name'];
    $image_name = $_FILES['vImage']['name'];
    $image_name = str_replace(' ', '', $image_name);
    // echo "<pre>";print_r( $_FILES);print_r($_POST);echo "</pre>";exit;
    if ('' !== $image_name) {
        $filecheck = basename($_FILES['vImage']['name']);
        $fileextarr = explode('.', $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        if ('jpg' !== $ext && 'gif' !== $ext && 'png' !== $ext && 'jpeg' !== $ext && 'bmp' !== $ext && 'svg' !== $ext1) {
            $flag_error = 1;
            $var_msg = 'Not valid image extension of .jpg, .jpeg, .gif, .png, .svg';
        }
        if ($_FILES['vImage']['size'] > 2_097_152) {
            $flag_error = 1;
            $var_msg = 'Image size is too large';
        }
        if (1 === $flag_error) {
            $_SESSION['success'] = 3;
            $_SESSION['var_msg'] = $var_msg;
            header('location:'.$backlink);

            // getPostForm($_POST, $var_msg, $tconfig['tsite_url_main_admin'] . "page_action.php?id=".$id."&success=3");
            exit;
        }
        // $Photo_Gallery_folder = $tconfig["tsite_upload_page_images_panel"] . '/';
        if (!is_dir($Photo_Gallery_folder)) {
            mkdir($Photo_Gallery_folder, 0777);
        }
        $img = $UPLOAD_OBJ->UploadImage($Photo_Gallery_folder, $image_object, $image_name, '', 'jpg,png,gif,jpeg,svg'); // fileupload

        $vImage = $img[0];
        // echo "<pre>";print_r($img);exit;
    }

    $image_object1 = $_FILES['vImage1']['tmp_name'];
    $image_name1 = $_FILES['vImage1']['name'];
    $image_name1 = str_replace(' ', '', $image_name1);
    // echo "<pre>";print_r( $_FILES);echo "</pre>";exit;
    if ('' !== $image_name1) {
        $filecheck1 = basename($_FILES['vImage1']['name']);
        $fileextarr1 = explode('.', $filecheck1);
        $ext1 = strtolower($fileextarr1[count($fileextarr1) - 1]);
        $flag_error1 = 0;
        if ('jpg' !== $ext1 && 'gif' !== $ext1 && 'png' !== $ext1 && 'jpeg' !== $ext1 && 'bmp' !== $ext1 && 'svg' !== $ext1) {
            $flag_error1 = 1;
            $var_msg = 'Not valid image extension of .jpg, .jpeg, .gif, .png, .svg';
        }
        if ($_FILES['vImage1']['size'] > 2_097_152) {
            $flag_error1 = 1;
            $var_msg = 'Image size is too large';
        }
        if (1 === $flag_error1) {
            $_SESSION['success'] = 3;
            $_SESSION['var_msg'] = $var_msg;
            header('location:'.$backlink);

            // getPostForm($_POST, $var_msg, $tconfig['tsite_url_main_admin'] . "page_action.php?id=".$id."&success=3");
            exit;
        }
        // $Photo_Gallery_folder = $tconfig["tsite_upload_page_images_panel"] . '/';
        if (!is_dir($Photo_Gallery_folder)) {
            mkdir($Photo_Gallery_folder, 0777);
        }
        $img1 = $UPLOAD_OBJ->UploadImage($Photo_Gallery_folder, $image_object1, $image_name1, '', 'jpg,png,gif,jpeg,svg'); // fileupload
        // echo "<pre>";print_r($img);exit;
        $vImage1 = $img1[0];
    }

    // if(!empty($vImage2)) {
    $image_object2 = $_FILES['vImage2']['tmp_name'];
    $image_name2 = $_FILES['vImage2']['name'];
    $image_name2 = str_replace(' ', '', $image_name2);
    // echo "<pre>";print_r( $_FILES);echo "</pre>";exit;
    if ('' !== $image_name2) {
        $filecheck2 = basename($_FILES['vImage2']['name']);
        $fileextarr2 = explode('.', $filecheck2);
        $ext2 = strtolower($fileextarr2[count($fileextarr2) - 1]);
        $flag_error2 = 0;
        if ('jpg' !== $ext2 && 'gif' !== $ext2 && 'png' !== $ext2 && 'jpeg' !== $ext2 && 'bmp' !== $ext2 && 'svg' !== $ext2) {
            $flag_error2 = 1;
            $var_msg = 'Not valid image extension of .jpg, .jpeg, .gif, .png, .svg';
        }
        if ($_FILES['vImage2']['size'] > 2_097_152) {
            $flag_error2 = 1;
            $var_msg = 'Image size is too large';
        }
        if (1 === $flag_error2) {
            $_SESSION['success'] = 3;
            $_SESSION['var_msg'] = $var_msg;
            header('location:'.$backlink);

            // getPostForm($_POST, $var_msg, $tconfig['tsite_url_main_admin'] . "page_action.php?id=".$id."&success=3");
            exit;
        }
        // $Photo_Gallery_folder = $tconfig["tsite_upload_page_images_panel"] . '/';
        if (!is_dir($Photo_Gallery_folder)) {
            mkdir($Photo_Gallery_folder, 0777);
        }
        $img2 = $UPLOAD_OBJ->UploadImage($Photo_Gallery_folder, $image_object2, $image_name2, '', 'jpg,png,gif,jpeg,svg'); // fileupload
        // echo "<pre>";print_r($img);exit;
        $vImage2 = $img2[0];
    }
    // }

    // ------------------------------ update query ------------------------------

    $update_array['vTitle'] = $vTitle;
    $update_array['tMetaKeyword'] = $tMetaKeyword;
    $update_array['tMetaDescription'] = $tMetaDescription;
    $update_array['iOrderBy'] = $iOrderBy;
    if ('Add' === $action) {
        $update_array['vPageName'] = $vPageName;
    }
    if ('' !== $image_name) {
        $update_array['vImage'] = $vImage;
    }
    if ('' !== $image_name1) {
        $update_array['vImage1'] = $vImage1;
    }
    if ('' !== $image_name2) {
        $update_array['vImage2'] = $vImage2;
    }

    $where = "`iPageId` = '".$iPageId."'";

    $id = $obj->MySQLQueryPerform($tbl_name, $update_array, 'update', $where);

    // ------------------------------ update query ------------------------------

    if ('Add' === $action) {
        $iPageId = $obj->GetInsertId();
    }

    // header("Location:page_action.php?id=".$iPageId.'&success=1');
    if ('Add' === $action) {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INSERT_MSG'];
    } else {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
    }
    $oCache->flushData();
    if (!empty($OPTIMIZE_DATA_OBJ)) {
        $page_ids = explode(',', $OPTIMIZE_DATA_OBJ->page_ids);
        if (in_array($iPageId, $page_ids, true)) {
            $OPTIMIZE_DATA_OBJ->ExecuteMethod('loadStaticPages');
        }
    }
    header('location:'.$backlink);

    exit;
}

// for Edit

if ('Edit' === $action) {
    $sql = 'SELECT * FROM '.$tbl_name." WHERE iPageId = '".$id."'";
    $db_data = $obj->MySQLSelect($sql);
    // echo '<pre>'; print_R($db_data); echo '</pre>'; exit;
    $vLabel = $id;

    if (count($db_data) > 0) {
        for ($i = 0; $i < count($db_master); ++$i) {
            foreach ($db_data as $key => $value) {
                $vPageTitle = 'vPageTitle_'.$db_master[$i]['vCode'];
                ${$vPageTitle} = $value[$vPageTitle];
                $tPageDesc = 'tPageDesc_'.$db_master[$i]['vCode'];
                ${$tPageDesc} = $value[$tPageDesc];

                if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                    $pageSubtitle = $value['pageSubtitle'];
                    $pageSubtitleArr = json_decode($pageSubtitle, true);
                }
                if ('Yes' === $cubexthemeon && 52 === $iPageId) {
                    $pageSubtitle = $value['pageSubtitle'];
                    $pageSubtitleArr = json_decode($pageSubtitle, true);
                }

                $vPageName = $value['vPageName'];
                $vTitle = $value['vTitle'];
                $tMetaKeyword = $value['tMetaKeyword'];
                $tMetaDescription = $value['tMetaDescription'];
                $vImage = $value['vImage'];
                $vImage1 = $value['vImage1'];
                $vImage2 = $value['vImage2'];
                $iOrderBy = $value['iOrderBy']; // added by SP for pages orderby,active/inactive functionality
            }
        }
    }
}

// echo "<pre>"; print_r($pageSubtitleArr); exit;
$serviceArray = $serviceIdArray = [];
$serviceArray = json_decode(serviceCategories, true);
$serviceIdArray = array_column($serviceArray, 'iServiceId');

$become_restaurant = '';
if ('YES' === strtoupper(DELIVERALL)) {
    if (1 === count($serviceIdArray) && 1 === $serviceIdArray[0]) {
        $become_restaurant = $langage_lbl_admin['LBL_RESTAURANT_TXT'];
    } else {
        $become_restaurant = $langage_lbl_admin['LBL_STORE'];
    }
}
$activetab = 'usertab';
$hotelPanel = $MODULES_OBJ->isEnableHotelPanel();

$EN_available = $LANG_OBJ->checkLanguageExist();
$db_master = $LANG_OBJ->getLangDataDefaultFirst($db_master);

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title>Admin | Static Page <?php echo $action; ?></title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />

        <?php include_once 'global_files.php'; ?>
        <!-- PAGE LEVEL STYLES -->
        <link rel="stylesheet" href="../assets/plugins/Font-Awesome/css/font-awesome.css" />
        <link rel="stylesheet" href="../assets/plugins/wysihtml5/dist/bootstrap-wysihtml5-0.0.2.css" />
        <style>
            ul.wysihtml5-toolbar > li {
                position: relative;
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
                            <h2><?php echo $action; ?> Static Page</h2>
                            <a href="page.php" class="back_link">
                                <input type="button" value="Back to Listing" class="add-btn">
                            </a>
                        </div>
                    </div>
                    <hr />
                    <?php include 'valid_msg.php'; ?>
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
                            <?php if ('Yes' === $cubexthemeon && in_array($iPageId, ['1', '52'], true)) {
                                include_once 'aboutus.php';
                            } else { ?>
                                <form method="post" action="" name="_page_form" id="_page_form"  enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                                    <input type="hidden" name="previousLink" id="previousLink" value="<?php echo $previousLink; ?>"/>
                                    <input type="hidden" name="backlink" id="backlink" value="page.php"/>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label>Page/Section</label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <input type="text" class="form-control" name="vPageName"  id="vPageName" value="<?php echo htmlspecialchars($vPageName); ?>" placeholder="Page Name" <?php echo ('Edit' === $action) ? 'readonly disabled' : ''; ?> >
                                        </div>
                                    </div>
                                    <?php if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                                        if (48 === $iPageId) { ?>

                                        <?php if (count($db_master) > 1) { ?>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Page Title <span class="red"> *</span></label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <input type="text" class="form-control <?php echo ('' === $id) ? 'readonly-custom' : ''; ?>" id="vPageSubTitle_Default" value="<?php echo $pageSubtitleArr['pageSubtitle_'.$default_lang]; ?>" data-originalvalue="<?php echo $pageSubtitleArr['pageSubtitle_'.$default_lang]; ?>" readonly="readonly" <?php if ('' === $id) { ?> onclick="editPageSubTitle('Add')" <?php } ?>>
                                            </div>
                                            <?php if ('' !== $id) { ?>
                                            <div class="col-lg-2">
                                                <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editPageSubTitle('Edit')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                            </div>
                                            <?php } ?>
                                        </div>

                                        <div  class="modal fade" id="tPageSubTitle_Modal"  role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                            <div class="modal-dialog" >
                                                <div class="modal-content nimot-class">
                                                    <div class="modal-header">
                                                        <h4>
                                                            <span id="modal_action"></span> Page Title
                                                            <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vPageSubTitle_')">x</button>
                                                        </h4>
                                                    </div>

                                                    <div class="modal-body">
                                                        <?php

                                                                for ($i = 0; $i < $count_all; ++$i) {
                                                                    $vCode = $db_master[$i]['vCode'];
                                                                    $vLTitle = $db_master[$i]['vTitle'];
                                                                    $eDefault = $db_master[$i]['eDefault'];

                                                                    $vPageSubTitleS = "vPageSubTitle_{$vCode}";

                                                                    $vPageSubTitle = "vPageSubTitle[{$vCode}]";

                                                                    $pageSubtitle = 'pageSubtitle_'.$vCode;
                                                                    ${$pageSubtitle} = $pageSubtitleArr[$pageSubtitle];

                                                                    $required = ('Yes' === $eDefault) ? 'required' : '';
                                                                    $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                                                    ?>
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <label>Page Title (<?php echo $vLTitle; ?>) <?php echo $required_msg; ?></label>

                                                                    </div>
                                                                    <div class="col-lg-12">
                                                                        <input type="text" class="form-control" name="<?php echo $vPageSubTitle; ?>" id="<?php echo $vPageSubTitleS; ?>" value="<?php echo ${$pageSubtitle}; ?>" data-originalvalue="<?php echo ${$pageSubtitle}; ?>" placeholder="<?php echo $vLTitle; ?> Value">
                                                                        <div class="text-danger" id="<?php echo $pageSubtitle.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                    </div>

                                                                    <?php
                                                                                if (count($db_master) > 1) {
                                                                                    if ($EN_available) {
                                                                                        if ('EN' === $vCode) { ?>
                                                                            <div class="col-lg-12">
                                                                                <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vPageSubTitle_', 'EN');" style="margin-top: 10px">Convert To All Language</button>
                                                                            </div>
                                                                        <?php }
                                                                                        } else {
                                                                                            if ($vCode === $default_lang) { ?>
                                                                            <div class="col-lg-12">
                                                                                <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vPageSubTitle_', '<?php echo $default_lang; ?>');" style="margin-top: 10px">Convert To All Language</button>
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
                                                        <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                        <div class="nimot-class-but" style="margin-bottom: 0">
                                                            <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="savePageSubTitle()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                            <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vPageSubTitle_')">Cancel</button>
                                                        </div>
                                                    </div>

                                                    <div style="clear:both;"></div>
                                                </div>
                                            </div>

                                        </div>
                                        <?php } else { ?>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Page Title <span class="red"> *</span></label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <input type="text" class="form-control" id="vPageSubTitle_<?php echo $default_lang; ?>" name="vPageSubTitle[<?php echo $default_lang; ?>]" value="<?php echo $pageSubtitleArr['pageSubtitle_'.$default_lang]; ?>" required>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    <?php }
                                        ?>
                                    <input type="hidden" class="form-control" name="vTitle"  id="vTitle" value="<?php echo $vTitle; ?>" placeholder="Meta Title">
                                    <ul class="nav nav-tabs">
                                        <li class="<?php if ('usertab' === $activetab) { ?> active <?php }  ?>">
                                            <a data-toggle="tab" href="#usertab"><?php echo $langage_lbl_admin['LBL_RIDER']; ?></a>
                                        </li>
                                        <li class="<?php if ('drivertab' === $activetab) { ?> active <?php }  ?>">
                                            <a data-toggle="tab" href="#drivertab"><?php echo $langage_lbl_admin['LBL_SIGNIN_DRIVER']; ?></a>
                                        </li>
                                        <?php if ('YES' !== strtoupper(ONLYDELIVERALL)) { ?><li class="<?php if ('companytab' === $activetab) { ?> active <?php }  ?>">
                                            <a data-toggle="tab" href="#companytab"><?php echo $langage_lbl_admin['LBL_COMPANY_SIGNIN']; ?></a>
                                        </li>
                                        <?php } if (!empty($become_restaurant)) { ?><li class="<?php if ('restauranttab' === $activetab) { ?> active <?php }  ?>">
                                            <a data-toggle="tab" href="#restauranttab"><?php echo $become_restaurant; ?></a>
                                        </li>
                                        <?php } if ('YES' === strtoupper($ENABLE_CORPORATE_PROFILE)) { ?><li class="<?php if ('organizationtab' === $activetab) { ?> active <?php }  ?>">
                                            <a data-toggle="tab" href="#organizationtab"><?php echo $langage_lbl_admin['LBL_ORGANIZATION']; ?></a>
                                        </li>
                                        <?php } if ($MODULES_OBJ->isEnableTrackServiceFeature()) { ?><li class="<?php if ('trackservicetab' === $activetab) { ?> active <?php }  ?>">
                                            <a data-toggle="tab" href="#trackservicetab">Tracking Company</a>
                                        </li>
                                        <?php } if (48 === $iPageId) { ?>
                                        <?php if ($hotelPanel > 0) { ?><li class="<?php if ('hoteltab' === $activetab) { ?> active <?php }  ?>">
                                            <a data-toggle="tab" href="#hoteltab"><?php echo $langage_lbl_admin['LBL_HOTEL_LOGIN']; ?></a>
                                        </li>
                                        <?php }
                                        } ?>
                                    </ul>
                                    <div class="tab-content">
                                        <div id="usertab" class="tab-pane <?php if ('usertab' === $activetab) { ?> active <?php }  ?>">

                                            <?php $style_v = '';
                                        if (in_array($iPageId, ['29', '30'], true)) {
                                            $style_v = "style = 'display:none;'";
                                        }
                                        ?>

                                            <?php
                                        if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                                            $vPageTitle_Default_User = 'vPageTitle_'.$default_lang;
                                            $vPageDesc_Default_User = 'tPageDesc_'.$default_lang;
                                            $pagetitlearr = json_decode($db_data[0][$vPageTitle_Default_User], true);
                                            $pagedescarr = json_decode($db_data[0][$vPageDesc_Default_User], true);
                                            $titleval = $pagetitlearr['user_pages'];
                                            $descval = $pagedescarr['user_pages'];
                                        } else {
                                            $titleval = $db_data[0][$vPageTitle_Default_User];
                                            $descval = $db_data[0][$vPageDesc_Default_User];
                                        }
                                        if (count($db_master) > 1) {
                                            if (!in_array($iPageId, [48, 50], true)) {// bcoz no need it in signup page
                                                ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Sub Description</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control" name="vUserPageTitle_Default"  id="vUserPageTitle_Default" readonly="readonly" <?php if ('' === $id) { ?> onclick="editUserPage('Add')" <?php } ?> data-originalvalue="<?php echo $titleval; ?>"><?php echo $titleval; ?></textarea>
                                                </div>
                                                <?php if ('' !== $id) { ?>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editUserPage('Edit')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                                </div>
                                                <?php } ?>
                                            </div>
                                            <?php } ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Description </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control ckeditor" rows="10" name="tUserPageDesc_Default"  id="tUserPageDesc_Default" readonly="readonly"> <?php echo $descval; ?></textarea>
                                                </div>
                                                <?php if ('' !== $id) { ?>
                                                <div class="col-lg-1">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editDescWeb('Edit', 'UserDesc_Modal')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                                </div>
                                                <?php } ?>
                                            </div>

                                            <div  class="modal fade" id="tPageUserDesc_Modal" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg" >
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="modal_action_user"></span> <?php echo $langage_lbl_admin['LBL_RIDER']; ?> Page Sub Description
                                                                <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vUserPageTitle_')">x</button>
                                                            </h4>
                                                        </div>

                                                        <div class="modal-body">
                                                            <?php

                                                                   for ($i = 0; $i < $count_all; ++$i) {
                                                                       $vCode = $db_master[$i]['vCode'];
                                                                       $vLTitle = $db_master[$i]['vTitle'];
                                                                       $eDefault = $db_master[$i]['eDefault'];

                                                                       $vPageTitleU = 'vUserPageTitle_'.$vCode;
                                                                       $vPageTitle = 'vPageTitle_'.$vCode;

                                                                       if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                                                                           $pagetitlearr = json_decode(${$vPageTitle}, true);
                                                                           $titleval = $pagetitlearr['user_pages'];
                                                                       } else {
                                                                           $titleval = ${$vPageTitle};
                                                                       }
                                                                       ?>
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                            <label>Page Sub Description (<?php echo $vLTitle; ?>) <?php echo $required_msg; ?></label>
                                                                        </div>
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
                                                                        <div class="<?php echo $page_title_class; ?>">
                                                                            <textarea class="form-control" name="<?php echo $vPageTitleU; ?>"  id="<?php echo $vPageTitleU; ?>" placeholder="<?php echo $vLTitle; ?> Value" data-originalvalue="<?php echo $titleval; ?>"><?php echo $titleval; ?></textarea>
                                                                            <div class="text-danger" id="<?php echo $vPageTitleU.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                        </div>

                                                                        <?php
                                                                       if (count($db_master) > 1) {
                                                                           if ($EN_available) {
                                                                               if ('EN' === $vCode) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vUserPageTitle_', 'EN');">Convert To All Language</button>
                                                                                </div>
                                                                            <?php }
                                                                               } else {
                                                                                   if ($vCode === $default_lang) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vUserPageTitle_', '<?php echo $default_lang; ?>');">Convert To All Language</button>
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
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveUserPage()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vUserPageTitle_')">Cancel</button>
                                                            </div>
                                                        </div>

                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div  class="modal fade" id="UserDesc_Modal" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg" >
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="modal_action"></span> <?php echo $langage_lbl_admin['LBL_RIDER']; ?> Page Description
                                                                <button type="button" class="close" data-dismiss="modal">x</button>
                                                            </h4>
                                                        </div>

                                                        <div class="modal-body">
                                                            <?php

                                                for ($i = 0; $i < $count_all; ++$i) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vLTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];

                                                    $tPageDescU = 'tUserPageDesc_'.$vCode;
                                                    $tPageDesc = 'tPageDesc_'.$vCode;

                                                    if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                                                        $pagedescarr = json_decode(${$tPageDesc}, true);
                                                        $descval = $pagedescarr['user_pages'];
                                                    } else {
                                                        $descval = ${$tPageDesc};
                                                    }
                                                    ?>

                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                            <label>Page Description (<?php echo $vLTitle; ?>)</label>

                                                                        </div>
                                                                        <div class="col-lg-12">
                                                                            <textarea class="form-control ckeditor" rows="10" name="<?php echo $tPageDescU; ?>"  id="<?php echo $tPageDescU; ?>"  placeholder="<?php echo $tPageDesc; ?> Value"> <?php echo $descval; ?></textarea>
                                                                            <div class="text-danger" id="<?php echo $tPageDescU.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                        </div>
                                                                    </div>
                                                                <?php
                                                }
                                            ?>
                                                        </div>
                                                        <div class="modal-footer" style="margin-top: 0">
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveDescWeb('tUserPageDesc_', 'UserDesc_Modal')"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Cancel</button>
                                                            </div>
                                                        </div>

                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php } else { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Sub Description <span class="red"> *</span></label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control" name="vUserPageTitle_<?php echo $default_lang; ?>"  id="vUserPageTitle_<?php echo $default_lang; ?>"><?php echo $titleval; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Description </label>
                                                </div>
                                                <div class="col-lg-12">
                                                    <textarea class="form-control ckeditor" rows="10" name="tUserPageDesc_<?php echo $default_lang; ?>"  id="tUserPageDesc_<?php echo $default_lang; ?>"> <?php echo $descval; ?></textarea>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <div id="drivertab" class="tab-pane <?php if ('drivertab' === $activetab) { ?> active <?php }  ?>">

                                            <?php $style_v = '';
                                        if (in_array($iPageId, ['29', '30'], true)) {
                                            $style_v = "style = 'display:none;'";
                                        }
                                        ?>
                                            <?php
                                        if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                                            $vPageTitle_Default_Driver = 'vPageTitle_'.$default_lang;
                                            $vPageDesc_Default_Driver = 'tPageDesc_'.$default_lang;
                                            $pagetitlearr = json_decode($db_data[0][$vPageTitle_Default_Driver], true);
                                            $pagedescarr = json_decode($db_data[0][$vPageDesc_Default_Driver], true);
                                            $titleval = $pagetitlearr['provider_pages'];
                                            $descval = $pagedescarr['provider_pages'];
                                        } else {
                                            $titleval = $db_data[0][$vPageTitle_Default_Driver];
                                            $descval = $db_data[0][$vPageDesc_Default_Driver];
                                        }
                                        if (count($db_master) > 1) {
                                            if (!in_array($iPageId, [48, 50], true)) {// bcoz no need it in signup page?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Sub Description</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control" name="vProviderPageTitle_Default"  id="vProviderPageTitle_Default" readonly="readonly" <?php if ('' === $id) { ?> onclick="editProviderPage('Add')" <?php } ?> data-originalvalue="<?php echo $titleval; ?>"><?php echo $titleval; ?></textarea>
                                                </div>
                                                <?php if ('' !== $id) { ?>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editProviderPage('Edit')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                                </div>
                                                <?php } ?>
                                            </div>
                                            <?php } ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Description </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control ckeditor" rows="10" name="tProviderPageDesc_Default"  id="tProviderPageDesc_Default" readonly="readonly"> <?php echo $descval; ?></textarea>
                                                </div>
                                                <?php if ('' !== $id) { ?>
                                                <div class="col-lg-1">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editDescWeb('Edit', 'ProDesc_Modal')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                                </div>
                                                <?php } ?>
                                            </div>

                                            <div  class="modal fade" id="tPageProviderDesc_Modal"  role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg" >
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="modal_action_provider"></span> <?php echo $langage_lbl_admin['LBL_SIGNIN_DRIVER']; ?> Page Sub Description
                                                                <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vProviderPageTitle_')">x</button>
                                                            </h4>
                                                        </div>

                                                        <div class="modal-body">
                                                            <?php

                                                               for ($i = 0; $i < $count_all; ++$i) {
                                                                   $vCode = $db_master[$i]['vCode'];
                                                                   $vLTitle = $db_master[$i]['vTitle'];
                                                                   $eDefault = $db_master[$i]['eDefault'];

                                                                   $vPageTitleU = 'vProviderPageTitle_'.$vCode;
                                                                   $vPageTitle = 'vPageTitle_'.$vCode;

                                                                   if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                                                                       $pagetitlearr = json_decode(${$vPageTitle}, true);
                                                                       $titleval = $pagetitlearr['provider_pages'];
                                                                   } else {
                                                                       $titleval = ${$vPageTitle};
                                                                   }
                                                                   ?>
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                            <label>Page Sub Description (<?php echo $vLTitle; ?>) <?php echo $required_msg; ?></label>
                                                                        </div>
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
                                                                        <div class="<?php echo $page_title_class; ?>">
                                                                            <textarea class="form-control" name="<?php echo $vPageTitleU; ?>"  id="<?php echo $vPageTitleU; ?>" placeholder="<?php echo $vLTitle; ?> Value" data-originalvalue="<?php echo $titleval; ?>"><?php echo $titleval; ?></textarea>
                                                                            <div class="text-danger" id="<?php echo $vPageTitleU.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                        </div>

                                                                        <?php
                                                                   if (count($db_master) > 1) {
                                                                       if ($EN_available) {
                                                                           if ('EN' === $vCode) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vProviderPageTitle_', 'EN');">Convert To All Language</button>
                                                                                </div>
                                                                            <?php }
                                                                           } else {
                                                                               if ($vCode === $default_lang) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vProviderPageTitle_', '<?php echo $default_lang; ?>');">Convert To All Language</button>
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
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveProviderPage()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vProviderPageTitle_')">Cancel</button>
                                                            </div>
                                                        </div>

                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div  class="modal fade" id="ProDesc_Modal" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg" >
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="modal_action"></span> <?php echo $langage_lbl_admin['LBL_SIGNIN_DRIVER']; ?> Page Description
                                                                <button type="button" class="close" data-dismiss="modal">x</button>
                                                            </h4>
                                                        </div>

                                                        <div class="modal-body">
                                                            <?php

                                                for ($i = 0; $i < $count_all; ++$i) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vLTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];

                                                    $tPageDescU = 'tProviderPageDesc_'.$vCode;
                                                    $tPageDesc = 'tPageDesc_'.$vCode;

                                                    if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                                                        $pagedescarr = json_decode(${$tPageDesc}, true);
                                                        $descval = $pagedescarr['provider_pages'];
                                                    } else {
                                                        $descval = ${$tPageDesc};
                                                    }
                                                    ?>
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                            <label>Page Description (<?php echo $vLTitle; ?>)</label>

                                                                        </div>
                                                                        <div class="col-lg-12">
                                                                            <textarea class="form-control ckeditor" rows="10" name="<?php echo $tPageDescU; ?>"  id="<?php echo $tPageDescU; ?>"  placeholder="<?php echo $tPageDesc; ?> Value"> <?php echo $descval; ?></textarea>
                                                                            <div class="text-danger" id="<?php echo $tPageDescU.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                        </div>
                                                                    </div>
                                                                <?php
                                                }
                                            ?>
                                                        </div>
                                                        <div class="modal-footer" style="margin-top: 0">
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveDescWeb('tProviderPageDesc_', 'ProDesc_Modal')"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Cancel</button>
                                                            </div>
                                                        </div>

                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php } else { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Sub Description <span class="red"> *</span></label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control" name="vProviderPageTitle_<?php echo $default_lang; ?>"  id="vProviderPageTitle_<?php echo $default_lang; ?>"><?php echo $titleval; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Description </label>
                                                </div>
                                                <div class="col-lg-12">
                                                    <textarea class="form-control ckeditor" rows="10" name="tProviderPageDesc_<?php echo $default_lang; ?>"  id="tProviderPageDesc_<?php echo $default_lang; ?>"> <?php echo $descval; ?></textarea>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <div id="companytab" class="tab-pane <?php if ('companytab' === $activetab) { ?> active <?php }  ?>">
                                            <?php $style_v = '';
                                        if (in_array($iPageId, ['29', '30'], true)) {
                                            $style_v = "style = 'display:none;'";
                                        }
                                        ?>
                                            <?php
                                        if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                                            $vPageTitle_Default_Company = 'vPageTitle_'.$default_lang;
                                            $vPageDesc_Default_Company = 'tPageDesc_'.$default_lang;
                                            $pagetitlearr = json_decode($db_data[0][$vPageTitle_Default_Company], true);
                                            $pagedescarr = json_decode($db_data[0][$vPageDesc_Default_Company], true);
                                            $titleval = $pagetitlearr['company_pages'];
                                            $descval = $pagedescarr['company_pages'];
                                        } else {
                                            $titleval = $db_data[0][$vPageTitle_Default_Company];
                                            $descval = $db_data[0][$vPageDesc_Default_Company];
                                        }
                                        if (count($db_master) > 1) { ?>
                                                <?php if (!in_array($iPageId, [48], true)) { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Sub Description</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control" name="vCompanyPageTitle_Default"  id="vCompanyPageTitle_Default" rows="3" readonly="readonly" <?php if ('' === $id) { ?> onclick="editCompanyPage('Add')" <?php } ?> data-originalvalue="<?php echo $titleval; ?>"><?php echo $titleval; ?></textarea>
                                                </div>
                                                <?php if ('' !== $id) { ?>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editCompanyPage('Edit')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                                </div>
                                                <?php } ?>
                                            </div>
                                            <?php } ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Description </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control ckeditor" rows="10" name="tCompanyPageDesc_Default"  id="tCompanyPageDesc_Default" readonly="readonly"> <?php echo $descval; ?></textarea>
                                                </div>
                                                <?php if ('' !== $id) { ?>
                                                <div class="col-lg-1">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editDescWeb('Edit', 'CompDesc_Modal')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                                </div>
                                                <?php } ?>
                                            </div>

                                            <div  class="modal fade" id="tPageCompanyDesc_Modal" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg" >
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="modal_action_company"></span> <?php echo $langage_lbl_admin['LBL_COMPANY_SIGNIN']; ?> Page Sub Description
                                                                <button type="button" class="close" data-dismiss="modal" >x</button>
                                                            </h4>
                                                        </div>

                                                        <div class="modal-body">
                                                            <?php

                                                            for ($i = 0; $i < $count_all; ++$i) {
                                                                $vCode = $db_master[$i]['vCode'];
                                                                $vLTitle = $db_master[$i]['vTitle'];
                                                                $eDefault = $db_master[$i]['eDefault'];

                                                                $vPageTitleU = 'vCompanyPageTitle_'.$vCode;
                                                                $vPageTitle = 'vPageTitle_'.$vCode;

                                                                if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                                                                    $pagetitlearr = json_decode(${$vPageTitle}, true);
                                                                    $titleval = $pagetitlearr['company_pages'];
                                                                } else {
                                                                    $titleval = ${$vPageTitle};
                                                                }
                                                                ?>
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                            <label>Page Sub Description (<?php echo $vLTitle; ?>) <?php echo $required_msg; ?></label>
                                                                        </div>
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
                                                                        <div class="<?php echo $page_title_class; ?>">
                                                                            <textarea class="form-control" name="<?php echo $vPageTitleU; ?>"  id="<?php echo $vPageTitleU; ?>" placeholder="<?php echo $vLTitle; ?> Value" data-originalvalue="<?php echo $titleval; ?>"><?php echo $titleval; ?></textarea>
                                                                            <div class="text-danger" id="<?php echo $vPageTitleU.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                        </div>

                                                                        <?php
                                                                if (count($db_master) > 1) {
                                                                    if ($EN_available) {
                                                                        if ('EN' === $vCode) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vCompanyPageTitle_', 'EN');">Convert To All Language</button>
                                                                                </div>
                                                                            <?php }
                                                                        } else {
                                                                            if ($vCode === $default_lang) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vCompanyPageTitle_', '<?php echo $default_lang; ?>');">Convert To All Language</button>
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
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveCompanyPage()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vCompanyPageTitle_')">Cancel</button>
                                                            </div>
                                                        </div>

                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div  class="modal fade" id="CompDesc_Modal"  role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg" >
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="modal_action"></span> <?php echo $langage_lbl_admin['LBL_COMPANY_SIGNIN']; ?> Page Description
                                                                <button type="button" class="close" data-dismiss="modal">x</button>
                                                            </h4>
                                                        </div>

                                                        <div class="modal-body">
                                                            <?php

                                                for ($i = 0; $i < $count_all; ++$i) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vLTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];

                                                    $tPageDescU = 'tCompanyPageDesc_'.$vCode;
                                                    $tPageDesc = 'tPageDesc_'.$vCode;

                                                    if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                                                        $pagedescarr = json_decode(${$tPageDesc}, true);
                                                        $descval = $pagedescarr['company_pages'];
                                                    } else {
                                                        $descval = ${$tPageDesc};
                                                    }
                                                    ?>
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                            <label>Page Description (<?php echo $vLTitle; ?>)</label>

                                                                        </div>
                                                                        <div class="col-lg-12">
                                                                            <textarea class="form-control ckeditor" rows="10" name="<?php echo $tPageDescU; ?>"  id="<?php echo $tPageDescU; ?>"  placeholder="<?php echo $tPageDesc; ?> Value"> <?php echo $descval; ?></textarea>
                                                                            <div class="text-danger" id="<?php echo $tPageDescU.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                        </div>
                                                                    </div>
                                                                <?php
                                                }
                                            ?>
                                                        </div>
                                                        <div class="modal-footer" style="margin-top: 0">
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveDescWeb('tCompanyPageDesc_', 'CompDesc_Modal')"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Cancel</button>
                                                            </div>
                                                        </div>

                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php } else { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Sub Description <span class="red"> *</span></label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control" name="vCompanyPageTitle_<?php echo $default_lang; ?>"  id="vCompanyPageTitle_<?php echo $default_lang; ?>"><?php echo $titleval; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Description </label>
                                                </div>
                                                <div class="col-lg-12">
                                                    <textarea class="form-control ckeditor" rows="10" name="tCompanyPageDesc_<?php echo $default_lang; ?>"  id="tCompanyPageDesc_<?php echo $default_lang; ?>"> <?php echo $descval; ?></textarea>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <div id="restauranttab" class="tab-pane <?php if ('restauranttab' === $activetab) { ?> active <?php }  ?>">
                                            <?php $style_v = '';
                                        if (in_array($iPageId, ['29', '30'], true)) {
                                            $style_v = "style = 'display:none;'";
                                        }
                                        ?>
                                            <?php
                                        if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                                            $vPageTitle_Default_Restaurant = 'vPageTitle_'.$default_lang;
                                            $vPageDesc_Default_Restaurant = 'tPageDesc_'.$default_lang;
                                            $pagetitlearr = json_decode($db_data[0][$vPageTitle_Default_Restaurant], true);
                                            $pagedescarr = json_decode($db_data[0][$vPageDesc_Default_Restaurant], true);
                                            $titleval = $pagetitlearr['restaurant_pages'];
                                            $descval = $pagedescarr['restaurant_pages'];
                                        } else {
                                            $titleval = $db_data[0][$vPageTitle_Default_Restaurant];
                                            $descval = $db_data[0][$vPageDesc_Default_Restaurant];
                                        }
                                        if (count($db_master) > 1) { ?>
                                              <?php if (!in_array($iPageId, [48], true)) { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Sub Description</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control" name="vRestaurantPageTitle_Default"  id="vRestaurantPageTitle_Default" rows="3" readonly="readonly" <?php if ('' === $id) { ?> onclick="editRestaurantPage('Add')" <?php } ?> data-originalvalue="<?php echo $titleval; ?>"><?php echo $titleval; ?></textarea>
                                                </div>
                                                <?php if ('' !== $id) { ?>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editRestaurantPage('Edit')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                                </div>
                                                <?php } ?>
                                            </div>
                                            <?php } ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Description </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control ckeditor" rows="10" name="tRestaurantPageDesc_Default"  id="tRestaurantPageDesc_Default" readonly="readonly"> <?php echo $descval; ?></textarea>
                                                </div>
                                                <?php if ('' !== $id) { ?>
                                                <div class="col-lg-1">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editDescWeb('Edit', 'RestDesc_Modal')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                                </div>
                                                <?php } ?>
                                            </div>

                                            <div  class="modal fade" id="tRestaurantPageDesc_Modal"  role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg" >
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="modal_action_store"></span> <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT']; ?> Page Sub Description
                                                                <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vRestaurantPageTitle_')">x</button>
                                                            </h4>
                                                        </div>

                                                        <div class="modal-body">
                                                            <?php

                                                            for ($i = 0; $i < $count_all; ++$i) {
                                                                $vCode = $db_master[$i]['vCode'];
                                                                $vLTitle = $db_master[$i]['vTitle'];
                                                                $eDefault = $db_master[$i]['eDefault'];

                                                                $vPageTitleU = 'vRestaurantPageTitle_'.$vCode;
                                                                $vPageTitle = 'vPageTitle_'.$vCode;

                                                                if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                                                                    $pagetitlearr = json_decode(${$vPageTitle}, true);
                                                                    $titleval = $pagetitlearr['restaurant_pages'];
                                                                } else {
                                                                    $titleval = ${$vPageTitle};
                                                                }
                                                                ?>
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                            <label>Page Sub Description (<?php echo $vLTitle; ?>) <?php echo $required_msg; ?></label>
                                                                        </div>
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
                                                                        <div class="<?php echo $page_title_class; ?>">
                                                                            <textarea class="form-control" name="<?php echo $vPageTitleU; ?>"  id="<?php echo $vPageTitleU; ?>" placeholder="<?php echo $vLTitle; ?> Value" data-originalvalue="<?php echo $titleval; ?>"><?php echo $titleval; ?></textarea>
                                                                            <div class="text-danger" id="<?php echo $vPageTitleU.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                        </div>

                                                                        <?php
                                                                if (count($db_master) > 1) {
                                                                    if ($EN_available) {
                                                                        if ('EN' === $vCode) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vRestaurantPageTitle_', 'EN');">Convert To All Language</button>
                                                                                </div>
                                                                            <?php }
                                                                        } else {
                                                                            if ($vCode === $default_lang) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vRestaurantPageTitle_', '<?php echo $default_lang; ?>');">Convert To All Language</button>
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
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveRestaurantPage()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vRestaurantPageTitle_')">Cancel</button>
                                                            </div>
                                                        </div>

                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div  class="modal fade" id="RestDesc_Modal" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg" >
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="modal_action"></span> <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT']; ?> Page Description
                                                                <button type="button" class="close" data-dismiss="modal">x</button>
                                                            </h4>
                                                        </div>

                                                        <div class="modal-body">
                                                            <?php

                                                for ($i = 0; $i < $count_all; ++$i) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vLTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];

                                                    $tPageDescU = 'tRestaurantPageDesc_'.$vCode;
                                                    $tPageDesc = 'tPageDesc_'.$vCode;

                                                    if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                                                        $pagedescarr = json_decode(${$tPageDesc}, true);
                                                        $descval = $pagedescarr['restaurant_pages'];
                                                    } else {
                                                        $descval = ${$tPageDesc};
                                                    }
                                                    ?>
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                            <label>Page Description (<?php echo $vLTitle; ?>)</label>

                                                                        </div>
                                                                        <div class="col-lg-12">
                                                                            <textarea class="form-control ckeditor" rows="10" name="<?php echo $tPageDescU; ?>"  id="<?php echo $tPageDescU; ?>"  placeholder="<?php echo $tPageDesc; ?> Value"> <?php echo $descval; ?></textarea>
                                                                            <div class="text-danger" id="<?php echo $tPageDescU.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                        </div>
                                                                    </div>
                                                                <?php
                                                }
                                            ?>
                                                        </div>
                                                        <div class="modal-footer" style="margin-top: 0">
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveDescWeb('tRestaurantPageDesc_', 'RestDesc_Modal')"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Cancel</button>
                                                            </div>
                                                        </div>

                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php } else { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Sub Description <span class="red"> *</span></label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control" name="vRestaurantPageTitle_<?php echo $default_lang; ?>"  id="vRestaurantPageTitle_<?php echo $default_lang; ?>"><?php echo $titleval; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Description </label>
                                                </div>
                                                <div class="col-lg-12">
                                                    <textarea class="form-control ckeditor" rows="10" name="tRestaurantPageDesc_<?php echo $default_lang; ?>"  id="tRestaurantPageDesc_<?php echo $default_lang; ?>"> <?php echo $descval; ?></textarea>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <div id="organizationtab" class="tab-pane <?php if ('organizationtab' === $activetab) { ?> active <?php }  ?>">
                                            <?php $style_v = '';
                                        if (in_array($iPageId, ['29', '30'], true)) {
                                            $style_v = "style = 'display:none;'";
                                        }
                                        ?>
                                            <?php
                                        if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                                            $vPageTitle_Default_Org = 'vPageTitle_'.$default_lang;
                                            $vPageDesc_Default_Org = 'tPageDesc_'.$default_lang;
                                            $pagetitlearr = json_decode($db_data[0][$vPageTitle_Default_Org], true);
                                            $pagedescarr = json_decode($db_data[0][$vPageDesc_Default_Org], true);
                                            $titleval = $pagetitlearr['org_pages'];
                                            $descval = $pagedescarr['org_pages'];
                                        } else {
                                            $titleval = $db_data[0][$vPageTitle_Default_Org];
                                            $descval = $db_data[0][$vPageDesc_Default_Org];
                                        }
                                        if (count($db_master) > 1) { ?>
                                                <?php if (!in_array($iPageId, [48], true)) { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Sub Description</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control" name="vOrgPageTitle_Default"  id="vOrgPageTitle_Default" rows="3" readonly="readonly" <?php if ('' === $id) { ?> onclick="editOrgPage('Add')" <?php } ?> data-originalvalue="<?php echo $titleval; ?>"><?php echo $titleval; ?></textarea>
                                                </div>
                                                <?php if ('' !== $id) { ?>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editOrgPage('Edit')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                                </div>
                                                <?php } ?>
                                            </div>
                                            <?php } ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Description </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control ckeditor" rows="10" name="tOrgPageDesc_Default"  id="tOrgPageDesc_Default" readonly="readonly"> <?php echo $descval; ?></textarea>
                                                </div>
                                                <?php if ('' !== $id) { ?>
                                                <div class="col-lg-1">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editDescWeb('Edit', 'OrgDesc_Modal')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                                </div>
                                                <?php } ?>
                                            </div>

                                            <div  class="modal fade" id="tOrgPageDesc_Modal" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg" >
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="modal_action_org"></span> <?php echo $langage_lbl_admin['LBL_ORGANIZATION']; ?> Page Sub Description
                                                                <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vOrgPageTitle_')">x</button>
                                                            </h4>
                                                        </div>

                                                        <div class="modal-body">
                                                            <?php

                                                            for ($i = 0; $i < $count_all; ++$i) {
                                                                $vCode = $db_master[$i]['vCode'];
                                                                $vLTitle = $db_master[$i]['vTitle'];
                                                                $eDefault = $db_master[$i]['eDefault'];

                                                                $vPageTitleU = 'vOrgPageTitle_'.$vCode;
                                                                $vPageTitle = 'vPageTitle_'.$vCode;

                                                                if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                                                                    $pagetitlearr = json_decode(${$vPageTitle}, true);
                                                                    $titleval = $pagetitlearr['org_pages'];
                                                                } else {
                                                                    $titleval = ${$vPageTitle};
                                                                }
                                                                ?>
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                            <label>Page Sub Description (<?php echo $vLTitle; ?>) <?php echo $required_msg; ?></label>
                                                                        </div>
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
                                                                        <div class="<?php echo $page_title_class; ?>">
                                                                            <textarea class="form-control" name="<?php echo $vPageTitleU; ?>"  id="<?php echo $vPageTitleU; ?>" placeholder="<?php echo $vLTitle; ?> Value" data-originalvalue="<?php echo $titleval; ?>"><?php echo $titleval; ?></textarea>
                                                                            <div class="text-danger" id="<?php echo $vPageTitleU.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                        </div>

                                                                        <?php
                                                                if (count($db_master) > 1) {
                                                                    if ($EN_available) {
                                                                        if ('EN' === $vCode) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vOrgPageTitle_', 'EN');">Convert To All Language</button>
                                                                                </div>
                                                                            <?php }
                                                                        } else {
                                                                            if ($vCode === $default_lang) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vOrgPageTitle_', '<?php echo $default_lang; ?>');">Convert To All Language</button>
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
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveOrgPage()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vOrgPageTitle_')">Cancel</button>
                                                            </div>
                                                        </div>

                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div  class="modal fade" id="OrgDesc_Modal" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg" >
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="modal_action"></span> <?php echo $langage_lbl_admin['LBL_ORGANIZATION']; ?> Page Description
                                                                <button type="button" class="close" data-dismiss="modal">x</button>
                                                            </h4>
                                                        </div>

                                                        <div class="modal-body">
                                                            <?php

                                                for ($i = 0; $i < $count_all; ++$i) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vLTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];

                                                    $tPageDescU = 'tOrgPageDesc_'.$vCode;
                                                    $tPageDesc = 'tPageDesc_'.$vCode;

                                                    if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                                                        $pagedescarr = json_decode(${$tPageDesc}, true);
                                                        $descval = $pagedescarr['org_pages'];
                                                    } else {
                                                        $descval = ${$tPageDesc};
                                                    }
                                                    ?>

                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                            <label>Page Description (<?php echo $vLTitle; ?>)</label>

                                                                        </div>
                                                                        <div class="col-lg-12">
                                                                            <textarea class="form-control ckeditor" rows="10" name="<?php echo $tPageDescU; ?>"  id="<?php echo $tPageDescU; ?>"  placeholder="<?php echo $tPageDesc; ?> Value"> <?php echo $descval; ?></textarea>
                                                                            <div class="text-danger" id="<?php echo $tPageDescU.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                        </div>
                                                                    </div>
                                                                <?php
                                                }
                                            ?>
                                                        </div>
                                                        <div class="modal-footer" style="margin-top: 0">
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveDescWeb('tOrgPageDesc_', 'OrgDesc_Modal')"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Cancel</button>
                                                            </div>
                                                        </div>

                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php } else { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Sub Description <span class="red"> *</span></label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control" name="vOrgPageTitle_<?php echo $default_lang; ?>"  id="vOrgPageTitle_<?php echo $default_lang; ?>"><?php echo $titleval; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Description </label>
                                                </div>
                                                <div class="col-lg-12">
                                                    <textarea class="form-control ckeditor" rows="10" name="tOrgPageDesc_<?php echo $default_lang; ?>"  id="tOrgPageDesc_<?php echo $default_lang; ?>"> <?php echo $descval; ?></textarea>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>

                                        <div id="trackservicetab" class="tab-pane <?php if ('trackservicetab' === $activetab) { ?> active <?php }  ?>">
                                            <?php $style_v = '';
                                        if (in_array($iPageId, ['29', '30'], true)) {
                                            $style_v = "style = 'display:none;'";
                                        }
                                        ?>
                                            <?php
                                        if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                                            $vPageTitle_Default_TrackService = 'vPageTitle_'.$default_lang;
                                            $vPageDesc_Default_TrackService = 'tPageDesc_'.$default_lang;
                                            $pagetitlearr = json_decode($db_data[0][$vPageTitle_Default_TrackService], true);
                                            $pagedescarr = json_decode($db_data[0][$vPageDesc_Default_TrackService], true);
                                            $titleval = $pagetitlearr['trackservice_pages'];
                                            $descval = $pagedescarr['trackservice_pages'];
                                        } else {
                                            $titleval = $db_data[0][$vPageTitle_Default_TrackService];
                                            $descval = $db_data[0][$vPageDesc_Default_TrackService];
                                        }
                                        if (count($db_master) > 1) {
                                            ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Sub Description</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control" name="vTrackServicePageTitle_Default" rows="3" id="vTrackServicePageTitle_Default" readonly="readonly" <?php if ('' === $id) { ?> onclick="editTrackServicePage('Add')" <?php } ?> data-originalvalue="<?php echo $titleval; ?>"><?php echo $titleval; ?></textarea>
                                                </div>
                                                <?php if ('' !== $id) { ?>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editTrackServicePage('Edit')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                                </div>
                                                <?php } ?>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Description </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control ckeditor" rows="10" name="tTrackServicePageDesc_Default"  id="tTrackServicePageDesc_Default" readonly="readonly"> <?php echo $descval; ?></textarea>
                                                </div>
                                                <?php if ('' !== $id) { ?>
                                                <div class="col-lg-1">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editDescWeb('Edit', 'TrackServiceDesc_Modal')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                                </div>
                                                <?php } ?>
                                            </div>

                                            <div  class="modal fade" id="tTrackServicePageDesc_Modal" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg" >
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="modal_action_TrackService"></span> Tracking Company Page Sub Description
                                                                <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vTrackServicePageTitle_')">x</button>
                                                            </h4>
                                                        </div>

                                                        <div class="modal-body">
                                                            <?php

                                                               for ($i = 0; $i < $count_all; ++$i) {
                                                                   $vCode = $db_master[$i]['vCode'];
                                                                   $vLTitle = $db_master[$i]['vTitle'];
                                                                   $eDefault = $db_master[$i]['eDefault'];

                                                                   $vPageTitleU = 'vTrackServicePageTitle_'.$vCode;
                                                                   $vPageTitle = 'vPageTitle_'.$vCode;

                                                                   if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                                                                       $pagetitlearr = json_decode(${$vPageTitle}, true);
                                                                       $titleval = $pagetitlearr['trackservice_pages'];
                                                                   } else {
                                                                       $titleval = ${$vPageTitle};
                                                                   }
                                                                   ?>
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                            <label>Page Sub Description (<?php echo $vLTitle; ?>) <?php echo $required_msg; ?></label>
                                                                        </div>
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
                                                                        <div class="<?php echo $page_title_class; ?>">
                                                                            <textarea class="form-control" name="<?php echo $vPageTitleU; ?>"  id="<?php echo $vPageTitleU; ?>" placeholder="<?php echo $vLTitle; ?> Value" data-originalvalue="<?php echo $titleval; ?>"><?php echo $titleval; ?></textarea>
                                                                            <div class="text-danger" id="<?php echo $vPageTitleU.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                        </div>

                                                                        <?php
                                                                   if (count($db_master) > 1) {
                                                                       if ($EN_available) {
                                                                           if ('EN' === $vCode) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vTrackServicePageTitle_', 'EN');">Convert To All Language</button>
                                                                                </div>
                                                                            <?php }
                                                                           } else {
                                                                               if ($vCode === $default_lang) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vTrackServicePageTitle_', '<?php echo $default_lang; ?>');">Convert To All Language</button>
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
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveTrackServicePage()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vTrackServicePageTitle_')">Cancel</button>
                                                            </div>
                                                        </div>

                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div  class="modal fade" id="TrackServiceDesc_Modal" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg" >
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="modal_action"></span> Tracking Company Page Description
                                                                <button type="button" class="close" data-dismiss="modal">x</button>
                                                            </h4>
                                                        </div>

                                                        <div class="modal-body">
                                                            <?php

                                                for ($i = 0; $i < $count_all; ++$i) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vLTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];

                                                    $tPageDescU = 'tTrackServicePageDesc_'.$vCode;
                                                    $tPageDesc = 'tPageDesc_'.$vCode;

                                                    if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                                                        $pagedescarr = json_decode(${$tPageDesc}, true);
                                                        $descval = $pagedescarr['trackservice_pages'];
                                                    } else {
                                                        $descval = ${$tPageDesc};
                                                    }
                                                    ?>

                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                            <label>Page Description (<?php echo $vLTitle; ?>)</label>

                                                                        </div>
                                                                        <div class="col-lg-12">
                                                                            <textarea class="form-control ckeditor" rows="10" name="<?php echo $tPageDescU; ?>"  id="<?php echo $tPageDescU; ?>"  placeholder="<?php echo $tPageDesc; ?> Value"> <?php echo $descval; ?></textarea>
                                                                            <div class="text-danger" id="<?php echo $tPageDescU.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                        </div>
                                                                    </div>
                                                                <?php
                                                }
                                            ?>
                                                        </div>
                                                        <div class="modal-footer" style="margin-top: 0">
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveDescWeb('tTrackServicePageDesc_', 'TrackServiceDesc_Modal')"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Cancel</button>
                                                            </div>
                                                        </div>

                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php } else { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Sub Description <span class="red"> *</span></label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control" name="vTrackServicePageTitle_<?php echo $default_lang; ?>"  id="vTrackServicePageTitle_<?php echo $default_lang; ?>"><?php echo $titleval; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Description </label>
                                                </div>
                                                <div class="col-lg-12">
                                                    <textarea class="form-control ckeditor" rows="10" name="tTrackServicePageDesc_<?php echo $default_lang; ?>"  id="tOrgPageDesc_<?php echo $default_lang; ?>"> <?php echo $descval; ?></textarea>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>

                                        <div id="hoteltab" class="tab-pane <?php if ('hoteltab' === $activetab) { ?> active <?php }  ?>">

                                            <?php $style_v = '';
                                        if (in_array($iPageId, ['29', '30'], true)) {
                                            $style_v = "style = 'display:none;'";
                                        }
                                        ?>
                                            <?php
                                        if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                                            $vPageTitle_Default_Hotel = 'vPageTitle_'.$default_lang;
                                            $vPageDesc_Default_Hotel = 'tPageDesc_'.$default_lang;
                                            $pagetitlearr = json_decode($db_data[0][$vPageTitle_Default_Hotel], true);
                                            $pagedescarr = json_decode($db_data[0][$vPageDesc_Default_Hotel], true);
                                            $titleval = $pagetitlearr['hotel_pages'];
                                            $descval = $pagedescarr['hotel_pages'];
                                        } else {
                                            $titleval = $db_data[0][$vPageTitle_Default_Hotel];
                                            $descval = $db_data[0][$vPageDesc_Default_Hotel];
                                        }
                                        if (count($db_master) > 1) { ?>
                                             <?php if (!in_array($iPageId, [48], true)) { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Sub Description</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control" name="vHotelPageTitle_Default"  id="vHotelPageTitle_Default" readonly="readonly" <?php if ('' === $id) { ?> onclick="editHotelPage('Add')" <?php } ?> data-originalvalue="<?php echo $titleval; ?>"><?php echo $titleval; ?></textarea>
                                                </div>
                                                <?php if ('' !== $id) { ?>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editHotelPage('Edit')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                                </div>
                                                <?php } ?>
                                            </div>
                                            <?php } ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Description </label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control ckeditor" rows="10" name="tHotelPageDesc_Default"  id="tHotelPageDesc_Default" readonly="readonly"> <?php echo $descval; ?></textarea>
                                                </div>
                                                <?php if ('' !== $id) { ?>
                                                <div class="col-lg-1">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editDescWeb('Edit', 'HotelDesc_Modal')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                                </div>
                                                <?php } ?>
                                            </div>

                                            <div  class="modal fade" id="tHotelPageDesc_Modal" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg" >
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="modal_action_hotel"></span> <?php echo $langage_lbl_admin['LBL_HOTEL_LOGIN']; ?> Page Sub Description
                                                                <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vHotelPageTitle_')">x</button>
                                                            </h4>
                                                        </div>

                                                        <div class="modal-body">
                                                            <?php

                                                            for ($i = 0; $i < $count_all; ++$i) {
                                                                $vCode = $db_master[$i]['vCode'];
                                                                $vLTitle = $db_master[$i]['vTitle'];
                                                                $eDefault = $db_master[$i]['eDefault'];

                                                                $vPageTitleU = 'vHotelPageTitle_'.$vCode;
                                                                $vPageTitle = 'vPageTitle_'.$vCode;

                                                                if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                                                                    $pagetitlearr = json_decode(${$vPageTitle}, true);
                                                                    $titleval = $pagetitlearr['hotel_pages'];
                                                                } else {
                                                                    $titleval = ${$vPageTitle};
                                                                }
                                                                ?>
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                            <label>Page Sub Description (<?php echo $vLTitle; ?>) <?php echo $required_msg; ?></label>
                                                                        </div>
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
                                                                        <div class="<?php echo $page_title_class; ?>">
                                                                            <textarea class="form-control" name="<?php echo $vPageTitleU; ?>"  id="<?php echo $vPageTitleU; ?>" placeholder="<?php echo $vLTitle; ?> Value" data-originalvalue="<?php echo $titleval; ?>"><?php echo $titleval; ?></textarea>
                                                                            <div class="text-danger" id="<?php echo $vPageTitleU.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                        </div>

                                                                        <?php
                                                                if (count($db_master) > 1) {
                                                                    if ($EN_available) {
                                                                        if ('EN' === $vCode) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vHotelPageTitle_', 'EN');" >Convert To All Language</button>
                                                                                </div>
                                                                            <?php }
                                                                        } else {
                                                                            if ($vCode === $default_lang) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vHotelPageTitle_', '<?php echo $default_lang; ?>');">Convert To All Language</button>
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
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveHotelPage()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vHotelPageTitle_')">Cancel</button>
                                                            </div>
                                                        </div>

                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div  class="modal fade" id="HotelDesc_Modal" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg" >
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="modal_action"></span> <?php echo $langage_lbl_admin['LBL_HOTEL_LOGIN']; ?> Page Description
                                                                <button type="button" class="close" data-dismiss="modal">x</button>
                                                            </h4>
                                                        </div>

                                                        <div class="modal-body">
                                                            <?php

                                                for ($i = 0; $i < $count_all; ++$i) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vLTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];

                                                    $tPageDescU = 'tHotelPageDesc_'.$vCode;
                                                    $tPageDesc = 'tPageDesc_'.$vCode;

                                                    if ('Yes' === $cubexthemeon && in_array($iPageId, $pageArray, true)) {
                                                        $pagedescarr = json_decode(${$tPageDesc}, true);
                                                        $descval = $pagedescarr['hotel_pages'];
                                                    } else {
                                                        $descval = ${$tPageDesc};
                                                    }
                                                    ?>

                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                            <label>Page Description (<?php echo $vLTitle; ?>)</label>

                                                                        </div>
                                                                        <div class="col-lg-12">
                                                                            <textarea class="form-control ckeditor" rows="10" name="<?php echo $tPageDescU; ?>"  id="<?php echo $tPageDescU; ?>"  placeholder="<?php echo $tPageDesc; ?> Value"> <?php echo $descval; ?></textarea>
                                                                            <div class="text-danger" id="<?php echo $tPageDescU.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                        </div>
                                                                    </div>
                                                                <?php
                                                }
                                            ?>
                                                        </div>
                                                        <div class="modal-footer" style="margin-top: 0">
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveDescWeb('tHotelPageDesc_', 'HotelDesc_Modal')"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Cancel</button>
                                                            </div>
                                                        </div>

                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php } else { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Sub Description <span class="red"> *</span></label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control" name="vHotelPageTitle_<?php echo $default_lang; ?>"  id="vHotelPageTitle_<?php echo $default_lang; ?>"><?php echo $titleval; ?></textarea>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Description </label>
                                                </div>
                                                <div class="col-lg-12">
                                                    <textarea class="form-control ckeditor" rows="10" name="tHotelPageDesc_<?php echo $default_lang; ?>"  id="tHotelPageDesc_<?php echo $default_lang; ?>"> <?php echo $descval; ?></textarea>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php } else { ?>

                                        <!--<textarea class="form-control ckeditor" rows="10" name="aaa"  id="editortest"  placeholder="aa Value"></textarea>-->
                                        <?php
                                        $style_v = '';
                                        if (in_array($iPageId, ['29', '30', '53'], true)) {
                                            $style_v = "style = 'display:none;'";
                                        }
                                        if (count($db_master) > 1) {
                                            if (!in_array($iPageId, ['55', '56'], true)) {
                                                ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Title <span class="red"> *</span></label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <input type="text" class="form-control <?php echo ('' === $id) ? 'readonly-custom' : ''; ?>" id="vPageTitle_Default" value="<?php echo $db_data[0]['vPageTitle_'.$default_lang]; ?>" data-originalvalue="<?php echo $db_data[0]['vPageTitle_'.$default_lang]; ?>" readonly="readonly" <?php if ('' === $id) { ?> onclick="editPage('Add')" <?php } ?>>
                                                </div>
                                                <?php if ('' !== $id) { ?>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editPage('Edit')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                                </div>
                                                <?php } ?>
                                            </div>
                                            <?php } ?>
                                            <?php if (!in_array($iPageId, ['53'], true)) { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Description <span class="red"> *</span></label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <textarea class="form-control ckeditor" rows="10" id="tPageDesc_Default" readonly="readonly"><?php echo $db_data[0]['tPageDesc_'.$default_lang]; ?></textarea>
                                                </div>
                                                <?php if ('' !== $id) { ?>
                                                <div class="col-lg-2">
                                                    <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editDescWeb('Edit', 'PageDesc_Modal')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                                </div>
                                                <?php } ?>
                                            </div>

                                            <div  class="modal fade" id="tPageDesc_Modal" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg" >
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="modal_action"></span> Page Title
                                                                <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vPageTitle_')">x</button>
                                                            </h4>
                                                        </div>

                                                        <div class="modal-body">
                                                            <?php

                                                                    for ($i = 0; $i < $count_all; ++$i) {
                                                                        $vCode = $db_master[$i]['vCode'];
                                                                        $vLTitle = $db_master[$i]['vTitle'];
                                                                        $eDefault = $db_master[$i]['eDefault'];

                                                                        $vPageTitle = 'vPageTitle_'.$vCode;

                                                                        if ('' === $style_v) {
                                                                            $required = ('Yes' === $eDefault) ? 'required' : '';
                                                                            $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                                                        }
                                                                        ?>
                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                            <label>Page Title (<?php echo $vLTitle; ?>) <?php echo $required_msg; ?></label>
                                                                        </div>
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
                                                                        <div class="<?php echo $page_title_class; ?>">
                                                                            <input type="text" class="form-control" name="<?php echo $vPageTitle; ?>" id="<?php echo $vPageTitle; ?>" value="<?php echo ${$vPageTitle}; ?>" data-originalvalue="<?php echo ${$vPageTitle}; ?>" placeholder="<?php echo $vLTitle; ?> Value">
                                                                            <div class="text-danger" id="<?php echo $vPageTitle.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                        </div>

                                                                        <?php
                                                                        if (count($db_master) > 1) {
                                                                            if ($EN_available) {
                                                                                if ('EN' === $vCode) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vPageTitle_', 'EN');" >Convert To All Language</button>
                                                                                </div>
                                                                            <?php }
                                                                                } else {
                                                                                    if ($vCode === $default_lang) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vPageTitle_', '<?php echo $default_lang; ?>');" >Convert To All Language</button>
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
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="savePage()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vPageTitle_')">Cancel</button>
                                                            </div>
                                                        </div>

                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>

                                            </div>
                                            <?php } ?>
                                            <div  class="modal fade" id="PageDesc_Modal" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                                <div class="modal-dialog modal-lg" >
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="modal_action"></span> Page Description
                                                                <button type="button" class="close" data-dismiss="modal" >x</button>
                                                            </h4>
                                                        </div>

                                                        <div class="modal-body">
                                                            <?php

                                                    for ($i = 0; $i < $count_all; ++$i) {
                                                        $vCode = $db_master[$i]['vCode'];
                                                        $vLTitle = $db_master[$i]['vTitle'];
                                                        $eDefault = $db_master[$i]['eDefault'];

                                                        $tPageDesc = 'tPageDesc_'.$vCode;

                                                        if ('' === $style_v) {
                                                            $required = ('Yes' === $eDefault) ? 'required' : '';
                                                            $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                                        }
                                                        ?>

                                                                    <div class="row">
                                                                        <div class="col-lg-12">
                                                                            <label>Page Description (<?php echo $vLTitle; ?>) <?php echo $required_msg; ?></label>

                                                                        </div>
                                                                        <div class="col-lg-12">
                                                                            <textarea class="form-control ckeditor" rows="10" name="<?php echo $tPageDesc; ?>"  id="<?php echo $tPageDesc; ?>"  placeholder="<?php echo $vLTitle; ?> Value"> <?php echo ${$tPageDesc}; ?></textarea>
                                                                            <div class="text-danger" id="<?php echo $tPageDesc.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                        </div>
                                                                    </div>
                                                                <?php
                                                    }
                                            ?>
                                                        </div>
                                                        <div class="modal-footer" style="margin-top: 0">
                                                            <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveDescWeb('tPageDesc_', 'PageDesc_Modal')"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Cancel</button>
                                                            </div>
                                                        </div>

                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>

                                            </div>
                                        <?php } else { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Title <span class="red"> *</span></label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <input type="text" class="form-control" id="vPageTitle_<?php echo $default_lang; ?>" name="vPageTitle_<?php echo $default_lang; ?>" value="<?php echo $db_data[0]['vPageTitle_'.$default_lang]; ?>">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Page Description <span class="red"> *</span></label>
                                                </div>
                                                <div class="col-lg-12">
                                                    <textarea class="form-control ckeditor" rows="10" id="tPageDesc_<?php echo $default_lang; ?>" name="tPageDesc_<?php echo $default_lang; ?>"><?php echo $db_data[0]['tPageDesc_'.$default_lang]; ?></textarea>
                                                </div>
                                            </div>
                                        <?php } ?>
                                            <?php

                                    }
                                if (!in_array($iPageId, ['23', '24', '25', '26', '27', '46', '48', '49', '50', '54', '55', '56', '57'], true)) {
                                    ?>
                                        <div class="row" <?php echo $style_v; ?>>
                                            <div class="col-lg-12">
                                                <label>Meta Title</label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <input type="text" class="form-control" name="vTitle"  id="vTitle" value="<?php echo htmlspecialchars($vTitle); ?>" placeholder="Meta Title">
                                            </div>
                                        </div>
                                        <div class="row" <?php echo $style_v; ?>>
                                            <div class="col-lg-12">
                                                <label>Meta Keyword</label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <input type="text" class="form-control" name="tMetaKeyword"  id="tMetaKeyword" value="<?php echo htmlspecialchars($tMetaKeyword); ?>" placeholder="Meta Keyword">
                                            </div>
                                        </div>

                                        <div class="row" <?php echo $style_v; ?>>
                                            <div class="col-lg-12">
                                                <label>Meta Description</label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <textarea class="form-control" rows="10" name="tMetaDescription"  id="<?php echo $tMetaDescription; ?>"  placeholder="<?php echo $tMetaDescription; ?> Value" <?php echo $required; ?>> <?php echo $tMetaDescription; ?></textarea>
                                            </div>
                                        </div>

                                        <?php
                                } if (!in_array($iPageId, ['1', '2', '7', '4', '3', '6', '23', '27', '33', '44', '55', '56', '57'], true)) {
                                    ?>
                                        <?php
                                    if ('Yes' === $cubexthemeon && in_array($iPageId, $pageidCubexImage, true)) {
                                        ?>
                                            <br><br>
                                            <?php if (50 !== $iPageId) { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Image (Left side shown)</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <?php if ('' !== $vImage) { ?>
                                                        <a target="_blank" href="<?php echo $images.$vImage; ?>"><img src="<?php echo $images.$vImage; ?>" style="width:200px;height:100px;"></a>
                                                    <?php } ?>
                                                    <input type="file" class="form-control" name="vImage" id="vImage" /><br/>
                                                    <span class="notes">[Note: For Better Resolution Upload only image size of 330px * 500px.]</span>
                                                </div>
                                            </div>
                                            <?php } ?>
                                            <?php if (!in_array($iPageId, [48], true)) { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <?php if (50 === $iPageId) { ?>
                                                    <label>Image (Left side shown)</label>
                                                    <?php } else { ?>
                                                    <label>Background Image</label>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <?php if ('' !== $vImage1) { ?>
                                                        <a target="_blank" href="<?php echo $images.$vImage1; ?>"><img src="<?php echo $images.$vImage1; ?>" style="width:200px;height:100px;"></a>
                                                    <?php } ?>
                                                    <input type="file" class="form-control" name="vImage1" id="vImage1" /><br/>
                                                     <span class="notes">[Note: For Better Resolution Upload only image size of 943px * 1920px.]</span>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        <?php } elseif ('Yes' === $cubexthemeon && 52 === $iPageId) { ?>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>First Image (Left side shown)</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <?php if ('' !== $vImage) { ?>
                                                        <a target="_blank" href="<?php echo $images.$vImage; ?>"><img src="<?php echo $images.$vImage; ?>" style="width:200px;height:100px;"></a>
                                                    <?php } ?>
                                                    <input type="file" class="form-control" name="vImage" id="vImageaaa2" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Second Image (Right side shown)</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <?php if ('' !== $vImage1) { ?>
                                                        <a target="_blank" href="<?php echo $images.$vImage1; ?>"><img src="<?php echo $images.$vImage1; ?>" style="width:200px;height:100px;"></a>
                                                    <?php } ?>
                                                    <input type="file" class="form-control" name="vImage1" id="vImagea1" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Third Image (Left side shown)</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <?php if ('' !== $vImage2) { ?>
                                                        <a target="_blank" href="<?php echo $images.$vImage2; ?>"><img src="<?php echo $images.$vImage2; ?>" style="width:200px;height:100px;"></a>
                                                    <?php } ?>
                                                    <input type="file" class="form-control" name="vImage2" id="vImagea2" />
                                                </div>
                                            </div>
                                        <?php } elseif ('Yes' === $cubexthemeon && 22 === $iPageId) { ?>
                                        <div class="row" style="<?php echo $style_vimage; ?>">
                                                <div class="col-lg-12">
                                                    <label>Image</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <?php if ('' !== $vImage1) { ?>
                                                        <a target="_blank" href="<?php echo $images.$vImage1; ?>"><img src="<?php echo $images.$vImage1; ?>" style="width:200px;height:100px;"></a>
                                                    <?php } ?>
                                                    <input type="file" class="form-control" name="vImage1" id="vImagen1" />
                                                    <br/>
                                                    <span class="notes">[Note: For Better Resolution Upload only image size of 190px * 190px.]</span>
                                                </div>
                                            </div>
                                        <?php } else {
                                            $style_vimage = '';
                                            if (!in_array($iPageId, ['53'], true)) {
                                                $style_vimage = 'display:none;';
                                            }
                                            ?>
                                            <div class="row" style="<?php echo $style_vimage; ?>">
                                                <div class="col-lg-12">
                                                    <label>Image</label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <?php if ('' !== $vImage) { ?>
                                                        <a target="_blank" href="<?php echo $images.$vImage; ?>"><img src="<?php echo $images.$vImage; ?>" style="width:200px;height:100px;"></a>
                                                    <?php } ?>
                                                    <input type="file" class="form-control" name="vImage" id="vImage" />
                                                    <br/>
                                                    <span class="notes">[Note: For Better Resolution Upload only image size of 1903px * 626px.]</span>
                                                </div>
                                            </div>
                                        <?php } ?>



                                    <?php } if (!in_array($iPageId, [2, 22, 44, 46, 48, 49, 54, 50, 55, 56, 57], true)) { ?>
                                    <!--                                added by SP for pages orderby,active/inactive functionality  -->
                                    <div class="row" <?php echo $style_v; ?>>
                                        <div class="col-lg-12">
                                            <label>Display Order</label>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <input type="number" class="form-control" name="iOrderBy" id="iOrderBy" value="<?php echo $iOrderBy; ?>" placeholder="Page displayed according to this number" min="0">
                                        </div>
                                    </div>
                                    <?php } ?>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <?php if (('Edit' === $action && $userObj->hasPermission('edit-pages')) || ('Add' === $action && $userObj->hasPermission('create-pages'))) { ?>
                                                <input type="submit" class="btn btn-default" name="submit" id="submit" value="<?php echo $action; ?> Static Page">
                                                <input type="reset" value="Reset" class="btn btn-default">
                                            <?php } ?>
                                            <!-- <a href="javascript:void(0);" onclick="reset_form('_page_form');" class="btn btn-default">Reset</a> -->
                                            <a href="page.php" class="btn btn-default back_link">Cancel</a>
                                        </div>
                                    </div>
                                </form>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
            <!--END PAGE CONTENT -->
        </div>
        <!--END MAIN WRAPPER -->

        <div class="row loding-action" id="loaderIcon" style="display:none; z-index: 99999">
            <div align="center">
                <img src="default.gif">
                <span>Language Translation is in Process. Please Wait...</span>
            </div>
        </div>
        <?php include_once 'footer.php'; ?>

        <!-- PAGE LEVEL SCRIPTS -->
        <script src="../assets/plugins/ckeditor/ckeditor.js"></script>
        <script src="../assets/plugins/ckeditor/config.js"></script>

        <script>
            /* CKEDITOR.replace( 'ckeditor',{
             allowedContent : {
             i:{
             classes:'fa*'
             },
             span: true
             }
             } ); */
        </script>
        <script>
            var myVar;
            $(document).ready(function () {
                var referrer;
                if ($("#previousLink").val() == "") {
                    referrer = document.referrer;
                } else {
                    referrer = $("#previousLink").val();
                }
                if (referrer == "") {
                    referrer = "page.php";
                } else {
                    $("#backlink").val(referrer);
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

            function editPage(action)
            {
                $('#modal_action').html(action);
                $('#tPageDesc_Modal').modal('show');
            }

            function savePage()
            {
                var tPageDescLength = CKEDITOR.instances['tPageDesc_<?php echo $default_lang; ?>'].getData().replace(/<[^>]*>/gi, '').length;

                if($('#vPageTitle_<?php echo $default_lang; ?>').val() == "") {
                    $('#vPageTitle_<?php echo $default_lang; ?>_error').show();
                    $('#vPageTitle_<?php echo $default_lang; ?>').focus();
                    clearInterval(myVar);
                    myVar = setTimeout(function() {
                        $('#vPageTitle_<?php echo $default_lang; ?>_error').hide();
                    }, 5000);
                    return false;
                }/*
                else if(!tPageDescLength) {
                    $('#tPageDesc_<?php echo $default_lang; ?>_error').show();
                    $('#tPageDesc_<?php echo $default_lang; ?>').focus();
                    clearInterval(myVar);
                    myVar = setTimeout(function() {
                        $('#tPageDesc_<?php echo $default_lang; ?>_error').hide();
                    }, 5000);
                    e.preventDefault();
                    return false;
                }*/

                $('#vPageTitle_Default').val($('#vPageTitle_<?php echo $default_lang; ?>').val());
                $('#vPageTitle_Default').closest('.row').removeClass('has-error');
                $('#vPageTitle_Default-error').remove();
                var tPageDescHTML = CKEDITOR.instances['tPageDesc_<?php echo $default_lang; ?>'].getData();
                CKEDITOR.instances['tPageDesc_Default'].setData(tPageDescHTML)
                $('#tPageDesc_Modal').modal('hide');
            }

            function editPageSubTitle(action)
            {
                $('#modal_action').html(action);
                $('#tPageSubTitle_Modal').modal('show');
            }

            function savePageSubTitle()
            {
                if($('#vPageSubTitle_<?php echo $default_lang; ?>').val() == "") {
                    $('#vPageSubTitle_<?php echo $default_lang; ?>_error').show();
                    $('#vPageSubTitle_<?php echo $default_lang; ?>').focus();
                    clearInterval(myVar);
                    myVar = setTimeout(function() {
                        $('#vPageSubTitle_<?php echo $default_lang; ?>_error').hide();
                    }, 5000);
                    return false;
                }

                $('#vPageSubTitle_Default').val($('#vPageSubTitle_<?php echo $default_lang; ?>').val());
                $('#vPageSubTitle_Default').closest('.row').removeClass('has-error');
                $('#vPageSubTitle_Default-error').remove();
                $('#tPageSubTitle_Modal').modal('hide');
            }

            $(document).on("keypress keyup blur paste keydown", '#tPageDesc_Default, #vPageTitle_Default',function (event) {
                event.preventDefault();
            });

            function editUserPage(action)
            {
                $('#modal_action_user').html(action);
                $('#tPageUserDesc_Modal').modal('show');
            }

            function saveUserPage()
            {
                //var tPageDescULength = CKEDITOR.instances['tUserPageDesc_<?php echo $default_lang; ?>'].getData().replace(/<[^>]*>/gi, '').length;

                if($('#vUserPageTitle_<?php echo $default_lang; ?>').val() == "") {
                    $('#vUserPageTitle_<?php echo $default_lang; ?>_error').show();
                    $('#vUserPageTitle_<?php echo $default_lang; ?>').focus();
                    clearInterval(myVar);
                    myVar = setTimeout(function() {
                        $('#vUserPageTitle_<?php echo $default_lang; ?>_error').hide();
                    }, 5000);
                    return false;
                }/*
                else if(!tPageDescLength) {
                    $('#tPageDesc_<?php echo $default_lang; ?>_error').show();
                    $('#tPageDesc_<?php echo $default_lang; ?>').focus();
                    clearInterval(myVar);
                    myVar = setTimeout(function() {
                        $('#tPageDesc_<?php echo $default_lang; ?>_error').hide();
                    }, 5000);
                    e.preventDefault();
                    return false;
                }*/

                $('#vUserPageTitle_Default').val($('#vUserPageTitle_<?php echo $default_lang; ?>').val());
                $('#vUserPageTitle_Default').closest('.row').removeClass('has-error');
                $('#vUserPageTitle_Default-error').remove();
                var tPageDescUHTML = CKEDITOR.instances['tUserPageDesc_<?php echo $default_lang; ?>'].getData();
                CKEDITOR.instances['tUserPageDesc_Default'].setData(tPageDescUHTML)
                $('#tPageUserDesc_Modal').modal('hide');
            }

            function editProviderPage(action)
            {
                $('#modal_action_provider').html(action);
                $('#tPageProviderDesc_Modal').modal('show');
            }

            function saveProviderPage()
            {
                //var tPageDescULength = CKEDITOR.instances['tProviderPageDesc_<?php echo $default_lang; ?>'].getData().replace(/<[^>]*>/gi, '').length;

                if($('#vProviderPageTitle_<?php echo $default_lang; ?>').val() == "") {
                    $('#vProviderPageTitle_<?php echo $default_lang; ?>_error').show();
                    $('#vProviderPageTitle_<?php echo $default_lang; ?>').focus();
                    clearInterval(myVar);
                    myVar = setTimeout(function() {
                        $('#vProviderPageTitle_<?php echo $default_lang; ?>_error').hide();
                    }, 5000);
                    return false;
                }/*
                else if(!tPageDescLength) {
                    $('#tPageDesc_<?php echo $default_lang; ?>_error').show();
                    $('#tPageDesc_<?php echo $default_lang; ?>').focus();
                    clearInterval(myVar);
                    myVar = setTimeout(function() {
                        $('#tPageDesc_<?php echo $default_lang; ?>_error').hide();
                    }, 5000);
                    e.preventDefault();
                    return false;
                }*/

                $('#vProviderPageTitle_Default').val($('#vProviderPageTitle_<?php echo $default_lang; ?>').val());
                $('#vProviderPageTitle_Default').closest('.row').removeClass('has-error');
                $('#vProviderPageTitle_Default-error').remove();
                var tPageDescPHTML = CKEDITOR.instances['tProviderPageDesc_<?php echo $default_lang; ?>'].getData();
                CKEDITOR.instances['tProviderPageDesc_Default'].setData(tPageDescPHTML)
                $('#tPageProviderDesc_Modal').modal('hide');
            }


            function editCompanyPage(action)
            {
                $('#modal_action_company').html(action);
                $('#tPageCompanyDesc_Modal').modal('show');
            }

            function saveCompanyPage()
            {
                //var tPageDescULength = CKEDITOR.instances['tProviderPageDesc_<?php echo $default_lang; ?>'].getData().replace(/<[^>]*>/gi, '').length;

                if($('#vCompanyPageTitle_<?php echo $default_lang; ?>').val() == "") {
                    $('#vCompanyPageTitle_<?php echo $default_lang; ?>_error').show();
                    $('#vCompanyPageTitle_<?php echo $default_lang; ?>').focus();
                    clearInterval(myVar);
                    myVar = setTimeout(function() {
                        $('#vCompanyPageTitle_<?php echo $default_lang; ?>_error').hide();
                    }, 5000);
                    return false;
                }/*
                else if(!tPageDescLength) {
                    $('#tPageDesc_<?php echo $default_lang; ?>_error').show();
                    $('#tPageDesc_<?php echo $default_lang; ?>').focus();
                    clearInterval(myVar);
                    myVar = setTimeout(function() {
                        $('#tPageDesc_<?php echo $default_lang; ?>_error').hide();
                    }, 5000);
                    e.preventDefault();
                    return false;
                }*/

                $('#vCompanyPageTitle_Default').val($('#vCompanyPageTitle_<?php echo $default_lang; ?>').val());
                $('#vCompanyPageTitle_Default').closest('.row').removeClass('has-error');
                $('#vCompanyPageTitle_Default-error').remove();
                var tPageDescCHTML = CKEDITOR.instances['tCompanyPageDesc_<?php echo $default_lang; ?>'].getData();
                CKEDITOR.instances['tCompanyPageDesc_Default'].setData(tPageDescCHTML)
                $('#tPageCompanyDesc_Modal').modal('hide');
            }

            function editRestaurantPage(action)
            {
                $('#modal_action_store').html(action);
                $('#tRestaurantPageDesc_Modal').modal('show');
            }

            function saveRestaurantPage()
            {
                //var tPageDescULength = CKEDITOR.instances['tProviderPageDesc_<?php echo $default_lang; ?>'].getData().replace(/<[^>]*>/gi, '').length;

                if($('#vRestaurantPageTitle_<?php echo $default_lang; ?>').val() == "") {
                    $('#vRestaurantPageTitle_<?php echo $default_lang; ?>_error').show();
                    $('#vRestaurantPageTitle_<?php echo $default_lang; ?>').focus();
                    clearInterval(myVar);
                    myVar = setTimeout(function() {
                        $('#vRestaurantPageTitle_<?php echo $default_lang; ?>_error').hide();
                    }, 5000);
                    return false;
                }/*
                else if(!tPageDescLength) {
                    $('#tPageDesc_<?php echo $default_lang; ?>_error').show();
                    $('#tPageDesc_<?php echo $default_lang; ?>').focus();
                    clearInterval(myVar);
                    myVar = setTimeout(function() {
                        $('#tPageDesc_<?php echo $default_lang; ?>_error').hide();
                    }, 5000);
                    e.preventDefault();
                    return false;
                }*/

                $('#vRestaurantPageTitle_Default').val($('#vRestaurantPageTitle_<?php echo $default_lang; ?>').val());
                $('#vRestaurantPageTitle_Default').closest('.row').removeClass('has-error');
                $('#vRestaurantPageTitle_Default-error').remove();
                var tPageDescRHTML = CKEDITOR.instances['tRestaurantPageDesc_<?php echo $default_lang; ?>'].getData();
                CKEDITOR.instances['tRestaurantPageDesc_Default'].setData(tPageDescRHTML)
                $('#tRestaurantPageDesc_Modal').modal('hide');
            }

            function editOrgPage(action)
            {
                $('#modal_action_org').html(action);
                $('#tOrgPageDesc_Modal').modal('show');
            }

            function saveOrgPage()
            {
                //var tPageDescULength = CKEDITOR.instances['tProviderPageDesc_<?php echo $default_lang; ?>'].getData().replace(/<[^>]*>/gi, '').length;

                if($('#vOrgPageTitle_<?php echo $default_lang; ?>').val() == "") {
                    $('#vOrgPageTitle_<?php echo $default_lang; ?>_error').show();
                    $('#vOrgPageTitle_<?php echo $default_lang; ?>').focus();
                    clearInterval(myVar);
                    myVar = setTimeout(function() {
                        $('#vOrgPageTitle_<?php echo $default_lang; ?>_error').hide();
                    }, 5000);
                    return false;
                }/*
                else if(!tPageDescLength) {
                    $('#tPageDesc_<?php echo $default_lang; ?>_error').show();
                    $('#tPageDesc_<?php echo $default_lang; ?>').focus();
                    clearInterval(myVar);
                    myVar = setTimeout(function() {
                        $('#tPageDesc_<?php echo $default_lang; ?>_error').hide();
                    }, 5000);
                    e.preventDefault();
                    return false;
                }*/

                $('#vOrgPageTitle_Default').val($('#vOrgPageTitle_<?php echo $default_lang; ?>').val());
                $('#vOrgPageTitle_Default').closest('.row').removeClass('has-error');
                $('#vOrgPageTitle_Default-error').remove();
                var tPageDescOHTML = CKEDITOR.instances['tOrgPageDesc_<?php echo $default_lang; ?>'].getData();
                CKEDITOR.instances['tOrgPageDesc_Default'].setData(tPageDescOHTML)
                $('#tOrgPageDesc_Modal').modal('hide');
            }

            function editTrackServicePage(action)
            {
                $('#modal_action_trackservice').html(action);
                $('#tTrackServicePageDesc_Modal').modal('show');
            }

            function saveTrackServicePage()
            {
                //var tPageDescULength = CKEDITOR.instances['tProviderPageDesc_<?php echo $default_lang; ?>'].getData().replace(/<[^>]*>/gi, '').length;

                if($('#vTrackServicePageTitle_<?php echo $default_lang; ?>').val() == "") {
                    $('#vTrackServicePageTitle_<?php echo $default_lang; ?>_error').show();
                    $('#vTrackServicePageTitle_<?php echo $default_lang; ?>').focus();
                    clearInterval(myVar);
                    myVar = setTimeout(function() {
                        $('#vTrackServicePageTitle_<?php echo $default_lang; ?>_error').hide();
                    }, 5000);
                    return false;
                }/*
                else if(!tPageDescLength) {
                    $('#tPageDesc_<?php echo $default_lang; ?>_error').show();
                    $('#tPageDesc_<?php echo $default_lang; ?>').focus();
                    clearInterval(myVar);
                    myVar = setTimeout(function() {
                        $('#tPageDesc_<?php echo $default_lang; ?>_error').hide();
                    }, 5000);
                    e.preventDefault();
                    return false;
                }*/

                $('#vTrackServicePageTitle_Default').val($('#vTrackServicePageTitle_<?php echo $default_lang; ?>').val());
                $('#vTrackServicePageTitle_Default').closest('.row').removeClass('has-error');
                $('#vTrackServicePageTitle_Default-error').remove();
                var tPageDescOHTML = CKEDITOR.instances['tTrackServicePageDesc_<?php echo $default_lang; ?>'].getData();
                CKEDITOR.instances['tTrackServicePageDesc_Default'].setData(tPageDescOHTML)
                $('#tTrackServicePageDesc_Modal').modal('hide');
            }

            function editHotelPage(action)
            {
                $('#modal_action_hotel').html(action);
                $('#tHotelPageDesc_Modal').modal('show');
            }

            function saveHotelPage()
            {
                //var tPageDescULength = CKEDITOR.instances['tProviderPageDesc_<?php echo $default_lang; ?>'].getData().replace(/<[^>]*>/gi, '').length;

                if($('#vHotelPageTitle_<?php echo $default_lang; ?>').val() == "") {
                    $('#vHotelPageTitle_<?php echo $default_lang; ?>_error').show();
                    $('#vHotelPageTitle_<?php echo $default_lang; ?>').focus();
                    clearInterval(myVar);
                    myVar = setTimeout(function() {
                        $('#vHotelPageTitle_<?php echo $default_lang; ?>_error').hide();
                    }, 5000);
                    return false;
                }/*
                else if(!tPageDescLength) {
                    $('#tPageDesc_<?php echo $default_lang; ?>_error').show();
                    $('#tPageDesc_<?php echo $default_lang; ?>').focus();
                    clearInterval(myVar);
                    myVar = setTimeout(function() {
                        $('#tPageDesc_<?php echo $default_lang; ?>_error').hide();
                    }, 5000);
                    e.preventDefault();
                    return false;
                }*/

                $('#vHotelPageTitle_Default').val($('#vHotelPageTitle_<?php echo $default_lang; ?>').val());
                $('#vHotelPageTitle_Default').closest('.row').removeClass('has-error');
                $('#vHotelPageTitle_Default-error').remove();
                var tPageDescRHTML = CKEDITOR.instances['tHotelPageDesc_<?php echo $default_lang; ?>'].getData();
                CKEDITOR.instances['tHotelPageDesc_Default'].setData(tPageDescRHTML)
                $('#tHotelPageDesc_Modal').modal('hide');
            }

            function editAboutUsWeb(action)
            {
                $('#modal_action').html(action);
                $('#aboutUsWeb_Modal').modal('show');
            }

            function saveAboutUsWeb()
            {
                //var tPageDescULength = CKEDITOR.instances['tProviderPageDesc_<?php echo $default_lang; ?>'].getData().replace(/<[^>]*>/gi, '').length;

                if($('#vPageTitle_<?php echo $default_lang; ?>').val() == "") {
                    $('#vPageTitle_<?php echo $default_lang; ?>_error').show();
                    $('#vPageTitle_<?php echo $default_lang; ?>').focus();
                    clearInterval(myVar);
                    myVar = setTimeout(function() {
                        $('#vPageTitle_<?php echo $default_lang; ?>_error').hide();
                    }, 5000);
                    return false;
                }/*
                else if(!tPageDescLength) {
                    $('#tPageDesc_<?php echo $default_lang; ?>_error').show();
                    $('#tPageDesc_<?php echo $default_lang; ?>').focus();
                    clearInterval(myVar);
                    myVar = setTimeout(function() {
                        $('#tPageDesc_<?php echo $default_lang; ?>_error').hide();
                    }, 5000);
                    e.preventDefault();
                    return false;
                }*/

                $('#vPageTitle_Default').val($('#vPageTitle_<?php echo $default_lang; ?>').val());
                $('#vPageTitle_Default').closest('.row').removeClass('has-error');
                $('#vPageTitle_Default-error').remove();
                /*var vPageSubTitleHTML = CKEDITOR.instances['vPageSubTitle_<?php echo $default_lang; ?>'].getData();
                CKEDITOR.instances['vPageSubTitle_Default'].setData(vPageSubTitleHTML);

                var tPageDescHTML = CKEDITOR.instances['tPageDesc_<?php echo $default_lang; ?>'].getData();
                CKEDITOR.instances['tPageDesc_Default'].setData(tPageDescHTML);

                var tPageSecDescHTML = CKEDITOR.instances['tPageSecDesc_<?php echo $default_lang; ?>'].getData();
                CKEDITOR.instances['tPageSecDesc_Default'].setData(tPageSecDescHTML);

                var tPageThirdDescHTML = CKEDITOR.instances['tPageThirdDesc_<?php echo $default_lang; ?>'].getData();
                CKEDITOR.instances['tPageThirdDesc_Default'].setData(tPageThirdDescHTML);*/
                $('#aboutUsWeb_Modal').modal('hide');
            }

            function editAboutUsApp(action)
            {
                $('#modal_action').html(action);
                $('#aboutUsApp_Modal').modal('show');
            }

            function saveAboutUsApp()
            {
                //var tPageDescULength = CKEDITOR.instances['tProviderPageDesc_<?php echo $default_lang; ?>'].getData().replace(/<[^>]*>/gi, '').length;

                if($('#vPageTitle_1_<?php echo $default_lang; ?>').val() == "") {
                    $('#vPageTitle_1_<?php echo $default_lang; ?>_error').show();
                    $('#vPageTitle_1_<?php echo $default_lang; ?>').focus();
                    clearInterval(myVar);
                    myVar = setTimeout(function() {
                        $('#vPageTitle_1_<?php echo $default_lang; ?>_error').hide();
                    }, 5000);
                    return false;
                }/*
                else if(!tPageDescLength) {
                    $('#tPageDesc_<?php echo $default_lang; ?>_error').show();
                    $('#tPageDesc_<?php echo $default_lang; ?>').focus();
                    clearInterval(myVar);
                    myVar = setTimeout(function() {
                        $('#tPageDesc_<?php echo $default_lang; ?>_error').hide();
                    }, 5000);
                    e.preventDefault();
                    return false;
                }*/

                $('#vPageTitle_1_Default').val($('#vPageTitle_1_<?php echo $default_lang; ?>').val());
                $('#vPageTitle_1_Default').closest('.row').removeClass('has-error');
                $('#vPageTitle_1_Default-error').remove();

                var tPageDescHTML = CKEDITOR.instances['tPageDesc_1_<?php echo $default_lang; ?>'].getData();
                CKEDITOR.instances['tPageDesc_1_Default'].setData(tPageDescHTML);

                $('#aboutUsApp_Modal').modal('hide');
            }

            function editDescApp(action)
            {
                $('#DescApp_Modal').find('#modal_action').html(action);
                $('#DescApp_Modal').modal('show');
            }

            function saveDescApp()
            {
                var DescAppLength = CKEDITOR.instances['tPageDesc_1_<?php echo $default_lang; ?>'].getData().replace(/<[^>]*>/gi, '').length;

                if(!DescAppLength) {
                    $('#tPageDesc_1_<?php echo $default_lang; ?>_error').show();
                    $('#tPageDesc_1_<?php echo $default_lang; ?>').focus();
                    clearInterval(myVar);
                    myVar = setTimeout(function() {
                        $('#tPageDesc_1_<?php echo $default_lang; ?>_error').hide();
                    }, 5000);
                    e.preventDefault();
                    return false;
                }

                var tPageDescHTML = CKEDITOR.instances['tPageDesc_1_<?php echo $default_lang; ?>'].getData();
                CKEDITOR.instances['tPageDesc_1_Default'].setData(tPageDescHTML);

                $('#DescApp_Modal').modal('hide');
            }

            function editDescWeb(action, modal_id)
            {
                $('#'+modal_id).find('#modal_action').html(action);
                $('#'+modal_id).modal('show');
            }

            function saveDescWeb(input_id, modal_id)
            {
                var DescLength = CKEDITOR.instances[input_id+'<?php echo $default_lang; ?>'].getData().replace(/<[^>]*>/gi, '').length;
                if(!DescLength) {
                    $('#'+input_id+'<?php echo $default_lang; ?>_error').show();
                    $('#'+input_id+'<?php echo $default_lang; ?>').focus();
                    clearInterval(myVar);
                    myVar = setTimeout(function() {
                        $('#'+input_id+'<?php echo $default_lang; ?>_error').hide();
                    }, 5000);
                    e.preventDefault();
                    return false;
                }

                var DescHTML = CKEDITOR.instances[input_id + '<?php echo $default_lang; ?>'].getData();
                CKEDITOR.instances[input_id+'Default'].setData(DescHTML);
                $('#'+modal_id).modal('hide');
            }
        </script>
    </body>
    <!-- END BODY-->
</html>
