<?php
define('SITE_ADMIN_URL', 'Admin');
$_REQUEST['CUS_APP_TYPE'] = 'Ride-Delivery-UberX';
$_REQUEST['CUS_PACKAGE_TYPE'] = 'SHARK';
$_REQUEST['CUS_PARENT_UFX_CATID'] = '0';
define('ENABLE_EXTENDED_VERSION_MANUAL_BOOKING', 'Yes');
define('ENABLE_MANUAL_BOOKING_UBERX', 'No');
define('ENABLE_OUR_SERVICES_MENU', 'Yes');
define('RIDE_SHARE_ENABLED', 'Yes');
define('TRACK_ANY_SERVICE_ENABLED', 'Yes');
define('NEARBY_ENABLED', 'Yes');
define('RENT_CARS_ENABLED', 'Yes');
define('RENT_ESTATE_ENABLED', 'Yes');
define('RENT_ITEM_ENABLED', 'Yes');
define('MED_UFX_ENABLED', 'Yes');
define('VC_ENABLED', 'Yes');
define('BIDDING_ENABLED', 'Yes');
define('RUNNER_ENABLED', 'Yes');
define('GENIE_ENABLED', 'Yes');
define('DELIVERALL_ENABLED', 'Yes');
define('UFX_ENABLED', 'Yes');
define('DELIVERY_ENABLED', 'Yes');
define('RIDE_ENABLED', 'Yes');
define('ENC_POS', '85');
define('ENC_IV', 'Sxx7UvuZ7lzgpycU');
define('ENC_KEY', 'FsXkG7J2c6Cbvmnyu3cKrzPltib3UizZ');
define('GCS_SUFFIX', 'lsprox_627526');
define('ENABLE_CJX_X_DOCTOR_V2_THEME', 'No');
define('GOOGLE_PLAN_ACCOUNTS_LIMIT', '10');
define('ENABLE_ORDER_FROM_STORE_KIOSK', 'Yes');
define('ENABLE_SERVER_REQUIREMENT_VALIDATION', 'Yes');
define('ENABLE_BULK_ITEM_DATA', 'Yes');
define('ENABLE_DELIVERYKING_THEME', 'No');
define('ENABLE_ADD_PROVIDER_FROM_STORE', 'Yes');
define('ENABLE_SAFETY_PRACTICE', 'Yes');
define('ENABLE_TAKE_AWAY', 'Yes');
define('IS_SINGLE_STORE_SELECTION', 'No');
define('ENABLE_DELIVERY_PREFERENCE', 'Yes');
define('ENABLE_STORE_CATEGORIES_MODULE', 'Yes');
define('DELIVERY_MODULE_AVAILABLE', 'Yes');
define('DELIVERALL_MODULE_AVAILABLE', 'Yes');
define('RIDE_MODULE_AVAILABLE', 'Yes');
define('ENABLE_RIDE_DELIVERY_X_THEME', 'No');
define('ENABLE_DELIVERY_X_THEME', 'No');
define('ENABLE_SERVICE_X_THEME', 'No');
define('ENABLE_CUBEJEK_X_V3_PRO_THEME', 'Yes');
define('ENABLE_DELIVERALL_X_THEME', 'Yes');
define('ENABLE_RIDE_CX_THEME', 'No');
define('ENABLE_MONGO_CONNECTION', 'Yes');
define('ENABEL_SERVICE_PROVIDER_MODULE', 'Yes');
define('COUNTRY_IMAGE_UPLOAD', 'No');
define('ENABLE_CUBEJEK_X_V3_THEME', 'No');
define('ENABLE_CUBEJEK_X_V2_THEME', 'No');
define('ENABLE_CUBEJEK_X_THEME', 'No');
define('IS_CUBE_X_THEME', 'No');
define('ENABLE_RENTAL_OPTION', 'Yes');
define('ENABLE_MULTI_DELIVERY', 'Yes');
define('ENABLEHOTELPANEL', 'Yes');
define('ENABLEKIOSKPANEL', 'Yes');
define('ONLYDELIVERALL', 'No');
define('DELIVERALL', 'Yes');
define('T_SITE_FOLDER_PANEL_PATH', '/');
if (defined('T_SITE_FOLDER_PANEL_PATH')) {
    $tconfig['tsite_folder'] = T_SITE_FOLDER_PANEL_PATH;
} else {
    $tconfig['tsite_folder'] = '/';
}
// DO NOT CHANGE SITE_ADMIN_URL
if (!defined('SITE_ADMIN_URL')) {
    define('SITE_ADMIN_URL', 'admin');
}
// DO NOT CHANGE SITE_ADMIN_URL
define('ManualBookingAPIUrl', 'webservice_shark.php');
define('HotelAPIUrl', 'webservice_shark.php');
// Added By HV on 09-11-2020 for General webservice for web as discussed with KS
define('WEBSERVICE_API_FILE_NAME', 'webservice_shark.php');
define('ENABLE_CHANGE_CURRENCY_ROUNDING_OPTION', 'Yes');
// Used in provider application
// added by SP for country images on 07-10-2019 start
if (!defined('COUNTRY_IMAGE_UPLOAD')) {
    define('COUNTRY_IMAGE_UPLOAD', 'Yes');
}
define('ENABLE_EXPIRE_DOCUMENT', 'No'); // Added By HJ On 09-12-2019 For Show/Hide (Restrict Drivers to be online if one or more document is expired) Button As Per Discuss with KS
define('ENABLE_NEW_WALLET_WITHDRAWAL_FLOW_DRIVER', $IS_INHOUSE_DOMAINS ? 'Yes' : 'No');
if (empty($mongoConnectionStr)) {
    $mongoConnectionStr = 'mongodb://localhost:27017/';
}
if (!defined('ENABLE_MONGO_CONNECTION')) {
    define('ENABLE_MONGO_CONNECTION', $IS_INHOUSE_DOMAINS ? 'Yes' : 'No');
}
define('ENABLE_MEMCACHED', 'No');
define('AUTH_EMAIL_SYSTEM', 'systemuser@system.com');
define('INTERVAL_SECONDS', '86400'); // Added By HJ On 13-03-2020 Which is Used in Webservice and God's View
if (!defined('ENABLE_ADD_PROVIDER_FROM_STORE')) {
    define('ENABLE_ADD_PROVIDER_FROM_STORE', !empty($_REQUEST['CUS_ENABLE_ADD_PROVIDER_FROM_STORE']) ? $_REQUEST['CUS_ENABLE_ADD_PROVIDER_FROM_STORE'] : 'No');
}
if (!defined('ENABLE_SERVER_REQUIREMENT_VALIDATION')) {
    define('ENABLE_SERVER_REQUIREMENT_VALIDATION', SITE_TYPE === 'Demo' ? 'No' : 'Yes');
}
define('ENABLE_CACHE_QUERIES_DATA', 'Yes');
define('ENABLE_DELIVERY_TIP_IN_HISTORY', 'Yes');
define('ENABLE_TAX_IN_GENIE', 'No');
define('PICK_DROP_GENIE', 'Yes');
define('ENABLE_TAX_IN_TOLL_OTHER_CHARGES', 'Yes');
define('ENABLE_PRESCRIPTION_UPLOAD', 'Yes');
define('ENABLE_MANUAL_BOOKING_DELIVERY', 'No');
define('ENABLE_LIVE_CHAT_TRACK_ORDER', 'Yes');
if (!defined('ENABLE_EXTENDED_VERSION_MANUAL_BOOKING')) {
    define('ENABLE_EXTENDED_VERSION_MANUAL_BOOKING', 'No');
}
if (!defined('ENABLE_WEBSERVICECALL_MANUALBOOKING')) {
    define('ENABLE_WEBSERVICECALL_MANUALBOOKING', 'No');
}
if (!defined('ENABLE_MONGO_OPS')) {
    define('ENABLE_MONGO_OPS', 'No');
}
if (!defined('IS_CUBEX_APP')) {
    define('IS_CUBEX_APP', 'No');
}
if (!defined('IS_DELIVERYKING_APP')) {
    define('IS_DELIVERYKING_APP', 'No');
}
if (!defined('ENABLE_EARN_PAGE')) {
    define('ENABLE_EARN_PAGE', 'Yes');
}
define('ENABLE_DYNAMIC_CREATE_PAGE', 'Yes');
define('CKFINDER_LICENCE_NAME', 'ckfinder_licence');
define('CKFINDER_LICENCE_KEY', '*D?F-*1**-C**R-*C**-*5**-Q*V*-2**H');
if (!defined('ENABLE_ORDER_FROM_STORE_KIOSK')) {
    define('ENABLE_ORDER_FROM_STORE_KIOSK', 'No');
}
if (!defined('KIOSK_ORDER_NO_LENGTH')) {
    define('KIOSK_ORDER_NO_LENGTH', 4);
}
if (!defined('STORE_KIOSK_ORDER_PLACED_SCREEN_TIME')) {
    define('STORE_KIOSK_ORDER_PLACED_SCREEN_TIME', 20);
}
if (!defined('USER_IDLE_TIMER')) {
    define('USER_IDLE_TIMER', 4);
}
if (!defined('GOOGLE_PLAN_ACCOUNTS_LIMIT')) {
    define('GOOGLE_PLAN_ACCOUNTS_LIMIT', 10);
}
if (!defined('RIDE_SHARE_PASSENGER_NOS')) {
    define('RIDE_SHARE_PASSENGER_NOS', 10);
}
if (!defined('IS_CUBEX_APP')) {
    define('IS_CUBEX_APP', 'No');
}

