<?php 
include_once($tconfig['tpanel_path'] . SITE_ADMIN_URL . '/server_requirement_functions.php');

class Dashboard {
    public function __construct() {
		$this->SHOW_ALL_MISSING = isset($_REQUEST['SHOW_ALL_MISSING']) && $_REQUEST['SHOW_ALL_MISSING'] == "Yes" ? 'Yes' : 'No';
    }

    public function server_settings()
    {
        global $tconfig, $MODULES_OBJ;

        $server_settings = array(
	        'PHP Version = 7.1'                 => (version_compare(PHP_VERSION, '7.1', '>=')) ? 1 : 0,    
	        'Mod Security (Must be "Off")'      => (checkModSecurity() == false) ? 1 : 0,
	        '.htaccess Support'                 => (is_readable($tconfig['tpanel_path'].'.htaccess') && checkHtaccess()) ? 1 : 0,
	        'MYSQL localhost server connection' => (stripos(TSITE_SERVER, 'localhost') !== false) ? 1 : 0,
	        'Nginx (Must be "Disabled")'        => (stripos($_SERVER["SERVER_SOFTWARE"], 'nginx') == false) ? 1 : 0,
	        // 'Force HTTPS (Must be "Disabled")'  => (checkForceHttps()) ? 1 : 0,
	        'Ghostscript'                    	=> (checkGhostScript()) ? 1 : 0,
	    );

	    $server_settings['PHP-Pear Package'] = checkPhpPearPackage() ? 1 : 0;
	    $server_settings['PHP ini files'] = (!empty(checkIniFiles()) && count(checkIniFiles()) > 0) ? 0 : 1;

		$server_settings_status = 1;
	    foreach ($server_settings as $srkey => $server_setting) 
	    {
	    	if($this->SHOW_ALL_MISSING == "Yes")
	        {
	            $server_settings[$srkey] = 0;       
	        }

	        if($server_setting == 0 || $this->SHOW_ALL_MISSING == "Yes")
	        {
	            $server_settings_status = 0;
	        }
	    }

	    return $server_settings_status;
    }

