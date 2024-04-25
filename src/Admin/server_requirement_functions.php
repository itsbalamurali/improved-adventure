<?php


function checkModSecurity()
{
    ob_start();
    phpinfo(INFO_MODULES);
    $contents = ob_get_clean();

    return strpos($contents, 'mod_security');
}

function checkPostMaxSize()
{
    $post_max_size = ini_get('post_max_size');

    return str_replace('M', '', $post_max_size);
}

function checkUploadMaxFileSize()
{
    $upload_max_filesize = ini_get('upload_max_filesize');

    return str_replace('M', '', $upload_max_filesize);
}

function checkSqlMode()
{
    global $obj;
    $sql_mode = $obj->MySQLSelect('SELECT @@sql_mode');

    return $sql_mode[0]['@@sql_mode'];
}

function checkSqlCharset()
{
    global $obj;
    $sql_mode = $obj->MySQLSelect('SELECT @@character_set_database, @@collation_database');
    $default_charset = mysqli_character_set_name($obj->GetConnection());
    if ('utf8' === $default_charset) {
        return $default_charset;
    }
    if ('utf8' === $sql_mode[0]['@@character_set_database']) {
        return $sql_mode[0]['@@character_set_database'];
    }

    return $default_charset;
}

function checkCurlExtension()
{
    $curl = curl_init();
    curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => 'https://www.google.com/']);
    $curl_resp = '';
    if (curl_exec($curl)) {
        $curl_resp = curl_exec($curl);
    }
    curl_close($curl);

    if (!empty($curl_resp)) {
        return true;
    }

    return false;
}

function checkPHPandMySqlTimeZone()
{
    global $obj;
    $php_time_zone = date_default_timezone_get();
    $mysql_time_zone = $obj->MySQLSelect('SELECT @@system_time_zone');
    $mysql_time_zone = $mysql_time_zone[0]['@@system_time_zone'];

    if ($php_time_zone === $mysql_time_zone) {
        return true;
    }

    return false;
}

function getTimezoneOffset()
{
    $phpTime = date('Y-m-d H:i:s');
    $timezone = new DateTimeZone(date_default_timezone_get());
    $offset = $timezone->getOffset(new DateTime($phpTime));
    $offsetHours = round(abs($offset) / 3_600);
    if ($offsetHours >= 0) {
        $str_offset = "+0{$offsetHours}:00";
    } else {
        $str_offset = "-0{$offsetHours}:00";
    }

    return $str_offset;
}

function checkSocketCluster()
{
    global $tconfig;
    $sc_host = $tconfig['tsite_sc_host'];
    $sc_port = $tconfig['tsite_host_sc_port'];
    $sc_connection = @fsockopen($sc_host, $sc_port, $errno, $errstr, 5);

    return $sc_connection;
}

function checkOpenPort($host, $port)
{
    $connection = @fsockopen($host, $port, $errno, $errstr, 5);
    if (is_resource($connection)) {
        fclose($connection);

        return true;
    }

    return false;
}

function check_innodb_file_per_table()
{
    global $obj;
    $innodb_file_per_table = $obj->MySQLSelect('SELECT @@innodb_file_per_table');

    return $innodb_file_per_table[0]['@@innodb_file_per_table'];
}

function check_query_cache_type()
{
    global $obj;
    $query_cache_type = $obj->MySQLSelect('SELECT @@query_cache_type');

    return $query_cache_type[0]['@@query_cache_type'];
}

function check_open_files_limit()
{
    global $obj;
    $open_files_limit = $obj->MySQLSelect('SELECT @@open_files_limit');

    return $open_files_limit[0]['@@open_files_limit'];
}

function check_max_allowed_packet()
{
    global $obj;
    $max_allowed_packet = $obj->MySQLSelect('SELECT @@max_allowed_packet');

    return $max_allowed_packet[0]['@@max_allowed_packet'];
}

function check_max_connections()
{
    global $obj;
    $max_connections = $obj->MySQLSelect('SELECT @@max_connections');

    return $max_connections[0]['@@max_connections'];
}

function check_max_user_connections()
{
    global $obj;
    $max_user_connections = $obj->MySQLSelect('SELECT @@max_user_connections');

    return $max_user_connections[0]['@@max_user_connections'];
}

function check_innodb_buffer_pool_size()
{
    global $obj;
    $innodb_buffer_pool_size = $obj->MySQLSelect('SELECT @@innodb_buffer_pool_size/1024 as innodb_buffer_pool_size');

    return $innodb_buffer_pool_size[0]['innodb_buffer_pool_size'];
}

