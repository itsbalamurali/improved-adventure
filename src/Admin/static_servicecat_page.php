<?php
include_once '../common.php';

$page = $_REQUEST['page'] ?? '';
$id = $_REQUEST['id'] ?? '';
$tbl_name = getAppTypeWiseHomeTable();
$getLangQry = "SELECT hc.*,lm.vTitle FROM {$tbl_name} as hc LEFT JOIN language_master as lm on lm.vCode = hc.vCode WHERE 1 = 1 AND hc.eStatus = 'Active' AND iLanguageMasId = '{$id}'";

$getLangData = $obj->MySQLSelect($getLangQry);
$vCode = $getLangData[0]['vCode'];
if (!empty($page) && 'earn' === $page) {
    $field = 'learnServiceCatSection';
} elseif (!empty($page) && 'business' === $page) {
    $field = 'lbusinessServiceCatSection';
}
$getHomeDataQry = "SELECT {$field} FROM {$tbl_name} where vCode='".$vCode."'";
$getHomeData = $obj->MySQLSelect($getHomeDataQry);
$learnServiceCatData = (array) json_decode($getHomeData[0][$field]);
$vHomepageLogo = $learnServiceCatData['vHomepageLogo'];
$vHomepageBanner = $learnServiceCatData['vHomepageBanner'];
if ('Yes' === $THEME_OBJ->isPXCProThemeActive()) {
    $redirectUrl = 'homepage_content.php?id='.$_REQUEST['id'];
} else {
    $redirectUrl = 'home_content_cubex_action.php?id='.$_REQUEST['id'];
}
if (isset($_POST['btnsubmit_homepage'])) {
    if (isset($_FILES['vHomepageLogo']) && '' !== $_FILES['vHomepageLogo']['name']) {
        $filecheck = basename($_FILES['vHomepageLogo']['name']);
        $fileextarr = explode('.', $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        $data = getimagesize($_FILES['vHomepageLogo']['tmp_name']);
        if (1 === $flag_error) {
            $_SESSION['success'] = '';
            $_SESSION['var_msg'] = '';
            header('Location:static_servicecat_page.php?id='.$id.'&var_msg='.$var_msg.'&goback=1');

            exit;
        }
    }
    if (isset($_FILES['vHomepageBanner']) && '' !== $_FILES['vHomepageBanner']['name']) {
        $filecheck = basename($_FILES['vHomepageBanner']['name']);
        $fileextarr = explode('.', $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        $data = getimagesize($_FILES['vHomepageBanner']['tmp_name']);
        $width = $data[0];
        $height = $data[1];
        if (1 === $flag_error) {
            $_SESSION['success'] = '';
            $_SESSION['var_msg'] = '';
            header('Location:static_servicecat_page.php?id='.$id.'&var_msg='.$var_msg.'&goback=1');

            exit;
        }
    }
    $vacategoryid = $id;
    $img_arr = $_FILES;
    if (!empty($img_arr)) {
        foreach ($img_arr as $key => $value) {
            if (!empty($value['name'])) {
                $img_path = $tconfig['tsite_upload_home_page_service_images_panel'];
                $temp_gallery = $img_path.'/';
                $image_object = $value['tmp_name'];
                $image_name = $value['name'];
                $check_file_query = "SELECT {$field} FROM {$tbl_name} where vCode='".$vCode."'";
                $check_file = $obj->MySQLSelect($check_file_query);
                $sectionData = json_decode($check_file[0][$field], true);
                if ('' !== $message_print_id) {
                    $check_file = $img_path.'/'.$sectionData[0][$field][$key];
                    if ('' !== $check_file && file_exists($sectionData[0][$field][$key])) {
                        @unlink($check_file);
                    }
                }
                $Photo_Gallery_folder = $img_path.'/';
                if (!is_dir($Photo_Gallery_folder)) {
                    mkdir($Photo_Gallery_folder, 0777);
                }
                $img = $UPLOAD_OBJ->UploadImage($Photo_Gallery_folder, $image_object, $image_name, '', 'png,jpg,jpeg,gif,svg');
                if ('1' === $img[2]) {
                    $_SESSION['success'] = '0';
                    $_SESSION['var_msg'] = $img[1];
                    header('location:'.$backlink);
                }
                if (!empty($img[0])) {
                    $sectionData[$key] = $img[0];
                    $sectionDatajson = stripslashes(json_encode($sectionData));
                    $sql = 'UPDATE '.$tbl_name.' SET  '.$field." = '".$sectionDatajson."' WHERE `vCode` = '".$vCode."'";
                    $obj->sql_query($sql);
                } else {
                    $_SESSION['success'] = '0';
                    $_SESSION['var_msg'] = $img[1];
                }
            }
        }
    }
    $check_file_query = 'SELECT '.$field." FROM {$tbl_name} where vCode='".$vCode."'";
    $check_file = $obj->MySQLSelect($check_file_query);
    $sectionData = json_decode($check_file[0][$field], true);
    $earnData['vCatNameHomepage'] = $_POST['vCatNameHomepage'];
    $earnData['vCatTitleHomepage'] = $_POST['vCatTitleHomepage'];
    $earnData['lCatDescHomepage'] = $_POST['lCatDescHomepage'];
    $earnData['vCatDescbtnHomepage'] = $_POST['vCatDescbtnHomepage'];
    $earnData['vCatSloganHomepage'] = $_POST['vCatSloganHomepage'];
    $earnData['vHomepageLogo'] = $sectionData['vHomepageLogo'];
    $earnData['vHomepageBanner'] = $sectionData['vHomepageBanner'];
    $earnBusinessDetailsJson = $obj->SqlEscapeString(json_encode($earnData));
    $query = 'UPDATE `'.$tbl_name.'` SET
        '.$field." = '".$earnBusinessDetailsJson."' WHERE `vCode` = '".$vCode."'";
    $id = $obj->sql_query($query);
    if ('Add' === $action) {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INSERT_MSG'];
    } else {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
    }
    header('location: '.$redirectUrl);

    exit;
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title>Admin | Service category</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <link href="assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
        <?php
        include_once 'global_files.php';
?>
        <!-- On OFF switch -->
        <link href="../assets/css/jquery-ui.css" rel="stylesheet" />
        <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
    </head>
    <!-- END  HEAD-->
    <!-- BEGIN BODY-->
    <body class="padTop53 " >
        <!-- MAIN WRAPPER -->
        <div id="wrap">
            <?php
    include_once 'header.php';

include_once 'left_menu.php';
?>
            <!--PAGE CONTENT -->
            <div id="content">
                <div class="inner">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2>Page</h2>
                        </div>
                    </div>
                    <hr />
                    <div class="body-div">
                        <div class="form-group">
                            <form id="vtype" method="post" action="" enctype="multipart/form-data">
                                <?php
                    $vTitle = $vCode;
$eDefault = 'EN';
$vCatNameHomepageN = 'vCatNameHomepage';
$vCatTitleHomepageN = 'vCatTitleHomepage';
$lCatDescHomepageN = 'lCatDescHomepage';
$vCatDescbtnHomepageN = 'vCatDescbtnHomepage';
$vCatSloganHomepage = 'vCatSloganHomepage';
?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Name (<?php echo $vTitle; ?>)</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="<?php echo $vCatNameHomepageN; ?>" id="<?php echo $vCatNameHomepageN; ?>" value="<?php echo $learnServiceCatData[$vCatNameHomepageN]; ?>" placeholder="<?php echo $vTitle.' Value'; ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Title (<?php echo $vTitle; ?>)</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name='<?php echo $vCatTitleHomepageN; ?>' id='<?php echo $vCatTitleHomepageN; ?>' value="<?php echo $learnServiceCatData[$vCatTitleHomepageN]; ?>" placeholder="<?php echo $vTitle.' Value'; ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Slogan (<?php echo $vTitle; ?>)</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="<?php echo $vCatSloganHomepage; ?>" id="<?php echo $vCatSloganHomepage; ?>" value="<?php echo $learnServiceCatData[$vCatSloganHomepage]; ?>" placeholder="<?php echo $vTitle.' Value'; ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Description (<?php echo $vTitle; ?>)</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <textarea class="form-control" name="<?php echo $lCatDescHomepageN; ?>" id="<?php echo $lCatDescHomepageN; ?>" placeholder="<?php echo $vTitle.' Value'; ?>"><?php echo $learnServiceCatData[$lCatDescHomepageN]; ?></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Button text (<?php echo $vTitle; ?>)</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name='<?php echo $vCatDescbtnHomepageN; ?>' id='<?php echo $vCatDescbtnHomepageN; ?>' value="<?php echo $learnServiceCatData[$vCatDescbtnHomepageN]; ?>" placeholder="<?php echo $vTitle.' Value'; ?>">
                                    </div>
                                </div>
                                <div class="row imagebox">
                                    <div class="col-lg-12">
                                        <label>Logo</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <?php if (isset($vHomepageLogo) && '' !== $vHomepageLogo) { ?>
                                            <img src="<?php echo $tconfig['tsite_upload_home_page_service_images'].'/'.$vHomepageLogo; ?>" style="width:100px;height:100px;">

                                        <?php } ?>
                                        <input type="file" class="form-control" name="vHomepageLogo" <?php echo $required_rule; ?> id="vHomepageLogo" placeholder="" style="padding-bottom: 39px;">
                                        <br/>
                                        Note: Upload only png image size of 360px*360px.
                                    </div>
                                </div>

                                <div class="row imagebox">
                                    <div class="col-lg-12">
                                        <label>Banner</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <?php if (isset($vHomepageBanner) && '' !== $vHomepageBanner) { ?>
                                            <img src="<?php echo $tconfig['tsite_upload_home_page_service_images'].'/'.$vHomepageBanner; ?>" style="width:200px;">

                                        <?php } ?>
                                        <input type="file" class="form-control" name="vHomepageBanner" <?php echo $required_rule; ?> id="vHomepageBanner" placeholder="" style="padding-bottom: 39px;">
                                        <br/>
                                        Note: Recommended dimension for banner image is 2880 * 1620.
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <?php if ($userObj->hasPermission('edit-pages')) { ?>
                                            <input type="hidden" class="save btn-info" name="id" value="<?php echo $_REQUEST['id']; ?>">
                                            <input type="submit" class="save btn-info" name="btnsubmit_homepage" id="btnsubmit_homepage" value="Edit">
                                            <input type="reset" value="Reset" class="btn btn-default">
                                        <?php } ?>
                                        <a href="<?php echo $redirectUrl; ?>" class="btn btn-default back_link">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>



