<?php
require_once(TPATH_CLASS . 'include_header.php');
if (!defined('ALLOWED_DOMAINS')) { exit; }
class FireBaseClass{
	function __construct() {
		global $tconfig;
		if(file_exists($tconfig['tsite_script_file_path'] . 'firebase_config.json')) { 
			$FirebaseConfig = self::getFirebaseConfig();
		} 
	}

	public static function getFirebaseConfig() {
		global $tconfig;

		return json_decode(GetFileData($tconfig['tsite_script_file_path'] . 'firebase_config.json'), true);
	}
}
?>