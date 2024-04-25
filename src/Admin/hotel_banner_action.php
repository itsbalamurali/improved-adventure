<?php
include_once '../common.php';

require_once TPATH_CLASS.'Imagecrop.class.php';

$default_lang = $LANG_OBJ->FetchSystemDefaultLang();
$id = $_REQUEST['id'] ?? ''; // iUniqueId
$success = $_REQUEST['success'] ?? '';
$action = ('' !== $id) ? 'Edit' : 'Add';
// $temp_gallery = $tconfig["tpanel_path"];
$tbl_name = 'hotel_banners';
$script = 'hotel_banners';

// fetch all lang from language_master table
$sql = "SELECT vCode FROM `language_master` where eStatus='Active' ORDER BY `iDispOrder`";
$db_master = $obj->MySQLSelect($sql);
$count_all = count($db_master);

$vImage = $_POST['vImage_old'] ?? '';
$eStatus_check = $_POST['eStatus'] ?? 'off';
$vTitle = $_POST['vTitle'] ?? '';
$eStatus = ('on' === $eStatus_check) ? 'Active' : 'Inactive';
$thumb = new thumbnail();
// to fetch max iDisplayOrder from table for insert
$select_order = $obj->MySQLSelect('SELECT MAX(iDisplayOrder) AS iDisplayOrder FROM '.$tbl_name." WHERE vCode = '".$default_lang."'");
$iDisplayOrder = $select_order[0]['iDisplayOrder'] ?? 0;
++$iDisplayOrder; // Maximum order number

$iDisplayOrder = $_POST['iDisplayOrder'] ?? $iDisplayOrder;
$temp_order = $_POST['temp_order'] ?? '';

if (isset($_POST['submit'])) { // form submit
    if ('Add' === $action && !$userObj->hasPermission('create-hotel-banner')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to create hotel banner.';
        header('Location:hotel_banner.php');

        exit;
    }

    if ('Edit' === $action && !$userObj->hasPermission('edit-hotel-banner')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to update hotel banner.';
        header('Location:hotel_banner.php');

        exit;
    }

    if (SITE_TYPE === 'Demo') {
        $_SESSION['success'] = 2;
        header('Location:hotel_banner.php');

        exit;
    }
    if ($temp_order > $iDisplayOrder) {
        for ($i = $temp_order; $i >= $iDisplayOrder; --$i) {
            $obj->sql_query('UPDATE '.$tbl_name.' SET iDisplayOrder = '.($i + 1).' WHERE iDisplayOrder = '.$i);
        }
    } elseif ($temp_order < $iDisplayOrder) {
        for ($i = $temp_order; $i <= $iDisplayOrder; ++$i) {
            $obj->sql_query('UPDATE '.$tbl_name.' SET iDisplayOrder = '.($i - 1).' WHERE iDisplayOrder = '.$i);
        }
    }

    $select_order = $obj->MySQLSelect('SELECT MAX(iUniqueId) AS iUniqueId FROM '.$tbl_name." WHERE vCode = '".$default_lang."'");
    $iUniqueId = $select_order[0]['iUniqueId'] ?? 0;
    ++$iUniqueId; // Maximum order number

    if ($count_all > 0) {
        for ($i = 0; $i < $count_all; ++$i) {
            $q = 'INSERT INTO ';
            $where = '';

            if ('' !== $id) {
                $q = 'UPDATE ';
                $where = " WHERE `iUniqueId` = '".$id."' AND vCode = '".$db_master[$i]['vCode']."'";
                $iUniqueId = $id;
            }
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

                $image_info = getimagesize($_FILES['vImage']['tmp_name']);
                $image_width = $image_info[0];
                $image_height = $image_info[1];

                if (1 === $flag_error) {
                    $_SESSION['success'] = '3';
                    $_SESSION['var_msg'] = $var_msg;
                    header('Location:hotel_banner.php');

                    exit;
                }
                $Photo_Gallery_folder = $tconfig['tsite_upload_images_hotel_banner_path'].'/';
                if (!is_dir($Photo_Gallery_folder)) {
                    mkdir($Photo_Gallery_folder, 0777);
                }
                $img = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, '', 'jpg,png,gif,jpeg');
                $vImage = $img[0];
            }

            $query = $q.' `'.$tbl_name."` SET
				`vTitle` = '".$vTitle."',
				`vImage` = '".$vImage."',
				`eStatus` = '".$eStatus."',
				`iUniqueId` = '".$iUniqueId."',
				`iDisplayOrder` = '".$iDisplayOrder."',
				`vCode` = '".$db_master[$i]['vCode']."'"
            .$where;
            $obj->sql_query($query);

            if ('' !== $id) {
                $_SESSION['success'] = '1';
                $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
            } else {
                $_SESSION['success'] = '1';
                $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INSERT_MSG'];
            }
        }
        header('Location:hotel_banner.php');

        exit;
    }
}