if (!defined('IS_CUBE_X_V2_THEME')) {
    define('IS_CUBE_X_V2_THEME', '');
}

// Added By HJ On 10-08-2019 For Define URL name For Login and Sign Up Of Front Panel Start
/*$cjSignIn = "cj-sign-in";
$cjSignUp = "cj-SignUp";
$cjProviderLogin = "cj-provider-login";
$cjDriverLogin = "cj-driver-login";
$cjUserLogin = "cj-user-login";
$cjRiderLogin = "cj-rider-login";
$cjCompanyLogin = "cj-company-login";
$cjOrganizationLogin = "cj-organization-login";
$cjSignUpUser = "cj-sign-up-user";
$cjSignUpRider = "cj-sign-up-rider";
$cjSignupCompany = "cj-sign-up";
$cjSignupRestaurant = "cj-sign-up-restaurant";
$cjSignupOrganization = "cj-sign-up-organization";*/
// if (IS_CUBE_X_THEME == 'Yes' || ENABLE_CUBEJEK_X_THEME == 'Yes' || ENABLE_RIDE_CX_THEME == 'Yes' || ENABLE_DELIVERALL_X_THEME == 'Yes' || ENABLE_DELIVERY_X_THEME == 'Yes' || ENABLE_RIDE_DELIVERY_X_THEME == 'Yes' || ENABLE_SERVICE_X_THEME == 'Yes' || ENABLE_DELIVERYKING_THEME == 'Yes') {
$cjSignIn = $cjUserLogin = $cjRiderLogin = 'sign-in?type=user';
$cjCompanyLogin = 'sign-in?type=company';
$cjOrganizationLogin = 'sign-in?type=organization';
if (IS_CUBE_X_V2_THEME === 'Yes' || ENABLE_CUBEJEK_X_V3_THEME === 'Yes' || ENABLE_CUBEJEK_X_V3_PRO_THEME === 'Yes') {
    $cjProviderLogin = $cjDriverLogin = 'sign-in?type=provider';
} else {
    $cjProviderLogin = $cjDriverLogin = 'sign-in?type=driver';
}



