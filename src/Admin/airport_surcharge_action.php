<?php
include_once '../common.php';

require_once TPATH_CLASS.'/Imagecrop.class.php';
$thumb = new thumbnail();

$id = $_REQUEST['id'] ?? '';
$success = $_REQUEST['success'] ?? 0;
$action = ('' !== $id) ? 'Edit' : 'Add';
$script = 'airportsurcharge_fare';

$tbl_name = 'airportsurcharge_fare';
$tbl_name1 = 'location_master';

$iLocationIds = $_POST['iLocationIds'] ?? '';
$vFromCity = $_POST['vFromCity'] ?? '';
$fpickupsurchargefare = $_POST['fpickupsurchargefare'] ?? '';
$fdropoffsurchargefare = $_POST['fdropoffsurchargefare'] ?? '';
$backlink = $_POST['backlink'] ?? '';
$previousLink = $_POST['backlink'] ?? '';
$iVehicleTypeId = $_POST['iVehicleTypeId'] ?? '';

$sql = "SELECT vName,vSymbol FROM currency WHERE eDefault = 'Yes'";
$db_currency = $obj->MySQLSelect($sql);

if (isset($_POST['submit'])) {
    if ('Add' === $action && !$userObj->hasPermission('create-airport-surcharge')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to create airport surcharge.';
        header('Location:airport_surcharge.php');

        exit;
    }

    if ('Edit' === $action && !$userObj->hasPermission('edit-airport-surcharge')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to update airport surcharge.';
        header('Location:airport_surcharge.php');

        exit;
    }
    // if(!empty($id)){
    //   if(SITE_TYPE =='Demo'){
    //    $_SESSION['success'] = 2;
    //    header("Location:airport_surcharge.php?id=".$id);
    //    exit;
    //   }
    // }

    if (SITE_TYPE === 'Demo') {
        $_SESSION['success'] = 2;
        header('Location:airport_surcharge.php?id='.$id);

        exit;
    }

    if ('Add' === $action) {
        $sqlquery = 'SELECT count(iLocatioId) as location FROM '.$tbl_name." WHERE  iLocationIds = '".$iLocationIds."' AND iVehicleTypeId = '".$iVehicleTypeId."' ";
        $db_getlocation = $obj->MySQLSelect($sqlquery);

        if ($db_getlocation[0]['location'] > 0) {
            $_SESSION['success'] = '3';
            $_SESSION['var_msg'] = 'Same Entry already Exists please check again.';
            header('Location:'.$backlink);

            exit;
        }
    }

    $q = 'INSERT INTO ';
    $where = '';

    if ('' !== $id) {
        $q = 'UPDATE ';
        $where = " WHERE `iLocatioId` = '".$id."'";
    }

    $query = $q.' `'.$tbl_name."` SET
			`fpickupsurchargefare` = '".$fpickupsurchargefare."',
  			`fdropoffsurchargefare` = '".$fdropoffsurchargefare."',
			`iLocationIds` = '".$iLocationIds."',
  			`vFromCity` = '".$vFromCity."',
			`iVehicleTypeId` = '".$iVehicleTypeId."'".$where;

    $obj->sql_query($query);

    $id = ('' !== $id) ? $id : $obj->GetInsertId();

    if ('Add' === $action) {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INSERT_MSG'];
    } else {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
    }

    header('Location:'.$backlink);

    exit;
}
// for Edit
if ('Edit' === $action) {
    $sql = 'SELECT * FROM '.$tbl_name." WHERE iLocatioId = '".$id."'";
    $db_data = $obj->MySQLSelect($sql);

    $vLabel = $id;
    if (count($db_data) > 0) {
        foreach ($db_data as $key => $value) {
            $vFromCity = $value['vFromCity'];
            $iVehicleTypeId = $value['iVehicleTypeId'];
            $iLocationIds = $value['iLocationIds'];
            $fpickupsurchargefare = $value['fpickupsurchargefare'];
            $fdropoffsurchargefare = $value['fdropoffsurchargefare'];
        }
    }
}

