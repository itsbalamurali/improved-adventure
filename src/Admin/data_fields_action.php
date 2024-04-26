<?php
include_once '../common.php';
$eMasterType = isset($_REQUEST['eType']) ? geteTypeForBSR($_REQUEST['eType']) : 'RentItem';

if (!$userObj->hasPermission('edit-'.strtolower($eMasterType).'-fields')) {
    $userObj->redirect();
}
$iMasterServiceCategoryId = get_value($master_service_category_tbl, 'iMasterServiceCategoryId', 'eType', $eMasterType, '', 'true');

$script = $eMasterType.'Fields';

$id = $_REQUEST['id'] ?? '';
$success = $_REQUEST['success'] ?? 0;
$action = ('' !== $id) ? 'Edit' : 'Add';

$backlink = $_POST['backlink'] ?? '';
$previousLink = $_POST['backlink'] ?? '';

$tbl_name = 'rentitem_fields';

$select_order = $obj->MySQLSelect('SELECT count(iOrder) AS iOrder FROM '.$tbl_name);
$iOrder = $select_order[0]['iOrder'] ?? 0;
$iDisplayOrder_max = $iOrder + 1; // Maximum order number

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
$eAllowFloat = $_POST['eAllowFloat'] ?? 'No';
$eRequired = $_POST['eRequired'] ?? 'Yes';
$eEditable = $_POST['eEditable'] ?? 'Yes';
$iRentItemId = $_POST['iRentItemId'] ?? '';
$eTitle = $_POST['eTitle'] ?? 'No';
$eDescription = $_POST['eDescription'] ?? 'No';
$eListing = $_POST['eListing'] ?? 'No';

$AddonOptions = $_POST['AddonOptions'] ?? '';

$optTypeaddon = $_POST['optTypeaddon'] ?? '';

$addonId = $_POST['addonId'] ?? '';

$addons_lang_all = $_POST['addons_lang_all'] ?? '';

function check_diff($arr1, $arr2)
{
    $check = (is_array($arr1) && count($arr1) > 0) ? true : false;

    $result = ($check) ? ((is_array($arr2) && count($arr2) > 0) ? $arr2 : []) : [];

    if ($check) {
        foreach ($arr1 as $key => $value) {
            if (isset($result[$key])) {
                $result[$key] = array_diff($value, $result[$key]);
            } else {
                $result[$key] = $value;
            }
        }
    }

    return $result;
}

