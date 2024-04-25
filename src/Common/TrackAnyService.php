<?php



namespace Kesk\Web\Common;

class TrackAnyService
{
    public function __construct() {}

    public function getServiceCategories($vLang, $tCategoryDetails = [])
    {
        global $obj, $tconfig;
        $trackServiceCategory = $obj->MySQLSelect("SELECT JSON_UNQUOTE(JSON_EXTRACT(vCategoryName, '$.vCategoryName_".$vLang."')) as vCategoryName, vImage, eMemberType FROM track_service_category WHERE eStatus = 'Active'");
        $ServiceCategoryArr = [];
        foreach ($trackServiceCategory as $ServiceCategory) {
            $TrackServiceArr = $this->getCategories($tCategoryDetails, $ServiceCategory['eMemberType']);
            if (!empty($ServiceCategory['vImage']) && file_exists($tconfig['tpanel_path'].'webimages/icons/DefaultImg/'.$ServiceCategory['vImage'])) {
                $ServiceCategory['vImage'] = $tconfig['tsite_url'].'webimages/icons/DefaultImg/'.$ServiceCategory['vImage'];
                $ServiceCategory['vListLogo'] = $ServiceCategory['vImage'];
            }
            $ServiceCategory['vCategory'] = $ServiceCategory['vCategoryName'];
            $ServiceCategory['vTitle'] = $ServiceCategory['vCategoryName'];
            $ServiceCategory['MemberType'] = $ServiceCategory['eMemberType'];
            $ServiceCategory['eCatType'] = 'TrackAnyService';
            $ServiceCategory['TrackServiceSection'] = 'Yes';
            $ServiceCategoryArr[] = $ServiceCategory;
        }

        return $ServiceCategoryArr;
    }

    public function getCategories($tCategoryDetails, $eMemberType)
    {
        global $languageLabelsArrTrackService, $tconfig, $obj, $master_service_category_tbl;
        if (empty($tCategoryDetails)) {
            $service_details = $obj->MySQLSelect("SELECT tCategoryDetails FROM {$master_service_category_tbl} WHERE eType = 'TrackAnyService' ");
            $tCategoryDetails = json_decode($service_details[0]['tCategoryDetails'], true);
        }
        $vImageTrackService = '';
        if (!empty($tCategoryDetails['TrackService']['vImage']) && file_exists($tconfig['tsite_upload_app_home_screen_images_path'].$tCategoryDetails['TrackService']['vImage'])) {
            $imagedata = getimagesize($tconfig['tsite_upload_app_home_screen_images_path'].$tCategoryDetails['TrackService']['vImage']);
            $vImageWidthTrackService = (string) $imagedata[0];
            $vImageHeightTrackService = (string) $imagedata[1];
            $vImageTrackService = $tconfig['tsite_upload_app_home_screen_images'].$tCategoryDetails['TrackService']['vImage'];
        }
        $vImageTrackServiceAdd = '';
        if (!empty($tCategoryDetails['TrackServiceAdd']['vImage']) && file_exists($tconfig['tsite_upload_app_home_screen_images_path'].$tCategoryDetails['TrackServiceAdd']['vImage'])) {
            $imagedata = getimagesize($tconfig['tsite_upload_app_home_screen_images_path'].$tCategoryDetails['TrackServiceAdd']['vImage']);
            $vImageWidthTrackServiceAdd = (string) $imagedata[0];
            $vImageHeightTrackServiceAdd = (string) $imagedata[1];
            $vImageTrackServiceAdd = $tconfig['tsite_upload_app_home_screen_images'].$tCategoryDetails['TrackServiceAdd']['vImage'];
        }
        $category_arr = [['vCategory' => $languageLabelsArrTrackService['LBL_TRACK_SERVICE_TRACK_MEMBER_TXT'], 'vImage' => $vImageTrackService, 'vListLogo' => $vImageTrackService, 'vImageWidth' => $vImageWidthTrackService, 'vImageHeight' => $vImageHeightTrackService, 'eCatType' => 'TrackService'], ['vCategory' => $languageLabelsArrTrackService['LBL_TRACK_SERVICE_SETUP_PROFILE_TXT'], 'vImage' => $vImageTrackServiceAdd, 'vListLogo' => $vImageTrackServiceAdd, 'vImageWidth' => $vImageWidthTrackServiceAdd, 'vImageHeight' => $vImageHeightTrackServiceAdd, 'eCatType' => 'TrackServiceAdd']];
        if (isset($_REQUEST['type']) && 'checkTrackingProfileSetup' === $_REQUEST['type']) {
            $tracking_users = $this->listTrackingUsers(0, $eMemberType);
            if (!empty($tracking_users['message']) && \count($tracking_users['message']) > 0) {
                $category_arr = ['vCategory' => $languageLabelsArrTrackService['LBL_TRACK_SERVICE_TRACK_MEMBER_TXT'], 'vImage' => $vImageTrackService, 'vListLogo' => $vImageTrackService, 'vImageWidth' => $vImageWidthTrackService, 'vImageHeight' => $vImageHeightTrackService, 'eCatType' => 'TrackService'];
            } else {
                $category_arr = ['vCategory' => $languageLabelsArrTrackService['LBL_TRACK_SERVICE_SETUP_PROFILE_TXT'], 'vImage' => $vImageTrackServiceAdd, 'vListLogo' => $vImageTrackServiceAdd, 'vImageWidth' => $vImageWidthTrackServiceAdd, 'vImageHeight' => $vImageHeightTrackServiceAdd, 'eCatType' => 'TrackServiceAdd'];
            }
        }

        return $category_arr;
    }

