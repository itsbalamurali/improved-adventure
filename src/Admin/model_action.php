<?php
include_once '../common.php';

$id = $_REQUEST['id'] ?? '';
$success = $_REQUEST['success'] ?? 0;
$action = ('' !== $id) ? 'Edit' : 'Add';

$tbl_name = 'model';
$script = 'Model';

$backlink = $_POST['backlink'] ?? '';
$previousLink = $_POST['backlink'] ?? '';

// set all variables with either post (when submit) either blank (when insert)
$vTitle = $_POST['vTitle'] ?? '';
$iMakeId = $_POST['iMakeId'] ?? '';
$eStatus_check = $_POST['eStatus'] ?? 'off';
$eStatus = ('on' === $eStatus_check) ? 'Active' : 'Inactive';

$sql = "SELECT * from make WHERE eStatus='Active' ORDER BY vMake ASC ";
$db_make = $obj->MySQLSelect($sql);

if (isset($_POST['submit'])) {
    if ('Add' === $action && !$userObj->hasPermission('create-vehicle-model')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to create model.';
        header('Location:model.php');

        exit;
    }

    if ('Edit' === $action && !$userObj->hasPermission('edit-vehicle-model')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to update model.';
        header('Location:model.php');

        exit;
    }

    if (SITE_TYPE === 'Demo') {
        header('Location:model_action.php?id='.$id.'&success=2');

        exit;
    }

    $q = 'INSERT INTO ';
    $where = '';

    if ('' !== $id) {
        $q = 'UPDATE ';
        $where = " WHERE `iModelId` = '".$id."'";
    }

    $query = $q.' `'.$tbl_name."` SET
		`vTitle` = '".$vTitle."',
		`iMakeId` = '".$iMakeId."',
		`eStatus` = '".$eStatus."'"
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
    $sql = "SELECT model.*,make.vMake FROM model left join make on make.iMakeId = model.iMakeId  WHERE iModelId = '".$id."'";
    $db_data = $obj->MySQLSelect($sql);

    $vLabel = $id;
    if (count($db_data) > 0) {
        foreach ($db_data as $key => $value) {
            $vTitle = $value['vTitle'];
            $vMake = $value['vMake'];
            $eStatus = $value['eStatus'];
            $iMakeId = $value['iMakeId'];
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
		<title>Admin | Model <?php echo $action; ?></title>
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
							<h2><?php echo $action; ?> Model</h2>
							<a href="model.php" class="back_link">
								<input type="button" value="Back to Listing" class="add-btn">
							</a>
						</div>
					</div>
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
								<?php }?>
							<form method="post" name="_model_form" id="_model_form" action="">
								<input type="hidden" name="id" value="<?php echo $id; ?>"/>
								<input type="hidden" name="previousLink" id="previousLink" value="<?php echo $previousLink; ?>"/>
								<input type="hidden" name="backlink" id="backlink" value="model.php"/>
								<div class="row">
									<div class="col-lg-12">
										<label>Make</label>
									</div>
									<div class="col-lg-6 ">
										<select name = "iMakeId" id="iMakeId" class="form-control">
											<?php for ($j = 0; $j < count($db_make); ++$j) {?>
												<option value="<?php echo $db_make[$j]['iMakeId']; ?>" <?php if ($iMakeId === $db_make[$j]['iMakeId']) {?> selected <?php }?>><?php echo $db_make[$j]['vMake']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-12">
										<label>Model<span class="red"> *</span></label>
									</div>
									<div class="col-lg-6">
										<input type="text" class="form-control" name="vTitle"  id="vTitle" value="<?php echo $vTitle; ?>" placeholder="Model" required>
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
										<?php if (('Edit' === $action && $userObj->hasPermission('edit-vehicle-model')) || ('Add' === $action && $userObj->hasPermission('create-vehicle-model'))) { ?>
											<input type="submit" class="btn btn-default" name="submit" id="submit" value="<?php echo $action; ?> Model">
											<input type="reset" value="Reset" class="btn btn-default">
										<?php } ?>
										<!-- <a href="javascript:void(0);" onclick="reset_form('_model_form');" class="btn btn-default">Reset</a> -->
                                        <a href="model.php" class="btn btn-default back_link">Cancel</a>
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
	</body>
	<!-- END BODY-->
</html>
<script>
$(document).ready(function() {
	var referrer;
	if($("#previousLink").val() == "" ){
		referrer =  document.referrer;
	}else {
		referrer = $("#previousLink").val();
	}

	if(referrer == "") {
		referrer = "make.php";
	}else {
		$("#backlink").val(referrer);
	}
	$(".back_link").attr('href',referrer);
});
</script>