if (isset($_POST['submit'])) {
    // echo '<prE>'; print_R($_REQUEST); echo '</pre>';die;
    if (SITE_TYPE === 'Demo') {
        header('Location:data_fields_action.php?id='.$id.'&success=2');

        exit;
    }

    if (count($db_master) > 0) {
        $str = '';
        for ($i = 0; $i < count($db_master); ++$i) {
            $vNameval1 = 'vFieldName_'.$db_master[$i]['vCode'];

            ${$vNameval1} = $_REQUEST[$vNameval1];

            $str .= ' '.$vNameval1." = '".${$vNameval1}."',";
        }
    }

    for ($i = 0; $i < count($db_master); ++$i) {
        $str = '';

        if (isset($_POST['tFieldName_'.$db_master[$i]['vCode']])) {
            $str = $_POST['tFieldName_'.$db_master[$i]['vCode']];
        }

        $strArr['tFieldName_'.$db_master[$i]['vCode']] = $str;
    }

    $jsonFieldName = getJsonFromAnArr($strArr);

    for ($i = 0; $i < count($db_master); ++$i) {
        $Descstr = '';

        if (isset($_POST['tDesc_'.$db_master[$i]['vCode']])) {
            $Descstr = $_POST['tDesc_'.$db_master[$i]['vCode']];
        }

        $DescstrArr['tDesc_'.$db_master[$i]['vCode']] = $Descstr;
    }

    $jsonFieldDesc = getJsonFromAnArr($DescstrArr);

    $query_p['vFieldName'] = $vName;

    $query_p['tFieldName'] = $jsonFieldName;

    $query_p['tDesc'] = $jsonFieldDesc;

    $query_p['iOrder'] = $iOrder;

    $query_p['iRentItemId'] = $iRentItemId;

    $query_p['eInputType'] = $eInputType;

    // $query_p['tDesc'] = $tDesc;

    $query_p['eAllowFloat'] = $eAllowFloat;

    $query_p['eRequired'] = $eRequired;

    $query_p['eEditable'] = $eEditable;

    $query_p['eStatus'] = $eStatus;

    $query_p['eTitle'] = $eTitle;

    $query_p['eDescription'] = $eDescription;

    $query_p['eListing'] = $eListing;

    if ('' !== $id) {
        $where = "  `iRentFieldId` = '".$id."'";

        $obj->MySQLQueryPerform($tbl_name, $query_p, 'update', $where);

        $iRentFieldIdNew = $id;
    } else {
        $iRentFieldIdNew = $obj->MySQLQueryPerform($tbl_name, $query_p, 'insert');
    }

    foreach ($AddonOptions as $key => $value) {
        if ('' !== trim($value)) {
            $addon_array[$key]['iOptionId'] = $addonId[$key];

            $addon_array[$key]['vFieldName'] = $value;

            $addon_array[$key]['iRentFieldId'] = $iRentFieldIdNew;

            // $addon_array[$key]['tFieldName'] = trim(addslashes(stripslashes($addons_lang_all[$key])), '\"');

            $addon_array[$key]['tFieldName'] = $addons_lang_all[$key];

            $addon_array[$key]['eStatus'] = 'Active';
        }
    }

    if (!empty($iRentFieldIdNew)) {
        $q = "SELECT * FROM rent_item_fields_option WHERE iRentFieldId ='".$iRentFieldIdNew."'";

        $addonOptionOldData = $obj->MySQLSelect($q);
        if (count($addonOptionOldData) > 0) {
            $addonOptionDiffres = check_diff($addonOptionOldData, $addon_array);

            foreach ($addonOptionDiffres as $j => $AddonOptionsVal) {
                if (!empty($AddonOptionsVal['iOptionId'])) {
                    $newoptioidsAddonArr[$j]['iOptionId'] = $AddonOptionsVal['iOptionId'];

                    $newoptioidsAddonArr[$j]['iRentFieldId'] = $iRentFieldIdNew;
                }
            }

            if (count($newoptioidsAddonArr) > 0) {
                foreach ($newoptioidsAddonArr as $ky => $addonoptionidArr) {
                    $q = 'UPDATE ';

                    $where = " WHERE `iOptionId` = '".$addonoptionidArr['iOptionId']."' AND `iRentFieldId` = '".$addonoptionidArr['iRentFieldId']."'";

                    $addonupdatequery = $q." `rent_item_fields_option` SET `eStatus` = 'Inactive'".$where;

                    $obj->sql_query($addonupdatequery);
                }
            }

            if (count($addon_array) > 0) {
                foreach ($addon_array as $key => $value) {
                    $Data_update_option = [];

                    if ('' === $value['iOptionId']) {
                        $Data_update_option['iRentFieldId'] = $iRentFieldIdNew;

                        $Data_update_option['vFieldName'] = $value['vFieldName'];

                        $Data_update_option['eStatus'] = $value['eStatus'];

                        $Data_update_option['tFieldName'] = trim(addslashes(stripslashes($value['tFieldName'])), '\"');

                        $id22 = $obj->MySQLQueryPerform('rent_item_fields_option', $Data_update_option, 'insert');
                    } else {
                        $where = " `iOptionId` = '".$value['iOptionId']."'";

                        $Data_update_option['iRentFieldId'] = $iRentFieldIdNew;

                        $Data_update_option['vFieldName'] = $value['vFieldName'];

                        $Data_update_option['eStatus'] = $value['eStatus'];

                        $Data_update_option['tFieldName'] = trim(addslashes(stripslashes($value['tFieldName'])), '\"');

                        $id11 = $obj->MySQLQueryPerform('rent_item_fields_option', $Data_update_option, 'update', $where);
                    }
                }
            }
        } else {
            if (count($addon_array) > 0) {
                foreach ($addon_array as $key => $value) {
                    $Data_update_option = [];

                    $Data_update_option['iRentFieldId'] = $iRentFieldIdNew;

                    $Data_update_option['vFieldName'] = $value['vFieldName'];

                    $Data_update_option['eStatus'] = $value['eStatus'];

                    $Data_update_option['tFieldName'] = trim(addslashes(stripslashes($value['tFieldName'])), '\"');

                    $id11 = $obj->MySQLQueryPerform('rent_item_fields_option', $Data_update_option, 'insert');
                }
            }
        }
    }

    if ('' !== $id) {
        $_SESSION['success'] = '1';

        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
    } else {
        $_SESSION['success'] = '1';

        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INSERT_MSG'];
    }

    header('location:data_fields.php?eType='.$_REQUEST['eType']);
}

