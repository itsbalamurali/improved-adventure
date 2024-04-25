<?php
// $APP_DELIVERY_MODE = $CONFIG_OBJ->getConfigurations("configurations", "APP_DELIVERY_MODE");
// $RIDE_LATER_BOOKING_ENABLED = $CONFIG_OBJ->getConfigurations("configurations", "RIDE_LATER_BOOKING_ENABLED");
// $DRIVER_SUBSCRIPTION_ENABLE = $CONFIG_OBJ->getConfigurations("configurations", "DRIVER_SUBSCRIPTION_ENABLE");
$catdata = serviceCategories;
$allservice_cat_data = json_decode($catdata, true);
// echo ONLYDELIVERALL ;
// Added By HJ On 16-10-2019 For Get New Feature Configuratio  Start
$getSetupData = $obj->MySQLSelect('SELECT lAddOnConfiguration FROM setup_info');
// echo "<pre>";print_r($getSetupData);die;
$DONATION = $DRIVER_DESTINATION = $FAVOURITE_DRIVER = $FAVOURITE_STORE = $DRIVER_SUBSCRIPTION = $GOJEK_GOPAY = $MULTI_STOPOVER_POINTS = $MANUAL_STORE_ORDER_WEBSITE = $MANUAL_STORE_ORDER_STORE_PANEL = $MANUAL_STORE_ORDER_ADMIN_PANEL = 'No';
if (isset($getSetupData[0]['lAddOnConfiguration'])) {
    $addOnData = json_decode($getSetupData[0]['lAddOnConfiguration'], true);
    foreach ($addOnData as $key => $val) {
        ${$key} = $val;
    }
    // echo "<pre>";print_r($addOnData);die;
}
$leftcubexthemeon = $leftcubejekxthemeon = $leftufxserviceon = $leftdeliverallxthemeon = $leftridedeliveryxthemeon = $leftdeliveryxthemeon = $leftservicexthemeon = $leftridecxthemeon = $leftdeliverykingthemeon = $leftcubejekxv3themeon = $leftmedicalservicethemeon = 0;
if ('Yes' === $THEME_OBJ->isCubexThemeActive() || 'Yes' === $THEME_OBJ->isCubeXv2ThemeActive()) {
    $leftcubexthemeon = 1;
}
if ('Yes' === $THEME_OBJ->isCubeJekXThemeActive() || 'Yes' === $THEME_OBJ->isCubeJekXv2ThemeActive() || 'Yes' === $THEME_OBJ->isCJXDoctorv2ThemeActive()) {
    $leftcubejekxthemeon = 1;
}
if ('Yes' === $THEME_OBJ->isCubeJekXv3ThemeActive() || 'Yes' === $THEME_OBJ->isCubeJekXv3ProThemeActive()) {
    $leftcubejekxv3themeon = 1;
}
if ('Yes' === $THEME_OBJ->isRideCXThemeActive() || 'Yes' === $THEME_OBJ->isRideCXv2ThemeActive()) {
    $leftridecxthemeon = 1;
}
if ('Yes' === $THEME_OBJ->isDeliverallXThemeActive()) {
    $leftdeliverallxthemeon = 1;
}
if ('Yes' === $THEME_OBJ->isRideDeliveryXThemeActive()) {
    $leftridedeliveryxthemeon = 1;
}
if ('Yes' === $THEME_OBJ->isDeliveryXThemeActive() || 'Yes' === $THEME_OBJ->isDeliveryXv2ThemeActive()) {
    $leftdeliveryxthemeon = 1;
}
if ('Yes' === $THEME_OBJ->isDeliveryKingThemeActive() || 'Yes' === $THEME_OBJ->isDeliveryKingXv2ThemeActive()) {
    $leftdeliverykingthemeon = 1;
}
if ('Yes' === $THEME_OBJ->isServiceXThemeActive() || 'Yes' === $THEME_OBJ->isServiceXv2ThemeActive()) {
    $leftservicexthemeon = 1;
}
if ('Yes' === $THEME_OBJ->isMedicalServicev2ThemeActive()) {
    $leftmedicalservicethemeon = 1;
}
$leftufxserviceon = $MODULES_OBJ->isUberXFeatureAvailable('Yes') ? 1 : 0; // add function to modules availibility
$leftrideEnable = $MODULES_OBJ->isRideFeatureAvailable('Yes') ? 'Yes' : 'No';
$leftdeliveryEnable = $MODULES_OBJ->isDeliveryFeatureAvailable('Yes') ? 'Yes' : 'No';
$leftdeliverallEnable = $MODULES_OBJ->isDeliverAllFeatureAvailable('Yes') ? 'Yes' : 'No';
// Added By HJ On 16-10-2019 For Get New Feature Configuratio  End
if ('UberX' === $APP_TYPE) {
    if ($MODULES_OBJ->isEnableAdminPanelV3Pro()) {
        $menu = include 'left_menu_uberapp_array_v3_pro.php';
    } elseif ($MODULES_OBJ->isEnableAdminPanelV2()) {
        $menu = include 'left_menu_ufx_array_v3.php';
    } else {
        $menu = include 'left_menu_ufx_array.php';
    }
} elseif (ONLYDELIVERALL === 'Yes') {
    if ($MODULES_OBJ->isEnableAdminPanelV3Pro()) {
        $menu = include 'left_menu_uberapp_array_v3_pro.php';
    } elseif ($MODULES_OBJ->isEnableAdminPanelV2()) {
        $menu = include 'left_menu_deliverall_array_v3.php';
    } else {
        $menu = include 'left_menu_deliverall_array.php';
    }
} else {
    if ($MODULES_OBJ->isEnableAdminPanelV3Pro()) {
        $menu = include 'left_menu_uberapp_array_v3_pro.php';
    } elseif ($MODULES_OBJ->isEnableAdminPanelV2()) {
        $menu = include 'left_menu_uberapp_array_v3.php';
    } else {
        $menu = include 'left_menu_uberapp_array.php';
    }
}
?>
<section class="sidebar">
    <!-- Sidebar -->
    <div id="sidebar" class="test">
        <nav class="menu">
            <?php echo get_admin_nav($menu); ?>
        </nav>
    </div>
</section>
<script type="text/javascript">
    function checkHotelAddress(elem) {

        var ajaxData = {

            'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>checkhoteladdress.php',

            'AJAX_DATA': {adminId: '<?php echo $_SESSION['sess_iAdminUserId']; ?>'},

        };

        getDataFromAjaxCall(ajaxData, function (response) {

            if (response.action == "1") {

                var data = response.result;

                if (data != "") {

                    data = JSON.parse(data);

                    if (data[0].vAddress == "" || data[0].vAddressLat == "" || data[0].vAddressLong == "") {

                        alert('<?php echo $langage_lbl_admin['LBL_ADD_ADDRESS_NOTE']; ?>');

                        return false;

                    }

                } else {

                    alert("Hotel not found.");

                    return false;

                }

            } else {

                console.log(response.result);

            }

        });

    }
</script>