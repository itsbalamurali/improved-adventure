<?php
include_once '../common.php';

$AUTH_OBJ->checkMemberAuthentication();

$id = $_REQUEST['id'] ?? '';
$success = $_REQUEST['success'] ?? 0;
$action = ('' !== $id) ? 'Edit' : 'Add';
$backlink = $_POST['backlink'] ?? '';
$previousLink = $_POST['backlink'] ?? '';

$tbl_name = 'visit_address';
$script = 'Visit';

$vSourceLatitude = $_POST['vSourceLatitude'] ?? '';
$vSourceLongitude = $_POST['vSourceLongitude'] ?? '';
$vDestLatitude = $_POST['vDestLatitude'] ?? '';
$vDestLongitude = $_POST['vDestLongitude'] ?? '';
$tDestLocationName = $_POST['tDestLocationName'] ?? '';
$tDestAddress = $_POST['tDestAddress'] ?? '';
$eStatus_check = $_POST['eStatus'] ?? 'off';
$iHotelId = $_POST['iHotelId'] ?? ''; // added by SP for hotel wise changes on 1-7-2019
$eStatus = ('on' === $eStatus_check) ? 'Active' : 'Inactive';

if (isset($_POST['submit'])) {
    if ('Add' === $action && !$userObj->hasPermission('create-visit')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to create visit location.';
        header('Location:visit.php');

        exit;
    }

    if ('Edit' === $action && !$userObj->hasPermission('edit-visit')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to update visit location.';
        header('Location:visit.php');

        exit;
    }
    // echo $_SESSION['SessionUserType'];exit;
    if (SITE_TYPE === 'Demo' && 'hotel' !== $_SESSION['SessionUserType']) {
        header('Location:visit_address_action.php?id='.$id.'&success=2');

        exit;
    }

    $q = 'INSERT INTO ';
    $where = '';

    if ('' !== $id) {
        $q = 'UPDATE ';
        $where = " WHERE `iVisitId` = '".$id."'";
    }

    // added by SP for hotel wise changes on 1-7-2019
    $query = $q.' `'.$tbl_name."` SET
	`vSourceLatitude` = '".$vSourceLatitude."',
	`vSourceLongitude` = '".$vSourceLongitude."',
	`vDestLatitude` = '".$vDestLatitude."',
	`vDestLongitude` = '".$vDestLongitude."',
	`tDestLocationName` = '".$tDestLocationName."',
	`tDestAddress` = '".$tDestAddress."',
            `ihotelId` = '".$iHotelId."',
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
    $sql = 'SELECT * FROM '.$tbl_name." WHERE iVisitId = '".$id."'";
    $db_data = $obj->MySQLSelect($sql);
    $vLabel = $id;
    if (count($db_data) > 0) {
        foreach ($db_data as $key => $value) {
            $vSourceLatitude = $value['vSourceLatitude'];
            $vSourceLongitude = $value['vSourceLongitude'];
            $vDestLatitude = $value['vDestLatitude'];
            $vDestLongitude = $value['vDestLongitude'];
            $tDestLocationName = $value['tDestLocationName'];
            $tDestAddress = $value['tDestAddress'];
            $eStatus = $value['eStatus'];
            $iHotelId = $value['iHotelId'];
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
		<title>Admin | Visit Location <?php echo $action; ?></title>

		<meta content="width=device-width, initial-scale=1.0" name="viewport" />

		<link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />

		<?php include_once 'global_files.php'; ?>
		<!-- On OFF switch -->
		<link href="../assets/css/jquery-ui.css" rel="stylesheet" />

		<link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
		 <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&language=en&key=<?php echo $GOOGLE_SEVER_API_KEY_WEB; ?>"></script>
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
							<h2><?php echo $action; ?> Visit Location</h2>
							<a href="visit.php" class="back_link">
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
							<form method="post" name="_visit_address_form" id="_visit_address_form" action="">
								<input type="hidden" name="vSourceLatitude" id="vSourceLatitude" value="<?php echo $vSourceLatitude; ?>"/>
								<input type="hidden" name="vSourceLongitude" id="vSourceLongitude" value="<?php echo $vSourceLongitude; ?>"/>
								<input type="hidden" name="vDestLatitude"  id="vDestLatitude"value="<?php echo $vDestLatitude; ?>"/>
								<input type="hidden" name="vDestLongitude" id="vDestLongitude"  value="<?php echo $vDestLongitude; ?>"/>
								<input type="hidden" name="id" value="<?php echo $id; ?>"/>
								<input type="hidden" name="previousLink" id="previousLink" value="<?php echo $previousLink; ?>"/>
								<input type="hidden" name="backlink" id="backlink" value="visit.php"/>

                                <?php // added by SP for hotel wise changes on 1-7-2019 start
                                    if ('hotel' !== $_SESSION['SessionUserType']) { ?>
								<div class="row">
									<div class="col-lg-12">
										<label>Select Hotel<span class="red"> *</span></label>
									</div>
									<div class="col-lg-6">
                                        <?php
                                        $sql = "SELECT * FROM administrators WHERE iGroupId = 4 and eStatus='Active'";
                                        $db_visithotel = $obj->MySQLSelect($sql);
                                        ?>
                                        <select name="iHotelId" class="form-control" required>
                                            <option value="">Select Hotel</option>
                                            <?php foreach ($db_visithotel as $key => $value) { ?>
                                            <option value="<?php echo $value['iAdminId']; ?>" <?php if ($iHotelId === $value['iAdminId']) { ?>selected <?php } ?>><?php echo clearName(' '.$value['vFirstName'].' '.$value['vLastName']); ?></option>
                                            <?php } ?>
                                        </select>

									</div>
								</div>
                                <?php } else { ?>
                                <input type="hidden" name="iHotelId" value="<?php echo $_SESSION['sess_iAdminUserId']; ?>">
                                <?php } // added by SP for hotel wise changes on 1-7-2019 end?>
								<div class="row">
									<div class="col-lg-12">
										<label>Destination Location Title<span class="red"> *</span></label>
									</div>
									<div class="col-lg-6">
										<input type="text" class="form-control" name="tDestLocationName"  id="tDestLocationName" value="<?php echo $tDestLocationName; ?>" placeholder="Destination Location Title" required>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-12">
										<label>Destination Location<span class="red"> *</span></label>
									</div>
									<div class="col-lg-6">
										<input type="text" class="form-control" name="tDestAddress"  id="tDestAddress" value="<?php echo $tDestAddress; ?>" placeholder="Destination Location" required>
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
										<input type="submit" class=" btn btn-default" name="submit" id="submit" value="<?php echo $action; ?> Visit Location">
										<a href="javascript:void(0);" onclick="reset_form('_visit_address_form');" class="btn btn-default">Reset</a>
                                        <a href="visit.php" class="btn btn-default back_link">Cancel</a>
									</div>
								</div>
							</form>
						</div>
					</div>
					<div class="admin-notes">
                        <h4>Notes:</h4>
                        <ul>
                            <li>
                                This Module will list the Pre defined location for hotels.
                            </li>
							<li>
                                Admin can add the location on behalf of hotel.
                            </li>
							<li>
                                Also, hotel can add their predefined location from their hotel session.
                            </li>
						</ul>
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
		referrer = "visit.php";
	}else {
		$("#backlink").val(referrer);

	}
	$(".back_link").attr('href',referrer);


	$('#vSourceAddresss').keyup(function (e) {
			buildAutoComplete("vSourceAddresss",e, "<?php echo $MIN_CHAR_REQ_GOOGLE_AUTO_COMPLETE; ?>","<?php echo $_SESSION['sess_lang']; ?>", function(latitude, longitude, address){
				$('#vSourceLatitude').val(latitude);
				$('#vSourceLongitude').val(longitude);
			});
		});

		$('#tDestAddress').keyup(function (e) {
			buildAutoComplete("tDestAddress",e, "<?php echo $MIN_CHAR_REQ_GOOGLE_AUTO_COMPLETE; ?>","<?php echo $_SESSION['sess_lang']; ?>", function(latitude, longitude, address){
				$('#vDestLatitude').val(latitude);
				$('#vDestLongitude').val(longitude);
			});
		});

});

	// var from = document.getElementById('vSourceAddresss');
	// autocomplete_from1 = new google.maps.places.Autocomplete(from);
	// google.maps.event.addListener(autocomplete_from1, 'place_changed', function() {
		// var placeaddress = autocomplete_from1.getPlace();

		// $('#vSourceLatitude').val(placeaddress.geometry.location.lat());
		// $('#vSourceLongitude').val(placeaddress.geometry.location.lng());
		// chkform();
	// });

		 // var tolocation = document.getElementById('tDestAddress');
			// autocomplete_from = new google.maps.places.Autocomplete(tolocation);
			// google.maps.event.addListener(autocomplete_from, 'place_changed', function() {
				// var placelocation = autocomplete_from.getPlace();
				// //console.log(placelocation.geometry.location.lat());
				// //console.log(placelocation.geometry.location.lng());
				// $('#vDestLatitude').val(placelocation.geometry.location.lat());
				// $('#vDestLongitude').val(placelocation.geometry.location.lng());

	// });



			google.maps.event.addDomListener(tolocation, 'keydown', function(event) {
		    if (event.keyCode === 13) {
		        event.preventDefault();
		    }
		  });



</script>
