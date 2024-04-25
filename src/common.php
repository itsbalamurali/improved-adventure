<?php
require __DIR__.'/../vendor/autoload.php';

$master_service_category_tbl = 'master_service_category_pro';
global $master_service_category_tbl;

Kesk\Web\Common\SystemInfo::Initiate(__DIR__);

require_once $tconfig['tsite_libraries_v'].'include_configurations.php';