    public function server_ports()
    {
    	global $tconfig, $obj;

    	$tProjectData = $obj->MySQLSelect("SELECT * FROM setup_info");
		$tProjectPortData = json_decode($tProjectData[0]['tProjectPortData'], true);
		$lAddOnConfiguration_obj = json_decode($tProjectData[0]['lAddOnConfiguration'], true);

        $pStatus = 0;
	    $ports = array(2195);
		$ports_list = array();
		foreach ($ports as $port) {
		    $host = $_SERVER['HTTP_HOST'];
		    if($port == 2195) {
		        $host = 'gateway.push.apple.com';
		    }
		    $ports_list[$port] = ($pStatus ? 1 : (checkOpenPort($host, $port) ? 1 : 0));
		}

		$socket_cluster_status_html = '<br><a href="' . $tconfig['tsite_url_main_admin'] . 'sc_diagnostics.php?time='. time() . '" target="_blank">Click here to confirm that socket cluster is working.</a>';
		if(isset($tProjectPortData['tSocketClusterPort']) && $tProjectPortData['tSocketClusterPort'] != "") {
		    $host = $tconfig["tsite_sc_host"];
		    $port = $tProjectPortData['tSocketClusterPort'];
		    $ports_list[$tProjectPortData['tSocketClusterPort']] = ($pStatus ? 1 : (checkOpenPort($host, $port) ? 1 : 0));
		} else {
		    $host = $tconfig["tsite_sc_host"];
		    $port = $tconfig['tsite_host_sc_port'];
		    $port_status = (checkOpenPort($host, $port)) ? 1 : 0;
		    if($port_status) {
		        $port_html = '<span>' . $port . $socket_cluster_status_html .'</span>';
		        $ports_list[$port_html] = ($pStatus ? 1 : (checkOpenPort($host, $port) ? 1 : 0));
		    }
		    else {
		        $ports_list[$port] = ($pStatus ? 1 : (checkOpenPort($host, $port) ? 1 : 0));
		    }
		}

		if(isset($tProjectPortData['tSCClientPHPPort']) && $tProjectPortData['tSCClientPHPPort'] != "") {
		    $host = $tconfig["tsite_sc_host"];
		    $port = $tProjectPortData['tSCClientPHPPort'];
		    $port_status = ($pStatus ? 1 : (checkOpenPort($host, $port) ? 1 : 0));
		    if($port_status) {
		        $port_html = '<span>' . $port . $socket_cluster_status_html .'</span>';
		        $ports_list[$port_html] = ($pStatus ? 1 : (checkOpenPort($host, $port) ? 1 : 0));
		    }
		    else {
		        $ports_list[$port] = ($pStatus ? 1 : (checkOpenPort($host, $port) ? 1 : 0));
		    }
		}

		if(!empty($lAddOnConfiguration_obj['GOOGLE_PLAN'])) {
		    if(isset($tProjectPortData['tAdminMongoPort']) && $tProjectPortData['tAdminMongoPort'] != "") {
		        $host = API_SERVICE_DOMAIN;
		        $port = $tProjectPortData['tAdminMongoPort'];
		        $ports_list[$tProjectPortData['tAdminMongoPort']] = ($pStatus ? 1 : (checkOpenPort($host, $port) ? 1 : 0));
		    } else {
		        $host = API_SERVICE_DOMAIN;
		        $port = $tconfig['tmongodb_port'];
		        $ports_list[$tconfig['tmongodb_port']] = ($pStatus ? 1 : (checkOpenPort($host, $port) ? 1 : 0));
		    }

		    if(isset($tProjectPortData['tMapsApiPort']) && $tProjectPortData['tMapsApiPort'] != "") {
		        $host = $tconfig["tsite_gmap_replacement_host"];
		        $port = $tProjectPortData['tMapsApiPort'];
		        $ports_list[$tProjectPortData['tMapsApiPort']] = ($pStatus ? 1 : (checkOpenPort($host, $port) ? 1 : 0));
		    } else {
		        $host = $tconfig["tsite_gmap_replacement_host"];
		        $port = $tconfig['tsite_host_gmap_replacement_port'];
		        $ports_list[$tconfig['tsite_host_gmap_replacement_port']] = ($pStatus ? 1 : (checkOpenPort($host, $port) ? 1 : 0));
		    }
		}

		if(isset($tconfig['tsite_webrtc_port']) && $tconfig['tsite_webrtc_port'] != "") {
			$host = $tconfig["tsite_webrtc_host"];
		    $port = $tconfig['tsite_webrtc_port'];
		    $port_status = ($pStatus ? 1 : (checkOpenPort($host, $port) ? 1 : 0));
		    if($port_status) {
		        $ports_list[$port] = ($pStatus ? 1 : (checkOpenPort($host, $port) ? 1 : 0));
		    }
		    else {
		        $ports_list[$port] = ($pStatus ? 1 : (checkOpenPort($host, $port) ? 1 : 0));
		    }
		}

		if(isset($tconfig['tsite_webrtc_stun_port']) && $tconfig['tsite_webrtc_stun_port'] != "") {
			$host = $tconfig["tsite_webrtc_stun_host"];
		    $port = $tconfig['tsite_webrtc_stun_port'];
		    $port_status = ($pStatus ? 1 : (checkOpenPort($host, $port) ? 1 : 0));
		    if($port_status) {
		        $ports_list[$port] = ($pStatus ? 1 : (checkOpenPort($host, $port) ? 1 : 0));
		    }
		    else {
		        $ports_list[$port] = ($pStatus ? 1 : (checkOpenPort($host, $port) ? 1 : 0));
		    }
		}

		if(isset($tconfig['tsite_webrtc_turn_port']) && $tconfig['tsite_webrtc_turn_port'] != "" && $tconfig['tsite_webrtc_turn_port'] != $tconfig['tsite_webrtc_stun_port']) {
			$host = $tconfig["tsite_webrtc_turn_host"];
		    $port = $tconfig['tsite_webrtc_turn_port'];
		    $port_status = ($pStatus ? 1 : (checkOpenPort($host, $port) ? 1 : 0));
		    if($port_status) {
		        $ports_list[$port] = ($pStatus ? 1 : (checkOpenPort($host, $port) ? 1 : 0));
		    }
		    else {
		        $ports_list[$port] = ($pStatus ? 1 : (checkOpenPort($host, $port) ? 1 : 0));
		    }
		}

		$server_requirement_status = 1;
	    $ports_content_html = "";
	    $all_ports_content_html = "";
	    foreach ($ports_list as $plkey => $port1) 
	    {
	        $ports_status = $ports_list[$plkey];
	        if($ports_status == 0 || $this->SHOW_ALL_MISSING == "Yes") {
	        	$server_requirement_status = 0;
	        }
	    }
	    
        return $server_requirement_status;
    }

