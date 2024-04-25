<?php
require_once($tconfig['tsite_libraries_v'] . 'include_header.php');
if (!defined('ALLOWED_DOMAINS')) { exit; }
include_once $tconfig['tsite_libraries_v'] . 'models/class.gcs.php';
if(stripos_arr($_SERVER['REQUEST_URI'], ['setup_validation.php', 'language_setup.php']) === false) {
	$GCS_OBJ = new GoogleCloudStorage;
}
// die('test');
?>