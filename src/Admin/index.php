<?php

include_once '../common.php';
if ($MODULES_OBJ->isEnableAdminPanelV2()) {
    include_once 'index_v3.php';

    exit;
}
$tsiteUrl = $tconfig['tsite_url'];
$userType = $_REQUEST['userType'] ?? 'admin';
$data = $obj->MySQLSelect('SELECT * FROM setup_info');
if (isset($data[0]['eEnableHotel']) && 'No' === $data[0]['eEnableHotel'] && 'hotel' === strtolower($userType)) {
    header('Location:'.$tsiteUrl.'admin');

    exit;
}
$cubeDeliverallOnly = $MODULES_OBJ->isOnlyDeliverAllSystem();
$filePanel = 'Admin'; // Used In setup_validation.php File For Include Js File By HJ On 22-04-2019
if (false === $IS_INHOUSE_DOMAINS && '192.168.1.141' === $_SERVER['HTTP_HOST']) {
    $data = $obj->MySQLSelect('SELECT * FROM setup_info');
    if (isset($data[0]['iSetupId']) && $data[0]['iSetupId'] > 0) {
        include_once '../setup_validation.php';
        if ($errorcountsystemvalidation > 0) {
            exit;
        }
    }
}
$_SESSION['sess_signin'] = 'admin';
$AUTH_OBJ->AuthAdminRedirect();

