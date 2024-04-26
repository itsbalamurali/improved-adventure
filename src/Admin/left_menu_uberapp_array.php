<?php


// added by SP as discussed with bmam on 28-6-2019
$adminUsersTxt = $langage_lbl_admin['LBL_ADMIN'];
// Added By HJ On 16-06-2020 For Custome App Type CubejekX-Deliverall As Per Dicsuss With KS Start
$cubeDeliverallOnly = $MODULES_OBJ->isOnlyDeliverAllSystem();
$onlyDeliverallModule = strtoupper(ONLYDELIVERALL);
// $deliverallModule = strtoupper(DELIVERALL);
$deliverallModule = $MODULES_OBJ->isDeliverAllFeatureAvailable() ? 'YES' : 'NO';
if ($cubeDeliverallOnly > 0) {
    $onlyDeliverallModule = 'YES';
}

// Added By HJ On 16-06-2020 For Custome App Type CubejekX-Deliverall As Per Dicsuss With KS End
if ('SHARK' === $PACKAGE_TYPE && ('Ride' === $APP_TYPE || 'Ride-Delivery-UberX' === $APP_TYPE) && 'NO' === $onlyDeliverallModule) {
    $adminUsersTxt = $langage_lbl_admin['LBL_ADMIN'];
    if ('YES' === strtoupper(ENABLEHOTELPANEL)) { // added by SP to chk hotel panel enable then only shown word at admin side.
        $adminUsersTxt .= '/Hotel';
    }
}
// Added By HJ On 15-06-2020 For Custome Setup - CubejekX-Deliverall As Per Discuss With KS - Manage Service Menu End
$addOnsDataArr = $obj->MySQLSelect('SELECT lAddOnConfiguration,eCubejekX,eCubeX,eRideX,eDeliverallX FROM setup_info LIMIT 0,1');
$addOnsDataArr_orig = $addOnsDataArr;

