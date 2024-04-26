<?php
include_once '../common.php';

$script = 'languages';

$id = $_REQUEST['id'] ?? '';
// $pageid 		= isset($_REQUEST['lp_id'])?$_REQUEST['lp_id']:0;
$lp_name = $_REQUEST['lp_name'] ?? '';
$success = $_REQUEST['success'] ?? 0;
$var_msg = $_REQUEST['var_msg'] ?? '';
$action = ('' !== $id) ? 'Edit' : 'Add';

$tbl_name = 'cancel_reason';
$backlink = $_POST['backlink'] ?? '';
$previousLink = $_POST['backlink'] ?? '';

// fetch all lang from language_master table
$sql = 'SELECT * FROM `language_master` ORDER BY `iDispOrder`';
$db_master = $obj->MySQLSelect($sql);
$count_all = count($db_master);

// set all variables with either post (when submit) either blank (when insert)
$vLabel = $_POST['vLabel'] ?? $id;
$lPage_id = $_POST['lPage_id'] ?? '';
if ($count_all > 0) {
    for ($i = 0; $i < $count_all; ++$i) {
        $vValue = 'vValue_'.$db_master[$i]['vCode'];
        ${$vValue} = $_POST[$vValue] ?? '';
    }
}

if (isset($_POST['submit'])) {
    if ('Add' === $action && !$userObj->hasPermission('create-cancel-reasons')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to create Cancel Reasons.';
        header('Location:cancel_reason.php');

        exit;
    }

    if ('Edit' === $action && !$userObj->hasPermission('edit-cancel-reasons')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to update Cancel Reasons.';
        header('Location:cancel_reason.php');

        exit;
    }

    if ('' === $id) {
        $sql = "SELECT * FROM `language_label` WHERE vLabel = '".$vLabel."'";
        $db_label_check = $obj->MySQLSelect($sql);
        if (count($db_label_check) > 0) {
            $var_msg = 'Language Label Already Exists In General Label';
            header('Location:languages_action.php?var_msg='.$var_msg.'&success=0');

            exit;
        }

        $sql = "SELECT * FROM `language_label_other` WHERE vLabel = '".$vLabel."'";
        $db_label_check_ride = $obj->MySQLSelect($sql);
        if (count($db_label_check_ride) > 0) {
            $var_msg = 'Language Label Already Exists In Ride Label';
            header('Location:languages_action.php?var_msg='.$var_msg.'&success=0');

            exit;
        }
    }

    if (SITE_TYPE === 'Demo') {
        header('Location:languages_action.php?id='.$vLabel.'&success=2');

        exit;
    }

    if ($count_all > 0) {
        for ($i = 0; $i < $count_all; ++$i) {
            $q = 'INSERT INTO ';
            $where = '';

            if ('' !== $id) {
                $q = 'UPDATE ';
                $sql = 'SELECT vLabel FROM '.$tbl_name." WHERE LanguageLabelId = '".$id."'";
                $db_data = $obj->MySQLSelect($sql);
                $sql = 'SELECT * FROM '.$tbl_name." WHERE vLabel = '".$db_data[0]['vLabel']."'";
                $db_data = $obj->MySQLSelect($sql);
                $vLabel = $db_data[0]['vLabel'];
                $where = " WHERE `vLabel` = '".$vLabel."' AND vCode = '".$db_master[$i]['vCode']."'";
            }

            $vValue = 'vValue_'.$db_master[$i]['vCode'];

            $query = $q.' `'.$tbl_name."` SET
				`vLabel` = '".$vLabel."',
				`lPage_id` = '".$lPage_id."',
				`vCode` = '".$db_master[$i]['vCode']."',
				`vValue` = '".${$vValue}."'"
                    .$where;

            $obj->sql_query($query);
        }
    }

    // header("Location:languages.php?id=".$vLabel.'&success=1');
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
    $sql = 'SELECT * FROM '.$tbl_name." WHERE iCancelReasonId = '".$id."'";
    $db_data_reason = $obj->MySQLSelect($sql);

    // echo "<pre>";print_R($db_data_reason);die;
    // $sql = "SELECT vCode FROM language_master";
    // $db_data = $obj->MySQLSelect($sql);
    // echo '<pre>'; print_R($db_data); echo '</pre>'; exit;
    // $vLabel = $id;
    // $vLabel = $db_master[0]['vLabel'];
    // $lPage_id = $db_master[0]['lPage_id'];
    if (count($db_master) > 0) {
        foreach ($db_master as $key => $value) {
            $vValue = 'vTitle_'.$value['vCode'];
            ${$vValue} = $db_data_reason[0][$vValue];
            $arr[] = [$vValue => ${$vValue}];
        }
    }
    // echo "<pre>";print_r($arr);exit;
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title>Admin | Cancel Reason <?php echo $action; ?></title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
        <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />

        <?php include_once 'global_files.php'; ?>
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
                            <h2><?php echo $action; ?> Cancel Reason</h2>
                            <a href="languages.php" class="back_link">
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
                            <?php } elseif (0 === $success && '' !== $var_msg) { ?>
                                <div class="alert alert-danger alert-dismissable">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                    <?php echo $var_msg; ?>
                                </div><br/>
                            <?php } ?>
                            <form method="post" name="_languages_form" id="_languages_form" action="">
                                <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                                <input type="hidden" name="previousLink" id="previousLink" value="<?php echo $previousLink; ?>"/>
                                <input type="hidden" name="backlink" id="backlink" value="languages.php"/>
                                <div class="row">
                                    <div class="col-lg-12" id="errorMessage">
                                    </div>
                                </div>

                                <?php
                                if ($count_all > 0) {
                                    for ($i = 0; $i < $count_all; ++$i) {
                                        $vCode = $db_master[$i]['vCode'];
                                        $vTitle = $db_master[$i]['vTitle'];
                                        $eDefault = $db_master[$i]['eDefault'];

                                        $vValue = 'vTitle_'.$vCode;

                                        $required = ('Yes' === $eDefault) ? 'required' : '';
                                        $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                        ?>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label><?php echo $vTitle; ?> Value <?php echo $required_msg; ?></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="<?php echo $vValue; ?>" id="<?php echo $vValue; ?>" value="<?php echo ${$vValue}; ?>" placeholder="<?php echo $vTitle; ?> Value" <?php echo $required; ?>>
                                                <div class="text-danger" id="<?php echo $vValue.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                            </div>
                                            <?php
                                            if ($vCode === $default_lang) {
                                                ?>
                                                <div class="col-lg-6">
                                                    <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vTitle_', '<?php echo $default_lang; ?>');">Convert To All Language</button>
                                                </div>

                                                <?php
                                            }
                                        ?>
                                        </div>
                                    <?php }
                                    }