function checkHtaccess()
{
    global $tconfig;
    stream_context_set_default([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ]);
    $url = $tconfig['tsite_url'].'sign-in';
    $headers = get_headers($url);
    if (str_contains($headers[0], '200')) {
        return true;
    }

    return false;
}

function checkForceHttps()
{
    global $tconfig;
    $url = $tconfig['tsite_url_main_admin'].'server_details.php';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($response, true);
    if ('http' === $result['REQUEST_SCHEME']) {
        return true;
    }

    return false;
}

function checkMapAPIreplacementAvailable()
{
    global $tconfig;
    $host = $tconfig['tsite_gmap_replacement_host'];
    $port = $tconfig['tsite_host_gmap_replacement_port'];
    $connection = @fsockopen($host, $port);

    return $connection;
}

function checkMapAPIService()
{
    global $obj;

    $db_con = $obj->MySQLSelect("SELECT cn.vCountryCode,cn.vCountry,cn.tLatitude,cn.tLongitude from country cn inner join configurations c on c.vValue=cn.vCountryCode where c.vName='DEFAULT_COUNTRY_CODE_WEB'");
    $vCountry = $db_con[0]['vCountryCode'];
    $tLatitude = $db_con[0]['tLatitude'];
    $tLongitude = $db_con[0]['tLongitude'];
    $session_token = 'Passenger_4_7899765332757';
    $search_address = $db_con[0]['vCountry']; // Country Name

    $returnValue = false;
    $language_code = $_SESSION['sess_lang'];
    // =========autocomplete
    $search_address = str_replace(' ', '+', $search_address);
    $params_autocomp = '?language_code='.$language_code.'&search_query='.$search_address.'&latitude='.$tLatitude.'&longitude='.$tLongitude.'&TSITE_DB='.TSITE_DB.'&session_token='.$session_token.'';
    $url_autocomplete = GOOGLE_API_REPLACEMENT_URL.'autocomplete'.$params_autocomp;
    // $response = json_encode(file_get_contents($url));
    $response_autocomp = curlCall($url_autocomplete);

    // $response_autocomp = json_decode(file_get_contents($url_autocomplete));
    $response_count_auto = count($response_autocomp->data);
    // =========geocode
    $params_geo_code = '?language_code='.$language_code.'&latitude='.$tLatitude.'&longitude='.$tLongitude.'&TSITE_DB='.TSITE_DB.'&session_token='.$session_token.'';
    $url_geo_code = GOOGLE_API_REPLACEMENT_URL.'reversegeocode'.$params_geo_code;
    // $response_geo_code = json_decode(file_get_contents($url_geo_code));
    $response_geo_code = curlCall($url_geo_code);
    $response_count_geo_code = count($response_geo_code->address);
    // =========direction
    $waypoint0 = $tLatitude.','.$tLongitude;
    $waypoint1 = $tLatitude.','.$tLongitude;
    $params_direction = '?language_code='.$language_code.'&source_latitude='.$tLatitude.'&source_longitude='.$tLongitude.'&dest_latitude='.$tLatitude.'&dest_longitude='.$tLongitude.'&TSITE_DB='.TSITE_DB.'&session_token='.$session_token.'&waypoint0='.$waypoint0.'&waypoint1='.$waypoint1.'';
    $url_direction = GOOGLE_API_REPLACEMENT_URL.'direction'.$params_direction;
    // $response_direction = json_decode(file_get_contents($url_direction));
    $response_direction = curlCall($url_direction);
    // echo "<pre>"; print_r($response_direction); exit;
    $response_count_direction = count($response_direction->data);
    // =========check in all condition

    if ($response_count_auto > 0 && $response_count_geo_code > 0 && $response_count_direction > 0) {
        $returnValue = true;
    }

    return $returnValue;
}

function getSystemMemInfo()
{
    $data = explode("\n", file_get_contents('/proc/meminfo'));
    $data = array_filter($data);
    $meminfo = [];
    foreach ($data as $line) {
        [$key, $val] = explode(':', $line);
        $meminfo[$key] = trim($val);
    }

    return $meminfo;
}

