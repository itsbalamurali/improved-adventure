<?php
include_once '../common.php';

require_once TPATH_CLASS.'Imagecrop.class.php';
$thumb = new thumbnail();
$default_lang = $LANG_OBJ->FetchSystemDefaultLang();
$id = $_REQUEST['id'] ?? ''; // iDriverId
$success = $_REQUEST['success'] ?? 0;
$backlink = $_POST['backlink'] ?? '';
$action = ('' !== $id) ? 'Edit' : 'Add';
$tbl_name = 'home_driver';
$script = 'service_provider';
// For Restaurants
$ssqlsc = ' AND iServiceId IN('.$enablesevicescategory.')';
$sql = "SELECT * FROM `company` where eStatus !='Deleted' AND eSystem = 'DeliverAll' {$ssqlsc} ORDER BY `vCompany`";
$db_company = $obj->MySQLSelect($sql);

// fetch all lang from language_master table
$sql = 'SELECT * FROM `language_master` ORDER BY `iDispOrder`';
$db_master = $obj->MySQLSelect($sql);
$count_all = count($db_master);

// set all variables with either post (when submit) either blank (when insert)
$eStatus_check = $_POST['eStatus'] ?? 'off';

$eStatus = ('on' === $eStatus_check) ? 'Active' : 'Inactive';
$thumb = new thumbnail();
// to fetch max iDisplayOrder from table for insert
$select_order = $obj->MySQLSelect('SELECT MAX(iDisplayOrder) AS iDisplayOrder FROM '.$tbl_name);
$iDisplayOrder = $select_order[0]['iDisplayOrder'] ?? 0;
++$iDisplayOrder; // Maximum order number