$userEditDataArr = [];
// for Edit
if ('Edit' === $action) {
    $sql = 'SELECT * FROM '.$tbl_name." WHERE iRentFieldId = '".$id."'";
    $db_data = $obj->MySQLSelect($sql);

    $vCategoryName = json_decode($db_data[0]['tFieldName'], true);

    foreach ($vCategoryName as $key => $value) {
        $userEditDataArr[$key] = $value;
    }

    $vDesc = json_decode($db_data[0]['tDesc'], true);

    foreach ($vDesc as $key => $value) {
        $userEditDataArrDesc[$key] = $value;
    }

    $vName = $db_data[0]['vFieldName'];
    $iRentItemId = $db_data[0]['iRentItemId'];
    $eStatus = $db_data[0]['eStatus'];
    $iOrder_db = $db_data[0]['iOrder'];
    // $tDesc = $db_data[0]['tDesc'];
    $eAllowFloat = $db_data[0]['eAllowFloat'];
    $eRequired = $db_data[0]['eRequired'];
    $eEditable = $db_data[0]['eEditable'];
    $eInputType = $db_data[0]['eInputType'];

    $eTitle = $db_data[0]['eTitle'];
    $eDescription = $db_data[0]['eDescription'];

    $eListing = $db_data[0]['eListing'];

    $sql2 = "SELECT * FROM rent_item_fields_option WHERE iRentFieldId = '".$id."' AND eStatus = 'Active' ORDER BY iOptionId ASC";
    $db_addonsdata = $obj->MySQLSelect($sql2);
}

$EN_available = $LANG_OBJ->checkLanguageExist();
$db_master = $LANG_OBJ->getLangDataDefaultFirst($db_master);

