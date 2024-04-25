<?php
include_once 'server_requirement_functions.php';
// Added By HJ On 21-09-2020 For Store setup_info Data into Cache Start
if (empty($getSetupCacheData) || 0 === count($getSetupCacheData)) {
    $setupInfoApcKey = md5('setup_info');
    $getSetupCacheData = $oCache->getData($setupInfoApcKey);
    if (!empty($getSetupCacheData) && count($getSetupCacheData) > 0) {
        $tProjectPortData = $setupData = $getSetupCacheData;
    } else {
        $tProjectPortData = $setupData = $obj->MySQLSelect('SELECT * FROM setup_info LIMIT 0,1');
        $setSetupCacheData = $oCache->setData($setupInfoApcKey, $tProjectPortData);
    }
} else {
    $tProjectPortData = $setupData = $getSetupCacheData;
}
// Added By HJ On 21-09-2020 For Store setup_info Data into Cache End

$tProjectPortData = json_decode($tProjectPortData[0]['tProjectPortData'], true);
$SHOW_ALL_MISSING = (isset($_REQUEST['SHOW_ALL_MISSING']) && 'Yes' === $_REQUEST['SHOW_ALL_MISSING']) ? 'Yes' : 'No';
$server_requirements = ['PHP Version >= 7.1' => ['status' => ((version_compare(PHP_VERSION, '7.1', '>=')) ? 1 : 0), 'current_val' => '', 'suggested_val' => 'true'], 'Mod Security (Must be "Off")' => ['status' => ((false === checkModSecurity()) ? 1 : 0), 'current_val' => '', 'suggested_val' => '<strong>Note: Please contact Technical team to resolve this.</strong>'], '.htaccess Support' => ['status' => ((is_readable($tconfig['tpanel_path'].'.htaccess') && checkHtaccess()) ? 1 : 0), 'current_val' => '', 'suggested_val' => '<strong>Note: Please contact Technical team to resolve this.</strong>'], 'MYSQL localhost server connection' => ['status' => ((false !== stripos(TSITE_SERVER, 'localhost')) ? 1 : 0), 'current_val' => '', 'suggested_val' => '<strong>Note: MySql database server is not hosted on same server. Please contact Technical team for detailed information.</strong>'], 'Nginx (Must be "Disabled")' => ['status' => ((false === stripos($_SERVER['SERVER_SOFTWARE'], 'nginx')) ? 1 : 0), 'current_val' => '', 'suggested_val' => '<strong>Note: Please contact to server\'s support team to disable this.</strong>'], /* 'Force HTTPS (Must be "Disabled")'  =>  array('status' => ((checkForceHttps()) ? 1 : 0),'current_val' => '','suggested_val' => '<strong>Note: Please contact to server\'s support team to disable this.</strong>'), */ 'Ghostscript' => ['status' => ((checkGhostScript()) ? 1 : 0), 'current_val' => '', 'suggested_val' => '<strong>Note: Please contact Technical team to resolve this.</strong>']];

$server_requirements['PHP-Pear Package'] = ['status' => checkPhpPearPackage() ? 1 : 0, 'current_val' => '', 'suggested_val' => '<strong>Note: Please contact Technical team to resolve this.</strong>'];

$php_ini_files = checkIniFiles();

$php_ini_files_list = '';
if (!empty($php_ini_files) && count($php_ini_files) > 0) {
    foreach ($php_ini_files as $php_ini_file) {
        $php_ini_files_list .= '<br>'.$php_ini_file;
    }
}
$server_requirements['PHP ini files'] = ['status' => (!empty($php_ini_files) && count($php_ini_files) > 0) ? 0 : 1, 'current_val' => '', 'suggested_val' => (!empty($php_ini_files) && count($php_ini_files) > 0) ? '<strong>Note: Please remove below ini files.</strong>'.$php_ini_files_list : ''];

$server_requirements_visible = 1;
foreach ($server_requirements as $srkey => $server_requirement) {
    if ('Yes' === $SHOW_ALL_MISSING) {
        $server_requirements[$srkey]['status'] = 0;
    }
    if (0 === $server_requirement['status'] || 'Yes' === $SHOW_ALL_MISSING) {
        $server_requirements_visible = 0;
    }
}
$server_requirements['visible'] = $server_requirements_visible;
$php_ini_settings = [
    'zlib.output_compression (Must be "On")' => [
        'status' => (('On' === ini_get('zlib.output_compression') || '1' === ini_get('zlib.output_compression') || ini_get('zlib.output_compression')) ? 1 : 0),
        'current_val' => 'zlib.output_compression='.ini_get('zlib.output_compression'),
        'suggested_val' => 'zlib.output_compression=On',
    ],
    'post_max_size >= 900MB' => [
        'status' => ((checkPostMaxSize() >= 900) ? 1 : 0),
        'current_val' => 'post_max_size='.checkPostMaxSize().'M',
        'suggested_val' => 'post_max_size=900M',
    ],
    'upload_max_filesize >= 900MB' => [
        'status' => ((checkUploadMaxFileSize() >= 900) ? 1 : 0),
        'current_val' => 'upload_max_filesize='.checkUploadMaxFileSize().'M',
        'suggested_val' => 'upload_max_filesize=900M',
    ],
    'max_execution_time = 0' => [
        'status' => ((ini_get('max_execution_time') <= 0) ? 1 : 0),
        'current_val' => 'max_execution_time='.ini_get('max_execution_time'),
        'suggested_val' => 'max_execution_time=0',
    ],
    'max_input_time = 0' => [
        'status' => ((ini_get('max_input_time') <= 0) ? 1 : 0),
        'current_val' => 'max_input_time='.ini_get('max_input_time'),
        'suggested_val' => 'max_input_time=0',
    ],
    'memory_limit = -1' => [
        'status' => ((-1 === ini_get('memory_limit')) ? 1 : 0),
        'current_val' => 'memory_limit='.ini_get('memory_limit'),
        'suggested_val' => 'memory_limit=-1',
    ],
    'allow_url_fopen (Must be "On")' => [
        'status' => (('On' === ini_get('allow_url_fopen') || '1' === ini_get('allow_url_fopen') || ini_get('allow_url_fopen')) ? 1 : 0),
        'current_val' => 'allow_url_fopen='.ini_get('allow_url_fopen'),
        'suggested_val' => 'allow_url_fopen=On',
    ],
    'max_file_uploads >= 20' => ['status' => ((ini_get('max_file_uploads') >= 20) ? 1 : 0),
        'current_val' => 'max_file_uploads='.ini_get('max_file_uploads'),
        'suggested_val' => 'max_file_uploads=20',
    ],
    'short_open_tag (Must be "On")' => [
        'status' => (('On' === ini_get('short_open_tag') || '1' === ini_get('short_open_tag') || ini_get('short_open_tag')) ? 1 : 0),
        'current_val' => 'short_open_tag='.ini_get('short_open_tag'),
        'suggested_val' => 'short_open_tag=On',
    ],
    'zend.enable_gc (Must be "On")' => [
        'status' => (('On' === ini_get('zend.enable_gc') || '1' === ini_get('zend.enable_gc') || ini_get('zend.enable_gc')) ? 1 : 0),
        'current_val' => 'zend.enable_gc='.ini_get('zend.enable_gc'),
        'suggested_val' => 'zend.enable_gc=On',
    ],
    'max_input_vars >= 10000' => [
        'status' => ((ini_get('max_input_vars') >= 10_000) ? 1 : 0),
        'current_val' => 'max_input_vars='.ini_get('max_input_vars'),
        'suggested_val' => 'max_input_vars=10000',
    ],
    'default_charset = UTF-8' => [
        'status' => (('UTF-8' === ini_get('default_charset')) ? 1 : 0),
        'current_val' => 'default_charset='.ini_get('default_charset'),
        'suggested_val' => 'default_charset=UTF-8',
    ],
];