    public function phpini_settings()
    {
        $php_ini_settings = array(
	        'zlib.output_compression (Must be "On")'    => (ini_get('zlib.output_compression') == "On" || ini_get('zlib.output_compression') == "1" || ini_get('zlib.output_compression')) ? 1 : 0,
	        'post_max_size >= 900MB'                    => (checkPostMaxSize() >= 900) ? 1 : 0,
	        'upload_max_filesize >= 900MB'              => (checkUploadMaxFileSize() >= 900) ? 1 : 0,
	        'max_execution_time = 0'                 	=> (ini_get('max_execution_time') <= 0) ? 1 : 0,
	        'max_input_time = 0'                     	=> (ini_get('max_input_time') <= 0) ? 1 : 0,
	        'memory_limit = -1'                         => (ini_get('memory_limit') == -1) ? 1 : 0,
	        'allow_url_fopen (Must be "On")'            => (ini_get('allow_url_fopen') == "On" || ini_get('allow_url_fopen') == "1" || ini_get('allow_url_fopen')) ? 1 : 0,
	        'max_file_uploads >= 20'                    => (ini_get('max_file_uploads') >= 20) ? 1 : 0,
	        'short_open_tag (Must be "On")'             => (ini_get('short_open_tag') == "On" || ini_get('short_open_tag') == "1" || ini_get('short_open_tag')) ? 1 : 0,
	        'zend.enable_gc (Must be "On")'             => (ini_get('zend.enable_gc') == "On" || ini_get('zend.enable_gc') == "1" || ini_get('zend.enable_gc')) ? 1 : 0,
	        'max_input_vars >= 10000'                   => (ini_get('max_input_vars') >= 10000) ? 1 : 0,
	        'default_charset = UTF-8'                   => (ini_get('default_charset') == "UTF-8") ? 1 : 0,
	    );

	    $phpini_settings_status = 1;
	    foreach ($php_ini_settings as $pskey => $ini_setting) 
	    {
	    	if($this->SHOW_ALL_MISSING == "Yes")
	        {
	            $php_ini_settings[$pskey] = 0;       
	        }

	        if($ini_setting == 0 || $this->SHOW_ALL_MISSING == "Yes")
	        {
	            $phpini_settings_status = 0;
	        }
	    }
	    return $phpini_settings_status;
    }

    public function php_modules()
    {
        $extensions = get_loaded_extensions();
	    $php_extensions = array(
	        'exif'              => (in_array('exif', $extensions)) ? 1 : 0,
	        'mbstring'          => (in_array('mbstring', $extensions)) ? 1 : 0,
	        'curl'  			=> (in_array('curl', $extensions) && checkCurlVersion()) ? 1 : 0,
	        'gd'                => (in_array('gd', $extensions)) ? 1 : 0, 
	        'ionCube Loader'    => (in_array('ionCube Loader', $extensions)) ? 1 : 0, 
	        'mysqli'            => (in_array('mysqli', $extensions)) ? 1 : 0, 
	        'dom'               => (in_array('dom', $extensions)) ? 1 : 0,
	        'fileinfo'          => (in_array('fileinfo', $extensions)) ? 1 : 0,
	        'ctype'             => (in_array('ctype', $extensions)) ? 1 : 0, 
	        'gettext'           => (in_array('gettext', $extensions)) ? 1 : 0, 
	        'hash'              => (in_array('hash', $extensions)) ? 1 : 0, 
	        'json'              => (in_array('json', $extensions)) ? 1 : 0, 
	        'libxml'            => (in_array('libxml', $extensions)) ? 1 : 0, 
	        'mcrypt'            => (in_array('mcrypt', $extensions) ? 1 : (version_compare(PHP_VERSION, '7.2', '>=') ? 1 : 0)),
	        'mysqlnd'           => (in_array('mysqlnd', $extensions)) ? 1 : 0, 
	        'openssl'           => (in_array('openssl', $extensions)) ? 1 : 0, 
	        'sockets'           => (in_array('sockets', $extensions)) ? 1 : 0, 
	        'zlib'              => (in_array('zlib', $extensions)) ? 1 : 0, 
	        'soap'              => (in_array('soap', $extensions)) ? 1 : 0, 
	        'memcache-4.0.5.2'  => (in_array('memcache', $extensions)) ? 1 : 0, 
	        'mongodb'           => (in_array('mongodb', $extensions)) ? 1 : 0, 
	        'imagick'           => (in_array('imagick', $extensions)) ? 1 : 0, 
	        'ffmpeg'            => (checkffmpeg()) ? 1 : 0, 
	    );

	    $php_extensions_status = 1;
	    foreach ($php_extensions as $pekey => $extension) 
	    {
	    	if($this->SHOW_ALL_MISSING == "Yes")
	        {
	            $php_extensions[$pekey] = 0;       
	        }

	        if($extension == 0 || $this->SHOW_ALL_MISSING == "Yes")
	        {
	            $php_extensions_status = 0;
	        }
	    }
	    return $php_extensions_status;
    }