$ordersql = ' ORDER BY iMasterServiceCategoryId,iDisplayOrder';
$rSql = "AND iMasterServiceCategoryId = '".$iMasterServiceCategoryId."' AND ( estatus = 'Active' || estatus = 'Inactive' )";
$rentitem = $RENTITEM_OBJ->getRentItemMaster('admin', $rSql, 0, 0, $default_lang, $ordersql);

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD-->
<head>
	<meta charset="UTF-8" />
	<title>Admin | Fields <?php echo $action; ?></title>
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
						<h2><?php echo $action; ?> Field</h2>
						<a href="data_fields.php?eType=<?php echo $_REQUEST['eType']; ?>" class="back_link">
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
							<input type="hidden" name="backlink" id="backlink" value="data_fields.php?eType=<?php echo $_REQUEST['eType']; ?>"/>
							<div class="row">
								<div class="col-lg-12">
									<label>Field Label<span class="red"> *</span></label>
								</div>
								<div class="col-lg-6">
									<input type="text" class="form-control" name="vFieldName"  id="vFieldName" value="<?php echo $vName; ?>" placeholder="Field Label" required>
								</div>
							</div>
							<?php if (count($db_master) > 1) { ?>
								<div class="row">
	                                <div class="col-lg-12">
	                                    <label>Field Name</label>
	                                </div>
	                                <div class="col-lg-6">
	                                    <input type="text" class="form-control <?php echo ('' === $id) ? 'readonly-custom' : ''; ?>" id="tFieldName_Default" value="<?php echo $userEditDataArr['tFieldName_'.$default_lang]; ?>" data-originalvalue="<?php echo $userEditDataArr['tFieldName_'.$default_lang]; ?>" readonly="readonly" <?php if ('' === $id) { ?> onclick="editPackageType('Add')" <?php } ?>>
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
	                                                <span id="modal_action"></span>Field Name
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

                                                        $required = ('Yes' === $eDefault) ? 'required' : '';
                                                        $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                                        ?>
	                                                    <div class="row">
	                                                        <div class="col-lg-12">
	                                                            <label>Field Name(<?php echo $vLTitle; ?>) <?php echo $required_msg; ?></label>

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
	                                                <button type="button" class="save" style="margin-left: 0 !important" onclick="savePackageType()"><?php echo $langage_lbl['LBL_Save']; ?></button>
	                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'tFieldName_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
	                                            </div>
	                                        </div>

	                                        <div style="clear:both;"></div>
	                                    </div>
	                                </div>

	                            </div>
                            <?php } else { ?>
	                            <div class="row">
	                                <div class="col-lg-12">
	                                    <label>Field Name</label>
	                                </div>
	                                <div class="col-lg-6">
	                                    <input type="text" class="form-control" id="tFieldName_<?php echo $default_lang; ?>" name="tFieldName_<?php echo $default_lang; ?>" value="<?php echo $userEditDataArr['tFieldName_'.$default_lang]; ?>" >
	                                </div>
	                            </div>
                            <?php } ?>

                            <div class="row">

                                <div class="col-lg-12">

                                    <label>Parent category</label>

                                </div>

                                <div class="col-lg-6">

                                    <select name="iRentItemId" class="form-control" <?php if ('Edit' === $action) { ?> readonly style="pointer-events: none;" <?php }?>>

                                        <option value="">Select Category</option>

                                        <?php
                                        foreach ($rentitem as $rentkey => $rentitemval) { ?>
										  	<option value="<?php echo $rentitemval['iRentItemId']; ?>"  <?php echo $rentitemval['iRentItemId'] === $iRentItemId ? 'selected' : ''; ?> ><?php echo $rentitemval['vTitle']; ?></option>
										<?php } ?>

                                    </select>

                                    <input type="hidden" name="oldDisplayOrder" id="oldDisplayOrder" value="<?php echo $iDisplayOrder; ?>">

                                </div>

                            </div>

                            <?php if (count($db_master) > 1) { ?>
								<div class="row">
	                                <div class="col-lg-12">
	                                    <label>Place Holder</label>
	                                </div>
	                                <div class="col-lg-6">
	                                    <input type="text" class="form-control <?php echo ('' === $id) ? 'readonly-custom' : ''; ?>" id="tDesc_Default" value="<?php echo $userEditDataArrDesc['tDesc_'.$default_lang]; ?>" data-originalvalue="<?php echo $userEditDataArrDesc['tDesc_'.$default_lang]; ?>" readonly="readonly" <?php if ('' === $id) { ?> onclick="editDescription('Add')" <?php } ?>>
	                                </div>
	                                <?php if ('' !== $id) { ?>
	                                <div class="col-lg-2">
	                                    <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editDescription('Edit')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
	                                </div>
	                                <?php } ?>
	                            </div>

	                            <div  class="modal fade" id="package_desc_Modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
	                                <div class="modal-dialog" >
	                                    <div class="modal-content nimot-class">
	                                        <div class="modal-header">
	                                            <h4>
	                                                <span id="modal_action1"></span> Place Holder
	                                                <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'tDesc_')">x</button>
	                                            </h4>
	                                        </div>

	                                        <div class="modal-body">
	                                            <?php

							        for ($i = 0; $i < $count_all; ++$i) {
							            $vCode = $db_master[$i]['vCode'];
							            $vLTitle = $db_master[$i]['vTitle'];
							            $eDefault = $db_master[$i]['eDefault'];

							            $vNameval = 'tDesc_'.$vCode;
							            ${$vNameval} = $userEditDataArrDesc[$vNameval];
							            ${$Desc} = 'Place Holder '.$vCode;

							            $required = ('Yes' === $eDefault) ? 'required' : '';
							            $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
							            ?>
	                                                    <div class="row">
	                                                        <div class="col-lg-12">
	                                                            <label>Place Holder Name(<?php echo $vLTitle; ?>) <?php echo $required_msg; ?></label>

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
	                                                                    <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('tDesc_', 'EN');" style="margin-top: 10px">Convert To All Language</button>
	                                                                </div>
	                                                            <?php }
							                                } else {
							                                    if ($vCode === $default_lang) { ?>
	                                                                <div class="col-lg-12">
	                                                                    <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('tDesc_', '<?php echo $default_lang; ?>');" style="margin-top: 10px">Convert To All Language</button>
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
	                                                <button type="button" class="save" style="margin-left: 0 !important" onclick="saveDescription()"><?php echo $langage_lbl['LBL_Save']; ?></button>
	                                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'tDesc_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
	                                            </div>
	                                        </div>

	                                        <div style="clear:both;"></div>
	                                    </div>
	                                </div>

	                            </div>
                            <?php } else { ?>
								<div class="row">
									<div class="col-lg-12">
										<label>Place Holder<span class="red"> *</span></label>
									</div>
									<div class="col-lg-6">
										<input type="text" class="form-control" name="tDesc_<?php echo $default_lang; ?>"  id="tDesc_<?php echo $default_lang; ?>" value="<?php echo $userEditDataArrDesc['tDesc_'.$default_lang]; ?>" placeholder="Place Holder" >
									</div>
								</div>
							<?php } ?>

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
										<!-- <option value="">Select</option> -->
										<option value="Text" <?php echo ('Text' === $eInputType) ? 'selected' : ''; ?>>Text</option>
										<option value="Textarea" <?php echo ('Textarea' === $eInputType) ? 'selected' : ''; ?>>Textarea</option>
										<option value="Number" <?php echo ('Number' === $eInputType) ? 'selected' : ''; ?>>Number</option>
										<option value="Select" <?php echo ('Select' === $eInputType) ? 'selected' : ''; ?>>Select</option>
									</select>
								</div>
							</div>

							<div class="row" id="SelectField" style="display: none;">
                                <div class="col-lg-6">
	                               <div class="panel panel-default servicecatresponsive">

	                                    <div class="panel-heading">

	                                        <div class="row" style="padding-bottom:0;">

	                                            <div class="col-lg-6">

	                                                <h5><b>Select Fields Options  <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title='Add Select Field Options'></i></b>

	                                                </h5>

	                                            </div>
	                                             <?php if ('Yes' !== $eListing) { ?>
	                                            <div class="col-lg-6 text-right">

	                                                <button class="btn btn-success" type="button" onclick="addon_fields();">

	                                                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>

	                                                </button>

	                                            </div>
	                                        <?php } ?>
	                                        </div>

	                                    </div>

	                                    <div class="panel-body" style="padding: 25px; overflow-y: auto;height: 340px;">

	                                        <div id="addon_fields">

	                                            <?php if (count($db_addonsdata) > 0) {
	                                                $a = 0;

	                                                foreach ($db_addonsdata as $k => $addon) {
	                                                    ++$a;

	                                                    ?>

	                                                    <div class="form-group removeclassaddon<?php echo $a; ?>">

	                                                        <div class="col-sm-4">

	                                                            <div class="form-group">

	                                                                <input type="text" class="form-control" id="AddonOptions" name="AddonOptions[]" value="<?php echo $addon['vFieldName']; ?>" placeholder="Option Name" required readonly>

	                                                            </div>

	                                                        </div>

	                                                        <div class="col-sm-4">

	                                                            <div class="form-group">
	                                                                <input type="hidden" name="optTypeaddon[]" value="Addon"/>

	                                                                <input type="hidden" name="addonId[]" value="<?php echo $addon['iOptionId']; ?>"/>

	                                                                <textarea name="addons_lang_all[]" style="display: none;"><?php echo trim($addon['tFieldName'], '"'); ?></textarea>
	                                                            </div>

	                                                        </div>

	                                                        <div class="col-sm-2">

	                                                            <div class="form-group">

	                                                                <div class="input-group">

	                                                                    <div class="input-group-btn">

	                                                                                <span>

	                                                                                    <button class="btn btn-info" type="button" onclick="edit_addon_fields('<?php echo $a; ?>');" data-toggle="tooltip" data-original-title="Edit" style="margin-right: 20px"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>

	                                                                                </span>
	                                                                        <?php if ('Yes' !== $eListing) { ?>

	                                                                        <span>

                                                                            	<button class="btn btn-danger" type="button" onclick="remove_addon_fields('<?php echo $a; ?>');" data-toggle="tooltip" data-original-title="Remove" style="margin-right: 20px"> <span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button>

	                                                                        </span>

	                                                                        <?php } ?>

	                                                                    </div>

	                                                                </div>

	                                                            </div>

	                                                        </div>

	                                                        <div class="clear"></div>

	                                                    </div>

	                                                    <?php

	                                                }
	                                            }

?>

	                                        </div>
	                                    </div>
	                                </div>
                                </div>
                            </div>

							<div class="row" id="NumberField" style="display: none;">
								<div class="col-lg-12">
									<label>AllowFloat<span class="red"> *</span></label>
								</div>
								<div class="col-lg-6">
									<select class="form-control" name = 'eAllowFloat' id="eAllowFloat" >

										<option value="No" <?php echo ('No' === $eAllowFloat) ? 'selected' : ''; ?>>No</option>
										<option value="Yes" <?php if ('Yes' === $eAllowFloat) {
										    echo 'selected';
										}?>>Yes</option>

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

							<!-- <div class="row">
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
							</div> -->

							<div class="row" id="eTitlemain" style="display: none;">
								<div class="col-lg-12">
									<label>Is this Item Name Field?<span class="red"> *</span></label>
								</div>
								<div class="col-lg-6">
									<select class="form-control" name = 'eTitle' id="eTitle" >
										<option value="">Select</option>
										<option value="Yes" <?php echo ('Yes' === $eTitle) ? 'selected' : ''; ?>>Yes</option>
										<option value="No" <?php echo ('No' === $eTitle) ? 'selected' : ''; ?>>No</option>

									</select>
								</div>
							</div>

							<div class="row" id="eDescriptionmain" style="display: none;">
								<div class="col-lg-12">
									<label>Is this Item Descirption Field?<span class="red"> *</span></label>
								</div>
								<div class="col-lg-6">
									<select class="form-control" name = 'eDescription' id="eDescription" >
										<option value="">Select</option>
										<option value="Yes" <?php echo ('Yes' === $eDescription) ? 'selected' : ''; ?>>Yes</option>
										<option value="No" <?php echo ('No' === $eDescription) ? 'selected' : ''; ?>>No</option>

									</select>
								</div>
							</div>

							<div class="row" id="eListingmain" style="display: none;">
								<div class="col-lg-12">
									<label>Is this Listing Type?<span class="red"> *</span></label>
								</div>
								<div class="col-lg-6">
									<select class="form-control" name = 'eListing' id="eListing" >
										<option value="">Select</option>
										<option value="Yes" <?php echo ('Yes' === $eListing) ? 'selected' : ''; ?>>Yes</option>
										<option value="No" <?php echo ('No' === $eListing) ? 'selected' : ''; ?>>No</option>

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
									<input type="submit" class=" btn btn-default" name="submit" id="submit" value="<?php echo $action; ?> Field">
									<input type="reset" value="Reset" class="btn btn-default">
									<!-- <a href="javascript:void(0);" onclick="reset_form('_make_form');" class="btn btn-default">Reset</a> -->
                                    <a href="data_fields.php?eType=<?php echo $_REQUEST['eType']; ?>" class="btn btn-default back_link">Cancel</a>
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

	<div class="modal fade" id="add_options_toppings" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">

	    <div class="modal-dialog">

	        <div class="modal-content nimot-class">

	            <div class="modal-header">

	                <h4>

	                    <span id="option_addon_title"></span>

	                    <button type="button" class="close" data-dismiss="modal">x</button>

	                </h4>

	            </div>

	            <div class="modal-body">

	                <input type="hidden" name="option_addon_type" id="option_addon_type">

	                <input type="hidden" name="option_addon_action" id="option_addon_action">

	                <input type="hidden" name="option_addon_id" id="option_addon_id">

	                <input type="hidden" id="iOptionsCategoryId">

	                <?php

                    if (count($db_master) > 1) {
                        for ($i = 0; $i < $count_all; ++$i) {
                            $vCode = $db_master[$i]['vCode'];

                            $vTitle = $db_master[$i]['vTitle'];

                            $eDefault = $db_master[$i]['eDefault'];

                            $vValue = 'tOptionNameLang_'.$vCode;

                            $vValueName = 'tOptionName_'.$vCode;

                            $required = ('Yes' === $eDefault) ? 'required' : '';

                            $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';

                            ?>

	                        <div class="form-group row">

	                            <div class="col-md-12">

	                                <label><span id="<?php echo $vValueName; ?>">Option Name</span> (<?php echo $vTitle; ?>)</label>

	                                <input type="text" class="form-control" name="<?php echo $vValue; ?>" id="<?php echo $vValue; ?>" placeholder="<?php echo $vTitle; ?> Value" <?php echo $required; ?>>

	                                <div class="text-danger" id="<?php echo $vValue.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>

	                            </div>
	                        </div>

	                        <?php

                            if (count($db_master) > 1) {
                                if ($EN_available) {
                                    if ('EN' === $vCode) { ?>

	                                    <div class="form-group row">

	                                        <div class="col-md-12">

	                                            <button type="button" class="btn btn-primary" onclick="getAllLanguageCode('tOptionNameLang_', 'EN');">Convert To All Language</button>

	                                        </div>

	                                    </div>

	                                    <?php

                                    }
                                } else {
                                    if ($vCode === $defaultLang) { ?>

	                                    <div class="form-group row">

	                                        <div class="col-md-12">

	                                            <button type="button" class="btn btn-primary" onclick="getAllLanguageCode('tOptionNameLang_', '<?php echo $default_lang; ?>');">Convert To All Language</button>

	                                        </div>

	                                    </div>
	                                    <?php

                                    }
                                }
                            }

                            ?><?php

                        }
                    } else { ?>

	                    <div class="form-group row">

	                        <div class="col-md-12">

	                            <label>Option Name (<?php echo $db_master[0]['vTitle']; ?>)</label>

	                            <input type="text" class="form-control" name="tOptionNameLang_<?php echo $default_lang; ?>" id="tOptionNameLang_<?php echo $default_lang; ?>" placeholder="<?php echo $db_master[0]['vTitle']; ?> Value">

	                            <div class="text-danger" id="<?php echo $vValue.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>

	                        </div>

	                    </div>

	                <?php } ?>

	            </div>

	            <div class="modal-footer" style="margin-top: 0">

	                <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">

	                    <strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>

	                <div class="nimot-class-but" style="margin-bottom: 0">

	                    <button type="button" class="save" id="add_options_toppings_btn" style="margin-left: 0 !important"><?php echo $langage_lbl['LBL_ADD']; ?></button>

	                    <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Cancel</button>

	                </div>

	            </div>

	            <div style="clear:both;"></div>

	        </div>

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
		referrer = "data_fields.php?eType=<?php echo $_REQUEST['eType']; ?>";
	}else { //alert('hi');
		$("#backlink").val(referrer);
		// alert($("#backlink").val(referrer));
	}
	$(".back_link").attr('href',referrer);
	//alert($(".back_link").attr('href',referrer));
});