$query = 'SELECT vLocationName,iLocationId FROM '.$tbl_name1." WHERE eFor = 'AirportSurcharge' AND eStatus = 'Active'";
$db_location = $obj->MySQLSelect($query);
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
     <!-- BEGIN HEAD-->
     <head>
          <meta charset="UTF-8" />
          <title>Admin | Airport Surcharge <?php echo $action; ?></title>
          <meta content="width=device-width, initial-scale=1.0" name="viewport" />
		  <link href="css/bootstrap-select.css" rel="stylesheet" />
          <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
          <?php include_once 'global_files.php'; ?>
          <!-- On OFF switch -->
          <link href="../assets/css/jquery-ui.css" rel="stylesheet" />
          <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
     </head>
     <!-- END  HEAD-->
     <!-- BEGIN BODY-->
     <body class="padTop53">
          <!-- MAIN WRAPPER -->
          <div id="wrap">
               <?php include_once 'header.php'; ?>
               <?php include_once 'left_menu.php'; ?>
               <!--PAGE CONTENT -->
               <div id="content">
                    <div class="inner">
                         <div class="row">
                              <div class="col-lg-12">
                                   <h2><?php echo $action.' Airport Surcharge'; ?> </h2>
                                   <a class="back_link" href="airport_surcharge.php">
                                        <input type="button" value="Back to Listing" class="add-btn">
                                   </a>
                              </div>
                         </div>
                         <hr />
                         <?php if (2 === $success) { ?>
               						<div class="alert alert-danger alert-dismissable msgs_hide">
               								 <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
               								 <?php echo $langage_lbl_admin['LBL_EDIT_DELETE_RECORD']; ?>
               						</div><br/>
               					<?php } ?>
                         <div class="body-div">
                           <div class="form-group location-wise-box">
                                     <?php if (1 === $success) { ?>
                                     <div class="alert alert-success alert-dismissable msgs_hide">
                                          <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                         <?php echo $langage_lbl_admin['LBL_Record_Updated_successfully']; ?>
                                     </div><br/>
                                     <?php } ?>
                                     <form method="post" action="" enctype="multipart/form-data" id="locationfareForm">
                                          <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                                          <input type="hidden" name="previousLink" id="previousLink" value="<?php echo $previousLink; ?>"/>
                                          <input type="hidden" name="backlink" id="backlink" value="airport_surcharge.php"/>

                                          <div class="row">
                                               <div class="col-lg-12">
                                                    <label>Airport Location <span class="red"> *</span></label>
                                               </div>
                                               <div class="col-lg-6">
                                                    <select name="iLocationIds" class="form-control" required="required" onChange="getvehilcetype(this.value,'');">
                                                      <option value="">Select Airport Location Name</option>
                                                      <?php foreach ($db_location as $key => $value) { ?>
                                                      <option value="<?php echo $value['iLocationId']; ?>" <?php if ($value['iLocationId'] === $iLocationIds) {
                                                          echo 'selected';
                                                      } ?>>
                                                        <?php echo $value['vLocationName']; ?></option>
                                                        <?php } ?>
                                                   </select>
                                               </div>
                                               <?php if ($userObj->hasPermission('view-geo-fence-locations')) { ?>
                                                 <div class="col-lg-6">
                                                   <a class="btn btn-primary" href="location.php" target="_blank">Enter New Location</a>
                                                </div>
                                              <?php } ?>
                                          </div>

                                          <div class="row">
                                            <div class="col-lg-12">
                                              <label>Vehicle Type<span class="red"> *</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                              <select class="form-control selectpicker"  name = 'iVehicleTypeId' id="iVehicleTypeId" required="required" data-live-search="true" >
                                                <option value="">Select Vehicle Type</option>
                                              </select>
                                            </div>
                                            <div class="clear"></div>
                                            <div class="col-lg-12 restrict_area">
                                              <div class="exist_area error"></div>
                                            </div>
                                          </div>
										  <div class="row">
                                               <div class="col-lg-12">
                                                    <label>Enter Pickup Surcharge (x)<span class="red">*</span>&nbsp;<i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title='If the user source destination falls in this region then surge applied.'></i></label>
                                               </div>
                                               <div class="col-lg-6">
                                                    <input type="text" class="form-control" name="fpickupsurchargefare" min="1" id="fpickupsurchargefare" value="<?php echo $fpickupsurchargefare; ?>" required="required">
													<br/>
														[Note: Enter 1 if you dont want to add surcharge]

                                               </div>
                                          </div>
										  <div class="row">
                                               <div class="col-lg-12">
                                                    <label>Enter Dropoff Surcharge (x)<span class="red">*</span>&nbsp;<i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title='If the user destination falls in this region then surge applied.'></i></label>
                                               </div>
                                               <div class="col-lg-6">
                                                    <input type="text" class="form-control" name="fdropoffsurchargefare" min="1" id="fdropoffsurchargefare" value="<?php echo $fdropoffsurchargefare; ?>" required="required">
													<br/>
														[Note: Enter 1 if you dont want to add surcharge]

                                               </div>
                                          </div>

                                          <div class="row">
                                               <div class="col-lg-12">
                                                <?php if (('Edit' === $action && $userObj->hasPermission('edit-airport-surcharge')) || ('Add' === $action && $userObj->hasPermission('create-airport-surcharge'))) { ?>
                                                    <input type="submit" class="save btn-info" name="submit" id="submit" value="<?php if ('Add' === $action) {?><?php echo $action; ?> Airport Surcharge<?php } else { ?>Update<?php } ?>">
                                                  <?php } else { ?>
                                                    <a href="airport_surcharge.php" class="btn btn-default back_link">Cancel</a>
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
          <?php include_once 'footer.php'; ?>
      <script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
		  <script src="js/bootstrap-select.js"></script>
      <script>

		  $(document).ready(function() {
		  $(window).keydown(function(event){
			if(event.keyCode == 13) {
			  event.preventDefault();
			  return false;
			}
		  });
		});

	 var successMSG1 = '<?php echo $success; ?>';

		if(successMSG1 != ''){
			 setTimeout(function() {
				$(".msgs_hide").hide(1000)
			}, 5000);
		}

    $(document).ready(function() {
      var referrer;
      if($("#previousLink").val() == "" ){
        referrer =  document.referrer;
        //alert(referrer);
      }else {
        referrer = $("#previousLink").val();
      }
      if(referrer == "") {
        referrer = "vehicles.php";
      }else {
        $("#backlink").val(referrer);
      }
      $(".back_link").attr('href',referrer);

      // jquery validation
        $('#locationfareForm').validate({
            rules: {
                iLocationIds: {
                    required: true
                },
                fpickupsurchargefare:{
                  required: true,
                  number: true
                },
				fdropoffsurchargefare:{
                  required: true,
                  number: true
                }
            },
            messages: {
                iLocationIds: {
                    required: 'Please Select From Location.'
                },
                fpickupsurchargefare:{
                   required: 'Please Add pickup surcharge.'
                },
                fdropoffsurchargefare:{
                   required: 'Please Add drop surcharge.'
                }
            }
        });

    });
    getvehilcetype('<?php echo $iLocationIds; ?>','<?php echo $iVehicleTypeId; ?>');
    // function getvehilcetype(id,selected='')
	function getvehilcetype(id,selected)
    {
		selected = selected || '';
		// var request = $.ajax({
		// 	type: "POST",
		// 	url: 'ajax_get_vehicletype_airportsurcharge.php',
		// 	data: {iLocationId: id,selected: selected},
		// 	success: function (dataHtml)
		// 	{
		// 	  $("#iVehicleTypeId").html(dataHtml).selectpicker('refresh');
		// 	}
        // });

        var ajaxData = {
            'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_get_vehicletype_airportsurcharge.php',
            'AJAX_DATA': {iLocationId: id,selected: selected},
        };
        getDataFromAjaxCall(ajaxData, function(response) {
            if(response.action == "1") {
                var dataHtml = response.result;
                $("#iVehicleTypeId").html(dataHtml).selectpicker('refresh');
            }
            else {
                console.log(response.result);
            }
        });
    }

    var firstval = $("select[name=iLocationIds] option:selected").val();
    $('select[name=iToLocationId]').find('option').prop('disabled', function() {
          return this.value == firstval
    });
/*    var secondval = $("select[name=iToLocationId] option:selected").val();
    $('select[name=iFromLocationId]').find('option').prop('disabled', function() {
          return this.value == secondval
    });*/
  </script>
     </body>
     <!-- END BODY-->
</html>