if ('cubetaxishark' === $host_system || 'cubetaxi5plus' === $host_system) {
    $logo = 'logo-taxi.png';
} elseif ('cubedelivery' === $host_system) {
    $logo = 'logo_delivery.png';
} else {
    if ('Yes' === $THEME_OBJ->isCubeJekXThemeActive() || 'Yes' === $THEME_OBJ->isRideCXThemeActive() || 'Yes' === $THEME_OBJ->isServiceXThemeActive() || 'Yes' === $THEME_OBJ->isDeliveryKingThemeActive() || 'Yes' === $THEME_OBJ->isCubexThemeActive() || 'Yes' === $THEME_OBJ->isServiceXv2ThemeActive() || 'Yes' === $THEME_OBJ->isRideCXv2ThemeActive() || 'Yes' === $THEME_OBJ->isCubeXv2ThemeActive()) {
        $logo = 'logo-admin.png';
    } elseif ('Yes' === $THEME_OBJ->isDeliveryXThemeActive()) {
        $logo = 'admin-logo.png';
    } else {
        $logo = 'logo-admin.png';
    }
}
$activeTab = '1';
$activeTabId = 'super001';
if ('billing' === $userType) {
    $activeTab = '3';
    $activeTabId = 'billing001';
}
if (file_exists($tconfig['tpanel_path'].$logogpath.$logo)) {
    $admin_logo = $tsiteUrl.$logogpath.$logo;
} else {
    $admin_logo = $tsiteUrl.''.SITE_ADMIN_URL.'/'.'images/'.$logo;
}
$fav_icon_image = 'favicon.ico';
if (file_exists($tconfig['tsite_upload_apptype_images_panel'].$template.'/'.$fav_icon_image)) {
    $fav_icon_image = $tconfig['tsite_upload_apptype_images'].$template.'/'.$fav_icon_image;
} else {
    if (file_exists($tconfig['tpanel_path'].$logogpath.$fav_icon_image)) {
        $fav_icon_image = $tsiteUrl.$logogpath.$fav_icon_image;
    } else {
        $fav_icon_image = $tsiteUrl.$fav_icon_image;
    }
}
$adminUrl = $tconfig['tsite_url_main_admin'];
$serviceArray = $serviceIdArray = [];
$serviceArray = json_decode(serviceCategories, true);
$serviceIdArray = array_column($serviceArray, 'iServiceId');
$become_restaurant = '';
if ('YES' === strtoupper(DELIVERALL)) {
    if (1 === count($serviceIdArray) && 1 === $serviceIdArray[0]) {
        $become_restaurant = $langage_lbl['LBL_RESTAURANT_TXT'];
    } else {
        $become_restaurant = $langage_lbl['LBL_STORE'];
    }
}
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8"/>
    <title>Admin | Login Page</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <link rel="icon" href="<?php echo $fav_icon_image; ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?php echo $adminUrl; ?>css/bootstrap.css"/>
    <link rel="stylesheet" href="<?php echo $adminUrl; ?>css/login.css"/>
    <link rel="stylesheet" href="<?php echo $adminUrl; ?>css/style.css"/>
    <link rel="stylesheet" href="<?php echo $tsiteUrl; ?>assets/css/animate/animate.min.css"/>
    <link rel="stylesheet" href="<?php echo $tsiteUrl; ?>assets/plugins/magic/magic.css"/>
    <link rel="stylesheet" href="<?php echo $adminUrl; ?>css/font-awesome.css"/>
    <link rel="stylesheet" href="<?php echo $tsiteUrl; ?>assets/plugins/font-awesome-4.6.3/css/font-awesome.min.css"/>
    <link href="https://fonts.googleapis.com/css?family=Exo+2:100,300,400,500,600,700&display=swap" rel="stylesheet">
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="nobg loginPage">
<input type="hidden" name="hdf_class" id="hdf_class" value="<?php echo $_SESSION['edita']; ?>">
<div class="topNav">
    <div class="userNav">
        <ul>
            <li>
                <a href="<?php echo $tsiteUrl; ?>index.php" title="">
                    <i class="icon-reply"></i>
                    <span>Main website</span>
                </a>
            </li>
            <li>
                <a href="<?php echo $tsiteUrl.$cjRiderLogin; ?>" title="">
                    <i class="icon-user"></i>
                    <span><?php echo $langage_lbl_admin['LBL_RIDER']; ?> Login</span>
                </a>
            </li>
            <li>
                <a href="<?php echo $tsiteUrl.$cjDriverLogin; ?>" title="">
                    <i class="icon-comments"></i>
                    <span><?php echo $langage_lbl_admin['LBL_DRIVER']; ?> Login</span>
                </a>
            </li>
            <?php if ('YES' !== strtoupper(ONLYDELIVERALL) && false === $cubeDeliverallOnly) {
                $labelCompany = $langage_lbl_admin['LBL_COMPANY_SIGNIN'];
                if (DELIVERALL === 'Yes' && '' === $become_restaurant) {
                    $labelCompany = $langage_lbl_admin['LBL_COMPANY'];
                }
                ?>
                <li>
                    <a href="<?php echo $tsiteUrl.$cjCompanyLogin; ?>" title="">
                        <i class="icon-home"></i>
                        <span><?php echo $labelCompany; ?> Login</span>
                    </a>
                </li>
            <?php }
            if (!empty($become_restaurant)) {
                $serviceArray = $serviceIdArray = [];
                $serviceArray = json_decode(serviceCategories, true);
                $serviceIdArray = array_column($serviceArray, 'iServiceId');
                $become_restaurant = '';
                if ('YES' === strtoupper(DELIVERALL)) {
                    if (1 === count($serviceIdArray) && 1 === $serviceIdArray[0]) {
                        $become_restaurant = $langage_lbl['LBL_RESTAURANT_TXT'];
                        $slink = 'sign-in?type=restaurant';
                    } else {
                        $become_restaurant = $langage_lbl['LBL_STORE'];
                        $slink = 'sign-in?type=store';
                    }
                }
                ?>
                <li>
                    <a href="<?php echo $tsiteUrl.$slink; ?>" title="">
                        <i class="icon-home"></i>
                        <span><?php echo $become_restaurant; ?> Login</span>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>
<!-- PAGE CONTENT -->
<div class="container animated fadeInDown">
    <div class="text-center admin-logo">
        <img src="<?php echo $admin_logo; ?>" id="Admin" alt=" Admin"/>
    </div>
    <?php if ('hotel' === $userType) { ?>
        <div id="login">
            <p style="display:none; padding:5px 0;" class="btn-block btn btn-rect btn-success" id="success"></p>
            <?php
            if (isset($_SESSION['checkadminmsg']) && !empty($_SESSION['checkadminmsg'])) {
                $msg = $_SESSION['checkadminmsg'];
                echo ' <div class="data-msg-center"><p style="display:block;" class="btn-block btn btn-rect btn-danger errormsg text-muted text-center errormsg" id="errmsg">'.$msg.' </p></div>';
                unset($_SESSION['checkadminmsg']);
            } else {
                ?>
                <div class="data-msg-center">
                    <p style="display:none;" class="btn-block btn btn-rect btn-danger text-muted text-center"
                       id="errmsg"></p>
                </div>
            <?php } ?>
            <div class="admin-home-tab">
                <div class="tab-content clearfix custom-tab">
                    <h4>Hotel Administrator</h4>
                    <div>
                        <form action="" class="form-signin" method="post" id="login_box" onSubmit="return chkValid();"
                              style="margin:0 auto;border:0;">
                            <br>
                            <b>
                                <label for="email">Hotel Administrator E-mail</label>
                                <input type="text" placeholder="Email Address" class="form-control" name="vEmail"
                                       id="vEmail" required Value=""/>
                            </b>
                            <b>
                                <label for="password">Password</label>
                                <input type="password" placeholder="Password" class="form-control" name="vPassword"
                                       id="vPassword" required Value=""/>
                            </b>
                            <input type="hidden" name="group_id" id="group_id" value="4"/>
                            <input type="submit" class="btn text-muted text-center btn-default" value="SIGN IN"/>
                            <br>
                        </form>
                    </div>
                </div>
                <?php if (SITE_TYPE === 'Demo') { ?>
                    <!--<div class="tab-content">

                        <div id="super001" class="tab-pane active">

                            <h3> Use below Detail for Demo Version</h3>

                            <p><b>User Name:</b> hoteladmin@demo.com</p>

                            <p><b>Password:</b> 123456 </p>

                            <p>Hotel Administrator can book taxi.</p>

                        </div>

                    </div>-->
                <?php } ?>
                <div style="clear:both;"></div>
            </div>
        </div>
    <?php } else { ?>
        <div class="tab-content">
            <div id="login" class="tab-pane active">
                <p style="display:none; padding:5px 0;" class="btn-block btn btn-rect btn-success" id="success"></p>
                <?php
                if (isset($_SESSION['checkadminmsg']) && !empty($_SESSION['checkadminmsg'])) {
                    $msg = $_SESSION['checkadminmsg'];
                    echo '<div class="data-msg-center"><p style="display:block;" class="btn-block btn btn-rect btn-danger text-muted text-center errormsg" id="errmsg">'.$msg.' </p></div>';
                    unset($_SESSION['checkadminmsg']);
                } else {
                    ?>
                    <div class="data-msg-center">
                        <p style="display:none;"
                           class="btn-block btn btn-rect btn-danger text-muted text-center errormsg" id="errmsg"></p>
                    </div>
                <?php } ?>
                <!--

                        <form action="" class="form-signin" method = "post" id="login_box" onSubmit="return chkValid();">

                        <p class="head_login_005">Login</p>

                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>

                        <input type="text" placeholder="Email Address" class="form-control" name="vEmail" id="vEmail" required />

                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>

                        <input type="password" placeholder="Password" class="form-control" name="vPassword" id="vPassword" required />

                        <input type="submit" class="btn text-muted text-center btn-default" value="SIGN IN"/>

                        <br>

                </form>-->
                <div class="admin-home-tab">
                    <ul class="nav nav-tabs">
                        <li <?php if ('admin' === $userType) { ?>class="active"<?php } ?> onclick="setAdminType('1')">
                            <a data-toggle="tab" href="#super001">All
                                <br>
                                Administrator
                            </a>
                        </li>
                        <li onclick="setAdminType('2')">
                            <a data-toggle="tab" href="#dispatch001">Dispatcher Administrator</a>
                        </li>
                        <li <?php if ('billing' === $userType) { ?>class="active"<?php } ?> onclick="setAdminType('3')">
                            <a data-toggle="tab" class="active" href="#billing001">Billing Administrator</a>
                        </li>
                    </ul>
                    <div class="tab-content clearfix custom-tab">
                        <div class="tab-pane active" id="super001">
                            <form action="" class="form-signin" method="post" id="login_box"
                                  onSubmit="return chkValid();" style="margin:0 auto;border:0;">
                                <br>
                                <b>
                                    <label for="email">Administrator E-mail</label>
                                    <input type="text" placeholder="Email Address" class="form-control" name="vEmail"
                                           id="vEmail" required Value=""/>
                                </b>
                                <b>
                                    <label for="password">Password</label>
                                    <input type="password" placeholder="Password" class="form-control" name="vPassword"
                                           id="vPassword" required Value=""/>
                                </b>
                                <input type="hidden" name="group_id" id="group_id" value="1"/>
                                <input type="submit" class="btn text-muted text-center btn-default" value="SIGN IN"/>
                                <br>
                            </form>
                        </div>
                    </div>
                    <?php if (SITE_TYPE === 'Demo') { ?>
                        <!--<div class="tab-content">

                            <div id="super001admin" class="tab-pane active">

                                <h3> Use below Detail for Demo Version</h3>

                                <p><b>User Name:</b> demo@demo.com</p>

                                <p><b>Password:</b> 123456 </p>

                                <p>Super Administrator can manage whole system and other user's rights too.</p>

                            </div>

                            <div id="dispatch001admin" class="tab-pane">

                                <h3> Use below Detail for Demo Version</h3>

                                <p><b>User Name:</b> demo2@demo.com</p>

                                <p><b>Password:</b> 123456 </p>

                                <p>Call Center Panel / Administrator Dispatcher Panel / Manual Taxi Booking Panel. This panel allows one to see all taxi's on map using God's View. And book taxi's for customer's who would call to book a taxi.</p>

                            </div>

                            <div id="billing001admin" class="tab-pane">

                                <h3> Use below Detail for Demo Version</h3>

                                <p><b>User Name:</b> demo3@demo.com</p>

                                <p><b>Password:</b> 123456 </p>

                                <p>This use will have access to reports only. Will be used by Accounts Team to manage finances and see profits/revenue.</p>

                            </div>

                        </div>-->
                    <?php } ?>
                    <div style="clear:both;"></div>
                </div>
            </div>
            <div id="forgot" class="tab-pane">
                <form class="form-signin" method="post" id="frmforget">
                    <input type="email" required="required" placeholder="Your E-mail" class="form-control" id="femail"/>
                    <br/>
                    <button class="btn text-muted text-center btn-success" type="submit" onClick="forgotPass();">Recover
                        Password
                    </button>
                </form>
            </div>
        </div>
    <?php } ?>
</div>
<!--END PAGE CONTENT -->
<?php if (SITE_TYPE === 'Demo') { ?>
    <section class="warning">
        <div class="warning-inner">
            <div class="warning-caption">
                <h3>
                    <span class="blink">Warning!</span>
                </h3>
                <strong>This system is Designed and Developed by
                    <span><a href="https://www.v3cube.com/" target="_blank">V3Cube.com.</a></span>
                </strong>
                <strong>
                    <span><a href="https://www.v3cube.com/" target="_blank">V3Cube.com</a></span>
                    holds 100% selling rights for this system.
                </strong>
                <br>
                <p>V3Cube is located in the Western part of India.</p>
                <p>We do
                    <span>NOT</span>
                    have any Representatives, Resellers or Partner companies anywhere in the world;
                </p>
                <p>Please email at
                    <a href="mailto:sales@v3cube.com" target="_blank">"sales@v3cube.com"</a>
                    if someone claims this <?php echo PROJECT_SITE_NAME; ?> system to be his and is planning to sell it to you.
                </p>
                <p>He is probably a
                    <span>scammer.</span>
                </p>
                <strong>This system belongs to
                    <span><a href="https://www.v3cube.com/" target="_blank">V3Cube.com</a></span>
                    and its below listed sister concerns.
                </strong>
                <div class="button-block">
                    <a href="https://www.cubeTaxi.com/" target="_blank">
                        <button style="margin-bottom: 10px;">CubeTaxi.com</button>
                    </a>
                    <a href="https://www.esiteworld.com/" target="_blank">
                        <button style="margin-bottom: 10px;">eSiteWorld.com</button>
                    </a>
                    <a href="https://www.gojekclone.com/" target="_blank">
                        <button style="margin-bottom: 10px;">GojekClone.com</button>
                    </a>
                </div>
            </div>
        </div>
    </section>
<?php } ?>
<!-- PAGE LEVEL SCRIPTS -->
<script src="<?php echo $tsiteUrl; ?>assets/plugins/jquery-2.0.3.min.js"></script>
<script src="<?php echo $tsiteUrl; ?>assets/plugins/bootstrap/js/bootstrap.js"></script>
<script src="<?php echo $tsiteUrl; ?>assets/js/login.js"></script>
<script src="<?php echo $tsiteUrl; ?>assets/js/getDataFromApi.js"></script>
<script>
    var testLink = '<?php echo $_SESSION['current_link']; ?>';



    <?php if ('hotel' !== $userType) { ?>

    $(document).ready(function () {

        passLoginid('<?php echo $activeTabId; ?>', '<?php echo $activeTab; ?>');

        setCredentials('<?php echo $activeTab; ?>', '<?php echo SITE_TYPE; ?>');

    });

    <?php } ?>

    function setCredentials(tpd, site_type) {

        if (site_type == "Demo") {

            //if (tpd == 2) {

            //    $("#vEmail").val('demo2@demo.com');

            //    $("#vPassword").val('123456');

            //} else if (tpd == 3)

            //{

            //    $("#vEmail").val('demo3@demo.com');

            //    $("#vPassword").val('123456');

            //} else

            //{

            //    $("#vEmail").val('demo@demo.com');

            //    $("#vPassword").val('123456');

            //}

        }

    }

    function passLoginid(tabid, login_group_id) {

        $(".custom-tab .tab-pane").attr('id', tabid);

        $("#group_id").val(login_group_id);

        $("#super001admin,#dispatch001admin,#billing001admin").hide();

        if (tabid == "dispatch001") {

            $("#dispatch001admin").show();

            $("label[for = email]").text("Dispatcher Administrator E-mail");

        } else if (tabid == "billing001") {

            $("#billing001admin").show();

            $("label[for = email]").text("Billing Administrator E-mail");

        } else {

            $("#super001admin").show();

            $("label[for = email]").text("Administrator E-mail");

        }

    }

    $('input').keyup(function () {

        $this = $(this);

        if ($this.val().length == 1) {

            var x = new RegExp("[\x00-\x80]+"); // is ascii

            var isAscii = x.test($this.val());

            if (isAscii) {

                $this.attr("dir", "ltr");

            } else {

                $this.attr("dir", "rtl");

            }

        }

    });

    function change_heading(heading, addClass, removeClass) {

        document.getElementById("login").innerHTML = heading;

        document.getElementById(addClass).className = "tab-pane";

        document.getElementById(removeClass).className = "tab-pane active";

    }

    function chkValid() {

        var id = document.getElementById("vEmail").value;

        var pass = document.getElementById("vPassword").value;

        if (id == '' || pass == '') {

            document.getElementById("errmsg").style.display = '';

            setTimeout(function () {

                document.getElementById('errmsg').style.display = 'none';

            }, 2000);

        } else {

            var ajaxData = {

                'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_login_action.php',

                'AJAX_DATA': $("#login_box").serialize(),

            };

            getDataFromAjaxCall(ajaxData, function (response) {

                if (response.action == "1") {

                    var dataHTml = response.result;

                    dataHTml = dataHTml.trim();

                    if (dataHTml == 1) {

                        document.getElementById("errmsg").innerHTML = 'You are not active.Please contact administrator to activate your account.';

                        document.getElementById("errmsg").style.display = '';

                        return false;

                    } else if (dataHTml == 2) {

                        document.getElementById("errmsg").style.display = 'none';

                        var hdf_class = $("#hdf_class").val();

                        if (hdf_class != "") {

                            window.location = "<?php echo $adminUrl; ?>languages.php";

                        } else {

                            <?php

                            // added by SP for redirection on admin after login on 15-7-2019

                            $redirecturl = explode('/', $_SESSION['login_redirect_url']);

if (!empty($redirecturl[count($redirecturl) - 1])) {
    if ('dashboard.php' === $redirecturl[count($redirecturl) - 1] && (ONLYDELIVERALL === 'Yes' || $cubeDeliverallOnly > 0)) {
        $dashboardLink = $adminUrl.'store-dashboard.php';
    } elseif ('store-dashboard.php' === $redirecturl[count($redirecturl) - 1] && (ONLYDELIVERALL === 'No' && false === $cubeDeliverallOnly)) {
        $dashboardLink = $adminUrl.'dashboard.php';
    } elseif ('ajax_check_server_requirements.php' === $redirecturl[count($redirecturl) - 1]) {
        $dashboardLink = (ONLYDELIVERALL === 'Yes' || $cubeDeliverallOnly > 0) ? $adminUrl.'store-dashboard.php' : $adminUrl.'dashboard.php';
    } elseif ('userbooking.php?userType1=admin' === $redirecturl[count($redirecturl) - 1]) { // added by SP because it will be redirect to the mainurl instead of admin url
        $dashboardLink = $tconfig['tsite_url'].'userbooking.php?userType1=admin';
    } else {
        $dashboardLink = $adminUrl.$redirecturl[count($redirecturl) - 1];
    }
} else {
    $dashboardLink = (ONLYDELIVERALL === 'Yes' || $cubeDeliverallOnly > 0) ? $adminUrl.'store-dashboard.php' : $adminUrl.'dashboard.php';
}

$_SESSION['dashboardLink'] = $dashboardLink;

if ('192.168.1.131' === $_SERVER['HTTP_HOST'] || '192.168.1.141' === $_SERVER['HTTP_HOST'] || '192.168.1.151' === $_SERVER['HTTP_HOST']/*  || $_SERVER["HTTP_HOST"] == "www.mobileappsdemo.com" || $_SERVER["HTTP_HOST"] == "www.webprojectsdemo.com" || $_SERVER["HTTP_HOST"] == "mobileappsdemo.com" || $_SERVER["HTTP_HOST"] == "webprojectsdemo.com" || $_SERVER["HTTP_HOST"] == "mobileappsdemo.net" || $_SERVER["HTTP_HOST"] == "www.mobileappsdemo.net" */) {
    $dashboardLink = $adminUrl.'project_setup.php';
}

?>

                            if (testLink == "") {

                                testLink = "<?php echo $dashboardLink; ?>";

                            }

                            window.location = testLink;

                        }

                        return true; // success registration

                    } else if (dataHTml == 3) {

                        document.getElementById("errmsg").innerHTML = 'Invalid combination of username & Password';

                        document.getElementById("errmsg").style.display = '';

                        //return false;

                    } else {

                        document.getElementById("errmsg").innerHTML = 'Invalid Email or Password';

                        document.getElementById("errmsg").style.display = '';

                    }

                    if ($('#errmsg').html() != '') {

                        setTimeout(function () {

                            $('#errmsg').fadeOut();

                        }, 2000);

                    }

                } else {

                    console.log(response.result);

                }

            });

        }

        return false;

    }

    function forgotPass() {

        var id = document.getElementById("femail").value;

        if (id == '') {

            document.getElementById("errmsg_email").style.display = '';

            document.getElementById("errmsg_email").innerHTML = 'Please enter Email Address';

            return false;

        } else {

            var ajaxData = {

                'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_fpass_action.php',

                'AJAX_DATA': $("#frmforget").serialize(),

            };

            getDataFromAjaxCall(ajaxData, function (response) {

                if (response.action == "1") {

                    var data = response.result;

                    if (data == 1) {

                        document.getElementById("page_title").innerHTML = "Login";

                        document.getElementById("forgot").className = "tab-pane";

                        document.getElementById("login").className = "tab-pane active";

                        document.getElementById("success").innerHTML = 'Your Password has been sent Successfully.';

                        document.getElementById("success").style.display = '';

                        return false;

                    } else if (data == 0) {

                        document.getElementById("errmsg_email").innerHTML = 'Error in Sending Password.';

                        document.getElementById("errmsg_email").style.display = '';

                        return false;

                    } else if (data == 3) {

                        document.getElementById("errmsg_email").innerHTML = 'Sorry ! The Email address you have entered is not found.';

                        document.getElementById("errmsg_email").style.display = '';

                        return false;

                    }

                    return false;

                } else {

                    console.log(response.result);

                }

            });

            return false;

        }

        return false;

    }


    function setAdminType(group_id) {

        $('#group_id').val(group_id);

    }
</script>
<!--END PAGE LEVEL SCRIPTS -->
</body>
<!-- END BODY -->
<!-- Powered by V3Cube.com -->
</html>