    public function verifyPairingCode(): void
    {
        global $obj, $LANG_OBJ;
        $iMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $vPairingCode = $_REQUEST['vPairingCode'] ?? '';
        $vLangCode = isset($_REQUEST['vGeneralLang']) ? clean($_REQUEST['vGeneralLang']) : '';
        if ('' === $vLangCode || null === $vLangCode) {
            $vLangCode = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, '1', '');
        $tracking_code_db = $obj->MySQLSelect("SELECT iUserId FROM track_service_pairing_codes WHERE vPairingCode = '{$vPairingCode}' ");
        if (!empty($tracking_code_db) && \count($tracking_code_db) > 0) {
            $track_user_db = $obj->MySQLSelect('SELECT iTrackServiceUserId FROM track_service_users WHERE FIND_IN_SET('.$tracking_code_db[0]['iUserId'].", tUserIds) AND iTrackServiceUserId = {$iMemberId}");
            if (!empty($track_user_db) && \count($track_user_db) > 0) {
                $returnArr['Action'] = '0';
                $returnArr['message'] = 'LBL_TRACK_SERVICE_USER_ALREADY_PAIRED_MSG';
            } else {
                $userData = $obj->MySQLSelect("SELECT vPhoneCode, vPhone FROM register_user WHERE iUserId = '".$tracking_code_db[0]['iUserId']."' ");
                $vPhoneNo = substr($userData[0]['vPhone'], -4);
                $returnArr['Action'] = '1';
                $returnArr['message'] = str_replace('#PHONE_NO#', 'XXXXXX'.$vPhoneNo, $languageLabelsArr['LBL_TRACK_SERVICE_VERIFICATION_CODE_HINT']);
                $returnArr['Phone'] = $userData[0]['vPhone'];
                $returnArr['PhoneCode'] = $userData[0]['vPhoneCode'];
                $returnArr['PairedUserId'] = $tracking_code_db[0]['iUserId'];
            }
        } else {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_TRACK_SERVICE_PAIRING_CODE_INVALID';
        }
        setDataResponse($returnArr);
    }

