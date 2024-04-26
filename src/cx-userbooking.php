<?php

    use Admin\library\User;

    include_once 'common.php';

if ('admin' === $_REQUEST['userType1'] && empty($_SESSION['sess_iAdminUserId'])) {
    $_SESSION['login_redirect_url'] = $tconfig['tsite_url_main_admin'];
    header('location: '.$tconfig['tsite_url_main_admin']);

    exit;
}

if ('admin' !== $_REQUEST['userType1']) {
    $AUTH_OBJ->checkManualTaxiMemberAuthentication();
/* $abc = 'company';
  $url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  setRole($abc, $url); */
} else {
    if (!isset($userObj) || empty($userObj)) {
        include_once $tconfig['tpanel_path'].'/'.SITE_ADMIN_URL.'/library/common_include.php';
        $userObj = new User();
    }

    if (!$userObj->hasPermission('create-manage-manual-booking') && !$userObj->hasPermission('manage-create-request')) {
        $userObj->redirect();
    }
}

$script = 'booking';
?>
<!DOCTYPE html>
<html lang="en" dir="<?php echo (isset($_SESSION['eDirectionCode']) && '' !== $_SESSION['eDirectionCode']) ? $_SESSION['eDirectionCode'] : 'ltr'; ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">

        <title><?php echo $SITE_NAME; ?> | <?php echo $langage_lbl['LBL_MANUAL_TAXI_DISPATCH']; ?></title>

        <link rel="stylesheet" href="assets/plugins/bootstrap/css/bootstrap.css" /><!-- This is only used for calendar only-->
        <!-- Default Top Script and css -->
        <?php include_once 'top/top_script.php'; ?>
        <?php include_once 'top/validation.php'; ?>
        <!-- End: Default Top Script and css-->
        <link rel="stylesheet" href="assets/css/MoneAdmin.css" />
        <link rel="stylesheet" href="assets/plugins/Font-Awesome/css/font-awesome.css" />
        <link rel="stylesheet" href="assets/plugins/Font-Awesome/css/font-awesome_new.css" />
        <!--<link rel="stylesheet" href="assets/css/manualstyle.css" />-->
        <link rel="stylesheet" href="assets/css/apptype/<?php echo $template; ?>/manualstyle.css" />
        <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script> -->
        <script src="https://maps.google.com/maps/api/js?sensor=true&key=<?php echo $GOOGLE_SEVER_API_KEY_WEB; ?>&libraries=places" type="text/javascript"></script>
        <script type='text/javascript' src='assets/map/gmaps.js'></script>
        <script type='text/javascript' src='assets/js/bootbox.min.js'></script>
        <link href="assets/css/jquery.mCustomScrollbar.css" rel="stylesheet" />
        <script src="assets/js/jquery.mCustomScrollbar.concat.min.js"></script>

        <script>
            (function ($) {
                $(window).on('load', function(){
                    $(".content").mCustomScrollbar({
                        scrollInertia: 400,
                        scrollButtons: {
                            enable: true,
                        },
                        advanced: {
                            updateOnBrowserResize: true, /*update scrollbars on browser resize (for layouts based on percentages): boolean*/
                            updateOnContentResize: true, /*auto-update scrollbars on content resize (for dynamic content): boolean*/autoExpandHorizontalScroll: true, /*auto-expand width for horizontal scrolling: boolean*/
                        },
                        autoHideScrollbar: true
                    });
                    /* disable */
                    $("#disable-scrollbar").click(function (e) {
                        e.preventDefault();
                        $("#content_1").mCustomScrollbar("disable", true);
                    });
                    $("#disable-scrollbar-no-reset").click(function (e) {
                        e.preventDefault();
                        $("#content_1").mCustomScrollbar("disable");
                    });
                    $("#enable-scrollbar").click(function (e) {
                        e.preventDefault();
                        $("#content_1").mCustomScrollbar("update");
                    });
                    /* destroy */
                    $("#destroy-scrollbar").click(function (e) {
                        e.preventDefault();
                        $("#content_1").mCustomScrollbar("destroy");
                    });
                    $("#rebuild-scrollbar").click(function (e) {
                        e.preventDefault();
                        $("#content_1").mCustomScrollbar({
                            scrollButtons: {
                                enable: true
                            }
                        });
                    });
                });
            })(jQuery);
        </script>
    </head>
    <body>
        <!-- home page -->
        <div id="main-uber-page">
            <!-- Left Menu -->
            <?php include_once 'top/left_menu.php'; ?>
            <!-- End: Left Menu-->
            <!-- Top Menu -->
            <?php include_once 'top/header_topbar.php'; ?>
            <!-- End: Top Menu-->
            <!--<div class="page-contant1">-->
            <div class="page-contant-inner1">
                <?php
                if (isset($_REQUEST['ufxservice'])) {
                    include_once 'booking_ufxservice/cx-add_booking.php';
                } elseif ($MODULES_OBJ->isEnableMultiDeliveryInBooking()) {
                    include_once 'booking/cx-add_booking_multidelivery.php';
                } else {
                    include_once 'booking/cx-add_booking.php';
                }
?>
            </div>
            <!--</div>-->
            <!-- footer part -->
            <?php
            if (empty($_SESSION['sess_iAdminUserId']) && str_contains($_SERVER['SCRIPT_FILENAME'], 'userbooking.php')) {
                include_once 'footer/footer_home.php';
            }
?>
            <!-- footer part end -->
            <!-- End:contact page-->
            <div style="clear:both;"></div>
        </div>
        <!-- home page end-->
        <!-- Footer Script -->
<?php include_once 'top/footer_script.php'; ?>
    </body>
</html>