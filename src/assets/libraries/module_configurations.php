<?php



require_once TPATH_CLASS.'include_header.php';
if (!defined('ALLOWED_DOMAINS')) {
    exit;
}

if (!defined('ENABLE_RENTAL_OPTION')) {
    define('ENABLE_RENTAL_OPTION', 'Yes');
}
if (!defined('ENABLE_MULTI_DELIVERY')) {
    define('ENABLE_MULTI_DELIVERY', 'Yes');
}

if (!defined('ENABLEHOTELPANEL')) {
    define('ENABLEHOTELPANEL', 'Yes');
}

if (!defined('ENABLEKIOSKPANEL')) {
    define('ENABLEKIOSKPANEL', 'Yes');
}

if (!defined('ENABLE_BULK_ITEM_DATA')) {
    define('ENABLE_BULK_ITEM_DATA', $IS_INHOUSE_DOMAINS ? 'Yes' : 'No'); // Added By HJ On 07-07-2020 For Import Bult Item Data CSV File
}

if (defined(IS_CUBE_X_THEME)) {
    if (!defined('ENABEL_SERVICE_PROVIDER_MODULE')) {
        if (IS_CUBE_X_THEME === 'Yes' || IS_CUBE_X_V2_THEME === 'Yes') {
            define('ENABEL_SERVICE_PROVIDER_MODULE', 'No');
        } else {
            define('ENABEL_SERVICE_PROVIDER_MODULE', 'Yes');
        }
    }
} else {
    if (!defined('ENABEL_SERVICE_PROVIDER_MODULE')) {
        define('ENABEL_SERVICE_PROVIDER_MODULE', 'Yes');
    }
}

if (!defined('IS_SINGLE_STORE_SELECTION')) {
    define('IS_SINGLE_STORE_SELECTION', !empty($_REQUEST['CUS_IS_SINGLE_STORE_SELECTION']) ? $_REQUEST['CUS_IS_SINGLE_STORE_SELECTION'] : 'No');
}

if (!defined('DELIVERY_MODULE_AVAILABLE')) {
    define('DELIVERY_MODULE_AVAILABLE', 'Yes');
}
if (!defined('DELIVERALL_MODULE_AVAILABLE')) {
    define('DELIVERALL_MODULE_AVAILABLE', 'Yes');
}
if (!defined('RIDE_MODULE_AVAILABLE')) {
    define('RIDE_MODULE_AVAILABLE', 'Yes');
}

if (ENABLE_SERVICE_X_THEME === 'Yes') {
    define('ENABLEHOTELPANEL', 'No');
}

define('RIDE_ENABLED', 'Yes');
define('DELIVERY_ENABLED', 'Yes');
define('UFX_ENABLED', 'Yes');
define('DELIVERALL_ENABLED', 'Yes');
define('GENIE_ENABLED', 'Yes');
define('RUNNER_ENABLED', 'Yes');
define('BIDDING_ENABLED', 'Yes');
define('VC_ENABLED', 'Yes');
define('MED_UFX_ENABLED', 'Yes');
define('RENT_ITEM_ENABLED', 'Yes');
define('RENT_ESTATE_ENABLED', 'Yes');
define('RENT_CARS_ENABLED', 'Yes');
define('NEARBY_ENABLED', 'Yes');
define('TRACK_SERVICE_ENABLED', 'Yes');
define('RIDE_SHARE_ENABLED', 'Yes');
define('TRACK_ANY_SERVICE_ENABLED', 'Yes');
define('PARKING_ENABLED', 'Yes');
define('TAXI_BID_ENABLED', 'Yes');