// for Edit
if ('Edit' === $action) {
    $sql = 'SELECT vTitle,eStatus,vImage,iDisplayOrder FROM '.$tbl_name." WHERE iUniqueId = '".$id."'";
    $db_data = $obj->MySQLSelect($sql);
    $iUniqueId = $id;
    if (count($db_data) > 0) {
        foreach ($db_data as $key => $value) {
            // $vTitle 			= 'vTitle_'.$value['vCode'];
            $vTitle = $value['vTitle'];
            $eStatus = $value['eStatus'];
            $vImage = $value['vImage'];
            $iDisplayOrder = $value['iDisplayOrder'];
        }
    }
}
?>
<!DOCTYPE html>
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

	<!-- BEGIN HEAD-->
	<head>
		<meta charset="UTF-8" />
		<title>Admin | Hotel Banner <?php echo $action; ?></title>
		<meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />

		<?php include_once 'global_files.php'; ?>
		<!-- On OFF switch -->
		<link href="../assets/css/jquery-ui.css" rel="stylesheet" />
		<link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
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
							<h2><?php echo $action; ?> Hotel Banner</h2>
							<a href="hotel_banner.php">
								<input type="button" value="Back to Listing" class="add-btn">
							</a>
						</div>
					</div>
					<hr />
					<div class="body-div">
						<div class="form-group">
						<?php if (0 === $success && '' !== $_REQUEST['var_msg']) {?>
							<div class="alert alert-danger alert-dismissable">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								<?php echo $_REQUEST['var_msg']; ?>
							</div><br/>
						<?} ?>
						<?php if (1 === $success) { ?>
								<div class="alert alert-success alert-dismissable">
									<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
									<?php echo $langage_lbl_admin['LBL_Record_Updated_successfully']; ?>
							</div><br/>
						<?php } ?>
						<?php if (2 === $success) {?>
		                 <div class="alert alert-danger alert-dismissable">
		                      <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
		                      <?php echo $langage_lbl_admin['LBL_EDIT_DELETE_RECORD']; ?>
								</div><br/>
							<?php } ?>
							<form method="post" action="" enctype="multipart/form-data">
								<input type="hidden" name="id" value="<?php echo $id; ?>"/>
								<input type="hidden" name="vImage_old" value="<?php echo $vImage; ?>">

								<div class="row">
									<div class="col-lg-12">
										<label>Image<?php echo ('' === $vImage) ? '<span class="red"> *</span>' : ''; ?></label>
									</div>
									<div class="col-lg-6">
										<?php if ('' !== $vImage) { ?>
											<!-- <img src="<?php echo $tconfig['tsite_upload_images_hotel_banner'].'/'.$vImage; ?>" style="width:200px;height:100px;"> -->

											<img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?w=400&h=200&src='.$tconfig['tsite_upload_images_hotel_banner'].'/'.$vImage; ?>" style="width:200px;height:100px;">

											<input type="file" class="form-control" name="vImage" id="vImage" value="<?php echo $vImage; ?>"/>
										<?php } else { ?>
											<input type="file" class="form-control" name="vImage" id="vImage" value="<?php echo $vImage; ?>" required/>
										<?php } ?>
										<br/>
										[Note: Recommended dimension for Hotel banner image is 2880 X 1440.]
									</div>
								</div>
								<div class="row">
									<div class="col-lg-12">
										<label>Title</label>
									</div>
									<div class="col-lg-6">
										<input type="text" name="vTitle" id="vTitle" value="<?php echo $vTitle; ?>" class="form-control" />
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
										<label>Order</label>
									</div>
									<div class="col-lg-6">
										<?php
                                            $temp = 1;

						    $dataArray = [];

						    $query1 = 'SELECT iDisplayOrder FROM '.$tbl_name." WHERE vCode = '".$default_lang."' ORDER BY iDisplayOrder";
						    $data_order = $obj->MySQLSelect($query1);

						    foreach ($data_order as $value) {
						        $dataArray[] = $value['iDisplayOrder'];
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
											<?if($action=="Add") {?>
												<option value="<?php echo $temp; ?>" >
													-- <?php echo $temp; ?> --
												</option>
											<?php }?>
										</select>
									</div>
								</div>

								<div class="row">
									<?php if (('Edit' === $action && $userObj->hasPermission('edit-hotel-banner')) || ('Add' === $action && $userObj->hasPermission('create-hotel-banner'))) { ?>
										<div class="col-lg-12">
											<input type="submit" class="save btn-info" name="submit" id="submit" value="<?php echo $action; ?> Hotel Banner">
										</div>
									<?php } ?>
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
	</body>
	<!-- END BODY-->
</html>