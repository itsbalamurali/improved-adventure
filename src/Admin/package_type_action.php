<?php
include_once '../common.php';

$id = $_REQUEST['id'] ?? '';
$success = $_REQUEST['success'] ?? 0;
$action = ('' !== $id) ? 'Edit' : 'Add';

$backlink = $_POST['backlink'] ?? '';
$previousLink = $_POST['backlink'] ?? '';

$tbl_name = 'package_type';
$script = 'Package';

// echo '<prE>'; print_R($_REQUEST); echo '</pre>';
// set all variables with either post (when submit) either blank (when insert)
$vName = $_POST['vName'] ?? '';
$eStatus_check = $_POST['eStatus'] ?? 'off';
$eStatus = ('on' === $eStatus_check) ? 'Active' : 'Inactive';

$vTitle_store = [];
$sql = 'SELECT * FROM `language_master` ORDER BY `iDispOrder`';
$db_master = $obj->MySQLSelect($sql);
$count_all = count($db_master);
if ($count_all > 0) {
    for ($i = 0; $i < $count_all; ++$i) {
        $vValue = 'vName_'.$db_master[$i]['vCode'];
        $vTitle_store[] = $vValue;
        ${$vValue} = $_POST[$vValue] ?? '';
    }
}

if ('Delivery' === $APP_TYPE || 'Ride-Delivery' === $APP_TYPE || 'Ride-Delivery-UberX' === $APP_TYPE) {
    $sql = "select iDeliveryFieldId,vFieldName from delivery_fields where eStatus = 'Active' AND eInputType='Select'";
    $db_delivery_fields_data = $obj->MySQLSelect($sql);
}