$addOnData = json_decode($addOnsDataArr[0]['lAddOnConfiguration'], true);
$eCubeX = $eCubejekX = $eRideX = $eDeliverallX = 'No';
if (isset($addOnsDataArr[0]['eCubeX']) && '' !== $addOnsDataArr[0]['eCubeX']) {
    $eCubeX = $addOnsDataArr[0]['eCubeX'];
}
if (isset($addOnsDataArr[0]['eCubejekX']) && '' !== $addOnsDataArr[0]['eCubejekX']) {
    $eCubejekX = $addOnsDataArr[0]['eCubejekX'];
}
if (isset($addOnsDataArr[0]['eRideX']) && '' !== $addOnsDataArr[0]['eRideX']) {
    $eRideX = $addOnsDataArr[0]['eRideX'];
}
if (isset($addOnsDataArr[0]['eDeliverallX']) && '' !== $addOnsDataArr[0]['eDeliverallX']) {
    $eDeliverallX = $addOnsDataArr[0]['eDeliverallX'];
}
if ('YES' === strtoupper($eCubejekX) || 'YES' === strtoupper($eCubeX) || 'YES' === strtoupper($eDeliverallX)) {
    foreach ($addOnData as $addOnKey => $addOnVal) {
        ${$addOnKey} = $addOnVal;
    }
}
// Added By HJ On 15-06-2020 For Custome Setup - CubejekX-Deliverall As Per Discuss With KS - Manage Service Menu End
// var_dump($langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']);
// var_dump($langage_lbl_admin);
$restaurantAdmin = 'Store';
if (isset($langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN'])) {
    $restaurantAdmin = $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN'];
}

$hotelPanel = $MODULES_OBJ->isEnableHotelPanel();
$kioskPanel = $MODULES_OBJ->isEnableKioskPanel();

$foodCategoryAvailable = 'No';
if (count($service_categories_ids_arr) >= 1 && in_array(1, $service_categories_ids_arr, true)) {
    $foodCategoryAvailable = 'Yes';
}

$menu = [
    [
        'title' => 'Dashboard',
        'url' => 'dashboard.php',
        'icon' => 'fa fa-tachometer',
        'visible' => ('NO' === $onlyDeliverallModule),
    ],
    [
        'title' => 'Dashboard',
        'url' => 'store-dashboard.php',
        'icon' => 'fa fa-tachometer',
        'visible' => ('YES' === $onlyDeliverallModule),
    ],
    [
        'title' => 'Site Statistics',
        'url' => 'dashboard-a.php',
        'icon' => 'fa fa-sitemap',
        'active' => 'site',
        'visible' => ($userObj->hasPermission('view-site-statistics') && 'NO' === $onlyDeliverallModule),
    ], [
        'title' => 'Profile',
        'url' => 'profile.php',
        'icon' => ['class' => 'icon-user1', 'url' => 'images/icon/admin-icon.png'],
        'active' => 'profile',
        'visible' => $userObj->hasPermission('manage-profile') && '1' !== $_SESSION['sess_iGroupId'] /* && $APP_TYPE == 'Ride' */ && ($hotelPanel > 0 || $kioskPanel > 0),
    ], [
        'title' => 'Create request',
        'url' => 'create_request.php',
        'icon' => ['class' => 'icon-user1 hotel-create-request', 'url' => 'images/manual-taxi-icon.png'],
        'active' => 'booking',
        'visible' => $userObj->hasPermission('manage-create-request') && '1' !== $_SESSION['sess_iGroupId'] /* && $APP_TYPE == 'Ride' */ && ($hotelPanel > 0 || $kioskPanel > 0),
        'target' => 'blank',
    ], [
        'title' => 'Admin',
        'url' => 'javascript:',
        'icon' => ['class' => 'icon-user1', 'url' => 'images/icon/admin-icon.png'],
        'visible' => ($userObj->hasRole(1) || $userObj->hasPermission('view-admin')),
        'children' => [
            [
                'title' => $adminUsersTxt,
                'url' => 'admin.php',
                'icon' => 'fa fa-certificate',
                'active' => 'Admin',
            ], [
                'title' => 'Admin Groups',
                'url' => 'admin_groups.php',
                'icon' => 'fa fa-certificate',
                'active' => 'AdminGroups',
                'visible' => $userObj->hasRole(1) && 'SHARK' === $PACKAGE_TYPE,
            ], /* [
        'title' => 'Permissions',
        'url' => "admin_permissions.php",
        "icon" => "fa fa-certificate",
        "active" => "AdminPermissions",
        "visible" => $userObj->hasRole(1),
        ], */
        ],
    ], [
        'title' => 'Company',
        'url' => 'company.php',
        'icon' => 'fa fa-building-o',
        'active' => 'Company',
        'visible' => $userObj->hasPermission('view-company'),
    ], [
        'title' => $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'],
        'url' => 'driver.php',
        'icon' => ['class' => 'icon-user1', 'url' => 'images/icon/driver-icon.png'],
        'active' => 'Driver',
        'visible' => $userObj->hasPermission('view-providers'),
    ],

    [
        'title' => $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Reward',

        'url' => 'javascript:',

        'icon' => ['class' => 'icon-user1', 'url' => 'images/icon/driver-icon.png'],

        'visible' => ($userObj->hasPermission('manage-driver-reward-setting') || $userObj->hasPermission('view-driver-reward-report')) && $MODULES_OBJ->isEnableDriverRewardModule(),

        'children' => [
            [
                'title' => 'Reward Reports',

                'url' => 'reports.php',

                'icon' => 'fa fa-certificate',

                'active' => 'Reports',

                'visible' => $userObj->hasPermission('view-driver-reward-report'),
            ], [
                'title' => 'Reward Setting',

                'url' => 'reward.php',

                'icon' => 'fa fa-certificate',

                'active' => 'Reward',
                'visible' => $userObj->hasPermission('manage-driver-reward-setting'),
            ],
        ],
    ],
    [
        'title' => $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'],
        'url' => 'rider.php',
        'icon' => ['class' => 'icon-group1', 'url' => 'images/rider-icon.png'],
        'active' => 'Rider',
        'visible' => $userObj->hasPermission('view-users'),
    ],
    [
        'title' => $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Vehicles',
        'url' => 'vehicles.php',
        'icon' => 'fa fa-taxi',
        'active' => 'Vehicle',
        'visible' => ($userObj->hasPermission('view-provider-taxis') && ('Ride-Delivery-UberX' !== $APP_TYPE || 'YES' === $onlyDeliverallModule)),
    ], [
        'title' => $langage_lbl_admin['LBL_VEHICLE_TYPE_RENTAL_TXT'],
        'url' => 'rental_vehicle_list.php',
        'icon' => 'fa fa-dot-circle-o',
        'active' => 'Rental Package',
        'visible' => ($userObj->hasPermission('view-rental-packages') && ('Ride' === $APP_TYPE || 'Ride-Delivery' === $APP_TYPE) && ENABLE_RENTAL_OPTION === 'Yes' && 'Yes' === $leftrideEnable),
    ], [
        'title' => $langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'],
        'url' => 'vehicle_type.php',
        'icon' => ['class' => 'icon-user1', 'url' => 'images/icon/vehicle-type-icon.png'],
        'active' => 'VehicleType',
        'visible' => ($userObj->hasPermission('view-vehicle-type') && 'Ride-Delivery-UberX' !== $APP_TYPE),
    ], [
        'title' => $langage_lbl_admin['LBL_DRIVER_SUBSCRIPTION'],
        'url' => 'javascript:',
        'icon' => ['class' => 'icon-user1', 'url' => 'images/icon/subscription-icon.png'],
        'visible' => (($userObj->hasPermission('manage-driver-subscription') || $userObj->hasPermission('manage-driver-subscription-report')) && 'Yes' === $DRIVER_SUBSCRIPTION_ENABLE && 'YES' !== $onlyDeliverallModule),
        'children' => [
            [
                'title' => $langage_lbl_admin['LBL_DRIVER_SUBSCRIPTION_PLAN'],
                'url' => 'driver_subscription.php',
                'icon' => 'fa fa-building-o',
                'active' => 'DriverSubscriptionPlan',
                'visible' => ($userObj->hasPermission('manage-driver-subscription') && 'Yes' === $DRIVER_SUBSCRIPTION_ENABLE && 'YES' !== $onlyDeliverallModule),
            ],
            [
                'title' => $langage_lbl_admin['LBL_DRIVER_SUBSCRIPTION_REPORT'],
                'url' => 'driver_subscription_report.php',
                'icon' => ['class' => 'icon-user1', 'url' => 'images/icon/subscriptionreport-icon.png'],
                'active' => 'DriverSubscriptionReport',
                'visible' => ($userObj->hasPermission('manage-driver-subscription-report') && 'Yes' === $DRIVER_SUBSCRIPTION_ENABLE && 'YES' !== $onlyDeliverallModule),
            ],
        ],
    ],
    [
        'title' => 'Service Category',
        'url' => 'vehicle_category.php',
        'icon' => 'fa fa-certificate',
        'active' => 'VehicleCategory',
        'visible' => ($userObj->hasPermission('view-vehicle-category') && 'Ride-Delivery-UberX' !== $APP_TYPE && ('Ride' !== $APP_TYPE && 'YES' !== $onlyDeliverallModule)),
    ],
    [
        'title' => 'Manage Services',
        'icon' => 'fa fa-wrench',
        'visible' => ($userObj->hasPermission('manage-services') && 'Ride-Delivery-UberX' === $APP_TYPE),
        'children' => [
            [
                'title' => $langage_lbl_admin['LBL_RIDE_DELIVERY_VEHICLE_TXT'].' Vehicles',
                'icon' => 'fa fa-taxi',
                'visible' => $userObj->hasPermission('manage-vehicles'),
                'children' => [
                    [
                        'title' => $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Vehicles',
                        'url' => 'vehicles.php',
                        'icon' => 'fa fa-dot-circle-o',
                        'active' => 'Vehicle',
                        'visible' => $userObj->hasPermission('view-provider-taxis'),
                    ], [
                        'title' => $langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'],
                        'url' => 'vehicle_type.php',
                        'icon' => 'fa fa-dot-circle-o',
                        'active' => 'VehicleType',
                        'visible' => $userObj->hasPermission('view-vehicle-type'),
                    ], [
                        'title' => $langage_lbl_admin['LBL_VEHICLE_TYPE_RENTAL_TXT'],
                        'url' => 'rental_vehicle_list.php',
                        'icon' => 'fa fa-dot-circle-o',
                        'active' => 'Rental Package',
                        'visible' => ($userObj->hasPermission('view-rental-packages') && ENABLE_RENTAL_OPTION === 'Yes' && 'Yes' === $leftrideEnable),
                    ],
                ],
            ], [
                'title' => 'Manage Other Services',
                'icon' => 'fa fa-wrench',
                'visible' => $userObj->hasPermission(['view-vehicle-category', 'view-service-type']),
                'children' => [
                    [
                        'title' => 'Service Category',
                        'url' => $MODULES_OBJ->isEnableAppHomeScreenLayout() ? 'master_service_category.php' : 'vehicle_category.php',
                        'icon' => '',
                        'active' => 'VehicleCategory',
                        'visible' => $MODULES_OBJ->isEnableAppHomeScreenLayout() ? $userObj->hasPermission('view-master-service-category') : $userObj->hasPermission('view-vehicle-category'),
                    ], [
                        'title' => 'Service Type',
                        'url' => 'service_type.php',
                        'icon' => 'fa fa-wrench',
                        'active' => 'ServiceType',
                        'visible' => $userObj->hasPermission('view-service-type') && ('Ride-Delivery-UberX' === $APP_TYPE && 1 === $leftufxserviceon),
                    ],
                    [
                        'title' => $langage_lbl_admin['LBL_DRIVER_SERVICE_REQUESTS_TXT'],
                        'url' => 'driver_service_request.php',
                        'icon' => 'fa fa-wrench',
                        'active' => 'DriverRequest',
                        'visible' => (('Ride-Delivery-UberX' === $APP_TYPE || 'UberX' === $APP_TYPE) && 1 === $leftufxserviceon),
                    ],
                ],
            ],
        ],
    ], [
        'title' => 'Manage Fly Vehicles', // added by SP for fly stations on 13-08-2019
        'icon' => 'fa fa-map-marker',
        'visible' => $userObj->hasPermission('manage-fly-vehicles') && 'Yes' === $ENABLE_FLY_VEHICLES,
        'children' => [
            [
                'title' => $langage_lbl_admin['LBL_FLY_STATIONS'],
                'url' => 'fly_stations.php',
                'icon' => 'fa fa-map-marker',
                'active' => 'fly_stations',
                'visible' => $userObj->hasPermission('view-fly-stations'),
            ],
            [
                'title' => $langage_lbl_admin['LBL_FLY_VEHICLE_TYPE'],
                'url' => 'fly_vehicle_type.php',
                'icon' => 'fa fa-dot-circle-o',
                'active' => 'FlyVehicleType',
                'visible' => $userObj->hasPermission('view-fly-vehicle-type'),
            ],
            [
                'title' => $langage_lbl_admin['LBL_FLY_LOCATION_WISE_FARE'],
                'url' => 'fly_locationwise_fare.php',
                'icon' => 'fa fa-map-signs',
                'active' => 'fly_locationwise_fare',
                'visible' => $userObj->hasPermission('view-fly-fare'),
            ],
        ],
    ],
    /* [
    'title' => "App Main Screen Settings",
    'url' => "app_home_settings.php",
    "icon" => "fa fa-globe",
    "active" => "App Main Screen Settings",
    "visible" => ($userObj->hasPermission('manage-app-main-screen-settings') && $APP_TYPE == 'Ride-Delivery-UberX'),
    ], */ [
        'title' => 'Manual Booking',
        'url' => 'add_booking.php',
        'icon' => ['class' => 'fa fa-taxi1', 'url' => 'images/manual-taxi-icon.png'],
        'active' => 'booking',
        'visible' => ($userObj->hasPermission('manage-manual-booking') && 'NO' === $onlyDeliverallModule),
        'target' => 'blank',
        // && ($APP_TYPE != "Delivery" || ($APP_TYPE == "Delivery" && $APP_DELIVERY_MODE != "Multi"))
    ], [
        'title' => $langage_lbl_admin['LBL_RIDE_LATER_BOOKINGS_ADMIN'],
        'url' => 'cab_booking.php',
        'icon' => ['class' => 'icon-book1', 'url' => 'images/ride-later-bookings.png'],
        'active' => 'CabBooking',
        'visible' => ($userObj->hasPermission('manage-ride-job-later-bookings') && 'Yes' === $RIDE_LATER_BOOKING_ENABLED),
    ], [
        'title' => $langage_lbl_admin['LBL_TRIPS_TXT_ADMIN'],
        'url' => 'trip.php',
        'icon' => ['class' => 'fa fa-exchange1', 'url' => 'images/trips-icon.png'],
        'active' => 'Trips',
        'visible' => ($userObj->hasPermission('manage-trip-jobs') && 'NO' === $onlyDeliverallModule),
    ], [
        'title' => 'Manage Stores',
        'icon' => 'fa fa-building-o',
        'visible' => ($userObj->hasPermission('manage-stores') && 'YES' === $deliverallModule && 'Yes' === $leftdeliverallEnable),
        'children' => [
            [
                'title' => 'Store Dashboard',
                'url' => 'store_dashboard.php',
                'icon' => 'fa fa-tachometer',
                'active' => 'Store Dashboard',
                'visible' => ($userObj->hasPermission('manage-store-dashboard') && 'NO' === $onlyDeliverallModule),
            ], [
                'title' => $restaurantAdmin,
                'url' => 'store.php',
                'icon' => 'fa fa-building-o',
                'active' => 'DeliverAllStore',
                'visible' => $userObj->hasPermission('view-store'),
            ], [
                'title' => $langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'],
                'url' => 'store_vehicle_type.php',
                'icon' => 'fa fa-taxi',
                'active' => 'StoreVehicleType',
                'visible' => $userObj->hasPermission('view-vehicle-type'),
            ], [
                'title' => $restaurantAdmin.' Items',
                'icon' => 'fa fa-columns',
                'visible' => $userObj->hasPermission('manage-store-items'),
                'children' => [
                    [
                        'title' => 'Import Bulk Items',
                        'url' => 'import_item_data.php',
                        'icon' => 'fa fa-file',
                        'active' => 'ImportItem',
                        'visible' => (($userObj->hasPermission('view-item-categories') || $userObj->hasPermission('view-item')) && $MODULES_OBJ->isEnableBulkImportItems()),
                    ],
                    [
                        'title' => 'Item Category',
                        'url' => 'food_menu.php',
                        // "icon" => "fa fa-cutlery",
                        'icon' => ['class' => 'icon-user1 icon-sitemap'],
                        'active' => 'FoodMenu',
                        'visible' => $userObj->hasPermission('view-item-categories'),
                    ], [
                        'title' => 'Items',
                        'url' => 'menu_item.php',
                        'icon' => 'fa fa-list-alt',
                        'active' => 'MenuItems',
                        'visible' => $userObj->hasPermission('view-item'),
                    ], [
                        'title' => 'Item Type',
                        'url' => 'cuisine.php',
                        // "icon" => "fa fa-spoon",
                        'icon' => ['class' => 'icon-user1 hotel-create-request', 'url' => 'images/item-type.png'],
                        'active' => 'Cuisine',
                        'visible' => $userObj->hasPermission('view-item-type'),
                    ],
                ],
            ], [
                'title' => 'Store Orders',
                'icon' => 'fa fa-first-order',
                'visible' => $userObj->hasPermission('manage-orders'),
                'children' => [
                    [
                        'title' => 'Processing',
                        'url' => 'allorders.php?type=processing',
                        'icon' => 'fa fa-fire',
                        'active' => 'Processing Orders',
                        'visible' => $userObj->hasPermission('view-processing-orders'),
                    ], [
                        'title' => 'Cancelled',
                        'url' => 'cancelled_orders.php',
                        'icon' => 'fa fa-undo',
                        'active' => 'CancelledOrders',
                        'visible' => $userObj->hasPermission('view-cancelled-orders'),
                    ], [
                        'title' => 'All Orders',
                        'url' => 'allorders.php?type=allorders',
                        'icon' => 'fa fa-list',
                        'active' => 'All Orders',
                        'visible' => $userObj->hasPermission('view-all-orders'),
                    ],
                ],
            ], [
                'title' => 'Site Earning',
                'icon' => 'icon-money',
                'visible' => $userObj->hasPermission('manage-site-earning'),
                'children' => [
                    [
                        'title' => 'Admin Earning',
                        'url' => 'admin_payment_report.php',
                        'icon' => 'fa fa-fire',
                        'active' => 'Admin Payment_Report',
                        'visible' => $userObj->hasPermission('manage-admin-earning'),
                    ], [
                        'title' => 'Payout to '.$restaurantAdmin,
                        'url' => 'restaurants_pay_report.php',
                        'icon' => 'fa fa-undo',
                        'active' => 'Restaurant Payment Report',
                        'visible' => $userObj->hasPermission('manage-store-payment'),
                    ], [
                        'title' => 'Payout to '.$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'],
                        'url' => 'store_driver_pay_report.php',
                        'icon' => 'fa fa-list',
                        'active' => 'Deliverall Driver Payment Report',
                        'visible' => $userObj->hasPermission('manage-provider-payment'),
                    ], [
                        'title' => 'Cancelled Order Report',
                        'url' => 'cancelled_report.php',
                        'icon' => 'fa fa-list',
                        'active' => 'Cancelled Order Report',
                        'visible' => $userObj->hasPermission('manage-cancelled-order-report'),
                    ],
                ],
            ], [
                'title' => 'Utility',
                'icon' => 'fa fa-wrench',
                'visible' => $userObj->hasPermission('manage-store-utility'),
                'children' => [
                    [
                        'title' => 'DeliveryAll Service Category',
                        'url' => 'service_category.php',
                        'icon' => 'fa fa-question',
                        'active' => 'service_category',
                        'visible' => $userObj->hasPermission('view-service-category') && 'YES' === $onlyDeliverallModule && 'Yes' === $leftdeliverallEnable,
                    ], [
                        'title' => 'Order Status',
                        'url' => 'order_status.php',
                        'icon' => 'fa fa-first-order',
                        'active' => 'order_status',
                        'visible' => $userObj->hasPermission('view-order-status'),
                    ], [
                        'title' => 'Delivery Charges',
                        'url' => 'delivery_charges.php',
                        'icon' => 'fa fa-map-signs',
                        'active' => 'Delivery Charges',
                        'visible' => $userObj->hasPermission('view-delivery-charges'),
                    ], [
                        'title' => 'Distance wise Delivery Charges',
                        'url' => 'custom_delivery_charge_order.php',
                        'icon' => 'fa fa-map-signs',
                        'active' => 'Custom Delivery Charges',
                        'visible' => $userObj->hasPermission('view-custom-delivery-charges') && $MODULES_OBJ->isEnableDistanceWiseDeliveryChargeOrder(),
                    ], [
                        'title' => 'Banner',
                        'url' => 'store_banner.php',
                        'icon' => 'fa fa-chevron-right',
                        'active' => 'Store Banner',
                        'visible' => $userObj->hasPermission('view-banner'),
                    ], [
                        'title' => 'Manage '.$langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN'].' Categories',
                        'url' => 'store_category.php',
                        'icon' => 'fa fa-building-o',
                        'active' => 'ManageStoreCategories',
                        'visible' => ($userObj->hasPermission('view-store-categories') && $MODULES_OBJ->isStoreClassificationEnable()),
                    ], [
                        'title' => $langage_lbl_admin['LBL_DELIVERY_PREF'],
                        'url' => 'delivery_preferences.php',
                        'icon' => ['class' => 'fa fa-exchange1', 'url' => 'images/delivery-pref.png'],
                        'active' => 'DeliveryPreferences',
                        'visible' => ($userObj->hasPermission('view-delivery-preference') && $MODULES_OBJ->isDeliveryPreferenceEnable()),
                    ], [
                        'title' => 'Manage OTP For Service Categories',
                        'url' => 'manage_otp_for_stores.php',
                        'icon' => ['url' => 'images/manage-otp.png'],
                        'active' => 'otpservicecategory',
                        'visible' => ($userObj->hasPermission('manage-otp-for-stores') && $MODULES_OBJ->isEnableOTPVerificationDeliverAll()),
                    ], [
                        'title' => 'Rating Feedback Questions',
                        'url' => 'rating_feedback_ques.php',
                        'icon' => 'fa fa-question',
                        'active' => 'RatingFeedbackQuestions',
                        'visible' => ($userObj->hasPermission('view-rating-feedback-ques') && $MODULES_OBJ->isEnableMultiOptionsToppings() && 'Yes' === $foodCategoryAvailable),
                    ],
                    /* [

                'title'   => "Cancel Reason",

                "url"     => "store_cancel_reason.php",

                "icon"    => "fa fa-question",

                "active"  => "cancel_reason",

                "visible" => $userObj->hasPermission('view-cancel-reasons'),

                ], */
                ],
            ], [
                'title' => 'Orders Reviews',
                'url' => 'store_review.php',
                'icon' => 'icon-comments',
                'active' => 'Store Review',
                'visible' => $userObj->hasPermission('manage-store-reviews'),
            ],
        ],
    ], [
        'title' => $langage_lbl_admin['LBL_MANUAL_STORE_ORDER_TXT'],
        'url' => '../user-order-information?order=admin',
        'icon' => ['class' => 'fa fa-taxi1', 'url' => 'images/shopping-cart.png'],
        'active' => 'store_order_book',
        'target' => 'blank',
        'visible' => ($userObj->hasPermission('manage-restaurant-order') && 'YES' === $deliverallModule && 'Yes' === $MANUAL_STORE_ORDER_ADMIN_PANEL),
    ], [
        'title' => 'PromoCode',
        'url' => 'coupon.php',
        'icon' => ['class' => 'fa fa-product-hunt1', 'url' => 'images/promo-code-icon.png'],
        'active' => 'Coupon',
        'visible' => $userObj->hasPermission('view-promocode'),
    ], [
        'title' => "God's View",
        'url' => 'map_godsview.php',
        'icon' => ['class' => 'icon-map-marker1', 'url' => 'images/god-view-icon.png'],
        'active' => 'LiveMap',
        'visible' => $userObj->hasPermission('manage-gods-view'),
    ], [
        'title' => 'Heat View',
        'url' => 'heatmap.php',
        'icon' => ['class' => 'fa-header1', 'url' => 'images/heat-icon.png'],
        'active' => 'Heat Map',
        'visible' => $userObj->hasPermission('manage-heat-view'),
    ], [
        'title' => 'Reviews',
        'url' => 'review.php',
        'icon' => ['class' => 'icon-comments1', 'url' => 'images/reviews-icon.png'],
        'active' => 'Review',
        'visible' => ($userObj->hasPermission('manage-reviews') && 'NO' === $onlyDeliverallModule),
    ], [
        'title' => 'Advertisement Banners',
        'url' => 'advertise_banners.php',
        'icon' => 'fa fa-bullhorn',
        'active' => 'Advertisement Banners',
        'visible' => $userObj->hasPermission('view-advertise-banner') && ('Disable' !== $ADVERTISEMENT_TYPE && 'SHARK' === $PACKAGE_TYPE),
    ],
    [
        'title' => 'Decline/Cancelled Alert',
        'url' => 'blocked_driver.php',
        'icon' => 'fa fa-bullhorn',
        'active' => 'Driver',
        'visible' => $userObj->hasPermission('view-blocked-driver') && 'SHARK' === $PACKAGE_TYPE && 'YES' !== $onlyDeliverallModule, // This Module Enable For Shark Package As Per Discss With KS Sir By HJ On 05-11-2019
        'children' => [
            [
                'title' => 'Alert For '.$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'],
                'url' => 'blocked_driver.php',
                'icon' => 'fa fa-user',
                'active' => 'blockeddriver',
                'visible' => $userObj->hasPermission('view-blocked-driver'),
            ], [
                'title' => 'Alert For '.$langage_lbl_admin['LBL_RIDER'],
                'url' => 'blocked_rider.php',
                'icon' => 'fa fa-user',
                'active' => 'blockedrider',
                'visible' => $userObj->hasPermission('view-blocked-rider'),
            ],
        ],
    ],
    [
        'title' => 'Reports',
        'icon' => ['class' => 'icon-cogs1', 'url' => 'images/reports-icon.png'],
        'visible' => $userObj->hasPermission('manage-report'),
        'children' => [
            [
                'title' => 'Payment Report',
                'url' => 'payment_report.php',
                'icon' => 'icon-money',
                'active' => 'Payment_Report',
                'visible' => ($userObj->hasPermission('manage-payment-report') && 'NO' === $onlyDeliverallModule),
            ], [
                'title' => 'Hotel Payment Report',
                'url' => 'hotel_payment_report.php',
                'icon' => 'icon-money',
                'active' => 'hotelPayment_Report',
                'visible' => $userObj->hasPermission('manage-hotel-payment-report') && ($hotelPanel > 0 || $kioskPanel > 0),
            ], [
                'title' => 'Referral Report',
                'url' => 'referrer.php',
                'icon' => 'fa fa-hand-peace-o',
                'active' => 'referrer',
                'visible' => ($userObj->hasPermission('manage-referral-report') && 'Yes' === $REFERRAL_SCHEME_ENABLE),
            ], [
                'title' => 'Wallet Report',
                'url' => 'wallet_report.php',
                'icon' => 'fa fa-google-wallet',
                'active' => 'Wallet Report',
                'visible' => ($userObj->hasPermission('manage-user-wallet-report') && 'Yes' === $WALLET_ENABLE),
            ], [
                'title' => $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Payment Report',
                'url' => 'driver_pay_report.php',
                'icon' => 'icon-money',
                'active' => 'Driver Payment Report',
                'visible' => ($userObj->hasPermission('manage-provider-payment-report') && 'NO' === $onlyDeliverallModule),
            ], [
                'title' => $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Log Report',
                'url' => 'driver_log_report.php',
                'icon' => 'glyphicon glyphicon-list-alt',
                'active' => 'Driver Log Report',
                'visible' => $userObj->hasPermission('manage-provider-log-report'),
            ], [
                'title' => 'Cancelled '.$langage_lbl_admin['LBL_TRIP_TXT_ADMIN'],
                'url' => 'cancelled_trip.php',
                'icon' => 'fa fa-exchange',
                'active' => 'CancelledTrips',
                'visible' => ($userObj->hasPermission('manage-cancelled-trip-job-report') && 'NO' === $onlyDeliverallModule),
            ], [
                'title' => $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'].' Acceptance Report',
                'url' => 'ride_acceptance_report.php',
                'icon' => 'icon-group',
                'active' => 'Driver Accept Report',
                'visible' => ($userObj->hasPermission('manage-trip-job-request-acceptance-report') && 'NO' === $onlyDeliverallModule),
            ], [
                'title' => $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'].' Time Variance',
                'url' => 'driver_trip_detail.php',
                'icon' => 'fa fa-taxi',
                'active' => 'Driver Trip Detail',
                'visible' => ($userObj->hasPermission('manage-trip-job-time-variance-report') && 'NO' === $onlyDeliverallModule),
            ], [
                'title' => 'Insurance Report',
                'icon' => 'fa fa-globe',
                'visible' => $MODULES_OBJ->isInsuranceReportEnable() && $userObj->hasPermission('manage-payment-report'),
                'children' => [
                    [
                        'title' => 'Idle Time',
                        'url' => 'insurance_idle_report.php',
                        'icon' => 'fa fa-taxi',
                        'active' => 'Insurance_Idle_time_Report',
                        'visible' => 'SHARK' === $PACKAGE_TYPE && 'UberX' !== $APP_TYPE && 'Yes' === $ENABLE_INSURANCE_IDLE_REPORT,
                    ], [
                        'title' => 'After '.$langage_lbl_admin['LBL_TRIP_TXT_ADMIN'].' Accept',
                        'url' => 'insurance_accept_report.php',
                        'icon' => 'fa fa-taxi',
                        'active' => 'Insurance_accept_trip_Report',
                        'visible' => 'SHARK' === $PACKAGE_TYPE && 'UberX' !== $APP_TYPE && 'Yes' === $ENABLE_INSURANCE_ACCEPT_REPORT,
                    ], [
                        'title' => 'After '.$langage_lbl_admin['LBL_TRIP_TXT_ADMIN'].' Start',
                        'url' => 'insurance_trip_report.php',
                        'icon' => 'fa fa-taxi',
                        'active' => 'Insurance_start_trip_Report',
                        'visible' => 'SHARK' === $PACKAGE_TYPE && 'UberX' !== $APP_TYPE && 'Yes' === $ENABLE_INSURANCE_TRIP_REPORT,
                    ],
                ],
            ], [
                'title' => 'User Outstanding Report',
                'url' => 'outstanding_report.php',
                'icon' => 'glyphicon glyphicon-list-alt',
                'active' => 'outstanding_report',
                'visible' => $userObj->hasPermission('view-user-outstanding-report'),
            ],
        ],
    ], [
        'title' => 'Support Requests',
        'icon' => ['class' => 'icon-cogs1', 'url' => 'images/support-icon.png'],
        'visible' => ($userObj->hasPermission('view-contactus-report') || $userObj->hasPermission('view-sos-request-report') || $userObj->hasPermission('view-trip-job-help-request-report') || $userObj->hasPermission('view-order-help-request-report') || $userObj->hasPermission('view-payment-request-report') || $userObj->hasPermission('view-withdraw-request-report')),
        'children' => [
            [
                'title' => 'Contact Us Form Requests',
                'url' => 'contactus.php',
                'icon' => 'glyphicon glyphicon-list-alt',
                'active' => 'contactus',
                'visible' => $userObj->hasPermission('view-contactus-report'),
            ], [
                'title' => 'SOS Requests',
                'url' => 'emergency_contact_data.php',
                'icon' => 'glyphicon glyphicon-list-alt',
                'active' => 'emergency_contact_data',
                'visible' => $userObj->hasPermission('view-sos-request-report'),
            ], [
                'title' => $langage_lbl_admin['LBL_TRIPS_TXT_ADMIN'].' Help Requests',
                'url' => 'trip_help_details.php',
                'icon' => 'glyphicon glyphicon-list-alt',
                'active' => 'trip_help_details',
                'visible' => $userObj->hasPermission('view-trip-job-help-request-report') && 'NO' === $onlyDeliverallModule,
            ], [
                'title' => 'Order Help Requests',
                'url' => 'order_help_details.php',
                'icon' => 'glyphicon glyphicon-list-alt',
                'active' => 'order_help_details',
                'visible' => $userObj->hasPermission('view-order-help-request-report') && 'YES' === $deliverallModule,
            ], [
                'title' => 'Payment Requests',
                'url' => 'payment_requests_report.php',
                'icon' => 'glyphicon glyphicon-list-alt',
                'active' => 'payment_requests',
                'visible' => $userObj->hasPermission('view-payment-request-report'),
            ], [
                'title' => 'Withdraw Requests',
                'url' => 'withdraw_requests_report.php',
                'icon' => 'glyphicon glyphicon-list-alt',
                'active' => 'withdraw_requests',
                'visible' => $userObj->hasPermission('view-withdraw-request-report'),
            ],
        ],
    ], [
        'title' => 'Manage Locations',
        'icon' => ['class' => 'fa fa-header1', 'url' => 'images/location-icon.png'],
        'visible' => $userObj->hasPermission('manage-locations'),
        'children' => [
            [
                'title' => 'Geo Fence Location',
                'url' => 'location.php',
                'icon' => 'fa fa-map-marker',
                'active' => 'Location',
                'visible' => $userObj->hasPermission('view-geo-fence-locations'),
            ],
            [
                'title' => 'Restricted Area',
                'url' => 'restricted_area.php',
                'icon' => 'fa fa-map-signs',
                'active' => 'Restricted Area',
                'visible' => $userObj->hasPermission('view-restricted-area'),
            ],
            [
                'title' => 'Locationwise Fare',
                'url' => 'locationwise_fare.php',
                'icon' => 'fa fa-map-signs',
                'active' => 'locationwise_fare',
                'visible' => $userObj->hasPermission('view-location-wise-fare') && 'STANDARD' !== $PACKAGE_TYPE && 'Delivery' !== $APP_TYPE && 'UberX' !== $APP_TYPE && $MODULES_OBJ->isRideFeatureAvailable(),
            ], [
                'title' => 'Airport Surcharge',
                'url' => 'airport_surcharge.php',
                'icon' => 'fa fa-map-signs',
                'active' => 'airportsurcharge_fare',
                'visible' => $userObj->hasPermission('view-airport-surcharge') && 'Yes' === $ENABLE_AIRPORT_SURCHARGE_SECTION && 'UberX' !== $APP_TYPE && 'Delivery' !== $APP_TYPE && ('NO' === $onlyDeliverallModule && $MODULES_OBJ->isRideFeatureAvailable()),
            ],
        ],
    ], [
        'title' => 'Settings',
        'icon' => ['class' => 'icon-cogs1', 'url' => 'images/settings-icon.png'],
        'visible' => $userObj->hasPermission('manage-settings'),
        'children' => [
            [
                'title' => 'General',
                'url' => 'general.php',
                'icon' => 'fa-cogs fa',
                'active' => 'General',
                'visible' => $userObj->hasPermission('manage-general-settings'),
            ],
            [
                'title' => 'Referral Settings',
                'url' => 'referral_settings.php',
                'icon' => 'fa-cogs fa',
                'active' => 'Referral',
                'visible' => ($userObj->hasPermission('view-referral-settings') && $MODULES_OBJ->isEnableMultiLevelReferralSystem()),
            ],
            [
                'title' => 'Email Templates',
                'url' => 'email_template.php',
                'icon' => 'fa fa-envelope',
                'active' => 'email_templates',
                'visible' => $userObj->hasPermission('view-email-templates'),
            ],
            [
                'title' => 'SMS Templates',
                'url' => 'sms_template.php',
                'icon' => 'fa fa-comment',
                'active' => 'sms_templates',
                'visible' => $userObj->hasPermission('view-sms-templates'),
            ],
            [
                'title' => 'Manage Documents',
                'url' => 'document_master_list.php',
                'icon' => 'fa fa-file-text',
                'active' => 'Document Master',
                'visible' => $userObj->hasPermission('view-documents'),
            ], [
                'title' => 'Language Label - Pages',
                'url' => 'master_lng_pages.php',
                'icon' => 'fa fa-language',
                'active' => 'MasterLanguagePages',
                'visible' => $userObj->hasPermission('view-documents') && $IS_INHOUSE_DOMAINS && $displayInhousePage > 0,
            ], [
                'title' => 'Language Label',
                'icon' => 'fa fa-language',
                'visible' => $userObj->hasPermission('manage-language-label') && ('YES' === $deliverallModule || 'YES' === $onlyDeliverallModule),
                'children' => static function () {
                    global $allservice_cat_data, $userObj;

                    $languages_childs = [
                        [
                            'title' => 'General Label',
                            'url' => 'languages.php',
                            'icon' => 'fa fa-language',
                            'active' => 'language_label',
                            'visible' => $userObj->hasPermission('view-general-label'),
                        ],
                        /*[
                    'title' => "Manage Screen Label",
                    "icon" => "fa fa-globe",
                    "visible" => $userObj->hasPermission('manage-screen-label'),
                    'children' => [
                    [
                    'title' => "Manage App Screen",
                    "url" => "app_screen.php",
                    "icon" => "fa fa-dot-circle-o",
                    "active" => "Manage Screen",
                    "visible" => $userObj->hasPermission('view-app-screen'),
                    ],
                    [
                    'title' => "Manage Screen Label",
                    "url" => "screen_label.php",
                    "icon" => "fa fa-dot-circle-o",
                    "active" => "Manage Label",
                    "visible" => $userObj->hasPermission('view-app-screen-label'),
                    ],
                    ],
                    ],*/
                    ];

                    if (count($allservice_cat_data) >= 1 && !empty($allservice_cat_data)) {
                        foreach ($allservice_cat_data as $key => $value) {
                            $languages_childs[] = [
                                'title' => $value['vServiceName'].' Label',
                                'url' => 'languages.php?selectedlanguage='.$value['iServiceId'],
                                'icon' => 'fa fa-dot-circle-o',
                                'active' => 'language_label_'.$value['iServiceId'],
                                'visible' => $userObj->hasPermission('view-general-label'),
                            ];
                        }
                    }

                    return $languages_childs;
                },
            ], [
                'title' => 'Language Label',
                'url' => 'languages.php',
                'icon' => 'fa fa-language',
                'active' => 'language_label',
                'visible' => $userObj->hasPermission('manage-language-label') && (!('YES' === $deliverallModule || 'YES' === $onlyDeliverallModule)),
            ], [
                'title' => 'Currency',
                'url' => 'currency.php',
                'icon' => 'fa fa-usd',
                'active' => 'Currency',
                'visible' => $userObj->hasPermission('manage-currency'),
            ], [
                'title' => 'Language',
                'url' => 'language.php',
                'icon' => 'fa fa-language',
                'active' => 'Language',
                'visible' => $userObj->hasPermission('manage-language'),
            ], [
                'title' => 'SEO Settings',
                'url' => 'seo_setting.php',
                'icon' => 'fa fa-info',
                'active' => 'seo_setting',
                'visible' => $userObj->hasPermission('view-seo-setting'),
            ],
            [
                'title' => 'Maps API Settings',
                'url' => 'map_api_setting.php',
                'icon' => 'fa-cogs fa',
                'active' => 'map_api_setting',
                'visible' => $userObj->hasPermission('view-map-api-setting') && (true === $MODULES_OBJ->mapAPIreplacementAvailable()),
            ],
            [
                'title' => 'Banner',
                'url' => 'banner.php',
                'icon' => 'fa fa-chevron-right',
                'active' => 'Banner',
                'visible' => ($userObj->hasPermission('view-banner') && ('UberX' === $APP_TYPE || 1 === $leftcubejekxthemeon || 1 === $leftcubexthemeon || 1 === $leftdeliverykingthemeon)),
            ],
        ],
    ], [
        'title' => 'Utility',
        'icon' => 'fa fa-wrench',
        'visible' => $userObj->hasPermission('manage-utility'),
        'children' => [
            [
                'title' => 'Localization',
                'icon' => 'fa fa-globe',
                'visible' => $userObj->hasPermission('manage-localization'),
                'children' => [
                    [
                        'title' => 'Country',
                        'url' => 'country.php',
                        'icon' => 'fa fa-dot-circle-o',
                        'active' => 'country',
                        'visible' => $userObj->hasPermission('view-country'),
                    ],
                    [
                        'title' => 'State',
                        'url' => 'state.php',
                        'icon' => 'fa fa-dot-circle-o',
                        'active' => 'state',
                        'visible' => $userObj->hasPermission('view-state'),
                    ],
                    [
                        'title' => 'City',
                        'url' => 'city.php',
                        'icon' => 'fa fa-dot-circle-o',
                        'active' => 'city',
                        'visible' => $userObj->hasPermission('view-city') && ('Yes' === $SHOW_CITY_FIELD),
                    ],
                ],
            ], [
                'title' => 'Pages',
                'url' => 'page.php',
                'icon' => 'fa fa-file-text-o',
                'active' => 'page',
                'visible' => $userObj->hasPermission('view-pages'),
            ], [
                'title' => 'Edit Home Page',
                'url' => 'home_content_new.php',
                'icon' => 'fa fa-file-text-o',
                'active' => 'homecontent',
                // "visible" => $userObj->hasPermission('view-home-page-content') && ($APP_TYPE == 'Ride-Delivery-UberX'),
                'visible' => $userObj->hasPermission('view-home-page-content') && ('Ride-Delivery-UberX' === $APP_TYPE && 1 !== $leftcubexthemeon && 1 !== $leftcubejekxthemeon && 1 !== $leftdeliverykingthemeon),
            ], [
                'title' => 'Edit Home Page',
                'url' => 'home_content_ridedeliveryx.php',
                'icon' => 'fa fa-file-text-o',
                'active' => 'homecontent',
                'visible' => $userObj->hasPermission('view-home-page-content') && 1 === $leftridedeliveryxthemeon,
            ], [
                'title' => 'Edit Home Page',
                'url' => 'home_content_deliveryx.php',
                'icon' => 'fa fa-file-text-o',
                'active' => 'homecontent',
                'visible' => $userObj->hasPermission('view-home-page-content') && 1 === $leftdeliveryxthemeon,
            ], [
                'title' => 'Edit Home Page',
                'url' => 'home_content_deliveryking.php',
                'icon' => 'fa fa-file-text-o',
                'active' => 'homecontent',
                'visible' => $userObj->hasPermission('view-home-page-content') && 1 === $leftdeliverykingthemeon,
            ], [
                'title' => 'Edit Home Page',
                'url' => 'home_content_ridecx.php',
                'icon' => 'fa fa-file-text-o',
                'active' => 'homecontent',
                'visible' => $userObj->hasPermission('view-home-page-content') && 1 === $leftridecxthemeon,
            ], [
                'title' => 'Edit Home Page',
                'url' => 'home_content_cubejekx.php',
                'icon' => 'fa fa-file-text-o',
                'active' => 'homecontent',
                // "visible" => $userObj->hasPermission('view-home-page-content') && ($APP_TYPE == 'Ride-Delivery-UberX'),
                'visible' => $userObj->hasPermission('view-home-page-content') && (1 === $leftcubejekxthemeon),
            ], [
                'title' => 'Edit Home Page',
                'url' => 'home_content_cubex.php',
                'icon' => 'fa fa-file-text-o',
                'active' => 'homecontent',
                'visible' => $userObj->hasPermission('view-home-page-content') && ('Ride-Delivery-UberX' === $APP_TYPE && (1 === $leftcubexthemeon && ENABLE_DYNAMIC_CREATE_PAGE === 'Yes')),
            ], [
                'title' => 'Edit Home Page',
                'icon' => 'fa fa-file-text-o',
                'active' => 'homecontent',
                'visible' => $userObj->hasPermission('view-home-page-content') && ('Ride-Delivery-UberX' === $APP_TYPE && (1 === $leftcubexthemeon && ENABLE_DYNAMIC_CREATE_PAGE === 'No')),
                'children' => [
                    [
                        'title' => 'Main Page',
                        'url' => 'home_content_cubex.php',
                        'icon' => 'fa fa-dot-circle-o',
                        'active' => 'homecontent',
                        'visible' => $userObj->hasPermission('view-pages') && (1 === $leftcubexthemeon && ENABLE_DYNAMIC_CREATE_PAGE === 'No'),
                    ],
                    [
                        'title' => 'Taxi Page',
                        'url' => 'home_content_taxi.php',
                        'icon' => 'fa fa-dot-circle-o',
                        'active' => 'homecontentride',
                        'visible' => $userObj->hasPermission('view-pages') && (1 === $leftcubexthemeon && ENABLE_DYNAMIC_CREATE_PAGE === 'No'),
                    ],
                    [
                        'title' => 'Food Page',
                        'url' => 'home_content_food.php',
                        'icon' => 'fa fa-dot-circle-o',
                        'active' => 'homecontentfood',
                        'visible' => $userObj->hasPermission('view-pages') && (1 === $leftcubexthemeon && ENABLE_DYNAMIC_CREATE_PAGE === 'No'),
                    ],
                    [
                        'title' => 'Grocery Page',
                        'url' => 'home_content_grocery.php',
                        'icon' => 'fa fa-dot-circle-o',
                        'active' => 'homecontentgrocery',
                        'visible' => $userObj->hasPermission('view-pages') && (1 === $leftcubexthemeon && ENABLE_DYNAMIC_CREATE_PAGE === 'No'),
                    ],
                    [
                        'title' => 'Moto Page',
                        'url' => 'home_content_moto.php',
                        'icon' => 'fa fa-dot-circle-o',
                        'active' => 'homecontentmoto',
                        'visible' => $userObj->hasPermission('view-pages') && (1 === $leftcubexthemeon && ENABLE_DYNAMIC_CREATE_PAGE === 'No'),
                    ],
                    [
                        'title' => 'Delivery Page',
                        'url' => 'home_content_delivery.php',
                        'icon' => 'fa fa-dot-circle-o',
                        'active' => 'homecontentdelivery',
                        'visible' => $userObj->hasPermission('view-pages') && (1 === $leftcubexthemeon && ENABLE_DYNAMIC_CREATE_PAGE === 'No'),
                    ],
                    [
                        'title' => 'Fly Page',
                        'url' => 'home_content_fly.php',
                        'icon' => 'fa fa-dot-circle-o',
                        'active' => 'homecontentfly',
                        'visible' => $userObj->hasPermission('view-pages') && (1 === $leftcubexthemeon && ENABLE_DYNAMIC_CREATE_PAGE === 'No'),
                    ],
                    [
                        'title' => 'Earn Page',
                        'url' => 'home_content_earn.php',
                        'icon' => 'fa fa-dot-circle-o',
                        'active' => 'homecontentearn',
                        'visible' => $userObj->hasPermission('view-pages') && (1 === $leftcubexthemeon && ENABLE_DYNAMIC_CREATE_PAGE === 'No'),
                    ],
                    [
                        'title' => 'Business Page',
                        'url' => 'home_content_business.php',
                        'icon' => 'fa fa-dot-circle-o',
                        'active' => 'homecontentbusiness',
                        'visible' => $userObj->hasPermission('view-pages') && (1 === $leftcubexthemeon && ENABLE_DYNAMIC_CREATE_PAGE === 'No'),
                    ],
                ],
            ], [
                'title' => 'Edit Home Page',
                'url' => 'home_content.php',
                'icon' => 'fa fa-file-text-o',
                'active' => 'home_content',
                'visible' => $userObj->hasPermission('view-home-page-content') && ('Ride-Delivery-UberX' !== $APP_TYPE) && 1 !== $leftridecxthemeon && 1 !== $leftridedeliveryxthemeon & 1 !== $leftdeliveryxthemeon,
            ], /*[
                'title' => "Our " . $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'],
                "url" => "home_driver.php",
                "icon" => "fa fa-users",
                "active" => "home_driver",
                "visible" => $userObj->hasPermission('view-our-provider') && ($APP_TYPE != 'Ride-Delivery-UberX' && $APP_TYPE != 'Ride' && $APP_TYPE != 'Ride-Delivery'),
            ],*/ [
                'title' => $langage_lbl_admin['LBL_CAR_MAKE_ADMIN'],
                'url' => 'make.php',
                'icon' => 'fa fa-car',
                'active' => 'Make',
                'visible' => $userObj->hasPermission('view-vehicle-make') && ('UberX' !== $APP_TYPE),
            ], [
                'title' => $langage_lbl_admin['LBL_CAR_MODEL_ADMIN'],
                'url' => 'model.php',
                'icon' => 'fa fa-taxi',
                'active' => 'Model',
                'visible' => $userObj->hasPermission('view-vehicle-model') && ('UberX' !== $APP_TYPE),
            ], [
                'title' => 'Kiosk predefined destination',
                'url' => 'visit.php',
                'icon' => 'fa fa-map-marker',
                'active' => 'Visit',
                'visible' => $userObj->hasPermission('view-visit') && $kioskPanel > 0 && 'SHARK' === strtoupper($PACKAGE_TYPE), // added by SP on 1-7-2019 by asking with KS
            ], [
                'title' => 'Donation',
                'url' => 'donation.php',
                'icon' => 'fa fa-money',
                'active' => 'donation',
                'visible' => $userObj->hasPermission('view-donation') && ('Yes' === $DONATION && 'Yes' === $DONATION_ENABLE),
            ], [
                'title' => 'Hotel Banner',
                'url' => 'hotel_banner.php',
                'icon' => 'fa fa-bullhorn',
                'active' => 'hotel_banners',
                'visible' => $userObj->hasPermission('view-banner') && $kioskPanel > 0 && 'SHARK' === strtoupper($PACKAGE_TYPE), // added by SP on 1-7-2019 by asking with KS
            ], [
                'title' => 'News',
                'url' => 'news.php',
                'icon' => 'fa fa-file-text-o',
                'active' => 'news',
                'visible' => $userObj->hasPermission('view-news') && ('Yes' === $ENABLE_NEWS_SECTION),
            ], [
                'title' => 'Newsletter Subscribers',
                'url' => 'newsletter.php',
                'icon' => 'fa fa-file-text-o',
                'active' => 'newsletters-subscribers',
                'visible' => $userObj->hasPermission('manage-newsletter') && ('Yes' === $ENABLE_NEWSLETTERS_SUBSCRIPTION_SECTION),
            ], [
                'title' => 'Faq',
                'url' => 'faq.php',
                'icon' => 'fa fa-question',
                'active' => 'Faq',
                'visible' => $userObj->hasPermission('view-faq'),
            ], [
                'title' => 'Faq Categories',
                'url' => 'faq_categories.php',
                'icon' => 'fa fa-question-circle-o',
                'active' => 'faq_categories',
                'visible' => $userObj->hasPermission('view-faq-categories'),
            ], [
                'title' => 'Help Topics',
                'url' => 'help_detail.php',
                'icon' => 'fa fa-question',
                'active' => 'help_detail',
                'visible' => $userObj->hasPermission('view-help-detail'),
            ], [
                'title' => 'Help Topic Categories',
                'url' => 'help_detail_categories.php',
                'icon' => 'fa fa-question-circle-o',
                'active' => 'help_detail_categories',
                'visible' => $userObj->hasPermission('view-help-detail-category'),
            ],
            [
                'title' => 'Cancel Reason',
                'url' => 'cancellation_reason.php',
                'icon' => 'fa fa-question',
                'active' => 'cancel_reason',
                'visible' => $userObj->hasPermission('view-cancel-reasons'),
            ],
            [
                'title' => $langage_lbl_admin['LBL_PACKAGE_TYPE_ADMIN'],
                'url' => 'package_type.php',
                'icon' => 'fa fa-globe',
                'active' => 'Package',
                'visible' => $userObj->hasPermission('view-package-type') && ('Ride-Delivery-UberX' === $APP_TYPE || 'Ride-Delivery' === $APP_TYPE || 'Delivery' === $APP_TYPE) && 'YES' !== $onlyDeliverallModule && 'Yes' === $leftdeliveryEnable, // $onlyDeliverallModule != "YES" Added By HJ On 05-07-2019 As Per Discuss With KS Sir
            ],
            /*[
                'title' => $langage_lbl_admin['LBL_MULTI_DELIVERY_FORM'],
                "url" => "delivery_fields.php",
                "icon" => "fa fa-globe",
                "active" => "delivery_package",
                "visible" => $userObj->hasPermission('view-delivery-field') && ($APP_TYPE == 'Ride-Delivery-UberX' || $APP_TYPE == 'Ride-Delivery' || $APP_TYPE == 'Delivery') && $onlyDeliverallModule != "YES" && $leftdeliveryEnable == 'Yes',
            ],*/
            [
                'title' => 'Send Push-Notification',
                'url' => 'send_notifications.php',
                'icon' => 'fa fa-globe',
                'active' => 'Push Notification',
                'visible' => $userObj->hasPermission('manage-send-push-notification'),
            ], [
                'title' => 'DB Backup',
                'url' => 'backup.php',
                'icon' => 'fa fa-database',
                'active' => 'Back-up',
                'visible' => $userObj->hasPermission('view-db-backup'),
            ],
            [
                'title' => 'System Diagnostic',
                'url' => 'system_diagnostic.php',
                'icon' => 'fa fa-sitemap',
                'active' => 'site',
                'visible' => isset($_SESSION['SessionUserType']) && 'hotel' !== $_SESSION['SessionUserType'] && !($MODULES_OBJ->isEnableServerRequirementValidation() && SITE_TYPE === 'Live'), // added by SP on 1-7-2019 for not showing this module to hotel
            ],
            [
                'title' => 'Expired Documents',
                'url' => 'expired_documents.php',
                'icon' => 'fa fa-file',
                'active' => 'Expired Documents',
                // "visible" => $userObj->hasPermission('expired-documents') && $MODULES_OBJ->isDocumentExpiredFeatureEnable(),
                'visible' => $userObj->hasPermission('expired-documents'),
            ],
            [
                'title' => 'App Launch Info',
                'url' => 'app_launch_info.php',
                'icon' => 'fa fa-chevron-right',
                'active' => 'app_launch_info',
                'visible' => $userObj->hasPermission('manage-app-launch-info'),
            ],
            [
                'title' => 'App Banner Info',
                'url' => 'app_banner_info.php',
                'icon' => 'fa fa-chevron-right',
                'active' => 'app_banner_info',
                'visible' => $userObj->hasPermission('manage-app-banner-info') && $MODULES_OBJ->isEnableRideDeliveryV1(),
            ],
        ],
    ], [
        'title' => 'Manage Ride Profiles',
        'icon' => 'fa fa-taxi',
        'visible' => $userObj->hasPermission('manage-ride-profiles') && $MODULES_OBJ->isOrganizationModuleEnable() && 'Yes' === $leftrideEnable,
        'children' => [
            [
                'title' => 'Organization',
                'url' => 'organization.php',
                'icon' => 'fa fa-globe',
                'active' => 'Organization',
                'visible' => $userObj->hasPermission('view-organization'),
            ], [
                'title' => 'Ride Profile Type',
                'url' => 'user_profile_master.php',
                'icon' => 'fa fa-file-text-o',
                'active' => 'RideProfileType',
                'visible' => $userObj->hasPermission('view-user-profile'),
            ], [
                'title' => 'Business Trip Reason',
                'url' => 'trip_reason.php',
                'icon' => 'fa fa-book',
                'active' => 'BusinessTripReason',
                'visible' => $userObj->hasPermission('view-trip-reason'),
            ], [
                'title' => 'Organization Payment Report',
                'url' => 'org_payment_report.php',
                'icon' => 'fa fa-taxi',
                'active' => 'OrganizationPaymentReport',
                'visible' => $userObj->hasPermission('manage-organization-payment-report'),
            ], [
                'title' => 'Organization Outstanding Report',
                'url' => 'org_outstanding_report.php',
                'icon' => 'glyphicon glyphicon-list-alt',
                'active' => 'org_outstanding_report',
                'visible' => $userObj->hasPermission('view-org-outstanding-report'),
            ],
        ],
    ],
    [
        'title' => 'Logout',
        'url' => 'logout.php',
        'icon' => ['class' => 'icon-signin1', 'url' => 'images/logout-icon.png'],
    ],
];

return $menu;