?>
                                <!--<div class="row">
                                    <div class="col-lg-12">
                                        <label>Allow Cancellation Charges</label>
                                    </div>
                                    <div class="col-lg-6">
                                        <select name="eAllowedCharge" class="form-control" >
                                            <option value="Yes" <?php if ('Yes' === $db_data_reason[0]['eAllowedCharge']) { ?>selected<?php } ?>>Yes</option>
                                            <option value="No" <?php if ('No' === $db_data_reason[0]['eAllowedCharge']) { ?>selected<?php } ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                                 <div class="row">
                                        <div class="col-lg-12">
                                                <label>Display Order</label>
                                        </div>
                                        <div class="col-lg-6">
                                                <select name="iSortId" class="form-control">
                                                        <option value="Yes">Yes</option>
                                                </select>
                                        </div>
                                </div> -->
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
                                        <?php if (('Edit' === $action && $userObj->hasPermission('edit-cancel-reasons')) || ('Add' === $action && $userObj->hasPermission('create-cancel-reasons'))) { ?>
                                            <input type="submit" class="btn btn-default" name="submit" id="submit" value="<?php echo $action; ?> Label">
                                            <a href="javascript:void(0);" onclick="reset_form('_languages_form');" class="btn btn-default">Reset</a>
                                        <?php } ?>
                                        <a href="languages.php" class="btn btn-default back_link">Cancel</a>
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

        <div class="row loding-action" id="loaderIcon" style="display:none;">
            <div align="center">
                <img src="default.gif">
                <span>Language Translation is in Process. Please Wait...</span>
            </div>
        </div>


        <?php include_once 'footer.php'; ?>
    </body>
    <!-- END BODY-->
</html>
<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
<script type="text/javascript" language="javascript">
                                            $(document).ready(function () {

                                                $('#loaderIcon').hide();


                                            });
</script>
<script>
    $(document).ready(function () {
        var referrer;
        if ($("#previousLink").val() == "") {
            referrer = document.referrer;
            //alert(referrer);
        } else {
            referrer = $("#previousLink").val();
        }
        if (referrer == "") {
            referrer = "page.php";
        } else {
            $("#backlink").val(referrer);
        }
        $(".back_link").attr('href', referrer);
    });
</script>