function getDirectoriesList($directories)
{
    global $tconfig;
    $all_directories = [];

    foreach ($directories as $directory) {
        if (false === stripos($directory, 'domain_cert_files')) {
            $permission = substr(sprintf('%o', fileperms($tconfig['tpanel_path'].$directory)), -4);
            $all_directories['main_dirs'][] = ['path' => '/'.$directory, 'permission' => $permission];
            $all_directories['sub_dirs'][] = [];

            $dir_path = $tconfig['tpanel_path'].$directory.'/*';
            $all_sub_directories = getSubDirectories($dir_path);

            foreach ($all_sub_directories as $sub_directory) {
                $permission = substr(sprintf('%o', fileperms($sub_directory)), -4);
                $all_directories['sub_dirs'][] = ['path' => '/'.str_replace($tconfig['tpanel_path'], '', $sub_directory), 'permission' => $permission];
            }
        }
    }

    return $all_directories;
}

function getSubDirectories($dir)
{
    $subDir = [];
    $directories = array_filter(glob($dir), 'is_dir');
    $subDir = array_merge($subDir, $directories);
    foreach ($directories as $directory) {
        if (false === stripos($directory, 'domain_cert_files')) {
            $permission = substr(sprintf('%o', fileperms($directory)), -4);
            $subDir = array_merge($subDir, getSubDirectories($directory.'/*'));
        }
    }

    return $subDir;
}

function checkLanguageSetup($field)
{
    global $obj,$oCache,$getSetupCacheData;
    // Added By HJ On 21-09-2020 For Store setup_info Data into Cache Start
    if (empty($getSetupCacheData) || 0 === count($getSetupCacheData)) {
        $setupInfoApcKey = md5('setup_info');
        $getSetupCacheData = $oCache->getData($setupInfoApcKey);
        if (!empty($getSetupCacheData) && count($getSetupCacheData) > 0) {
            $setup_info_data = $getSetupCacheData;
        } else {
            $setup_info_data = $obj->MySQLSelect('SELECT * FROM setup_info LIMIT 0,1');
            $setSetupCacheData = $oCache->setData($setupInfoApcKey, $setup_info_data);
        }
    } else {
        $setup_info_data = $getSetupCacheData;
    }
    // echo "<pre>";print_r($setup_info_data);die;
    // Added By HJ On 21-09-2020 For Store setup_info Data into Cache End
    // $setup_info_data = $obj->MySQLSelect("SELECT * FROM setup_info");
    $eLanguageFieldsSetup = $setup_info_data[0]['eLanguageFieldsSetup'];
    $eCurrencyFieldsSetup = $setup_info_data[0]['eCurrencyFieldsSetup'];
    $eLanguageLabelConversion = $setup_info_data[0]['eLanguageLabelConversion'];
    $eOtherTableValueConversion = $setup_info_data[0]['eOtherTableValueConversion'];

    if ('eLanguageFieldsSetup' === $field) {
        return ('Yes' === $eLanguageFieldsSetup) ? true : false;
    }
    if ('eCurrencyFieldsSetup' === $field) {
        return ('Yes' === $eCurrencyFieldsSetup) ? true : false;
    }
    if ('eLanguageLabelConversion' === $field) {
        return ('Yes' === $eLanguageLabelConversion && 'Yes' === $eOtherTableValueConversion) ? true : false;
    }
}

function checkSystemTypeCongiguration()
{
    global $APP_TYPE, $parent_ufx_catid;
    if ('Ride-Delivery-UberX' === $APP_TYPE) {
        return (0 === $parent_ufx_catid) ? true : false;
    }

    return true;
}

function checkGhostScript()
{
    $min_gs_version = 1.0;
    $retval = '';
    if (!function_exists('system')) {
        return false;
    }

    if (0 === $retval || shell_exec('gs --version') >= $min_gs_version) {
        return true;
    }

    return false;
}

function checkCurlVersion()
{
    if (checkCurlExtension()) {
        if (curl_version()['version_number'] >= 476_160) {
            return true;
        }
    }

    return false;
}

function checkPhpPearPackage()
{
    include_once 'System.php';

    return class_exists('System');
}

function checkffmpeg()
{
    exec('ffmpeg -version', $output);
    if (!empty($output)) {
        return true;
    }

    return false;
}

function checkIniFiles()
{
    global $tconfig;
    $files = scandir($tconfig['tpanel_path']);
    $fileListArr = [];
    foreach ($files as $file) {
        if (!is_dir($file) && !in_array($file, ['.', '..'], true)) {
            $file_temp = explode('.', $file);
            $ext = $file_temp[count($file_temp) - 1];
            if ('ini' === strtolower($ext)) {
                $fileListArr[] = $file;
            }
        }
    }

    return $fileListArr;
}

function curlCall($url)
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ]);

    $response = curl_exec($curl);

    curl_close($curl);

    return json_decode($response);
}