var i=1;

  $(document).on('click', '.btn_remove', function(){
       var button_id = $(this).attr("id");
       $('#row'+button_id+'').remove();
  });

function editPackageType(action)
{
    $('#modal_action').html(action);
    $('#package_type_Modal').modal('show');
}

function editDescription(action)
{
    $('#modal_action1').html(action);
    $('#package_desc_Modal').modal('show');
}

<?php if (count($db_addonsdata) > 0) { ?>

var addonid = '<?php echo count($db_addonsdata); ?>';

<?php } else { ?>

var addonid = 0;

<?php } ?>

function addon_fields() {

    $('#option_addon_title').html("Add Option");

    $('#option_addon_type').val("addons");

    $('#option_addon_action').val("add");

    $('#add_options_toppings_btn').html("<?php echo $langage_lbl_admin['LBL_ADD']; ?>");


    $('#item_option_topping_price').prop('readonly', false);

    $('[name^=tOptionNameLang_], #item_option_topping_price').val("");

    $('#add_options_toppings .modal-body').animate({

        scrollTop: 0

    }, 'fast');

    $('#add_options_toppings').modal('show');

}

function saveDescription()
{
    if($('#tDesc_<?php echo $default_lang; ?>').val() == "") {
        $('#tDesc_<?php echo $default_lang; ?>_error').show();
        $('#tDesc_<?php echo $default_lang; ?>').focus();
        clearInterval(myVar);
        myVar = setTimeout(function() {
            $('#tDesc_<?php echo $default_lang; ?>_error').hide();
        }, 5000);
        return false;
    }

    $('#tDesc_Default').val($('#tDesc_<?php echo $default_lang; ?>').val());
    $('#package_desc_Modal').modal('hide');
}