$php_ini_settings_visible = 1;
foreach ($php_ini_settings as $pskey => $ini_setting) {
    if ('Yes' === $SHOW_ALL_MISSING) {
        $php_ini_settings[$pskey]['status'] = 0;
    }
    if (0 === $ini_setting['status'] || 'Yes' === $SHOW_ALL_MISSING) {
        $php_ini_settings_visible = 0;
    }
}
$php_ini_settings['visible'] = $php_ini_settings_visible;
$extensions = get_loaded_extensions();
$php_extensions = [
    'exif' => (in_array('exif', $extensions, true)) ? 1 : 0,
    'mbstring' => (in_array('mbstring', $extensions, true)) ? 1 : 0,
    'curl (version >= 7.75.0)' => (in_array('curl', $extensions, true) && checkCurlVersion()) ? 1 : 0,
    'gd' => (in_array('gd', $extensions, true)) ? 1 : 0,
    'ionCube Loader' => (in_array('ionCube Loader', $extensions, true)) ? 1 : 0,
    'mysqli' => (in_array('mysqli', $extensions, true)) ? 1 : 0,
    'dom' => (in_array('dom', $extensions, true)) ? 1 : 0,
    'fileinfo' => (in_array('fileinfo', $extensions, true)) ? 1 : 0,
    'ctype' => (in_array('ctype', $extensions, true)) ? 1 : 0,
    'gettext' => (in_array('gettext', $extensions, true)) ? 1 : 0,
    'hash' => (in_array('hash', $extensions, true)) ? 1 : 0,
    'json' => (in_array('json', $extensions, true)) ? 1 : 0,
    'libxml' => (in_array('libxml', $extensions, true)) ? 1 : 0,
    'mcrypt' => (in_array('mcrypt', $extensions, true) ? 1 : (version_compare(PHP_VERSION, '7.2', '>=') ? 1 : 0)),
    'mysqlnd' => (in_array('mysqlnd', $extensions, true)) ? 1 : 0,
    'openssl' => (in_array('openssl', $extensions, true)) ? 1 : 0,
    'sockets' => (in_array('sockets', $extensions, true)) ? 1 : 0,
    'zlib' => (in_array('zlib', $extensions, true)) ? 1 : 0,
    'soap' => (in_array('soap', $extensions, true)) ? 1 : 0,
    'memcache-4.0.5.2' => (in_array('memcache', $extensions, true)) ? 1 : 0,
    'mongodb' => (in_array('mongodb', $extensions, true)) ? 1 : 0,
    'imagick' => (in_array('imagick', $extensions, true)) ? 1 : 0,
    'ffmpeg' => (checkffmpeg()) ? 1 : 0,
    // 'apcu'              => (in_array('apcu', $extensions)) ? 1 : 0,
];

$php_extensions_visible = 1;
foreach ($php_extensions as $pekey => $extension) {
    if ('Yes' === $SHOW_ALL_MISSING) {
        $php_extensions[$pekey] = 0;
    }
    if (0 === $extension || 'Yes' === $SHOW_ALL_MISSING) {
        $php_extensions_visible = 0;
    }
}
$php_extensions['visible'] = $php_extensions_visible;
$mysql_settings = ['default_charset = UTF-8' => ['status' => (('utf8' === checkSqlCharset()) ? 1 : 0), 'current_val' => checkSqlCharset(), 'suggested_val' => 'default_charset=utf8'], 'sql_mode = NO_ENGINE_SUBSTITUTION' => ['status' => ((false !== stripos(checkSqlMode(), 'NO_ENGINE_SUBSTITUTION')) ? 1 : 0), 'current_val' => checkSqlMode(), 'suggested_val' => 'sql_mode=NO_ENGINE_SUBSTITUTION'], 'mysql strict mode (Must be "Off")' => ['status' => ((false !== stripos(checkSqlMode(), 'STRICT')) ? 0 : 1), 'current_val' => '', 'suggested_val' => 'sql_mode=NO_ENGINE_SUBSTITUTION'], 'innodb_file_per_table (Must be "On")' => ['status' => ((1 === check_innodb_file_per_table()) ? 1 : 0), 'current_val' => '', 'suggested_val' => 'innodb_file_per_table=1'], 'query_cache_type = 0' => ['status' => ((0 === check_query_cache_type() || 'OFF' === check_query_cache_type()) ? 1 : 0), 'current_val' => '', 'suggested_val' => 'query_cache_type=0'], 'open_files_limit >= 10000' => ['status' => ((check_open_files_limit() >= 10_000) ? 1 : 0), 'current_val' => check_open_files_limit(), 'suggested_val' => 'open_files_limit=10000'], 'max_allowed_packet >= 256MB' => ['status' => ((check_max_allowed_packet() >= 268_435_456) ? 1 : 0), 'current_val' => check_max_allowed_packet(), 'suggested_val' => 'max_allowed_packet=268435456'], 'max_user_connections >= 250' => ['status' => ((check_max_user_connections() >= 250 || 0 === check_max_user_connections()) ? 1 : 0), 'current_val' => check_max_user_connections(), 'suggested_val' => 'max_user_connections=250']];

$memory_info = getSystemMemInfo();
$MemTotal = trim(str_replace(['kb', 'KB', 'kB', 'Kb'], '', $memory_info['MemTotal']));

$other_params1 = 200;
$other_params2 = 5;
$innodb_buffer_pool_size_value1 = (0.4 * $MemTotal);
$innodb_buffer_pool_size1 = ((0.4 * $MemTotal) / 1_024) / 1_024;
$innodb_buffer_pool_size1 = ($innodb_buffer_pool_size1 < 1) ? round($innodb_buffer_pool_size1 * 1_024).' MB' : round($innodb_buffer_pool_size1, 1).' GB';

$innodb_buffer_pool_size_value2 = (0.5 * $MemTotal);
$innodb_buffer_pool_size2 = ((0.5 * $MemTotal) / 1_024) / 1_024;
$innodb_buffer_pool_size2 = ($innodb_buffer_pool_size2 < 1) ? round($innodb_buffer_pool_size2 * 1_024).' MB' : round($innodb_buffer_pool_size2).' GB';

$memtotal1 = (0.6 * $MemTotal) / 1_024;
$memtotal2 = (0.65 * $MemTotal) / 1_024;

$max_connections1 = ($memtotal1 - ($other_params1 + ($innodb_buffer_pool_size_value2 / 1_024))) / $other_params2;
$max_connections1 = round($max_connections1);
$max_connections2 = ($memtotal2 - ($other_params1 + ($innodb_buffer_pool_size_value2 / 1_024))) / $other_params2;
$max_connections2 = round($max_connections2);

$current_innodb_buffer_pool_size = check_innodb_buffer_pool_size();
$current_innodb_buffer_pool_size = ($current_innodb_buffer_pool_size / 1_024) / 1_024;
$current_innodb_buffer_pool_size = ($current_innodb_buffer_pool_size < 1) ? round($current_innodb_buffer_pool_size * 1_024).' MB' : round($current_innodb_buffer_pool_size, 1).' GB';