// }
define('RANDOM_COLORS_ARR', ['#2EAA0C', '#0b89fe', '#4BB5F5', '#7a497f', '#00537b', '#363e4f', '#078a01', '#e97318', '#FFA60A', '#3CCA59', '#027BFF', '#e6008b', '#e9b600', '#FC6542', '#eb4b01', '#00d094', '#5773c2', '#C60C0C', '#7a00e5', '#4D0ED6', '#c000e2', '#343438', '#F98766', '#d25179', '#903258', '#5855D6', '#fea208', '#fc4d00', '#44ac00', '#000000', '#29d4c1', '#0a5687', '#ebb32e', '#557bda', '#ac7ffc', '#f02b7d', '#f89321', '#2f009c', '#6200ed', '#00acec', '#3b5998', '#99001e', '#d900be', '#810071', '#009b9b', '#520b4e', '#b1adfd', '#0cb14b', '#0089d1', '#02270e', '#114f48', '#1c2c54', '#d175b7']);
// Used in provider application
// BG_COLOR
// TEXT_COLOR - fff
$color = [['BG_COLOR' => '#2EAA0C', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#0b89fe', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#4BB5F5', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#7a497f', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#00537b', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#363e4f', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#078a01', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#e97318', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#FFA60A', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#3CCA59', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#027BFF', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#e6008b', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#e9b600', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#FC6542', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#eb4b01', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#00d094', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#5773c2', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#C60C0C', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#7a00e5', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#4D0ED6', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#c000e2', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#343438', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#F98766', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#d25179', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#903258', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#5855D6', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#fea208', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#fc4d00', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#44ac00', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#000000', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#29d4c1', 'TEXT_COLOR' => '#000000'], ['BG_COLOR' => '#0a5687', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#ebb32e', 'TEXT_COLOR' => '#000000'], ['BG_COLOR' => '#557bda', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#ac7ffc', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#f02b7d', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#f89321', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#2f009c', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#6200ed', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#00acec', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#3b5998', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#99001e', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#d900be', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#810071', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#009b9b', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#520b4e', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#b1adfd', 'TEXT_COLOR' => '#000000'], ['BG_COLOR' => '#0cb14b', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#0089d1', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#02270e', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#114f48', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#1c2c54', 'TEXT_COLOR' => '#ffffff'], ['BG_COLOR' => '#d175b7', 'TEXT_COLOR' => '#ffffff']];
define('RANDOM_COLORS_KEY_VAL_ARR', $color);
if (!defined('APP_THEME_COLOR')) {
    define('APP_THEME_COLOR', '#00A9B7');
}
define('API_SERVICE_DOMAIN', 'apiservice.'.get_server_domain($_SERVER['HTTP_HOST']));

generateDomainName();
if (empty($tconfig['tsite_sc_host']) || empty($tconfig['tsite_sc_php_host'])) {
    $tconfig['tsite_sc_protocol'] = 'https://'; // Protocol to access Socket Cluster.
    $tconfig['tsite_sc_host'] = API_SERVICE_DOMAIN; // In which socket cluster is installed.
    $tconfig['tsite_host_sc_port'] = '2289'; // In which socket cluster is running on.
    $tconfig['tsite_host_sc_path'] = '/socketcluster/'; // This path should not change.
    // Yalgaar settings url
    $tconfig['tsite_yalgaar_url'] = 'https://'.$_SERVER['SERVER_ADDR'].':0000';
    // Yalgaar settings url
    $tconfig['tsite_sc_php_protocol'] = 'https://'; // Protocol to access Socket Cluster via PHP.
    $tconfig['tsite_sc_php_host'] = API_SERVICE_DOMAIN; // In which socket cluster support for PHP is installed.
    $tconfig['tsite_host_sc_php_port'] = '4073'; // In which socket cluster support for PHP is running on.
    $tconfig['tsite_host_sc_php_path'] = '/publish/'; // This path should not change.
}


if (empty($tconfig['tsite_gmap_replacement_host'])) {
    // google api replacement start
    $tconfig['tsite_gmap_replacement_protocol'] = 'https://';
    $tconfig['tsite_gmap_replacement_host'] = API_SERVICE_DOMAIN;
    $tconfig['tsite_host_gmap_replacement_port'] = '5631';
    $tconfig['tsite_host_gmap_replacement_path'] = '/';
    // google api replacement end
}
if (empty($tconfig['tsite_app_service_host'])) {
    // Appservice start
    $tconfig['tsite_app_service_protocol'] = 'https://';
    $tconfig['tsite_app_service_host'] = API_SERVICE_DOMAIN;
    $tconfig['tsite_host_app_service_port'] = 'CLIENT_LIVE_APP_SERVICE_PORT';
    $tconfig['tsite_host_app_service_path'] = '/';
    // Appservice end
}
// Webrtc configurations
if (empty($tconfig['tsite_webrtc_host'])) {
    $tconfig['tsite_webrtc_protocol'] = 'https://';
    $tconfig['tsite_webrtc_host'] = API_SERVICE_DOMAIN;
    $tconfig['tsite_webrtc_port'] = '4850';
    $tconfig['tsite_webrtc_path'] = '/';
    $tconfig['tsite_webrtc_stun_host'] = API_SERVICE_DOMAIN;
    $tconfig['tsite_webrtc_stun_port'] = '6977';
    $tconfig['tsite_webrtc_turn_host'] = API_SERVICE_DOMAIN;
    $tconfig['tsite_webrtc_turn_port'] = '6977';
    $tconfig['tsite_webrtc_username'] = 'equatorturnserver';
    $tconfig['tsite_webrtc_pass'] = 'RPgXCcxFHuKEqafJ1nZA';
    $file_path = dirname(__DIR__, 3).'/turnserver_cus_config';
    if (file_exists($file_path)) {
        $tmp_turn_data = json_decode(file_get_contents($file_path), true);
        $tconfig['tsite_webrtc_username'] = $tmp_turn_data['USER'];
        $tconfig['tsite_webrtc_pass'] = $tmp_turn_data['PASSWORD'];
        $tconfig['tsite_webrtc_stun_port'] = $tmp_turn_data['PORT'];
        $tconfig['tsite_webrtc_turn_port'] = $tmp_turn_data['PORT'];
    }
}
global $ICE_SERVER_URLS_ARR;
$ICE_SERVER_URLS_ARR = [
    [
        'URL' => $tconfig['tsite_webrtc_stun_host'],
        'USER_NAME' => $tconfig['tsite_webrtc_username'],
        'PASSWORD' => $tconfig['tsite_webrtc_pass'],
        'STUN_PORT' => $tconfig['tsite_webrtc_stun_port'],
        'STUN_PATH' => '',
        'TURN_PORT' => $tconfig['tsite_webrtc_turn_port'],
        'TURN_PATH' => '',
    ],
    [
        'URL' => 'a.relay.metered.ca',
        'USER_NAME' => '649edbfef2abf2bdde8464e8',
        'PASSWORD' => 'pAL8NdyAJUEKUA/C',
        'STUN_PORT' => '80',
        'STUN_PATH' => '',
        'TURN_PORT' => '80,443',
        'TURN_PATH' => 'transport=tcp',
    ],
];
// Webrtc configurations End
define('GOOGLE_API_REPLACEMENT_URL', $tconfig['tsite_gmap_replacement_protocol'].$tconfig['tsite_gmap_replacement_host'].':'.$tconfig['tsite_host_gmap_replacement_port'].$tconfig['tsite_host_gmap_replacement_path']);
define('APP_SERVICE_URL', $tconfig['tsite_app_service_protocol'].$tconfig['tsite_app_service_host'].':'.$tconfig['tsite_host_app_service_port'].$tconfig['tsite_host_app_service_path']);
define('ENABLE_PXCPRO_THEME', '');
if (!isset($service_categories_ids_arr) && empty($service_categories_ids_arr)) {
    if (!empty($FOOD_ONLY) /* && strtoupper($FOOD_ONLY) == "YES" */) {
        $service_categories_ids_arr = [1];
        $service_categories_ids_arr = explode(',', $service_categories_ids_arr);
    } else {
        if (IS_CUBE_X_V2_THEME === 'Yes' || ENABLE_PXCPRO_THEME === 'Yes') {
            $service_categories_ids_arr = [1, 2, 5];
        } else {
            // $service_categories_ids_arr = [1, 2, 3, 4, 5, 7];
            $service_categories_ids_arr = [1, 2, 3, 5, 12, 13, 14, 15, 16, 17, 18];
            // $service_categories_ids_arr = [2];
        }
    }
}
if (!empty($_REQUEST['type']) && 'getServiceCategories' === $_REQUEST['type']) {
    $_REQUEST['DEFAULT_SERVICE_CATEGORY_ID'] = '';
}
if (isset($_REQUEST['DEFAULT_SERVICE_CATEGORY_ID']) && '' !== $_REQUEST['DEFAULT_SERVICE_CATEGORY_ID']) {
    $service_categories_ids_arr_new_arr = $_REQUEST['DEFAULT_SERVICE_CATEGORY_ID'];
    $service_categories_ids_arr = (array) $service_categories_ids_arr_new_arr;
}


$enablesevicescategory = implode(',', $service_categories_ids_arr);
define('SERVICE_CATEGORIES_ARR', $enablesevicescategory);
define('WEBRTC_SOCKET_URL', $tconfig['tsite_webrtc_protocol'].$tconfig['tsite_webrtc_host'].':'.$tconfig['tsite_webrtc_port'].$tconfig['tsite_webrtc_path']);
define('WEBRTC_STUN_URL', 'stun:'.$tconfig['tsite_webrtc_stun_host'].':'.$tconfig['tsite_webrtc_stun_port']);
define('WEBRTC_TURN_URL', 'turn:'.$tconfig['tsite_webrtc_turn_host'].':'.$tconfig['tsite_webrtc_turn_port']);
$iceServerList = '[{"STUN_URL":"stun:'.$tconfig['tsite_webrtc_stun_host'].':'.$tconfig['tsite_webrtc_stun_port'].'","TURN_URL":"turn:'.$tconfig['tsite_webrtc_turn_host'].':'.$tconfig['tsite_webrtc_turn_port'].'","USER_NAME":"'.$tconfig['tsite_webrtc_username'].'","Password":"'.$tconfig['tsite_webrtc_pass'].'"},{"STUN_URL":"stun:openrelay.metered.ca:80","TURN_URL":"turn:openrelay.metered.ca:80","USER_NAME":"openrelayproject","Password":"openrelayproject"},{"STUN_URL":"stun:openrelay.metered.ca:80","TURN_URL":"turn:openrelay.metered.ca:443","USER_NAME":"openrelayproject","Password":"openrelayproject"},{"STUN_URL":"stun:stun.l.google.com:19302","TURN_URL":"turn:'.$tconfig['tsite_webrtc_turn_host'].':'.$tconfig['tsite_webrtc_turn_port'].'","USER_NAME":"'.$tconfig['tsite_webrtc_username'].'","Password":"'.$tconfig['tsite_webrtc_pass'].'"},{"STUN_URL":"stun:stun1.l.google.com:19302","TURN_URL":"turn:'.$tconfig['tsite_webrtc_turn_host'].':'.$tconfig['tsite_webrtc_turn_port'].'","USER_NAME":"'.$tconfig['tsite_webrtc_username'].'","Password":"'.$tconfig['tsite_webrtc_pass'].'"},{"STUN_URL":"stun:stun2.l.google.com:19302","TURN_URL":"turn:'.$tconfig['tsite_webrtc_turn_host'].':'.$tconfig['tsite_webrtc_turn_port'].'","USER_NAME":"'.$tconfig['tsite_webrtc_username'].'","Password":"'.$tconfig['tsite_webrtc_pass'].'"},{"STUN_URL":"stun:stun3.l.google.com:19302","TURN_URL":"turn:'.$tconfig['tsite_webrtc_turn_host'].':'.$tconfig['tsite_webrtc_turn_port'].'","USER_NAME":"'.$tconfig['tsite_webrtc_username'].'","Password":"'.$tconfig['tsite_webrtc_pass'].'"},{"STUN_URL":"stun:stun4.l.google.com:19302","TURN_URL":"turn:'.$tconfig['tsite_webrtc_turn_host'].':'.$tconfig['tsite_webrtc_turn_port'].'","USER_NAME":"'.$tconfig['tsite_webrtc_username'].'","Password":"'.$tconfig['tsite_webrtc_pass'].'"}]';
define('WEBRTC_ICE_SERVER_LIST', json_decode($iceServerList, true));
if (!defined('ENABLE_OUR_SERVICES_MENU')) {
    define('ENABLE_OUR_SERVICES_MENU', 'No');
}
define('ENABLE_SUB_PAGES', 'Yes');
define('ENABLE_DATAFEILDS_ADMIN', 'No');
define('ENABLE_PIP_MODE', 'Yes');
define('WEBRTC_SOCKET_URL', $tconfig['tsite_webrtc_protocol'].$tconfig['tsite_webrtc_host'].':'.$tconfig['tsite_webrtc_port'].$tconfig['tsite_webrtc_path']);
define('WEBRTC_STUN_URL', 'stun:'.$tconfig['tsite_webrtc_stun_host'].':'.$tconfig['tsite_webrtc_stun_port']);
define('WEBRTC_TURN_URL', 'turn:'.$tconfig['tsite_webrtc_turn_host'].':'.$tconfig['tsite_webrtc_turn_port']);
$iceServerList = '[{"STUN_URL":"stun:'.$tconfig['tsite_webrtc_stun_host'].':'.$tconfig['tsite_webrtc_stun_port'].'","TURN_URL":"turn:'.$tconfig['tsite_webrtc_turn_host'].':'.$tconfig['tsite_webrtc_turn_port'].'","USER_NAME":"'.$tconfig['tsite_webrtc_username'].'","Password":"'.$tconfig['tsite_webrtc_pass'].'"},{"STUN_URL":"stun:openrelay.metered.ca:80","TURN_URL":"turn:openrelay.metered.ca:80","USER_NAME":"openrelayproject","Password":"openrelayproject"},{"STUN_URL":"stun:openrelay.metered.ca:80","TURN_URL":"turn:openrelay.metered.ca:443","USER_NAME":"openrelayproject","Password":"openrelayproject"},{"STUN_URL":"stun:stun.l.google.com:19302","TURN_URL":"turn:'.$tconfig['tsite_webrtc_turn_host'].':'.$tconfig['tsite_webrtc_turn_port'].'","USER_NAME":"'.$tconfig['tsite_webrtc_username'].'","Password":"'.$tconfig['tsite_webrtc_pass'].'"},{"STUN_URL":"stun:stun1.l.google.com:19302","TURN_URL":"turn:'.$tconfig['tsite_webrtc_turn_host'].':'.$tconfig['tsite_webrtc_turn_port'].'","USER_NAME":"'.$tconfig['tsite_webrtc_username'].'","Password":"'.$tconfig['tsite_webrtc_pass'].'"},{"STUN_URL":"stun:stun2.l.google.com:19302","TURN_URL":"turn:'.$tconfig['tsite_webrtc_turn_host'].':'.$tconfig['tsite_webrtc_turn_port'].'","USER_NAME":"'.$tconfig['tsite_webrtc_username'].'","Password":"'.$tconfig['tsite_webrtc_pass'].'"},{"STUN_URL":"stun:stun3.l.google.com:19302","TURN_URL":"turn:'.$tconfig['tsite_webrtc_turn_host'].':'.$tconfig['tsite_webrtc_turn_port'].'","USER_NAME":"'.$tconfig['tsite_webrtc_username'].'","Password":"'.$tconfig['tsite_webrtc_pass'].'"},{"STUN_URL":"stun:stun4.l.google.com:19302","TURN_URL":"turn:'.$tconfig['tsite_webrtc_turn_host'].':'.$tconfig['tsite_webrtc_turn_port'].'","USER_NAME":"'.$tconfig['tsite_webrtc_username'].'","Password":"'.$tconfig['tsite_webrtc_pass'].'"}]';
define('WEBRTC_ICE_SERVER_LIST', json_decode($iceServerList, true));
