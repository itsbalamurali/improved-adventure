<?php
include_once '../common.php';

    $AUTH_OBJ->checkMemberAuthentication();

$id = $_REQUEST['id'] ?? '';
$success = $_REQUEST['success'] ?? 0;
$action = ('' !== $id) ? 'Edit' : 'Add';

    $backlink = $_POST['backlink'] ?? '';
$previousLink = $_POST['backlink'] ?? '';

$tbl_name = 'delivery_fields';
$script = 'Delivery Package';

    $select_order = $obj->MySQLSelect('SELECT count(iOrder) AS iOrder FROM '.$tbl_name);
$iOrder = $select_order[0]['iOrder'] ?? 0;
$iDisplayOrder_max = $iOrder + 1; // Maximum order number

// echo '<prE>'; print_R($_REQUEST); echo '</pre>';

// fetch all lang from language_master table
$sql = 'SELECT * FROM `language_master`';
$db_master = $obj->MySQLSelect($sql);
$count_all = count($db_master);
if ($count_all > 0) {
    for ($i = 0; $i < $count_all; ++$i) {
        $vNameval1 = 'vFieldName_'.$db_master[$i]['vCode'];
        ${$vNameval1} = $_POST[$vNameval1] ?? '';
    }
}

// set all variables with either post (when submit) either blank (when insert)
$vName = $_POST['vFieldName'] ?? '';
$eStatus_check = $_POST['eStatus'] ?? 'off';
$eStatus = ('on' === $eStatus_check) ? 'Active' : 'Inactive';
$iOrder = $_POST['iOrder'] ?? $iOrder;
$temp_order = $_POST['temp_order'] ?? '';
$eInputType = $_POST['eInputType'] ?? 'Text';
$tDesc = $_POST['tDesc'] ?? '';
$eAllowFloat = $_POST['eAllowFloat'] ?? '';
$eRequired = $_POST['eRequired'] ?? 'Yes';
$eEditable = $_POST['eEditable'] ?? 'Yes';