    public function mysql_settings()
    {
        $mysql_settings = array(
			'default_charset = UTF-8'				=> (checkSqlCharset() == "utf8") ? 1 : 0,
	        'sql_mode = NO_ENGINE_SUBSTITUTION'     => (stripos(checkSqlMode(), "NO_ENGINE_SUBSTITUTION") !== false) ? 1 : 0,
	        'mysql strict mode (Must be "Off")'     => (stripos(checkSqlMode(), "STRICT")  !== false) ? 0 : 1,
	        'innodb_file_per_table (Must be "On")'  => (check_innodb_file_per_table() == 1) ? 1 : 0,
	        'query_cache_type = 0'                  => (check_query_cache_type() == 0 || check_query_cache_type() == "OFF") ? 1 : 0,
	        'open_files_limit >= 10000'             => (check_open_files_limit() >= 10000) ? 1 : 0,
	        'max_allowed_packet >= 256MB'           => (check_max_allowed_packet() >= 268435456) ? 1 : 0,
	        'max_user_connections >= 250'           => (check_max_user_connections() >= 250 || check_max_user_connections() == 0) ? 1 : 0,
	    );

	    $mysql_settings_status = 1;
	    foreach ($mysql_settings as $mskey => $mysql_setting) 
	    {
	    	if($this->SHOW_ALL_MISSING == "Yes")
	        {
	            $mysql_settings[$mskey] = 0;       
	        }

	        if($mysql_setting == 0 || $this->SHOW_ALL_MISSING == "Yes")
	        {
	            $mysql_settings_status = 0;
	        }
	    }

	    $memory_info = getSystemMemInfo();
	    $MemTotal = trim(str_replace(["kb", "kB", "Kb", "KB"], "", $memory_info['MemTotal']));
	    
	    $other_params1 = 200;
	    $other_params2 = 5;
	    $innodb_buffer_pool_size_value1 = (0.4 * $MemTotal);
	    $innodb_buffer_pool_size_value2 = (0.5 * $MemTotal);

	    $memtotal1 = (0.6 * $MemTotal) / 1024;
	    $memtotal2 = (0.65 * $MemTotal) / 1024;

	    $max_connections1 = ($memtotal1 - ($other_params1 + ($innodb_buffer_pool_size_value2 / 1024))) / $other_params2;
	    $max_connections1 = round($max_connections1);
	    $max_connections2 = ($memtotal2 - ($other_params1 + ($innodb_buffer_pool_size_value2 / 1024))) / $other_params2;
	    $max_connections2 = round($max_connections2);

	    $server_requirement_status = 0;
	    if((check_innodb_buffer_pool_size() >= $innodb_buffer_pool_size_value1 && check_innodb_buffer_pool_size() <= $innodb_buffer_pool_size_value2) || (check_innodb_buffer_pool_size() >= $innodb_buffer_pool_size_value2) && $this->SHOW_ALL_MISSING != 'Yes')
	    {
	        $server_requirement_status_alt1 = 1;
	    }
	    else {
	        $server_requirement_status_alt1 = 0;
	    }

	    if(((check_max_connections() >= $max_connections1 && check_max_connections() <= $max_connections2) || check_max_connections() >= $max_connections2) && $this->SHOW_ALL_MISSING != 'Yes')
	    {
	       $server_requirement_status_alt2 = 1; 
	    }
	    else {
	    	$server_requirement_status_alt2 = 0;	
	    }

	    if($server_requirement_status_alt1 == 0 || $server_requirement_status_alt2 == 0 || $this->SHOW_ALL_MISSING == 'Yes')
	    {
	    	$server_requirement_status = 0;
	    }
	    else {
	    	$server_requirement_status = 1;
	    }

	    if($mysql_settings_status && $server_requirement_status) {
	    	return 1;
	    } else {
	    	return 0;
	    }
    }

