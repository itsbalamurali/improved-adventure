<?php
include_once '../common.php';

$id = $_REQUEST['id'] ?? ''; // iUniqueId

$eMasterType = isset($_REQUEST['eType']) ? geteTypeForBSR($_REQUEST['eType']) : 'RentItem';

if (!$userObj->hasPermission('edit-payment-plan-'.strtolower($eMasterType))) {
    $userObj->redirect();
}

$iMasterServiceCategoryId = get_value($master_service_category_tbl, 'iMasterServiceCategoryId', 'eType', $eMasterType, '', 'true');

$script = $eMasterType.'PaymentPlan';

$default_lang = $LANG_OBJ->FetchSystemDefaultLang();

$success = $_REQUEST['success'] ?? '';

$action = ('' !== $id) ? 'Edit' : 'Add';

$tbl_name = 'rent_item_payment_plan';

$db_master = $obj->MySQLSelect('SELECT * FROM `language_master` ORDER BY `iDispOrder`');

$count_all = count($db_master);

$vCategoryName = $_POST['vCategoryName'] ?? '';

$eStatus_check = $_POST['eStatus'] ?? 'off';

$eStatus = ('on' === $eStatus_check) ? 'Active' : 'Inactive';

$eFreePlan = $_POST['eFreePlan'] ?? 'No';

$eFeaturedPlan = $_POST['eFeaturedPlan'] ?? 'No';

$iTotalDays = $_POST['iTotalDays'] ?? '';

$fAmount = $_POST['fAmount'] ?? '';

$iTotalPost = $_POST['iTotalPost'] ?? '';

$eAvailability = $_POST['eAvailability'] ?? '';

if (isset($_POST['submit'])) { // form submit
    if (SITE_TYPE === 'Demo') {
        $_SESSION['success'] = 2;

        header('Location:item_payment_plans.php?eType='.$_REQUEST['eType']);

        exit;
    }

    for ($i = 0; $i < count($db_master); ++$i) {
        $vCategoryName = '';

        if (isset($_POST['vPlanName_'.$db_master[$i]['vCode']])) {
            $vCategoryName = $_POST['vPlanName_'.$db_master[$i]['vCode']];
        }

        $vCategoryNameArr['vPlanName_'.$db_master[$i]['vCode']] = $vCategoryName;
    }

    $jsonCategoryName = getJsonFromAnArr($vCategoryNameArr);

    for ($i = 0; $i < count($db_master); ++$i) {
        $tDescription = '';

        if (isset($_POST['tDescription_'.$db_master[$i]['vCode']])) {
            $tDescription = $_POST['tDescription_'.$db_master[$i]['vCode']];
        }

        $tDescriptionArr['tDescription_'.$db_master[$i]['vCode']] = $tDescription;
    }

    $jsonDescription = getJsonFromAnArr($tDescriptionArr);

    $query_p['vPlanName'] = $jsonCategoryName;

    $query_p['tDescription'] = $jsonDescription;

    $query_p['eStatus'] = $eStatus;

    $query_p['eFreePlan'] = $eFreePlan;

    $query_p['eFeaturedPlan'] = $eFeaturedPlan;

    $query_p['iTotalDays'] = $iTotalDays;

    $query_p['fAmount'] = $fAmount;

    $query_p['iTotalPost'] = $iTotalPost;

    $query_p['eAvailability'] = $eAvailability;

    $query_p['iMasterServiceCategoryId'] = $iMasterServiceCategoryId;

    if ('' !== $id) {
        $where = "  `iPaymentPlanId` = '".$id."'";

        $obj->MySQLQueryPerform($tbl_name, $query_p, 'update', $where);
    } else {
        $obj->MySQLQueryPerform($tbl_name, $query_p, 'insert');
    }

    if ('' !== $id) {
        $_SESSION['success'] = '1';

        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
    } else {
        $_SESSION['success'] = '1';

        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INSERT_MSG'];
    }

    header('Location:item_payment_plans.php?eType='.$_REQUEST['eType']);

    exit;
}

// for Edit

$userEditDataArr = [];

$vDescriptionArr = [];

