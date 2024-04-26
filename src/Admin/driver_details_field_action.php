<?php
include_once '../common.php';
if (!$userObj->hasPermission('edit-driver-detail-fields-rideshare')) {
    $userObj->redirect();
}

$id = $_REQUEST['id'] ?? '';
$success = $_REQUEST['success'] ?? 0;
$action = ('' !== $id) ? 'Edit' : 'Add';

$backlink = $_POST['backlink'] ?? '';
$previousLink = $_POST['backlink'] ?? '';

$tbl_name = 'ride_share_driver_fields';
$script = 'RideShareDriverFields';

$db_master = $obj->MySQLSelect('SELECT * FROM `language_master` ORDER BY `iDispOrder`');
$count_all = count($db_master);

// set all variables with either post (when submit) either blank (when insert)
$eStatus_check = $_POST['eStatus'] ?? 'off';
$eStatus = ('on' === $eStatus_check) ? 'Active' : 'Inactive';
$iDisplayOrder = $_POST['iDisplayOrder'] ?? '1';
$oldDisplayOrder = $_POST['oldDisplayOrder'] ?? '';
$vFieldName = $_POST['vFieldName'] ?? '';
$tDescription = $_POST['tDescription'] ?? '';
$eInputType = $_POST['eInputType'] ?? 'Text';
$eRequired = $_POST['eRequired'] ?? 'Yes';
$eEditable = $_POST['eEditable'] ?? 'Yes';

if (isset($_POST['submit'])) {
    if (SITE_TYPE === 'Demo') {
        header('Location:driver_details_field_action.php?id='.$id.'&success=2');

        exit;
    }

    $display_order_cuisine = $obj->MySQLSelect("SELECT MAX(iDisplayOrder) as max_display_order, MIN(iDisplayOrder) as min_display_order FROM {$tbl_name} WHERE eStatus != 'Deleted' ");
    $max_display_order = $display_order_cuisine[0]['max_display_order'];
    $min_display_order = $display_order_cuisine[0]['min_display_order'];

    if ('Add' === $action) {
        if ($iDisplayOrder < $max_display_order) {
            $obj->sql_query("UPDATE {$tbl_name} SET iDisplayOrder = (iDisplayOrder + 1) WHERE iDisplayOrder >= '{$iDisplayOrder}' AND eStatus != 'Deleted' ");
        }
    } else {
        if (($iDisplayOrder < $max_display_order && $iDisplayOrder > $oldDisplayOrder) || ($iDisplayOrder === $max_display_order)) {
            $obj->sql_query("UPDATE {$tbl_name} SET iDisplayOrder = (iDisplayOrder - 1) WHERE iDisplayOrder <= '{$iDisplayOrder}' AND iDisplayOrder > '{$oldDisplayOrder}' AND eStatus != 'Deleted' ");
        } elseif ($iDisplayOrder < $max_display_order && $iDisplayOrder < $oldDisplayOrder) {
            $obj->sql_query("UPDATE {$tbl_name} SET iDisplayOrder = (iDisplayOrder + 1) WHERE iDisplayOrder >= '{$iDisplayOrder}' AND iDisplayOrder < '{$oldDisplayOrder}' AND eStatus = 'Deleted' ");
        }
    }

    $tFieldNameArr = [];
    for ($i = 0; $i < count($db_master); ++$i) {
        $tFieldNameArr['tFieldName_'.$db_master[$i]['vCode']] = $_POST['tFieldName_'.$db_master[$i]['vCode']];
    }

    $jsonFieldName = getJsonFromAnArr($tFieldNameArr);

    $Data_Field['vFieldName'] = $vFieldName;
    $Data_Field['tFieldName'] = $jsonFieldName;
    $Data_Field['tDescription'] = $tDescription;
    $Data_Field['iDisplayOrder'] = $iDisplayOrder;
    $Data_Field['eRequired'] = $eRequired;
    $Data_Field['eInputType'] = $eInputType;
    $Data_Field['eEditable'] = $eEditable;
    $Data_Field['eStatus'] = $eStatus;

    $_SESSION['success'] = '1';

    if ('' !== $id) {
        $where = "  `iFieldId` = '".$id."'";
        $obj->MySQLQueryPerform($tbl_name, $Data_Field, 'update', $where);

        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
    } else {
        $obj->MySQLQueryPerform($tbl_name, $Data_Field, 'insert');

        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INSERT_MSG'];
    }

    header('location:driver_details_field.php');

    exit;
}