$iCompanyId = $_POST['iCompanyId'] ?? $iCompanyId;
$iDriverId = $_POST['iDriverId'] ?? $iDriverId;
$iDisplayOrder = $_POST['iDisplayOrder'] ?? $iDisplayOrder;
$temp_order = $_POST['temp_order'] ?? '';
$vImage = $_POST['vImage_old'] ?? '';
// form submit
if (isset($_POST['submit'])) {
    if ('Add' === $action && !$userObj->hasPermission('create-popular-stores')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to create popular stores.';
        header('Location:service_provider.php');

        exit;
    }
    if ('Edit' === $action && !$userObj->hasPermission('edit-popular-stores')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to update popular stores.';
        header('Location:service_provider.php');

        exit;
    }
    if (!empty($iDriverId)) {
        if (SITE_TYPE === 'Demo') {
            header('Location:service_provider_action.php?id='.$id.'&success=2');

            exit;
        }
    }
    if ($temp_order > $iDisplayOrder) {
        for ($i = $temp_order; $i >= $iDisplayOrder; --$i) {
            $sql = 'UPDATE '.$tbl_name.' SET iDisplayOrder = '.($i + 1).' WHERE iDisplayOrder = '.$i;
            $obj->sql_query($sql);
        }
    } elseif ($temp_order < $iDisplayOrder) {
        for ($i = $temp_order; $i <= $iDisplayOrder; ++$i) {
            $sql = 'UPDATE '.$tbl_name.' SET iDisplayOrder = '.($i - 1).' WHERE iDisplayOrder = '.$i;
            $obj->sql_query($sql);
        }
    }
    $q = 'INSERT INTO ';
    $where = '';
    if ('' !== $id) {
        $q = 'UPDATE ';
        $where = " WHERE `iDriverId` = '".$id."'";
    }
    if (isset($_FILES['vImage']) && '' !== $_FILES['vImage']['name']) {
        $image_object = $_FILES['vImage']['tmp_name'];
        $image_name = $_FILES['vImage']['name'];

        if ('' !== $image_name) {
            $filecheck = basename($_FILES['vImage']['name']);
            $fileextarr = explode('.', $filecheck);
            $ext = strtolower($fileextarr[count($fileextarr) - 1]);
            $flag_error = 0;
            if ('jpg' !== $ext && 'gif' !== $ext && 'png' !== $ext && 'jpeg' !== $ext && 'bmp' !== $ext) {
                $flag_error = 1;
                $var_msg = 'Not valid image extension of .jpg, .jpeg, .gif, .png';
            }
            if ($_FILES['vImage']['size'] > 1_048_576) {
                $flag_error = 1;
                $var_msg = 'Image Size is too Large';
            }
            if (1 === $flag_error) {
                header('Location:service_provider_action.php?id='.$id.'&success=0&var_msg='.$var_msg);

                exit;
            }
            $Photo_Gallery_folder = $tconfig['tsite_upload_images_panel'];

            $img = $UPLOAD_OBJ->GeneralUploadImageService($image_object, $image_name, $Photo_Gallery_folder, $tconfig['tsite_upload_images_home'], '', '', '', '', '', 'Y', '', $Photo_Gallery_folder);
            $vImage = $img;
        }
    }
    $sql_str = '';
    $query = $q.' `'.$tbl_name.'` SET
			'.$sql_str."
			`vImage` = '".$vImage."',
			`eStatus` = '".$eStatus."',
			`iCompanyId`= '".$iCompanyId."',
			`iDisplayOrder` = '".$iDisplayOrder."'"
            .$where;
    $obj->sql_query($query);
    $id = ('' !== $id) ? $id : $obj->GetInsertId();
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
    $sql = 'SELECT * FROM '.$tbl_name." WHERE iDriverId = '".$id."'";
    $db_data = $obj->MySQLSelect($sql);
    if ($count_all > 0) {
        for ($i = 0; $i < $count_all; ++$i) {
            $iCompanyId = $db_data[0]['iCompanyId'];
            $vImage = $db_data[0]['vImage'];
            $eStatus = $db_data[0]['eStatus'];
            $iDisplayOrder = $db_data[0]['iDisplayOrder'];
        }
    }
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title>Admin | Home Page Store <?php echo $action; ?></title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
        <?php include_once 'global_files.php'; ?>
        <!-- On OFF switch -->
        <link href="../assets/css/jquery-ui.css" rel="stylesheet" />
        <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
        <!-- PAGE LEVEL STYLES -->
        <link rel="stylesheet" href="../assets/plugins/Font-Awesome/css/font-awesome.css" />
        <link rel="stylesheet" href="../assets/plugins/wysihtml5/dist/bootstrap-wysihtml5-0.0.2.css" />
        <link rel="stylesheet" href="../assets/css/Markdown.Editor.hack.css" />
        <link rel="stylesheet" href="../assets/plugins/CLEditor1_4_3/jquery.cleditor.css" />
        <link rel="stylesheet" href="../assets/css/jquery.cleditor-hack.css" />
        <link rel="stylesheet" href="../assets/css/bootstrap-wysihtml5-hack.css" />
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
                            <h2><?php echo $action; ?> Home Page Store </h2>
                            <a href="service_provider.php">
                                <input type="button" value="Back to Listing" class="add-btn">
                            </a>
                        </div>
                    </div>
                    <hr />
                    <div class="body-div">
                        <div class="form-group">
                            <?php if (0 === $success && '' !== $_REQUEST['var_msg']) { ?>
                                <div class="alert alert-danger alert-dismissable">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                    <?php echo $_REQUEST['var_msg']; ?>
                                </div><br/>
                            <?php } ?>
                            <?php if (1 === $success) { ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                    <?php echo $langage_lbl_admin['LBL_Record_Updated_successfully']; ?>
                                </div><br/>
                            <?php } ?>
                            <?php if (2 === $success) { ?>
                                <div class="alert alert-danger alert-dismissable">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                    <?php echo $langage_lbl_admin['LBL_EDIT_DELETE_RECORD']; ?>
                                </div><br/>
                            <?php } ?>
                            <form method="post" action="" enctype="multipart/form-data" id="home_driver_action" name="home_driver_action">
                                <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                                <input type="hidden" name="temp_order" id="temp_order" value="1	">
                                <input type="hidden" name="vImage_old" value="<?php echo $vImage; ?>">
                                <input type="hidden" name="backlink" id="backlink" value="service_provider.php"/>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Image</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <?php if ('' !== $vImage) { ?>
                                            <img src="<?php echo $tconfig['tsite_upload_images'].$vImage; ?>" style="height:100px;">
                                        <?php } ?>
                                        <input type="file" name="vImage" id="vImage" value="<?php echo $vImage; ?>"/>
                                        <br/>
                                        [Note: Upload only png image size of 290px * 270px.]
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Restaurant<span class="red"> *</span></label>
                                    </div>
                                    <div class="col-lg-6">
                                        <select name="iCompanyId" class="form-control" id="iCompanyId" required  onchange="changeDisplayOrderCompany(this.value, '<?php echo $id; ?>')">
                                            <option value="" >Select Restaurant</option>
                                            <?php foreach ($db_company as $dbc) { ?>
                                                <option value="<?php echo $dbc['iCompanyId']; ?>"<?php if ($dbc['iCompanyId'] === $iCompanyId) { ?>selected<?php } ?>><?php echo clearCmpName($dbc['vCompany']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Order</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <?php
                                        $temp = 1;
$query1 = $obj->MySQLSelect('SELECT max(iDisplayOrder) as maxnumber FROM '.$tbl_name.' ORDER BY iDisplayOrder');
$maxnum = $query1[0]['maxnumber'] ?? 0;
$dataArray = [];
for ($i = 1; $i <= $maxnum; ++$i) {
    $dataArray[] = $i;
    $temp = $iDisplayOrder;
}
?>
                                        <input type="hidden" name="temp_order" id="temp_order" value="<?php echo $temp; ?>">
                                        <select name="iDisplayOrder" class="form-control">
                                            <?php foreach ($dataArray as $arr) { ?>
                                                <option <?php echo $arr === $temp ? ' selected="selected"' : ''; ?> value="<?php echo $arr; ?>" >
                                                    -- <?php echo $arr; ?> --
                                                </option>
                                            <?php } ?>
                                            <?php if ('Add' === $action) { ?>
                                                <option value="<?php echo $temp; ?>" >
                                                    -- <?php echo $temp; ?> --
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
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
                                        <?php if (('Edit' === $action && $userObj->hasPermission('edit-popular-stores')) || ('Add' === $action && $userObj->hasPermission('create-popular-stores'))) { ?>
                                            <input type="submit" class="btn btn-default" name="submit" id="submit" value="<?php echo $action; ?> Home Page Store" >
                                            <input type="reset" value="Reset" class="btn btn-default">
                                        <?php } ?>
                                        <a href="service_provider.php" class="btn btn-default back_link">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
            <!--END PAGE CONTENT -->
        </div>
        <!--END MAIN WRAPPER -->
        <?php include_once 'footer.php'; ?>
        <script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
        <!-- PAGE LEVEL SCRIPTS -->
        <script src="../assets/plugins/wysihtml5/lib/js/wysihtml5-0.3.0.js"></script>
        <script src="../assets/plugins/bootstrap-wysihtml5-hack.js"></script>
        <script src="../assets/plugins/CLEditor1_4_3/jquery.cleditor.min.js"></script>
        <script src="../assets/plugins/pagedown/Markdown.Converter.js"></script>
        <script src="../assets/plugins/pagedown/Markdown.Sanitizer.js"></script>
        <script src="../assets/plugins/Markdown.Editor-hack.js"></script>
        <script src="../assets/js/editorInit.js"></script>
    </body>
    <!-- END BODY-->
</html>