if ('Edit' === $action) {
    $rentitem = $RENTITEM_OBJ->getRentItemPlan('admin', $id);
    // echo"<pre>";print_r($rentitem);die;

    $vCategoryName = json_decode($rentitem['vPlanName'], true);

    foreach ($vCategoryName as $key => $value) {
        $userEditDataArr[$key] = $value;
    }

    $vDescription = json_decode($rentitem['tDescription'], true);

    foreach ($vDescription as $key => $value) {
        $vDescriptionArr[$key] = $value;
    }

    $iPaymentPlanId = $rentitem['iPaymentPlanId'];

    $iTotalDays = $rentitem['iTotalDays'];

    $fAmount = $rentitem['fAmount'];

    $eFreePlan = $rentitem['eFreePlan'];

    $eFeaturedPlan = $rentitem['eFeaturedPlan'];

    $eStatus = $rentitem['eStatus'];

    $iTotalPost = $rentitem['iTotalPost'];

    $eAvailability = $rentitem['eAvailability'];

    $iMasterServiceCategoryId = $rentitem['iMasterServiceCategoryId'];
}

$EN_available = $LANG_OBJ->checkLanguageExist();

$db_master = $LANG_OBJ->getLangDataDefaultFirst($db_master);

?>

<!DOCTYPE html>

<!--[if !IE]><!-->

<html lang="en">

<!--<![endif]-->

<!-- BEGIN HEAD-->



<head>

    <meta charset="UTF-8" />

    <title>Admin | Payment Plan <?php echo $action; ?></title>

    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />

    <?php include_once 'global_files.php'; ?>

    <!-- On OFF switch -->

    <link href="../assets/css/jquery-ui.css" rel="stylesheet" />

    <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />

</head>

<!-- END  HEAD-->

<!-- BEGIN BODY-->



