<?php
include_once '../common.php';

$id = $_REQUEST['id'] ?? '';
$state_id = $_REQUEST['state_id'] ?? '';
$success = $_REQUEST['success'] ?? 0;
$action = ('' !== $id) ? 'Edit' : 'Add';
$tbl_name = $script = 'state';

$backlink = $_POST['backlink'] ?? '';
$previousLink = $_POST['backlink'] ?? '';

// set all variables with either post (when submit) either blank (when insert)
$vCountry = $_POST['vCountry'] ?? '';
$vState = $_POST['vState'] ?? '';
$vStateCode = $_POST['vStateCode'] ?? '';
$eStatus_check = $_POST['eStatus'] ?? 'off';
$eStatus = ('on' === $eStatus_check) ? 'Active' : 'Inactive';

if (isset($_POST['submit'])) {
    $oCache->flushData();

    if ('Add' === $action && !$userObj->hasPermission('create-state')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to create state.';
        header('Location:state.php');

        exit;
    }

    if ('Edit' === $action && !$userObj->hasPermission('edit-state')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to update state.';
        header('Location:state.php');

        exit;
    }

    // if (SITE_TYPE == 'Demo' && $id != "") {
    //     $_SESSION['success'] = '2';
    //     header("location:" . $backlink);
    //     exit;
    // }

    if (SITE_TYPE === 'Demo') {
        $_SESSION['success'] = '2';
        header('location:'.$backlink);

        exit;
    }

    require_once 'Library/validation.class.php';
    $validobj = new validation();
    $validobj->add_fields($_POST['vCountry'], 'req', 'Country is required');
    $validobj->add_fields($_POST['vState'], 'req', 'State Name is required');
    $validobj->add_fields($_POST['vStateCode'], 'req', 'State Code is required');
    $error = $validobj->validate();
    // Added By HJ On 21-01-2019 For Check State Name and It's Code As Per Client Bug - 6726 Start
    $whereCond = '';
    if ('' !== $id) {
        $whereCond = " AND `iStateId` != '".$id."'";
    }
    $checkStateCode = $obj->MySQLSelect("SELECT iStateId FROM state WHERE eStatus='Active' AND iCountryId='".$vCountry."' AND (`vState` LIKE '".$vState."' OR `vStateCode` LIKE '".$vStateCode."')".$whereCond);
    if (count($checkStateCode) > 0) {
        $error = 'State Name or Code already exists.';
    }
    // Added By HJ On 21-01-2019 For Check State Name and It's Code As Per Client Bug - 6726 End
    if ($error) {
        $success = 3;
        $newError = $error;
    // exit;
    } else {
        $q = 'INSERT INTO ';
        $where = '';

        if ('' !== $id) {
            $q = 'UPDATE ';
            $where = " WHERE `iStateId` = '".$id."'";
        }

        $query = $q.' `'.$tbl_name."` SET
			`iCountryId` = '".$vCountry."',
			`vState` = '".$vState."',
			`vStateCode` = '".$vStateCode."',
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
}

$sql1 = "SELECT * FROM country WHERE vCountry != '' ORDER BY vCountry ASC";
$db_data1 = $obj->MySQLSelect($sql1);

// for Edit
if ('Edit' === $action) {
    $sql = "SELECT * FROM state WHERE iStateId = '".$id."'";
    $db_data = $obj->MySQLSelect($sql);

    $vLabel = $id;
    if (count($db_data) > 0) {
        foreach ($db_data as $key => $value) {
            // $vCountry	 = $value['vCountry'];
            $vCountry = $value['iCountryId'];
            $vState = $value['vState'];
            $vStateCode = $value['vStateCode'];
            $eStatus = $value['eStatus'];
            // $vCountryCodeISO_3	 = $value['vCountryCodeISO_3'];
            // $vPhoneCode	 = $value['vPhoneCode'];
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
        <title>Admin | State <?php echo $action; ?></title>
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
                            <h2><?php echo $action; ?> State</h2>
                            <a href="state.php" class="back_link">
                                <input type="button" value="Back to Listing" class="add-btn">
                            </a>
                        </div>
                    </div>
                    <hr />
                    <div class="body-div">
                        <div class="form-group">
                            <?php if (2 === $success) { ?>
                                <div class="alert alert-danger alert-dismissable">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                    <?php echo $langage_lbl_admin['LBL_EDIT_DELETE_RECORD']; ?>
                                </div><br/>
                            <?php } ?>
                            <?php if (3 === $success) { ?>
                                <div class="alert alert-danger alert-dismissable">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                    <?php print_r($error); ?>
                                </div><br/>
                            <?php } ?>
                            <form method="post" action="" name="_state_form" id="_state_form" >
                                <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                                <input type="hidden" name="previousLink" id="previousLink" value="<?php echo $previousLink; ?>"/>
                                <input type="hidden" name="backlink" id="backlink" value="state.php"/>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Country Name<span class="red"> *</span></label>
                                    </div>
                                    <div class="col-lg-6">
                                        <select id="lunch" name="vCountry" class="selectpicker" data-live-search="true">
                                            <option value="">Select Country</option>
                                            <?php foreach ($db_data1 as $country) { ?>
                                                <?php if ($country['iCountryId'] === $vCountry) { ?>
                                                    <option selected="selected" value="<?php echo $country['iCountryId']; ?>"><?php echo $country['vCountry']; ?></option>
                                                <?php } else { ?>
                                                    <option value="<?php echo $country['iCountryId']; ?>"><?php echo $country['vCountry']; ?></option>
                                                <?php } ?>
                                            <?php } ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>State Name<span class="red"> *</span></label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="vState"  id="vState" value="<?php echo $vState; ?>" placeholder="State Name" >
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>State Code<span class="red"> *</span></label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="vStateCode"  id="vStateCode" value="<?php echo $vStateCode; ?>" placeholder="State Code" >
                                    </div>
                                </div>
                                <!-- <div class="row">
                                        <div class="col-lg-12">
                                                <label>Country Code ISO_3<span class="red"> *</span></label>
                                        </div>
                                        <div class="col-lg-6">
                                                <input type="text" class="form-control" name="vCountryCodeISO_3"  id="vCountryCodeISO_3" value="<?php echo $vCountryCodeISO_3; ?>" placeholder="Country Code ISO_3" required>
                                        </div>
                                </div> -->

                                <!-- <div class="row">
                                        <div class="col-lg-12">
                                                <label>Country Phone Code<span class="red"> *</span></label>
                                        </div>
                                        <div class="col-lg-6">
                                                <input type="text" class="form-control" name="vPhoneCode"  id="vPhoneCode" value="<?php echo $vPhoneCode; ?>" placeholder="Country Phone Code" required>
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
                                        <?php if (('Edit' === $action && $userObj->hasPermission('edit-state')) || ('Add' === $action && $userObj->hasPermission('create-state'))) { ?>
                                            <input type="submit" class="btn btn-default" name="submit" id="submit" value="<?php echo $action; ?> State">
                                            <input type="reset" value="Reset" class="btn btn-default">
                                        <?php } ?>
                                        <!-- <a href="javascript:void(0);" onclick="reset_form('_state_form');" class="btn btn-default">Reset</a> -->
                                        <a href="state.php" class="btn btn-default back_link">Cancel</a>
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

        <script>
            $(document).ready(function () {
                var referrer;
                if ($("#previousLink").val() == "") {
                    referrer = document.referrer;
                } else {
                    referrer = $("#previousLink").val();
                }
                if (referrer == "") {
                    referrer = "state.php";
                } else {
                    $("#backlink").val(referrer);
                }
                $(".back_link").attr('href', referrer);
            });
        </script>
        <script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
        <script src="js/bootstrap-select.js"></script>
    </body>
    <!-- END BODY-->
</html>
