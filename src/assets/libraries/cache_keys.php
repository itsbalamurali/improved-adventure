<?php


/*
 * File Type : PHP
 * File Created On  : 04-08-2020
 * File Created By : HJ
 * Purpose : For Defined Cache Key With Value
 */
$host = '';
if (isset($_SERVER['HTTP_HOST'])) {
    $host = $_SERVER['HTTP_HOST'];
}
$cacheKeysArrTemp = ['country' => 'country_all', 'language_label_' => 'language_label_', 'service_categories' => 'service_categories_config_file', 'currency' => 'currency_all', 'language_master' => 'language_master_all', 'configurations_payment' => 'configurations_payment', 'configurations' => 'configurations', 'vehicle_category_serviceprovider' => 'vehicle_category_ServiceProvider', 'notification_sound_active' => 'notification_sound_active', 'page' => 'page_', 'state' => 'state_all', 'city' => 'city_all', 'language_label_global_config_' => 'language_label_global_config_', 'homecontent' => 'homecontent_apptype', 'pages' => 'static_page', 'vehicle_category' => 'vehicle_category', 'master_vehicle_category' => 'master_vehicle_category'];
$cacheKeysArr = [];
foreach ($cacheKeysArrTemp as $key => $val) {
    $cacheKeysArr[$key] = $host.'_'.$val;
}