$userEditDataArr = [];
// for Edit
if ('Edit' === $action) {
    $sql = 'SELECT * FROM '.$tbl_name." WHERE iFieldId = '".$id."'";
    $db_data = $obj->MySQLSelect($sql);

    $tFieldNameArr = json_decode($db_data[0]['tFieldName'], true);

    foreach ($tFieldNameArr as $key => $value) {
        $userEditDataArr[$key] = $value;
    }

    $iFieldId = $db_data[0]['iFieldId'];
    $vFieldName = $db_data[0]['vFieldName'];
    $eStatus = $db_data[0]['eStatus'];
    $iDisplayOrder = $db_data[0]['iDisplayOrder'];
    $tDescription = $db_data[0]['tDescription'];
    $eRequired = $db_data[0]['eRequired'];
    $eEditable = $db_data[0]['eEditable'];
    $eInputType = $db_data[0]['eInputType'];
}

$EN_available = $LANG_OBJ->checkLanguageExist();
$db_master = $LANG_OBJ->getLangDataDefaultFirst($db_master);

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en"> <!--<![endif]-->

<!-- BEGIN HEAD-->
<head>
	<meta charset="UTF-8" />
	<title>Admin | Driver Details Fields <?php echo $action; ?></title>
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
						<h2><?php echo $action; ?> Driver Details Field</h2>
						<a href="driver_details_field.php" class="back_link">
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
						</div>
						<?php } elseif (2 === $success) { ?>
						<div class="alert alert-danger alert-dismissable">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							<?php echo $langage_lbl_admin['LBL_EDIT_DELETE_RECORD']; ?>
						</div>
						<?php } ?>

						<form name="_driver_field_form" id="_driver_field_form" action="" method="post">
							<input type="hidden" name="id" value="<?php echo $id; ?>"/>
							<input type="hidden" name="previousLink" id="previousLink" value="<?php echo $previousLink; ?>"/>
							<input type="hidden" name="backlink" id="backlink" value="driver_details_field.php"/>
							<div class="row">
								<div class="col-lg-12">
									<label>Field Label<span class="red"> *</span></label>
								</div>
								<div class="col-lg-6">
									<input type="text" class="form-control" name="vFieldName"  id="vFieldName" value="<?php echo $vFieldName; ?>" placeholder="Field Label" required>
								</div>
							</div>
							<?php if (count($db_master) > 1) { ?>
							<div class="row">
                                <div class="col-lg-12">
                                    <label>Field Name<span class="red"> *</span></label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control <?php echo ('' === $id) ? 'readonly-custom' : ''; ?>" id="tFieldName_Default" name="tFieldName_Default" value="<?php echo $userEditDataArr['tFieldName_'.$default_lang]; ?>" data-originalvalue="<?php echo $userEditDataArr['tFieldName_'.$default_lang]; ?>" readonly="readonly" <?php if ('' === $id) { ?> onclick="editFieldName('Add')" <?php } ?>>
                                </div>
                                <?php if ('' !== $id) { ?>
                                <div class="col-lg-2">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editFieldName('Edit')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                </div>
                                <?php } ?>
                            </div>

                            <div  class="modal fade" id="field_name_Modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
                                <div class="modal-dialog" >
                                    <div class="modal-content nimot-class">
                                        <div class="modal-header">
                                            <h4>
                                                <span id="modal_action"></span> Field Name
                                                <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vFieldName_')">x</button>
                                            </h4>
                                        </div>

                                        <div class="modal-body">
                                            <?php
                                                for ($i = 0; $i < $count_all; ++$i) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vLTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];

                                                    $vNameval = 'tFieldName_'.$vCode;
                                                    ${$vNameval} = $userEditDataArr[$vNameval];
                                                    ${$Desc} = 'Field Name '.$vCode;

                                                    $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <label>Field Name (<?php echo $vLTitle; ?>) <?php echo $required_msg; ?></label>

                                                        </div>
                                                        <div class="col-lg-12">
                                                            <input type="text" class="form-control" name="<?php echo $vNameval; ?>"  id="<?php echo $vNameval; ?>" value="<?php echo ${$vNameval}; ?>" data-originalvalue="<?php echo ${$vNameval}; ?>" placeholder="<?php echo ${$Desc}; ?> Value">
															<div class="text-danger" id="<?php echo $vNameval.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                        </div>
                                                        <?php
                                                                if (count($db_master) > 1) {
                                                                    if ($EN_available) {
                                                                        if ('EN' === $vCode) { ?>
                                                                <div class="col-lg-12">
                                                                    <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('tFieldName_', 'EN');" style="margin-top: 10px">Convert To All Language</button>
                                                                </div>
                                                            <?php }
                                                                        } else {
                                                                            if ($vCode === $default_lang) { ?>
                                                                <div class="col-lg-12">
                                                                    <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('tFieldName_', '<?php echo $default_lang; ?>');" style="margin-top: 10px">Convert To All Language</button>
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
                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                <button type="button" class="save" style="margin-left: 0 !important" onclick="saveFieldName()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vFieldName_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                            </div>
                                        </div>

                                        <div style="clear:both;"></div>
                                    </div>
                                </div>

                            </div>
                            <?php } else { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Field Name<span class="red"> *</span></label>
                                </div>
                                <div class="col-lg-6">
                                    <input type="text" class="form-control" id="vFieldName_<?php echo $default_lang; ?>" name="vFieldName_<?php echo $default_lang; ?>" value="<?php echo $arrLang['vFieldName_'.$default_lang]; ?>" >
                                </div>
                            </div>
                            <?php } ?>

							<div class="row">
								<div class="col-lg-12">
									<label>Description</label>
								</div>
								<div class="col-lg-6">
									<input type="text" class="form-control" name="tDescription"  id="tDescription" value="<?php echo $tDescription; ?>" placeholder="Description">
								</div>
							</div>

							<div class="row">
								<div class="col-lg-12">
									<label>Input Type<span class="red"> *</span></label>
								</div>
								<div class="col-lg-6">
									<select class="form-control" name="eInputType" id="eInputType" >
										<option value="Text" <?php echo ('Text' === $eInputType) ? 'selected' : ''; ?>>Text</option>
										<option value="Textarea" <?php echo ('Textarea' === $eInputType) ? 'selected' : ''; ?>>Textarea</option>
										<option value="Number" <?php echo ('Number' === $eInputType) ? 'selected' : ''; ?>>Number</option>
									</select>
								</div>
							</div>

							<div class="row">
								<div class="col-lg-12">
									<label>Required<span class="red"> *</span></label>
								</div>
								<div class="col-lg-6">
									<select class="form-control" name="eRequired" id="eRequired" >
										<option value="Yes" <?php echo ('Yes' === $eRequired) ? 'selected' : ''; ?>>Yes</option>
										<option value="No" <?php echo ('No' === $eRequired) ? 'selected' : ''; ?>>No</option>

									</select>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12">
									<label>Editable<span class="red"> *</span></label>
								</div>
								<div class="col-lg-6">
									<select class="form-control" name="eEditable" id="eEditable" >
										<option value="Yes" <?php echo ('Yes' === $eEditable) ? 'selected' : ''; ?>>Yes</option>
										<option value="No" <?php echo ('No' === $eEditable) ? 'selected' : ''; ?>>No</option>
									</select>
								</div>
							</div>

							<div class="row">
                                <div class="col-lg-12">
                                    <label>Display Order <span class="red"> *</span></label>
                                </div>
                                <div class="col-md-6 col-sm-6" id="showDisplayOrder001">
                                </div>
                                <input type="hidden" name="oldDisplayOrder" value="<?php echo $iDisplayOrder; ?>">
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
									<input type="submit" class=" btn btn-default" name="submit" id="submit" value="<?php echo $action; ?> Field">
									<input type="reset" value="Reset" class="btn btn-default">
                                    <a href="driver_details_field.php" class="btn btn-default back_link">Cancel</a>
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
	<div class="row loding-action" id="loaderIcon" style="display:none; z-index: 99999">
        <div align="center">
            <img src="default.gif">
            <span>Language Translation is in Process. Please Wait...</span>
        </div>
    </div>

	<?php include_once 'footer.php'; ?>
	<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>

	<script>
		$(document).ready(function() {
			var referrer;
			if($("#previousLink").val() == "" ) {
				referrer =  document.referrer;
			} else {
				referrer = $("#previousLink").val();
			}

			if(referrer == "") {
				referrer = "driver_details_field.php";
			}else {
				$("#backlink").val(referrer);
			}
			$(".back_link").attr('href', referrer);

			changeDisplayOrder();
		});

		function editFieldName(action)
		{
		    $('#modal_action').html(action);
		    $('#field_name_Modal').modal('show');
		}

		function saveFieldName()
		{
		    if($('#tFieldName_<?php echo $default_lang; ?>').val() == "") {
		        $('#tFieldName_<?php echo $default_lang; ?>_error').show();
		        $('#tFieldName_<?php echo $default_lang; ?>').focus();
		        clearInterval(myVar);
		        myVar = setTimeout(function() {
		            $('#tFieldName_<?php echo $default_lang; ?>_error').hide();
		        }, 5000);
		        return false;
		    }

		    $('#tFieldName_Default').val($('#tFieldName_<?php echo $default_lang; ?>').val());
		    $('#field_name_Modal').modal('hide');
		}

		function changeDisplayOrder() {
            var ajaxData = {
                'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_display_order.php',
                'AJAX_DATA': {page: 'driver_details_field', method: '<?php echo $action; ?>', iDisplayOrder: '<?php echo $iDisplayOrder; ?>'},
            };
            getDataFromAjaxCall(ajaxData, function(response) {
                if(response.action == "1") {
                    var data = response.result;
                    $("#showDisplayOrder001").html('');
                    $("#showDisplayOrder001").html(data);
                }
                else {
                    // console.log(response.result);
                }
            });

        }
	</script>
</body>
	<!-- END BODY-->
</html>