    public function mysql_suggestions()
    {
		$memory_info = getSystemMemInfo();
	    $MemTotal = trim(str_replace(["kb", "kB", "Kb", "KB"], "", $memory_info['MemTotal']));
	    
	    $other_params1 = 200;
	    $other_params2 = 5;
	    $innodb_buffer_pool_size_value1 = (0.4 * $MemTotal);
	    $innodb_buffer_pool_size_value2 = (0.5 * $MemTotal);

	    $memtotal1 = (0.6 * $MemTotal) / 1024;
	    $memtotal2 = (0.65 * $MemTotal) / 1024;

	    $max_connections1 = ($memtotal1 - ($other_params1 + ($innodb_buffer_pool_size_value2 / 1024))) / $other_params2;
	    $max_connections1 = round($max_connections1);
	    $max_connections2 = ($memtotal2 - ($other_params1 + ($innodb_buffer_pool_size_value2 / 1024))) / $other_params2;
	    $max_connections2 = round($max_connections2);

	    $server_requirement_status = 0;
	    if((check_innodb_buffer_pool_size() >= $innodb_buffer_pool_size_value1 && check_innodb_buffer_pool_size() <= $innodb_buffer_pool_size_value2) || (check_innodb_buffer_pool_size() >= $innodb_buffer_pool_size_value2) && $this->SHOW_ALL_MISSING != 'Yes')
	    {
	        $server_requirement_status_alt1 = 1;
	    }
	    else {
	        $server_requirement_status_alt1 = 0;
	    }

	    if(((check_max_connections() >= $max_connections1 && check_max_connections() <= $max_connections2) || check_max_connections() >= $max_connections2) && $this->SHOW_ALL_MISSING != 'Yes')
	    {
	       $server_requirement_status_alt2 = 1; 
	    }
	    else {
	    	$server_requirement_status_alt2 = 0;	
	    }

	    if($server_requirement_status_alt1 == 0 || $server_requirement_status_alt2 == 0 || $this->SHOW_ALL_MISSING == 'Yes')
	    {
	    	$server_requirement_status = 0;
	    }
	    else {
	    	$server_requirement_status = 1;
	    }

        return $server_requirement_status;
    }

    public function system_settings()
    {
		$system_settings = array(
	        'Language Set Up'       		=> checkLanguageSetup('eLanguageFieldsSetup') ? 1 : 0,
	        'Language Conversion'   		=> checkLanguageSetup('eLanguageLabelConversion') ? 1 : 0,
	        'Currency Setup'   				=> checkLanguageSetup('eCurrencyFieldsSetup') ? 1 : 0,
	        'System Type configurations'    => checkSystemTypeCongiguration() ? 1 : 0
	    );

	    $system_settings_status = 1;
	    foreach ($system_settings as $sskey => $sys_setting) 
	    {
	    	if($this->SHOW_ALL_MISSING == "Yes")
	        {
	            $system_settings[$sskey] = 0;       
	        }

	        if($sys_setting == 0 || $this->SHOW_ALL_MISSING == "Yes")
	        {
	            $system_settings_status = 0;
	        }
	    }
	    return $system_settings_status;

    }

    public function cron_jobs_status()
    {
		global $tconfig,$MODULES_OBJ;
		$cron_last_executed = file_get_contents($tconfig['tsite_script_file_path'] . "system_cron_jobs_last_executed.txt");

		$cron_status = file_get_contents($tconfig['tsite_script_file_path'] . "system_cron_jobs_status.txt");
		$server_requirement_status = 1;
		if(round(((strtotime(date('Y-m-d H:i:s')) - strtotime($cron_last_executed)) / 60), 2) >= 5 || $cron_status == "error")
		{
			$server_requirement_status = 0;
		}

		if($this->SHOW_ALL_MISSING == "Yes")
        {
            $server_requirement_status = 0;  
        }

		return $server_requirement_status;
    }

    

