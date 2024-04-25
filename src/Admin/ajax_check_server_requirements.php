<?php



include_once '../common.php';

include_once 'server_requirement_functions.php';

$AUTH_OBJ->checkMemberAuthentication();

session_write_close();
$tProjectData = $setupData = $obj->MySQLSelect('SELECT * FROM setup_info');
$tProjectPortData = json_decode($tProjectData[0]['tProjectPortData'], true);
$lAddOnConfiguration_obj = json_decode($tProjectData[0]['lAddOnConfiguration'], true);

$SHOW_ALL_MISSING = (isset($_POST['SHOW_ALL_MISSING']) && 'Yes' === $_POST['SHOW_ALL_MISSING']) ? 'Yes' : 'No';

if (isset($_POST['server_requirement']) && '' !== $_POST['server_requirement']) {
    $server_requirement = $_POST['server_requirement'];

    if ('server_settings' === $server_requirement) {
        $server_settings = [
            'PHP Version = 7.1' => (version_compare(PHP_VERSION, '7.1', '>=')) ? 1 : 0,
            'Mod Security (Must be "Off")' => (false === checkModSecurity()) ? 1 : 0,
            '.htaccess Support' => (is_readable($tconfig['tpanel_path'].'.htaccess') && checkHtaccess()) ? 1 : 0,
            'MYSQL localhost server connection' => (false !== stripos(TSITE_SERVER, 'localhost')) ? 1 : 0,
            'Nginx (Must be "Disabled")' => (false === stripos($_SERVER['SERVER_SOFTWARE'], 'nginx')) ? 1 : 0,
            'Force HTTPS (Must be "Disabled")' => (checkForceHttps()) ? 1 : 0,
            'Ghostscript' => (checkGhostScript()) ? 1 : 0,
        ];

        $server_settings['PHP-Pear Package'] = checkPhpPearPackage() ? 1 : 0;

        $server_settings_status = 1;
        foreach ($server_settings as $srkey => $server_setting) {
            if ('Yes' === $SHOW_ALL_MISSING) {
                $server_settings[$srkey] = 0;
            }

            if (0 === $server_setting || 'Yes' === $SHOW_ALL_MISSING) {
                $server_settings_status = 0;
            }
        }

        $server_requirement_status = $server_settings_status;
    } elseif ('phpini_settings' === $server_requirement) {
        $php_ini_settings = [
            'zlib.output_compression (Must be "On")' => ('On' === ini_get('zlib.output_compression')) ? 1 : 0,
            'post_max_size >= 900MB' => (checkPostMaxSize() >= 900) ? 1 : 0,
            'upload_max_filesize >= 900MB' => (checkUploadMaxFileSize() >= 900) ? 1 : 0,
            'max_execution_time = 0' => (ini_get('max_execution_time') <= 0) ? 1 : 0,
            'max_input_time = 0' => (ini_get('max_input_time') <= 0) ? 1 : 0,
            'memory_limit = -1' => (-1 === ini_get('memory_limit')) ? 1 : 0,
            'allow_url_fopen (Must be "On")' => ('On' === ini_get('allow_url_fopen')) ? 1 : 0,
            'max_file_uploads >= 20' => (ini_get('max_file_uploads') >= 20) ? 1 : 0,
            'short_open_tag (Must be "On")' => ('On' === ini_get('short_open_tag')) ? 1 : 0,
            'zend.enable_gc (Must be "On")' => ('On' === ini_get('zend.enable_gc')) ? 1 : 0,
            'max_input_vars >= 10000' => (ini_get('max_input_vars') >= 10_000) ? 1 : 0,
            'default_charset = UTF-8' => ('UTF-8' === ini_get('default_charset')) ? 1 : 0,
        ];

        $phpini_settings_status = 1;
        foreach ($php_ini_settings as $pskey => $ini_setting) {
            if ('Yes' === $SHOW_ALL_MISSING) {
                $php_ini_settings[$pskey] = 0;
            }

            if (0 === $ini_setting || 'Yes' === $SHOW_ALL_MISSING) {
                $phpini_settings_status = 0;
            }
        }
        $server_requirement_status = $phpini_settings_status;
    } elseif ('php_modules' === $server_requirement) {
        $extensions = get_loaded_extensions();
        $php_extensions = [
            'exif' => (in_array('exif', $extensions, true)) ? 1 : 0,
            'mbstring' => (in_array('mbstring', $extensions, true)) ? 1 : 0,
            'curl' => (in_array('curl', $extensions, true) && checkCurlVersion()) ? 1 : 0,
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

        $php_extensions_status = 1;
        foreach ($php_extensions as $pekey => $extension) {
            if ('Yes' === $SHOW_ALL_MISSING) {
                $php_extensions[$pekey] = 0;
            }

            if (0 === $extension || 'Yes' === $SHOW_ALL_MISSING) {
                $php_extensions_status = 0;
            }
        }
        $server_requirement_status = $php_extensions_status;
    } elseif ('mysql_settings' === $server_requirement) {
        $mysql_settings = [
            'default_charset = UTF-8' => ('utf8' === checkSqlCharset()) ? 1 : 0,
            'sql_mode = NO_ENGINE_SUBSTITUTION' => (false !== stripos(checkSqlMode(), 'NO_ENGINE_SUBSTITUTION')) ? 1 : 0,
            'mysql strict mode (Must be "Off")' => (false !== stripos(checkSqlMode(), 'STRICT')) ? 0 : 1,
            'innodb_file_per_table (Must be "On")' => (1 === check_innodb_file_per_table()) ? 1 : 0,
            'query_cache_type = 0' => (0 === check_query_cache_type() || 'OFF' === check_query_cache_type()) ? 1 : 0,
            'open_files_limit >= 10000' => (check_open_files_limit() >= 10_000) ? 1 : 0,
            'max_allowed_packet >= 256MB' => (check_max_allowed_packet() >= 268_435_456) ? 1 : 0,
            'max_user_connections >= 250' => (check_max_user_connections() >= 250 || 0 === check_max_user_connections()) ? 1 : 0,
        ];

        $mysql_settings_status = 1;
        foreach ($mysql_settings as $mskey => $mysql_setting) {
            if ('Yes' === $SHOW_ALL_MISSING) {
                $mysql_settings[$mskey] = 0;
            }

            if (0 === $mysql_setting || 'Yes' === $SHOW_ALL_MISSING) {
                $mysql_settings_status = 0;
            }
        }
        $server_requirement_status = $mysql_settings_status;
    } elseif ('server_ports' === $server_requirement) {
        $pStatus = 0;
        $ports = [2_195];
        $ports_list = [];
        foreach ($ports as $port) {
            $host = $_SERVER['HTTP_HOST'];
            if (2_195 === $port) {
                $host = 'gateway.push.apple.com';
            }
            $ports_list[$port] = $pStatus ? 1 : (checkOpenPort($host, $port)) ? 1 : 0;
        }

        $socket_cluster_status_html = '<br><a href="'.$tconfig['tsite_url_main_admin'].'sc_diagnostics.php?time='.time().'" target="_blank">Click here to confirm that socket cluster is working.</a>';
        if (isset($tProjectPortData['tSocketClusterPort']) && '' !== $tProjectPortData['tSocketClusterPort']) {
            $host = $tconfig['tsite_sc_host'];
            $port = $tProjectPortData['tSocketClusterPort'];
            $port_html = '<span>'.$port.$socket_cluster_status_html.'</span>';
            $ports_list[$port_html] = $pStatus ? 1 : (checkOpenPort($host, $port)) ? 1 : 0;
        } else {
            $host = $tconfig['tsite_sc_host'];
            $port = $tconfig['tsite_host_sc_port'];
            $port_status = (checkOpenPort($host, $port)) ? 1 : 0;
            $port_html = '<span>'.$port.$socket_cluster_status_html.'</span>';
            $ports_list[$port_html] = $pStatus ? 1 : (checkOpenPort($host, $port)) ? 1 : 0;
        }

        if (isset($tProjectPortData['tSCClientPHPPort']) && '' !== $tProjectPortData['tSCClientPHPPort']) {
            $host = $tconfig['tsite_sc_host'];
            $port = $tProjectPortData['tSCClientPHPPort'];
            $port_status = $pStatus ? 1 : (checkOpenPort($host, $port)) ? 1 : 0;
            $ports_list[$port_html] = $pStatus ? 1 : (checkOpenPort($host, $port)) ? 1 : 0;
        }

        $lAddOnConfiguration_obj = json_decode($setupData[0]['lAddOnConfiguration'], true);
        if (!empty($lAddOnConfiguration_obj['GOOGLE_PLAN'])) {
            if (isset($tProjectPortData['tAdminMongoPort']) && '' !== $tProjectPortData['tAdminMongoPort']) {
                $host = API_SERVICE_DOMAIN;
                $port = $tProjectPortData['tAdminMongoPort'];
                $ports_list[$tProjectPortData['tAdminMongoPort']] = $pStatus ? 1 : (checkOpenPort($host, $port)) ? 1 : 0;
            } else {
                $host = API_SERVICE_DOMAIN;
                $port = $tconfig['tmongodb_port'];
                $ports_list[$tconfig['tmongodb_port']] = $pStatus ? 1 : (checkOpenPort($host, $port)) ? 1 : 0;
            }

            if (isset($tProjectPortData['tMapsApiPort']) && '' !== $tProjectPortData['tMapsApiPort']) {
                $host = $tconfig['tsite_gmap_replacement_host'];
                $port = $tProjectPortData['tMapsApiPort'];
                $ports_list[$tProjectPortData['tMapsApiPort']] = $pStatus ? 1 : (checkOpenPort($host, $port)) ? 1 : 0;
            } else {
                $host = $tconfig['tsite_gmap_replacement_host'];
                $port = $tconfig['tsite_host_gmap_replacement_port'];
                $ports_list[$tconfig['tsite_host_gmap_replacement_port']] = $pStatus ? 1 : (checkOpenPort($host, $port)) ? 1 : 0;
            }
        }

        if (isset($tconfig['tsite_webrtc_port']) && '' !== $tconfig['tsite_webrtc_port']) {
            $host = $tconfig['tsite_webrtc_host'];
            $port = $tconfig['tsite_webrtc_port'];
            $port_status = $pStatus ? 1 : (checkOpenPort($host, $port)) ? 1 : 0;
            if ($port_status) {
                $ports_list[$port] = $pStatus ? 1 : (checkOpenPort($host, $port)) ? 1 : 0;
            } else {
                $ports_list[$port] = $pStatus ? 1 : (checkOpenPort($host, $port)) ? 1 : 0;
            }
        }

        if (isset($tconfig['tsite_webrtc_stun_port']) && '' !== $tconfig['tsite_webrtc_stun_port']) {
            $host = $tconfig['tsite_webrtc_stun_host'];
            $port = $tconfig['tsite_webrtc_stun_port'];
            $port_status = $pStatus ? 1 : (checkOpenPort($host, $port)) ? 1 : 0;
            if ($port_status) {
                $ports_list[$port] = $pStatus ? 1 : (checkOpenPort($host, $port)) ? 1 : 0;
            } else {
                $ports_list[$port] = $pStatus ? 1 : (checkOpenPort($host, $port)) ? 1 : 0;
            }
        }

        if (isset($tconfig['tsite_webrtc_turn_port']) && '' !== $tconfig['tsite_webrtc_turn_port'] && $tconfig['tsite_webrtc_turn_port'] !== $tconfig['tsite_webrtc_stun_port']) {
            $host = $tconfig['tsite_webrtc_turn_host'];
            $port = $tconfig['tsite_webrtc_turn_port'];
            $port_status = $pStatus ? 1 : (checkOpenPort($host, $port)) ? 1 : 0;
            if ($port_status) {
                $ports_list[$port] = $pStatus ? 1 : (checkOpenPort($host, $port)) ? 1 : 0;
            } else {
                $ports_list[$port] = $pStatus ? 1 : (checkOpenPort($host, $port)) ? 1 : 0;
            }
        }

        $server_requirement_status = 1;
        $ports_content_html = '';
        $all_ports_content_html = '';
        foreach ($ports_list as $plkey => $port1) {
            $ports_status = $ports_list[$plkey];
            if (0 === $ports_status || 'Yes' === $SHOW_ALL_MISSING) {
                if (!$MODULES_OBJ->isEnableAdminPanelV2()) {
                    $ports_content_html .= '<li class="list-group-item d-flex justify-content-between align-items-center">'.$plkey.'<span class="status-icon-danger"><i class="fa fa-times"></i></span></li>';
                }

                $server_requirement_status = 0;
            }

            $ports_content_html .= '<li class="list-group-item d-flex justify-content-between align-items-center"><span>'.$plkey.'</span>'.(0 === $ports_status ? '<span class="icon server-status-icon pending-color"><i class="ri-alert-line"></i></span>' : '<span class="icon server-status-icon success-color"><i class="ri-check-line"></i></span>').'</li>';

            $all_ports_content_html .= '<li class="list-group-item d-flex justify-content-between align-items-center">'.$plkey;
            if (0 === $ports_status || 'Yes' === $SHOW_ALL_MISSING) {
                if (!$MODULES_OBJ->isEnableAdminPanelV2()) {
                    $all_ports_content_html .= '<span class="status-icon-danger"><i class="fa fa-times"></i></span>';
                } else {
                    $all_ports_content_html .= '<span class="icon server-status-icon pending-color"><i class="ri-alert-line"></i></span>';
                }
            } else {
                if (!$MODULES_OBJ->isEnableAdminPanelV2()) {
                    $all_ports_content_html .= '<span class="status-icon-success"><i class="fa fa-check"></i></span>';
                } else {
                    $all_ports_content_html .= '<span class="icon server-status-icon success-color"><i class="ri-check-line"></i></span>';
                }
            }
            $all_ports_content_html .= '</li>';
        }

        $returnArr['server_requirement_html'] = $ports_content_html;
        $returnArr['all_ports_html'] = $all_ports_content_html;
    } elseif ('cron_jobs_status' === $server_requirement) {
        $cron_last_executed = GetFileData($tconfig['tsite_script_file_path'].'system_cron_jobs_last_executed.txt');

        $cron_status = GetFileData($tconfig['tsite_script_file_path'].'system_cron_jobs_status.txt');
        $server_requirement_status = 1;
        if (round((strtotime(date('Y-m-d H:i:s')) - strtotime($cron_last_executed)) / 60, 2) >= 5 || 'error' === $cron_status) {
            $server_requirement_status = 0;
        }

        if ('Yes' === $SHOW_ALL_MISSING) {
            $server_requirement_status = 0;
        }
    } elseif ('mysql_suggestions' === $server_requirement) {
        $memory_info = getSystemMemInfo();
        $MemTotal = trim(str_replace(['kb', 'kB', 'Kb', 'KB'], '', $memory_info['MemTotal']));

        $other_params1 = 200;
        $other_params2 = 5;
        $innodb_buffer_pool_size_value1 = (0.4 * $MemTotal);
        $innodb_buffer_pool_size_value2 = (0.5 * $MemTotal);

        $memtotal1 = (0.6 * $MemTotal) / 1_024;
        $memtotal2 = (0.65 * $MemTotal) / 1_024;

        $max_connections1 = ($memtotal1 - ($other_params1 + ($innodb_buffer_pool_size_value2 / 1_024))) / $other_params2;
        $max_connections1 = round($max_connections1);
        $max_connections2 = ($memtotal2 - ($other_params1 + ($innodb_buffer_pool_size_value2 / 1_024))) / $other_params2;
        $max_connections2 = round($max_connections2);

        $server_requirement_status = 0;
        if ((check_innodb_buffer_pool_size() >= $innodb_buffer_pool_size_value1 && check_innodb_buffer_pool_size() <= $innodb_buffer_pool_size_value2) || (check_innodb_buffer_pool_size() >= $innodb_buffer_pool_size_value2) && 'Yes' !== $SHOW_ALL_MISSING) {
            $server_requirement_status_alt1 = 1;
        } else {
            $server_requirement_status_alt1 = 0;
        }

        if (((check_max_connections() >= $max_connections1 && check_max_connections() <= $max_connections2) || check_max_connections() >= $max_connections2) && 'Yes' !== $SHOW_ALL_MISSING) {
            $server_requirement_status_alt2 = 1;
        } else {
            $server_requirement_status_alt2 = 0;
        }

        if (0 === $server_requirement_status_alt1 || 0 === $server_requirement_status_alt2 || 'Yes' === $SHOW_ALL_MISSING) {
            $server_requirement_status = 0;
        } else {
            $server_requirement_status = 1;
        }
    } elseif ('folder_permissions' === $server_requirement) {
        $directories = ['webimages', 'assets/img'];
        $all_directories = getDirectoriesList($directories);

        $directory_permissions = [];
        $server_requirement_status = 1;
        foreach ($all_directories as $dkey => $directories) {
            foreach ($directories as $directory) {
                if ('0777' !== $directory['permission'] && '0755' !== $directory['permission']) {
                    $server_requirement_status = 0;
                    if ('sub_dirs' === $dkey) {
                        $dir_path = explode('/', $directory['path']);
                        array_pop($dir_path);
                        $dir_path = implode('/', $dir_path);
                        if (!isset($directory_permissions[$dir_path])) {
                            // $base_path = preg_replace('~/+~', '/', $tconfig['tpanel_path'].$dir_path);
                            $base_path = preg_replace('~/+~', '/', $tconfig['tpanel_path'].$dir_path);
                            $directory_permissions['sub_dirs'][$dir_path] = $directory['permission'];
                        }
                    } else {
                        $directory_permissions['main_dirs'][] = $directory;
                    }
                }
            }
        }

        if ('Yes' === $SHOW_ALL_MISSING) {
            $server_requirement_status = 0;
        }
        $folder_permissions_html = '';
        if (count($directory_permissions) > 0) {
            foreach ($directory_permissions['main_dirs'] as $dir_permission_main) {
                if (!$MODULES_OBJ->isEnableAdminPanelV2()) {
                    $folder_permissions_html .= '<li class="list-group-item d-flex justify-content-between align-items-center"><span>Path: '.$dir_permission_main['path'].'<br><span style="color: #999999;">Current Permission: '.$dir_permission_main['permission'].'</span></span><span class="status-icon-danger"><i class="fa fa-times"></i></span></li>';
                } else {
                    $folder_permissions_html .= '<li class="list-group-item d-flex justify-content-between align-items-center"><span>Path: '.$dir_permission_main['path'].'<br><span style="color: #999999;">Current Permission: '.$dir_permission_main['permission'].'</span></span><span class="icon server-status-icon pending-color"><i class="ri-alert-line"></i></span></li>';
                }
            }

            $directory_permissions['sub_dirs'] = array_filter($directory_permissions['sub_dirs']);
            if (count($directory_permissions['sub_dirs']) > 0) {
                if (!$MODULES_OBJ->isEnableAdminPanelV2()) {
                    $folder_permissions_html .= '<li class="list-group-item"><span class="w-100 pull-left" style="margin-bottom: 5px"><strong>Subfolder permissions missing </strong><span class="status-icon-danger pull-right"><i class="fa fa-times"></i></span></span><span class="w-100">';
                } else {
                    $folder_permissions_html .= '<li class="list-group-item"><span class="w-100 pull-left" style="margin-bottom: 5px"><strong>Subfolder permissions missing </strong><span class="icon server-status-icon pending-color"><i class="ri-alert-line"></i></span></span><span class="w-100">';
                }

                foreach ($directory_permissions['sub_dirs'] as $subdirkey => $dir_permission_sub) {
                    $folder_permissions_html .= '<hr class="w-100 pull-left"><span><span style="word-break: break-all;">Path: '.$subdirkey.'</span><br><span style="color: #999999;">Current Permission: '.$dir_permission_sub.'</span></span><br>';
                }

                $folder_permissions_html .= '</span></li>';
            }
        } else {
            if (!$MODULES_OBJ->isEnableAdminPanelV2()) {
                $folder_permissions_html .= '<li class="list-group-item d-flex justify-content-between align-items-center"><span>All set correctly</span><span class="status-icon-success"><i class="fa fa-check"></i></span></li>';
            } else {
                $folder_permissions_html .= '<li class="list-group-item d-flex justify-content-between align-items-center"><span>All set correctly</span><span class="icon server-status-icon success-color"><i class="ri-check-line"></i></span></li>';
            }
        }

        if (empty($folder_permissions_html)) {
            $server_requirement_status = 1;

            if (!$MODULES_OBJ->isEnableAdminPanelV2()) {
                $folder_permissions_html .= '<li class="list-group-item d-flex justify-content-between align-items-center"><span>All set correctly</span><span class="status-icon-success"><i class="fa fa-check"></i></span></li>';
            } else {
                $folder_permissions_html .= '<li class="list-group-item d-flex justify-content-between align-items-center"><span>All set correctly</span><span class="icon server-status-icon success-color"><i class="ri-check-line"></i></span></li>';
            }
        }
        $returnArr['server_requirement_html'] = $folder_permissions_html;
    } elseif ('system_settings' === $server_requirement) {
        $system_settings = [
            'Language Set Up' => checkLanguageSetup('eLanguageFieldsSetup') ? 1 : 0,
            'Language Conversion' => checkLanguageSetup('eLanguageLabelConversion') ? 1 : 0,
            'Currency Setup' => checkLanguageSetup('eCurrencyFieldsSetup') ? 1 : 0,
            'System Type configurations' => checkSystemTypeCongiguration() ? 1 : 0,
        ];

        $system_settings_status = 1;
        foreach ($system_settings as $sskey => $sys_setting) {
            if ('Yes' === $SHOW_ALL_MISSING) {
                $system_settings[$sskey] = 0;
            }

            if (0 === $sys_setting || 'Yes' === $SHOW_ALL_MISSING) {
                $system_settings_status = 0;
            }
        }
        $server_requirement_status = $system_settings_status;
    }

    $returnArr['Action'] = $server_requirement_status;
    echo json_encode($returnArr);

    exit;
}

return; // Added By NM on 25/8 after confirm with Hemant

exit;
