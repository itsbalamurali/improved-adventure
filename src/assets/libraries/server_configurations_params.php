<?php

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 0);
ini_set('zlib.output_compression', 'On');
ini_set('default_socket_timeout', 10);

error_reporting(0);
ini_set('display_errors','0');
ini_set('display_startup_errors', 0);

if(isset($_REQUEST['ENABLE_DEBUG'])) {
	error_reporting(1);
	error_reporting(E_ALL);  
	ini_set('display_errors','1');
	ini_set('display_startup_errors', 0);	
}

date_default_timezone_set('Europe/London'); 

$API_DOMIAN_SERVICE = "apiservice.".get_server_domain($_SERVER['HTTP_HOST']);

//ini_set('precision', '40'); //when divide value to the ratio and then multiply with same value then problem so put it by getting exact value..

?>