    public function folder_permissions()
    {
        $directories = array('webimages', 'assets/img');
	    $all_directories = getDirectoriesList($directories);

	    $directory_permissions = array();
	    $server_requirement_status = 1;
	    foreach ($all_directories as $dkey => $directories) 
	    {
	    	foreach ($directories as $directory) 
	    	{
	    		if(isset($directory['permission']) && $directory['permission'] != '0777' && $directory['permission'] != '0755')
		        {
		            $server_requirement_status = 0;
		            if($dkey == "sub_dirs") {
		                $dir_path = explode('/', $directory['path']);
		                array_pop($dir_path);
		                $dir_path = implode('/', $dir_path);
		                if(!isset($directory_permissions[$dir_path]))
		                {
		                    $base_path = preg_replace('~/+~', '/', $tconfig['tpanel_path'].$dir_path);
		                    $directory_permissions['sub_dirs'][$dir_path] = $directory['permission'];
		                }
		            } else {
		                $directory_permissions['main_dirs'][] = $directory;
		            }
		        }
	    	}
	    }

	    if($this->SHOW_ALL_MISSING == 'Yes')
	    {
	    	$server_requirement_status = 0;
	    }
	    $folder_permissions_html = "";
	    if(count($directory_permissions) > 0) 
	    {
		    foreach ($directory_permissions['main_dirs'] as $dir_permission_main) 
		    {
	            $folder_permissions_html .= '<li class="list-group-item d-flex justify-content-between align-items-center"><span>Path: ' . $dir_permission_main['path'] . '<br><span style="color: #999999;">Current Permission: ' . $dir_permission_main['permission'] . '</span></span><span class="status-icon-danger"><i class="fa fa-times"></i></span></li>';
	        }
	        
	        $directory_permissions['sub_dirs'] = array_filter($directory_permissions['sub_dirs']);
	        if(count($directory_permissions['sub_dirs']) > 0) 
	        {
	        	$folder_permissions_html .= '<li class="list-group-item"><span class="w-100 pull-left" style="margin-bottom: 5px"><strong>Subfolder permissions missing </strong><span class="status-icon-danger pull-right"><i class="fa fa-times"></i></span></span><span class="w-100">';

	        	foreach ($directory_permissions['sub_dirs'] as $subdirkey => $dir_permission_sub) 
	        	{
	                $folder_permissions_html .= '<hr class="w-100 pull-left"><span><span style="word-break: break-all;">Path: ' . $subdirkey . '</span><br><span style="color: #999999;">Current Permission: ' . $dir_permission_sub . '</span></span><br>';
	            }
	            
	            $folder_permissions_html .= '</span></li>';
	        }
	    }
	    else {
	    	$folder_permissions_html .= '<li class="list-group-item d-flex justify-content-between align-items-center"><span>All set correctly</span><span class="status-icon-success"><i class="fa fa-check"></i></span></li>';
	    }
		
		if(empty($folder_permissions_html)) {
			$server_requirement_status = 1;
			$folder_permissions_html .= '<li class="list-group-item d-flex justify-content-between align-items-center"><span>All set correctly</span><span class="status-icon-success"><i class="fa fa-check"></i></span></li>';
		}

        $returnArr['server_requirement_html'] = $folder_permissions_html;
		return $server_requirement_status;
    }

    public function getSystemDiagnosticData() {
    	$dataArr = array();
    	$dataArr[] = array('title' => 'Service Configuration', 'value' => $this->apiServiceConfig(), 'modal_id' => 'api_service_modal');
    	$dataArr[] = array('title' => 'Server Settings', 'value' => $this->server_settings(), 'modal_id' => 'server_settings_modal');
    	$dataArr[] = array('title' => 'Server Ports', 'value' => $this->server_ports(), 'modal_id' => 'server_ports_modal');
    	$dataArr[] = array('title' => 'PHP ini Settings', 'value' => $this->phpini_settings(), 'modal_id' => 'phpini_settings_modal');
    	$dataArr[] = array('title' => 'PHP Modules', 'value' => $this->php_modules(), 'modal_id' => 'php_modules_modal', 'subtitle' => '<small><b>Make sure that "php-devel" package is installed on the server.</b></small>');
    	$dataArr[] = array('title' => 'MySQL Settings', 'value' => $this->mysql_settings(), 'modal_id' => 'mysql_settings_modal');
    	$dataArr[] = array('title' => 'System Settings', 'value' => $this->system_settings(), 'modal_id' => 'system_settings_modal');
    	$dataArr[] = array('title' => 'System Cron Jobs', 'value' => $this->cron_jobs_status(), 'modal_id' => 'cron_jobs_status_modal');
    	$dataArr[] = array('title' => 'Folder Permissions', 'value' => $this->folder_permissions(), 'modal_id' => 'folder_permissions_modal');

    	return $dataArr;
    }