if (isset($_POST['submit'])) {
    if (SITE_TYPE === 'Demo') {
        header('Location:delivery_package_action.php?id='.$id.'&success=2');

        exit;
    }
    if ($temp_order > $iOrder) {
        for ($i = $temp_order - 1; $i >= $iOrder; --$i) {
            $sql = 'UPDATE '.$tbl_name." SET iOrder = '".($i + 1)."' WHERE iOrder = '".$i."'";
            $obj->sql_query($sql);
        }
    } elseif ($temp_order < $iOrder) {
        for ($i = $temp_order + 1; $i <= $iOrder; ++$i) {
            $sql = 'UPDATE '.$tbl_name." SET iOrder = '".($i - 1)."' WHERE iOrder = '".$i."'";
            $obj->sql_query($sql);
        }
    }

    if (count($db_master) > 0) {
        $str = '';
        for ($i = 0; $i < count($db_master); ++$i) {
            $vNameval1 = 'vFieldName_'.$db_master[$i]['vCode'];

            ${$vNameval1} = $_REQUEST[$vNameval1];

            $str .= ' '.$vNameval1." = '".${$vNameval1}."',";
        }
    }

    $q = 'INSERT INTO ';
    $where = '';

    if ('' !== $id) {
        $q = 'UPDATE ';
        $where = " WHERE `iDeliveryFieldId` = '".$id."'";
    }

    $query = $q.' `'.$tbl_name.'` SET '.$str."
		`vFieldName` = '".$vName."',
		`iOrder` = '".$iOrder."',
		`eInputType` = '".$eInputType."',
		`tDesc` = '".$tDesc."',
		`eAllowFloat` = '".$eAllowFloat."',
		`eRequired` = '".$eRequired."',
		`eEditable` = '".$eEditable."',
		`eStatus` = '".$eStatus."'"

    .$where;

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
    $sql = 'SELECT * FROM '.$tbl_name." WHERE iDeliveryFieldId = '".$id."'";
    $db_data = $obj->MySQLSelect($sql);
    // echo "<pre>"; print_r($db_data); exit;

    $vLabel = $id;
    if (count($db_data) > 0) {
        for ($i = 0; $i < count($db_master); ++$i) {
            foreach ($db_data as $key => $value) {
                $vNameval = 'vFieldName_'.$db_master[$i]['vCode'];
                ${$vNameval} = $value[$vNameval];
                $vName = $value['vFieldName'];
                $eStatus = $value['eStatus'];
                $iOrder_db = $db_data[0]['iOrder'];
                $tDesc = $db_data[0]['tDesc'];
                $eAllowFloat = $db_data[0]['eAllowFloat'];
                $eRequired = $db_data[0]['eRequired'];
                $eEditable = $db_data[0]['eEditable'];
                $eInputType = $db_data[0]['eInputType'];
                $arrLang[$vNameval] = ${$vNameval};
            }
        }
    }
}

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
		<title>Admin | Make <?php echo $action; ?></title>
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
							<h2><?php echo $action; ?> Package Type</h2>
							<a href="delivery_package.php" class="back_link">
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
							<form method="post" name="_make_form" id="_make_form" action="">
								<input type="hidden" name="id" value="<?php echo $id; ?>"/>
								<input type="hidden" name="previousLink" id="previousLink" value="<?php echo $previousLink; ?>"/>
								<input type="hidden" name="backlink" id="backlink" value="delivery_package.php"/>
								<div class="row">
									<div class="col-lg-12">
										<label>Delivery Package Type Label<span class="red"> *</span></label>
									</div>
									<div class="col-lg-6">
										<input type="text" class="form-control" name="vFieldName"  id="vFieldName" value="<?php echo $vName; ?>" placeholder="Delivery Package Label" required>
									</div>
								</div>
								<?php if (count($db_master) > 1) { ?>
								<div class="row">
                                    <div class="col-lg-12">
                                        <label>Delivery Package Type</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control <?php echo ('' === $id) ? 'readonly-custom' : ''; ?>" id="vFieldName_Default" value="<?php echo $arrLang['vFieldName_'.$default_lang]; ?>" data-originalvalue="<?php echo $arrLang['vFieldName_'.$default_lang]; ?>" readonly="readonly" <?php if ('' === $id) { ?> onclick="editPackageType('Add')" <?php } ?>>
                                    </div>
                                    <?php if ('' !== $id) { ?>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editPackageType('Edit')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                    </div>
                                    <?php } ?>
                                </div>

                                <div  class="modal fade" id="package_type_Modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
                                    <div class="modal-dialog" >
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="modal_action"></span> Delivery Package Type
                                                    <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vFieldName_')">x</button>
                                                </h4>
                                            </div>

                                            <div class="modal-body">
                                                <?php

                                                    for ($i = 0; $i < $count_all; ++$i) {
                                                        $vCode = $db_master[$i]['vCode'];
                                                        $vLTitle = $db_master[$i]['vTitle'];
                                                        $eDefault = $db_master[$i]['eDefault'];

                                                        $vNameval = 'vFieldName_'.$vCode;
                                                        ${$Desc} = 'Delivery Package Name '.$vCode;

                                                        $required = ('Yes' === $eDefault) ? 'required' : '';
                                                        $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                                        ?>
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <label>Delivery Package Type (<?php echo $vLTitle; ?>) <?php echo $required_msg; ?></label>

                                                            </div>
                                                            <div class="col-lg-12">
                                                                <input type="text" class="form-control" name="<?php echo $vNameval; ?>"  id="<?php echo $vNameval; ?>" value="<?php echo ${$vNameval}; ?>" data-originalvalue="<?php echo ${$vNameval}; ?>" placeholder="<?php echo ${$Desc}; ?> Value" <?php echo $required; ?>>
																<div class="text-danger" id="<?php echo $vNameval.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                            </div>
                                                            <?php
                                                                    if (count($db_master) > 1) {
                                                                        if ($EN_available) {
                                                                            if ('EN' === $vCode) { ?>
                                                                    <div class="col-lg-12">
                                                                        <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vFieldName_', 'EN');" style="margin-top: 10px">Convert To All Language</button>
                                                                    </div>
                                                                <?php }
                                                                            } else {
                                                                                if ($vCode === $default_lang) { ?>
                                                                    <div class="col-lg-12">
                                                                        <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vFieldName_', '<?php echo $default_lang; ?>');" style="margin-top: 10px">Convert To All Language</button>
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
                                                    <button type="button" class="save" style="margin-left: 0 !important" onclick="savePackageType()"><?php echo $langage_lbl['LBL_Save']; ?></button>
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
                                        <label>Delivery Package Type</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" id="vFieldName_<?php echo $default_lang; ?>" name="vFieldName_<?php echo $default_lang; ?>" value="<?php echo $arrLang['vFieldName_'.$default_lang]; ?>" >
                                    </div>
                                </div>
                                <?php } ?>
									<?php /*if($count_all > 0) {
                                        for($i=0;$i<$count_all;$i++) {
                                            $vCode = $db_master[$i]['vCode'];
                                            $vLTitle = $db_master[$i]['vTitle'];
                                            $eDefault = $db_master[$i]['eDefault'];

                                            $vNameval = 'vFieldName_'.$vCode;
                                            $$Desc = 'Delivery Package Name '.$vCode;


                                            $required = ($eDefault == 'Yes')?'required':'';
                                            $required_msg = ($eDefault == 'Yes')?'<span class="red"> *</span>':'';
                                        ?>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label> Delivery Package Type (<?=$vLTitle;?>) <?=$required_msg;?></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="<?=$vNameval;?>"  id="<?=$vNameval;?>" value="<?=$$vNameval;?>" placeholder="<?=$$Desc;?> Value" <?=$required;?>>
                                                <div class="text-danger" id="<?= $vNameval.'_error'; ?>" style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                            </div>
                                            <? if($vCode == $default_lang  && count($db_master) > 1){ ?>
                                            <div class="col-lg-6">
                                                <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vFieldName_', '<?= $default_lang ?>');">Convert To All Language</button>
                                            </div>
                                            <? } ?>
                                        </div>


                                        <? }
                                    }*/ ?>
								<div class="row">
									<div class="col-lg-12">
										<label>Description<span class="red"> *</span></label>
									</div>
									<div class="col-lg-6">
										<input type="text" class="form-control" name="tDesc"  id="tDesc" value="<?php echo $tDesc; ?>" placeholder="Description" required>
									</div>
								</div>
									<div class="row">
									<div class="col-lg-12">
										<label>Order</label>
									</div>
									<div class="col-lg-6">

										<input type="hidden" name="temp_order" id="temp_order" value="<?php echo ('Edit' === $action) ? $iOrder_db : '1'; ?>">
										<?php
                                            $display_numbers = ('Add' === $action) ? $iDisplayOrder_max : $iOrder;
?>
										<select name="iOrder" class="form-control">
											<?php for ($i = 1; $i <= $display_numbers; ++$i) { ?>
												<option value="<?php echo $i; ?>" <?if($i == $iOrder_db){echo "selected";}?>> -- <?php echo $i; ?> --</option>
											<?php } ?>
										</select>

									</div>
								</div>
									<div class="row">
											<div class="col-lg-12">
												<label>InputType<span class="red"> *</span></label>
											</div>
											<div class="col-lg-6">
												<select class="form-control" name = 'eInputType' id="eInputType" >
													<option value="">Select</option>
													<option value="Text" <?php echo ('Text' === $eInputType) ? 'selected' : ''; ?>>Text</option>
													<option value="Textarea" <?php echo ('Textarea' === $eInputType) ? 'selected' : ''; ?>>Textarea</option>
													<option value="Select" <?php echo ('Select' === $eInputType) ? 'selected' : ''; ?>>Select</option>
													<option value="Number" <?php echo ('Number' === $eInputType) ? 'selected' : ''; ?>>Number</option>


												</select>
											</div>
										</div>
										<div class="row">
											<div class="col-lg-12">
												<label>AllowFloat<span class="red"> *</span></label>
											</div>
											<div class="col-lg-6">
												<select class="form-control" name = 'eAllowFloat' id="eAllowFloat" >
													<option value="">Select</option>
													<option value="Yes" <?php if ('Yes' === $eAllowFloat) {
													    echo 'selected';
													}?>>Yes</option>
													<option value="No" <?php echo ('No' === $eAllowFloat) ? 'selected' : ''; ?>>No</option>

												</select>
											</div>
										</div>
										<div class="row">
											<div class="col-lg-12">
												<label>Required<span class="red"> *</span></label>
											</div>
											<div class="col-lg-6">
												<select class="form-control" name = 'eRequired' id="eRequired" >
													<option value="">Select</option>
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
												<select class="form-control" name = 'eEditable' id="eEditable" >
													<option value="">Select</option>
													<option value="Yes" <?php echo ('Yes' === $eEditable) ? 'selected' : ''; ?>>Yes</option>
													<option value="No" <?php echo ('No' === $eEditable) ? 'selected' : ''; ?>>No</option>

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
										<input type="submit" class=" btn btn-default" name="submit" id="submit" value="<?php echo $action; ?> Delivery Package">
										<input type="reset" value="Reset" class="btn btn-default">
										<!-- <a href="javascript:void(0);" onclick="reset_form('_make_form');" class="btn btn-default">Reset</a> -->
                                        <a href="delivery_package.php" class="btn btn-default back_link">Cancel</a>
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
	</body>
	<!-- END BODY-->
</html>
<script>
$(document).ready(function() {
	var referrer;
	if($("#previousLink").val() == "" ){ //alert('pre1');
		referrer =  document.referrer;
		// alert(referrer);
	}else { //alert('pre2');
		referrer = $("#previousLink").val();
	}

	if(referrer == "") {
		referrer = "delivery_package.php";
	}else { //alert('hi');
		$("#backlink").val(referrer);
		// alert($("#backlink").val(referrer));
	}
	$(".back_link").attr('href',referrer);
	//alert($(".back_link").attr('href',referrer));
});

function editPackageType(action)
{
    $('#modal_action').html(action);
    $('#package_type_Modal').modal('show');
}

function savePackageType()
{
    if($('#vFieldName_<?php echo $default_lang; ?>').val() == "") {
        $('#vFieldName_<?php echo $default_lang; ?>_error').show();
        $('#vFieldName_<?php echo $default_lang; ?>').focus();
        clearInterval(myVar);
        myVar = setTimeout(function() {
            $('#vFieldName_<?php echo $default_lang; ?>_error').hide();
        }, 5000);
        return false;
    }

    $('#vFieldName_Default').val($('#vFieldName_<?php echo $default_lang; ?>').val());
    $('#package_type_Modal').modal('hide');
}
</script>