    public function sendAuthOtpTracking(): void
    {
        global $obj, $MOBILE_NO_VERIFICATION_METHOD, $COMM_MEDIA_OBJ, $SITE_NAME, $LANG_OBJ;
        $iMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $PairedUserId = $_REQUEST['PairedUserId'] ?? '';
        $vLangCode = isset($_REQUEST['vGeneralLang']) ? clean($_REQUEST['vGeneralLang']) : '';
        if ('' === $vLangCode || null === $vLangCode) {
            $vLangCode = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $res = $obj->MySQLSelect("SELECT * from send_message_templates where vEmail_Code = 'TRACKING_AUTH_OTP'");
        $message = $res[0]['vBody_'.$vLangCode];
        $userData = $obj->MySQLSelect("SELECT vPhoneCode, vPhone FROM register_user WHERE iUserId = '".$PairedUserId."' ");
        $otp = random_int(1_000, 9_999);
        $message = str_replace(['#OTP#', '#SITE_NAME#'], [$otp, $SITE_NAME], $message);
        $returnArr['Action'] = '1';
        $returnArr['MOBILE_NO_VERIFICATION_METHOD'] = $MOBILE_NO_VERIFICATION_METHOD;
        if ('FIREBASE' !== strtoupper($MOBILE_NO_VERIFICATION_METHOD)) {
            $result = $COMM_MEDIA_OBJ->SendSystemSMS($userData[0]['vPhone'], $userData[0]['vPhoneCode'], $message);
        } else {
            $result = 1;
        }
        if (0 === $result) {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_FAILED_SEND_AUTH_OTP';
            setDataResponse($returnArr);
        } else {
            $returnArr['Action'] = '1';
            $returnArr['message'] = $otp;
            $returnArr['Phone'] = $userData[0]['vPhone'];
            $returnArr['PhoneCode'] = $userData[0]['vPhoneCode'];
            setDataResponse($returnArr);
        }
        setDataResponse($returnArr);
    }

    public function pairTrackingUser(): void
    {
        global $obj, $EVENT_MSG_OBJ, $LANG_OBJ, $SITE_NAME;
        $iMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $PairedUserId = $_REQUEST['PairedUserId'] ?? '';
        $vPairingCode = $_REQUEST['vPairingCode'] ?? '';
        if (empty($vPairingCode)) {
        }
        $pairing_code_db = $obj->MySQLSelect("SELECT eMemberType FROM track_service_pairing_codes WHERE vPairingCode = '{$vPairingCode} ' ");
        if (!empty($pairing_code_db) && \count($pairing_code_db) > 0) {
            $MemberType = $pairing_code_db[0]['eMemberType'];
        } else {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_SOMETHING_WENT_WRONG_MSG';
            setDataResponse($returnArr);
        }
        $track_user_db = $obj->MySQLSelect("SELECT tUserIds FROM track_service_users WHERE iTrackServiceUserId = '{$iMemberId}' ");
        $tUserIds = $track_user_db[0]['tUserIds'];
        $tUserIdsArr = [];
        if (!empty($tUserIds)) {
            $tUserIdsArr = explode(',', $tUserIds);
        }
        $tUserIdsArr[] = $PairedUserId;
        $tUserIdsStr = implode(',', $tUserIdsArr);
        $obj->sql_query("UPDATE track_service_users SET tUserIds = '".$tUserIdsStr."', eMemberType = '{$MemberType}' WHERE iTrackServiceUserId = '".$iMemberId."'");
        $userData = $obj->MySQLSelect("SELECT iUserId, vLang, eDeviceType, iGcmRegId, eAppTerminate, eDebugMode, eHmsDevice, tSessionId FROM register_user WHERE iUserId = '{$PairedUserId}'");
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($userData[0]['vLang'], '1', '');
        $alertMsg = str_replace('#SITE_NAME#', $SITE_NAME, $languageLabelsArr['LBL_TRACK_SERVICE_DEVICE_PAIRED_SUCCESS_MSG']);
        $vMsgCode = (string) time();
        $message_arr = [];
        $message_arr['vMsgCode'] = $vMsgCode;
        $message_arr['vTitle'] = $alertMsg;
        $message_arr['MsgType'] = 'TrackMemberPaired';
        $generalDataArr = [];
        $generalDataArr[] = ['eDeviceType' => $userData[0]['eDeviceType'], 'deviceToken' => $userData[0]['iGcmRegId'], 'eAppTerminate' => $userData[0]['eAppTerminate'], 'eDebugMode' => $userData[0]['eDebugMode'], 'eHmsDevice' => $userData[0]['eHmsDevice'], 'tSessionId' => $userData[0]['tSessionId'], 'alertMsg' => $alertMsg, 'message' => $message_arr, 'channelName' => 'PASSENGER_'.$userData[0]['iUserId']];
        $EVENT_MSG_OBJ->send(['GENERAL_DATA' => $generalDataArr], RN_USER);
        $returnArr['Action'] = '1';
        $returnArr['USER_DATA'] = $this->getTrackingMemberDetailInfo($iMemberId);
        setDataResponse($returnArr);
    }

    public function listTrackingUsers($iTrackServiceUserId = 0, $eMemberType = '')
    {
        global $obj, $LANG_OBJ, $iServiceId, $tconfig;
        $iMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $vLang = $_REQUEST['vGeneralLang'] ?? '';
        if (empty($vLang)) {
            $vLang = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, '1', $iServiceId);
        $returnArr['Action'] = '1';
        $ssql = '';
        if ($iTrackServiceUserId > 0) {
            $ssql = " AND iTrackServiceUserId = '{$iTrackServiceUserId}' ";
        }
        if (!empty($eMemberType)) {
            $ssql .= " AND eMemberType = '{$eMemberType}' ";
        }
        $tracking_users = $obj->MySQLSelect("SELECT iTrackServiceUserId, CONCAT(vName, ' ', vLastName) as userName, CONCAT('+', vPhoneCode, vPhone) as userPhone, vImage, vLocation as userLocation, vAddress as userAddress, vLatitude as userLatitude, vLongitude as userLongitude, eLocationTracking, eGpsStatus FROM track_service_users WHERE FIND_IN_SET({$iMemberId}, tUserIds) AND eStatus = 'Active' {$ssql} ");
        if (!empty($tracking_users) && \count($tracking_users) > 0) {
            $userArr = [];
            foreach ($tracking_users as $k => $user) {
                $userArr[$k]['iTrackServiceUserId'] = $user['iTrackServiceUserId'];
                $userArr[$k]['userName'] = $user['userName'];
                $userArr[$k]['userPhone'] = $user['userPhone'];
                $userArr[$k]['userAddress'] = !empty($user['userAddress']) ? $user['userAddress'] : $user['userLocation'];
                $userArr[$k]['userLatitude'] = $user['userLatitude'];
                $userArr[$k]['userLongitude'] = $user['userLongitude'];
                $userArr[$k]['vImage'] = '';
                $image_path = $tconfig['tsite_upload_images_track_company_user_path'].'/'.$user['iTrackServiceUserId'].'/'.$user['vImage'];
                if (!empty($user['vImage']) && file_exists($image_path)) {
                    $userArr[$k]['vImage'] = $tconfig['tsite_upload_images_track_company_user'].'/'.$user['iTrackServiceUserId'].'/3_'.$user['vImage'];
                }
                $userArr[$k]['LocationTrackingStatus'] = $user['eLocationTracking'];
                $userArr[$k]['GpsStatus'] = $user['eGpsStatus'];
            }
            if ($iTrackServiceUserId > 0) {
                return $userArr[0];
            }
            $returnArr['message'] = $userArr;
        } else {
            $returnArr['message'] = '';
        }
        if (isset($_REQUEST['type']) && 'checkTrackingProfileSetup' === $_REQUEST['type']) {
            return $returnArr;
        }
        setDataResponse($returnArr);
    }

    public function GeneratePairingCode()
    {
        global $obj;
        $random = RandomString(6, 'Yes');
        $db_str = $obj->MySQLSelect("SELECT vPairingCode FROM track_service_pairing_codes WHERE vPairingCode ='".$random."'");
        if (!empty($db_str) && \count($db_str) > 0) {
            $code = GeneratePairingCode();
        } else {
            $code = $random;
        }

        return $code;
    }

    public function getTrackingMemberDetailInfo($iMemberId)
    {
        global $obj, $langLabels, $demo_site_msg, $generalSystemConfigDataArr, $tconfig, $vTimeZone, $vUserDeviceCountry, $iServiceId, $country_data_retrieve, $country_data_arr, $languageLabelDataArr, $MODULES_OBJ, $LANG_OBJ;
        $where = " iTrackServiceUserId = '".$iMemberId."'";
        $tblName = 'track_service_users';
        $data_version['iAppVersion'] = '2';
        $data_version['eLogout'] = 'No';
        $data_version['eDebugMode'] = $_REQUEST['IS_DEBUG_MODE'] ?? '';
        $data_version['tApiFileName'] = pathinfo(__FILE__, PATHINFO_FILENAME);
        $arr_app_version = [];
        $arr_app_version['AppVersionName'] = $_REQUEST['GeneralAppVersion'] ?? '';
        $arr_app_version['AppVersionCode'] = $_REQUEST['GeneralAppVersionCode'] ?? '';
        $data_version['tVersion'] = (string) json_encode($arr_app_version);
        $data_version['tDeviceData'] = $_REQUEST['DEVICE_DATA'] ?? '';
        $data_version['eHmsDevice'] = $_REQUEST['HMS_DEVICE'] ?? 'No';
        $obj->MySQLQueryPerform($tblName, $data_version, 'update', $where);
        $row = $obj->MySQLSelect('SELECT *,iTrackServiceUserId as iMemberId FROM '.$tblName." WHERE iTrackServiceUserId ='".$iMemberId."'");
        $userDetailsArr[$tblName.'_'.$iMemberId] = $row;
        if (\count($row) > 0) {
            $vLanguage = $row[0]['vLang'];
            if ('' === $vLanguage || null === $vLanguage) {
                $vLanguage = $LANG_OBJ->FetchDefaultLangData('vCode');
            }
            if (isset($languageLabelDataArr['language_label_union_other_'.$vLanguage])) {
                $langLabels = $languageLabelDataArr['language_label_union_other_'.$vLanguage];
            } else {
                $langLabels = $LANG_OBJ->FetchLanguageLabels($vLanguage, '1');
                $languageLabelDataArr['language_label_union_other_'.$vLanguage] = $langLabels;
            }
            foreach ($generalSystemConfigDataArr as $key => $value) {
                if (null === $generalSystemConfigDataArr[$key] || empty($generalSystemConfigDataArr[$key])) {
                    $generalSystemConfigDataArr[$key] = '';
                }
            }
            $row[0] = array_merge($row[0], $generalSystemConfigDataArr);
            if ('' !== $_REQUEST['APP_TYPE']) {
                $row[0]['APP_TYPE'] = $_REQUEST['APP_TYPE'];
            }
            $row[0]['REFERRAL_SCHEME_ENABLE'] = 'No';
            $row[0]['MULTI_LEVEL_REFERRAL_SCHEME_ENABLE'] = 'No';
            $row[0]['GOOGLE_ANALYTICS'] = '';
            $row[0]['SERVER_MAINTENANCE_ENABLE'] = $row[0]['MAINTENANCE_APPS'];
            if (isset($row[0]['AUDIO_CALLING_METHOD']) && 'SINCH' === strtoupper($row[0]['AUDIO_CALLING_METHOD'])) {
                if (isset($row[0]['SINCH_APP_ENVIRONMENT_HOST']) && ('' === $row[0]['SINCH_APP_ENVIRONMENT_HOST'] || str_contains($row[0]['SINCH_APP_ENVIRONMENT_HOST'], '#'))) {
                    $row[0]['RIDE_DRIVER_CALLING_METHOD'] = 'Normal';
                }
                if (isset($row[0]['SINCH_APP_KEY']) && ('' === $row[0]['SINCH_APP_KEY'] || str_contains($row[0]['SINCH_APP_KEY'], '#'))) {
                    $row[0]['RIDE_DRIVER_CALLING_METHOD'] = 'Normal';
                }
                if (isset($row[0]['SINCH_APP_SECRET_KEY']) && ('' === $row[0]['SINCH_APP_SECRET_KEY'] || str_contains($row[0]['SINCH_APP_SECRET_KEY'], '#'))) {
                    $row[0]['RIDE_DRIVER_CALLING_METHOD'] = 'Normal';
                }
                $usercountrycode = $row[0]['vCountry'];
                if ('' !== $usercountrycode) {
                    $eEnableSinch = checkCountryVoipMethod($usercountrycode);
                    if ('NO' === strtoupper($eEnableSinch)) {
                        $row[0]['RIDE_DRIVER_CALLING_METHOD'] = 'Normal';
                    }
                }
            }
            if ('' === $row[0]['tDeviceSessionId']) {
                $random = substr(md5(random_int(0, getrandmax())), 0, 7);
                $Update_Device_Session['tDeviceSessionId'] = session_id().time().$random;
                $Update_Device_Session_id = $obj->MySQLQueryPerform($tblName, $Update_Device_Session, 'update', $where);
                $row[0]['tDeviceSessionId'] = $Update_Device_Session['tDeviceSessionId'];
            }
            if ('' === $row[0]['tSessionId']) {
                $Update_Session['tSessionId'] = session_id().time();
                $Update_Session_id = $obj->MySQLQueryPerform($tblName, $Update_Session, 'update', $where);
                $row[0]['tSessionId'] = $Update_Session['tSessionId'];
            }
            if ('' !== $row[0]['vImage'] && 'NONE' !== $row[0]['vImage']) {
                $row[0]['vImage'] = '3_'.$row[0]['vImage'];
            }
            if ('Active' !== $row[0]['eStatus']) {
                $returnArr['Action'] = '0';
                $returnArr['message'] = 'LBL_ACC_DELETE_TXT';
                if ('Deleted' !== $row[0]['eStatus']) {
                    $returnArr['message'] = 'LBL_CONTACT_US_STATUS_NOTACTIVE_PASSENGER';
                }
                setDataResponse($returnArr);
            }
            $row[0]['Allow_Edit_Profile'] = 'Yes';
            $row[0]['SITE_TYPE'] = SITE_TYPE;
            $row[0]['SITE_TYPE_DEMO_MSG'] = $demo_site_msg;
            $usercountrydetailbytimezone = FetchMemberCountryData($iMemberId, 'Tracking', $vTimeZone, $vUserDeviceCountry);
            $row[0]['vDefaultCountry'] = $usercountrydetailbytimezone['vDefaultCountry'];
            $row[0]['vDefaultCountryCode'] = $usercountrydetailbytimezone['vDefaultCountryCode'];
            $row[0]['vDefaultPhoneCode'] = $usercountrydetailbytimezone['vDefaultPhoneCode'];
            $row[0]['vRCountryImage'] = $usercountrydetailbytimezone['vRImageMember'];
            $row[0]['vSCountryImage'] = $usercountrydetailbytimezone['vSImageMember'];
            $row[0]['vDefaultCountryImage'] = empty($row[0]['vSCountryImage']) ? $usercountrydetailbytimezone['vDefaultCountryImage'] : $row[0]['vSCountryImage'];
            $row[0]['vPhoneCode'] = empty($row[0]['vPhoneCode']) ? $row[0]['vDefaultPhoneCode'] : $row[0]['vPhoneCode'];
            $row[0]['vCountry'] = empty($row[0]['vCountry']) ? $row[0]['vDefaultCountryCode'] : $row[0]['vCountry'];
            $SITE_POLICE_CONTROL_NUMBER = getMemberCountryPoliceNumber($iMemberId, 'Tracking', $row[0]['vCountry']);
            $row[0]['SITE_POLICE_CONTROL_NUMBER'] = $SITE_POLICE_CONTROL_NUMBER;
            $row[0]['MONGO_DB'] = $tconfig['tmongodb_databse'];
            $row[0]['MONGO_DB_CONNECTION_PORT'] = $tconfig['tmongodb_port'];
            $row[0]['SERVER_DEFAULT_TIMEZONE'] = date_default_timezone_get();
            $row[0]['tsite_upload_docs_file_extensions'] = $tconfig['tsite_upload_docs_file_extensions'];
            $row[0]['tsite_upload_image_file_extensions'] = $tconfig['tsite_upload_image_file_extensions'];
            $row[0]['tsite_upload_video_file_extensions'] = $tconfig['tsite_upload_video_file_extensions'];
            $row[0]['SC_CONNECT_URL'] = getSocketURL();
            if (\count($country_data_retrieve) > 0) {
                $getCountryData = [];
                for ($h = 0; $h < \count($country_data_retrieve); ++$h) {
                    if ('ACTIVE' === strtoupper($country_data_retrieve[$h]['eStatus'])) {
                        $getCountryData[] = $country_data_retrieve[$h]['iCountryId'];
                    }
                }
            } else {
                $getCountryData = $obj->MySQLSelect("SELECT iCountryId FROM country WHERE eStatus='Active'");
            }
            $multiCountry = 'No';
            if (\count($getCountryData) > 1) {
                $multiCountry = 'Yes';
            }
            $row[0]['showCountryList'] = $multiCountry;
            $row[0]['RANDOM_COLORS_KEY_VAL_ARR'] = RANDOM_COLORS_KEY_VAL_ARR;
            $row[0]['AUTH_EMAIL_SYSTEM'] = AUTH_EMAIL_SYSTEM;
            $getPageData = $obj->MySQLSelect('SELECT iPageId,eStatus FROM pages WHERE iPageId IN(4,33,52)');
            foreach ($getPageData as $kPage => $vPage) {
                if (4 === $vPage['iPageId']) {
                    $pagename = 'showTermsCondition';
                }
                if (33 === $vPage['iPageId']) {
                    $pagename = 'showPrivacyPolicy';
                }
                if (52 === $vPage['iPageId']) {
                    $pagename = 'showAboutUs';
                }
                $row[0][$pagename] = 'Active' === $vPage['eStatus'] ? 'Yes' : 'No';
            }
            $row[0]['APP_LAUNCH_IMAGES'] = '';
            if (!empty(getAppLaunchImages($vLanguage, 'TrackServiceUser'))) {
                $row[0]['APP_LAUNCH_IMAGES'] = getAppLaunchImages($vLanguage, 'TrackServiceUser');
            }
            $row[0]['DEVICE_PAIRED'] = 'No';
            $row[0]['PAIRED_MEMBERS'] = '';
            if (!empty($row[0]['tUserIds'])) {
                $row[0]['DEVICE_PAIRED'] = 'Yes';
                $tUserIds = $row[0]['tUserIds'];
                $members = $obj->MySQLSelect("SELECT iUserId, CONCAT(vName, ' ', vLastName) as MemberName, CONCAT('+', vPhoneCode, vPhone) as MemberPhone, vImgName FROM register_user WHERE iUserId IN ({$tUserIds}) AND eStatus = 'Active'");
                $membersArr = [];
                foreach ($members as $member) {
                    $membersArr[] = ['iUserId' => $member['iUserId'], 'MemberName' => $member['MemberName'], 'MemberPhone' => $member['MemberPhone'], 'MemberType' => 'Family' === $row[0]['eMemberType'] ? $langLabels['LBL_TRACK_SERVICE_FAMILY_MEMBER_TXT'] : $langLabels['LBL_TRACK_SERVICE_EMPLOYEE_MEMBER_TXT'], 'vImage' => !empty($member['vImgName']) ? $tconfig['tsite_upload_images_passenger'].'/'.$member['iUserId'].'/3_'.$member['vImgName'] : ''];
                }
                $row[0]['PAIRED_MEMBERS'] = $membersArr;
            }
            $row[0]['TSITE_DB'] = TSITE_DB;
            $row[0]['GOOGLE_API_REPLACEMENT_URL'] = GOOGLE_API_REPLACEMENT_URL;
            $row[0]['ENABLE_APPLE_LOGIN_FOR_USER'] = ($MODULES_OBJ->isEnableAppleLoginForUser()) ? 'Yes' : 'No';
            $row[0]['WEBRTC_SOCKET_URL'] = WEBRTC_SOCKET_URL;
            $row[0]['WEBRTC_STUN_URL'] = WEBRTC_STUN_URL;
            $row[0]['WEBRTC_TURN_URL'] = WEBRTC_TURN_URL;
            $row[0]['WEBRTC_ICE_SERVER_LIST'] = WEBRTC_ICE_SERVER_LIST;
            $row[0]['WEBRTC_USERNAME'] = $tconfig['tsite_webrtc_username'];
            $row[0]['WEBRTC_PASS'] = $tconfig['tsite_webrtc_pass'];
            $row[0]['isSmartLoginEnable'] = $MODULES_OBJ->isEnableSmartLogin() ? 'Yes' : 'No';
            $row[0]['RIDE_ENABLED'] = 'No';
            $row[0]['DELIVERY_ENABLED'] = 'No';
            $row[0]['UFX_ENABLED'] = 'No';
            $row[0]['DELIVERALL_ENABLED'] = 'No';
            $row[0]['GENIE_ENABLED'] = 'No';
            $row[0]['RUNNER_ENABLED'] = 'No';
            $row[0]['BIDDING_ENABLED'] = 'No';
            $row[0]['VC_ENABLED'] = 'No';
            $row[0]['MED_UFX_ENABLED'] = 'No';
            $row[0]['RENT_ITEM_ENABLED'] = 'No';
            $row[0]['RENT_ESTATE_ENABLED'] = 'No';
            $row[0]['RENT_CARS_ENABLED'] = 'No';
            $row[0]['NEARBY_ENABLED'] = 'No';
            $row[0]['TRACK_SERVICE_ENABLED'] = 'No';
            $row[0]['RIDE_SHARE_ENABLED'] = 'No';
            $row[0]['TRACK_ANY_SERVICE_ENABLED'] = 'Yes';
            unset($row[0]['vLatitude'], $row[0]['vLongitude'], $row[0]['eLocationTracking'], $row[0]['eGpsStatus']);

            return $row[0];
        }
        $returnArr['Action'] = '0';
        $returnArr['message'] = 'LBL_TRY_AGAIN_LATER_TXT';
        setDataResponse($returnArr);
    }

    public function getPairingCode(): void
    {
        global $obj;
        $iUserId = $_REQUEST['GeneralMemberId'] ?? '';
        $MemberType = $_REQUEST['MemberType'] ?? '';
        if (empty($MemberType)) {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_SOMETHING_WENT_WRONG_MSG';
            setDataResponse($returnArr);
        }
        $pairing_code = $this->GeneratePairingCode();
        $pairing_code_db = $obj->MySQLSelect("SELECT iCodeId,vPairingCode FROM track_service_pairing_codes WHERE iUserId = '{$iUserId}' AND eMemberType = '{$MemberType}' ");
        if (!empty($pairing_code_db) && \count($pairing_code_db) > 0) {
            $Data_update = [];
            $Data_update['vPairingCode'] = $pairing_code;
            $where = " iCodeId = '".$pairing_code_db[0]['iCodeId']."'";
            $obj->MySQLQueryPerform('track_service_pairing_codes', $Data_update, 'update', $where);
        } else {
            $Data_insert = [];
            $Data_insert['iUserId'] = $iUserId;
            $Data_insert['vPairingCode'] = $pairing_code;
            $Data_insert['eMemberType'] = $MemberType;
            $obj->MySQLQueryPerform('track_service_pairing_codes', $Data_insert, 'insert');
        }
        $returnArr['Action'] = '1';
        $returnArr['message'] = $pairing_code;
        setDataResponse($returnArr);
    }

    public function configTrackingTripStatus(): void
    {
        global $obj;
        $iMemberId = $_REQUEST['iMemberId'] ?? '';
        $vLatitude = $_REQUEST['vLatitude'] ?? '';
        $vLongitude = $_REQUEST['vLongitude'] ?? '';
        if ('' !== $iMemberId) {
            if (!empty($vLatitude) && !empty($vLongitude)) {
                $driver_update['vLatitude'] = $vLatitude;
                $driver_update['vLongitude'] = $vLongitude;
            }
            if (\count($driver_update) > 0) {
                $where = " iTrackServiceUserId = '".$iMemberId."'";
                $Update_driver = $obj->MySQLQueryPerform('track_service_users', $driver_update, 'update', $where);
            }
        }
        $returnArr['Action'] = '1';
        setDataResponse($returnArr);
    }

    public function removeLinkedMember(): void
    {
        global $obj, $LANG_OBJ, $EVENT_MSG_OBJ, $SITE_NAME;
        $iMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $UserType = $_REQUEST['GeneralUserType'] ?? '';
        $PairedUserId = $_REQUEST['PairedUserId'] ?? '';
        if (!empty($PairedUserId)) {
            if ('Tracking' === $UserType) {
                $obj->sql_query("UPDATE track_service_users SET tUserIds = TRIM(BOTH ',' FROM REPLACE(CONCAT(',', tUserIds, ','), ',{$PairedUserId},', ',')) WHERE iTrackServiceUserId = '{$iMemberId}'");
                $userData = $obj->MySQLSelect("SELECT iUserId, CONCAT(vName, ' ', vLastName) as userName, vLang, eDeviceType, iGcmRegId, eAppTerminate, eDebugMode, eHmsDevice, tSessionId FROM register_user WHERE iUserId = '{$PairedUserId}'");
                $trackUserData = $obj->MySQLSelect("SELECT CONCAT(vName, ' ', vLastName) as trackUserName FROM track_service_users WHERE iTrackServiceUserId = '{$iMemberId}'");
                $alertUserName = $trackUserData[0]['trackUserName'];
                $channelName = 'PASSENGER_'.$userData[0]['iUserId'];
                $alertLabel = 'LBL_TRACK_SERVICE_PAIRED_MEMBER_REMOVED_MSG';
            } else {
                $obj->sql_query("UPDATE track_service_users SET tUserIds = TRIM(BOTH ',' FROM REPLACE(CONCAT(',', tUserIds, ','), ',{$iMemberId},', ',')) WHERE iTrackServiceUserId = '{$PairedUserId}'");
                $userData = $obj->MySQLSelect("SELECT iTrackServiceUserId, CONCAT(vName, ' ', vLastName) as userName, vLang, eDeviceType, iGcmRegId, eAppTerminate, eDebugMode, eHmsDevice, tSessionId FROM track_service_users WHERE iTrackServiceUserId = '{$PairedUserId}'");
                $trackUserData = $obj->MySQLSelect("SELECT CONCAT(vName, ' ', vLastName) as userName FROM register_user WHERE iUserId = '{$iMemberId}'");
                $alertUserName = $trackUserData[0]['userName'];
                $channelName = 'TRACKING_'.$userData[0]['iTrackServiceUserId'];
                $alertLabel = 'LBL_TRACK_SERVICE_TRACK_MEMBER_REMOVED_MSG';
            }
            $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($userData[0]['vLang'], '1', '');
            $alertMsg = str_replace(['#NAME#', '#SITE_NAME#'], [$alertUserName, $SITE_NAME], $languageLabelsArr[$alertLabel]);
            $vMsgCode = (string) time();
            $message_arr = [];
            $message_arr['vMsgCode'] = $vMsgCode;
            $message_arr['vTitle'] = $alertMsg;
            $message_arr['MsgType'] = 'TrackMemberRemoved';
            $generalDataArr = [];
            $generalDataArr[] = ['eDeviceType' => $userData[0]['eDeviceType'], 'deviceToken' => $userData[0]['iGcmRegId'], 'eAppTerminate' => $userData[0]['eAppTerminate'], 'eDebugMode' => $userData[0]['eDebugMode'], 'eHmsDevice' => $userData[0]['eHmsDevice'], 'tSessionId' => $userData[0]['tSessionId'], 'alertMsg' => $alertMsg, 'message' => $message_arr, 'channelName' => $channelName];
            $returnArr['Action'] = '1';
            if ('Tracking' === $UserType) {
                $EVENT_MSG_OBJ->send(['GENERAL_DATA' => $generalDataArr], RN_USER);
                $returnArr['message'] = '';
                $returnArr['USER_DATA'] = $this->getTrackingMemberDetailInfo($iMemberId);
            } else {
                $EVENT_MSG_OBJ->send(['GENERAL_DATA' => $generalDataArr], RN_TRACK_USER);
                $returnArr['message'] = '';
                $returnArr['USER_DATA'] = getPassengerDetailInfo($iMemberId, '', '');
            }
        } else {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_SOMETHING_WENT_WRONG_MSG';
        }
        setDataResponse($returnArr);
    }

    public function updateLocationTrackingStatus(): void
    {
        global $obj, $LANG_OBJ, $EVENT_MSG_OBJ, $SITE_NAME;
        $iMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $LocationTrackingStatus = $_REQUEST['LocationTrackingStatus'] ?? '';
        $GpsStatus = $_REQUEST['GpsStatus'] ?? '';
        if (!empty($LocationTrackingStatus)) {
            $obj->sql_query("UPDATE track_service_users SET eLocationTracking = '{$LocationTrackingStatus}' WHERE iTrackServiceUserId = '{$iMemberId}' ");
        }
        if (!empty($GpsStatus)) {
            $obj->sql_query("UPDATE track_service_users SET eGpsStatus = '{$GpsStatus}' WHERE iTrackServiceUserId = '{$iMemberId}' ");
        }
        $track_user_db = $obj->MySQLSelect("SELECT tUserIds, CONCAT(vName, ' ', vLastName) as userName, eLocationTracking, eGpsStatus FROM track_service_users WHERE iTrackServiceUserId = '{$iMemberId}' ");
        $LocationTrackingStatus = $track_user_db[0]['eLocationTracking'];
        $GpsStatus = $track_user_db[0]['eGpsStatus'];
        $returnArr['Action'] = '1';
        $returnArr['LocationTrackingStatus'] = $LocationTrackingStatus;
        $returnArr['GpsStatus'] = $GpsStatus;
        setDataResponse($returnArr);
    }
}