$current_max_connections = check_max_connections();
if (check_innodb_buffer_pool_size() >= $innodb_buffer_pool_size_value1 && 'Yes' !== $SHOW_ALL_MISSING) {
    $innodb_buffer_pool_size = 'innodb_buffer_pool_size >= '.$innodb_buffer_pool_size1;
    $mysql_suggestions[$innodb_buffer_pool_size] = 1;

    if ($MODULES_OBJ->isEnableAdminPanelV2()) {
        $innodb_buffer_pool_size = 'innodb_buffer_pool_size >= '.$innodb_buffer_pool_size1;
        $mysql_settings[$innodb_buffer_pool_size] = ['status' => 1, 'current_val' => $current_innodb_buffer_pool_size, 'suggested_val' => 'innodb_buffer_pool_size='.($innodb_buffer_pool_size_value2 * 1_024)];
    }
} else {
    $innodb_buffer_pool_size = 'innodb_buffer_pool_size >= '.$innodb_buffer_pool_size1.' & <= '.$innodb_buffer_pool_size2.'<br><small><strong>Current Value: '.$current_innodb_buffer_pool_size.'</strong></small><br><small><strong>Recommended Value: '.$innodb_buffer_pool_size2.'</strong><br><input type="text" class="suggested-value" value="innodb_buffer_pool_size='.($innodb_buffer_pool_size_value2 * 1_024).'" size="'.(strlen('innodb_buffer_pool_size = '.($innodb_buffer_pool_size_value2 * 1_024)) - 3).'" style="border: none; outline: none"><i class="fa fa-copy copy-value" style="margin-left: 10px; cursor: pointer" data-toggle="tooltip" data-title="Click to copy below value"></i></small>';
    $mysql_suggestions[$innodb_buffer_pool_size] = 0;

    if ($MODULES_OBJ->isEnableAdminPanelV2()) {
        $innodb_buffer_pool_size = 'innodb_buffer_pool_size >= '.$innodb_buffer_pool_size1;
        $mysql_settings[$innodb_buffer_pool_size] = ['status' => 0, 'current_val' => $current_innodb_buffer_pool_size, 'suggested_val' => 'innodb_buffer_pool_size='.($innodb_buffer_pool_size_value2 * 1_024)];
    }
}

if (((check_max_connections() >= $max_connections1 && check_max_connections() <= $max_connections2) || check_max_connections() >= $max_connections2) && 'Yes' !== $SHOW_ALL_MISSING) {
    $max_connections_text = 'max_connections  >= '.$max_connections1;
    $mysql_suggestions[$max_connections_text] = 1;

    if ($MODULES_OBJ->isEnableAdminPanelV2()) {
        $max_connections_text = 'max_connections  >= '.$max_connections1;
        $mysql_settings[$max_connections_text] = ['status' => 1, 'current_val' => $current_max_connections, 'suggested_val' => 'max_connections='.$max_connections2];
    }
} else {
    $max_connections_text = 'max_connections  >= '.$max_connections1.' & <= '.$max_connections2.'<br><small><strong>Current Value: '.$current_max_connections.'</strong></small><br><small><strong>Recommended Value: <input type="text" class="suggested-value" value="max_connections='.$max_connections2.'" size="'.strlen('max_connections = '.$max_connections2).'" style="border: none; outline: none"></strong><i class="fa fa-copy copy-value" style="cursor: pointer" data-toggle="tooltip" data-title="Click to copy"></i></small>';
    $mysql_suggestions[$max_connections_text] = 0;

    if ($MODULES_OBJ->isEnableAdminPanelV2()) {
        $max_connections_text = 'max_connections  >= '.$max_connections1;
        $mysql_settings[$max_connections_text] = ['status' => 0, 'current_val' => $current_max_connections, 'suggested_val' => 'max_connections='.$max_connections2];
    }
}

$mysql_settings_visible = 1;
foreach ($mysql_settings as $mskey => $mysql_setting) {
    if ('Yes' === $SHOW_ALL_MISSING) {
        $mysql_settings[$mskey]['status'] = 0;
    }

    if (0 === $mysql_setting['status'] || 'Yes' === $SHOW_ALL_MISSING) {
        $mysql_settings_visible = 0;
    }
}
$mysql_settings['visible'] = $mysql_settings_visible;

$cron_last_executed = GetFileData($tconfig['tsite_script_file_path'].'system_cron_jobs_last_executed.txt');

$cron_status = GetFileData($tconfig['tsite_script_file_path'].'system_cron_jobs_status.txt');
$cron_status_log = GetFileData($tconfig['tsite_script_file_path'].'system_cron_jobs_error_log.txt');

$system_cron_jobs = GetFileData($tconfig['tsite_script_file_path'].'system_cron_logs');
$system_cron_jobs = json_decode($system_cron_jobs, true);

$cron_errors = [];
if (round((strtotime(date('Y-m-d H:i:s')) - strtotime($cron_last_executed)) / 60, 2) >= 5 || 'Yes' === $SHOW_ALL_MISSING) {
    $cron_errors[] = 'System Cron Jobs';
} elseif ('error' === $cron_status) {
    $cron_status_log = json_decode($cron_status_log, true);
    foreach ($cron_status_log as $log) {
        $cron_errors[] = $log['purpose'];
    }
}

$running_crons = [];
if (!empty($system_cron_jobs)) {
    foreach ($system_cron_jobs as $cron_job) {
        if (!in_array($cron_job['purpose'], $cron_errors, true)) {
            $running_crons[] = $cron_job['purpose'];
        }
    }
}

if (in_array('System Cron Jobs', $cron_errors, true)) {
    $running_crons = [];
}

$all_requirements = ['Server Settings' => $server_requirements, 'PHP ini Settings' => $php_ini_settings, 'PHP Modules' => $php_extensions];

$system_settings = ['Language Set Up' => checkLanguageSetup('eLanguageFieldsSetup') ? 1 : 0, 'Language Conversion' => checkLanguageSetup('eLanguageLabelConversion') ? 1 : 0, 'Currency Setup' => checkLanguageSetup('eCurrencyFieldsSetup') ? 1 : 0, 'System Type configurations' => checkSystemTypeCongiguration() ? 1 : 0];