	public function getServerInfo() {
		$server_check_version = '1.0.4';
		$start_time = microtime(TRUE);

		$operating_system = "";
		if(defined('PHP_OS_FAMILY')) {
			$operating_system = PHP_OS_FAMILY;
		}

		if ($operating_system === 'Windows') {
			/* Win CPU */
			$wmi = new COM('WinMgmts:\\\\.');
			$cpus = $wmi->InstancesOf('Win32_Processor');
			$cpuload = 0;
			$cpu_count = 0;
			foreach ($cpus as $key => $cpu) {
				$cpuload += $cpu->LoadPercentage;
				$cpu_count++;
			}
			/* WIN MEM */
			$res = $wmi->ExecQuery('SELECT FreePhysicalMemory,FreeVirtualMemory,TotalSwapSpaceSize,TotalVirtualMemorySize,TotalVisibleMemorySize FROM Win32_OperatingSystem');
			$mem = $res->ItemIndex(0);
			$memtotal = round($mem->TotalVisibleMemorySize / 1000000,2);
			$memavailable = round($mem->FreePhysicalMemory / 1000000,2);
			$memused = round($memtotal-$memavailable,2);
			/* WIN CONNECTIONS */
			$connections = shell_exec('netstat -nt | findstr :80 | findstr ESTABLISHED | find /C /V ""'); 
			$totalconnections = shell_exec('netstat -nt | findstr :80 | find /C /V ""');
		} else {
			/*  Linux CPU */
			$load = sys_getloadavg();
			$cpuload = $load[0];
			/* Linux MEM */
			$free = shell_exec('free');
			
			$free = (string)trim($free);
			$free_arr = explode("\n", $free);
			$mem = explode(" ", $free_arr[1]);
			$mem = array_filter($mem, function($value) { return ($value !== null && $value !== false && $value !== ''); });
			$mem = array_merge($mem);
			$memtotal = round($mem[1] / 1000000,2);
			$memused = round($mem[2] / 1000000,2);
			$memfree = round($mem[3] / 1000000,2);
			$memshared = round($mem[4] / 1000000,2);
			$memcached = round($mem[5] / 1000000,2);
			$memavailable = round($mem[6] / 1000000,2);
			/* Linux Connections */
			$connections = `netstat -ntu | grep :80 | grep ESTABLISHED | grep -v LISTEN | awk '{print $5}' | cut -d: -f1 | sort | uniq -c | sort -rn | grep -v 127.0.0.1 | wc -l`; 
			$totalconnections = `netstat -ntu | grep :80 | grep -v LISTEN | awk '{print $5}' | cut -d: -f1 | sort | uniq -c | sort -rn | grep -v 127.0.0.1 | wc -l`; 
		}

		$memusage = round(($memavailable/$memtotal)*100);

		$phpload = round(memory_get_usage(true) / 1000000,2);

		$diskfree = round(disk_free_space(".") / 1000000000);
		$disktotal = round(disk_total_space(".") / 1000000000);
		$diskused = round($disktotal - $diskfree);

		$diskusage = round($diskused/$disktotal*100);

		if ($memusage > 85 || $cpuload > 85 || $diskusage > 85) {
			$trafficlight = 'red';
		} elseif ($memusage > 50 || $cpuload > 50 || $diskusage > 50) {
			$trafficlight = 'orange';
		} else {
			$trafficlight = '#2F2';
		}

		$end_time = microtime(TRUE);
		$time_taken = $end_time - $start_time;
		$total_time = round($time_taken,4);

		if ( is_null( $ver ) )
			$ver = version_compare( PHP_VERSION, '5.3.0', '>=' );

		  	if ( $runs++ > 0 ) {
		    
		    if ( $ver ) {
		      	clearstatcache( true, '/proc' );
		    } else {
		      	clearstatcache();
		    }
		}
		  
		$stat = stat( '/proc' );

		$php_process_count = ( ( false !== $stat && isset( $stat[3] ) ) ? $stat[3] : 0 );

		$data = array(
			'memusage'			=> $memusage,
			'cpuload'			=> $cpuload ,
			'diskusage'			=> $diskusage,
			'connections'		=> $connections,
			'totalconnections'	=> $totalconnections,
			'memtotal'			=> $memtotal,
			'memused'			=> $memused,
			'memavailable'		=> $memavailable,
			'diskfree'			=> $diskfree,
			'diskused'			=> $diskused,
			'disktotal'			=> $disktotal,
			'phpload'			=> $phpload,
			'total_time'		=> $total_time,
			'php_process_count'	=> $php_process_count,
		);

		return $data;
	}