if (isset($_POST['submit'])) {
    if (('Delivery' === $APP_TYPE || 'Ride-Delivery' === $APP_TYPE || 'Ride-Delivery-UberX' === $APP_TYPE) && !empty($db_delivery_fields_data)) {
        $iDeliveryFieldId = $_POST['iDeliveryFieldId'] ?? 0;
    } else {
        $iDeliveryFieldId = 0;
    }

    if ('Add' === $action && !$userObj->hasPermission('create-package-type-parcel-delivery')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to create package type.';
        header('Location:state.php');

        exit;
    }

    if ('Edit' === $action && !$userObj->hasPermission('edit-package-type-parcel-delivery')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to update package type.';
        header('Location:state.php');

        exit;
    }

    if (SITE_TYPE === 'Demo') {
        header('Location:package_type_action.php?id='.$id.'&success=2');

        exit;
    }
    for ($i = 0; $i < count($vTitle_store); ++$i) {
        $vValue = 'vName_'.$db_master[$i]['vCode'];

        $q = 'INSERT INTO ';
        $where = '';

        if ('' !== $id) {
            $q = 'UPDATE ';
            $where = " WHERE `iPackageTypeId` = '".$id."'";
        }

        $query = $q.' `'.$tbl_name."` SET
			`vName` = '".$_POST['vName_'.$default_lang]."',
			`eStatus` = '".$eStatus."',
            `iDeliveryFieldId` = '".$iDeliveryFieldId."',

			".$vValue." = '".$_POST[$vTitle_store[$i]]."'"
                .$where;

        $obj->sql_query($query);
        $id = ('' !== $id) ? $id : $obj->GetInsertId();
    }

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
    $sql = 'SELECT * FROM '.$tbl_name." WHERE iPackageTypeId = '".$id."'";
    $db_data = $obj->MySQLSelect($sql);

    $vLabel = $id;
    if (count($db_data) > 0) {
        for ($i = 0; $i < count($db_master); ++$i) {
            foreach ($db_data as $key => $value) {
                $vValue = 'vName_'.$db_master[$i]['vCode'];
                ${$vValue} = $value[$vValue];
                $vName = $value['vName'];
                $eStatus = $value['eStatus'];
                $iDeliveryFieldId = $value['iDeliveryFieldId'];

                $arrLang[$vValue] = ${$vValue};
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
        <title>Admin | Package <?php echo $action; ?></title>
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
                            <a href="package_type.php" class="back_link">
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
                            <?php } ?>
                            <form method="post" name="_package_type" id="_package_type" action="">
                                <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                                <input type="hidden" name="previousLink" id="previousLink" value="<?php echo $previousLink; ?>"/>
                                <input type="hidden" name="backlink" id="backlink" value="package_type.php"/>
                                <div class="col-lg-12" id="errorMessage"></div>
                                <!-- 								<div class="row">
                                                                                                        <div class="col-lg-12">
                                                                                                                <label>Package Type Label<span class="red"> *</span></label>
                                                                                                        </div>
                                                                                                        <div class="col-md-6 col-sm-6">
                                                                                                                <input type="text" class="form-control" name="vName"  id="vName" value="<?php echo $vName; ?>" placeholder="Package Label" required>
                                                                                                        </div>
                                                                                        </div> -->
                                <?php if (count($db_master) > 1) { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Package Type <span class="red"> *</span></label>
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                        <input type="text" class="form-control <?php echo ('' === $id) ? 'readonly-custom' : ''; ?>" id="vName_Default" name="vName_Default" value="<?php echo $arrLang['vName_'.$default_lang]; ?>" data-originalvalue="<?php echo $arrLang['vName_'.$default_lang]; ?>" readonly="readonly" <?php if ('' === $id) { ?> onclick="editPackageType('Add')" <?php } ?>>
                                    </div>
                                    <?php if ('' !== $id) { ?>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editPackageType('Edit')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                    </div>
                                    <?php } ?>
                                </div>

                                <div  class="modal fade" id="package_type_Modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg" >
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="modal_action"></span> Package Type
                                                    <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vName_')">x</button>
                                                </h4>
                                            </div>

                                            <div class="modal-body">
                                                <?php

                                                    for ($i = 0; $i < $count_all; ++$i) {
                                                        $vCode = $db_master[$i]['vCode'];
                                                        $vTitle = $db_master[$i]['vTitle'];
                                                        $eDefault = $db_master[$i]['eDefault'];
                                                        $vValue = 'vName_'.$vCode;

                                                        $required = ('Yes' === $eDefault) ? 'required' : '';
                                                        $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                                        ?>
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
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <label>Package Type (<?php echo $vTitle; ?>) <?php echo $required_msg; ?></label>

                                                            </div>
                                                            <div class="<?php echo $page_title_class; ?>">
                                                                <input type="text" class="form-control" name="<?php echo $vValue; ?>" id="<?php echo $vValue; ?>" value="<?php echo ${$vValue}; ?>" data-originalvalue="<?php echo ${$vValue}; ?>" placeholder="<?php echo $vTitle; ?> Value">
                                                                <div class="text-danger" id="<?php echo $vValue.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                            </div>
                                                            <?php
                                                            if (count($db_master) > 1) {
                                                                if ($EN_available) {
                                                                    if ('EN' === $vCode) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vName_', 'EN');" >Convert To All Language</button>
                                                                    </div>
                                                                <?php }
                                                                    } else {
                                                                        if ($vCode === $default_lang) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vName_', '<?php echo $default_lang; ?>');" >Convert To All Language</button>
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
                                                    <button type="button" class="save" style="margin-left: 0 !important" onclick="savePackageType()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                    <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vName_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                </div>
                                            </div>

                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>

                                </div>
                                <?php } else { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Package Type <span class="red"> *</span></label>
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                        <input type="text" class="form-control" id="vName_<?php echo $default_lang; ?>" name="vName_<?php echo $default_lang; ?>" value="<?php echo $arrLang['vName_'.$default_lang]; ?>" required>
                                    </div>
                                </div>
                                <?php } ?>

                                  <?php if (('Delivery' === $APP_TYPE || 'Ride-Delivery' === $APP_TYPE || 'Ride-Delivery-UberX' === $APP_TYPE) && !empty($db_delivery_fields_data)) {?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Delivery Field (Only for Multi Delivery)<span class="red"> *</span></label>
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                            <select class="form-control" name = 'iDeliveryFieldId' id="iDeliveryFieldId"  required>
                                            <option value="">Select Delivery Field</option>
                                            <?php for ($i = 0; $i < count($db_delivery_fields_data); ++$i) { ?>
                                                <option <?php if ('Edit' === $action && ($db_delivery_fields_data[$i]['iDeliveryFieldId'] === $iDeliveryFieldId)) {
                                                    echo 'selected';
                                                }?> value = "<?php echo $db_delivery_fields_data[$i]['iDeliveryFieldId']; ?>"><?php echo $db_delivery_fields_data[$i]['vFieldName']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            <?php }?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Status</label>
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                        <div class="make-switch" data-on="success" data-off="warning">
                                            <input type="checkbox" name="eStatus" <?php echo ('' !== $id && 'Inactive' === $eStatus) ? '' : 'checked'; ?>/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <?php if (('Edit' === $action && $userObj->hasPermission('edit-package-type-parcel-delivery')) || ('Add' === $action && $userObj->hasPermission('create-package-type-parcel-delivery'))) { ?>
                                            <input type="submit" class=" btn btn-default" name="submit" id="submit" value="<?php echo $action; ?> Package">
                                            <input type="reset" value="Reset" class="btn btn-default">
                                        <?php } ?>
                                        <!-- <a href="javascript:void(0);" onclick="reset_form('_make_form');" class="btn btn-default">Reset</a> -->
                                        <a href="package_type.php" class="btn btn-default back_link">Cancel</a>
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

        <div class="row loding-action" id="loaderIcon" style="display:none;">
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
                                            $(document).ready(function () {
                                                var referrer;
                                                if ($("#previousLink").val() == "") { //alert('pre1');
                                                    referrer = document.referrer;
                                                } else {
                                                    referrer = $("#previousLink").val();
                                                }

                                                if (referrer == "") {
                                                    referrer = "package_type.php";
                                                } else {
                                                    $("#backlink").val(referrer);
                                                }
                                                $(".back_link").attr('href', referrer);
                                            });

 <?php if (('Delivery' === $APP_TYPE || 'Ride-Delivery' === $APP_TYPE || 'Ride-Delivery-UberX' === $APP_TYPE) && !empty($db_delivery_fields_data)) {?>
    $('#_make_form').validate({
        rules: {
            iDeliveryFieldId: {
                required: true
            },
        }
    });
<?php }?>

function editPackageType(action)
{
    $('#modal_action').html(action);
    $('#package_type_Modal').modal('show');
}

function savePackageType()
{
    if($('#vName_<?php echo $default_lang; ?>').val() == "") {
        $('#vName_<?php echo $default_lang; ?>_error').show();
        $('#vName_<?php echo $default_lang; ?>').focus();
        clearInterval(langVar);
        langVar = setTimeout(function() {
            $('#vName_<?php echo $default_lang; ?>_error').hide();
        }, 5000);
        return false;
    }

    $('#vName_Default').val($('#vName_<?php echo $default_lang; ?>').val());
    $('#vName_Default').closest('.row').removeClass('has-error');
    $('#vName_Default-error').remove();
    $('#package_type_Modal').modal('hide');
}
</script>