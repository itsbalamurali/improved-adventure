<?php
include_once '../common.php';

$id = $_REQUEST['id'] ?? '';
$state_id = $_REQUEST['state_id'] ?? '';
$country_id = $_REQUEST['country_id'] ?? '';
$success = $_REQUEST['success'] ?? 0;
$action = ('' !== $id) ? 'Edit' : 'Add';

$tbl_name = $script = 'city';

// echo '<prE>'; print_R($_REQUEST); echo '</pre>';
// set all variables with either post (when submit) either blank (when insert)
$vCountry = $_POST['vCountry'] ?? '';
$vState = $_POST['vState'] ?? '';
$vCity = $_POST['vCity'] ?? '';
$vCountryCodeISO_3 = $_POST['vCountryCodeISO_3'] ?? '';
$vPhoneCode = $_POST['vPhoneCode'] ?? '';
$eStatus_check = $_POST['eStatus'] ?? 'off';
$eStatus = ('on' === $eStatus_check) ? 'Active' : 'Inactive';
$backlink = $_POST['backlink'] ?? '';
$previousLink = $_POST['backlink'] ?? '';

if (isset($_POST['submit'])) {
    if ('Add' === $action && !$userObj->hasPermission('create-city')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to create City.';
        header('Location:city.php');

        exit;
    }

    if ('Edit' === $action && !$userObj->hasPermission('edit-city')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to update City.';
        header('Location:city.php');

        exit;
    }

    if (SITE_TYPE === 'Demo' && '' !== $id) {
        $_SESSION['success'] = '2';
        header('location:'.$backlink);

        exit;
    }

    // Add Custom validation
    require_once 'Library/validation.class.php';
    $validobj = new validation();
    $validobj->add_fields($_POST['vCountry'], 'req', 'Country is required');
    $validobj->add_fields($_POST['vState'], 'req', 'State is required');
    $validobj->add_fields($_POST['vCity'], 'req', 'City Name is required');

    $error = $validobj->validate();
    // Added By HJ On 21-01-2019 For Check City Name and It's Code As Per Client Bug - 6726 Start
    $whereCond = '';
    if ('' !== $id) {
        $whereCond = " AND `iCityId` != '".$id."'";
    }
    $checkCityName = $obj->MySQLSelect("SELECT iCityId FROM city WHERE eStatus='Active' AND iCountryId='".$vCountry."' AND iStateId='".$vState."' AND (`vCity` LIKE '".$vCity."')".$whereCond);
    if (count($checkCityName) > 0) {
        $error = 'City already exists in this state.';
    }
    // Added By HJ On 21-01-2019 For Check City Name and It's Code As Per Client Bug - 6726 End
    if ($error) {
        $success = 3;
        $newError = $error;
    // exit;
    } else {
        $q = 'INSERT INTO ';
        $where = '';

        if ('' !== $id) {
            $q = 'UPDATE ';
            $where = " WHERE `iCityId` = '".$id."'";
        }

        $query = $q.' `'.$tbl_name."` SET
			`iCountryId` = '".$vCountry."',
			`iStateId` = '".$vState."',
			`vCity` = '".$vCity."',
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

// for Edit
if ('' !== $id) {
    $sql = 'SELECT * FROM '.$tbl_name." WHERE iCityId = '".$id."'";
    $db_data = $obj->MySQLSelect($sql);

    $vLabel = $id;
    if (count($db_data) > 0) {
        foreach ($db_data as $key => $value) {
            $vCity = $value['vCity'];
            $country_id = $value['iCountryId'];
            $state_id = $value['iStateId'];
            $eStatus = $value['eStatus'];
        }
    }
}

$sql_country = "SELECT iCountryId,vCountry FROM country WHERE vCountry != '' ORDER BY vCountry ASC";
$db_data_country = $obj->MySQLSelect($sql_country);

$sql_state = "SELECT iStateId,vState FROM state WHERE iCountryId='".$country_id."' AND vState != '' ORDER BY vState ASC";
$db_data_state = $obj->MySQLSelect($sql_state);

// echo '<pre>'; print_R($db_data_state); echo '</pre>';die;
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title>Admin | City <?php echo $action; ?></title>
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
                            <h2><?php echo $action; ?> City</h2>
                            <a href="city.php" class="back_link">
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
                            <form method="post" name="_city_form" id="_city_form" action="">
                                <input type="hidden" name="previousLink" id="previousLink" value="<?php echo $previousLink; ?>"/>
                                <input type="hidden" name="backlink" id="backlink" value="city.php"/>
                                <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Country Name<span class="red"> *</span></label>
                                    </div>
                                    <div class="col-lg-6">
                                        <select id="lunch" onChange="showState(this.value);" name="vCountry" class="selectpicker" data-live-search="true">
                                            <option value="">Select Country</option>
                                            <?php foreach ($db_data_country as $country) { ?>
                                                <?php if ($country['iCountryId'] === $country_id) { ?>
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
                                        <select  id="state" name="vState" class="selectpicker" data-live-search="true">
                                            <option value="">Select State</option>
                                            <?php foreach ($db_data_state as $state) { ?>
                                                <?php if ($state['iStateId'] === $state_id) { ?>
                                                    <option selected="selected" value="<?php echo $state['iStateId']; ?>"><?php echo $state['vState']; ?></option>
                                                <?php } else { ?>
                                                    <option value="<?php echo $state['iStateId']; ?>"><?php echo $state['vState']; ?></option>
                                                <?php } ?>
                                            <?php } ?>

                                        </select>

                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>City Name<span class="red"> *</span></label>
                                    </div>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" name="vCity"  id="vCity" value="<?php echo $vCity; ?>" placeholder="City Name" >
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
                                        <?php if (('Edit' === $action && $userObj->hasPermission('edit-city')) || ('Add' === $action && $userObj->hasPermission('create-city'))) { ?>
                                            <input type="submit" class="btn btn-default" name="submit" id="submit" value="<?php echo $action; ?> City">
                                            <input type="reset" value="Reset" class="btn btn-default">
                                        <?php } ?>
                                        <!-- <a href="javascript:void(0);" onclick="reset_form('_city_form');" class="btn btn-default">Reset</a> -->
                                        <a href="city.php" class="btn btn-default back_link">Cancel</a>
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
        <script src="https://maps.google.com/maps/api/js?sensor=true&key=<?php echo $GOOGLE_SEVER_API_KEY_WEB; ?>&libraries=places" type="text/javascript"></script>

        <script type="text/javascript">

                                            $(document).ready(function () {
                                                $(window).keydown(function (event) {
                                                    if (event.keyCode == 13) {
                                                        event.preventDefault();
                                                        return false;
                                                    }
                                                });
                                            });

                                            $(function () {
                                                var from = document.getElementById('vCity');
                                                autocomplete_from = new google.maps.places.Autocomplete(from);
                                                google.maps.event.addListener(autocomplete_from, 'place_changed', function () {
                                                    setCityValues($("#vCity").val());
                                                    $("#vCity").val('');
                                                });
                                            });

                                            function setCityValues(address) {
                                                // $.ajax({
                                                //     type: "POST",
                                                //     url: "set_city_values.php",
                                                //     data: "address=" + address,
                                                //     success: function (dataHtml) {
                                                //         $("#body_data").html(dataHtml);
                                                //         $("#myModal").modal('show');
                                                //     },
                                                //     error: function (dataHtml) {

                                                //     }
                                                // });

                                                var ajaxData = {
                                                    'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>set_city_values.php',
                                                    'AJAX_DATA': "address=" + address,
                                                };
                                                getDataFromAjaxCall(ajaxData, function(response) {
                                                    if(response.action == "1") {
                                                        var dataHtml = response.result;
                                                        $("#body_data").html(dataHtml);
                                                        $("#myModal").modal('show');
                                                    }
                                                    else {
                                                        console.log(response.result);
                                                    }
                                                });

                                            }

                                            function getTheSelected() {
                                                var area = $('input[name=setArea]:checked').val();
                                                $("#vCity").val(area);
                                                $("#myModal").modal('hide');
                                                // alert(area);
                                            }


                                            function showState(id) {
                                                // $.ajax({
                                                //     type: "POST",
                                                //     url: "functions_area.php",
                                                //     data: "country_id=" + id,
                                                //     success: function (data) {
                                                //         if (data.success) {
                                                //             //document.write//alert(data.status);
                                                //             $('#state').html(data);
                                                //         } else {
                                                //             //alert(data);
                                                //             // $('#msg').html(data).fadeIn('slow');
                                                //             $('#state').html(data); //also show a success message
                                                //             $('#state').selectpicker('refresh');
                                                //             //CityId=$('#state option:selected').val();
                                                //             /*  var json_obj = $.parseJSON(data);//parse JSON
                                                //              alert(json_obj.json);
                                                //              */	// var a=JSON.stringify(data);
                                                //             //alert(a);
                                                //             //showCity(CityId);
                                                //         }
                                                //     }
                                                // });

                                                var ajaxData = {
                                                    'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>functions_area.php',
                                                    'AJAX_DATA': "country_id=" + id,
                                                };
                                                getDataFromAjaxCall(ajaxData, function(response) {
                                                    if(response.action == "1") {
                                                        var data = response.result;
                                                        if (data.success) {
                                                            $('#state').html(data);
                                                        } else {
                                                            $('#state').html(data); //also show a success message
                                                            $('#state').selectpicker('refresh');
                                                        }
                                                    }
                                                    else {
                                                        console.log(response.result);
                                                    }
                                                });
                                            }

        </script>
        <script>
            $(document).ready(function () {
                var referrer;
                if ($("#previousLink").val() == "") {
                    referrer = document.referrer;
                } else {
                    referrer = $("#previousLink").val();
                }
                if (referrer == "") {
                    referrer = "city.php";
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
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-large">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">x</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"> Please select an correct city from below list </h4>
            </div>
            <div class="modal-body" id="body_data">

            </div>
            <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button><a class="btn btn-success btn-ok " onClick="getTheSelected();" >Select</a></div>
        </div>
    </div>
</div>