function savePackageType()
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
    $('#package_type_Modal').modal('hide');
}

var eTitle = $('#eTitle').find(":selected").val();
if(eTitle == "Yes"){
	$("#eDescription").val('No');
  	$("#eDescriptionmain").hide();
}

var eDescription = $('#eDescription').find(":selected").val();
if(eDescription == "Yes"){
	$("#eTitle").val('No');
  	$("#eTitlemain").hide();
}

var eListing = $('#eListing').find(":selected").val();
if(eListing == "Yes"){
	$("#eTitle").val('No');
	$("#eDescription").val('No');
  	$("#eTitlemain").hide();
  	$("#eDescriptionmain").hide();
}

$('#eTitle').on('change', function() {
  $("#eDescriptionmain").show();
  $("#eListingmain").show();
  if(this.value == 'Yes'){
  	$("#eDescription").val('No');
  	$("#eDescriptionmain").hide();
  	$("#eListing").val('No');
  	$("#eListingmain").hide();
  }
});

$('#eDescription').on('change', function() {
  $("#eTitlemain").show();
   $("#eListingmain").show();
  if(this.value == 'Yes'){
  	$("#eTitle").val('No');
  	$("#eTitlemain").hide();
  	$("#eListing").val('No');
  	$("#eListingmain").hide();
  }
});