<body class="padTop53 ">

    <!-- MAIN WRAPPER -->

    <div id="wrap">

        <?php include_once 'header.php'; ?>

        <?php include_once 'left_menu.php'; ?>

        <!--PAGE CONTENT -->

        <div id="content">

            <div class="inner">

                <div class="row">

                    <div class="col-lg-12">

                        <h2><?php echo $action; ?> Payment Plan</h2>

                        <a href="item_payment_plans.php?eType=<?php echo $_REQUEST['eType']; ?>">

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

                            </div>

                            <br />

                        <?php } ?>

                        <?php if (1 === $success) { ?>

                            <div class="alert alert-success alert-dismissable">

                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>

                                <?php echo $langage_lbl_admin['LBL_Record_Updated_successfully']; ?>

                            </div>

                            <br />

                        <?php } ?>

                        <?php if (2 === $success) { ?>

                            <div class="alert alert-danger alert-dismissable">

                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>

                                <?php echo $langage_lbl_admin['LBL_EDIT_DELETE_RECORD']; ?>

                            </div>

                            <br />

                        <?php } ?>

                        <form method="post" action="" enctype="multipart/form-data" id="rentItem_category_form">

                            <input type="hidden" name="id" value="<?php echo $id; ?>" />

                            <input type="hidden" name="iMasterServiceCategoryId" value="<?php echo $iMasterServiceCategoryId; ?>" />


                            <?php if (count($db_master) > 1) { ?>

                                <div class="row">

                                    <div class="col-lg-12">

                                        <label>Plan Name <span class="red">*</span></label>

                                    </div>

                                    <div class="col-md-4 col-sm-4">

                                        <input type="text" class="form-control <?php echo ('' === $id) ? 'readonly-custom' : ''; ?>" id="vPlanName_Default" name="vPlanName_Default" value="<?php echo htmlspecialchars($userEditDataArr['vPlanName_'.$default_lang]); ?>" data-originalvalue="<?php echo $userEditDataArr['vPlanName_'.$default_lang]; ?>" readonly="readonly" required <?php if ('' === $id) { ?> onclick="editCategoryName('Add')" <?php } ?>>

                                    </div>

                                    <?php if ('' !== $id) { ?>

                                        <div class="col-lg-2">

                                            <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editCategoryName('Edit')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>

                                        </div>

                                    <?php } ?>

                                </div>



                                <div class="modal fade" id="Category_Modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">

                                    <div class="modal-dialog modal-lg">

                                        <div class="modal-content nimot-class">

                                            <div class="modal-header">

                                                <h4>

                                                    <span id="modal_action"></span> Payment Plan Name

                                                    <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vPlanName_')">x</button>

                                                </h4>

                                            </div>



                                            <div class="modal-body">

                                                <?php

                                                for ($i = 0; $i < $count_all; ++$i) {
                                                    $vCode = $db_master[$i]['vCode'];

                                                    $vPlanName = $db_master[$i]['vTitle'];

                                                    $eDefault = $db_master[$i]['eDefault'];

                                                    $vValue = 'vPlanName_'.$vCode;

                                                    ${$vValue} = $userEditDataArr[$vValue];

                                                    $required_msg = ('Yes' === $eDefault) ? ' <span class="red"> *</span>' : '';

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

                                                        <label>Payment Plan Name (<?php echo $vPlanName; ?>) <?php echo $required_msg; ?></label>



                                                    </div>

                                                    <div class="<?php echo $page_title_class; ?>">

                                                        <input type="text" class="form-control" name="<?php echo $vValue; ?>" id="<?php echo $vValue; ?>" value="<?php echo htmlspecialchars(${$vValue}); ?>" data-originalvalue="<?php echo ${$vValue}; ?>" placeholder="<?php echo $vPlanName; ?> Value">

                                                        <div class="text-danger" id="<?php echo $vValue.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?>

                                                        </div>

                                                    </div>

                                                    <?php

                                                        if (count($db_master) > 1) {
                                                            if ($EN_available) {
                                                                if ('EN' === $vCode) { ?>

                                                                <div class="col-md-3 col-sm-3">

                                                                    <button type="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vPlanName_', 'EN');">Convert To All

                                                                        Language</button>

                                                                </div>

                                                            <?php }
                                                                } else {
                                                                    if ($vCode === $default_lang) { ?>

                                                                <div class="col-md-3 col-sm-3">

                                                                    <button type="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vPlanName_', '<?php echo $default_lang; ?>');">Convert

                                                                        To All Language</button>

                                                                </div>

                                                    <?php }
                                                                    }
                                                        }

                                                    ?>

                                                </div>

                                                <?php } ?>

                                            </div>

                                            <div class="modal-footer" style="margin-top: 0">

                                                <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">

                                                    <strong><?php echo $langage_lbl['LBL_NOTE']; ?>:

                                                    </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?>

                                                </h5>

                                                <div class="nimot-class-but" style="margin-bottom: 0">

                                                    <button type="button" class="save" style="margin-left: 0 !important" onclick="saveCategoryName()"><?php echo $langage_lbl['LBL_Save']; ?></button>

                                                    <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vPlanName_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>

                                                </div>

                                            </div>



                                            <div style="clear:both;"></div>

                                        </div>

                                    </div>

                                </div>

                            <?php } else { ?>

                                <div class="row">

                                    <div class="col-lg-12">

                                        <label>Payment Plan Name</label>

                                    </div>

                                    <div class="col-md-4 col-sm-4">

                                        <input type="text" class="form-control" id="vPlanName_<?php echo $default_lang; ?>" name="vPlanName_<?php echo $default_lang; ?>" value="<?php echo htmlspecialchars($userEditDataArr['vPlanName_'.$default_lang]); ?>">

                                    </div>

                                </div>

                            <?php } ?>



                            <?php if (count($db_master) > 1) { ?>

                                <div class="row">

                                    <div class="col-lg-12">

                                        <label>Description <span class="red">*</span></label>

                                    </div>

                                    <div class="col-md-4 col-sm-4">

                                        <input type="text" class="form-control <?php echo ('' === $id) ? 'readonly-custom' : ''; ?>" id="tDescription_Default" name="tDescription_Default" value="<?php echo htmlspecialchars($vDescriptionArr['tDescription_'.$default_lang]); ?>" data-originalvalue="<?php echo $vDescriptionArr['tDescription_'.$default_lang]; ?>" readonly="readonly" <?php if ('' === $id) { ?> onclick="editDescription('Add')" <?php } ?> required >

                                    </div>

                                    <?php if ('' !== $id) { ?>

                                        <div class="col-lg-2">

                                            <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editDescription('Edit')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>

                                        </div>

                                    <?php } ?>

                                </div>



                                <div class="modal fade" id="tDescription_Modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">

                                    <div class="modal-dialog modal-lg">

                                        <div class="modal-content nimot-class">

                                            <div class="modal-header">

                                                <h4>

                                                    <span id="tDescriptionmodal_action"></span> Description

                                                    <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'tDescription_')">x</button>

                                                </h4>

                                            </div>



                                            <div class="modal-body">

                                                <?php

                                                for ($i = 0; $i < $count_all; ++$i) {
                                                    $vCode = $db_master[$i]['vCode'];

                                                    $vTitle = $db_master[$i]['vTitle'];

                                                    $eDefault = $db_master[$i]['eDefault'];

                                                    $vValue = 'tDescription_'.$vCode;

                                                    ${$vValue} = $vDescriptionArr[$vValue];

                                                    $required_msg = ('Yes' === $eDefault) ? ' <span class="red"> *</span>' : '';

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

                                                            <label>Description (<?php echo $vTitle; ?>) <?php echo $required_msg; ?></label>



                                                        </div>

                                                        <div class="<?php echo $page_title_class; ?>">

                                                            <input type="text" class="form-control" name="<?php echo $vValue; ?>" id="<?php echo $vValue; ?>" value="<?php echo htmlspecialchars(${$vValue}); ?>" data-originalvalue="<?php echo ${$vValue}; ?>" placeholder="<?php echo $vTitle; ?> Value">

                                                            <div class="text-danger" id="<?php echo $vValue.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?>

                                                            </div>

                                                        </div>

                                                        <?php

                                                        if (count($db_master) > 1) {
                                                            if ($EN_available) {
                                                                if ('EN' === $vCode) { ?>

                                                                    <div class="col-md-3 col-sm-3">

                                                                        <button type="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('tDescription_', 'EN');">Convert To

                                                                            All Language</button>

                                                                    </div>

                                                                <?php }
                                                                } else {
                                                                    if ($vCode === $default_lang) { ?>

                                                                    <div class="col-md-3 col-sm-3">

                                                                        <button type="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('tDescription_', '<?php echo $default_lang; ?>');">Convert

                                                                            To All Language</button>

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

                                                <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;">

                                                    <strong><?php echo $langage_lbl['LBL_NOTE']; ?>:

                                                    </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?>

                                                </h5>

                                                <div class="nimot-class-but" style="margin-bottom: 0">

                                                    <button type="button" class="save" style="margin-left: 0 !important" onclick="saveDescription()"><?php echo $langage_lbl['LBL_Save']; ?></button>

                                                    <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'tDescription_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>

                                                </div>

                                            </div>



                                            <div style="clear:both;"></div>

                                        </div>

                                    </div>

                                </div>

                            <?php } else { ?>

                                <div class="row">

                                    <div class="col-lg-12">

                                        <label>Description</label>

                                    </div>

                                    <div class="col-md-4 col-sm-4">

                                        <input type="text" class="form-control" id="tDescription_<?php echo $default_lang; ?>" name="tDescription_<?php echo $default_lang; ?>" value="<?php echo $userEditDataArr['tDescription_'.$default_lang]; ?>">

                                    </div>

                                </div>

                            <?php } ?>


                            <?php if ('Yes' === $eFreePlan) { ?>
                                <div class="row">

                                    <div class="col-lg-12">

                                        <label>Free Plan?</label>

                                    </div>

                                    <div class="col-lg-6">

                                        <div class="make-switch" data-on="success" data-off="warning">

                                            <input type="checkbox" name="eFreePlan" <?php echo ('' !== $id && 'Yes' === $eFreePlan) ? 'checked' : ''; ?> value="Yes" id="eFreePlan"/>

                                        </div>

                                    </div>

                                </div>
                                <div class="row">

                                    <div class="col-lg-12">

                                        <label>Select Availability</label>

                                    </div>

                                    <div class="col-md-4 col-sm-4">

                                        <select name="eAvailability" class="form-control" id="eAvailability">
                                            <option value="">Select Availability</option>
                                            <option value="FirstTime" <?php echo ('FirstTime' === $eAvailability) ? 'selected' : ''; ?>>1st time</option>
                                            <option value="EveryPost" <?php echo ('EveryPost' === $eAvailability) ? 'selected' : ''; ?>>For Every Post</option>
                                        </select>

                                    </div>

                                </div>
                            <?php } ?>


                            <!-- <div class="row eFeaturedPlanShow" style="display: none">

                                <div class="col-lg-12">

                                    <label>Featured/Premium Plan?</label>

                                </div>

                                <div class="col-lg-6">

                                    <div class="make-switch" data-on="success" data-off="warning">

                                        <input type="checkbox" name="eFeaturedPlan" <?php echo ('' !== $id && 'Yes' === $eFeaturedPlan) ? 'checked' : ''; ?> value="Yes" />

                                    </div>

                                </div>

                            </div> -->



                            <div class="row">

                                <div class="col-lg-12">

                                    <label>Enter Days <span class="red">*</span></label>

                                </div>

                                <div class="col-md-4 col-sm-4">

                                    <input type="number" class="form-control" id="iTotalDays" name="iTotalDays" value="<?php echo $iTotalDays; ?>" min="1" step="1">

                                </div>

                            </div>

                            <?php if ('Yes' !== $eFreePlan) { ?>
                            <div class="row">

                                <div class="col-lg-12">

                                    <label>Enter Number of Post</label>

                                </div>

                                <div class="col-md-4 col-sm-4">

                                    <input type="number" class="form-control" id="iTotalPost" name="iTotalPost" value="<?php if ($iTotalPost > 0) {
                                        echo $iTotalPost;
                                    } else {
                                        echo '';
                                    }?>" min="0" step="1">

                                </div>

                            </div>
                        <?php } ?>


                            <div class="row eFreePlanSelected">

                                <div class="col-lg-12">

                                    <label>Enter Amount <span class="red">*</span></label>

                                </div>

                                <div class="col-md-4 col-sm-4">

                                    <input type="number" class="form-control" id="fAmount" name="fAmount" value="<?php echo $fAmount; ?>" min="1">

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

                                    <input type="submit" class="save btn-info" name="submit" id="submit" value="<?php echo $action.' '.'Payment Plan'; ?>" style="margin-right: 10px">

                                    <a href="item_payment_plans.php?eType=<?php echo $_REQUEST['eType']; ?>" class="btn btn-default back_link">Cancel</a>

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



    <script type="text/javascript">

        function editCategoryName(action) {

            $('#modal_action').html(action);

            $('#Category_Modal').modal('show');

        }



        function saveCategoryName() {

            if ($('#vPlanName_<?php echo $default_lang; ?>').val().trim() == "") {

                $('#vPlanName_<?php echo $default_lang; ?>_error').show();

                $('#vPlanName_<?php echo $default_lang; ?>').focus();

                $('#vPlanName_<?php echo $default_lang; ?>').val('');

                clearInterval(langVar);

                langVar = setTimeout(function() {

                    $('#vPlanName_<?php echo $default_lang; ?>_error').hide();

                }, 5000);

                return false;

            }



            $('#vPlanName_Default').val($('#vPlanName_<?php echo $default_lang; ?>').val());

            $('#vPlanName_Default').closest('.row').removeClass('has-error');

            $('#vPlanName_Default-error').remove();

            $('#Category_Modal').modal('hide');

        }



        if ($('#eFreePlan').is(":checked") == false) {

          $(".eFeaturedPlanShow").show();

          $(".eFreePlanSelected").show();

        } else {

          $(".eFeaturedPlanShow").hide();

          $(".eFreePlanSelected").hide();

        }



        $('#eFreePlan').change(function () {

            if ($('#eFreePlan').is(":checked") == false) {

              $(".eFeaturedPlanShow").show();

              $(".eFreePlanSelected").show();

            } else {

                $(".eFeaturedPlanShow").hide();

                $(".eFreePlanSelected").hide();

            }

         });



        $('#iListMaxCount').keyup(function(e) {

            if (/\D/g.test(this.value)) {

                this.value = this.value.replace(/\D/g, '');

            }

        });



        $(document).ready(function () {

            $('#rentItem_category_form').validate({

                rules: {

                    iTotalDays: {

                        required: true,

                        number: true

                    },

                    fAmount: {

                        required: true,

                        number: true

                    }

                },

            });

        });



        function editDescription(action) {

            $('#tDescriptionmodal_action').html(action);

            $('#tDescription_Modal').modal('show');

        }



        function saveDescription() {

            if ($('#tDescription_<?php echo $default_lang; ?>').val().trim() == "") {

                $('#tDescription_<?php echo $default_lang; ?>_error').show();

                $('#tDescription_<?php echo $default_lang; ?>').focus();

                $('#tDescription_<?php echo $default_lang; ?>').val('');

                clearInterval(langVar);

                langVar = setTimeout(function() {

                    $('#tDescription_<?php echo $default_lang; ?>_error').hide();

                }, 5000);

                return false;

            }



            $('#tDescription_Default').val($('#tDescription_<?php echo $default_lang; ?>').val());

            $('#tDescription_Default').closest('.row').removeClass('has-error');

            $('#tDescription_Default-error').remove();

            $('#tDescription_Modal').modal('hide');

        }


    </script>

</body>

<!-- END BODY-->

</html>