$system_settings_visible = 1;
foreach ($system_settings as $sskey => $sys_setting) {
    if ('Yes' === $SHOW_ALL_MISSING) {
        $system_settings[$sskey] = 0;
    }

    if (0 === $sys_setting || 'Yes' === $SHOW_ALL_MISSING) {
        $system_settings_visible = 0;
    }
}
if (0 === $system_settings['System Type configurations']) {
    unset($system_settings['System Type configurations']);
    $sys_type_conf = '<span>System Type configurations (<strong>Error Code: 0X859AR7</strong>)<br><small>Please contact Project Manager or Lead team regarding this error.</small></span>';
    $system_settings[$sys_type_conf] = 0;

    $system_settings_visible = 0;
}
$system_settings['visible'] = $system_settings_visible;
?>
<div class="modal fade requirements-modal" tabindex="-1" role="dialog" id="requirements_modal" data-keyboard="true" data-backdrop="static" style="font-size: 15px">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="width: 100%">
            <div class="modal-body">
                <h1 class="text-center modal-head">Requirements</h1>
                <div class="clearfix"></div>
                <div class="row requirement-list">
                    <div class="col-md-12 ml-auto mr-auto">
                        <ul class="list-group">
                            <?php foreach ($all_requirements as $key => $requirements) { ?>
                                <?php if ('PHP ini Settings' === $key || 'Server Settings' === $key) { ?>
                                    <li class="list-group-item bg-green-light"><?php echo $key; ?></li>
                                    <?php foreach ($requirements as $key1 => $requirement) { ?>
                                        <?php if ('visible' !== $key1) { ?>
                                            <?php if ($requirement['status']) { ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <?php echo $key1; ?>
                                                <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                                                <span class="status-icon-success"><i class="fa fa-check"></i></span>
                                                <?php } else { ?>
                                                <span class="icon server-status-icon success-color"><i class="ri-check-line"></i></span>
                                                <?php } ?>
                                            </li>
                                            <?php } else { ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>
                                                    <?php echo $key1; ?>
                                                    <?php if ('' !== $requirement['suggested_val']) { ?>
                                                        <?php if ('Server Settings' === $key) { ?>
                                                            <?php if ('Socket Cluster' === $key1 || 'PHP Version >= 7.1' === $key1) { ?>
                                                                <br><small><strong>Note: </strong>See below given steps to resolve this.</small>
                                                            <?php } else { ?>
                                                                <br><small><?php echo $requirement['suggested_val']; ?></small>
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            <br><small><strong>Suggested Value: <input type="text" class="suggested-value" value="<?php echo $requirement['suggested_val']; ?>" size="<?php echo strlen($requirement['suggested_val']) + 2; ?>" style="border: none; outline: none;"></strong></small>

                                                            <i class="fa fa-copy copy-value" style="cursor: pointer;" data-toggle="tooltip" data-title="Click to copy"></i>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </span>
                                                <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                                                <span class="status-icon-danger"><i class="fa fa-times"></i></span>
                                                <?php } else { ?>
                                                <span class="icon server-status-icon pending-color"><i class="ri-alert-line"></i></span>
                                                <?php } ?>

                                            </li>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } else { ?>
                                    <li class="list-group-item bg-green-light"><?php echo $key; ?></li>
                                    <?php foreach ($requirements as $key1 => $requirement) { ?>
                                        <?php if ('visible' !== $key1) { ?>
                                            <?php if ($requirement) { ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <?php echo $key1; ?>
                                                <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                                                <span class="status-icon-success"><i class="fa fa-check"></i></span>
                                                <?php } else { ?>
                                                <span class="icon server-status-icon success-color"><i class="ri-check-line"></i></span>
                                                <?php } ?>
                                            </li>
                                            <?php } else { ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <?php echo $key1; ?>
                                                <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                                                <span class="status-icon-danger"><i class="fa fa-times"></i></span>
                                                <?php } else { ?>
                                                <span class="icon server-status-icon pending-color"><i class="ri-alert-line"></i></span>
                                                <?php } ?>
                                            </li>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                            <li class="list-group-item bg-green-light">Server Ports</li>
                            <div class="server-ports-content">
                            </div>
                            <li class="list-group-item bg-green-light">MySQL Settings</li>
                            <?php foreach ($mysql_settings as $mkey => $mysql_setting) { ?>
                                <?php if ('visible' !== $mkey) { ?>
                                    <?php if ($mysql_setting['status']) { ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?php echo $mkey; ?>
                                        <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                                        <span class="status-icon-success"><i class="fa fa-check"></i></span>
                                        <?php } else { ?>
                                        <span class="icon server-status-icon success-color"><i class="ri-check-line"></i></span>
                                        <?php } ?>
                                    </li>
                                    <?php } else { ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                            <?php echo $mkey; ?>
                                            <?php if ('' !== $mysql_setting['suggested_val']) { ?>
                                                <br><small><strong>Suggested Value: <input type="text" class="suggested-value" value="<?php echo $mysql_setting['suggested_val']; ?>" size="<?php echo strlen($mysql_setting['suggested_val']) + 2; ?>" style="border: none; outline: none;"></strong></small>

                                                <i class="fa fa-copy copy-value" style="cursor: pointer;" data-toggle="tooltip" data-title="Click to copy"></i>
                                            <?php } ?>
                                            </span>
                                            <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                                            <span class="status-icon-danger"><i class="fa fa-times"></i></span>
                                            <?php } else { ?>
                                            <span class="icon server-status-icon pending-color"><i class="ri-alert-line"></i></span>
                                            <?php } ?>
                                        </li>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                            <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                            <li class="list-group-item bg-green-light">MySQL Suggestions</li>
                               <?php foreach ($mysql_suggestions as $mkey => $mysql_suggestion) { ?>
                                <?php if ($mysql_suggestion) { ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo $mkey; ?>
                                    <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                                    <span class="status-icon-success"><i class="fa fa-check"></i></span>
                                    <?php } else { ?>
                                    <span class="icon server-status-icon success-color"><i class="ri-check-line"></i></span>
                                    <?php } ?>
                                </li>
                                <?php } else { ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                        <?php echo $mkey; ?>
                                        </span>
                                        <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                                        <span class="status-icon-danger"><i class="fa fa-times"></i></span>
                                        <?php } else { ?>
                                        <span class="icon server-status-icon pending-color"><i class="ri-alert-line"></i></span>
                                        <?php } ?>
                                    </li>
                                <?php } ?>
                            <?php }
                               } ?>
                            <li class="list-group-item bg-green-light">System Settings</li>
                            <?php foreach ($system_settings as $mkey => $sys_setting) { ?>
                                <?php if ('visible' !== $mkey) { ?>
                                    <?php if ($sys_setting) { ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?php echo $mkey; ?>
                                        <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                                        <span class="status-icon-success"><i class="fa fa-check"></i></span>
                                        <?php } else { ?>
                                        <span class="icon server-status-icon success-color"><i class="ri-check-line"></i></span>
                                        <?php } ?>
                                    </li>
                                    <?php } else { ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                            <?php echo $mkey; ?>
                                            </span>
                                            <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                                            <span class="status-icon-danger"><i class="fa fa-times"></i></span>
                                            <?php } else { ?>
                                            <span class="icon server-status-icon pending-color"><i class="ri-alert-line"></i></span>
                                            <?php } ?>
                                        </li>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                            <li class="list-group-item bg-green-light">System Cron Jobs Status</li>
                            <?php foreach ($running_crons as $running_cron) { ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo $running_cron; ?>
                                    <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                                    <span class="status-icon-success"><i class="fa fa-check"></i></span>
                                    <?php } else { ?>
                                    <span class="icon server-status-icon success-color"><i class="ri-check-line"></i></span>
                                    <?php } ?>
                                </li>
                            <?php } if (count($cron_errors) > 0) {
                                foreach ($cron_errors as $cron_error) { ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?php echo $cron_error; ?>
                                        <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                                        <span class="status-icon-danger"><i class="fa fa-times"></i></span>
                                        <?php } else { ?>
                                        <span class="icon server-status-icon pending-color"><i class="ri-alert-line"></i></span>
                                        <?php } ?>
                                    </li>
                                <?php } ?>
                            <?php } ?>

                            <li class="list-group-item bg-green-light">Folder Permissions</li>
                            <div class="folder-permissions-content">
                            </div>
                        </ul>
                    </div>
                </div>
                <div class="text-center">
                    <a href="javascript:void(0);" class="btn btn-info" style="margin-top: 10px" onclick="closeRequirementsModal('requirements_modal')">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="server_settings_modal" data-keyboard="true" data-backdrop="static" style="font-size: 15px">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="width: 100%">
            <div class="modal-body">
                <h1 class="text-center modal-head">Server Settings</h1>
                <div class="clearfix"></div>
                <div class="row requirement-list">
                    <div class="col-md-12 ml-auto mr-auto">
                        <ul class="list-group">
                            <?php foreach ($server_requirements as $key1 => $requirement) { ?>
                                <?php if (/* !$requirement['status'] && */ 'visible' !== $key1) { ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <?php echo $key1; ?>
                                        <?php if ('' !== $requirement['suggested_val']) { ?>
                                            <?php if ('Socket Cluster' === $key1 || 'PHP Version >= 7.1' === $key1) { ?>
                                                <br><small><strong>Note: </strong>See below given steps to resolve this.</small>
                                            <?php } else { ?>
                                                <br><small><?php echo $requirement['suggested_val']; ?></small>
                                            <?php } ?>
                                        <?php } ?>
                                    </span>
                                    <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                                    <span class="status-icon-danger"><i class="fa fa-times"></i></span>
                                    <?php } else { ?>
                                        <?php if (!$requirement['status']) { ?>
                                        <span class="icon server-status-icon pending-color"><i class="ri-alert-line"></i></span>
                                        <?php } else { ?>
                                        <span class="icon server-status-icon success-color"><i class="ri-check-line"></i></span>
                                        <?php } ?>
                                    <?php } ?>
                                </li>
                                <?php } ?>
                            <?php } ?>
                        </ul>
                        <?php // if(!$server_requirements['PHP Version >= 7.1']['status']) {?>
                            <p><strong>How to do? (PHP Version = 7.1)</strong></p>
                            <p>1. For WHM users:</p>
                            <ul class="list-how-to-do">
                                <li>Step 1: Login to WHM</li>
                                <li>Step 2: Search for "MultiPHP" in search box. Open "MultiPHP Manager" (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/server_settings_step_2_phpv.png" target="_blank">View Image</a>)</li>
                                <li>Step 3: Select "PHP 7.1" for your website from dropdown. (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/server_settings_step_3_phpv.png" target="_blank">View Image</a>)</li>
                            </ul>
                        <?php // }?>

                        <?php /*if(!$server_requirements['Socket Cluster']['status']) { ?>
                            <p><strong>How to do? (Socket Cluster)</strong></p>
                            <p>1. For WHM users:</p>
                            <ul class="list-how-to-do">
                                <li>Step 1: Login to WHM</li>
                                <li>Step 2: Search for "Terminal" in search box. Open "Terminal" (<a href="<?= $tconfig['tsite_url'] ?>webimages/server_requirements/mysql_settings_step_2.png" target="_blank">View Image</a>)</li>
                                <li>Step 3: Run following command:</li>
                                <li style="list-style-type: none;">
                                    <ul class="command-list">
                                        <li>
                                            <span style="word-break: break-all;">bash <?= preg_replace('/(\/+)/','/', $tconfig['tsite_script_file_path']); ?>sys_prj_config.sh</span> <br>(<a href="<?= $tconfig['tsite_url'] ?>webimages/server_requirements/server_settings_command1_sc.png" target="_blank">View Image</a>)
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        <?php }*/ ?>
                    </div>
                </div>
                <div class="text-center">
                    <a href="javascript:void(0);" class="btn btn-info" style="margin-top: 10px" onclick="closeRequirementsModal('server_settings_modal')">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="phpini_settings_modal" data-keyboard="true" data-backdrop="static" style="font-size: 15px">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="width: 100%">
            <div class="modal-body">
                <h1 class="text-center modal-head">PHP ini Settings</h1>
                <div class="clearfix"></div>
                <div class="row requirement-list">
                    <div class="col-md-12 ml-auto mr-auto">
                        <ul class="list-group">
                            <?php foreach ($php_ini_settings as $key1 => $requirement) { ?>
                                <?php if (/* !$requirement['status'] && */ 'visible' !== $key1) { ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <?php echo $key1; ?>
                                        <?php if ('' !== $requirement['suggested_val']) { ?>
                                            <br><small><strong>Suggested Value: <input type="text" class="suggested-value" value="<?php echo $requirement['suggested_val']; ?>" size="<?php echo strlen($requirement['suggested_val']); ?>" style="border: none; outline: none;"></strong></small>

                                            <i class="fa fa-copy copy-value" style="cursor: pointer;" data-toggle="tooltip" data-title="Click to copy"></i>
                                        <?php } ?>
                                    </span>
                                    <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                                    <span class="status-icon-danger"><i class="fa fa-times"></i></span>
                                    <?php } else { ?>
                                        <?php if (!$requirement['status']) { ?>
                                        <span class="icon server-status-icon pending-color"><i class="ri-alert-line"></i></span>
                                        <?php } else { ?>
                                        <span class="icon server-status-icon success-color"><i class="ri-check-line"></i></span>
                                        <?php } ?>
                                    <?php } ?>
                                </li>
                                <?php } ?>
                            <?php } ?>
                        </ul>
                        <p><strong>How to do?</strong></p>
                        <p>1. For WHM users:</p>
                        <ul class="list-how-to-do">
                            <li>Step 1: Login to WHM</li>
                            <li>Step 2: Search for "MultiPHP" in search box. Open "MultiPHP Manager" (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/php_ini_settings_step_2.png" target="_blank">View Image</a>)</li>
                            <li>Step 3: Check PHP version for the website. (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/php_ini_settings_step_3.png" target="_blank">View Image</a>)</li>
                            <li>Step 4: Search for "MultiPHP" in search box. Open "MultiPHP INI Editor". (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/php_ini_settings_step_4.png" target="_blank">View Image</a>)</li>
                            <li>Step 5: Click on "Editor Mode" and select PHP version from Step-3. (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/php_ini_settings_step_5.png" target="_blank">View Image</a>)</li>
                            <li>Step 6: Search above PHP ini settings in editor and replace its value with the suggested values. (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/php_ini_settings_step_6.png" target="_blank">View Image</a>)</li>
                            <li>Step 7: Click on "Save". (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/php_ini_settings_step_7.png" target="_blank">View Image</a>)</li>
                            <li>Step 8: Search for "php-fpm" in search box. Open "PHP-FPM service for Apache" and click on "Yes" to restart the service. (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/php_ini_settings_step_8.png" target="_blank">View Image</a>)</li>
                            <li>Step 9: Search for "apache" in search box. Open "HTTP Server (Apache)" and click on "Yes" to restart the service. (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/php_ini_settings_step_9.png" target="_blank">View Image</a>)</li>
                        </ul>
                    </div>
                </div>
                <div class="text-center">
                    <a href="javascript:void(0);" class="btn btn-info" style="margin-top: 10px" onclick="closeRequirementsModal('phpini_settings_modal')">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="php_modules_modal" data-keyboard="true" data-backdrop="static" style="font-size: 15px">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="width: 100%">
            <div class="modal-body">
                <h1 class="text-center modal-head">PHP Modules</h1>
                <div class="clearfix"></div>
                <div class="row requirement-list">
                    <div class="col-md-12 ml-auto mr-auto">
                        <ul class="list-group">
                            <?php foreach ($php_extensions as $key1 => $requirement) { ?>
                                <?php if (/* !$requirement && */ 'visible' !== $key1) { ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <?php echo $key1; ?><br>
                                        <?php if (in_array($key1, ['memcache-4.0.5.2', 'mongodb', 'imagick'], true)) { ?>
                                        <small><strong>Follow Step-5 below</strong></small>
                                        <?php } elseif (in_array($key1, ['ffmpeg'], true)) { ?>
                                        <small><strong>Follow Step-7 below</strong></small>
                                        <?php } else { ?>
                                        <small><strong>Follow Step-6 below</strong></small>
                                        <?php } ?>
                                    </span>
                                    <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                                    <span class="status-icon-danger"><i class="fa fa-times"></i></span>
                                    <?php } else { ?>
                                        <?php if (!$requirement) { ?>
                                        <span class="icon server-status-icon pending-color"><i class="ri-alert-line"></i></span>
                                        <?php } else { ?>
                                        <span class="icon server-status-icon success-color"><i class="ri-check-line"></i></span>
                                        <?php } ?>
                                    <?php } ?>
                                </li>
                                <?php } ?>
                            <?php } ?>
                        </ul>
                        <p><strong>How to do?</strong></p>
                        <p>1. For WHM users:</p>
                        <ul class="list-how-to-do">
                            <li>Step 1: Login to WHM</li>
                            <li>Step 2: Search for "MultiPHP" in search box. Open "MultiPHP Manager" (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/php_modules_step_2.png" target="_blank">View Image</a>)</li>
                            <li>Step 3: Check PHP version for the website. It will he used in next below steps. (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/php_modules_step_3.png" target="_blank">View Image</a>)</li>
                            <li>Step 4: Search for "module installers" in search box. Open "Module Installers" (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/php_modules_step_4.png" target="_blank">View Image</a>)</li>
                            <li>Step 5: Click on "Manage" for "PHP PECL". (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/php_modules_step_5.png" target="_blank">View Image</a>) <br>
                                Select PHP version from Step-3 and click on "Apply". (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/php_modules_step_5_0.png" target="_blank">View Image</a>) <br>
                                Install following modules one by one by entering into "Install a PHP PECL" input and click on "Install Now":
                                <ul class="command-list">
                                    <li>mongodb (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/php_modules_step_5_1.png" target="_blank">View Image</a>)</li>
                                    <li>memcache-4.0.5.2 (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/php_modules_step_5_2.png" target="_blank">View Image</a>)</li>
                                    <li>imagick (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/php_modules_step_5_3.png" target="_blank">View Image</a>)</li>
                                </ul>
                            </li>

                            <li>Step 6: For other modules:
                                <ul class="command-list">
                                    <li>Search for "easyapache 4" in search box. Open "EasyApache 4" (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/php_modules_step_6.png" target="_blank">View Image</a>)</li>
                                    <li>Click on "Customize" in "Currently Installed Packages" (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/php_modules_step_6_1.png" target="_blank">View Image</a>)</li>
                                    <li>Click on "PHP Extensions", search for module for example, "curl" and enable it for the php version (in Step-3) (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/php_modules_step_6_2.png" target="_blank">View Image</a>)</li>
                                    <li>Click on "Review". Your enabled "PHP extension" will be listed under "Review". Click on "Provision" button to finalize.(<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/php_modules_step_6_3.png" target="_blank">View Image</a>)</li>
                                </ul>
                            </li>
                            <li>Step 7: For ffmpeg:
                                <ul class="command-list">
                                    <li>Search for "Terminal" in search box. Open "Terminal" (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/mysql_settings_step_2.png" target="_blank">View Image</a>)</li>
                                    <li>Enter following commands:</li>
                                    <li style="list-style-type: none;">
                                        <ul class="command-list">
                                            <li>sudo rpm --import http://li.nux.ro/download/nux/RPM-GPG-KEY-nux.ro</li>
                                            <li>sudo rpm -Uvh http://li.nux.ro/download/nux/dextop/el7/x86_64/nux-dextop-release-0-5.el7.nux.noarch.rpm</li>
                                            <li>sudo yum install ffmpeg ffmpeg-devel -y</li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li>Step 8: Restart services:
                                <ul class="command-list">
                                    <li>Search for "php-fpm" in search box. Open "PHP-FPM service for Apache" and click on "Yes" to restart the service. (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/php_ini_settings_step_8.png" target="_blank">View Image</a>)</li>
                                    <li>Search for "apache" in search box. Open "HTTP Server (Apache)" and click on "Yes" to restart the service. (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/php_ini_settings_step_9.png" target="_blank">View Image</a>)</li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="text-center">
                    <a href="javascript:void(0);" class="btn btn-info" style="margin-top: 10px" onclick="closeRequirementsModal('php_modules_modal')">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="mysql_settings_modal" data-keyboard="true" data-backdrop="static" style="font-size: 15px">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="width: 100%">
            <div class="modal-body">
                <h1 class="text-center modal-head">MySql Settings</h1>
                <div class="clearfix"></div>
                <div class="row requirement-list">
                    <div class="col-md-12 ml-auto mr-auto">
                        <ul class="list-group">
                            <?php foreach ($mysql_settings as $key1 => $requirement) { ?>
                                <?php if (/* !$requirement['status'] && */ 'visible' !== $key1) { ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <?php echo $key1; ?>
                                        <?php if ('' !== $requirement['suggested_val']) { ?>
                                            <br><small><strong>Suggested Value: <input type="text" class="suggested-value" value="<?php echo $requirement['suggested_val']; ?>" size="<?php echo strlen($requirement['suggested_val']) + 2; ?>" style="border: none; outline: none;"></strong></small>

                                            <i class="fa fa-copy copy-value" style="cursor: pointer;" data-toggle="tooltip" data-title="Click to copy"></i>
                                        <?php } ?>
                                    </span>
                                    <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                                    <span class="status-icon-danger"><i class="fa fa-times"></i></span>
                                    <?php } else { ?>
                                        <?php if (!$requirement['status']) { ?>
                                        <span class="icon server-status-icon pending-color"><i class="ri-alert-line"></i></span>
                                        <?php } else { ?>
                                        <span class="icon server-status-icon success-color"><i class="ri-check-line"></i></span>
                                        <?php } ?>
                                    <?php } ?>
                                </li>
                                <?php } ?>
                            <?php } ?>
                        </ul>
                        <p><strong>How to do?</strong></p>
                        <p>1. For WHM users:</p>
                        <ul class="list-how-to-do">
                            <li>Step 1: Login to WHM</li>
                            <li>Step 2: Search for "Terminal" in search box. Open "Terminal" (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/mysql_settings_step_2.png" target="_blank">View Image</a>)</li>
                            <li>Step 3: Enter following commands:</li>
                            <li style="list-style-type: none;">
                                <ul class="command-list">
                                    <li>vi /etc/my.cnf (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/mysql_settings_command1.png" target="_blank">View Image</a>)</li>
                                    <li>Go to end of file and press "ins" OR "Insert" from keyboard. Copy above suggested values and paste into the terminal. (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/mysql_settings_command2.png" target="_blank">View Image</a>)</li>
                                    <li>Press "Esc". Type ":wq" and press Enter. (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/mysql_settings_command3.png" target="_blank">View Image</a>)</li>
                                </ul>
                            </li>
                            <li>Step 4: Search for "restart" in search box. Open "SQL Server (MySQL)" and click on "Yes" to restart the service. (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/mysql_settings_step_4.png" target="_blank">View Image</a>)</li>
                        </ul>
                    </div>
                </div>
                <div class="text-center">
                    <a href="javascript:void(0);" class="btn btn-info" style="margin-top: 10px" onclick="closeRequirementsModal('mysql_settings_modal')">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="server_ports_modal" data-keyboard="true" data-backdrop="static" style="font-size: 15px">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="width: 100%">
            <div class="modal-body">
                <h1 class="text-center modal-head">Server Ports</h1>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-md-12 ml-auto mr-auto">
                        <ul class="list-group requirement-list"></ul>
                        <p><strong>Note: </strong>Above mentioned ports must be opened for both Inbound & Outbound connection. And these must be publicly accessible. Please contact server support team. If you face any problem then contact technical team.</p>
                    </div>
                </div>
                <div class="text-center">
                    <a href="javascript:void(0);" class="btn btn-info" style="margin-top: 10px" onclick="closeRequirementsModal('server_ports_modal')">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" tabindex="-1" role="dialog" id="cron_jobs_status_modal" data-keyboard="true" data-backdrop="static" style="font-size: 15px">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="width: 100%">
            <div class="modal-body">
                <h1 class="text-center modal-head">Cron Jobs Status</h1>
                <div class="clearfix"></div>
                <div class="row requirement-list">
                    <div class="col-md-12 ml-auto mr-auto">
                        <ul class="list-group">
                            <?php foreach ($cron_errors as $cron_error) { ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><?php echo $cron_error; ?></span>
                                    <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                                    <span class="status-icon-danger"><i class="fa fa-times"></i></span>
                                    <?php } else { ?>
                                    <span class="icon server-status-icon pending-color"><i class="ri-alert-line"></i></span>
                                    <?php } ?>
                                </li>
                            <?php } ?>
                        </ul>
                        <?php if ('System Cron Jobs' === $cron_errors[0]) { ?>
                            <p><strong>How to do?</strong></p>
                            <p>1. For WHM users:</p>
                            <ul class="list-how-to-do">
                                <li>Step 1: Login to WHM</li>
                                <li>Step 2: Open "List Accounts" (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/system_cron_job_step_2.png" target="_blank">View Image</a>)</li>
                                <li>Step 3: Open your website's cpanel from the list. (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/system_cron_job_step_3.png" target="_blank">View Image</a>)</li>
                                <li>Step 4: Search for "Cron Jobs" in search box. Open "Cron Jobs" (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/system_cron_job_step_4.png" target="_blank">View Image</a>)</li>
                                <li>Step 5: Select "Once Per Minute" under "Common Settings" (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/system_cron_job_step_5.png" target="_blank">View Image</a>)</li>
                                <li>Step 6: Copy below line and paste into "Command" input. Click on "Add New Cron Job". (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/system_cron_job_step_6.png" target="_blank">View Image</a>)
                                    <br> <span style="word-break: break-all;">wget -q -O /dev/null <?php echo $tconfig['tsite_url'].'system_cron_jobs.php'; ?> --no-check-certificate</span>
                                </li>
                            </ul>
                        <?php } ?>
                    </div>
                </div>
                <div class="text-center">
                    <a href="javascript:void(0);" class="btn btn-info" style="margin-top: 10px" onclick="closeRequirementsModal('cron_jobs_status_modal')">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" tabindex="-1" role="dialog" id="mysql_suggestions_modal" data-keyboard="true" data-backdrop="static" style="font-size: 15px">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="width: 100%">
            <div class="modal-body">
                <h1 class="text-center modal-head">MySQL Suggestions</h1>
                <div class="clearfix"></div>
                <div class="row requirement-list">
                    <div class="col-md-12 ml-auto mr-auto">
                        <ul class="list-group">
                            <?php foreach ($mysql_suggestions as $mkey => $mysql_suggestion) { ?>
                                <?php // if(!$mysql_suggestion) {?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><?php echo $mkey; ?></span>
                                    <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                                    <span class="status-icon-danger"><i class="fa fa-times"></i></span>
                                    <?php } else { ?>
                                    <span class="icon server-status-icon pending-color"><i class="ri-alert-line"></i></span>
                                    <?php } ?>
                                </li>
                                <?php // }?>
                            <?php } ?>
                        </ul>
                        <p><strong>How to do?</strong></p>
                        <p>1. For WHM users:</p>
                        <ul class="list-how-to-do">
                            <li>Step 1: Login to WHM</li>
                            <li>Step 2: Search for "Terminal" in search box. Open "Terminal" (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/mysql_settings_step_2.png" target="_blank">View Image</a>)</li>
                            <li>Step 3: Enter following commands:</li>
                            <li style="list-style-type: none;">
                                <ul class="command-list">
                                    <li>vi /etc/my.cnf (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/mysql_settings_command1.png" target="_blank">View Image</a>)</li>
                                    <li>Go to end of file and press "ins" OR "Insert" from keyboard. Copy above recommended values and paste into the terminal. (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/mysql_suggestions_command2.png" target="_blank">View Image</a>)</li>
                                    <li>Press "Esc". Type ":wq" and press Enter. (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/mysql_suggestions_command3.png" target="_blank">View Image</a>)</li>
                                </ul>
                            </li>
                            <li>Step 4: Search for "restart" in search box. Open "SQL Server (MySQL)" and click on "Yes". (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/mysql_settings_step_4.png" target="_blank">View Image</a>)</li>
                        </ul>
                    </div>
                </div>
                <div class="text-center">
                    <a href="javascript:void(0);" class="btn btn-info" style="margin-top: 10px" onclick="closeRequirementsModal('mysql_suggestions_modal')">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="folder_permissions_modal" data-keyboard="true" data-backdrop="static" style="font-size: 15px">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="width: 100%">
            <div class="modal-body">
                <h1 class="text-center modal-head">Folder Permissions<br><small><strong>Required "0755" or "0777"</strong></small></h1>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-md-12 ml-auto mr-auto">
                        <ul class="list-group requirement-list"></ul>

                        <p><strong>How to do?</strong></p>
                        <p>1. For WHM users:</p>
                        <ul class="list-how-to-do">
                        <li>Step 1: Login to WHM</li>
                        <li>Step 2: Search for "Terminal" in search box. Open "Terminal" (<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/folder_permissions_step_2.png" target="_blank">View Image</a>)</li>
                        <li>Step 3: Run following commands: <br>
                            <span style="word-break: break-all;">chmod 777 -Rf <?php echo preg_replace('/(\/+)/', '/', $tconfig['tpanel_path']); ?>webimages/</span>(<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/folder_permissions_step_3.png" target="_blank">View Image</a>)<br>
                            <span style="word-break: break-all;">chmod 777 -Rf <?php echo preg_replace('/(\/+)/', '/', $tconfig['tpanel_path']); ?>assets/img/</span>(<a href="<?php echo $tconfig['tsite_url']; ?>webimages/server_requirements/folder_permissions_step_3.png" target="_blank">View Image</a>)
                        </li>
                    </div>
                </div>
                <div class="text-center">
                    <a href="javascript:void(0);" class="btn btn-info" style="margin-top: 10px" onclick="closeRequirementsModal('folder_permissions_modal')">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="system_settings_modal" data-keyboard="true" data-backdrop="static" style="font-size: 15px">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="width: 100%">
            <div class="modal-body">
                <h1 class="text-center modal-head">System Settings</h1>
                <div class="clearfix"></div>
                <div class="row requirement-list">
                    <div class="col-md-12 ml-auto mr-auto">
                        <ul class="list-group">
                            <?php foreach ($system_settings as $key1 => $requirement) { ?>
                                <?php if (/* !$requirement && */ 'visible' !== $key1) { ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><?php echo $key1; ?></span>
                                    <?php if (!$MODULES_OBJ->isEnableAdminPanelV2()) { ?>
                                    <span class="status-icon-danger"><i class="fa fa-times"></i></span>
                                    <?php } else { ?>
                                        <?php if (!$requirement) { ?>
                                        <span class="icon server-status-icon pending-color"><i class="ri-alert-line"></i></span>
                                        <?php } else { ?>
                                        <span class="icon server-status-icon success-color"><i class="ri-check-line"></i></span>
                                        <?php } ?>
                                    <?php } ?>
                                </li>
                                <?php } ?>
                            <?php } ?>
                        </ul>
                        <p><strong>Note: </strong>There is a some problem with system settings. Please contact Technical team to resolve this. </p>
                    </div>
                </div>
                <div class="text-center">
                    <a href="javascript:void(0);" class="btn btn-info" style="margin-top: 10px" onclick="closeRequirementsModal('system_settings_modal')">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

$lAddOnConfiguration_obj = json_decode($setupData[0]['lAddOnConfiguration'], true);
$tProjectPortData_obj = json_decode($setupData[0]['tProjectPortData'], true);
$portsTobeOpened = [];

if (!empty($lAddOnConfiguration_obj['GOOGLE_PLAN'])) {
    $portsTobeOpened[] = $tProjectPortData_obj['tMapsApiPort'];
    $portsTobeOpened[] = $tProjectPortData_obj['tAdminMongoPort'];
}
if (isset($tProjectPortData_obj['tSocketClusterPort']) && '' !== $tProjectPortData_obj['tSocketClusterPort']) {
    $portsTobeOpened[] = $tProjectPortData_obj['tSocketClusterPort'];
}
if (isset($tProjectPortData_obj['tSCClientPHPPort']) && '' !== $tProjectPortData_obj['tSCClientPHPPort']) {
    $portsTobeOpened[] = $tProjectPortData_obj['tSCClientPHPPort'];
}
$portsTobeOpened[] = '2195';
$portsTobeOpened[] = '443';
?>
<div class="modal fade" tabindex="-1" role="dialog" id="things_todo_modal" data-keyboard="true" data-backdrop="static" style="font-size: 15px">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="width: 100%">
            <div class="modal-body">
                <h1 class="text-center modal-head">Things to do on Server</h1>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-md-12">
                        <p>System will use below ports on the server. Make sure that below mentioned ports are opened on your server.</p>
                        <ul class="list-how-to-do">
                            <?php foreach ($portsTobeOpened as $portsTobeOpened_item) { ?>
                            <li class="mb-5"><?php echo $portsTobeOpened_item; ?></li>
                            <?php } ?>
                        </ul>
                        <p>Above mentioned ports must be opened for both Inbound & Outbound connection. And these must be publicly accessible.</p>
                        <hr class="hr-dark" style="margin-bottom: 10px">
                        <a href="<?php echo $tconfig['tsite_url_main_admin'].'sc_diagnostics.php'; ?>" target="_blank">Click here to confirm that socket cluster is working.</a>
                        <hr class="hr-dark" style="margin-bottom: 10px">
                        <p>Make sure that <strong>php-devel</strong> package is installed on your server.</p>
                        <hr class="hr-dark">
                        <?php if (false !== strpos_arr($_SERVER['HTTP_HOST'], $NOT_IN_DOMAINS_ARR)) { ?>
                        <p>Run below command on your server if you have not executed yet. You can do this by going into WHM's terminal. This will install required components on the server.</p>
                        <p><strong>Command:</strong> bash
                        <span style="word-break: break-all;"><?php echo preg_replace('/(\/+)/', '/', $tconfig['tsite_script_file_path']); ?>install_sys_components.sh</span></p>
                        <?php } ?>
                    </div>
                </div>
                <div class="text-center">
                    <a href="javascript:void(0);" class="btn btn-info" style="margin-top: 10px" onclick="closeRequirementsModal('things_todo_modal')">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="api_service_modal" data-keyboard="true" data-backdrop="static" style="font-size: 15px">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="width: 100%">
            <div class="modal-body">
                <h1 class="text-center modal-head">Service Configuration</h1>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-md-12 ml-auto mr-auto">
                        <ul class="list-group requirement-list">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <?php echo API_SERVICE_DOMAIN; ?> subdomain working
                                    <?php if (!$DASHBOARD_OBJ->checkApiServiceDomain()) { ?>
                                    <br><small><strong>Note: Kindly create "<?php echo API_SERVICE_DOMAIN; ?>" subdomain on the server.</strong></small>
                                    <?php } ?>
                                </span>
                                <?php if ($DASHBOARD_OBJ->checkApiServiceDomain()) { ?>
                                <span class="icon server-status-icon success-color"><i class="ri-check-line"></i></span>
                                <?php } else { ?>
                                <span class="icon server-status-icon pending-color"><i class="ri-alert-line"></i></span>
                                <?php } ?>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <?php echo API_SERVICE_DOMAIN; ?> subdomain directly pointed to Server IP
                                    <?php if (!$DASHBOARD_OBJ->apiServiceIPConfig()) { ?>
                                    <br><small><strong>Note: The "<?php echo API_SERVICE_DOMAIN; ?>" subdomain must be pointed directly to the Server IP (<?php echo $DASHBOARD_OBJ->getServerIP(); ?>).</strong></small>
                                    <?php } ?>
                                </span>
                                <?php if ($DASHBOARD_OBJ->apiServiceIPConfig()) { ?>
                                <span class="icon server-status-icon success-color"><i class="ri-check-line"></i></span>
                                <?php } else { ?>
                                <span class="icon server-status-icon pending-color"><i class="ri-alert-line"></i></span>
                                <?php } ?>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    System Component script executed
                                    <?php if (!$DASHBOARD_OBJ->checkSysComponentScript()) { ?>
                                    <br>
                                    <small>
                                        This will install required components on the server.
                                        <p><strong>Command:</strong> bash
                                        <span style="word-break: break-all;"><?php echo preg_replace('/(\/+)/', '/', $tconfig['tsite_script_file_path']); ?>install_sys_components.sh</span></p>
                                    </small>
                                    <?php } else { ?>
                                    <br>
                                    <small>
                                        This will install required components on the server.
                                        <p style="font-size: inherit; line-height: inherit;"><strong>Command:</strong> bash
                                        <span style="word-break: break-all;"><?php echo preg_replace('/(\/+)/', '/', $tconfig['tsite_script_file_path']); ?>sys_prj_config.sh</span></p>
                                    </small>
                                    <?php } ?>
                                </span>
                                <?php if ($DASHBOARD_OBJ->checkSysComponentScript()) { ?>
                                <span class="icon server-status-icon success-color"><i class="ri-check-line"></i></span>
                                <?php } else { ?>
                                <span class="icon server-status-icon pending-color"><i class="ri-alert-line"></i></span>
                                <?php } ?>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    Socket Cluster <br>
                                    <small><a href="<?php echo $tconfig['tsite_url_main_admin'].'sc_diagnostics.php'; ?>" target="_blank">Click here to confirm that socket cluster is working.</a></small>
                                </span>
                                <?php if (is_resource(checkSocketCluster())) { ?>
                                <span class="icon server-status-icon success-color"><i class="ri-check-line"></i></span>
                                <?php } else { ?>
                                <span class="icon server-status-icon pending-color"><i class="ri-alert-line"></i></span>
                                <?php } ?>
                            </li>
                            <?php if ($MODULES_OBJ->mapAPIreplacementAvailable()) { ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        Google Replacement API Service <br>
                                        <?php if (!is_resource(checkMapAPIreplacementAvailable()) || !checkMapAPIService()) { ?>
                                        <small><strong>Note:</strong> Please contact Technical team to resolve this.</small>
                                        <?php } ?>
                                    </span>
                                    <?php if (is_resource(checkMapAPIreplacementAvailable()) && checkMapAPIService()) { ?>
                                    <span class="icon server-status-icon success-color"><i class="ri-check-line"></i></span>
                                    <?php } else { ?>
                                    <span class="icon server-status-icon pending-color"><i class="ri-alert-line"></i></span>
                                    <?php } ?>
                                </li>
                            <?php } ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    Memcache Service <br>
                                    <?php if (!$DASHBOARD_OBJ->checkMemcacheService()) { ?>
                                    <small><strong>Note:</strong> Please contact Technical team to resolve this.</small>
                                    <?php } ?>
                                </span>
                                <?php if ($DASHBOARD_OBJ->checkMemcacheService()) { ?>
                                <span class="icon server-status-icon success-color"><i class="ri-check-line"></i></span>
                                <?php } else { ?>
                                <span class="icon server-status-icon pending-color"><i class="ri-alert-line"></i></span>
                                <?php } ?>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="text-center">
                    <a href="javascript:void(0);" class="btn btn-info" style="margin-top: 10px" onclick="closeRequirementsModal('api_service_modal')">Close</a>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="SHOW_ALL_MISSING" id="show_all_missing" value="<?php echo $SHOW_ALL_MISSING; ?>">