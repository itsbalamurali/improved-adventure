<?php
require_once($tconfig['tsite_libraries_v'] . 'include_header.php');
if (!defined('ALLOWED_DOMAINS')) { exit; }

require_once $tconfig['tpanel_path'] . 'assets/libraries/GoogleCloud/autoload.php';

    use Google\Cloud\Storage\StorageClient;

    class GoogleCloudStorage {
	const BUCKET_NAME = 'system_' . GCS_SUFFIX;
    const ANDROID_USER = 'ANDROID_USER_' . GCS_SUFFIX . '.txt';
    const ANDROID_DRIVER = 'ANDROID_PROVIDER_' . GCS_SUFFIX . '.txt';
    const ANDROID_STORE = 'ANDROID_STORE_' . GCS_SUFFIX . '.txt';
    const IOS_USER = 'IOS_USER_' . GCS_SUFFIX . '.txt';
    const IOS_DRIVER = 'IOS_PROVIDER_' . GCS_SUFFIX . '.txt';
    const IOS_STORE = 'IOS_STORE_' . GCS_SUFFIX . '.txt';

	function __construct() {
		global $tconfig;

		if(file_exists($tconfig['tsite_script_file_path'] . 'gcs_config.json')) {
			$gcs_config = json_decode(GetFileData($tconfig['tsite_script_file_path'] . 'gcs_config.json'), true);
			$GOOGLE_PROJECT_ID = $gcs_config['project_id'];
			
			$this->storage = new StorageClient([
			    'projectId' => $GOOGLE_PROJECT_ID,
			    'keyFilePath' => $tconfig['tsite_script_file_path'] . 'gcs_config.json',  
			]);
		}

		if(!empty($this->storage)) {
			$this->BUCKET_NAME = strtolower(self::BUCKET_NAME);
			$bucket = $this->storage->bucket($this->BUCKET_NAME);

			if (!$bucket->exists()) {
				try {
					$this->storage->createBucket($this->BUCKET_NAME, [
				        'location' => "EU",
				        'predefinedAcl' => 'PUBLICREAD'
				    ]);	
					
					$this->updateGCSData();
				} catch (Exception $e) {
					echo "<h2>Error: Failed to create GCS bucket.</h2>";
					exit;
				}
			}
		} else {
			echo "<h2>Error: Missing configuration file \"gcs_config.json\" in webimages/script_files/</h2>";
			exit;
		}
	}

	public function updateGCSData() {
		$SECRET_KEY_ENC = ENC_KEY;
		$SECRET_IV_ENC = ENC_IV;

		$bucket = $this->storage->bucket($this->BUCKET_NAME);

		$GCS_APP_ARR = array('Passenger', 'Driver', 'Company');
		foreach ($GCS_APP_ARR as $GCS_APP) {
			$content = $this->getGeneralConfigData($GCS_APP);

			if($GCS_APP == "Passenger") {
				$GCS_APP_ANDROID = self::ANDROID_USER;
				$GCS_APP_IOS = self::IOS_USER;
			} elseif ($GCS_APP == "Driver") {
				$GCS_APP_ANDROID = self::ANDROID_DRIVER;
				$GCS_APP_IOS = self::IOS_DRIVER;
			} else {
				$GCS_APP_ANDROID = self::ANDROID_STORE;
				$GCS_APP_IOS = self::IOS_STORE;
			}

		    $content = $this->reConfigureData(securedEncryptGT($content));
			
		    $bucket->upload($content, [
		        'name' => $GCS_APP_ANDROID,
		        'predefinedAcl' => 'PUBLICREAD',
		        'metadata' => [
			      	'cacheControl' => "public, max-age=0"
			    ]
		    ]);

		    $bucket->upload($content, [
		        'name' => $GCS_APP_IOS,
		        'predefinedAcl' => 'PUBLICREAD',
		        'metadata' => [
			      	'cacheControl' => "public, max-age=0"
			    ]
		    ]);
		}
	}

	private function getGeneralConfigData($UserType) {
		global $tconfig;

		$webservice_url = $tconfig['tsite_url'] . WEBSERVICE_API_FILE_NAME . '?type=generalConfigData&GeneralAppVersion=1.0&GeneralDeviceType=Android&GeneralUserType=' . $UserType;

		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $webservice_url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $response = curl_exec($ch);
	    curl_close($ch);

	    return $response;
	}

	private function reConfigureData($data) {
		$ENC_KEY_ARR = str_split(ENC_KEY, 4);
		$ENC_IV_ARR = str_split(ENC_IV, 4);
		$ENC_KEY_IV_ARR = array_merge($ENC_KEY_ARR, $ENC_IV_ARR);

		$key_pos = ENC_POS;
		foreach ($ENC_KEY_IV_ARR as $key_block) {
			$data = substr_replace($data, $key_block, $key_pos, 0);
			$key_pos = $key_pos + ENC_POS + 4;
		}

		return $data;
	}
}
?>