$('#eListing').on('change', function() {
   $("#eTitlemain").show();
   $("#eDescriptionmain").show();
  if(this.value == 'Yes'){
  	$("#eTitle").val('No');
	$("#eDescription").val('No');
  	$("#eTitlemain").hide();
  	$("#eDescriptionmain").hide();
  }
});


$('#eInputType').on('change', function() {
  if(this.value == 'Number'){
  	$("#NumberField").show();
  } else if(this.value == 'Select'){
  	$("#SelectField").show();
  }else {
  	$("#NumberField").hide();
  	$("#SelectField").hide();
  }
});

var NumberField = $('#eInputType').find(":selected").val();
if(NumberField == "Number"){
	$("#NumberField").show();
} else {
	$("#NumberField").hide();
}
varSelectField = $('#eInputType').find(":selected").val();
if(varSelectField == "Select"){
	$("#SelectField").show();
} else {
	$("#SelectField").hide();
}

$('#add_options_toppings_btn').click(function () {

    <?php if ($EN_available) { ?>

	    if ($('#tOptionNameLang_EN').val().trim() == "") {

	        $('#tOptionNameLang_EN_error').show();

	        $('#tOptionNameLang_EN').focus();

	        $('#tOptionNameLang_EN').val("");



	        clearInterval(myVar);

	        myVar = setTimeout(function () {

	            $('#tOptionNameLang_EN_error').hide();

	        }, 5000);

	        return false;

	    }

    <?php } else { ?>

	    if ($('#tOptionNameLang_<?php echo $default_lang; ?>').val().trim() == "") {

	        $('#tOptionNameLang_<?php echo $default_lang; ?>_error').show();

	        $('#tOptionNameLang_<?php echo $default_lang; ?>').focus();

	        $('#tOptionNameLang_<?php echo $default_lang; ?>').val("");



	        clearInterval(myVar);

	        myVar = setTimeout(function () {

	            $('#tOptionNameLang_<?php echo $default_lang; ?>_error').hide();

	        }, 5000);

	        return false;

	    }

    <?php } ?>


    jsonObj = {};

    $('[name^=tOptionNameLang_]').each(function () {

        jsonObj[$(this).attr('name')] = $(this).val();

    });

    console.log(jsonObj);

    if ($('#option_addon_action').val() == "add") {

        if ($('#option_addon_type').val() == "addons") {

            options_fields_add(jsonObj);

        }

    } else {

        var option_id = $('#option_addon_id').val();


        $('#addon_fields').find('.removeclassaddon' + option_id).find('[name="AddonOptions[]"]').val(jsonObj.tOptionNameLang_<?php echo $default_lang; ?>);

        $('#addon_fields').find('.removeclassaddon' + option_id).find('[name="addons_lang_all[]"]').text(JSON.stringify(jsonObj));

        $('#add_options_toppings').modal('hide');

    }

});