	public function serverStatusInfo()
	{
		global $tconfig;
		$file_url = $tconfig['tsite_script_file_path'] . "server_information_usage.txt";
		$fopen = fopen($file_url, 'r');
		$fread = fread($fopen, filesize($file_url));
		fclose($fopen);

		$remove = "\n";
		$split = explode($remove, $fread);

		$array = array();
		$tab = "\t";

		foreach ($split as $string) {

			$splitString = explode('=', $string);
			if (isset($string) && !empty($string)) {

				if ($splitString[0] == 'COMMAND_EXECUTED_TIME') {
					$date = explode(',', $splitString[1]);
					$date_array = [];
					foreach ($date as $key => $a) {
						$search = array('@UTC','T');
						$replace = array(' ', ' ');
						$tmp_date = str_replace($search, $replace, $a);
						$systemTimeZone = date_default_timezone_get();
						
						$localdate = converToTz($tmp_date,$systemTimeZone,'Etc/UTC');
						$date_array[$key] = $localdate;
					}
					$array[$splitString[0]] =  $date_array;
				} else {
					$array[$splitString[0]] = explode(',', $splitString[1]);
				}
			}
		}
		return $array;
	}

	public function getSetupInfo()
	{
		global $obj;
		$setupinfo = $obj->MySQLSelect("SELECT * FROM setup_info LIMIT 0,1");
		return $setupinfo;
	}

	public function apiServiceConfig() {
    	$server_ip = $this->getServerIP();
    	$api_service_ip = gethostbyname(API_SERVICE_DOMAIN);
    	
    	if(!$this->checkApiServiceDomain()) {
    		return false;
    	}
    	if(!empty($server_ip) && !empty($api_service_ip)) {
    		if($server_ip != $api_service_ip) {
    			return false;
    		}
    	}
    	if(!$this->checkSysComponentScript()) {
    		return false;
    	}

    	return true;
    }

    public function apiServiceIPConfig() {
    	$server_ip = $this->getServerIP();
    	$api_service_ip = gethostbyname(API_SERVICE_DOMAIN);

    	if(!empty($server_ip) && !empty($api_service_ip)) {
    		if($server_ip != $api_service_ip) {
    			return false;
    		}
    	}

    	return true;
    }

	public function getServerIP() {
		$ip_services = array(
			'https://checkip.amazonaws.com',
			'https://ifconfig.me/ip',
			'https://ifconfig.co/ip',
			'https://api.infoip.io/ip'
		);

		foreach ($ip_services as $service_url) {
			$result = trim($this->getIpServiceDetails($service_url));
			if(!empty($result)) {
		        if(filter_var($result, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {    
		            return $result;
		        }
		    }
		    continue;
		}
	}

	private function getIpServiceDetails($url) {
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    $response = curl_exec($ch);
	    curl_close($ch);

	    return $response;
	}

	public function checkApiServiceDomain() {
		$api_service_ip = gethostbyname(API_SERVICE_DOMAIN);
		if(!empty($api_service_ip)) {
	        if(filter_var($api_service_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {    
	            return true;
	        }
	    }
	    return false;
	}

	public function checkSysComponentScript() {
		global $tconfig;
		$server_ip = $this->getServerIP();
		if(!empty($server_ip) && file_exists($tconfig['tsite_script_file_path'] . md5($server_ip) . '.txt')) {
			return true;
		}

		return false;
	}

	public function checkMemcacheService() {
		global $oCache;

		$cacheKeyTmp = md5("MEMCACHE_SERVICE");
		$oCache->setData($cacheKeyTmp, "Yes");

		$getCacheData = $oCache->getData($cacheKeyTmp);
		if(!empty($getCacheData) && $getCacheData == "Yes") {
			$oCache->delData($cacheKeyTmp);
			return true;
		}

		return false;
	}
}
?>