function options_fields_add(addon_toppings) {

    var item_addons = JSON.stringify(addon_toppings);

    var baseAddonValue = jsonObj.tOptionNameLang_<?php echo $default_lang; ?>;


    addonid++;

    var objTo = document.getElementById('addon_fields');

    var divtest = document.createElement("div");

    divtest.setAttribute("class", "form-group removeclassaddon" + addonid);

    divtest.innerHTML = '<div class="col-sm-4"><div class="form-group"> <input type="text" class="form-control" id="AddonOptions" name="AddonOptions[]" value="' + baseAddonValue + '" placeholder="Option Name" required readonly></div></div><div class="col-sm-4"><div class="form-group"> <input type="hidden" name="addonId[]" value="" /><input type="hidden" name="optTypeaddon[]" value="Addon" /><textarea name="addons_lang_all[]" style="display: none">' + item_addons + '</textarea></div></div><div class="col-sm-2"><div class="form-group"><div class="input-group"><div class="input-group-btn"> <span><button class="btn btn-info" type="button" onclick="edit_addon_fields(' + addonid + ');" data-toggle="tooltip" data-original-title="Edit" style="margin-right: 20px"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></span><span><button class="btn btn-danger" type="button" onclick="remove_addon_fields(' + addonid + ');" style="margin-right: 20px;"> <span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button></span></div></div></div></div><div class="clear"></div>';

    objTo.appendChild(divtest);

    $('#add_options_toppings').modal('hide');

}

function edit_addon_fields(eid) {

    $('#option_addon_title').html("Edit Options");

    $('#option_addon_type').val("addons");

    $('#option_addon_id').val(eid);

    $('#option_addon_img_title').html("<?php echo $langage_lbl_admin['LBL_ADDON_TOPPING_IMG']; ?>");


    var addon_values = $('.removeclassaddon' + eid).find('[name="addons_lang_all[]"]').text();

    var addon_AddonOptions = $('.removeclassaddon' + eid).find('[name="AddonOptions[]"]').val();


    if (addon_values != "") {

        addon_values = JSON.parse(addon_values);

        $('[name^=tOptionNameLang_]').each(function () {

            var attr_name = $(this).attr('name');

            $(this).val(addon_values[attr_name]);

        });

    } else {

        <?php if ($EN_available) { ?>

        $('#tOptionNameLang_EN').val(addon_AddonOptions);

        <?php } else { ?>

        $('#tOptionNameLang_<?php echo $default_lang; ?>').val(addon_AddonOptions);

        <?php } ?>

    }

    $('#option_addon_action').val("edit");

    $('#add_options_toppings_btn').html("<?php echo $langage_lbl_admin['LBL_Save']; ?>");

    $('#add_options_toppings .modal-body').animate({

        scrollTop: 0

    }, 'fast');

    $('#add_options_toppings').modal('show');

}

    function remove_addon_fields(rid) {

    $('.removeclassaddon' + rid).remove();

}
</script>